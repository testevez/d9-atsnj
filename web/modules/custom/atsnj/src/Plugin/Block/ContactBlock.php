<?php
/**
 * @file
 * Contains \Drupal\atsnj_admin\Plugin\Block\ContactBlock.
 */
namespace Drupal\atsnj\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;



/**
 * Provides a 'Contact Info' block.
 *
 * @Block(
 *   id = "contact_block",
 *   admin_label = @Translation("Contact Info block"),
 *   category = @Translation("ATSNJ")
 * )
 */
class ContactBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    return array(
        '#type' => 'processed_text',
        '#text' => isset($config['body']['value']) ? $config['body']['value'] : '<h3>ATSNJ, Inc.</h3>
<p>224 West State Street <br />
Trenton NJ 08608<br />
Phone: 973-55-ATSNJ  (973-552-8765)</p>
<p><a href="/contact-us">Contact Us</a>',
        '#format' => isset($config['body']['format']) ? $config['body']['format'] : 'full_html',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['body'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Body'),
      '#format' => isset($config['body']['format']) ? $config['body']['format'] : 'full_html',
      '#default_value' => isset($config['body']['value']) ? $config['body']['value'] : '<h3>ATSNJ, Inc.</h3>
<p>224 West State Street <br />
Trenton NJ 08608<br />
Phone: 973-55-ATSNJ  (973-552-8765)</p>
<p><a href="/contact-us">Contact Us</a>'
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