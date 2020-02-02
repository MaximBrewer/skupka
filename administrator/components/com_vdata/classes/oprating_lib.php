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
defined('_JEXEC') or die('Restricted access');

class operating
{
	 function loading()
	 {
		if(!defined('APP_ROOT'))
		define('APP_ROOT', JPATH_BASE.'/components/com_vdata/operating');
		
		require_once (JPATH_BASE.'/components/com_vdata/operating/read_config.php');
		$class_name = 'Error';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/error/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/error/class.'.$class_name.'.inc.php');
           
        }
	   $class_name = 'PSI_Interface_OS';
        if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/interface/class.'.$class_name.'.inc.php')) {
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/interface/class.'.$class_name.'.inc.php');
            
        }
		$class_name = 'OS';
       if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/os/class.'.$class_name.'.inc.php')) {
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/os/class.'.$class_name.'.inc.php');
			if(PHP_OS == "Darwin"){
			include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/os/class.BSDCommon.inc.php');	
			}
           
        }
		$class_name = 'System';
       if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/class.'.$class_name.'.inc.php')) {
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'PSI_Interface_Output';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/interface/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/interface/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'CommonFunctions';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'Parser';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/class.'.$class_name.'.inc.php');
           
        }
		$class_name = PSI_OS;
       if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/os/class.'.$class_name.'.inc.php')) {
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/os/class.'.$class_name.'.inc.php');
           
        }
		 
		$class_name = 'XML';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/xml/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/xml/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'SimpleXMLExtended';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/xml/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/xml/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'CpuDevice';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'DiskDevice';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'NetDevice';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'HWDevice';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'SensorDevice';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/to/device/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'Output';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/output/class.'.$class_name.'.inc.php')) { 
            include_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/output/class.'.$class_name.'.inc.php');
           
        }
		$class_name = 'WebpageXML';
		 if (file_exists(JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/output/class.'.$class_name.'.inc.php')) {
            require_once (JPATH_ADMINISTRATOR.'/components/com_vdata/operating/includes/output/class.'.$class_name.'.inc.php');
           
        }
		return true;
	}
}


 