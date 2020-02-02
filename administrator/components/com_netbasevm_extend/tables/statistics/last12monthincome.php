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

class last12monthincome extends gvviewclass
{
	function __construct($feedDir, $className)
	{
		global $curdate;
                
		parent::__construct($feedDir, $className);
		
		$this->strTitleName1 = JText::_('COM_NETBASE_MONTHLY_REVENUE');
		$this->strTitleName2 = JText::_('COM_NETBASE_MONTHLY_REVENUE');
		$this->strSwitchText = JText::_('COM_NETBASE_SWITCH');
		$this->bAllowCsv = true;
		$this->bSingleView = false;
		$this->bDateFiltered = false;
		$this->strVisToinclude1 = "columnchart";
                
                if(!isset($curdate)){
                    $curdate= 'ord.created_on';
                }  


		$this->strToolTip = JText::_('This bar chart shows total store income for the 12 preceding months. Toggle views to see comparison of monthly income until current day of month. You can also download all the data as a CSV.');
		//for VM3
                if(VMRELEASE == 'OLD'){
			$this->strSql1 = "SELECT FROM_UNIXTIME( ord.cdate, '%b/%y' ) as 'Month', IFNULL(SUM(ord.order_total),0) as 'Total orders' 
			FROM #__vm_orders as ord 
			WHERE ord.order_status IN (" . ENDSTATUSES . ") AND FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) >= (LAST_DAY(" . $curdate . " - INTERVAL 1 YEAR)+INTERVAL 1 DAY) 
			GROUP By FROM_UNIXTIME( ord.cdate, '%b/%y' ) 
			ORDER BY STR_TO_DATE(FROM_UNIXTIME( ord.cdate, '%b/%y' ),'%b/%y') ASC";
			$this->strSql2 = "SELECT FROM_UNIXTIME( ord.cdate, '%b/%y' ) as 'Month', IFNULL(SUM(ord.order_total),0) as 'Total orders' 
			FROM #__vm_orders as ord 
			WHERE ord.order_status IN (" . ENDSTATUSES . ") AND FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) >= (" . $curdate . " - INTERVAL 1 YEAR) AND DAYOFMONTH(FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' )) <= DAYOFMONTH(" . $curdate . ")
			GROUP By FROM_UNIXTIME( ord.cdate, '%b/%y' ) 
			ORDER BY STR_TO_DATE(FROM_UNIXTIME( ord.cdate, '%b/%y' ),'%b/%y') ASC";
		}
		else{
			$this->strSql1 = "SELECT DATE_FORMAT( ord.created_on, '%b/%y' ) as 'Month', IFNULL(SUM(ord.order_total),0) as 'Total orders'
			FROM #__virtuemart_orders as ord
			WHERE ord.order_status IN (" . ENDSTATUSES . ") AND DATE( ord.created_on) >= (LAST_DAY(" . $curdate . " - INTERVAL 1 YEAR)+INTERVAL 1 DAY)
			GROUP By DATE_FORMAT( ord.created_on, '%b/%y' )
			ORDER BY STR_TO_DATE(DATE_FORMAT( ord.created_on, '%b/%y' ),'%b/%y') ASC";
			$this->strSql2 = "SELECT DATE_FORMAT( ord.created_on, '%b/%y' ) as 'Month', IFNULL(SUM(ord.order_total),0) as 'Total orders'
			FROM #__virtuemart_orders as ord
			WHERE ord.order_status IN (" . ENDSTATUSES . ") AND DATE( ord.created_on) >= (LAST_DAY(" . $curdate . " - INTERVAL 1 YEAR)+INTERVAL 1 DAY)
			GROUP By DATE_FORMAT( ord.created_on, '%b/%y' )
			ORDER BY STR_TO_DATE(DATE_FORMAT( ord.created_on, '%b/%y' ),'%b/%y') ASC";
		}

	}
	

public function getHandleFunctions()
	{

    
		$handler = "
		function handlelast12monthincome_fQueryResponse(response) 
		{
               
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
                        document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
			var data = response.getDataTable();
			var last12MonthChart = new google.visualization.ColumnChart(document.getElementById('" . $this->getBodyId() . "'));
			last12MonthChart.draw(data, {legend: 'none', width: '446px', min: 0, axisFontSize: 10,titleTextStyle: {color: 'red'}});
		}
		
		function handlelast12monthincome_sQueryResponse(response) 
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
        
			document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
			var data = response.getDataTable();
			var last12MonthChart = new google.visualization.ScatterChart(document.getElementById('" . $this->getBodyId() . "'));
			last12MonthChart.draw(data, {legend: 'none', width: '446px', min: 0, axisFontSize: 10,titleTextStyle: {color: 'red'}});
		}";

		return $handler;
	}
}

?>