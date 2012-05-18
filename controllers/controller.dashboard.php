<?php
/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle dashboard for user management area
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   views
 * @discussion Handles the dashboard for user management area
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class dashboardController
 *  @abstract Handles the dashboard
 */
class dashboardController
{

	/**
	 * @var registry object
	 * @abstract Global class handler
	 */
	private $registry;

	/**
	 *! @function __construct
	 *  @abstract Class loader
	 *  @param registry array - Global array of class objects
	 */
	public function __construct($registry)
	{
		/* load and start up session support */
		if (!class_exists('dbSession')){
			exit('Error loading database session support, unable to proceed. 0x0c6');
		}

		/* create new instance of sessions? */
		if (!is_object($regsitry->sessions)){
			$registry->sessions = new dbSession($settings['sessions'], $registry);
		}

		/* generate or use CSRF token */
		if (!isset($_SESSION[$registry->libs->_getRealIPv4()]['csrf'])) {
			$_SESSION[$registry->libs->_getRealIPv4()]['csrf'] = $registry->libs->uuid();
		}

		/* always reset the session_id */
		$registry->sessions->regen(true);
		echo '<pre>'; print_r($_SESSION); echo '</pre>';

		$this->registry = $registry;
	}

	/**
	 *! @function __auth
	 *  @abstract Handles authentication
	 */
	private function __auth($token)
	{
		$email = (!empty($_SESSION[$this->registry->libs->_getRealIPv4()]['email'])) ? array('email'=>$_SESSION[$this->registry->libs->_getRealIPv4()]['email']) : $_POST;
		$this->registry->keyring = new keyring($this->registry, $this->registry->val->__do($email));
		$auth = authentication::instance($this->registry);
		return ($auth->__reauth($token)) ? true : false;
	}

	/**
	 *! @function __perms
	 *  @abstract Handles permissions examination on resources
	 */
	private function __perms($token)
	{

	}

	/**
	 *! @function __load
	 *  @abstract Handles loading of associated view classes
	 */
	private function __load($file, $class)
	{
		if (file_exists($file)) {
			require $file;
		}
		$class::instance($this->registry);
	}

	/**
	 *! @function index
	 *  @abstract Calls default view
	 */
	public function index()
	{
		if ($this->__auth($_SESSION['token'])) {
			$auth = authentication::instance($this->registry);

			$l = $auth->__level($_SESSION[$this->registry->libs->_getRealIPv4()]['token']);
			$g = $auth->__group($_SESSION[$this->registry->libs->_getRealIPv4()]['token']);

			if (($l) && ($g)) {
				switch($l) {
					/* super user, admin access level and admin group membership */
					case (($l === "admin") && ($g === "admin")):
						$p = 'views/admin/view.dashboard.php';
						$c = 'dashboardView';
						break;
					/* group super user, admin access level and non-admin group membership */
					case (($l === "admin") && ($g !== "admin")):
						$p = 'views/admin/view.dashboard.php';
						$c = 'dashboardView';
						break;
					/* normal user, non-admin access level and non-admin group membership */
					case (($l !== "admin") && ($g !== "admin")):
						$p = 'views/admin/view.dashboard.php';
						$c = 'dashboardView';
						break;
					/* provide disallowed view default */
					default:
						$p = 'views/admin/view.dashboardDisallowed.php';
						$c = 'dashboardDisallowed';
						break;
				}
			} else {
				/* problem with supplied user & group default to deny */
				$p = 'views/admin/view.dashboardDisallowed.php';
				$c = 'dashboardDisallowed';
			}
		} else {
			/* problem with authentication default to deny */
			$p = 'views/admin/view.dashboardDisallowed.php';
			$c = 'dashboardDisallowed';
		}
		/* remove these once dashboard complete */
		$p = 'views/admin/view.dashboard.php';
		$c = 'dashboardView';
		$this->__load($p, $c);
	}
}
?>
