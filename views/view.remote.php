<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle default page views
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   views
 * @discussion Handles remote auth template opts
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class remoteView
 *  @abstract Handles remote auth template opts
 */
class remoteView
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
	 *  @abstract Initializes singleton for indexView class
	 *  @param registry array - Global array of class objects
	 */
	private function __construct($registry)
	{
		$this->registry = $registry;
		$this->registry->tpl = new templates();
		$this->registry->tpl->strTemplateDir = $this->registry->opts['template'];
		$this->registry->tpl->strCacheDir = $this->registry->opts['caching'];
		$this->registry->tpl->boolCache=true;
		$this->registry->tpl->intTimeout=2629744;
		$this->_main();
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
	 *! @function _main
	 *  @abstract Handles all template loading at once
	 */
	private function _main()
	{
		$vm = '';
		if (!empty($_SERVER['HTTP_ORIGIN'])) {
			if (preg_match('/\:\d+$/', $_SERVER['HTTP_ORIGIN'])) {
				$vm = preg_split('/:/', $_SERVER['HTTP_ORIGIN']);
				$vm = ':'.$vm[2];
			}
		}

		$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
		$path = $proto.$_SERVER['HTTP_HOST'].$vm.'/';
		$this->registry->tpl->assign('templates', $path.'/'.$this->registry->tpl->strTemplateDir, null, null, null);
		$this->registry->tpl->assign('server', $proto.$_SERVER['HTTP_HOST'].$vm, null, null, null);
		$this->registry->tpl->assign('token', $_SESSION[$this->registry->libs->_getRealIPv4()]['csrf'], null, null, null);
		$this->registry->tpl->display('remote.tpl', true, null, $this->registry->libs->_getRealIPv4());
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
