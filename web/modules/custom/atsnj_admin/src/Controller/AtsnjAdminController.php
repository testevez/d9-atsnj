<?php
/**
 * @file
 * Contains \Drupal\atsnj_admin\Controller\AtsnjAdminController.
 */

namespace Drupal\atsnj_admin\Controller;

use Drupal\flag\FlagInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\atsnj_admin\Form\AtsnjMarkAllReadForm;


class AtsnjAdminController {

  /**
   * Performs a flagging for all entities for a user when called via a route.
   *
   * @param int $flag_id
   *   The flag ID.
   * @param int $user_id
   *   The user ID.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse|\Symfony\Component\HttpFoundation\RedirectResponse|null
   *   The response object, only if successful.
   *
   */
  public function flagByAccount ($flag_id, $user_id) {

    $parameter = ['flag_id' => $flag_id, 'user_id' => $user_id];
    $form = \Drupal::formBuilder()->getForm('\Drupal\atsnj_admin\Form\AtsnjMarkAllReadForm', $parameter);
    return $form;

  }

  /**
   * Performs an access check.
   *
   * @param int $flag_id
   *   The flag ID.
   * @param int $user_id
   *   The user ID.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   *
   */
  public function access($flag_id, $user_id) {

    // Current User.
    $current_user = \Drupal::currentUser();

    // Load the account.
    /** @var User $account */
    $account = User::load($user_id);

    $condition_1 = $current_user->hasPermission('mark all messages read for any user');
    $condition_2 =  $current_user->hasPermission('mark all my flags read') && ($current_user->id() == $account->id());

    return AccessResult::allowedIf($condition_1 || $condition_2);
  }


}