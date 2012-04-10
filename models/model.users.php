<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

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

/**
 *! @class users
 *  @abstract Handles user account manangement
 */
class users
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
		if (!$this->__setup($registry)){
			exit(array('Error'=>'Necessary keys are missing, cannot continue'));
		}
	}

	public static function instance($registry)
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new self($registry);
		}
		return self::$instance;
	}

	/**
	 *! @function setup
	 *  @abstract Performs initial requirements
	 */
	private function __setup($args)
	{
		if ((!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['email']))&&
				(!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['privateKey']))&&
				(!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['publicKey']))&&
				(!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['password']))){
			return true;
		}else{
			return false;
		}
	}

	/**
	 *! @function __do
	 *  @abstract Determines action and acts accordingly
	 */
	public function __do($obj)
	{
		$d = $this->__decrypt($obj);

		$auth = authentication::instance($this->registry);
		$u = $this->__permsUser($auth->__user($_SESSION['token']));
        $g = $this->__permsGroup($auth->__group($_SESSION['token']));

		if (!empty($d['do'])){
			switch($d['do']){
				case 'add':
					break;
				case 'edit':
					break;
				case 'del':
					break;
				default:
					break;
			}
		}
	}

    /**
     *! @function __permsUser
     *  @abstract Retrieves object permissions by users
     */
    private function __permsUser($u)
    {
		try{
			$sql = sprintf('CALL Perms_SearchUser("%s", "%s")', $this->registry->db->sanitize($u), $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'], 2048))));
			$r = $this->registry->db->query($sql);
		} catch(Exception $e){
			// error handler
		}
        return (($r) && (is_array($r))) ? $r : false;
    }

    /**
     *! @function __permsGroup
     *  @abstract Retrieves object permissions by group
     */
    private function __permsGroup($g)
    {
		try{
			$sql = sprintf('CALL Perms_SearchGroup("%s", "%s")', $this->registry->db->sanitize($g), $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'], 2048))));
			$r = $this->registry->db->query($sql);
		} catch(Exception $e){
			// error handler
		}
        return (($r) && (is_array($r))) ? $r : false;
    }

	/**
	 *! @function __decrypt
	 *  @abstract Handle decryption of submitted form data
	 */
	private function __decrypt($obj)
	{
		if (count($obj)>0){
			$x = array();
			foreach($obj as $key => $value){
				$x[$key] = $this->registry->keyring->ssl->privDenc($value, $_SESSION[$this->registry->libs->_getRealIPv4()]['privateKey'], $_SESSION[$this->registry->libs->_getRealIPv4()]['password']);
			}
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
