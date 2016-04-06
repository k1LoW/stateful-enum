<?php

namespace StatefulEnum\Model\Behavior\Exception;

use Cake\Core\Exception\Exception;

class InvalidTransitionSettingException extends Exception
{
    protected $_templateMessage = 'Invalid transition setting (%s)';
}
