<?php

namespace Drupal\new_relic_rpm_intstrumentation_test\EventSubscriber;

use Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * A request event subscriber.
 */
class NewRelicRpmInstrumentationRequestSubscriber implements EventSubscriberInterface {

  public function __construct(private readonly NewRelicAdapterInterface $adapter) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [KernelEvents::REQUEST => ['onRequest']];
  }

  /**
   * Call NewRelicAdapterInterface->getBrowserTimingFooter().
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The current response event for the page.
   */
  public function onRequest(RequestEvent $event): void {
    // Call NewRelicAdapterInterface->getBrowserTimingFooter() to simulate
    // big_pipe, which calls
    // HtmlResponseAttachmentsProcessor->processAttachments() multiple times
    // per request. This will allow the static caching in
    // ::getBrowserTimingFooter() to be tested.
    $this->adapter->getBrowserTimingFooter();
  }

}
