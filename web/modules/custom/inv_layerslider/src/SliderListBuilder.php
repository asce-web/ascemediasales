<?php

/**
 * @file
 * Contains \Drupal\inv_layerslider\SliderListBuilder.
 */

namespace Drupal\inv_layerslider;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Slider entities.
 *
 * @ingroup inv_layerslider
 */
class SliderListBuilder extends EntityListBuilder {
//  use Link;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Slider ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\inv_layerslider\Entity\Slider */
    $row['id'] = $entity->id();
	$row['name'] = Link::fromTextAndUrl(
		$entity->label(),
      new Url(
        'entity.inv_slider.edit_form', array(
          'inv_slider' => $entity->id(),
        )
      )
	);
    $row += parent::buildRow($entity);
    
    $row['operations']['data']['#links']['edit_slides'] = [
      'title' => t('Edit Slides'),
      'weight' => 0,
      'url' => \Drupal\Core\Url::fromRoute('entity.inv_slider.edit_slides_form', ['inv_slider' => $entity->id()]),
    ];
    
    $row['operations']['data']['#links']['slider_settings'] = [
      'title' => t('Settings'),
      'weight' => 1,
      'url' => \Drupal\Core\Url::fromRoute('entity.inv_slider.settings_form', ['inv_slider' => $entity->id()]),
    ];
    
    $row['operations']['data']['#links']['slider_duplicate'] = [
      'title' => t('Duplicate'),
      'weight' => 2,
      'url' => \Drupal\Core\Url::fromRoute('entity.inv_slider.duplicate', ['inv_slider' => $entity->id()]),
    ];
    
    $row['operations']['data']['#links']['slider_export'] = [
      'title' => t('Export'),
      'weight' => 2,
      'url' => \Drupal\Core\Url::fromRoute('inv_slider.export', ['inv_slider' => $entity->id()]),
    ];
    
    $weight = array();
    foreach ($row['operations']['data']['#links'] as $key => $link)
    {
        $weight[$key] = $link['weight'];
    }
    array_multisort($weight, SORT_ASC, $row['operations']['data']['#links']);
    
    return $row;
  }

}
