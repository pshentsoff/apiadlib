<?php
/**
 * An extension of the {@link SoapClientFactory} for the Yandex.Direct API.
 *
 * PHP version 5
 *
 * Copyright 2012, Vadim Pshentsov. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    APIAdLib
 * @subpackage YDirect/Lib
 * @category   WebServices
 * @copyright  2012, Vadim Pshentsov. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Vadim Pshentsov <pshentsoff@gmail.com>
 * @see        SoapClientFactory
 **/

/** Required classes. **/
require_once dirname(__FILE__) . '/../../Common/Lib/AdsUser.php';
require_once dirname(__FILE__) . '/../../Common/Lib/SoapClientFactory.php';

/**
 * Factory class for SOAP clients for the Yandex.Direct API.
 * @package APIAdLib
 * @subpackage YDirect/Lib
 */
class YDirectSoapClientFactory extends SoapClientFactory {
  /**
   * The constructor for the Yandex.Direct API SOAP client factory.
   * @param AdsUser $user the user which the client will use for credentials
   * @param string $version the version to generate clients for
   * @param string $server the server to generate clients for
   * @param bool $validateOnly if the clients should be created in validateOnly mode
   * @param bool $partialFailure if the service should be created in partialFailure mode
   */
  public function __construct(AdsUser $user, $version, $server, $validateOnly, $partialFailure) {
      $headerOverrides = array();
      if (isset($validateOnly) || isset($partialFailure)) {
        $headerOverrides['validateOnly'] = $validateOnly;
        $headerOverrides['partialFailure'] = $partialFailure;
      }
      parent::__construct($user, $version, $server, 'ydirect', $headerOverrides);
    }

  /**
   * Initiates a require_once for the service.
   * @param string $serviceName the service to instantiate
   */
  public function DoRequireOnce($serviceName) {
    $service = implode("/", array(dirname(__FILE__), $serviceName . '.php'));
    if(file_exists($service)) {
      require_once $service;
      return TRUE;
      } else {
      return FALSE;
      } 
  }
  
  /**
   * Generates a SOAP client for the given service name. Generates a user level
   * error if this instalation of PHP does not have the extension for SOAP
   * installed.
   * @param string $serviceName the name of the service to generate a client for
   * @return AdsSoapClient an instantiated SOAP client
   */
  public function GenerateSoapClient($serviceName) {
    if (extension_loaded('soap')) {
      if($this->DoRequireOnce($serviceName)) {
        $soapClient = $this->GenerateServiceClient($serviceName);
        } else {
        $soapClient = 'TODO: YDirectSoapClientFactory - Create SoapClient for call to service';
        }
      return $soapClient;
    } else {
      trigger_error('This client library requires the SOAP extension to be'
          . ' activated. See http://php.net/manual/en/soap.installation.php for'
          . ' details.', E_USER_ERROR);
    }
  }

}

?>
