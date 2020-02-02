<?php
defined('_JEXEC') or die('Restricted access');

class plgVmpaymentRobokassaInstallerScript{
	function preflight($route, $x){
		if ($route == 'install') {
			if(version_compare(JVERSION,'3.0.0','ge')) {
				JFile::copy(dirname(__FILE__).'/vmshippingmethods.php',JPATH_ADMINISTRATOR.'/components/com_virtuemart/fields/vmshippingmethods.php');
				JFile::copy(dirname(__FILE__).'/vmrobokassacurrency.php',JPATH_ADMINISTRATOR.'/components/com_virtuemart/fields/vmrobokassacurrency.php');
			} else {
				JFile::copy(dirname(__FILE__).'/vmshippingmethods.php',JPATH_ADMINISTRATOR.'/components/com_virtuemart/elements/vmshippingmethods.php');
				JFile::copy(dirname(__FILE__).'/vmrobokassacurrency.php',JPATH_ADMINISTRATOR.'/components/com_virtuemart/elements/vmrobokassacurrency.php');
			}
		}
	}

	public function postflight( $type, $parent ) {
		$db = JFactory::getDBO();
		$db->setQuery('UPDATE #__extensions set enabled=1 where `type`="plugin" and
			element="robokassa" and folder="vmpayment"');
		$db->query();
	}
}