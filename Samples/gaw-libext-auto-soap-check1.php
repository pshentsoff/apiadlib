<?php

/**
 * APIAdLib Samples
 *  Check Autoload Library SOAP Client for Yandex.Direct  
 *  
 * This sample demonstrate simple work with library with autoloading
 * Sample demonstrate using nonWSDL-SOAP+OAUTH protocols and calls PingAPI(), 
 * GetAvailableVersions(), GetVersion(), GetClientInfo(), GetClientsList(), 
 * GetClientsUnits(), GetCampaignsList(), GetCampaignsParams() 
 *   
 * PHP Version 5.2    
 *   
 * NOTE: 
 *  - Sample doesn't perform any changes with Ad system's data
 *  - Sample tested in Yandex.Direct, in sandbox and real account too. 
 *  - Result of calls to API functions returned as StdClass. If you need an array 
 *    - then just convert it:
 *      (array)StdClass_result
 *          
 *
 * @package    APIAdLib
 * @subpackage Samples 
 * @category   WebServices
 * @copyright  2012, Vadim Pshentsov. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @author     V.Pshentsov <pshentsoff@gmail.com>
 *
 */ 

error_reporting(E_ALL & ~E_STRICT);

define('_APIADLIB_PATHTO', '/..'); // path to apiadlib dir
define('_APIADLIB_AUTHINI_PATHTO', '../../../ini/'); // path to ini files

require_once dirname(__FILE__) . _APIADLIB_PATHTO . '/apiadlib.autoload.php';

/**
 * Create Yandex.Direct user instance
 *  
 */  
$auth_file = _APIADLIB_AUTHINI_PATHTO.'auth_adwords.ini';
$settings_file = _APIADLIB_PATHTO.'/AdWords/settings.ini';
$user = new AdWordsUserExt($auth_file, $settings_file);
var_dump($user);

/**
 * Initializing SOAP client object
 *  
 */ 
$wsdlurl = NULL;
$soap_params = array(
        'trace'=> 1,
    );

$client = new YDirectSoapClient($wsdlurl, $soap_params, $user, 'APIAdLib', 'API');
var_dump($client);

