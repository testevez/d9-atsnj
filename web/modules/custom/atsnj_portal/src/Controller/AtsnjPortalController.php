<?php
/**
 * @file
 * Contains \Drupal\atsnj_portal\Controller\AtsnjPortalController.
 */

namespace Drupal\atsnj_portal\Controller;

class AtsnjPortalController {

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content() {

    $account = \Drupal::currentUser();
    // $user = User::load(\Drupal::currentUser()->id()); to load the user

    /*
    if (!$account->hasPermission('access atsnj members content')){
      // go to members page
      $response = new RedirectResponse('/members');
      $response->send();
    }
    */

    return array(
      '#type' => 'markup',
      '#title' => t('Portal for '. $account->getAccountName()),
      '#markup' => t(''),
    );
  }

}