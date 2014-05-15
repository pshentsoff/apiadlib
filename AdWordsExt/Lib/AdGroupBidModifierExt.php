<?php
/**
 * @file        AdGroupBidModifierExt.php
 * @description Represents an adgroup level bid modifier override for campaign level criterion bid modifier values.
 * @see         https://developers.google.com/adwords/api/docs/reference/v201402/AdGroupBidModifierService.AdGroupBidModifier?hl=en type AdGroupBidModifier (v201402)
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

defined('APIADLIB_BID_MODIFIER') or define('APIADLIB_BID_MODIFIER', 1.5);
defined('APIADLIB_CRITERION_ID') or define('APIADLIB_CRITERION_ID', 30001);

class AdGroupBidModifierExt extends AdGroupBidModifier {

    public $user;
    public $bidModifier;
    public $bidModifierService;
    public $lastResponse;

    public function __construct() {

        if(func_num_args() > 0) {

            foreach(func_get_args() as $i => $arg) {

                if($arg instanceof AdWordsUserExt) {
                    $this->user = $arg;
                } elseif($arg instanceof AdGroupBidModifier) {
                    $this->campaign = $arg;
                } elseif(is_string($arg)) {
                    $this->user = new AdWordsUserExt($arg, func_get_arg($i+1));
                    break;
                }

            }

        } else {
            $trace = debug_backtrace();
            trigger_error(
                'At least one must be sent: AdWordsUserExt user, AdGroupBidModifier bid modifier or auth and settings ini files '.
                ' Exception at file ' . $trace[0]['file'] .
                '[' . $trace[0]['line'].']',
                E_USER_ERROR);
        }

        if(isset($this->user)) {
            $this->bidModifierService = $this->user->GetService('AdGroupBidModifierService');
        }

        if(!isset($this->bidModifier)&&isset($this->bidModifierService)) {
            $this->bidModifier = new AdGroupBidModifier();
            $this->setDefaults();
        }

    }

    public function setDefaults() {
        $this->bidModifier->criterion = new Platform();
        $this->bidModifier->criterion->id = APIADLIB_CRITERION_ID;
        $this->bidModifier->bidModifier = APIADLIB_BID_MODIFIER;
        $this->lastResponse = false;
    }

    function __get($name) {

        if($name == 'bidModifier') {
            return $this;
        } elseif(property_exists($this->bidModifier, $name)) {
            return $this->bidModifier->$name;
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

        if($name == 'bidModifier') {
            $this->bidModifier = $value;
            $this->lastResponse = false;
            return;
        } elseif(property_exists($this->bidModifier, $name)) {
            $this->bidModifier->$name = $value;
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
        return method_exists($this->bidModifier, $name) && isset($this->bidModifier->$name);
    }

    function __unset($name) {
        unset($this->bidModifier->$name);
    }

    function __call($name, $arguments) {
        if(method_exists($this->bidModifier, $name)) {
            return call_user_func_array(array($this->bidModifier, $name), $arguments);
        }
    }

    function operation($operator = 'ADD') {

        // Create operation.
        $operation = new CampaignOperation();
        $operation->operand = $this->bidModifier;
        $operation->operator = $operator;

        $operations = array();
        $operations[] = $operation;

        // Make the mutate request.
        $this->lastResponse = $this->bidModifierService->mutate($operations);

        unset($operation);
        unset($operations);

        return $this->lastResponse;
    }
}