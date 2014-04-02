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

//if(!class_exists('')) {
//    die('This script can not be executed directly.');
//}

class AdWordsCampaignExt {

    public $user;
    public $budgetService;
    public $budget;
    public $campaignService;
    public $campaign;
    public $biddingStrategyConfiguration;
    public $biddingScheme;
    public $keywordMatchSetting;
    public $networkSetting;
    public $frequencyCap;
    public $geoTargetTypeSetting;

    public function __construct($user = null, $auth_file = null, $settings_file = null) {

        if(isset($user)) {
            $this->user = $user;
        } elseif(isset($auth_file) && isset($settings_file)) {
            $this->user = new AdWordsUserExt($auth_file, $settings_file);
        } else {
            throw new Exception('Need AdWords user object or auth & settings files.');
        }

        // Get the BudgetService, which loads the required classes.
        $this->budgetService = $user->GetService('BudgetService', ADWORDS_VERSION);

        // Create the shared budget (required).
        $this->budget = new Budget();

        // Get the CampaignService, which loads the required classes.
        $this->campaignService = $user->GetService('CampaignService', ADWORDS_VERSION);

        // Create campaign.
        $this->campaign = new Campaign();

        // Set bidding strategy (required).
        $this->biddingStrategyConfiguration = new BiddingStrategyConfiguration();

        // You can optionally provide a bidding scheme in place of the type.
        $this->biddingScheme = new ManualCpcBiddingScheme();

        // Set keyword matching setting (required).
        $this->keywordMatchSetting = new KeywordMatchSetting();

        // Set network targeting (recommended).
        $this->networkSetting = new NetworkSetting();

        // Set frequency cap (optional).
        $this->frequencyCap = new FrequencyCap();

        // Set advanced location targeting settings (optional).
        $this->geoTargetTypeSetting = new GeoTargetTypeSetting();

        $this->setDefaults();
    }

    function setDefaults() {

        $this->budget->name = 'New Campaign Budget #' . uniqid();
        $this->budget->period = 'DAILY';
        $this->setBudgetAmount(50000000);
        $this->budget->deliveryMethod = 'STANDARD';

        $this->campaign->name = 'New Campaign #' . uniqid();
        // Set additional settings (optional).
        $this->campaign->status = 'PAUSED';
        $this->campaign->startDate = date('Ymd', strtotime('+1 day'));
        $this->campaign->endDate = date('Ymd', strtotime('+1 month'));
        $this->campaign->adServingOptimizationStatus = 'ROTATE';
        $this->campaign->advertisingChannelType = 'SHOPPING';

//@todo servingStatus и список в форме (https://developers.google.com/adwords/api/docs/reference/v201402/CampaignService.Campaign)

        $this->biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPC';

        $this->biddingScheme->enhancedCpcEnabled = false;

        $this->keywordMatchSetting->optIn = true;

        $this->networkSetting->targetGoogleSearch = true;
        $this->networkSetting->targetSearchNetwork = true;
        $this->networkSetting->targetContentNetwork = true;

        $this->frequencyCap->impressions = 5;
        $this->frequencyCap->timeUnit = 'DAY';
        $this->frequencyCap->level = 'ADGROUP';

        $this->geoTargetTypeSetting->positiveGeoTargetType = 'DONT_CARE';
        $this->geoTargetTypeSetting->negativeGeoTargetType = 'DONT_CARE';
    }

    function setBudgetAmount($budgetAmount) {
        if(isset($this->budget->amount)) {
            unset($this->budget->amount);
        }
        $this->budget->amount = new Money($budgetAmount);
    }

    function budgetOperation($operator = 'ADD') {
        $operations = array();

        // Create operation.
        $operation = new BudgetOperation();
        $operation->operand = $this->budget;
        $operation->operator = $operator;
        $operations[] = $operation;

        // Make the mutate request.
        $result = $this->budgetService->mutate($operations);
        $this->campaign->budget = $result->value[0];
//        $this->campaign->budget->budgetId = $this->budget->budgetId;

        unset($operation);
        unset($operations);

        return $result;
    }

    function campaignOperation($operator = 'ADD') {
        $operations = array();
        $this->biddingStrategyConfiguration->biddingScheme = $this->biddingScheme;
        $this->campaign->biddingStrategyConfiguration = $this->biddingStrategyConfiguration;
        $this->campaign->settings[] = $this->keywordMatchSetting;
        $this->campaign->networkSetting = $this->networkSetting;
        $this->campaign->frequencyCap = $this->frequencyCap;
        $this->campaign->settings[] = $this->geoTargetTypeSetting;

        // Create operation.
        $operation = new CampaignOperation();
        $operation->operand = $this->campaign;
        $operation->operator = $operator;
        $operations[] = $operation;

        // Make the mutate request.
        $result = $this->campaignService->mutate($operations);

        unset($operation);
        unset($operations);

        return $result;
    }
}
 