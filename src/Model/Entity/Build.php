<?php
namespace ARC\ProductConfigurator\Model\Entity;

use Cake\ORM\Entity;

/**
 * Build Entity
 *
 * @property string $id
 * @property array $images
 * @property array $components
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property array $extra
 */
class Build extends Entity
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
        'images' => true,
        'components' => true,
        'created' => true,
        'modified' => true,
        'extra' => true,
    ];
}
