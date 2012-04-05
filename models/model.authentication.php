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
 *! @class authentication
 *  @abstract Performs primary and additional authentication mechanisms
 */
class authentication
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
	 *  @abstract Performs initial authentication
	 */
	public function __do($creds)
	{
		if (!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['token'])){
			$x = $this->__redo($_SESSION[$this->registry->libs->_getRealIPv4()]['token']);
		}else{
			$obj = $this->__decrypt($creds);
			$x = $this->__auth($obj);
			if (is_array($x)){
				return $x;
			}else{
				$token = $this->__genToken($obj);
				$obj['signature'] = $this->registry->keyring->ssl->sign($token, $_SESSION[$this->registry->libs->_getRealIPv4()]['privateKey'], $_SESSION[$this->registry->libs->_getRealIPv4()]['password']);
				$x = $this->__register($obj);
			}
		}
		return $x;
	}

	/**
	 *! @function __auth
	 *  @abstract Returns booleans on database lookup of decrypted credentials
	 */
	private function __auth($creds)
	{
		if (is_array($creds)){
			if ((!empty($creds['email']))&&(!empty($creds['password']))){
				try{
					$sql = sprintf('CALL Auth_CheckUser("%s", "%s", "%s")',
									$this->registry->db->sanitize($creds['email']),
									$this->registry->db->sanitize($creds['password']),
									$this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'], 2048))));
					$r = $this->registry->db->query($sql);
					if ($r['x']<=0){
						$x = false;
					}else{
						$x = true;
					}
				} catch(Exception $e){
					// error handling
				}
			}else{
				$x = false;
			}
		}else{
			$x = false;
		}
		return (!$x) ? array('error'=>'User authentication failed') : $x;
	}

	/**
	 *! @function __redo
	 *  @abstract Decodes token and re-authenticates user
	 */
	public function __redo($token)
	{
		$a = $this->__decode($token);

		if (!$this->__hijack($a)){
			return false;
		}

		if (!$this->__timeout($a[6], $this->registry->opts['timeout'])){
			return false;
		}

		$s = $this->__getSignature($a[0]);

		if (!$s){
			return false;
		}

		if (!$this->__checkSignature($_SESSION[$this->registry->libs->_getRealIPv4()]['token'], $s)){
			return false;
		}

		$obj['email'] = $a[0];

		$token = $this->__genToken($obj);

		$obj['signature'] = $this->registry->keyring->ssl->sign($token, $_SESSION[$this->registry->libs->_getRealIPv4()]['privateKey'], $_SESSION[$this->registry->libs->_getRealIPv4()]['password']);
		$x = $this->__register($obj);

		//return ((empty($x['error']))&&(!empty($x['success']))) ? true : false;
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

	/**
	 *! @function __decode
	 *  @abstract Handle decoding of authentication token
	 */
	private function __decode($token)
	{
		return (!empty($token)) ? preg_split('/:/', $token) : false;
	}

	/**
	 *! @function __hijack
	 *  @abstract Performs anti-session hijacking validations
	 */
	private function __hijack($a)
	{
		if (!is_array($a)){
			$x = ((strcmp($a[3], sha1($this->registry->libs->_getRealIPv4()))===0)&&(strcmp($a[4], sha1(genenv('HTTP_USER_AGENT')))===0)&&(filter_var($a[5], FILTER_VALIDATE_REGEXP, array('options'=> array('regexp'=>'/^http(s?)\:\/\/'.getenv('HTTP_REFERER').'/Di')))));
		}else{
			$x = false;
		}
		return $x;
	}

	/**
	 *! @function __genToken
	 *  @abstract Creates unique token based on visiting machine and
	 *            authenticated user information
	 */
	private function __genToken($obj)
	{
		if (count($obj)>=2){
			if (($a = $this->__getLevelGroup($obj['email']))===false){
				return array('error'=>'Authenticated token generation failed, cannot continue');
			}
			$token = sprintf("%s:", "%s:", "%s:", "%s:", "%s:", "%s:", "%d",
								$this->registry->val->__do($obj['email'], 'email'),
								$this->registry->val->__do($a['level'], 'string'),
								$this->registry->val->__do($a['group'], 'string'),
								$this->registry->val->__do(sha1($this->registry->libs->_getRealIPv4()), 'string'),
								$this->registry->val->__do(sha1(getenv('HTTP_USER_AGENT')), 'string'),
								$this->registry->val->__do(getenv('HTTP_REFERER'), 'string'),
								time());
			$_SESSION[$this->registry->libs->_getRealIPv4()]['token'] = $token;
			return $token;
		}
	}

	/**
	 *! @function __getLevelGroup
	 *  @abstract Retrieves access leval and group membership for authenticated
	 *            user account
	 */
	private function __getLevelGroup($email)
	{
		try{
			$sql = sprintf('CALL Auth_GetLevelGroup("%s", "%s")',
							$this->registry->val->__do($email, 'email'),
							$this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'], 2048))));
		} catch(Exception $e){
			// error handler
		}
		$r = $this->registry->db->query($sql);
		return ($r) ? $r : false;
	}

	/**
	 *! @function __register
	 *  @abstract Registers current authentication token within users table
	 */
	private function __register($obj)
	{
		try{
			$sql = sprintf('CALL Users_AddUpdateToken("%s", "%s", "%s")',
							$this->registry->db->sanitize($obj['email']),
							$this->registry->db->sanitize($obj['signature']),
							$this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'], 2048))));
			$r = $this->registry->db->query($sql);
		} catch(Exception $e){
			// error handling
		}
		return ($r) ? array('success'=>'User was successfully authenticated') :
			array('error'=>'An error occured when associating token with user');
	}

	/**
	 *! @function __getSignature
	 *  @abstract Retrieve currently authenticated users signature associated with token
	 */
	private function __getSignature($email)
	{
		$r = false;
		if (!empty($email)){
			try{
				$sql = sprintf('CALL Users_GetToken("%s", "%s")', $this->registry->db->sanitize($email), $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'], 2048))));
				$r = $this->registry->db->query($sql);
			} catch(Exception $e){
				// error handler
			}
		}
		return ($r) ? $r : false;
	}

	/**
	 *! @function __checkSignature
	 *  @abstract Compares signature associated with authentication token
	 */
	private function __checkSignature($token, $signature)
	{
		if ((empty($token))||(empty($signature))){
			return false;
		}
		if (!$this->registry->ssl->verify($token, $signature, $_SESSION[$this->registry->libs->_getRealIPv4()]['publicKey'])){
			return false;
		}
		return true;
	}

	/**
	 *! @function __timeout
	 *  @abstract Returns boolean of current time vs. allowed time
	 */
	private function __timeout($a, $v)
	{
		return ($a < (time() - $v));
	}

}
?>
