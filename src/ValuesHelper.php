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
        App::log()->debug(json_encode($values));
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
                case 1: $classes += array_values(Arr::flatten($res)); break;
                case 2: $classes += array_values($res); break;
                case 3: $classes += is_array($res) ? $res : [$res]; break;
                default: break;
            }
        }

        $classes = implode(' ', $classes);
        $classes = !$classes && $or ? $class : $class.( $classes ? ' ' : '').$classes;

        return $classes ? "class=\"$classes\"" : '';
    }

    // public function class($paths, string $class = '', bool $orValue = false)
    // {
    //     if (!is_array($paths)) $paths = [$paths];

    //     foreach ($paths as $arg1 => $arg2) {

    //         if (is_numeric($arg1)) {
    //             $res = $this->values->get($arg2, []);
    //             if (is_array($res)) {
    //                 $values = array_values(
    //                     Arr::flatten($res)
    //                 );
    //             }
    //             else {
    //                 $values = [$res];
    //             }
    //         }
    //         else {
    //             $res = $this->values->get($arg1, []);
    //             if (is_array($res)) {
    //                 $values = array_values(
    //                     Arr::flatten(
    //                         array_intersect_key(
    //                             $res,
    //                             array_flip($arg2)
    //                         )
    //                     )
    //                 );
    //             }
    //             else {
    //                 $values = [$res];
    //             }
    //         }

    //         $class .= ($class ? ' ' : '') . implode(' ', $values);
    //     }

    //     return $class ? "class=\"$class\"" : '';
    // }

    public function attr(string $attr, string $path, string $value = '', bool $renderIfNoValue = false)
    {
        if (count(explode('.', $path)) != 2) return "INVALID_PATH";

        $arr = $this->values->get($path, []);

        foreach ($arr as $key => $val) {
            if (is_bool($val)) $val = $val ? 'true' : 'false';
            $value .= "$key:$val;";
        }

        return ($value || $renderIfNoValue) ? "$attr=\"$value\"" : '';
    }

    public function video(string $path)
    {
        if (count(explode('.', $path)) != 3) return "INVALID_PATH";

        $src = $this->values->get($path, '');
        return $src ? "src=\"{$this->view->url($src)}\" uk-video" : '';
    }

    public function image(string $path)
    {
        if (count(explode('.', $path)) != 3) return 'INVALID_PATH';

        $src = $this->values->get($path, '');
        return $src ? "data-src=\"{$this->view->url($src)}\" uk-img" : '';
    }

    public function src(string $path)
    {
        if (count(explode('.', $path)) != 3) return 'INVALID_PATH';

        $src = $this->values->get($path, '');
        return $src ? "src=\"{$this->view->url($src)}\"" : '';
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