<?php

namespace Drupal\commerce_groupon\Form;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderType;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Commerce groupon settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_groupon_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['commerce_groupon.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['supplier_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Supplier id'),
      '#default_value' => $this->config('commerce_groupon.settings')->get('supplier_id'),
      '#required' => TRUE,
    ];
    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token'),
      '#default_value' => $this->config('commerce_groupon.settings')->get('token'),
      '#required' => TRUE,
    ];

    $order_types = \Drupal::entityTypeManager()->getStorage('commerce_order_type')->loadMultiple();
    $options = [];
    foreach ($order_types as $order_type) {
      $options[$order_type->id()] = $order_type->label();
    }
    $form['order_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Order type'),
      '#options' => $options,
      '#default_value' => $this->config('commerce_groupon.settings')->get('order_type'),
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('commerce_groupon.settings')
      ->set('supplier_id', $form_state->getValue('supplier_id'))
      ->set('token', $form_state->getValue('token'))
      ->set('order_type', $form_state->getValue('order_type'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
