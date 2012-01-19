<?php

/* Database configuration settings */
$settings['db']['engine']   = 'mysql';
$settings['db']['hostname'] = 'localhost';
$settings['db']['username'] = 'licensing2012';
$settings['db']['password'] = 'd3v3l0pm3n+';
$settings['db']['database'] = 'licensing2012';

/* Authenticated sessions timeout */
$settings['opts']['timeout'] = 3600;

/* Default theme template location */
$settings['opts']['template'] = 'default';

/* Default caching directory location */
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
$settings['opt']['db-key'] = 'my$1+3-KeY';

?>
