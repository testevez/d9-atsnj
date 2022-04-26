<?php

namespace Drupal\atsnj_committee\Routing;

use Drupal\media\Entity\MediaType;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for group_media group content.
 */
class CommitteeGroupRouteProvider {

  /**
   * Provides the shared collection route for committee routes.
   */
  public function getRoutes() {
    $routes = [];
/*
    $routes['entity.group_content.minutes'] = new Route('group/{group}/minutes');
    $routes['entity.group_content.minutes']
      ->setDefaults([
        '_title_callback' => '\Drupal\atsnj_committee\Controller\CommitteeMinutesController::title',
        '_controller' => '\Drupal\atsnj_committee\Controller\CommitteeMinutesController::minutesList',
      ])
      ->setRequirement('_permission', 'access content')
      ->setOption('_group_operation_route', TRUE);
*/
    return $routes;
  }

}
