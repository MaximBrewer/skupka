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

class periodordersbystatus extends gvviewclass
{
	function __construct($feedDir, $className)
	{
		global $tfWhere;
		parent::__construct($feedDir, $className);
		
		$this->strTitleName1 = JText::_('COM_NETBASE_ORDER_STATUS');
		$this->strTitleName2 = JText::_('Period total orders by status');
		$this->strSwitchText = JText::_('COM_NETBASE_SWITCH');
		$this->bAllowCsv = true;
		$this->bSingleView = false;
		$this->bDateFiltered = true;
		$this->strVisToinclude1 = "piechart";
		$this->strVisToinclude2 = "table";
		$this->intFrameHeight = 153;
		$this->strToolTip = JText::_('Click on the chart for details of each segment. You can also switch to table view, or download the data as a CSV using the links below the chart.');
		if(VMRELEASE == 'OLD'){
			$ordersTable = "#__vm_orders";
			$orderStatusTable = "#__vm_order_status";
		}
		else{
			$ordersTable = "#__virtuemart_orders";
			$orderStatusTable = "#__virtuemart_orderstates";
		}
		$this->strSql1 = "SELECT os.order_status_name as Status, sum(ord.order_total) as 'Total orders', ord.order_status as 'status code' 
		FROM " . $ordersTable . " as ord, " . $orderStatusTable . " os 
		WHERE os.order_status_code = ord.order_status" . $tfWhere . "GROUP BY os.order_status_name";
		$this->strSql2 = "SELECT os.order_status_name as Status, sum(ord.order_total) as 'Total orders', ord.order_status as 'status code' 
		FROM " . $ordersTable . " as ord, " . $orderStatusTable . " os WHERE os.order_status_code = ord.order_status" . $tfWhere . "GROUP BY os.order_status_name";
	}
	

public function getHandleFunctions()
	{
		$vmOrderPath = "option=com_virtuemart&view=orders";
		
		$handler = "
		function handleperiodordersbystatus_fQueryResponse(response)
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
			
			var data = response.getDataTable();
				
			var row = data.getNumberOfRows(),
				col = data.getNumberOfColumns();
			
			for(var i=0;i < row; i ++){
					var vl = data.getValue(i,2);
					if(vl == 'C'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CONFIRMED')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'P'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_PENDING')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'U'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CONFIRMED_BY_SHOPPER')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'X'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CANCELLED')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'R'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_REFUNDED')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'S'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_SHIPPED')."';
						data.setValue(i, 2, vl);
					}
			}		
			//alert(data.getValue(2,2));
				
			var pieChart = new google.visualization.PieChart(document.getElementById('" . $this->getBodyId() . "'));
			var view = new google.visualization.DataView(data);

			view.setColumns([2,1]); // Create a view with the first and second columns only.
			document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
	        pieChart.draw(view, {allowHtml: true, fontSize: 60,width: 500, height: 300, is3D: true, enableTooltip: true, position: 'top', textStyle: {color: 'blue', fontSize: 60}, pieHole: 0.4});
		}
		
		function handleperiodordersbystatus_sQueryResponse(response) 
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
			
			document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
			var data = response.getDataTable();
					
			var row = data.getNumberOfRows(),
				col = data.getNumberOfColumns();
			
			for(var i=0;i < row; i ++){
					var vl = data.getValue(i,2);
					if(vl == 'C'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CONFIRMED')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'P'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_PENDING')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'U'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CONFIRMED_BY_SHOPPER')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'X'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CANCELLED')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'R'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_REFUNDED')."';
						data.setValue(i, 2, vl);
					}
					if(vl == 'S'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_SHIPPED')."';
						data.setValue(i, 2, vl);
					}
			}		
					
		    var table = new google.visualization.AreaChart(document.getElementById('" . $this->getBodyId() . "'));
			var formatter = new google.visualization.PatternFormat('<a href=" . $this->httpType.$this->host.$this->uri . "/index.php?" . $vmOrderPath . "\" target=\"_blank\">{1}</a>');
			//formatter.format(data, [2, 1],0); // Apply formatter and set the formatted value of the first column.
			var view = new google.visualization.DataView(data);
			view.setColumns([2,1]); // Create a view with the first and second columns only.
		    table.draw(view, {allowHtml: true, showRowNumber: true, sortAscending: false, sortColumn: 1, pieHole: 0.4});
	    }";
		
		return $handler;
	}
}

?>