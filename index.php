<?php

/* define application path */
define('__SITE', realpath(dirname(__FILE__)));

/* first load the application config */
if (!file_exists(__SITE.'/config/configuration.php')){
 exit('Necessary configuration missing, unable to proceed.');
}
include __SITE.'/config/configuration.php';

/* verify settings, or call the installer */
if (!is_array($settings)){
	exit('Necessary configuration settings missing.');
}

/* verify settings exist */
if ((empty($settings['db']['hostname']))&&(empty($settings['db']['username']))&&(empty($settings['db']['password']))&&(empty($settings['db']['database']))){
	exit('Necessary configuration options missing.');
}

/* execute autoload */
if (!file_exists(__SITE.'/models/model.autoloader.php')){
	exit('Error loading autoloader class, unable to proceed. 0x0c2');
}
include __SITE.'/models/model.autoloader.php';

/* load the registry class */
if (!class_exists('autoloader')){
	exit('Error initializing autoloader class, unable to proceed. 0x0c3');
}
new autoloader('models/');

/* load the registry class */
if (!class_exists('registry')){
	exit('Error initializing registry class, unable to proceed. 0x0c4');
}
$registry = new registry;

/* intialize the libraries */
if (!class_exists('libraries')){
	exit('Error initializing libraries class, unable to proceed. 0x0c5');
}
$registry->libs = libraries::init();

/* load up configured database driver */
$eng = $registry->libs->_dbEngine($settings['db']['engine']);
if (!class_exists($eng)){
	exit('Error loading configured database class, unable to proceed. 0x0c6');
}
$registry->db = new $eng($settings['db']);

/* prepare the secret key */
$settings['sessions']['db-key'] = $registry->libs->_hash($settings['sessions']['db-key'], $registry->libs->_salt($settings['sessions']['db-key'], 2048));

/* query for application settings */

/* load and start up session support */
if (!class_exists('dbSession')){
	exit('Error loading database session support, unable to proceed. 0x0c6');
}

/* create new instance of sessions? */
$registry->sessions = dbSession::instance($settings['sessions'], $registry);

/* generate or use CSRF token */
if (!isset($_SESSION[$registry->libs->_getRealIPv4()]['csrf'])) {
	$_SESSION[$registry->libs->_getRealIPv4()]['csrf'] = $registry->libs->uuid();
}

/* always reset the session_id */
$registry->sessions->regen(true);

/* Set application defaults within registry */
$settings['opts']['dbKey'] = $settings['sessions']['db-key'];
$registry->opts = $settings['opts'];

/* start logging access */
if (!class_exists('logging')){
	exit('Error initializing logging class, unable to proceed. 0x0c7');
}
new logging($registry);

/* load up access filtering */
if (!class_exists('access')){
	exit('Error initializing access class, unable to proceed. 0x0c8');
}

/* apply some security headers for clients */
header('X-Alt-Referer: '.$_SESSION[$registry->libs->_getRealIPv4()]['csrf']);
header('X-Forwarded-Proto: http');
header('X-Frame-Options: deny');
header('X-XSS-Protecton: 1;mode=deny');

/* set headers when $_OPTIONS asks for specific access controls */
/* this should also do a comparision against whitelist of allowed referal applications */
if (!empty($_SERVER['HTTP_ORIGIN'])) {
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: http://rapp.dev:8080');
	header('Access-Control-Allow-Methods: POST');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer, X-Requested-With');
	header("Content-Type: application/json");
}

/* perform check against ACL to visitor */
$access = new access($registry);
if (!$access->_do()){
	exit('Error due to access restrictions. 0x0c9');
}

/* load the router via the registry */
$registry->router = new router($registry);

/* route requests through controllers */
$registry->router->setPath(__SITE.'/controllers');

/* load the template via the registry */
$registry->template = new template($registry);

/* begin routing requests */
$registry->router->loader();

?>
