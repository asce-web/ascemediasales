<?php

namespace Drupal\filebrowser\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;


class DeleteForm extends ConfirmFormBase {

  /**
   * @var int
   */
  protected $queryFid;

  /**
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * Common methods
   * @var \Drupal\filebrowser\Services\Common
   */
  protected $common;

  /**
   * Validator methods
   *
   * @var \Drupal\filebrowser\Services\FilebrowserValidator
   */
  protected $validator;

  /**
   * Filebrowser object holds specific data
   *
   * @var \Drupal\filebrowser\Filebrowser
   */
  protected $filebrowser;

  /**
   * @var array
   * Array of fid of files to delete
   */
  protected $itemsToDelete;

  /**
   * ConfirmForm constructor.
   */
  public function __construct() {
    $this->validator = \Drupal::service('filebrowser.validator');
    $this->common = \Drupal::service('filebrowser.common');
    $this->itemsToDelete = null;
  }

  public function getFormId() {
    return 'filebrowser_delete_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $nid = null, $query_fid = 0, $fids_str =  null, $ajax = null) {
    $this->node = Node::load($nid);
    $this->queryFid = $query_fid;
    $this->filebrowser = $this->node->filebrowser;
    $fids = explode(',', $fids_str);

    //  \Drupal::logger('filebrowser')->notice('delete form fids_str  : ' .  $fids_str);

    // This flag indicates that a folder has been selected for deletion.
    $folder_selected = false;
    $files = $this->common->nodeContentLoadMultiple($fids);
    foreach ($files as $fid => $file) {
      // Additional data.
      $file['type'] = unserialize($file['file_data'])->type;
      $file['full_path'] = $this->validator->encodingToFs($this->filebrowser->encoding, $this->validator->getNodeRoot($this->filebrowser->folderPath . $file['path']));
      $file['display_name'] = $this->validator->safeBaseName($file['full_path']);

      // Store item data.
      $this->itemsToDelete[$fid] = $file;
    }

    // Compose the list of files being deleted.
    $list = '<ul>';
    foreach ($this->itemsToDelete as $item) {
      $list .= '<li>';
      if ($item['type'] == 'dir') {
        $folder_selected = true;
        $list .= '<b>' . $item['display_name'] . '</b>';
      }
      else {
        $list .= $item['display_name'];
      }
      $list .= '</li>';
    }
    $list .= '</ul>';

    if ($ajax) {
      $form['#attributes'] = [
        'class' => [
          'form-in-slide-down'
        ],
      ];
      // Add a close slide-down-form button
      $form['close_button'] = $this->common->closeButtonMarkup();
    }
    $form['items'] = array(
      '#type' => 'item',
      '#title' => $this->t('Items being deleted'),
      '#markup' => $list
    );

    // If at least a folder has been selected, add a confirmation checkbox.
    if ($folder_selected) {
      $form['confirmation'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Confirm deletion of selected <b>folders</b> and all of their content.'),
        '#default_value' => false,
      );
    }
    else {
      // No confirmation needed, we'll add a "fake" field.
      $form['confirmation'] = array(
        '#type' => 'value',
        '#value' => TRUE,
      );
    }
    return parent::buildForm($form, $form_state);
  }

  public function getQuestion() {
    return $this->t('Are you sure you want to delete the following items?');
  }

  public function getCancelUrl() {
    return $this->node->urlInfo();
  }

  public function getDescription() {
    return $this->t('this action can not be undone.');
  }

  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    foreach ($this->itemsToDelete as $item) {
      $data = unserialize($item['file_data']);
      $success = file_unmanaged_delete_recursive($data->uri);
      if ($success) {
        // invalidate the cache for this node
        Cache::invalidateTags(['filebrowser:node:' . $this->node->id()]);
      }
      else {
        drupal_set_message($this->t('Unable to delete @file', ['@file' => $data->uri]), 'warning');
      }
    }
    $route = $this->common->redirectRoute($this->queryFid, $this->node->id());
    $form_state->setRedirect($route['name'], $route['node'], $route['query']);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Check if the confirmation checkbox has been checked.
    if (empty($form_state->getValue('confirmation'))) {
      $this->common->debugToConsole('validate');
      $form_state->setErrorByName('confirmation', $this->t('You must confirm deletion of selected folders.'));
    }
    parent::validateForm($form, $form_state);
  }

}