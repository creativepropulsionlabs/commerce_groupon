<?php

namespace Drupal\commerce_groupon\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ProductVariationUpc constraint.
 */
class ProductVariationUpcConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    if (!$item = $items->first()) {
      return;
    }

    $upc = $item->value;
    if (isset($upc) && $upc !== '') {
      $upc_exists = (bool) \Drupal::entityQuery('commerce_product_variation')
        ->condition('groupon_upc', $upc)
        ->condition('variation_id', (int) $items->getEntity()->id(), '<>')
        ->range(0, 1)
        ->count()
        ->execute();

      if ($upc_exists) {
        $this->context->buildViolation($constraint->message)
          ->setParameter('%upc', $this->formatValue($upc))
          ->addViolation();
      }
    }
  }

}
