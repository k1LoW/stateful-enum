# Simple stateful enum plugin for CakePHP 3 [![Build Status](https://travis-ci.org/k1LoW/stateful-enum.svg?branch=master)](https://travis-ci.org/k1LoW/stateful-enum)

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require k1low/stateful-enum
```
## Usage

```php
class BugsTable extends Table
{
    const STATUS_UNASSIGNED = 'unassigned';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    public $transitions = [
        'status' => [
            'assign' => [
                'from' => self::STATUS_UNASSIGNED,
                'to' => self::STATUS_ASSIGNED
            ],
            'resolve' => [
                [self::STATUS_ASSIGNED, self::STATUS_UNASSIGNED], // from
                self::STATUS_RESOLVED // to
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
```

So, This plugin set build state rules automatically.

## References

This plugin is inspired by https://github.com/amatsuda/stateful_enum
