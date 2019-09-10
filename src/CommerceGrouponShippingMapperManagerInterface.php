<?php


namespace Drupal\commerce_groupon;


use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;

interface CommerceGrouponShippingMapperManagerInterface {

  /**
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   Drupal order.
   * @param array $groupon_order
   *   Groupon order.
   *
   * @return \Drupal\commerce_shipping\Entity\ShipmentInterface
   *   Generated shipment.
   */
  public function getShipment(OrderInterface $order,$groupon_order);


  /**
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   Drupal order.
   *
   * @return string
   *   Carrier ID.
   */
  public function getCarrier(OrderInterface $order);

  /**
   * Get shiping Item.
   *
   * @param \Drupal\commerce_order\Entity\OrderItemInterface $order_item
   *   Drupal Order Item
   * @param array $line_item
   *   Groupon Line Item.
   * @param array $groupon_order
   *   Groupon order.
   *
   * @return \Drupal\commerce_shipping\ShipmentItem
   *   Shipping item.
   */
  public function getShippingItem(OrderItemInterface $order_item, $line_item, $groupon_order);

  }