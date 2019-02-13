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
    private $fieldsets = null;
    private $theme = null;

    public function main(Application $app)
    {
        $this->fs = $app->file();
        $this->locator = $app->locator();
        $this->module = $app->module();
    }

    private function fieldsets()
    {
        if ($this->fieldsets) return $this->fieldsets;

        foreach (['theme-kit:fieldsets/', 'theme:fieldsets/'] as $path) {
            if ($path = $this->locator->get($path)) {
                $files = $this->fs->listDir($path);
                foreach ($files as $name) {
                    $parts = explode('.', $name);
                    if (count($parts) == 2 && $parts[1] == 'json') {
                        $json = json_decode(file_get_contents($path.$name), true);
                        if (isset($this->fieldsets[$parts[0]])) {
                            $this->fieldsets[$parts[0]] = array_merge($json, $this->fieldsets[$parts[0]]);
                        }
                        else $this->fieldsets[$parts[0]] = $json;
                    }
                }
            }
        }

        return $this->fieldsets;
    }

    private function load(string $key, bool $doInherit = true)
    {
        $config = $this->theme->get($key, []);

        // merge fieldsets into config
        foreach ($config as $name => $form) {
            $config[$name]['fieldsets'] = array_intersect_key($this->fieldsets(), array_flip($form['fieldsets']));
        }

        if ($doInherit && $this->theme->get('settings-theme', false)) {
            // add inherit key to fieldsets
            foreach($config as $f => $form) {
                foreach ($form['fieldsets'] as $fs => $fieldset) {
                    if (in_array($fs, $this->theme->get("settings-theme.$f.fieldsets", []))) {
                        $config[$f]['fieldsets'][$fs]['inherit'] = [
                            'label' => $this->theme->get("settings-theme.$f.label").' > '.$this->fieldsets[$fs]['label'],
                            'path' => "$f.$fs"
                        ];
                    }
                    else if (in_array($fs, $this->theme->get("settings-theme.defaults.fieldsets", []))) {
                        $config[$f]['fieldsets'][$fs]['inherit'] = [
                            'label' => $this->theme->get("settings-theme.defaults.label").' > '.$this->fieldsets[$fs]['label'],
                            'path' => "defaults.$fs"
                        ];
                    }
                }
            }
        }

        return $config;
    }

    public function onViewInit($event, $view)
    {
        $view->addHelpers([
            new ValuesHelper($this)
        ]);

        // $view->map('layout', 'theme-kit/template.php');
    }


    public function onSiteEdit($event, $view)
    {
        $view->data('$config', $this->load('node-theme'));

        $view->script('node-theme', 'theme-kit:app/bundle/node-theme.js', 'site-edit');
    }

    public function onSiteSettings($event, $view)
    {
        $view->data('$config', $this->load('settings-theme', false));
        $view->data('$themeKit', $this->config());

        $view->script('settings-theme-kit', 'theme-kit:app/bundle/settings-theme-kit.js', 'site-settings');
    }

    public function onWidgetEdit($event, $view)
    {
        $view->data('$config', $this->load('widget-theme'));

        $view->script('widget-theme', 'theme-kit:app/bundle/widget-theme.js', 'widget-edit');
    }

    public function subscribe()
    {
        // search for a module which require theme-kit
        foreach ($this->module as $module) {
            if ($module->get('require', '') == 'theme-kit') {
                $this->theme = $module;
                continue;
            }
        }

        if (!$this->theme) return;

        return [
            'view.init' => ['onViewInit', -10],
            'view.system/site/admin/edit' => 'onSiteEdit',
            'view.system/site/admin/settings' => 'onSiteSettings',
            'view.system/widget/edit' => 'onWidgetEdit'
        ];
    }
}