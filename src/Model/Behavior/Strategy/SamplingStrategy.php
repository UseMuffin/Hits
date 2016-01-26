<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

use Cake\ORM\Table;

/**
 * @see http://stackoverflow.com/a/4762559/2020428
 */
class SamplingStrategy implements StrategyInterface
{
    protected $_strategy;
    protected $_size;

    public function __construct(StrategyInterface $strategy, $size = 100)
    {
        $this->_strategy = $strategy;
        $this->_size = $size;
    }

    public function increment(Table $table, $counter, $identifier)
    {
        if (mt_rand(1, $this->_size) !== 1) {
            return;
        }

        $this->_strategy->increment($table, $counter, $identifier);
    }

}
