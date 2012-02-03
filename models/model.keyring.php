<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Manage RSA keys and encryption
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
 *! @class keyring
 *  @abstract Handles OpenSSL RSA keys
 */
class keyring
{

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  * @var ssl object
  * @abstract Handles current users ssl object
  */
 private $ssl;

 /**
  *! @var instance object - class singleton object
  */
 protected static $instance;

 /**
  *! @var config array - OpenSSL configuration settings
  */
 private $config = array('config'              => 'config/openssl.cnf',
                         'encrypt_key'         => true,
                         'private_key_type'    => OPENSSL_KEYTYPE_RSA,
                         'digest_algorithm'    => 'sha256',
                         'private_key_bits'    => 2048,
                         'x509_extensions'     => 'usr_cert',
                         'encrypt_key_cipher'  => OPENSSL_CIPHER_3DES);

 /**
  *! @var dn array - Contains location specific settings
  */
 private $dn = array('countryName'             => 'US',
                     'stateOrProvinceName'     => 'Utah',
                     'localityName'            => 'Salt Lake City',
                     'organizationName'        => 'University Of Utah',
                     'organizationalUnitName'  => 'Marriott Library',
                     'commonName'              => 'localhost:8080',
                     'emailAddress'            => 'licensing@utah.edu');

 /**
  *! @function __construct
  *  @abstract Initializes singleton for proxyView class
  *  @param registry array - Global array of class objects
  */
 public function __construct($registry)
 {
  $this->registry = $registry;
  $this->__setup($this->registry->val->__do($_POST['email'], 'email'));

 }

 /**
  *! @function instance
  *  @abstract Creates non-deserializing, non-cloneable instance object
  *  @param configuration array - server, username, password, database
  *  @return Singleton - Singleton object
  */
 public static function instance($config, $args)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($config, $args);
  }
  return self::$instance;
 }

 /**
  *! @function setup
  *  @abstract Creates new OpenSSL object based on current user or
  *            default settings
  */
 private function __setup($email=false)
 {
  $c = $this->__settings();
  $d = $this->__dn();

  //$this->config = ($this->__vsettings($c)) ? $c : $this->config;
  $this->dn = ($this->__vdn($d)) ? $d : $this->dn;

  if (class_exists('openssl')){
   $this->ssl = openssl::instance(array('config'=>$this->config,
                                        'dn'=>$this->dn));

  $this->ssl->genRand();
  $h = openssl_pkey_new(array('private_key_bits'=>1024));
  print_r($h);
  openssl_pkey_export($h, $key, $this->registry->opts['dbKey']);
  print_r($key);

  echo 'PRIVATE KEY: <pre>'; print_r($this->ssl->genPriv($this->registry->opts['dbKey'])); echo '</pre>';
  echo 'PUBLIC KEY: <pre>'; print_r($this->ssl->genPub()); echo '</pre>';

  }
 }

 /*
  * Verify our $this->config array
  */
 private function __vsettings($array)
 {
  return ((!empty($array['config']))&&
          (!empty($array['encrypt_key']))&&
          (!empty($array['private_key_type']))&&
          (!empty($array['digest_algorithm']))&&
          (!empty($array['private_key_bits']))&&
          (!empty($array['x509_extensions']))&&
          (!empty($array['encrypt_key_cipher']))) ? true : false;
 }

 /*
  * Verify our $this->dn array
  */
 private function __vdn($array)
 {
  return ((!empty($array['countryName']))&&
          (!empty($array['stateOrProvinceName']))&&
          (!empty($array['localityName']))&&
          (!empty($array['organizationName']))&&
          (!empty($array['organizationalUnitName']))&&
          (!empty($array['commonName']))&&
          (!empty($array['emailAddress']))) ? true : false;
 }

 /**
  *! @function __public
  *  @abstract Retrieves the public key for the specified account (defaults to
  *            the current system key pair if email is empty)
  */
 public function __public($email=false)
 {
  $r = false;
  try{
   if (!$email){
    $sql = sprintf('CALL Configuration_def_get("%s")',
                   $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'],
                                                                              $this->registry->libs->_salt($this->registry->opts['dbKey'],
                                                                              2048))));
   } else {
    $sql = sprintf('CALL Configuration_keys_get("%s, %s")',
                   $this->registry->db->sanitize($email),
                   $this->registry->db->sanitize($this->registry->libs->_hash($this->registry->opts['dbKey'],
                                                                              $this->registry->libs->_salt($this->registry->opts['dbKey'],
                                                                              2048))));
   }
   $r = $this->registry->db->query($sql);
   $r = ((!empty($r['publicKey']))&&(!empty($r['emailAddress']))) ? array('email'=>$r['email'],'key'=>$r['publicKey']) : false;
  } catch(Exception $e){
   // error handler
  }
  return $r;
 }

 /**
  *! @function __settings
  *  @abstract Handles the retrieval of current OpenSSL configuration data
  */
 private function __settings()
 {
  try{
   $sql = sprintf('CALL Configuration_cnf_get()');
   $r = $this->registry->db->query($sql);
  } catch(Exception $e){
   // error handler
  }
  return (!empty($r)) ? $r : false;
 }

 /**
  *! @function __dn
  *  @abstract Handles the retrieval of current OpenSSL DN
  */
 private function __dn()
 {
  try{
   $sql = sprintf('CALL Configuration_get_dn()');
   $r = $this->registry->db->query($sql);
  } catch(Exception $e){
   // error handler
  }
  return (!empty($r)) ? $r : false;
 }

 /**
  *! @function __decrypt
  *  @abstract Handle decryption of submitted form data
  */
 private function __decrypt($obj)
 {
  return array('successs'=>'Processing encrypted form data');
 }

 public function __clone() {
  trigger_error('Cannot clone instance of Singleton pattern ...', E_USER_ERROR);
 }

 public function __wakeup() {
  trigger_error('Cannot deserialize instance of Singleton pattern ...', E_USER_ERROR);
 }

 public function __destruct()
 {
  unset($instance);
  return true;
 }
}
/*
echo $this->registry->opts['dbKey'];
$this->ssl->genRand();
echo '<pre>'; print_r($this->ssl->genPriv($this->registry->opts['dbKey'])); echo '</pre>';
echo '<pre>'; print_r($this->ssl->genPub()); echo '</pre>';
echo '<pre>'; print_r($this->registry->libs->_hash($this->registry->opts['dbKey'], $this->registry->libs->_salt($this->registry->opts['dbKey'],2048))); echo '</pre>';

CALL Configuration_def_add('Marriott Library - Software Licensing', 'default', 'default/cache', 1, 'licensing@utah.edu', 3600, '-----BEGIN ENCRYPTED PRIVATE KEY-----
MIIFDjBABgkqhkiG9w0BBQ0wMzAbBgkqhkiG9w0BBQwwDgQIawFdxeJ/e/oCAggA
MBQGCCqGSIb3DQMHBAgsaetAtZlzmgSCBMi/XNdF8G+d/PrpEj3C6hEnxPSYPpJc
6RWJQKx4h8QvQD4q3HuOPBjBtEnFJl0vfRM12Vm72h+Kdm5KcCrjCWXmAyp3eyJ8
EO+eq5lqTBviZfxHZgrToGoWrkNpzU5HizYZ+6i+7khDyP0kNQ3osIdPSjDdbfah
a5SGaUQt8pno1ByQxbVcYJFJhr1iRkAK0AMd+zVdd5CVuzikkT1pjyS3x9tnCS5O
y3wZN/a/WAWUSGdA7mNzNWJ79VxZLvfWJ2uC2jXTZ34OFiC4Bzf3YRSOaLSds6yJ
9z0owYAZ0LKfr3zXVgWYqqZ5nFyz2/C2lezfbpCzMhkUS37N1UqCZFHEApo2qUpQ
QzWY0QNYnnDb3cN57d2y8eyltaqQzL0WbDmgPPwni2J6Ed1wosqXPqfR8tou2YaF
da3S797Cv2//g1nUtCPUW5zTPXifO94YDRVLUeN3zM936ng3GF8fh3r/G1DG8/zB
wlgEffomvbr5zFibDUNLslYTOXx+kPaEdYsTk4i7YhtctgH1qnDJdVnsqM1JAPfi
As/s4MIxKPc4hxwp6txRJSEZMTSGDutgO5KRRV4YA3VzDe0edMoI/vT3zXhuczqg
KdxlQLk1+KybsyD5MQ4mXwRUFVPGYM0+0Eio9sUWegMS2rKgXzgXis6SDeJ0hydH
PZOsCJCNbqQ/ep1iFs4lHLRq5wZzC5CN14eSJjFckEvJIduC4R69CMFT92zQOmwy
0H/y7Si7Fqm8iFZOJDPhlM7DfUZUzJP8zN4sJEfFqQ8VM0IpEFMuLtiIrWGsZWK7
ndMdBgaJ6nrrMd1+QcYjtLjK23vyL6sWefLAPrWr8awdyXWRPMNhlvp69co75d8b
mSUQqzRbT9/IgCokcxvSxt6YmuztdItunA6o7R4SEfGgg/xLC1bVLikQqLoSAAni
3t9l3A7TlO5+yGwbuiyVF2y3WDR7asKuPMOUbF3HTFKDX0vpMIs4yTFwRzQHfDcl
kAZ5bd69qfnuUZFHsO0LnzCuHHNUdv+amJkS2XMuXuM54lvGtlEhlotuHbjr+rOV
4+LT5n3ObWIbG7ndc74rRz7/eeEesk6o/fz3hgk3x2Yl1+CWdc8xnE8X8ojBSTnp
xWkFllfKxP+i/MGsNvmFf4EFBVSZvZ+csqr99A6fQUUl06WZrYXWVAlHg8jfWD+x
tpSGpOi6KHg3qUVUF+qqruKU3DGIXc1GMFzxqEf7hP57f28eml0ZboIVpJ+INrZ+
Sqpiu0q0qaPuf0rC8phWoVBcDMOcnMB9dfi2rUcS/d2IbeGBx4go1tPKHf6LJoCK
C4zvmoMi0zBxyLcWqVnnXaCQ018cr8QAUHlHJjKLJdWDXCyb0XNjWsoX3L/MHuI2
2dd3JXC3kZfL+2vxnIlAIxhbynV/lginlScfYep6FglMnaCSQS0uRnldIdjeUX0g
8NkhYiJrZpa/wBOE22Km3gl9fQnsby+feL+ADyHcJmGBDaw+beeW+wgEpTfJw0+1
vXdBHEjlQJ1l8pFrP4QZx/kz1xBL18EkmOqtFk7tmX/SYBt8tB/TDLXbcetrTz/T
h8JPONr6j7Pl1UBUiahDkEjG67pEcvUUg5dMVdT0UXAuGluKsHabrTA1uaKNGQX+
wcY=
-----END ENCRYPTED PRIVATE KEY-----', '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA50kQjkvlfmi/jOvM+0bO
BLL7xwtyeDy54zIW2U441R0qbZiXQdxRhOsFUPRHIwmdR6IxUSQ0Ij6/bmFB8vrt
yCvThFXqhEodD0rlpXg494bWTQJOV0Kp+0Fkc4oP2suVkNpeVNqeBAqYJE9dJC1T
c5/HrpLwA/5DA1gvaPugsdyZEBx380jG63unB4rnjktCEDHYx2c6bfkjPTrJBdmK
/SIq+xZiBsna2e8XphoyXnx0PjdHbbmle58vXVHAqjTFVh0KO8Hi/jFw8ZR3mxHi
hGlkC/btFbIPCTZjyWSQgv2FHU5IhrD6PuEWfdGxgSwNqsipXXPeoF5Q+AGOV11a
mQIDAQAB
-----END PUBLIC KEY-----', '$2a$07$31a9f929d102f5f0374deughM4JFpGypzdIROjgU4dLK32NCKN2Y.', 'UT', 'Utah', 'Salt Lake City', 'University Of Utah', 'Marriott Library', 'localhost:8080');
*/
?>
