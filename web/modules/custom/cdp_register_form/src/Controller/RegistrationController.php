<?php

namespace Drupal\cdp_register_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\user\UserStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegistrationController extends ControllerBase {

  protected $entityFormBuilder;

  protected $userStorage;

  public function __construct(EntityFormBuilderInterface $entity_form_builder, UserStorageInterface $user_storage) {
    $this->entityFormBuilder = $entity_form_builder;
    $this->userStorage = $user_storage;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.form_builder'),
      $container->get('entity_type.manager')->getStorage('user')
    );
  }

  public function registerTechleadForm() {
    $developer = $this->userStorage->create();
    $content['registration_techlead_form'] = $this->entityFormBuilder->getForm($developer, 'registration_techlead',['role' => 'techlead']);

    return $content;
  }
  public function registerDeveloperForm() {
    $developer = $this->userStorage->create();
    $content['registration_developers_form'] = $this->entityFormBuilder->getForm($developer, 'registration_developers',['role' => 'developer']);
    return $content;
  }

}
