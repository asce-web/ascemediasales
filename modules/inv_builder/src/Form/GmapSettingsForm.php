<?php

namespace Drupal\inv_builder\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class GmapSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inv_builder_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('inv_builder.gmap_settings');
    $form['shortcodes'] = [
      '#type' => 'details',
      '#title' => t('Shortcode Settings'),
    ];
    $form['shortcodes']['gmap_api_key'] = [
        '#type' => 'textfield',
        '#title' => t('Gmap API key'),
        '#default_value' => $config->get('gmap_api_key'),
    ];
    return parent::buildForm($form, $form_state);
  }
  
  public function getEditableConfigNames() {
    return [
      'inv_builder.gmap_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('inv_builder.gmap_settings')
      ->set('gmap_api_key', $values['gmap_api_key'])
      ->save();
    drupal_flush_all_caches();
  }

}
