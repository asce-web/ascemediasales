<?php

/**
 * @file
 * Contains \Drupal\inv_builder\Plugin\Shortcode\BuilderCarousels.
 */

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\inv_builder\Plugin\Shortcode\BuilderElement;

/**
 * The carousels shortcode.
 *
 * @Shortcode(
 *   id = "carousels",
 *   title = @Translation("Carousels"),
 *   description = @Translation("Carousel wrapper"),
 *   group = @Translation("Content"),
 *   child = {
 *     "carousel"
 *   }
 * )
 */
class BuilderCarousels extends BuilderElement {
      /**
   * {@inheritdoc}
   */
  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
      // Merge with default attributes.
      $attributes = $this->getAttributes(array(
          'carousel_id' => '',
          'pager' => 'true',
          'control' => 'true',
          'auto' => 'true',
		  'thumbnail' => ''
        ),$attributes);

      $wrapper_id = Html::getId('inv-carousel-'. uniqid());
      global $shortcode_carousel_stack;
	  global $thumbnail;
      $output = array(
          '#theme' => 'inv_builder_carousels',
          '#carousel_id' => $wrapper_id,
          '#carousels' =>  $shortcode_carousel_stack,
          '#pager' => $attributes['pager'],
          '#control' => $attributes['control'],
          '#auto' => $attributes['auto'],
		  '#thumbnail' => $thumbnail,
      );
	  if (Count($thumbnail) > 0) {
		$output['#attached']['library'] = 'inv_builder/inv_carousel';  
	  }
      $shortcode_carousel_stack = null;
	  $thumbnail = null;
      return $this->render($output);
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    return $text;
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    
    $form['general_options']['pager'] = array(
      '#type' => 'select',
      '#title' => $this->t('Pager'),
      '#options' => array('false' => $this->t('No'), 'true' => $this->t('Yes'), 'thumbnail' => $this->t('Thumbnail')),
	  '#description' => t('Display dot pager control on slide when choosing Yes or Thumbnail option image'),
      '#default_value' => $this->get('pager'),
    );
    
    $form['general_options']['control'] = array(
      '#type' => 'select',
      '#title' => $this->t('Show Next/Previous button'),
	  '#options' => array('false' => $this->t('No'), 'true' => $this->t('Yes')),
	  '#description' => t('Display next/previous icon on slide when choosing Yes'),
      '#default_value' => $this->get('control'),
    );
    
	$form['general_options']['auto'] = array(
      '#type' => 'select',
      '#title' => $this->t('Auto Slide'),
	  '#options' => array('false' => $this->t('No'), 'true' => $this->t('Yes')),
	  '#description' => t('The slide will auto transition when choosing Yes'),
      '#default_value' => $this->get('auto', 'true'),
    );

    return $form;
  }
}
