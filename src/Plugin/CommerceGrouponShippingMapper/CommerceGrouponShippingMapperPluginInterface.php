<?php

namespace Drupal\commerce_groupon\Plugin\CommerceGrouponShippingMapper;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\BaseFormIdInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Plugin\PluginFormInterface;



interface CommerceGrouponShippingMapperPluginInterface extends  ConfigurablePluginInterface, PluginFormInterface, PluginInspectionInterface, DerivativeInspectionInterface {
  public function getShippingItem(OrderItemInterface $order_item, $groupon_line_item, $groupon_order);
  public function getShipment(OrderInterface $order, $groupon_order);
  public function getShippingProfile(OrderInterface $order, $groupon_order);

  /**
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   * @param $groupon_order
   * @return \Drupal\commerce_shipping\Entity\ShippingMethodInterface
   */
  public function getShippingMethod(OrderInterface $order, $groupon_order);
}