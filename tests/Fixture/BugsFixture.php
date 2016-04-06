<?php

namespace StatefulEnum\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class BugsFixture extends TestFixture
{
    public $table = 'enum_bugs';

    public $fields = [
        'id' => ['type' => 'integer'],
        'title' => ['type' => 'string'],
        'body' => ['type' => 'text'],
        'status' => ['type' => 'string'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ];

    public $records = [
        [
            'title' => 'Bummy bug',
            'body' => 'Bug Bug Bug',
            'status' => 'unassigned',
        ],
        [
            'title' => 'Bummy bug',
            'body' => 'Bug Bug Bug',
            'status' => 'assigned',
        ],
    ];
}
