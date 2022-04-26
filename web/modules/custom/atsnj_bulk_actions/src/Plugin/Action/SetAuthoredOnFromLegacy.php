<?php

namespace Drupal\atsnj_bulk_actions\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Action description.
 *
 * @Action(
 *   id = "set_authored_on_from_legacy",
 *   label = @Translation("Update Authored-On Date from Legacy"),
 *   type = "node",
 * )
 */
class SetAuthoredOnFromLegacy extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity->hasField('field_legacy_post_date')) {
      $value = $entity->get('field_legacy_post_date')->getValue();
      $entity->created->value = $value[0]['value'];
      $entity->save();
    }

  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {

    return TRUE;

    $result = $object->access('update', $account, TRUE)
      ->andIf($object->field_legacy_post_date->access('edit', $account, TRUE));

    return $return_as_object ? $result : $result->isAllowed();
  }

}
