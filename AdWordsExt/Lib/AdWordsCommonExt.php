<?php
/**
 * @file        AdWordsCommonExt.php
 * @description
 *
 * PHP Version  5.3.13
 *
 * @package 
 * @category
 * @plugin URI
 * @copyright   2014, Vadim Pshentsov. All Rights Reserved.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @author      Vadim Pshentsov <pshentsoff@gmail.com> 
 * @link        http://pshentsoff.ru Author's homepage
 * @link        http://blog.pshentsoff.ru Author's blog
 *
 * @created     17.06.14
 */

require_once dirname(__FILE__) . '/../../apiadlib.autoload.php';

/**
 * Class AdWordsCommonExt
 */
//@todo Переводим всех родителей на этот класс
class AdWordsCommonExt {

    public $user;
    public $service;
    public $object;
    public $lastResponse;

    const SERVICE_NAME = null;
    const WRAPPED_CLASS_NAME = null;

    public function __construct() {

        if(func_num_args() > 0) {
            foreach(func_get_args() as $i => $arg) {
                if($arg instanceof AdWordsUserExt) {
                    $this->user = $arg;
                } elseif(is_object($arg) && get_class($arg) === static::WRAPPED_CLASS_NAME) {
                    $this->object = $arg;
                } elseif(is_string($arg)) {
                    $this->user = new AdWordsUserExt($arg, func_get_arg($i+1));
                    break;
                }
            }
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'At least one must be sent: AdWordsUserExt user, '.static::WRAPPED_CLASS_NAME.' or auth and settings ini files '.
                ' Exception at file ' . $trace[0]['file'] .
                '[' . $trace[0]['line'].']',
                E_USER_ERROR);
        }

        if(isset($this->user)) {
            $this->service = $this->user->GetService(static::SERVICE_NAME);
        }

        if(!isset($this->object) && isset($this->service)) {
            $class = static::WRAPPED_CLASS_NAME;
            $this->object = new $class();
            $this->setDefaults();
        }
    }

    public function setDefaults() {
        $this->lastResponse = false;
    }

    function __get($name) {

        if(mb_strtolower($name) === mb_strtolower(static::WRAPPED_CLASS_NAME)) {
            return $this;
        } elseif(is_object($this->object) && property_exists($this->object, $name)) {
            return $this->object->$name;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property in __get(): ' . $name .
            ' at file ' . $trace[0]['file'] .
            '[' . $trace[0]['line'].']',
            E_USER_ERROR);
        return null;
    }

    function __set($name, $value) {

        //@debug remove
        $args = func_get_args();

        if(mb_strtolower($name) === mb_strtolower(static::WRAPPED_CLASS_NAME)) {
            //@debug remove
            $args[] = 'Wrapped class';
//            print_pre('Class ['.get_class().'] setter call',$args);

            $this->object = $value;
            $this->lastResponse = false;
            return;
        } elseif(property_exists($this->object, $name)) {
            //@debug remove
            $args[] = 'Property of wrapped class';
//            print_pre('Class ['.get_class().'] setter call',$args);

            $this->object->$name = $value;
            $this->lastResponse = false;
            return;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property in __set(): ' . $name .
            ' at file ' . $trace[0]['file'] .
            '[' . $trace[0]['line'].']',
            E_USER_ERROR);
    }

    function __isset($name) {
        return method_exists($this->object, $name) && isset($this->object->$name);
    }

    function __unset($name) {
        unset($this->object->$name);
    }

    function __call($name, $arguments) {
        if(method_exists($this->object, $name)) {
            return call_user_func_array(array($this->object, $name), $arguments);
        }
    }

    function operation($operator = 'ADD') {

        // Create operation.
        //@todo CampaignOperation? Really? At Common?
        $operation = new CampaignOperation();
        $operation->operand = $this->object;
        $operation->operator = $operator;

        $operations = array();
        $operations[] = $operation;

        // Make the mutate request.
        $this->lastResponse = $this->service->mutate($operations);

        unset($operation);
        unset($operations);

        return $this->lastResponse;
    }
}