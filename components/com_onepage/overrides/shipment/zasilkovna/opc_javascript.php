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
	$zas_model = VmModel::getModel('zasilkovna');
	if (!empty($zas_model))
	{
		$zas_model->setConfig($method->zasilkovna_api_pass); 
        $js_url    = $zas_model->updateJSApi();
		
		JHtml::script($js_url); 
	}
	*/

$js = '
//<![CDATA[  
 function opc_zas_change(el, vmid, runText)
 {
   var id = el.options[el.selectedIndex].value; 
   var branch_id = id; 
   
   var ctp3 = true; 
   if (typeof runText != \'undefined\')
   if (runText == false)
   ctp3 = false; 
  
   var d = document.getElementById(\'zas_branch_\'+opc_last_zas);
   if (d != null) 
   d.style.display = \'none\'; 
    opc_last_zas = id; 
   var d = document.getElementById(\'zas_branch_\'+id); 
   if (d != null) 
   d.style.display = \'block\'; 
   
   //var branch_id = document.getElementById(\'branch_id\'+id).value; 
   document.getElementById(\'branch_id\').value= branch_id; 
 //   document.getElementById(\'branch_currency\').value= document.getElementById(\'branch_currency\'+id).value; 
 //	 document.getElementById(\'branch_name_street\').value= document.getElementById(\'branch_name_street\'+id).value; 
	 
	var d = document.getElementById(\'shipment_id_\'+vmid);
	if (d != null)
	{
		if (ctp3)
		if (!d.checked)
		{
	if (typeof jQuery != \'undefined\') jQuery(d).click(); 
	else
	if (typeof d.click != \'undefined\')
	d.click(); 
		
		var isChecked = false; 
		}
		else var isChecked = true; 
		
	//d.setAttribute(\'checked\', true); 
	//d.setAttribute(\'selected\', true); 
	if (id == "") 
	 {
	   d.value=d.value+"|choose_shipping"; 
	 
	 }
     else
	 {
      d.value = vmid; 
	  d.setAttribute(\'saved_id\', \'zasilkovna_shipment_id_\'+vmid+\'_\'+id); 
	 }
	 
	 if (ctp3)
	 Onepage.changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);
	}
	return true; 
	
 }
 var storedBranch = null; 
 var storedId = null; 
 function saveZas()
 {
  
   var b = document.getElementsByName(\'branch\'); 
   for (var i=0; i<b.length; i++)
     {
	   if (b[i].options != null)
	   if (b[i].selectedIndex != null)
	   if (b[i].selectedIndex >= 0)
	   {
	    storedId = b[i].id;
	    storedBranch = b[i].selectedIndex; 
		//Onepage.op_log(storedBranch); 
	    return; 
	   }
	 }
 }
 function restoreZas()
 {
 
   if (typeof storedBranch != \'undefined\')
   if (storedBranch != null)
   {
    var d = document.getElementById(storedId); 
	if (d!= null) 
	  {
	    d.selectedIndex = storedBranch; 
		var vmid = storedId.split(\'branchselect_\').join(\'\'); 
		opc_zas_change(d, vmid, false); 
	  }
    
   }

 }
 
 
 addOpcTriggerer(\'callBeforeAjax\', \'saveZas()\'); 
 addOpcTriggerer(\'callAfterRender\', \'restoreZas()\'); 
 var opc_last_zas = 0; 
//]]> 
'; 
JFactory::getDocument()->addScriptDeclaration($js);