<?php

namespace Drupal\atsnj_committee\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Link;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\group\Entity\GroupInterface;
use Drupal\node\NodeStorageInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\media\Entity\Media;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Upload Minutes file, create media entity and assign to group in one simple step
 */
class UploadCommitteeMinutes extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atsnj_committee_upload_minutes';
  }

  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, GroupInterface $group = NULL) {

    $form = array(
      '#attributes' => array('enctype' => 'multipart/form-data'),
    );
    $markup =  '';
    $markup .= '<h2>Instructions</h2>';
    $markup .= '<p>Use this form to add minutes to this committee.</p>';
    $markup .= '<ol>';
    $markup .= '<li>Use the file field to locate the minutes file on your machine</li>';
    $markup .= '<li>Use the label field to label the minutes</li>';
    $markup .= '<li>Submit form; you will be redirected to the minutes administration table for this committee</li>';
    $markup .= '<li>Note: Minutes are published by default</li>';
    $markup .= '<li>Re-order the minutes as needed; remember that only the first five will appear in the block</li>';

    $form['file_upload_details'] = array(
      '#markup' => t($markup),
    );

    $gid = $group->id();

    $validators = array(
      'file_validate_extensions' => array('pdf', 'txt', 'doc', 'docx'), // Taken from the file settings for the media entity form for minutes
    );
    $form['my_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'my_file',
      '#title' => t('File *'),
      '#size' => 20,
      '#description' => t('Upload the minutes for this committee.'),
      '#upload_validators' => $validators,
      '#upload_location' => 'private://',
    );
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Minutes Label'),
      '#description' => t('e.g., 2019 Annual Business Meeting Minutes'),
    );
    $form['group_id'] = array(
      '#type' => 'hidden',
      '#value' => $gid,
    );


    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Upload File'),
      '#button_type' => 'primary',
    );

    $url = Url::fromRoute('atsnj_committee.group.minutes_admin')->setRouteParameter('group', $gid);
    $form_state->setRedirectUrl($url);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('my_file') == NULL) {
      $form_state->setErrorByName('my_file', $this->t('You need to supply a file.'));
    }
  }

  /**
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   * @param \Drupal\group\Entity\GroupInterface $group
   *   The group.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Need to get file details i.e upload file name, size etc.

    //dpm($form_state->getValue('my_file'));

    // TODO: Display success message.


    // In this variable you will have file entity
    $file = \Drupal::entityTypeManager()->getStorage('file')
      ->load($form_state->getValue('my_file')[0]); // Just FYI. The file id will be stored as an array
    // And you can access every field you need via standard method
    $group_id = $form_state->getValue('group_id');

    /** @var \Drupal\group\Entity\GroupInterface $group */
    $group = \Drupal::entityTypeManager()->getStorage('group')->load($group_id);

    $label = $form_state->getValue('label');


    $media = Media::create([
      'bundle' => 'minutes',
      'name' => $label,
      'field_file_private' => [
        'target_id' => $file->id(),
      ],
    ]);
    $media->save();

    // Assign media to Group
    $instance_id = 'group_media:minutes';
    $group->addContent($media, $instance_id);
    $url = Url::fromRoute('atsnj_committee.group.minutes_admin')->setRouteParameter('group', $group_id);
    $form_state->setRedirectUrl($url);
    \Drupal::messenger()->addMessage('Your minutes have been added to the committee');

    return $this->redirect('atsnj_committee.group.minutes_admin', ['group' => $group_id]);

  }

}
