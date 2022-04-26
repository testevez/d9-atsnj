<?php
/**
 * @file
 * Contains \Drupal\atsnj_ebp\Controller\AttendanceController.
 */

namespace Drupal\atsnj_ebp\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config;
use Drupal\Core\ConfigFactory;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\comment\Entity\Comment;
use Drupal\message\Entity\Message;
use Drupal\taxonomy\Entity\Term;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\paragraphs\Entity\Paragraph;


class AttendanceController {

  /**
   * Only show for ebp_session nodes.
   *
   * @return array
   */
  public function checkAccess(NodeInterface $node) {
    return AccessResult::allowedif($node->bundle() === 'ebp_session');
  }

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content(NodeInterface $node = NULL) {

    $header = array(
      'id' => t('<a>NID</a>'),
      'title' => t('<a>Title</a>'),
      'updated' => t('<a> Last Updated</a>'),
      'cert_page' => t('<a>Certificate Page</a>'),
    );

    $nid = $node->id();
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'ebp_attendee')
      ->condition('field_conference_reference', $nid);
    $attendee_nids = $query->execute();

    $attendee_nodes = Node::loadMultiple($attendee_nids);
    $rows = [];

    foreach ($attendee_nodes as $attendee_node) {
      $rows[] = array(
        'data' => array(
          $attendee_node->id(),
          $attendee_node->get('title')->getString(),
          format_date($attendee_node->getCreatedTime(), 'long'),
          t('<a href="/certificate/'. $attendee_node->id() .'/'. $nid .'">Link to Certificate page</a>'),
        )
      );
    }


    return array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => array('class'=>array('attendance-table')),
    );
  }

}