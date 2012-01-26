<?php
/**
 * Libraries
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
 * @copyright  2010-2011 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class libraries {
 protected static $instance;
 private function __construct()
 {
  return;
 }
 public static function init()
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self();
  }
  return self::$instance;
 }

 /**
  * @function _dbEngine
  * @abstract Determine database engine and class
  * @param $opt Option passed for database class to load
  */
 function _dbEngine($opt)
 {
  switch($opt){
   case 'mssql':
    $eng = 'mssqlDBconn';
    break;
   case 'pgsql':
    $eng = 'pgSQLDBconn';
    break;
   case 'mysql':
    $eng = 'mysqlDBconn';
    break;
   default:
    $eng = 'mysqlDBconn';
    break;
  }
  return $eng;
 }

 /**
  * @function _16
  * @abstract Creates substring of argument
  * @param $string string String to return sub-string of
  */
 function _16($string)
 {
  return substr($string, round(strlen($string)/3, 0, PHP_ROUND_HALF_UP), 16);
 }

 /**
  * @function _uuid
  * @abstract Generates a random GUID
  */
 function uuid() {
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff),
                 mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000,
                 mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff),
                 mt_rand(0, 0xffff), mt_rand(0, 0xffff));
 }

 /**
  * @function _getRealIPv4
  * @abstract Try all methods of obtaining 'real' IP address
  */
 function _getRealIPv4()
 {
  return (getenv('HTTP_CLIENT_IP') && $this->_ip(getenv('HTTP_CLIENT_IP'))) ?
           getenv('HTTP_CLIENT_IP') :
            (getenv('HTTP_X_FORWARDED_FOR') && $this->_forwarded(getenv('HTTP_X_FORWARDED_FOR'))) ?
             $this->_forwarded(getenv('HTTP_X_FORWARDED_FOR')) :
              (getenv('HTTP_X_FORWARDED') && $this->_ip(getenv('HTTP_X_FORWARDED'))) ?
                getenv('HTTP_X_FORWARDED') :
                 (getenv('HTTP_X_FORWARDED_HOST') && $this->_ip(getenv('HTTP_FORWARDED_HOST'))) ?
                   getenv('HTTP_X_FORWARDED_HOST') :
                    (getenv('HTTP_X_FORWARDED_SERVER') && $this->_ip(getenv('HTTP_X_FORWARDED_SERVER'))) ?
                      getenv('HTTP_X_FORWARDED_SERVER') :
                       (getenv('HTTP_X_CLUSTER_CLIENT_IP') && $this->_ip(getenv('HTTP_X_CLIUSTER_CLIENT_IP'))) ?
                         getenv('HTTP_X_CLUSTER_CLIENT_IP') :
                          getenv('REMOTE_ADDR');
 }

 /**
  * @function _ip
  * @abstract Attempts to determine if IP is non-routeable
  */
 function _ip($ip)
 {
  if (!empty($ip) && ip2long($ip)!=-1 && ip2long($ip)!=false){
   $nr = array(array('0.0.0.0','2.255.255.255'),
               array('10.0.0.0','10.255.255.255'),
               array('127.0.0.0','127.255.255.255'),
               array('169.254.0.0','169.254.255.255'),
               array('172.16.0.0','172.31.255.255'),
               array('192.0.2.0','192.0.2.255'),
               array('192.168.0.0','192.168.255.255'),
               array('255.255.255.0','255.255.255.255'));
   foreach($nr as $r){
    $min = ip2long($r[0]);
    $max = ip2long($r[1]);
    if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
   }
   return true;
  } else {
   return false;
  }
 }

 /**
  * @function _forwarded
  * @abstract A helper for HTTP_X_FORWARDED_FOR, loops over comma
  *           separated list of proxies associated with request
  */
 function _forwarded($l)
 {
  if (!empty($l)){
   foreach (explode(',', $l) as $i){
    if (_ip(trim($i))) {
     return (!_ip(trim($i))) ? false : $i;
    }
   }
  } else {
   return false;
  }
 }

 /**
  *! @function __flatten
  *  @abstract Flattens a multi-dimensional array into one array
  */
 public function __flatten($a)
 {
  $x = array();
  if (count($a)>0){
   foreach($a as $k => $v){
    if (is_array($v)){
     $x[] = $this->__flatten($v);
    } else {
     $x[] = $v;
    }
   }
  } else {
   $x = $a;
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
