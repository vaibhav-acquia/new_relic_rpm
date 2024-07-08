<?php

namespace Drupal\new_relic_rpm\Render;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\AttachmentsInterface;
use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
use Drupal\Core\Render\HtmlResponseAttachmentsProcessor;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface;

/**
 * Processes attachments of HTML responses.
 *
 * This class is used by the rendering service to process the #attached part of
 * the render array, for HTML responses.
 *
 * To render attachments to HTML for testing without a controller, use the
 * 'bare_html_page_renderer' service to generate a
 * Drupal\Core\Render\HtmlResponse object. Then use its getContent(),
 * getStatusCode(), and/or the headers property to access the result.
 *
 * @see template_preprocess_html()
 * @see \Drupal\Core\Render\AttachmentsResponseProcessorInterface
 * @see \Drupal\Core\Render\BareHtmlPageRenderer
 * @see \Drupal\Core\Render\HtmlResponse
 * @see \Drupal\Core\Render\MainContent\HtmlRenderer
 */
class HtmlResponseAttachmentsProcessorDecorator implements AttachmentsResponseProcessorInterface {

  /**
   * The decorated HtmlResponseAttachmentsProcessor service.
   *
   * @var \Drupal\Core\Render\HtmlResponseAttachmentsProcessor
   */
  protected $decorated;

  /**
   * The New Relic Adapster service.
   *
   * @var \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface
   */
  protected $adapter;

  /**
   * A config object for New Relic configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $configNewRelic;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a HtmlResponseAttachmentsProcessorDecorator object.
   *
   * @param \Drupal\Core\Render\HtmlResponseAttachmentsProcessor $decorated
   *   The decorated HtmlResponseAttachmentsProcessor service.
   * @param \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface $adapter
   *   The New Relic Adapster service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(
    private readonly HtmlResponseAttachmentsProcessor $decorated,
    private readonly NewRelicAdapterInterface $adapter,
    private readonly ConfigFactoryInterface $config_factory,
    private readonly RendererInterface $renderer,
  ) {
    $this->decorated = $decorated;
    $this->adapter = $adapter;
    $this->configNewRelic = $config_factory->get('new_relic_rpm.settings');
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function processAttachments(AttachmentsInterface $response) {
    if ($this->configNewRelic->get('rum_instrumentation') === 'manual'
      && $markup = $this->adapter->getBrowserTimingFooter()
      ) {

      $script_render_array = [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#value' => Markup::create($markup),
        '#attributes' => [
          'type' => 'text/javascript',
        ],
      ];

      $content = $response->getContent();
      $attached = $response->getAttachments();
      // Perform a string replacement to insert raw JS markup directly
      // into $content.
      if (isset($attached['html_response_attachment_placeholders']['scripts_bottom'])) {
        $content = str_replace(
          $attached['html_response_attachment_placeholders']['scripts_bottom'],
          $attached['html_response_attachment_placeholders']['scripts_bottom'] . PHP_EOL . (string) $this->renderer->renderPlain($script_render_array),
          $content
        );
        $response->setContent($content);
      }
    }

    return $this->decorated->processAttachments($response);
  }

}
