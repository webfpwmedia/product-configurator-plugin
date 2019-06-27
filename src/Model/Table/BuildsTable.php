<?php
namespace ARC\ProductConfigurator\Model\Table;

use ARC\ProductConfigurator\ORM\Table;
use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Cake\View\StringTemplate;

/**
 * Builds Model
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Build get($primaryKey, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Build newEntity($data = null, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Build[] newEntities(array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Build|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Build saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Build patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Build[] patchEntities($entities, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Build findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BuildsTable extends Table
{
    use LocatorAwareTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('builds');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->requirePresence('images', 'create')
            ->isArray('images')
            ->allowEmptyArray('images', false);

        $validator
            ->requirePresence('components', 'create')
            ->isArray('components')
            ->allowEmptyArray('components', false);

        return $validator;
    }

    /**
     * beforeMarshal
     *
     * @param Event $event Event
     * @param ArrayObject $data Patch data
     * @param ArrayObject $options Patch options
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        $selections = $data->getArrayCopy();
        $selections = collection($selections)
            ->map(function ($componentSelections) use ($selections) {
                return $this->__withInheritance($selections, $componentSelections);
            })
            ->toArray();

        $data['images'] = $this->__generateImageJson($selections);
        $data['components'] = $selections;
    }

    /**
     * Returns array of images that match selected options
     *
     * @param array $selections Selections by component ID
     * @return array
     */
    private function __generateImageJson(array $selections) : array
    {
        /** @var ImagesTable $ImagesTable */
        $ImagesTable = $this->getTableLocator()->get('ARC/ProductConfigurator.Images');
        /** @var ComponentsTable $ComponentsTable */
        $ComponentsTable = $this->getTableLocator()->get('ARC/ProductConfigurator.Components');

        $return = [];
        foreach ($selections as $componentId => $selection) {
            $component = $ComponentsTable->get($componentId);

            $stringTemplate = new StringTemplate(['mask' => $component->image_mask]);

            $images = $ImagesTable
                ->find()
                ->select([
                    'position',
                    'name',
                    'layer',
                ])
                ->where([
                    'mask' => $stringTemplate->format('mask', $selection)
                ])
                ->enableHydration(false)
                ->groupBy('position')
                ->map(function ($imagesByPosition) {
                    return [
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

    /**
     * Replaces inherited attributes with their submitted values
     *
     * @param array $data Full submitted data for all components
     * @param array $selections User selections for this component
     * @return array
     */
    private function __withInheritance($data, $selections)
    {
        foreach ($selections as $token => $selection) {
            if (stripos($selection, 'inherits:') === false) {
                continue;
            }

            list(, $inheritableComponentId, $inheritableToken) = explode(':', $selection);

            $selections[$token] = Hash::get($data, "$inheritableComponentId.$inheritableToken");
        }

        return $selections;
    }
}
