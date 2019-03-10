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

    private function node(): array
    {
        $config = $this->theme->get('node-theme', []);
        $config['general'] = [
            'label' => 'General',
            'categories' => ['Site'],
            'fieldsets' => ['node', 'heading', 'inverse']
        ];
        return $config;
    }

    private function widget(): array
    {
        $config = $this->theme->get('widget-theme', []);
        $config['widget'] = [
            'label' => 'Widget',
            'fieldsets' => ['heading', 'text', 'visibility', 'inverse', 'custom'],
        ];
        return $config;
    }

    private function process(array $forms, $inheritForm = true, $inheritFieldset = true)
    {
        if (empty($this->fieldsets)) {
            $this->loadFieldsetsFromDirectory('theme-kit:fieldsets');
            $this->loadFieldsetsFromDirectory('theme:fieldsets');
        }

        foreach ($forms as &$form) {
            $form['fieldsets'] = array_intersect_key($this->fieldsets, array_flip($form['fieldsets']));
            if (!isset($form['inherit'])) $form['inherit'] = $inheritForm;
            foreach ($form['fieldsets'] as &$fieldset) {
                if (!isset($fieldset['inherit'])) $fieldset['inherit'] = $inheritFieldset;
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
        $view->data('$config', $this->process($this->node()));
        $view->script('node-theme', 'theme-kit:app/bundle/node-theme.js', 'site-edit');
    }

    public function onSiteSettings($event, $view)
    {
        $view->data('$themeKit', $this->config());
        $fieldsets = [];

        foreach (['node', 'widget'] as $ui) {
            $config = $this->{$ui}();
            foreach($config as $f => $form) {
                if (isset($form['inherit']) && !$form['inherit']) unset($config[$f]);
                $fieldsets = array_merge($form['fieldsets'], $fieldsets);
            }
            $view->data('$config'.ucfirst($ui), $this->process($config, false));
            $view->script('settings-theme-'.$ui, 'theme-kit:app/bundle/settings-theme-'.$ui.'.js', 'site-settings');
        }

        $view->data('$configFieldsets', $this->process([
            'defaults' => [
                'label' => 'Defaults',
                'fieldsets' => $fieldsets
            ]
            ], false, false));
        $view->script('settings-theme-fieldsets', 'theme-kit:app/bundle/settings-theme-fieldsets.js', 'site-settings');
    }

    public function onWidgetEdit($event, $view)
    {
        $view->data('$config', $this->process($this->widget()));
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
