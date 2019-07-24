<?php
namespace ARC\ProductConfigurator\Model\Entity;

use ARC\ProductConfigurator\Model\Json\Component as ComponentJson;
use ARC\ProductConfigurator\Model\Json\ComponentCollection;
use ARC\ProductConfigurator\Model\Table\ImagesTable;
use Cake\Database\Expression\IdentifierExpression;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Exception;

/**
 * Build Entity
 *
 * @property string $id
 * @property array $components
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property array $extra
 */
class Build extends Entity
{
    use LocatorAwareTrait;

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
        'components' => true,
        'created' => true,
        'modified' => true,
        'extra' => true,
    ];

    /**
     * @var array
     */
    protected $_virtual = [
        'images',
    ];

    /**
     * Returns array of images that match selected options
     *
     * @return array
     */
    public function _getImages()
    {
        /** @var ImagesTable $ImagesTable */
        $ImagesTable = $this->getTableLocator()->get('ARC/ProductConfigurator.Images');

        $return = [];
        $componentCollection = new ComponentCollection();
        foreach ($this->components as $component) {
            if (!$component instanceof ComponentJson) {
                $component = ComponentJson::fromArray($componentCollection, $component);
            }

            try {
                $template = $component->getImageTemplate();
            } catch (Exception $exception) {
                // don't bother looking for an image for component if the mask is invalid or not all selections are made
                continue;
            }

            $images = $ImagesTable
                ->find()
                ->select([
                    'position',
                    'name',
                    'layer',
                ])
                ->where([
                    '"' . $template . '" REGEXP' => new IdentifierExpression('mask'),
                ])
                ->enableHydration(false)
                ->groupBy('position')
                ->map(function ($imagesByPosition) use ($component) {
                    return [
                        'component' => $component->getId(),
                        'path' => $imagesByPosition[0]['name'],
                        'layer' => $imagesByPosition[0]['layer'],
                    ];
                })
                ->toArray();

            if ($images) {
                $return[] = $images;
            }
        }

        return $return;
    }
}
