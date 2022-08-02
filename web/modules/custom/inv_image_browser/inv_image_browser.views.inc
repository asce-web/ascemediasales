<?php

/**
 * @file
 * Provide custom views data for rendering file previews.
 */

/**
 * Implements hook_views_data_alter().
 */
function inv_image_browser_views_data_alter(&$data) {
  $data['file_managed']['inv_image_browser_preview'] = array(
    'title' => t('Image Browser Preview'),
    'help' => t('Displays a preview of a file.'),
    'field' => array(
      'id' => 'inv_image_browser_preview',
    ),
  );
}
