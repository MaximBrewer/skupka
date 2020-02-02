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

class totalordersbycountry extends gvviewclass
{
	function __construct($feedDir, $className)
	{
		global $tfWhere;
		parent::__construct($feedDir, $className);
		
		$this->strTitleName1 = JText::_('COM_NETBASE_ORDERS_CONTRY');
		$this->strTitleName2 = JText::_('Total orders by country');
		$this->strSwitchText = JText::_('COM_NETBASE_SWITCH');
		$this->bAllowCsv = true;
		$this->bSingleView = false;
		$this->bDateFiltered = true;
		$this->strVisToinclude1 = "geomap";
		$this->strVisToinclude2 = "table";
		$this->strToolTip = JText::_('Shows total order income from each country in a map or table view. The darker the map - the more orders from that area! You can switch between map and table views, and download the data as a CSV.');
		if(VMRELEASE == 'OLD'){
			$this->strSql1 = "SELECT co.country_name as Country, SUM(ord.order_total) as 'Total orders' 
			FROM #__vm_orders ord, #__vm_country co, #__vm_order_user_info ou 
			WHERE (ou.order_id = ord.order_id AND ou.country = co.country_3_code) AND ou.address_type = \"BT\" AND (ord.order_status IN (" . ENDSTATUSES . "))" . $tfWhere . " GROUP BY co.country_name";
			$this->strSql2 = "SELECT co.country_name as Country, SUM(ord.order_total) as 'Total orders' 
			FROM #__vm_orders ord, #__vm_country co, #__vm_order_user_info ou 
			WHERE (ou.order_id = ord.order_id AND ou.country = co.country_3_code) AND ou.address_type = \"BT\" AND (ord.order_status IN (" . ENDSTATUSES . "))" . $tfWhere . " GROUP BY co.country_name";
		}
		else{
			$this->strSql1 = "SELECT co.country_name as Country, SUM(ord.order_total) as 'Total orders'
			FROM #__virtuemart_orders ord, #__virtuemart_countries co, #__virtuemart_order_userinfos ou
			WHERE (ou.virtuemart_order_id = ord.virtuemart_order_id AND ou.virtuemart_country_id = co.virtuemart_country_id) AND ou.address_type = \"BT\" AND (ord.order_status IN (" . ENDSTATUSES . "))" . $tfWhere . " GROUP BY co.country_name";
			$this->strSql2 = "SELECT co.country_name as Country, SUM(ord.order_total) as 'Total orders'
			FROM #__virtuemart_orders ord, #__virtuemart_countries co, #__virtuemart_order_userinfos ou
			WHERE (ou.virtuemart_order_id = ord.virtuemart_order_id AND ou.virtuemart_country_id = co.virtuemart_country_id) AND ou.address_type = \"BT\" AND (ord.order_status IN (" . ENDSTATUSES . "))" . $tfWhere . " GROUP BY co.country_name";
		}
	}
	

public function getHandleFunctions()
	{	$abc="cms_no_data";
		$handler = "
		function handletotalordersbycountry_fQueryResponse(response)
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
			
			document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
			var data = response.getDataTable();

					
			if(data.getNumberOfRows() == 0){
				//document.getElementById('" . $this->getBodyId() . "').innerHTML = '<div id=".$abc." style=\"text-align: center; vertical-align: middle\">jnvjfdbn</div>';
				var options = {dataMode: 'regions'};
						var data2 = google.visualization.arrayToDataTable([
					        ['Country',   'total'],
					        ['France',  50],
					        ['United States', 27],
					        ['Viet Nam', 23],
					      ]);
				var geoMap = new google.visualization.GeoChart(document.getElementById('" . $this->getBodyId() . "'));
				geoMap.draw(data2, options);
				jQuery(document).ready(function (){
					jQuery('#" . $this->getBodyId() . "').after('<span class=".$abc.">No data<p>(Data Demo)</p></span>');
				});
			}
			else
			{	
				jQuery(document).ready(function (){
					jQuery('.cms_no_data').remove();
				});
				var geoMap = new google.visualization.GeoChart(document.getElementById('" . $this->getBodyId() . "'));
				geoMap.draw(data, {width: '100%', height: '315px', displayMode : 'regions'});
			}
		}
		
		function handletotalordersbycountry_sQueryResponse(response) 
		{
			if (response.isError()) 
			{
				alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
				return;
			}
			
			document.getElementById('" . $this->getBodyId() . "').innerHTML = '';
			var data = response.getDataTable();
		    var table = new google.visualization.Table(document.getElementById('" . $this->getBodyId() . "'));
		    table.draw(data, {allowHtml: true, showRowNumber: true, sortAscending: false, sortColumn: 1, width: '100%', height: '100%', pageSize: 10, page: 'enable'});
	    }";
		
		return $handler;
	}
}

?>