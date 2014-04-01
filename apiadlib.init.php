<?php
/**
 * @file        apiadlib.init.php
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
 * @created     12.02.14
 */

error_reporting(E_STRICT | E_ALL);

require_once 'AdWordsExt/Lib/AdWordsEnums.php';

require_once 'Common/Lib/ValidationException.php';
require_once 'Common/Util/OAuth2Handler.php';
require_once 'apiadlib.functions.inc';
require_once 'apiadlib.autoload.php';
