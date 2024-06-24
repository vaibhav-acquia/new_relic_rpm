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
    // newrelic_get_browser_timing_footer() returns an empty string if called
    // more than once during a transaction. With big_pipe enabled,
    // HtmlResponseAttachmentsProcessor->processAttachments() gets called
    // several times.
    $key = 'new_relic_rpm:rum_footer_js';
    $cache = \Drupal::cache();
    $cached_js = $cache->get($key);
    if ($cached_js && $cached_js->data) {
      return $cached_js->data;
    }
    else {
      $footer_script = $this->mimicNewRelicFooterScriptFunction();
      $cache->set($key, $footer_script);
      return $footer_script;
    }
  }

  /**
   * Mimic newrelic_get_browser_timing_footer().
   *
   * The newrelic_get_browser_timing_footer() function returns an empty string
   * if called more than once during a transaction.
   *
   * @return string
   *   A test string or an empty string.
   */
  private function mimicNewRelicFooterScriptFunction() {
    $called = &drupal_static(__METHOD__);
    if ($called) {
      return '';
    }
    else {
      $called = TRUE;
      return "console.log('footer script inserted');";
    }
  }

}
