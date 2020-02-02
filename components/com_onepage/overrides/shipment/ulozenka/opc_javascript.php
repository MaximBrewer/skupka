<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
defined('_JEXEC') or die('Restricted access');

JHTMLOPC::_('behavior.modal', 'a.ulozenkamodal'); 
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.'helper.php'))
{
$db = JFactory::getDBO(); 
		$q = 'select shipment_params from #__virtuemart_shipmentmethods where shipment_element = \'ulozenka\' '; 
		$db->setQuery($q); 
		$params = $db->loadResult(); 
		$err = true; 
		if (empty($params)) $err = true; 
		else
		{
		$a = explode('|', $params); 
		$obj = new stdClass(); 
		foreach ($a as $p)
		 {
		    $a2 = explode('=', $p); 
			if (!empty($a2) && (count($a2)==2))
			 {
			   $keyX = $a2[0]; 
			   $obj->$keyX = json_decode($a2[1]); 
			 }
		 }

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'ulozenka'.DIRECTORY_SEPARATOR.'helper.php'); 
$xml = UlozenkaHelper::getPobocky($obj); 

$js_adresa="\n\nvar adresa=new Array();";
				$js_oteviraci_doba="\n\nvar oteviraci_doba=new Array();";
				$js_cena="\n\nvar cena=new Array();";
				$js_values="\n\nvar values=new Array();";
//				$js_mapa="\n\nvar mapa=new Array();";

//if (!defined('ulozenka_javascript'))
{
$document = JFactory::getDocument(); 
$document->addScriptDeclaration(
				"\n".'//<![CDATA['."\n" 
				."\n\nvar detail_url='".JURI::root()."plugins/vmshipment/ulozenka/detail_pobocky.php?id=';\n"	
				."\n\n
				function changeUlozenka(id) {
				    if (typeof jQuery != 'undefined')
					 jQuery('.zasielka_div1').not('#ulozenka_branch_' + id).hide();
					document.getElementById('ulozenka_pobocka').value=id;
					
					var d = document.getElementById('ulozenka_saved'); 
					if (d != null)
					d.value = id; 
					
					var dd = document.getElementById('ulozenka_branch_'+id); 
					if (dd != null)
						dd.style.display='block';
					
					if (typeof jQuery != 'undefined')
					{
					  jQuery('#shipment_id_".$method->virtuemart_shipmentmethod_id."').click(); 
					}
					else
					document.getElementById('shipment_id_".$method->virtuemart_shipmentmethod_id."').onclick();
					
					if (typeof Onepage != 'undefined')
					Onepage.changeTextOnePage3();
					
				};\n".'

 
 var ulozenkaId = null;
 var ulozenkaIndex = null;  
 var ulozenkaVmId = '.$method->virtuemart_shipmentmethod_id.'; 
 var lastVmOpt = \'\'; 
 function saveUlozenka()
 {
   var dx = document.getElementsByName(\'virtuemart_shipmentmethod_id\'); 
   if (dx != null)
   for (var i = 0; i<dx.length; i++)
    {
	  if (dx[i].checked)
	  lastVmOpt = dx[i].id; 
	}
   var b = document.getElementsByName(\'pobocky\'); 
   if (b != null)
   for (var i=0; i<b.length; i++)
     {
	   if (b[i].options != null)
	   if (b[i].selectedIndex != null)
	   if (b[i].selectedIndex > 0)
	   {
	    ulozenkaId = b[i].options[b[i].selectedIndex].value;
	    ulozenkaIndex =  b[i].selectedIndex; 
	    return; 
	   }
	 }
 }
 function restoreUlozenka()
 {
   if (lastVmOpt != \'shipment_id_\'+ulozenkaVmId) return; 
   
   
   
   Onepage.op_log(ulozenkaId); 
   
   if (typeof ulozenkaId != \'undefined\')
   if (ulozenkaId != null)
   {
    var d = document.getElementById(\'pobocky\'); 
	if (d!= null) 
	  {
	    if (d.options[ulozenkaIndex] != null)
		if (d.options[ulozenkaIndex].value != null)
		if (d.options[ulozenkaIndex].value == ulozenkaId)
		{
	     d.selectedIndex = ulozenkaIndex; 
		 var vmid = d.getAttribute(\'vmid\'); 
		 changeUlozenka(ulozenkaId); 
		}
	  }
    
   }

 }
 
 
 addOpcTriggerer(\'callBeforeAjax\', \'saveUlozenka()\'); 
 addOpcTriggerer(\'callAfterRender\', \'restoreUlozenka()\'); 

				
				
//]]>'."\n");  
 //define('ulozenka_javascript'); 
 }				
	}			
}	