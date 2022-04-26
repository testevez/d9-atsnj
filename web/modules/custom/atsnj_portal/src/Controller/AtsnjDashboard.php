<?php
/**
 * @file
 * Contains \Drupal\atsnj_portal\Controller\AtsnjDashboard.
 */

namespace Drupal\atsnj_portal\Controller;

use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use \Drupal\Core\Access\AccessResultInterface;
use \Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;


class AtsnjDashboard {

  /**
   * Check for access.
   *
   * @param \Drupal\user\Entity\User $account
   * The user whose dashboard it is.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(UserInterface $account = NULL) {

    $current_user = User::load(\Drupal::currentUser()->id());
    $current_user_id = $current_user->id();

    if (is_null($account)) {
      $account = \Drupal\user\Entity\User::load($current_user_id);
    }

    if ($current_user_id == $account->id()) {
      $has_permission = $account->hasPermission('access my dashboard');
    }
    else {
      $has_permission = $account->hasPermission('access any dashboard');
    }

    return AccessResult::allowedIf($has_permission);

  }

  /**
   * Display the page.
   *
   * @param \Drupal\user\Entity\User $user
   * The user whose dashboard it is.
   *
   * @return array
   */
  public function content(UserInterface $user = NULL) {

    $account = $user;
    // $user = User::load(\Drupal::currentUser()->id()); to load the user

    // Build markup.
    $markup = '<p>';

    if ($account->hasRole('cas_user_in_state') || $account->hasRole('privilege_user') || $account->hasRole('non_nata_atsnj_member')) {
      $markup = '<p>You are a valid and current member of ATSNJ.</p>';
    }
    if ($account->hasRole('administrator') || $account->hasRole('awards_and_scholarships') || $account->hasRole('editor') || $account->hasRole('content_creator') || $account->hasRole('content_reviewer') || $account->hasRole('ebp_admin') || $account->hasRole('nominations_committee_chair') || $account->hasRole('minutes_editor')) {

      $roles = Role::loadMultiple($account->getRoles());
      if ($roles) {
        $markup .= '<h2>You have administrative roles within ATSNJ.com:</h2>';
        $markup .= '<ul>';

        $roles_to_check = [
          'administrator',
          'awards_and_scholarships',
          'editor',
          'content_creator',
          'content_reviewer',
          'ebp_admin',
          'nominations_committee_chair',
          'minutes_editor'
        ];
        foreach ($roles_to_check as $role_to_check) {
          if ($account->hasRole($role_to_check)) {
            // Load label
            foreach ($roles as $role) {
              if ($role->id() == $role_to_check) {
                $markup .= '<li>'. $role->label() .'</li>';
              }
            }
          }
        }
      }


      $markup .= '<ul>';
    }
    $markup .= '</p>';

    return array(
      '#type' => 'markup',
      '#title' => t('Dashboard for '. $account->getAccountName()),
      '#markup' => $markup,
    );

  }
}
