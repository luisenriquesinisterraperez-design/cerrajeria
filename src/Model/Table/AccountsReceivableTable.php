<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AccountsReceivable Model
 *
 * @property \App\Model\Table\ClientsTable&\Cake\ORM\Association\BelongsTo $Clients
 * @property \App\Model\Table\OrdersTable&\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \App\Model\Entity\AccountsReceivable newEmptyEntity()
 * @method \App\Model\Entity\AccountsReceivable newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\AccountsReceivable> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AccountsReceivable get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\AccountsReceivable findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\AccountsReceivable patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\AccountsReceivable> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AccountsReceivable|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\AccountsReceivable saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\AccountsReceivable>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountsReceivable>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AccountsReceivable>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountsReceivable> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AccountsReceivable>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountsReceivable>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AccountsReceivable>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountsReceivable> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AccountsReceivableTable extends Table
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

        $this->setTable('accounts_receivable');
        $this->setDisplayField('status');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Clients', [
            'foreignKey' => 'client_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('OriginOrders', [
            'className' => 'Orders',
            'foreignKey' => 'order_id',
        ]);
        $this->hasMany('AccountPayments', [
            'foreignKey' => 'accounts_receivable_id',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'accounts_receivable_id',
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
            ->integer('client_id')
            ->notEmptyString('client_id');

        $validator
            ->integer('order_id')
            ->allowEmptyString('order_id');

        $validator
            ->decimal('amount')
            ->notEmptyString('amount');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->notEmptyString('status');

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
        $rules->add($rules->existsIn(['client_id'], 'Clients'), ['errorField' => 'client_id']);
        $rules->add($rules->existsIn(['order_id'], 'OriginOrders'), ['errorField' => 'order_id']);

        return $rules;
    }
}
