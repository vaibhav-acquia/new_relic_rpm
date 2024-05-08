<?php

namespace Drupal\rum_manual_instrumentation\ExtensionAdapter;

/**
 * Factory to create a New Relic adapter.
 */
class AdapterFactory {

  /**
   * Returns a test new relic adapter.
   *
   * @return \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface
   *   The new relic adapter.
   */
  public static function getAdapter() {
    return new NullAdapter();
  }

}
