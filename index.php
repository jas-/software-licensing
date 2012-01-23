<?php
/* define application path */
define('__SITE', realpath(dirname(__FILE__)));

/* load our class initialization object */
if (!file_exists(__SITE.'/controllers/controller.initialization.php')){
 exit('Necessary controller missing, unable to proceed. 0x0c0');
}
include __SITE.'/controllers/controller.initialization.php';

/* intialize the libraries */
$registry->libs = libraries::init();

/* generate or use CSRF token */
$token = (!empty($_SESSION['csrf'])) ? $_SESSION['csrf'] : $registry->libs->uuid();

/* apply customized headers */
header('X-Alt-Referer: '.$token);
header('X-Forwarded-Proto: http');
header('X-Frame-Options: deny');
header('X-XSS-Protecton: 1;mode=deny');

/* load the router via the registry */
$registry->router = new router($registry);

/* route requests through controllers */
$registry->router->setPath(__SITE.'/controllers');

/* load the template via the registry */
$registry->template = new template($registry);

/* begin routing requests */
$registry->router->loader();

?>
