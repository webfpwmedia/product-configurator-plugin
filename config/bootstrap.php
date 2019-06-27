<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

// merge user configuration
$userConfiguration = Configure::read('ARC.ProductConfigurator');
Configure::load('ARC/ProductConfigurator.config');
$config = Configure::read('ARC.ProductConfigurator');
Configure::write('ARC.ProductConfigurator', Hash::merge($config, $userConfiguration));

ConnectionManager::setConfig('configurator', [
    'url' => 'mysql://root:root@mysql/configurator?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false',
]);
