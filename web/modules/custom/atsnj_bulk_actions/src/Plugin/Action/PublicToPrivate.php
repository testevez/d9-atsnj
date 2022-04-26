<?php

namespace Drupal\atsnj_bulk_actions\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\Entity\User;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config;
use Drupal\Core\ConfigFactory;
use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormStateInterface;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\File\FileSystem;
use Drupal\media\Entity\Media;


/**
 * Action description.
 *
 * @Action(
 *   id = "public_to_private",
 *   label = @Translation("Transforms File to File, private"),
 *   type = "media",
 * )
 */
class PublicToPrivate extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  public $settings;

  public $request;

  public $filesystem;

  /**
   * {@inheritdoc}
   */
  public function execute($media = NULL) {

    if ($media->bundle() == 'file') {
      // Grab the name
      $name = $media->getName();
      // Grab the file
      $file = $media->get('field_media_file')->getValue();
      // Grab the category
      $cat = $media->get('field_media_category');

      // Create new media and assign file to it
      $new_media = Media::create([
        'bundle' => 'media_type_file_private',
        'name' => $name,
        'field_file_private' => $file,
        'field_media_category' => $cat,
      ]);
      $new_media->save();
      $media->delete();
    }
    else {
      \Drupal::messenger()->addMessage('This entity is not eligible to be made private.');
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
