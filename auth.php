<?php
/**
 * @var $sso string SSO server
 */
$sso = 'http://sso.dev/?nxs=proxy/remote';

/**
 * @var $uid string Unique CSRF token
 */
$uid = uuid();

/**
 * @array $opt Array of header options
 */
$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$opt = array('Origin: '.$proto.$_SERVER['HTTP_HOST'],
			 'X-Requested-With: XMLHttpRequest',
			 'X-Alt-Referer: '.$uid,
			 'Content-MD5: '.base64_encode(md5($uid)));

session_start();
if (!empty($_GET['a'])){
 $_SESSION['token']=$_GET['a'];
 header('Location: '.$_SERVER['HTTP_REFERER']);
}
if (!empty($_SESSION['token'])){
 $m='<div class="success">Re-authentication successful</div>';
}
session_regenerate_id(true);

_do($sso, $uid, $opt);

/**
 * @function _do
 * @abstract Perform page request
 * @var $sso string SSO server argument
 * @var $uid string UUID for CSRF validation
 * @var $opt array Array of header options for page request
 */
function _do($sso, $uid, $opt)
{
	$h=curl_init();
	curl_setopt($h, CURLOPT_URL, $sso);
	curl_setopt($h, CURLOPT_HEADER, false);
	curl_setopt($h, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($h, CURLOPT_REFERER, $sso);
	curl_setopt($h, CURLOPT_HTTPHEADER, $opt);
	$r=curl_exec($h);
	curl_close($h);
}

/**
 * @function _uuid
 * @abstract Generates a random GUID
 */
function uuid()
{
	return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff),
					mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000,
					mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff),
					mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

?>
