<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
/**
 * Provides a shortcode for bootstrap column.
 *
 * @Shortcode(
 *   id = "col",
 *   title = @Translation("Column"),
 *   description = @Translation("Builds a div with col-screen-size class"),
 *   group = @Translation("Layout"),
 * )
 */
class BuilderColumn extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrObj = $this->createAttribute($attributes);
    $attrs = $this->getAttributes(array(
      'class' => '',
	  'id' => '',
      'xs' => '',
      'sm' => '',
      'md' => '',
      'lg' => '',
        ), $attributes
    );
	if ($attrs['id']) {
		$attrObj['id'] = $attrs['id'];
	}
    $attrObj->addClass($attrs['class']);
    foreach (['xs', 'sm', 'md', 'lg'] as $size) {
      if ($attrs[$size]) {
        $attrObj->addClass('col-' . $size . '-' . $attrs[$size]);
      }
    }
    return '<div' . $attrObj->__toString() . '>' . $text . '</div>';
  }

  public function processBuilder($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['xs'] = array(
      '#type' => 'number',
      '#title' => $this->t('XS'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('xs', 12),
    );

    $form['general_options']['sm'] = array(
      '#type' => 'number',
      '#title' => $this->t('SM'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('sm', 12),
    );

    $form['general_options']['md'] = array(
      '#type' => 'number',
      '#title' => $this->t('MD'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('md', 6),
    );

    $form['general_options']['lg'] = array(
      '#type' => 'number',
      '#title' => $this->t('LG'),
      '#min' => 0,
      '#max' => 12,
      '#default_value' => $this->get('lg', 6),
    );

	$form['general_options']['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Column HTML ID'),
      '#default_value' => $this->get('id', ''),
    );
    
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
