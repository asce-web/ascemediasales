<?php
namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
use Drupal\inv_builder\Plugin\Shortcode\BuilderElement;

/**
 * Provides a shortcode for Icon.
 *
 * @Shortcode(
 *   id = "icon",
 *   title = @Translation("Icon"),
 *   description = @Translation("Icon"),
 *   group = @Translation("Content"),
 * )
 */
class BuilderIcon extends BuilderElement {

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    $css = $this->getCSS($attributes);
    $attributes = $this->getAttributes(array(
      'icon' => '',
      'icon_library' => '',
      'fontsize' => '',
      'icon_width' => '',
      'icon_height' => '',
      'icon_border_width' => '',
      'icon_border_radius' => '',
      'icon_bg' => '',  
      'title' => '',
      'tooltip' => '',  
      'link' => '',
      'target' => '_self',  
      'class' => '',
	  'id' => '',
      'icon_style' => '',      
        ), $attributes
    );

    $attribute = new Attribute();
	if ($attributes['id']) {
		$attribute['id'] = $attributes['id'];
	}
    $attribute->addClass('inv-icon');
    $attribute->addClass($attributes['class']);
    $attribute->addClass($attributes['icon_style']);

    $attribute->setAttribute('style', $css);

    $icon_css = [];
    if($attributes['fontsize']){
      $icon_css[] = 'font-size:' . $attributes['fontsize'];
    }
	
	$link = '';
    if($attributes['link']){
      if($attributes['link'] == '#'){
        $link = $attributes['link'];
      }else{
        try{
          $link = \Drupal\Core\Url::fromUserInput($attributes['link'])->toString();
        }catch (\Exception $e){
          $link = $attributes['link'];
        }
      }
    }

    if($attributes['icon_width']){
      $icon_css[] = 'width:' . $attributes['icon_width'];
      $icon_css[] = 'text-align: center';
    }
    if($attributes['icon_height']){
      $icon_css[] = 'height:' . $attributes['icon_height'];
      $icon_css[] = 'line-height:' . $attributes['icon_height'];
    }
    if($attributes['icon_border_width']){
      $icon_css[] = 'border-style: solid';
      $icon_css[] = 'border-width:' . $attributes['icon_border_width'];
      if($attributes['icon_border_radius']){
        $icon_css[] = 'border-radius:' . $attributes['icon_border_radius'];
      }
    }
    if($attributes['icon_bg']){
      $icon_css[] = 'background-color:' . $attributes['icon_bg'];
    }
    $icon_attribute = new Attribute();
    $icon_attribute->addClass($attributes['icon']);
	if (!empty($icon_css)) {
		$icon_attribute->setAttribute('style', implode(';', $icon_css));
	}
    $output = array(
      '#theme' => 'inv_builder_icon',
      '#icon' =>  $attributes['icon'],
      '#title' => $attributes['title'],
      '#tooltip' => $attributes['tooltip'],
      '#link' => $link,
      '#target' => $attributes['target'],
      '#icon_attributes' => $icon_attribute,  
      '#attributes' => $attribute,
	  '#icon_style' =>$attributes['icon_style'], 
    );
    if($attributes['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attributes['icon_library']))){
      $output['#attached']['library'][] = $icon_plugin->library();
    }
	if($attributes['target'] == 'popup'){
      $icon_attribute->addClass('inv-video-popup');
      $output['#target'] = '_self';
	  $output['#attached']['library'][] = 'inv_builder/video-popup';
    }
    
    return $this->render($output);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
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

    $form['general_options']['fontsize'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Fontsize Icon'),
      '#default_value' => $this->get('fontsize'),
    );
    $form['general_options']['icon_width'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Width'),
        '#default_value' => $this->get('icon_width', ''),
    );
    $form['general_options']['icon_height'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Height'),
        '#default_value' => $this->get('icon_height', ''),
    );

    $form['general_options']['icon_border_width'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Border width'),
        '#default_value' => $this->get('icon_border_width', ''),
        '#attributes' => ['placeholder' => 'top right bottom left'],
        '#description' => $this->t('https://www.w3schools.com/cssref/pr_border-width.asp')
    );
    $form['general_options']['icon_border_radius'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Border radius'),
      '#default_value' => $this->get('icon_border_radius', ''),
    );
    $form['general_options']['icon_bg'] = array(
       '#type' => 'textfield',
       '#title' => $this->t('Background'),
       '#default_value' => $this->get('icon_bg', ''),
       '#attributes' => ['class' => ['color']],
    );
    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title'),
    );
    
    $form['general_options']['tooltip'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Tooltip'),
      '#default_value' => $this->get('tooltip'),
    );
   
    $form['general_options']['link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#default_value' => $this->get('link'),
    );
    
    $form['general_options']['target'] = array(
      '#type' => 'select',
      '#options'=>['_blank'=>'_blank','_self' => '_self', '_parent'=>'_parent','top'=>'top','framename'=>'framename'],  
      '#title' => $this->t('Link Target'),
      '#default_value' => $this->get('target'),
    );

	$form['general_options']['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('HTML ID'),
      '#default_value' => $this->get('id'),
    );

    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class'),
    );
    $form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }

  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array(
      'title' => '',
      'icon' => '',
      'icon_library' => ''  
      ), $attributes
    );
	$icon_attribute = new Attribute();
    $icon_attribute->addClass($attributes['icon']);
    $output = array(
      '#theme' => 'inv_builder_icon',
      '#icon_attributes' =>  $icon_attribute,
    );
    if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }  
    return $this->render($output);
  }

}
