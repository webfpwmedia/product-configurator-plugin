<?php
/**
 * @var \ARC\ProductConfigurator\View\AppView $this
 */

use Cake\Core\Configure;

?>

<!doctype html>
<html class="no-js h-100" lang="en">
    <head>
        <?= $this->Html->charset() ?>
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>
            <?= __(Configure::read('ARC.ProductConfigurator.common.name')) .
                ' | ' .
                __($this->fetch('title')) ?>

            <?= $this->fetch('subtitle')
                ? '(' . __($this->fetch('subtitle')) . ')'
                : null ?>
        </title>

        <meta name="description" content="<?= __(Configure::read('ARC.ProductConfigurator.meta.description')) ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <?= $this->elementHook('layoutMetaTags') ?>

        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <?php

        echo $this->fetch('cssPreApp');

        echo $this->Html->css('ARC/ProductConfigurator.app');

        echo $this->fetch('cssPostApp');

        ?>
    </head>
    <body class="h-100">
        <div class="container-fluid">
            <div class="row">
                <!-- Main Sidebar -->
                <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
                    <div class="main-navbar">
                        <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
                            <a class="navbar-brand w-100 mr-0" href="<?= $this->Url->build('/') ?>" style="line-height: 25px;">
                                <div class="d-table m-auto">
                                    <span class="d-md-inline ml-1">
                                        <?= __(Configure::read('ARC.ProductConfigurator.common.name')) ?>
                                    </span>
                                </div>
                            </a>
                            <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
                                <i class="material-icons">&#xE5C4;</i>
                            </a>
                        </nav>
                    </div>

                    <div class="nav-wrapper">
                        <?= $this->elementHook('layoutNavPre') ?>

                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $this->Url->build(
                                        ['plugin' => 'ARC/ProductConfigurator', 'controller' => 'Configurators', 'action' => 'index']) ?>">

                                    <i class="material-icons">settings</i>
                                    <span><?= __('Configurators') ?></span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= $this->Url->build(
                                        ['plugin' => 'ARC/ProductConfigurator', 'controller' => 'Components', 'action' => 'index']) ?>">

                                    <i class="material-icons">extension</i>
                                    <span><?= __('Components') ?></span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= $this->Url->build(
                                        ['plugin' => 'ARC/ProductConfigurator', 'controller' => 'Images', 'action' => 'index']) ?>">

                                    <i class="material-icons">image</i>
                                    <span><?= __('Image Index') ?></span>
                                </a>
                            </li>
                        </ul>

                        <?= $this->elementHook('layoutNavPost') ?>
                    </div>
                </aside>
                <!-- End Main Sidebar -->

                <main class="main-content col-lg-10 col-md-9 col-sm-12 p-0 offset-lg-2 offset-md-3">
                    <div class="main-navbar sticky-top bg-white">
                        <!-- Main Navbar -->
                        <nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0 flex-row-reverse">
                            <?= $this->elementHook('layoutNavTop') ?>

                            <nav class="nav">
                                <a href="#" class="nav-link nav-link-icon toggle-sidebar d-inline d-md-none text-center border-left" data-toggle="collapse" data-target=".header-navbar" aria-expanded="false" aria-controls="header-navbar">
                                    <i class="material-icons"></i>
                                </a>
                            </nav>
                        </nav>
                    </div>

                    <?= $this->Flash->render() ?>

                    <div class="main-content-container container-fluid px-4">
                        <?= $this->elementHook('layoutHeader') ?>

                        <?= $this->fetch('content') ?>
                    </div>

                    <footer class="main-footer d-flex p-2 px-3 bg-white border-top">
                        <span class="copyright ml-auto my-auto mr-2">
                            <?= __('Copyright') ?> &copy; <?= date('Y') ?>
                            <a href="<?= Configure::read('ARC.ProductConfigurator.common.website') ?>" rel="nofollow">
                                <?= Configure::read('ARC.ProductConfigurator.common.copyright') ?>
                            </a>
                        </span>
                    </footer>
                </main>
            </div>
        </div>

        <?php

        echo $this->fetch('jsPreApp');

        echo $this->Html->script('ARC/ProductConfigurator.dist/arc-product-configurator.bundle');

        echo $this->fetch('jsPostApp');

        ?>
    </body>
</html>
