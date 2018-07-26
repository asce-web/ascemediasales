<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
/**
 * Provides a shortcode for tabs wrapper.
 *
 * @Shortcode(
 *   id = "tabs",
 *   title = @Translation("Tabs"),
 *   description = @Translation("Togglable tabs"),
 *   group = @Translation("Content"),
 *   child = {
 *     "tab"
 *   }
 * )
 */
class BuilderTabs extends BuilderElement {

  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    
    $attributes = $this->getAttributes(array(
      'class' => '',
      'fade' => '',
	  'tab_style' => '',
        ), $attributes
    );
    
    global $builder_tabs_stack;
    $return =array(
      '#theme' => 'inv_builder_tabs',
      '#tabs' => $builder_tabs_stack,
      '#fade' => $attributes['fade'],
      '#class' => $attributes['class'],
	  '#tab_style' => $attributes['tab_style'],
    );
    $builder_tabs_stack = array();
    return $this->render($return);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['general_options']['fade'] = array(
      '#type' => 'select',
      '#title' => $this->t('Tabs effect'),
      '#options' => array('' => $this->t('None'), 'fade' => $this->t('Fade')),
      '#default_value' => $this->get('fade'),
    );
    
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
    );
    
    return $form;
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);

    return $text;
  }

}
