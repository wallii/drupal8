<?php
/**
 * @file
 * @author Waliullah Khan
 * Contains \Drupal\demo\Controller\DemoController.
 * Please place this file under your demo(module_root_folder)/src/Controller/
 */
namespace Drupal\demo\Controller;
/**
 * Provides route responses for the Example module.
 */
class BasicConfiguration {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function configuration() {
	echo "Hello";
	die;
	
  }
}
?>