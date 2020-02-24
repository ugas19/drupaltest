<?php

namespace Drupal\cdp_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Field\FieldItemListInterface;
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
   * MainController constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The http kernel interface.
   * @param \Drupal\Core\Routing\AccessAwareRouterInterface $router
   *   The access aware router interface.
   */
  public function __construct(RequestStack $request_stack, HttpKernelInterface $http_kernel, AccessAwareRouterInterface $router) {
    $this->requestStack = $request_stack;
    $this->httpKernel   = $http_kernel;
    $this->router       = $router;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('http_kernel'),
      $container->get('router')
    );
  }

  public function FormBuilder(){
    
  }
  /**
   * Profile frontpage.
   *

   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function profileFront() {
    $form_class = '\Drupal\cdp_profile\Form\TestForm';
    $builderform = \Drupal::formBuilder()->getForm($form_class);
    $entityType = 'user';
    //    $display = entity_get_display($entityType, $node->getType(), $viewmode);
    $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entityType);
    $entity     = $this->entityTypeManager()->getStorage($entityType)->load(1);
    $entity_url = $entity->toUrl();
    $viewBuilder->view($entity)['#view_mode'] = 'user.profilepage';
//    dump($viewBuilder->view($entity)['#view_mode']);
    $viewBuilder->view($entity)['#view_mode'] = 'user.profilepage';

//    $viewBuilder->viewField($viewBuilder);


//    $build['form'] = \Drupal::formBuilder()->getForm($form_class);
    $build['user'][1]['#view_mode'] = 'user.profilepage';
    return $build;
    $request     = $this->requestStack->getCurrentRequest();
    $sub_request = clone $request;
    $sub_request->attributes->add($this->router->match('/' . $entity_url->getInternalPath()));
//    if()
    $sub_request->attributes->add(['view_mode' => 'user.profilepage']);
    $sub_request->attributes->add(['_form' => $builderform]);
    dump($sub_request);
    $response = $this->httpKernel->handle($sub_request, HttpKernelInterface::SUB_REQUEST);
    dump($response);
    return $response;
    return [$builderform,
      $response];


  }
}
