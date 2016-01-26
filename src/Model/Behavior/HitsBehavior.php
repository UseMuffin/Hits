<?php
namespace Muffin\Hits\Model\Behavior;

use ArrayObject;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class HitsBehavior extends Behavior
{
    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->_normalizeConfig($config);
    }

    /**
     * Auto-detects find operations performed using the primary key and increments the
     * associated view counter(s).
     *
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Query $query
     * @param \ArrayObject $options
     * @param $primary
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, $primary)
    {
        if (!$primary) {
            return;
        }

        $query->traverseExpressions(function ($expression) use ($options) {
            $primaryKey = $this->_table->primaryKey();
            $allowedFields = [$primaryKey, $this->_table->aliasField($primaryKey)];
            if (!method_exists($expression, 'getField')
                || !in_array($expression->getField(), $allowedFields)
            ) {
                return $expression;
            }

            foreach ($this->config('counters') as $counter => $config) {
                $args = [$counter, $options];
                if (!empty($config['callback']) && is_callable($config['callback'])
                    && !call_user_func_array($config['callback'], $args)
                ) {
                    continue;
                }

                $this->increment($counter, $expression->getValue(), $config['conditions']);
            }
        });
    }

    /**
     * @param $counter
     * @param $primaryKey
     * @param array $conditions
     */
    public function increment($counter, $primaryKey, array $conditions = [])
    {
        $increment = $this->config('counters.' . $counter . '.increment');
        $expression = new QueryExpression("$counter = $counter + $increment");

        $table = $this->_table;
        if (strpos($counter, '.') !== false) {
            $parts = explode('.', $counter);
            array_pop($parts);
            $table = TableRegistry::get(implode('.', $parts));
        }

        $conditions[$this->_table->primaryKey()] = $primaryKey;

        return $table->updateAll($expression, $conditions);
    }

    /**
     * @param $config
     */
    protected function _normalizeConfig($counters)
    {
        foreach ($counters as $counter => $options) {
            if (is_numeric($counter) && is_string($options)) {
                unset($counters[$counter]);
                $counter = $options;
                $options = [];
            }

            if (is_array($options)
                && !isset($options['conditions'])
                && !isset($options['callback'])
            ) {
                $options = ['conditions' => $options];
            }

            if (is_callable($options)) {
                $options = ['callback' => $options];
            }

            $options += [
                'callback' => null,
                'conditions' => [],
                'increment' => 1,
            ];

            $counters[$counter] = $options;
        }

        $this->_config = [
            'counters' => $counters,
            'implementedMethods' => [],
        ];
    }
}
