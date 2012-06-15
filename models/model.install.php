<?php

/* define the namespace */
//namespace models\installer;

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Installer class
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
 * @copyright  2010-2011 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class install
 *  @abstract Handles preliminary installation
 */
class install {

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
	public static function init()
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *! @function __construct
	 *  @abstract Class initialization and ip to access/deny processing
	 *  @param $args array Array of registry items
	 */
	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	/**
	 *! @function _main
	 *  @abstract Public interface for application installation
	 */
	public function _main($args)
	{
		// examine post data
		
		// make modifications to all database files based on user supplied input

		// connect to database as root user
		
		// import database schema
		
		// import all stored procedures

		// create default configuration settings

		// create hash for global database aes encryption

		// create default set of keys for application

		
	}

	/**
	 *! @function parsegeo
	 *  @abstract Retrieves specific location data from GeoIP lookup
	 */
	private function parsegeo($data, $ip, $config)
	{
		$settings['organizationName'] = $ip;
		$settings['organizationalUnitName'] = $ip;
		$settings['emailAddress'] = $ip;
		$settings['localityName'] = (!empty($data['geoplugin_city'])) ?	$data['geoplugin_city'] : $config['dn']['localityName'];
		$settings['stateOrProvinceName'] = (!empty($data['geoplugin_region'])) ? $data['geoplugin_region'] : $config['dn']['stateOrProvinceName'];
		$settings['countryName'] = (!empty($data['geoplugin_countryCode'])) ? $data['geoplugin_countryCode'] : $config['dn']['CountryName'];
		$settings['commonName'] = ((!empty($data['geoplugin_latitude']))&&(!empty($data['geoplugin_longitude']))) ? $data['geoplugin_latitude'].'::'.$data['geoplugin_longitude'] : $ip;
		return $settings;
	}

	public function __clone() {
		trigger_error('Cloning prohibited', E_USER_ERROR);
	}
	public function __wakeup() {
		trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
	}
	private function __destruct()
	{
		unset(self::$instance);
		return true;
	}
}

class envrionment
{
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
	public static function init()
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *! @function __construct
	 *  @abstract Class initialization and ip to access/deny processing
	 *  @param $args array Array of registry items
	 */
	public function __construct($registry)
	{
		$this->registry = $registry;
	}

	public function _test()
	{
		$req = array('php'=>array('version'=>'5.4', 'functions'=>array()),
					 'openssl'=>array('version'=>'1.0'),
					 'mysql'=>'5.1');
	}
}
?>
