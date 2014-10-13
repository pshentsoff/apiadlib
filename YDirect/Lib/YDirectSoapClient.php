<?php
/**
 * An extension of the {@link AdsSoapClient} for the Yandex.Direct API.
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
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @author     Vadim Pshentsov <pshentsoff@gmail.com>
 * @see        AdsSoapClient
 **/

/** Required classes. **/
require_once dirname(__FILE__) . '/../../Common/Lib/AdsSoapClient.php';

/**
 * An extension of the {@link AdsSoapClient} for the Direct API.
 * @package APIAdLib
 * @subpackage YDirect/Lib
 */
class YDirectSoapClient extends AdsSoapClient {

  protected static $DEFAULT_TRACE = 0;
  protected static $DEFAULT_EXCEPTIONS = 0;
  protected static $DEFAULT_ENCODING = "UTF-8";
  protected static $DEFAULT_SOAP_VERSION = SOAP_1_1;
  protected static $DEFAULT_URI = 'API';
  protected static $DEFAULT_LOCALE = 'ru';

  /**
   * Constructor for the Yandex.Direct API SOAP client.
   * @param string $wsdl URI of the WSDL file or <var>NULL</var> if working in
   *     non-WSDL mode
   * @param array $options the SOAP client options
   * @param AdsUser $user the user which is responsible for this client
   * @param string $serviceName the name of the service which is making this call
   * @param string $serviceNamespace the namespace of the service
   */
  public function __construct($wsdl, array $options, AdsUser $user,
        $serviceName = NULL, $serviceNamespace = NULL) {
      
     $this->CheckOptions($wsdl, $options, $user);
     
     foreach($options as $key => $value) {
        $this->SetHeaderValue($key, $value);
        }   
        
     $oAuthInfo = $user->GetOAuth2Info();
     if($oAuthInfo) {
      foreach($oAuthInfo as $key => $value) {
        $this->SetHeaderValue($key, $value);
        }
      }
      //var_dump($options);
      parent::__construct($wsdl, $options, $user, $serviceName, $serviceNamespace);
    }
    
  /**
   *
   */
  protected function CheckOptions($wsdl, &$options, $user) {
    if(!$wsdl) {
      // keys 'location' and 'uri' must be set in non-WSDL mode
      if(!array_key_exists('location', $options)) {
        if(isset($user)&&$user->GetDefaultServer()) {
          $options['location'] = $user->GetDefaultServer();
          } else {
          throw new Exception("Location must be specified at non-WSDL SOAP. Pass it as 'location' keyed value at options array or as default server at passed user class.");
          }
        }
      //TODO: check this 'uri' role for requests
      if(!array_key_exists('uri', $options)) {
        $options['uri'] = YDirectSoapClient::$DEFAULT_URI;
        }
      } else {
      // remove that keys if exists
      if(array_key_exists('location', $options)) unset($options['location']);
      if(array_key_exists('uri', $options)) unset($options['uri']);
      }
        
      if(!array_key_exists('locale', $options)) {
        if(isset($user)&&$user->GetLocale()) {
          $options['locale'] = $user->GetLocale();
          } else {
          $options['locale'] = YDirectSoapClient::$DEFAULT_LOCALE;
          }
        }
      if(!array_key_exists('trace', $options)) {
        $options['trace'] = YDirectSoapClient::$DEFAULT_TRACE ;
        }
      if(!array_key_exists('exceptions', $options)) {
        $options['exceptions'] = YDirectSoapClient::$DEFAULT_EXCEPTIONS;
        }
      if(!array_key_exists('encoding', $options)) {
        $options['encoding'] = YDirectSoapClient::$DEFAULT_ENCODING;
        }
      if(!array_key_exists('soap_version', $options)) {
        $options['soap_version'] = YDirectSoapClient::$DEFAULT_SOAP_VERSION;
        }
  }     

  /**
   * Overrides the method __doRequest().  When OAuth authentication is used
   * the URL has OAuth parameters added.
   * @param string $request the request XML
   * @param string $location the URL to request
   * @param string $action the SOAP action
   * @param string $version the SOAP version
   * @param int $one_way if set to 1, this method returns nothing
   * @return string the XML SOAP response
   */
  function __doRequest($request , $location , $action , $version, $one_way = 0) {
    
    $dom = new DomDocument('1.0', 'UTF-8'); 
    $dom->preserveWhiteSpace = false; 
    $dom->loadXML($request);
    $hdr = $dom->createElementNS('http://schemas.xmlsoap.org/soap/envelope/', 'SOAP-ENV:Header');
    $dom->documentElement->insertBefore($hdr, $dom->documentElement->firstChild);
    foreach($this->headers as $element_name => $element_value) {
      $hdr_element = $hdr->appendChild($dom->createElement($element_name));
      $hdr_element->appendChild($dom->createTextNode($element_value));
      }
    
    $request = $dom->saveXML();
   
    //$this->GenerateSoapHeader(); 
    //echo 'YDirect::__doRequest(), $request = ';
    //var_dump($request);
    
    return SoapClient::__doRequest($request, $location, $action, $version);
  }

   function __soapCall($function_name, $arguments, $options = NULL,
      $input_headers = NULL, &$output_headers = NULL) {
      
    //echo 'YDirect::__soapCall(), $input_headers = ';
    //var_dump($input_headers);

      return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
    }
    
 /**
   * Generates the SOAP header for the client.
   * @return SoapHeader the instantiated SoapHeader ready to set
   * @access protected
   */
  protected function GenerateSoapHeader() {
   
   return new SoapHeader($this->headers); 
    
  }

  /**
   * Removes the authentication token from the request before being logged.
   * @param string $request the request with sensitive data to remove
   * @return string the request with the authentication token removed
   * @access protected
   */
  protected function RemoveSensitiveInfo($request) {
    $result = preg_replace('/(.*token>)(.*)(<\/.*token>.*)/sU', '\1*****\3', $request);
    return isset($result) ? $result : $request;
  }

  /**
   * Gets the effective user the request was made against.
   * @return string the effective user the request was made against
   */
  public function GetEffectiveUser() {
    return $this->GetAdsUser()->GetLogin();
  }
  /**
   * Generates the request info message containing:
   * <ul>
   * <li>email</li>
   * <li>effectiveUser</li>
   * <li>service</li>
   * <li>method</li>
   * <li>operators</li>
   * <li>responseTime</li>
   * <li>requestId</li>
   * <li>operations</li>
   * <li>units</li>
   * <li>server</li>
   * <li>isFault</li>
   * <li>faultMessage</li>
   * </ul>
   * @return string the request info message to log
   * @access protected
   */
  protected function GenerateRequestInfoMessage() {
    return 
      'email=' . $this->GetEmail() . 
      ' effectiveUser='. $this->GetEffectiveUser() . 
      ' service=' . $this->GetServiceName().
      ' method=' . $this->GetLastMethodName() . 
      ' operators='. $this->GetLastOperators() . 
      ' responseTime='. $this->GetLastResponseTime() . 
      ' requestId='. $this->GetLastRequestId() . 
      ' operations='. $this->GetLastOperations() . 
      ' units='. $this->GetLastUnits() . 
      ' server=' . $this->GetServer().
      ' isFault=' . ($this->IsFault() ? 'true' : 'false').
      ' faultMessage=' . $this->GetLastFaultMessage();
  }


}

?>
