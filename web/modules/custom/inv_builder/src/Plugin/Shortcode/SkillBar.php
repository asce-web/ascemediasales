<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for progress.
 *
 * @Shortcode(
 *   id = "progress",
 *   title = @Translation("Progress"),
 *   description = @Translation("Progress"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class Skillbar extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);

    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
	  'icon_library' => '',
	  'skillbar_style' => '',
      'percent' => '0',
      'class' => '',
	  'id' => '',
        ), $attributes
    );

	$classes = $attrs['class']. ' '. $attrs['skillbar_style'];
    $output = array(
      '#theme' => 'inv_builder_skillbar',
      '#title' => $attrs['title'],
      '#icon' => $attrs['icon'],
      '#percent' => $attrs['percent'],
	  '#skillbar_style' => $attrs['skillbar_style'],
      '#class' => $classes,
      '#id' => $attrs['id'],
    );
	
	if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
	$output['#attached']['library'] = 'inv_builder/skillbar';
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['general_options']['icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $this->get('icon', ''),
      '#attributes' => ['class' => ['icon-select']],
	  '#prefix' => '<div class="col-md-3">',
	  '#suffix' => '</div>',
    );

    $form['general_options']['title'] = array(
      '#type' => 'textfield',
	  '#required' => true,
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
	  '#prefix' => '<div class="col-md-9">',
	  '#suffix' => '</div>',
    );

	$form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
	  '#prefix' => '<div class="col-md-12">',
	  '#suffix' => '</div>',
    );
	
    $form['general_options']['percent'] = array(
      '#type' => 'textfield',
	  '#required' => true,
      '#title' => $this->t('Pecent'),
      '#default_value' => $this->get('percent'),
	  '#prefix' => '<div class="col-md-3">',
	  '#suffix' => '</div>',
    );

	$form['general_options']['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('HTML ID'),
      '#default_value' => $this->get('id'),
	  '#prefix' => '<div class="col-md-3">',
	  '#suffix' => '</div>',
    );

    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
	  '#prefix' => '<div class="col-md-6">',
	  '#suffix' => '</div>',
    );

    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
      'percent' => '0',
      'class' => '',
        ), $attributes
    );
    return '[Progress: ' . $attrs['title'] . ' ' . $attrs['percent'] . '%]';
  }

}