<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for tab element.
 *
 * @Shortcode(
 *   id = "tab",
 *   title = @Translation("Tab"),
 *   description = @Translation("Tab content"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "tabs"
 *   },
 *   child = {
 *     "html",
 *     "single_image",
 *     "embed"
 *   }
 * )
 */
class BuilderTab extends BuilderElement {

  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
	  'icon_library' => '',
      'class' => '',
        ), $attributes
    );
    global $builder_tabs_stack;
    $class = array($attrs['class']);
    $classes = $this->addClass($class, 'tab-pane');
    if (empty($builder_tabs_stack)) {
      $builder_tabs_stack = array();
      $classes = $this->addClass($classes, 'active');
    }
    $tab_id = \Drupal\Component\Utility\Html::getUniqueId('inv_builder_tab_' . REQUEST_TIME);
    $output = array(
      'title' => $attrs['title'],
      'icon' => $attrs['icon'],
      'tab_id' => $tab_id,
      'class' => $classes,
      'content' => $text,
    );
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    $builder_tabs_stack[] = $output;
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $this->get('icon'),
      '#attributes' => ['class' => ['icon-select']],
    );
    $form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
	$form['general_options']['title'] = array(
      '#type' => 'textfield',
	  '#required' => true,
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
    );
    $form['general_options']['custom_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('custom_class'),
    );

    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'icon' => '',
      'icon_library' => '',
	  'title' => '',
        ), $attributes
    );
	$icon = "";
	if ($attrs['icon']) {
		$icon = "<i class='".$attrs['icon']. "'></i>";
	}
	$output['#markup'] = $icon. ' '.$attrs['title'].$text;
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    return $this->render($output);
  }

}
