<?php
namespace Humanriseedu\Jfg\Components;
namespace FormBuilder\Components;

abstract class AbstractFormComponent {
    protected string $templateDir = '';
    protected string $templateName = ''; // ex: input_default.tem
    protected string $type = '';
    protected string $id = '';
    protected string $name = '';
    protected string $class = '';
    protected string $style = '';
    protected string $value = '';
    protected string $placeholder = '';
    protected bool $readonly = false;
    protected bool $required = false;
    protected bool $disabled = false;
    protected int $index = 0;

    protected array $attributs = [];
    protected array $events = [];

    protected string $jscode = '';
    protected string $jscode_onload = '';
    protected string $jscontrole = '';

    public function __construct(array $config = [], string $templateDir = '') {
        $this->templateDir = rtrim($templateDir, '/');
        $this->hydrate($config);
    }

    protected function hydrate(array $config): void {
        foreach ($config as $key => $val) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($val);
            } elseif (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }
    }

    public function setTemplateName(string $name): void {
        $this->templateName = $name;
    }

    protected function getCommonAttributesAsString(): string {
        $attrs = [
            'id' => $this->id,
            'name' => $this->name,
            'class' => $this->class,
            'style' => $this->style,
            'placeholder' => $this->placeholder,
            'value' => $this->value,
            'required' => $this->required ? 'required' : null,
            'readonly' => $this->readonly ? 'readonly' : null,
            'disabled' => $this->disabled ? 'disabled' : null
        ];

        $result = '';
        foreach ($attrs as $key => $val) {
            if ($val !== null && $val !== '') {
                $result .= sprintf('%s="%s" ', $key, htmlspecialchars((string)$val, ENT_QUOTES));
            }
        }

        foreach ($this->attributs as $attr) {
            foreach ($attr as $k => $v) {
                $result .= sprintf('%s="%s" ', $k, htmlspecialchars((string)$v, ENT_QUOTES));
            }
        }

        foreach ($this->events as $evt) {
            foreach ($evt as $k => $v) {
                $result .= sprintf('%s="%s" ', $k, htmlspecialchars((string)$v, ENT_QUOTES));
            }
        }

        return trim($result);
    }

    protected function renderTemplate(array $vars = []): string {
        $filename = $this->templateDir . '/' . static::classToTemplateDir() . '/' . ($this->templateName ?: static::defaultTemplateName());

        if (!file_exists($filename)) {
            throw new \Exception("Template not found: $filename");
        }

        $template = file_get_contents($filename);
        foreach ($vars as $key => $value) {
            $template = str_replace('{{ ' . $key . ' }}', htmlspecialchars((string)$value, ENT_QUOTES), $template);
        }

        return $template;
    }

    protected static function classToTemplateDir(): string {
        return strtolower((new \ReflectionClass(static::class))->getShortName());
    }

    protected static function defaultTemplateName(): string {
        return static::classToTemplateDir() . '.tem';
    }

    public function getCommonVars(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'class' => $this->class,
            'style' => $this->style,
            'value' => $this->value,
            'placeholder' => $this->placeholder,
            'attrs' => $this->getCommonAttributesAsString(),
            'index' => $this->index,
            'jscontrole' => $this->jscontrole,
            'jscode' => $this->jscode,
            'jscode_onload' => $this->jscode_onload,
        ];
    }

    abstract public function render(): string;
}
