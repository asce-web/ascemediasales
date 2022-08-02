<?php

/**
 * @file
 * Contains inv_slider.page.inc..
 *
 * Page callback for Slider entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Slider templates.
 *
 * Default template: inv_slider.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_inv_slider(array &$variables) {
  // Fetch Slider Entity Object.
  $inv_slider = $variables['elements']['#inv_slider'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
