<?php
/**
 * Overrided portion of JDocumentRaw class for OPC2 on Virtuemart 3 and Joomla 3.8
 *
 * This class was overrided to support addCustomScript and other non raw header insertion from raw view in ajax
 * Later update will include synchronization of the added scripts and css with already generated header data
 *
 * @package One Page Checkout for VirtueMart 3
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
*/

 // Check to ensure this file is included in Joomla!
namespace Joomla\CMS\Document;

defined('JPATH_PLATFORM') or die('Restricted access');

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

jimport('joomla.utilities.utility');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'opchtmlclassj38.php'); 
class JDocumentOpchtml extends JDocumentOpchtmlclass
{
	
}
class OpchtmlDocument extends JDocumentOpchtml {
	
}