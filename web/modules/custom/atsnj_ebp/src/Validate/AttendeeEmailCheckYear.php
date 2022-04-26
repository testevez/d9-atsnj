<?php

namespace Drupal\atsnj_ebp\Validate;

use Drupal\Core\Field\FieldException;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\webform\Plugin\WebformHandlerBase;



/**
 * Form API callback. Validate element value.
 */
class AttendeeEmailCheckYear {
  /**
   * Validates given element.
   *
   * @param array              $element      The form element to process.
   * @param FormStateInterface $formState    The form state.
   * @param array              $form The complete form structure.
   */
  public static function validate(array &$element, FormStateInterface $formState, array &$form) {
    $webformKey = $element['#webform_key']; // 'attendee_email_year'

    $value = $formState->getValue($webformKey);

    // Skip empty unique fields or arrays (aka #multiple).
    if ($value === '' || is_array($value)) {
      return;
    }

    // do some validation here...
    // and set some error variable, e.g. $error

    $email_to_check = strtolower($value);


    // Load all attendees for the current year
    $config = \Drupal::config('atsnj_ebp.settings');
    $conference_year_id = $config->get('current_year');


    $attendee_ids = \Drupal::entityQuery('node')
      ->condition('field_conference_year', $conference_year_id)
      ->condition('type', 'ebp_attendee')
      ->execute();
    $attendees = Node::loadMultiple($attendee_ids);

    $attendee_emails = array();

    foreach ($attendees as $attendee) {

      $attendee_email = $attendee->get('field_email')->getString();
      $attendee_emails[] = strtolower($attendee_email);

    }

    // Add an email that always will let you in
    $attendee_emails[] = 'evaluation@secret.com';

    $error = FALSE;
    if (!in_array($email_to_check, $attendee_emails)) {
      $error = TRUE;
    }

    if ($error) {
      if (isset($element['#title'])) {
        $tArgs = array(
          '%name' => empty($element['#title']) ? $element['#parents'][0] : $element['#title'],
          '%value' => $value,
        );
        $formState->setError(
          $element,
          t('This email is not associated with an attendee of this year\'s conference.')
        );
      } else {
        $formState->setError($element);
      }
    }

  }

}
