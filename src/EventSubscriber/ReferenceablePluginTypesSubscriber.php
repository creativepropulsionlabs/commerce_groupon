<?php

namespace Drupal\commerce_groupon\EventSubscriber;

use Drupal\commerce\Event\CommerceEvents;
use Drupal\commerce\Event\ReferenceablePluginTypesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReferenceablePluginTypesSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      CommerceEvents::REFERENCEABLE_PLUGIN_TYPES => 'onPluginTypes',
    ];
  }

  /**
   * Registers the 'commerce_shipping_method' plugin type as referenceable.
   *
   * @param \Drupal\commerce\Event\ReferenceablePluginTypesEvent $event
   *   The event.
   */
  public function onPluginTypes(ReferenceablePluginTypesEvent $event) {
    $plugin_types = $event->getPluginTypes();
    $plugin_types['commerce_groupon_shipping_mapper'] = t('Groupon shipping method');
    $event->setPluginTypes($plugin_types);
  }

}
