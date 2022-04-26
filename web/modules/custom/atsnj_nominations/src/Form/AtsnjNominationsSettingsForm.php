<?php

namespace Drupal\atsnj_nominations\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Configure atsnj_nominations settings for this site.
 */
class AtsnjNominationsSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atsnj_nominations_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'atsnj_nominations.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('atsnj_nominations.settings');

    $form['from'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('From'),
      '#default_value' => $config->get('from'),
    );
    $form['signature'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Signature'),
      '#format' => $config->get('signature') ? $config->get('signature')['format'] : 'full_html',
      '#default_value' => $config->get('signature') ? $config->get('signature')['value'] : '<p>Karen E. Manista, ATC<br>Summit High School 125 Kent Place Blvd.<br>Summit, NJ 07901<br>908-273-1494 X5466<br>732-513-8613 (c)<br>ATSNJ Honors and Awards Chair<br>NATA: Health Care for Life and Sport</p>',
    ];
    $form['disable_emails'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Disable emails'),
      '#default_value' => $config->get('disable_emails'),
      '#description' => $this->t('Disables emails which is useful during testing.'),
    );
    $vid = 'conference_year';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    $term_data[0] = 'None';
    foreach ($terms as $term) {
      $term_data[$term->tid] = $term->name;
    }
    $form['year'] = array(
      '#type' => 'select',
      '#title' => 'Which year do incoming nominations get tagged with?',
      '#options' => $term_data,
      '#default_value' => $config->get('year'),

    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    $this->configFactory->getEditable('atsnj_nominations.settings')
      // Set the submitted configuration setting
      ->set('from', $form_state->getValue('from'))
      // You can set multiple configurations at once by making
      // multiple calls to set()
      ->set('signature', $form_state->getValue('signature'))
      ->set('disable_emails', $form_state->getValue('disable_emails'))
      ->set('year', $form_state->getValue('year'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}