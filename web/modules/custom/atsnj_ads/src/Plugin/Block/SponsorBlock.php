<?php

namespace Drupal\atsnj_ads\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Display all instances for 'SponsorBlock' block plugin.
 *
 * @Block(
 *   id = "atsnj_ads_block",
 *   admin_label = @Translation("Sponsor Block"),
 *   deriver = "Drupal\atsnj_ads\Plugin\Derivative\SponsorBlock"
 * )
 */

class SponsorBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $id = $this->configuration['id'];
    $parts = explode(':', $id);
    $nid = $parts[1];

    $entity_type = 'node';
    $view_mode = 'square_ad';
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
    $node = $storage->load($nid);
    $build = $view_builder->view($node, $view_mode);
    $output = render($build);
    return array(
      '#type' => 'markup',
      '#markup' => $output,
    );
  }
}