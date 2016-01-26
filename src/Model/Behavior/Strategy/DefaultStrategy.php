<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

use Cake\Database\Expression\QueryExpression;
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

    public function increment($counter, $identifier)
    {
        list($alias, $field) = $this->_counterSplit($counter);
        $table = TableRegistry::get($alias);
        $expression = new QueryExpression("$counter = $counter + " . $this->_offset);
        $conditions = [$table->aliasField($table->primaryKey()) => $identifier];
        return $table->updateAll($expression, $conditions);
    }
}
