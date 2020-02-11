<?php

namespace Drupal\cdp_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'cdp_core' Block.
 *
 * @Block(
 *   id = "hello_block",
 *   admin_label = @Translation("cdp_core block"),
 *   category = @Translation("from cdp_core model"),
 * )
 */
class TestBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#markup' => $this->t('cdp_core block'),
      '#attached' => [
        'library' => [
          'cdp_core/message',
        ],
      ],
    ];
  }

}
