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

    public function parse(string $expr)
    {
        $matches = [];

        if (preg_match('/^([a-zA-Z0-9\.]+)$/', $expr)) {
            return [$expr, 'all', null];
        }
        // $path:$keys
        else if (preg_match('/^([a-zA-Z0-9\.]+)(:|:!)([a-zA-Z0-9\,]+)$/', $expr, $matches)) {
            array_shift($matches);
            return $matches;
        }

        // $path $prop ? $val_i:$keys_i [...]
        // checks if $prop in $path is equal $val_i, then takes $key_i
        else if (preg_match('/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\s([a-zA-Z0-9]+)\s\?\s(([a-zA-Z0-9]+(:|:!)[a-zA-Z0-9,]+\s?)+)/', $expr, $matches)) {
            $value = $this->values->get($matches[1].'.'.$matches[2]);
            foreach(explode(' ', $matches[3]) as $_expr) {
                $parts = [];
                if (preg_match('/^([a-zA-Z0-9]+)(:|:!)([a-zA-Z0-9\,]+)$/', $_expr, $parts)) {
                    switch ($parts[1]) {
                        case 'true': $a = true; break;
                        case 'false': $a = false; break;
                        default: $a = $parts[1]; break;
                    }
                    if ($a == $value) {
                        return [$matches[1], $parts[2], $parts[3]];
                    }
                }
            }
        }

        return ['','',''];
    }

    public function class($sources, string $class = '', bool $or = false)
    {
        if (!is_array($sources)) $sources = [$sources];

        $classes = [];

        foreach ($sources as $expr) {
            $parts = $this->parse($expr);
            $res = $this->values->get($parts[0], []);
            switch ($parts[1]) {
                case 'all':
                    switch (substr_count($parts[0],'.')) {
                        case 0:
                            $classes = Arr::merge($classes, array_values(Arr::flatten($res)));
                            break;
                        case 1:
                            $classes = Arr::merge($classes, array_values($res));
                            break;
                        default:
                            break;
                    }
                    break;
                case ':':
                    $classes = Arr::merge($classes, array_values(Arr::extract($res, explode(',', $parts[2]))));
                    break;

                case ':!':
                    Arr::remove($res, explode(',', $parts[2]));
                    $classes = Arr::merge($classes, array_values($res));
                    break;
            }
        }

        $classes = implode(' ', $classes);

        if ($or) $classes = $classes ? $classes : $class;
        else $classes = $classes ? $class.' '.$classes : $class;

        return $classes ? "class=\"$classes\"" : '';
    }

    public function attr(string $attr, string $expr, bool $render = true, string $default = '')
    {
        $parse = $this->parse($expr);
        $arr = $this->values->get($parse[0], []);
        $value = '';

        switch ($parse[1]) {
            case ':':
                $arr = Arr::extract($arr, explode(',', $parse[2]));
                break;
            case ':!':
                Arr::remove($arr, explode(',', $parse[2]));
                break;
            default:
                break;
        }
        foreach ($arr as $key => $val) {
            if (is_bool($val)) $val = $val ? 'true' : 'false';
            if ($val) $value .= "$key:$val;";
        }

        if ($value || $render) {
            $value = $default.$value;
            return "$attr=\"$value\"";
        }
        else return '';
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
