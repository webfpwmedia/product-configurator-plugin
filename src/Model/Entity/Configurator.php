<?php
namespace ARC\ProductConfigurator\Model\Entity;

use Cake\ORM\Entity;

/**
 * Configurator Entity
 *
 * @property string $id
 * @property string $name
 * @property array $bootstrap
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int $width
 * @property int $height
 *
 * @property \ARC\ProductConfigurator\Model\Entity\Component[] $components
 * @property \ARC\ProductConfigurator\Model\Entity\Step[] $steps
 */
class Configurator extends Entity
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
        'name' => true,
        'bootstrap' => true,
        'created' => true,
        'modified' => true,
        'components' => true,
        'steps' => true,
        'width' => true,
        'height' => true,
    ];
}
