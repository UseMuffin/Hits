<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\TableRegistry;

class DefaultStrategy extends AbstractStrategy
{
    protected $_conditions;
    protected $_increment;

    public function __construct(array $conditions = [], $increment = 1)
    {
        $this->_conditions = $conditions;
        $this->_increment = 1;
    }

    public function increment($counter, $identifier)
    {
        list($alias, $field) = $this->_counterSplit($counter);
        $table = TableRegistry::get($alias);
        $expression = new QueryExpression("$counter = $counter + " . $this->_increment);
        $conditions = [$table->aliasField($table->primaryKey()) => $identifier];
        return $table->updateAll($expression, $conditions);
    }
}
