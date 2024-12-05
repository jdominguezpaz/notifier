<?php
declare(strict_types=1);

namespace Bakkerij\Notifier\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Validation\Validator;

class NotificationsTable extends Table
{
    /**
     * Configurations
     * @var array
     */
    protected array $config = [];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('notifications');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create')
            ->allowEmptyString('title')
            ->allowEmptyString('body')
            ->integer('state')
            ->allowEmptyString('state');

        return $validator;
    }
}
