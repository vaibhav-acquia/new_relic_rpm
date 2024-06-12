<?php

namespace Drupal\Tests\new_relic_rpm\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests manual instrumentation.
 *
 * @package Drupal\Tests\new_relic_rpm\FunctionalJavascript
 * @group new_relic_rpm
 */
class InstrumentationTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['new_relic_rpm', 'new_relic_rpm_intstrumentation_test'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests markup is rendered.
   */
  public function testManualInstrumentation() {
    $assert = $this->assertSession();

    // Verify setting is disabled by default.
    $this->assertSame(
      \Drupal::config('new_relic_rpm.settings')
        ->get('rum_instrumentation'),
      'auto'
    );

    $this->drupalGet('/');

    // NullAdapter, provided by new_relic_instrumentation_test, returns JS that
    // inserts text into the DOM. By checking for this text, this test is also
    // checking that the JS is executed in the browser.
    $assert->responseNotContains("header script inserted");
    $assert->responseNotContains("footer script inserted");

    $this->container->get('config.factory')
      ->getEditable('new_relic_rpm.settings')
      ->set('rum_instrumentation', 'manual')
      ->save();

    $this->drupalGet('/');

    $assert->responseContains("header script inserted");
    $assert->responseContains("footer script inserted");
  }

}
