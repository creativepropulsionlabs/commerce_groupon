<?php

namespace Drupal\commerce_groupon\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Ensures product variation UPC uniqueness.
 *
 * @Constraint(
 *   id = "ProductVariationUpc",
 *   label = @Translation("The UPC of the product variation.", context = "Validation")
 * )
 */
class ProductVariationUpcConstraint extends Constraint {

  public $message = 'The UPC %upc is already in use and must be unique.';

}
