<?php

/**
 * APIAdLib samples
 *  
 * This sample demonstrate simple work with library with autoloading
 * Sample demonstrate using nonWSDL-SOAP+OAUTH protocols and calls PingAPI(), 
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

error_reporting(E_STRICT | E_ALL);

define('_APIADLIB_PATHTO', '/..'); // path to apiadlib dir
define('_APIADLIB_AUTHINI_PATHTO', '../../../ini/'); // path to ini files

require_once dirname(__FILE__) . _APIADLIB_PATHTO . '/apiadlib.autoload.php';

echo __FILE__."<br />";

$wsdlurl = NULL;
$soap_params = array(
        'trace'=> 1,
    );

/**
 * Create Yandex.Direct user instance
 *  
 */  
$auth_file = _APIADLIB_AUTHINI_PATHTO.'auth_ydirect.ini';
echo $auth_file."<br />";
$user = new YDirectUser($auth_file);
$user->ValidateUser();
//var_dump($user);


/**
 * Initializing SOAP client object
 *  
 */ 
$client = new YDirectSoapClient($wsdlurl, $soap_params, $user, 'APIAdLib', 'API');
var_dump($client);

/**
 * Testing connection by calling PingAPI() method
 * 
 */  
$result = $client->PingAPI();
echo "<br />PingAPI(): "; var_dump($result);
//var_dump($client);

# Gets array of available API versions
$result = $client->GetAvailableVersions();
echo "<br />GetAvailableVersions(): "; var_dump($result);
//var_dump($client);

/**
 * Get connected version
 * 
 */  
$result = $client->GetVersion();
echo "<br />GetVersion(): "; var_dump($result);
//var_dump($client);

/**
 * Get user's Info
 *  
 */ 
$result = $client->GetClientInfo();
echo "<br />GetClientInfo(): "; var_dump($result);
//var_dump($client);

/**
  * Get User's clients list (user is agency account)
  *   
  */  
$clients_list = $client->GetClientsList();
echo "<br />GetClientsList(): "; var_dump($clients_list);
//var_dump($client);

/**
 * Get list of clients campaigns with short data
 *  
 */ 
if(count((array)$clients_list) > 0) {
  $clients_logins = array();
  foreach((array)$clients_list as $key => $client_data) {
    $clients_logins[] = $client_data->Login;
    }
  $clients_campaigns = $client->GetCampaignsList($clients_logins);
  echo "<br />GetCampaignsList(): "; var_dump($clients_campaigns);
  //var_dump($client);
  } else {
  echo "<br />GetCampaignsList(): There is no clients to get any campaigns.";
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
  $campaigns_params = $client->GetCampaignsParams($campaigns_ids);
  echo "<br />GetCampaignsParams(): "; var_dump($campaigns_params);
  //var_dump($client);
  } else {
  echo "<br />GetCampaignsParams(): There is no campaigns to get params.";
  }


