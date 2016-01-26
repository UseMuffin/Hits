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

        $fields = ['view_count' => $defaults];
        $expected = compact('fields', 'implementedMethods');
        $this->assertEquals($expected, $behavior->config());

        $behavior->initialize($fields);
        $this->assertEquals($expected, $behavior->config());

        $config = ['view_count' => function () {}];
        $fields = ['view_count' => ['callback' => $config['view_count']] + $defaults];
        $expected = compact('fields', 'implementedMethods');
        $behavior->initialize($config);
        $this->assertEquals($expected, $behavior->config());

        $config = ['view_count' => ['status' => 'active']];
        $fields = ['view_count' => ['conditions' => $config['view_count']] + $defaults];
        $expected = compact('fields', 'implementedMethods');
        $behavior->initialize($config);
        $this->assertEquals($expected, $behavior->config());
    }
}
