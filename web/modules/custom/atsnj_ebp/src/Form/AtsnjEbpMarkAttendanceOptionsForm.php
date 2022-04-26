<?php

namespace Drupal\atsnj_ebp\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Messenger\MessengerInterface;



/**
 * Implements the SimpleForm form controller.
 *
 * This example demonstrates a simple form with a singe text input element. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class AtsnjEbpMarkAttendanceOptionsForm extends AtsnjEbpMarkAttendanceForm {

  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $conference_year_id = NULL, $ebp_session_id = NULL) {

    // Load list of years
    $vid = 'conference_year';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_data[$term->tid] = (string)$term->name;
    }

    $config = \Drupal::config('atsnj_ebp.settings');
    $conference_year_id = $config->get('current_year');

    $form['conference_year_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Conference Year.'),
      '#default_value' => array_search($conference_year_id, $term_data),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#options' => $term_data,
      '#default_value' => $conference_year_id,
    ];

    // Find the EBP Session ID
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'ebp_session')
      ->condition('field_conference_year', $conference_year_id);
    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);
    if (count($nodes)) {

      foreach ($nodes as $node) {
        $nid = $node->get('nid')->getString();
        $node_data[$nid] = $node->get('title')->getString();
      }

      $form['ebp_session_id'] = [
        '#type' => 'select',
        '#title' => $this->t('EBP Session.'),
        //'#description' => $this->t('Conference Year.'),
        '#required' => TRUE,
        '#options' => $node_data,
      ];

    }
    else {
      // No Session have been made yet
      $form['ebp_session_id']['#markup'] = 'No sessions have been made for the conference year. This form will not work properly without sessions being created.';
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller. It must be
   * unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'atsnj_ebp_mark_attendance';
  }

  /**
   * Implements form validation.
   *
   * The validateForm method is the default method called to validate input on
   * a form.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $conference_year_id = $form_state->getValue('conference_year_id');
    $ebp_session_id = $form_state->getValue('ebp_session_id');
    $emails_all_string = $form_state->getValue('emails');
    $emails = explode('
', $emails_all_string);

    $bad_emails = array();
    $good_emails = array();
    $attendee_nids = array();

    foreach ($emails as $email) {
      $email = trim($email);
      // Find the attendee
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'ebp_attendee')
        ->condition('field_email', $email)
        ->condition('field_conference_year', $conference_year_id);
      $nids = $query->execute();

      if (count($nids)) {
        // We found one
        $good_emails[] = $email;
        $attendee_nids[] = current($nids);
      }
      else {
        // No attendee for this email
        $bad_emails[] = $email;
      }

    }
    $nodes = Node::loadMultiple($attendee_nids);

    foreach ($nodes as $node) {
      $do_not_add = FALSE;
      // Load existing references
      $existing = $node->get('field_conference_reference')->getValue();

      if (count($existing)) {
        foreach ($existing as $target_id) {
          if (current($target_id) == $ebp_session_id) {
            $do_not_add = TRUE;
          }
        }
      }
      if (!$do_not_add) {
        // Format the new references
        $values = $existing;
        $values[] = ['target_id' => $ebp_session_id];
        $node->set('field_conference_reference', $values);
        $node->save();
      }
    }
    if (count($good_emails)) {
      $message = 'Please note that the following emails were marked as in attendance.: ';
      foreach ($good_emails as $email) {
        $message .= $email . ' ';
      }
      \Drupal::messenger()->addWarning($message);
    }

    if (count($bad_emails)) {
      $error_message = 'Please note that the following emails did not mate to an attendee: ';
      //$error_message .= '<ul>';
      foreach ($bad_emails as $email) {
        $error_message .= $email . ' ';
      }
      \Drupal::messenger()->addWarning($error_message);
    }

  }

}
