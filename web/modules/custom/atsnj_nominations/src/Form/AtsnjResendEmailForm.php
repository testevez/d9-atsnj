<?php
/**
 * @file
 * Contains \Drupal\atsnj_nominations\Form\AtsnjResendEmailForm.
 */
namespace Drupal\atsnj_nominations\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;



class AtsnjResendEmailForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atsnj_resend_email_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['question'] = array(
      '#markup'  => t('Are you sure you want to resend this CTA email?'),
    );
    $form['webform_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Webform ID'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => isset($_GET['webform_id']) ? $_GET['webform_id'] : FALSE,
    );
    $form['nid'] = array (
      '#type' => 'textfield',
      '#title' => t('Webform ID'),
      '#required' => TRUE,
      '#disabled' => TRUE,
      '#default_value' => isset($_GET['nid']) ? $_GET['nid'] : FALSE,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('webform_id')) {
      $form_state->setErrorByName('webform_id', $this->t('webform_id must be set'));
    }
    if (!$form_state->getValue('nid')) {
      $form_state->setErrorByName('nid', $this->t('nid must be set'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $nid = $form_state->getValue('nid');
    $node = Node::load($nid);
    $tid = $node->get('field_nomination_type')->getString();
    $term = Term::load($tid);

    $webform_id = $form_state->getValue('webform_id');
    $constrain = [$webform_id];

    _atsnj_resend_cta_emails($term, $node, $constrain);

  }

}
