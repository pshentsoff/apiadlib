<?php
/**
 * @file        AdWordsAdGroupBidModifierExt.php
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

class AdWordsAdGroupBidModifierExt extends AdWordsCommonExt {

    const SERVICE_NAME = 'AdGroupBidModifierService';
    const WRAPPED_CLASS_NAME = 'AdGroupBidModifier';

    public function setDefaults() {
        parent::setDefaults();
        $this->bidModifier->criterion = new Platform();
        $this->bidModifier->criterion->id = APIADLIB_CRITERION_ID;
        $this->bidModifier->bidModifier = APIADLIB_BID_MODIFIER;
    }

}