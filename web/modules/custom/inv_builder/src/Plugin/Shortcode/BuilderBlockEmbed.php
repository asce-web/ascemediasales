<?php

namespace Drupal\inv_builder\Plugin\Shortcode;

use Drupal\Core\Language\Language;

/**
 * Provides a shortcode for bootstrap embed block.
 *
 * @Shortcode(
 *   id = "embed_block",
 *   title = @Translation("Embed Block"),
 *   description = @Translation("Embed block"),
 *   group = @Translation("Content"),
 *   child = {},
 * )
 */
class BuilderBlockEmbed extends BuilderElement {

  function process($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = $this->getAttributes(array('block' => ''), $attributes);

	$block = \Drupal\block\Entity\Block::load($attrs['block']);
	if (isset($block)) { 
		$block_content = \Drupal::entityManager()->getViewBuilder('block')->view($block);
		return drupal_render($block_content);
	}
	return '';
  }

  function processBuilder($attributes, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED){
    $attrs = $this->getAttributes(array('block' => ''), $attributes);
    return '[block: ' . $attrs['block'] . ']';
  }
  
  function settingsForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);
    $options = [];
	$blocks = \Drupal\block\Entity\Block::loadMultiple();
    foreach ($blocks as $block){
		$options[$block->id()] = $block->label();
    }
    $form['general_options']['block'] = array(
      '#title' => $this->t('Select Block'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $this->get('block'),
    );
    unset($form['design_options']);
    unset($form['animate_options']);
    return $form;
  }

}
