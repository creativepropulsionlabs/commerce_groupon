<?php

namespace Drupal\commerce_groupon;

use Drupal\commerce_groupon\CommerceGrouponShippingMapperManagerInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

class CommerceGrouponShippingMapperPluginManager extends DefaultPluginManager implements CommerceGrouponShippingMapperPluginManagerInterface, PluginManagerInterface {

  /**
   * Constructs a CommerceGrouponShippingMapperPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/CommerceGrouponShippingMapper',
      $namespaces,
      $module_handler,
      'Drupal\commerce_groupon\Plugin\CommerceGrouponShippingMapper\CommerceGrouponShippingMapperPluginInterface',
      'Drupal\commerce_groupon\Annotation\CommerceGrouponShippingMapper');

    $this->alterInfo('commerce_groupon_shipping_mapper_info');
    $this->setCacheBackend($cache_backend, 'commerce_groupon_shipping_mapper_plugins');
  }

}
