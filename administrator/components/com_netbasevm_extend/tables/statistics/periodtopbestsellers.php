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

class periodtopbestsellers extends gvviewclass
{
	function __construct($feedDir, $className)
	{
		global $tfWhere;
		parent::__construct($feedDir, $className);
		
		$this->strTitleName1 = JText::_('COM_NETBASE_TOP5_BESTPRIOD');
		$this->strTitleName2 = JText::_('COM_NETBASE_TOP5_BESTPRIOD');
		$this->strSwitchText = JText::_('COM_NETBASE_SWITCH');
		$this->bAllowCsv = true;
		$this->bSingleView = false;
		$this->bDateFiltered = true;
		$this->bTakeTimeFromOrderItem = true;
		$this->strVisToinclude1 = "table";
		$this->strToolTip = JText::_('Best selling products in the selected period. You can switch between the top 5 or all the products, and download the data as a CSV.');
		if(VMRELEASE == 'OLD'){
			$orderItemTable = "#__vm_order_item";
		}
		else{
			$orderItemTable = "#__virtuemart_order_items";
		}
		$this->strSql1 = "SELECT oi.order_item_name as Item, oi.order_item_sku as SKU, SUM(oi.product_quantity) as 'Total Quantity', SUM(oi.product_final_price*oi.product_quantity) as 'Total has sold' 
		FROM " . $orderItemTable . " oi 
		WHERE (oi.order_status IN (" . ENDSTATUSES . "))" . $tfWhere . "GROUP BY oi.order_item_sku 
		ORDER BY SUM(oi.product_quantity) DESC LIMIT 0,5";
		$this->strSql2 = "SELECT oi.order_item_name as Item, oi.order_item_sku as SKU, SUM(oi.product_quantity) as 'Total Quantity', SUM(oi.product_final_price*oi.product_quantity) as 'Total has sold' 
		FROM " . $orderItemTable . " oi 
		WHERE (oi.order_status IN (" . ENDSTATUSES . "))" . $tfWhere . "GROUP BY oi.order_item_sku 
		ORDER BY SUM(oi.product_quantity) DESC LIMIT 0,5";
	}
	
	
	public function getHandleFunctions()
	{
		$handler = "
		function handleperiodtopbestsellers_fQueryResponse(response) 
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
				document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
				var data = response.getDataTable();
				if(data.getNumberOfRows() == 0)
					document.getElementById('" . $this->getBodyId() . "').innerHTML = '<div style=\"text-align: center; vertical-align: middle\"> No Data</div>';
				else
				{
				    var table = new google.visualization.Table(document.getElementById('" . $this->getBodyId() . "'));
					var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
					formatter.format(data, 2); // Apply formatter to third column
					
					var tableSettings = {allowHtml: true, showRowNumber: true, sortAscending: false, sortColumn: 2, width: '100%', height: '100%', pageSize: 5};				
				    
					table.draw(data, tableSettings);
				}
	    }
	    
	    function handleperiodtopbestsellers_sQueryResponse(response) 
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
				document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
				var data = response.getDataTable();
				if(data.getNumberOfRows() == 0)
					document.getElementById('" . $this->getBodyId() . "').innerHTML = '<div style=\"text-align: center; vertical-align: middle\"> No Data</div>';
				else
				{
				
					var row = data.getNumberOfRows(),
						col = data.getNumberOfColumns();
					for(var i=0;i < row; i ++){
						if(i >= 5) data.removeRow(i-1);
					}	
				
				    var table = new google.visualization.LineChart(document.getElementById('" . $this->getBodyId() . "'));
					var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
					formatter.format(data, 2); // Apply formatter to third column
					
					var tableSettings = {allowHtml: true, showRowNumber: true, sortAscending: false, sortColumn: 2, width: '100%', height: '100%', pageSize: 5, page: 'enable'};				
				    
					table.draw(data, tableSettings);
				}
	    }";
		
		return $handler;
	}
}

?>