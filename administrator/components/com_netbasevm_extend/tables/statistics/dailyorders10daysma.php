<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/
error_reporting(0);
require_once("helperclass.php");

class dailyorders10daysma extends gvviewclass
{
	function __construct($feedDir, $className)
	{
		global $tfWhere;
		parent::__construct($feedDir, $className);
		
		$this->strTitleName1 = JText::_('Daily orders vs. 10 days moving average');
		$this->bAllowCsv = true;
		$this->bSingleView = true;
		$this->bDateFiltered = false;
		$this->strVisToinclude1 = "annotatedtimeline";
		$this->strToolTip = JText::_('The blue line on the graph shows the daily store income. The red line shows the 10 day moving average. You can download all the data as a CSV by using the link below the graph.');
		$this->overflow = "none";
		$this->width = "436px";
		//for VM3
			$this->strSql1 = "SELECT DATE(ord.created_on) AS Date, IFNULL(sum(ord.order_total),0) as 'Daily orders', IFNULL((SELECT sum(ord1.order_total)/10
			FROM #__virtuemart_orders as ord1
			WHERE DATE_FORMAT(ord1.created_on, '%Y-%m-%d') BETWEEN DATE(ord.created_on) - INTERVAL 10 DAY AND DATE(ord.created_on) - INTERVAL 1 DAY AND ord1.order_status IN (" . ENDSTATUSES . ")),0) as '10d MA' FROM #__virtuemart_orders as ord WHERE DATE(ord.created_on) >= (SELECT MIN(DATE(ord2.created_on) + INTERVAL 11 day) FROM #__virtuemart_orders as ord2 WHERE ord2.order_status IN (" . ENDSTATUSES . ")) AND (ord.order_status IN (" . ENDSTATUSES . "))
			GROUP BY DATE(ord.created_on) ORDER BY DATE(ord.created_on)";
		
	}
	

public function getHandleFunctions()
	{
		$handler = "
		function handledailyorders10daysmaQueryResponse(response) 
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
	        
			var data = response.getDataTable();
			var maTimeline = new google.visualization.AnnotatedTimeLine(document.getElementById('" . $this->getBodyId() . "'));
			maTimeline.draw(data, {displayAnnotations: false});
	    }";
		
		return $handler;
	}
}

?>