<?php

namespace Drupal\Tests\new_relic_rpm\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests update functions.
 *
 * @group new_relic_rpm
 */
class UpdateTest extends KernelTestBase {

  // phpcs:disable DrupalPractice.Objects.StrictSchemaDisabled
  /**
   * {@inheritdoc}
   *
   * Disable check of config schema so update functions can be tested.
   */
  protected $strictConfigSchema = FALSE;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'new_relic_rpm',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['new_relic_rpm']);
  }

  /**
   * Tests new_relic_rpm_post_update_instrumentation().
   */
  public function testUpdateInstrumentation() {
    \Drupal::service('module_handler')->loadInclude('new_relic_rpm', 'php', 'new_relic_rpm.post_update');
    $config = \Drupal::service('config.factory')->getEditable('new_relic_rpm.settings');

    // Set config to pre-update state with disable_autorum unset.
    $data = $config->getRawData();
    unset($data['rum_instrumentation']);
    unset($data['disable_autorum']);
    $config->setData($data)->save();

    // Run post-update function.
    new_relic_rpm_post_update_instrumentation();

    $rum_instrumentation = \Drupal::service('config.factory')->get('new_relic_rpm.settings')->get('rum_instrumentation');
    $this->assertEquals($rum_instrumentation, 'auto');

    $disable_autorum = \Drupal::service('config.factory')->get('new_relic_rpm.settings')->get('disable_autorum');
    $this->assertNull($disable_autorum);

    // Set config to pre-update state with disable_autorum enabled.
    $data = $config->getRawData();
    unset($data['rum_instrumentation']);
    $data['disable_autorum'] = TRUE;
    $config->setData($data)->save();

    // Run post-update function.
    new_relic_rpm_post_update_instrumentation();

    $rum_instrumentation = \Drupal::service('config.factory')->get('new_relic_rpm.settings')->get('rum_instrumentation');
    $this->assertEquals($rum_instrumentation, 'disabled');

    $disable_autorum = \Drupal::service('config.factory')->get('new_relic_rpm.settings')->get('disable_autorum');
    $this->assertNull($disable_autorum);

    // Set config to pre-update state with disable_autorum disabled.
    $data = $config->getRawData();
    unset($data['rum_instrumentation']);
    $data['disable_autorum'] = FALSE;
    $config->setData($data)->save();

    // Run post-update function.
    new_relic_rpm_post_update_instrumentation();

    $rum_instrumentation = \Drupal::service('config.factory')->get('new_relic_rpm.settings')->get('rum_instrumentation');
    $this->assertEquals($rum_instrumentation, 'auto');

    $disable_autorum = \Drupal::service('config.factory')->get('new_relic_rpm.settings')->get('disable_autorum');
    $this->assertNull($disable_autorum);
  }

}
