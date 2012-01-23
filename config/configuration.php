<?php

/* Database configuration settings */
$settings['db']['engine']     = 'mysql';
$settings['db']['hostname']   = 'localhost';
$settings['db']['username']   = 'licensing';
$settings['db']['password']   = 'd3v3l0pm3n+';
$settings['db']['database']   = 'licensing';

/* Application specific settings */
$settings['opts']['title']    = 'Marriott Library - Software Licensing';
$settings['opts']['timeout']  = 3600;
$settings['opts']['template'] = 'views/default';
$settings['opts']['caching']  = 'cache';

/*
 * Site wide random salt (WARNING)
 * This random value gets generated upon initial framework
 * installation. Various portions of the encryption
 * features rely on this key. If you feel this key has been
 * compromised you must use the install/repair.php utility
 * which will first decrypt each database table then generate
 * a new random site key and re-encrypt and store the
 * database contents. If you change this manually you will
 * loose everything in the database.
 */
$settings['opts']['db-key']   = 't3AhaJ34aYAd92UWuWrEVephefR79Ec2c6U2e7aPrAwrepaF4caMEFeCR7B5asP9';

?>
