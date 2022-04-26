<?php
/**
 * @file
 * Contains \Drupal\atsnj\Controller\AtsnjController.
 */

namespace Drupal\atsnj\Controller;

class AtsnjController {

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