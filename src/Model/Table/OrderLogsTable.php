<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * OrderLogs Model
 *
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\OrderLog newEmptyEntity()
 * @method \App\Model\Entity\OrderLog newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\OrderLog> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\OrderLog get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\OrderLog findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\OrderLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\OrderLog> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\OrderLog|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\OrderLog saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\OrderLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\OrderLog>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\OrderLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\OrderLog> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\OrderLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\OrderLog>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\OrderLog>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\OrderLog> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrderLogsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('order_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('order_id')
            ->allowEmptyString('order_id');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('modification_details')
            ->requirePresence('modification_details', 'create')
            ->notEmptyString('modification_details');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
