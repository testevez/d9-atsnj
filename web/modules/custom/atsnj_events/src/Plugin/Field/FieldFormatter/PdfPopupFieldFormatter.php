<?php

namespace Drupal\atsnj_events\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatterBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\Core\Cache\Cache;

/**
 * Plugin implementation of the 'pdf_popup_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "pdf_popup_field_formatter",
 *   label = @Translation("ATSNJ: PDF Popup"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class PdfPopupFieldFormatter extends ImageFormatterBase implements ContainerFactoryPluginInterface {
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The link generator.
   *
   * @var \Drupal\Core\Utility\LinkGeneratorInterface
   */
  protected $linkGenerator;

  /**
   * The image style entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * Constructs an ImageFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings settings.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Utility\LinkGeneratorInterface $link_generator
   *   The link generator service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $image_style_storage
   *   The entity storage for the image.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, AccountInterface $current_user, LinkGeneratorInterface $link_generator, EntityStorageInterface $image_style_storage) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->currentUser = $current_user;
    $this->linkGenerator = $link_generator;
    $this->imageStyleStorage = $image_style_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user'),
      $container->get('link_generator'),
      $container->get('entity.manager')->getStorage('image_style')
    //$container->get('entity.manager')->getStorage('image_style_popup')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
        'show_description' => '',
        'tag' => '',
        'image_style' => '',
        'image_link' => '',
      ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $image_styles = image_style_options(FALSE);
    $elements = parent::settingsForm($form, $form_state);
    $elements['show_description'] = array(
      '#type' => 'checkbox',
      '#title' => t('Description'),
      '#description' => t('Show file description beside image'),
      '#options' => array(0 => t('No'), 1 => t('Yes')),
      '#default_value' => $this->getSetting('show_description'),
    );
    $elements['tag'] = array(
      '#type' => 'radios',
      '#title' => t('HTML tag'),
      '#description' => t('Select which kind of HTML element will be used to theme elements'),
      '#options' => array('span' => 'span', 'div' => 'div'),
      '#default_value' => $this->getSetting('tag'),
    );
    $elements['image_style'] = array(
      '#title' => t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => t('None (original image)'),
      '#options' => $image_styles,
      '#description' => array(
        '#markup' => $this->linkGenerator->generate($this->t('Configure Image Styles'), new Url('entity.image_style.collection')),
        '#access' => $this->currentUser->hasPermission('administer image styles'),
      ),
    );
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();

    $image_styles = image_style_options(FALSE);
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);
    // Styles could be lost because of enabled/disabled modules that defines
    // their styles in code.
    $image_style_setting = $this->getSetting('image_style');
    if (isset($image_styles[$image_style_setting])) {
      $summary[] = t('Image style: @style', array('@style' => $image_styles[$image_style_setting]));
    }
    else {
      $summary[] = t('Original image');
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    global $base_url;

    $elements = array();
    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    $show_description = $this->getSetting('show_description');
    $tag = $this->getSetting('tag');
    $image_style_name = $this->getSetting('image_style');

    $popup_width = 750;


    // Collect cache tags to be added for each item in the field.
    $cache_tags = array();
    if (!empty($image_style_setting)) {
      $image_style = $this->imageStyleStorage->load($image_style_setting);
      $cache_tags = $image_style->getCacheTags();
    }

    foreach ($files as $delta => $file) {

      $fid = $file->id();

      $cache_contexts = array();

      $cache_tags = Cache::mergeTags($cache_tags, $file->getCacheTags());

      // Extract field item attributes for the theme function, and unset them
      // from the $item so that the field template does not re-render them.
      $item = $file->_referringItem;
      $item_attributes = $item->_attributes;
      unset($item->_attributes);

      if (isset($item->description)) {
        $item_attributes['alt'] = $item->description;
        $item_attributes['title'] = $item->description;
      }
      $item_attributes['class'][] = 'pdfpreview-file';

      // Separate the PDF previews from the other files.
      $show_preview = FALSE;
      if ($file->getMimeType() == 'application/pdf') {
        $preview_uri = \Drupal::service('pdfpreview.generator')->getPDFPreview($file);
        $preview = \Drupal::service('image.factory')->get($preview_uri);

        $width = $preview->getWidth();
        $height = $preview->getHeight();
        $file_url = "$base_url/atsnj_events/pdf-render/$fid"; ///". $width .':'. $height;


        if ($preview->isValid()) {
          $show_preview = TRUE;
          $item->uri = $preview_uri;
          $item->width = $width;
          $item->height = $height;
          $elements[$delta] = array(
            '#theme' => 'image_formatter',
            '#item' => $item,
            '#item_attributes' => $item_attributes,
            '#image_style' => $image_style_name,
            '#show_description' => $show_description,
            '#tag' => $tag,
            '#file_url' => $file_url,
            '#popup_width' => $popup_width,
            '#cache' => array(
              'tags' => $cache_tags,
              'contexts' => $cache_contexts,
            ),
          );
        }
      }
      if (!$show_preview) {
        $elements[$delta] = array(
          '#theme' => 'file_link',
          '#file' => $file,
          '#cache' => array(
            'tags' => $file->getCacheTags(),
          ),
        );
      }

      $elements[$delta]['#description'] = $item->description;
      $elements[$delta]['#theme_wrappers'][] = 'pdf_popup_field_formatter';
      $elements[$delta]['#settings'] = $this->getSettings();
      $elements[$delta]['#fid'] = $fid;
    }
    return $elements;
  }

}
