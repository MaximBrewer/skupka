<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	jimport( 'joomla.application.component.model' );
	jimport( 'joomla.filesystem.file' );

	
class JModelNumbering extends OPCModel
{
    function __construct()
		{
			parent::__construct();
		
		}
		
		function createTableAgendas()
		{
			$q = 'CREATE TABLE IF NOT EXISTS `#__onepage_agendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `format` varchar(255) NOT NULL DEFAULT \'nnnnnn\',
  `depends` int(11) NOT NULL DEFAULT \'0\',
  `reseton` int(11) NOT NULL DEFAULT \'0\',
  `name` varchar(255) NOT NULL,
  `changed` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			
			'; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$db->query(); 
			
			$q = 'INSERT INTO `#__onepage_agendas` (`id`, `format`, `depends`, `reseton`, `name`, `changed`) VALUES
(NULL, \'{R}YYYYmmddnnnnnn\', 0, 3, \'Order Number\', 0),
(NULL, \'IInnnnnn\', 0, 0, \'Issued Invoice Number\', 0),
(NULL, \'DNnnnnnn\', 2, 0, \'Delivery Note Number\', 0); '; 
			
			$db->setQuery($q); 
			$db->query(); 
			
		}
		
		function createTableNumbering()
		{
			$q = '
CREATE TABLE IF NOT EXISTS `#__onepage_numbering` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ref_idagenda` int(11) NOT NULL COMMENT \'OPC Agenda for Number formatting\',
  `ref_idtype` int(11) NOT NULL DEFAULT \'0\' COMMENT \'A specific order_id or invoice_id or other entity\',
  `ref_type` int(11) NOT NULL COMMENT \'Either invoice, order or OPC export theme\',
  `ai` int(11) NOT NULL,
  `reserved` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `result` varchar(255) NOT NULL DEFAULT \'\',
  PRIMARY KEY (`id`),
  KEY `ref_idagenda` (`ref_idagenda`,`ref_idtype`,`ref_type`,`ai`,`created`),
  KEY `ref_idagenda_2` (`ref_idagenda`,`ref_type`,`ai`,`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 '; 
		
		$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$db->query(); 
		
		}
	function getAgendas()
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		
		if (!OPCmini::tableExists('onepage_agendas'))
		{
			$this->createTableAgendas(); 
		}
		
		if (!OPCmini::tableExists('onepage_numbering'))
		{
			$this->createTableNumbering(); 
		}

		
		$db = JFActory::getDBO(); 
		$q = 'select * from #__onepage_agendas where 1 limit 999'; 
		try {
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		} catch (Exception $e) { 
		 $res = array(); 
		}
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'numbering.php'); 
		foreach ($res as $k=>$agenda)
		{
			$created = null; 
			$id = (int)$agenda['id']; 
			$ai = 0; 
			if ($id === 0)
			$numbering = OPCNumbering::getNext($id, 1, -1, $created, $ai);
			else
			if ($id === 1)
			$numbering = OPCNumbering::getNext($id, 2, -1, $created, $ai);
			else
			if ($id > 1)
			$numbering = OPCNumbering::getNext($id, 3, -1, $created, $ai);
		    
			
			
			$res[$k]['nextai'] = $ai; 
			$res[$k]['formatted'] = $numbering; 
		}
		
		return $res; 
	}
	
		


    
}