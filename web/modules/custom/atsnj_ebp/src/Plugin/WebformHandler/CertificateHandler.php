<?php
namespace Drupal\atsnj_ebp\Plugin\WebformHandler;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\webform\Element\WebformHtmlEditor;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform\WebformTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\message\Entity\Message;
use Drupal\media\Entity\Media;



/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "certificate_handler",
 *   label = @Translation("Email EBP Cert"),
 *   category = @Translation("ATSNJ Custom"),
 *   description = @Translation("Evaluation form handler that emails a Certificate"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class CertificateHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    if ($_POST['op'] == 'Submit') {
      // Get an array of the values from the submission.
      $values = $webform_submission->getData();

      // Find attendee by email & session ID
      $email = $values['attendee_email'];
      $webform = $webform_submission->getWebform();
      $webform_id = $webform->id();
      // Get the reverse reference to the session
      $ebp_session_id = _atsnj_ebp_get_session_id_from_evaluation($webform_id);

      $attendee = _atsnj_load_attendees_by_email($email, $ebp_session_id);
      // load the certs for this user
      // Send the cert!!
      // Send email(s)
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'atsnj_ebp';
      $key = 'send_certificate_mail';
      $to = $email;
      $params = [];
      $params['session_id'] = $ebp_session_id;

      $cert = FALSE;

      // Load the Cert
      $cert_ids = $attendee->get('field_certificate_reference')->getValue();
      foreach ($cert_ids as  $cert_id) {
        $temp_cert = Media::load($cert_id['target_id']);
        if ($temp_cert->get('field_ebp_session_reference')->getString() == $ebp_session_id) {
          $cert = $temp_cert;
          break;
        }
      }

      if (!$cert) {
        // No cert, lets make one in real-time

        _atsnj_bulk_actions_make_certs($attendee);

        // Load the Cert
        $cert_ids = $attendee->get('field_certificate_reference')->getValue();
        foreach ($cert_ids as  $cert_id) {
          $temp_cert = Media::load($cert_id['target_id']);
          if ($temp_cert->get('field_ebp_session_reference')->getString() == $ebp_session_id) {
            $cert = $temp_cert;
            break;
          }
        }

      }

      // If still not cert (not on prod)
      if (!$cert) {
        \Drupal::messenger()->addMessage(t('Certificate was not made. Please note that certs can only be made on production.'), 'error');
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

      // TODO: Add internal messages

      return true;
    }


  }
}

