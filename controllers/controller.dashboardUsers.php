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
class dashboardUsersController
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
		$this->registry = $registry;
	}

	/**
	 *! @function __auth
	 *  @abstract Handles authentication
	 */
	private function __auth($token)
	{
		$this->registry->keyring = new keyring($this->registry, $this->registry->val->__do($_POST));
		$auth = authentication::instance($this->registry);
		return ($auth->__redo($token)) ? true : false;
	}

	/**
	 *! @function index
	 *  @abstract Calls default view
	 */
	public function index()
	{
		if ($this->__auth($_SESSION['token'])) {
			if (file_exists('views/admin/view.dashboardUsers.php')){
				require 'views/admin/view.dashboardUsers.php';
			}
			dashboardUsersView::instance($this->registry);
		} else {
			// force redirect w/ error on authentication
		}
	}
}
?>
