<?php
/**
 * Handle default page views
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   views
 * @discussion Handles default page views
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class indexController
 *  @abstract Handles default page views
 */
class indexController
{

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  *! @function __construct
  *  @abstract Class loader
  *  @param registry array - Global array of class objects
  */
 public function __construct($registry)
 {
  $this->registry = $registry;
 }

 /**
  *! @function index
  *  @abstract Calls default view
  */
 public function index()
 {
  if (file_exists('views/view.index.php')){
   require 'views/view.index.php';
  }
  indexView::instance($this->registry);
 }
}
?>