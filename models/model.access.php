<?php
/**
 * Manage access lists (allow/deny)
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

class access {
 public static $loader;
 public static function init($args)
 {
  if (self::$loader == NULL)
   self::$loader = new self($args);
  return self::$loader;
 }
 private function __construct($args)
 {
  return ($this->compare($this->load($args['dbconn']), $args['ip'])) ? true : false;
 }
 public function load($db)
 {
  $sql='SELECT `allow`,`deny` FROM `configuration-access-list` ORDER BY `id` ASC';
  $results = $db->query($sql);
  return ($db->affected($results)>0) ? $db->results($results) : 0;
 }
 public function compare($array, $ip)
 {
  $array = (count($array)>0) ? $this->parse($array) : false;
  return (($array>0)&&($this->validate($array, $ip)===true)) ? true : false;
 }
 private function validate($array, $ip)
 {
  $x = false;
  foreach($array as $k => $v) {
   foreach($v as $key => $value) {
    if ($k==='deny') {
     if (($this->denied($value, $ip, $k)===true)&&
         ($this->allowed($value, $ip, $k)===false)) {
      return false;
     }
    } else {
     if ($this->allowed($value, $ip, $k)===true) {
      $x = true;
      continue;
     }
    }
   }
  }
  return $x;
 }
 private function denied($array, $ip, $type)
 {
  if (($array['ip']===$ip)&&($type==='deny')) {
   return true;
  } else {
   $x = $this->calculate($array, $ip);
  }
  return $x;
 }
 private function allowed($array, $ip, $type)
 {
  if (($array['ip']===$ip)&&($type==='allow')) {
   return true;
  } else {
   $x = $this->calculate($array, $ip);
  }
  return $x;
 }
 private function calculate($array, $ip)
 {//echo '<pre>'; print_r(func_get_args()); echo '</pre>';
  if ($this->ranges(array($array['octet-1'],
                          $array['octet-2'],
                          $array['octet-3'],
                          $array['octet-4']), $array['class'])===true) {
   $this->within($array, $ip);
  } else {
   return false;
  }
 }
 private function within($array, $ip)
 {
  //echo '<pre>'; print_r(func_get_args()); echo '</pre>';
 }
 private function ranges($octets, $type)
 {
  switch($type) {
   case 'X':
    return false;
   case 'R':
    return ($this->reserved($octets)===1) ? true : false;
   case 'A':
    return $this->a($octets) ? true : false;
   case 'B':
    return $this->b($octets) ? true : false;
   case 'C':
    return $this->c($octets) ? true : false;
   default:
    return 0;
  }
 }
 private function parse($array)
 {
  $results = array(); $x = array();
  foreach($array as $k => $v) {
   foreach($v as $key => $value) {
    preg_match('/((\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3}))(\/(\d{1,3}))?/',
               $value, $match);
    $results[$key][$value]=array('ip' => $match[1],
                                 'octet-1' => $match[2],
                                 'octet-2' => $match[3],
                                 'octet-3' => $match[4],
                                 'octet-4' => $match[5],
                                 'bits' => (!empty($match[7])) ? $match[7] : 0,
                                 'class' => $this->type(array((int)$match[2],
                                                              (int)$match[3],
                                                              (int)$match[4],
                                                              (int)$match[5])));
   }
  }
  return $results;
 }
 private function type($octet)
 {
  switch($octet) {
   case $this->all($octet):
    return 'X';
   case $this->reserved($octet):
    return 'R';
   case $this->a($octet):
    return 'A';
   case $this->b($octet):
    return 'B';
   case $this->c($octet):
    return 'C';
   default:
    return 0;
  }
 }
 private function a($octet)
 {
  return (($octet[0]>=1)&&($octet[0]!==10)&&($octet[0]<=126)&&
          ($octet[1]>=0)&&($octet[1]<=255)&&
          ($octet[2]>=0)&&($octet[2]<=255)&&
          ($octet[3]>=0)&&($octet[3]<=255)) ? true : false;
 }
 private function b($octet)
 {
  return (($octet[0]>=128)&&($octet[0]!==172)&&($octet[0]<=191)&&
          ($octet[1]>=0)&&($octet[1]<=255)&&
          ($octet[2]>=0)&&($octet[2]<=255)&&
          ($octet[3]>=0)&&($octet[3]<=255)) ? true : false;
 }
 private function c($octet)
 {
  return (($octet[0]>=192)&&($octet[0]<=223)&&
          ($octet[1]>=0)&&($octet[1]!==168)&&($octet[1]<=255)&&
          ($octet[2]>=1)&&($octet[2]<=255)&&
          ($octet[3]>=0)&&($octet[3]<=255)) ? true : false;
 }
 private function all($octet)
 {
  return (($octet[0]===0)&&
          ($octet[1]===0)&&
          ($octet[2]===0)&&
          ($octet[3]===0)) ? true : false;
 }
 private function reserved($octet)
 {
  return (($this->reserved1($octet)===1)||
          ($this->reserved2($octet)===1)||
          ($this->reserved3($octet)===1)) ? true : false;
 }
 private function reserved1($octet)
 {
  return (($octet[0]===10)&&
          ($octet[1]>=0)&&($octet[1]<=255)&&
          ($octet[2]>=0)&&($octet[2]<=255)&&
          ($octet[3]>=0)&&($octet[3]<=255)) ? 1 : 0;
 }
 private function reserved2($octet)
 {
  return (($octet[0]===172)&&
          ($octet[1]>=16)&&($octet[1]<=31)&&
          ($octet[2]>=0)&&($octet[2]<=255)&&
          ($octet[3]>=0)&&($octet[3]<=255)) ? 1 : 0;
 }
 private function reserved3($octet)
 {
  return (($octet[0]===192)&&
          ($octet[1]===168)&&
          ($octet[2]>=0)&&($octet[2]<=255)&&
          ($octet[3]>=0)&&($octet[3]<=255)) ? 1 : 0;
 }
 public function __destruct()
 {
  unset($this->init);
  return;
 }
}
?>
