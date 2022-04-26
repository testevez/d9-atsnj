<?php
/**
 * @file
 * Contains \Drupal\atsnj_admin\Controller\AtsnjAdminController.
 */

namespace Drupal\atsnj_ads\Controller;

class AtsnjAdsController {

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content() {
    return array(
      '#type' => 'markup',
      '#markup' => t('This will be a settings form later'),
    );
  }

}