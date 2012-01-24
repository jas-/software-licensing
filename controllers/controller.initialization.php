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

/* generate or use CSRF token */
$settings['opts']['token'] = (!empty($_SESSION['csrf'])) ?
                              $_SESSION['csrf'] : $registry->libs->uuid();

/* Set application defaults within registry */
$registry->opts = $settings['opts'];

/* begin logging */

/* query for application settings */

/* perform access list evaluation */

/* evaluate authentication status */

/* initialize key exchange */

/* apply customized headers */
header('X-Alt-Referer: '.$settings['opts']['token']);
header('X-Forwarded-Proto: http');
header('X-Frame-Options: deny');
header('X-XSS-Protecton: 1;mode=deny');

?>
