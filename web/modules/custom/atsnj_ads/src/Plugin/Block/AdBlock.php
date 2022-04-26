<?php
/**
 * @file
 */
namespace Drupal\atsnj_ads\Plugin\Block;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Placeholder' ad block.
 *
 * @Block(
 *   id = "ad_block",
 *   admin_label = @Translation("Ad block"),
 *   category = @Translation("ATSNJ")
 * )
 */
class AdBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#type' => 'markup',
      '#markup' => '<p>Placeholder for eventual dynamic Ad Block</p>',
    );
  }
}
