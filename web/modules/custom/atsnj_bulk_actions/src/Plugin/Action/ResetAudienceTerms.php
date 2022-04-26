<?php

namespace Drupal\atsnj_bulk_actions\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsPreconfigurationInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Action description.
 *
 * @Action(
 *   id = "reset_audience_terms",
 *   label = @Translation("Reset the audience terms of a node"),
 *   type = "node",
 * )
 */
class ResetAudienceTerms extends ViewsBulkOperationsActionBase implements ViewsBulkOperationsPreconfigurationInterface, PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL)
  {
    /*
     * All config resides in $this->configuration.
     * Passed view rows will be available in $this->context.
     * Data about the view used to select results and optionally
     * the batch context are available in $this->context or externally
     * through the public getContext() method.
     * The entire ViewExecutable object  with selected result
     * rows is available in $this->view or externally through
     * the public getView() method.
     */

    // Do some processing..
    $config = $this->configuration;
    if ($config['erase']) {
      if ($entity->hasField('field_audience')) {
        $entity->set('field_audience', NULL);
        $entity->save();
      }
    }


    if (count($config['add'])) {

      if ($entity->hasField('field_audience')) {
        foreach ($config['add'] as $tid) {
          if ($tid) {
            $entity->field_audience[] = $tid;
          }
        }
        $entity->save();
      }
    }

    //return sprintf('Example action (configuration: %s)', print_r($this->configuration, TRUE));
  }

  /**
   * {@inheritdoc}
   */
  public function buildPreConfigurationForm(array $form, array $values, FormStateInterface $form_state) {
    $form['erase'] = [
      '#title' => $this->t('Erase all existing Audience terms'),
      '#type' => 'checkbox',
      '#default_value' => '',
      '#description' => $this->t('Check this box to clear all Audience terms from this node.'),
    ];
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', "audience");
    $tids = $query->execute();
    $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);

    $i = 0;
    $options = array();
    foreach ($terms as $term) {
      $name = $term->toLink()->getText();
      if (isset($tids[$i])) {
        $tid = $tids[$i];
        $options[$tid] = $name;
      }
      $i++;
    }
    $form['add'] = [
      '#title' => $this->t('Add these Audience terms to the node'),
      '#type' => 'checkboxes',
      '#options' => $options,
    ];
    return $form;
  }

  /**
   * Configuration form builder.
   *
   * If this method has implementation, the action is
   * considered to be configurable.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return array
   *   The configuration form.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['erase'] = [
      '#title' => $this->t('Erase all existing Audience terms'),
      '#type' => 'checkbox',
      '#default_value' => $form_state->getValue('erase'),
    ];
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', "audience");
    $tids = $query->execute();
    $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
    $options = [];
    foreach ($terms as $tid => $term) {
      $name = $term->toLink()->getText();
      $options[$tid] = $name;
    }
    $form['add'] = [
      '#title' => $this->t('Add these Audience terms to the node'),
      '#type' => 'checkboxes',
      '#default_value' => $form_state->getValue('add'),
      '#options' => $options,
    ];

    return $form;
  }

  /**
   * Submit handler for the action configuration form.
   *
   * If not implemented, the cleaned form values will be
   * passed direclty to the action $configuration parameter.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // This is not required here, when this method is not defined,
    // form values are assigned to the action configuration by default.
    // This function is a must only when user input processing is needed.
    $this->configuration['erase'] = $form_state->getValue('erase');
    $this->configuration['add'] = $form_state->getValue('add');
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType() === 'node') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    // Other entity types may have different
    // access methods and properties.
    return TRUE;
  }

}
