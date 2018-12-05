<?php
namespace Drupal\custom_eventsubscriber\EventSubscriber;
 
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Url;
/**
 * Redirect .html pages to corresponding Node page.
 */
 class CustomredirectSubscriber implements EventSubscriberInterface {
 
  /** @var int */
  private $redirectCode = 301;
 
  /**
   * Redirect pattern based url
   * @param GetResponseEvent $event
   */
  public function customRedirection(GetResponseEvent $event) {
    //echo "hello";
	//die;
    $request = \Drupal::request();
    $requestUrl = $request->server->get('REQUEST_URI', null);
    //echo $requestUrl;
	//die;
    /**
     * Here i am redirecting the about-us.html to respective /about-us node.
     * Here you can implement your logic and search the URL in the DB
     * and redirect them on the respective node.
     */
    if ($requestUrl=='/drupal-8/about-us.html') {
        $response = new RedirectResponse('/drupal-8/node/1', $this->redirectCode);
        $response->send();
        exit(0);
    }
  }
 
  /**
   * Listen to kernel.request events and call customRedirection.
   * {@inheritdoc}
   * @return array Event names to listen to (key) and methods to call (value)
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('customRedirection');
    return $events;
  }
}
?>