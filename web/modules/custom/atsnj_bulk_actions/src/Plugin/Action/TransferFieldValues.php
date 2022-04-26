<?php

namespace Drupal\atsnj_bulk_actions\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsPreconfigurationInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormState;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\media\Entity\Media;
use Drupal\media\Entity\MediaType;


/**
 * Action description.
 *
 * @Action(
 *   id = "tansfer_field_value",
 *   label = @Translation("Transfer the value from one field to another"),
 *   type = "media",
 * )
 */
class TransferFieldValues extends ViewsBulkOperationsActionBase implements ViewsBulkOperationsPreconfigurationInterface, PluginFormInterface {

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

    $before = $config['from']; //'field_media_file_1';
    $after = $config['to']; //field_file_private';


    if ($entity->hasField($before) && $entity->hasField($after) ) {
      $value1 = $entity->get($before)->getValue();
      $value = current($value1);

      $id = $value['target_id'];
      $file = \Drupal\file\Entity\File::load($id);
      $entity->{$after}->entity = $file ;

      if ($config['erase']) {
        $entity->{$before}->entity = NULL ;

      }
      $entity->save();
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildPreConfigurationForm(array $form, array $values, FormStateInterface $form_state) {
    $form['from'] = [
      '#title' => $this->t('From'),
      '#type' => 'textfield',
      '#default_value' => 'field_media_file_1',
      '#description' => $this->t('The machine name of the field to extract the value from.'),
    ];
    $form['to'] = [
      '#title' => $this->t('To'),
      '#type' => 'textfield',
      '#default_value' => 'field_file_private',
      '#description' => $this->t('The machine name of the field to set the value to.'),
    ];
    $form['erase'] = [
      '#title' => $this->t('Erase <em>from</em> field value'),
      '#type' => 'checkbox',
      '#default_value' => TRUE,
      '#description' => $this->t('Check this box to clear all Audience terms from this node.'),
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
    $form['from'] = [
      '#title' => $this->t('From'),
      '#type' => 'textfield',
      '#default_value' => 'field_media_file_1',
      '#description' => $this->t('The machine name of the field to extract the value from.'),
    ];
    $form['to'] = [
      '#title' => $this->t('To'),
      '#type' => 'textfield',
      '#default_value' => 'field_file_private',
      '#description' => $this->t('The machine name of the field to set the value to.'),
    ];
    $form['erase'] = [
      '#title' => $this->t('Erase <em>from</em> field value'),
      '#type' => 'checkbox',
      '#default_value' => TRUE,
      '#description' => $this->t('Check this box to clear all Audience terms from this node.'),
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
    $this->configuration['from'] = $form_state->getValue('from');
    $this->configuration['to'] = $form_state->getValue('to');

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
