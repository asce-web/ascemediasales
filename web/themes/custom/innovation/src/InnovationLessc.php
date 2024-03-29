<?php

#require_once dirname(__FILE__) . '/lessc.inc.php';
namespace Drupal\innovation;

use Drupal\Core\File\FileSystemInterface;
use Drupal\innovation\Less\LessCompile;

class InnovationLessc {

  var $theme;
  var $output;
  var $css;
  var $lessc;
  var $importDir;
  var $fonts;
  var $google_fonts = array();

  function __construct($theme) {
    $this->theme = $theme;
    $this->lessc = new LessCompile();
    //$this->lessc->setImportDir(drupal_get_path('theme', $theme->theme));
    $this->lessc->setImportDir(DRUPAL_ROOT);
    //$this->importDir = drupal_get_path('theme', $theme->theme);
    $this->importDir = DRUPAL_ROOT;
    $this->__addPresetVariables();
    $this->__addBaseCSS();
  }

  function addVariable($name, $value) {
    $this->output .= "@{$name}:{$value};\n";
  }

    public function copyImages($src, $dst) {
    $update = false;
    if (!is_dir($dst)) {
      //file_prepare_directory($dst, FILE_CREATE_DIRECTORY);
      //file_prepare_directory($dst, FILE_MODIFY_PERMISSIONS);
	  \Drupal::service('file_system')->prepareDirectory($dst, FileSystemInterface::CREATE_DIRECTORY);
      \Drupal::service('file_system')->prepareDirectory($dst, FileSystemInterface::MODIFY_PERMISSIONS);
      $update = true;
    }
    if (filemtime($src) > filemtime($dst)) {
      $update = true;
    }
    if ($update) {
      if ($handle = @opendir($src)) {
        while (false !== ( $file = readdir($handle))) {
          if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
              $this->copyImages($src . '/' . $file, $dst . '/' . $file);
            }
            else {
              \Drupal::service('file_system')->copy($src . '/' . $file, $dst, FileSystemInterface::EXISTS_REPLACE );
            }
          }
        }
        closedir($handle);
      }
      else {
        \Drupal::logger('file')->error('@dir can not be opened', array('@dir' => $src));
      }
    }
  }
 
  function filetime($file) {
    if (($time = @filemtime($file)) != FALSE) {
      return $time;
    }
    if (($time = @filemtime($this->importDir . '/' . $file)) != FALSE) {
      return $time;
    }
    return 0;
  }


  function complie($file = null) {
    $update = false;

	$config =\Drupal::service('config.factory')->getEditable('innovation.settings');
	if ($config->get('updated')) {
		$update = true;
	}

	if (!is_file($file)) {
		$update = true;
	}

    $ftime = $this->filetime($file);
    if (!empty($this->theme->lessc)) {
      foreach ($this->theme->lessc as $lessc_file) {
        if ($ftime < $this->filetime($lessc_file)) {
          $update = true;
        }
        $this->output .= "@import \"$lessc_file\";\n";
      }
    }

    if ($update) {
      $this->__setupFonts();
      if(!empty($this->google_fonts)){
        $this->output = '@import url(//fonts.googleapis.com/css?family='.implode('|',$this->google_fonts).');' . $this->output;
      }
      try {
		$theme_path = drupal_get_path('theme', $this->theme->theme);  
		if(is_dir($theme_path. '/assets/images')){
			$this->copyImages($theme_path. '/assets/images', 'public://innovation/' . $this->theme->theme . '/images');
		}
        $this->css = $this->lessc->compile($this->output);
      } catch (exception $e) {
        Drupal::messenger()->addMessage("fatal error: " . $e->getMessage(), 'error');
        return FALSE;
      }
      if ($file) {
        $css_output = "/*This file is generated by less css (http://lesscss.org) */\n/*Please do not modify this file content. It will be generated again when you change style*/\n" . $this->css;
        \Drupal::service('file_system')->saveData($css_output, $file, FileSystemInterface::EXISTS_REPLACE);
      }
    }
	$config->set('updated', false);
	$config->save();
    return $this->css;
  }

  private function __addPresetVariables() {
    foreach ($this->theme->lessc_vars as $key => $value) {
      $this->addVariable($key, $value);
    }
  }

  private function __addBaseCSS() {
      $this->output .= 'body{color: @text_color;}a:not(.btn){color:@link_color; &:hover{color:@link_hover_color}}h1,h2,h3,h4,h5,h6{color:@heading_color}';
  }
  private function __setupFonts() {
    $elements = array('body','h1','h2','h3','h4','h5','h6','custom_font_1','custom_font_2','custom_font_3','custom_font_4','custom_font_5','custom_font_6','custom_font_7','custom_font_8','custom_font_9','custom_font_10','global_font_1','global_font_2','global_font_3','global_font_4','global_font_5');
    foreach($elements as $element){
      $this->__setupFont($element);
    }
  }
  private function __setupFont($element) {
    if(empty($this->fonts)){
      $fonts = json_decode(file_get_contents(__DIR__.'/font-list.json'));
      foreach($fonts->items as $k=>$font){
        $fonts->items[$k]->label = $font->family;
        $fonts->items[$k]->value = $font->family;
        if(!isset($fonts->items[$k]->{'data-google'})){
          $fonts->items[$k]->{'data-google'} = 1;
        }
      }
      $this->fonts = $fonts->items;
    }
    $font = theme_get_setting($element);
    if(!empty($font)){
      $font_arr = explode(':', $font,5);
      $font_family = str_replace('+','',$font_arr[0]);
      $this->addVariable($element,'"'.$font_family.'"');
      if(empty($font_arr[0])) return false;
      if(strpos($element,'global_font') !== false){
        $font = innovation_getfont($font_arr[0]);
        if($font->{'data-google'} == 1){
          $this->google_fonts[] = str_replace(' ','+',$font_arr[0]) . ':' . $font_arr[1];
        }
        return false;
      }
      if(strpos($element,'custom_font') !== false && empty($font_arr[4])) return false;
      $font = innovation_getfont($font_arr[0]);
      if($font->{'data-google'} == 1){
        $this->google_fonts[] = str_replace(' ','+',$font_arr[0]) . ':' . $font_arr[1];
      }
      $css = array();
      $selector = $element;
      if($font->{'data-google'} != 1){
        $css[] = "font-family:{$font->family};";
      }elseif(isset($font->{'font-family'})){
        $css[] = "font-family:{$font->{'font-family'}};";
      }else{
        $css[] = "font-family:\"{$font_arr[0]}\";";
      }
      if(strpos($font_arr[1],'italic') !== false){
        $css[] = "font-style: italic;";
        $font_arr[1] = str_replace('italic','',$font_arr[1]);
        if(!empty($font_arr[1])){
          $css[] = "font-weight: {$font_arr[1]};";
        }
      }elseif($font_arr[1]== 'regular'){
        $css[] = "font-weight: 400;";
      }else{
        $css[] = "font-weight: {$font_arr[1]};";
      }
      if(isset($font_arr[2]) && !empty($font_arr[2])){
        $css[] = "font-size: $font_arr[2];";
      }
      if(isset($font_arr[3]) && !empty($font_arr[3])){
        $css[] = "line-height: $font_arr[3];";
      }
      if(isset($font_arr[4]) && !empty($font_arr[4])){
        $selector = $font_arr[4];
      }
      $this->output .= $selector . '{'.implode('',$css).'}';
    }
  }
}

/*Fonts helper*/
function innovation_getfont($family){
  $drupal_static = &drupal_static(__FUNCTION__);
  if (!isset($drupal_static['innovation_fonts'])) {
    $fonts = json_decode(file_get_contents(__DIR__.'/font-list.json'));
    foreach($fonts->items as $k=>$font){
      $fonts->items[$k]->label = $font->family;
      $fonts->items[$k]->value = $font->family;
      if(!isset($fonts->items[$k]->{'data-google'})){
        $fonts->items[$k]->{'data-google'} = 1;
      }
    }
    $drupal_static['innovation_fonts'] = $fonts;
  }else{
    $fonts = $drupal_static['innovation_fonts'];
  }
  foreach($fonts->items as $font){
    if($font->family == $family){
      return $font;
    }
  }
  $font = new \stdClass();
  $font->family = $family;
  $font->{'data-google'} = 0;
  return $font;
}
