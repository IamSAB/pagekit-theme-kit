<?php

namespace SAB\ThemeKit;

use Pagekit\Module\Module;
use Pagekit\Application as App;
use Pagekit\Util\Arr;


class ThemeKit extends Module
{
    private $app;
    private $fieldsets = [];
    private $theme = null;

    const INHERIT_PREFIX_NODE = 'n_';
    const INHERIT_PREFIX_WIDGET = 'w_';

    public function main(App $app)
    {
        $this->app = $app;
        $this->applyOverwrites($app->locator()->get('theme-kit:views/overwrites'));
    }

    private function applyOverwrites(string $dir, $root = '')
    {
        if ($dir) {
            foreach ($this->app->file()->listDir("$dir/$root") as $name) {
                $parts = explode('.', $name);
                $path = ($root ? $root.'/' : $root) . $parts[0];
                if (isset($parts[1])) {
                    $this->app->on("view.$path", function ($event) use ($path) {
                        $event->setTemplate("theme-kit/overwrites/$path.php");
                    });
                }
                else {
                    $this->applyOverwrites($dir, $path);
                }
            }
        }
    }

    private function loadFieldsetsFromDirectory(string $path): void
    {
        if ($path = $this->app->locator()->get($path)) {
            $matches = [];
            $files = $this->app->file()->listDir($path);
            foreach ($files as $name) {
                if (preg_match('/([A-Za-z]+)\.json/', $name, $matches)) {
                    $this->fieldsets[$matches[1]] = json_decode(file_get_contents($path.'/'.$matches[0]), true);
                }
            }
        }
    }
    private function load(string $ui, array $default = []): array
    {
        $config = $this->theme->get($ui, []);

        // merge fieldsets into config
        foreach ($config as $name => $form) {
            $config[$name]['fieldsets'] = array_intersect_key($this->fieldsets(), array_flip($form['fieldsets']));
        }

        return $config;
    }

    private function loadNodeTheme(): array
    {
        $config = $this->theme->get('node-theme', []);
        $config['general'] = [
            'label' => 'General',
            'categories' => ['Site'],
            'fieldsets' => ['node', 'heading', 'inverse']
        ];
        return $this->process($config);
    }

    private function loadWidgetTheme()
    {
        $config = $this->theme->get('widget-theme', []);
        $config['widget'] = [
            'label' => 'Widget',
            'fieldsets' => ['heading', 'text', 'visibility', 'inverse', 'custom'],
        ];
        return $this->process($config);
    }

    private function process(array $forms)
    {
        if (empty($this->fieldsets)) {
            $this->loadFieldsetsFromDirectory('theme-kit:fieldsets');
            $this->loadFieldsetsFromDirectory('theme:fieldsets');
        }

        $defaults = $this->theme->get('defaults');

        foreach ($forms as $f => &$form) {
            $form['fieldsets'] = array_intersect_key($this->fieldsets, array_flip($form['fieldsets']));
            foreach ($form['fieldsets'] as $fs => &$fieldset) {
                if (in_array($fs, $defaults)) {
                    $fieldset['inherit'] = true;
                }
            }
        }

        // remove reference
        unset($form); unset($fieldset);

        return $forms;
    }

    public function onViewInit($event, $view)
    {
        $view->addHelper(new ValuesHelper);
    }


    public function onSiteEdit($event, $view)
    {
        $view->data('$config', $this->loadNodeTheme());
        $view->script('node-theme', 'theme-kit:app/bundle/node-theme.js', 'site-edit');
    }

    public function onSiteSettings($event, $view)
    {
        $config = [];

        foreach($this->loadNodeTheme() as $f => $form) {
            if (Arr::get($form, 'inherit', false)) {
                unset($form['inherit']);
                $config[self::INHERIT_PREFIX_NODE.$f] = $form;
            }
        }

        foreach($this->loadWidgetTheme() as $f => $form) {
            if (Arr::get($form, 'inherit', false)) {
                unset($form['inherit']);
                $config[self::INHERIT_PREFIX_WIDGET.$f] = $form;
            }
        }

        $config['defaults'] = [
            'label' => 'Defaults',
            'categories' => ['Fieldsets'],
            'fieldsets' => array_intersect_key($this->fieldsets, array_flip($this->theme->get('defaults')))
        ];

        $view->data('$config', $config);
        $view->data('$themeKit', $this->config());

        $view->script('settings-theme-kit', 'theme-kit:app/bundle/settings-theme-kit.js', 'site-settings');
    }

    public function onWidgetEdit($event, $view)
    {
        $view->data('$config', $this->loadWidgetTheme());
        $view->script('widget-theme', 'theme-kit:app/bundle/widget-theme.js', ['widget-edit', 'panel-finder' ]);
    }

    public function subscribe()
    {
        // search for a module which require theme-kit
        foreach ($this->app->module() as $module) {
            if ($module->get('require', '') == 'theme-kit') {
                $this->theme = $module;
                continue;
            }
        }

        if (empty($this->theme)) return [];

        return [
            'view.init' => ['onViewInit', -10],
            'view.system/site/admin/edit' => 'onSiteEdit',
            'view.system/site/admin/settings' => 'onSiteSettings',
            'view.system/widget/edit' => 'onWidgetEdit'
        ];
    }
}
