<?php

use Drupal\block\Entity\Block;
function inv_shortcodes_embed_shortcode_info(&$shortcodes) {
    $shortcodes['embed'] = array(
        'title' => t('Embed content'),
        'description' => t('Embed a block content by block_id'),
        'process callback' => 'inv_shortcodes_process_embed',
    );
}

function inv_shortcodes_process_embed($attrs, $text) {
    $attributes = shortcode_attrs(array(
        'block_id' => ''), $attrs
    );
    $block = Block::load($attributes['block_id']);
	if ($block != null) {
		$block_content = \Drupal::service('entity_type.manager')
			->getViewBuilder('block')
			->view($block);

		return \Drupal::service('renderer')->render($block_content);
	}
	return "";
}

