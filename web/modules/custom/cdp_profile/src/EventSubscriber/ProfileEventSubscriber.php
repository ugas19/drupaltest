<?php
namespace Drupal\cdp_profile\EventSubscriber;

use Drupal\Core\Routing\RouteProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ProfileEventSubscriber.
 *
 * @package Drupal\cdp_profile\EventSubscriber
 */
class ProfileEventSubscriber implements EventSubscriberInterface {

  /**
   * The user account.
   *
   * @var \Drupal\user\UserInterface
   */
  public $account;

  protected $routes;

  /**
   * ProfileEventSubscriber constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account_proxy
   *   Gets passed current account.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route
   *   Gets passed route provider.
   */
  public function __construct(AccountProxyInterface $account_proxy, RouteProviderInterface $route) {
    $this->account = $account_proxy;
    $this->routes = $route;
  }

  /**
   * Alters the controller output.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Gets passed event.
   */
  public function onProfile(GetResponseEvent $event) {
    if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
      return;
    }
    $current_path = $event->getRequest()->getPathInfo();
    $roles = $this->account->getAccount()->getRoles();
    if ($this->account->id() != NULL) {
      if ($current_path === '/user/' . $this->account->id()) {
        if ($this->routes->getRoutesByNames(['cdp_profile.developer_page']) && in_array('authenticated', $roles)) {
          $profile_page_object = Url::fromRoute('cdp_profile.developer_page');
          $profile_page_url = $profile_page_object->toString();
          $response = new RedirectResponse($profile_page_url);
          $response->send();

        }
        elseif ($this->routes->getRoutesByNames(['cdp_profile.techlead_page']) && in_array('authenticated', $roles)) {
          $profile_page_object = Url::fromRoute('cdp_profile.techlead_page');
          $profile_page_url = $profile_page_object->toString();
          $response = new RedirectResponse($profile_page_url);
          $response->send();
        }
        else {
          return;
        }

      }
    }
  }

  /**
   * Get subscribed events.
   *
   * @return array
   *   Returns request event.
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['onProfile', 50];
    return $events;
  }

}