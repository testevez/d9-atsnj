<?php
/**
 * @file
 * Contains \Drupal\atsnj_nominations\Form\AtsnjResendEmailForm.
 */
namespace Drupal\atsnj_admin\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\flag\FlagInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\flag\Ajax\ActionLinkFlashCommand;
use Drupal\flag\FlagServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Html;


class AtsnjMarkAllReadForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atsnj_mark_all_read_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $parameter = NULL) {

    $user_id = $parameter['user_id'];
    $flag_id = $parameter['flag_id'];



    // Current User.
    $current_user = \Drupal::currentUser();
    // Load the account.
    /** @var User $account */
    $account = User::load($user_id);

    $form['headline'] = array(
      '#markup'  => t('<h1>Mark All Messages as <em>Seen</em></h1>'),
    );

    if ($current_user->id() == $user_id) {
      $form['question'] = array(
        '#markup'  => t('<p>Are you sure you want to mark all your messages <em>Seen</em>?</p>'),
      );
    }
    else {
      $form['question'] = array(
        '#markup'  => t('<p>Are you sure you want to mark all messages for this user as <em>Seen</em>?</p>'),
      );
    }

    $form['flag_id'] = array(
      '#type' => 'hidden',
      '#title' => t('Webform ID'),
      '#required' => TRUE,
      '#default_value' => $flag_id,
    );
    $form['user_id'] = array (
      '#type' => 'hidden',
      '#title' => t('Webform ID'),
      '#required' => TRUE,
      '#default_value' => $user_id,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Mark all as <em>Seen</em>'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $user_id = $form_state->getValue('user_id');
    $flag_id = $form_state->getValue('flag_id');

    if (!$user_id || !$flag_id) {
      $form_state->setErrorByName('question', $this->t('Please try again.'));
    }

    // Current User.
    $current_user = \Drupal::currentUser();
    // Load the account.
    /** @var User $account */
    $account = User::load($user_id);
    $condition_1 = $current_user->hasPermission('mark all messages read for any user');
    $condition_2 =  $current_user->hasPermission('mark all my flags read') && ($current_user->id() == $account->id());

    if (!($condition_1 || $condition_2)) {
      $form_state->setErrorByName('question', $this->t('You do not have sufficient permission.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $user_id = $form_state->getValue('user_id');
    $flag_id = $form_state->getValue('flag_id');

    $count =  _atsnj_admin_bulk_flag($user_id, $flag_id);

  }

}
