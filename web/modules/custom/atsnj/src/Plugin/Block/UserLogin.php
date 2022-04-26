<?php
/**
 * @file
 * Contains \Drupal\atsnj\Plugin\Block\UserLogin.
 */
namespace Drupal\atsnj\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;



/**
 * Provides a 'User Login' block.
 *
 * @Block(
 *   id = "user_login",
 *   admin_label = @Translation("User Login block"),
 *   category = @Translation("ATSNJ")
 * )
 */
class UserLogin extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    $array = array(
      '#type' => 'markup',
      '#theme' => 'user_login',
    );

    if (\Drupal::currentUser()->isAnonymous()) {
      // Anonymous user...
      $array['#content'] = [
        '#type' => 'processed_text',
        '#text' => isset($config['body_logged_out']['value']) ? $config['body_logged_out']['value'] : '<p>Please click the link below to login to your NATA account. You will be re-directed back to this site upon a successful authentication.</p>
<p><a class="button-style cas-login-link" data-drupal-selector="edit-cas-login-link" href="/caslogin?target=[current-page:url:path]" id="edit-cas-login-link">Login</a></p>',
        '#format' => isset($config['body_logged_out']['format']) ? $config['body_logged_out']['format'] : 'full_html_token_media_embed',
      ];
    }
    else {
      // Authenticated user...
      $array['#content'] = [
        '#type' => 'processed_text',
        '#text' => isset($config['body_logged_in']['value']) ? $config['body_logged_in']['value'] : '<p>Welcome back, [current-user:account-name]</p>
<p><a class="button-style" data-drupal-link-system-path="user/logout" href="/user/logout">Log out</a></p>',
        '#format' => isset($config['body_logged_in']['format']) ? $config['body_logged_in']['format'] : 'full_html_token_media_embed',
      ];
    }

    return $array;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['body_logged_in'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Body for Logged In'),
      '#format' => isset($config['body_logged_in']['format']) ? $config['body_logged_in']['format'] : 'full_html_token_media_embed',
      '#default_value' => isset($config['body_logged_in']['value']) ? $config['body_logged_in']['value'] : '<p>Welcome back, [current-user:account-name]</p>
<p><a class="button-style" data-drupal-link-system-path="user/logout" href="/user/logout">Log out</a></p>',
    ];
    $form['body_logged_out'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Body for Logged Out'),
      '#format' => isset($config['body_logged_out']['format']) ? $config['body_logged_out']['format'] : 'full_html_token_media_embed',
      '#default_value' => isset($config['body_logged_out']['value']) ? $config['body_logged_out']['value'] : '<p>Please click the link below to login to your NATA account. You will be re-directed back to this site upon a successful authentication.</p>
<p><a class="button-style cas-login-link" data-drupal-selector="edit-cas-login-link" href="/caslogin?target=[current-page:url:path]" id="edit-cas-login-link">Login</a></p>'
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