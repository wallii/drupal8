<?php
/**
 * @file
 * @author: Waliullah Khan
 * Contains \Drupal\custom_events\EventSubscriber\ConfigEventsSubscriber.
 * Please place this file under your custom_events(module_root_folder)/src/EventSubscriber/
 */
namespace Drupal\custom_events\EventSubscriber;

use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/**
 * Class EntityTypeSubscriber.
 *
 * @package Drupal\custom_events\EventSubscriber
**/

class ConfigEventsSubscriber implements EventSubscriberInterface {
	/**
		* {@inheritdoc}
		*
		* @return array
		*   The event names to listen for, and the methods that should be executed.
	**/
	public static function getSubscribedEvents() {
		return [
		ConfigEvents::SAVE => 'configSave',
		ConfigEvents::DELETE => 'configDelete',
		];
	}
	/**
		* React to a config object being saved.
		*
		* @param \Drupal\Core\Config\ConfigCrudEvent $event
		*   Config crud event.
	**/
	public function configSave(ConfigCrudEvent $event) {
		$config = $event->getConfig();
		drupal_set_message('Saved config: ' . $config->getName());
	}
	/**
		* React to a config object being deleted.
		*
		* @param \Drupal\Core\Config\ConfigCrudEvent $event
		*   Config crud event.
	**/
	public function configDelete(ConfigCrudEvent $event) {
		$config = $event->getConfig();
		drupal_set_message('Deleted config: ' . $config->getName());
	}


}
?>