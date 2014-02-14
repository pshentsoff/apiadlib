<?php

/**
  *  APIADLib
  * This class extends functionality of {@link AdWordsUser} 
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
  */
  
require_once dirname(__FILE__) . '/../../apiadlib.constants.inc';
require_once dirname(__FILE__) . '/../../apiadlib.functions.inc';

/** Required classes **/
require_once dirname(__FILE__) . '/../../AdWords/Lib/AdWordsUser.php';
require_once dirname(__FILE__) . '/../Util/AdWordsException.php';
require_once dirname(__FILE__) . '/../../Common/Util/SimpleOAuth2Handler.php';

  
/**
 * Extends user class {@link AdWordsUser} for the Google AdWords API to create 
 *  SOAP clients to the available API services.
 * @package APIAdLib
 * @subpackage AdWordsExt/Lib
 */
class AdWordsUserExt extends AdWordsUser {

    protected $defaultLogsDir = '';
    protected $logsRelativePathBase = '';

    protected $isAuthLoaded = FALSE;
    protected $isSettingsLoaded = FALSE;

    /**
    *  Redeclare fields that declared as 'private' at original {@link AdsUser} class
    */
    protected $logsDirectory;
    protected $soapCompression;
    protected $soapCompressionLevel;
    protected $wsdlCache;
//    protected $oauthHandler;
//    protected $oauth2Handler;
    protected $authServer;
       
 /**
   * Class constructor
   * 
   * @param1 - auth data. Can be array with auth info, 
   *    xml-type content, filename of auth xml or ini file
   * @param2 - settings data. Can be array with settings info,
   *    xml-type content, filename of settings ini or xml file.       
   *         
   */     
  public function __construct($auth = NULL, $settings = NULL) {
    
    $this->defaultLogsDir = dirname(__FILE__);
    $this->logsRelativePathBase = dirname(__FILE__);

    // parent constructor not needed
//    AdsUser::__construct();
//      parent::__construct($auth,null,null,null,null,null,null,$settings);
  
    if(isset($auth)&&$auth) {
      $this->CheckAuth($auth);
      }
      
    if(isset($settings)&&$settings) {
      $this->CheckSettings($settings);
      }

      parent::__construct($auth,null,null,null,null,null,null,$settings);

      $oAuth2Handler = $this->GetOAuth2Handler();
      if(!isset($oAuth2Handler) || empty($oAuth2Handler) || !is_object($oAuth2Handler)) {
          $oAuth2Handler = new SimpleOAuth2Handler();
          $this->SetOAuth2Handler($oAuth2Handler);
      }
  }
    
  /**
   *  Function checks $auth param and load it's data
   *  @param - auth kind file or data
   *  @result TRUE/FALSE
   */           
  public function CheckAuth($auth) {
    // $auth - array with authentication data
    if(is_array($auth)) {
      $this->isAuthLoaded = $this->LoadAuth($auth);
      
      // $auth - ini file  
      } elseif(preg_match("/.ini$/i", $auth, $match)) {

      $this->isAuthLoaded = $this->LoadAuthINIFile($auth);
      
      // $auth - xml file
      } elseif(preg_match("/.xml$/i", $auth, $match)) {
        
      $this->isAuthLoaded = $this->LoadAuthXMLFile($auth);
      
      // $auth - xml data
      } elseif(preg_match("/^<\?xml /i", $auth, $match)) {
      
      $this->isAuthLoaded = $this->LoadAuthXML($auth);
      
      // $auth data not detected
      } else {

      throw new YDirectException("Wrong auth parameter kind.");

      }
     
    return $this->isAuthLoaded;
      
    }
    
      
  /**
   * Loads authentication data from array
   * 
   * @param - array with auth data
   * @result - TRUE/FALSE
   */                
  public function LoadAuth($auth = array()) {
  
    $result = TRUE;
    
    if(!is_array($auth)) {
      throw new YDirectException('Array required.');
      }
      
    //change all keys cases to lower
    array_change_key_case_recursive($auth, CASE_LOWER);
    
    if(array_key_exists('login', $auth)) {
      $this->SetLogin($auth['login']);
      }
    if(array_key_exists('email', $auth)) {
      $this->SetEmail($auth['email']);
      }
    
    if(array_key_exists('oauth', $auth)) {
      $this->SetOAuthInfo((array)$auth['oauth']);
      }

      // Требуется дял OAUTH2 авторизации
      if(array_key_exists('oauth2', $auth)) {
          $this->SetOAuth2Info((array)$auth['oauth2']);
      }

      if(array_key_exists('developertoken', $auth)) {
          $this->SetDeveloperToken($auth['developertoken']);
      }

      // Требуется дял OAUTH2 авторизации
      if(array_key_exists('useragent', $auth)) {
          $this->SetUserAgent($auth['useragent']);
      }

    return $result;
    
    }
  
  /**
   * Parse auth data from xml to array
   * 
   * @param - auth data as xml 
   * @result - TRUE/FALSE
   */                
  public function LoadAuthXML($auth_xml) {
  
    $result = FALSE;
    $auth = new SimpleXMLElement($auth_xml);
    $result = $this->LoadAuth(convert_xml_to_assoc($auth));
    
    return $result;
    
    }
  
  /**
   * Loading authtentication data from xml file  
   * Function parse xml-file to array and call LoadAuth() with this array as par
   * 
   * @param - xml file (with path if needed)
   * @result - TRUE/FALSE            
   */       
  public function LoadAuthXMLFile($auth_xmlfile) {
  
    $result = FALSE;
    
    $xmlfile = realpath($auth_xmlfile);
    if(!file_exists($xmlfile)) {
      throw new YDirectException("File not found: '$auth_xmlfile'");
      }
      
    $auth = simplexml_load_file($xmlfile);
    if(!$auth) {
      throw new YDirectException("Not XML file format: '$auth_xmlfile'");
      }
    $result = $this->LoadAuth(convert_xml_to_assoc($auth));
      
    return $result;
    
    }
    
  /**
   * Loading authtentication data from ini file  
   * Function parse ini-file to array and call LoadAuth() with this array as par
   * 
   * @param - ini file (with path if needed)
   * @result - TRUE/FALSE            
   */       
  public function LoadAuthINIFile($auth_inifile) {
  
    $result = FALSE;
    
    $inifile = realpath($auth_inifile);
    if(!file_exists($inifile)) {
      throw new 
        YDirectException("File not found: '$auth_inifile'");
      }
      
    $auth = parse_ini_file($inifile, TRUE);
    if(!$auth) {
      throw new YDirectException("Not INI file format: '$auth_inifile'");
      }
    $result = $this->LoadAuth((array)$auth);
      
    return $result;
    
    }

  /**
   *  Function checks $settings param and load it's data
   *  @param - Settings kind file or data
   *  @result TRUE/FALSE
   */           
  public function CheckSettings($settings) {
    // $settings - array with authentication data
    if(is_array($settings)) {
      $this->isSettingsLoaded = $this->LoadSettingsExt($settings);
      
      // $settings - ini file  
      } elseif(preg_match("/.ini$/i", $settings, $match)) {

      $this->isSettingsLoaded = $this->LoadSettingsINIFile($settings);
      
      // $settings - xml file
      } elseif(preg_match("/.xml$/i", $settings, $match)) {
        
      $this->isSettingsLoaded = $this->LoadSettingsXMLFile($settings);
      
      // $settings - xml data
      } elseif(preg_match("/^<\?xml /i", $settings, $match)) {
      
      $this->isSettingsLoaded = $this->LoadSettingsXML($settings);
      
      // $settings - not detected
      } else {

      throw new YDirectException("Wrong settings parameter kind.");

      }
     
    return $this->isSettingsLoaded;  
    }
      
  /**
   * Override parent {@link AdsUser} LoadSettings()
   * 
   * @param1 - pth to settings INI file
   * other parameters not used at <Lib>Ext, backward compatibility and
   * kickback to PHP standards   
   * 
   * @return - no results returned      
   */              
  public function LoadSettings($settingsIniPath, $defaultVersion,
      $defaultServer, $defaultLogsDir, $logsRelativePathBase) {
    $this->LoadSettingsINIFile($settingsIniPath);
    }
    
  /**
   * Loads settings data from $settings array
   * As tasks needed - parental methods LoadSettings is can't be fully overriden
   *  at PHP ('Strict standards')      
   * 
   * @param - array with Settings data
   * @result - TRUE/FALSE
   */                
  public function LoadSettingsExt($settings) {
  
    $result = TRUE;
    
    if(!is_array($settings)) {
      throw new YDirectException('Array required.');
      }
    
    //DONE: ~ 03.04.2012 9:44:11 большие/маленькие
    //change all keys case to lower
    array_change_key_case_recursive($settings, CASE_LOWER);
         
    if(array_key_exists('local', $settings)) {
      
      // Set locale
      if(array_key_exists('default_locale', $settings['local'])) {
        $this->SetLocale($settings['local']['default_locale']);
        } else {
        $this->SetLocale(_APIADLIB_LOCAL_DEFAULT_LOCALE);
        }
    
      // Set no time limit for PHP operations.
      if(array_key_exists('php_time_limit', $settings['local'])) {
        set_time_limit($settings['local']['php_time_limit']);
        } else {
        set_time_limit(_APIADLIB_LOCAL_PHP_TIME_LIMIT);
        }
        
      // sets sockets streams timeouts to 8 minutes?
      if(array_key_exists('default_socket_timeout', $settings['local'])) {
        ini_set('default_socket_timeout', $settings['local']['default_socket_timeout']);
        } else {
        ini_set('default_socket_timeout', _APIADLIB_LOCAL_DEFAULT_SOCKET_TIMEOUT);
        }
      }
      
    // Logging settings.
    if (isset($settings['logging']['path_relative']) && $settings['logging']['path_relative'] == 1) {
      $path = realpath($this->_logsRelativePathBase . '/'
          . $settings['logging']['lib_log_dir_path']);
      $this->logsDirectory = ($path === FALSE) ? $this->_defaultLogsDir : $path;
      } else {
      $this->logsDirectory = isset($settings['logging']['lib_log_dir_path']) ? $settings['logging']['lib_log_dir_path'] : '';
      }
    //TODO ~ 03.04.2012 14:20:47 check this
    $this->InitLogs();

    // Server settings.
    if (array_key_exists('server', $settings)) {
      if(array_key_exists('default_version', $settings['server'])) {
        $this->SetDefaultVersion($settings['server']['default_version']);
        } else {
        $this->SetDefaultVersion(ADWORDS_VERSION);
        }
      if (array_key_exists('default_server', $settings['server'])) {
        $this->SetDefaultServer($settings['server']['default_server']);
        } else {
        $this->SetDefaultServer(self::$DEFAULT_SERVER);
        }
      }
    
    // SOAP settings.
    if (array_key_exists('soap', $settings)) {
      // SOAP Compression level
      if(array_key_exists('compression', (array)$settings['soap'])) {
        $this->soapCompression = (bool) $settings['soap']['compression'];
        } else {
        $this->soapCompression = _APIADLIB_SOAP_COMPRESSION_DEFAULT;
        }
      if(array_key_exists('compression_level', $settings['soap'])) {
        if($settings['soap']['compression_level'] < _APIADLIB_SOAP_COMPRESSION_LEVEL_MIN) {
          $settings['soap']['compression_level'] = _APIADLIB_SOAP_COMPRESSION_LEVEL_MIN;
          }
        if($settings['soap']['compression_level'] > _APIADLIB_SOAP_COMPRESSION_LEVEL_MAX) {
          $settings['soap']['compression_level'] = _APIADLIB_SOAP_COMPRESSION_LEVEL_MAX;
          }
        $this->soapCompressionLevel = (int) $settings['soap']['compression_level'];
        } else {
        $this->soapCompressionLevel = _APIADLIB_SOAP_COMPRESSION_LEVEL_DEFAULT;
        }
      
      // WSDL cache  
      if(array_key_exists('wsdl_cache', (array)$settings['soap'])) {
        if($settings['soap']['wsdl_cache'] < _APIADLIB_SOAP_WSDL_CACHE_MIN) {
          $settings['soap']['wsdl_cache'] = _APIADLIB_SOAP_WSDL_CACHE_MIN;
          }
        if($settings['soap']['wsdl_cache'] > _APIADLIB_SOAP_WSDL_CACHE_MAX) {
          $settings['soap']['wsdl_cache'] = _APIADLIB_SOAP_WSDL_CACHE_MAX;
          }
        $this->wsdlCache = (int) $settings['soap']['wsdl_cache'];
        } else {
        $this->wsdlCache = _APIADLIB_SOAP_WSDL_CACHE_DEFAULT;
        }
      }
      
    // Proxy settings.
    if (array_key_exists('proxy', $settings)) {
      if (array_key_exists('host', $settings['proxy'])) {
        $this->Define('HTTP_PROXY_HOST', $settings['proxy']['host']);
        }
      if (array_key_exists('port', $settings['proxy'])) {
        $this->Define('HTTP_PROXY_PORT', (int) $settings['proxy']['port']);
        }
      if (array_key_exists('user', $settings['proxy'])) {
        $this->Define('HTTP_PROXY_USER', $settings['proxy']['user']);
        }
      if (array_key_exists('password', $settings['proxy'])) {
        $this->Define('HTTP_PROXY_PASSWORD', $settings['proxy']['password']);
        }
      }

    // Auth settings.
    if (array_key_exists('auth', $settings)) {
      //TODO: ~ 03.04.2012 18:02:51 Check this google
      if (array_key_exists('auth_server', $settings['auth'])) {
        $this->authServer = $settings['auth']['auth_server'];
        }
      // Auth 1.0 not used now
//      if (array_key_exists('oauth_handler_class', $settings['auth'])) {
//        $this->oauthHandler = new $settings['auth']['oauth_handler_class']();
//        } else {
//        $extensions = get_loaded_extensions();
//        if (in_array('OAuth', $extensions)) {
//          $this->oauthHandler = new PeclOAuthHandler();
//          } else {
//          $this->oauthHandler = new AndySmithOAuthHandler();
//          }
//        }

        //@todo обработать auth2handler если будет указан
      }

    // SSL settings.
    if (array_key_exists('ssl', $settings)) {
      if (array_key_exists('verify_peer', $settings['ssl'])) {
        $this->Define('SSL_VERIFY_PEER', $settings['ssl']['verify_peer']);
      }
      if (array_key_exists('ca_path', $settings['ssl'])) {
        $this->Define('SSL_CA_PATH', $settings['ssl']['ca_path']);
      }
      if (array_key_exists('CA_FILE', $settings['ssl'])) {
        $this->Define('SSL_CA_FILE', $settings['ssl']['ca_file']);
      }
    }

    return $result;
    
    }
  
  /**
   * Parse Settings data from xml string to array
   * 
   * @param - Settings data as xml string
   * @result - TRUE/FALSE
   */                
  public function LoadSettingsXML($settings_xml) {
  
    $result = FALSE;
    $settings = new SimpleXMLElement($settings_xml);
    //DONE: ~ 03.04.2012 16:46:37 to array al subkeys
    $result = $this->LoadSettingsExt(convert_xml_to_assoc($settings));
    
    return $result;
    
    }
  
  /**
   * Loading settings data from xml file  
   * Function parse xml-file to array and call LoadSettingsExt() with this array as par
   * 
   * @param - xml file (with path if needed)
   * @result - TRUE/FALSE            
   */       
  public function LoadSettingsXMLFile($settings_xmlfile) {
  
    $result = FALSE;
    
    $xmlfile = realpath($settings_xmlfile);
    if(!file_exists($xmlfile)) {
      throw new YDirectException("File not found: '$settings_xmlfile'");
      }
      
    $settings = simplexml_load_file($xmlfile);
    if(!$settings) {
      throw new YDirectException("Not XML file format: '$settings_xmlfile'");
      }
    
    //DONE: ~ 03.04.2012 16:46:37 to array all subkeys
    $result = $this->LoadSettingsExt(convert_xml_to_assoc($settings));
      
    return $result;
    
    }
    
  /**
   * Loading settings data from ini file  
   * Function parse ini-file to array and call LoadSettingsExt() 
   *    with this array as par
   * 
   * @param - ini file (with path if needed)
   * @result - TRUE/FALSE            
   */       
  public function LoadSettingsINIFile($settings_inifile) {
  
    $result = FALSE;
    
    $inifile = realpath($settings_inifile);
    if(!file_exists($inifile)) {
      throw new 
        YDirectException("File not found: '$settings_inifile'");
      }
      
    $settings = parse_ini_file($inifile, TRUE);
    if(!$settings) {
      throw new YDirectException("Not INI file format: '$settings_inifile'");
      }
    $result = $this->LoadSettingsExt((array)$settings);
      
    return $result;
    
    }
} 

