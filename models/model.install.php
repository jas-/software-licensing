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

		// save hash and prepare for database content

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

	/**
	 *! @function __installer
	 *  @abstract Temporary installer function
	 */
	private function __installer()
	{
		$this->ssl->genRand();
		$privateKey = $this->ssl->genPriv($this->registry->opts['dbKey']);
		$publicKey = $this->ssl->genPub();
		try{
			$sql = sprintf('CALL Configuration_def_add("%s", "%s", "%s", "%d", "%s","%d", "%s", "%s", "%s", "%s","%s", "%s", "%s", "%s", "%s", "%s")',
							$this->registry->db->sanitize($this->registry->opts['title']),
							$this->registry->db->sanitize($this->registry->opts['template']),
							$this->registry->db->sanitize($this->registry->opts['caching']),
							$this->registry->db->sanitize($this->config['encrypt_key']),
							$this->registry->db->sanitize($this->dn['emailAddress']),
							$this->registry->db->sanitize($this->registry->opts['timeout']),
							$this->registry->db->sanitize($privateKey),
							$this->registry->db->sanitize($publicKey),
							$this->registry->db->sanitize($this->registry->opts['dbKey']),
							$this->registry->db->sanitize($this->dn['countryName']),
							$this->registry->db->sanitize($this->dn['stateOrProvinceName']),
							$this->registry->db->sanitize($this->dn['localityName']),
							$this->registry->db->sanitize($this->dn['organizationName']),
							$this->registry->db->sanitize($this->dn['organizationalUnitName']),
							$this->registry->db->sanitize($this->dn['commonName']),
							$this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'], 2048))));
		} catch(Exception $e){
			// error handling
		}
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
