<?php

namespace Drupal\Tests\new_relic_rpm\Functional;

use Drupal\Tests\system\Functional\Render\HtmlResponseAttachmentsTest as CoreHtmlResponseAttachmentsTest;

/**
 * Ensure HtmlResponseAttachmentsProcessorDecorator doesn't break core.
 *
 * @package Drupal\Tests\new_relic_rpm\Functional
 * @group new_relic_rpm
 */
class HtmlResponseAttachmentsTest extends CoreHtmlResponseAttachmentsTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['render_attached_test', 'new_relic_rpm', 'new_relic_rpm_intstrumentation_test'];

}
