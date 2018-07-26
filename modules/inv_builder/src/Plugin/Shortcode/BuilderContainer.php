<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
/**
 * Provides a shortcode for container element.
 *
 * @Shortcode(
 *   id = "container",
 *   title = @Translation("Container"),
 *   description = @Translation("Builds a div with any class"),
 *   group = @Translation("Layout"),
 * )
 */
class BuilderContainer extends BuilderElement{
  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'class' => '',
        ), $attributes
    );
    $attrObj->addClass($attrs['class']);
    return '<div' . $attrObj->__toString() . '>' . $text . '</div>';
  }
  
  public function processBuilder($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class', ''),
    );
    $form['design_options'] += $this->designOptions();
    $form['design_options']['custom_css'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Custom CSS'),
      '#default_value' => $this->get('custom_css',''),
    );
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }
}
