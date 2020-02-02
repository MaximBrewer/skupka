<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
echo $this->loadTemplate('header'); 

$default_config = array('vm_lang'=>0, 'vm_menu_en_gb'=>0, 'selected_menu'=>0, 'menu_'=>0, 'tojlanguage'=>'*'); 
$session = JFactory::getSession(); 
$config = $session->get('opc_utils', $default_config); 
include(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php");

if (!version_compare(JVERSION,'2.5.0','ge'))
{
  $j15 = true; 
  
}

$pane = OPCPane::getInstance('tabs', array('startOffset'=>0, 'active'=>'paneladw'), 'toptabs');

if (!empty($j15)) echo '<div>'.JText::_('COM_ONEPAGE_ONLY_J25').'</div>'; 
if (!empty($j15)) echo '<div style="display: none;">'; 


?>
<div id="debug_window">&nbsp;</div>
<form action="index.php" id="adminForm" method="post" class="form-horizontal">
<?php

echo $pane->startPane('pane');
echo $pane->startPanel(JText::_('COM_ONEPAGE_TRACKING_PANEL'), 'paneladw');


?>
		<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_TRACKING_GENERAL'); ?></legend><?php OPCVideoHelp::show('COM_ONEPAGE_TRACKING_GENERAL'); ?>
        <p><?php //echo JText::_('COM_ONEPAGE_TRACKING_DESC'); 
		?></p>
        <table class="admintable" id="comeshere" style="width: 100%;">
	    <tr>
	    <td >
		 <input value="1" id="adwords_enabled_0" type="checkbox" name="adwords_enabled_0" <?php if (!empty($this->isEnabled)) echo 'checked="checked" '; ?>/>
	     
	    </td>
	    <td>
	   <label for="adwords_enabled_0"><span style="color: <?php if (!empty($this->isEnabled)) echo 'green'; else echo 'red'; ?>;"><?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_ENABLE'); ?></span></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
		<tr style="display: none;">
	    <td >
		 <input value="1" id="aba_enabled" type="checkbox" name="aba_enabled" <?php if (!empty($this->aba_enabled)) echo 'checked="checked" '; ?>/>
	     
	    </td>
	    <td>
	   <label for="aba_enabled"><span ><?php echo JText::_('COM_ONEPAGE_BANDONED_CARTS_ENABLED'); ?></span></label> 
	    </td>
		<td>
		</td>
		</tr>
		
	
		
		
		  <tr>
	    <td >
		 <input id="tracking_order" type="text" name="tracking_order" value="<?php 
		 if (!empty($this->tracking_order)) echo $this->tracking_order;
		else echo '9999'; ?>" />
	     
	    </td>
	    <td>
	   <label for="adwords_enabled_0"><?php echo JText::_('COM_ONEPAGE_TRACKING_ORDER'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
	    <tr style="display: none;">
	    <td class="key">
	     <label for="adwords_name_0"><?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_NAME'); ?></label> 
	    </td>
	    <td>
	    <input id="adwords_name_0" type="text" name="adwords_timeout" value="<?php if (!empty($adwords_timeout)) echo (int)$adwords_timeout; else echo "4000"; ?>"/>
	    </td><td><?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_NAME_DESC'); ?></td>
		</tr>

	    <tr style="display: none;">
	    <td class="key">
	     <label for="adwords_amount_0"><?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_AMOUNT'); ?></label> 
	    </td>
	    <td>
	    <input id="adwords_amount_0" type="text" name="adwords_amount_0" value="<?php if (!empty($adwords_amount[0])) echo $adwords_amount[0]; ?>"/>
	    </td><td><?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_AMOUNT_NAME'); ?></td>
		</tr>
		
		
		<tr >
	   <?php 
	   $negative_statuses = OPCconfig::getValue('tracking_negative', 'negative_statuses', 0, array()); 

	   if (empty($negative_statuses))
	     {
		   $negative_statuses = array('X', 'R'); 
		 }
		 ?>
	    <td>
	     <select name="negative_statuses[]" multiple="multiple" style="width: 150px;" class="vm-chzn-select" >
		 <?php 
		  foreach ($this->statuses as $k=>$s)
		   {
		      echo '<option '; 
			  
			  if (!empty($negative_statuses))
		      if (in_array($s['order_status_code'], $negative_statuses)) echo ' selected="selected" '; 
		   
			  
			  echo ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }

		  
		  ?>
		 </select>

	    </td>
		 
		<td><?php echo JText::_('COM_ONEPAGE_TRACKING_NEGATIVE_STATUSES_DESC'); ?></td>
		</tr>
			<tr >
	    <td >
		<?php
		$advanced_tracking = OPCconfig::getValue('advanced_tracking', '', 0, false); 
		?>
		 <input value="1" id="advanced_tracking" type="checkbox" name="advanced_tracking" <?php if (!empty($advanced_tracking)) echo 'checked="checked" '; ?>/>
	     
	    </td>
	    <td>
	   <label for="advanced_tracking"><span ><?php echo JText::_('COM_ONEPAGE_ENABLE_ADVANCED_FEATURES'); ?></span><br /><?php echo JText::_('COM_ONEPAGE_ENABLE_ADVANCED_FEATURES_TRACKING_DESC'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
	    <tr>
	    <td colspan="2" >
		 </td>
	    
		</tr>
		
		</table>
</fieldset>
        <?php if (!empty($advanced_tracking)) { ?>
		<fieldset class="adminform"><legend>
		<?php  echo JText::_('COM_ONEPAGE_TRACKING_ENABLE_TRACKING_HERE'); ?>
		</legend>
		<?php
		//if (false) 
		{
		$pane2 = OPCPane::getInstance('tabs', array('startOffset'=>0, 'active'=>'pane2P'), 'tab2');
		echo $pane2->startPane('pane2a');
		
		jimport( 'joomla.filesystem.file' );
		foreach ($this->statuses as $k=>$s)
		{
		$label = JText::_($s['order_status_name']); 
		$adwords_code[$s['order_status_code']] = JFile::read(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."trackers".DIRECTORY_SEPARATOR."body.html"); 
		if (!empty($adwords_code[$s['order_status_code']]))
		$label .= '...'; 
		echo $pane2->startPanel($label, 'pane2'.$s['order_status_code']); 
		
		
		{
		echo '<label for=="">'.JText::_('COM_ONEPAGE_TRACKING_TRIGGER_WHEN').'</label>'; 
		echo '<select name="opc_tracking_only_when_'.$s['order_status_code'].'" id="opc_tracking_only_when'.$s['order_status_code'].'">'; 
		echo '<option value=""'; 
		if (empty($opc_tracking_only_when[$s['order_status_code']])) echo ' selected="selected" '; 
		echo ' >'.JText::_('COM_ONEPAGE_UTILS_ALL').'</option>'; 
		foreach ($this->statuses as $k2=>$s2)
		{
		     echo '<option '; 
		 
			//if ((!empty($opc_tracking_only_when[$s['order_status_code']])) && ($opc_tracking_only_when[$s2['order_status_code']] == $s2['order_status_code'])) echo ' selected="selected" '; 
			  if (!empty($this->config[$s['order_status_code']]->only_when))
			  {
			  if ($this->config[$s['order_status_code']]->only_when  == $s2['order_status_code'])
			  echo ' selected="selected" ';
			  }
			  echo ' value="'.$s2['order_status_code'].'">'.JText::_($s2['order_status_name']).'</option>'; 
		}
		echo '</select>'; 
		echo '<label>'.JText::_('COM_ONEPAGE_TRACKING_TRIGGER_WHEN_TO_THIS_STATUS').' <span style="color: red;">'.JText::_($s['order_status_name']).'</span></label>'; 
		echo '<br style="clear:both;"/>'; 
		
		echo '<br style="clear: both;"/>';
		?>
		<table>
		
		
		<?php
		//echo '<select name="opc_tracking_php_'.$s['order_status_code'].'" id="opc_tracking_php_'.$s['order_status_code'].'">'; 
		

		foreach ($this->trackingfiles as $k2=>$s2)
		{
		/*
					$default = new stdClass(); 
					$ic = OPCconfig::getValue('tracking_config', $s2, 0, $default); 
					if (empty($ic->enabled)) continue; 
         */
		    $enabled = $this->model->isPluginEnabled($s2, $this->config); 					
		     if (!$enabled) continue; 
			 
		     echo '<tr><td><input value="1" type="checkbox" name="'.$s2.'_'.$s['order_status_code'].'" id="id'.$s2.'_'.$s['order_status_code'].'" '; 
		    
		    
			  if (!empty($this->config[$s['order_status_code']]->$s2)) echo ' checked="checked" ';
			  
			  echo ' value="'.$s2.'" /></td><td><label for="id'.$s2.'_'.$s['order_status_code'].'">';
			  
			  // the name of the tracking:
			  echo $this->forms[$s2]['title'];
			  //echo $s2; 
			  echo '</label></td><td>';

	
			  echo '</td></tr>'; 
		}
		//echo '</select><br style="clear:both;"/>'; 
		
		
		?>
		</table>
		<?php
		if ($advanced_tracking) { ?>
	    <textarea placeholder="<?php echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_CODE'); ?>" id="adwords_code_<?php echo $s['order_status_code']; ?>" name="adwords_code_<?php echo $s['order_status_code']; ?>" cols="60" rows="20"><?php 
		if (!empty($this->config[$s['order_status_code']]->code))
		echo  $this->config[$s['order_status_code']]->code; 	
		?></textarea> <br style="clear:both;" />
		<?php 
		}
		}
		echo $pane2->endPanel();
		}
		echo $pane2->endPane();
		
		
		
				?>
				<br style="clear: both;"/>
		<?php 		//echo JText::_('COM_ONEPAGE_TRACKING_ADWORDS_CODE_NOTE'); 
		
	    	if (!empty($use_ssl))
				$op_securl = JURI::base(true).basename($_SERVER['PHP_SELF']);
			else
				$op_securl = JURI::base(true).basename($_SERVER['PHP_SELF']);
			
			$op_securl = str_replace('/administrator', '/', $op_securl); 
		?>
	        <?php //echo JText::sprintf('COM_ONEPAGE_TRACKING_ADWORDS_CODE_DESC', $op_securl,$op_securl  ); 
			}
			?>
		</fieldset>
	   
<?php
		} // advanced config (in 302 and before... 
?>


 

<?php

		echo $pane->endPanel(); 
		
		echo $pane->startPanel(JText::_('COM_ONEPAGE_OPC_EXTENSIONS_PANEL'), 'opcextpanel');
		?>
		<table class="adminlist">
		<thead>
			<tr>
				<th class="title">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<th class="title">
					<?php 
					
					
					echo JText::_('COM_ONEPAGE_OPC_EXTENSIONS_PANEL'); ?>
				</th>
			</tr>
		</thead>
		 <tbody>
		  <?php 
		  $i = -1; 
		  foreach ($this->trackingfiles as $s2 => $item) { 
		  $i++; 
		  ?>
		  	<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php 
					
					$enabled = false; 
					$file = $item; 
					
					
					$enabled = $this->model->isPluginEnabled($item, $this->config); 
					/*
					
					$default = new stdClass(); 
					$ic = OPCconfig::getValue('tracking_config', $item, 0, $default); 
					
					if (!empty($ic->enabled)) $enabled = true; 
					
					foreach ($this->config as $status=>$c)
					 {
					    if (!empty($c->$item)) 
						$enabled = true; 
					 }
					 */
					
					$html = JHtml::_('jgrid.published', $enabled, $i, $item.'.', true); 
					$html = str_replace('listItemTask', 'toggleOpcExt', $html); 
					$html = str_replace('javascript:void(0);', '#', $html); 
					echo $html; 
					?>
					<input type="hidden" id="plugin_<?php echo $item; ?>" name="plugin_<?php echo $item; ?>" value="<?php if ($enabled) echo 1; else echo 0; ?>" />
				</td>
				<td>
				  <?php   echo $this->forms[$item]['title']; ?>
				</td>
		    </tr>
		  <?php } ?>
		 </tbody>
		</table>
		<script>
		 function toggleOpcExt(num, data)
		  {
		     var a = data.split('.'); 
			 var d = document.getElementById('plugin_'+a[0]); 
			 if (d != null)
			  {
			    if (a[1] == 'unpublish')
				{
			     d.value = '0'; 
				 document.getElementById('plugin_'+a[0]).value='0'; 
				}
				else 
				{
				
				d.value = 1; 
				}
				
				return Joomla.submitbutton('apply');
				adminForm.submit(); 
				return false; 
			  }
			  return false; 
		  }
		</script>
		<?php
		
		echo $pane->endPanel(); 
		
		echo $pane->startPanel(JText::_('COM_ONEPAGE_OPC_ORDER_VARS'), 'vars2a'); 
		?>
		<table>
		<tr><th><?php echo JText::_('COM_ONEPAGE_OPC_ORDER_VARS_TITLE'); ?></th>
		<th><?php echo JText::_('COM_ONEPAGE_OPC_OBJECT_NAME'); ?></th>
		<th><?php echo JText::_('COM_ONEPAGE_OPC_ORDER_VARS_TITLE_VALUE'); ?></th>
		
		</tr>
		<?php foreach ($this->orderVars as $key=>$val) { ?>
		<tr><td><?php echo '{'.$key.'}'; ?>
		</td>
		<td><?php if (!empty($this->named[$key])) echo '$this->'.substr($this->named[$key], 1); ?>&nbsp; </td>
		<td> <?php 
		if (is_array($val)) print_r($val); 
		elseif (is_string($val)) echo $val; 
		/*
		if (!is_array($val))
		echo $val; 
		else print_r($val); 
		*/
		?>
		</td></tr>
		<?php } ?>
		</table>
		<?php
		echo $pane->endPanel(); 
		
		
		 
		foreach ($this->trackingfiles as $k3=>$s3)
		{
		if (!$this->model->isPluginEnabled($s3, $this->config)) continue; 
		
	
		
		
		if (!empty($this->forms[$s3]))
		{
		echo $pane->startPanel($this->forms[$s3]['title'], 'adw'.$s3);
		
		
		$configX = new stdClass(); 
	    $prevConfig = OPCconfig::getValue('tracking_config', $s3, 0, $configX); 	
		if (!empty($prevConfig->advanced)) {
		  $prevConfig->advanced = (array)$prevConfig->advanced; 
		  if (!empty($prevConfig->advanced)) {
		     echo '<b style="color:red;">'.JText::_('COM_ONEPAGE_TRACKING_NOTEADV').'</b>'; 
		  }
		}
		
		
		if (!empty($this->forms[$s3]['description']))
		{
		echo '<fieldset class="adminform">'; 
		echo '<legend>'.JText::_('JGLOBAL_DESCRIPTION').'</legend>';
		echo '<p>'.$this->forms[$s3]['description'].'</p>';
		echo '</fieldset>'; 
		}
		
		
		
		
				
			   if (empty($advanced_tracking)) { 
	   ?>
	   <fieldset class="adminForm">
	    <legend><?php echo JText::_('COM_ONEPAGE_TRACKING_ENABLE_TRACKING_HERE'); ?></legend>
	   <table>
	   <tr>
	   <td><?php echo JText::_('COM_ONEPAGE_TRACKING_TRIGGER'); ?></td>
	   <td><select name="<?php echo $s3.'_order_status_code[]'; ?>" multiple="multiple" style="width: 150px;" class="vm-chzn-select" >
	   <?php foreach ($this->statuses as $k=>$s)
		{
		$label = JText::_($s['order_status_name']); 
		?><option value="<?php echo $s['order_status_code']; ?>" <?php 
		
		
		
		if (!empty($this->config[$s['order_status_code']]->$s3)) echo ' selected="selected" ';
		?>/><?php echo $label; ?></option>
		<?php
		}
		?>
	   </select>
	   </td>
	   </tr>
	   </table>

	   </fieldset>
	   
	   <?php
	   }
		
		
		echo '<fieldset class="adminform">'; 
		//echo '<legend>'.$this->forms[$s3]['title'].'</legend>'; 
		echo $this->forms[$s3]['params']; 
	    echo '</fieldset>'; 
	   
	
	   if (!empty($this->forms[$s3]['general'])) { 
		echo '<fieldset class="adminform">'; 
		//echo '<legend>'.JText::_('COM_ONEPAGE_GENERAL_PANEL').'</legend>'; 
		echo $this->forms[$s3]['general']; 
	    echo '</fieldset>'; 	 
        }
		
		
		if ((!empty($this->forms[$s3]['nm'])))
		{
				echo '<fieldset class="adminform"><legend>'.JText::_('COM_ONEPAGE_TRACKING_MULTILANGUAGEVARIABLES').'</legend>';  
				
				
			foreach ($this->forms[$s3]['nm'] as $key=>$val)
			{
				echo '<label for="'.$s3.'_'.$key.'">'.$key.'</label>'; 
				echo '<input type="text" value="'.$val.'" name="'.$s3.'['.$key.']" id="'.$s3.'_'.$key.'" placeholder="'.$key.'" />'; 
			}
			  echo '</fieldset>'; 	
		}
		
		echo $pane->endPanel();
		}
		}

		
				echo $pane->endPane(); 
				?>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="apply" id="task" />
<input type="hidden" name="view" value="tracking" />
</form>
<?php
if (!empty($j15)) echo '</div>'; 