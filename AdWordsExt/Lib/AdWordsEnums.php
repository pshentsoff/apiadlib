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

function advertisingChannelTypeList() {
    return array(
        'UNKNOWN' => 'unknown',
        'SEARCH' => 'search',
        'DISPLAY' => 'display',
        'SHOPPING' => 'shopping',
    );
}

function locationList() {
    return array(
        'CONTENT_LABEL' => 'Content label',
        'KEYWORD' => 'Keyword',
        'PLACEMENT' => "Placement",
        'VERTICAL' => "Vertical",
        'USER_LIST' => 'User lists',
        'USER_INTEREST' => 'User interests',
        'MOBILE_APPLICATION' => 'Mobile application',
        'MOBILE_APP_CATEGORY' => 'Mobile application categories',
        'PRODUCT' => 'Product target',
        'PRODUCT_PARTITION' => 'Product partition',
        'IP_BLOCK' => 'IP addresses to exclude',
        'WEBPAGE' => "Webpages of an advertiser's website to target",
        'LANGUAGE' => 'Languages to target',
        'LOCATION' => 'Geographic regions to target',
        'AGE_RANGE' => 'Age Range to exclude',
        'CARRIER' => 'Mobile carriers to target',
        'OPERATING_SYSTEM_VERSION' => 'Mobile operating system versions to target',
        'MOBILE_DEVICE' => 'Mobile devices to target',
        'GENDER' => 'Gender to exclude',
        'PROXIMITY' => 'Proximity (area within a radius) to target',
        'PLATFORM' => 'Platforms to target',
        'AD_SCHEDULE' => 'AdSchedule or specific days and time intervals to target',
        'LOCATION_GROUPS' => 'Targeting based on location groups',
        'PRODUCT_SCOPE' => 'Scope of products',
        'PRODUCT_SALES_CHANNEL' => "Targeting based on product's sales channel",
        'UNKNOWN' => 'Used for return value',
    );
}