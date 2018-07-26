<?php

namespace Drupal\inv_builder\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\inv_builder\Ajax\CloseBuilderModalDialogCommand;

class ShortcodeSettingsForm extends FormBase {

  /**
   * 
   * @param array $form
   * @param FormStateInterface $form_state
   * @param type $shortcode_id
   * @param type $action: add or edit
   * @return string
   */
  public function buildForm(array $form, FormStateInterface $form_state, $shortcode_id = null, $action = 'add') {

    $builderShortcodeService = \Drupal::service('inv_builder.shortcode');
    $shortcode_plugin = $builderShortcodeService->getShortcodePlugin($shortcode_id);
    $form = $shortcode_plugin->settingsForm($form, $form_state);
    $form['#prefix'] = '<div class="inv-builder-shortcode-settings">';
    $form['#suffix'] = '</div>';
    $form['status_messages'] = [
      '#type' => 'status_messages',
      '#weight' => -10,
    ];

    $form['shortcode_id'] = array(
      '#type' => 'hidden',
      '#default_value' => $shortcode_id,
    );

    $form['text'] = array(
      '#type' => 'hidden',
      '#default_value' => \Drupal::request()->get('text'),
    );

    $form['action'] = array(
      '#type' => 'hidden',
      '#default_value' => $action,
    );

    $form['actions'] = array(
      '#type' => 'actions',
    );

    $form['actions']['cancel'] = array(
      '#type' => 'button',
      '#value' => 'Cancel',
      '#ajax' => array(
        'callback' => '::closeModal',
      ),
    );

    $form['actions']['save'] = array(
      '#type' => 'button',
      '#value' => $this->t('Save'),
      '#ajax' => array(
        'callback' => '::submitForm',
      ),
    );
    
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    return $form;
  }

  public function getFormId() {
    return 'inv_builder_shortcode_settings';
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
	 if($form_state->hasAnyErrors()){
      $response->addCommand(new ReplaceCommand('.inv-builder-shortcode-settings', $form));
      return $response;
    }

    $values = $form_state->getValues();
    foreach (array('cancel', 'save', 'form_build_id', 'form_token', 'form_id', 'op', 'action', 'shortcode_id', 'text', 'element_settings__active_tab', 'html_content') as $key) {
      unset($values[$key]);
    }
    $attr = [];
    foreach ($values as $k => $v) {
      if(isset($v['fids'])){
        if(isset($v['fids'][0])){
          $attr[] = $k . '=' . '\'file:' . $v['fids'][0] . '\'';
        }
      }else{
        $attr[] = $k . '=' . '\'' . $v . '\'';
      }
    }
    $shortcode_id = $form_state->getValue('shortcode_id');
    $text = $form_state->getValue('text');
    
    if($html_content = $form_state->getValue('html_content')){
      $text = is_array($html_content) ? $html_content['value'] : $html_content;
    }
    
    $output = "[{$shortcode_id} " . implode(' ', $attr) . "]{$text}[/{$shortcode_id}]";

    $builderShortcodeService = \Drupal::service('inv_builder.shortcode');
    $html = $builderShortcodeService->process($output, null, 'innovation_builder');
    $action = $form_state->getValue('action');
    $selector = $action == 'add' ? '.active-element-content' : '.active-element';
    if ($action == 'add') {
      $response->addCommand(new AppendCommand($selector, $html));
    }
    else {
      $response->addCommand(new ReplaceCommand($selector, $html));
    }
    $response->addCommand(new CloseBuilderModalDialogCommand());
    return $response;
  }

  public function closeModal() {
    $response = new AjaxResponse();
    $command = new CloseBuilderModalDialogCommand();
    $response->addCommand($command);
    return $response;
  }

}