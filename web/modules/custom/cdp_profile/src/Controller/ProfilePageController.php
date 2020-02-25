<?php

namespace Drupal\cdp_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\user\UserStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Routing\AccessAwareRouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;


/**
 * Class ProfilePageController.
 *
 * @package Drupal\cdp_profile\Controller
 */
class ProfilePageController extends ControllerBase {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The http kernel interface.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * The access aware router interface.
   *
   * @var \Drupal\Core\Routing\AccessAwareRouterInterface
   */
  protected $router;

  /**
   * Entity form builder interface.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface
   */
  protected $entityFormBuilder;

  /**
   * User storage interface.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * MainController constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The http kernel interface.
   * @param \Drupal\Core\Routing\AccessAwareRouterInterface $router
   *   The access aware router interface.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   Entity form builder interface.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   User storage interface.
   */
  public function __construct(RequestStack $request_stack, HttpKernelInterface $http_kernel, AccessAwareRouterInterface $router, EntityFormBuilderInterface $entity_form_builder, UserStorageInterface $user_storage) {
    $this->requestStack      = $request_stack;
    $this->httpKernel        = $http_kernel;
    $this->router            = $router;
    $this->entityFormBuilder = $entity_form_builder;
    $this->userStorage       = $user_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('http_kernel'),
      $container->get('router'),
      $container->get('entity.form_builder'),
      $container->get('entity_type.manager')->getStorage('user')
    );
  }

  /**
   * Profile frontpage.
   *
   * @return array
   *   Return Profile page.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function profileFront() {
    $entityType  = 'user';
    $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entityType);
    $entity      = $this->entityTypeManager()->getStorage($entityType)->load(1);
    $developer   = $this->userStorage->create();
    $passform    = $this->entityFormBuilder->getForm($developer, 'pass_change');
    $detailsform = $this->entityFormBuilder->getForm($developer, 'details_change');


    return [
      $viewBuilder->view($entity, 'profilepage'),
      $passform,
      $detailsform,
    ];

  }

}
