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

class periodorderslist extends gvviewclass
{
	function __construct($feedDir, $className)
	{
            
		global $tfWhere;
		parent::__construct($feedDir, $className);
		
		$this->strTitleName1 = JText::_('COM_NETBASE_ORDERS_LIST');
		$this->bAllowCsv = true;
		$this->bSingleView = true;
		$this->bDateFiltered = true;
		$this->strVisToinclude1 = "table";
		$this->strToolTip = JText::_('Shows all orders in the selected period. You can sort by any column by clicking the column header. Drill down by clicking on the order number or the customer name. You can also download all the displayed orders as a CSV.');

                if(VMRELEASE == 'OLD'){
			$this->strSql1 = "SELECT FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) as 'Order date', ord.order_id as 'Order ID', CONCAT(us.first_name, ' ', us.last_name) as name, ord.order_total as Amount, os.order_status_name as Status, ord.user_id 
			FROM #__vm_orders as ord, #__vm_order_status os, #__vm_order_user_info us 
			WHERE ord.user_id = us.user_id AND us.address_type = \"BT\" AND us.order_id = ord.order_id AND os.order_status_code = ord.order_status" . $tfWhere . " ORDER BY FROM_UNIXTIME( ord.cdate, '%Y-%m-%d' ) DESC, ord.order_id DESC";
		}
		else{
			$this->strSql1 = "SELECT DATE_FORMAT(ord.created_on, '%Y-%m-%d') as 'Order date', ord.virtuemart_order_id as 'Order ID', CONCAT(us.first_name, ' ', us.last_name) as 'User', ord.order_total as 'Amount', os.order_status_name as 'Status', ord.virtuemart_user_id as 'User ID'
			FROM #__virtuemart_orders as ord, #__virtuemart_orderstates os, #__virtuemart_order_userinfos us
			WHERE ord.virtuemart_user_id = us.virtuemart_user_id AND us.address_type = \"BT\" AND us.virtuemart_order_id = ord.virtuemart_order_id AND os.order_status_code = ord.order_status" . $tfWhere . " ORDER BY DATE_FORMAT(ord.created_on, '%Y-%m-%d') DESC, ord.virtuemart_order_id DESC";
                       
		}

	}
	

public function getHandleFunctions()
	{
		$vMuserPath = "option=com_virtuemart&view=user&task=edit&cid[]={0}";
		$vmOrderPath = "option=com_virtuemart&view=orders&task=edit&virtuemart_order_id={0}";
		
		$handler = "
		function handleperiodorderslistQueryResponse(response) 
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
	    
			document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
			var data = response.getDataTable();
					//alert(data, 5);
			if(data.getNumberOfRows() == 0)
					document.getElementById('" . $this->getBodyId() . "').innerHTML = '<div style=\"text-align: center; vertical-align: middle\"> No Data</div>';
			else
			{	google.load('visualization', '1', {packages:['table']});
							
			    var table = new google.visualization.Table(document.getElementById('" . $this->getBodyId() . "'));
				
			    var formatter = new google.visualization.NumberFormat({fractionDigits: 0, groupingSymbol: ''});
				formatter.format(data, 1); // Apply formatter to second column
				
				//formatter.format(data, 5); // Apply formatter to third column
				
				var formatter1 = new google.visualization.PatternFormat('<a href=\"" . $this->httpType.$this->host.$this->uri. "/index.php?" . $vmOrderPath . "\" target=\"_blank\">{1}</a>');
				formatter1.format(data, [1, 1],1); // Apply formatter and set the formatted value of the second column.
				
				var formatter2 = new google.visualization.PatternFormat('<a href=\"" . $this->httpType.$this->host.$this->uri. "/index.php?" . $vMuserPath . "\" target=\"_blank\">{1}</a>');
				formatter2.format(data, [5, 2],2); // Apply formatter and set the formatted value of the third column.
						
				var row = data.getNumberOfRows(),
					col = data.getNumberOfColumns();	
						
				//data.setColumnLabel(1, 'No.');
						
				for(var i=0;i < row; i ++){
					var vl = data.getValue(i,col-2);
					if(vl == 'COM_VIRTUEMART_ORDER_STATUS_CONFIRMED'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CONFIRMED')."';
						data.setValue(i, col-2, vl);
					}
					if(vl == 'COM_VIRTUEMART_ORDER_STATUS_PENDING'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_PENDING')."';
						data.setValue(i, col-2, vl);
					}
					if(vl == 'COM_VIRTUEMART_ORDER_STATUS_CONFIRMED_BY_SHOPPER'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CONFIRMED_BY_SHOPPER')."';
						data.setValue(i, col-2, vl);
					}
					if(vl == 'COM_VIRTUEMART_ORDER_STATUS_CANCELLED'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_CANCELLED')."';
						data.setValue(i, col-2, vl);
					}
					if(vl == 'COM_VIRTUEMART_ORDER_STATUS_REFUNDED'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_REFUNDED')."';
						data.setValue(i, col-2, vl);
					}
					if(vl == 'COM_VIRTUEMART_ORDER_STATUS_SHIPPED'){
						vl = '".JText::_('COM_VIRTUEMART_ORDER_STATUS_SHIPPED')."';
						data.setValue(i, col-2, vl);
					}
				}		
				
				var view = new google.visualization.DataView(data);
				view.setColumns([0,1,2,3,4]); // Create a view with the first 5 columns only.
				
				var option = {allowHtml: true, pageSize: 5, showRowNumber: true, sortAscending: false, sortColumn: 0, width: '100%', height: '100%', page: 'enable'};
				table.draw(view, option);
			}
	    }";
		
		return $handler;
	}
}

?>