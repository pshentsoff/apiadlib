<?php

/**
  * APIAdLib 
  *   YDirect utilities
  *
  * PHP Version 5.2    
  *     
  * @package    APIAdLib
  * @category   WebServices
  * @copyright  2012, Vadim Pshentsov. All Rights Reserved.
  * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
  * @author     V.Pshentsov <pshentsoff@gmail.com>
  *    
  */

/**
  * Function check object is 'SoapFault' and can throw exception with message 
  *   and code from SoapFault 
  * @param $object - checked object
  * @param $throwException - throw or not exception
  * @result - boolean TRUE if object is 'SoapFault' and FALSE if not
  */            
function YDirectIsSoapFault($object, $throwException = TRUE) {

  if(!isset($object) || empty($object)) {
    if($throwException) {
      throw new YDirectException('Parameter is NULL or empty');
      }
    }
     
  if(is_a($object, 'SoapFault')) {
    if($throwException) {
      throw new YDirectException($object->getMessage(), $object->getCode());
      } else {
      return TRUE;
      }
    }
    
  return FALSE;
}                   

/**
  * Function check is soap client created for account with agency role or not
  * @param $soap_client - YDirectSOAPClient<Ext> object
  * @result - TRUE if role ia 'Agency', FALSE - if not
  */       
function YDirectIsAgency($soap_client) {

  $resp = $soap_client->GetClientInfo();
  
  YDirectIsSoapFault($resp);
  if(is_array($resp)) $resp = array_shift($resp);
  //DEBUG: 
  //kpr($resp);
  
  $role = huf_issetor($resp->Role, '');   
  return (strtolower($role) == 'agency');
  
  }    

/**
 * Function return agency clients login list. If agency hasn't clients 
 *  - then function return FALSE.
 * 
 * @param $soap_client must be YDirectSoapClient<Ext> object 
 * @result - array with agency clients logins or FALSE   
 */ 
function YDirectGetAgencyClientLogins($soap_client) {
 
  try {
  
    if(!YDirectIsAgency($soap_client)) return FALSE;
    
    $resp = $soap_client->GetClientsList();
    
    YDirectIsSoapFault($resp);
      
    if(is_array($resp)) {
      $clients = array();
      foreach($resp as $client) {
        $clients[] = $client->Login;
        }
      return $clients;
      }
    
    } catch (Exception $e) {
      throw $e;
    }
    
  return FALSE;  
}

/**
  * Function gets array with short clients info objects
  * @param $soap_client - YDirectSOAPClient<Ext> object
  * @param $withArchived - return or not clients with archived status
  * @result - array with clients info objects or FALSE
  */         
function YDirectGetAgencyClientsInfoShort($soap_client, $withArchived = FALSE) {

  try {
  
    if(!YDirectIsAgency($soap_client)) return FALSE;
    /**
    $filter = (object)array(
      'Filter' => array(
        'StatusArch' => IntToYDirectBool($withArchived),
        ),
      );
    
    $infos = $soap_client->GetClientsList($filter);
       **/
    $infos = $soap_client->GetClientsList();
    YDirectIsSoapFault($infos);
    
    return $infos;
    
    } catch (Exception $e) {
    throw $e;
    }
    
    return FALSE;
}

/**
  * Function gets array with clients info objects
  * @param $soap_client - YDirectSOAPClient<Ext> object
  * @param $withArchived - return or not clients with archived status
  * @result - array with clients info objects or FALSE
  */         
function YDirectGetAgencyClientsInfo($soap_client) {

  try {
  
    if(!YDirectIsAgency($soap_client)) return FALSE;
    
    //TODO:...
    
    } catch (Exception $e) {
    throw $e;
    }
    
    return FALSE;
}

/**
 *  Function to convert Yandex Direct boolean (Yes/No) 
 *  to int (1/0)
 *   
 */
function YDirectBoolToInt($value) {
  return (strtolower($value) == 'no') ? 0 : 1;
}

/**
 *  Function to convert int (1/0)
 *  to Yandex Direct boolean (Yes/No) 
 *   
 */
function IntToYDirectBool($value) {
  return ($value) ? 'Yes' : 'No';
} 

