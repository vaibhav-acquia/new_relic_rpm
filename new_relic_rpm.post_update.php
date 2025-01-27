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

  // If disable_autorum is either 1) not set or 2) is FALSE.
  if (empty($data['disable_autorum'])) {
    $config->set('rum_instrumentation', 'auto');
  }
  elseif ($data['disable_autorum'] === TRUE) {
    $config->set('rum_instrumentation', 'disabled');
  }

  $config->clear('disable_autorum')->save();
}
