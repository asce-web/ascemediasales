<?php

/**
 * @file
 * Contains \Drupal\inv_builder\Plugin\Shortcode\BuilderCarousel.
 */

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\inv_builder\Plugin\Shortcode\BuilderElement;

/**
 * The carousel item shortcode.
 *
 * @Shortcode(
 *   id = "carousel",
 *   title = @Translation("Carousel Item"),
 *   description = @Translation("Carousel Slide Item"),
 *   group = @Translation("Content"),
 *   parent = {
 *     "carousels"
 *   }
 * )
 */
class BuilderCarousel extends BuilderElement {
      /**
   * {@inheritdoc}
   */
  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
	// Merge with default attributes.
    $attributes = $this->getAttributes(array(
        'image' => 0
    ),$attributes);
    $fid = str_replace('file:', '', $attributes['image']);

    global $shortcode_carousel_stack;
	global $thumbnail;
    if (!is_array($shortcode_carousel_stack)) $shortcode_carousel_stack = array();
    $active = "";
    if (Count($shortcode_carousel_stack) == 0) $active = "active";

	if($file = \Drupal\file\Entity\File::load($fid)){
	  $path = file_create_url($file->getFileUri());
	  $item = "<div class='item ".$active."'><img alt='' src='". $path ."'/>".$text."</div>";
	  $shortcode_carousel_stack[] = $item;
	  $thumbnail[] = $path;
    }

    return $item;
  }



  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['image'] = array(
        '#type' => 'image_browser',
        '#title' => $this->t('Image'),
        '#default_value' => ['fid:' => $this->get('image',0)],
    );
    return $form;
  }
}
