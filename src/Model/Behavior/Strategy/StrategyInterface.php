<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

use Cake\ORM\Table;

interface StrategyInterface
{
    public function increment(Table $table, $counter, $identifier);
}
