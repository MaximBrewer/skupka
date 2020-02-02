<?php
/*--------------------------------------------------------------------------------------------------------
# VP One Page Checkout - Joomla! System Plugin for VirtueMart 3
----------------------------------------------------------------------------------------------------------
# Copyright:     Copyright (C) 2012-2017 VirtuePlanet Services LLP. All Rights Reserved.
# License:       GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
# Author:        Abhishek Das
# Email:         info@virtueplanet.com
# Websites:      https://www.virtueplanet.com
----------------------------------------------------------------------------------------------------------
$Revision: 3 $
$LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
$Id: eusystemcheck.php 3 2017-09-20 14:30:08Z Abhshek Das $
----------------------------------------------------------------------------------------------------------*/
defined('JPATH_PLATFORM') or die;

class JFormFieldEusystemcheck extends JFormField
{
	protected $type = 'Eusystemcheck';
	
	function getInput()
	{
		return '';
	}
	
	function getLabel()
	{
		$errors = array();
		$html   = '';
		
		if(!class_exists('SoapClient'))
		{
			$errors[] = 'SoapClient is missing. You need to have Soap library installed and enabled in your server to run EU VAT check. Refer to the PHP <a href="http://php.net/manual/en/class.soapclient.php" target="_blank">documentation</a> to learn more.';
		}
		
		if(!empty($errors))
		{
			$html .= '<div id="' . $this->id . '" style="background-color:#f2dede;border: 1px solid #ebccd1;color:#a94442;margin-bottom:15px;padding:8px 14px;">';
			
			foreach($errors as $error)
			{
				$html .= '<p>' . $error . '</p>';
			}
			
			$html .= '</div>';
			
			$html .= '<script>';
			$html .= 'jQuery(document).ready(function($) {$("#' . $this->id . '").closest(".control-label").css("width", "auto");})';
			$html .= '</script>';
		}
		
		return $html;
	}
}