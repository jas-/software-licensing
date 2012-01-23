<?php

class indexView
{

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;
 public function __construct($registry)
 {
  $this->registry = $registry;
  $this->registry->tpl = new templates();
  $this->registry->tpl->strTemplateDir = $this->registry->opts['template'];
  $this->registry->tpl->boolCache=true;
 }

 public function index()
 {
  $this->_header();
  $this->_main();
  $this->_footer();
 }

 private function _header()
 {
  $this->registry->tpl->assign('title',
                               $this->registry->opts['title'], NULL, NULL);
  $this->registry->tpl->assign('timeout',
                               $this->registry->opts['timeout'], NULL, NULL);
  $this->registry->tpl->assign('templates',
                               $this->registry->tpl->strTemplateDir, NULL, NULL);
  $this->registry->tpl->display('header.tpl', true, NULL,
                                $this->registry->libs->_getRealIPv4());
 }

 private function _main()
 {
  $this->__main();
  $this->_menu();
  $this->_login();
  $this->registry->tpl->display('index.tpl', true, NULL, $this->registry->libs->_getRealIPv4());
  
 }

 private function _footer()
 {
  $this->registry->tpl->display('footer.tpl', true, NULL, $this->registry->libs->_getRealIPv4());
 }

 private function __main()
 {
  $this->registry->tpl->assign('main', $this->registry->tpl->assign(NULL, NULL, 'main.tpl', true), NULL);
 }

 private function _login()
 {
  $this->registry->tpl->assign('login', $this->registry->tpl->assign(NULL, NULL, 'login.tpl', true), NULL);
 }

 private function _menu()
 {
  $this->registry->tpl->assign('menu', $this->registry->tpl->assign(NULL, NULL, 'menu.tpl', true), NULL);
 }
}
?>