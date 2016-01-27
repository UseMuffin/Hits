<?php
namespace Muffin\Hits\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Muffin\Hits\Model\Behavior\Strategy\DefaultStrategy;
use Muffin\Hits\Model\Behavior\Strategy\StrategyInterface;

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

        $query->traverseExpressions(function ($expression) use ($query, $options) {
            $primaryKey = $this->_table->primaryKey();
            $allowedFields = [$primaryKey, $this->_table->aliasField($primaryKey)];
            if (!method_exists($expression, 'getField')
                || !in_array($expression->getField(), $allowedFields)
            ) {
                return $expression;
            }

            foreach ($this->config('counters') as $counter => $config) {
                $args = [$query, $options, $counter];
                if (!empty($config['callback']) && is_callable($config['callback'])
                    && !call_user_func_array($config['callback'], $args)
                ) {
                    continue;
                }

                $config['strategy']->increment($this->_table, $counter, $expression->getValue());
            }
        });
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
                && !isset($options['strategy'])
                && !isset($options['callback'])
            ) {
                $options = ['conditions' => $options];
            }

            if ($options instanceof StrategyInterface) {
                $options = ['strategy' => $options];
            }

            if (is_callable($options)) {
                $options = ['callback' => $options];
            }

            if (isset($options['conditions']) || isset($options['offset'])) {
                $options += ['conditions' => [], 'offset' => 1];
                if (!isset($options['strategy'])) {
                    $options['strategy'] = new DefaultStrategy($options['conditions'], $options['offset']);
                }
                unset($options['conditions'], $options['offset']);
            }

            $options += [
                'callback' => null,
                'strategy' => new DefaultStrategy(),
            ];

            $counters[$counter] = $options;
        }

        $this->_config = [
            'counters' => $counters,
            'implementedMethods' => [],
        ];
    }
}
