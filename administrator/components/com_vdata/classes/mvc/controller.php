<?php
/*------------------------------------------------------------------------
# com_vdata - vdata
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2015 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport ( 'joomla.application.component.controller' );

if (version_compare ( JVERSION, '3.0', 'ge' )) {
	class VDController extends JControllerLegacy {
		public function display($cachable = false, $urlparams = array()) {
			parent::display ( $cachable, $urlparams );
		}
	}
} else if (version_compare ( JVERSION, '2.5', 'ge' )) {
	class VDController extends JController {
		public function display($cachable = false, $urlparams = false) {
			parent::display ( $cachable, $urlparams );
		}
	}
} else {
	class VDController extends JController {
		public function display($cachable = false) {
			parent::display ( $cachable );
		}
	}
}