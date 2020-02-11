<?php

namespace Drupal\cdp_text\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "form_block",
 *   admin_label = @Translation("form block"),
 *   category = @Translation("form"),
 * )
 */
class Formblock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    if (!empty($config['link'])) {
      $link = $config['link'];
    }
    else {
      $link = $this->t('no link');
    }
    if (!empty($config['text'])) {
      $text = $config['text'];
    }
    else {
      $text = $this->t('no text');
    }
    if (!empty($config['html'])) {
      $html = $config['html'];
    }
    else {
      $html = $this->t('no html');
    }

    return [
      '#theme' => 'cdp_text_theme',
      '#link' => $link,
      '#text' => strip_tags($text),
      '#html' => $html,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = \Drupal::config('cdp_text.settings');

    $form['link'] = [
      '#type' => 'url',
      '#title' => $this->t('Give your link'),
      '#description' => $this->t('What link you want'),
      '#default_value' => $config->get('hello_block_name'),
    ];
    $form['text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Give plain text'),
      '#description' => $this->t('This text will be simple'),
      '#default_value' => $config->get('hello_block_name'),
    ];
    $form['html'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Give html'),
      '#description' => $this->t('This text will show Html tags'),
      '#default_value' => $config->get('hello_block_name'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['link'] = $values['link'];
    $this->configuration['text'] = $values['text'];
    $this->configuration['html'] = $values['html'];
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, $return_as_object = FALSE) {
    return AccessResult::allowedIf($account->isAuthenticated());
  }

}
