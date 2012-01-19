<?php
/**
 * Manage RSA keys
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

class keyring
{
 protected static $instance;
 private function __construct($config, $args)
 {
  return;
 }
 public static function instance($config, $args)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($config, $args);
  }
  return self::$instance;
 }
 public function locationexists($db, $ip)
 {
  if (!empty($ip)) {
   $sql = sprintf('SELECT `computer` FROM `authentication-locations` WHERE
                  `computer` = "%s" LIMIT 1', $db->sanitize($ip));
   $results = $db->query($sql);
  }
  return ($db->affected($results)>0) ? $db->results($results) : false;
 }
 public function getkeys($db, $ip)
 {
  if (!empty($ip)) {
   $sql = sprintf('SELECT `private-key`,`public-key` FROM `authentication-keys` WHERE
                  `computer`="%s" LIMIT 1', $db->sanitize($ip));
   $results = $db->query($sql);
  }
  return ($db->affected($results)>0) ? $db->results($results) : false;
 }
 public function getkeysbyemail($db, $ip, $email='')
 {
  if (!empty($ip)) {
   $sql = sprintf('SELECT `private-key`,`public-key` FROM `authentication-keys` WHERE
                  `computer`="%s" AND `e-mail`="%s" LIMIT 1', $db->sanitize($ip),
                  $db->sanitize($email));
   $results = $db->query($sql);
  }
  return ($db->affected($results)>0) ? $db->results($results) : false;
 }
 public function add($db, $ip, $expire)
 {
  if ((!empty($ip))&&(!empty($expire))) {
   $expire = $expire * 86400;
   $sql = sprintf('INSERT INTO `authentication-locations`
                  (`resource` ,`computer`, `create-date`,
                   `expiration-date`) VALUES
                  ("%s", "%s", UNIX_TIMESTAMP(NOW()),
                   UNIX_TIMESTAMP(NOW())+%d) ON DUPLICATE KEY UPDATE
                   `resource` = "%s", `computer` = "%s",
                   `expiration-date` = "%s"', $db->sanitize(md5($ip)),
                   $db->sanitize($ip), $expire, $db->sanitize(md5($ip)),
                   $db->sanitize($ip), $expire);
   $results = $db->query($sql);
  }
  return ($db->affected($results)>0) ? true : false;
 }
 public function create($db, $ssl, $ip, $key, $pub, $config)
 {
  if ((!empty($ip))&&(!empty($key))&&(is_array($config))) {
   $config['config']['expires'] = $config['config']['expires'] * 86400;
   $sql = sprintf('INSERT INTO `authentication-keys`
                  (`resource` ,`computer`, `private-key`, `public-key`,
                   `expiration-date`) VALUES
                  ("%s", "%s", "%s", "%s", UNIX_TIMESTAMP(NOW())+%d)
                   ON DUPLICATE KEY UPDATE `computer` = "%s",
                   `private-key` = "%s", `public-key` = "%s",
                   `expiration-date` = "%s"', $db->sanitize(md5($ip)),
                   $db->sanitize($ip), $db->sanitize($key), $db->sanitize($pub),
                   $config['config']['expires'], $db->sanitize($ip),
                   $db->sanitize($key), $db->sanitize($pub),
                   $config['config']['expires']);
   $results = $db->query($sql);
   if ($this->newcertificate($db, $ssl, $key, $config)) {
    return ($db->affected($results)>0) ? true : false;
   } else {
    return false;
   }
  }
 }
 public function newcertificate($db, $ssl, $key, $config)
 {
  if (!empty($key)) {
   $expires = $config['config']['expires'] * 86400;
   /* gnarly gnarlesworth */
   $sql = sprintf('INSERT INTO `authentication-certificates`
                  (`resource`, `key-cipher`, `key-size`,
                   `create-date`, `expire-date`, `country-name`,
                   `province-name`, `locality-name`, `organization-name`,
                   `unit-name`, `common-name`, `certificate`) VALUES
                  ("%s", "%s", "%s", UNIX_TIMESTAMP(NOW()),
                   UNIX_TIMESTAMP(NOW()+%d), "%s", "%s", "%s", "%s",
                   "%s", "%s", "%s") ON DUPLICATE KEY UPDATE
                   `resource`="%s", `key-cipher`="%s", `key-size`="%s",
                   `expire-date`=UNIX_TIMESTAMP(NOW()+%d), `country-name`="%s",
                   `province-name`="%s", `locality-name`="%s",
                   `organization-name`="%s", `unit-name`="%s",
                   `common-name`="%s", `certificate`="%s"',
                   $db->sanitize(md5($_SERVER['REMOTE_ADDR'])),
                   $db->sanitize($config['config']['algorithm']),
                   $db->sanitize($config['config']['keybits']),
                   $db->sanitize($expires),
                   $db->sanitize($config['dn']['countryName']),
                   $db->sanitize($config['dn']['stateOrProvinceName']),
                   $db->sanitize($config['dn']['localityName']),
                   $db->sanitize($config['dn']['organizationName']),
                   $db->sanitize($config['dn']['organizationalUnitName']),
                   $db->sanitize($config['dn']['commonName']),
                   $db->sanitize($ssl->genCsr($config['dn'], $key,
                                              $config['config'],
                                              $_SERVER['REMOTE_ADDR'])),
                   $db->sanitize(md5($_SERVER['REMOTE_ADDR'])),
                   $db->sanitize($config['config']['algorithm']),
                   $db->sanitize($config['config']['keybits']),
                   $db->sanitize($expires),
                   $db->sanitize($config['dn']['countryName']),
                   $db->sanitize($config['dn']['stateOrProvinceName']),
                   $db->sanitize($config['dn']['localityName']),
                   $db->sanitize($config['dn']['organizationName']),
                   $db->sanitize($config['dn']['organizationalUnitName']),
                   $db->sanitize($config['dn']['commonName']),
                   $db->sanitize($ssl->genCsr($config['dn'], $key,
                                              $config['config'],
                                              $_SERVER['REMOTE_ADDR'])));
    $results = $db->query($sql);
  }
  return ($db->affected($results)>0) ? true : false;
 }
 public function __clone() {
  trigger_error('Cannot clone instance of Singleton pattern ...', E_USER_ERROR);
 }
 public function __wakeup() {
  trigger_error('Cannot deserialize instance of Singleton pattern ...', E_USER_ERROR);
 }
 private function __destruct()
 {
  unset(self::$instance);
  return true;
 }
}
?>
