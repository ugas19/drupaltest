<?php

namespace Drupal\cdp_register_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CdpDeveloperRegisterForm extends FormBase {

  protected $userStorage;

  public function __construct(UserStorageInterface $user_storage) {
    var_dump("kreys");
    $this->userStorage = $user_storage;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('user'),
    );
  }

  public function getFormId() {
    return 'cdp_developer_register_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
//    $form['name'] = [
//      '#type' => 'textfield',
//      '#title' => $this->t('Username'),
//      '#description' => $this->t('New user name.'),
//      '#required' => TRUE,
//    ];

    $form['mail'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('New user email.'),
      '#required' => TRUE,
    ];

//    $form['#validate'][] = '::validateName';
    $form['#validate'][] = [$this, 'validateMail'];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = ['#type' => 'submit', '#value' => $this->t('Custom user register')];

    return $form;
  }

  public function validateName(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');

    $users = $this->userStorage->loadByProperties([
      'name' => $name,
    ]);

    if (count($users) !== 0) {
      $form_state->setError($form['name'], $this->t('User name taken.'));
    }
  }

  public function validateMail(array &$form, FormStateInterface $form_state) {
    $mail = trim($form_state->getValue('mail'));

    $users = $this->userStorage->loadByProperties([
      'mail' => $mail,
    ]);

    if (count($users) !== 0) {
      $form_state->setError($form['mail'], $this->t('User mail taken.'));
    }
  }

  /**
   * Description.
   *
   * @param string $name
   *   Name.
   *
   * @return string|string[]
   *   Decsription.
   */
  public function makeName($name) {
    $newName = explode('@', $name);
    return $newName[0];
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
//    $name = $form_state->getValue('name');
    $name = $this->makeName($form_state->getValue('mail'));
    $mail = trim($form_state->getValue('mail'));
    $role_id = 'cdp_custom_registered_user';
    $pass = user_password();
    $user = $this->userStorage->create([
      'name' => $name,
      'pass' => $pass,
      'mail' => $mail,
      'init' => $mail,
      'roles' => [
        $role_id,
      ],
    ]);

    if ($user->save()) {
      $this->messenger()->addStatus('New user registered!');
      _user_mail_notify('register_pending_approval', $user);
      $form_state->setRedirect('user.login');
    }

  }

}