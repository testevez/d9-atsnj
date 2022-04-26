<?php
/**
 * @file
 */
namespace Drupal\atsnj_ads\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\views\Views;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Landscape ads block.
 *
 * @Block(
 *   id = "landscape_ads_block",
 *   admin_label = @Translation("Landscape ads block"),
 *   category = @Translation("ATSNJ")
 * )
 */
class LandscapeAdsBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    $view = views::getview('landscape_ads');
    if ($view) {
      $view->execute();
      foreach ($view->result as $rid => $row) {
        foreach ($view->field as $fid => $field ) {
          $items[$rid][$fid . '_value'] = $field->getValue($row);
          if (in_array($fid, array('field_landscape_ad_image', 'field_sponsorship_badge'))) {
            $items[$rid][$fid . '_render'] = $field->advancedRender($view->result[$rid]);
          }
        }
      }
    }
    $variables = array(
      '#type' => 'markup',
      '#theme' => 'landscape_ads_block',
      '#items' => $items,
      '#attached' => array(
        'library' => array('atsnj_ads/landscape_ads_block'),
      ),
    );
    $variables['#attached']['drupalSettings']['atsnj_ads']['landscapeAdsBlock']['interval'] = isset($config['interval']) ? $config['interval'] : '2500';
    return $variables;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['interval'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Time Interval'),
      '#default_value' => isset($config['interval']) ? $config['interval'] : '2500',
      '#description' => $this->t('The number of milliseconds to wait between switching ads (2500 is 2.5 seconds).'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration = $values;
  }

}
