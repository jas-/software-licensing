<?php
/**
 * Handle networking functions
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   utilities
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class networking
{
 protected static $instance;
 private $handle;
 private function __construct($configuration)
 {
  return;
 }
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }
 public function dnsrecordtype($hostname, $type='A')
 {
  return checkdnsrr($hostname, $type);
 }
 public function dnsrecord($hostname, $type=DNS_ALL, $opt=NUL, $ext=NULL)
 {
  return dns_get_record($hostname, $type, $opt, $ext);
 }
 public function ip2hostname($ip)
 {
  return gethostbyaddr($ip);
 }
 public function hostname2ip($hostname)
 {
  return gethostbyname($hostname);
 }
 public function hostname2iparray($hostname)
 {
  return gethostbynamel($hostname);
 }
 public function vnetmask($netmask)
 {
  return cidr::validNetMask($netmask);
 }
 public function ccidr($netmask)
 {
  return cidr::maskToCIDR($netmask);
 }
 public function returncidr($ip, $netmask)
 {
  return cidr::alignedCIDR($ip, $netmask);
 }
 public function ipinrange($ip, $cidr)
 {
  return cidr::IpisWithinCIDR($ip, $cidr);
 }
 public function cidrrange($start, $end)
 {
  return cidr::rangeToCIDRList($start, $end);
 }
 private function __destruct()
 {
  if (isset($this->handle)) {
   unset($this->handle);
  }
  return;
 }
}
/**
 * CIDR.php
 *
 * Utility Functions for IPv4 ip addresses.
 *
 * @author Jonavon Wilcox <jowilcox@vt.edu>
 * @version Sat Jun  6 21:26:48 EDT 2009
 * @copyright Copyright (c) 2009 Jonavon Wilcox
 */
 /**
  * class CIDR.
  * Holds static functions for ip address manipulation.
  */
class cidr extends networking
{
	/**
	 * method CIDRtoMask
	 * Return a netmask string if given an integer between 0 and 32. I am 
	 * not sure how this works on 64 bit machines.
	 * Usage:
	 *     CIDR::CIDRtoMask(22);
	 * Result:
	 *     string(13) "255.255.252.0"
	 * @param $int int Between 0 and 32.
	 * @access public
	 * @static
	 * @return String Netmask ip address
	 */
	public static function CIDRtoMask($int) {
		return long2ip(-1 << (32 - (int)$int));
	}

	/**
	 * method countSetBits.
	 * Return the number of bits that are set in an integer.
	 * Usage:
	 *     CIDR::countSetBits(ip2long('255.255.252.0'));
	 * Result:
	 *     int(22)
	 * @param $int int a number
	 * @access public
	 * @static
	 * @see http://stackoverflow.com/questions/109023/best-algorithm-to-co\
	 * unt-the-number-of-set-bits-in-a-32-bit-integer
	 * @return int number of bits set.
	 */
	public static function countSetbits($int){
		$int = $int - (($int >> 1) & 0x55555555);
		$int = ($int & 0x33333333) + (($int >> 2) & 0x33333333);
		return (($int + ($int >> 4) & 0xF0F0F0F) * 0x1010101) >> 24;
	}
	
	/**
	 * method validNetMask.
	 * Determine if a string is a valid netmask.
	 * Usage:
	 *     CIDR::validNetMask('255.255.252.0');
	 *     CIDR::validNetMask('127.0.0.1');
	 * Result:
	 *     bool(true)
	 *     bool(false)
	 * @param $netmask String a 1pv4 formatted ip address.
	 * @see http://www.actionsnip.com/snippets/tomo_atlacatl/calculate-if-\
	 * a-netmask-is-valid--as2-
	 * @access public
	 * @static
	 * return bool True if a valid netmask.
	 */
	public static function validNetMask($netmask) {
		$netmask = ip2long($netmask);
		$neg = ((~(int)$netmask) & 0xFFFFFFFF);
		return (($neg + 1) & $neg) === 0;
	}

	/**
	 * method maskToCIDR.
	 * Return a CIDR block number when given a valid netmask.
	 * Usage:
	 *     CIDR::maskToCIDR('255.255.252.0');
	 * Result:
	 *     int(22)
	 * @param $netmask String a 1pv4 formatted ip address.
	 * @access public
	 * @static
	 * @return int CIDR number.
	 */
	public static function maskToCIDR($netmask){
		if(self::validNetMask($netmask)){
			return self::countSetBits(ip2long($netmask));
		}
	}

	/**
	 * method alignedCIDR.
	 * It takes an ip address and a netmask and returns a valid CIDR
	 * block.
	 * Usage:
	 *     CIDR::alignedCIDR('127.0.0.1','255.255.252.0');
	 * Result:
	 *     string(12) "127.0.0.0/22"
	 * @param $ipinput String a IPv4 formatted ip address.
	 * @param $netmask String a 1pv4 formatted ip address.
	 * @access public
	 * @static
	 * @return String CIDR block.
	 */
	public static function alignedCIDR($ipinput,$netmask){
		$alignedIP = long2ip((ip2long($ipinput)) & (ip2long($netmask)));
		return "$alignedIP/" . self::maskToCIDR($netmask);
	}

	/**
	 * method IPisWithinCIDR.
	 * Check wether an IP is within a CIDR block.
	 * Usage:
	 *     CIDR::IPisWithinCIDR('127.0.0.33','127.0.0.1/24');
	 *     CIDR::IPisWithinCIDR('127.0.0.33','127.0.0.1/27');
	 * Result: 
	 *     bool(true)
	 *     bool(false)
	 * @param $ipinput String a IPv4 formatted ip address.
	 * @param $cidr String a IPv4 formatted CIDR block. Block is aligned
	 * during execution.
	 * @access public
	 * @static
	 * @return String CIDR block.
	 */
	public static function IPisWithinCIDR($ipinput,$cidr){
		$cidr = explode('/',$cidr);
		$cidr = self::alignedCIDR($cidr[0],self::CIDRtoMask((int)$cidr[1]));
		$cidr = explode('/',$cidr);
		$ipinput = (ip2long($ipinput));
		$ip1 = (ip2long($cidr[0]));
		$ip2 = ($ip1 + pow(2, (32 - (int)$cidr[1])) - 1);
		return (($ip1 <= $ipinput) && ($ipinput <= $ip2));
	}

	/**
	 * method maxBlock.
	 * Determines the largest CIDR block that an IP address will fit into.
	 * Used to develop a list of CIDR blocks.
	 * Usage:
	 *     CIDR::maxBlock("127.0.0.1");
	 *     CIDR::maxBlock("127.0.0.0");
	 * Result:
	 *     int(32)
	 *     int(8)
	 * @param $ipinput String a IPv4 formatted ip address.
	 * @access public
	 * @static
	 * @return int CIDR number.
	 */
	public static function maxBlock($ipinput) {
		return self::maskToCIDR(long2ip(-(ip2long($ipinput) & -(ip2long($ipinput)))));
	}
	
	/**
	 * method rangeToCIDRList.
	 * Returns an array of CIDR blocks that fit into a specified range of
	 * ip addresses.
	 * Usage:
	 *     CIDR::rangeToCIDRList("127.0.0.1","127.0.0.34");
	 * Result:
	 *     array(7) { 
	 *       [0]=> string(12) "127.0.0.1/32"
	 *       [1]=> string(12) "127.0.0.2/31"
	 *       [2]=> string(12) "127.0.0.4/30"
	 *       [3]=> string(12) "127.0.0.8/29"
	 *       [4]=> string(13) "127.0.0.16/28"
	 *       [5]=> string(13) "127.0.0.32/31"
	 *       [6]=> string(13) "127.0.0.34/32"
	 *     }
	 * @param $startIPinput String a IPv4 formatted ip address.
	 * @param $startIPinput String a IPv4 formatted ip address.
	 * @see http://null.pp.ru/src/php/Netmask.phps
	 * @return Array CIDR blocks in a numbered array.
	 */
	public static function rangeToCIDRList($startIPinput,$endIPinput) {
		$start = ip2long($startIPinput);
		$end = ip2long($endIPinput);
		while($end >= $start) {
			$maxsize = self::maxBlock(long2ip($start));
			$maxdiff = 32 - intval(log($end - $start + 1)/log(2));
			$size = ($maxsize > $maxdiff)?$maxsize:$maxdiff;
			$listCIDRs[] = long2ip($start) . "/$size";
			$start += pow(2, (32 - $size));
		}
		return $listCIDRs;
	}
}
?>
