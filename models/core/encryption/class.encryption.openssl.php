<?php
/**
 * Handle openssl encryption functionality
 *
 * Creates public / private OpenSSL keys
 * Used to encrypt & decrypt data with supplied keys
 * Optional file output methods
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   encryption
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class openssl
{
 protected static $instance;
 private $handle=null;
 private $opt=array();
 private $dn=array();
 private function __construct($configuration)
 {
  if (function_exists('openssl_pkey_new')) {
   $this->setOpt($configuration);
   $this->setDN($configuration);
   $this->handle = $this->setHandle();
   return;
  } else {
   echo 'The openssl extensions are not loaded.';
   unset($instance);
   exit;
  }
 }
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }
 private function setOpt($configuration)
 {
  $this->opt = $configuration['cnf'];
 }
 private function setDN($configuration)
 {
  $this->dn = $configuration['dn'];
 }
 private function setHandle()
 {
  $this->handle = openssl_pkey_new();
 }
 public function genPriv($password)
 {
  $this->handle = openssl_pkey_new();
  openssl_pkey_export($this->handle, $privatekey, $password);
  return $privatekey;
 }
 public function genPub()
 {
  $results = openssl_pkey_get_details($this->handle);
  return $results;
 }
 public function getPub($cert)
 {
  return openssl_pkey_get_details(openssl_csr_get_public_key($cert));
 }
 public function genCSR($password)
 {
  $this->dn['emailAddress']= (empty($this->dn['emailAddress'])) ? 'blank@blank.com' : $this->dn['emailAddress'];
  $t = openssl_pkey_export($this->handle, $password);
  return openssl_csr_new($this->dn, $t);
 }
/*
 public function genCsr($dn, $opts, $password)
 {
  $csr = $this->signReq($dn, $this->handle, $opts['expires']);
  $signed = $this->sign($csr, $private, $password, $opts['expires'], $opts);
  openssl_x509_export($signed, $output);
  return $output;
 }
*/
 public function createPKCS12($x509, $private, $password, $opts='')
 {
  $a = openssl_pkey_get_private($private, $password);
  print_r(openssl_error_string());echo '<hr>';
  $b = openssl_x509_read($x509);
  print_r(openssl_error_string());echo '<hr>';
  openssl_pkcs12_export($b, $output, $a, $opts);
  print_r(openssl_error_string());
  return $output;
 }
 public function decodeCert($certificate)
 {
  return openssl_x509_parse($certificate);
 }
 private function signReq($dn, $private, $opt)
 {
  return ((is_array($opt))&&(!empty($opt))) ?
          openssl_csr_new($dn, $private, $opt) :
          openssl_csr_new($dn, $private);
 }
 private function sign($csr, $private, $password, $days, $opt, $ca=NULL)
 {
  $a = openssl_pkey_get_private($private, $password);
  return openssl_csr_sign($csr, $ca, $a, $days, $opt);
 }
 public function enc($private, $data, $password)
 {
  if ((!empty($private))&&(!empty($data))) {
   $res = openssl_get_privatekey($private, $password);
   return (!openssl_private_encrypt($data, $output, $res)) ? false : $this->convertHex($output);
  } else {
   return false;
  }
 }
 public function penc($data, $public)
 {
  if ((!empty($public))&&(!empty($data))) {
   $res = openssl_get_publickey($public);
   return (!openssl_public_encrypt($data, $output, $res)) ? false : $this->convertHex($output);
  } else {
   return false;
  }
 }
 public function pubDenc($crypt, $key, $flag=0)
 {
  $res = (is_array($key)) ? openssl_get_publickey($key['key']) :
                            openssl_get_publickey($key);
  openssl_public_decrypt($this->convertBin($crypt), $output, $res);
  return ($_SERVER["HTTP_X_REQUESTED_WITH"]==='XMLHttpRequest') ?
          base64_decode($output) : $output;
 }
 public function privDenc($crypt, $key, $pass, $flag=0)
 {
  $res = (is_array($key)) ? openssl_get_privatekey($key['key'], $pass) :
                            openssl_get_privatekey($key, $pass);
  openssl_private_decrypt($this->convertBin($crypt), $output, $res);
  return (($_SERVER["HTTP_X_REQUESTED_WITH"]==='XMLHttpRequest')&&($flag===0)) ?
          base64_decode($output) : $output;
 }
 public function aesEnc($data, $password, $iv='', $cipher='aes-256-cbc', $raw=false)
 {
  return openssl_encrypt($data, $cipher, $password, $raw, $iv);
 }
 public function aesDenc($data, $password, $iv='', $cipher='aes-256-cbc', $raw=false)
 {
  return openssl_decrypt($data, $cipher, $password, $raw, $iv);
 }
 private function convertHex($key)
 {
  return bin2hex($key);
 }
 private function convertBin($key)
 {
  $data = '';
  $len = strlen($key);
  for ($i=0;$i<$len;$i+=2) {
   $data .= pack("C",hexdec(substr($key,$i,2)));
  }
  return $data;
 }
 private function __destruct()
 {
  openssl_free_key($this->handle);
  unset($this->instance);
 }
}
?>
