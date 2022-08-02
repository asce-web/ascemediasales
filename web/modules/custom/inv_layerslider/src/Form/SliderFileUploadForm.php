<?php

/**
 * @file
 * Contains \Drupal\inv_layerslider\Entity\Form\SliderFileUploadForm.
 */

namespace Drupal\inv_layerslider\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inv_layerslider\Ajax\FileUpload;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\file\Entity\File;

/**
 * Form controller for Slider edit forms.
 *
 * @ingroup inv_layerslider
 */
class SliderFileUploadForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inv_layerslider_file_upload';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $name = '', $default_value = 'file:0') {
    /* @var $entity \Drupal\inv_layerslider\Entity\Slider */
    
    $form[$name] = array(
      '#type' => 'image_browser',
      '#default_value' => $default_value,
    );
	$form['#prefix'] = '<div>';
	$form['#suffix'] = '</div>';
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }

}
