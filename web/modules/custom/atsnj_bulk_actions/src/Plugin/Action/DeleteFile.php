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
 *   id = "delete_file",
 *   label = @Translation("Deletes File"),
 *   type = "file",
 * )
 */
class DeleteFile extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  public $settings;

  public $request;

  public $filesystem;

  /**
   * {@inheritdoc}
   */
  public function execute($file = NULL) {

    if ($file->id()) {
      $file->delete();
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
