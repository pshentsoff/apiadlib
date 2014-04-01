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
    public $budgetAmount;
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
    }


}
 