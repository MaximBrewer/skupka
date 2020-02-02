<?php
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

	JToolBarHelper::Title('OPC Order Management' , 'generic.png');
	JToolBarHelper::save();
	JToolBarHelper::cancel();
	jimport('joomla.html.pane');
	$pane = OPCPane::getInstance('tabs', array('active'=>'px', 'startOffset'=>0));
	
	
$session = JFactory::getSession(); 
$msg = $session->get('msg', ''); 
	
if (!empty($msg))
{

?>
<div style="width:100%; border: solid 1px;">
<?php
$session->clear('msg'); 
$txt = $msg; 

$txt = str_replace('<div class="shop_info">', '', $txt);
$txt = str_replace('</div>', '', $txt);
$txt = str_replace('<div >', '', $txt);
echo $txt;
//$txt = str_replace('<br>', '', $txt);
?>
</div>
<?php
}
	
	
     
        
	JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
    JHTMLOPC::script('onepage_ajax.js', 'administrator/components/com_onepage/assets/js/', false);
    
// Load the virtuemart main parse code
	
	
    
  	
  	echo $pane->startPane('order_general');
	echo $pane->startPanel(JText::_('COM_VIRTUEMART_ORDERS'), 'px');

// missing variables:
	//$limitstart = JRequest::getVar('limitstart', 0);
	$limitstart  = $this->pagination->limitstart;
	$limit = $this->pagination->limit;
	$num_rows = $this->total;
	//$limit   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 50, 'int');
	//$limit = JRequest::getVar('limit', 50);
	$keyword = JRequest::getVar('keyword', '');
	$modulename = 'order';	
	//$ps_vendor_id = 1;
	//$ps_order_status = new ps_order_status;
	//$ps_html = new ps_html;
	//$GLOBALS('ps_order_status') = $ps_order_status;
	//$VM_LANG->load('order');
	//$db = new ps_DB;
	$db = JFactory::getDBO(); 
	$show = JRequest::getVar('show', '');


   // check if we have to load order list or order details:
/*
   $order_id = JRequest::getVar('order_id', 0);
   
   if ($order_id===0)
*/
   	$document = JFactory::getDocument();
	$style = '
	
	div.current {
	 float: left;
	 padding: 5 !important;
	 width: 98%;
	}
	div {
	 text-indent: 0;
	}
	dl {
	 margin-left: 0 !important;
	 padding: 0 !important;
	}
	dd {
	 margin-left: 0 !important;
	 padding: 0 !important;
	 width: 100%;
	}
	dd div {
	 margin-left: 0 !important;
	 padding-left: 0 !important;
	 text-indent: 0 !important;
	 
	 
	}
	div.current dd {
	 
	 padding-left:1px;
     padding-right:1px;
     margin-left:1px;
     margin-right:1px;
     text-indent:1px;
     float: left;
	}
	#vmMainPageOPC .tab-content {
	 overflow: hidden !important; 
	}
	
	';
	
	
   $document->addStyleDeclaration($style);

/**
* Rest of this file is a modified copy of order.order_list.php of virtuemart page file
*
* @version $Id: order.order_list.php 1958 2009-10-08 20:09:57Z soeren_nb $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
//mm_showMyFileName( __FILE__ );
//global $page;
//, $ps_order_status;

$show = JRequest::getVar('show', ''); 
//$pageNav = new JPagination( $this->total, $limit_start, $limit );
$pageNav = $this->pagination;
//require_once( CLASSPATH . "htmlTools.class.php" );

$ehelper = new OnepageTemplateHelper();
$templates = $ehelper->getExportTemplates('ALL', true);
if (!empty($templates))
{
?><a href="#" style='float: right;' onclick="javascript:return opShow('mytmps');"><img src="<?php echo $this->getUrl()."components/com_onepage/assets/img/pdf_button.png"; ?>" alt='Create' title='Create' /></a>
<div style="position: absolute; right: 20px; text-align: left; background-color: #CCCCCC; border: 1px solid; margin-top:30px; z-index: 99; display: none; clear: both;" id="mytmps">
<?php
foreach ($templates as $t)
{
 //if (empty($t['tid_special'] || (!empty($t['tid_ai']) && ($t['tid_special']=='1') && ($t['tid_
 echo "<a style='float: left;' href='#' onclick='javascript:return op_runCmd(\"sendXmlMulti\", this);' id='createpdf_".$t['tid']."' ><img src='".$this->getUrl()."components/com_onepage/assets/img/pdf_button.png' alt='Create ".$t['tid_name']."' title='Create ".$t['tid_name']."' />".$t['tid_name']."</a><br style='clear: both;'/>";
}
?></div><?php
}
$listObj = new listFactory( $this->pagination );
// end template export part
?>
<div style="text-align: center; margin-left: auto; margin-right: auto;">

<?php
//echo $this->pagination->getPagesLinks();
echo $this->pagination->getPagesCounter();
 $show = JRequest::getVar('show', '');
$keyword = JRequest::getVar('keyword', ''); 
$keyword = urlencode($keyword);  
foreach ($this->statuses as $k=>$s) {
if ($show !== $s['order_status_code']) { 
?> 
  <a href="index.php?view=orders&amp;option=com_onepage<?php if (!empty($keyword)) { echo '&amp;keyword='.$keyword; } ?>&amp;show=<?php echo $s['order_status_code']; ?>">
<?php } ?>
  <b><?php echo JText::_($s['order_status_name']); ?></b>
  <?php if ($show !== $s['order_status_code']) { 
  ?>
  </a>
  <?php } ?>
      | 
<?php 
} 
?>
    <a href="index.php?view=orders&amp;option=com_onepage&amp;show="><b>
    <?php echo JText::_('COM_VIRTUEMART_ALL') ?></b></a>
	
	
	
</div>
<form method="get" action="index.php" name="adminFormK">
<input type="text" placeholder="<?php echo addslashes(JText::_('JSEARCH_FILTER')); ?>" name="keyword" value="<?php echo addslashes(JRequest::getVar('keyword', '')); ?>" />
		<input type="hidden" name="task" id="task" value="display" />
		
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="limitstart" value="0" />
<?php 

if (!empty($show))
{
	?><input type="hidden" name="show" value="<?php echo $show; ?>" />
	<?php
}
?>
</form>
<br />


<form method="post" action="index.php?option=com_onepage&amp;view=orders<?php if (!empty($keyword)) { echo '&amp;keyword='.htmlentities($keyword); } ?>" name="adminForm" novalidate="novalidate" id="adminForm">
<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="save" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />
		<input type="hidden" name="cmd" id="cmd" value="" />
		<input type="hidden" name="noValidate" id="noValidate" value="0" />
		<input type="hidden" name="show" id="show" value="<?php echo addslashes(JRequest::getVar('show', '')); ?>" />
	
<?php

if (!empty($keyword)) {
		?><input type="hidden" name="keyword" value="<?php echo addslashes(htmlentities($keyword)); ?>" /><?php
}

$form_code = '';
$listObj->startTable();


$upsi = false; 
 

// these are the columns in the table
$checklimit = ($num_rows < $limit) ? $this->total : $limit;

$columns = Array(  "#" => "width=\"20\"", 
					"<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"checkAll(".count($this->items).")\" />" => "width=\"20\"",
					JText::_('COM_VIRTUEMART_ORDER_LIST_ID') => '',
					JText::_('COM_VIRTUEMART_NAME') => '',
					
					JText::_('COM_VIRTUEMART_PRINT') => '',
					JText::_('COM_VIRTUEMART_ORDER_CDATE') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_MDATE') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_STATUS') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_NOTIFY') => '',
					JText::_('COM_VIRTUEMART_ORDER_LIST_TOTAL') => '',
					'Referal' => "width=\"5%\""
				);

$listObj->writeTableHeader( $columns );
// so we can determine if shipping labels can be printed
$dbl = JFactory::getDBO();

//$db->query($list);
$i = 0;


//while ($db->next_record()) 
foreach ($this->items as $item)
{ 
    
	$listObj->newRow();
	
	// The row number
	$listObj->addCell( $pageNav->getRowOffset( $i ) );
		
	// The Checkbox
	$html_o = '<input type="checkbox" id="cb'.$i.'" name="order_id[]" value="'.$item->order_id.'" onclick="isChecked(this.checked);">'; 
	$listObj->addCell( $html_o );

	
	$order_id = $item->order_id;
	$url = 'index.php?option=com_onepage&amp;view=order_details&amp;order_id='.$order_id;
	$tmp_cell = '<a href="'.$url.'">'.sprintf("%08d", $order_id).'<br />'.$item->order_number."</a><br />";
	

	
	$listObj->addCell( $tmp_cell );

		
	$tmp_cell = $item->first_name.' '.$item->last_name;
	
	
		
	$tmp_cell = '<a href="'. $url .'">'.$tmp_cell.'</a>';
	
	
	$listObj->addCell( $tmp_cell );
	
	
	
	
	
	
	
	


	$print_url = juri::root () . 'index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=' . $item->order_id . '&order_number=' . $item->order_number . '&order_pass=' . $item->order_pass;
	$print_link = "<a  href=\"javascript:void window.open('$print_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
	$print_link .= '<span  class="hasTip print_32" title="' . JText::_ ('COM_VIRTUEMART_PRINT') . '">&nbsp;</span>';
	$print_link .= '</a>'; 
	
	
    $listObj->addCell( $print_link );
	// Creation Date
	$listObj->addCell( $item->created_on);
	// Last Modified Date
    $listObj->addCell( $item->modified_on);
	$order_id = $item->order_id;
    // Order Status Drop Down List
	$html = '<input type="hidden" name="changed_'.$order_id.'" id="changed_'.$order_id.'" value="0" />
	 <select name="order_status_'.$order_id.'"  style="width: 150px;" class="vm-chzn-select" onchange="return updateOrderStatus(this, '.$order_id.');" >'; 
		 
		   foreach ($this->statuses as $k=>$s)
		   {
		      $html .= '<option '; 
			  
			  
		      if ($s['order_status_code']== $item->order_status) $html .= ' selected="selected" '; 
		   
			  
			  $html .= ' value="'.$s['order_status_code'].'">'.JText::_($s['order_status_name']).'</option>'; 
		   }

		  
		 $html .= '
		 </select>'; 
		 
	
    //$html = $ps_order_status->getOrderStatusList($item->order_status, "onchange=\"document.adminForm.changed_$order_id.value='1';\"");
    //$html = str_replace('name="order_status"', 'name="order_status_'.$order_id.'"', $html);
	$listObj->addCell( $html );
		
	// Notify Customer checkbox
	$listObj->addCell( '<input type="checkbox" class="inputbox" name="notify_customer_'.$order_id.'" id="notify_customer_'.$order_id.'" value="1" />' 
				."" );
	
	$listObj->addCell($item->order_total);
	$ref = $this->getRefOrders($order_id);
	$listObj->addCell($ref);
	$i++; 
}

$listObj->writeTable();
$listObj->endTable();
echo '<br style="clear: both;" />';
echo '<table style="width: 100%;">';
echo '<tr><td>';
echo $this->pagination->getListFooter(  );
echo '</td></tr></table>';
echo $form_code.'</form>';

 

echo $pane->endPanel();
echo $pane->startPanel(JText::_('COM_ONEPAGE_EXPORTED_ITEMS'), 'ei');
$ehelper->listExports();
echo $pane->endPanel();


 echo $pane->startPanel(JText::_('COM_ONEPAGE_EXCELL_EXPORT'), 'ei2');
 JHTML::_('behavior.calendar');
 if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php'))
 {
 ?>
 <h3><?php echo JText::_('COM_ONEPAGE_FILTER_ORDER_EXPORT'); ?></h3>
 <form method="post" action="index.php" name="adminForm2">
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="eexport" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />
 <table>
 <tr>
  <th><?php echo JText::_('COM_ONEPAGE_FROM'); ?></th>
  <th><?php echo JText::_('COM_ONEPAGE_TO'); ?></th>
  <th><?php echo JText::_('COM_ONEPAGE_EXPORT'); ?></th>
 </tr>
 <tr>
 <td>
 <?php
 $cal = $this->model->datePicker('mm-dd-yy', 'startdate', 'startdate', '', JText::_('COM_ONEPAGE_FROM').'...'); 
 echo $cal; 
 ?>
 

</td>
<td>


<?php
 $cal = $this->model->datePicker('mm-dd-yy', 'enddate', 'enddate', '', JText::_('COM_ONEPAGE_TO').'...'); 
 echo $cal; 
 ?>
 

</td>
<td>
 <input type="submit" value="<?php echo JText::_('COM_ONEPAGE_EXPORT_ORDER_ITEMS_BY_DATE'); ?>" />
</td>
</tr>
<tr>
<td>
 <input class="inputbox" type="text" name="startid" placeholder="<?php echo JText::_('COM_ONEPAGE_FROM_ORDER_ID'); ?>"
	id="startid" size="25" maxlength="25"
value="" />
</td>
<td>
 <input class="inputbox" type="text" name="endid" placeholder="<?php echo JText::_('COM_ONEPAGE_TO_ORDER_ID'); ?>"
	id="endid" size="25" maxlength="25"
value="" />
</td>
<td>
 <input type="submit" value="<?php echo JText::_('COM_ONEPAGE_EXPORT_ORDER_ITEMS_BY_ORDERID'); ?>" />
</td>
</tr>

<tr>
<td colspan="2">
 <label for="export_all_history"><?php echo JText::_('COM_ONEPAGE_EXPORT_WHOLE_HISTORY'); ?></label>
</td>
<td>
 <input type="checkbox" value="1" id="export_all_history"   name="export_all_history" />
 
</td>
</tr>

</table>
</form>

<?php

if (!class_exists('ZipArchive')) 
{
	echo '<p style="color: red;">phpExcel requires php-zip library to be available. Please consult your hosting to enable php-zip for you.</p>'; 
}

?>

  <form method="post" action="index.php" name="adminFormX">
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="reinstallphpexcell" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />
		<input type="submit" name="submit" id="submit" value="<?php echo JText::_('COM_ONEAPAGE_DOWNLOAD_AND_REINSTALL'); ?>" />
		
  </form>

 <?php
 echo '<a href="index.php?option=com_onepage&amp;view=order_excell" style="float: left;">'.JText::_('COM_ONEPAGE_EXPORT_ALL_WITHOUT_DETAILS').'</a>';
 }
 else 
 {
 
 ?>
 <p><?php echo JText::_('COM_ONEPAGE_PHPEXCELL_NOTICE'); ?> <?php echo JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php' ?></p>
  <form method="post" action="index.php" name="adminFormX">
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="installphpexcell" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />
		<input type="submit" name="submit" id="submit" value="<?php echo JText::_('COM_ONEAPAGE_DOWNLOAD_AND_INSTALL'); ?>" />
		
  </form>
 
 <?php
 }
 
 echo $pane->endPanel();



echo $pane->startPanel(JText::_('COM_ONEPAGE_EXPORT_XML_EXPORT_TAB'), 'xmkl');
?>

<h3><?php echo JText::_('COM_ONEPAGE_EXPORT_XML_EXPORT_TAB'); ?></h3>

 <form method="post" action="index.php" name="adminForm3">
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="xmlexport" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="scrolly" id="scrolly" value="0" />
		<input type="hidden" name="op_curtab" id="op_curtab" value="0" />

 <?php
 $tids = ' <select name="selected_tid" >'; 
 $ci = 0; 
 foreach ($templates as $t)
 {
 //if (empty($t['tid_special'] || (!empty($t['tid_ai']) && ($t['tid_special']=='1') && ($t['tid_
  if (!empty($t['tid_type']))
  if ( $t['tid_type']=='ORDER_DATA_TXT')
  {
  $tids .= '<option value="'.$t['tid'].'">'.$t['tid_name'].'</option>';
  $ci++; 
  }
 }
 $tids .= '
 </select>'; 
 ?>
 <table>
 <tr>
  <th><?php echo JText::_('COM_ONEPAGE_FROM'); ?></th>
  <th><?php echo JText::_('COM_ONEPAGE_TO'); ?></th>
  <th><?php echo JText::_('COM_ONEPAGE_EXPORT'); ?></th>
 </tr>
 <?php if (!empty($ci)) { ?>
 <tr>
 <td colspan="3"><?php echo $tids; ?></td>
 </tr>
 
 <?php } ?>
 
 <tr class="row1" style="margin-top: 10px;"><td colspan="3"><?php echo JText::_('COM_ONEPAGE_EXPORT_ORDER_ITEMS_BY_DATE'); ?></td></tr>
 <tr>
 <td>
 <?php
 //$cal = vmJsApi::jDate('', 'startdateo', 'startdateo', true, '');  
 
 $cal = $this->model->datePicker('mm-dd-yy', 'startdateo', 'startdateo', '', JText::_('COM_ONEPAGE_FROM').'...'); 
					  
					  
					  
echo $cal; 
 ?>
 
</td>
<td>
 
<?php 
					  
$cal = $this->model->datePicker('mm-dd-yy', 'enddateo', 'enddateo', '', JText::_('COM_ONEPAGE_TO').'...'); 
echo $cal; 
?>

</td>
<td>
 <div  <?php //if (empty($ci)) echo ' style="display: none;" '; ?>><input type="submit" value="<?php echo JText::_('COM_ONEPAGE_EXPORT_ORDER_ITEMS_BY_DATE'); ?>" /></div>
</td>
</tr>
<tr class="row1" style="margin-top: 10px;"><td colspan="3"><?php echo JText::_('COM_ONEPAGE_EXPORT_ORDER_ITEMS_BY_ORDERID'); ?></td></tr>
<tr>
<td>
 <input class="inputbox" type="text" name="startid" 
	id="startid" size="25" maxlength="25" placeholder="<?php JText::_('COM_ONEPAGE_FROM_ORDER_ID'); ?>"
value="" />
</td>
<td>
 <input class="inputbox" type="text" name="endid" placeholder="<?php JText::_('COM_ONEPAGE_TO_ORDER_ID'); ?>"
	id="endid" size="25" maxlength="25"
value="" />
</td>
<td>
 <input   <?php //if (empty($tcount)) echo ' style="display: none;" '; ?> type="submit" value="<?php echo JText::_('COM_ONEPAGE_EXPORT_ORDER_ITEMS_BY_ORDERID'); ?>" />
</td>
</tr>
<tr class="row1" style="margin-top: 10px;">
<td colspan="3">
<input type="hidden" name="export_eu_csv" value="0" id="export_eu_csv" />

 <?php echo JText::_('COM_ONEPAGE_EXPORT_FOR_EU_VAT'); ?>
 <br />
 <input type="submit" value="<?php echo JText::_('COM_ONEPAGE_EXPORT_CSV'); ?>" onclick="javascript: adminForm3.export_eu_csv.value=1" />
 <?php
 
 ?>
</td>
 </tr>
</table>
</form>
<?php
echo $pane->endPanel();

echo $pane->endPane();


$processing_html = "<img id='status_img' src='".$this->getUrl()."components/com_onepage/assets/img/process.png' alt='' title='' />";
$error_html = "<img id='status_img' src='".$this->getUrl()."components/com_onepage/assets/img/remove.png' alt='' title='' />";
$created_html = "<img id='status_img' src='".$this->getUrl()."components/com_onepage/assets/img/pdf_button.png' alt='' title='' />";
$order_id = 0;
echo '<div id="debug_window" style="position: fixed; bottom: 0px; right: 0px; width: 30%; overflow:auto; height: 30%; background-color: transparent; color: black; font-size: 10px; text-align: right;"></div>';
echo '<script language="javascript" type="text/javascript">//<![CDATA[
		          		var opTimer = null;
		          		var opStop = false;
		          		var opTemplates = [];
						var focusedE = null;
						var timeOut = null;
						var tmpElement = null;
						var deb = document.getElementById("debug_window");							
						var op_params = "option=com_onepage&view=order_details&task=ajax&ajax=yes&order_number=0"; '."\n".'
						var op_url = "'.$this->ehelper->getUrl().'/administrator/index.php";
						var op_localid = null;
						var multiOrders = true;
							//]]></script>';



	




