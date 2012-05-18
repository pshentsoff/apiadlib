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

$wsdlurl = NULL;
$soap_params = array(
        'trace'=> 1,
    );

/**
 * Create Yandex.Direct user instance
 *  
 */  
$auth_file = _APIADLIB_AUTHINI_PATHTO.'auth_ydirect.ini';
$user = new YDirectUser($auth_file);
var_dump($user);

/** Tests using factory **/

/**
 * Testing connection by calling PingAPI() method
 * 
 */  
$result = $user->PingAPI();
echo "<br />PingAPI(): "; var_dump($result);

/**
 * Gets array of available API versions
 *  
 */ 
$result = $user->GetAvailableVersions();
echo "<br />GetAvailableVersions(): "; var_dump($result);
/**
 * Get connected version
 * 
 */  
$result = $user->GetVersion();
echo "<br />GetVersion(): "; var_dump($result);

/**
 * Get user's Info
 *  
 */ 
$result = $user->GetClientInfo();
echo "<br />GetClientInfo(): "; var_dump($result);

/**
  * Get User's clients list (user is agency account)
  *   
  */  
$clients_list = $user->GetClientsList();
echo "<br />GetClientsList(): "; var_dump($clients_list);

/**
 * Get clients units balance
 * Get list of clients campaigns with short data
 *  
 */ 
if(count((array)$clients_list) > 0) {
  $clients_logins = array();
  foreach((array)$clients_list as $key => $client_data) {
    $clients_logins[] = $client_data->Login;
    }
  $clients_units = $user->GetClientsUnits($clients_logins);
  echo "<br />GetClientsUnits(): "; var_dump($clients_units);
  $clients_campaigns = $user->GetCampaignsList($clients_logins);
  echo "<br />GetCampaignsList(): "; var_dump($clients_campaigns);
  } else {
  echo "<br />GetClientsUnits(), GetCampaignsList(): There is no clients to get any campaigns.";
  }
  
/**
 * Gets full info for all clients campaigns at account
 * 
 */  
if(count((array)$clients_campaigns) > 0) {
  $campaigns_ids = array('CampaignIDS' => array());
  foreach((array)$clients_campaigns as $key => $campaign_data) {
    $campaigns_ids['CampaignIDS'][] = $campaign_data->CampaignID;
    } 
  $campaigns_params = $user->GetCampaignsParams($campaigns_ids);
  echo "<br />GetCampaignsParams(): "; var_dump($campaigns_params);
  } else {
  echo "<br />GetCampaignsParams(): There is no campaigns to get params.";
  }
