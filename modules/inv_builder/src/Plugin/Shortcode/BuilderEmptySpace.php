<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for empty space.
 *
 * @Shortcode(
 *   id = "empty_space",
 *   title = @Translation("Empty Space"),
 *   description = @Translation("Add Empty Html element"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderEmptySpace extends BuilderElement{
  
  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'class' => '',
      'height' => '',
        ), $attributes
    );
    $attribute = new Attribute();
    if($attrs['class']){
      $attribute->addClass($attrs['class']);
    }
    if($attrs['height']){
      $attribute->setAttribute('style', 'height:' . $attrs['height'] . 'px');
    }
    return '<div' . $attribute->__toString() . '></div>';
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array(
      'class' => '',
      'height' => '',
        ), $attributes
    );
    return '<div style="text-align:center; color: #888; font-size: 10px"><span class="fa fa-angle-up"></span><br/><span>' . $attrs['height'] . 'px</span><br/><span class="fa fa-angle-down"></span></div>';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['general_options']['height'] = array(
      '#title' => $this->t('Height'),
      '#type' => 'number',
      '#field_suffix' => 'px',
      '#default_value' => $this->get('height', ''),
      '#description' => $this->t('Enter empty space height'),
    );
    
    $form['general_options']['class'] = array(
      '#title' => $this->t('Class'),
      '#type' => 'textfield',
      '#default_value' => $this->get('class', ''),
    );
    
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }
}
