<?php 
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('behavior.tooltip');

$attribs = array('target'=>'_blank');
?>

<div style="float:left;" id="j-sidebar-container" class="span2">
	<?php echo JHtmlSidebar::render(); ?>
</div>

<div id="j-main-container" class="span10">

<div class="adminform">
  <div id="yandexmenu-info">
	<h3><?php echo JText::_('COM_MAPYANDEX_ABOUT');?></h3>
    <?php echo JHTML::link("http://slyweb.ru/developer/yandexmap",JText::_('COM_MAPYANDEX_DEV'),$attribs);?></a>

	<h3> <?php echo JText::_('COM_MAPYANDEX_SITESLYWEB');?></h3>
	<?php echo JHTML::link("http://slyweb.ru/developer/yandexmap",'http://slyweb.ru/developer/yandexmap',$attribs);?>
	<h3><?php echo JText::_('COM_MAPYANDEX_LICENSE');?></h3>
    <?php echo JHTML::link("http://www.gnu.org/licenses/gpl-2.0.html",'GPLv2',$attribs);?>
  </div>

</div>
</div>