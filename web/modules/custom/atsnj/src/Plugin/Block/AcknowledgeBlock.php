<?php

namespace Drupal\atsnj\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'Standard' Block that checks for acknowledgement of important information.
 *
 * @Block(
 *   id = "acknowledge_block",
 *   admin_label = @Translation("Important Information: Acknowledgement Block"),
 * )
 */
class AcknowledgeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = \Drupal::config('atsnj.acknowledge_settings');

    $value = $config->get('atsnj_details_header_copy');
    $header =  isset($value) ? $value : ATSNJ_DEFAULT_DETAILS_HEADER_COPY;

    // Details.
    $element['details'] = array(
      '#type' => 'details',
      '#title' => $this
        ->t($header),
    );

    $format = $config->get('atsnj_acknowledge_copy')['format'];
    $value = $config->get('atsnj_acknowledge_copy')['value'];
    $element['details']['copy'] = array(
      '#type' => 'processed_text',
      '#text' => isset($value) ? $value : ATSNJ_DEFAULT_ACKNOWLEDGE_COPY,
      '#format' => isset($format) ? $format : 'full_html',
    );
    $markup = render($element);

    return [
      '#type' => 'markup',
      '#markup' => $markup,
      '#attached' => [
        'library' => [
          'atsnj/acknowledge_block',
        ],
      ]
    ];
  }

/*
adding: 15px;
margin-bottom: 15px;
border: 1px solid #dadada;
*/
}
