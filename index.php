<?php
/* define application path */
define('__SITE', realpath(dirname(__FILE__)));

/* load our class initialization object */
if (!file_exists(__SITE.'/controllers/controller.initialization.php')){
 exit('Necessary controller missing, unable to proceed. 0x0c0');
}
include __SITE.'/controllers/controller.initialization.php';

/* load the router via the registry */
$registry->router = new router($registry);

/* route requests through controllers */
$registry->router->setPath(__SITE.'/controllers');

/* intialize the libraries */
$registry->libs = libraries::init();

/* load the template via the registry */
$registry->template = new template($registry);

/* begin routing requests */
$registry->router->loader();

?>
