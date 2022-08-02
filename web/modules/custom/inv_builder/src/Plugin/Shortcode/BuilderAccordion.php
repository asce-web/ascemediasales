<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\inv_builder\Plugin\Shortcode\BuilderElement;

/**
 * Provides a shortcode for Accordion.
 *
 * @Shortcode(
 *   id = "accordion",
 *   title = @Translation("Accordion"),
 *   description = @Translation("Accordion content"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "accordions"
 *   },
 *   child = {
 *     "html",
 *     "single_image"
 *   }
 * )
 */
class BuilderAccordion extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    $attrs = $this->getAttributes(array(
      'title' => 'no',
      'icon' => '',
      'icon_library' => '',
      'expand' => false,
      'class' => '',
        ), $attributes
    );
    global $builder_accordions_stack;
    $collapsed = "collapsed";
    if ($attrs['expand'] == true) {
      $collapsed = '';
    }
    $accordion_id = Html::getUniqueId('inv_builder_accordion_' . REQUEST_TIME);
    $output = array(
      'title' => $attrs['title'],
      'accordion_id' => $accordion_id,
      'icon' => $attrs['icon'],
      'class' => $attrs['class'],
      'collapsed' => $collapsed,
      'content' => $text,
    );
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    $builder_accordions_stack[] = $output;
    return '';
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
    );
    $form['general_options']['icon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#default_value' => $this->get('icon', ''),
      '#attributes' => ['class' => ['icon-select']],
    );
    $form['general_options']['icon_library'] = array(
      '#type' => 'hidden',
      '#default_value' => $this->get('icon_library', ''),
    );
    $form['general_options']['expand'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Set the panel expand as default?'),
      '#default_value' => $this->get('expand'),
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