<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.10.7
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php
include(ACYMAILING_BACK.'views'.DS.'subscriber'.DS.'view.html.php');

class FrontsubscriberViewFrontsubscriber extends SubscriberViewSubscriber
{

	var $ctrl='frontsubscriber';

	function display($tpl = null)
	{
		acymailing_addStyle(false, ACYMAILING_CSS.'frontendedition.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'frontendedition.css'));

		global $Itemid;
		$this->Itemid = $Itemid;

		parent::display($tpl);
	}

	function listing(){

		if(empty($_POST) && !acymailing_getVar('int', 'start') && !acymailing_getVar('int', 'limitstart')){
			acymailing_setVar('limitstart',0);
		}

		return parent::listing();
	}

	function addqueue(){
		include_once(ACYMAILING_BACK.'views'.DS.'send'.DS.'view.html.php');
		$sendView = new SendViewSend();
		$values = $sendView->addqueue();

		if(!empty($values)){
			$this->subscriber = $values['subscriber'];
			$this->emaildrop = $values['emaildrop'];
			$this->hours = $values['hours'];
			$this->minutes = $values['minutes'];
		}
	}
}
