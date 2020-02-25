<?php

namespace Drupal\cdp_profile\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserInterface;

/**
 * Provides a user password reset form.
 */
class ChangeDetailsForm extends ContentEntityForm {

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
  public function form(array $form, FormStateInterface $form_state, UserInterface $user = NULL) {
    $this->entity = User::load($this->currentUser()->id());
    $config = \Drupal::config('user.settings');
    $form['#cache']['tags'] = $config->getCacheTags();;
    $form['account'] = [
      '#type'   => 'container',
      '#weight' => -10,
    ];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = ['#type' => 'submit', '#value' => $this->t('Submit')];
    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $account = $this->entity;
    $account->save();
    $form_state->setValue('uid', $account->id());
    $this->messenger()->addStatus($this->t('The changes have been saved.'));
  }

}
