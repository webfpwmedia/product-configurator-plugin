<?php
namespace ARC\ProductConfigurator\Model\Table;

use ARC\ProductConfigurator\Model\Entity\Configurator;
use ARC\ProductConfigurator\Model\Json\Component;
use ARC\ProductConfigurator\Model\Json\StepCollection;
use ARC\ProductConfigurator\ORM\Table;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Validation\Validator;

/**
 * Configurators Model
 *
 * @property \ARC\ProductConfigurator\Model\Table\StepsTable|\Cake\ORM\Association\HasMany $Steps
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator get($primaryKey, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator newEntity($data = null, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator[] newEntities(array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator[] patchEntities($entities, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ConfiguratorsTable extends Table
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

        $this->setTable('configurators');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this
            ->addBehavior('ARC/ProductConfigurator.Json', [
                'fields' => ['bootstrap'],
            ]);

        $this->hasMany('Steps', [
            'foreignKey' => 'configurator_id',
            'className' => 'ARC/ProductConfigurator.Steps'
        ]);
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
            ->scalar('name')
            ->maxLength('name', 75)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', false);

        $validator
            ->requirePresence('bootstrap', 'create')
            ->allowEmptyString('bootstrap', false);

        $validator
            ->requirePresence('width', 'create')
            ->greaterThan('width', 0, __('Must be greater than 0.'));

        $validator
            ->requirePresence('height', 'create')
            ->greaterThan('height', 0, __('Must be greater than 0.'));

        return $validator;
    }

    /**
     * Validates `$context` against `Configurator` based on:
     *
     * - Components existing in database.
     * - Components existing in steps associated with `Configurator`.
     *
     * @param Configurator $configurator
     * @param array $context
     *
     * @return bool
     */
    public function validate(Configurator $configurator, array $context)
    {
        $componentsTable = $this
            ->getTableLocator()
            ->get('ARC/ProductConfigurator.Components');

        $steps = $this->Steps
            ->find()
            ->where(['configurator_id' => $configurator->id]);

        $steps = new StepCollection($steps->toList());

        $componentIds = collection($steps->getComponentCollection()->getComponents())
            ->map(function (Component $component) {
                return $component->getId();
            })
            ->toList();

        foreach ($context as $component) {
            $componentId = key($component);

            if (!$componentsTable->exists(['id' => $componentId])) {
                return false;
            }

            if (!in_array($componentId, $componentIds)) {
                return false;
            }
        }

        return true;
    }
}
