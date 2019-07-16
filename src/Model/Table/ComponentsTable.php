<?php
namespace ARC\ProductConfigurator\Model\Table;

use ARC\ProductConfigurator\ORM\Table;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Components Model
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Component get($primaryKey, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Component newEntity($data = null, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Component[] newEntities(array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Component|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Component saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Component patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Component[] patchEntities($entities, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Component findOrCreate($search, callable $callback = null, $options = [])
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

        $this->addBehavior('ARC/ProductConfigurator.Json', [
            'fields' => ['options'],
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

        $validator
            ->isArray('options')
            ->requirePresence('options', 'create')
            ->allowEmptyArray('options', true);

        return $validator;
    }
}
