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
use Drupal\Core\Database\Database;



/**
 * Action description.
 *
 * @Action(
 *   id = "node_to_media",
 *   label = @Translation("Transforms Minute Nodes to Minute Media"),
 *   type = "node",
 * )
 */
class NodeToMedia extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  public $settings;

  public $request;

  public $filesystem;

  /**
   * {@inheritdoc}
   *
   *  @param \Drupal\node\NodeInterface $node
   */
  public function execute(NodeInterface $node = NULL) {

    if ($node->bundle() == 'minute') {
      // Grab the nid
      $nid = $node->id();
      // Grab the title
      $name = $node->getTitle();
      // Grab the file
      $file = $node->get('field_file_private')->getValue();
      // Grab the group
      $gid = FALSE;
      $database = \Drupal::database();
      $query = $database->select('group_content_field_data', 'gc');
      $query->condition('gc.entity_id', $nid, '=');
      $query->condition('gc.type', 'committee-group_node-minute', '=');
      $query->range(0, 1);
      $query->fields('gc', ['gid']);
      $result = $query->execute();
      foreach ($result as $record) {
        $gid = $record->gid;
      }
      // Create new media and assign file to it
      $new_media = Media::create([
        'bundle' => 'minutes',
        'name' => $name,
        'field_file_private' => $file,
        //'field_media_category' => $cat,
      ]);
      $new_media->save();
      if ($gid) {
        $group = \Drupal\group\Entity\Group::load($gid);
        $plugin_id = 'group_media:minutes';
        $group->addContent($new_media, $plugin_id);
      }
      $node->delete();
    }
    else {
      \Drupal::messenger()->addMessage('This node is not eligible to be made into a media minute.');
    }

  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {

    return TRUE;

  }

}
