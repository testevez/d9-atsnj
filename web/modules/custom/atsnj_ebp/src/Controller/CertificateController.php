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
use Drupal\Core\Entity\NodeInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\comment\Entity\Comment;
use Drupal\message\Entity\Message;
use Drupal\taxonomy\Entity\Term;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\HttpFoundation\Response;



class CertificateController {


  /**
   * Display the markup.
   *
   * @return array
   */
  public function content($ebp_attendee_id = NULL, $ebp_session_id = NULL) {

    if ('cat-a' == $ebp_session_id) {
      $ebp_attendee_node = Node::load($ebp_attendee_id);
      // TODO: Make this dynamoic
      $at = '36th Annual ATSNJ Conference and Business Meeting';
      $date_formatted =   'February 27-February 28, 2022';

      $build = array(
        'page' => array(
          '#theme' => 'astnj_cat_a_certificate',
          '#name' => $ebp_attendee_node->getTitle(),
          '#at' => $at,
          '#date' => $date_formatted,
        ),
      );

      $html = \Drupal::service('renderer')->renderRoot($build);
      $response = new Response();
      $response->setContent($html);
    }
    else {
      $ebp_attendee_node = Node::load($ebp_attendee_id);
      $ebp_session_node = Node::load($ebp_session_id);

      $presented_by = check_markup($ebp_session_node->get('field_presented_by')->value, $ebp_session_node->get('field_presented_by')->format);
      $at = check_markup($ebp_session_node->get('field_at')->value, $ebp_session_node->get('field_at')->format);

      // get unix timestamp $timestamp = $ebp_session_node->field_date_only->date->getTimestamp();
      // get a formatted date
      $date_formatted = $ebp_session_node->field_date_only->date->format('F d, Y');

      $build = array(
        'page' => array(
          '#theme' => 'astnj_ebp_certificate',
          '#name' => $ebp_attendee_node->getTitle(),
          '#session_title' =>$ebp_session_node->getTitle(),
          '#presented_by' => $presented_by,
          '#at' => $at,
          '#date' => $date_formatted,
        ),
      );

      $html = \Drupal::service('renderer')->renderRoot($build);
      $response = new Response();
      $response->setContent($html);
    }


    return $response;
  }

}
