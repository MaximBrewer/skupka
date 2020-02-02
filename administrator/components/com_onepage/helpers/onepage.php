<?php
// No direct access to this file
defined('_JEXEC') or die;
 
/**
 * OnePage component helper.
 */
abstract class OnepageHelper
{
        /**
         * Configure the Linkbar.
         */
        public static function addSubmenu($submenu) 
        {
                JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_CONFIGURATION_TITLE'),
			'index.php?option=com_onepage&view=config',
			$submenu == 'config'
		);
		
		if (file_exists(JPATH_SITE.DS.'components'.DS.'com_hikashop'.DS.'hikashop.php')) {
			 JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_HIKA_CONFIGURATION_TITLE'),
			'index.php?option=com_onepage&view=hikaconfig',
			$submenu == 'hikaconfig'
		);
		}
		
		JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_OPC_FILTERS'),
			'index.php?option=com_onepage&view=filters',
			$submenu == 'filters'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_OPC_THEME_EDITOR'),
			'index.php?option=com_onepage&view=edittheme',
			$submenu == 'edittheme'
		);
		
	JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_TRACKING_PANEL'),
			'index.php?option=com_onepage&view=tracking',
			$submenu == 'tracking'
		);
	
	JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_UTILS'),
			'index.php?option=com_onepage&view=utils',
			$submenu == 'utils'
		);
		
		
		JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_XML_EXPORT'),
			'index.php?option=com_onepage&view=xmlexport',
			$submenu == 'xmlexport'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_ORDER_EXPORT_CONFIG'),
			'index.php?option=com_onepage&view=order_export',
			$submenu == 'order_export'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_ORDER_MANAGEMENT').' ('.JText::_('COM_ONEPAGE_EXPERIMENTAL').')',
			'index.php?option=com_onepage&view=orders',
			$submenu == 'orders'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_NUMBERING'),
			'index.php?option=com_onepage&view=numbering',
			$submenu == 'numbering'
		);
		//COM_ONEPAGE_NUMBERING
		
		/*
		 $db = JFactory::getDBO(); 
	 $q = "select shipment_params from #__virtuemart_shipmentmethods where shipment_element = 'pickup_or_free' and published = 1 limit 0,1"; 
	 $db->setQuery($q); 
	 $json = $db->loadResult(); 
	 if (!empty($json))
	  {
	     JSubMenuHelper::addEntry(
			JText::_('COM_ONEPAGE_ROUTE_CONFIG_LINK'),
			'index.php?option=com_onepage&view=pickup',
			$submenu == 'pickup'
		);
	  }
	  */
		
		
                // set some global property
				/*
                $document = JFactory::getDocument();
                $document->addStyleDeclaration('.icon-48-helloworld ' .
                                               '{background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
                */
        }
}