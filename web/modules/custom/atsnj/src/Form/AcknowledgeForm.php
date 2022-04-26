<?php

namespace Drupal\atsnj\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure atsnj_ebp settings for this site.
 */
class AcknowledgeForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atsnj_acknowledge_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'atsnj.acknowledge_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('atsnj.acknowledge_settings');

    $format = $config->get('atsnj_acknowledge_copy')['format'];
    $value = $config->get('atsnj_acknowledge_copy')['value'];

    $form['atsnj_acknowledge_copy'] = [
      '#type' => 'text_format',
      '#required' => TRUE,
      '#title' => $this->t('Acknowledge Copy'),
      '#base_type' => 'textarea',
      '#format' => isset($format) ? $format : 'full_html',
      '#default_value' => isset($value) ? $value : ATSNJ_DEFAULT_ACKNOWLEDGE_COPY,
    ];

    //$format = $config->get('atsnj_acknowledge_button_copy')['format'];
    $value = $config->get('atsnj_acknowledge_button_copy');

    $form['atsnj_acknowledge_button_copy'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Acknowledge Button Copy'),
      '#default_value' => isset($value) ? $value : ATSNJ_DEFAULT_ACKNOWLEDGE_BUTTON_COPY,
      '#description' => $this->t('Only seen on the popup modal.')
    ];
/*
    $value = $config->get('atsnj_modal_header_copy');
    $form['atsnj_modal_header_copy'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Modal Header Text'),
      '#default_value' => isset($value) ? $value : ATSNJ_DEFAULT_MODAL_HEADER_COPY,
      '#description' => $this->t('Only seen on the popup modal.')
    ];
*/
    $value = $config->get('atsnj_details_header_copy');
    $form['atsnj_details_header_copy'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Details Header Text'),
      '#default_value' => isset($value) ? $value : ATSNJ_DEFAULT_DETAILS_HEADER_COPY,
      '#description' => $this->t('Only seen on the Block.')
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->configFactory->getEditable('atsnj.acknowledge_settings')
      // Set the submitted configuration setting
      ->set('atsnj_acknowledge_copy', $form_state->getValue('atsnj_acknowledge_copy'))
      ->set('atsnj_acknowledge_button_copy', $form_state->getValue('atsnj_acknowledge_button_copy'))
      ->set('atsnj_details_header_copy', $form_state->getValue('atsnj_details_header_copy'))
      ->set('atsnj_modal_header_copy', $form_state->getValue('atsnj_modal_header_copy'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}