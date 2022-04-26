<?php

namespace Drupal\atsnj_ads\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\views\Views;


class SponsorBlock extends DeriverBase
{
  public function getDerivativeDefinitions($base_plugin_definition)
  {

    $values = [];
    $view = views::getview('ads_administration');
    if ($view) {
      $view->execute('block_1');
      foreach ($view->result as $rid => $row) {
        foreach ($view->field as $fid => $field ) {
          $values[$rid][$fid . '-value'] = $field->getValue($row);
          $values[$rid][$fid . '-render'] = $field->render($row);
        }
      }
    }

    foreach ($values as $row) {
      $nid = $row['nid-value'];
      $title = $row['title-value'];
      $this->derivatives[$nid] = $base_plugin_definition;
      $this->derivatives[$nid]['admin_label'] = t('ATSNJ Ads: @title ', array('@title' => $title));
    }

    return $this->derivatives;
  }
}