<?php
/**
 * @file
 * Contains \Drupal\atsnj_committee\Plugin\Block\CommitteeMembersBlock.
 */
namespace Drupal\atsnj_committee\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\group\Entity\GroupContent;
use Drupal\group\Entity\GroupInterface;
use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Committee Members' block.
 *
 * @Block(
 *   id = "me",
 *   admin_label = @Translation("Committee Members block"),
 *   category = @Translation("ATSNJ")
 * )
 */
class CommitteeMembersBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $markup = '';
    $config = $this->getConfiguration();

    $current_route = \Drupal::routeMatch();
    $group = $current_route->getParameters()->get('group');

    if (!isset($group) || !$group || is_string($group)) {
      $group_id = $current_route->getParameters()->get('group_id');
      if ($group_id) {
        $group = \Drupal\group\Entity\Group::load($group_id);
      }
    }

    if (!isset($group) || !$group || $group == NULL || !is_object($group)) {
      return [];
    }

    $account = \Drupal::currentUser();

    $user_storage = \Drupal::entityTypeManager()->getStorage('user');
    $user_entity = $user_storage->load($account->id());

    $membership = $group->getMember($account);

    // Load the user view mode
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('user');
    $build = $view_builder->view($user_entity, 'token');
    $name = trim(strip_tags(render($build)));

    if ($membership) {
      // For Members.
      //$markup .= '<p>Welcome to the <strong>'. $group->label() .'</strong> committee. You are not a member of this committee.</p>';
      $roles = $membership->getRoles();
      if (array_key_exists('committee-admin', $roles) || $account->id() === 1 ) {
        // For members that are not admins.
        $markup .= '<p>Welcome back, '. $name .'.</p><p>You are an <em>administrator</em> of the <strong>'. $group->label() .'</strong> Committee.</p>';
        //$markup .= '<h3>How to add minutes</h3>';
        //$markup .= '<p>Adding minutes is a 2 step process</p>';
        //$markup .= '<ol><li>Add minutes</li><li>Relate the minutes to this committee</li><ol></ol>';
        //$markup .= '<p>You may use the <em>Media</em> tab to create and relate new minutes for this committee. To see a list of all minutes, across all committees, as well as those that are not related to any committee (i.e. general organization meeting minutes) you may visit this link: https://atsnj.ddev.site/admin/atsnj/media?field_media_category_target_id_op=or&bundle%5B%5D=minutes .</p>';
      }
      else {
        $markup = '';
        $markup .= '<p>Welcome back, '. $name .'.</p><p>You are a <em>member</em> of the <strong>'. $group->label() .'</strong> Committee.</p>';

        //$markup .= '<a class="button-style" href="/group/'. $group->id() .'/content/create/group_media%3Aminutes?destination=/group/'. $group->id() .'">Add Minutes</a>';
      }

    }
    else {
      $markup .= '<p>Welcome, '. $name .'.</p><p>You are a not a member of the <strong>'. $group->label() .'</strong> Committee.</p>';
    }



    // Add committee minutes button
    //$url = Url::fromRoute('entity.group_content.group_media_add_page');
    //$markup .= \Drupal::l(t('Add minutes'), $url);

    //group/2/content/create/group_media%3Aminutes
    return array(
      '#type' => 'processed_text',
      '#text' => $markup,
      '#format' => 'full_html',
    );

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
/*
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
*/
    return $form;
  }

  public function getCacheTags() {
    $account = \Drupal::currentUser();
    // Add account ID to cache tag.
    return Cache::mergeTags(parent::getCacheTags(), array('account:' . $account->id()));
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

