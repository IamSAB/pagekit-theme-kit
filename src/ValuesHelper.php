<?php

namespace SAB\ThemeKit;

use Pagekit\View\Helper\Helper;
use Pagekit\Util\ArrObject;
use Pagekit\View\View;
use Pagekit\Application as App;


class ValuesHelper extends Helper
{
    private $values = null;

    public function register(View $view)
    {
        $this->view = $view;
        $this->values = $view->params;
    }

    /**
     * Get values rendered as attribute
     *
     * @param   string|array    $paths Define paths to get the values:
     *
     *                          'main' for all form values
     *                          'main' => ['section', 'container'] for specific fieldset values
     *                          'main.section' for all fieldset values
     *                          'main.section.style' for field value
     *                          'main.section' => ['style', 'size'] for specific fieldset values
     *
     * @param   string          $value Default value to which values are appended
     * @param   string          $attr Attribute to render
     * @return  string
     */
    function __invoke($paths, $value = '', $attr = 'class'): string
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        foreach ($paths as $key => $val) {

            if (is_numeric($key)) {
                $data = (array) $this->values->get($val, []);
                $path = $val;
            }
            else {
                $data = array_intersect_key((array) $this->values->get($key, []), array_flip($val));
                $path = $key;
            }

            $path = explode('.', $path);
            $array_key = end($path);

            $data = $this->flattenRetrieve($array_key, $data);

            if ($attr == 'class') {
                $value .= ' '.implode(' ', array_values($data));
            }
            else {
                foreach ($data as $key => $val) {
                    $value .= "$key: $val; ";
                }
            }
        }

        return $value ? "$attr=\"$value\"" : "";
    }

    public function img(string $path)
    {
        if ($src = $this->values->get($path, false)) {
            return "data-src=\"{$this->view->url($src)}\" uk-img";
        }
    }

    private function flattenRetrieve(string $array_key, array $array, array $result = [])
    {
        if (isset($array['inherit'])) {
            // use defaults if inherit enabled
            if ($array['inherit']) {
                return $this->flattenRetrieve($array_key, $this->view->params->get("defaults.$array_key", []));
            }
            // not needed value
            else {
                unset($array['inherit']);
            }
        }

        // flatten to associative array
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenRetrieve($key, $value, $result));
            }
            else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function has($path)
    {
        return $this->values->has($path);
    }

    public function get($path, $default = '')
    {
        return $this->values->get($path, $default);
    }

    public function use($values)
    {
        $this->values = $values instanceOf ArrObject ? $values : new ArrObject($values);
    }

    public function reset()
    {
        $this->values = $this->view->params;
    }

    public function getName()
    {
        return 'values';
    }
}