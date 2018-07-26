<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for video.
 *
 * @Shortcode(
 *   id = "video",
 *   title = @Translation("Video Embed"),
 *   description = @Translation("Embed youtube and viemo video"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class Video extends BuilderElement {

  public function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    parent::process($attributes, $text, $langcode);
    $attrs = $this->getAttributes(array(
      'type' => 'youtube',
      'id' => '',
      'ratio' => '16by9',
      'autoplay' => 'no',
      'custom_class' => '',
    ),
      $attributes
    );
    $embed_url = "";
    $autoplay = $attrs['autoplay'];
    $ratio = $attrs['autoplay'] == '4by3'? '4by3' : '16by9';
    if($attributes['type'] == 'vimeo'){
      $embed_url = "http://player.vimeo.com/video/{$attributes['id']}?autoplay={$autoplay}&rel=0";
    }else{
      $embed_url = "https://www.youtube.com/embed/{$attributes['id']}?autoplay={$autoplay}&rel=0&html5=1";
    }
    return '<div class="embed-responsive embed-responsive-' . $ratio . ' ' . $attrs['custom_class'] .'"><iframe class="embed-responsive-item" src="' . $embed_url . '"></iframe></div>';
  }
  
  public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array(
      'type' => 'youtube',
      'id' => '',
      'ratio' => '16by9',
      'autoplay' => 'no',
      'custom_class' => '',
    ),
      $attributes
    );
    
    return '[' . $attrs['type'] . ':' . $attrs['id'] . ']';
  }
      
  public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $form['general_options']['type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Video source'),
      '#options' => ['youtube' => 'Youtube', 'vimeo' => 'Vimeo'],
      '#default_value' => $this->get('type', 'youtube'),
    );
    
    $form['general_options']['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Video ID'),
      '#default_value' => $this->get('id', ''),
      '#description' => 'https://www.youtube.com/watch?v=<strong>video_id</strong>, https://vimeo.com/<strong>video_id</strong>',
    );
    
    $form['general_options']['ratio'] = array(
      '#type' => 'select',
      '#title' => $this->t('Video ratio'),
      '#options' => ['4by3' => '4:3', '16by9' => '16:9'],
      '#default_value' => $this->get('ratio', '16by9'),
    );
    
    $form['general_options']['autoplay'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay'),
      '#default_value' => $this->get('autoplay', 1),
    );
    
    $form['general_options']['custom_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Custom class'),
      '#default_value' => $this->get('custom_class', ''),
    );
    return $form;
  }

}
