<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

if(version_compare(JVERSION,'3.0.0','ge')) {
	class VM2SMSProxyView extends JViewLegacy{}
} else {
	class VM2SMSProxyView extends JView{}
}


class Vm2smsViewVm2sms extends VM2SMSProxyView
{

	protected $items;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		if(!defined('VM_VERSION')){
			include(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		}
		VmConfig::loadJLang('com_virtuemart_orders',TRUE);
		$db = JFactory::getDBO();
		$db->setQuery('SELECT s.order_status_code,s.order_status_name,vs.text_sms,vs.send_sms,vs.worktime,vs.manager_text_sms,vs.manager_send_sms,vs.manager_worktime,vs.include_comment FROM #__virtuemart_orderstates as s
				left join #__vm2sms as vs on vs.status=s.order_status_code');
		$this->items = $db->loadObjectList();
		JToolBarHelper::save('save','JTOOLBAR_APPLY');
		JToolBarHelper::preferences('com_vm2sms');
		JToolBarHelper::title(JText::_('COM_VM2SMS_SETTINGS'));
		parent::display($tpl);
	}

}
