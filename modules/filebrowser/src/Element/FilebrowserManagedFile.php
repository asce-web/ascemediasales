<?php

namespace Drupal\filebrowser\Element;

use Drupal\file\Element\ManagedFile;

/**
 * Provides an AJAX/progress aware widget for uploading and saving a file.
 *
 * @FormElement("filebrowser_managed_file")
 */
class FilebrowserManagedFile extends ManagedFile {
  /**
   * @inheritDoc
   */
  public function getInfo() {
    $array = parent::getInfo();
    $array['#multiple'] = true;
    return $array;
  }

}