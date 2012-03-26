<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle authentication
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   libraries
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class users
 *  @abstract Handles user account manangement
 */
class users
{
 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  *! @var instance object - class singleton object
  */
 protected static $instance;

 /**
  *! @function __construct
  *  @abstract Initializes singleton for proxyView class
  *  @param registry array - Global array of class objects
  */
 private function __construct($registry)
 {
  $this->registry = $registry;
  if (!$this->__setup($registry)){
   exit(array('Error'=>'Necessary keys are missing, cannot continue'));
  }
 }

 public static function instance($registry)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($registry);
  }
  return self::$instance;
 }

 /**
  *! @function setup
  *  @abstract Performs initial requirements
  */
 private function __setup($args)
 {
  if ((!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['email']))&&
      (!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['privateKey']))&&
      (!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['publicKey']))&&
      (!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['password']))){
   return true;
  }else{
   return false;
  }
 }

 /**
  *! @function __do
  *  @abstract Determines action and acts accordingly
  */
 public function __do($obj)
 {
  //if (!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['token'])){
   $d = $this->__decrypt($obj);
   echo '<pre>'; print_r($d); echo '</pre>';
   // determine action, process add/edit/delete/search functionality
  //}else{
  // $x = array('error'=>'Authentication token missing, must authenticate');
  //}
  return $x;
 }

 /**
  *! @function __decrypt
  *  @abstract Handle decryption of submitted form data
  */
 private function __decrypt($obj)
 {
  if (count($obj)>0){
   $x = array();
   foreach($obj as $key => $value){
    $x[$key] = $this->registry->keyring->ssl->privDenc($value,
                                                       $_SESSION[$this->registry->libs->_getRealIPv4()]['privateKey'],
                                                       $_SESSION[$this->registry->libs->_getRealIPv4()]['password']);
   }
  }
  return $x;
 }

 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }

 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }

 public function __destruct()
 {
  unset($this->instance);
  return true;
 }
}
?>
