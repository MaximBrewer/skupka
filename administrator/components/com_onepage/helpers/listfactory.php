<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* This file contains functions and classes for common html tasks
*
* @version $Id: htmlTools.class.php 2744 2011-02-18 16:44:08Z zanardi $
* @package VirtueMart
* @subpackage classes
* @copyright Copyright (C) 2004-2009 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*

*
* @package VirtueMart
* @subpackage Classes
* @author soeren
*
* Modified by stAn, rupostel.com
*/
class listFactory {

	/** @var int the number of columns in the table */
	var $columnCount = 0;
	/** @var array css classes for alternating rows (row0 and row1 ) */
	var $alternateColors;
	/** @var int The column number */
	var $x = -1;
	/** @var int The row number */
	var $y = -1;
	/** @var array The table cells */
	var $cells = Array();
	/** @var vmPageNavigation The Page Navigation Object */
	var $pageNav;
	/** @var int The smallest number of results that shows the page navigation */
	var $_resultsToShowPageNav = 6;
	
	function __construct( $pageNav=null ) {
		if( defined('_VM_IS_BACKEND')) {
			$this->alternateColors = array( 0 => 'row0', 1 => 'row1' );
		}
		else {
			$this->alternateColors = array( 0 => 'sectiontableentry1', 1 => 'sectiontableentry2' );
		}
		$this->pageNav = $pageNav;
	}
	
	/**
	* Writes the start of the button bar table
	*/
	function startTable() {
		?><script type="text/javascript"><!--
		function MM_swapImgRestore() { //v3.0
			var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
		} //-->
		</script>
		<table class="adminlist" width="100%">
		<?php
	}
	/**
	* writes the table header cells
	* Array $columnNames["Product Name"] = "class=\"small\" id=\"bla\"
	*/
	function writeTableHeader( $columnNames ) {
		if( !is_array( $columnNames ))
			$this->columnCount = intval( $columnNames );
		else {
			$this->columnCount = count( $columnNames );
			echo '<tr>';
			foreach( $columnNames as $name => $attributes ) {
				$name = html_entity_decode( $name );
				echo "<th class=\"title\" $attributes>$name</th>\n";
			}
			echo "</tr>\n";
		}
	}
	/**
	 * Adds a new row to the list
	 *
	 * @param string $class The additional CSS class name
	 * @param string $id The ID of the HTML tr element
	 * @param string $attributes Additional HTML attributes for the tr element
	 */
	function newRow( $class='', $id='', $attributes='') {
		$this->y++;
		$this->x = 0;
		if( $class != '') {
			$this->cells[$this->y]['class'] = $class;
		}
		if( $id != '') {
			$this->cells[$this->y]['id'] = $id;
		}
		if( $attributes != '' ) {
			$this->cells[$this->y]['attributes'] = $attributes;
		}
		
	}
	
	function addCell( $data, $attributes="" ) {
	
		$this->cells[$this->y][$this->x]["data"] = $data;
		$this->cells[$this->y][$this->x]["attributes"] = $attributes;
		
		$this->x++;
	}
	
	/** 
	* Writes a table row with data
	* Array 
	* $row[0]["data"] = "Cell Value";
	* $row[0]["attributes"] = "align=\"center\"";
	*/
	function writeTable() {
		if( !is_array( $this->cells ))
			return false;
		
		else {
			$i = 0;
			foreach( $this->cells as $row ) {
				echo "<tr class=\"".$this->alternateColors[$i];
				if( !empty($row['class'])) {
					echo ' '.$row['class'];
				}
				echo '"';
				if( !empty($row['id'])) {
					echo ' id="'.$row['id'].'" ';
				}
				if( !empty($row['attributes'])) {
					echo $row['attributes'];
				}
				echo ">\n";
				foreach( $row as $cell ) {
					if( $cell["data"] == 'i' || !isset( $cell["data"] ) || !is_array($cell)) continue;
					$value = $cell["data"];
					$attributes = $cell["attributes"];
					echo "<td  $attributes>$value</td>\n";
				}
				echo "</tr>\n";
				$i == 0 ? $i++ : $i--;
			}
		}
	}
	
	function endTable() {
		echo "</table>\n";
	}
	
	/**
	* This creates a header above the list table, containing a search box
	* @param The Label for the list (will be used as list heading!)
	* @param The core module name (e.g. "product")
	* @param The page name (e.g. "product_list" )
	* @param Additional varaibles to include as hidden input fields
	*/
	function writeSearchHeader( $title, $image="", $modulename, $pagename) {
	
		global $sess, $keyword, $VM_LANG;
	  
		if( !empty( $keyword )) {
			$keyword = urldecode( $keyword );
		}
		else {
			$keyword = "";
		}
		$search_date = JRequest::getVar('search_date', null); //vmGet( $_REQUEST, 'search_date', null);
		$show = JRequest::getVar('show', ''); //( $_REQUEST, "show", "" );
		
		$header = '<a name="listheader"></a>';
		$header .= '<form name="adminForm" action="'.$_SERVER['PHP_SELF'].'" method="post">
					
					<input type="hidden" name="option" value="'.VM_COMPONENT_NAME.'" />
					<input type="hidden" name="page" value="'. $modulename . '.' . $pagename . '" />
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="func" value="" />
					<input type="hidden" name="vmtoken" value="'.vmSpoofValue($sess->getSessionId()).'" />
					<input type="hidden" name="no_menu" value="'.vmRequest::getInt( 'no_menu' ).'" />
					<input type="hidden" name="no_toolbar" value="'.vmRequest::getInt('no_toolbar').'" />
					<input type="hidden" name="only_page" value="'.vmRequest::getInt('only_page').'" />
					<input type="hidden" name="boxchecked" />';
		if( defined( "_VM_IS_BACKEND") || @$_REQUEST['pshop_mode'] == "admin"  ) {
			$header .= "<input type=\"hidden\" name=\"pshop_mode\" value=\"admin\" />\n";
		}
        if(( $title != "" ) || !empty( $pagename )) {
			$header .= '<table><tr>';
			if( $title != "" ) {
				$style = ($image != '') ? 'style="background:url('.$image.') no-repeat;text-indent: 30px;line-height: 50px;"' : '';
				$header .= '<td><div class="header" '.$style.'><h2 style="margin: 0px;">'.$title.'</h2></div></td>'."\n";
				$GLOBALS['vm_mainframe']->setPageTitle( $title );
			}
		
			if( !empty( $pagename ))
				$header .= '<td width="20%">
				<input class="inputbox" type="text" size="25" name="keyword" value="'.shopMakeHtmlSafe($keyword).'" />
				<input class="button" type="submit" name="search" value="'.$VM_LANG->_('PHPSHOP_SEARCH_TITLE').'" />
				</td>';
			
			$header .= "\n</tr></table><br style=\"clear:both;\" />\n";
		}
		
		if ( !empty($search_date) ) // Changed search by date
			$header .= '<input type="hidden" name="search_date" value="'.$search_date.'" />';
		
		if( !empty($show) ) {
			$header .= "<input type=\"hidden\" name=\"show\" value=\"$show\" />\n";
		}
		
		echo $header;
	}

	/**
	* This creates a list footer (page navigation)
	* @param The core module name (e.g. "product")
	* @param The page name (e.g. "product_list" )
	* @param The Keyword from a search by keyword
	* @param Additional varaibles to include as hidden input fields
	*/
	function writeFooter($keyword, $extra="") {
		$footer= "";
		if( $this->pageNav !== null ) {
			if( $this->_resultsToShowPageNav <= $this->pageNav->total ) {
		
				$footer = $this->pageNav->getListFooter();
			}
		}
		else {
			$footer = "";
		}
			
		if(!empty( $extra )) {
			$extrafields = explode("&", $extra);
			array_shift($extrafields);
			foreach( $extrafields as $key => $value) {
				$field = explode("=", $value);
				$footer .= '<input type="hidden" name="'.$field[0].'" value="'.@shopMakeHtmlSafe($field[1]).'" />'."\n";
			}
		}
		$footer .= '</form>';
		
		echo $footer;
	}
}
