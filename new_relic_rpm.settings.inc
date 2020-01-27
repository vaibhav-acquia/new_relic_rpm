<?php

/**
 * @file
 * New Relic Drupal setting form and callbacks.
 */

/**
 * New Relic settings form.
 *
 * @param $form
 * @param $form_state
 * @return mixed
 */
function new_relic_rpm_settings($form, &$form_state) {
  $form['transaction_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Transactions'),
    '#weight' => -10,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['transaction_settings']['new_relic_rpm_track_drush'] = array(
    '#type' => 'select',
    '#title' => t('Drush transactions'),
    '#description' => t('How do you wish New Relic to track Drush commands?'),
    '#options' => array(
      'ignore' => t('Ignore completely'),
      'bg' => t('Track as background tasks'),
      'norm' => t('Track normally'),
    ),
    '#default_value' => variable_get('new_relic_rpm_track_drush', 'norm'),
  );

  $form['transaction_settings']['new_relic_rpm_track_cron'] = array(
    '#type' => 'select',
    '#title' => t('Cron transactions'),
    '#description' => t('How do you wish New Relic to track Cron tasks?'),
    '#options' => array(
      'ignore' => t('Ignore completely'),
      'bg' => t('Track as background tasks'),
      'norm' => t('Track normally'),
    ),
    '#default_value' => variable_get('new_relic_rpm_track_cron', 'norm'),
  );

  $form['transaction_settings']['new_relic_rpm_use_menu_item_as_transaction'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use the active menu item to name transactions'),
    '#description' => t('If enabled the path of the active menu item on a request will be used as a transaction name in New Relic. This allows you to use fine granular key transaction tracking.'),
    '#default_value' => variable_get('new_relic_rpm_use_menu_item_as_transaction', FALSE),
  );

  $form['transaction_settings']['new_relic_rpm_add_node_type_to_node_page_paths'] = array(
    '#type' => 'checkbox',
    '#title' => t('Put the node type in the paths for node pages'),
    '#description' => t('If enabled the node type will be inserted into the path where the nid placeholder would be. This depends on using the menu item path for the name.'),
    '#default_value' => variable_get('new_relic_rpm_add_node_type_to_node_page_paths', FALSE),
  );

  // Deployments
  $form['deployments_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Deployments'),
    '#weight' => -9,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['deployments_settings']['new_relic_rpm_module_deployment'] = array(
    '#type' => 'select',
    '#title' => t('Track module changes'),
    '#description' => t('Turning this on will create and mark a "deployment" on the New Relic dashboard each time modules are enabled or disabled. This will help you track before and after statistics.'),
    '#options' => array(
      '1' => t('Enable (recommended)'),
      '0' => t('Disable'),
    ),
    '#default_value' => variable_get('new_relic_rpm_module_deployment', 1),
  );

  $form['rum_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('RUM (Real User Monitoring)'),
    '#weight' => -7,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['rum_settings']['new_relic_rpm_disable_autorum'] = array(
    '#type' => 'checkbox',
    '#title' => t('Disable automatic RUM'),
    '#description' => t('Disable automatic injection of the New Relic Browser snippet for Real User Monitoring (RUM). See also @reference_url.', array('@reference_url' => 'https://docs.newrelic.com/docs/agents/php-agent/php-agent-api/newrelic_disable_autorum')),
    '#default_value' => variable_get('new_relic_rpm_disable_autorum', FALSE),
  );

  $form['rum_settings']['new_relic_rpm_add_manual_rum_instrumentation'] = array(
    '#type' => 'checkbox',
    '#title' => t('Manually add RUM instructions to header and footer'),
    '#description' => t("When compress cached pages and cache pages for anonymous users is turned on, the New Relic Browser JavaScript is not inserted into the served pages for anonymous users. This is because Drupal's pages are compressed before they are stored in the cache (with gzip), so New Relic's PHP agent does not have a chance to parse any HTML, to add header and footer automatically. In this situation, manual instrumentation provides a better opportunity to capture data for anonymous users. See also @reference_url.", array('@reference_url' => 'https://docs.newrelic.com/docs/agents/php-agent/frameworks-libraries/drupal-specific-functionality#page-load-timing-rum')),
    '#default_value' => variable_get('new_relic_rpm_add_manual_rum_instrumentation', FALSE),
  );

  $form['users_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Users & Roles'),
    '#weight' => -6,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['users_settings']['new_relic_rpm_ignored_roles'] = array(
    '#type' => 'select',
    '#multiple' => TRUE,
    '#title' => t('Ignored Roles'),
    '#required' => FALSE,
    '#options' => user_roles(),
    '#description' => t('Select user roles you wish New Relic to ignore.'),
    '#default_value' => variable_get('new_relic_rpm_ignored_roles', array()),
  );

  $form['url_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('URL rules'),
    '#weight' => -5,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['url_settings']['new_relic_rpm_ignore_urls'] = array(
    '#type' => 'textarea',
    '#wysiwyg' => FALSE,
    '#title' => t('Ignore URLs'),
    '#description' => t('Enter URLs you wish New Relic to ignore. Enter one URL per line.'),
    '#default_value' => variable_get('new_relic_rpm_ignore_urls', ''),
  );

  $form['url_settings']['new_relic_rpm_bg_urls'] = array(
    '#type' => 'textarea',
    '#wysiwyg' => FALSE,
    '#title' => t('Background URLs'),
    '#description' => t('Enter URLs you wish to have tracked as background tasks. Enter one URL per line.'),
    '#default_value' => variable_get('new_relic_rpm_bg_urls', ''),
  );

  $form['url_settings']['new_relic_rpm_exclusive_urls'] = array(
    '#type' => 'textarea',
    '#wysiwyg' => FALSE,
    '#title' => t('Exclusive URLs'),
    '#description' => t('Enter URLs you wish to exclusively track. This is useful for debugging specific issues. **NOTE** Entering URLs here effectively marks all other URLs as ignored. Leave blank to disable.'),
    '#default_value' => variable_get('new_relic_rpm_exclusive_urls', ''),
  );

  $form['error_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Errors & Logging'),
    '#weight' => -4,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );

  $form['error_settings']['new_relic_rpm_watchdog_severities'] = array(
    '#type' => 'select',
    '#multiple' => TRUE,
    '#title' => t('Forward watchdog messages'),
    '#description' => t('Select which watchdog severities should be forwarded to New Relic API as errors.'),
    '#options' => watchdog_severity_levels(),
    '#default_value' => variable_get('new_relic_rpm_watchdog_severities', array()),
  );

  $form['error_settings']['new_relic_rpm_override_exception_handler'] = array(
    '#type' => 'checkbox',
    '#title' => t('Override exception handler'),
    '#description' => t('Check to override default Drupal exception handler and to have exceptions passed to New Relic.'),
    '#default_value' => variable_get('new_relic_rpm_override_exception_handler', FALSE),
  );

  $form['error_settings']['new_relic_rpm_suppress_module_not_enabled_error_on_cli'] = array(
    '#type' => 'checkbox',
    '#title' => t('Suppress error message on CLI when New Relic extension is not present'),
    '#description' => t('By default the module will show an error message when New Relic extension is not present. Suppress that message only when drupal invoked from command line interface, for example by drush.'),
    '#default_value' => variable_get('new_relic_rpm_suppress_module_not_enabled_error_on_cli', FALSE),
  );

  $form['error_settings']['new_relic_rpm_suppress_module_not_enabled_error_always'] = array(
    '#type' => 'checkbox',
    '#title' => t('Always suppress error message when New Relic extension is not present'),
    '#description' => t('By default the module will show an error message when New Relic extension is not present. Suppress that message always, for example on a local development environment.'),
    '#default_value' => variable_get('new_relic_rpm_suppress_module_not_enabled_error_always', FALSE),
  );

  $form['api_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('API Key'),
    '#weight' => -1,
    '#collapsible' => TRUE,
    '#collapsed' => !empty(variable_get('new_relic_rpm_api_key', '')),
  );

  $form['api_settings']['new_relic_rpm_api_key'] = array(
    '#type' => 'textfield',
    '#title' => t('API Key'),
    '#description' => t('Enter your New Relic API key if you wish to view some reports and analysis within Drupal.'),
    '#default_value' => variable_get('new_relic_rpm_api_key', ''),
  );

  return system_settings_form($form);
}

/**
 * Form callback for manually creating a deployment.
 *
 * @param $form
 * @param $form_state
 * @return array
 */
function new_relic_rpm_deploy($form, &$form_state) {
  $form['deploy_user'] = array(
    '#type' => 'textfield',
    '#title' => t('Deployer/Deployment Name'),
    '#required' => TRUE,
    '#description' => t('Enter the name for this deployment of your application. This will be the name shown in your list of deployments on the New Relic website.'),
  );

  $form['deploy_description'] = array(
    '#type' => 'textarea',
    '#title' => t('Deployment Description'),
    '#description' => t('Provide some notes and description regarding this deployment.'),
  );

  $form['deploy_changelog'] = array(
    '#type' => 'textarea',
    '#title' => t('Deployment Changelog'),
    '#description' => t('Provide a specific changelog for this deployment.'),
  );

  $form['deploy_revision'] = array(
    '#type' => 'textfield',
    '#title' => t('Deployment Revision'),
    '#description' => t('Add a revision number to this deployment.'),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Create Deployment'),
  );

  return $form;
}

/**
 * FormAPI submit callback for the manual deployment creation.
 * @param $form
 * @param $form_state
 */
function new_relic_rpm_deploy_submit($form, &$form_state) {
  $deployments = _new_relic_rpm_deploy($form_state['values']['deploy_user'],
    $form_state['values']['deploy_description'],
    $form_state['values']['deploy_changelog'],
    $form_state['values']['deploy_revision']);

  if (strlen($deployments) > 20) {
    drupal_set_message(t('New Relic deployment created successfully'), 'status');
  }
  else {
    drupal_set_message(t('New Relic deployment failed to be recorded. Please ensure your account is configured on the <a href="@settings">New Relic Drupal admin page</a>.', array('@settings' => url('admin/config/development/new-relic-rpm'))), 'error');
  }
}
