<?php

namespace Drupal\atsnj_nominations\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'entity reference ID' formatter.
 *
 * @FieldFormatter(
 *   id = "atsnj_remaining_webform",
 *   label = @Translation("Remaining Webforms"),
 *   description = @Translation("Webform title and resend link."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class AtsnjRemainingWebformFormatter extends EntityReferenceFormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'separator' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['separator'] = [
      '#title' => t('Separator For title and resend link'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('separator'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->getSetting('separator') ? 'Separator : ' . $this->getSetting('separator') : t('No Separator');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = [];
    global $base_url;
    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $uri = $entity->urlInfo();
      $internal_path = $uri->getInternalPath();
      $label = $entity->label();
      $id = $entity->id();
      $node = _get_current_controller_entity();
      if ($node) {
        $nid = $node->id();
        $elements[$delta] = ['#markup' => $label . $this->getSetting('separator') . '<a href="/admin/atsnj_nominations/resend?destination='. \Drupal::destination()->get() .'&webform_id='. $id .'&nid='. $nid .'">Re-send CTA email</a>'];
      }
      else {
        $elements[$delta] = ['#markup' => $label];
      }

    }
    return $elements;
  }

}
