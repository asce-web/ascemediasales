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
  
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'image' => 0,
	  'title' => '',
	  'long_desc' => '',
      'class' => '',
	  'class_link' => '',
	  'link' => '',
	  'wrap' => 0,
	  'class_wrapper' => '',
	  'caption_text' => '',
    ),$attrs);
    $fid = str_replace('file:', '', $attrs['image']);
	$link = '';
	$class_link = $attrs['class_link'];
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
	$render = "";
    if($file = \Drupal\file\Entity\File::load($fid)){
	  $render = '<img class="' . $attrs['class'] . '" src="' . file_create_url($file->getFileUri()) . '" title="'.$attrs['title'].'"/>';	
	  if ($attrs['long_desc']) {
		 $render = '<img class="' . $attrs['class'] . '" src="' . file_create_url($file->getFileUri()) . '" title="'.$attrs['title'].'" longdesc="'. $attrs['long_desc'].'"/>'; 
	  }
	  if($link){
		$render = '<a href="' . $link . '" class="'. $class_link .'">'.$render .'</a>';
	  }
	  if ($attrs['wrap']) {
		  $render = $render .'<figcaption>'. $attrs['caption_text']. '</figcaption>';
		  $render = '<figure class="'.$attrs['class_wrapper'].'">'. $render . '</figure>'; 
	  }	
    }
    return $render;
  }
  
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['image'] = array(
      '#type' => 'image_browser',
      '#title' => $this->t('Image'),
      '#default_value' => $this->get('image', 0),
    );
	$form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Image Title'),
      '#default_value' => $this->get('title', ''),
    );
	$form['general_options']['long_desc'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Long Description'),
      '#default_value' => $this->get('long_desc', ''),
	  '#description' => $this->t('Specifies a URL to a detailed description of an image')
    );
	$form['general_options']['link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Image link to'),
      '#default_value' => $this->get('link', ''),
    );
	$form['general_options']['class_link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Class to link'),
      '#default_value' => $this->get('class_link', ''),
	  '#description' => $this->t('Some useful class: colorbox if you want to show popup')
    );
    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class', ''),
      '#description' => $this->t('Some useful class: img-responsive, center-block, img-rounded, img-circle, img-thumbnail')
    );
    $form['general_options']['wrap'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add figure wapper'),
      '#default_value' => $this->get('wrap', 0),
    );
	$form['general_options']['class_wrapper'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Figure class'),
      '#default_value' => $this->get('class_wrapper', ''),
	  '#states' => array(
          'visible' => array(
            ':input[name=wrap]' => array('checked' => true),
          ),
        ),
    );
	$form['general_options']['caption_text'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Figcaption HTML text'),
      '#default_value' => $this->get('caption_text', 'HTML inner figcaption'),
	  '#states' => array(
          'visible' => array(
            ':input[name=wrap]' => array('checked' => true),
          ),
        ),
    );
    return $form;
  }
}
