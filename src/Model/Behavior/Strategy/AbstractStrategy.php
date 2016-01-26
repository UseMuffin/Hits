<?php
namespace Muffin\Hits\Model\Behavior\Strategy;

abstract class AbstractStrategy implements StrategyInterface
{
    protected function _counterSplit($name)
    {
        $parts = explode('.', $name);
        $field = array_pop($parts);
        $alias = array_implode('.', $parts);
        return [$alias, $field];
    }
}
