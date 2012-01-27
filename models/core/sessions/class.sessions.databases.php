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
	private $registry;
 public function __construct($configuration, $opts)
 {
		if ((class_exists('dbConn'))||(is_object($opts))) {
   if (is_object($opts->db)) $this->dbconn = $opts->db;
			$this->options($configuration);
			$this->registry = $opts;
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
			exit('Database class handler is missing.');
			unset($instance);
			exit;
		}
 }
 public static function instance($configuration, $opts)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration, $opts);
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
  if (isset($id)){
   try{
    $sql = $query = sprintf('CALL Session_Search("%s")', $this->dbconn->sanitize($id));
    $result = $this->dbconn->query($sql);
			} catch(PDOException $e){
				// error handling
			}
   return (count($result)>0) ? $this->sanitizeout($result['session_data']) : '';
  }
  return '';
 }
 public function write($id, $data)
 {
  if ((isset($id))&&(isset($data))){
   $sql = sprintf('CALL Session_Add("%s", "%s", "%d", "%s", "%s", "%s")',
																		$this->dbconn->sanitize($id), $this->sanitizein($data),
																		$this->dbconn->sanitize((int)time()),
																		$this->dbconn->sanitize(sha1($_SERVER['HTTP_USER_AGENT'])),
																		$this->dbconn->sanitize(sha1($this->registry->libs->_getRealIPv4())),
																		$this->dbconn->sanitize($_SERVER['HTTP_REFERER']));
   $r = $this->dbconn->query($sql);
   return ((is_resource($r))&&($this->dbconn->affected($r)>0)) ? true : false;
  }
  return false;
 }
 public function close()
 {
  return true;
 }
 private function destroy($id)
 {
  if (isset($id)){
   $sql = sprintf('CALL Session_destroy("%s")', $this->dbconn->sanitize($id));
			$r = $this->dbconn->query($sql);
   return ((is_resource($r))&&($this->dbconn->affected($this->dbconn)>0)) ? true : false;
  }
  return false;
 }
 private function sanitizein($string)
 {
		if (version_compare(PHP_VERSION, '5.2.11')>=0) {
   return $this->dbconn->sanitize(addslashes(serialize($string)));
  } else {
   return $this->dbconn->sanitize($string);
  }
 }
 private function sanitizeout($string)
 {
		if (version_compare(PHP_VERSION, '5.2.11')>=0) {
   return stripslashes(stripslashes(unserialize($string)));
  } else {
   return stripslashes($string);
  }
 }
 public function regen($flag=false)
	{
  if ($flag!==false){
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
   $sql = sprintf('CALL Session_Timeout("%d")', time()-$timeout);
   $r = $this->dbconn->query($sql);
   return ((is_resource($r))&&($this->dbconn->affected()>0)) ? true : false;
  }
  return false;
 }
}
?>
