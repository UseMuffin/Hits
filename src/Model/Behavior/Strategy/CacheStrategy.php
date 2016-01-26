<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

use Cake\Cache\CacheEngine;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class CacheStrategy extends AbstractStrategy
{
    protected $_cache;
    protected $_interval;
    protected $_offset;

    public function __construct(CacheEngine $cache, $interval = 100, $offset = 1)
    {
        $this->_cache = $cache;
        $this->_interval = $interval;
        $this->_offset = $offset;
    }

    public function increment(Table $table, $counter, $identifier)
    {
        list($alias, $field) = $this->_counterSplit($counter);
        $key = $table->primaryKey();

        if ($table->alias() !== $alias) {
            $key = $table->$alias->bindingKey();
            $table = TableRegistry::get($alias);
        }

        if (!$this->_cache->read($counter)) {
            $options = ['fields' => [$field]];
            $this->_cache->write($counter, $table->get($identifier, $options)->$field);
        }

        $count = $this->_cache->increment($counter, $this->_offset);

        if (!($count % $this->_interval)) {
            return;
        }

        $table->updateAll([$field => $count], [$key => $identifier]);
    }
}
