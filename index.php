<?php

use Pagekit\Application;
use SAB\ThemeKit\ValuesHelper;

return [

    'name' => 'theme-kit',

    'autoload' => [
        'SAB\\ThemeKit\\' => 'src'
    ],

    'main' => 'SAB\\ThemeKit\\ThemeKit',

    'config' => [
        'defaults' => [],
        'theme' => []
    ]
];