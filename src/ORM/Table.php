<?php
namespace ARC\ProductConfigurator\ORM;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

/**
 * Class Table.
 *
 * @package ARC\ProductConfigurator\Model\Table
 */
class Table extends \Cake\ORM\Table
{

    /**
     * initialize.
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
    }

    /**
     * defaultConnectionName.
     *
     * @return string
     */
    public static function defaultConnectionName() {
        return ConnectionManager::get(Configure::read('ARC.ProductConfigurator.connection'));
    }
}
