<?php

/**
  *  APIADLib
  * This class extends functionality of {@link AdWordsSoapClient} 
  *   (what is more depenedent from Google API Common and Ads library).
  * <Lib>Ext extensions developed for using xml.     
  * 
  * Store user's authentication and settings data - mostly all that this class
  * must do. This extension added more usability to class. INI loading keeped 
  * for compatibility and XML loading added - both in more flexible kinds. Now 
  * auth and settings can be passed as INI or XML file, as parametred array and 
  * as xml data. Last two features (pass as arrays or xml) added for usability 
  * with DB in any CMS without developing special data layers - just store auth
  * and settings as serialized array or xml text and pass it to class 
  * constructor.
  * Examples:
  *   You use one kind of settings to connect to ads servers that stored in ini 
  *   file, but several accounts with different authentications which stored at
  *   db text field:
  *     require_once '/path/to/apiadlib/apiadlib.autoload.php';
  *     $settings = 'path/to/settings.ini';
  *     $auth = YourDBFunctionGetAuthAsXML($auth_id);
  *     $user = new AdWordsUserExt($auth, $settings);       
  *     
  * NOTES:
  *   - Due to PHP standards there is 'bad practice' to override functions 
  *     and changes their parameters. But sometimes need to do this =)
  *     To avoid strict messages use report settings that bypassed 
  *     'Strict standard' messages:
  *       error_reporting(E_ALL & ~E_STRICT);
  *                              
  */
  
require_once dirname(__FILE__) . '/../../apiadlib.constants.inc';
require_once dirname(__FILE__) . '/../../apiadlib.functions.inc';

/** Required classes **/
require_once dirname(__FILE__) . '/../../AdWords/Lib/AdWordsSoapClient.php';
require_once dirname(__FILE__) . '/../../AdWordsExt/Lib/AdWordsUserExt.php';
require_once dirname(__FILE__) . '/../Util/AdWordsException.php';

  
/**
 * Extends user class {@link AdWordsSoapClient} for the Yandex.Direct 
 *  API to create SOAP clients to the available API services.
 *   
 * @package APIAdLib
 * @subpackage AdWordsExt/Lib
 */
class AdWordsSoapClientExt extends AdWordsSoapClient {

  public function __doRequest($request, $location = NULL, $action = NULL, $version = NULL, $one_way = 0) {
    if(!$location) $location = $this->location;
    if(!$version) $version = $this->_soap_version;
    return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}