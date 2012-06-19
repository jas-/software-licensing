<?php
/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handles hashing
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   phpMyLibraries
 * @discussion Handles hashing
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class hashes
 *  @abstract Handles hashing
 */
class hashes
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
	 *! @function __construct
	 *  @abstract Class loader
	 *  @param registry array - Global array of class objects
	 */
	public function __construct($registry)
	{
		$this->registry = $registry;
	}

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
	 * @function _salt
	 * @abstract Generate a random salt value of specified length based on input
	 */
	public function _salt($string, $len=null)
	{
		return (!empty($len)) ? hash('sha512', str_pad($string, (strlen($string) + $len), substr(hash('sha512', $string), @round((float)strlen($string)/3, 0, PHP_ROUND_HALF_UP), ($len - strlen($string))), STR_PAD_BOTH)) : hash('sha512', substr($string, @round((float)strlen($string)/3, 0, PHP_ROUND_HALF_UP), 16));
	}

	/**
	 * @function _hash
	 * @abstract Mimic bcrypt hasing functionality
	 */
	public function _hash($string, $salt=null)
	{
		return (CRYPT_BLOWFISH===1) ? (!empty($salt)) ? crypt($string, "\$2a\$07\$".substr($salt, 0, CRYPT_SALT_LENGTH)) : crypt($string, $this->_salt("\$2a\$07\$".substr($string, 0, CRYPT_SALT_LENGTH))) : false;
	}

	/**
	 * @function _pbkdf2
	 * @abstract Creates hash using abstract pbkdf2
	 */
	public function _pbkdf2($p, $s, $c, $kl, $a='sha512')
	{
		$h = false;
		if (!function_exists('hash_pbkdf2')) {
		    $hl = strlen(hash($a, null, true));
		    $kb = ceil($kl / $hl);
		    $dk = '';
		    for ( $block = 1; $block <= $kb; $block ++ ) {
		        $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
		        for ( $i = 1; $i < $c; $i ++ )
		            $ib ^= ($b = hash_hmac($a, $b, $p, true));
		        $dk .= $ib;
		    }
		    $h = substr($dk, 0, $kl);
		} else {
			$h = hash_pkdf2($a, $p, $s, $c, $kl);
		}
		return $h;
	}
}
