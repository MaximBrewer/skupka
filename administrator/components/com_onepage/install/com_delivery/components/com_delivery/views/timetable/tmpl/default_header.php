<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z 
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$document =& JFactory::getDocument();
JHTMLOPC::script('helper.js', 'administrator/components/com_delivery/views/config/tmpl/js/', false);
JHTMLOPC::stylesheet('view.css', 'administrator/components/com_delivery/views/config/tmpl/css/', false);
$document->setTitle('Delivery configuration for OPC Pickup or Delivery Plugin'); 


JHTML::_('behavior.tooltip');



if (!version_compare(JVERSION,'2.5.0','ge'))
{
  $j15 = true; 
  
}
jimport('joomla.html.pane');
jimport('joomla.utilities.utility');


if (!empty($j15)) echo '<div>'.JText::_('COM_ONEPAGE_ONLY_J25').'</div>'; 
if (!empty($j15)) echo '<div style="display: none;">'; 

