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

//changed to tes problem svn commit

class AdWordsCampaignExt {

    public $user;
    public $campaign;

    public function __construct(AdWordsUserExt $user = null, $auth_file = null, $settings_file = null) {

        if(isset($user)) {
            $this->user = $user;
        } elseif(isset($auth_file) && isset($settings_file)) {
            $this->user = new AdWordsUserExt($auth_file, $settings_file);
        } else {
            throw new Exception('Need AdWords user object or auth & settings files.');
        }

        // Create campaign.
        $this->campaign = new Campaign();

        $this->setDefaults();
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

        $this->campaign->budget->name = 'New Campaign Budget #' . uniqid();
        $this->campaign->budget->period = 'DAILY';
        $this->setBudgetAmount(50000000);
        $this->campaign->budget->deliveryMethod = 'STANDARD';

        //@todo servingStatus и список в форме как R/O (https://developers.google.com/adwords/api/docs/reference/v201402/CampaignService.Campaign)

        $this->campaign->biddingStrategyConfiguration->biddingStrategyType = 'MANUAL_CPC';
        $this->campaign->biddingStrategyConfiguration->biddingScheme->enhancedCpcEnabled = false;

        $this->campaign->networkSetting->targetGoogleSearch = true;
        $this->campaign->networkSetting->targetSearchNetwork = true;
        $this->campaign->networkSetting->targetContentNetwork = true;

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

        $budgetService = new BudgetService();
        $result = $budgetService->mutate($operations);
        $this->campaign->budget = $result->value[0];

        unset($budgetService);
        unset($operation);
        unset($operations);

        return $result;
    }

    /**
     * @param string $operator
     * @return mixed
     */
    function campaignOperation($operator = 'ADD') {

        // Create operation.
        $operation = new CampaignOperation();
        $operation->operand = $this->campaign;
        $operation->operator = $operator;

        $operations = array();
        $operations[] = $operation;

        // Make the mutate request.
        $campaignService = new CampaignService();
        $result = $campaignService->mutate($operations);

        unset($campaignService);
        unset($operation);
        unset($operations);

        return $result;
    }
}
 