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

class generatemodeType extends acymailingClass{
	function __construct(){
		parent::__construct();

		$this->values = array();
		$this->values[] = acymailing_selectOption(1, acymailing_translation('AUTONEWS_SEND'));
		$this->values[] = acymailing_selectOption(0, acymailing_translation('AUTONEWS_WAIT'));

	}

	function display($map,$value){
		return acymailing_select(  $this->values, $map, 'class="inputbox" size="1" ', 'value', 'text',(int) $value );
	}

}
