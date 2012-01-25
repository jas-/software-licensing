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
 * @category   access
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class access
 *  @abstract Handles allow/deny list
 */
class access {

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  * @var instance object
  * @abstract This class handler
  */
 protected static $instance;

 /**
  *! @function init
  *  @abstract Creates singleton for allow/deny class
  *  @param $args array Array of registry items
  */
 public static function init($args)
 {
  if (self::$instance == NULL)
   self::$instance = new self($args);
  return self::$instance;
 }

 /**
  *! @function __construct
  *  @abstract Class initialization and ip to access/deny processing
  *  @param $args array Array of registry items
  */
 private function __construct($registry)
 {
  $this->registry = $registry;
 }

 /**
  *! @function _do
  *  @abstract Perform access list to visitor allow/deny functions
  */
 public function _do()
 {
  return $this->__compare($this->__visitor(), $this->_get());
 }

 /**
  *! @function _get
  *  @abstract Retrieves currently configured list of allowed/denied hosts
  */
 private function _get()
 {
  $list = 0;
  try{
   $list = $this->registry->db->query($this->__query(), true);
  } catch(PDOException $e){
   // error handling
  }
  return $list;
 }

 /**
  *! @function __visitor
  *  @abstract Retrieves and assigns the visiting address
  */
 private function __visitor()
 {
  return $this->registry->libs->_getRealIPv4();
 }

 /**
  *! @function __query
  *  @abstract Generates SQL query to access list of allowed/denied hosts
  */
 private function __query()
 {
  return sprintf('SELECT `allow`,`deny` FROM `configuration_access`');
 }

 /**
  *! @function __compare
  *  @abstract Performs comparisions on hosts, ips and additional network
  *            declarations
  */
 private function __compare($i, $l)
 {
  if (count($l)>0){
   foreach($l as $k => $v){
    $n = (class_exists('IPFilter')) ? new IPFilter($v) : false;
    if ($n){
     $a = $n->check($i);
    }
    unset($n);
   }
  }
  return $a;
 }

 /**
  *! @function __allow
  *  @abstract Help process allowed hosts/ranges & cidr's
  */
 private function __allow($i, $l)
 {
  $r = false;
  $n = (class_exists('networking')) ? networking::init() : false;
  if (is_object($n)){
   $this->__process($i, $l, $n);
  } else {
   $r = true;
  }
  unset($n);
  return $r;
 }

 /**
  *! @function __deny
  *  @abstract Help process denied hosts/ranges & cidr's
  */
 private function __deny($i, $l)
 {
  $r = false;
  $n = (class_exists('networking')) ? networking::init() : false;
  if (is_object($n)){

  } else {
   $r = true;
  }
  unset($n);
  return $r;
 }

 /**
  *! @function __process
  *  @abstract Conditional processing
  */
 private function __process($i, $l, $n)
 {
  $r = false;
  switch($l){
   case (filter_var($l, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)):
    $r = (strcmp($l, $i)===0) ? true : false;
    break;
   case (filter_var($l, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/\-/Di')))):
    $a = explode('-', $l);

    print_r($a);
    break;
   case (filter_var($l, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/\//Di')))):

    break;
   default:
    break;
  }
  return $r;
 }

 public function __destruct()
 {
  return;
 }
}
?>
