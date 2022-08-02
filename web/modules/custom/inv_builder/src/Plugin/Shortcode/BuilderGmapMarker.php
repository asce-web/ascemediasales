<?php

/**
 * @file
 * Contains \Drupal\inv_builder\Plugin\Shortcode\BuilderGmapMarker.
 */

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;


/**
 * The map marker shortcode.
 *
 * @Shortcode(
 *   id = "gmap_marker",
 *   title = @Translation("Gmap Marker"),
 *   description = @Translation("Create gmap marker"),
 *   parent = {
 *     "gmap"
 *   },
 * )
 */
class BuilderGmapMarker extends BuilderElement {

  /**
   * {@inheritdoc}
   */
  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {

    $attrs = $this->getAttributes([
        'address' => '',
	    'link' => '',
        'title' => '',
        'image' => '',
        'phone' => '',
        'icon' => '',
        'lat' => '',
        'lng' => ''
	], $attributes);

    global $builder_gmap_stack;
    if($attrs['icon']){
      $fid = str_replace('file:', '', $attrs['icon']);
      $file = \Drupal\file\Entity\File::load($fid);
	  if (isset($file)) {
		$attrs['icon'] = file_create_url($file->getFileUri());
	  }
    }
    if (empty($builder_gmap_stack)) {
      $builder_gmap_stack = array();
    }
    $builder_gmap_stack[] = $attrs;
    return '';
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'address' => '',
    ), $attributes);
	$marker = '<center><i class="fa fa-map-marker"></i> ' . $attrs['title'] . ':' . $attrs['address']. '</center>';
	$output = [
      '#markup' => $marker,
      '#attached' => ['library' => ['inv_builder/gmap-api']],
    ];
    return $this->render($output);
  }
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['address'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#default_value' => $this->get('address', ''),
      '#required' => true,
    );
    $form['general_options']['title'] = array(
          '#title' => t('Title'),
          '#type' => 'textfield',
          '#required' => true,
          '#default_value' => $this->get('title', ''),
          '#description' => 'Address display on marker Popup',
    );
    $form['general_options']['lng'] = array(
          '#title' => t('Longitude'),
          '#type' => 'textfield',
	  '#required' => true,
          '#default_value' => $this->get('lng', ''),
    );
    $form['general_options']['lat'] = array(
          '#title' => t('Latitude'),
	  '#required' => true,
          '#type' => 'textfield',
          '#default_value' => $this->get('lat', ''),
    );

   $form['general_options']['find_ln_lg'] = array(
      '#type' => 'button',
      '#value' => 'Find longitude, latitude from address',
      '#id' => 'inv-builder-gmap-marker-find-ln-lg',
    );
    $form['general_options']['gmap_preview'] = array(
      '#markup' => '<div id="inv-builder-gmap-preview"></div>',
    );	 	
    $form['general_options']['image'] = array(
          '#title' => t('Image'),
          '#type' => 'textfield',
          '#default_value' => $this->get('image', ''),
          '#description' => 'Image display on marker Popup',
    );
    $form['general_options']['link'] = array(
          '#title' => t('Link'),
          '#type' => 'textfield',
          '#default_value' => $this->get('link', ''),
          '#description' => 'Link when clicking on addres of marker Popup',
    );
    $form['general_options']['icon'] = array(
          '#title' => t('Icon Mapker'),
          '#type' => 'image_browser',
          '#default_value' => $this->get('icon', ''),
    );
    $form['general_options']['phone'] = array(
          '#title' => t('Phone'),
          '#type' => 'textfield',
          '#default_value' => $this->get('phone', ''),
          '#description' => 'Phone display on marker Popup',
    );
    $form['#attached']['library'][] = 'inv_builder/gmap-admin';

    return $form;
  }
  
}
