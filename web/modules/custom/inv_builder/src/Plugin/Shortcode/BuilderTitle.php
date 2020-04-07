<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for title.
 *
 * @Shortcode(
 *   id = "title",
 *   title = @Translation("Title"),
 *   description = @Translation("Render Title element like block title"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderTitle extends BuilderElement {

  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
	  'icon' => '',
	  'icon_library' => '',
      'subtitle' => '',
      'backword' => '',
	  'title_style' => '',
	  'class' => '',
        ), $attributes
    );
    $output = [
      '#theme' => 'inv_builder_title',
      '#title' => $attrs['title'],
	  '#icon' => $attrs['icon'],
      '#subtitle' => $attrs['subtitle'],
      '#backword' => $attrs['backword'],
      '#class' => $attrs['class'],
	  '#title_style' => $attrs['title_style'],
    ];
	if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    return $this->render($output);
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array(
      'title' => '',
	  'icon' => '',
	  'icon_library' => '',
      'subtitle' => '',
      'backword' => '',
      'class' => '',
        ), $attributes
    );
	$output['#markup'] = '<h2><i class="' . $attrs['icon'].'"></i>'.$attrs['title'] . '</h2>';
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form  = parent::settingsForm($form, $form_state);
    $form['general_options']['icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon Title'),
      '#default_value' => $this->get('icon', ''),
      '#attributes' => ['class' => ['icon-select']],
	  '#prefix' => '<div class="col-md-3">',
	  '#suffix' => '</div>',
    );
	$form['general_options']['title'] = [
      '#type' => 'textfield',
	  '#required' => true,
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
	  '#prefix' => '<div class="col-md-9">',
	  '#suffix' => '</div>',
    ];
	$form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
	  '#prefix' => '<div class="col-md-12">',
	  '#suffix' => '</div>',
    );
    $form['general_options']['subtitle'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Sub title'),
      '#default_value' => $this->get('subtitle'),
	  '#rows' => 3,
	  '#prefix' => '<div class="col-md-12">',
	  '#suffix' => '</div>',
    ];
    $form['general_options']['backword'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Backword'),
      '#default_value' => $this->get('backword'),
	  '#prefix' => '<div class="col-md-6">',
	  '#suffix' => '</div>',
    ];
    $form['general_options']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
	  '#prefix' => '<div class="col-md-6">',
	  '#suffix' => '</div>',
    ];
	$form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }
}