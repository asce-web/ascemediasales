<?php

namespace Drupal\innovation\Less;
use Drupal\innovation\Less\LesscFormatterClassic;

class LesscFormatterLessjs extends LesscFormatterClassic {
	public $disableSingle = true;
	public $breakSelectors = true;
	public $assignSeparator = ": ";
	public $selectorSeparator = ",";
}


