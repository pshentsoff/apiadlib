<?php

/**
  *
  * APIAdLib Samples
  *  
  *   
  * PHP Version 5.2    
  *   
  * NOTE: 
  *  - Sample doesn't perform any changes with Ad system's data
  *  - Sample tested in Yandex.Direct, in sandbox and real account too. 
  *    - then just convert it:
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

require_once dirname(__FILE__) . '/../apiadlib.autoload.php';

$auth['xml'] = '../YDirectExt/auth.xml';
$auth['ini'] = '../YDirect/auth.ini';
$auth['xmlstr'] = <<<EOXML
<?xml version="1.0" encoding="utf-8"?>
<auth>
  <login>ENTER_YOUR_LOGIN_HERE</login>
  <email>ENTER_YOUR_EMAIL_HERE</email>
  <oauth>
    <locale>ru</locale>
    <login>ENTER_YOUR_OAUTH_LOGIN_HERE</login>
    <token comment="oauth token">ENTER_YOUR_OAUTH_TOKEN_HERE</token>
    <application_id comment="On oauth.yandex.ru called as client_id">ENTER_YOUR_APPLICATION_ID_HERE</application_id>
  </oauth>
</auth>
EOXML;

$settings['xml'] = '../YDirectExt/settings.xml'; 
$settings['ini'] = '../YDirect/settings.ini'; 
$settings['xmlstr'] = <<<EOXML
<?xml version="1.0" encoding="utf-8"?>
<settings>
  <server>
    <default_server>https://api-sandbox.direct.yandex.ru/api/v4/</default_server>
    <default_version>v4</default_version>
  </server>
  <local comment="Local settings">
    <default_locale>ru</default_locale>
  </local>
  <soap>
    <compression comment="Enable/disable gzip compression on SOAP requests and responses.">1</compression>
    <compression_level comment="The level of gzip compression to use, from 1 to 9. The higher the level the greater the compression and time needed to perform the compression. The recommended and default value is 1.">1</compression_level>
    <wsdl_cache comment="The type of WSDL caching to use. The possible values are 0 (none), 1 (disk), 2 (memory), or 3 (disk and memory). The default value is 0.">0</wsdl_cache>
  </soap>
  <ssl>
    <verify_peer comment="Enable/disable peer verification of SSL certificates. If enabled, specify either 'ca_path' or 'ca_file'.">0</verify_peer>
    <ca_path comment="The certificate authority directory to search in when performing peer validation. For example: /etc/ssl/certs"></ca_path>
    <ca_file commit="The certificate authority file to use when performing peer validation."></ca_file>
  </ssl>
  <logging>
    <path_relative comment="Log directory is either an absolute path, or relative path to the YDirectUser.php file.">0</path_relative>
    <lib_log_dir_path>T:\home\local\ams\logs</lib_log_dir_path>
  </logging>
</settings>
EOXML;

$yd_user['xml'] = new YDirectUserExt($auth['xml'], $settings['xml']);  
$yd_user['ini'] = new YDirectUserExt($auth['ini'], $settings['ini']);
$yd_user['xmlstr'] = new YDirectUserExt($auth['xmlstr'], $settings['xmlstr']);

var_dump($yd_user);  
