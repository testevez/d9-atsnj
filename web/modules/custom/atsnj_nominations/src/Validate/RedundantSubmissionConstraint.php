<?php

namespace Drupal\atsnj_nominations\Validate;

use Drupal\Core\Field\FieldException;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\webform\Plugin\WebformHandlerBase;



/**
 * Form API callback. Validate element value.
 */
class RedundantSubmissionConstraint {
  /**
   * Validates given element.
   *
   * @param array              $element      The form element to process.
   * @param FormStateInterface $formState    The form state.
   * @param array              $form The complete form structure.
   */
  public static function validate(array &$element, FormStateInterface $formState, array &$form) {
    $webformKey = $element['#webform_key'];
    $value = $formState->getValue($webformKey);

    // Skip empty unique fields or arrays (aka #multiple).
    if ($value === '' || is_array($value)) {
      return;
    }

    // do some validation here...
    // and set some error variable, e.g. $error

    $build_info =  $formState->getBuildInfo();
    $form_name = $build_info['base_form_id'];
    $form_name_len = strlen($form_name);
    $start = strlen('webform_submission_');
    $this_webform_id = substr($form_name, $start, ($form_name_len - ($start + strlen('_form'))));



    // Load up all the submissions for this nomination node
    $node = Node::load($value);
    $submissions = $node->get('field_nomination_submission')->getValue();

    foreach ($submissions as $i => $array) {
      $sid = $array['target_id'];
      $submission = \Drupal\webform\Entity\WebformSubmission::load($sid);
      // Get the webform_id for each submission
      $webform = $submission->getWebform();
      $webform_id = $webform->id();

      $has_permission = \Drupal::currentUser()->hasPermission('administer atsnj nominations module');

      if ($this_webform_id == $webform_id && !$has_permission) {
        // Set $error = TRUE if there is a submission for this webform already.
        $error = TRUE;
      }
      else {
        $error = FALSE;
      }

    }
    
    if ($error) {
      if (isset($element['#title'])) {
        $tArgs = array(
          '%name' => empty($element['#title']) ? $element['#parents'][0] : $element['#title'],
          '%value' => $value,
        );
        /*
        $formState->setError(
          $element,
          t('The value %value is not allowed for element %name. Please use a different value.', $tArgs)
        );
        */
        $formState->setError(
          $element,
          t('This form has already been submitted.')
        );
      } else {
        $formState->setError($element);
      }
    }
  }
}