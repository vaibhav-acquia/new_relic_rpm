<?php

namespace Drupal\Tests\new_relic_rpm\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests admin UI.
 *
 * @package Drupal\Tests\new_relic_rpm\Functional
 * @group new_relic_rpm
 */
class AdminUiTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['new_relic_rpm'];

  protected $defaultTheme = 'stark';

  /**
   * The WebAssert.
   *
   * @var \Drupal\Tests\WebAssert
   */
  private $assert;

  /**
   * The DocumentElement.
   *
   * @var \Behat\Mink\Element\DocumentElement
   */
  private $page;

  /**
   * {@inheritDoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUp(): void {
    parent::setUp();

    $admin = $this->createUser([], NULL, TRUE);
    $admin->addRole('administrator');
    $admin->save();
    $this->drupalLogin($admin);

    if (!isset($this->assert) || !isset($this->page)) {
      $this->assert = $this->assertSession();
      $this->page = $this->getSession()->getPage();
    }
  }

  /**
   * Tests the settings page elements.
   */
  public function testSettingsPage() {
    $this->drupalGet('/admin/config/development/new-relic');
    $this->assert->statusCodeEquals(200);

    // General.
    $this->assert->fieldExists('api_key');

    // Transactions.
    $this->assert->fieldExists('track_drush');
    $this->assert->fieldExists('track_cron');
    $this->assert->fieldExists('ignore_roles[]');
    $this->assert->fieldExists('ignore_urls');
    $this->assert->fieldExists('bg_urls');
    $this->assert->fieldExists('exclusive_urls');

    // Error analytics.
    $this->assert->fieldExists('watchdog_severities[]');
    $this->assert->fieldExists('override_exception_handler');

    // Deployment.
    $this->assert->fieldExists('module_deployment');
    $this->assert->fieldExists('config_import');

    // Insight.
    $this->assert->fieldExists('views_log_slow');
    $this->assert->fieldExists('views_log_threshold');
  }

}
