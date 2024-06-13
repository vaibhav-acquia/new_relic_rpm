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
    return "console.log('header script inserted');";
  }

  /**
   * {@inheritdoc}
   */
  public function getBrowserTimingFooter() {
    return "console.log('footer script inserted');";
  }

}
