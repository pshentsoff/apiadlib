<?php

/**
 * APIAdLib samples
 *  
 * This is first sample of how to works with APIAdLib library
 * Sample demonstrate using nonWSDL-SOAP+OAUTH protocols 
 * and calls sample API methods 
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

/** Required classes. **/
require_once dirname(__FILE__) . _APIADLIB_PATHTO.'/YDirect/Lib/YDirectSoapClient.php';
require_once dirname(__FILE__) . _APIADLIB_PATHTO.'/YDirect/Lib/YDirectUser.php';

echo __FILE__."<br />";

$wsdlurl = NULL;
$soap_params = array(
        'trace'=> 1,
  //      'passphrase' => ''
    );

$auth_file = _APIADLIB_AUTHINI_PATHTO.'auth_ydirect.ini';
echo $auth_file."<br />";
$user = new YDirectUser($auth_file);
$user->ValidateUser();
//var_dump($user);


# Initializing SOAP object
$client = new YDirectSoapClient($wsdlurl, $soap_params, $user, 'APIAdLib', 'API');
var_dump($client);

$result = $client->PingAPI();
echo "<br />PingAPI(): "; var_dump($result);
//var_dump($client);


$result = $client->GetAvailableVersions();
echo "<br />GetAvailableVersions(): "; var_dump($result);
//var_dump($client);

$result = $client->GetVersion();
echo "<br />GetVersion(): "; var_dump($result);
//var_dump($client);

$result = $client->GetClientInfo();
echo "<br />GetClientInfo(): "; var_dump($result);
//var_dump($client);

?>
