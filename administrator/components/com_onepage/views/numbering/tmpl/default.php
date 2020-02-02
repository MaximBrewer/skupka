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
echo $this->loadTemplate('header'); 
$agendas = $this->model->getAgendas(); 
?><div class="note"><?php echo JText::_('COM_ONEPAGE_DATE_NOTICE'); ?> <?php 

$c = JFactory::getConfig(); 
$t = $c->get('offset'); 
$ts = date_default_timezone_get();
date_default_timezone_set($t);
echo date("F j, Y, g:i a").'.  ';
date_default_timezone_set($ts); 

?><?php echo JText::_('COM_ONEPAGE_DATE_NOTICE2'); ?> </div>
<form action="index.php" method="post" id="adminForm" name="adminForm" class="form-horizontal">
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="view" value="numbering" />
<input type="hidden" name="task" value="apply" id="task" />
<?php
foreach ($agendas as $k=>$row) {
?> 
<fieldset><legend><?php echo JText::_('COM_ONEPAGE_NUMBERING_AGENDA'); ?>: <?php echo $row['name']; ?></legend>
 <div class="control-group">
 <label class="control-label" for="textinput"><?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT'); ?></label>
 <div class="controls">
   <input type="text" placeholder="<?php echo JText::_("COM_ONEPAGE_NUMBERING_FORMAT"); ?>" name="nformat[<?php echo $row['id']; ?>]" value="<?php echo $row['format']; ?>" />
 </div>
 </div>

 <div class="control-group">
 <label class="control-label" for="textinput"><?php echo JText::_('COM_ONEPAGE_NUMBERING_AGENDA_LABEL'); ?></label>
 <div class="controls">
   <input type="text" placeholder="<?php echo JText::_("COM_ONEPAGE_NUMBERING_AGENDA_LABEL"); ?>" name="aname[<?php echo $row['id']; ?>]" value="<?php echo $row['name']; ?>" />
 </div>
 </div>
 
  <div class="control-group">
 <label class="control-label" for="textinput"><?php echo JText::_('COM_ONEPAGE_NUMBERING_RESETON'); ?></label>
 <div class="controls">
  <select name="reseton[<?php echo $row['id']; ?>]" >
   <?php for ($i=0; $i<=3; $i++) { ?>
    <option <?php 
	if ($row['reseton'] == $i) echo ' selected="selected" '; 
	?>value="<?php echo $i; ?>"><?php echo JText::_('COM_ONEPAGE_NUMBERING_RESETON_'.$i); ?></option>
   <?php } ?>
  </select>
 </div>
 </div>
 
 
 <div class="control-group">
 <label class="control-label" for="textinput"><?php echo JText::_('COM_ONEPAGE_NUMBERING_DEPENDSON'); ?></label>
 <div class="controls">
  <select name="dependson[<?php echo $row['id']; ?>]" >
   <option value="0"><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
   <?php foreach ($agendas as $k2=>$row2) { ?>
    <option <?php 
	if ($row['depends'] == $row2['id']) echo ' selected="selected" '; 
	?> value="<?php echo $row2['id']; ?>"><?php echo $row2['name']; ?></option>
   <?php } ?>
  </select>
 </div>
 </div>
 
 
 <div class="control-group">
 <label class="control-label" for="textinput"><?php echo JText::_('COM_ONEPAGE_NUMBERING_NEXTAI'); ?></label>
 <div class="controls">
  <input type="text" value="<?php echo $row['nextai']; ?>" name="nextai[<?php echo $row['id']; ?>]" /><br /><?php echo $row['formatted']; ?>
 </div>
 </div>
 
 
</fieldset>


<?php }

?>
 </form>
 <div ><h2><?php echo JText::_('COM_ONEPAGE_NUMBERING_AVAILABLE_FORMAT_CODE'); ?></h2>
 
 <div class="control-group">
 <?php echo JText::_('COM_ONEPAGE_NUMBERING_BASIC_DESC'); ?>
 
 </div>
 <div class="controls">
   <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_NNNN'); ?>
   
 </div>

  <div class="control-group">
 YY
 </div>
 <div class="controls">
  <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_YY'); ?>
 </div>
 <hr />
  <div class="control-group">
 YYYY
 </div>
 <div class="controls">
<?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_YYYY'); ?>  
  
 </div>
  <hr />
  <div class="control-group">
 MM
 </div>
 <div class="controls">
 <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_MM'); ?>
   
 </div>
  <hr />
 <div class="control-group">
 DD
 </div>
 <div class="controls">
 <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_DD'); ?>
   
 </div>

 <div class="controls">
 <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_COMPLETE'); ?>
 </div>
 

 <div ><h2><?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_SPECIAL'); ?></h2>
  <div class="control-group">
 R
 </div>
 <div class="controls">
 <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_R'); ?>
   
 </div>
  <hr />
 <div class="control-group">
 Q
 </div>
 <div class="controls">
 <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_Q'); ?>
   
 </div>
 <hr />
 <div class="control-group">
 q
 </div>
 <div class="controls">
 <?php echo JText::_('COM_ONEPAGE_NUMBERING_FORMAT_Z'); ?>
   
 </div>
 
 </div>
 
