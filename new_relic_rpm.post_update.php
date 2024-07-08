<?php

/**
 * @file
 * Post update functions for the New Relic module.
 */

/**
 * Replace disable_autorum setting with rum_instrumentation setting.
 */
function new_relic_rpm_post_update_instrumentation(): void {
  $config = \Drupal::configFactory()
    ->getEditable('new_relic_rpm.settings');
  $data = $config->getRawData();

  if (!isset($data['disable_autorum'])) {
    $data['rum_instrumentation'] = 'auto';
  }
  elseif ($data['disable_autorum'] == TRUE) {
    $data['rum_instrumentation'] = 'disabled';
  }
  elseif ($data['disable_autorum'] == FALSE) {
    $data['rum_instrumentation'] = 'auto';
  }

  unset($data['disable_autorum']);
  $config->setData($data)->save();
}
