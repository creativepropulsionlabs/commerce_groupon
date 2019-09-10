<?php

namespace Drupal\commerce_groupon;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class CommerceGrouponShippingMapperManager implements CommerceGrouponShippingMapperManagerInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  protected $entityManager;

  /**
   * The plugin manager.
   *
   * @var \Drupal\commerce_groupon\CommerceGrouponShippingMapperManager
   */
  protected $pluginManager;

  /**
   * Constructs an AutoEntityLabelManager object.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to add the automatic label to.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager
   * @param CommerceAutoSkuGeneratorManagerInterface $generatorManager
   *   Token manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityTypeManagerInterface $entity_type_manager, CommerceGrouponShippingMapperPluginManagerInterface $commerce_groupon_shipping_mapper_plugin_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityManager = $entity_manager;
    $this->pluginManager = $commerce_groupon_shipping_mapper_plugin_manager;
  }

  protected function getPlugin($groupon_shipping_method) {
    $plugins = $this->pluginManager->getDefinitions();
    foreach ($plugins as $plugin_id => $plugin) {
      if ($plugin['groupon_shipping_method'] == $groupon_shipping_method) {
        return $this->pluginManager->createInstance($plugin_id);
      }
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCarrier(OrderInterface $order) {
    $groupon_order = json_decode($order->getData('groupon_raw'));
    /** @var \Drupal\commerce_groupon\Plugin\CommerceGrouponShippingMapper\CommerceGrouponShippingMapperPluginInterface $shipping_method_plugin */
    $shipping_method_plugin = $this->getPlugin($groupon_order->shipping->method);
    if (!$shipping_method_plugin) {
      return;
    }
    $shipment = $shipping_method_plugin->getCarrier($order);

    return $shipment;
  }

  /**
   * {@inheritdoc}
   */
  public function getShipment(OrderInterface $order, $groupon_order) {
    /** @var \Drupal\commerce_groupon\Plugin\CommerceGrouponShippingMapper\CommerceGrouponShippingMapperPluginInterface $shipping_method_plugin */
    $shipping_method_plugin = $this->getPlugin($groupon_order["shipping"]["method"]);
    if (!$shipping_method_plugin) {
      return;
    }
    $shipment = $shipping_method_plugin->getShipment($order, $groupon_order);

    return $shipment;
  }


  /**
   * {@inheritdoc}
   */
  public function getShippingItem(OrderItemInterface $order_item, $line_item, $groupon_order) {
    /** @var \Drupal\commerce_groupon\Plugin\CommerceGrouponShippingMapper\CommerceGrouponShippingMapperPluginInterface $shipping_method_plugin */
    $shipping_method_plugin = $this->getPlugin($groupon_order["shipping"]["method"]);
    if (!$shipping_method_plugin) {
      return;
    }
    $shipment = $shipping_method_plugin->getShippingItem($order_item, $line_item, $groupon_order);

    return $shipment;
  }
}
