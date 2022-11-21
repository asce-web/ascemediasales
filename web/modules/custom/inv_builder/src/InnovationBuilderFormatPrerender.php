<?php

namespace Drupal\inv_builder;

use Drupal\Core\Security\TrustedCallbackInterface;

class InnovationBuilderFormatPrerender implements TrustedCallbackInterface {

  /**
   * Constructs the InnovationBuilderFormatPrerender class.
   */
  public function __construct() {}

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['inv_builder_pre_render_text_format_enhance'];
  }

	/**
	 * Set the necessary divs to work with visual help.
	 */
  public static function inv_builder_pre_render_text_format_enhance($element) {
	  if (!isset($element['#format'])) {
		return $element;
	  }
	  if (isset($element['value'])) {
		if (!isset($element['format'])) {
		  return $element;
		}
		if (isset($element['value']) && $element['value']['#type'] == 'textarea') {
		  $element['value'] = InnovationBuilderFormatPrerender::inv_builder_load_field($element['value'], $element['format']['format']);
		}
	  }

	  return $element;
 }

	/**
	 * Make the divs and other elements required for the builder help.
	 */
	public static function inv_builder_load_field($field, $format) {
	  $textarea_id = $field['#id'];
	  if (is_array($format)) {
		$format_arr = $format;
		$format = isset($format_arr['#value']) ? $format_arr['#value'] : $format_arr['#default_value'];
	  }
	  $builder_id = \Drupal\Component\Utility\Html::getUniqueId('inv-builder');
	  // Display the link that enable the visual element.
	  $suffix = '<div class="inv-builder" id="' . $builder_id . '" data-id="' . $textarea_id . '"><div class="builder-toolbar"><span class="fa fa-plus add-element"></span></div><div class="inv-builder-inner"></div><div class="builder-toolbar"><span class="fa fa-plus add-element"></span></div></div>';

	  // Set all div and libraries.
	  $field['#suffix'] = (isset($field['#suffix']) ? $field['#suffix'] : '') . $suffix;
	  //$field['#suffix'] = $suffix . (isset($field['#suffix']) ? $field['#suffix'] : '');
	  $field['#attached']['library'][] = 'inv_builder/backend';
	  return $field;
	}
}
