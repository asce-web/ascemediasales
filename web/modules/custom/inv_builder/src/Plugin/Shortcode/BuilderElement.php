<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\Core\Template\Attribute;

/**
 * Base shortcode element of Builder
 */
class BuilderElement extends ShortcodeBase {

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Get default shortcode param value
   * @param type $key
   * @param type $default
   * @return type
   */
  public function get($key, $default = '') {
    $attr = \Drupal::request()->get('attr');
    if (is_array($attr) && isset($attr[$key])) {
      return $attr[$key];
    }
    return $default;
  }

  /**
   * generate Attribute element from shortcode attribues
   * @param type $attr
   * @return css string
   */
  public function getCSS($attr = array()) {
    $attrObject = $this->createAttribute($attr);
    return $attrObject->toArray()['style'];
  }
  
  public function createAttribute($attr = array()){
    $attrObject = new Attribute();
    $keys = ['margin_left', 'margin_top', 'margin_right', 'margin_bottom', 'padding_left', 'padding_top', 'padding_right', 'padding_bottom'];
    $css = [];
    foreach ($keys as $key) {
      if (isset($attr[$key]) && $attr[$key] != '') {
        $css[] = str_replace('_', '-', $key) . ':' . $attr[$key];
      }
    }
    $border_width = 'border-width:%top %right %bottom %left';
    $border_style = 'border-style:%style';
    $border_radius = 'border-radius:%radius';
    $border_color = 'border-color:%color';
    $attrs = $this->getAttributes(array(
      'border_top' => 0,
      'border_right' => 0,
      'border_bottom' => 0,
      'border_left' => 0,
      'border_style' => 'solid',
      'border_color' => '',
      'border_radius' => 0,
      'background_image' => '',
      'background_type' => 'default',
      'background_repeat' => 'repeat',
      'background_attachment' => 'scroll',
      'background_size' => 'auto',
      'background_color' => '',
      'animate' => '',
      'animate_delay' => 0,
	  'custom_css' => '',
        ), $attr
    );
    if ($attrs['border_top'] || $attrs['border_right'] || $attrs['border_bottom'] || $attrs['border_left']) {
      $css[] = str_replace(array('%top', '%right', '%bottom', '%left'), array(
        $attrs['border_top'] == '' ? 0 : $attrs['border_top'] . 'px',
        $attrs['border_right'] == '' ? 0 : $attrs['border_right'] . 'px',
        $attrs['border_bottom'] == '' ? 0 : $attrs['border_bottom'] . 'px',
        $attrs['border_left'] == '' ? 0 : $attrs['border_left'] . 'px',
          ), $border_width);

      $css[] = str_replace('%style', $attrs['border_style'], $border_style);
      $css[] = str_replace('%color', $attrs['border_color'], $border_color);
      //$css[] = $this->t($border_color, array('%color' => $attrs['border_color']))->__toString();
      if ($attrs['border_radius']) {
        $css[] = str_replace('%radius', $attrs['border_radius'], $border_radius);
      }
    }
    //Background
    if ($attrs['background_color']) {
      $css[] = 'background-color:' . $attrs['background_color'];
    }
    if ($attrs['background_image']) {
      $fid = str_replace('file:', '', $attrs['background_image']);
      $file = \Drupal\file\Entity\File::load($fid);
      if ($file) {
        $css[] = 'background-image:url(\'' . file_create_url($file->getFileUri()) . '\')';
        if($attrs['background_type'] == 'parallax'){
          $attrObject->addClass('inv-parallax');
          $attrObject->setAttribute('data-stellar-background-ratio', '0.5');
          $css[] = 'background-repeat: repeat';
        }else{
          $css[] = 'background-repeat: ' . $attrs['background_repeat'];
        }
        if($attrs['background_type'] == 'fixed'){
          $css[] = 'background-attachment: fixed';
        }
        if($attrs['background_size'] != 'auto'){
          $css[] = 'background-size: ' . $attrs['background_size'];
        }
      }
    }
	if($attrs['custom_css']){
      $css[] = $attrs['custom_css'];
    }

    //Animate
    if($attrs['animate']){
      $attrObject->addClass('animated inv-animate');
      $attrObject->setAttribute('data-animate', $attrs['animate']);
      $attrObject->setAttribute('data-animate-delay', $attrs['animate_delay']);
    }
    $attrObject->setAttribute('style', implode(';', $css));
    return $attrObject;
  }

  public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    
  }

  public function render(array &$element) {
    $renderer = \Drupal::service('renderer');
    return $renderer->render($element);
  }

  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['element_settings'] = array(
      '#type' => 'vertical_tabs',
    );

    $form['general_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('General'),
      '#group' => 'element_settings',
      '#open' => TRUE,
    );

    $form['design_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('Design Options'),
      '#group' => 'element_settings',
    );

    $form['animate_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('Animate'),
      '#group' => 'element_settings',
    );

    return $form;
  }

  public function designOptions() {
    return array(
      'background' => array(
        '#type' => 'details',
        '#title' => $this->t('Background'),
        '#open' => TRUE,
        '#attributes' => ['class' => ['row']],
        'background_color' => array(
          '#title' => $this->t('Background color'),
          '#type' => 'textfield',
          '#default_value' => $this->get('background_color', ''),
          '#attributes' => ['class' => ['color']],
        ),
        'background_image' => array(
          '#title' => $this->t('Background image'),
          '#type' => 'image_browser',
          '#default_value' => 'file:' . $this->get('background_image'),
        ),
        'background_type' => array(
          '#title' => $this->t('Background Image Type'),
          '#type' => 'select',
          '#options' => ['default' => 'Default', 'fixed' =>'Fixed', 'parallax' => 'Parallax'],
          '#default_value' => $this->get('background_type','default'),
        ),
        'background_repeat' => array(
          '#type' => 'select',
          '#title' => t('Background Image Repeat'),
          '#default_value' => $this->get('background_repeat','no-repeat'),
          '#options' => ['no-repeat' => 'no-repeat', 'repeat' => 'repeat', 'repeat-x'=>'repeat-x', 'repeat-y' => 'repeat-y'],
        ),
        'background_size' => array(
          '#type' => 'select',
          '#title' => t('Background Image Size'),
          '#default_value' => $this->get('background_size','auto'),
          '#options' => ['auto' => 'auto', 'cover' => 'cover', 'contain' => 'contain'],
        ),
      ),
      'margin' => array(
        '#type' => 'details',
        '#title' => $this->t('Margin'),
        '#attributes' => ['class' => ['row']],
        '#open' => TRUE,
        'margin_top' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Top'),
          '#default_value' => $this->get('margin_top'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'margin_right' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Right'),
          '#default_value' => $this->get('margin_right'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'margin_bottom' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Bottom'),
          '#default_value' => $this->get('margin_bottom'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'margin_left' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Left'),
          '#default_value' => $this->get('margin_left'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
      ),
      'padding' => array(
        '#type' => 'details',
        '#title' => $this->t('Padding'),
        '#attributes' => ['class' => ['row']],
        '#open' => TRUE,
        'padding_top' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Top'),
          '#default_value' => $this->get('padding_top'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'padding_right' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Right'),
          '#default_value' => $this->get('padding_right'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'padding_bottom' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Bottom'),
          '#default_value' => $this->get('padding_bottom'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'padding_left' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Left'),
          '#default_value' => $this->get('padding_left'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
      ),
      'border' => array(
        '#type' => 'details',
        '#title' => $this->t('Border'),
        '#attributes' => ['class' => ['row']],
        '#open' => TRUE,
        'border_top' => array(
          '#type' => 'number',
          '#title' => $this->t('Border top'),
          '#min' => 0,
          '#field_suffix' => 'px',
          '#default_value' => $this->get('border_top'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'border_right' => array(
          '#type' => 'number',
          '#title' => $this->t('Border right'),
          '#min' => 0,
          '#field_suffix' => 'px',
          '#default_value' => $this->get('border_right'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'border_bottom' => array(
          '#type' => 'number',
          '#title' => $this->t('Border bottom'),
          '#min' => 0,
          '#field_suffix' => 'px',
          '#default_value' => $this->get('border_bottom'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'border_left' => array(
          '#type' => 'number',
          '#title' => $this->t('Border left'),
          '#min' => 0,
          '#field_suffix' => 'px',
          '#default_value' => $this->get('border_left'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'border_color' => array(
          '#type' => 'color',
          '#title' => $this->t('Border color'),
          '#default_value' => $this->get('border_color', ''),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'border_style' => array(
          '#type' => 'select',
          '#title' => $this->t('Border style'),
          '#options' => ['dotted' => 'dotted', 'dashed' => 'dashed', 'solid' => 'solid', 'double' => 'double', 'groove' => 'groove', 'ridge' => 'ridge', 'inset' => 'inset', 'outset' => 'outset', 'initial' => 'initial', 'inherit' => 'inherit'],
          '#default_value' => $this->get('border_style', 'solid'),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
        'border_radius' => array(
          '#type' => 'textfield',
          '#title' => $this->t('Border radius'),
          '#default_value' => $this->get('border_radius', ''),
          '#wrapper_attributes' => ['class' => ['col-md-3']],
        ),
      ),
    );
  }

  public function animateOptions() {
    return array(
      'animate' => array(
        '#type' => 'select',
        '#title' => $this->t('Animate effect'),
        '#options' => $this->__animates(),
        '#default_value' => $this->get('animate'),
      ),
      'animate_delay' => array(
        '#type' => 'number',
        '#title' => $this->t('Delay'),
        '#min' => 0,
        '#default_value' => $this->get('animate_delay', 0),
        '#description' => $this->t('Delay time before animate effect starts in millisecond'),
        '#field_suffix' => 'ms',
      ),
    );
  }

  /**
   * Helper: return an array of effects
   */
  protected function __animates() {
    return [
      '' => 'None',
      'Attention Seekers' => [
        'bounce' => 'bounce',
        'flash' => 'flash',
        'pulse' => 'pulse',
        'rubberBand' => 'rubberBand',
        'shake' => 'shake',
        'swing' => 'swing',
        'tada' => 'tada',
        'wobble' => 'wobble',
        'jello' => 'jello',
      ],
      'Bouncing Entrances' => [
        'bounceIn' => 'bounceIn',
        'bounceInDown' => 'bounceInDown',
        'bounceInLeft' => 'bounceInLeft',
        'bounceInRight' => 'bounceInRight',
        'bounceInUp' => 'bounceInUp',
      ],
      'Fading Entrances' => [
        'fadeIn' => 'fadeIn',
        'fadeInDown' => 'fadeInDown',
        'fadeInDownBig' => 'fadeInDownBig',
        'fadeInLeft' => 'fadeInLeft',
        'fadeInLeftBig' => 'fadeInLeftBig',
        'fadeInRight' => 'fadeInRight',
        'fadeInRightBig' => 'fadeInRightBig',
        'fadeInUp' => 'fadeInUp',
        'fadeInUpBig' => 'fadeInUpBig',
      ],
      'Flippers' => [
        'flip' => 'flip',
        'flipInX' => 'flipInX',
        'flipInY' => 'flipInY',
        'flipOutX' => 'flipOutX',
        'flipOutY' => 'flipOutY',
      ],
      'Lightspeed' => [
        'lightSpeedIn' => 'lightSpeedIn',
        'lightSpeedOut' => 'lightSpeedOut',
      ],
      'Rotating Entrances' => [
        'rotateIn' => 'rotateIn',
        'rotateInDownLeft' => 'rotateInDownLeft',
        'rotateInDownRight' => 'rotateInDownRight',
        'rotateInUpLeft' => 'rotateInUpLeft',
        'rotateInUpRight' => 'rotateInUpRight',
      ],
      'Sliding Entrances' => [
        'slideInUp' => 'slideInUp',
        'slideInDown' => 'slideInDown',
        'slideInLeft' => 'slideInLeft',
        'slideInRight' => 'slideInRight',
      ],
      'Zoom Entrances' => [
        'zoomIn' => 'zoomIn',
        'zoomInDown' => 'zoomInDown',
        'zoomInLeft' => 'zoomInLeft',
        'zoomInRight' => 'zoomInRight',
        'zoomInUp' => 'zoomInUp',
      ],
      'Specials' => [
        'hinge' => 'hinge',
        'rollIn' => 'rollIn',
        'rollOut' => 'rollOut',
      ],
    ];
  }

}
