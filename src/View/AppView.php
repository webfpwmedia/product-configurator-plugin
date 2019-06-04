<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace ARC\ProductConfigurator\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application’s default view class
 *
 * @link https://book.cakephp.org/3.0/en/views.html#the-app-view
 */
class AppView extends View
{

    /**
     * initialize.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        # Bootstrap 4 templates.
        $this->loadHelper('Form', [
            'templates' => 'bootstrap4',
            'widgets' => [
                'json' => ['ARC/ProductConfigurator.Json'],
            ],
        ]);
    }
}
