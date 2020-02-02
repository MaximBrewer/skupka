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

include_once("gvServerAPI.php");

class gvColCellEx extends gvColCell
{
	public function __construct($id,$label,$type,$pattern="")
	{
		parent::__construct($id,$label,$type,$pattern);
	}
	
	public function setType($type)
	{
		$bRes = true;
		
		if((isset($type)) && (!is_null($type)))
		{
			switch ($type)
			{
				case "string":	//mysql
				case "varchar": //postgresql,mysqli
				case "bpchar": //postgresql
				case "cidr": //postgresql
				case "inet": //postgresql
				case "macaddr": //postgresql
				case "text": //postgresql
				case "char": //mysqli
				case "enum": //mysqli
				case "set": //mysqli
				case "blob": //mysqli
				case "tinyblob": //mysqli
				case "mediumblob": //mysqli
				case "longblob": //mysqli
					$this->type = 'string';
					break;
				case "number": //mysql
				case "int":	//mysql,mysqli
				case "real": //mysql
				case "int2": //postgresql
				case "int4": //postgresql
				case "int8": //postgresql
				case "bit": //postgresql,mysqli
				case "varbit": //postgresql
				case "bytea": //postgresql
				case "float4": //postgresql
				case "float8": //postgresql
				case "interval": //postgresql
				case "money": //postgresql
				case "numeric": //postgresql
				case "tinyint": //mysqli
				case "smallint": //mysqli
				case "mediumint": //mysqli
				case "bigint": //mysqli
				case "float": //mysqli
				case "double": //mysqli
				case "decimal": //mysqli
					$this->type = 'number';
					break;
				case "date": //mysql, postgresql,mysqli	
				case "year": //mysql,mysqli
				case "timetz": //postgresql
				case "timestamptz": //postgresql
					$this->type = 'date';
					break;
				case "timestamp": //mysql,mysqli
				case "datetime": //mysql,mysqli
					$this->type = 'datetime';
					break;
				case "time": //mysql,postgresql,mysqli
					$this->type = 'timeofday';
					break;
				case "bool": //mysql, postgresql
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
	
	public function getColType()
	{
		return $this->type;
	}
	
	public function getCsvData()
	{
		if(isset($this->type) && !is_null($this->type) && isset($this->label) && !is_null($this->label) && isset($this->id) && !is_null($this->id))
			$colCellStr = $this->label;
		else
			$colCellStr = "Error: Some of the column properties are not set or null ";
		return $colCellStr;
	}
}

class gvRowCellEx extends gvRowCell
{
	public function __construct($type=null, $value=null, $fValue=null)
	{
		parent::__construct($type, $value, $fValue);
	}
	
	public function getCsvData($bRawValues = FALSE)
	{
		if(is_null($this->value) && is_null($this->fValue))
			return "";
		
		if((isset($this->fValue)) && (!is_null($this->fValue)) && !$bRawValues)
				$rowCellStr = $this->fValue;
		else
		{
			if($this->value === true)
				$rowCellStr = "true";
			elseif($this->value === false)
				$rowCellStr = "false";
			else
				$rowCellStr = $this->value;
		}
		
		if(strpos($rowCellStr,",") !== FALSE || strpos($rowCellStr,"\t"))
			$rowCellStr = "\"" . $rowCellStr . "\"";
		return $rowCellStr;
	}
}

class gvColumnsEx extends gvColumns
{
	public function addColumn($id,$label,$type,$pattern="")
	{
		if(isset($type) && !is_null($type) && isset($label) && !is_null($label) && isset($id) && !is_null($id))
			array_push($this->columns,new gvColCellEx($id,$label,$type,$pattern));
		else
			return false;
	}
	
	public function setColPattern($index, $pattern)
	{
		$bRes = true;
		
		if($index < 0 | $index >= count($this->columns))
			$bRes = false;
		else
			$this->columns[$index]->setPattern($pattern);
		
		return $bRes;
	}
	
	public function getColumnType($index)
	{
		if($index < 0 | $index >= count($this->columns))
			return false;
		else
			 return $this->columns[$index]->getColType();
	}
	
	public function getCsvData($delim)
	{
		$columnStr = "";
		
		$cell = reset($this->columns);
		while($cell !== FALSE)
		{
			$columnStr .= $cell->getCsvData();
			$cell = next($this->columns);
			if($cell !== FALSE)
				$columnStr .= $delim;
		}
		
		$columnStr .= "\n";
		return $columnStr;
	}
}

class gvRowEx extends gvRow
{
	public function addRowCell($type, $value,$fValue)
	{
		array_push($this->rowCells,new gvRowCellEx($type, $value,$fValue));
	}
	
	public function addEmptyRowCell()
	{
		array_push($this->rowCells,new gvRowCellEx(null, null,null));
	}
	
	public function getCsvData($delim,$bRawValues = FALSE)
	{
		$rowStr = "";
		
		$row = reset($this->rowCells);
		while($row !== FALSE)
		{
			$rowStr .= $row->getCsvData($bRawValues);
			$row = next($this->rowCells);
			if($row !== FALSE)
				$rowStr .= $delim;
		}
		
		$rowStr .= "\n";
		return $rowStr;
	}
}

class gvStreamerEx extends gvStreamer
{
	protected $reason;	
	protected $message;	
	protected $detailedMessage;	
	protected $gvJsonStatus;
	protected $gvJsonSignature;
	protected $hashSig;
	protected $version;
	protected $info;
	protected $out;
	protected $csvFile;
	
	public function __construct()
	{
		$this->requestId = "@@@@";
		$this->signature = "@@@@";
		$this->version = "0.6"; // since version parameter is not must in the request, we initialize it to the current version
		$this->info = "no"; // this parameter control whether to send information on the implementation
		$this->detailedMessage = "";
		$this->message = "";
		$this->out = "json";
		$this->csvFile = "";
		$this->csvRawValues = FALSE;
	}
	
	public function init($tqx=null, $resHandler=null)
	{
		//$handle = fopen("c:\qtx.txt", "w");
    //fwrite($handle, $tqx);
    //fclose($handle);
		$bRes = true;
		
		//do some intializations
		if($resHandler == null)
			$this->gvJsonStart = "google.visualization.Query.setResponse({version:'{VERSION}',";
		else
			$this->gvJsonStart = htmlspecialchars($resHandler,ENT_QUOTES,"UTF-8") . "({version:'{VEERSION}',";
		$this->gvJsonEnd = "}});";
		
		//if there is no tqx parameter - that's OK
		if(!isset($tqx) || is_null($tqx))
		{
			$this->requestId = 0;
			$this->status = "ok";
			//signature will be set at the end
		}
		else
		{
			//echo $tqx;
			//there is tqx parameter - validate it
			$params = explode(";", $tqx);
			$this->array_walk($params, "analyzeTqx");
			if($this->requestId == "@@@@")
			{
				//reqId is required and must be 
				$this->status = "error";
				$this->reason = "invalid_request";
				$this->message = "Invalid request";
				$this->detailedMessage = "Internal error(invalid-key)";
				$bRes =  false;
			}
			elseif($this->version != "0.5" && $this->version != "0.6")
			{
				//only version 0.5  and 0.6 is supported
				$this->status = "error";
				$this->reason = "not_supported";
				$this->message = "Version not supported";
				$this->detailedMessage = "This data source does not support the version reqested";
				$bRes =  false;
			}
			elseif($this->info == "yes")
			{
				//we just want to return a message about the protocol implementation
				$this->status = "error";
				$this->reason = "info";
				$this->message = "GVSP1.5"; // GV Streamer PHP v1.5
				$this->detailedMessage = "";
				$bRes =  false;
			}
			elseif($this->out == "tsv" && $this->version == "0.5")
			{
				//tsv output is only supported in version 0.6 or higher
				$this->status = "error";
				$this->reason = "not_supported";
				$this->message = "Output format not supported";
				$this->detailedMessage = "tsv output is only supported from version 0.6";
				$bRes =  false;
			}
			else
			{
				$this->status = "ok";
				//signature will be set at the end
			}
			
			if(($this->out == "csv" || $this->out == "tsv") && (!isset($this->csvFile) || $this->csvFile == ""))
				$this->csvFile = "dataSource";
		}
		$this->gvColumns = new gvColumnsEx();

		return $bRes;
	}
	
	public function addNewRow()
	{
		array_push($this->gvRow, new gvRowEx());
	}
	
	public function setColumnPattern($index, $pattern)
	{
		return  $this->gvColumns->setColPattern($index, $pattern);
	}
	
	public function getColumnType($index)
	{
		return  $this->gvColumns->getColumnType($index);
	}
	
	public function convertMysqlRes(&$result, $numberFormat=null, $dateFormat=null, $timeFormat=null, $datetimeFormat=null)
	{
		$bRes = true;
		$excludeIds = array_pad( array(), 1000, 0 );
		$colIdx = 0; 
		$bExcluded= false;
		
		//nothing to do if there is no mysql result
		if(!isset($result) || is_null($result))
			return false;
		
		//get number of fields (columns) and number of rows	
		$fields = mysql_num_fields($result);
		$rows   = mysql_num_rows($result);
	
		if($fields > 1000)
			return false;
		
		//go over the mysql result fields (columns) and insert columns
		for ($i=0; $i < $fields; $i++) 
		{
	    $type  = mysql_field_type($result, $i);
	    //$name  = mysql_field_name($result, $i);
	    $name  = htmlspecialchars(mysql_field_name($result, $i),ENT_QUOTES,"UTF-8");
	    
	    if(/*$type == "blob" ||*/ $type == "null" || $type == "unknown")
	    {
	    	//this types are not supported. we need to remember the indexes for later, when we'll go over the rows
	    	$excludeIds[$i] = 1;
	    	$bExcluded= true;
	    	continue;
	    }
	    $this->addColumn(strval($colIdx),$name, $type);
	    //$this->addColumn(chr($colIdx+65),$name, $type);
	    $colIdx++;
		}
		
		// go over the records and add each row
		while($row = mysql_fetch_array($result))
		{
			$this->addNewRow();
			$colIdx = 0;
			//for each row, go over the cells
			for ($i=0; $i < $fields; $i++)
			{
				//is this cell of type that is not supported?
				if($excludeIds[$i] == 1)
					continue;
				
				//$value = $row[$i];
				$value = htmlspecialchars($row[$i],ENT_QUOTES,"UTF-8");

				if(is_null($value))
					$this->addEmptyCellToRow();
				//for date and number columns, check if format is needed
				elseif($this->getColumnType($colIdx) == "date")
				{
					$value = strtotime($row[$i]);
					if(!is_null($dateFormat))
					{
						$fValue = date($dateFormat, $value);
					}
					else
						$fValue = null;
					$this->addDateCellToRow(date("Y", $value),date("n", $value), date("j", $value), date("G", $value), date("i", $value), date("s", $value), $fValue);
				}
				elseif($this->getColumnType($colIdx) == "datetime")
				{
					$value = strtotime($row[$i]);
					if(!is_null($datetimeFormat))
					{
						$fValue = date($datetimeFormat, $value);
					}
					else
						$fValue = null;
					$this->addDatetimeCellToRow(date("Y", $value),date("n", $value), date("j", $value), date("G", $value), date("i", $value), date("s", $value), $fValue);
				}
				elseif($this->getColumnType($colIdx) == "timeofday")
				{
					$value = strtotime($row[$i]);
					if(!is_null($timeFormat))
					{
						$fValue = date($timeFormat, $value);
					}
					else
						$fValue = null;
					$this->addTimeCellToRow(date("G", $value), date("i", $value), date("s", $value), 0, $fValue);
				}
				elseif($this->getColumnType($colIdx) == "number")
				{
					if(!is_null($numberFormat))
						$fValue = sprintf($numberFormat,$value);
					else
						$fValue = null;
					
					$this->addNumberCellToRow($value, $fValue);
				}
				elseif($this->getColumnType($colIdx) == "boolean")
				{
					$this->addBoolCellToRow($value);
				}
				else
				{
					//$this->addStringCellToRow(htmlspecialchars($value,ENT_QUOTES,"UTF-8"));
					$this->addStringCellToRow($value);
				}
				
				$colIdx++;
			}
		}
		
		if($bExcluded == true)
		{
			//some of the fields were of types that are not supported and therefore were truncated
			$this->addWarning("data_truncated", "Retrieved data was truncated", "Unprintable data was truncated");
		}
		return $bRes;
	}
	
	public function convertMysqliRes(&$result, $numberFormat=null, $dateFormat=null, $timeFormat=null, $datetimeFormat=null)
	{
           
		$bRes = true;
		$excludeIds = array_pad( array(), 1000, 0 );
		$colIdx = 0; 
		$bExcluded= false;
		$mysql_data_type_hash = array(
		    1=>'tinyint',
		    2=>'smallint',
		    3=>'int',
		    4=>'float',
		    5=>'double',
			6=>'null',
		    7=>'timestamp',
		    8=>'bigint',
		    9=>'mediumint',
		    10=>'date',
		    11=>'time',
		    12=>'datetime',
		    13=>'year',
			14=>'date',
		    16=>'bit',
			246=>'decimal',
			247=>'enum',
			248=>'set',
			249=>'tinyblob',
			250=>'mediumblob',
			251=>'longblob',
		    252=>'blob',
		    253=>'varchar',
		    254=>'char',
		    255=>'geometry'
		);
		
		//nothing to do if there is no mysql result
		if(!isset($result) || is_null($result))
			return false;
		
		//get number of fields (columns) and number of rows	
		$fields = $result->field_count;
		$rows   = $result->num_rows;
	
		if($fields > 1000)
			return false;
		
		//go over the mysql result fields (columns) and insert columns
		for ($i=0; $i < $fields; $i++) 
		{
		$result->field_seek($i);
		$finfo = $result->fetch_field();
	    $type  = isset($mysql_data_type_hash[$finfo->type]) ? $mysql_data_type_hash[$finfo->type] : "null";
		// echo $type . ",";
		// continue;
	    $name  = htmlspecialchars($finfo->name,ENT_QUOTES,"UTF-8");
	    
	    if($type == "geometry" || $type == "null" || $type == "unknown")
	    {
	    	//this types are not supported. we need to remember the indexes for later, when we'll go over the rows
	    	$excludeIds[$i] = 1;
	    	$bExcluded= true;
	    	continue;
	    }
	    $this->addColumn(strval($colIdx),$name, $type);
	    //$this->addColumn(chr($colIdx+65),$name, $type);
	    $colIdx++;
		}
		
		// go over the records and add each row
		while($row = $result->fetch_array())
		{
			$this->addNewRow();
			$colIdx = 0;
			//for each row, go over the cells
			for ($i=0; $i < $fields; $i++)
			{
				//is this cell of type that is not supported?
				if($excludeIds[$i] == 1)
					continue;
				
				//$value = $row[$i];
				$value = htmlspecialchars($row[$i],ENT_QUOTES,"UTF-8");
                              
				if(is_null($value))
					$this->addEmptyCellToRow();
				//for date and number columns, check if format is needed
                               
				elseif($this->getColumnType($colIdx) == "date")
				{
					$value = strtotime($row[$i]);
					if(!is_null($dateFormat))
					{
						$fValue = date($dateFormat, $value);
					}
					else
						$fValue = null;
					$this->addDateCellToRow(date("Y", $value),date("n", $value), date("j", $value), date("G", $value), date("i", $value), date("s", $value), $fValue);
				}
				elseif($this->getColumnType($colIdx) == "datetime")
				{
					$value = strtotime($row[$i]);
					if(!is_null($datetimeFormat))
					{
						$fValue = date($datetimeFormat, $value);
					}
					else
						$fValue = null;
					$this->addDatetimeCellToRow(date("Y", $value),date("n", $value), date("j", $value), date("G", $value), date("i", $value), date("s", $value), $fValue);
				}
				elseif($this->getColumnType($colIdx) == "timeofday")
				{
					$value = strtotime($row[$i]);
					if(!is_null($timeFormat))
					{
						$fValue = date($timeFormat, $value);
					}
					else
						$fValue = null;
					$this->addTimeCellToRow(date("G", $value), date("i", $value), date("s", $value), 0, $fValue);
				}
				elseif($this->getColumnType($colIdx) == "number")
				{
					if(!is_null($numberFormat))
						$fValue = sprintf($numberFormat,$value);
					else
						$fValue = null;
					
					$this->addNumberCellToRow($value, $fValue);
				}
				elseif($this->getColumnType($colIdx) == "boolean")
				{
					$this->addBoolCellToRow($value);
				}
				else
				{
					//$this->addStringCellToRow(htmlspecialchars($value,ENT_QUOTES,"UTF-8"));
					$this->addStringCellToRow($value);
				}
				
				$colIdx++;
			}
		}
		
		if($bExcluded == true)
		{
			//some of the fields were of types that are not supported and therefore were truncated
			$this->addWarning("data_truncated", "Retrieved data was truncated", "Unprintable data was truncated");
		}
		return $bRes;
	}
	
	public function convertPGRes(&$result, $numberFormat=null, $dateFormat=null, $timeFormat=null, $datetimeFormat=null)
	{
		$bRes = true;
		$excludeIds = array_pad( array(), 1000, 0 );
		$colIdx = 0; 
		$bExcluded= false;
		
		//nothing to do if there is no mysql result
		if(!isset($result) || is_null($result))
			return false;
		
		//get number of fields (columns) and number of rows	
		$fields = pg_num_fields($result);
		$rows   = pg_num_rows($result);
	
		if($fields > 1000)
			return false;
		
		//go over the mysql result fields (columns) and insert columns
		for ($i=0; $i < $fields; $i++) 
		{
	    $type  = pg_field_type($result, $i);
	    //$name  = mysql_field_name($result, $i);
	    $name  = htmlspecialchars(pg_field_name($result, $i),ENT_QUOTES,"UTF-8");
	    
	    if($type == "box" || $type == "circle" || $type == "line" || $type == "lseg" || $type == "path" || $type == "point" || $type == "polygon" || $type == "null" || $type == "unknown")
	    {
	    	//this types are not supported. we need to remember the indexes for later, when we'll go over the rows
	    	$excludeIds[$i] = 1;
	    	$bExcluded= true;
	    	continue;
	    }
	    $this->addColumn(strval($colIdx),$name, $type);
	    //$this->addColumn(chr($colIdx+65),$name, $type);
	    $colIdx++;
		}
		
		// go over the records and add each row
		while($row = pg_fetch_array($result))
		{
			$this->addNewRow();
			$colIdx = 0;
			//for each row, go over the cells
			for ($i=0; $i < $fields; $i++)
			{
				//is this cell of type that is not supported?
				if($excludeIds[$i] == 1)
					continue;
				
				//$value = $row[$i];
				$value = htmlspecialchars($row[$i],ENT_QUOTES,"UTF-8");
				if(is_null($value))
					$this->addEmptyCellToRow();
				//for date and number columns, check if format is needed
				elseif($this->getColumnType($colIdx) == "date")
				{
					$value = strtotime($row[$i]);
					if(!is_null($dateFormat))
					{
						$fValue = date($dateFormat, $value);
					}
					else
						$fValue = null;
					$this->addDateCellToRow(date("Y", $value),date("n", $value), date("j", $value), date("G", $value), date("i", $value), date("s", $value), $fValue);
				}
				elseif($this->getColumnType($colIdx) == "datetime")
				{
					$value = strtotime($row[$i]);
					if(!is_null($datetimeFormat))
					{
						$fValue = date($datetimeFormat, $value);
					}
					else
						$fValue = null;
					$this->addDatetimeCellToRow(date("Y", $value),date("n", $value), date("j", $value), date("G", $value), date("i", $value), date("s", $value), $fValue);
				}
				elseif($this->getColumnType($colIdx) == "timeofday")
				{
					$value = strtotime($row[$i]);
					if(!is_null($timeFormat))
					{
						$fValue = date($timeFormat, $value);
					}
					else
						$fValue = null;
					$this->addTimeCellToRow(date("G", $value), date("i", $value), date("s", $value), 0, $fValue);
				}
				elseif($this->getColumnType($colIdx) == "number")
				{
					if(!is_null($numberFormat))
						$fValue = sprintf($numberFormat,$value);
					else
						$fValue = null;
					
					$this->addNumberCellToRow($value, $fValue);
				}
				elseif($this->getColumnType($colIdx) == "boolean")
				{
					$this->addBoolCellToRow($value);
				}
				else
				{
					$this->addStringCellToRow(htmlspecialchars($value,ENT_QUOTES,"UTF-8"));
				}
				
				$colIdx++;
			}
		}
		
		if($bExcluded == true)
		{
			//some of the fields were of types that are not supported and therefore were truncated
			$this->addWarning("data_truncated", "Retrieved data was truncated", "Unprintable data was truncated");
		}
		return $bRes;
	}
	
	public function __toString()
	{
		if($this->out == "json")
		{
			$gvJsonStr = str_replace("{VERSION}",$this->version,$this->gvJsonStart);
			
			if($this->status == "ok" || $this->status == "warning")
			{
				$gvJsonStr2 = $this->gvColumns;
				$gvJsonStr2 .= ",rows: [";
				$row = reset($this->gvRow);
				while($row !== FALSE)
				{
					$gvJsonStr2 .= $row;
					$row = next($this->gvRow);
					if($row !== FALSE)
						$gvJsonStr2 .= ",";				
				}
				
				$gvJsonStr2 .= "]";
				$gvJsonStr2 .= $this->gvJsonEnd;
				
				$whiteChars = array("\r", "\n", "\t");
				$gvJsonStr2 = str_replace($whiteChars, "", $gvJsonStr2);
				
				$this->getHashSig();
				
				if($this->signature != $this->hashSig)			
				{
					$gvJsonStr .= "reqId:'" . $this->requestId . "',";	
					$gvJsonStr .= "status:'" . $this->status . "',";
					if($this->status == "warning")
					{
						$gvJsonStr .= "warnings:[{";
						$gvJsonStr .= "reason:'" . $this->reason . "'";	
						if($this->message != "")
							$gvJsonStr .= ",message:'" . $this->message . "'";	
						if($this->detailedMessage != "")
							$gvJsonStr .= ", detailed_message:'" . $this->detailedMessage . "'";	
						$gvJsonStr .= "}],";
					}
					$gvJsonStr .= "sig:'" . $this->hashSig . "',";
					$gvJsonStr .= "table:{";
					$gvJsonStr .= $gvJsonStr2;
				}
				else
				{
					$gvJsonStr .= "reqId:'0',status:'error',errors:[{reason:'not_modified',message:'Data not modified'}]});
";
				}
			}
			else
			{
				//some error occured along the way
				$gvJsonStr .= "reqId:'0',";	
				$gvJsonStr .= "status:'" . $this->status . "',";	
				$gvJsonStr .= "errors:[{";
				$gvJsonStr .= "reason:'" . $this->reason . "',";	
				$gvJsonStr .= "message:'" . $this->message . "'";	
				if($this->detailedMessage != "")
					$gvJsonStr .= ", detailed_message:'" . $this->detailedMessage . "'";	
				$gvJsonStr .= "}]});";
			}

			return $gvJsonStr;
		}
		elseif($this->out == "csv" || $this->out == "tsv")
		{
			$gvJsonStr = "";
			if($this->status == "ok" || $this->status == "warning")
			{
				$delim = $this->out == "csv" ? "," : "\t";
				$gvJsonStr = $this->gvColumns->getCsvData($delim);
				$row = reset($this->gvRow);
				while($row !== FALSE)
				{
					$gvJsonStr .= $row->getCsvData($delim,$this->csvRawValues);
					$row = next($this->gvRow);
				}
				$whiteChars = array("\r");
				$gvJsonStr = str_replace($whiteChars, "", $gvJsonStr);
			}
			else
			{
				$gvJsonStr = "Error occured while creating the data source\n";
				$gvJsonStr .= "status:," . $this->status . ",\n";	
				$gvJsonStr .= "reason:," . $this->reason . ",\n";	
				$gvJsonStr .= "message:," . $this->message . ",\n";	
				if($this->detailedMessage != "")
					$gvJsonStr .= "detailed_message:," . $this->detailedMessage . ",\n";	
			}
			// Headers for an download:
			$ext = $this->out == "csv" ? ".csv" : ".xls";
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $this->csvFile . $ext . '"'); 
			header('Content-Transfer-Encoding: binary');
			header("Cache-control: private"); //use this to open files directly
			return $gvJsonStr;
		}
		else
		{
			return "";
		}
	}
	
	public function getHashSig()
	{
		$this->hashSig = md5($this->gvColumns . $this->gvRow);
		return $this->hashSig ;
	}
	
	public function setReqId($reqId)
	{
		$this->requestId = $reqId;
	}
	
	public function createError($reason, $msg="", $detailedMsg = "")
	{
		$bRes = true;
		if(is_null($reason))
			$bRes = false;
		else
		{
			$this->status = "error";
			$this->reason = htmlspecialchars($reason,ENT_QUOTES,"UTF-8");
			$this->message = htmlspecialchars($msg,ENT_QUOTES,"UTF-8");
			$this->detailedMessage = htmlspecialchars($detailedMsg,ENT_QUOTES,"UTF-8");
		}
	}
	
	public function addWarning($reason, $msg="", $detailedMsg = "")
	{
		$bRes = true;
		if(is_null($reason))
			$bRes = false;
		else
		{
			$this->status = "warning";
			$this->reason = htmlspecialchars($reason,ENT_QUOTES,"UTF-8");
			$this->message = htmlspecialchars($msg,ENT_QUOTES,"UTF-8");
			$this->detailedMessage = htmlspecialchars($detailedMsg,ENT_QUOTES,"UTF-8");
		}
	}
	
	private function array_walk($input, $funcname) 
	{
    foreach ($input as $key => $value) $this->$funcname($value, $key);
  }
  
  private function analyzeTqx($item, $key)
	{
		$variabel = explode(":", $item);
		switch (trim($variabel[0]))
		{
			case "reqId":
				$this->requestId = $variabel[1];
				break;
			case "sig":
				$this->signature = $variabel[1];
				break;
			case "version":
				$this->version = $variabel[1];
				break;
			case "info":
				$this->info = $variabel[1];
				break;
			case "out":
				$this->out = $variabel[1];
				break;
			case "outFileName":
			case "csvFile":
				$this->csvFile = $variabel[1];
				break;
			case "csvRawValues":
				$this->csvRawValues = $variabel[1] == "yes" ? TRUE : FALSE;
				break;
		}
	}
}

?>
