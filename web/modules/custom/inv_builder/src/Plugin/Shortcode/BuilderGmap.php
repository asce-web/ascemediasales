<?php

/**
 * @file
 * Contains \Drupal\inv_builder\Plugin\Shortcode\BuilderGmap.
 */

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * The Gmap wrapper shortcode.
 *
 * @Shortcode(
 *   id = "gmap",
 *   title = @Translation("Gmap wrapper"),
 *   description = @Translation("Create gmap"),
 *   child = {
 *     "gmap_marker"
 *   }
 * )
 */
class BuilderGmap extends BuilderElement {

  /**
   * {@inheritdoc}
   */
  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

    global $builder_gmap_stack;
    $attrs = $this->getAttributes(array(
      'height' => '',
      'custom_style' => '',
      'zoom' => '',
      ), $attributes
    );

    $output = [
      '#theme' => 'inv_builder_gmap',
      '#height' => $attrs['height'],
      '#zoom' => $attrs['zoom'],
      '#markers' => $builder_gmap_stack,
      '#custom_style' => $attrs['custom_style'],
      '#attached' => array(
        'library' => array(
          'inv_builder/gmap'
        ),
      )
    ];
	
    $builder_gmap_stack = array();
    return $this->render($output);
  }

  public function processBuilder($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $output = array(
      '#markup' => $text,
      '#attached' => ['library' => ['inv_builder/gmap-admin']],
    );
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['height'] = array(
      '#type' => 'number',
      '#title' => $this->t('Height'),
      '#default_value' => $this->get('height', 400),
    );
    $form['general_options']['zoom'] = array(
      '#type' => 'number',
      '#title' => $this->t('Zoom Level'),
      '#default_value' => $this->get('zoom', 14),
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class', ''),
    );
    $form['advanced'] = array(
      '#type' => 'details',
      '#title' => $this->t('Custom style'),
      '#group' => 'element_settings',
    );
    $form['advanced']['custom_style'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Gmap custom style'),
      '#description' => $this->t('Buil your style at <a href="https://mapstyle.withgoogle.com/" target="_blank">https://mapstyle.withgoogle.com/</a> and paste json code here'),
      '#default_value' => $this->get('custom_style'),
    );
    return $form;
  }

}
