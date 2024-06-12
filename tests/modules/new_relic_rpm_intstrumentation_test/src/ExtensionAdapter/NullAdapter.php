<?php

namespace Drupal\new_relic_rpm_intstrumentation_test\ExtensionAdapter;

use Drupal\new_relic_rpm\ExtensionAdapter\NullAdapter as ExtendedNullAdapter;

/**
 * Null Adapter extended for testing.
 */
class NullAdapter extends ExtendedNullAdapter {

  /**
   * {@inheritdoc}
   */
  public function getBrowserTimingHeader() {
    return "<script>document.body.appendChild(document.createTextNode('header script inserted'));</script>";
  }

  /**
   * {@inheritdoc}
   */
  public function getBrowserTimingFooter() {
    return "<script>document.body.appendChild(document.createTextNode('footer script inserted'));</script>";
  }

}
