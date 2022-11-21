<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\inv_builder\Plugin\Shortcode\BuilderElement;

/**
 * Provides a shortcode for Accordion wrapper.
 *
 * @Shortcode(
 *   id = "accordions",
 *   title = @Translation("Accordions"),
 *   description = @Translation("Accordion wrapper"),
 *   group = @Translation("Content"),
 *   child = {
 *     "accordion"
 *   }
 * )
 */
class BuilderAccordions extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);

    $attrs = $this->getAttributes(array(
      'type' => 'accordion',
      'class' => '',
        ), $attributes
    );
    $accordion_wrapper_id = Html::getUniqueId('inv_builder_accordion_wrapper_' . REQUEST_TIME);
    global $builder_accordions_stack;
    $class = array($attrs['class']);
    if ($attrs['type'] == "toggle") {
      $classes = $this->addClass($class, 'toggle');
    } else {
      $classes = $this->addClass($class, 'accordion');
    }

    $output = array(
      '#theme' => 'inv_builder_accordions',
      '#type' => $attrs['type'],
      '#accordions' => $builder_accordions_stack,
      '#wrapper_id' => $accordion_wrapper_id,
      '#class' => $classes,
      '#attached' => [],
    );
    
    foreach($builder_accordions_stack as $stack){
      if(isset($stack['#attached'])){
        $output['#attached'] =  array_merge($output['#attached'], $stack['#attached']);
      }
    }
	$builder_accordions_stack = [];
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['general_options']['type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Accordion Type'),
      '#options' => array('accordion' => $this->t('Accordion'), 'toggle' => $this->t('Toggle')),
      '#default_value' => $this->get('type'),
    );

    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
    );

    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

}
