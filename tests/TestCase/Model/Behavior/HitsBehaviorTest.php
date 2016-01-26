<?php
namespace Muffin\Hits\Test\Model\Behavior;

use Cake\TestSuite\TestCase;
use Muffin\Hits\Model\Behavior\HitsBehavior;

class HitsBehaviorTest extends TestCase
{

    public function testNormalizeFields()
    {
        $table = $this->getMock('Cake\ORM\Table');
        $behavior = new HitsBehavior($table, ['view_count']);

        $implementedMethods = [];
        $defaults = [
            'callback' => null,
            'conditions' => [],
            'increment' => 1,
        ];

        $counters = ['view_count' => $defaults];
        $expected = compact('counters', 'implementedMethods');
        $this->assertEquals($expected, $behavior->config());

        $behavior->initialize($counters);
        $this->assertEquals($expected, $behavior->config());

        $config = ['view_count' => function () {}];
        $counters = ['view_count' => ['callback' => $config['view_count']] + $defaults];
        $expected = compact('counters', 'implementedMethods');
        $behavior->initialize($config);
        $this->assertEquals($expected, $behavior->config());

        $config = ['view_count' => ['status' => 'active']];
        $counters = ['view_count' => ['conditions' => $config['view_count']] + $defaults];
        $expected = compact('counters', 'implementedMethods');
        $behavior->initialize($config);
        $this->assertEquals($expected, $behavior->config());
    }
}
