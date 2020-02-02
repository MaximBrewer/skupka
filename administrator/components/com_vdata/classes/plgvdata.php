<?php
/*------------------------------------------------------------------------
# vData Plugin Class
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! vData Plugin
 *
 * @package		Joomla
 * @subpackage	Plugin
 */
class  plgVdata extends JPlugin
{

	public $profile = null;
	
	public function getProfile()
	{
		$db = JFactory::getDbo();
		
		$profileid = JFactory::getApplication()->input->getInt('profileid', 0);
		
		$query = 'select i.*, e.name as plugin, e.element, concat("plg_", e.folder, "_", e.element) as extension FROM #__vd_profiles as i left join #__extensions as e on (e.extension_id=i.pluginid and e.enabled=1) where i.id = '.$db->quote($profileid);
		$db->setQuery( $query );
		$item = $db->loadObject();
		
		if(empty($item))	{
			$item = new stdClass();
			$item->id = null;
			$item->title = null;
            $item->iotype = JFactory::getApplication()->input->getInt('iotype', 0);
            $item->quick = null;
			$item->pluginid = 0;
			$item->params = new stdClass();

		}
		else {
			$item->params = json_decode($item->params);
		}

		$this->profile = $item;

		return true;
		
	}
	
}