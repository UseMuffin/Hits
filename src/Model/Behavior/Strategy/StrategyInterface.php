<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

interface StrategyInterface
{
    public function increment($counter, $identifier);
}
