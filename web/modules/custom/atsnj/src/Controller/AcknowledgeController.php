<?php
/**
 * @file
 * Contains \Drupal\atsnj\Controller\AtsnjController.
 */

namespace Drupal\atsnj\Controller;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\user\Entity\User;
use Drupal\Core\Config;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\comment\Entity\Comment;
use Drupal\message\Entity\Message;
use Drupal\taxonomy\Entity\Term;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\image\Entity\ImageStyle;


class AcknowledgeController {

  /**
   * Display the markup.
   *
   * @return array
   */
  public function modal() {

    $config = \Drupal::config('atsnj.acknowledge_settings');

    $format = $config->get('atsnj_acknowledge_copy')['format'];
    $value = $config->get('atsnj_acknowledge_copy')['value'];
    $element = array(
      '#type' => 'processed_text',
      '#text' => $value,
      '#format' => $format,
    );
    $copy = render($element);
    $part1 = (isset($copy) ? $copy : ATSNJ_DEFAULT_ACKNOWLEDGE_BUTTON_COPY);

    // Button.
    $value = $config->get('atsnj_acknowledge_button_copy');
    $button_copy =  isset($value) ? $value : ATSNJ_DEFAULT_ACKNOWLEDGE_BUTTON_COPY;
    $part2 = '<p><a class="button-style">'. $button_copy .'</a></p>';

    return array(
      '#type' => 'markup',
      '#markup' => $part1 . $part2,
    );
  }

}