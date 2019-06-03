<?php
namespace ARC\ProductConfigurator\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Steps Model
 *
 * @property \ARC\ProductConfigurator\Model\Table\ConfiguratorsTable|\Cake\ORM\Association\BelongsTo $Configurators
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Step get($primaryKey, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Step newEntity($data = null, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Step[] newEntities(array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Step|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Step saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Step patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Step[] patchEntities($entities, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Step findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StepsTable extends Table
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

        $this->setTable('steps');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Configurators', [
            'foreignKey' => 'configurator_id',
            'joinType' => 'INNER',
            'className' => 'App.Configurators'
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
            ->maxLength('name', 45)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', false);

        $validator
            ->nonNegativeInteger('sort')
            ->requirePresence('sort', 'create')
            ->allowEmptyString('sort', false);

        $validator
            ->requirePresence('config', 'create')
            ->allowEmptyString('config', false);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['configurator_id'], 'Configurators'));

        return $rules;
    }
}
