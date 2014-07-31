<?php

/**
  * APIAdLib 
  *   Constants
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

defined('ADWORDS_VERSION') or define('ADWORDS_VERSION', 'v201406');
//defined('ADWORDS_VERSION') or define('ADWORDS_VERSION', 'v201402');

/**
 *  Local settings
 */
if(!defined('_APIADLIB_LOCAL_PHP_TIME_LIMIT'))
  define('_APIADLIB_LOCAL_PHP_TIME_LIMIT', 0);
if(!defined('_APIADLIB_LOCAL_DEFAULT_SOCKET_TIMEOUT'))
  define('_APIADLIB_LOCAL_DEFAULT_SOCKET_TIMEOUT', 480);
if(!defined('_APIADLIB_LOCAL_DEFAULT_LOCALE'))
  define('_APIADLIB_LOCAL_DEFAULT_LOCALE', 'ru'); 

/**
 *  SOAP Constant parameters
 */   
if(!defined('_APIADLIB_SOAP_COMPRESSION_DEFAULT')) 
  define('_APIADLIB_SOAP_COMPRESSION_DEFAULT', TRUE); 
   
if(!defined('_APIADLIB_SOAP_COMPRESSION_LEVEL_MIN')) 
  define('_APIADLIB_SOAP_COMPRESSION_LEVEL_MIN', 1); 
   
if(!defined('_APIADLIB_SOAP_COMPRESSION_LEVEL_MAX')) 
  define('_APIADLIB_SOAP_COMPRESSION_LEVEL_MAX', 9);  

if(!defined('_APIADLIB_SOAP_COMPRESSION_LEVEL_DEFAULT')) 
  define('_APIADLIB_SOAP_COMPRESSION_LEVEL_DEFAULT', 1);  

/**
 * SOAP WSDL cache settings
 */
if(!defined('_APIADLIB_SOAP_WSDL_CACHE_MIN'))
  define('_APIADLIB_SOAP_WSDL_CACHE_MIN', 0); 
if(!defined('_APIADLIB_SOAP_WSDL_CACHE_MAX'))
  define('_APIADLIB_SOAP_WSDL_CACHE_MAX', 3); 
if(!defined('_APIADLIB_SOAP_WSDL_CACHE_DEFAULT'))
  define('_APIADLIB_SOAP_WSDL_CACHE_DEFAULT', WSDL_CACHE_NONE); 
  