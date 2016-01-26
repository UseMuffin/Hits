<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class DefaultStrategy extends AbstractStrategy
{
    protected $_conditions;
    protected $_offset;

    public function __construct(array $conditions = [], $offset = 1)
    {
        $this->_conditions = $conditions;
        $this->$_offset = 1;
    }

    public function increment(Table $table, $counter, $identifier)
    {
        list($alias, $field) = $this->_counterSplit($counter);
        $key = $table->primaryKey();
        if ($table->alias() !== $alias) {
            $key = $table->$alias->bindingKey();
            $table = TableRegistry::get($alias);
        }

        $expression = new QueryExpression("$field = $field + " . $this->_offset);
        $conditions = [$key => $identifier] + $this->_conditions;
        return $table->updateAll($expression, $conditions);
    }
}
