<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;
/**
 * Provides a shortcode for bootstrap row.
 *
 * @Shortcode(
 *   id = "box",
 *   title = @Translation("Box Icon"),
 *   description = @Translation("Builds a div with col-screen-size class"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderBox extends BuilderElement {

  public function process($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $css = $this->getCSS($attrs);
    $attrs = $this->getAttributes(array(
      'icon' => '',
      'use_text' => 0,
	  'use_image' => 0,
      'icon_text' => '',
	  'icon_image'=>'',
      'icon_library' => '',
      'icon_bg' => '',
      'title' => '',
	  'read_more' =>'',
	  'link' => '',
      'class' => '',
      'animate' => '',
      'animate_delay' => 0,
      'icon_width' => '',
      'icon_height' => '',
      'icon_border_width' => '',
      'icon_border_radius' => '',
      'icon_size' => '',
	  'box_style' => '',
        ), $attrs
    );
    $attribute = new Attribute();
    $attribute->addClass($attrs['class']);
	$attribute->addClass($attrs['box_style']);
    $attribute->setAttribute('style', $css);
    if ($attrs['animate']) {
      $attribute->addClass('animated inv-animate');
      $attribute->setAttribute('data-animate', $attrs['animate']);
      $attribute->setAttribute('data-animate-delay', $attrs['animate_delay']);
    }
    $icon_attribute = new Attribute();
    //$icon_attribute->addClass($attrs['icon']);
	$fid = str_replace('file:', '', $attrs['icon_image']);
	$image_path = "";
    if($file = \Drupal\file\Entity\File::load($fid)){
      $image_path = '<img src="' . file_create_url($file->getFileUri()) . '"/>';
    }
	if ($image_path !="") {
		$attribute->addClass('use-image');
	}
    $icon_css = [];
    if($attrs['icon_bg']){
      $icon_css[] = 'background-color:' . $attrs['icon_bg'];
    }
    if($attrs['icon_size']){
      $icon_css[] = 'font-size:' . $attrs['icon_size'];
    }
    if($attrs['icon_width']){
      $icon_css[] = 'width:' . $attrs['icon_width'];
      $icon_css[] = 'text-align: center';
    }
    if($attrs['icon_height']){
      $icon_css[] = 'height:' . $attrs['icon_height'];
      $icon_css[] = 'line-height:' . $attrs['icon_height'];
    }
    if($attrs['icon_border_width']){
      $icon_css[] = 'border-style: solid';
      $icon_css[] = 'border-width:' . $attrs['icon_border_width'];
      if($attrs['icon_border_radius']){
        $icon_css[] = 'border-radius:' . $attrs['icon_border_radius'];
      }
    }
    $icon_attribute->setAttribute('style', implode(';', $icon_css));
    //$icon = '<i' . $icon_attribute->__toString() . '></i>';
    $output = [
      '#theme' => 'inv_builder_box',
      '#icon' => $attrs['icon'],
      '#use_text' => $attrs['use_text'],
      '#icon_text' => $attrs['icon_text'],
	  '#icon_image' => $image_path,
	  '#read_more' => $attrs['read_more'],
	  '#link' => $attrs['link'],
      '#icon_attributes' => $icon_attribute,
      '#title' => $attrs['title'],
      '#content' => $text,
      '#attributes' => $attribute,
	  '#box_style' => $attrs['box_style'],
    ];
    
    if($attrs['use_text'] != 1 && $attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
      $output['#attached']['library'] = $icon_plugin->library();
    }
    
    return $this->render($output);
  }

  public function processBuilders($attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return $text;
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['box_icon'] = array(
      '#type' => 'details',
      '#title' => $this->t('Icon settings'),
      '#open' => true,
      'icon' => array(
        '#type' => 'textfield',
        '#default_value' => $this->get('icon', ''),
        '#attributes' => ['class' => ['icon-select']],
        '#states' => array(
          'visible' => array(
            ':input[name=use_text]' => array('checked' => FALSE),
			':input[name=use_image]' => array('checked' => FALSE),
          ),
        ),
      ),
      'icon_library' => array(
        '#type' => 'hidden',
        '#default_value' => $this->get('icon_library', ''),
      ),
      'use_text' => array(
        '#type' => 'checkbox',
        '#title' => $this->t('Use text'),
        '#default_value' => $this->get('use_text'),
      ),
	  'use_image' => array(
        '#type' => 'checkbox',
        '#title' => $this->t('Use Image'),
        '#default_value' => $this->get('use_image'),
      ),
      'icon_text' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Text'),
        '#default_value' => $this->get('icon_text'),
        '#states' => array(
          'visible' => array(
            ':input[name=use_text]' => array('checked' => TRUE),
          ),
        ),
      ),
	  'icon_image' => array(
        '#type' => 'image_browser',
        '#title' => $this->t('Image'),
        '#default_value' => $this->get('icon_image'),
        '#states' => array(
          'visible' => array(
            ':input[name=use_image]' => array('checked' => TRUE),
          ),
        ),
      ),
      'icon_size' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Font Size'),
        '#default_value' => $this->get('icon_size', ''),
      ),
      'icon_width' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Width'),
        '#default_value' => $this->get('icon_width', ''),
      ),
      'icon_height' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Height'),
        '#default_value' => $this->get('icon_height', ''),
      ),
      'icon_bg' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Background'),
        '#default_value' => $this->get('icon_bg', ''),
        '#attributes' => ['class' => ['color']],
      ),
      'icon_border_width' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Border width'),
        '#default_value' => $this->get('icon_border_width', ''),
        '#attributes' => ['placeholder' => 'top right bottom left'],
        '#description' => $this->t('https://www.w3schools.com/cssref/pr_border-width.asp')
      ),
      'icon_border_radius' => array(
        '#type' => 'textfield',
        '#title' => $this->t('Border radius'),
        '#default_value' => $this->get('icon_border_radius', ''),
      ),
    );
    $form['general_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->get('title', ''),
    );
    $form['general_options']['link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#default_value' => $this->get('link', ''),
    );
    $form['general_options']['html_content'] = array(
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Content'),
      '#default_value' => $this->get('html_content', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'),
    );

	$form['general_options']['read_more'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Readmore Text'),
      '#default_value' => $this->get('read_more', ''),
	  '#description' => $this->t('Leave empty if you dont want to show it.'),
    );

    $form['general_options']['class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('class', ''),
    );
    $form['design_options'] += $this->designOptions();
    $form['animate_options'] += $this->animateOptions();
    return $form;
  }

}