<?php

namespace Drupal\cdp_profile\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Password\PasswordInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a user password reset form.
 */
class ChangePasswordForm extends ContentEntityForm {

  /**
   * The Password Hasher.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $passwordHasher;

  /**
   * Constructs a UserPasswordForm object.
   *
   * @param \Drupal\Core\Password\PasswordInterface $password_hasher
   *   The password service.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(PasswordInterface $password_hasher, EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->passwordHasher = $password_hasher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('password'),
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'change_password_form';
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param \Drupal\user\UserInterface $user
   *   The user object.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $account = User::load($this->currentUser()->id());
    $user = $this->currentUser();
    $config = \Drupal::config('user.settings');
    $form['#cache']['tags'] = $config->getCacheTags();
    $form['account'] = [
      '#type'   => 'container',
      '#weight' => -10,
    ];
    if ($user) {
      $form['account']['pass'] = [
        '#type' => 'password_confirm',
        '#size' => 25,
        '#description' => $this->t('To change the current user password, enter the new password in both fields.'),
        '#required' => TRUE,
      ];
      if ($user->id() != NULL) {
        $form['account']['current_pass'] = [
          '#type' => 'password',
          '#title' => $this->t('Current password'),
          '#size' => 25,
          '#access' => TRUE,
          '#weight' => -5,
          // Do not let web browsers remember this password, since we are
          // trying to confirm that the person submitting the form actually
          // knows the current one.
          '#attributes' => ['autocomplete' => 'off'],
          '#required' => TRUE,
        ];
        $form_state->set('user', $account);
      }
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = ['#type' => 'submit', '#value' => $this->t('Submit')];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $current_pass_input = trim($form_state->getValue('current_pass'));
    if ($current_pass_input) {
      $user = User::load(\Drupal::currentUser()->id());
      if (!$this->passwordHasher->check($current_pass_input, $user->getPassword())) {
        $form_state->setErrorByName('current_pass', $this->t('The current password you provided is incorrect.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = User::load($this->currentUser()->id());
    $user->setPassword($form_state->getValue('pass'));
    $user->save();
    $this->messenger()->addStatus($this->t('Your password has been changed.'));
  }

}
