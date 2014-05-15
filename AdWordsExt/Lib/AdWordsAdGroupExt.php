<?php
/**
 * @file        AdWordsAdGroupExt.php
 * @description Represents an ad group.
 * @see         https://developers.google.com/adwords/api/docs/reference/v201402/AdGroupService.AdGroup type AdGroup (v201402)
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
 * @created     15.05.14
 */

require_once dirname(__FILE__) . '/../../apiadlib.autoload.php';

class AdWordsAdGroupExt extends AdGroup {

    public $user;
    public $adGroup;
    public $adGroupService;
    public $lastResponse;

    public function __construct() {

        if(func_num_args() > 0) {

            foreach(func_get_args() as $i => $arg) {

                if($arg instanceof AdWordsUserExt) {
                    $this->user = $arg;
                } elseif($arg instanceof AdGroup) {
                    $this->adGroup = $arg;
                } elseif(is_string($arg)) {
                    $this->user = new AdWordsUserExt($arg, func_get_arg($i+1));
                    break;
                }

            }

        } else {
            $trace = debug_backtrace();
            trigger_error(
                'At least one must be sent: AdWordsUserExt user, AdGroup or auth and settings ini files '.
                ' Exception at file ' . $trace[0]['file'] .
                '[' . $trace[0]['line'].']',
                E_USER_ERROR);
        }

        if(isset($this->user)) {
            $this->adGroupService = $this->user->GetService('AdGroupService');
        }

        if(!isset($this->adGroup)&&isset($this->adGroupService)) {
            $this->adGroup = new AdGroup();
            $this->setDefaults();
        }

    }

    function setDefaults() {
    }

    function __get($name) {

        if($name == 'adGroup') {
            return $this;
        } elseif(property_exists($this->adGroup, $name)) {
            return $this->adGroup->$name;
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

        if($name == 'adGroup') {
            $this->adGroup = $value;
            $this->lastResponse = false;
            return;
        } elseif(property_exists($this->adGroup, $name)) {
            $this->adGroup->$name = $value;
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
        return method_exists($this->adGroup, $name) && isset($this->adGroup->$name);
    }

    function __unset($name) {
        unset($this->adGroup->$name);
    }

    function __call($name, $arguments) {
        if(method_exists($this->adGroup, $name)) {
            return call_user_func_array(array($this->adGroup, $name), $arguments);
        }
    }

    function operation($operator = 'ADD') {

        // Create operation.
        $operation = new CampaignOperation();
        $operation->operand = $this->adGroup;
        $operation->operator = $operator;

        $operations = array();
        $operations[] = $operation;

        // Make the mutate request.
        $this->lastResponse = $this->adGroupService->mutate($operations);

        unset($operation);
        unset($operations);

        return $this->lastResponse;
    }
}