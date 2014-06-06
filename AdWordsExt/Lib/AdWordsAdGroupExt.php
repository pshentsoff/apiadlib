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

class AdWordsAdGroupExt extends AdWordsCommonExt {

    const SERVICE_NAME = 'AdGroupService';
    const WRAPPED_CLASS_NAME = 'AdGroup';

    function setDefaults() {
    }

}