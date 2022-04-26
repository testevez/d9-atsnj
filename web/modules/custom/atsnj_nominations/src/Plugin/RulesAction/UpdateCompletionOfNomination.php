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
 * Provides a 'UpdateCompletionOfNomination' action.
 *
 * @RulesAction(
 *   id = "update_completion_of_momination",
 *   label = @Translation("ATSNJ: Update completion of nomination"),
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
class UpdateCompletionOfNomination extends RulesActionBase {

  /**
   *
   * Replaces tokens with values from a data array
   *
   * @param string $string
   * @param array $data
   * @return string
   */
  public function replaceTokens(string $string, array $data) {

    foreach ($data as $i => $v) {
      $string = str_replace('['. $i .']', $v, $string );
    }

    return $string;
  }

  /**
   * Does something.
   *
   * @param \Drupal\user\UserInterface $node
   *   The nomination node.
   */
  protected function doExecute(NodeInterface $node) {
    // Collect nominations config
    $config = \Drupal::config('atsnj_nominations.settings');

    _atsnj_nominations_update_completion($node);


  }

}