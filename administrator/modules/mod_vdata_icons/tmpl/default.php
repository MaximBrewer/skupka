<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined ('_JEXEC') or die ('resticted aceess');
        $user = JFactory::getUser();
        $canAdmin = $user->authorise('core.admin', 'com_vdata');
		$canManage = $user->authorise('core.manage', 'com_vdata');
		$canProfile = $user->authorise('core.profiles', 'com_vdata');
		$canSchedules = $user->authorise('core.schedules', 'com_vdata');
		
		$canImport = $user->authorise('core.import', 'com_vdata');
		$canExport = $user->authorise('core.export', 'com_vdata');
		$canQuick = $user->authorise('core.quick', 'com_vdata');
		$canView = $user->authorise('core.vdata', 'com_vdata');
		$canLogs = $user->authorise('core.logs', 'com_vdata');
		$canDisplay = $user->authorise('core.display', 'com_vdata');

?>

<div id="vdata-wrap" class="clearfix">
	<?php if($canManage) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=vdata'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_DASHBOARD'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/dashboard.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_DASHBOARD'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canAdmin) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=config'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_CONFIG'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/configuration.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_CONFIG'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canProfile) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=profiles'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_PROFILES'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/profiles.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_PROFILES'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canImport) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=import'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_IMPORT'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/import.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_IMPORT'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canExport) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=export'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_EXPORT'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/export.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_EXPORT'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canSchedules) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=schedules'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_CRON'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/feed.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_CRON'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canQuick) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=quick'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_QUICK'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/quick.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_QUICK'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canAdmin) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=notifications'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_NOTIFICATIONS'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/notifications.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_NOTIFICATIONS'); ?></span>
			</a>
		</div>
	</div>
	<?php } if($canDisplay){?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=display'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_DISPLAY'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/display.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_DISPLAY'); ?></span>
			</a>
		</div>
	</div>
	<?php } 
	if($canLogs) { ?>
	<div class="icon-wrapper">
		<div class="icon">
			<a href="<?php echo JRoute::_('index.php?option=com_vdata&view=logs'); ?>">
				<img alt="<?php echo JText::_('MOD_MENU_COM_VDATA_LOGS'); ?>" src="<?php echo JURI::root(true); ?>/administrator/modules/mod_vdata_icons/tmpl/images/logs.png" />
				<span><?php echo JText::_('MOD_MENU_COM_VDATA_LOGS'); ?></span>
			</a>
		</div>
	</div>
	<?php }?>
</div>

