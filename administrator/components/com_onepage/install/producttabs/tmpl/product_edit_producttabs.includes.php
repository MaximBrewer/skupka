<?php
/* license: commercial ! */


defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


JHtml::script('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/js/uikit.min.js'); 	
		JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/css/uikit.min.css'); 
		JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/css/components/sortable.min.css'); 
		JHtml::stylesheet('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/css/components/sortable.gradient.min.css'); 
		JHtml::script('//cdnjs.cloudflare.com/ajax/libs/uikit/2.26.3/js/components/sortable.js');

		
			$root = Juri::root(); 
		if (substr($root, -1) !== '/') $root .= '/'; 
		$sor = $root.'plugins/system/producttabs/assets/helper.js'; 
		JHtml::script($sor); 
		$sor = $root.'plugins/system/producttabs/assets/helper.css'; 
		JHtml::stylesheet($sor); 