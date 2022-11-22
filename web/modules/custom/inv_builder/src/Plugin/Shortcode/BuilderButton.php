<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use \Drupal\Component\Utility\Html;
use Drupal\inv_builder\Plugin\Shortcode\BuilderElement;
use Drupal\Core\Template\Attribute;

/**
 * Provides a shortcode for button.
 *
 * @Shortcode(
 *   id = "inv_button",
 *   title = @Translation("Button"),
 *   description = @Translation("Button"),
 *   group = @Translation("Content"),
 *   child = {}
 * )
 */
class BuilderButton extends BuilderElement {

    public function process(array $attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
	    $attrs = $this->getAttributes(array(
		  'title' => '',
		  'icon' => '',
		  'icon_library' => '',
		  'icon_align' => '',
		  'link' => '#',
		  'link_target' => '_self',
		  'type' => '',
		  'button_size' => '',
		  'font_size' => '',
		  'padding' => '',
		  'letter_spacing' => '',
		  'font_size' => '',
		  'radius' => '',
		  'alignment' => '',
		  'class' => '',
		  'id' => '',
		  'button_style' => '',
			), $attributes
		);

		$attribute = new Attribute();
		if ($attrs['id']) {
			$attribute['id'] = $attrs['id'];
		}
		$attribute->addClass('inv-button btn');
		$attribute->addClass($attrs['class']);
		$attribute->addClass($attrs['type']);
		$attribute->addClass($attrs['button_size']);
		$attribute->addClass($attrs['button_style']);

		$style = '';
		if ($attrs['padding'] != '') {
		  $style .= 'padding:' . $attrs['padding'] . ';';
		}
		if ($attrs['letter_spacing'] != '') {
		  $style .= ' letter-spacing:' . $attrs['letter_spacing'] . ';';
		}
		if ($attrs['font_size'] != '') {
		  $style .= ' font-size:' . $attrs['font_size'] . ';';
		}
		if ($attrs['radius'] != '') {
		  $style .= ' border-radius:' . $attrs['radius'] . ';';
		}
		$attribute->setAttribute('style', $style);

		if ($attrs['icon_align'] != '') {
		  $attribute->addClass('icon-' . $attrs['icon_align']);
		}
		if ($attrs['alignment'] != '') {
		  $attribute->addClass('btn-' . $attrs['alignment']);
		}
		$attribute->setAttribute('target', $attrs['link_target']);
		if($attrs['link']){
		  if($attrs['link'] == '#'){
			$link = $attrs['link'];
		  }else{
			try{
			  $link = \Drupal\Core\Url::fromUserInput($attrs['link'])->toString();
			}catch (\Exception $e){
			  $link = $attrs['link'];
			}
		  }
		  $attribute->setAttribute('href', $link);
		}
		$output = array(
		  '#theme' => 'inv_builder_button',
		  '#title' => $attrs['title'],
		  '#icon' => $attrs['icon'],
		  '#attributes' => $attribute,
		  '#button_style' =>  $attrs['button_style'],
		  '#attached' => ['library' => ['inv_builder/inv-button']],
		);
		if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
		  $output['#attached']['library'][] = $icon_plugin->library();
		}
		return $this->render($output);
    }

    public function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
        $form = parent::settingsForm($form, $form_state);

        $form['general_options']['title'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#default_value' => $this->get('title', ''),
        );
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
        $form['general_options']['icon_align'] = array(
            '#type' => 'select',
            '#title' => $this->t('Icon Alignment'),
            '#options' => array('left' => $this->t('Left'), 'right' => $this->t('Right')),
            '#default_value' => $this->get('icon_align', 'left'),
        );
        $form['general_options']['link'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Link'),
            '#description' => $this->t('Add link to button'),
            '#default_value' => $this->get('link', '#'),
        );
        $form['general_options']['link_target'] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('Open link on new Windows?'),
            '#default_value' => $this->get('link_target'),
        );
        $form['general_options']['type'] = array(
            '#type' => 'select',
            '#title' => $this->t('Button Type'),
			'#description' => $this->t('http://getbootstrap.com/css/#buttons-options'),
			'#options' => array(
			'' => $this->t('Normal'), 
			'btn-default' => $this->t('Default'), 
			'btn-primary' => $this->t('Primary'), 
			'btn-success' => $this->t('Success'), 
			'btn-info' => $this->t('Info'), 
			'btn-warning' => $this->t('Warning'), 
			'btn-danger' => $this->t('Danger'), 
			'btn-link' => $this->t('Link')
			),
			'#default_value' => $this->get('type', ''),
        );
		$form['general_options']['button_size'] = array(
            '#type' => 'select',
            '#title' => $this->t('Button Size'),
			'#options' => array(
			'' => $this->t('Medium'), 
			'btn-large' => $this->t('Large'), 
			'btn-small' => $this->t('Small'), 
			),
			'#default_value' => $this->get('button_size', ''),
        );
        $form['general_options']['padding'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Padding'),
            '#description' => $this->t('Ex: 15px 35px 15px 35px.'),
            '#default_value' => $this->get('padding', ''),
        );
        $form['general_options']['letter_spacing'] = array(
            '#type' => 'select',
            '#title' => $this->t('Letter Spacing'),
            '#options' => array('' => $this->t('Default'), '0.05em' => $this->t('Letter Spacing: 50'), '0.1em' => $this->t('Letter Spacing: 100'), '0.2em' => $this->t('Letter Spacing: 200'), '0.3em' => $this->t('Letter Spacing: 300'), '0.4em' => $this->t('Letter Spacing: 400'), '0.5em' => $this->t('Letter Spacing: 500')),            
            '#default_value' => $this->get('letter_spacing', ''),
        );
        $form['general_options']['font_size'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Font Size'),
            '#description' => $this->t('Ex: 15px'),
            '#default_value' => $this->get('font_size', ''),
        );
        $form['general_options']['radius'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Radius'),
            '#description' => $this->t('Ex: 15px 35px 15px 35px.'),
            '#default_value' => $this->get('radius', ''),
        );
        $form['general_options']['alignment'] = array(
            '#type' => 'select',
            '#title' => $this->t('Alignment'),
            '#options' => array('inline' => $this->t('Inline'), 'left' => $this->t('Left'), 'right' => $this->t('Right'), 'center' => $this->t('Center')),
            '#description' => $this->t('Select button alignment.'),
            '#default_value' => $this->get('alignment', ''),
        );
		$form['general_options']['id'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('HTML ID'),
            '#default_value' => $this->get('id', ''),
        );
        $form['general_options']['class'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Custom class'),
            '#default_value' => $this->get('class', ''),
        );
		unset($form['animate_options']);
        return $form;
    }

    public function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
        $attrs = $this->getAttributes(array(
		  'title' => '',
		  'icon' => '',
		  'icon_library' => '',
			), $attributes
		);
		$icon = "";
		if ($attrs['icon']) {
			$icon = "<i class='".$attrs['icon']. "'></i>";
		}
		$output['#markup'] = $icon. ' '.$attrs['title'].$text;
		if($attrs['icon_library'] && ($icon_plugin = \Drupal::service('inv_builder.fonticon')->getFontIconPlugin($attrs['icon_library']))){
		  $output['#attached']['library'][] = $icon_plugin->library();
		}
		return $this->render($output);
    }

}
