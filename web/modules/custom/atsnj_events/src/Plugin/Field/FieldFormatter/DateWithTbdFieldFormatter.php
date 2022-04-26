<?php

namespace Drupal\atsnj_events\Plugin\Field\FieldFormatter;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\datetime\Plugin\Field\FieldFormatter\DateTimeFormatterBase;


/**
 * Plugin implementation of the 'Datetime with TDB' formatter for 'datetime' fields.
 *
 * @FieldFormatter(
 *   id = "date_with_tbd",
 *   label = @Translation("ATSNJ: Date with TBD support"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class DateWithTbdFieldFormatter extends DateTimeFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'format_type' => 'medium',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  protected function formatDate($date, $date_only = FALSE) {
    if ($date_only) {
      $format_type = 'default_medium_date_w_o_time';
    }
    else {
      $format_type = $this->getSetting('format_type');
    }
    $timezone = $this->getSetting('timezone_override') ?: $date->getTimezone()->getName();
    return $this->dateFormatter->format($date->getTimestamp(), $format_type, '', $timezone != '' ? $timezone : NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $time = new DrupalDateTime();
    $format_types = $this->dateFormatStorage->loadMultiple();
    $options = [];
    foreach ($format_types as $type => $type_info) {
      $format = $this->dateFormatter->format($time->getTimestamp(), $type);
      $options[$type] = $type_info->label() . ' (' . $format . ')';
    }

    $form['format_type'] = [
      '#type' => 'select',
      '#title' => t('DateTime format'),
      '#description' => t("Choose a format for displaying the date & time."),
      '#options' => $options,
      '#default_value' => $this->getSetting('format_type'),
    ];
    $form['format_type_2'] = [
      '#type' => 'select',
      '#title' => t('Date format'),
      '#description' => t("Choose a format for displaying the date without the time (in cases where the <em>Time TBD or N/A option is chosen.</em>"),
      '#options' => $options,
      '#default_value' => $this->getSetting('format_type_2'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $date = new DrupalDateTime();
    $summary[] = t('Format: @display', ['@display' => $this->formatDate($date)]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // @todo Evaluate removing this method in
    // https://www.drupal.org/node/2793143 to determine if the behavior and
    // markup in the base class implementation can be used instead.
    $elements = [];

    foreach ($items as $delta => $item) {

      if (!empty($item->date)) {
        /** @var \Drupal\Core\Datetime\DrupalDateTime $date */
        $date = $item->date;

        /** @var \Drupal\node\NodeInterface $node */
        $node = $item->getEntity();
        // Check for event node.
        if ('event_v2' == $node->bundle()){
          // Is the date TDB?
          $tdb = $node->get('field_event_date_tbd')->getString();
          if ($tdb) {
            $build = [
              '#markup' => 'To be Determined', //$this->formatDate($date),
              '#cache' => [
                'contexts' => [
                  'timezone',
                ],
              ],
            ];
            $elements[$delta] = $build;
            continue;
          }
          else  {
            // Is the time TDB?
            // We nest this in an else because it's not worth checking if the entire date is TBD
            $time_tdb = $node->get('field_time_tbd_or_n_a')->getString();
            if ($time_tdb) {
              $build = [
                '#markup' => $this->formatDate($date, TRUE),
                '#cache' => [
                  'contexts' => [
                    'timezone',
                  ],
                ],
              ];
              $elements[$delta] = $build;
              continue;
            }
          }
        }
      }

      $elements[$delta] = $this->buildDate($date);
    }

    return $elements;
  }

}


