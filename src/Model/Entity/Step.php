<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Step Entity
 *
 * @property string $id
 * @property string $configurator_id
 * @property string $name
 * @property int $sort
 * @property array $config
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Configurator $configurator
 */
class Step extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'configurator_id' => true,
        'name' => true,
        'sort' => true,
        'config' => true,
        'created' => true,
        'modified' => true,
        'configurator' => true
    ];
}
