<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

Configure::load('ARC.ProductConfigurator', 'config', true);

ConnectionManager::setConfig('configurator', [
    'url' => 'mysql://root:root@mysql/configurator?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false',
]);
