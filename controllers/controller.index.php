<?php

class indexController
{

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;
 public function __construct($registry)
 {
  $this->registry = $registry;
 }

 public function index()
 {
  $this->registry->tpl = new templates();
  $this->registry->tpl->strTemplateDir = 'views/default';
  $this->registry->tpl->boolCache=true;
  $this->registry->tpl->assign('title', 'app', NULL, NULL);
  $this->registry->tpl->assign('timeout', 3600, NULL, NULL);
  $this->registry->tpl->assign('templates', $this->registry->tpl->strTemplateDir, NULL, NULL);
  $this->registry->tpl->display('header.tpl', true, NULL, $this->registry->libs->_getRealIPv4());

  $this->registry->tpl->display('footer.tpl', true, NULL, $this->registry->libs->_getRealIPv4());
 }
}
?>