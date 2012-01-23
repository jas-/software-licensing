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
  if (file_exists('views/view.index.php')){
   require 'views/view.index.php';
  }
  $index = new indexView($this->registry);
  $index->index();
 }
}
?>