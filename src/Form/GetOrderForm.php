<?php

namespace Drupal\commerce_groupon\Form;


use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Commerce GroupOn get order.
 */
class GetOrderForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_groupon_get_order';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['groupon_order_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Groupon Order ID'),
      '#description' => $this->t('Example: GG-HXZY-7T4W-2YJT-KTLY	'),
    ];

    $form['start_datetime'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start datetime'),
      '#description' => $this->t('Period should be less then 1 day long) Timestamp should be in MM/DD/YYYY HH:MM format (UTC)'),
    ];
    $form['end_datetime'] = [
      '#type' => 'textfield',
      '#title' => $this->t('End datetime'),
      '#description' => $this->t('Period should be less then 1 day long) Timestamp should be in MM/DD/YYYY HH:MM format (UTC)'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Fetch Order'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $groupon_order_data = [
      'groupon_order_id' => $form_state->getValue('groupon_order_id'),
      'start_datetime' => $form_state->getValue('start_datetime'),
      'end_datetime' => $form_state->getValue('end_datetime'),
    ];
    $orders = commerce_groupon_get_orders($groupon_order_data);
    if ($orders) {
      foreach ($orders as $key => $order) {
        if ($order['orderid'] != $groupon_order_data['groupon_order_id']) {
          unset($orders[$key]);
        }
      }
    }
    try {
      commerce_groupon_create_order($orders);
    } catch (InvalidPluginDefinitionException $e) {
      \Drupal::logger('commerce_groupon_Exception')->debug('<pre>@data</pre>', ['@data' => print_r($e->getMessage(),1)]);
    } catch (PluginNotFoundException $e) {
      \Drupal::logger('commerce_groupon_Exception')->debug('<pre>@data</pre>', ['@data' => print_r($e->getMessage(),1)]);
    } catch (EntityStorageException $e) {
      \Drupal::logger('commerce_groupon_Exception')->debug('<pre>@data</pre>', ['@data' => print_r($e->getMessage(),1)]);
    }
    $purchased_orders = commerce_groupon_get_purchased_orders($groupon_order_data);
    if ($purchased_orders) {
      foreach ($purchased_orders as $key => $purchased_order) {
        if ($purchased_order['po_number'] != $groupon_order_data['groupon_order_id']) {
          unset($purchased_order[$key]);
        }
      }
    }
    if (!empty($purchased_orders)) {
      try {
        commerce_groupon_process_purchased_orders($purchased_orders);
      } catch (InvalidPluginDefinitionException $e) {
        \Drupal::logger('commerce_groupon_Exception')->debug('<pre>@data</pre>', ['@data' => print_r($e->getMessage(),1)]);
      } catch (PluginNotFoundException $e) {
        \Drupal::logger('commerce_groupon_Exception')->debug('<pre>@data</pre>', ['@data' => print_r($e->getMessage(),1)]);
      }
    }
  }

}
