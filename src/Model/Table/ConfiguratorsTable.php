<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Configurators Model
 *
 * @property \App\Model\Table\ComponentsTable|\Cake\ORM\Association\HasMany $Components
 * @property \App\Model\Table\StepsTable|\Cake\ORM\Association\HasMany $Steps
 *
 * @method \App\Model\Entity\Configurator get($primaryKey, $options = [])
 * @method \App\Model\Entity\Configurator newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Configurator[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Configurator|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Configurator saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Configurator patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Configurator[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Configurator findOrCreate($search, callable $callback = null, $options = [])
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

        $this->addBehavior('Timestamp');

        $this->hasMany('Components', [
            'foreignKey' => 'configurator_id',
            'className' => 'App.Components'
        ]);
        $this->hasMany('Steps', [
            'foreignKey' => 'configurator_id',
            'className' => 'App.Steps'
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

        return $validator;
    }
}
