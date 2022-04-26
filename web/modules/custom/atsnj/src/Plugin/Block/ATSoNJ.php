<?php
/**
 * @file
 * Contains \Drupal\atsnj\Plugin\Block\ATSoNJ.
 */
namespace Drupal\atsnj\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;



/**
 * Provides a 'Registration' block.
 *
 * @Block(
 *   id = "atsonj",
 *   admin_label = @Translation("AT's Society of NJ State Meeting block"),
 *   category = @Translation("ATSNJ")
 * )
 */
class ATSoNJ extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    $array = array(
      '#theme' => 'atsonj',
    );

    if (\Drupal::currentUser()->isAnonymous()) {

      //$array['#form'] = openid_connect_client_generic_login

      // Anonymous user...
      $array['#content'] = [
        '#type' => 'processed_text',
        '#text' => isset($config['body_logged_out']['value']) ? $config['body_logged_out']['value'] : '
<h5>Join us…</h5>

<p><span class="date">January 9, 2021 @ 3:30PM</span> for the Athletic Trainers’ Society of New Jersey State Meeting at the VIRTUAL Annual EATA Convention.<span class="cta"> ATSNJ members must login below to register for this event.</span></p>

',
        '#format' => isset($config['body_logged_out']['format']) ? $config['body_logged_out']['format'] : 'full_html_token_media_embed',
      ];
    }
    else {
      // Authenticated user...
      $array['#content'] = [
        '#type' => 'processed_text',
        '#text' => isset($config['body_logged_in']['value']) ? $config['body_logged_in']['value'] : '
<h5>Join us…</h5>
<p><span class="date">January 9, 2021 @ 3:30PM</span>
for the Athletic Trainers’ Society of New Jersey State Meeting at the VIRTUAL Annual EATA Convention.<span class="cta">
<a href="https://us02web.zoom.us/webinar/register/WN_B1taf2YES36J6Nt0b1ZiYQ">REGISTER HERE</a></span></p>
',
        '#format' => isset($config['body_logged_in']['format']) ? $config['body_logged_in']['format'] : 'full_html_token_media_embed',
      ];
    }
   // return $this->formBuilder->getForm('Drupal\openid_connect\Form\LoginForm');
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
      '#default_value' => isset($config['body_logged_in']['value']) ? $config['body_logged_in']['value'] : '<h5>Join us…</h5>
<p><span class="date">January 9, 2021 @ 3:30PM</span>
for the Athletic Trainers’ Society of New Jersey State Meeting at the VIRTUAL Annual EATA Convention.<span class="cta">
<a href="https://us02web.zoom.us/webinar/register/WN_B1taf2YES36J6Nt0b1ZiYQ">REGISTER HERE</a></span></p>',
    ];
    $form['body_logged_out'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Body for Logged Out'),
      '#format' => isset($config['body_logged_out']['format']) ? $config['body_logged_out']['format'] : 'full_html_token_media_embed',
      '#default_value' => isset($config['body_logged_out']['value']) ? $config['body_logged_out']['value'] : '
<h5>Join us…</h5>

<p><span class="date">January 9, 2021 @ 3:30PM</span> for the Athletic Trainers’ Society of New Jersey State Meeting at the VIRTUAL Annual EATA Convention.<span class="cta"> ATSNJ members must <a href="#" onclick="jQuery(\'#edit-atsnj-portal-salesforce-login\').click();">LOG-IN</a> to Register for this event.</span></p>

'
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