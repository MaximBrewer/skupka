<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport ( 'joomla.application.component.view' );

if (version_compare ( JVERSION, '3.0', 'ge' )) {
	class VDView extends JViewLegacy {
	}
} else {
	class VDView extends JView {
	}
}
