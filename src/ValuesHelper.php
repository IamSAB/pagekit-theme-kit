<?php

namespace SAB\ThemeKit;

use Pagekit\View\View;
use Pagekit\View\Helper\Helper;
use Pagekit\Util\Arr;
use Pagekit\Util\ArrObject;
use Pagekit\Application as App;


class ValuesHelper extends Helper
{
    private $values;
    private $defaults;
    private $params;

    function __construct(ThemeKit $kit)
    {
        $this->defaults = new ArrObject($kit->config());
    }

    public function register(View $view)
    {
        $this->view = $view;
        $this->params = $this->doInherit($view->app->node()->theme);
        $this->useParams();
    }

    public function doInherit(array $values): ArrObject
    {
        foreach ($values as $f => $form) {
            foreach ($form as $fs => $fieldset) {
                if (isset($fieldset['inherit'])) {
                    if ($fieldset['inherit']['enabled']) {
                        $values[$f][$fs] = $this->defaults->get($fieldset['inherit']['path'], []);
                    }
                    else unset($values[$f][$fs]['inherit']);
                }
            }
        }
        return new ArrObject($values);
    }

    public function use(array $values)
    {
        $this->values = $this->doInherit($values);
    }

    public function useParams()
    {
        $this->values = $this->params;
    }

    public function has(string $path)
    {
        return $this->values->has($path);
    }

    public function get(string $path, $default = '')
    {
        return $this->values->get($path, $default);
    }

    function __invoke(string $path, string $default = '')
    {
        return $this->get($path, $default);
    }

    public function class($sources, string $class = '', bool $or = false)
    {
        if (!is_array($sources)) $sources = [$sources];

        $paths = [];

        foreach ($sources as $source) {
            $parts = explode(':', $source);
            if (isset($parts[1])) {
                foreach (explode(',', $parts[1]) as $key) {
                    $paths[] = $parts[0].'.'.$key;
                }
            }
            else {
                $paths[] = $parts[0];
            }
        }

        $classes = [];

        foreach ($paths as $path) {
            $res = $this->values->get($path, []);
            switch (count(explode('.', $path))) {
                // form values
                case 1: $classes = Arr::merge($classes, array_values(Arr::flatten($res))); break;
                // fieldset values
                case 2: $classes = Arr::merge($classes, array_values($res)); break;
                // field value
                case 3: $classes = Arr::merge($classes, is_array($res) ? $res : [$res]); break;
                // non-matching path
                default: break;
            }
        }

        $classes = implode(' ', $classes);

        if ($or) $classes = $classes ? $classes : $class;
        else $classes = $classes ? $class.' '.$classes : $class;

        return $classes ? "class=\"$classes\"" : '';
    }

    public function attr(string $attr, string $path, bool $checkEnable = true, string $value = '')
    {
        // TODO allow selecting keys->values for attr

        if (substr_count($path, '.') != 1) return 'invalid-path';

        $arr = $this->values->get($path, []);

        // attribute should only be rendered, if its enabled (needed as most attributes do something without a value)
        if ($checkEnable) {
            if (Arr::has($arr, '_enable')) {
                if (!Arr::get($arr, '_enable', false)) return '';
                else unset($arr['_enable']);
            }
            else return '';
        }

        foreach ($arr as $key => $val) {
            if (is_bool($val)) $val = $val ? 'true' : 'false';
            if ($val) $value .= "$key:$val;";
        }

        return "$attr=\"$value\"";
    }

    public function video(string $path)
    {
        if (substr_count($path, '.') != 2) return "invalid-path";

        $src = $this->values->get($path, '');
        return $src ? "src=\"{$this->view->url($src)}\" uk-video" : '';
    }

    public function image(string $path)
    {
        if (substr_count($path, '.') != 2) return 'invalid-path';

        $src = $this->values->get($path, '');
        return $src ? "data-src=\"{$this->view->url($src)}\" uk-img" : '';
    }

    public function src(string $path)
    {
        if (substr_count($path, '.') != 2) return 'invalid-path';

        $src = $this->values->get($path, '');
        return $src ? "src=\"{$this->view->url($src)}\"" : '';
    }

    public function icon(string $path, $ratio = 1)
    {
        $icon = $this->values->get($path);
        return $icon ? "uk-icon=\"icon:$icon;ratio:$ratio\"" : '';
    }

    public function raw($path)
    {
        return json_encode($this->values->get($path));
    }

    public function getName()
    {
        return 'values';
    }
}
