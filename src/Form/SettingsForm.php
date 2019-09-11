<?php

namespace Drupal\commerce_groupon\Form;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderType;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity\BundleFieldDefinition;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

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
    $order_types = array_map(function ($order_type) {
      return $order_type->label();
    }, $order_types);

    $form['order_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Order type'),
      '#options' => $order_types,
      '#default_value' => $this->config('commerce_groupon.settings')->get('order_type'),
      '#required' => TRUE,
    ];
    $form['debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Debug'),
      '#default_value' => $this->config('commerce_groupon.settings')->get('debug'),
    ];

    $form['start_datetime'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start datetime'),
      '#description' => $this->t('(DEBUG USE ONLY, Period should be less then 1 day long) Timestamp should be in MM/DD/YYYY HH:MM format (UTC)'),
      '#default_value' => $this->config('commerce_groupon.settings')->get('start_datetime'),
      '#required' => TRUE,
    ];
    $form['end_datetime'] = [
      '#type' => 'textfield',
      '#title' => $this->t('End datetime'),
      '#description' => $this->t('(DEBUG USE ONLY, Period should be less then 1 day long) Timestamp should be in MM/DD/YYYY HH:MM format (UTC)'),
      '#default_value' => $this->config('commerce_groupon.settings')->get('end_datetime'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $bundle_id = $form_state->getValue('order_type');
    $this->config('commerce_groupon.settings')
      ->set('supplier_id', $form_state->getValue('supplier_id'))
      ->set('token', $form_state->getValue('token'))
      ->set('debug', $form_state->getValue('debug'))
      ->set('start_datetime', $form_state->getValue('start_datetime'))
      ->set('end_datetime', $form_state->getValue('end_datetime'))
      ->set('order_type', $bundle_id)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
