<?php

/**
  * APIAdLib Autoload
  * Class for register and autoload library classes with spl_autoload...
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
 * Class APIAdLibAutoload
 * Class for register and autoload library classes with spl_autoload... 
 */ 
class APIAdLibAutoload {
  /**
   *
   * Global Array with descriptions array for every supported library
   * Descriptions array format:
   *  'path' - path to library folder
   *     
   */ 
   
  //TODO: Describe Utils classes autoload
  public static $APIADLIB_LIBRARIES = array(
    'AdWords' => array(
      'path' => 'AdWords/Lib/',
      ),
    'Ads'     => array(
      'path' => 'Common/Lib/',
      ),
    'YDirect' => array(
      'path' => 'YDirect/Lib/',
      ),
    );
  
  // Saves last loaded filename for future logging purposes  
  private static $_lastLoadedFilename;
    
  /**
   * Library classes autoload function for SPL autoload functions stack
   */  
  //TODO: Catch Utils classes for autoload
  public static function LoadClass($class_name) {
  
    foreach(self::$APIADLIB_LIBRARIES as $lib_name => $lib_settings) {
    
      if(!preg_match("/$lib_name/i", $class_name)) continue;
      
      self::$_lastLoadedFilename = $lib_settings['path'].$class_name.'.php';
      require_once(self::$_lastLoadedFilename);
      break;
      
      }
      
  }

}

/**
 * Register library autoload class method in spl_autoload stack
 */     
spl_autoload_register(array('APIADLibAutoload','LoadClass'));