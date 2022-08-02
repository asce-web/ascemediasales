<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\views\Views;

/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "embed",
 *   title = @Translation("Embed View"),
 *   description = @Translation("Embed view"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderViewEmbed extends BuilderElement {

  function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array('view' => ''), $attributes);
    $value = explode(':', $attrs['view']);
    $view = Views::getView($value[0]);
    if($view){
      $output = $view->buildRenderable($value[1]);
      return $this->render($output);
    }
    return '';
  }

  function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array('view' => ''), $attributes);
    return '[views: ' . $attrs['view'] . ']';
  }
  
  function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $options = [];
    foreach (\Drupal\views\Views::getAllViews() as $view){
      //$options[$view->label()] = [];
      foreach($view->get('display') as $display){
        if($display['id'] != 'default'){
          $options[$view->label()][$view->id() . ':' . $display['id']] = $view->label() . ': ' .$display['display_title'];
        }
      }
    }
    $form['general_options']['view'] = array(
      '#title' => $this->t('Select View'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $this->get('view'),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }

}
