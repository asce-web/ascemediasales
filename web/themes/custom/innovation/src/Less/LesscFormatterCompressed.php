<?php

namespace Drupal\innovation\Less;
use Drupal\innovation\Less\LesscFormatterClassic;

class LesscFormatterCompressed extends LesscFormatterClassic {
	public $disableSingle = true;
	public $open = "{";
	public $selectorSeparator = ",";
	public $assignSeparator = ":";
	public $break = "";
	public $compressColors = true;

	public function indentStr($n = 0) {
		return "";
	}
}



