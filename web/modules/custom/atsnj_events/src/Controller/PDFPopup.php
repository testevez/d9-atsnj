<?php

/**
 * @file
 * Contains \Drupal\atsnj_events\Controller\PDFPopup.
 */

namespace Drupal\atsnj_events\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;

/**
 * Class PDFPopup.
 *
 * @package Drupal\atsnj_events\Controller
 */
class PDFPopup extends ControllerBase {
  /**
   * Render.
   *
   * @return string
   *   Return Hello string.
   */
  public function render($fid) {

    $item = \Drupal::entityTypeManager()->getStorage('file')->load($fid);

    if ($item->getMimeType() == 'application/pdf') {

      $file_url = file_create_url($item->getFileUri());

      $iframe_src = file_create_url(base_path() . 'libraries/pdf.js/web/viewer.html') . '?file=' . rawurlencode($file_url);
      $iframe_src = !empty($query) && $keep_pdfjs ? $iframe_src . '#' . $query : $iframe_src;

      // Add JS  to resize
      $html = [
        '#theme' => 'file_pdf',
        '#attributes' => [
          'class' => ['pdf'],
          'webkitallowfullscreen' => '',
          'mozallowfullscreen' => '',
          'allowfullscreen' => '',
          'frameborder' => 'no',
          'width' =>'100%',
          'height' => '1000px',
          'src' => $iframe_src,
          'data-src' => $file_url,
          'title' => $item->label(),
        ],
      ];
      $element = $html;

    }
    else {

      $element = [
        '#theme' => 'file_link',
        '#file' => $item,
      ];

    }

    return $element;

  }

}
