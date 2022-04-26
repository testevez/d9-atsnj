<?php
/**
 * @file
 * Contains \Drupal\atsnj_committee\Controller\CommitteeMinutesController.
 */

namespace Drupal\atsnj_committee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\group\Entity\GroupInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\Renderer;
use Drupal\group\Entity\Controller\GroupContentController;


class CommitteeMinutesController extends ControllerBase {

  /**
   * Provides all the minutes for a group.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group to join.
   *
   * @return array
   *   A group join form.
   */
  public function test(GroupInterface $group) {
    return array(
      '#type' => 'markup',
      '#markup' => t('This will be a settings form later'),
    );
  }

  /**
   * Provides all the minutes for a group.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group.
   *
   * @return array
   *   A group join form.
   */
  public function minutesList(GroupInterface $group = NULL) {

    // Get renderable array for view.
    $view = views_embed_view('committee_minutes_media_', 'embed_1', $group->id());
    // Render view.
    $output = \Drupal::service('renderer')->render($view);

    return array(
      '#type' => 'markup',
      '#markup' => $output,
    );
  }

  public function title(GroupInterface $group = NULL) {
    $label = $group->label();
    return "$label Minutes";
  }

  /**
   * Provides all the minutes for a group.
   *
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The relevant committee.
   *
   * @return array
   *   A group join form.
   */
  public function minutesAdmin(GroupInterface $group = NULL) {

    // Get renderable array for view.
    $view = views_embed_view('committee_minutes_media_', 'embed_2', $group->id());
    // Render view.
    $output = \Drupal::service('renderer')->render($view);

    return array(
      '#type' => 'markup',
      '#markup' => $output,
    );
  }

  public function minutesAdminTitle(GroupInterface $group = NULL) {
    $label = $group->label();
    return "$label Minutes Administration";
  }

}
