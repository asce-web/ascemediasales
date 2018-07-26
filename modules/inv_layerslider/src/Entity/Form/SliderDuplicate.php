<?php

/**
 * @file
 * Contains \Drupal\inv_layerslider\Entity\Form\SliderDuplicate.
 */

namespace Drupal\inv_layerslider\Entity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inv_layerslider\Entity\Slider;

/**
 * Provides a form for deleting Slider entities.
 *
 * @ingroup inv_layerslider
 */
class SliderDuplicate extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inv_layerslider_slides_edit';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $inv_slider = 0) {
    $slider = Slider::load($inv_slider);
    $form['name'] = array(
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#description' => t('The name of the Slider entity.'),
      '#default_value' => 'Copy of ' . $slider->get('name')->getString(),
    );
    
    $form['slider_id'] = array(
      '#type' => 'hidden',
      '#default_value' => $inv_slider,
    );
    
    $form['actions'] = array(
      '#type' => 'actions',
    );
    
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => $this->t('Duplicate'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $slider = Slider::load($form_state->getValue('slider_id'));
    $slider_new = $slider->createDuplicate();
    $slider_new->set('name', $form_state->getValue('name'));
    $slider_new->save();
    drupal_set_message('Slider has been duplicated.');
    $form_state->setRedirect('entity.inv_slider.collection');
  }

}
