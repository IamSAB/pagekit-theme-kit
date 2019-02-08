<?php

namespace SAB\ThemeKit;

use Pagekit\Module\Module;
use Pagekit\Application;
use Pagekit\Util\Arr;


class ThemeKit extends Module
{
    private $fs;
    private $locator;
    private $module;
    private $fieldsets = [];

    public function main(Application $app)
    {
        $this->fs = $app->file();
        $this->locator = $app->locator();
        $this->module = $app->module();
    }

    private function fieldsets()
    {
        if ($this->fieldsets) return $this->fieldsets;

        foreach (['theme:fieldsets/', 'theme-kit:fieldsets/'] as $path) {
            if ($path = $this->locator->get($path)) {
                $files = $this->fs->listDir($path);
                foreach ($files as $name) {
                    $parts = explode('.', $name);
                    if (count($parts) == 2 && $parts[1] == 'json') {
                        $this->fieldsets[$parts[0]] = json_decode(file_get_contents($path.$name), true);
                    }
                }
            }
        }

        return $this->fieldsets;
    }

    private function load(string $path)
    {
        $config = [];

        // get theme config
        foreach ($this->module as $module) {
            if ($module->get('require', '') == 'theme-kit') {
                $config = $module->get($path, $config);
                continue;
            }
        }

        // merge fieldsets into config
        foreach ($config as $name => $form) {
            $config[$name]['fieldsets'] = array_intersect_key($this->fieldsets(), array_flip($form['fieldsets']));
        }

        return $config;
    }

    public function onViewInit($event, $view)
    {
        $view->addHelpers([
            new ValuesHelper
        ]);

        $view->map('layout', 'theme-kit/template.php');
    }


    public function onSiteEdit($event, $view)
    {
        $view->data('$config', $this->load('node-theme'));
        $view->script('node-theme', 'theme-kit:app/bundle/node-theme.js', 'site-edit');
    }

    public function onSiteSettings($event, $view)
    {
        $view->data('$themeKit', $this->config());

        $view->data('$configTheme', $this->load('settings-theme'));
        $view->script('settings-theme', 'theme-kit:app/bundle/settings-theme.js', 'site-settings');

        $view->data('$configThemeKit', [
            'defaults' => [
                'label' => 'Defaults',
                'fieldsets' => $this->fieldsets()
            ]
        ]);
        $view->script('settings-theme-kit', 'theme-kit:app/bundle/settings-theme-kit.js', 'site-settings');
    }

    public function onWidgetEdit($event, $view)
    {
        $view->data('$config', $this->load('widget-theme'));
        $view->script('widget-theme', 'theme-kit:app/bundle/widget-theme.js', 'widget-edit');
    }



    public function subscribe()
    {
        return [
            'view.init' => ['onViewInit', -10],
            'view.system/site/admin/edit' => 'onSiteEdit',
            'view.system/site/admin/settings' => 'onSiteSettings',
            'view.system/widget/edit' => 'onWidgetEdit'
        ];
    }
}