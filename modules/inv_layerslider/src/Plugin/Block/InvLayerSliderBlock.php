<?php

namespace Drupal\inv_layerslider\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\inv_layerslider\Entity\Slider;

/**
 * Provides a 'LayerSlider' Block
 *
 * @Block(
 *   id = "inv_layerslider",
 *   admin_label = @Translation("Inv LayerSlider"),
 *   category = @Translation("Inv LayerSlider"),
 *   deriver = "Drupal\inv_layerslider\Plugin\Derivative\InvLayerSliderBlock",
 * )
 */
class InvLayerSliderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $slider_id = $this->getDerivativeId();
    $slider = $slider = Slider::load($slider_id);
    $html_id = \Drupal\Component\Utility\Html::getUniqueId('inv_layerslider_' . REQUEST_TIME);
    $render = [
      '#theme' => 'inv_layerslider_slider',
      '#slider' => $slider,
      '#html_id' => $html_id,
      '#cache' => [
        'max-age' => 0,
      ],
      '#contextual_links' => [
        'inv_layerslider' => [
          'route_parameters' => ['inv_slider' => $slider_id]
        ],
      ]
    ];
	$render['#attached']['library'][] = 'inv_layerslider/frontend';
	$render['#attached']['drupalSettings'] = ['inv_layerslider_settings' => [$html_id => $slider->getSettings()],];

    return $render;
  }

}