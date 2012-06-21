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
	 * @var files array
	 * @abstract Array of installation files
	 */
	private $path = __SITE;

	/**
	 * @var files array
	 * @abstract Array of installation files
	 */
	private $files = array(//'install/schema/database-schema.sql',
						   'install/stored-procedures/sp_authenction.sql',
						   'install/stored-procedures/sp_configuration.sql',
						   'install/stored-procedures/sp_configuration_access.sql',
						   'install/stored-procedures/sp_configuration_applications.sql',
						   'install/stored-procedures/sp_configuration_cnf.sql',
						   'install/stored-procedures/sp_configuration_keys.sql',
						   'install/stored-procedures/sp_logs.sql',
						   'install/stored-procedures/sp_permissions.sql',
						   'install/stored-procedures/sp_users.sql');

	/**
	 *! @function init
	 *  @abstract Creates singleton for allow/deny class
	 *  @param $args array Array of registry items
	 */
	public static function init($args)
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new self($args);
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
		$post = $this->registry->val->__do($args);

		if (!empty($post['install'])) {

			/* add form validation measures */

			$this->registry->db = new mysqlDBconn(array('username'=>$post['root'],
														'hostname'=>'localhost',
														'password'=>'p@ssw0rd'));

			/* fixup our configuration file */
			if (file_exists('config/configuration.php.example')) {
				$contents = file_get_contents('config/configuration.php.example');
				$contents = str_replace('[dbUser]', $post['dbUser'], $contents);
				$contents = str_replace('[dbPass]', $post['dbPass'], $contents);
				$contents = str_replace('[dbHost]', $post['dbHost'], $contents);
				$contents = str_replace('[dbName]', $post['dbName'], $contents);
				$contents = str_replace('[title]', $post['title'], $contents);
				$contents = str_replace('[timeout]', $post['timeout'], $contents);
				$contents = str_replace('[flogin]', $post['flogin'], $contents);
				$contents = str_replace('[template]', $post['template'], $contents);
				$contents = str_replace('[start]', '<?php', $contents);
				$contents = str_replace('[stop]', '?>', $contents);
				//file_put_contents('config/configuration.php', $contents);
			}

			/* first create the database & permissions */
			if (file_exists('install/schema/database-schema.sql')){
				$contents = file_get_contents('install/schema/database-schema.sql');
				$contents = str_replace('[dbUser]', $post['dbUser'], $contents);
				$contents = str_replace('[dbPassword]', $post['dbPass'], $contents);
				$contents = str_replace('[dbHost]', $post['dbHost'], $contents);
				$contents = str_replace('[dbName]', $post['dbName'], $contents);
				//$this->_dbCreate($contents);
			}

			unset($this->registry->db);
			$this->registry->db = new mysqlDBconn(array('username'=>$post['root'],
														'hostname'=>$post['dbHost'],
														'password'=>$post['password'],
														'database'=>$post['dbName']));

			/* fixup our sql files */
			foreach($this->files as $value) {
				if (file_exists($value)) {
					$contents = file_get_contents($value);
					$contents = str_replace('[dbUser]', $post['dbUser'], $contents);
					$contents = str_replace('[dbPassword]', $post['dbPass'], $contents);
					$contents = str_replace('[dbHost]', $post['dbHost'], $contents);
					$contents = str_replace('[dbName]', $post['dbName'], $contents);
					if (preg_match('/schema\//', $value)){
						file_put_contents(str_replace('schema/', 'tmp/', $value), $contents);
					} else {
						file_put_contents(str_replace('stored-procedures/', 'tmp/', $value), $contents);
						$cmd = sprintf('mysql -u %s --password=%s --database %s < %s',
									   $post['root'], $post['password'], $post['dbName'],
									   str_replace('stored-procedures/', 'tmp/', $value));
						`$cmd`;
					}
				}
			}

			// create default configuration settings

			// create hash for global database aes encryption

			// save hash and prepare for database content

			// create default set of keys for application
		}
	}

	/**
	 *! @function _dbCreate
	 *  @abstract Create the database, user & permissions
	 */
	private function _dbCreate($f)
	{
		echo '<pre>'; print_r($f); echo '</pre>';
		try {
			$this->registry->db->query($f);
		} catch(PDOException $e) {
			// error handling
		}
		return;
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
		$privateKey = $this->ssl->genPriv(hashes::init($this->registry)->_do($this->registry->opts['dbKey']));
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
							$this->registry->db->sanitize(hashes::init($this->registry)->_do($this->registry->opts['dbKey'])));
		} catch(Exception $e){
			// error handling
		}
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
