<?php
namespace ARC\ProductConfigurator\Model\Table;

use ARC\ProductConfigurator\Mask\TokensMissingException;
use ARC\ProductConfigurator\Model\Json\Component;
use ARC\ProductConfigurator\Model\Json\ComponentCollection;
use ARC\ProductConfigurator\Model\Json\OptionSet;
use ARC\ProductConfigurator\ORM\Table;
use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validation;
use Cake\Validation\Validator;

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
     * Name of input holding text labels
     */
    const TEXT_INPUT = '__text';

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

        $this->belongsTo('ARC/ProductConfigurator.Configurators');
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
        $componentCollection = new ComponentCollection();

        $selections = collection($data->getArrayCopy())
            ->filter(function ($value, $key) {
                return Validation::uuid($key) && is_array($value);
            })
            ->toArray();

        collection($selections)
            ->filter(function ($componentSelections) {
                return !isset($componentSelections[self::TOGGLE_INPUT]) || $componentSelections[self::TOGGLE_INPUT];
            })
            ->each(function ($componentSelections, $componentId) use ($componentCollection) {
                $component = $componentCollection->getComponent($componentId);
                if (!$component) {
                    $component = new Component($componentCollection, $componentId);
                    $componentCollection->addComponent($component);
                }

                if (isset($componentSelections[self::QTY_INPUT])) {
                    $component->setQty((int)$componentSelections[self::QTY_INPUT]);
                    unset($componentSelections[self::QTY_INPUT]);
                }

                // check for custom text label
                if (isset($componentSelections[self::TEXT_INPUT])) {
                    $component->addText($componentSelections[self::TEXT_INPUT]);
                    unset($componentSelections[self::TEXT_INPUT]);
                    unset($componentSelections[self::CUSTOM_TEXT_INPUT]);
                }

                unset($componentSelections[self::TOGGLE_INPUT]);
                $component->addSelections($componentSelections);
            });

        collection($componentCollection->getComponents())
            ->each(function (Component $component) use ($componentCollection) {
                foreach ($component->getOptions() as $optionSet) {
                    $inherits = $optionSet->getInherits();
                    if (!$inherits) {
                        continue;
                    }
                    $inheritOptions = $optionSet->getInheritsOptions();
                    if ($inheritOptions['showOptions']) {
                        continue;
                    }

                    $id = key($inherits);
                    if ($id === OptionSet::SELF) {
                        $id = $component->getId();
                    }

                    $inheritedComponent = $componentCollection->getComponent($id);
                    if (!$inheritedComponent) {
                        $componentCollection->removeComponent($component);
                    }

                    $component->addSelections([
                        $optionSet->getToken() => $inheritedComponent->getSelection($optionSet->getToken())
                    ]);
                }
            });

        collection($componentCollection->getComponents())
            ->each(function (Component $component) use ($componentCollection) {
                try {
                    $component->getOptionTemplate();
                } catch (TokensMissingException $exception) {
                    $componentCollection->removeComponent($component);
                }
            });

        collection($componentCollection->getComponents())
            ->each(function (Component $component) use ($componentCollection) {
                foreach ($component->getSelections() as $token => $selection) {
                    $optionSet = $component->getOptionSet($token);
                    $requires = $optionSet->getRequires();

                    if (!$requires) {
                        continue;
                    }

                    $id = key($requires);

                    if ($id === OptionSet::SELF) {
                        $id = $component->getId();
                    }

                    $requiredComponent = $componentCollection->getComponent($id);

                    if (!$requiredComponent) {
                        $componentCollection->removeComponent($component);

                        continue;
                    }

                    if (empty($requiredComponent->getSelection(current($requires)))) {
                        $componentCollection->removeComponent($component);
                    }
                }
            });

        $data['components'] = $componentCollection->getComponents();
    }
}
