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

require_once(JPATH_ADMINISTRATOR.DS."components".DS. "com_netbasevm_extend".DS."helpers/statistics".DS."config.php");
require_once(JPATH_ADMINISTRATOR.DS."components".DS. "com_netbasevm_extend".DS."helpers/statistics".DS."config1.php");

abstract class gvviewclass
{
	protected $strClassName;
	protected $strTitleName1;
	protected $strTitleName2;
	protected $strSwitchText;
	protected $bDateFiltered;
	protected $bSingleView;
	protected $bAllowCsv;
	protected $strVisToinclude1;
	protected $strVisToinclude2;
	protected $strFeedDir;
	protected $strSql1;
	protected $strSql2;
	protected $strToolTip;
	protected $intFrameHeight = 315;
	protected $width = "100%";
	protected $bTakeTimeFromOrderItem = false;
	protected $overflow = "auto";
	protected $httpType;
	protected $host;
	protected $uri;
	protected $bAddSelectEvent = false;
	protected $strSelectType = "products";
	
	/**
	 * 
	 * @param $feedDir
	 * @param $className
	 * @return unknown_type
	 */
	public function __construct($feedDir, $className)
	{
		$this->strFeedDir = $feedDir;
		$this->strClassName = $className;		
		
		if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
		 || $_SERVER['SERVER_PORT'] == '443')
		{
			$this->httpType = 'https://';
		}
		else
		{
			$this->httpType = 'http://';
		}
		
		$this->host  = $_SERVER['HTTP_HOST'];
		$this->uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	public function isDateFilter()
	{
		return $this->bDateFiltered;
	}
	
	public function isSingleView()
	{
		return $this->bSingleView;
	}
	
	public function isCsvAllowed()
	{
		return $this->bAllowCsv;
	}
	
	public function getVis1ToInclude()
	{
		return $this->strVisToinclude1;
	}
	
	public function getVis2ToInclude()
	{
		return $this->strVisToinclude2;
	}
	
	public function getTitleName1()
	{
		return $this->strTitleName1;
	}
	
	public function getTitleName2()
	{
		return $this->strTitleName2;
	}
	
	public function getSql1()
	{
		return $this->strSql1;
	}
	
	public function getSql2()
	{
		return $this->strSql2;
	}
	
	public function getDrawFunction()
	{
		$text = "";
		
		$fromDateStr = $this->bDateFiltered == true ? "fromDate" : "'all'";
		$toDateStr = $this->bDateFiltered == true ? "toDate" : "'all'";
		$paramName = $this->strClassName . "Param";
		//$paramValue = $this->bAddSelectEvent == true ? "document.getElementById('" . $this->getSelectId() . "').options[document.getElementById('" . $this->getSelectId() . "').selectedIndex].value" : "";
		$text .= "var " . $paramName . ";\n";

		if($this->isSingleView() == false)
		{
			$firstOrSecond = $this->strClassName . "ForS";
			$text .= "var " . $firstOrSecond . " = 'f';\n";
			$text .= "function draw" . $this->strClassName . "(allowSwitch)\n";
			$text .= "{\n";
			$text .= "	if(allowSwitch == true)\n";
			$text .= "	{\n";
			$text .= "		" . $firstOrSecond . " = " . $firstOrSecond . " == 'f' ? 's' : 'f';\n";
			$text .= "	}\n";
			$text .= "	if(" . $firstOrSecond . " == 'f')\n";
			$text .= "		draw" . $this->strClassName . "_f();\n";
			$text .= "	else\n";
			$text .= "		draw" . $this->strClassName . "_s();\n";
			$text .= "}\n";
			$text .= "\n";
			$text .= "function draw" . $this->strClassName . "_f()\n";
			$text .= "{\n";
			$text .= "	document.getElementById('" . $this->getTitleId() . "').innerHTML = '" . $this->strTitleName1 . "';\n";
			$text .= "	var query = new google.visualization.Query('" . $this->httpType.$this->host.$this->uri . "/components/com_netbasevm_extend/" . $this->strFeedDir . "/process.php?fid=" . $this->strClassName . "&fors=f&fd=' + " . $fromDateStr . " + '&td=' + " . $toDateStr . " + '&param1=' + " . $paramName . ");\n";
			$text .= "	query.send(handle" . $this->strClassName . "_fQueryResponse);\n";
			$text .= "}\n";
			$text .= "\n";
			$text .= "function draw" . $this->strClassName . "_s()\n";
			$text .= "{\n";
			$text .= "	document.getElementById('" . $this->getTitleId() . "').innerHTML = '" . $this->strTitleName2 . "';\n";
			$text .= "	var query = new google.visualization.Query('" . $this->httpType.$this->host.$this->uri . "/components/com_netbasevm_extend/" . $this->strFeedDir . "/process.php?fid=" . $this->strClassName . "&fors=s&fd=' + " . $fromDateStr . " + '&td=' + " . $toDateStr . " + '&param1=' + " . $paramName . ");\n";
			$text .= "	query.send(handle" . $this->strClassName . "_sQueryResponse);\n";
			$text .= "}\n";
		}
		else
		{
			$text .= "function draw" . $this->strClassName . "()\n";
			$text .= "{\n";
			$text .= "	var query = new google.visualization.Query('" . $this->httpType.$this->host.$this->uri . "/components/com_netbasevm_extend/" . $this->strFeedDir . "/process.php?fid=" . $this->strClassName . "&fors=&fd=' + " . $fromDateStr . " + '&td=' + " . $toDateStr . " + '&param1=' + " . $paramName . ");\n";
			$text .= "	query.send(handle" . $this->strClassName . "QueryResponse);\n";
			$text .= "}\n";
		}
		return $text;
	}
	
	public function getDrawFuctionCall()
	{
		$param = $this->isSingleView() ? "()" : "(false)";
		return "draw" . $this->strClassName . $param . ";";
	}
	
	public function getRefreshFunctionCall($forceCall = false)
	{
		$param = $this->isSingleView() ? "()" : "(false)";
		if($this->isDateFilter() || $forceCall)
			$funcCall = "draw" . $this->strClassName . $param . ";";
		else
			$funcCall = "";	
		
		return $funcCall;
	}
	
	public function getTitleId()
	{
		return $this->strClassName . "_title";
	}
	
	public function getBodyId()
	{
		return $this->strClassName . "_cms";
	}
	
	public function getSelectId()
	{
		return $this->strClassName . "_select";
	}
	
	public function createViewFrame()
	{
		$frameType = $this->isDateFilter() ? "title_red" : "title";
		$title = $this->getTitleHtml();
		$body = $this->getBodyHtml();
//		$title = "title";
//		$body = "body";
		$qobj = box::newBox($body,$title,$frameType);
		echo $qobj->createGetBox();
	}
	
	public function createViewNoFrame()
	{
		echo $this->getSimpleTitle();
		echo "<script type=\"text/javascript\">document.getElementById('" . $this->getTitleId() . "').style.display = 'none';</script>";
		echo $this->getBodyHtml();
		
	}
	public function isTimeFromOrderItem()
	{
		return $this->bTakeTimeFromOrderItem;	
	}
	
	abstract public function getHandleFunctions();
	
	//--------------------Helper functions-------------------/
	
	/**
	 * 
	 * @return unknown_type
	 */
	private function getTitleHtml()
	{
		$ttImage = $this->isDateFilter() ? "tooltip_blue.png" : "tooltip_red.png";
		
		$imgPath = JUri::root().'administrator/components/com_netbasevm_extend/assets/images/statistics/' . $ttImage;		
		
		$title = "<div><span id=\"" . $this->getTitleId() . "\" style=\"float: left;\">" . $this->strTitleName1 . "</span><div style=\"float: right;\">" . JHTML::tooltip($this->strToolTip, $this->strTitleName1, $imgPath, '', '') . "</div></div>";
		return $title;
	}
	
	private function getSimpleTitle()
	{
		$title = "<div><span id=\"" . $this->getTitleId() . "\" style=\"float: left;\">" . $this->strTitleName1 . "</span>";
		return $title;
	}
	
	private function getBodyHtml()
	{
		$widthStyle = $this->width != "" ? "width: " . $this->width : "";
		if($this->bAddSelectEvent == true )
		{
			if($this->strSelectType == "products")
				$selectControl = "<div id=\"" . $this->getSelectId() . "box\">Select product: ";
			else
				$selectControl = "<div id=\"" . $this->getSelectId() . "box\">";
			$selectControl .= "<select id=\"" . $this->getSelectId() . "\" onChange=\"" . $this->strClassName . "Param = this.options[selectedIndex].value;" . $this->getRefreshFunctionCall(true) . "\">\n";
			$selectControl .= $this->getOptions() . "\n";
			$selectControl .= "</select></div>\n";
		}
		else
			$selectControl = "";
		
		$body = $selectControl;
		$body .= "<div id=\"" . $this->getBodyId() . "\" style=\"height: " . $this->intFrameHeight . "px; overflow: " . $this->overflow . "; " . $widthStyle . "\"></div>";
		if(!$this->bSingleView)
		{
			$body .= "<span style=\"float: left;\"><a href=\"javascript:void(0)\" onclick=\"draw" . $this->strClassName . "(true);\">" . $this->strSwitchText . "</a></span>";
		}
		if($this->bAllowCsv)
		{
			$fromDateStr = $this->bDateFiltered == true ? "fromDate" : "'all'";
			$toDateStr = $this->bDateFiltered == true ? "toDate" : "'all'";
			$fors = $this->bSingleView == false ? "' + " . $this->strClassName . "ForS + '" : "' + '";
			$paramName = $this->strClassName . "Param";
			
			$body .= "<span style=\"float: right;\"><a href=\"javascript:void(0)\" onclick=\"location.href='" . $this->httpType.$this->host.$this->uri . "/components/com_netbasevm_extend/" . $this->strFeedDir . "/process.php?tqx=reqId:0;out:csv&fid=" . $this->strClassName . "&fors=" . $fors . "&fd=' + " . $fromDateStr . " + '&td=' + " . $toDateStr . " + '&param1=' + " . $paramName . ";\">" . JText::_('DOWNLOAD_AS_CSV') . "</a></span>";
		}
		
		return $body;
	}
	
	private function getOptions()
	{
		if($this->bAddSelectEvent == false)
			return "";
		
		$options = "<option disabled=\"disabled\" selected=\"selected\">Select...</option>\n";
		if($this->strSelectType == "products")
		{
			$db = JFactory::getDBO();
			if(VMRELEASE == 'OLD'){
				$sql = "SELECT DISTINCT order_item_name, product_id FROM #__vm_order_item";
			}
			else{
				$sql = "SELECT DISTINCT order_item_name, virtuemart_product_id FROM #__virtuemart_order_items";
			}
			$db->setQuery( $sql );
			$productsArray = $db->loadAssocList();
			foreach($productsArray as $productDetails)
			{
				$options .= "<option id=\"" . $this->strClassName . "_" . $productDetails['product_id'] . "\" value=\"" . $productDetails['order_item_name'] . "\" >" . $productDetails['order_item_name'] . "</option>\n";
			}
		}
		
		return $options;
	
	}
}

?>