<?php

/**
  * APIAdLib 
  *   Functions
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
 * Function recursively changes case of all array keys and subkeys
 *  
 * @param1 - $array to change case
 * @param2 - $case to change cases to (CASE_LOWER or CASE_UPPER),
 *            default value is CASE_LOWER
 * @param3 - $flag_rec recurse to subkeys (TRUE/FALSE)
 * 
 * @return - no return values
 */         
function array_change_key_case_recursive(&$array, $case = CASE_LOWER, $flag_rec = TRUE) {
   $array = array_change_key_case($array, $case);
   if ( $flag_rec ) {
     foreach ($array as $key => $value) {
         if ( is_array($value) ) {
          array_change_key_case_recursive($array[$key], $case);
          }
      }
    }
}
  
/**
 * Function converts Simple XML object to associative array
 * @param - $xml object to convert
 * @return - converted associative array
 */   
function convert_xml_to_assoc($xml) {
  $assoc = array();
  
  foreach((array)$xml as $key => $value) {
    if(is_object($value)) {
      $assoc[$key] = convert_xml_to_assoc($value);
      } else {
      $assoc[$key] = $value;
      }
    }
    
  return $assoc;
}

/**
 * Function for debug tasks
 * @param $label
 * @param $var
 */
function print_pre($label, $var) {
    ?>
<p><fieldset><legend><?php echo $label; ?></legend><p><pre>
<?php echo print_r($var, true); ?>
</pre></p></fieldset></p>
<?php }