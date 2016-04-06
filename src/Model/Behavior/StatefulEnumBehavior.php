<?php
namespace StatefulEnum\Model\Behavior;

use BadMethodCallException;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;

class StatefulEnumBehavior extends Behavior
{
    protected $_defaultConfig = [
        'forceTransition' => false,
    ];

    public function buildRules(Event $event, RulesChecker $rules)
    {
        if (empty($this->_table->transitions)) {
            return $rules;
        }
        foreach ($this->_table->transitions as $field => $transitions) {
            $ruleName = 'isValidStatefulEvent' . Inflector::camelize($field);
            $rules->add([$this, $ruleName], $ruleName, [
                'errorField' => $field,
                'message' => __d('cake', 'This transition is invalid'),
            ]);
        }
        return $rules;
    }

    /**
     * __call
     *
     */
    public function __call($method, $args){
        if (strpos($method, 'isValidStatefulEvent') !== 0) {
            throw new BadMethodCallException(sprintf('Call to undefined method (%s)', $method));
        }
        $field = Inflector::underscore(str_replace('isValidStatefulEvent', '', $method));
        list($entity, ) = $args;
        if (empty($this->_table->transitions[$field])) {
            return true;
        }

        if (empty($entity->{$this->_table->primaryKey()})) {
            return true;
        }

        $currentEntity = $this->_table->find()
                 ->contain([])
                 ->where([$this->_table->alias() . '.' . $this->_table->primaryKey() => $entity->{$this->_table->primaryKey()}])
                 ->first();
        $currentState = $currentEntity->{$field};
        $nextState = $entity->{$field};
        if ($this->config('forceTransition')  === false && $currentState === $nextState) {
            return true;
        }
        foreach ($this->_table->transitions[$field] as $event => $transition) {
            if (array_key_exists('from', $transition)) {
                $from = $transition['from'];
                $to = $transition['to'];
            } else if (array_key_exists(0, $transition)){
                $from = $transition[0];
                $to = $transition[1];
            } else {
                throw new InvalidTransitionSettingException([$field]);
            }

            if (in_array($currentState, (array)$from) && in_array($nextState, (array)$to)) {
                return true;
            }
        }
        return false;
    }
}
