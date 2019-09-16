<?php

namespace Drupal\commerce_groupon\Entity;

use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\Entity\ShippingMethod;

class GrouponShippingMethod extends ShippingMethod {

  /**
   * {@inheritdoc}
   */
  public function applies(ShipmentInterface $shipment) {
    if (strpos($shipment->getOrder()->getCustomer()->getEmail(), 'groupon') === 0) {
      return FALSE;
    }
    return parent::applies($shipment);
  }

}
