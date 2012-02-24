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
 *! @class authentication
 *  @abstract Performs primary and additional authentication mechanisms
 */
class authentication
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
  *  @abstract Performs initial authentication
  */
 public function __do($creds)
 {
  if (!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['token'])){
   $x = $this->__redo($_SESSION[$this->registry->libs->_getRealIPv4()]['token']);
  }else{
   $obj = $this->__decrypt($creds);
   $x = $this->__auth($obj);
   if (is_array($x)){
    return $x;
   }else{
    $token = $this->__genToken($obj);
    $x = $this->__register($obj);
   }
  }
  return $x;
 }

 /**
  *! @function __auth
  *  @abstract Returns booleans on database lookup of decrypted credentials
  */
 private function __auth($creds)
 {
  if (is_array($creds)){
   if ((!empty($creds['email']))&&(!empty($creds['password']))){
    try{
     $sql = sprintf('CALL Auth_CheckUser("%s", "%s", "%s")',
                    $this->registry->db->sanitize($creds['email']),
                    $this->registry->db->sanitize($creds['password']),
                    $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'],
                                                                               $this->registry->libs->_salt($this->registry->opts['dbKey'],
                                                                                                            2048))));
     $r = $this->registry->db->query($sql);
     if ($r['x']<=0){
      $x = false;
     }else{
      $x = true;
     }
    } catch(Exception $e){
     // error handling
    }
   }else{
    $x = false;
   }
  }else{
   $x = false;
  }
  return (!$x) ? array('error'=>'User authentication failed') : $x;
 }

 /**
  *! @function __redo
  *  @abstract Decodes and re-authenticates user
  */
 public function __redo($token)
 {
  // verify supplied token matches entry in users table

  // decode token

  // verify unique computer and timeout information

  // perform new token generation and registration per user account

  // return boolean
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

 /**
  *! @function __decode
  *  @abstract Handle decoding of authentication token
  */
 private function __decode($token)
 {
  // returns array of elements within authentication token
 }

 /**
  *! @function __hijack
  *  @abstract Performs anti-session hijacking validations
  */
 private function __hijack($array)
 {
  // performs validation of current session data to array of unique computer
  // identifiers located within decoded authentication token array
 }

 /**
  *! @function __genToken
  *  @abstract Creates unique token based on visiting machine and
  *            authenticated user information
  */
 private function __genToken($obj)
 {
  if (count($obj)>=2){
   // get access level and group
   $token = sprintf("%s", "%s", "%s", "%s", "%s", "%s", "%d",
                    $this->registry->val->__do($obj['email'], 'email'),
                    $this->registry->val->__do($obj['level'], 'string'),
                    $this->registry->val->__do($obj['group'], 'string'),
                    $this->registry->val->__do(sha1($this->registry->libs->_getRealIPv4()), 'string'),
                    $this->registry->val->__do(sha1(getenv('HTTP_USER_AGENT')), 'string'),
                    $this->registry->val->__do(getenv('HTTP_REFERER'), 'string'));
   $_SESSION[$this->registry->libs->_getRealIPv4()]['token'] = $token;
   return $token;
  }
 }

 /**
  *! @function __register
  *  @abstract Registers current authentication token within users table
  */
 private function __register($obj)
 {
  try{
   $sql = sprintf('CALL Users_AddUpdateToken("%s", "%s", "%s")',
                  $this->registry->db->sanitize($obj['email']),
                  $this->registry->db->sanitize($token),
                  $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'],
                                                                             $this->registry->libs->_salt($this->registry->opts['dbKey'],
                                                                                                          2048))));
   $r = $this->registry->db->sanitize($sql);
  } catch(Exception $e){
   // error handling
  }
  return ($r) ? array('success'=>'User was successfully authenticated') :
                array('error'=>'An error occured when associating token with user');
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
