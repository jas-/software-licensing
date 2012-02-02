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

 private $dn = array('countryName'             => 'US',
                     'stateOrProvinceName'     => 'Utah',
                     'localityName'            => 'Salt Lake City',
                     'organizationName'        => 'jQuery.pidCrypt',
                     'organizationalUnitName'  => 'Plug-in for easy implementation of RSA public key encryption',
                     'commonName'              => 'Jason Gerfen',
                     'emailAddress'            => 'jason.gerfen@gmail.com');

 /**
  *! @function __construct
  *  @abstract Initializes singleton for proxyView class
  *  @param registry array - Global array of class objects
  */
 public function __construct($registry)
 {
  $this->registry = $registry;
  if (class_exists('openssl')){
   $this->registry->ssl = openssl::instance(array('config'=>$this->__settings(),
                                                  'dn'=>$this->dn));
  }
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
     $x = $this->__public();;
     return;
    default:
     $x = false;
   }
  }
  return $x;
 }

 /**
  *! @function __public
  *  @abstract Retrieves the public key for the specified account (defaults to
  *            the current system key pair if email is empty)
  */
 private function __public($email=false)
 {
  $this->registry->ssl->genRand();
  $priv = $this->registry->ssl->genPriv($this->registry->opts['dbKey']);
  $pub = $this->registry->ssl->genPub();
  $email = (!empty($email)) ? $email : 'default';
  try{
   $sql = sprintf('CALL Configuration_def_add("%s", "%d", "%s", "%d", "%s", "%s", "%s", "%s")',
                  $this->registry->db->sanitize('Marriott Library - Software Licensing'),
                  $this->registry->db->sanitize(1),
                  $this->registry->db->sanitize('jason.gerfen@gmail.com'),
                  $this->registry->db->sanitize(3600),
                  $this->registry->db->sanitize($priv),
                  $this->registry->db->sanitize($pub),
                  $this->registry->db->sanitize($this->registry->opts['dbKey']),
                  $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'],
                                                                            $this->registry->libs->_salt($this->registry->opts['dbKey'],
                                                                            2048))));
   echo $sql;
   $r = $this->registry->db->query($sql);
  } catch(Exception $e){
   // error handler
  }
  $this->opts['config'] = (!empty($r)) ? $r : false;
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
  return (!empty($r)) ? $r : false;
 }

 /**
  *! @function __decrypt
  *  @abstract Handle decryption of submitted form data
  */
 private function __decrypt($obj)
 {

 }
}
?>