<?php

namespace Drupal\atsnj_ebp\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;


/**
 * Implements the SimpleForm form controller.
 *
 * This example demonstrates a simple form with a singe text input element. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class AtsnjEbpMarkAttendanceParamsForm extends AtsnjEbpMarkAttendanceForm {

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

    // Validate conf year
    $vid = 'conference_year';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_data[$term->name] = (string)$term->tid;
    }
    if (!in_array($conference_year_id, $term_data)){
      // bad term id
      return $this->redirect('atsnj_ebp.mark_attendance');
    }

    // Validate Session ID
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'ebp_session')
      ->condition('field_conference_year', $conference_year_id);
    $nids = $query->execute();
    if (!in_array($ebp_session_id, $nids)) {
      // bad node id
      return $this->redirect('atsnj_ebp.mark_attendance');
    }

    $node = Node::load($ebp_session_id);

    $form['conference_year_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Conference Year.'),
      //'#description' => $this->t('Conference Year.'),
      '#default_value' => array_search($conference_year_id, $term_data),
      '#required' => TRUE,
      '#disabled' => TRUE,
    ];
    $form['ebp_session_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('EBP Session.'),
      //'#description' => $this->t('Conference Year.'),
      '#default_value' => $node->get('title')->getString(),
      '#required' => TRUE,
      '#disabled' => TRUE,
    ];

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
    $title = $form_state->getValue('title');
    if (strlen($title) < 5) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    }
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
    /*
     * This would normally be replaced by code that actually does something
     * with the title.
     */
    $title = $form_state->getValue('title');
    $this->messenger()->addMessage($this->t('You specified a title of %title.', ['%title' => $title]));
  }

}
