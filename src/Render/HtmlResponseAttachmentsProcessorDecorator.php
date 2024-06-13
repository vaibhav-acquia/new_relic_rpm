<?php

namespace Drupal\new_relic_rpm\Render;

use Drupal\Core\Asset\AssetCollectionRendererInterface;
use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\HtmlResponseAttachmentsProcessor;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
   * @param \Drupal\Core\Asset\AssetResolverInterface $asset_resolver
   *   An asset resolver.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Drupal\Core\Asset\AssetCollectionRendererInterface $css_collection_renderer
   *   The CSS asset collection renderer.
   * @param \Drupal\Core\Asset\AssetCollectionRendererInterface $js_collection_renderer
   *   The JS asset collection renderer.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Language\LanguageManagerInterface|null $languageManager
   *   The language manager.
   * @param \Drupal\new_relic_rpm\ExtensionAdapter\NewRelicAdapterInterface $adapter
   *   The New Relic Adapster service.
   */
  public function __construct(HtmlResponseAttachmentsProcessor $decorated, NewRelicAdapterInterface $adapter, AssetResolverInterface $asset_resolver, ConfigFactoryInterface $config_factory, AssetCollectionRendererInterface $css_collection_renderer, AssetCollectionRendererInterface $js_collection_renderer, RequestStack $request_stack, RendererInterface $renderer, ModuleHandlerInterface $module_handler, protected ?LanguageManagerInterface $languageManager = NULL) {
    parent::__construct($asset_resolver, $config_factory, $css_collection_renderer, $js_collection_renderer, $request_stack, $renderer, $module_handler, $languageManager);
    $this->decorated = $decorated;
    $this->adapter = $adapter;
  }

  /**
   * {@inheritdoc}
   */
  protected function processAssetLibraries(AttachedAssetsInterface $assets, array $placeholders) {
    $variables = $this->decorated->processAssetLibraries($assets, $placeholders);

    if (\Drupal::config('new_relic_rpm.settings')->get('rum_instrumentation') == 'manual'
      && $markup = $this->adapter->getBrowserTimingFooter()
      ) {

      $variables['scripts_bottom'][] = [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#value' => Markup::create($markup),
        '#attributes' => [
          'type' => 'text/javascript',
        ],
      ];
    }

    return $variables;
  }

}
