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

	public function setDN($ssl, $handles)
	{
		if (empty($ssl)) {
			$a = $this->parsegeo($this->geolocation($_SERVER['REMOTE_ADDR']),
					$_SERVER['REMOTE_ADDR'],
					$ssl['dn']);
			$a = $handles['filter']->__do($a);
			$sql = sprintf('CALL ConfigurationOpenSSLDNUpdate("%s", "%s", "%s", "%s", "%s",
						"%s", "%s", @x)',
					$handles['db']->sanitize($a['countryName']),
					$handles['db']->sanitize($a['stateOrProvinceName']),
					$handles['db']->sanitize($a['localityName']),
					$handles['db']->sanitize($a['organizationName']),
					$handles['db']->sanitize($a['organizationalUnitName']),
					$handles['db']->sanitize($a['commonName']),
					$handles['db']->sanitize($a['emailAddress']));
			try {
				$x = $handles['db']->query($sql);
				return ($x['@x']==='0') ? false : true;
			} catch (PDOException $e) {
				return false;
			}
		}
		return false;
	}
	public function setup($results, $handles)
	{
		if ($results['@x']==='0') {
			$pass = $handles['openssl']->aesEnc($this->uuid(), $this->uuid(),
					$this->_16($this->uuid()));
			$x = $handles['openssl']->genPriv($pass);
			$a = $handles['openssl']->genPub();

			$pvkey = $handles['openssl']->aesEnc($x, $pass, $this->_16($pass));
			$pkey = $handles['openssl']->aesEnc($a['key'], $pass,
					$this->_16($pass));

			$sql = sprintf('CALL ConfigurationUpdate("%s", "%s", "%s", "%d", "%s", "%d",
						"%s", "%s", "%s", @x)',
					$handles['db']->sanitize($_POST['title']),
					$handles['db']->sanitize($_POST['templates']),
					$handles['db']->sanitize('cache'),
					$handles['db']->sanitize(1),
					$handles['db']->sanitize($_POST['email']),
					$handles['db']->sanitize($_POST['timeout']),
					$handles['db']->sanitize($pkey),
					$handles['db']->sanitize($pvkey),
					$handles['db']->sanitize($pass));
			try {
				$i = $handles['db']->query($sql);
				return ($i['@x']==='0') ? false : true;
			} catch (PDOException $e) {
				return false;
			}
		}
		return true;
	}
	public function acct($handles, $configuration)
	{
		$password = $handles['openssl']->penc($_POST['password'],
				$handles['openssl']->aesDenc($configuration['pkey'],
					$configuration['pass'],
					$this->_16($configuration['pass'])));
		$sql = sprintf('CALL AuthenticationUserAddUpdate("%s", "%s", "%s", "%s", "%s")',
				$handles['db']->sanitize(sha1($_POST['email'])),
				$handles['db']->sanitize($_POST['email']),
				$handles['db']->sanitize($password),
				$handles['db']->sanitize('admin'),
				$handles['db']->sanitize('admin'));
		try {
			$x = $handles['db']->query($sql);
			return ($x['@x']==='0') ? false : true;
		} catch (PDOException $e) {
			return false;
		}
	}
	public function permissions($handles)
	{
		$sql = sprintf('CALL ResourcesAddNew("%s", "%s", "%s", "%s", "%d", "%d", "%s",
					"%d", "%d", @x)',
				$handles['db']->sanitize(sha1($_POST['email'])),
				$handles['db']->sanitize($_POST['email']),
				$handles['db']->sanitize($_POST['email']),
				$handles['db']->sanitize('admin'),
				$handles['db']->sanitize(1), $handles['db']->sanitize(1),
				$handles['db']->sanitize($_POST['email']),
				$handles['db']->sanitize(1), $handles['db']->sanitize(1));
		try {
			$i = $handles['db']->query($sql);
			return ($i['@x']==='0') ? false : true;
		} catch (PDOException $e) {
			return false;
		}
	}

	public function __do($dbs, $filter, $variables, $errors, $language)
	{
		$variables['error'] = $errors[$language]['\x0iB'];
		$variables['class'] = 'good';
		$db = dbconn::instance(array('hostname'=>'localhost',
					'username'=>$_POST['ruser'],
					'password'=>$_POST['rpassword'],
					'database'=>''));
		if (is_object($db)) {

			if (($x = $this->_createdb($db, $errors))!==true) {
				$variables['error'] = $x;
			}

			if (($x = $this->_createdbuser($db, $errors))!==true) {
				$variables['error'] = $x;
			}

			if (($x = $this->_usedb($db, $errors))!==true) {
				$variables['error'] = $x;
			}

			if (($x = $this->_setupdb($db, $errors))!==true) {
				$variables['error'] = $x;
			}

			if (($x = $this->_setupdbuser($db, $errors))!==true) {
				$variables['error'] = $x;
			}

			if (($x = $this->_importsp($db))!==true) {
				$variables['error'] = $x;
			}

			if ((!is_array($ssl['dn']))&&(count($ssl['dn'])>=0)) {
				if ($this->setDN($ssl['dn'], array('db'=>$db, 'filter'=>$filter,
								'language'=>$language))===false) {
					$variables['error'] = $errors[$languages]['\x0i0'];
				}
			}
			$ssl['dn'] = $db->query('CALL ConfigurationOpenSSLDNSelect()');
			$openssl = openssl::instance($ssl);
			$results = $db->query('CALL ConfigurationCheck(@x)');
			if ($results['@x']==='0') {
				if ((empty($_POST['title']))&&(empty($_POST['templates']))&&
						(empty($_POST['timeout']))&&(empty($_POST['email']))) {
					$configuration = array('title'=>'empty title', 'templates'=>'templates/default',
							'cache'=>'cache', 'private'=>1, 'email'=>'admin',
							'timeout'=>3600);
				}
				if ($this->setup($results, array('db'=>$db, 'filter'=>$filter, 'openssl'=>$openssl,
								'language'=>$language))===false) { echo 1;
					$variables['error'] = $errors[$language]['\x0i1'];
				}
			}
			$configuration = $db->query('CALL ConfigurationSelect()');
			if ((!empty($_POST['email']))&&(!empty($_POST['password']))) {
				if ($this->acct(array('db'=>$db, 'filter'=>$filter, 'openssl'=>$openssl),
							$configuration)===false) {
					$variables['error'] = $errors[$language]['\x0i2'];
				}
				if ($this->permissions(array('db'=>$db, 'filter'=>$filter))===false) {
					$variables['error'] = $errors[$language]['\x0i3'];
				}
			}
			$variables['class'] = ($variables['error']!==$errors[$language]['\x0iB']) ? 'error' : 'good';
			return $variables;
		}
	}

	public function _createdb($db, $errors)
	{
		$a = sprintf('CREATE DATABASE %s', $db->sanitize($_POST['dbname']));
		try {
			$db->query($a);
		} catch (PDOException $e) {
			$error = $errors[$language]['\x0i4'];
		}
		return (isset($error)) ? $error : true;
	}

	public function _createdbuser($db, $errors)
	{
		$error = true;
		$b = sprintf('CREATE USER "%s"@"%s" IDENTIFIED BY "%s"',
				$db->sanitize($_POST['uname']), $db->sanitize($_POST['server']),
				$db->sanitize($_POST['upassword']));
		try {
			$db->query($b);
		} catch (PDOException $e) {
			$error = $errors[$language]['\x0i5'];
		}
		return (isset($error)) ? $error : true;
	}

	public function _usedb($db, $errors)
	{
		$c = sprintf('USE %s', $db->sanitize($_POST['dbname']));
		try {
			$db->query($c);
		} catch(PDOException $e) {
			$error = $errors[$language]['\x0i6'];
		}
		return (isset($error)) ? $error : true;
	}

	public function _setupdb($db, $errors)
	{
		if (file_exists('sql/database-schema.sql')) {
			$d = implode("\n", file('sql/database-schema.sql'));
			try {
				$db->query($d);
			} catch(PDOException $e) {
				$error = $errors[$language]['\x0i7'];
			}
		} else {
			$error = $errors[$language]['\x0i7'];
		}
		return (isset($error)) ? $error : true;
	}

	public function _setupdbuser($db, $errors)
	{
		$e = sprintf('GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES, INDEX,
				CREATE TEMPORARY TABLES, LOCK TABLES, TRIGGER, EXECUTE ON
				`%s`.* TO "%s"@"%s"', $db->sanitize($_POST['dbname']),
				$db->sanitize($_POST['uname']), $db->sanitize($_POST['server']));
		try {
			$db->query($e);
		} catch (PDOException $e) {
			$error = $errors[$language]['\x0i8'];
		}
		return (isset($error)) ? $error : true;
	}

	public function _importsp($db)
	{
		if (file_exists('sql/prepared-statements.sql')) {
			$cmd = sprintf('mysql -u %s --password=%s --database %s < sql/prepared-statements.sql',
					$db->sanitize($_POST['ruser']), $db->sanitize($_POST['rpassword']),
					$db->sanitize($_POST['dbname']));
			/* flipping importing stored procedures fails with PDO */
			`$cmd`;
		}
		return true;
	}

	public function _getsslcnf($db, $errors)
	{
		try {
			$cnf = $db->query('CALL ConfigurationOpenSSLCNFSelect()');
		} catch (PDOException $e) {
			$error = $errors[$language]['\x0i10'];
		}
		return ((is_array($cnf))&&(count($cnf)>=6)) ? $cnf : $error;
	}

	public function _getssldn($db, $errors)
	{
		try {
			$dn = $db->query('CALL ConfigurationOpenSSLDNSelect()');
		} catch (PDOException $e) {
			$error = $errors[$language]['\x0i11'];
		}
		return ((is_array($dn))&&(count($dn)>=7)) ? $dn : $error;
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
?>
