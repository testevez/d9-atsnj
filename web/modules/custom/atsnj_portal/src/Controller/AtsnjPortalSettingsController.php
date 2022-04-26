<?php
/**
 * @file
 * Contains \Drupal\atsnj_portal\Controller\AtsnjPortalSettingsController.
 */

namespace Drupal\atsnj_portal\Controller;

class AtsnjPortalSettingsController {

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