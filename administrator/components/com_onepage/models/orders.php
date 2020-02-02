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
	jimport( 'joomla.application.component.model' );
	jimport( 'joomla.filesystem.file' );
	
	
    
  // Load the virtuemart main parse code
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
    require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
	
class JModelOrders extends OPCModel
{
 /**
   * Items total
   * @var integer
   */
  var $_total = null;
 
  /**
   * Pagination object
   * @var object
   */
  var $_pagination = null;
	var $_data = null;
	
    function __construct()
		{
			parent::__construct();

			 $mainframe = JFactory::getApplication(); 
		
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('com_onepage.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$mainframe->setUserState( "com_onepage.limit", $limit );
		$mainframe->setUserState( "com_onepage.limitstart", $limitstart );
		
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
		
		$keyword = JRequest::getVar('keyword', ''); 
		$mainframe->setUserState( "com_onepage.keyword", $keyword );
		
		$show = JRequest::getVar('show', '');
		$mainframe->setUserState( "com_onepage.show", $show );
		
		//$this->setState('keyword', $keyword);
		//$this->setState('show', $show);
		
		}
		function getTemplates()
		{
		  return array(); 
		}
		function _buildQuery() 
		{
	$db = JFactory::getDBO(); 			
      $mainframe = JFactory::getApplication(); 
      $ehelper = new OnepageTemplateHelper();
      if ($ehelper->columnExists('#__virtuemart_orders', 'track_num'))
      {
        $ups = 'o.track_num, ';
      }
      else 
       $ups = ''; 

	  $keyword = JRequest::getVar('keyword', '');
	  if (empty($keyword)) { 
	  $keyword = $mainframe->getUserStateFromRequest('com_onepage.keyword', 'keyword', '');
	  }
	  $show = JRequest::getVar('show', '');
	   if (empty($show)) { 
	  $show = $mainframe->getUserStateFromRequest('com_onepage.show', 'show', '');
	  }
	  
	  
	
	  
      $list  = "SELECT distinct o.virtuemart_order_id as order_id, o.order_status, o.order_pass, ".$ups." o.order_number, o.created_on, o.modified_on, o.order_total, o.order_currency, o.virtuemart_user_id as user_id,";
	  $list .= "u.first_name, u.last_name FROM #__virtuemart_orders as o, #__virtuemart_order_items as oi,  #__virtuemart_order_userinfos as u WHERE ";
	  $list .= ' o.virtuemart_order_id = u.virtuemart_order_id '; 
	  $list .= ' and o.virtuemart_order_id = oi.virtuemart_order_id '; 
	  $count = "SELECT count(*) as num_rows FROM #__virtuemart_orders, #__virtuemart_order_userinfos  ";
	  $q = " and (u.address_type = 'BT') ";
	  
	  $keyword = trim($keyword); 
	  
	  if (!empty($keyword)) {
        
		$kw = $keyword; 
		
		$keywords = array(); 
		if (stripos($keyword, ' ')!==false)
		{
		 $keywords = explode(' ', $keyword); 
		}
		 
		
		$q  .= " AND ("; 
		/*o.`virtuemart_order_id` LIKE '%$keyword%' ";
        $q .= "OR o.order_status LIKE '%$keyword%' ";
        $q .= "OR u.first_name LIKE '%$keyword%' ";
        $q .= "OR u.last_name LIKE '%$keyword%' ";
		
        
	*/
	
	$fulltext = false; 
	/*
	// full text tests did not end up welll
	if (stripos($keyword, ' ')!==false) {
	  $fulltext = true; 
	}
	*/
	
	
	$db = JFactory::getDBO(); 
  $q2 = 'select * from #__virtuemart_orders where 1 order by virtuemart_order_id desc limit 0,1'; 
  $db->setQuery($q2); 
  $o = $db->loadAssoc(); 
  
  $q2 = 'select * from #__virtuemart_order_items where 1 order by virtuemart_order_id desc limit 0,1'; 
  $db->setQuery($q2); 
  $oi = $db->loadAssoc(); 
  
  $q2 = 'select * from #__virtuemart_order_userinfos where 1 order by virtuemart_order_id desc limit 0,1'; 
  $db->setQuery($q2); 
  $ou = $db->loadAssoc(); 
  
  if ($fulltext) { 
  $tables = array('u'=>'virtuemart_order_userinfos', 'oi'=>'virtuemart_order_items', 'o'=>'virtuemart_orders'); 
  $full_text_cols = array(); 
  foreach ($tables as $k=>$table) { 
  $q2 = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$db->getPrefix().$table."'"; 
  $db->setQuery($q2); 
  $ou3 = $db->loadAssocList(); 
  
 // var_dump($ou3); 
  
  foreach ($ou3 as $k6=>$v)
  {
	  if (!empty($v['COLLATION_NAME'])) {
		$type = $v['COLLATION_NAME']; 
		if (empty($v['COLUMN_NAME'])) continue; 
		
		
		
		if (empty($full_text_cols[$type])) $full_text_cols[$type] = array(); 
		if (empty($full_text_cols[$type][$table])) $full_text_cols[$type][$table] = array(); 
		
		
		if (empty($full_text_cols2[$type])) $full_text_cols2[$type] = array(); 
		if (empty($full_text_cols2[$type][$table])) $full_text_cols2[$type][$table] = array(); 
	    //$full_text_cols[$type][] = '`#__'.$table.'`.`'.$v['COLUMN_NAME'].'`'; 
		$full_text_cols[$type][$table][] = '`'.$k.'`.`'.$v['COLUMN_NAME'].'`'; 
		$full_text_cols2[$type][$table][] = $v['COLUMN_NAME'];
	  }
  }
  }
  }
 
 
  
  
  //$from .= ' LEFT JOIN #__virtuemart_order_items AS oi ON o.virtuemart_order_id = oi.virtuemart_order_id '; 
  
  $all_cols = array(); 
  
  $qs = array(); $qs1 = array(); $qs2 = array(); $qs3 = array(); 
  foreach ($o as $key=>$v)
  {
   
   $qs3[] = ' `o`.`'.$key."` LIKE '%".$db->escape($kw)."' "; 
   foreach ($keywords as $i=>$kwd) { 
     $or_qs3[] = ' `o`.`'.$key."` LIKE '%".$db->escape($kwd)."%' "; 
   }
   /*
   if ($fulltext) {
     $all_cols[] = 'CAST(`o`.`'.$key.'` AS VARCHAR(700) CHARACTER SET utf8 )'; 
   }
   */
   
  }


  foreach ($oi as $key=>$v)
  {
   
   $qs3[] = ' `oi`.`'.$key."` LIKE '%".$db->escape($kw)."%' "; 
   
   foreach ($keywords as $i=>$kwd) { 
     $or_qs3[] = ' `oi`.`'.$key."` LIKE '%".$db->escape($kwd)."%' "; 
   }

   
  }

  foreach ($ou as $key=>$v)
  {
   
   $qs3[] = ' `u`.`'.$key."` LIKE '%".$db->escape($kw)."%' "; 
   
   foreach ($keywords as $i=>$kwd) { 
     $or_qs3[] = ' `u`.`'.$key."` LIKE '%".$db->escape($kwd)."%' "; 
    }
   
  }
  
  
  if (($fulltext) && (!empty($full_text_cols))) {
	 foreach ($full_text_cols as $t => $cols_t) 
	   foreach ($cols_t as $table => $cols)
	 {
		 $stop = false; 
		 $zq = ' MATCH ( '.implode(',', $cols)." ) AGAINST ('".$db->escape($keyword)."' IN BOOLEAN MODE ) "; 
		  $m = 'opc_fulltext_'.md5($zq); 
		  if (!$this->checkIndex('#__'.$table, $m))
		  {
			  if (!$this->addIndex('#__'.$table, $m, $full_text_cols2[$t][$table]))
			  {
				  $stop = true; 
			  }
		  }
		  if (!$stop)
		 $qs3[] = $zq; 
	 }
  }
  
  
  foreach ($qs3 as $k=>$qs3q)
  {
	  $qs3[$k] = ' ( '.$qs3q.') '; 
  }
  $qs3q = implode(' or ', $qs3); 
  
  if (!empty($or_qs3)) { 
  foreach ($or_qs3 as $k=>$qs3q2)
  {
	  $or_qs3[$k] = ' ( '.$qs3q2.') '; 
  }
  $qs3q2 = implode(' or ', $or_qs3); 
  
  $qs3q .= ' or '.$qs3q2; 
  }
  
   
   $where = ' ('.$qs3q.' ) '; 
   
   $q .= $where.") ";
  
   
   
   
  }
         
	
	
	
	$db = JFactory::getDBO(); 
	
	if (!empty($show)) {
	 if (mb_strlen($show)===1) {
	  $q .= " AND o.order_status = '".$db->escape($show)."'  ";
	 }
	}

	$q .= "ORDER BY o.created_on DESC ";
	
	
	$list .= $q;
	
	
	
	$debug = JRequest::getVar('debug', false); 
	
	if (!empty($debug)) { 
	
	$list = str_replace('#__', $db->getPrefix(), $list); 
	echo $list; 
	$db->setQuery($list); 
	try {
	$db->loadAssocList(); 
	}
	catch (RuntimeException $e) { 
	 $msg = (string)$e; 
	 echo "<br /><br />".$msg."<br /><br />"; 
	}
	die(); 
	}
	
	//echo $list; die(); 
	
	
	// . " LIMIT $limitstart, " . $limit;
	//$count .= $q;   
    $query = $list; //.$limit;
	return $query;
	/*
        $this->_db->setQuery($query); 

        $this->_data = $this->_db->loadObjectList(); 
        $this->_total = count( $this->_data ) ; 
   */
    
} 

private function checkIndex($table, $key)
	{
		$db = JFactory::getDBO(); 
		$q = 'show KEYS from `'.$table.'`'; 
	try
	{
	$db->setQuery($q); 
	$res = $db->loadAssocList(); 
	foreach ($res as $row)
	{
		
		if ($row['Key_name'] !== $key) continue; 
		$it = strtoupper($row['Index_type']); 
		if ($it === 'FULLTEXT')
		{
		
			return $row['Key_name']; 
		}
	}
	return false; 
	
	}
	catch (Exception $e)
	{
		
		return 0; 
	}
	return false; 
	}
	private function addIndex($table, $key, $cols)
	{
		
		foreach ($cols as $k=>$v)
		{
			$cols[$k] = '`'.$v.'`'; 
		}
		 $q = "ALTER TABLE `".$table."` ADD FULLTEXT `".$key."` (".implode(',', $cols).")";
		 $db = JFactory::getDBO(); 
		 try { 
		 $db->setQuery($q); 
		 $db->query(); 
		 
		 $e = $db->getErrorMsg(); 
		 }
		 catch (Exception $e) {
		  return false; 
		 }
		 if (!empty($e)) { return false; }
		 
		 return true; 
	}
	

	function datePicker($jsDateFormat, $name, $id, $date='', $placeholder='')
	{
		$display  = '<input class="datepicker-db" id="'.$id.'" type="hidden" name="'.$name.'" value="'.$date.'" />';
		
		$formatedDate = $date; //JFactory::getDate($date); 
		
		$display .= '<input id="'.$id.'_text" class="datepicker" type="text" value="'.$formatedDate.'" placeholder="'.$placeholder.'" />';
		

		// If exist exit
		
		$front = 'components/com_virtuemart/assets/';

		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
//<![CDATA[
			jQuery(document).ready( function($) {
			$("#'.$id.'_text").live( "focus", function() {
				$( this ).datepicker({
					changeMonth: true,
					changeYear: true,
					dateFormat:"'.$jsDateFormat.'",
					altField: $(this).prev(),
					altFormat: "yy-mm-dd"
				});
			});
			$(".js-date-reset").click(function() {
				$(this).prev("input").val("'.$placeholder.'").prev("input").val("0");
			});
		});
//]]>
		');
		
		if (OPCJ3)
		{
			JHtml::_('jquery.framework');
			JHtml::_('jquery.ui', array('datepicker'));
		}
		else
		{
		vmJsApi::js ('jquery.ui.core',FALSE,'',TRUE);
		vmJsApi::js ('jquery.ui.datepicker',FALSE,'',TRUE);

		vmJsApi::css ('jquery.ui.all',$front.'css/ui' ) ;
		
		$lg = JFactory::getLanguage();
		$lang = $lg->getTag();

		$existingLang = array("af","ar","ar-DZ","az","bg","bs","ca","cs","da","de","el","en-AU","en-GB","en-NZ","eo","es","et","eu","fa","fi","fo","fr","fr-CH","gl","he","hr","hu","hy","id","is","it","ja","ko","kz","lt","lv","ml","ms","nl","no","pl","pt","pt-BR","rm","ro","ru","sk","sl","sq","sr","sr-SR","sv","ta","th","tj","tr","uk","vi","zh-CN","zh-HK","zh-TW");
		if (!in_array ($lang, $existingLang)) {
			$lang = substr ($lang, 0, 2);
		}
		elseif (!in_array ($lang, $existingLang)) {
			$lang = "en-GB";
		}
		vmJsApi::js ('jquery.ui.datepicker-'.$lang, $front.'js/i18n' ) ;
		}
		return $display; 
	}

	function loadVirtuemart()
	{
	   if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		VmConfig::loadConfig();
		


    $jlang =JFactory::getLanguage();
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, 'en-GB', true);
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
	
	$jlang->load('com_virtuemart_orders', JPATH_SITE, 'en-GB', true);
    $jlang->load('com_virtuemart_orders', JPATH_SITE, $jlang->getDefault(), true);
	
	$jlang->load('com_virtuemart_shoppers', JPATH_SITE, 'en-GB', true);
    $jlang->load('com_virtuemart_shoppers', JPATH_SITE, $jlang->getDefault(), true);

	
	
    $jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, null, true);

	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php');
		
	OPCJavascript::loadJquery();  
	//vmJsApi::jQuery();
		
		if (!class_exists('AdminUIHelper'))
		 {
		  // require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php'); 
		 }
		 $front = JURI::root(true).'/components/com_virtuemart/assets/';
		$admin = JURI::root(true).'/administrator/components/com_virtuemart/assets/';
		$document = JFactory::getDocument();

		//loading defaut admin CSS
		$document->addStyleSheet($admin.'css/admin_ui.css');
		//$document->addStyleSheet($admin.'css/admin_menu.css');
		$document->addStyleSheet($admin.'css/admin.styles.css');
		$document->addStyleSheet($admin.'css/toolbar_images.css');
		$document->addStyleSheet($admin.'css/menu_images.css');
		$document->addStyleSheet($front.'css/chosen.css');
		$document->addStyleSheet($front.'css/vtip.css');
		$document->addStyleSheet($front.'css/jquery.fancybox-1.3.4.css');
		$document->addStyleSheet($front.'css/ui/jquery.ui.all.css');
		//$document->addStyleSheet($admin.'css/jqtransform.css');

		//loading defaut script

		$document->addScript($front.'js/fancybox/jquery.mousewheel-3.0.4.pack.js');
		$document->addScript($front.'js/fancybox/jquery.easing-1.3.pack.js');
		$document->addScript($front.'js/fancybox/jquery.fancybox-1.3.4.pack.js');
		$document->addScript($admin.'js/jquery.coookie.js');
		$document->addScript($front.'js/chosen.jquery.min.js');
		$document->addScript($admin.'js/vm2admin.js');

		$vm2string = "editImage: 'edit image',select_all_text: '".JText::_('COM_VIRTUEMART_DRDOWN_SELALL')."',select_some_options_text: '".JText::_('COM_VIRTUEMART_DRDOWN_AVA2ALL')."'" ;
		$document->addScriptDeclaration ( "
//<![CDATA[
		var tip_image='".JURI::root(true)."/components/com_virtuemart/assets/js/images/vtip_arrow.png';
		var vm2string ={".$vm2string."} ;
		 jQuery( function($) {

			$('dl#system-message').hide().slideDown(400);
			$('.virtuemart-admin-area .toggler').vm2admin('toggle');
			$('#admin-ui-menu').vm2admin('accordeon');
			if ( $('#admin-ui-tabs').length  ) {

				$('#admin-ui-tabs').vm2admin('tabs',virtuemartcookie).find('select').chosen({enable_select_all: true,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text}); 
			}

			$('#content-box [title]').vm2admin('tips',tip_image);
			$('.modal').fancybox();
			$('.reset-value').click( function(e){
				e.preventDefault();
				none = '';
				jQuery(this).parent().find('.ui-autocomplete-input').val(none);
				
			});

		});
//]]>
		");
		 
		 
		 
	}
	
	function eexport()
	{
	
	}
		
	function getData() 
  {
	
        // if data hasn't already been obtained, load it
       if (empty($this->_data)) {
            $query = $this->_buildQuery();
			$db = JFactory::getDBO(); 
			$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
			try {
			$this->_data = $db->loadObjectList(); 
			}
			catch (Exception $e) { 
			  $msg = (string)$e; 
			  JFactory::getApplication()->enqueueMessage($msg); 
			}
			$e = $db->getErrorMsg(); if (!empty($e)) {
				JFactory::getApplication()->enqueueMessage($e); 
				
				}
            //$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
            //if (empty($this->_data)) {echo $this->_db->getErrorMsg(); return;}  

        }
        return $this->_data;
  }
 function getTotal()
  {
	  
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
			//testing: 
			$db= JFactory::getDBO(); 
			$db->setQuery($query); 
			try {
			
			$db->loadAssoc(); 
			
			
			
			}
			catch (Exception $e) { 
			  $msg = (string)$e; 
			  JFactory::getApplication()->enqueueMessage(msg); 
			}
			$e = $db->getError(); 
            $this->_total = $this->_getListCount($query);    
            
        }
        
        return $this->_total;
  }
function getPagination()
  {
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
       
        return $this->_pagination;
  }

	function save()
	{
	 $db = JFactory::getDBO();
	 $data = JRequest::get('post');
	 $e = '';
	 //var_dump($data); 
	 foreach ($data as $key => $d)
	 {
	  $arr = explode('_', $key);
	  if (count($arr)>1)
	  $order_id = (int)$arr[count($arr)-1];
	//   var_dump($arr); 

	  if (isset($order_id))
	  if (is_numeric($order_id))
	  {
	  
	    if (isset($data['changed_'.$order_id]) && ($data['changed_'.$order_id]=='1'))
	    {
	    
	    if (strpos($key, 'order_status')===0)
	    {
	     $vars = array();
	     if (isset($data['notify_customer_'.$order_id]))
	     {
	     $vars['notify_customer'] = 'Y';
	     //echo $data['notify_customer_'.$order_id]; die();
	     
	     }
	     else $vars['notify_customer'] = '';
	     $vars['order_status'] = $data['order_status_'.$order_id];
	     //echo $data['order_status_'.$order_id]; die();
	     $vars['curr_order_status'] = $data['current_order_status_'.$order_id];
	     $vars['order_item_id'] = '';
	     $vars['order_number'] = $data['order_number_'.$order_id];
	     $vars['order_comment'] = '';
	     $vars['order_id'] = $order_id;
	     $vars['include_comment'] = '';
	     $q = "select virtuemart_vendor_id from #__virtuemart_vendors where 1";
	     
	     $db->setQuery($q);
	     $vendor_id = $db->loadResult();
		 /*
	     $_SESSION['ps_vendor_id'] = $vendor_id;
	     $ps_order = new ps_order;
	     
	   	 ob_start();  
     	 if (!$ps_order->order_status_update($vars))  $e .= 'Error updating order status of order '.$order_id.'<br />';
		 */
		 die('order update'); 
  		 $e .= ob_get_clean();
  		
	    }
	    }
	  }
	  
	 }
	 //if (!empty($msg)) {echo $msg; die(); }
	 //die('stom');
	 //die();
	 $_SESSION['msg'] = $e;
	 return true;
	}


}