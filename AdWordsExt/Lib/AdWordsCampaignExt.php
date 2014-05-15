<?php
/**
 * @file        AdWordsCampaignExt.php
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
 * @created     01.04.14
 */

require_once dirname(__FILE__) . '/../../apiadlib.autoload.php';

//if(!class_exists('')) {
//    die('This script can not be executed directly.');
//}

//changed to tes problem svn commit

class AdWordsCampaignExt {

    public $user;
    public $campaign;
    public $campaignService;
    public $budgetService;

    public function __construct() {

        if(func_num_args() > 0) {

            foreach(func_get_args() as $i => $arg) {

                if($arg instanceof AdWordsUserExt) {
                    $this->user = $arg;
                } elseif($arg instanceof Campaign) {
                    $this->campaign = $arg;
                } elseif(is_string($arg)) {
                    $this->user = new AdWordsUserExt($arg, func_get_arg($i+1));
                    break;
                }

            }

        } else {
            $trace = debug_backtrace();
            trigger_error(
                'At least one must be sent: AdWordsUserExt user, Campaign campaign or auth and settings ini files '.
                ' Exception at file ' . $trace[0]['file'] .
                '[' . $trace[0]['line'].']',
                E_USER_ERROR);
        }

        if(isset($this->user)) {
            $this->campaignService = $this->user->GetService('CampaignService');
            $this->budgetService = $this->user->GetService('BudgetService');
        }

        if(!isset($this->campaign)&&isset($this->campaignService)) {
            $this->campaign = new Campaign();
            $this->setDefaults();
        }

    }

    function __get($name) {

        if($name == 'campaign') {
            return $this;
        } elseif(property_exists($this->campaign, $name)) {
            return $this->campaign->$name;
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

        if($name == 'campaign') {
            $this->campaign = $value;
            return;
        } elseif(property_exists($this->campaign, $name)) {
            $this->campaign->$name = $value;
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
        return method_exists($this->campaign, $name) && isset($this->campaign->$name);
    }

    function __unset($name) {
        unset($this->campaign->$name);
    }

    function __call($name, $arguments) {
        if(method_exists($this->campaign, $name)) {
            return call_user_func_array(array($this->campaign, $name), $arguments);
        }
    }

    /**
     * Sets campaign defaults
     */
    function setDefaults() {

        $this->campaign->name = 'New Campaign #' . uniqid();
        // Set additional settings (optional).
        $this->campaign->status = 'PAUSED';
        $this->campaign->startDate = date('Ymd', strtotime('+1 day'));
        $this->campaign->endDate = date('Ymd', strtotime('+1 month'));
        $this->campaign->adServingOptimizationStatus = 'ROTATE';
        $this->campaign->advertisingChannelType = 'SEARCH';

        $this->campaign->budget = new Budget();
        $this->campaign->budget->name = 'New Campaign Budget #' . uniqid();
        $this->campaign->budget->period = 'DAILY';
        $this->setBudgetAmount(50000000);
        $this->campaign->budget->deliveryMethod = 'STANDARD';

        //@todo servingStatus и список в форме как R/O (https://developers.google.com/adwords/api/docs/reference/v201402/CampaignService.Campaign)

        $this->campaign->biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $this->campaign->biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPC';
//        $this->campaign->biddingStrategyConfiguration->biddingScheme = new BiddingScheme();

        $this->campaign->networkSetting = new NetworkSetting();
        $this->campaign->networkSetting->targetGoogleSearch = true;
        $this->campaign->networkSetting->targetSearchNetwork = true;
        $this->campaign->networkSetting->targetContentNetwork = true;

        $this->campaign->frequencyCap = new FrequencyCap();
        $this->campaign->frequencyCap->impressions = 5;
        $this->campaign->frequencyCap->timeUnit = 'DAY';
        $this->campaign->frequencyCap->level = 'ADGROUP';

        $this->campaign->settings[] = new KeywordMatchSetting(true);
        $this->campaign->settings[] = new GeoTargetTypeSetting('DONT_CARE', 'DONT_CARE');
        //@todo Добавить в формы gaw-approve
        $this->campaign->settings[] = new DynamicSearchAdsSetting('-', 'en');
    }

    function setBudgetAmount($budgetAmount) {
        if(isset($this->campaign->budget->amount)) {
            unset($this->campaign->budget->amount);
        }
        $this->campaign->budget->amount = new Money($budgetAmount);
    }

    function budgetOperation($operator = 'ADD') {

        $operations = array();

        $operation = new BudgetOperation();
        $operation->operand = $this->campaign->budget;
        $operation->operator = $operator;
        $operations[] = $operation;

        $result = $this->budgetService->mutate($operations);
        $this->campaign->budget = $result->value[0];

        unset($operation);
        unset($operations);

        return $result;
    }

    /**
     * @param string $operator
     * @return mixed
     */
    function campaignOperation($operator = 'ADD') {
        return $this->operation($operator);
    }

    function operation($operator = 'ADD') {
        // Create operation.
        $operation = new CampaignOperation();
        $operation->operand = $this->campaign;
        $operation->operator = $operator;

        $operations = array();
        $operations[] = $operation;

        // Make the mutate request.
        $result = $this->campaignService->mutate($operations);

        unset($operation);
        unset($operations);

        return $result;
    }

    function getById($campaignId, $fields = array()) {

        unset($this->campaign);

        // Фильтр
        $predicate = new Predicate('Id','EQUALS',$campaignId);
        // Create selector.
        $selector = new Selector();

        if(empty($fields)) {
            $fields = array(
                'Id', 'Name', 'Status', 'ServingStatus', 'StartDate', 'EndDate',
                //@todo разобраться как правильно грузить бюджет
//            'Budget',
                //@todo разобраться как правильно грузить ConversionOptimizerEligibility
//            'ConversionOptimizerEligibility',
                'AdServingOptimizationStatus',
                //@todo разобраться как правильно грузить FrequencyCap
//            'FrequencyCap',
                'Settings',
                'AdvertisingChannelType',
                'TargetGoogleSearch', 'TargetSearchNetwork', 'TargetContentNetwork', 'TargetPartnerSearchNetwork',
                //@todo разобраться как правильно грузить BiddingStrategyConfiguration
//            'BiddingStrategyConfiguration',
                //@todo разобраться как правильно грузить ForwardCompatibilityMap
//            'ForwardCompatibilityMap',
                'DisplaySelect',
            );
        }

        $selector->fields = $fields;
        $selector->predicates[] = $predicate;

        $campaignsPage = $this->campaignService->get($selector);

        if($campaignsPage->totalNumEntries == 1) {

            $this->campaign = $campaignsPage->entries[0];

            return $this->campaign;

        } else {

            return false;

        }
    }
}
 