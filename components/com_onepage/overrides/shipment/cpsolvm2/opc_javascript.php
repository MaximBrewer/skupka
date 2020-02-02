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



/*
if (!empty($this->ref->methods))
{
  foreach ($this->ref->methods as $m)
   {
   $vm_id = $m->virtuemart_shipmentmethod_id; 
   break; 
   }
}
*/


$document = JFactory::getDocument(); 
 $js = '

//<![CDATA[
  function selectCspol(id)
   {
      var d = document.getElementById(\'shipment_id_\'+id); 

	  if (jQuery != \'undefined\')
	  {	  
	   jQuery(\'#shipment_id_\'+id).click(); 
	  }
	  else
	  {
	   d.onclick(); 
	  }
   }
//]]>
function checkIfSelected()
{
  var d = document.getElementsByName("virtuemart_shipmentmethod_id"); 
  if (d != null)
   {
      var iss = false; 
      for (var i=0; i<d.length; i++)
	   {
	      var first = d[i]; 
		  		   if (d[i].checked != null)
				   if (d[i].checked)
				   iss = true; 

	   }
	   if (!iss)
	    {
		  if (first != null)
		   if (typeof jQuery != \'undefined\')
		    jQuery(first).click(); 
		   else
		    first.onclick(); 
		}
   }
}
addOpcTriggerer(\'callAfterRender\', \'checkIfSelected()\'); 

  '; 

$document->addScriptDeclaration($js); 
