<?php
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

class authentication
{
 protected static $instance;
 private function __construct($handles, $credentials, $server)
 {
  return $this->decide($handles, $credentials, $server);
 }
 public static function instance($handles, $credentials, $server)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($handles, $credentials, $server);
  }
  return self::$instance;
 }
 private function decide($handles, $credentials=array(), $server)
 {
  $a = (array_key_exists('token', $credentials)) ?
                         $this->reauthenticate($handles, $credentials,
                         $server) :
                         $this->initial($handles, $credentials, $server);
  if ($a===1) {
   return ($this->newtoken($handles, $credentials, $server)===true) ? true : false;
  } else {
   return false;
  }
 }
 public function initial($handles, $credentials, $server)
 {
  if ((is_array($credentials))&&
      (array_key_exists('username', $credentials))&&
      (array_key_exists('password', $credentials))&&
      (array_key_exists('key', $credentials))) {
   if (is_object($handles['db'])) {
    $s = sprintf('SELECT `password` FROM `authentication` WHERE `e-mail` = "%s"',
                 $handles['db']->sanitize($u));
    $results = $handles['db']->query($s);
    if ($handles['db']->affected($results)>0) {
     $r = $handles['db']->results($results);
     return (strcmp($x, $p)===0) ? true : false;
    } else {
      return false;
    }
   } else {
    return false;
   }
  } else {
   return false;
  }
 }
 private function reauthenticate($handles, $credentials, $server)
 {
  return false;
 }
 private function expired($certificate)
 {

 }
 public function newtoken($handles, $credentials, $server)
 {
  if ((is_array($credentials))&&
      (array_key_exists('username', $credentials))&&
      (array_key_exists('password', $credentials))&&
      (array_key_exists('key', $credentials))) {
   if ((is_object($handles['ssl']))&&(is_object($handles['db']))) {
    $u = $handles['ssl']->privDenc($credentials['username'], $credentials['key'],
                                   $server['REMOTE_ADDR']);
    $s = sprintf('%s', $handles['db']->sanitize($u));
//echo $s;
    $results = $handles['db']->query($s);
/*
    if ($handles['db']->affected($results)>0) {
     $r = $handles['db']->results($results);
echo '<pre>'; print_r($r); echo '</pre>';
    } else {
     return false;
    }
*/
   } else {
    return false;
   }
  } else {
   return false;
  }
 }
 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
 private function __destruct()
 {
  unset(self::$instance);
  return true;
 }
}
?>
