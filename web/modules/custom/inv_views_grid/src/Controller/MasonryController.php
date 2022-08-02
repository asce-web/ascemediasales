<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 1/10/2016
 * Time: 11:26 AM
 */

namespace Drupal\inv_views_grid\Controller;


use Drupal\Core\Controller\ControllerBase;

class MasonryController {
    public function save($view, $item, $width, $height) {
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
        $result = \Drupal::database()->select('inv_masonry', 'm')
            ->fields('m')
            ->condition('view', $view, '=')
            ->condition('item', $item, '=')
            ->execute()
            ->fetchAssoc();
        if ($result) {
            // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
            // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
            \Drupal::database()->update('inv_masonry')
                ->fields(array(
                    'width' => $width,
                    'height' => $height,
                ))
                ->condition('view', $view, '=')
                ->condition('item', $item, '=')
                ->execute();
        } else {
            // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
            // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
            \Drupal::database()->insert('inv_masonry')
                ->fields(array(
                    'view' => $view,
                    'item' => $item,
                    'width' => $width,
                    'height' => $height,
                ))
                ->execute();
        }
        return array();
    }
}