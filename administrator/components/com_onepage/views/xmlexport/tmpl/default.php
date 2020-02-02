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
$document = JFactory::getDocument();
JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
$document->setTitle(JText::_('COM_ONEPAGE_TRACKING_PANEL')); 


JHTML::_('behavior.tooltip');
jimport ('joomla.html.html.bootstrap');
if (!OPCJ3)
{
JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());

$css = '
#vmMainPageOPC dl#pane {
 margin-bottom: 0; 
} '; 
$document = JFactory::getDocument(); 
$document->addStyleDeclaration($css); 

}

 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php');
		
		OPCJavascript::loadJquery();  



?>
<script type="text/javascript">
    window.addEvent('domready', function(){ 
       var JTooltips = new Tips($$('.hasTip'), 
       { maxTitleChars: 50, fixed: false}); 
    });
</script>
<?php
$default_config = array('vm_lang'=>0, 'vm_menu_en_gb'=>0, 'selected_menu'=>0, 'menu_'=>0, 'tojlanguage'=>'*'); 
$session = JFactory::getSession(); 
$config = $session->get('opc_utils', $default_config); 
include(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php");

JToolBarHelper::Title(JText::_('COM_ONEPAGE_XML_EXPORT') , 'generic.png');
//	JToolBarHelper::install();
JToolBarHelper::apply();

if (!version_compare(JVERSION,'2.5.0','ge'))
{
  $j15 = true; 
  
}
jimport('joomla.html.pane');
jimport('joomla.utilities.utility');
$pane = OPCPane::getInstance('tabs', array('startOffset'=>0, 'active'=>'paneladw'), 'toptabs');

if (!empty($j15)) echo '<div>'.JText::_('COM_ONEPAGE_ONLY_J25').'</div>'; 
if (!empty($j15)) echo '<div style="display: none;">'; 


?>
<div id="vmMainPageOPC">

<div id="opc_status">&nbsp;</div>
<form action="index.php" id="adminForm" method="post">
<?php

echo $pane->startPane('pane');
echo $pane->startPanel(JText::_('COM_ONEPAGE_GENERAL_PANEL'), 'paneladw');


?>
		<fieldset class="adminform">
        <legend><?php echo JText::_('COM_ONEPAGE_XML_EXPORT'); ?></legend>
		
        <p><input class="btn btn-success" type="button" onclick="return op_runExport(<?php
		
	
  if ($this->xml_export_num >= $this->numprods)
   {
      echo '1'; 
   }
   else echo '0'; 

		?>, batch);" value="<?php echo JText::_('JACTION_CREATE'); ?>" /> <?php //echo JText::_('COM_ONEPAGE_TRACKING_DESC'); 
		?></p><br />
		
        <table class="admintable admintable table table-striped" id="comeshere" style="width: 100%;">
	    <tr>
	    <td >
		 <input id="xml_general_enable" type="checkbox" name="xml_general_enable" <?php if (!empty($this->isEnabled)) echo 'checked="checked" '; ?> value="1" />
	     
	    </td>
	    <td>
	   <label for="xml_general_enable"><?php echo JText::_('COM_ONEPAGE_EML_EXPORT_ENABLED'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
		
		<tr>
	    <td >
		 <input id="xml_export_path" style="width: 90%;" type="text" name="xml_export_path" value="<?php if (!empty($this->xml_export_path)) echo $this->xml_export_path; ?>" />
	     
	    </td>
	    <td>
	   <label for="xml_export_path"><?php echo JText::_('COM_ONEPAGE_EML_EXPORT_PATH'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
		<tr>
	    <td >
		 <input id="xml_live_site" style="width: 90%;" type="text" name="xml_live_site" value="<?php if (!empty($this->xml_live_site)) echo $this->xml_live_site; ?>" />
	     
	    </td>
	    <td>
	   <label for="xml_live_site"><?php echo JText::_('COM_ONEPAGE_EML_EXPORT_LIVESITEURL'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
		<tr>
	    <td >
		 <input id="xml_export_num" style="width: 90%;" type="text" name="xml_export_num" value="<?php if (!empty($this->xml_export_num)) echo $this->xml_export_num; else echo "100000" ?>" />
	     
	    </td>
	    <td>
	   <label for="xml_export_num"><?php echo JText::_('COM_ONEPAGE_XML_EXPORT_NUMBER'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
		<tr>
	    <td >
		 <input id="xml_export_customs" type="checkbox" name="xml_export_customs" value="1" <?php if (!empty($this->xml_export_customs)) echo ' checked="checked" '; ?> />
	     
	    </td>
	    <td>
	   <label for="xml_export_customs"><?php echo JText::_('COM_ONEPAGE_XML_EXPORT_LOADCUSTOMS'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
		<tr>
	    <td >
		 <input id="xml_export_test" type="text" name="xml_export_test" value="<?php if (!empty($this->xml_export_test)) echo $this->xml_export_test; ?>" />
	     
	    </td>
	    <td>
	   <label for="xml_export_test"><?php echo JText::_('COM_ONEPAGE_XMLEXPORT_TESTPRODUCTS'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
		<tr>
	    <td >
		 <input id="xml_disable_categorycache" type="checkbox" name="xml_disable_categorycache" <?php if (!empty($this->xml_disable_categorycache)) echo ' checked="checked" '; ?> value="1" />
	     
	    </td>
	    <td>
	   <label for="xml_disable_categorycache"><?php echo JText::_('COM_ONEPAGE_XMLEXPORT_DISABLECACHE'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		
			<tr>
	    <td >
		 <input id="xml_export_disable_compression" type="checkbox" name="xml_export_disable_compression" value="1" <?php if (!empty($this->xml_export_disable_compression)) echo ' checked="checked" '; ?> />
	     
	    </td>
	    <td>
	   <label for="xml_export_disable_compression"><?php echo JText::_('COM_ONEPAGE_XML_EXPORT_DISABLECOMPRESSION'); ?></label> 
	    </td>
		<td>
		</td>
		</tr>
		
		<tr><td colspan="4">
		 <fieldset><legend><?php echo JText::_('COM_ONEPAGE_XML_FEEDS'); ?></legend>
		  <table class="admintable admintable table table-striped" style="width: 100%;">
		

		
	 <?php
	 
	 foreach ($this->trackingfiles as $k2=>$s2)
		{
		    
			$enabled = $this->model->isPluginEnabled($s2, $this->forms);
			if (!$enabled) continue; 
			
		     echo '<tr><td><input type="hidden" name="'.$s2.'[enabled]" id="id'.$s2.'" '; 
		    
			
			
		      if ((is_object($this->forms[$s2]['config']) &&
			  (!empty($this->forms[$s2]['config']->enabled)))) echo ' value="1" ';
			  else echo ' value="0" '; 
			  
			  
			  echo '/>
			  
			  <label for="id'.$s2.'">'.$this->forms[$s2]['title'].' ('.$s2.'.php)</label>
			  </td>';
			  
			  
			 

	
			  
			  $file = $this->forms[$s2]['config']->xmlpath; 
			  if (stripos($file, JPATH_SITE)===false)
			   {
			     $file = JPATH_SITE.DIRECTORY_SEPARATOR.$file; 
			   }
			  if (file_exists($file)) 
			  {
			   $time = filectime($file); 
			   $msg = JText::_('JGLOBAL_CREATED'); 
			   $date =  date('l jS \of F Y h:i:s A', $time);
			   $msg = '<span style="color:green;">   ( '.$msg.' - '.$date.' ) </span></td><td>'; 
			   
			   $msg .= '<input type="button" class="btn btn-success ttt" onclick="return op_runExport('; 
		
	
  if ($this->xml_export_num >= $this->numprods)
   {
      $msg .= '1'; 
   }
   else $msg .= '0'; 

		$msg .=', batch, \''.$s2.'\');" value="'.JText::_('JACTION_CREATE').'" />'; 
			  
$msg .= '			   <br /><br />
			  <a class="btn btn-success" href="index.php?option=com_onepage&view=pairing&asset=virtuemart_category_id&entity='.$s2.'&type=xmlexport">'.JText::_('COM_ONEPAGE_CATEGORY_PAIRING').'...</a>'; 
			  
			  }
			  else
			  {
			    $msg = '<span style="color:red;">   ( '.JText::_('COM_ONEPAGE_EML_EXPORT_NOTCREATEYET').' ) </span></td><td><input type="button" class="btn btn-success" onclick="return op_runExport('; 
		
	
  if ($this->xml_export_num >= $this->numprods)
   {
      $msg .= '1'; 
   }
   else $msg .= '0'; 

		$msg .=', batch, \''.$s2.'\');" value="'.JText::_('JACTION_CREATE').'" />'; 
			  }
			  echo '<td>
			  <a href="'.$this->forms[$s2]['config']->xmlurl.'">'.$this->forms[$s2]['config']->xmlurl.'
			  </a><br />
			  <a href="'.$this->forms[$s2]['config']->xmlurl2.'">'.JText::_('COM_ONEPAGE_EXPORT_ONDEMAND_LINK').'
			  </a>
			 
			  
			  </td><td>'.$msg.'</td>'; 
			  
			  echo '</tr>'; 
		}
	 
	 ?>
	     </table>
		</fieldset>
		
	   
	   </td>
	  </tr>
	  
		<tr>
		  <td colspan="4">
		  <fieldset><legend><?php echo JText::_('COM_ONEPAGE_GENERAL_PANEL'); ?></legend>
		  <textarea style="width: 100%;" rows="6">
#<?php echo JTExt::_('COM_ONEPAGE_XML_EXPORT_CRONSETTINGS')."\n".'#'.JText::_('COM_ONEPAGE_XML_EXPORT_CRONSETTINGS_DOCUMENTROOT').' '.$_SERVER['DOCUMENT_ROOT']."\n".'#'.JText::_('COM_ONEPAGE_XML_EXPORT_CRONSETTINGS_WGET').': '.$this->xml_live_site.'index.php?option=com_onepage&view=xmlexport&nosef=1&tmpl=component&format=opchtml'."\n\n"; ?>
40 1 * * * nice -n 15 php <?php echo JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'export.php'; ?> > /dev/null 
#adnother way to run it from a bash shell file: 

php7 /srv/www/rupostel.com/web/vm2/purity/administrator/components/com_onepage/cli.php \
--task=xmlexport \
--myurl=https://vm2.rupostel.com/purity/ \
--return_status_json=0 \
--debug=1 \
--override_jroot=/srv/www/rupostel.com/web/vm2/purity 

#when using OPC CLI it's recommended to set myurl and overrride_jroot as the paths can be different in CLI due to symlinks, jailkit, mounts and else
</textarea></fieldset>
		  </td> 
		</tr>
		
		</table>
</fieldset>
<?php

?>


 

<?php
		echo $pane->endPanel(); 
		
	   echo $pane->startPanel(JText::_('COM_ONEPAGE_LOG'), 'panel1');
	   ?><div id="opc_response">&nbsp;</div><?php
		
		echo $pane->endPanel(); 

		
		if ((!empty($this->xml_export_customs)) && (!empty($this->customFields))) {
		echo $pane->startPanel(JText::_('COM_ONEPAGE_XMLEXPORT_CUSTOMFIELDS'), 'opccustoms');  
		?>
		<table class="adminlist admintable table table-striped">
		<thead>
		</thead>
		<tbody>
		<?php
		  foreach ($this->customFields as $k=>$custom)
		  {
			  ?><tr><?php
			    ?><td class="col-md-1 span1"><?php
					echo JText::_($custom['custom_title']); 
				?></td><?php
			  ?><td class="col-md-7 span2"><?php
					echo implode(',', $custom['values']); 
				?></td><?php

				$default = ''; 
				$ic = OPCconfig::getValue('tracking_config_pairing', 'custom_pairing', $custom['virtuemart_custom_id'], $default); 
				$selected = false; 
				?><td class="col-md-2 span2"><select name="custom_pair[<?php echo $custom['virtuemart_custom_id']; ?>]">
				<option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
				<option value="-1" <?php if ($ic === '-1') {
				echo ' selected="selected" '; $selected=true; 
				}
				?> ><?php echo JText::_('COM_ONEPAGE_XMLEXPORT_ATTRIBUTES_DONOTUSE'); ?></option>
				<?php

				foreach ($this->coreGroups as $type)
				{
					echo '<option '; 
					if ((!empty($ic)) && ($ic === $type)) 
					{
						echo ' selected="selected" '; 
						$selected = true; 
					}
					echo ' value="'.$type.'" >'.JText::_('COM_ONEPAGE_XMLEXPORT_ATTRIBUTES_'.strtoupper($type)).'</option>'; 
				}
				?>
				<option value="<?php 

				echo $ic; 
				
				?>" <?php 
				if (!empty($ic) && (!$selected)) echo ' selected="selected" '; 
				?> ><?php echo JText::_('COM_ONEPAGE_XMLEXPORT_ATTRIBUTES_OTHER'); ?></option>
				</select>
				</td>
				<td class="col-md-2 span2">
				<input type="text" value="<?php 
				if (!$selected)
				echo OPCconfig::getValue('tracking_config_pairing', 'custom_pairing', $custom['virtuemart_custom_id'], $default); ?>" name="custom_pair_other[<?php echo $custom['virtuemart_custom_id']; ?>]" placeholder="<?php echo JText::_('COM_ONEPAGE_XMLEXPORT_ATTRIBUTES_OTHER'); ?>" /><?php
				
					
				?></td><?php
			  
			  ?></tr><?php
		  }
		  ?>
		  </tbody>
		  </table>
		  <?php
		echo $pane->endPanel(); 
		}
		echo $pane->startPanel(JText::_('COM_ONEPAGE_OPC_EXTENSIONS_PANEL'), 'opcextpanel');
		?>
		<table class="adminlist admintable table table-striped">
		<thead>
			<tr>
				<th class="title">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<th class="title">
					<?php 
					
					
					echo JText::_('COM_ONEPAGE_OPC_EXTENSIONS_PANEL'); ?>
				</th>
				<th>
				 <?php echo JText::_('COM_ONEPAGE_CATEGORY_PAIRING').'...'; ?>
				</th>
				
				
			</tr>
		</thead>
		 <tbody>
		  <?php 
		  $i = -1; 
		  foreach ($this->trackingfiles as $s6 => $item) { 
		  $i++; 
		  ?>
		  	<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php 
					
					$enabled = false; 
					$file = $item; 
					
					
					$enabled = $this->model->isPluginEnabled($item, $this->forms); 
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
				<td>
				 <a class="btn btn-success" href="index.php?option=com_onepage&view=pairing&asset=virtuemart_category_id&entity=<?php echo $item; ?>&type=xmlexport"><?php echo JText::_('COM_ONEPAGE_CATEGORY_PAIRING').'...'; ?></a>
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
			  
			    var d2 = document.getElementById('id'+a[0]); 
				
			  
			    if (a[1] == 'unpublish')
				{
			     d.value = '0'; 
				 document.getElementById('plugin_'+a[0]).value='0'; 
				 
				 if (d2 != null)
				{
				  d2.value = '0'; 
				}

				 
				}
				else 
				{
				
				d.value = 1; 
				
				if (d2 != null)
				{
				  d2.value = '1'; 
				}

				
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
		
		 
		foreach ($this->trackingfiles as $k3=>$s3)
		{
		
		if (!empty($this->forms[$s3]))
		{
		
		$enabled = $this->model->isPluginEnabled($s3, $this->forms);
	    if (!$enabled) continue; 
		
		echo $pane->startPanel($this->forms[$s3]['title'], 'adw'.$s3);
		if (!empty($this->forms[$s3]['description']))
		{
		echo '<fieldset class="adminform">'; 
		echo '<legend>'.JText::_('JGLOBAL_DESCRIPTION').'</legend>';
		echo '<p>'.$this->forms[$s3]['description'].'</p>';
		echo '</fieldset>'; 
		}
		
		?>
		<fieldset><legend><?php echo JText::_('COM_ONEPAGE_CATEGORY_PAIRING'); ?></legend>
		<a class="btn btn-success" href="index.php?option=com_onepage&view=pairing&asset=virtuemart_category_id&entity=<?php echo $s3; ?>&type=xmlexport"><?php echo JText::_('COM_ONEPAGE_CATEGORY_PAIRING').'...'; ?></a>;
		</fieldset>
		<?php
		
		echo '<fieldset class="adminform">'; 
		echo '<legend>'.$this->forms[$s3]['title'].'</legend>'; 
		echo $this->forms[$s3]['params']; 
	    echo '</fieldset>'; 
	 
		echo '<fieldset class="adminform">'; 
		echo '<legend>'.JText::_('COM_ONEPAGE_XML_GENERAL').'</legend>'; 
		echo '<table width="100%" class="paramlist admintable admintable table table-striped" cellspacing="1">';
		
		
		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_ONEPAGE_EML_EXPORT_FILENAME').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<input type="text" name="'.$s3.'[xmlfile]" value="'.$this->forms[$s3]['config']->xmlfile.'" width="50%" />'; 
		echo '</td></tr>';
		
		
		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_ONEPAGE_XML_CAMPAIGN_NAME').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<input type="text" name="'.$s3.'[cname]" value="'.$this->forms[$s3]['config']->cname.'" width="50%" />'; 
		echo '</td></tr>';
?>
<tr>
	    <td width="40%" class="paramlist_key" >
		 <span for="<?php echo $s3; ?>xml_export_unique"><?php echo JText::_('COM_ONEPAGE_XML_UNIQUE_TYPE'); ?></span> 
	     
	    </td>
	    <td>
		
		<select id="<?php echo $s3; ?>xml_export_unique"  name="<?php echo $s3; ?>[xml_export_unique]" >
		   <option <?php if (empty($this->forms[$s3]['config']->xml_export_unique)) echo ' selected="selected" '; ?> value="0">virtuemart_product_id</option>
		   <option <?php if (!empty($this->forms[$s3]['config']->xml_export_unique)) echo ' selected="selected" '; ?>  value="1">SKU</option>
		 </select>
		
	   
	    </td>
		
		</tr>

<?php
       $multilang = array(); 
	   $multilang[] = ''; 
       if (isset($this->forms[$s3]['xml']))
	   if (isset($this->forms[$s3]['xml']->multilang))
	    {
		  
		  foreach ($this->forms[$s3]['xml']->multilang->children() as $child)
		    {
			  $multilang[] = (string)$child; 
			}
		}
		$c = 0; 
		
		
		
		foreach ($multilang as $lang)
		{
		
		if (count($multilang)>1)
		{
		echo '<tr style="background-color: #ddd;"><td></td>'; 
		
	
		{
		  if (empty($lang)) $langt = JText::_('COM_ONEPAGE_DEFAULT'); 
		  else $langt = $lang; 
		  echo '<td colspan="2"><span style="font-weight: bold;">'.JText::_('COM_ONEPAGE_XML_LANGUAGE').': '.$langt.'</span></td>'; 
		}
		echo '</tr>';
		}
		
		if (!empty($lang))
		$lang_suffix = '_'.$lang;
		else $lang_suffix = ''; 
		
		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAITEXT').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<input type="text" name="'.$s3.'[avaitext'.$lang_suffix.']" value="';
		
		$key = 'avaitext'.$lang_suffix; 
		if (!isset($this->forms[$s3]['config']->$key))
		echo '1 day'; 
		else
		{
		
		echo $this->forms[$s3]['config']->$key; 
		}
		echo '" width="50%" />'; 
		echo '</td>'; 
		
		echo '</tr>';

		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAINUM').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<input type="text" name="'.$s3.'[avaidays'.$lang_suffix.']" value="';
		
		$key = 'avaidays'.$lang_suffix;
		if (!isset($this->forms[$s3]['config']->$key))
		echo 1;
		else
		{
		 
		 echo $this->forms[$s3]['config']->$key; 
		}
		echo '" width="50%" />'; 
		echo '</td>'; 
		echo '<td>'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAINUM').':</td>'; 
		echo '</tr>';

		foreach ($this->avai as $key=>$img)
		{
		  
		  
		  echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAIIMGTOTEXT'). ' ('.$img->img.')</td>'; 
		  
		  echo '<td class="paramlist_value">'; 
		  echo '<input type="text" alt="'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAIIMGTOTEXT').'" placeholder="'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAIIMGTOTEXT').'" name="'.$s3.'['.$key.'txt'.$lang_suffix.']" value="';
		  
		  if (isset($this->forms[$s3]['config']->{$key.'txt'.$lang_suffix}))
		  echo $this->forms[$s3]['config']->{$key.'txt'.$lang_suffix}; 
		  else 
		  echo $img->deliverytext; 
		  
		  echo '" width="50%" />'; 
		  echo '</td>'; 
		  
		  echo '<td class="paramlist_value">'; 
		  echo '<input type="text" alt="'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAIIMGTODAYS').'" placeholder="'.JText::_('COM_ONEPAGE_XML_DEFAULT_AVAIIMGTODAYS').'" name="'.$s3.'['.$key.'days'.$lang_suffix.']" value="'; 
		  
		  if (isset($this->forms[$s3]['config']->{$key.'days'.$lang_suffix}))
		  echo $this->forms[$s3]['config']->{$key.'days'.$lang_suffix}; 
		  else 
		  echo $img->avai; 
		  
		  echo '" width="50%" />'; 
		  echo '</td>'; 
		  
		  echo '</tr>';
		}
		}
		
		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('JFIELD_LANGUAGE_LABEL').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<select name="'.$s3.'[language]" >'; 
		foreach ($this->langs as $lang)
		 {
		   echo '<option '; 
		   if (isset($this->forms[$s3]['config']->language))
		   if ($lang == $this->forms[$s3]['config']->language) echo ' selected="selected" '; 
		   echo ' value="'.$lang.'">'.$lang.'</option>'; 
		 }
		echo '</select>'; 
		echo '</td></tr>';
		
		
		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_VIRTUEMART_PAYMENTMETHOD_FORM_SHOPPER_GROUP').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<select name="'.$s3.'[shopper_group]" >'; 
		foreach ($this->shoppergroups as $lang)
		 {
		 
		   echo '<option '; 
		   if (isset($this->forms[$s3]['config']->shopper_group))
		   if ($lang['virtuemart_shoppergroup_id'] == $this->forms[$s3]['config']->shopper_group) echo ' selected="selected" '; 
		   echo ' value="'.$lang['virtuemart_shoppergroup_id'].'">'.JText::_($lang['shopper_group_name']).'</option>'; 
		 }
		echo '</select>'; 
		echo '</td></tr>';
		
		
		
		
		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_ONEPAGE_XML_PRODUCT_LINK_CONFIG').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<select name="'.$s3.'[url_type]" >'; 
		//OPC SEF: $opts = array(1, 2, 3, 4); 
		$opts = array(1, 2, 3); 
		foreach ($opts as $opt)
		 {
		 
		   echo '<option '; 
		   if (isset($this->forms[$s3]['config']->url_type))
		   if ($opt == $this->forms[$s3]['config']->url_type) echo ' selected="selected" '; 
		   echo ' value="'.$opt.'">'.JText::_('COM_ONEPAGE_XML_PRODUCT_LINK_CONFIG_OPT'.$opt).'</option>'; 
		 }
		echo '</select><br style="clear: both;"/><div>'.JText::_('COM_ONEPAGE_XML_PRODUCT_LINK_CONFIG_NOTE').'</div>'; 
		echo '</td></tr>';


		echo '<tr><td width="40%" class="paramlist_key">'.JText::_('COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING').'</td>'; 
		echo '<td class="paramlist_value">'; 
		echo '<select name="'.$s3.'[child_type]" >'; 
		$opts = array(1, 2, 3); 
		foreach ($opts as $opt)
		 {
		 
		   echo '<option '; 
		   if (isset($this->forms[$s3]['config']->child_type))
		   if ($opt == $this->forms[$s3]['config']->child_type) echo ' selected="selected" '; 
		   echo ' value="'.$opt.'">'.JText::_('COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING_OPT'.$opt).'</option>'; 
		 }
		echo '</select>'; 
		echo '</td></tr>';

		
		
		
		echo '</table>'; 
	    echo '</fieldset>'; 
	 
	 
	 if (!empty($this->forms[$s3]['general'])) { 
		echo '<fieldset class="adminform">'; 
		//echo '<legend>'.JText::_('COM_ONEPAGE_GENERAL_PANEL').'</legend>'; 
		echo $this->forms[$s3]['general']; 
	    echo '</fieldset>'; 	 
        }
	 

		echo $pane->endPanel();
		}
		}

		
				echo $pane->endPane(); 
				?>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="task" value="apply" id="task" />
<input type="hidden" name="view" value="xmlexport" />
<input type="hidden" name="delete_ht" id="delete_ht" value="0" />
</form>

<?php 
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) {

      $isHttps = true;
	}
	else $isHttps = false;
  if ($isHttps) $this->xml_live_site = str_replace('http:', 'https:', $this->xml_live_site); 
  if (!$isHttps) $this->xml_live_site = str_replace('https:', 'http:', $this->xml_live_site); 
?>
<script type="text/javascript">
  var nproducts = <?php echo $this->numprods; ?>; 
  var batch = <?php echo $this->xml_export_num; ?>; 
  var stepstxt = '<?php echo JText::_('COM_ONEPAGE_XML_EXPORT_STEPS'); ?>'; 
  var op_ajaxurl = '<?php echo $this->xml_live_site; ?>index.php?option=com_onepage&view=xmlexport&format=opchtml&tmpl=component';
  var checkurl = '<?php echo $this->xml_live_site; ?>export/compression_test/test.xml'; 
  op_checkHt(); 
</script>
</div>
<?php 


if (!empty($j15)) echo '</div>'; 
