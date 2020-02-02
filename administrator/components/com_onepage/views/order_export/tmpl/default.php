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

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
$version = ''; 
if (!defined('OPCVERSION'))
{
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'version.php'))
{
  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'version.php'); 
}
}
JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());
jimport ('joomla.html.html.bootstrap');

JHTMLOPC::stylesheet('order_export.css', 'administrator/components/com_onepage/assets/css/', false);
JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);


//JHtml::_('formbehavior.chosen', 'select');

if (OPCVERSION != '{OPCVERSION}')
$version = ' ('.OPCVERSION.')'; 

	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	JToolBarHelper::Title(JText::_('COM_ONEPAGE_CONFIGURATION_TITLE').$version , 'generic.png');

	JToolBarHelper::apply();

$document = JFactory::getDocument();
				$selectText = JText::_('COM_ONEPAGE_TAXES_DONOT_DELETE_GIFTS_STATUSES');
				$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('Select All')."',select_some_options_text: '".JText::_($selectText)."'" ;
				


	if (!class_exists('VmConfig'))
	    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	    VmConfig::loadConfig(); 
		if (method_exists('vmJsApi', 'js'))
		{
		$app = JFactory::getApplication(); 
		$jq = $app->get('jquery'); 
		if (empty($jq) && (!OPCJ3))
		{
		//vmJsApi::js('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.6.4','',TRUE);
		//vmJsApi::js ('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16', '', TRUE);
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php');
		
		OPCJavascript::loadJquery();  		
		
		}
		if (OPCJ3)
		 {
		   JHtml::_('jquery.framework');
		   JHtml::_('jquery.ui');
		   JHtml::_('formbehavior.chosen', 'select');
		 }
		 else
		 {
		vmJsApi::js('chosen.jquery.min');
		vmJsApi::css('chosen');
		 }
		$document->addScriptDeclaration ( '
//<![CDATA[
		var vm2string ={'.$vm2string.'} ;
		 jQuery( function($) {
			$(".vm-chzn-select").chosen({enable_select_all: true,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text});
		});
//]]>
				');
		
		
		}
		else
		{
		vmJsApi::jQuery(); 
		}

		$base = JURI::base(); 
$jbase = str_replace('/administrator', '', $base); 	
if (substr($jbase, -1) !== '/') $jbase .= '/'; 

if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noConflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noConflict.js');
else
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jquery.noconflict.js'))
$document->addScript($jbase.'components/com_virtuemart/assets/js/jquery.noconflict.js');
		
		
	$css = ' .chzn-container-multi .chzn-choices .search-field input {
	 height: 25px; 
	} 
	iframe {
	  width: 95%; 
	  height: 300px;
	  border: 1px solid #ddd; 
	}
	html { display; block !important; }
	'; 
	
	$document->addStyleDeclaration($css); 
	$docj = JFactory::getDocument();
	$url = JURI::base(true); 
	if (substr($url, strlen($url))!= '/') $url .= '/'; 
	$javascript =  "\n".' var op_ajaxurl = "'.$url.'"; '."\n";
	/*
    $javascript .= 'if(window.addEventListener){ // Mozilla, Netscape, Firefox' . "\n";
    $javascript .= '    window.addEventListener("load", function(){ op_runAjax(); }, false);' . "\n";
    $javascript .= '} else { // IE' . "\n";
    $javascript .= '    window.attachEvent("onload", function(){ op_runAjax(); });' . "\n";
    $javascript .= '}';
    */
	$docj = JFactory::getDocument();
	$docj->addScriptDeclaration( $javascript );	
	
	$c = VmConfig::get('coupons_enable', true); 
	VmConfig::set('coupons_enable', 10); 
	$test = VmConfig::get('coupons_enable'); 
	VmConfig::set('coupons_enable', $c); 
	if ($test != 10)
	 {
	   $is_admin =false; 
	 }
	 else $is_admin = true; 
	
      $session = JFactory::getSession();
      
        jimport('joomla.html.pane');
        jimport('joomla.utilities.utility');
	
    
		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(true); 

	



$document = JFactory::getDocument();
//$document->addScript('/administrator/includes/js/joomla.javascript.js');

    include(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php");
   	$document = JFactory::getDocument();



if (!empty($_SESSION['onepage_err'])) $msg = unserialize($_SESSION['onepage_err']).'<br />';
else $msg = ''; 

if (isset($_SESSION['onepage_err']))
{
    $msg = @unserialize($_SESSION['onepage_err']);
	if (!empty($msg))
	{
	    echo '<div style="width = 100%; border: 2px solid red;">';
	    echo $msg;
	    unset($_SESSION['onepage_err']);
	    echo '</div>';
	}
}	
	
?>
	<div id="vmMainPageOPC">
	
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ONEPAGE_EXPORT_NOTICE'); ?></legend>
		<p><?php echo JText::_('COM_ONEPAGE_EXPORT_DESC'); ?></p>
	<p><?php echo JText::_('COM_ONEPAGE_EXPORT_NOTE'); ?></p>
	<form action="index.php" method="post" name="adminForm" id="adminForm">

	<?php
	
	
	
 
         $ehelper = $this->ehelper; 
		 $files = $ehelper->getExportTemplates('ALL');
		 if (!empty($files))
		 {
			 
			 $first = reset($files); 
			 
			 
		  $pane2 = OPCPane::getInstance('sliders', array('active'=>'p2anelexp'.$first['tid'], 'startOffset'=>0));
		  echo $pane2->startPane('tabse333');
		 }
		 
		 
       
        ?>
        
		
		 <?php
		 

		
		 foreach($files as $f)
		 {
		 $name = '';
		 if (!empty($f['tid_name'])) $name = $f['tid_name'].' - '.$f['file'];  
		 else $name = $f['file'];
		 
		 echo $pane2->startPanel($name, 'p2anelexp'.$f['tid']);
		 
		 ?>
		 <div class="wrapper">
		 <div class="container-fluid">
		  <fieldset><legend><?php if (!empty($f['tid_name'])) echo $f['tid_name'].' - ';  echo $f['file'] ?></legend>
		  
<table class="adminTable">
		  <tr>
		  <td>
		  <label for="tid_name_<?php echo $f['tid'] ?>" ><?php if (!isset($f['tid_name'])) $f['tid_name'] = $f['file']; ?>
		  
		  
			<?php echo JText::_('COM_ONEPAGE_EXPORT_TEMPLATE_NAME'); ?>
			</label>
		  
		  </td>
		  <td>
		  <input type="text" size="20" <?php if (isset($f['tid_name'])) 
		  { echo ' value="'.$f['tid_name'].'"'; } else echo ' value=""'; ?> name="tid_name_<?php echo $f['tid'] ?>" class="form-control" id="tid_name_<?php echo $f['tid'] ?>" />&nbsp;
		  </td>
		  <td>
		   <a href="<?php echo $ehelper->getTemplateLink($f['tid']); ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_DOWNLOAD'); ?></a>
		  </td>
		  </tr>
		  
		  <tr>
		  <td>
		  
		  
		  
		  
		  
		  
		  
		  <label for="uploadedupdatefile_<?php echo $f['tid']; ?>">
   		  <?php echo JText::_('COM_ONEPAGE_EXPORT_TEMPLATE_UPDATE'); ?></label>
		  </td>
		  <td>
		 
		  <input class="form-control-upload" id="uploadedupdatefile_<?php echo $f['tid']; ?>" name="uploadedupdatefile_<?php echo $f['tid']; ?>" type="file" />
		   </td>
		   <td>
		  <input type="button" value="Upload File" onclick="javascript: submitbuttonCustom('template_update_upload');" />
		 </td>
		 </tr>
		
		
		  
		  
		  <tr>
		  <td class="key">
		  <input value="1" type="checkbox" <?php if (isset($f['tid_enabled']) && $f['tid_enabled']=='1') echo 'checked="checked" '; ?> name="tid_enabled_<?php echo $f['tid'] ?>" id="tid_enabled_<?php echo $f['tid'] ?>"  />
		  </td>
		  <td>
		  <label for="tid_enabled_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_TEMPLATE_ENABLED'); ?></div>
		</td>
		</tr>
		<tr>
		<td>
		  <input value="1" type="checkbox" <?php if (isset($f['tid_special']) && $f['tid_special']=='1') echo 'checked="checked" '; ?> name="tid_special_<?php echo $f['tid'] ?>" id="Itid_special_<?php echo $f['tid'] ?>" />
		  </td>
		  <td>
		  
		  <label for="Itid_special_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_HAS_MANUAL_ENTRY'); ?></label>
		  
		  </td>
		  <td>
		  <table>
		   <tr>
		    <td>
		  <label for="tid_specials_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_HOW_MANY'); ?></label>
		    </td>
			</tr>
			<tr>
			 <td>
		  <input type="text" name="tid_specials_<?php echo $f['tid'] ?>" id="tid_specials_<?php echo $f['tid'] ?>" <?php if (isset($f['tid_specials'])) 
		  { echo ' value="'.$f['tid_specials'].'"'; } else echo ' value="1"'; ?> />
		    </td>
		   </tr>
		   </table>
		   
		  </td>
		  </tr>
		  
		  <tr>
		  
		  <td>
		  <label for="tid_ai_<?php echo $f['tid'] ?>">
		  <?php echo JText::_('COM_ONEPAGE_EXPORT_AUTOINCREMENT2'); ?>
		  </label>
		  </td>
		  
		  <td >
		  
		  
		  <select name="tid_ai_<?php echo $f['tid'] ?>" id="tid_ai_<?php echo $f['tid'] ?>" onchange="javascript: return changeNumbering(this);">
			<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
			<?php 
			  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');   
			  
			  //if (OPCmini::tableExists('onepage_agendas'))
			  {
				  require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'numbering.php');
				  $JModelNumbering = new JModelNumbering; 
				  $agendas = $JModelNumbering->getAgendas(); 
				  
				  if (!isset($f['tid_ai'])) $f['tid_ai'] = 2; 
				  
				  if (!empty($agendas))
				  foreach ($agendas as $k=>$row)
				  {
					  ?><option <?php 
					  if ((!empty($f['tid_ai'])) && ($f['tid_ai'] == $row['id'])) echo ' selected="selected" '; 
					  ?>value="<?php echo $row['id']; ?>" ><?php echo $row['name']; ?></option>
					  <?php
				  }
			  }
			?>
			
			<option value="new"><?php echo JText::_('COM_ONEPAGE_UTILS_NEW').'...'; ?></option>
		  </select>
		  
		  
		  
		  
		  	<script>
	 function changeNumbering(el)
	 {
		 if (el.options[el.selectedIndex].value == 'new')
			 window.location = 'index.php?option=com_onepage&view=numbering'; 
	 }
	</script>
		  
		  </td>
		  
		  
		  </tr>
		  
		  <tr>
		  <td>
		  <label id="tid_shared_<?php echo $f['tid']; ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_SHARED'); ?></label>
		  </td>
		  <td>
		  
		  <?php echo '<select name="tid_shared_'.$f['tid'].'"  id="tid_shared_'.$f['tid'].'">';
		   echo '<option value="" ';
		   if (empty($f['tid_shared'])) echo ' selected="selected" ';
		   echo '>'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option>';
		   foreach ($files as $ff)
		   {
		    if ($ff['tid']!=$f['tid'])
		    echo '<option value="'.$ff['tid'].'" ';
		    if (isset($f['tid_shared']) && ($f['tid_shared'] == $ff['tid'])) echo ' selected="selected" ';
		    echo '>'.$ff['tid_name'].'</option>';
		   }
		   echo '</select>';
		   ?>
		   
		   </td>
		   <td>

		  
		   </td>
		   </tr>
		   
		   <tr>
		   <td>
		  <input value="1" type="checkbox" <?php if (count($files)==1) echo ' disabled="disabled" '; if (isset($f['tid_foreign']) && $f['tid_foreign']=='1') echo 'checked="checked" '; ?> name="tid_foreign_<?php echo $f['tid'] ?>" id="tid_foreign_<?php echo $f['tid'] ?>"  />
		   </td>
		   <td>
		   
		  <label for="tid_foreign_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_FOREIGN_ENTRY'); ?> </label>
		  </td>
		  <td>
		  <table>
		  
		  <?php if (count($files)>1) 
		  {
		?>
		<tr><td>
		<label for="tid_foreigntemplate_<?php echo $f['tid']; ?>"><?php  echo JText::_('COM_ONEPAGE_EXPORT_FOREIGN_ENTRY_SELECT'); ?></label>
		   </td></tr>
		   <tr><td>
		   <?php
		   echo '<select name="tid_foreigntemplate_'.$f['tid'].'" id="tid_foreigntemplate_'.$f['tid'].'"><option value="">'.JText::_('COM_ONEPAGE_NOT_CONFIGURED').'</option>';
		   foreach ($files as $ff)
		   {
		    if ($ff['tid']!=$f['tid'])
		    echo '<option value="'.$ff['tid'].'" ';
		    if (isset($f['tid_foreigntemplate']) && ($f['tid_foreigntemplate'] == $ff['tid'])) echo ' selected="selected" ';
		    echo '>'.$ff['tid_name'].'</option>';
		   }
		   echo '</select>';
		   } 
		   
		   ?>
		   </td></tr>
		   </table>
		   
		  </td>
		  </tr>
		  <tr>
		   <td>
		  <input value="1" type="checkbox" <?php if (isset($f['tid_email']) && $f['tid_email']=='1') echo 'checked="checked" '; ?> name="tid_email_<?php echo $f['tid'] ?>" id="tid_email_<?php echo $f['tid'] ?>"  />
		  </td>
		  <td>
		  <label for="tid_email_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_EMAIL'); ?></label>
		 </td>
		 </tr>
		 
		 <tr>
		 <td>
		  <input value="1" type="checkbox" <?php if (isset($f['tid_autocreate']) && $f['tid_autocreate']=='1') echo 'checked="checked" '; ?> name="tid_autocreate_<?php echo $f['tid'] ?>" id="tid_autocreate_<?php echo $f['tid'] ?>"  />
		  </td>
		  <td>
		  
		  <label for="tid_autocreate_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_AUTOCREATE'); ?></label>
		  </td>
		  <td>
		  <select multiple="multiple" id="tid_autocreatestatus_<?php echo $f['tid'] ?>" name="tid_autocreatestatus_<?php echo $f['tid'] ?>[]">
		<?php
		  if (!empty($this->statuses))
		  foreach ($this->statuses as $s)
		  {
		    //if ($s['order_status_code'] == $f['tid_autocreatestatus']) 
			if (isset($f['tid_autocreatestatus']) && (in_array($s['order_status_code'], $f['tid_autocreatestatus'])))
			$ch = ' selected="selected" ';
		    else $ch = '';
		    echo '<option value="'.$s['order_status_code'].'" '.$ch.'>'.JText::_($s['order_status_name']).'</option>';
		  }
		  ?>
		  </select> 
		  </td>
		  
		  </tr>
		  
		  <tr>
		    <td>
		  <input value="1" type="checkbox" <?php if (isset($f['tid_num']) && $f['tid_num']=='1') echo 'checked="checked" '; ?> name="tid_num_<?php echo $f['tid'] ?>" id="tid_num_<?php echo $f['tid'] ?>"  />
		  </td>
		  <td>
		  <label for="tid_num_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_NUM'); ?></label>
		  </td>
		  </tr>
		  
		  <tr>
		   <td>
		
		   </td>
		   <td>
		  <label for="tid_nummax_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_NUMMAX'); ?></label>
		   </td>
		   <td>
		     <input type="text" value="<?php if (!empty($f['tid_nummax'])) echo $f['tid_nummax']; ?>" size="10" name="tid_nummax_<?php echo $f['tid'] ?>" id="tid_nummax_<?php echo $f['tid'] ?>"  />
			</td>
		   
		  </tr>
		  
		  <tr>
		   <td>
		  
		   </td>
		   <td>
		  <label for="tid_itemmax_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_ITEMMAX'); ?></label>
		   </td>
		   <td>
		     <input type="text" value="<?php if (!empty($f['tid_itemmax'])) echo $f['tid_itemmax']; ?>" size="10" name="tid_itemmax_<?php echo $f['tid'] ?>" id="tid_itemmax_<?php echo $f['tid'] ?>" />
		   </td>
		  </tr>
		  
		  <tr>
		    <td>
		  <input value="1" type="checkbox" <?php if (isset($f['tid_back']) && $f['tid_back']=='1') echo 'checked="checked" '; ?> name="tid_back_<?php echo $f['tid'] ?>" id="tid_back_<?php echo $f['tid'] ?>"  />
		   </td>
		   <td>
		  <label for="tid_back_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_BACK'); ?></label>
		   </td>
		  </tr>
		  
		  <tr>
		   <td>
		  <input value="1" type="checkbox" <?php if (isset($f['tid_forward']) && $f['tid_forward']=='1') echo 'checked="checked" '; ?> name="tid_forward_<?php echo $f['tid'] ?>" id="tid_forward_<?php echo $f['tid'] ?>"  />
		   </td>
		   <td>
		  <label for="tid_forward_<?php echo $f['tid'] ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_FORWARD'); ?></label>
		   </td>
		  </tr>
		  
		  <tr>
		  <td></td>
		   <td>
		  <label for="tid_type_<?php echo $f['tid']; ?>"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_TYPE'); ?> </label>
		   </td>
		   <td>
		  <select name="tid_type_<?php echo $f['tid']; ?>"  id="tid_type_<?php echo $f['tid']; ?>">
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDER_DATA')) echo ' selected="selected" '; ?> value="ORDER_DATA"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_SINGLE_OFFICE'); ?></option>
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDER_DATA_TXT')) echo ' selected="selected" '; ?>value="ORDER_DATA_TXT"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_SINGLE_LOCAL'); ?></option>
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDERS')) echo ' selected="selected" '; ?>value="ORDERS"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_MULTIPLE_OFFICE'); ?></option>
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDERS_TXT')) echo ' selected="selected" '; ?>value="ORDERS_TXT"><?php echo JText::_('COM_ONEPAGE_EXPORT_TID_MULTIPLE_LOCAL'); ?></option>
		  </select>
		  
		  </td>
		  </tr>
		  
		  <tr>
		   <td></td>
		   <td colspan="3">
		   <table>
		   <tr>
		     <td>
		  <b><?php echo JText::_('COM_ONEPAGE_EXPORT_EMAIL_CONF'); ?></b>
		   </td>
		   </tr>
		   
		   <tr>
		   
		   <td>
		  <?php echo JText::_('COM_ONEPAGE_EXPORT_EMAIL_SUBJ'); ?>
		  </td>
		  
		  <td>
		  <input type="text" value="<?php if (!empty($f['tid_emailsubject'])) echo $f['tid_emailsubject']; ?>" size="100" name="tid_emailsubject_<?php echo $f['tid'] ?>" id="tid_emailsubject_<?php echo $f['tid'] ?>" />
		  </td>
		  </tr>
		  <tr>
		  <td>
		  <?php echo JText::_('COM_ONEPAGE_EXPORT_EMAIL_BODY'); ?>
		  </td>
		  <td>
		  <textarea cols="100" rows="7" name="tid_emailbody_<?php echo $f['tid'] ?>" id="tid_emailbody_<?php echo $f['tid'] ?>"><?php
	 	   if (!empty($f['tid_emailbody'])) echo $f['tid_emailbody']; ?></textarea>
		   </td>
		   </tr>
		   </table>
		   </td>
		   </tr>
		   </table>
		  </fieldset>
		 </div>
		</div>
		  <?php


if (!empty($this->jforms[$f['tid_xml']]['params'])) echo $this->jforms[$f['tid_xml']]['params'];
		  
		  echo $pane2->endPanel();
		 }
		 
		 if (!empty($files))
		 {
			 
		  echo $pane2->startPanel(JText::_('COM_ONEPAGE_EXPORT_GENERAL'), 'generale');
		 }
		 ?>
		 <fieldset><legend><?php echo JText::_('COM_ONEPAGE_EXPORT_GENERAL_UPLOAD'); ?></legend></fieldset>
		 <table class="admintable admintable table table-striped">
		  <tr>
		  
		   <td>
	   <label for="uploadfilename"><?php echo JText::_('COM_ONEPAGE_EXPORT_GENERAL_UPLOAD'); ?></label> 
	    </td>
		  
		   <td>
		
		  <input id="uploadfilename" name="uploadedfile" type="file" />
		</td>
		
		
		
		</tr>
		
		<tr>
		 <td>
			<label for="uploadbutton"><?php echo JText::_('COM_ONEPAGE_EXPORT_GENERAL_BTN'); ?></label> 
	    </td>
		<td>
  		 <input id="uploadbutton" type="button" name="upload_btn" value="<?php echo JText::_('COM_ONEPAGE_EXPORT_GENERAL_BTN'); ?>" onclick="javascript: submitbuttonCustom('template_upload');" />
		</td>
		</tr>
		
		<tr>
		 <td>
			<label for="api_url"><?php echo JText::_('COM_ONEPAGE_EXPORT_API_URL'); ?></label> 
	    </td>
		<td>
  		 <input id="api_url" name="api_url" type="text" value="<?php echo $this->api_url; ?>" />
		</td>
		</tr>
		
		<tr>
		 <td>
			<label for="api_username"><?php echo JText::_('COM_ONEPAGE_EXPORT_API_USERNAME'); ?></label> 
	    </td>
		<td>
  		 <input id="api_username" name="api_username" type="text" value="<?php echo $this->api_username; ?>" />
		</td>
		</tr>
		
		<tr>
		 <td>
			<label for="api_password"><?php echo JText::_('COM_ONEPAGE_EXPORT_API_PASSWORD'); ?></label> 
	    </td>
		<td>
  		 <input id="api_password" name="api_password" type="text" value="<?php echo $this->api_password; ?>" />
		</td>
		</tr>
		
		
		<tr>
		 <td>
			<label for="xml_debug"><?php echo JText::_('COM_ONEPAGE_EXPORT_DEBUG_XML'); ?></label> 
	    </td>
		<td>
  		 <input id="xml_debug" name="xml_debug" type="checkbox" value="1"<?php if (!empty($this->xml_debug)) echo ' checked="checked" '; ?> />
		</td>
		</tr>
		
		
		<tr>
		 <td>
			<label for="export_dir"><?php echo JText::_('COM_ONEPAGE_EXPORT_DIR'); ?></label> 
	    </td>
		<td>
  		 <input id="export_dir" name="export_dir" type="text" value="<?php echo $this->export_dir; ?>" />
		</td>
		</tr>
		
		
		<tr>
		 <td colspan="2">
		<?php
		if (!empty($files)) {
		echo '<br />
		 <a href="?option=com_onepage&amp;view=order_export&amp;showvars=1" target="_blank" title="Show template variables">'.JText::_('COM_ONEPAGE_EXPORT_GENERAL_SHOW').'</a>';
		 $showvars = JRequest::getVar('showvars', '');
		 if (!empty($showvars))
		 {
		  echo JText::_('COM_ONEPAGE_EXPORT_GENERAL_AVAIABLE_TEMP').'<br /><textarea cols="40" rows="5">';
		 
 		 $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
		 echo '
		 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr" id="minwidth" >
		<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		</head>
		<body>';
		
		 
		 
		    if (empty($f['tid'])) $f['tid'] = 0; 
			
			$order_id = JRequest::getVar('order_id', ''); 
			
		    $data = $ehelper->getOrderDataEx($f['tid'], $order_id); 
		    foreach ($data as $k=>$v)
		    {
			
				
		     if (!empty($v) || ($v === '0'))
		     echo '{'.$k."}".$v."<br />\n";
		    }
			
		   echo '</body></html>';
		    die();
		   
		   echo '</textarea>';
		 }
		 } 
		 
		 ?>
		  </td>
		  </tr>
		 </table>
		
		 <?php
		 
		if (!empty($files))
		 {
		  echo $pane2->endPanel();
		 }
		 
		 if (!empty($files)) {
		  echo $pane2->endPane();
		 }
		

		?>
		<input type="hidden" name="task" id="task" value="save" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="order_export" />
		<input type="hidden" name="controller" value="order_export" />
		
  </form>
   </fieldset>
</div>
<?php



 
