<?php

namespace Drupal\commerce_groupon\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the commerce_groupon shipping mapper plugin annotation object.
 *
 * Plugin namespace: Plugin\CommerceGrouponShippingMapper.
 *
 * @Annotation
 */

class CommerceGrouponShippingMapper extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * The groupon shipping method id.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $groupon_shipping_method;

  /**
   * The drupal shipping method plugin id.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $drupal_shipping_method_plugin;

}