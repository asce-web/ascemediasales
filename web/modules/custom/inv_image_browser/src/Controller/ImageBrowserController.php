<?php
namespace Drupal\inv_image_browser\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Html;


class ImageBrowserController extends ControllerBase{
  
  public function page(){
    $html_id = Html::getUniqueId('inv-image-browser');
    return [
      [
        '#theme' => 'links',
        '#attributes' => ['class' => ['inv-image-browser-tabs']],
        '#links' => [
          'upload_link' => [
            'title' => $this->t('Upload'),
            'url' => \Drupal\Core\Url::fromRoute('inv_image_browser.upload'),
            'ajax' => [
              'wrapper' => $html_id,
              'method' => 'html',
            ],
          ],
          'library_browser' => [
            'title' => $this->t('Library'),
            'url' => \Drupal\Core\Url::fromRoute('inv_image_browser.library'),
            'ajax' => [
              'wrapper' => $html_id,
              'method' => 'html',
            ],
          ],
        ],
      ],
      ['#markup' => '<div id="' . $html_id . '"></div>'],
    ];
  }

}