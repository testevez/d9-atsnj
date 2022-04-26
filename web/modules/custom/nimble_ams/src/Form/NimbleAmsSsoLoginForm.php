<?php

namespace Drupal\nimble_ams\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RouteMatch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Drupal\Core\Url;

/**
 * Implements the SimpleForm form controller.
 *
 * This example demonstrates a simple form with a singe text input element. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class NimbleAmsSsoLoginForm extends FormBase {

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
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $conference_year_id = NULL, $ebp_session_id = NULL) {


    $options = [
      'query' => [
        'response_type' => '301',

      ],
    ];
    new RedirectResponse(\Drupal\Core\Url::fromUri('https://account.nata.org/services/oauth2/authorize'), $options);
/*

    with URL parameters...
?response_type=code
    &scope=openid
    &client_id=<your client key>
&redirect_uri=<one of your callback urls>

    return $this->redirect('https://account.nata.org/services/oauth2/authorize', $route_match->getRawParameters()->all());


    $config = $this->configFactory->getEditable('nimble_ams.settings');

    $redirect_white_list = $config->get('nimble_ams_sso_login_redirect_whitelist');

    if (!empty($_GET['retUrl'])) {
      $return_url_parsed = parse_url($_GET['retUrl']);
      if (!empty($return_url_parsed['host']) && in_array($return_url_parsed['host'], $redirect_white_list)) {
        $return_url = $_GET['retUrl'];
      }
    }
    // $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id()); Not needed unless we want th entire user object
    $user = \Drupal::currentUser();
    $uid= $user->get('uid')->value;

    if ($uid) {
      if (!empty($return_url)) {
        $parsed = drupal_parse_url($_GET['retUrl']);
        drupal_set_message('drupal_goto($parsed[\'path\'], array(\'query\' => $parsed[\'query\']))');
        //drupal_goto($parsed['path'], array('query' => $parsed['query']));
      }
      else {
        $member_role = variable_get('nimble_ams_member_role', 0);
        if (!empty($member_role) && user_has_role($member_role)) {
          // member
          $auth_user_redirect_path = variable_get('nimble_ams_sso_login_auth_user_redirect_path', '');
        }
        else {
          // non member
          $auth_user_redirect_path = variable_get('nimble_ams_sso_login_auth_non_member_user_redirect_path', '');
        }
        if (!empty($auth_user_redirect_path)) {
          drupal_goto($auth_user_redirect_path);
        }
        else {
          drupal_goto('user');
        }
      }
    }
    else {

      if (!empty($return_url)) {
        $parsed = drupal_parse_url($_GET['retUrl']);
        $_SESSION['openid_connect_destination'] = [
          $parsed['path'], ['query' => $parsed['query']],
        ];
        $_SESSION['openid_connect_destination_override'] = TRUE;
      }
      else {
        openid_connect_save_destination();
      }
      // code from openid_connect.forms.inc , openid_connect_login_form_submit()
      $client = openid_connect_get_client('nimble');
      $scopes = openid_connect_get_scopes();
      $_SESSION['openid_connect_op'] = 'login';
      $client->authorize($scopes);



    }

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
*/
  }

  /**
   * Getter method for Form ID.
   *
   * The form ID is used in implementations of hook_form_alter() to allow other
   * modules to alter the render array built by this form controller. It must be
   * unique site wide. It normally starts with the providing module's name.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'atsnj_ebp_mark_attendance';
  }

  /**
   * Implements form validation.
   *
   * The validateForm method is the default method called to validate input on
   * a form.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title');
    if (strlen($title) < 5) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    }
  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
