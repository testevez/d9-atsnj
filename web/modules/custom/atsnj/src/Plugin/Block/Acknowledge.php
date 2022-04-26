<?php

namespace Drupal\atsnj\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'Modal' Block that checks for acknowledgement of important information.
 *
 * @Block(
 *   id = "acknowledge",
 *   admin_label = @Translation("Important Information: Acknowledgement Modal"),
 * )
 */
class Acknowledge extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {


    $link_url = Url::fromRoute('atsnj.acknowledge');
    $link_url->setOptions([
      'attributes' => [
        'class' => [
          'use-ajax', 'button', 'button--small', 'open-important-information-intro',
        ],
        'data-dialog-type' => 'modal',
      ],
    ]);

    return [
      '#type' => 'markup',
      '#markup' => Link::fromTextAndUrl(t('Open modal'), $link_url)->toString(),
      '#attached' => [
        'library' => [
          'core/drupal.dialog.ajax',
          'atsnj/acknowledge',
        ],
      ],
    ];
  }


}
