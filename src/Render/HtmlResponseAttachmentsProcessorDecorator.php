<?php

namespace Drupal\new_relic_rpm\Render;

use Drupal\Core\Render\AttachmentsInterface;
use Drupal\Core\Render\HtmlResponseAttachmentsProcessor;

/**
 * Decorates the HtmlResponseAttachmentsProcessor service.
 */
class HtmlResponseAttachmentsProcessorDecorator extends HtmlResponseAttachmentsProcessor {

  /**
   * The decorated HtmlResponseAttachmentsProcessor service.
   *
   * @var \Drupal\Core\Render\HtmlResponseAttachmentsProcessor
   */
  protected $decorated;

  /**
   * Constructs a HtmlResponseAttachmentsProcessorDecorator object.
   *
   * @param \Drupal\Core\Render\HtmlResponseAttachmentsProcessor $decorated
   *   The decorated HtmlResponseAttachmentsProcessor service.
   */
  public function __construct(HtmlResponseAttachmentsProcessor $decorated) {
    $this->decorated = $decorated;
  }

  /**
   * {@inheritdoc}
   */
  public function processAttachments(AttachmentsInterface $response) {
    $response = $this->decorated->processAttachments($response);
    $attachments = $response->getAttachments();

    // If the rum_footer library is attached, move it to the end of the library
    // array so its JS is loaded last. JS included in Drupal libraries can
    // only have a negative weight, making this decorator necessary.
    if (in_array('new_relic_rpm/rum_footer', $attachments['library'])) {
      $key = array_search('new_relic_rpm/rum_footer', $attachments['library'], TRUE);
      unset($attachments['library'][$key]);
      $attachments['library'][] = 'new_relic_rpm/rum_footer';
      // Reset index.
      $attachments['library'] = array_values($attachments['library']);
      $response->setAttachments($attachments);
    }

    return $response;
  }

}
