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

    public function evaluate(string $expr)
    {
        $matches = [];

        $path = '';
        $operator = '';
        $keys = '';

        if (preg_match('/^[a-zA-Z0-9]+\.[a-zA-Z0-9]+\.([a-zA-Z0-9]+)$/', $expr, $matches)) {
            return [$matches[1] => $this->values->get($expr)];
        }
        else if (preg_match('/^([a-zA-Z0-9]+\.[a-zA-Z0-9]+)$/', $expr)) {
            $path = $expr;
        }
        // $path:$keys
        else if (preg_match('/^([a-zA-Z0-9]+\.[a-zA-Z0-9]+)(:|:!)([a-zA-Z0-9\,]+)$/', $expr, $matches)) {
            $path = $matches[1];
            $operator = $matches[2];
            $keys = $matches[3];
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
                        $path = $matches[1];
                        $operator = $parts[2];
                        $keys = $parts[3];
                    }
                }
            }
        }
        else {
            App::log()->warning("Invalid expression $expr");
        }

        $res = $this->values->get($path, []);

        $keys = explode(',', $keys);

        switch ($operator) {
            case ':':
                $res = Arr::extract($res, $keys);
                break;
            case ':!':
                Arr::remove($res, $keys);
                break;
            default:
                break;
        }

        return $res;
    }

    public function class($sources, string $class = '', bool $or = false)
    {
        if (!is_array($sources)) $sources = [$sources];

        $classes = [];

        foreach ($sources as $expr) {
            $classes = Arr::merge($classes, array_values($this->evaluate($expr)));
        }

        $classes = implode(' ', $classes);

        if ($or) $classes = $classes ? $classes : $class;
        else $classes = $classes ? $class.' '.$classes : $class;

        return $classes ? "class=\"$classes\"" : '';
    }

    public function attr(string $attr, string $expr, bool $render = true, string $default = '')
    {
        $value = '';

        foreach ($this->evaluate($expr) as $key => $val) {
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

    public function content(string $content)
    {
        return $this->view->app->content()->applyPlugins($content);
    }

    public function getName()
    {
        return 'values';
    }
}
