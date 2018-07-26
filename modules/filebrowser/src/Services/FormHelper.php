<?php

namespace Drupal\filebrowser\Services;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\node\NodeInterface;

/**
 * Class FormHelper
 * Service that provides utilities to forms
 */
class FormHelper extends ControllerBase {

  /**
   * @var NodeInterface
   * Node holding this directory listing
   */
  protected $node;

  public function initForm(&$form, NodeInterface $node) {
    $this->node = $node;
    $form['nid'] = [
      '#type' => 'value',
      '#value' => $this->node->id(),
    ];
    $form['fids'] = [
      '#type' => 'value',
      '#value' => null,
    ];
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'filebrowser/filebrowser-styles';
  }

  public function createActionBar(&$form, array $actions, $relative_fid) {
    //print_r(array_keys($params));die;
    $form['action'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => [
          'form-action-actions-wrapper',
        ],
      ],
    ];

    foreach($actions as $action) {
      switch ($action['type']) {
        case 'link':
          // Create a link that opens (JS) a form on the page
          $form['action'][$action['operation']] = $this->createLink($action, $this->node, $relative_fid);
          break;
        case 'button':
          // Create a submit button that opens (JS) a form on the page
          $form['action'][$action['operation']] = $this->createAjaxButton($action);
          break;
        default:
          // Create a normal submit button
          $form['action'][$action['operation']] = $this->createNormalButton($action);
      }
    }
  }

  public function createLink(array $action, NodeInterface $node, $relative_fid) {
    $route = 'filebrowser.action';
    $link_options = [
      'attributes' => [
        'class' => [
          'use-ajax',
          'form-action-link',
          'button'
        ]]];
    return [
      '#markup' => Link::createFromRoute($action['title'], $route, [
        'op' => $action['operation'],
        'method' => 'nojs',
        'nid' => $node->id(),
        'query_fid' => $relative_fid,
      ],
        $link_options)->toString(),
    ];
  }

  public function createAjaxButton($action) {
    return [
      '#type' => 'submit',
      '#return_value' => $action['operation'],
      '#value' => $action['title'],
      '#name' => $action['operation'],
      '#title' => $action['title'],
      '#attributes' => ['class' => ['use-ajax-submit',],],
      '#validate' => ['::ajaxValidate'],
      // We do not specify #action so the normal submit function is used
//            '#action' => Url::fromRoute('filebrowser.form.link.action', [
//            'op' => $action['operation'],
//            'method' => 'nojs',
//            'nid' => $this->nid,
//            'query_fid' => $this->relativeFid,
//            ])->getInternalPath(),
//            '#submit' => [],
    ];
  }

  public function createNormalButton($action) {
    return [
      '#type' => 'submit',
      '#return_value' => $action['operation'],
      '#value' => $action['title'],
      '#name' => $action['operation'],
      '#title' => $action['title'],
    ];
  }

}
