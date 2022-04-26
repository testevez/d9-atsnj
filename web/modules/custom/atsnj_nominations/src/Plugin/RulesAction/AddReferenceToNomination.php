<?php

namespace Drupal\atsnj_nominations\Plugin\RulesAction;

use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\node\Entity\Node;
use Drupal\comment\Entity\Comment;
use Drupal\user\Entity\User;
use Drupal\message\Entity\Message;
use Drupal\taxonomy\Entity\Term;
use Drupal\webform\WebformSubmissionInterface;


/**
 * Provides a 'AddReferenceToNomination' action.
 *
 * @RulesAction(
 *   id = "atsnj_nominations_add_reference_to_nomination",
 *   label = @Translation("ATSNJ: Add reference to nomination"),
 *   category = @Translation("ATSNJ"),
 *   context = {
 *     "webform_submission" = @ContextDefinition("entity:webform_submission",
 *       label = @Translation("Webform_submission"),
 *       description = @Translation("Specifies the nomination submission.")
 *     ),
 *   }
 * )
 *
 */
class AddReferenceToNomination extends RulesActionBase {

  /**
   * Runs immediately after any webform submimssion is made
   * If the webform submission has a value for 'nomination_node' in its
   * data, then that node is found and given a reference to the submission.
   *
   * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
   *   The nomination submission.
   */
  protected function doExecute(WebformSubmissionInterface $webform_submission) {


    // Load the data in the webform submission
    $data = $webform_submission->getData();
    $webform = $webform_submission->getWebform();
    $webform_id = $webform->id();

    // Logs a notice
    \Drupal::logger('atsnj_nominations')->notice(('@webform_id: was submitteds.'),
      array(
        '@webform_id' => $webform_id,
      ));

    if (isset($data['nomination_node'])) {
      $nomination_node_nid = $data['nomination_node'];

      // Logs a notice
      \Drupal::logger('atsnj_nominations')->notice(('@webform_id: was submitted with nomination node value of @nid.'),
        array(
          '@webform_id' => $webform_id,
          '@nid' => $nomination_node_nid,
        ));

      $node = Node::load($nomination_node_nid);
      if ($node) {
        $sid = $webform_submission->id();


        $submissions = $node->get('field_nomination_submission')->getValue();
        $submissions[] = ['target_id' => $sid];

        $node->set('field_nomination_submission', $submissions);
        $node->save();
      }
      else {
        // Logs an error
        \Drupal::logger('atsnj_nominations')->error(('@webform_id: was submitted with nomination node value of @nid but the node was not able to be loaded.'),
          array(
            '@webform_id' => $webform_id,
            '@nid' => $nomination_node_nid,
          ));
      }

    }
    else {
      // Logs a notice
      \Drupal::logger('atsnj_nominations')->notice(('@webform_id: was submitted with no nomination node value.'), array());

    }
  }
}