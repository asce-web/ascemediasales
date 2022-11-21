<?php

namespace Drupal\inv_builder\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurableInterface;

/**
 * Defines the interface for text processing fonticon plugins.
 *
 * @see \Drupal\shortcode\Annotation\Shortcode
 * @see \Drupal\shortcode\ShortCode\ShortcodePluginManager
 * @see \Drupal\shortcode\Plugin\ShortcodeBase
 * @see plugin_api
 */
interface FontIconInterface extends ConfigurableInterface, PluginInspectionInterface {

  /**
   * Returns the list of icons
   *
   * @return array
   */
  public function icons();
  
  public function library();
}
