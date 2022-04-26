<?php

namespace Drupal\atsnj_portal\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Database;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements a form to fix CAS user issues.
 */
class AtsnjCasFix extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'atsnj_cas_fix';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    // SELECT *, COUNT(*) FROM users_field_data GROUP BY mail HAVING COUNT(*) >= 2 ORDER BY COUNT(*) DESC

    $connection = \Drupal::database();
    $query = $connection->select('users_field_data', 'u')
      ->fields('u',['mail']);
    $query->addExpression('count(mail)', 'email_count');
    $query->groupBy("u.mail");
    $query->having('COUNT(mail) >= :matches', [':matches' => 2]);
    $query->allowRowCount = TRUE;

    $results = $query->execute()->fetchAll();
    $count = count($results);

    $form['info'] = [
      '#markup' => $this->t('We found ') . $count . $this->t(' user(s) with duplicate emails, signifying a CAS redundant user. Check the users you wish to fix.'),
    ];

    $opt = array();
    foreach ($results as $r) {
      $mail = $r->mail;
      $accounts = user_load_by_mail($mail);

      $opt[$mail .':remove:keep'] = "$mail: Remove some user and keep some user.";
    }

    $form['fixes'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t($mail),
      '#description' => $this->t('Fix these users.'),
      //'#default_value' => FALSE,
      '#options' => $opt,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Review each user to repair
    // Load previous user: uid, content, roles, first name, last name, zip
    // Assign to new user
    // archive previous user


    $values = $form_state->getValues();

  }
}