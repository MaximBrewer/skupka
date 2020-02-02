<?php
/**
 * 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
defined('_JEXEC') or die;
JToolBarHelper::Title(JText::_('COM_ONEPAGE_ROUTE_CONFIG') , 'generic.png');
JToolBarHelper::apply();

$select = '<select name="route_{n}">'; 
$app = JFactory::getApplication(); 
$jq = $app->get('jquery'); 
$document = JFactory::getDocument(); 

$base = JURI::base(); 
//$document->addStylesheet($base.'components/com_onepage/views/pickup/tmpl/pickup.css');  
JHTMLOPC::stylesheet('pickup.css', 'administrator/components/com_onepage/assets/css/', false);
$jbase = str_replace('/administrator', '', $base); 

$document->addStylesheet($jbase.'components/com_onepage/assets/js/datetimepicker-master/jquery.datetimepicker.css');  
if (empty($jq) && (!OPCJ3))
{
 
 $document->addScript($jbase.'components/com_onepage/assets/js/datetimepicker-master/jquery.js');  

}
else
if (OPCJ3)
		 {
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select');
		 }
		 

$document->addScript($jbase.'components/com_onepage/assets/js/datetimepicker-master/jquery.datetimepicker.js');  

$base = JURI::base(); 
$jbase = str_replace('/administrator', '', $base); 	
if (substr($jbase, -1) !== '/') $jbase .= '/'; 

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noConflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noConflict.js');
else
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noconflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noconflict.js');


$js = '

jQuery(document).ready(function() {
 jQuery(\'.opcdatetimepicker\').datetimepicker();
 
 }); 
'; 
$document->addScriptDeclaration($js); 

foreach ($this->routes as $k=>$v)
 {
    $select .= '<option value="'.$k.'" rel="'.$k.'_option_{n}">'.$v.'</option>'; 
 }
$select .= '</select>'; 
?>
<div class="master">
<div class="pwrapper">
  <div class="col1">
	<?php echo $select; ?> 
  </div>
  
  <div class="col2">
	<input type="text" value="" class="opcdatetimepicker" name="from_{n}" />
  </div>
  
  <div class="col3">
	<input type="text" value="" class="opcdatetimepicker" name="to_{n}" />
  </div>
  
</div>
<div class="col3add"><a href="#"><?php echo JText::_('COM_ONEPAGE_ADD_MORE'); ?> </a>
</div>
</div>