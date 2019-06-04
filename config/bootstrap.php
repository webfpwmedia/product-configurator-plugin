<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

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

ConnectionManager::setConfig('configurator', [
    'url' => 'mysql://root:root@mysql/configurator?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false',
]);
