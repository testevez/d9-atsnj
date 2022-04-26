<?php
/**
 * @file
 * Contains \Drupal\atsnj_portal\Plugin\Block\MemberStatus.
 */
namespace Drupal\atsnj_portal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\user\Entity\User;
use Drupal\user\Entity\Role;

/**
 * Provides a 'Member Status' block.
 *
 * @Block(
 *   id = "member_status",
 *   admin_label = @Translation("Member Status"),
 *   category = @Translation("ATSNJ")
 * )
 */
class MemberStatus extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $current_route = \Drupal::routeMatch();
    $account = $current_route->getParameters()->get('user');

    if (is_null($account)) {
      $current_user = User::load(\Drupal::currentUser()->id());
      $current_user_id = $current_user->id();
      $account = User::load($current_user_id);
    }

    // Build markup.
    $markup = '<p>';

    if ($account->hasRole('cas_user_in_state') || $account->hasRole('privilege_user') || $account->hasRole('non_nata_atsnj_member')) {
      $markup = '<p>You are a valid and current member of ATSNJ.</p>';
    }
    if ($account->hasRole('administrator') || $account->hasRole('awards_and_scholarships') || $account->hasRole('editor') || $account->hasRole('content_creator') || $account->hasRole('content_reviewer') || $account->hasRole('ebp_admin') || $account->hasRole('nominations_committee_chair') || $account->hasRole('minutes_editor')) {

      $roles = Role::loadMultiple($account->getRoles());
      if ($roles) {
        $markup .= '<p>You have administrative roles within ATSNJ.com</p>';
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
      '#markup' => t($markup),
    );
  }

}
