<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Component Entity
 *
 * @property string $id
 * @property string $configurator_id
 * @property string $name
 * @property string $mask
 * @property string $image_mask
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Configurator $configurator
 */
class Component extends Entity
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
        'mask' => true,
        'image_mask' => true,
        'created' => true,
        'modified' => true,
        'configurator' => true,
    ];
}
