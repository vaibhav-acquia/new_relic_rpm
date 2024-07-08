<?php

namespace Drupal\Tests\new_relic_rpm\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests manual instrumentation.
 *
 * @package Drupal\Tests\new_relic_rpm\Functional
 * @group new_relic_rpm
 */
class InstrumentationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['new_relic_rpm', 'new_relic_rpm_instrumentation_test'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests markup is rendered.
   */
  public function testManualInstrumentation(): void {
    $assert = $this->assertSession();

    // Verify setting is disabled by default.
    $this->assertSame('auto', $this->config('new_relic_rpm.settings')->get('rum_instrumentation'));

    $this->drupalGet('<front>');

    $assert->responseNotContains("<script type=\"text/javascript\">console.log('header script inserted');</script>");
    $assert->responseNotContains("<script type=\"text/javascript\">console.log('footer script inserted');</script>");

    $this->container->get('config.factory')
      ->getEditable('new_relic_rpm.settings')
      ->set('rum_instrumentation', 'manual')
      ->save();

    $this->drupalGet('<front>');

    $assert->responseContains("<script type=\"text/javascript\">console.log('header script inserted');</script>");
    $assert->responseContains("<script type=\"text/javascript\">console.log('footer script inserted');</script>");
  }

}
