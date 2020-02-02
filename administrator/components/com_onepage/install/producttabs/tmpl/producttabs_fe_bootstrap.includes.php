<?php
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

$i = $this->params->get('includeframework', true); 
if (!empty($i)) {
  JHtml::_('bootstrap.framework'); 
}