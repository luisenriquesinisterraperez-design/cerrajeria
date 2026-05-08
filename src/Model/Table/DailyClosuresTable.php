<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DailyClosures Model
 *
 * @method \App\Model\Entity\DailyClosure newEmptyEntity()
 * @method \App\Model\Entity\DailyClosure newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\DailyClosure> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DailyClosure get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\DailyClosure findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\DailyClosure patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\DailyClosure> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\DailyClosure|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\DailyClosure saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\DailyClosure>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyClosure>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DailyClosure>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyClosure> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DailyClosure>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyClosure>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\DailyClosure>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\DailyClosure> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DailyClosuresTable extends Table
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

        $this->setTable('daily_closures');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->date('date')
            ->requirePresence('date', 'create')
            ->notEmptyDate('date');

        $validator
            ->decimal('base_amount')
            ->notEmptyString('base_amount');

        $validator
            ->decimal('expected_amount')
            ->notEmptyString('expected_amount');

        $validator
            ->decimal('actual_amount')
            ->notEmptyString('actual_amount');

        $validator
            ->decimal('difference')
            ->notEmptyString('difference');

        $validator
            ->scalar('observations')
            ->allowEmptyString('observations');

        return $validator;
    }
}
