<?php


namespace Drupal\commerce_groupon\Plugin\CommerceGrouponShippingMapper;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\commerce_shipping\Entity\Shipment;
use Drupal\commerce_shipping\ShipmentItem;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\physical\Weight;
use Drupal\physical\WeightUnit;
use Symfony\Component\DependencyInjection\ContainerInterface;


abstract class CommerceGrouponShippingMapperPluginPluginBase extends PluginBase  implements CommerceGrouponShippingMapperPluginInterface, ContainerFactoryPluginInterface{

  /**
   * Entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  var $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getCarrier(OrderInterface $order) {
    return 'UPS';
  }

  public function getShipment(OrderInterface $order, $groupon_order) {



      /** @var \Drupal\commerce_shipping\Entity\ShipmentInterface $shipments */
      $shipment = Shipment::create([
        'type' => 'default',
        'title' => $groupon_order['supplier'],
        'order_id' => $order->id(),
        'shipping_method' => $this->getShippingMethod($order, $groupon_order),
//    'shipping_service' => $groupon_order['shipping']['carrier'],
        'shipping_service' => $this->getShippingService($order, $groupon_order),
//    $shipping_method->getPlugin()->getServices()['default']->getId(),
        'shipping_profile' => $this->getShippingProfile($order, $groupon_order),
        // TODO items ????
        'items' => $this->getShippingItems($order, $groupon_order),
        'weight' => $this->getDrupalWeight($groupon_order['shipping']['product_weight'], $groupon_order['shipping']['product_weight_unit']),
        'amount' => $this->getPrice((string)$groupon_order['amount']['shipping'], $order->getTotalPrice()->getCurrencyCode()),
        'state' => 'draft',
      ]);
      return $shipment;
  }

  public function getShippingService(OrderInterface $order, $groupon_order) {
    $shipping_method = $this->getShippingMethod($order, $groupon_order);
    $service = $shipping_method->getPlugin()->getServices()['default']->getId();
    return $service;
  }

  public function getShippingItems(OrderInterface $order, $groupon_order) {
    $shipping_items = [];
    foreach ($order->getItems() as $order_item) {
      $groupon_line_item = Json::decode($order_item->getData('groupon_raw', ''));
      $shipping_items[] = $this->getShippingItem($order_item, $groupon_line_item, $groupon_order);
    }
    return $shipping_items;
  }

  /**
   * {@inheritdoc}
   */
  public function getShippingProfile(OrderInterface $order, $groupon_order) {
    // Create shipping profile.
    $shipping_profile = $order->getBillingProfile()->createDuplicate();
    $shipping_profile
      ->enforceIsNew(TRUE)
      ->save();
    return $shipping_profile;
  }

  /**
   * {@inheritdoc}
   */
  public function getShippingMethod(OrderInterface $order, $groupon_order) {
    /** @var \Drupal\commerce_shipping\Plugin\Commerce\ShippingMethod\ShippingMethodInterface[] $shipping_methods */
    $shipping_methods = $this->entityTypeManager
      ->getStorage('commerce_shipping_method')
      ->loadByProperties([
        'plugin.target_plugin_id' => 'groupon',
        ]);
    foreach ($shipping_methods as $shipping_method) {
      $configuration = $shipping_method->getPlugin()->getConfiguration();
      if ($configuration['method'] == $groupon_order["shipping"]["method"]){
        return $shipping_method;
      }
    }
  }

  public function getPrice($amount, $currency_code) {
    return new Price($amount, $currency_code);
  }

  public function getShippingItem(OrderItemInterface $order_item, $groupon_line_item, $groupon_order) {
    $shipping_item = new ShipmentItem([
      'order_item_id' => $order_item->id(),
      'title' => $order_item->label(),
      'quantity' => $order_item->getQuantity(),
      'weight' => $this->getDrupalWeight($groupon_line_item['weight'], $groupon_order['shipping']['product_weight_unit']),
      'declared_value' => $order_item->getUnitPrice(),
    ]);

    return $shipping_item;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep($this->defaultConfiguration(), $configuration);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [
      'module' => [$this->pluginDefinition['provider']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents'], []);
      $this->setConfiguration($values);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  protected function getDrupalWeight($number, $unit) {
    return new Weight(
      (string)$number,
      $this->getWeightUnit($unit)
    );
  }

  /**
   * To commerce weight convert helper.
   *
   * @param $name
   *   Groupon weight name.
   *
   * @return string
   *   Commerce weight.
   */
  protected function getWeightUnit($name) {
    $name = mb_strtolower($name);
    switch ($name) {
      case 'pounds':
        return WeightUnit::POUND;
        break;
      case 'pound':
        return WeightUnit::POUND;
        break;
      case 'kilogram':
        return WeightUnit::KILOGRAM;
        break;
      case 'kilograms':
        return WeightUnit::KILOGRAM;
        break;
      case 'gram':
        return WeightUnit::GRAM;
        break;
      case 'grams':
        return WeightUnit::GRAM;
        break;
      default:
        return WeightUnit::KILOGRAM;
        break;
    }
  }

}