<?php

namespace Drupal\cdp_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides a 'form' Block.
 *
 * @Block(
 *   id = "forma_block",
 *   admin_label = @Translation("forma block"),
 *   category = @Translation("forma"),
 * )
 */
class Formablock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\cdp_form\Form\TestForm');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);



    return $form;
  }

  /**
   * {@inheritdoc}
   */
//  public function blockSubmit($form, FormStateInterface $form_state) {
//
//  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, $return_as_object = FALSE) {
    return AccessResult::allowedIf($account->isAuthenticated());
  }

}
