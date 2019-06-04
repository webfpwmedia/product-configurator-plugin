<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

Configure::load('config', 'default', true);

ConnectionManager::setConfig('configurator', [
    'url' => 'mysql://root:root@mysql/configurator?encoding=utf8&timezone=UTC&cacheMetadata=true&quoteIdentifiers=false&persistent=false',
]);
