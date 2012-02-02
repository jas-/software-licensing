<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Proxy view (conditionally handles XMLHttpRequests)
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   views
 * @discussion Handle XMLHttpRequests
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class proxyView
 *  @abstract Handles XMLHttpRequests
 */
class proxyView
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
  * @var opts array
  * @abstract Global array of OpenSSL options
  */
 private $opts = array();

 /**
  *! @function __construct
  *  @abstract Initializes singleton for proxyView class
  *  @param registry array - Global array of class objects
  */
 public function __construct($registry)
 {
  $this->registry = $registry;
  echo '<pre>'; print_r($registry); echo '</pre>';
  exit($this->registry->libs->JSONencode(array('success'=>$this->__decide($this->registry->router->action))));
 }

 /**
  *! @function instance
  *  @abstract Creates non-deserializing, non-cloneable instance object
  *  @param configuration array - server, username, password, database
  *  @return Singleton - Singleton object
  */
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }

 /**
  *! @function __decide
  *  @abstract Switch/Case to decide what to do
  *  @param args array - Array of arguments
  */
 private function __decide($cmd)
 {
  $x = false;
  if (!empty($cmd)){
   $cmd = $this->registry->val->__do($cmd, 'string');
   switch($cmd){
    case 'authenticate':
     $x = $this->__decrypt($this->registry->val->__do($_POST));
    case 'key':
     
     $x = $key;
     return;
    default:
     $x = false;
   }
  }
  return $x;
 }

 /**
  *! @function __settings
  *  @abstract Handles the retrieval of current OpenSSL configuration data
  */
 private function __settings()
 {
  try{
   $sql = sprintf('CALL Configuration_cnf_get()');
   $r = $this->registry->db->query($sql);
  } catch(Exception $e){
   // error handler
  }
  $this->opts['config'] = (!empty($r)) ? $r : false;
 }

 /**
  *! @function __decrypt
  *  @abstract Handle decryption of submitted form data
  */
 private function __decrypt($obj)
 {
  if (class_exists('openssl')){
   $this->registry->ssl = openssl::instance($opts, $this->opts['config']);
echo '<pre>'; print_r($this->opts['config']); echo '</pre>';
  }
 }
}
?>