<?php

namespace Drupal\commerce_groupon\Entity;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_promotion\Entity\Promotion;

class GrouponPromotion extends Promotion {

  /**
   * {@inheritdoc}
   */
  public function applies(OrderInterface $order) {
    if ($order->hasField('groupon_order_id') && !$order->get('groupon_order_id')->isEmpty()) {
      return FALSE;
    }
    return parent::applies($order);
  }

}
