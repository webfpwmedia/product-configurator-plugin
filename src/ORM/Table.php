<?php
namespace ARC\ProductConfigurator\ORM;

/**
 * Class Table.
 *
 * @package ARC\ProductConfigurator\Model\Table
 */
class Table extends \Cake\ORM\Table
{
    /**
     * defaultConnectionName.
     *
     * @return string
     */
    public static function defaultConnectionName() {
        return 'configurator';
    }
}
