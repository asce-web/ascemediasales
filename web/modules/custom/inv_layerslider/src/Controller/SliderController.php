<?php

namespace Drupal\inv_layerslider\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\inv_layerslider\Entity\Slider;
use Drupal\file\Entity\File;
use Drupal\Core\Ajax\AjaxResponse;

class SliderController extends ControllerBase{
  public function export($inv_slider){
    $slider = Slider::load($inv_slider);
    $data = $slider->get('data')->getValue()[0]['value'];
    $images = array();
    preg_match_all("/\"file:([0-9]+)\"/", $data, $output_array);
    
    foreach($output_array[1] as $fid){
      if($fid){
        $images[$fid] = self::image2base64($fid);
      }
    }
    $export = [
      'name' => $slider->get('name')->getValue()[0]['value'],
      'settings' => $slider->get('settings')->getValue()[0]['value'],
      'data' => $slider->get('data')->getValue()[0]['value'],
      'images' => $images,
    ];
    $export_data = json_encode($export);
    $filename = "slider_{$slider->id()}.txt"; 
    header("Content-Type: text/txt");
    header("Content-Disposition: attachment; filename={$filename}");
    header("Content-Length: " . strlen($export_data));
    print $export_data;
    exit();
  }
  
  public static function image2base64($fid){
    $file = File::load($fid);
    return [
      'data' => base64_encode(file_get_contents($file->getFileUri())),
      'mime' => $file->getMimeType(),
      'filename' => $file->getFilename(),
    ];
  }
  
  public function buildForm(){
    
    $selector = \Drupal::request()->get('selector');
    $name = \Drupal::request()->get('name', 'image');
    $default_value = \Drupal::request()->get('default_value', 'file:0');
    //dsm(\Drupal::request()->request->all());
    $form = \Drupal::formBuilder()->getForm('\Drupal\inv_layerslider\Form\SliderFileUploadForm', $name, $default_value);
    $response = new AjaxResponse();
    //$response->addCommand(new \Drupal\Core\Ajax\ReplaceCommand($selector, $form));
    $response->addCommand(new \Drupal\Core\Ajax\AfterCommand($selector, $form));
    return $response;
  }
}