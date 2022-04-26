<?php
/**
 * @file
 * Contains \Drupal\atsnj\Controller\AtsnjController.
 */

namespace Drupal\atsnj_nominations\Controller;

class AtsnjNominationsResendController {

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content() {
    return array(
      '#type' => 'markup',
      '#markup' => t('Look here'),
    );
  }

}