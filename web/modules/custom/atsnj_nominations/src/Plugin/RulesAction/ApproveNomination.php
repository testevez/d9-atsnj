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
use Drupal\Core\Link;
use Drupal\Core\Url;


/**
 * Provides a 'Approve Nomination' action.
 *
 * @RulesAction(
 *   id = "atsnj_nominations_approve_nomination",
 *   label = @Translation("ATSNJ: Approve Nomination"),
 *   category = @Translation("ATSNJ"),
 *   context = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the nomination node.")
 *     ),
 *   }
 * )
 *
 */
class ApproveNomination extends RulesActionBase {

  /**
   * Does something.
   *
   * @param \Drupal\user\UserInterface $node
   *   The nomination node.
   */
  protected function doExecute(NodeInterface $node) {
    // Collect nominations config
    $config = \Drupal::config('atsnj_nominations.settings');

    // Collect type config
    $tid = $node->get('field_nomination_type')->getString();
    $term = Term::load($tid);

    // check for any and all emails that must go out
    $emails = _atsnj_determine_nomination_emails($term, $node);

    _atsnj_nominations_send_emails($emails, $node);

  }

}