<?php
/**
 * @file
 * Contains \Drupal\atsnj_committee\Controller\AtsnjAccessDenied.
 */

namespace Drupal\atsnj\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;


class AtsnjAccessDenied extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content() {

    // Current User.
    $current_user = \Drupal::currentUser();
    //ksm($current_user->id());
    if ($current_user->id() > 0) {
      // Authenticated User
      $url = Url::fromRoute('entity.webform.canonical', ['webform' => 'contact']);
      $markup = t('<p>You have attempted to access content that is only for website administrators of ATSNJ.com. If you feel you are missing sufficient access privileges for your account, please <a href="@link">contact us</a>.</p>', ['@link' => $url->toString()]);
    }
    else {
      // Anonymous
      $url = Url::fromRoute('user.login', []);
      $markup = t('<p>You have attempted to access content that is only for ATSNJ members. Please <a href="@link">log in</a> to the site to access members only content.</p>',['@link' => $url->toString()]);

    }
    $markup .= t('<p>If you feel you have reached this page in error, please check the URL, use the <em>Main Navigation</em>, the <em>Quick Links</em> or the <em>Search</em> to find your target page.</p><p>Thank you.</p>');
    return [
      '#markup' => $markup,
    ];

  }

  /**
   * Display the title.
   *
   * @return array
   */
  public function title() {

    // Current User.
    $current_user = \Drupal::currentUser();

    if ($current_user->id() > 0) {
      return [
        '#markup' => t('Insufficient Privileges'),
      ];
    }
    else {
      return [
        '#markup' => t('Members Only'),
      ];
    }

  }

  /**
   * Access callback.
   *
   * @return \Drupal\Core\Access\AccessResultAllowed
   */
  public function access() {
    return AccessResult::allowed();
  }
}
