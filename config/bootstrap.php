<?php

use Cake\Core\Configure;

$config = [
    'common' => [
        'copyright' => 'Arc Point Group',
        'name' => 'Product Configurator',
        'website' => 'https://www.arcpointgroup.com/',
    ],

    'date' => [
        'default' => 'F j, Y g:ia',
    ],

    'meta' => [
        'description' => 'Arc Point Group product configurator plugin for CakePHP.',
    ],
];

Configure::write('ProductConfigurator', $config);
