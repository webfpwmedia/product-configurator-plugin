<?php
namespace ARC\ProductConfigurator\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Images Model
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Image get($primaryKey, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Image newEntity($data = null, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Image[] newEntities(array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Image|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Image saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Image patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Image[] patchEntities($entities, array $data, array $options = [])
 * @method \ARC\ProductConfigurator\Model\Entity\Image findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ImagesTable extends Table
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

        $this->setTable('images');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->allowEmptyString('name', false);

        $validator
            ->scalar('mask')
            ->maxLength('mask', 255)
            ->requirePresence('mask', 'create')
            ->allowEmptyString('mask', false);

        $validator
            ->scalar('position')
            ->maxLength('position', 45)
            ->requirePresence('position', 'create')
            ->allowEmptyString('position', false);

        $validator
            ->integer('layer')
            ->requirePresence('layer', 'create')
            ->allowEmptyString('layer', false);

        return $validator;
    }
}
