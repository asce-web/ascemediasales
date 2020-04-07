<?php

/**
 * @file
 * Contains \Drupal\inv_layerslider\Entity\Form\SliderEditSlidesForm.
 */

namespace Drupal\inv_layerslider\Form;

use Drupal\inv_layerslider\Entity\Slider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\file\Entity\File;

/**
 * Form controller for Slider edit forms.
 *
 * @ingroup inv_layerslider
 */
class SlidesEditForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inv_layerslider_slides_edit';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $inv_slider = 0) {
    $slider = Slider::load($inv_slider);
    
    $form['add_slide'] = array(
      '#markup' => '<a href="#" class="add-slide button button--small button-action">Add Slide</a>'
    );
    $form['slide_slist'] = array(
      '#markup' => '<ul id="slideslist"></ul>'
    );
    
    $form['slide_options'] = array(
      '#type' => 'details',
      '#title' => $this->t('Slide Settings'),
      '#open' => TRUE,
    );
    
    $form['slide_options']['title'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Slide Title'),
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="row"><div class="col-md-4">',
      '#suffix' => '</div>',
    );
    
    $form['slide_options']['background_image'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Background Image'),
      '#attributes' => array('class' => array('image-browser-js','slide-option', 'hidden')),
      '#prefix' => '<div class="col-md-4">',
      //'#suffix' => '</div>',
    );
    
    $form['slide_options']['background_color'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Background Color'),
      '#attributes' => array('class' => array('color','slide-option')),
      //'#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div>',
    );
    
    $form['slide_options']['slide_thumbnail_image'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Thumbnail Image'),
      '#attributes' => array('class' => array('image-browser-js', 'slide-option', 'hidden')),
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div></div>',
    );
    
    $form['slide_options']['data_transition'] = array(
      '#title' => $this->t('Slide Transition'),
      '#type' => 'select',
      '#options' => [
        'slideup' => 'Slide To Top',
        'slidedown' => 'Slide To Bottom',
        'slideright' => 'Slide To Right', 
        'slideleft' => 'Slide To Left',
        'slidehorizontal' => 'Slide Horizontal',
        'slidevertical' => 'Slide Vertical',
        'boxslide' => 'Slide Boxes',
        'slotslide-horizontal' => 'Slide Slots Horizontal',
        'slotslide-vertical' => 'Slide Slots Vertical',
        'boxfade' => 'Fade Boxes',
        'slotfade-horizontal' => 'Fade Slots Horizontal',
        'slotfade-vertical' => 'Fade Slots Vertical',
        'fadefromright' => 'Fade and Slide from Right',
        'fadefromleft' => 'Fade and Slide from Left',
        'fadefromtop' => 'Fade and Slide from Top',
        'fadefrombottom' => 'Fade and Slide from Bottom',
        'fadetoleftfadefromright' => 'Fade To Left and Fade From Right',
        'fadetorightfadefromleft' => 'Fade To Right and Fade From Left',
        'fadetotopfadefrombottom' => 'Fade To Top and Fade From Bottom',
        'fadetobottomfadefromtop' => 'Fade To Bottom and Fade From Top',
        'parallaxtoright' => 'Parallax to Right',
        'parallaxtoleft' => 'Parallax to Left',
        'parallaxtotop' => 'Parallax to Top',
        'parallaxtobottom' => 'Parallax to Bottom',
        'scaledownfromright' => 'Zoom Out and Fade From Right',
        'scaledownfromleft' => 'Zoom Out and Fade From Left',
        'scaledownfromtop' => 'Zoom Out and Fade From Top',
        'scaledownfrombottom' => 'Zoom Out and Fade From Bottom',
        'zoomout' => 'ZoomOut',
        'zoomin' => 'ZoomIn',
        'slotzoom-horizontal' => 'Zoom Slots Horizontal',
        'slotzoom-vertical' => 'Zoom Slots Vertical',
        'fade' => 'Fade',
        'random-static' => 'Random Flat',
        'random' => 'Random Flat and Premium',
        'curtain-1' => 'Curtain from Left',
        'curtain-2' => 'Curtain from Right',
        'curtain-3' => 'Curtain from Middle',
        '3dcurtain-horizontal' => '3D Curtain Horizontal',
        '3dcurtain-vertical' => '3D Curtain Vertical',
        'cube' => 'Cube Vertical',
        'cube-horizontal' => 'Cube Horizontal',
        'incube' => 'In Cube Vertical',
        'incube-horizontal' => 'In Cube Horizontal',
        'turnoff' => 'TurnOff Horizontal',
        'turnoff-vertical' => 'TurnOff Vertical',
        'papercut' => 'Paper Cut',
        'flyin' => 'Fly In',
        'random-premium' => 'Random Premium',
        'random' => 'Random Flat and Premium',
      ],
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="row"><div class="col-md-4">',
      '#suffix' => '</div>',
    );
    
    $form['slide_options']['data_slotamount'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Slotamount'),
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div>',
      '#statesx' => [
        'visible' => [
          ':input[name=data_transition]' => ['value' => 'slidedown']
        ]
      ]
    );
    
    $form['slide_options']['data_masterspeed'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Masterspeed'),
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div></div>',
    );
    
    $form['slide_options']['data_delay'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Delay'),
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="row"><div class="col-md-4">',
      '#suffix' => '</div>',
    );
    
    $form['slide_options']['data_link'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Slide Link'),
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div>',
    );
    
    $form['slide_options']['link_target'] = array(
      '#type' => 'select',
      '#title' => $this->t('Link target'),
      '#options' => ['_self' => 'Same window', '_blank' => 'New window'],
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div></div>',
    );
    
    $form['slide_options']['data_kenburns'] = array(
      '#type' => 'select',
      '#title' => $this->t('Ken Burns Effect'),
      '#options' => ['off' => $this->t('No'), 'on' => $this->t('Yes')],
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="row"><div class="col-md-4">',
      '#suffix' => '</div>'
    );
    
    $form['slide_options']['data_bgposition'] = array(
      '#type' => 'select',
      '#title' => $this->t('Start Background Position'),
      '#options' => [
        'center center' => 'center center',
        'center top' => 'center top',
        'center bottom' => 'center bottom',
        'left center' => 'left center',
        'left top' => 'left top',
        'left bottom' => 'left bottom',
        'right center' => 'right center',
        'right top' => 'right top',
        'right bottom' => 'right bottom',
      ],
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div>'
    );
    
    $form['slide_options']['data_bgpositionend'] = array(
      '#type' => 'select',
      '#title' => $this->t('End Background Position'),
      '#options' => [
        'center center' => 'center center',
        'center top' => 'center top',
        'center bottom' => 'center bottom',
        'left center' => 'left center',
        'left top' => 'left top',
        'left bottom' => 'left bottom',
        'right center' => 'right center',
        'right top' => 'right top',
        'right bottom' => 'right bottom',
      ],
      '#attributes' => ['class' => ['slide-option']],
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div></div>',
    );
    
    $form['markup'] = array(
      '#theme' => 'inv_edit_slides',
      '#slides' => [],
    );
    
    $form['id'] = array(
      '#type' => 'hidden',
      '#default_value' => $slider->id(),
    );
    $form['data'] = array(
      '#type' => 'hidden',
      '#default_value' => json_encode($slider->getSlides()),
    );
    
    $form['actions'] = array(
      '#type' => 'actions',
    );
    
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    
    $google_fonts = file_get_contents(drupal_get_path('module', 'inv_layerslider').'/vendor/google-fonts-api/google-fonts-api.json');
    $form['#attached']['library'][] = 'inv_layerslider/backend';
    $form['#attached']['drupalSettings']['inv_layerslider'] = array(
      'settings' => $slider->getSettings(),
      'slides' => $slider->getSlides(),
    );
    $form['#attached']['library'][] = 'inv_image_browser/image_browser';
    $form['#attached']['drupalSettings']['google_fonts'] = json_decode($google_fonts);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $slider = Slider::load($form_state->getValue('id'));
    $slider->set('data', $form_state->getValue('data'));
    $slider->save();
    $slides = $slider->getSlides();
    foreach($slides as $slide){
      if(isset($slide->background_image)){
        $this->saveFile($slide->background_image, $slider->id());
      }
      if(isset($slide->slide_thumbnail_image)){
        $this->saveFile($slide->slide_thumbnail_image, $slider->id());
      }
      foreach($slide->layers as $layer){
        if(isset($layer->image)){
          $this->saveFile($layer->image, $slider->id());
        }
        if(isset($layer->html5_video_poster)){
          $this->saveFile($layer->html5_video_poster, $slider->id());
        }
      }
    }
    $form_state->setRedirect('entity.inv_slider.collection');
    drupal_set_message('Slider has been saved.');
  }
  
  private  function saveFile($fid, $slider_id){
    if($fid){
      $fid = str_replace('file:', '', $fid);
      $file = File::load($fid);
      if($file){
        $file->setPermanent();
        $file->save();
        $file_usage = \Drupal::service('file.usage');
        $file_usage->add($file, 'inv_layerslider', 'inv_slider', $slider_id);
      }
    }
  }

}