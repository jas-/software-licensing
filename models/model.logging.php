<?php
/**
 * Handle access logging
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   logging
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class logging
 *  @abstract Handle logging for application usage
 */
class logging {

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
  if (self::$loader == NULL)
   self::$loader = new self($args);
  return self::$loader;
 }

 /**
  *! @function __construct
  *  @abstract Class initialization to handle access logging
  *  @param $args array Array of registry items
  */
 private function __construct($args)
 {
  $this->registry = $registry;
 }

 /**
  *! @function _get
  *  @abstract Breaks up some client information into an array
  */
 private function _ip()
 {
  return $this->registry->libs->_getRealIPv4();
 }

 public function __destruct()
 {
  unset($this->init);
  return;
 }
}
?>
