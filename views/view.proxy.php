<?php
/**
 * Authenticated dashboard
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
  *! @function __construct
  *  @abstract Initializes singleton for proxyView class
  *  @param registry array - Global array of class objects
  */
 private function __construct($registry)
 {
  $this->registry = $registry;

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
  *! @function __vRequest
  *  @abstract Verify the request was valid XMLHttpRequest
  */
 private function __vRequest($request)
 {
  return (strcmp($request, 'XMLHttpRequest')!==0) ? false : true;
 }

 /**
  *! @function __vCSRF
  *  @abstract Verify the CSRF token
  */
 private function __vCSRF($header, $token)
 {
  return (strcmp($header, $token)!==0) ? true : false;
 }

 /**
  *! @function __vCheckSum
  *  @abstract Verify the post data contained a valid checksum in the header
  */
 private function __vCheckSum($header, $array)
 {
  return (strcmp(base64_decode($header),
                 md5($registry->libs->_serialize($array)))!==0) ? false : true;
 }

 /**
  *! @function __clone
  *  @abstract Prevent cloning of singleton object
  */
 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }

 /**
  *! @function __wakeup
  *  @abstract Prevent deserialization of singleton object
  */
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
}
?>