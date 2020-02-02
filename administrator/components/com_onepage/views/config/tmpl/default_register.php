<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
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

$document = JFactory::getDocument(); 
$css = '

#opc_config_wrapper {
 display: none; 
}
'; 
$document->addStyleDeclaration($css); 

include(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php");

if (!isset($rupostel_email)) $rupostel_email = '';
 ?> <?php
?><div id="opc_registration_tab">
<fieldset><legend><?php echo JText::_('COM_ONEPAGE_REGISTRATION_HEADER'); ?></legend>
  <p><?php echo JText::_('COM_ONEPAGE_REGISTRATION_NOTICE'); ?></p>
  <table class="admintable" style="width: 100%;">
		
		<tr>
	    <td class="key">
	     <label for="opc_registration_username"><?php echo JText::_('COM_ONEPAGE_DESIRED_USERNAME'); ?></label> 
	    </td>
	    <td  >
		
		<input type="text" value="<?php echo $this->registration->opc_registration_username; ?>" name="opc_registration_username"  oninvalid="this.setCustomValidity('Enter User desired Username Here')" oninput="setCustomValidity('')"  id="opc_registration_username" />
        </td>
		</tr>

		<tr>		
	    <td class="key">
	     <label for="opc_registration_email"><?php echo JText::_('COM_ONEPAGE_REGISTRATON_EMAIL'); ?></label> 
	    </td>
	    <td  >
		
		<input type="email" value="<?php 
		if (stripos($rupostel_email, '@')!==false) echo $rupostel_email; 
		?>" name="opc_registration_email" id="opc_registration_email" validate="validate" required="required" class="required" onblur="return opc_validate(this);" />
		
        </td>
		</tr>

		<tr>
	    <td class="key">
	     <label for="opc_registration_name"><?php echo JText::_('COM_ONEPAGE_REGISTRATON_NAME'); ?></label> 
	    </td>
	    <td  >
		<input type="text" value="<?php echo $this->registration->opc_registration_name; ?>" name="opc_registration_name" id="opc_registration_name" />
        </td>
		</tr>
		
		
		<tr>
	    <td class="key">
	     <label for="installed_version"><?php echo JText::_('COM_ONEPAGE_REGISTRATON_MATCHING_NAME'); ?></label> 
	    </td>
	    <td  >
		<input type="text" value="<?php echo $this->registration->opc_registration_company; ?>"  name="opc_registration_company" id="opc_registration_company" />
        </td>
		</tr>
		
		

		<tr>
	    <td class="key">
	     <label for="installed_version"><?php echo JText::_('COM_ONEPAGE_REGISTRATION_AGREE_PRIVACY'); ?></label> 
	    </td>
	    <td  >
		<input type="checkbox" value="1" name="opc_registration_privacy" id="opc_registration_privacy" />
        </td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="installed_version"><?php echo JText::_('COM_ONEPAGE_REGISTRATION_AGREE_TOS'); ?></label> 
	    </td>
	    <td  >
		<input type="checkbox" value="1" name="opc_registration_tos" id="opc_registration_tos"  />
        </td>
		</tr>
		
		<tr>
	    <td class="key">
	     <label for="installed_version"><?php echo JText::_('COM_ONEPAGE_REGISTRATION_AGREE_NEWSLETTER'); ?></label> 
	    </td>
	    <td  >
		<input type="checkbox" value="1" name="opc_registration_newsletter" id="opc_registration_newsletter" />
        </td>
		</tr>
		
		
		<tr>
	    <td >
	     <?php echo JText::_('COM_ONEPAGE_REGISTRATION_REGISTER_NOTICE2'); ?>
	    </td>
	    <td  >
		<input type="button" value="<?php echo JText::_('COM_ONEPAGE_REGISTRATION_REGISTER_BUTTON'); ?>" name="opc_registration_newsletter" onclick="return registerMe();" id="opc_registration_newsletter" />
		<input type="button" value="<?php echo JText::_('COM_ONEPAGE_REGISTRATION_REGISTER_CLOSE'); ?>" name="opc_registration_newsletter" onclick="return closeR();" id="opc_registration_newsletter"  />
		<input type="button" onclick="return closeR2();" value="<?php echo JText::_('COM_ONEPAGE_REGISTRATION_REGISTER_DONOTSHOW'); ?>" name="opc_registration_newsletter" id="opc_registration_newsletter" />
        </td>
		</tr>
		
		
		
		</table>

		

		
</fieldset>
</div>

<script>
  function registerMe()
  {
     var opc_registration_username = document.getElementById('opc_registration_username').value; 
	 var opc_registration_email = document.getElementById('opc_registration_email').value; 
	 
	 var opc_registration_name = document.getElementById('opc_registration_name').value; 
	 
	 var opc_registration_company = document.getElementById('opc_registration_company').value; 
	 
	 var opc_registration_privacy = document.getElementById('opc_registration_privacy'); 
	 
	 var opc_registration_tos = document.getElementById('opc_registration_tos'); 
	 
	 if (!opc_registration_tos.checked)
	  {
	    alert("<?php echo addslashes(JText::_('COM_ONEPAGE_REGISTRATION_TOS_MUSTAGREE')); ?>"); 
	    return false; 
	  }
	  
	  if (!opc_registration_privacy.checked)
	  {
	    alert("<?php echo addslashes(JText::_('COM_ONEPAGE_REGISTRATION_PRIVACY_MUSTAGREE')); ?>"); 
	    return false; 
	  }
	 
	 
	 
	 var opc_registration_newsletter = document.getElementById('opc_registration_newsletter'); 
	 
	 if (typeof registerMeRupostel != 'undefined')
	 {
	    var data = {
		'opc_registration_username' : opc_registration_username, 
		'opc_registration_email':opc_registration_email, 
		'opc_registration_name':opc_registration_name, 
		'opc_registration_company':opc_registration_company, 
		'opc_registration_newsletter':opc_registration_newsletter.checked}; 
		
		registerMeRupostel(data); 
		
	    
	 }
	 
	 closeR();  
	 
	 return false; 
	 
  }
  
  function closeR2()
  {
     var d = document.getElementById('do_not_show_opcregistration'); 
	 if (d != null) 
	  d.value = 1; 
	 
	 var d = document.getElementById('rupostel_email'); 
	 if (d != null) 
	  if (d.value == '')
	   d.value = 'anonymous@anonymous'; 
	 
	 return closeR(); 
  }
  function opc_validate(el)
  {
    if (typeof el.checkValidity != 'undefined')
	{
 	  var r = el.checkValidity(); 
	}
    return true; 
  }
  function success_registration(data)
  { 
    console.log(data); 
	
	
	
	if (typeof data != 'undefined')
	if (data != null)
	{
	 var prefix = 0; 
	 var suffix = ''; 
	 var da = data.split('_'); 
	 if (da.length > 1)
	  {
	     if (!isNaN(da[0]))
		  {
		    prefix = da[0]; 
		  }
		 if (!isNaN(parseInt(da[1])))
		 {
		   suffix = da[1]; 
		 }
	  }
	 var opc_hash = data; 
	 
	 var d = document.getElementById('opc_registration_hash'); 
	 if (d != null)
	 d.value = prefix+'_'+suffix; 
	 
	 
	 var d = document.getElementById('do_not_show_opcregistration'); 
	 if (d != null) 
	 d.value = 1; 
	 
	 var d = document.getElementById('adminForm'); 
	 if (d != null) d.submit(); 
	 
	}
	return false; 
  }
  
  
  function closeR()
   {
      var re = document.getElementById('opc_registration_email'); 
	  var re2 = document.getElementById('rupostel_email'); 
	  
	  if (re.value != '')
	  re2.value = re.value; 
	  
	  var re = document.getElementById('opc_registration_username'); 
	  var re2 = document.getElementById('opc_registration_username_config'); 
	  if (re.value != '')
	  re2.value = re.value; 
	  
	  var re = document.getElementById('opc_registration_name'); 
	  var re2 = document.getElementById('opc_registration_name_config'); 
	  if (re.value != '')
	  re2.value = re.value; 
	  
	  //opc_registration_company
	  
	  var re = document.getElementById('opc_registration_company'); 
	  var re2 = document.getElementById('opc_registration_company_config'); 
	  
	  if (re.value != '')
	  re2.value = re.value; 
	  
	  
      var d = document.getElementById('opc_config_wrapper'); 
	  if (d != null)
	   {
	      d.style.display = 'block'; 
	   }
	   var d = document.getElementById('opc_registration_tab'); 
	   if (d != null) 
	    d.style.display = 'none'; 
	   return true; 
   }
</script>