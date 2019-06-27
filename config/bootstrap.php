<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;

// merge user configuration
$userConfiguration = Configure::read('ARC.ProductConfigurator');
Configure::load('ARC/ProductConfigurator.config');
$config = Configure::read('ARC.ProductConfigurator');
Configure::write('ARC.ProductConfigurator', Hash::merge($config, $userConfiguration));
