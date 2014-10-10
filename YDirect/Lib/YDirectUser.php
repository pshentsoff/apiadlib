<?php
/**
 * User class for the Yandex.Direct API to create SOAP clients to the available API
 * services.
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
 * @subpackage Lib
 * @category   WebServices
 * @copyright  2012, Vadim Pshentsov. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @author     Vadim Pshentsov <pshentsoff@gmail.com>
 * @see        AdsUser
 */

/** Required classes. **/
require_once dirname(__FILE__) . '/../../Common/Util/AuthToken.php';
require_once dirname(__FILE__) . '/../../Common/Lib/AdsUser.php';
require_once dirname(__FILE__) . '/../Util/YDirectReportUtils.php';
require_once 'YDirectSoapClientFactory.php';

/**
 * User class for the Yandex.Direct API to create SOAP clients to the available API
 * services.
 * @package APIAdLib
 * @subpackage YDirect/Lib
 */
class YDirectUser extends AdsUser {

  protected static $LIB_VERSION = '0.2.0-dev';
  protected static $LIB_NAME = 'APIAdLib';

  /**
   * The default version that is loaded if the settings INI cannot be loaded.
   * @var string default version of the Yandex.Direct API
   */
  protected static $DEFAULT_VERSION = 'v4';

  /**
   * The default server that is loaded if the settings INI cannot be loaded.
   * @var string default server of the Yandex.Direct API
   */
  protected static $DEFAULT_SERVER = 'https://soap.direct.yandex.ru/v4/soap/';
  
  /**
   * The default locale for messages that used if settings INI can't be loaded
   * @var string default locale
   */
  protected static $DEFAULT_LOCALE = 'ru';

  const OAUTH2_SCOPE = 'https://oauth.yandex.ru/authorize';
  const OAUTH2_HANDLER_CLASS = 'SimpleOAuth2Handler';

  /**
   * The name of the SOAP header that represents the user agent making API
   * calls.
   * @var string
   */
  const USER_AGENT_HEADER_NAME = 'userAgent';

  protected $libVersion;
  protected $libName;

  protected $defaultVersion;
  protected $defaultServer;

  protected $password;
  protected $userAgent;

  protected $login;
  protected $email;
  
  protected $locale;
  
  /**
   * The YDirectUser constructor.
   * <p>The YDirectUser class can be configured in one of two ways:
   * <ol><li>Using an authenitcation INI file</li>
   * <li>Using supplied credentials</li></ol></p>
   * <p>If an authentication INI file is provided and successfully loaded, those
   * values will be used unless a corresponding parameter overwrites it.
   * If the authentication INI file is not provided (e.g. it is <var>NULL</var>)
   * the class will attempt to load the default authentication file at the path
   * of "../auth.ini" relative to this file's directory. Any corresponding
   * parameter, which is not <var>NULL</var>, will, however, overwrite any
   * parameter loaded from the default INI.</p>
   * <p>Likewise, if a custom settings INI file is not provided, the default
   * settings INI file will be loaded from the path of "../settings.ini"
   * relative to this file's directory.</p>
   * @param string $authenticationIniPath the absolute path to the
   *     authentication INI or relative to the current directory (cwd). If
   *     <var>NULL</var>, the default authentication INI file will attempt to be
   *     loaded
   * @param string $email the email of the user (required header). Will
   *     overwrite the email entry loaded from any INI file
   * @param string $password the password of the user (required header). Will
   *     overwrite the password entry loaded from any INI file
   * @param string $developerToken the developer token (required header). Will
   *     overwrite the developer token entry loaded from any INI file
   * @param string $applicationToken the application token (required header).
   *     Will overwrite the application token entry loaded from any INI file
   * @param string $userAgent the user agent name (required header). Will
   *     be prepended with the library name and version. Will also overwrite the
   *     userAgent entry in any INI file
   * @param string $clientId the client email or ID to make the request against
   *     (optional header). Will overwrite the clientId, clientEmail, or
   *     clientCustomerId entries loaded from any INI file
   * @param string $settingsIniPath the path to the settings INI file. If
   *     <var>NULL</var>, the default settings INI file will be loaded
   * @param string $authToken the authToken to use for requests
   * @param array $oauthInfo the OAuth information to use for requests
   */   
  public function __construct($authenticationIniPath = NULL, $login = NULL, $email = NULL,
      $password = NULL, $developerToken = NULL, $applicationToken = NULL,
      $userAgent = NULL, $clientId = NULL, $settingsIniPath = NULL,
      $authToken = NULL, $oauthInfo = NULL 
      ) {
      
    parent::__construct();

    $this->libVersion = YDirectUser::$LIB_VERSION;
    $this->libName = YDirectUser::$LIB_NAME;
    $this->defaultVersion = YDirectUser::$DEFAULT_VERSION;
    $this->defaultServer = YDirectUser::$DEFAULT_SERVER;
    
    if (isset($authenticationIniPath)) {
      $authenticationIni = parse_ini_file(realpath($authenticationIniPath), TRUE);
      } else {
      $authenticationIni = parse_ini_file(dirname(__FILE__) . '/../auth.ini', TRUE);
      }

    $login = $this->GetAuthVarValue($login, 'login', $authenticationIni);
    $email = $this->GetAuthVarValue($email, 'email', $authenticationIni);
    $oauthInfo = $this->GetAuthVarValue($oauthInfo, 'OAUTH', $authenticationIni);
        

    $this->SetLogin($login);
    $this->SetEmail($email);
    $this->SetOAuthInfo($oauthInfo);

    if (!isset($settingsIniPath)) {
      $settingsIniPath = dirname(__FILE__) . '/../settings.ini';
    }

    $this->LoadSettings(
      $settingsIniPath,
      $this->defaultVersion,
      $this->defaultServer,
      dirname(__FILE__),
      dirname(__FILE__));
  }

  public function GetClientLibraryIdentifier() {
    return NULL;
  }

  protected function GetOAuthScope($server = NULL) {
    return YDirectUser::OAUTH2_SCOPE;
  }

  /**
   * Overrides {@link AdsUser::LoadSettings()}  
   * added settings - locale
   */  
   public function LoadSettings($settingsIniPath, $defaultVersion, $defaultServer, $defaultLogsDir, $logsRelativePathBase) {
      
      $settingsIni = parse_ini_file($settingsIniPath, TRUE);
      if (isset($settingsIni)) {
        if(array_key_exists('LOCAL', $settingsIni)) {
          if(array_key_exists('DEFAULT_LOCALE', $settingsIni['LOCAL'])) {
            $this->SetLocale($settingsIni['LOCAL']['DEFAULT_LOCALE']);
            }
          }
        }
      
      parent::LoadSettings($settingsIniPath, $defaultVersion, $defaultServer, $defaultLogsDir, $logsRelativePathBase);
   } 
   
  /**
   * Overrides {@link AdsUser::InitLogs()}, adding an additional log for report
   * download requests.
   */
  protected function InitLogs() {
    parent::InitLogs();
    Logger::LogToFile(YDirectReportUtils::$LOG_NAME,
        $this->GetLogsDirectory() . "/report_download.log");
    Logger::SetLogLevel(YDirectReportUtils::$LOG_NAME, Logger::$FATAL);
  }

  /**
   * Overrides {@link AdsUser::LogDefaults()}, setting an additional log level for
   * report download requests.
   */
  public function LogDefaults() {
    parent::LogDefaults();
    Logger::SetLogLevel(YDirectReportUtils::$LOG_NAME, Logger::$ERROR);
  }

  /**
   * Overrides {@link AdsUser::LogErrors()}, setting an additional log level for report
   * download requests.
   */
  public function LogErrors() {
    parent::LogErrors();
    Logger::SetLogLevel(YDirectReportUtils::$LOG_NAME, Logger::$ERROR);
  }

  /**
   * Overrides {@link AdsUser::LogAll()}, setting an additional log level for report
   * download requests.
   */
  public function LogAll() {
    parent::LogAll();
    Logger::SetLogLevel(YDirectReportUtils::$LOG_NAME, Logger::$INFO);
  }
    

  /**
   * Gets the email address of the user login.
   * @return string the user login email
   */
  public function GetEmail() {
    return $this->email;
  }

  /**
   * Sets the email address of the user login.
   * @param string $email the user login email
   */
  public function SetEmail($email) {
    $this->email = $email;
  }

  /**
   * Validates the user and throws a validation error if there are any errors.
   * @throws ValidationException if there are any validation errors
   */
  public function ValidateUser() {
    if ($this->GetOAuth2Info() != NULL) {
      $this->ValidateOAuth2Info();
    }
  }

  /**
   * @see AdsUser::GetUserAgentHeaderName()
   */
  public function GetUserAgentHeaderName() {
    return self::USER_AGENT_HEADER_NAME;
  }

  /**
   * @see AdsUser::GetClientLibraryNameAndVersion()
   */
  public function GetClientLibraryNameAndVersion() {
    return array($this->libName, $this->libVersion);
  }

  /**
   * Gets the value of user login
   * @return string value of user login
   */        
  public function GetLogin() {
    return $this->login;
  }

  /** 
   * Sets the value of user login
   * @param string $login setting
   */
   public function SetLogin($login) {
    $this->login = $login;
   }
         
  /**
   * Gets the value of locale setting
   * @return string value of locale setting
   */        
  public function GetLocale() {
    return $this->locale;
  }

  /** 
   * Sets the value of locale
   * @param string $locale setting
   */
   public function SetLocale($locale) {
    $this->locale = $locale;
   }
   
  /**
   * Gets the service by its service name and group.
   * @param $serviceName the service name
   * @param string $version the version of the service to get. If
   *     <var>NULL</var>, then the default version will be used
   * @param string $server the server to make the request to. If
   *     <var>NULL</var>, then the default server will be used
   * @param SoapClientFactory $serviceFactory the factory to create the client.
   *     If <var>NULL</var>, then the built-in SOAP client factory will be used
   * @param bool $validateOnly if the service should be created in validateOnly
   *     mode
   * @param bool $partialFailure if the service should be created in
   *     partialFailure mode
   * @return SoapClient the instantiated service
   */
  public function GetService($serviceName, $version = NULL, $server = NULL,
      SoapClientFactory $serviceFactory = NULL, $validateOnly = NULL,
      $partialFailure = NULL) {
    $this->ValidateUser();
    if (!isset($serviceFactory)) {
      if (!isset($version)) {
        $version = $this->GetDefaultVersion();
      }

      if (!isset($server)) {
        $server = $this->GetDefaultServer();
      }

      $serviceFactory = new YDirectSoapClientFactory($this, $version, $server,
          $validateOnly, $partialFailure);
    }

    return parent::GetServiceSoapClient($serviceName, $serviceFactory);
  }
  
  /**
   * Loads the classes within a service, so they can be used before the service
   * is constructed.
   * @param $serviceName the service name
   * @param string $version the version of the service to get. If
   *     <var>NULL</var>, then the default version will be used
   */
  public function LoadService($serviceName, $version = NULL) {
    if (!isset($version)) {
      $version = $this->GetDefaultVersion();
    }
    $serviceFactory = new YDirectSoapClientFactory($this, $version, NULL, NULL, NULL);
    $serviceFactory->DoRequireOnce($serviceName);
  }

  /**
   * Get the default OAuth2 Handler for this user.
   * @param NULL|string $className the name of the oauth2Handler class or NULL
   * @return mixed the configured OAuth2Handler class
   */
  public function GetDefaultOAuth2Handler($className = NULL) {
    $className = !empty($className) ? $className : self::OAUTH2_HANDLER_CLASS;
    return new $className($this->GetAuthServer(), self::OAUTH2_SCOPE);
  }

  /**
   * Handles calls to undefined methods.
   * @param string $name the name of the method being called
   * @param array $arguments the arguments passed to the method
   * @return mixed the result of the correct method call, or nothing if there
   *     is no correct method
   */
  public function __call($name, $arguments) {
    array_unshift($arguments, $name);
    return call_user_func_array(array($this, 'GetService'), $arguments);
  }

}
