<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.enviroment.browser' );



//$browser = JBrowser::getInstance();

/**
 * Example system plugin
 */
class plgSystemShopTracker extends JPlugin
{
        /**
         * Constructor
         *
         * For php4 compatability we must not use the __constructor as a constructor for plugins
         * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
         * This causes problems with cross-referencing necessary for the observer design pattern.
         *
         * @access      protected
         * @param       object  $subject The object to observe
         * @param       array   $config  An array that holds the plugin configuration
         * @since       1.0
         */
        public function __construct( &$subject, $config )
        {
                parent::__construct( $subject, $config );

                // Do some extra initialisation in this constructor if required
        }
		
		function plgVmConfirmedOrder($cart, $order) {
		   
		   $order_id = $order['details']['BT']->virtuemart_order_id; 
		   $this->registerCheckout($order_id, $order['details']['BT']->order_total); 
		}
		function registerCheckout($order_id, $order_total=0)
		{
			
			
	 
	 if (empty($order_id)) $order_id = JRequest::getVar('order_id', '');
	 if (empty($order_id))
	 {
	  $session = JFactory::getSession(); 
	  $myd = $session->get('myd', ''); 
	  if (!empty($myd))
	  {
	   $vars = $myd; 
	   if (!empty($vars['order_id'])) $order_id = $vars['order_id'];
	  }
	 }
	 
       
         
         
         
         if (!empty($order_id))
         {
 		
	    $db = JFactory::getDBO(); 
	    $order_id = $db->escape($order_id);
         // jos_partners_orders id, shop_id, order_id, order_total, ref_id
         // jos_partners_ref id, shop_id, order_id, title, url, non_sef_url, start, end
		 try
		 {
	   $q = "select order_id from #__partners_orders where order_id = '$order_id'";
	   $db->setQuery($q);
	   $res = $db->loadResult();
		 }
		 catch(Exception $e)
		 {
			 $this->install_tables(); 
			 return; 
		 }
	   
	   if (empty($res) || ($res===false))
	   {
		  
         $q = "insert into `#__partners_orders` (`shop_id`, `order_id`, `order_total`) VALUES ('0', '$order_id', '$order_total') "; 
         $db->setQuery($q);
         $db->query();
		 
         $id = $db->insertid();
		 
		 $session = JFactory::getSession(); 
		 $shop_tracker = $session->get('shop_tracker', array()); 
		
         if (!empty($shop_tracker))
         {
          foreach ($shop_tracker as $k=>$v)
          {
            $v['shop_id'] = 0; // current shop
            $v['order_id'] = $order_id;
            if (empty($v['end'])) $v['end'] = time();
//            $db->insertArray('INSERT', '#__partners_ref', $v);
			$q = "INSERT INTO `#__partners_ref` (`id`, `order_ref_id`, `title`, `url`, `non_sef_url`, `ref`, `start`, `end`) VALUES (NULL, '".(int)$id."', '".$db->escape($v['title'])."', '".$db->escape($v['url'])."', '".$db->escape($v['non_sef_url'])."', '".$db->escape($v['ref'])."', '".$db->escape($v['start'])."', '".$db->escape($v['end'])."') ";
	        //$db->buildQuery('INSERT', '#__partners_ref', $v);
            $db->setQuery($q);
            $db->query();
            $msg = $db->getErrorMsg();
            if (!empty($msg))
            {
				

            }
            
        
          }
		 
		 // we will not write it to the customer note
		  $session = JFactory::getSession(); 
		  $session->clear('shop_tracker'); 
           
         } // if set shop_tracker
         else 
         {
          // shop_tracker session variable is not set !!!
         }
		 
		 
         }
         }
		}
        /**
         * Do something onAfterInitialise 
         */
        function onAfterInitialise()
        {
			
		$mainframe = JFactory::getApplication(); 
		$tmp = JRequest::getVar('tmpl', ''); 
		$format = JRequest::getVar('format', ''); 
		// dont load for ajax pages
        if (!$mainframe->isAdmin() && ($format != 'raw') && ($tmp != 'component')) 
		{
         $this->track();
         

         }
		 
		 
        }
		function plgVmOnShowOrderBEPayment($virtuemart_order_id, $payment_method_id)
		{
			
			

			
		$mainframe = JFactory::getApplication(); 
		if (!$mainframe->isAdmin()) return; 
		
		
		$option = JRequest::getVar('option', ''); 
		$arr = array('com_virtuemart', 'com_onepage'); 
		if (!in_array($option, $arr)) return; 
		
		$view = JRequest::getVar('view', ''); 
		
		
		
	 $db = JFactory::getDBO();
	 
	 $q = "SELECT * FROM #__partners_orders where order_id = '".$virtuemart_order_id."' order by id desc LIMIT 0,1";
	 $db->setQuery($q);
	 $res = $db->loadAssocList();
	 $orders = array();
	 
	 if (empty($res)) return;
	 
	 $js = '
//<![CDATA[
function show_refs(id)
{

 var d = document.getElementById(\'order_ref_id_\'+id);
 if (d.style.display != \'none\')
 d.style.display = \'none\';
 else
 d.style.display = \'\';

 return false;
}
function submitbutton(task)
{

 var d = document.getElementById(\'task\');
 d.value = task;
 document.adminForm.submit();
 return true;
}
function addShop()
{
 //globals: num, new_shop_html
 var here = document.getElementById(\'here_\'+num);
 num++;
 here.innerHTML = new_shop_html.replace(/{n}/g, num.toString());
 return false;
}
function isChanged(caller, n)
{
 var d = document.getElementById(\'changed_new_\'+n);
 if (d != null)
 d.value = \'1\';
 else
 {
 var d = document.getElementById(\'changed_\'+n);
 d.value = \'1\';
 }
 
 if (caller != null)
 if (caller.name != null)
 {
  // existing shops
  if (n.toString().indexOf(\'e\')==0)
  {
    n = n.toString().replace(\'e\', \'\');
    if (caller.name == \'url_\'+n)
    {

     var d1 = document.getElementById(\'xml_\'+n); 
     if (d1 != null)
     {
      
      if (d1.value == \'\')
      {
       d1.value = caller.value+\'index.php?option=com_partners&view=client\';
      }
     }
    }
  }
  else
  {
     if (caller.name == \'url_new_\'+n)
     {
      var d1 = document.getElementById(\'xml_new_\'+n); 
      if (d1 != null)
      {
       if (d1.value == \'\')
       {
        d1.value = caller.value+\'index.php?option=com_partners&view=client\';
       }
      }
     }
  }
  
 }
 
 return true;
}
function changeById(id)
{
 var d = document.getElementById(id);
 if (id!=null)
 {
  d.value = \'1\';
 }
 return true;
}
function cChange(e, shop_id)
{
 
 var n = e.name;
 var d = document.getElementById(\'changed_\'+n);
 d.value = \'1\';
 
 isChanged(null, shop_id);
 return true;
}
function show_shop (id)
{
 var d = document.getElementById(\'shop_div_\'+id);
 if (d!=null)
 {
  if (d.style.display == \'none\') d.style.display = \'\';
  else d.style.display = \'none\';
 }
}
//]]>
'; 
	 
	 JFactory::getDocument()->addScriptDeclaration($js); 
	 foreach($res as $val)
	 {
	  
	  $order_id = $val['order_id'];
	  $orders[$order_id] = array();
	  
	  $q = "select first_name, last_name from #__virtuemart_order_userinfos where virtuemart_order_id = '".$order_id."' and address_type = 'BT' limit 0,1 ";
	  $db->setQuery($q);
	  $row = $db->loadAssoc();
	  $orders[$order_id]['first_name'] = $row['first_name'];
	  $orders[$order_id]['last_name'] = $row['last_name'];
	  $orders[$order_id]['order_total'] = $val['order_total'];
	  $q = "select * from #__partners_ref where order_ref_id = '".$val['id']."' order by start asc";
	  $db->setQuery($q); 
	  $data = $db->loadAssocList();
	  if (empty($data)) return ''; 
	  foreach ($data as $k)
	  {
	   $arr = array();
	   
	   $arr['title'] = $k['title'];
	   $arr['url'] = $k['url'];
	   $arr['ref'] = $k['ref'];
	   $start = $k['start'];
	   $end = $k['end'];
	   if (!empty($start) && (!empty($end)))
	   {
	    $time = $end-$start;
	    $time = number_format($time, 0).' sec ';
	    $arr['time'] = $time;
	   }
	   else $arr['time'] = 'Unknown';
	   if (empty($orders[$order_id]['ref']))
	   {
	    $orders[$order_id]['ref'] = array();
	    $orders[$order_id]['ref'][] = $arr;
	   }
	   else
	     $orders[$order_id]['ref'][] = $arr;
	  }
	 }
	 
	
			
			$html = ''; 
			$html .= '<fieldset class="adminform">';
        $html .= '<legend>Order Tracking Details</legend>';
    
foreach($orders as $order_id => $order)
{
 if (!empty($order['ref']))
 if (!empty($order['ref'][0]))
 if (!empty($order['ref'][0]['ref']))
 $ref = $order['ref'][0]['ref'];
 if (empty($ref)) $ref = '';
 $ref = urldecode($ref);
 $ref = str_replace('https://', '', $ref);
 $ref = str_replace('http://', '', $ref);
 
 $p1 = strpos($ref, '/');
 if ($p1 > 0)
 {
  $ref = substr($ref, 0, $p1);
 }
 $html .= '<a style="font-size: 130%; line-height: 150%;" href="#" onclick="javascript:return(show_refs('.$order_id.'));">'.$order_id.' '.$order['first_name'].' '.$order['last_name'].' | '.$ref.'</a><br class="clear: both;"/>';
 $html .= '<div style="display: none;" id="order_ref_id_'.$order_id.'">';
 
 foreach ($order['ref'] as $ind=>$ref)
 {
	 
   if (empty($ref['title'])) $ref['title'] = urldecode($ref['url']);
   $html .= '<span style="padding-left: 100px;">'.$ref['time'].' <a href="'.urldecode($ref['url']).'" target="_blank">'.urldecode($ref['title']).'</a></span>';
   $html .= '<br style="clear: both;"/>';
   if (strpos($ref['ref'], 'http')!==false)
   if ($ind>0)
   {
	if ($ref['ref'] != $orders[$order_id]['ref'][$ind-1]['url'])
	{
    $html .= '<span style="padding-left: 10%;">Ref: <a href="'.urldecode($ref['ref']).'" target="_blank">'.urldecode($ref['ref']).'</a></span>';
      $html .= '<br style="clear: both;"/>';
    }
   }
  else
 {
   $html .= '<span style="padding-left: 10%;">Ref: <a href="'.urldecode($ref['ref']).'" target="_blank">'.urldecode($ref['ref']).'</a></span>'; 
   $html .= '<br style="clear: both;"/>';
 }  
 
   
 }
 $html .= '</div>';
}



$html .= '</fieldset>'; 

			 return $html; 
		}
		
		
		function onExtensionAfterSave($tes2, $test)
	{
	
	  if (empty($test)) return; 
	  if (!is_object($test)) return; 
	 
	  if (!(($test->element === 'shoptracker')))
	  return; 
  
  

		 $db = JFactory::getDBO(); 
		 $q = "SHOW TABLES LIKE '".$db->getPrefix()."partners_orders'";
         
         $db->setQuery($q);
         $res = $db->loadResult();
         if (empty($res) || ($res === false))
         {
          $this->install_tables();
         } 
         else
         {
			 $session = JFactory::getSession(); 
		     $session->set('shop_tracker_tables_created', true); 
          
         }
  
	}
		
        /**
         * Do something onAfterRoute 
         */
        function onAfterRoute()
        {
			$mainframe = JFactory::getApplication(); 
			 if ($mainframe->isAdmin())
			{
		 // check if tables are created
		 $session = JFactory::getSession(); 
		 $shop_tracker_tables_created = $session->get('shop_tracker_tables_created', false); 
		 
         if (empty($shop_tracker_tables_created))
         {
		 $option = JRequest::getVar('option');
		 if ($option == 'com_plugins')
		 {
         $db = JFactory::getDBO(); 
		 $prefix = $db->getPrefix(); 
		 
		 $q = "SHOW TABLES LIKE '".$db->getPrefix()."partners_orders'";
        
         $db->setQuery($q);
         $res = $db->loadResult();
         if (empty($res) || ($res === false))
         {
          $this->install_tables();
         } 
         else
         {
		  $session->set('shop_tracker_tables_created', true); 
          
         }
		     }
		 }
		 }
        }

        

		function track()
		{
		         $non_sef =  ''; //JURI::getInstance()->_vars;
         
         $url = urlencode(JURI::current());
	 	
         if (stripos($url, 'administrator')===false)
		 if (stripos($url, 'format=')===false)
         {
        
          $mainframe = JFactory::getApplication(); 
      //    include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'ps_database.php');          
          $db = JFactory::getDBO(); 

          //$output = JResponse::getBody();
          //if (stripos($output, '<html')!==false)
          {
	          $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $my_arr = array();
          $my_arr['title'] = ''; 
          $my_arr['url'] = urlencode($actual_link);
          $my_arr['non_sef_url'] = '';
	      $my_arr['start'] = time();
	      $my_arr['end'] = '';
	      
		   

	  if (isset($_SERVER['HTTP_REFERER']))
	  $my_arr['ref'] = urlencode($_SERVER['HTTP_REFERER']);
	  else $my_arr['ref'] = 'Direct or not set';
	  
	 // if (isset($_SESSION['shop_tracker']))
	  $session = JFactory::getSession(); 
	  $data = $session->get('shop_tracker', array()); 
	  if (!empty($data))
	  {
	   //$data = $session->get('shop_tracker', array(), 'shop_tracker');
	   
	   $ind = count($data)-1; 
	   $last = $data[$ind];
	   //$last = $data[count($data)-1];
	   if ($last['url'] != $my_arr['url'])
	   {
		 
	    $data[$ind]['end'] = time();
	    $data[] = $my_arr;
	    
	   }
	   else 
	   {
	     
	    // wait for another click
	   }
	  }
	  else
	  {
	   $data = array();
	   $orig = JRequest::getVar('ref_partner');
	   if (!empty($orig))
	   {
	    $orig = urlencode(urldecode($orig));
	    $arr1 = array();
	    
	    $arr1['title'] = urlencode('RuposTel Marketing Systems');
        
        if (isset($_SERVER['HTTP_REFERER']))
	  	$arr1['url'] = urlencode($_SERVER['HTTP_REFERER']);
	  	else $arr1['url'] = 'RuposTel Systems';
        $arr1['non_sef_url'] = '';
	    $arr1['start'] = time();
	    $arr1['end'] = time();
		$arr1['ref'] = $orig;
		unset($_REQUEST['ref_partner']);
		$data[] = $arr1;
	   }
	   
	   $data[] = $my_arr;
	  
	   /*
	   $_SESSION['shop_tracker'] = array();
	   $_SESSION['shop_tracker'][] = $my_arr;
	   */
	  }
	  if (!empty($data))
	  $session->set('shop_tracker', $data); 
          
        }
        
        {
			//output is empty !!!
        }
        
       
		}
		
		
		
		}
        /**
         * Do something onAfterRender 
         */
        function onAfterRender()
        {
			$session = JFactory::getSession(); 
			$shop_tracker = $session->get('shop_tracker', array()); 
			
		 if (!empty($shop_tracker))
         {
           $mainframe = JFactory::getApplication(); 
		  //init joomla session
		  $session = JFactory::getSession(); 
		  if (method_exists($mainframe, 'getPageTitle'))
		  {
		    $shop_tracker[count($shop_tracker)-1]['title'] = urlencode($mainframe->getPageTitle());
			$session->set('shop_tracker', $shop_tracker); 
		  }
         }
        }
        
  
  function install_tables()
  {
    $mainframe = JFactory::getApplication(); 
 $db =  JFactory::getDBO();
 $prefix = $db->getPrefix();
 $q = array();
 $q[] = "CREATE TABLE IF NOT EXISTS `".$prefix."partners_orders` (
  `id` bigint(20) NOT NULL auto_increment,
  `shop_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_total` float NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ; ";
$q[] = " 
CREATE TABLE IF NOT EXISTS `".$prefix."partners_products` (
  `internal_id` int(11) NOT NULL auto_increment,
  `shop_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_url` varchar(512) NOT NULL,
  PRIMARY KEY  (`internal_id`),
  KEY `shop_id` (`shop_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ; ";
$q[] = " 
CREATE TABLE IF NOT EXISTS `".$prefix."partners_ref` (
  `id` bigint(20) NOT NULL auto_increment,
  `order_ref_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(512) NOT NULL,
  `non_sef_url` varchar(512) NOT NULL,
  `ref` varchar(1024) NOT NULL,
  `start` bigint(20) NOT NULL,
  `end` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `shop_id` (`order_ref_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ; ";
$q[] = "
 CREATE TABLE IF NOT EXISTS `".$prefix."partners_shops` (
  `id` int(11) NOT NULL auto_increment,
  `xml` varchar(512) NOT NULL,
  `name` varchar(200) NOT NULL,
  `min_price` float NOT NULL,
  `filter` text NOT NULL,
  `active` varchar(1) NOT NULL default 'N',
  `url` varchar(512) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ; ";
$q[] = "
CREATE TABLE IF NOT EXISTS `".$prefix."partners_statistics` (
  `id` bigint(20) NOT NULL auto_increment,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ref` varchar(512) NOT NULL,
  `internal_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ; ";
 foreach ($q as $query)
 {
	 try
	 {
       $db->setQuery($query);
       $db->query();
       $msg = $db->getErrorMsg();
	 }
	 catch(Exception $e)
	 {
		 
	 }
  
 if (!empty($msg)) {
	   return false; 
	 }
 
 }
 
 $session = JFactory::getSession(); 
 $session->set('shop_tracker_tables_created', true); 
 
    
  }
  
  
}

