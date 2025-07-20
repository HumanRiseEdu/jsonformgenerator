<?php
namespace Humanriseedu\Jfg\Components;

class InputText extends AbstractFormComponent {
    public function __construct(array $config = [], string $templateDir = '') {
        parent::__construct($config, $templateDir);
        $this->type = $config['type'] ?? 'text';
    }

    public function render(): string {
        $vars = $this->getCommonVars();
        $vars['type'] = $this->type;
        return $this->renderTemplate($vars);
    }
}
