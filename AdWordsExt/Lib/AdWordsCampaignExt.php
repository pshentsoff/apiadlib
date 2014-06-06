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

class AdWordsCampaignExt extends AdWordsCommonExt {

    const SERVICE_NAME = 'CampaignService';
    const WRAPPED_CLASS_NAME = 'Campaign';

    public $budgetService;

    /**
     * Sets campaign defaults
     */
    function setDefaults() {

        parent::setDefaults();

        if(isset($this->user)) {
            $this->budgetService = $this->user->GetService('BudgetService');
        }

        $this->object->name = 'New Campaign #' . uniqid();
        // Set additional settings (optional).
        $this->object->status = 'PAUSED';
        $this->object->startDate = date('Ymd', strtotime('+1 day'));
        $this->object->endDate = date('Ymd', strtotime('+1 month'));
        $this->object->adServingOptimizationStatus = 'ROTATE';
        $this->object->advertisingChannelType = 'SEARCH';

        $this->object->budget = new Budget();
        $this->object->budget->name = 'New Campaign Budget #' . uniqid();
        $this->object->budget->period = 'DAILY';
        $this->setBudgetAmount(50000000);
        $this->object->budget->deliveryMethod = 'STANDARD';

        //@todo servingStatus и список в форме как R/O (https://developers.google.com/adwords/api/docs/reference/v201402/CampaignService.Campaign)

        $this->object->biddingStrategyConfiguration = new BiddingStrategyConfiguration();
        $this->object->biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPC';
//        $this->class->biddingStrategyConfiguration->biddingScheme = new BiddingScheme();

        $this->object->networkSetting = new NetworkSetting();
        $this->object->networkSetting->targetGoogleSearch = true;
        $this->object->networkSetting->targetSearchNetwork = true;
        $this->object->networkSetting->targetContentNetwork = true;

        $this->object->frequencyCap = new FrequencyCap();
        $this->object->frequencyCap->impressions = 5;
        $this->object->frequencyCap->timeUnit = 'DAY';
        $this->object->frequencyCap->level = 'ADGROUP';

        $this->object->settings[] = new KeywordMatchSetting(true);
        $this->object->settings[] = new GeoTargetTypeSetting('DONT_CARE', 'DONT_CARE');
        //@todo Добавить в формы gaw-approve
        $this->object->settings[] = new DynamicSearchAdsSetting('-', 'en');
    }

    function setBudgetAmount($budgetAmount) {
        if(isset($this->object->budget->amount)) {
            unset($this->object->budget->amount);
        }
        $this->object->budget->amount = new Money($budgetAmount);
    }

    function budgetOperation($operator = 'ADD') {

        $operations = array();

        $operation = new BudgetOperation();
        $operation->operand = $this->object->budget;
        $operation->operator = $operator;
        $operations[] = $operation;

        $result = $this->budgetService->mutate($operations);
        $this->object->budget = $result->value[0];

        unset($operation);
        unset($operations);

        return $result;
    }

    function getById($campaignId, $fields = array()) {

        unset($this->object);

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

            $this->object = $campaignsPage->entries[0];

            return $this->object;

        } else {

            return false;

        }
    }
}
 