<?php

namespace Drupal\Tests\new_relic_rpm\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests manual instrumentation.
 *
 * @package Drupal\Tests\new_relic_rpm\Functional
 * @group new_relic_rpm
 */
class ManualInstrumentationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['new_relic_rpm', 'rum_manual_instrumentation'];

  protected $defaultTheme = 'stark';

  /**
   * Tests markup is rendered.
   */
  public function testMarkupRender() {
    $assert = $this->assertSession();

    // Verify setting is disabled by default.
    $this->assertFalse(
      \Drupal::config('new_relic_rpm.settings')
        ->get('rum_manual_instrumentation')
    );

    $this->drupalGet('/');

    $assert->responseNotContains("<script>console.log('header script inserted')</script>");
    $assert->responseNotContains("<script>console.log('footer script inserted')</script>");

    $this->container->get('config.factory')
      ->getEditable('new_relic_rpm.settings')
      ->set('rum_manual_instrumentation', TRUE)
      ->save();

    $this->drupalGet('/');

    $assert->responseContains("<script>console.log('header script inserted')</script>");
    $assert->responseContains("<script>console.log('footer script inserted')</script>");
  }

}
