<?php

namespace Drupal\nimble_ams\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure nimble_ams settings for this site.
 */
class NimbleAmsSettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nimble_ams_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'nimble_ams.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Load existing config
    $config = $this->config('nimble_ams.settings');


    $form['message'] = [
      '#markup' => $this->t('Authorize this website to communicate with Nimble AMS Community Hub by entering the consumer key and secret from a remote application. Clicking authorize will redirect you to Community Hub where you will be asked to grant access.'),
    ];
    $form['nimble_ams_consumer_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nimble AMS consumer key'),
      '#default_value' => $config->get('nimble_ams_consumer_key'),
      '#description'  => $this->t('Consumer key of the Nimble AMS remote application you want to grant access to'),
      '#required' => TRUE,

    );
    $form['nimble_ams_consumer_secret'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nimble AMS consumer secret'),
      '#default_value' => $config->get('nimble_ams_consumer_secret'),
      '#required' => TRUE,
      '#description'  => $this->t('Consumer secret of the Nimble AMS remote application you want to grant access to'),
    );
    $form['nimble_ams_endpoint'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nimble AMS endpoint'),
      '#default_value' => $config->get('nimble_ams_endpoint'),
      '#required' => TRUE,
      '#description'  => $this->t('Enter the URL of your Nimble AMS environment (for example, <code>https://test.nimble_ams.com</code>). <strong>Caution:</strong> Note that switching this setting after you have already synchronised data between your Drupal site and Nimble AMS will render any existing links between Nimble AMS objects and Drupal objects invalid!'),
    );
    $form['nimble_ams_sso_login_redirect_whitelist'] = array(
      '#type' => 'textarea', //TODO: check for format
      '#title' => $this->t('SSO Login retUrl redirect whitelist'),
      '#default_value' => $config->get('nimble_ams_sso_login_redirect_whitelist'),
      '#description'  => $this->t('List domains, one per line, that are allowed to have the user redirected to with the retUrl url parameter for the sso-login path. Do not include http:// or https:// e.g stage.nata.org'),
    );
    $form['nimble_ams_sso_login_auth_user_redirect_path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('SSO Login Authenticated Member User Redirect Path'),
      '#default_value' => $config->get('nimble_ams_sso_login_auth_user_redirect_path'),
      '#description'  => $this->t('When user goes to sso-login, automatic SSO login path, if the member user is already authenticated, redirect them here. e.g, "user"'),
    );

    /* we dont need this accountQuery stuff - i think */

//    $form['nimble_ams_enable_account_query'] = array(
//      '#title' => $this->t('Enable AccountQuery user field sync'),
//      '#type' => 'checkbox',
//      '#return_value' => 1,
//      '#default_value' => ($config->get('nimble_ams_sso_login_auth_user_redirect_path') ? $config->get('nimble_ams_sso_login_auth_user_redirect_path') : 0),
//    );
//    $form['nimble_ams_nuint_append_url'] = array(
//      '#title' => $this->t('Append URL'),
//      '#type' => 'textfield',
//      '#description' => t('Path to append to the base Nimble endpoint'),
//      '#required' => FALSE,
//      '#default_value' => ($config->get('nimble_ams_nuint_append_url') ? $config->get('nimble_ams_sso_login_auth_user_redirect_path') : 'services/apexrest/NUINT/NUIntegrationService'),
//    );
//    $form['nimble_ams_nuint_account_query_name'] = array(
//      '#title' => $this->t('AccountQuery Name'),
//      '#type' => 'textfield',
//      '#description' => $this->t('Name for the AccountQuery API'),
//      '#required' => FALSE,
//      '#default_value' => ($config->get('nimble_ams_nuint_account_query_name') ? $config->get('nimble_ams_nuint_account_query_name') : 'PDCAccountQuery'),
//    );
//    $form['nimble_ams_nuint_account_query_auth_key'] = array(
//      '#title' => $this->t('AccountQuery Authentication Key'),
//      '#type' => 'textfield',
//      '#description' => $this->t('Authentication key for the AccountQuery API'),
//      '#required' => TRUE,
//      '#default_value' => $config->get('nimble_ams_nuint_account_query_auth_key'),
//
//    );


/*


    $form['nimble_ams_enable_logging'] = array(
      '#title' => t('Enable logging to the Drupal log'),
      '#type' => 'checkbox',
      '#return' => 1,
      '#default_value' => variable_get('nimble_ams_enable_logging', 0),
    );
    // enable commerce profile setting
    $form['nimble_ams_enable_commerce_billing_profile_create'] = array(
      '#title' => t('Create commerce billing profile for user'),
      '#description' => t('Designed specifically for PDC. Enable to create a commerce billing profile if user does not already have one. Requires commerce and commerce_customer modules.'),
      '#type' => 'checkbox',
      '#return' => 1,
      '#default_value' => variable_get('nimble_ams_enable_commerce_billing_profile_create', 0),
    );
    $form['nimble_ams_enable_ceu_bank_populate'] = array(
      '#title' => t('Enable CEU bank population'),
      '#description' => t('Designed specifically for PDC. Enable to process membership to decide to give users CEU credits to their user CEU bank field'),
      '#type' => 'checkbox',
      '#return' => 1,
      '#default_value' => variable_get('nimble_ams_enable_ceu_bank_populate', 0),
    );
    $form['nimble_ams_instance'] = [
      '#title' => t('Nimble Instance'),
      '#description' => t('Are you connecting this site to the Nimble staging or production instance?'),
      '#type' => 'select',
      '#options' => ['staging' => 'Staging', 'production' => 'Production'],
      '#default_value' => variable_get('nimble_ams_instance', 'staging'),
      '#required' => TRUE,
    ];
    $form['nimble_ams_member_num_field'] = [
      '#title' => t('Member number field'),
      '#description' => t('Choose the field from the user object that stores the NATA member id. Used for matching to existing users on SSO login.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => $member_number_field,
    ];
    $form['roles'] = [
      '#title' => t('User roles settings'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    // build list of roles
    $user_roles = user_roles(TRUE);
    // remove authenticated user role from options
    if (isset($user_roles[2])) {
      unset($user_roles[2]);
    }
    $form['roles']['nimble_ams_automatic_roles'] = [
      '#type' => 'select',
      '#title' => t('Roles to automatically assign'),
      '#description' => t('Select roles that should be assigned to user on SSO login, regardless of other conditions. Used by PDC to assign LMS User role'),
      '#multiple' => TRUE,
      '#options' => $user_roles,
      '#default_value' => variable_get('nimble_ams_automatic_roles', []),
    ];
    $form['roles']['nimble_ams_roles_allowed_user_form'] = [
      '#type' => 'select',
      '#title' => t('Roles to allow to see the user edit form'),
      '#description' => t('Select roles that should have permission to see all fields on any user edit form.'),
      '#multiple' => TRUE,
      '#options' => $user_roles,
      '#default_value' => variable_get('nimble_ams_roles_allowed_user_form', []),
    ];
    $form['roles']['nimble_ams_member_role'] = [
      '#type' => 'select',
      '#title' => t('Member role'),
      '#description' => t('Select role to assign to members'),
      '#options' => $user_roles,
      '#default_value' => variable_get('nimble_ams_member_role', ''),
    ];
    $form['roles']['nimble_ams_non_member_role'] = [
      '#type' => 'select',
      '#title' => t('Non Member role'),
      '#description' => t('Select role to assign to non members'),
      '#options' => $user_roles,
      '#default_value' => variable_get('nimble_ams_non_member_role', ''),
    ];

    if (!empty($nimble_instance)) {
      $membership_types = ['' => 'None'] + nimble_ams_get_membership_types_array_keyed_by_id($nimble_instance);
      $form['roles']['other_roles'] = [
        '#type'        => 'fieldset',
        '#title'       => 'Other roles',
        '#description' => t('For each role, select a membership type the user must have to get role assigned. Do not map membership types to roles configured for the member and non-member roles above. If the user does not have the selected membership type, the role will be removed.'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      ];
      foreach ($user_roles as $rid => $role) {
        $form['roles']['other_roles']['nimble_ams_role_' . $rid . '_type_mapping'] = [
          '#title'         => $role,
          '#type'          => 'select',
          '#options'       => $membership_types,
          '#default_value' => variable_get('nimble_ams_role_' . $rid . '_type_mapping', ''),
        ];
      }
    }

    $form['field_mappings'] = [
      '#title' => t('Field Mappings'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    $form['field_mappings']['nimble_ams_membership_type_field'] = [
      '#title' => t('Membership Type field'),
      '#description' => t('The field to store the human readable label of the membership type.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_membership_type_field', ''),
    ];
    $form['field_mappings']['nimble_ams_membership_type_id_field'] = [
      '#title' => t('Membership Type ID field'),
      '#description' => t('The field to store the membership type id.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_membership_type_id_field', ''),
    ];
    $form['field_mappings']['nimble_ams_is_member_field'] = [
      '#title' => t('Is Member? field'),
      '#description' => t('The field to store Is Member? field, must be text, incoming values are "Yes" or "No'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_is_member_field', ''),
    ];
    $form['field_mappings']['nimble_ams_join_date_field'] = [
      '#title' => t('Join Date field'),
      '#description' => t('The field to store Join Date'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_join_date_field', ''),
    ];
    $form['field_mappings']['nimble_ams_start_date_field'] = [
      '#title' => t('Start Date field'),
      '#description' => t('The field to store Start Date'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_start_date_field', ''),
    ];
    $form['field_mappings']['nimble_ams_end_date_field'] = [
      '#title' => t('End Date field'),
      '#description' => t('The field to store End Date'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_end_date_field', ''),
    ];
    $form['field_mappings']['nimble_ams_end_date_override_field'] = [
      '#title' => t('End Date Override field'),
      '#description' => t('The field to store End Date Override date'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_end_date_override_field', ''),
    ];
    $form['field_mappings']['nimble_ams_district_field'] = [
      '#title' => t('District field'),
      '#description' => t('The field to store District'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_district_field', ''),
    ];
    $form['field_mappings']['nimble_ams_job_setting_field'] = [
      '#title' => t('Job setting field'),
      '#description' => t('The field to store job setting'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_job_setting_field', ''),
    ];
    $form['field_mappings']['nimble_ams_age_field'] = [
      '#title' => t('Age field'),
      '#description' => t('The field to store age'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_age_field', ''),
    ];
    $form['field_mappings']['nimble_ams_gender_field'] = [
      '#title' => t('Gender field'),
      '#description' => t('The field to store gender'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_gender_field', ''),
    ];
    $form['field_mappings']['nimble_ams_account_id_field'] = [
      '#title' => t('Account ID field'),
      '#description' => t('The field to store users Nimble Account ID'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_account_id_field', ''),
    ];
    $form['field_mappings']['nimble_ams_title_field'] = [
      '#title' => t('Title field'),
      '#description' => t('The title of the user'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_title_field', ''),
    ];
    $form['field_mappings']['nimble_ams_full_name_field'] = [
      '#title' => t('Full name field'),
      '#description' => t('The field to store full name, e.g Mr. John Smith ,Sr.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_full_name_field', ''),
    ];
    $form['field_mappings']['nimble_ams_name_field'] = [
      '#title' => t('Name field'),
      '#description' => t('The field to store name, First + Last, eg. John Smith.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_name_field', ''),
    ];
    $form['field_mappings']['nimble_ams_first_name_field'] = [
      '#title' => t('First name field'),
      '#description' => t('The field to store first name.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_first_name_field', ''),
    ];
    $form['field_mappings']['nimble_ams_last_name_field'] = [
      '#title' => t('Last name field'),
      '#description' => t('The field to store last name.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_last_name_field', ''),
    ];
    $form['field_mappings']['nimble_ams_company_account_id_field'] = [
      '#title' => t('Company Account ID'),
      '#description' => t('The field to store the users company Nimble account id.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_company_account_id_field', ''),
    ];
    $form['field_mappings']['nimble_ams_company_name_field'] = [
      '#title' => t('Company Name Field'),
      '#description' => t('The field to store the name of the company the user works for.'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_company_name_field', ''),
    ];
    $form['field_mappings']['nimble_ams_extra_email_field'] = [
      '#title' => t('Extra Email Field'),
      '#description' => t('The field to store the name of the extra email field. (optional)'),
      '#type' => 'select',
      '#options' => $user_fields,
      '#default_value' => variable_get('nimble_ams_extra_email_field', ''),
    ];
*/
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
   $config = $this->configFactory->getEditable('nimble_ams.settings');
    // Set the submitted configuration setting
    $values = $form_state->getValues();
    foreach ($values as $i => $v) {
      $config->set($i, $form_state->getValue($i));
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }
}