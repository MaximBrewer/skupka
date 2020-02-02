<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
$app = JFactory::getApplication();
$input = new JInput();

$this->imagespath = JUri::root(true) . '/administrator/components/com_accordeonck'; 
$this->colorpicker_class = 'color {required:false,pickerPosition:\'top\',pickerBorder:2,pickerInset:3,hash:true}';
?>
<?php echo $this->loadTemplate('head'); ?>
<?php echo $this->loadTemplate('edition'); ?>
<?php echo $this->loadTemplate('importexport'); ?>
<?php echo $this->loadTemplate('footer'); ?>
