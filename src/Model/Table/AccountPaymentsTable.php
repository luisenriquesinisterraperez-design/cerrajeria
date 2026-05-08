<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AccountPayments Model
 *
 * @method \App\Model\Entity\AccountPayment newEmptyEntity()
 * @method \App\Model\Entity\AccountPayment newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\AccountPayment> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AccountPayment get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\AccountPayment findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\AccountPayment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\AccountPayment> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\AccountPayment|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\AccountPayment saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\AccountPayment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountPayment>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AccountPayment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountPayment> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AccountPayment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountPayment>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\AccountPayment>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\AccountPayment> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AccountPaymentsTable extends Table
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

        $this->setTable('account_payments');
        $this->setDisplayField('payment_method');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AccountsReceivable', [
            'foreignKey' => 'accounts_receivable_id',
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
            ->integer('accounts_receivable_id')
            ->requirePresence('accounts_receivable_id', 'create')
            ->notEmptyString('accounts_receivable_id');

        $validator
            ->decimal('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('payment_method')
            ->maxLength('payment_method', 255)
            ->requirePresence('payment_method', 'create')
            ->notEmptyString('payment_method');

        return $validator;
    }
}
