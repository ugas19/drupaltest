<?php
namespace Drupal\cdp_profile\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\HttpKernelInterface;



class ProfileEventSubscriber implements EventSubscriberInterface {

  /**
   * The user account.
   *
   * @var \Drupal\user\UserInterface
   */
  public $account;

  public function __construct(AccountProxyInterface $account_proxy) {
    $this->account = $account_proxy;
  }
  /**
   * Alters the controller output.
   */
  public function onProfile(GetResponseEvent $event) {
    if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
      return;
    }

    $current_path = $event->getRequest()->getPathInfo();


    if ($this->account->id() != NULL) {
      if ($current_path === '/user/' . $this->account->id()) {
        $profile_page_object = Url::fromRoute('cdp_profile.page');
        $profile_page_url = $profile_page_object->toString();
        $response = new RedirectResponse($profile_page_url);
        $response->send();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
//    $events[KernelEvents::REQUEST][] = ['onProfile', 50];
    return NULL;
  }

}