<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "row",
 *   title = @Translation("Row"),
 *   description = @Translation("Builds a div with row or row-fluid class"),
 *   group = @Translation("Layout"),
 * )
 */

class BuilderRow extends BuilderElement{

  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'fluid' => 'no',
      'class' => '',
      'flex' => '',
      'wrapper' => '',
      'wrapper_class' => '',
        ), $attributes);
    $attrObj->addClass('builder-row');
    $attrObj->addClass($attrs['class']);
    if($attrs['flex']){
      $attrObj->addClass('row-flex');
    }
    $row_class = $attrs['fluid'] == 'yes' ? 'row-fluid' : 'row';

    if($attrs['wrapper']){
	  return '<div' . $attrObj->__toString() . '><div class="'. $attrs['wrapper_class'] .'"><div class="' . $row_class . '">' . $text . '</div></div></div>';
    }else{
      return '<div' . $attrObj->__toString() . '><div class="' . $row_class . '">' . $text . '</div></div>';
    }
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['general_options']['flex'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Row flex'),
      '#default_value' => $this->get('flex', ''),
      '#description' => $this->t('Make all columns in row has same height use flex display'),
    );
    
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Class'),
      '#default_value' => $this->get('class', ''),
    );
    
    $form['general_options']['wrapper'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add wrapper'),
      '#description' => $this->t('Add div element wrap row'),
      '#default_value' => $this->get('wrapper', 0),
    );
    
    $form['general_options']['wrapper_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Wrapper class'),
      '#description' => $this->t('Example: container'),
      '#default_value' => $this->get('wrapper_class', ''),
      '#states' => [
        'visible' => [
          ':input[name=wrapper]' => ['checked' => TRUE],
        ]
      ]
    );
    $form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();
    
    return $form;
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

}