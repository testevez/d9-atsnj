<?php

namespace Drupal\atsnj_ebp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure atsnj_ebp settings for this site.
 */
class AtsnjEbpSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atsnj_ebp_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'atsnj_ebp.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('atsnj_ebp.settings');

    $form['certificate_email_from'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Certificate Email From Email Address'),
      '#default_value' => $config->get('certificate_email_from'),
      '#description'  => $this->t('Leave blank to use the site settings account (<a href="/admin/config/system/site-information">here</a>). Also, this email can be use for EBP Support requests.'),
    );
    $form['certificate_email_copy'] = array(
      '#type' => 'details',
      '#title' => $this->t('Default EBP Email Copy'),
      '#description' => $this->t('Please note that this copy can be overridden by each session, in case a particular session requires a difference in the standard copy.'),
      '#open' => TRUE,

    );

    $form['certificate_email_copy']['textformat_subject'] = [
      '#type' => 'text_format',
      '#required' => TRUE,
      '#title' => $this->t('EBP Certificate Email Subject'),
      '#base_type' => 'textfield',
      '#format' => $config->get('textformat_subject')['format'],
      '#default_value' => $config->get('textformat_subject')['value'],
    ];
    $form['certificate_email_copy']['textformat_body'] = [
      '#type' => 'text_format',
      '#required' => TRUE,
      '#title' => $this->t('EBP Certificate Email Body'),
      '#base_type' => 'textarea',
      '#format' => $config->get('textformat_body')['format'],
      '#default_value' => $config->get('textformat_body')['value'],
    ];

    $form['cat_a_certificate_email_copy'] = array(
      '#type' => 'details',
      '#title' => $this->t('Default CAT A Email Copy'),
      '#open' => TRUE,
    );
    $form['cat_a_certificate_email_copy']['cat_a_textformat_subject'] = [
      '#type' => 'text_format',
      '#required' => TRUE,
      '#title' => $this->t('CAT A Certificate Email Subject'),
      '#base_type' => 'textfield',
      '#format' => $config->get('cat_a_textformat_subject')['format'],
      '#default_value' => $config->get('cat_a_textformat_subject')['value'],
    ];
    $form['cat_a_certificate_email_copy']['cat_a_textformat_body'] = [
      '#type' => 'text_format',
      '#required' => TRUE,
      '#title' => $this->t('CAT A Certificate Email Body'),
      '#base_type' => 'textarea',
      '#format' => $config->get('cat_a_textformat_body')['format'],
      '#default_value' => $config->get('cat_a_textformat_body')['value'],
    ];

    $vid = 'conference_year';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $term_data[0] = 'None';
    foreach ($terms as $term) {
      $term_data[$term->tid] = $term->name;
    }
    $form['current_year'] = array(
      '#type' => 'select',
      '#title' => 'Which year of the EBP are we in (for the purpose of attendance)?',
      '#options' => $term_data,
      '#default_value' => $config->get('current_year'),

    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->configFactory->getEditable('atsnj_ebp.settings')
      // Set the submitted configuration setting
      ->set('certificate_email_from', $form_state->getValue('certificate_email_from'))
      ->set('textformat_subject', $form_state->getValue('textformat_subject'))
      ->set('textformat_body', $form_state->getValue('textformat_body'))
      ->set('cat_a_textformat_subject', $form_state->getValue('cat_a_textformat_subject'))
      ->set('cat_a_textformat_body', $form_state->getValue('cat_a_textformat_body'))
      ->set('current_year', $form_state->getValue('current_year'))
      ->set('textformat', $form_state->getValue('textformat'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
