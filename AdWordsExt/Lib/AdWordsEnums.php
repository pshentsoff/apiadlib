<?php
/**
 * @file        AdWordsEnums.php
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

function campaignStatusList() {
    return array(
        'ACTIVE' => 'active',
        'PAUSED' => 'paused',
        'DELETED' => 'deleted',
    );
}

function budgetDeliveryMethodList() {
    return array(
        'STANDARD' => 'standard',
        'ACCELERATED' => 'accelerated',
    );
}

function adServingOptimizationStatusList() {
    return array(
        'OPTIMIZE' => 'optimize',
        'CONVERSION_OPTIMIZE' => 'conversion optimize',
        'ROTATE' => 'rotate',
        'ROTATE_INDEFINITELY' => 'rotate indefinitely',
        'UNAVAILABLE' => 'unavailable',
    );
}

function biddingStrategyTypeList() {
    return array(
        'BUDGET_OPTIMIZER' => 'budget optimizer',
        'CONVERSION_OPTIMIZER' => 'conversion optimizer',
        'MANUAL_CPC' => 'manual cpc',
        'MANUAL_CPM' => 'manual cpm',
        'PAGE_ONE_PROMOTED' => 'page one promoted',
        'PERCENT_CPA' => 'percent cpa',
        'TARGET_SPEND' => 'target spend',
        'ENHANCED_CPC' => 'enhanced cpc',
        'TARGET_CPA' => 'target cpa',
        'NONE' => 'none',
        'UNKNOWN' => 'unknown',
    );
}

function timeUnitList() {
    return array(
        'MINUTE' => 'minute',
        'HOUR' => 'hour',
        'DAY' => 'day',
        'WEEK' => 'week',
        'MONTH' => 'month',
        'LIFETIME' => 'lifetime',
    );
}

function levelList() {
    return array(
        'CREATIVE' => 'creative',
        'ADGROUP' => 'adgroup',
        'CAMPAIGN' => 'campaign',
        'UNKNOWN' => 'unknown',
    );
}

function positiveGeoTargetTypeList() {
    return array(
        'DONT_CARE' => "don't care",
        'AREA_OF_INTEREST' => 'area of interest',
        'LOCATION_OF_PRESENCE' => 'location of presence',
    );
}

function budgetPeriodList() {
    return array(
        'DAILY' => 'daily',
    );
}