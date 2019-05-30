<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Components Model
 *
 * @property \App\Model\Table\ConfiguratorsTable|\Cake\ORM\Association\BelongsTo $Configurators
 *
 * @method \App\Model\Entity\Component get($primaryKey, $options = [])
 * @method \App\Model\Entity\Component newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Component[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Component|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Component saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Component patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Component[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Component findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ComponentsTable extends Table
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

        $this->setTable('components');
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
            ->scalar('mask')
            ->maxLength('mask', 255)
            ->requirePresence('mask', 'create')
            ->allowEmptyString('mask', false);

        $validator
            ->scalar('image_mask')
            ->maxLength('image_mask', 255)
            ->requirePresence('image_mask', 'create')
            ->allowEmptyString('image_mask', false);

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
