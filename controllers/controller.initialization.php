<?php

/* first load the application config */
if (!file_exists(__SITE.'/config/configuration.php')){
 exit('Necessary configuration missing, unable to proceed. 0x0c1');
}
include __SITE.'/config/configuration.php';

/* verify settings, or call the installer */
if (!is_array($settings)){
 exit('Installer has not been built');
}

/* verify settings exist */
if ((empty($settings['db']['hostname']))&&
    (empty($settings['db']['username']))&&
    (empty($settings['db']['password']))&&
    (empty($settings['db']['database']))){
 exit('Installer has not been built, else call the installer');
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
autoloader::instance('models/');

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
$registry->db = $eng::instance($settings['db']);

/* load and start up session support */
if (!class_exists('dbSession')){
 exit('Error loading database session support, unable to proceed. 0x0c6');
}
$registry->sessions = dbSession::instance($settings['sessions'], $registry->db);
if ((empty($_SESSION['token']))||(empty($_SESSION['csrf']))){
 session_start();
}
echo '<pre>';  print_r($_SESSION); echo '</pre>';

/* query for application settings */

/* generate or use CSRF token */
$settings['opts']['token'] = (!empty($_SESSION['csrf'])) ?
                              $_SESSION['csrf'] : $registry->libs->uuid();

/* Set application defaults within registry */
$registry->opts = $settings['opts'];

if (!class_exists('logging')){
 exit('Error initializing logging class, unable to proceed. 0x0c7');
}
logging::init($registry);

if (!class_exists('access')){
 exit('Error initializing access class, unable to proceed. 0x0c8');
}
$registry->access = access::init($registry);
if (!$registry->access->_do()){
 exit('Error due to access restrictions');
}

/* apply customized headers */
header('X-Alt-Referer: '.$settings['opts']['token']);
header('X-Forwarded-Proto: http');
header('X-Frame-Options: deny');
header('X-XSS-Protecton: 1;mode=deny');

?>
