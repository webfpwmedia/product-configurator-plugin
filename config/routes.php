<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

Router::plugin('ARC/ProductConfigurator', ['path' => '/arc-product-configurator'], function (RouteBuilder $routes) {
    $routes->fallbacks(DashedRoute::class);
});
