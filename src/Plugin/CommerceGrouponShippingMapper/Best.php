<?php


namespace Drupal\commerce_groupon\Plugin\CommerceGrouponShippingMapper;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_price\Price;
use Drupal\commerce_shipping\Entity\Shipment;

/**
 * Provides the token commerce_autosku generator.
 *
 * @CommerceGrouponShippingMapper(
 *   id = "best",
 *   label = @Translation("Best"),
 *   groupon_shipping_method = "BEST",
 *   drupal_shipping_method_plugin = "flat_rate",
 *   weight = 1
 * )
 */

class Best extends CommerceGrouponShippingMapperPluginPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getCarrier(OrderInterface $order) {
    return 'USPS';
  }

}