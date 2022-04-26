<?php
namespace Drupal\atsnj_nominations\Plugin\WebformHandler;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\webform\Element\WebformHtmlEditor;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\WebformTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\message\Entity\Message;


/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "nomination_form_handler",
 *   label = @Translation("Nomination form handler"),
 *   category = @Translation("ATSNJ Custom"),
 *   description = @Translation("Creates nomination nodes"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class NominationFormHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // Is this edit or create?
    $edit = $webform_submission->id();
    
    // Get an array of the values from the submission.
    $values = $webform_submission->getData();
    // Get the year
    $config = \Drupal::config('atsnj_nominations.settings');
    $year_tid = $config->get('year');

    if (!$edit) {
      // New submission means new nomination node.
      // Create Nomination node
      $node = Node::create([
        'type' => 'nomination',
        'title' => $values['name_of_nominator'] .' nominates '. $values['name_of_nominee'],
        'field_nomination_type' => [
          'target_id' => $values['nomination_type'],
        ],
        'field_year' => [
          'target_id' => $year_tid,
        ],
      ]);
      $node->save();

      $nomination_nid = $node->id();

      // Update submission with a reference to the node
      $webform_submission->setElementData('nomination_node', $nomination_nid);

      // Determine who gets the internal messages
      $users_to_be_notified = _atsnj_get_users_nomination_message();

      foreach ($users_to_be_notified as $user) {
        // Send message(s)
        $message = Message::create(['template' => 'nomination_was_submitted', 'uid' => $user->id()]);
        $message->set('field_nomination', $node);
        $message->save();
      }
    }

    return true;

  }
}

