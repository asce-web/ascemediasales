<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * @Shortcode(
 *   id = "stats",
 *   title = @Translation("Statistics Counter"),
 *   description = @Translation("Statistics counter element"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderStats extends BuilderElement{
  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
	  'icon_library' => '',
      'number' => '',
      'duration' => 2000,
      'class' => '',
	  'id' => '',
        ), $attributes
    );
    $output = [
      '#theme' => 'inv_builder_stats',
      '#title' => $attrs['title'],
      '#class' => $attrs['class'],
	  '#id' => $attrs['id'],
      '#icon' => $attrs['icon'],
      '#number' => $attrs['number'],
      '#duration' => $attrs['duration'],
      '#attached' => ['library' => ['inv_builder/stats']],
    ];
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'][] = $icon_plugin->library();
    }

    return $this->render($output);
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = array(
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
      '#default_value' => $this->get('title'),
    );
    $form['general_options']['icon'] = array(
      '#title' => $this->t('Icon'),
      '#type' => 'textfield',
      '#default_value' => $this->get('icon'),
      '#attributes' => ['class' => ['icon-select']],
    );
    $form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
    $form['general_options']['number'] = array(
      '#title' => $this->t('Stats Number'),
      '#type' => 'number',
      '#default_value' => $this->get('number'),
    );
    
    $form['general_options']['duration'] = array(
      '#title' => $this->t('Duration'),
      '#type' => 'number',
      '#default_value' => $this->get('duration', 3000),
    );

    $form['general_options']['id'] = array(
      '#title' => $this->t('HTML ID'),
      '#type' => 'textfield',
      '#default_value' => $this->get('id'),
    );
	
    $form['general_options']['class'] = array(
      '#title' => $this->t('Custom class'),
      '#type' => 'textfield',
      '#default_value' => $this->get('class'),
    );
    
    return $form;
  }
}
