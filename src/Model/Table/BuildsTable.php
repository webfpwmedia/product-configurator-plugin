<?php
namespace ARC\ProductConfigurator\Model\Table;

use ARC\ProductConfigurator\Mask\TokensMissingException;
use ARC\ProductConfigurator\Model\Json\Component;
use ARC\ProductConfigurator\Model\Json\ComponentCollection;
use ARC\ProductConfigurator\ORM\Table;
use ArrayObject;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Event\Event;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;
use Cake\Validation\Validation;
use Cake\Validation\Validator;
use Exception;

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
     * Name of input holding custom user text
     */
    const CUSTOM_TEXT_INPUT = '__customtext';

    /**
     * Name of input holding qty
     */
    const QTY_INPUT = '__qty';

    /**
     * Name of toggle checkbox
     */
    const TOGGLE_INPUT = '__toggle';

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

        $validator
            ->requirePresence('extra', 'create')
            ->isArray('extra')
            ->allowEmptyArray('extra', true);

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
        $selections = collection($data->getArrayCopy())
            ->filter(function ($value, $key) {
                return Validation::uuid($key) && is_array($value);
            })
            ->toArray();
        $components = collection($selections)
            ->filter(function ($componentSelections) {
                return !isset($componentSelections[self::TOGGLE_INPUT]) || $componentSelections[self::TOGGLE_INPUT];
            })
            ->map(function ($componentSelections, $componentId) {
                $component = new Component(new ComponentCollection(), $componentId);

                if (isset($componentSelections[self::QTY_INPUT])) {
                    $component->setQty((int)$componentSelections[self::QTY_INPUT]);
                    unset($componentSelections[self::QTY_INPUT]);
                }

                unset($componentSelections[self::TOGGLE_INPUT]);
                $component->addSelections($componentSelections);

                // check for custom text label
                if (!empty($componentSelections[self::CUSTOM_TEXT_INPUT])) {
                    $component->addText($componentSelections[self::CUSTOM_TEXT_INPUT]);
                }

                return $component;
            })
            ->filter(function (Component $component) {
                try {
                    $component->getOptionTemplate();
                } catch (TokensMissingException $exception) {
                    // don't include this component if tokens are missing from selections
                    return false;
                }

                return true;
            })
            ->toList();

        $data['images'] = $this->__generateImageJson($components);
        $data['components'] = $components;
    }

    /**
     * Returns array of images that match selected options
     *
     * @param Component[] $components Components
     * @return array
     */
    private function __generateImageJson(array $components) : array
    {
        /** @var ImagesTable $ImagesTable */
        $ImagesTable = $this->getTableLocator()->get('ARC/ProductConfigurator.Images');

        $return = [];
        foreach ($components as $component) {
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
