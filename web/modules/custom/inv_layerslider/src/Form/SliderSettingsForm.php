<?php

/**
 * @file
 * Contains \Drupal\inv_layerslider\Entity\Form\SliderEditSlidesForm.
 */

namespace Drupal\inv_layerslider\Form;

use Drupal\inv_layerslider\Entity\Slider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
 * Form controller for Slider edit forms.
 *
 * @ingroup inv_layerslider
 */
class SliderSettingsForm extends FormBase {
  
  public $settings;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inv_layerslider_settings';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $inv_slider = 0) {
    $slider = Slider::load($inv_slider);
    $this->settings = $slider->getSettings();
    $form['general'] = array(
      '#type' => 'details',
      '#title' => $this->t('General Settings'),
      '#open' => TRUE,
    );
    $form['general']['delay'] = array(
      '#title' => $this->t('Default slide duration'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('delay',9000),
      '#description' => $this->t('The time one slide stay on screen in milliseconds. This value is gloal and can be adjust each slide in slides edit page'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['startwidth'] = array(
      '#title' => $this->t('Width'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('startwidth',1170),
      '#description' => $this->t(' This Width of the Grid where the Captions are displayed in Pixel. This Width is the Max width of Slider in Fullwidth Layout and in Responsive Layout.  In Fullscreen Layout the Gird will be centered Vertically in case the Slider is higher then this value.'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['startheight'] = array(
      '#title' => $this->t('Height'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('startheight',500),
      '#description' => $this->t(' This Height of the Grid where the Captions are displayed in Pixel. This Height is the Max height of Slider in Fullwidth Layout and in Responsive Layout.  In Fullscreen Layout the Gird will be centered Vertically in case the Slider is higher then this value.'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['onHoverStop'] = array(
      '#title' => $this->t('Pause on hover'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('onHoverStop', 'on'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['loopsingle'] = array(
      '#title' => $this->t('Loop Single Slide'),
      '#type' => 'select',
      '#options' => [0 => $this->t('No'), 1 => $this->t('Yes')],
      '#default_value' => $this->getSetting('loopsingle', 0),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['general']['touchenabled'] = array(
      '#title' => $this->t('Touch enable'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('touchenabled', 'on'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout'] = array(
      '#type' => 'details',
      '#title' => $this->t('Layout Settings'),
    );
    
    $form['layout']['fullWidth'] = array(
      '#title' => $this->t('Full Width'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('fullWidth','on'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['fullScreen'] = array(
      '#title' => $this->t('Full Screen'),
      '#type' => 'select',
      '#options' => ['on' => $this->t('Yes'),'off' => $this->t('No')],
      '#default_value' => $this->getSetting('fullScreen','off'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['spinner'] = array(
      '#title' => t('Spinner'),
      '#type' => 'select',
      '#options' => ['spinner1' => 'spinner1', 'spinner2' => 'spinner2', 'spinner3' => 'spinner3', 'spinner4' => 'spinner4', 'spinner5' => 'spinner5'],
      '#default_value' => $this->getSetting('spinner','spinner3'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['timer'] = array(
      '#title' => t('Banner Timer'),
      '#type' => 'select',
      '#options' => ['' => 'None', 'bottom' => 'Bottom', 'top' => 'Top'],
      '#default_value' => $this->getSetting('timer',''),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['shadow'] = array(
      '#title' => $this->t('Shadow'),
      '#type' => 'textfield',
      '#description' => $this->t('Possible values: 0,1,2,3  (0 == no Shadow, 1,2,3 - Different Shadow Types)'),
      '#default_value' => $this->getSetting('shadow', 0),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['layout']['dottedOverlay'] = array(
      '#title' => $this->t('Dotted Overlay'),
      '#type' => 'textfield',
      '#description' => $this->t('Possible Values: "none", "twoxtwo", "threexthree", "twoxtwowhite", "threexthreewhite" - Creates a Dotted Overlay for the Background images extra. Best use for FullScreen / fullwidth sliders, where images are too pixaleted.'),
      '#default_value' => $this->getSetting('dottedOverlay', 'none'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['nivagation'] = array(
      '#type' => 'details',
      '#title' => $this->t('Navigation Settings'),
    );
    
    $form['nivagation']['navigationType'] = array(
      '#title' => $this->t('Navigation type'),
      '#type' => 'select',
      '#options' => ['none' => $this->t('None'),'bullet' => $this->t('Bullet'), 'thumb' => $this->t('Thumbnail')],
      '#default_value' => $this->getSetting('navigationType', 'none'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['nivagation']['thumbAmount'] = array(
      '#title' => $this->t('Number of Thumbnails visible'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('thumbAmount', 2),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=navigationType]' => ['value' => 'thumb'],
        ],
      ],
    );
    
    $form['nivagation']['thumbWidth'] = array(
      '#title' => $this->t('Navigation type'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('thumbWidth', 50),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=navigationType]' => ['value' => 'thumb'],
        ],
      ],
    );
    
    $form['nivagation']['thumbHeight'] = array(
      '#title' => $this->t('Navigation type'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('thumbHeight', 50),
      '#attributes' => ['class'=> ['setting-option']],
      '#states' => [
        'visible' => [
          ':input[name=navigationType]' => ['value' => 'thumb'],
        ],
      ],
    );
    
    $form['nivagation']['navigationArrows'] = array(
      '#title' => $this->t('Navigation arrows'),
      '#type' => 'select',
      '#options' => ['nexttobullets' => $this->t('nexttobullets'),'solo' => $this->t('solo')],
      '#default_value' => $this->getSetting('navigationArrows', 'solo'),
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['nivagation']['navigationStyle'] = array(
      '#title' => $this->t('Navigation arrows'),
      '#type' => 'select',
      '#options' => ['preview1' => 'preview1', 'preview2' => 'preview2', 'preview3' => 'preview3', 'preview4' => 'preview4', 'round' => 'round', 'square' => 'square', 'round-old' => 'round-old', 'square-old' => 'square-old', 'navbar-old' => 'navbar-old'],
      '#default_value' => $this->getSetting('navigationStyle', 'preview1'),
      /*
      '#states' => [
        'visible' => [
          ':input[name=navigationType]' => ['value' => 'bullet'],
        ],
      ],
      */
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['nivagation']['hideThumbs'] = array(
      '#title' => $this->t('Hide Thumbnails'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('hideThumbs', 0),
      '#description' => 'Hide thumbs and navigation arrows, bullets after mouse leave. 0 is never',
      '#attributes' => ['class'=> ['setting-option']],
    );
    
    $form['id'] = array(
      '#type' => 'hidden',
      '#default_value' => $inv_slider,
    );
    
    $form['settings'] = array(
      '#type' => 'hidden',
      '#default_value' => json_encode($slider->getSettings()),
    );
    
    $form['actions'] = array(
      '#type' => 'actions',
    );
    
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    $form['#attached']['library'][] = 'inv_layerslider/settings';
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $slider = Slider::load($form_state->getValue('id'));
    $slider->set('settings', $form_state->getValue('settings'));
    $slider->save();
  }
  
  public function getSetting($setting, $default = null) {
    return isset($this->settings->{$setting})?$this->settings->$setting:$default;
  }
}
