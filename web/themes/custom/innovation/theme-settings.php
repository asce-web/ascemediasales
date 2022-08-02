<?php

use Drupal\innovation\LayoutSetting;
use Drupal\innovation\PresetSetting;
use Drupal\innovation\FontsSetting;
use Drupal\innovation\BasicSetting;

function innovation_form_system_theme_settings_alter(&$form, &$form_state){
  $form['innovation_theme_settings'] = [
    '#type' => 'vertical_tabs',
    '#parents' => ['innovation_theme_settings'],
  ];
  $form['theme_settings']['#group'] = 'innovation_theme_settings';
  $form['favicon']['#group'] = 'innovation_theme_settings';
  LayoutSetting::innovation_layout_settings_form_alter($form);
  PresetSetting::innovation_preset_settings_form_alter($form);
  FontsSetting::innovation_fonts_settings_form_alter($form);
  BasicSetting::innovation_basic_settings_form_alter($form);
  $form['#submit'][] = 'innovation_theme_settings_submit';
  $form['#validate'][] = 'innovation_theme_settings_validate';

}

function innovation_theme_settings_validate(&$form, &$form_state) {
    $layouts = '';
    $i = 0;
    $values= $form_state->getValues();
    while (!empty($values['inv_layout_' . $i]) && $i < 100) {
        $layouts .= $values['inv_layout_' . $i];
        $i++;
    }
}

function innovation_theme_settings_submit(&$form, &$form_state) {
	$config =\Drupal::service('config.factory')->getEditable('innovation.settings');
    $config->set('updated', true);
	$config->save();
    unset($_SESSION['innovation_default_preset']);
    //unset($_SESSION['innovation_default_direction']);
    //unset($_SESSION['innovation_layout']);
}