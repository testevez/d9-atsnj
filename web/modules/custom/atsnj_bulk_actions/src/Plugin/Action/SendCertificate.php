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
 *   id = "send_certificate",
 *   label = @Translation("Sends all the certificates for an EBP Attendee"),
 *   type = "node",
 * )
 */
class SendCertificate extends ViewsBulkOperationsActionBase {

  use StringTranslationTrait;

  public $settings;

  public $request;

  public $filesystem;

  /**
   * {@inheritdoc}
   */
  public function execute($attendee = NULL) {

    // Set up our mail params
    $email = $attendee->get('field_email')->getValue();
    $email = current($email);

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'atsnj_ebp';
    $key = 'send_certificate_mail';
    $to = $email['value'];
    $params = [];

    // Load the Certs
    $cert_ids = $attendee->get('field_certificate_reference')->getValue();
    foreach ($cert_ids as  $cert_id) {
      $attachments = [];

      $cert = Media::load($cert_id['target_id']);

      if (!$cert) {
        \Drupal::messenger()->addMessage(t('Error.'), 'error');
      }
      else {
        // Add the PDF
        $file_id = $cert->get('field_media_file')->getString();

        if ($file_id) {
          $file = \Drupal\file\Entity\File::load($file_id);
          $uri = $file->getFileUri();
          $attachments[] = $uri;
        }

        $params['attachments'] = $attachments;
        $params['session_id'] = $cert->get('field_ebp_session_reference')->getString();
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = TRUE;

        $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
        if ($result['result'] !== TRUE) {
          \Drupal::messenger()->addMessage(t('There was a problem sending your certificate and it was not sent.'), 'error');
        }
        else {
          \Drupal::messenger()->addMessage(t('Your Certificate has been sent to you via email.'));

        }
      }
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
