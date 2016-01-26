<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

use Cake\ORM\Table;

/**
 * @see http://stackoverflow.com/a/4762559/2020428
 */
class UnpreciseStrategy implements StrategyInterface
{
    protected $_precision;
    protected $_offset;

    public function __construct($precision = 100, $offset = 1)
    {
        $this->_precision = $precision;
        $this->_offset = $offset;
    }

    public function increment(Table $table, $counter, $identifier)
    {
        if (mt_rand(1, $this->_precision) !== 1) {
            return;
        }

        list($alias, $field) = $this->_counterSplit($counter);
        $key = $table->primaryKey();

        if ($table->alias() !== $alias) {
            $key = $table->$alias->bindingKey();
            $table = TableRegistry::get($alias);
        }

        $table->updateAll([$field => $this->_precision], [$key => $identifier]);
    }

}
