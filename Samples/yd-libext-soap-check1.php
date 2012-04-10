<?php

/**
 * APIAdLib Samples
 *  Check <Lib>Ext SOAP Client for Yandex.Direct  
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
$auth_file = _APIADLIB_AUTHINI_PATHTO.'auth_ydirect.ini';
$settings_file = dirname(__FILE__)._APIADLIB_PATHTO.'/YDirectExt/settings.xml';
$user = new YDirectUserExt($auth_file, $settings_file);

/**
 * Initializing SOAP client object
 *  
 */ 
$wsdlurl = NULL;
$soap_params = array(
        'trace'=> 1,
    );
$client = new YDirectSoapClientExt($wsdlurl, $soap_params, $user, 'APIAdLib', 'API');
var_dump($client);

/**
 * Get user's Info
 *  
 */ 

$request = <<<EOREQ
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="API" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"><SOAP-ENV:Body><ns1:GetClientInfo/></SOAP-ENV:Body></SOAP-ENV:Envelope>
EOREQ;
// <? /** - this is for my syntax highlighter which's not understand heredoc **/
$response = $client->__doRequest($request);
echo "Request: "; var_dump($request);
echo 'Response :';  var_dump($response);
