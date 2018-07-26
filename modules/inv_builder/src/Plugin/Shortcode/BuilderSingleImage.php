<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for single image.
 *
 * @Shortcode(
 *   id = "single_image",
 *   title = @Translation("Single Image"),
 *   description = @Translation("Add Single Image element"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderSingleImage extends BuilderElement {
  
  public function process($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'image' => 0,
      'class' => '',
	  'link' => ''
    ),$attrs);
    $fid = str_replace('file:', '', $attrs['image']);
	$link = '';
    if($attrs['link']){
      if($attrs['link'] == '#'){
        $link = $attrs['link'];
      } else{
        try{
          $link = \Drupal\Core\Url::fromUserInput($attrs['link'])->toString();
        } catch (\Exception $e){
          $link = $attrs['link'];
        }
      }
    }

    if($file = \Drupal\file\Entity\File::load($fid)){
      if($link){
        return '<a href="' . $link . '"><img class="' . $attrs['class'] . '" src="' . file_create_url($file->getFileUri()) . '"/></a>';
      }else{
        return '<img class="' . $attrs['class'] . '" src="' . file_create_url($file->getFileUri()) . '"/>';
      }
    }
    return '';
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['image'] = array(
      '#type' => 'image_browser',
      '#title' => $this->t('Image'),
      '#default_value' => $this->get('image', 0),
    );
	$form['general_options']['link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Image link to'),
      '#default_value' => $this->get('link', ''),
    );

    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class', ''),
      '#description' => $this->t('Some useful class: img-responsive, center-block, img-rounded, img-circle, img-thumbnail')
    );
    
    return $form;
  }
}
