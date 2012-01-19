<?php
/**
 * Handle database session functionality
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   sessions
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class dbSession
{
 protected static $instance;
 private $dbconn;
 public function __construct($configuration)
 {
		if (class_exists('dbConn')) {
   $this->options($configuration);
   session_set_save_handler(
    array(&$this, 'open'),
    array(&$this, 'close'),
    array(&$this, 'read'),
    array(&$this, 'write'),
    array(&$this, 'destroy'),
    array(&$this, 'gc')
   );
   register_shutdown_function('session_write_close');
   if (!isset($_SESSION)) session_start();
		} else {
			echo 'Database class handler is missing.';
			unset($instance);
			exit;
		}
 }
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }
 private function options($configuration)
 {
  ini_set('session.gc_maxlifetime', $configuration['timeout']);
  ini_set('session.name', $configuration['title']);
  ini_set('cache_limiter', $configuration['cache']);
		ini_set('cache_expire', $configuration['timeout']);
		ini_set('use_cookies', $configuration['cookie']);
 }
 private function open($configuration)
 {
  if ((!isset($this->dbconn))||(!is_object($this->dbconn))) {
   $this->dbconn = dbConn::instance($configuration);
   return (is_object($this->dbconn)) ? true : false;
  }
 }
 public function read($id)
 {
  if (isset($id)) {
   $query = "SELECT * FROM `sessions` WHERE `session-id` = \"".$this->dbconn->sanitize($id)."\" LIMIT 1";
   $result = $this->dbconn->query($query);
   if ((is_resource($result))&&($this->dbconn->affected($result)>0)) {
    $array = $this->dbconn->results($result);
    return $this->sanitizeout($array[0]['session_data']);
   }
  }
  return "";
 }
 public function write($id, $data)
 {
  if ((isset($id))&&(isset($data))) {
   $query = "INSERT INTO `sessions` (`session-id`, `session-data`,
                                     `session-expire`, `session-agent`,
                                     `session-ip`, `session-referrer`) VALUES
                                    (\"".$id."\", \"".$this->sanitizein($data)."\",
                                     \"".time()."\",
                                     \"".$this->sanitizein(md5($_SERVER['HTTP_USER_AGENT']))."\",
                                     \"".$this->sanitizein(md5($_SERVER['REMOTE_ADDR']))."\",
                                     \"".$this->sanitizein($_SERVER['HTTP_REFERER'])."\")
                                    ON DUPLICATE KEY UPDATE `session-id` = \"".$id."\",
                                    `session-data` = \"".$this->sanitizein($data)."\",
                                    `session-expire` = \"".time()."\"";
   $result = $this->dbconn->query($query);
   return ((is_resource($result))&&($this->dbconn->affected($result)>0)) ? true : false;
  }
  return false;
 }
 public function close()
 {
  return true;
 }
 private function destroy($id)
 {
  if (isset($id)) {
   $query = "DELETE FROM `sessions` WHERE `session-id` = \"".$this->dbconn->sanitize($id)."\" LIMIT 1";
   $result = $this->dbconn->query($query);
   return ((is_resource($result))&&($this->dbconn->affected($this->dbconn)>0)) ? true : false;
  }
  return false;
 }
 private function sanitizein($string)
 {
  if (version_compare(PHP_VERSION, '5.2.11')>=0) {
   return $this->dbconn->sanitize(serialize($string));
  } else {
   return $this->dbconn->sanitize($string);
  }
 }
 private function sanitizeout($string)
 {
  if (version_compare(PHP_VERSION, '5.2.11')>=0) {
   return stripslashes(unserialize($string));
  } else {
   return stripslashes($string);
  }
 }
 public function regen($flag=false)
	{
  if ($flag!==false) {
   $this->register('id', session_id());
   session_regenerate_id($flag);
   $this->id = session_id();
   $this->destroy($_SESSION['id']);
  }
  return;
	}
 public function register($name, $value)
 {
  return ((isset($name))&&(isset($value))) ? $_SESSION[$name] : false;
 }
 private function gc($timeout)
 {
  if (isset($timeout)) {
   $query = "DELETE FROM `sessions` WHERE `session_expire` > \"".time()-$timeout."\"";
   $result = $this->dbconn->query($query);
   return ((is_resource($result))&&($this->dbconn->affected()>0)) ? true : false;
  }
  return false;
 }
}
?>
