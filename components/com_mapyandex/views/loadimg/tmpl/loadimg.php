<?php
/*
 * @package Joomla 2.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

defined('_JEXEC') or die('Restricted access'); 

$load = $this->img;

echo '<script type="text/javascript">
	//window.top.document.getElementById("loader_img").style.display="none";
	//window.top.document.getElementById("loader_img_success").style.display="block";
	elements = parent.getElementsByClassName("document","loader_img");
  
	  n = elements.length;
	   for (var i = 0; i < n; i++) {
		 var e = elements[i];

		 if(e.style.display == \'block\') {
		   e.style.display = \'none\';
		 } else {
		   e.style.display = \'block\';
		 }
	  }
	  
	  elements = parent.getElementsByClassName("document","desc_load");
  
	  n = elements.length;
	   for (var i = 0; i < n; i++) {
		 var e = elements[i];

		 if(e.style.display == \'block\') {
		   e.style.display = \'none\';
		 } else {
		   e.style.display = \'block\';
		 }
	  }
	  elements = parent.getElementsByClassName("document","submitb");
  
	  n = elements.length;
	   for (var i = 0; i < n; i++) {
		 var e = elements[i];

		 if(e.style.display == \'block\') {
		   e.style.display = \'none\';
		 } else {
		   e.style.display = \'block\';
		 }
	  }
		
	parent.addfiletolist(\''.$load['startfile'].'\',\''.$load['smallfile'].'\');

	</script>';


?>