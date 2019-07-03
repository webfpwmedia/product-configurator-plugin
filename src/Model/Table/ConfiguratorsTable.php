<?php
namespace ARC\ProductConfigurator\Model\Table;

use ARC\ProductConfigurator\ORM\Table;
use Cake\Validation\Validator;

/**
 * Configurators Model
 *
 * @property \ARC\ProductConfigurator\Model\Table\ComponentsTable|\Cake\ORM\Association\HasMany $Components
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

        $this->hasMany('Components', [
            'foreignKey' => 'configurator_id',
            'className' => 'ARC/ProductConfigurator.Components'
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
            ->greaterThan('width', 0);

        $validator
            ->requirePresence('height', 'create')
            ->greaterThan('height', 0);

        return $validator;
    }
}
