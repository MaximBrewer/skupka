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

header('Content-Type: text/html; charset=utf-8');
   
class gvColCell
{
	protected $id;
	protected $label;
	protected $type;
	protected $pattern;
	
	public function __construct($id,$label,$type,$pattern="")
	{
		$this->setId($id);
		$this->setlabel($label);
		$this->setType($type);
		$this->setPattern($pattern);
	}
	
	public function setId($id)
	{
		$bRes = true;
		$reserved = array("and", "asc", "avg", "by", "count", "date", "datetime", "desc", "false", "format", "from", "group",	"is", "label", "limit", "max", "min", "not", "null", "offset" , "options", "or", "order", "pivot", "select", "sum", "timeofday", "timestamp", "true", "where");
		$replcae = array("and_id", "asc_id", "avg_id", "by_id", "count_id", "date_id", "datetime_id", "desc_id", "false_id", "format_id", "from_id", "group_id",	"is_id", "label_id", "limit_id", "max_id", "min_id", "not_id", "null_id", "offset_id" , "options_id", "or_id", "order_id", "pivot_id", "select_id", "sum_id", "timeofday_id", "timestamp_id", "true_id", "where_id");
		
		if((isset($id)) && (!is_null($id)))
			$this->id = str_replace($reserved, $replcae, $id);
		else
			$bRes = false;
			
		return $bRes;
	}
	
	public function setlabel($label)
	{
		$bRes = true;
		
		if((isset($label)) && (!is_null($label)))
			$this->label = $label;
		else
			$bRes = false;
			
		return $bRes;
	}
	
	public function setType($type)
	{
		$bRes = true;
		
		if((isset($type)) && (!is_null($type)))
		{
			switch ($type)
			{
				case "string":	
					$this->type = 'string';
					break;
				case "number":	
					$this->type = 'number';
					break;
				case "date":	
					$this->type = 'date';
					break;
				case "time":	
					$this->type = 'time';
					break;
				case "datetime":	
					$this->type = 'datetime';
					break;
				case "bool":	
					$this->type = 'boolean';
					break;
				default:
					$this->type = 'string';
			}
		}
		else
			$bRes = false;
			
		return $bRes;
	}
	public function setPattern($pattern)
	{
		$bRes = true;
		
		if((isset($pattern)) && (!is_null($pattern)))
			$this->pattern = $pattern;
		else
			$bRes = false;
			
		return $bRes;
	}
	
	public function __toString()
	{
		if(isset($this->type) && !is_null($this->type) && isset($this->label) && !is_null($this->label) && isset($this->id) && !is_null($this->id))
			$colCellStr = "{id:'" . $this->id . "',label:'" . $this->label . "',type:'" . $this->type . "',pattern:'" . $this->pattern . "'}";
		else
			$colCellStr = "Error: Some of the column properties are not set or null ";
		return $colCellStr;
	}
}

class gvRowCell
{
	protected $value;
	protected $fValue;
	protected $type;
	
	public function __construct($type=null, $value=null, $fValue=null)
	{
		$this->value = null;
		$this->fValue = null;
		$this->type = null;
		
		$this->setValue($value);
		$this->setfValue($fValue);
		$this->setType($type);
		
	}
	
	public function setValue($value)
	{
		if((isset($value)) && (!is_null($value)))
			$this->value = $value;
	}
	
	public function setfValue($fValue)
	{
		if((isset($fValue)) && (!is_null($fValue)))
			$this->fValue = $fValue;
	}
	
	public function setType($type)
	{
		if((isset($type)) && (!is_null($type)))
			$this->type = $type;
	}
	
	public function __toString()
	{
		if(is_null($this->value) && is_null($this->fValue))
			return "";
		
		$rowCellStr = "{v:";
		$rowCellStr .= $this->type == "string" ? "'" : "";
		if($this->value === true)
			$rowCellStr .= "true";
		elseif($this->value === false)
			$rowCellStr .= "false";
		else
			$rowCellStr .= $this->value;
		$rowCellStr .= $this->type == "string" ? "'" : "";
	
		if((isset($this->fValue)) && (!is_null($this->fValue)))
			$rowCellStr .= ",f:'" . $this->fValue . "'";

		$rowCellStr .= "}";
		
		return $rowCellStr;
	}
}

class gvColumns
{
	protected $columns = array();
	
	public function addColumn($id,$label,$type,$pattern="")
	{
		if(isset($type) && !is_null($type) && isset($label) && !is_null($label) && isset($id) && !is_null($id))
			array_push($this->columns,new gvColCell($id,$label,$type,$pattern));
		else
			return false;
	}
	
	public function __toString()
	{
		$columnStr = "cols: [";
		
		$cell = reset($this->columns);
		while($cell !== FALSE)
		{
			$columnStr .= $cell;
			$cell = next($this->columns);
			if($cell !== FALSE)
				$columnStr .= ",";
		}
		
		$columnStr .= "]";
		return $columnStr;
	}
}

class gvRow
{
	protected $rowCells = array();
	
	public function addRowCell($type, $value,$fValue)
	{
		array_push($this->rowCells,new gvRowCell($type, $value,$fValue));
	}
	
	public function addEmptyRowCell()
	{
		array_push($this->rowCells,new gvRowCell(null, null,null));
	}
	
	public function __toString()
	{
		$rowStr = "{c:[";
		
		$row = reset($this->rowCells);
		while($row !== FALSE)
		{
			$rowStr .= $row;
			$row = next($this->rowCells);
			if($row !== FALSE)
				$rowStr .= ",";
		}
		
		$rowStr .= "]}";
		return $rowStr;
	}
}

class gvStreamer
{
	protected $gvJsonStart = "";
	protected $gvJsonEnd = "";
	protected $gvColumns;
	protected $gvRow = array();
	protected $requestId;
	protected $signature;
	protected $status;
	
	public function __construct()
	{
	}
	
	public function init($tqx=null)
	{
		$this->gvJsonStart = "google.visualization.Query.setResponse({";
		$requestId = "0";
		$signature = $this->setSig();
		$this->gvJsonStart .= "version:'0.6',";
		$this->gvJsonStart .= "reqId:'" . $requestId . "',";
		$this->gvJsonStart .= "status:'ok',";
		$this->gvJsonStart .= "sig:'" . $signature . "',";
		$this->gvJsonStart .= "table:{";
		$this->gvJsonEnd = "}});";
		$this->gvColumns = new gvColumns();
	}
		
	public function addColumn($id,$label,$type,$pattern="")
	{
		$this->gvColumns->addColumn($id,$label,$type,$pattern);
	}
	
	public function addNewRow()
	{
		array_push($this->gvRow, new gvRow());
	}
	
	public function addEmptyCellToRow()
	{
		end($this->gvRow)->addEmptyRowCell();
	}
	
	public function addStringCellToRow($value,$fValue=null)
	{
		end($this->gvRow)->addRowCell("string", $value,$fValue);
	}
	
	public function addNumberCellToRow($value,$fValue=null)
	{
		end($this->gvRow)->addRowCell("number", $value,$fValue);
	}
	
	public function addDateCellToRow($year, $month, $day,$hour=0, $minutes=0, $seconds=0, $fValue=null)
	{
		//month in javascript is 0-11 
		if($month <= 0)
			$month = 0;
		else
		 $month -= 1;
		 
		end($this->gvRow)->addRowCell("date", "new Date(" . $year . "," . $month . "," . $day . "," . $hour . "," . $minutes . "," . $seconds . ")",$fValue);
	}
	
	public function addDatetimeCellToRow($year, $month, $day,$hour=0, $minutes=0, $seconds=0, $fValue=null)
	{
		//month in javascript is 0-11 
		if($month <= 0)
			$month = 0;
		else
		 $month -= 1;
		 
		end($this->gvRow)->addRowCell("datetime", "new Date(" . $year . "," . $month . "," . $day . "," . $hour . "," . $minutes . "," . $seconds . ")",$fValue);
	}
	
	public function addTimeCellToRow($hour, $minutes, $seconds=0, $milisec=0, $fValue=null)
	{
		end($this->gvRow)->addRowCell("time", "[" . $hour . "," . $minutes . "," . $seconds . "," . $milisec . "]",$fValue);
	}
	
	public function addBoolCellToRow($value,$fValue=null)
	{
		end($this->gvRow)->addRowCell("bool", $value,$fValue);
	}
	
	public function __toString()
	{
		$gvJsonStr = $this->gvJsonStart;
		$gvJsonStr .= $this->gvColumns;
		$gvJsonStr .= ",rows: [";
		$row = reset($this->gvRow);
		while($row !== FALSE)
		{
			$gvJsonStr .= $row;
			$row = next($this->gvRow);
			if($row !== FALSE)
				$gvJsonStr .= ",";				
		}
		
		$gvJsonStr .= "]";
		$gvJsonStr .= $this->gvJsonEnd;
		
		$whiteChars = array("\r", "\n", "\t");
		$gvJsonStr = str_replace($whiteChars, "", $gvJsonStr);

		return $gvJsonStr;
	}
	
	protected function setSig()
	{
		srand($this->make_seed());
		$randval1 = rand();
		$randval2 = rand();
		$randval3 = rand();
		
		return strval($randval1) . strval($randval2) . strval($randval3);
	}
	
	private function make_seed()
	{
	  list($usec, $sec) = explode(' ', microtime());
	  return (float) $sec + ((float) $usec * 100000);
	}	
}


?>
