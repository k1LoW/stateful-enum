<?php

namespace StatefulEnum\Test\TestCase\Model\Behavior;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class BugsTable extends Table
{
    const STATUS_UNASSIGNED = 'unassigned';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    public $transitions = [
        'status' => [
            'assign' => [
                self::STATUS_UNASSIGNED,
                self::STATUS_ASSIGNED
            ],
            'resolve' => [
                'from' => [self::STATUS_ASSIGNED, self::STATUS_UNASSIGNED],
                'to' => self::STATUS_RESOLVED
            ],
            'close' => [
                [self::STATUS_ASSIGNED, self::STATUS_UNASSIGNED, self::STATUS_RESOLVED],
                self::STATUS_CLOSED
            ],
        ]
    ];

    public function initialize(array $config)
    {
        $this->primaryKey('id');
        $this->addBehavior('StatefulEnum.StatefulEnum');
    }
}

class StatefulEnumBehaviorTest extends TestCase
{
    public $fixtures = [
        'plugin.StatefulEnum.bugs',
    ];

    protected $Bugs;

    public function setUp()
    {
        parent::setUp();

        $this->Bugs = TableRegistry::get('StatefulEnum.Bugs', [
            'className' => 'StatefulEnum\Test\TestCase\Model\Behavior\BugsTable',
            'table' => 'enum_bugs'
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
    }

    public function provideBuildRules()
    {
        return [
            [
                [
                    'id' => 1,
                    'title' => 'Bummy bug',
                    'body' => 'Bug Bug Bug',
                    'status' => 'assigned',
                ],
                [
                ],
            ],
            [
                [
                    'id' => 2,
                    'title' => 'Bummy bug',
                    'body' => 'Bug Bug Bug',
                    'status' => 'unassigned',
                ],
                [
                    'status' => [
                        'isValidStatefulEventStatus' => 'This transition is invalid'
                    ],
                ],
            ]
        ];
    }

    /**
     * @dataProvider provideBuildRules
     */
    public function testBuildRules($data, $expected)
    {
        $bug = new \Cake\ORM\Entity($data);
        $this->Bugs->save($bug);
        $result = $bug->errors();
        $this->assertEquals($expected, $result);
    }
}
