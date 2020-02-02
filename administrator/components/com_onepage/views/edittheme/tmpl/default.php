<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->setTitle(JText::_('COM_ONEPAGE_OPC_THEME_EDITOR')); 
JToolBarHelper::Title(JText::_('COM_ONEPAGE_OPC_THEME_EDITOR') , 'generic.png');
JHTMLOPC::script('translation_helper.js', 'administrator/components/com_onepage/assets/');
JHTMLOPC::script('colorPicker.js', 'administrator/components/com_onepage/assets/colorPicker/');
JHTMLOPC::stylesheet('edittheme.css', 'administrator/components/com_onepage/assets/css/');
$javascript = ' var op_secureurl = "'.JURI::base().'index.php?option=com_onepage&view=edittheme&format=raw&tmpl=component&task=ajax"; var op_css = true; 
var changedColors = new Array(); 
var origColors = new Array(); 
';
foreach ($this->colors as $idcolor=>$color)
{
	$javascript .= ' origColors.push(\''.str_replace('#', '', $idcolor).'\');'; 
}

$document->addScriptDeclaration($javascript);
if (!empty($this->msgs))
echo '<div style="color: red; clear: both;">'.$this->msgs.'</div>'; 
?>
<input type="hidden" name="current_template" id="current_template" value="<?php 
		  if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
		 echo str_replace('"', '\"', $selected_template).'_preview'; 
?>" />
	

	
	<?php
	$config     = JFactory::getConfig();
	if (method_exists($config, 'getValue'))
	$secret       = $config->getValue('secret');
	else $secret       = $config->get('secret');
		$opc_secret = md5('opcsecret'.$secret); 
		echo '<input type="hidden" name="opc_secret" value="'.$opc_secret.'" id="opc_secret" />'; 
		
	?>
<div class="wrapper">
<div class="opc_header"><h2><a class="opc_header" href="#" onclick="return opc_toggle(this, 'color_picker_section');"><span class="arrow-e" style="font-size:1em;">&nbsp;</span><?php echo JText::_('COM_ONEPAGE_COLOR_PICKER'); ?></a></h2></div>
<div class="color_picker_section" id="color_picker_section" style="display: none;">
<h2><?php echo JText::_('COM_ONEPAGE_OPC_COLORS'); ?></h2>
<p><?php echo JText::_('COM_ONEPAGE_COLOR_PICKER_DESC'); ?></p>

	
	<?php 
	
		$colors = $this->colors; 
	foreach ($this->colors as $idcolor=>$color)
	{
	 echo '<input class="unselected" type="text" title="'.$idcolor.'" alt="'. $idcolor.'" value="'. $idcolor.'" rel="'. $idcolor.'" onclick="return onColorClick(this);" ondblclick="return opc_colorPicker(event, this);" id="myColor'. str_replace('#', '', $idcolor).'" style="background-color:'.$idcolor.'; color:'.$idcolor.';"  onchange="return opc_onColorChange(this, \''.$idcolor.'\');"/>';
	 
	}
	?>
	
		
<h2><?php echo JText::_('COM_ONEPAGE_JOOMLA_COLORS'); ?></h2>		
		<input type="hidden" id="currentColor" value="" />
		<?php 
		
	foreach ($this->templatecolors as $color => $val)
	{
		echo '<input type="text" class="unselected" id="myColor'.$color.'" name="orig" style="background-color:'.$color.'" onclick="opc_changeColor(this, '."'".$color."'".')"  />'; 
		//echo $color."<br />"; 
	}
	?>
</div>		
<br style="clear: both;" />
<div class="opc_header"><h2><a class="opc_header" href="#" onclick="return opc_toggle(this, 'css_file_editor');"><span class="arrow-e" style="font-size:1em;">&nbsp;</span><?php echo JText::_('COM_ONEPAGE_CSS_EDITOR'); ?></a></h2></div>
<div class="css_file_editor" id="css_file_editor" style="display: none;">


<?php
//document.getElementsByClassName('."'".'activated'."'".')[0].style.backgroundColor
foreach ($this->cssfiles as $file)
 {
	 
    $lf = str_replace(JPATH_ROOT, '', $file); 
	echo 'Css file: '.$lf.'<input type="button" rel="'.md5($file).'" onclick="return op_runSST( this, \'editcss\');" value="'.JText::_('JTOOLBAR_EDIT').'..." /><br />';
 }
?>
</div>
<div id="hideme" style="display: none;">
<input type="button" value="<?php echo JText::_('JTOOLBAR_APPLY'); ?>" rel="" id="savecssid" onclick="return op_runSST( this, 'savecss');" /><br />
<textarea id="css_here" name="css" style="width: 100%;" rows="50"></textarea>
</div>
<input type="hidden" id="nickname" value="" />
<input class="theme_button" type="button" value="<?php echo JText::_('JGLOBAL_PREVIEW'); ?>" onclick="return op_runSST( '', 'preview');" />
<input class="theme_button" type="button" value="<?php echo JText::_('COM_ONEPAGE_THEME_RELOAD'); ?>" onclick="return reloadIframe();" />
<input class="theme_button" type="button" value="<?php echo JText::_('JTOOLBAR_APPLY');?>" onclick="return op_runSST( '', 'savepreview');" />
<iframe id="previewiframe" style="width: 100%; overflow: auto; height: 1000px;" src="<?php echo str_replace('/administrator', '', JRoute::_('index.php?option=com_virtuemart&view=cart&randomproduct=1&preview=1&opc_secret='.$opc_secret)); ?>"></iframe>
</div>