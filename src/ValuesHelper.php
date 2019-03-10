<?php

namespace SAB\ThemeKit;

use Pagekit\View\View;
use Pagekit\View\Helper\Helper;
use Pagekit\Util\Arr;
use Pagekit\Util\ArrObject;
use Pagekit\Application as App;
use Pagekit\Widget\Model\Widget;
use Pagekit\Site\Model\Node;


class ValuesHelper extends Helper
{
    private $values;
    private $defaults;
    private $nodeValues;

    public function register(View $view)
    {
        $this->view = $view;
        $this->defaults = $view->app->config('theme-kit');
        $this->nodeValues = $this->doInherit('node', $view->app->node()->theme);
        $this->useNode();
    }

    public function doInherit(string $prefix, array $values): ArrObject
    {
        // inherit forms
        foreach ($values as $f => &$form) {
            if ($f == 'main') App::log()->debug(json_encode($form));
            if (Arr::get($form, 'inherit', true)) {
                $form = $this->defaults->get("$prefix.$f", []);
            } else {
                unset($form['inherit']);
            }
            if ($f == 'main') App::log()->debug(json_encode($form));
            foreach ($form as $fs => &$fieldset) {
                if (Arr::get($fieldset, 'inherit', true)) {
                    $fieldset = $this->defaults->get("fieldsets.defaults.$fs", []);
                } else {
                    unset($fieldset['inherit']);
                }
            }
            if ($f == 'main') App::log()->debug(json_encode($form));
        }
        unset($form); unset($fieldset);

        return new ArrObject($values);
    }

    public function useNode(Node $node = null): void
    {
        if ($node) {
            $this->values = $this->doInherit('node', $node->theme);
        }
        else {
            $this->values = $this->nodeValues;
        }
    }

    public function useWidget(Widget $widget): void
    {
        $this->values = $this->doInherit('widget', $widget->theme);
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
