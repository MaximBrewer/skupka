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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
 
 

class JControllerOrder_details extends JControllerBase
{	
   
   function getViewName() 
	{
	/*
	  $view = JRequest::getVar('view', 'order_details');
	  return $view;
	 */
		return 'order_details';		
	} 
   function getModelName() 
	{		
	
		return 'order_details';
	}
	
	
	function showFile($order_id=0, $fid=0)
	{
	 require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
	 $ehelper = new OnepageTemplateHelper($order_id);
	 $order_id = JRequest::getVar('orderid');
	 $fid = JRequest::getVar('fid');
	  $ehelper->showFile($order_id, $fid);
	 /*
	
	 @ob_get_clean();@ob_get_clean();@ob_get_clean();@ob_get_clean();@ob_get_clean();

	 
	 // autorization should be here!
	 $ehelper = new OnepageTemplateHelper();
	 $data = $ehelper->getExportItemFile($fid);
	 if (!empty($data))
	 {
	  $pdf = urldecode($data['path']);
	  if (file_exists($pdf))
	  {
	  $pi = pathinfo($pdf);
	  $filename = $pi['basename'];
	  header('Content-type: application/pdf');
	  header('Content-Disposition: attachment; filename="'.$filename.'"');
	  header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	  readfile($pdf);
	  die();
	  }
	 }
	 die('Cannot find the requested file!');
	 */
	}
	
	
	function sendXml()
	{
	 //test URL: https://php7.rupostel.com/purity/administrator/index.php?option=com_onepage&view=order_details&order_id=1062&task=sendXml&tid=102
	 //echo 'Processing...<br />';
	 $order_id = JRequest::getVar('orderid');
	 $tid = JRequest::getVar('tid');
	 $data = JRequest::get('post');
	 $specials = array();
	 foreach ($data as $k=>$d)
	  {
	   if (strpos($k, 'specialentry_')!==false)
	   {
	    $data = explode('_', $k);
	    // 0: specialentry, 1: tid, 2: specialentry id
	    $specials[$data[2]] = $d;
	   }
	  }
	  
	  require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
	 $ehelper = new OnepageTemplateHelper($order_id);
	 
	 $ehelper->processTemplate($tid, $order_id, $specials);
	 
	 $ehelper->checkFile();
       
		$mainframe = JFactory::getApplication();
	    $mainframe->close();
		
	}
   function sendXmlMulti()
   {
	   
	   
    //echo 'Sending Multi Order Request... <br />';
    $tid = JRequest::getVar('tid');
    if (!is_numeric($tid)) die();
    $tid = (int)$tid; 
	
    $data = JRequest::get('post');
    $enum = 0;
	require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
    $ehelper = new OnepageTemplateHelper(); 
    $tt = $ehelper->getTemplate($tid);
    $ra = array();
    $localid = '';
    if ($tt['tid_type'] == 'ORDERS')
    {
    foreach ($data as $k=>$v)
    {
     //echo '<br />'.$k.' '.$v;
     if (strpos($k, 'selectedorder')!==false)
     {
       //echo 'Order: '.$v.'<br />';
       $ra[] = $ehelper->getOrderDataEx($tid, $v, null, $enum);
       $enum++;
       //$ra = array_merge($ra, $arr);
       if (!empty($localid)) $localid .= '_'.$v;
       else 
       $localid .= $v;
     }
    }
    $oa = array();
    foreach ($ra as $va)
     foreach ($va as $key=>$val)
    {
     $oa[$key] = $val;
    }
	
    $ehelper->setStatus($tid, $localid, 'PROCESSING');
    $xml = $ehelper->getXml($tid, $localid, $oa);
    $hash = $ehelper->getFileHash($tid);
    $XPost = '&xml='.urlencode((string)$xml);
    $ehelper->sendData($XPost, $tid, $localid);
    //file_put_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.'temp.xml', $xml);
    }
    else
    if ($tt['tid_type'] == 'ORDER_DATA')
    {
     foreach ($data as $k=>$v)
    {
     //echo '<br />'.$k.' '.$v;
     if (strpos($k, 'selectedorder')!==false)
     {
       //echo 'Order: '.$v.'<br />';
       $localid = (int)$v;
	   $ehelper->setStatus($tid, $localid, 'PROCESSING');
	  
	   $ra[] = $ehelper->getOrderDataEx($tid, $v);
       
	  
       
       $xml = $ehelper->getXml($tid, $localid);
	   
	    
	   
       $hash = $ehelper->getFileHash($tid);
	   
	  
	   
       $XPost = '&xml='.urlencode((string)$xml);
       $ehelper->sendData($XPost);
	   
	     
    //file_put_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.'temp.xml', $xml);

     }
    }
    }
    else
    if ($tt['tid_type'] == 'ORDER_DATA_TXT')
    {
   
     foreach ($data as $k=>$v)
    {
     //echo '<br />'.$k.' '.$v;
     if (strpos($k, 'selectedorder')!==false)
     {
		$order_id = (int)$v; 

       $ra = $ehelper->getOrderDataEx($tid, $order_id);
       $localid = $order_id;
       $ehelper->processTxtTemplate($tid, $order_id, $ra);
	   //echo 'som tu';
	   

     }
    } 
    } elseif ($tt['tid_type'] === 'ORDERS_TXT') {
	$data = JRequest::get();
	
	$order_ids = array(); 	
		foreach ($data as $k=>$v)
    {
     //echo '<br />'.$k.' '.$v;
	 
     if (strpos($k, 'selectedorder')!==false)
     {
		 
	   $order_id = (int)$v; 
	   $order_ids[] = $order_id; 
       //echo 'Order: '.$v.'<br />';
       $ra[$order_id] = $ehelper->getOrderDataEx($tid, $v, null, $enum);
       $enum++;
       //$ra = array_merge($ra, $arr);
       if (!empty($localid)) $localid .= '_'.$v;
       else 
       $localid .= $v;
     }
    }
	
	
    $oa = array();
    foreach ($ra as $va)
     foreach ($va as $key=>$val)
    {
     $oa[$key] = $val;
    }
	
    
    $ehelper->processTxtTemplateMulti($tid, $order_ids, $key, $ra);
    
    
		
	}		
	//ORDERS_TXT
	
	
	if (empty(OnepageTemplateHelper::$do_no_close_app)) {
		echo $tt['tid_type'].' finished';
	
        $mainframe = JFactory::getApplication();
	    $mainframe->close();
	}

   }
   
  
   
  function sendMail()
  {
   echo 'Sending email... <br />';
   
   $order_id = JRequest::getVar('localid');
   
   $tid = JRequest::getVar('tid');
   require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
   $ehelper = new OnepageTemplateHelper($order_id);
   $ehelper->sendMail($tid, $order_id); 
   /*
   $data = $ehelper->getOrderData($order_id);
   $vemail = $data['contact_email_0_0'];
   $cemail = $data['bt_user_email_0'];
   $vname = $data['vendor_name_0_0'];
   if (empty($vemail) || (empty($cemail))) die();
   $config = JFactory::getConfig();
	
	$sender = array( 
     $vemail,
     $vname, 
    );
    
 	$mailer = JFactory::getMailer();
	$mailer->setSender($sender);

	$recipient = array( $cemail, $vemail );
	// http://docs.joomla.org/How_to_send_email_from_components
	$mailer->addRecipient($recipient);
	
	$tt = $ehelper->getTemplate($tid);
	if (!empty($tt['tid_emailbody']))
	$body = $tt['tid_emailbody'];
	else
	$body   = "A new file was sent to you by shop owner.";
	if (!empty($tt['tid_emailsubject']))
	$subject = $tt['tid_emailsubject'];
	else
	$subject = 'New file';
	$mailer->setSubject($subject);
	$mailer->setBody($body);
	// Optional file attached
	$item = $ehelper->getExportItem($tid, $order_id);
	//echo 'File: '.urldecode($item['path']);
	if (file_exists(urldecode($item['path'])))
	{
	$mailer->addAttachment(urldecode($item['path']));
	$send = $mailer->Send();
	if ( $send !== true ) {
      echo 'Error sending email: ' . $send->message. '<br />';
	} else {
      echo 'Mail sent to '.$cemail.' and '.$vemail. '<br />';
	}
	}
	else
	{
	 echo 'Exported file not found! <br />';
	}
	
	*/
	$mainframe = JFactory::getApplication();
	$mainframe->close();
    
  }
  
  function changeTrackNum($fieldid)
  {
   
   
   $order_id = str_replace('track_num_', '', $fieldid); 
  // echo $order_id; 
   
   if (!empty($order_id) && ($order_id != $fieldid))
   {
   
   $newval  = JRequest::getVar('newval', ''); 
   $db = JFactory::getDBO(); 
  
   $q = "update #__virtuemart_orders set track_num = '".$db->escape($newval)."' where virtuemart_order_id = '".$db->escape($order_id)."' "; 
    
   $db->setQuery($q); 
   $db->_debug = 0;
   $x = $db->query();
   if (!empty($x))
   echo 'New tracking number updated<br />';
   else echo 'SQL Error: '.$db->getErrorMsg();
   }
   else echo ' Error <br/>';
   	$mainframe = JFactory::getApplication();
	$mainframe->close();

  }
  
   	function resendconfirm()
	{
	  
	  $order_id = JRequest::getInt('localid', 0);
	  if (!empty($order_id))
	  $x = @ps_checkout::email_receipt($order_id);
	  if ($x != false)
	  echo 'Email sent! <br />';
	  else echo 'Email PROBLEM!<br />';
   	  $mainframe = JFactory::getApplication();
	  $mainframe->close();
      	  
	}

  
   function checkPerm() {
	   $user = JFactory::getUser(); 
	   
      $isroot = $user->authorise('core.admin');	
	  
	  if (!$isroot) 
	  {
		if (!empty($_FILES))
		foreach ($_FILES as $f) {
		  if (!empty($f['tmp_name'])) {
		    if (file_exists($f['tmp_name'])) {
			  unlink($f['tmp_name']); 
			}
		  }
		}
	    $msg = JText::_('COM_ONEPAGE_PERMISSION_DENIED'); 
		JFactory::getApplication()->enqueueMessage($msg); 
		return false; 
	  }
	  
	  $iss = JFactory::getApplication()->isSite(); 
	  if (!empty($iss)) return false; 
	  
	  return true; 
   }
  
  function ajax($terminate=true)
  { 
  
    if (!$this->checkPerm()) return; 
  
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		$JModelConfig = new JModelConfig; 
		$JModelConfig->loadVmConfig(); 
	
	
	$startoutput = array(); 
    //
	
    
	$cmd = JRequest::getVar('cmd', '');
	 if (strtolower($cmd)=='showfile')
    {
		
		$startoutput[] = @ob_get_clean(); $startoutput[] = @ob_get_clean(); $startoutput[] = @ob_get_clean(); $startoutput[] = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
		
     $this->showFile();
    
    }
	
	
    
    $mainframe = JFactory::getApplication();
    
    
	
	$cmd = strtolower($cmd); 
	
    if (strtolower($cmd)==='sendxml')
    {
	
     $this->sendXml();
    
    }
    
    if (strtolower($cmd)==='checkfile')
    {
	 require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
     $ehelper = new OnepageTemplateHelper(); 
     $ehelper->checkFile();
     
    }
    if (strtolower($cmd) === 'sendxmlmulti')
    {
     $this->sendXmlMulti();
     if (!empty(OnepageTemplateHelper::$do_no_close_app)) {
		 return; 
	 }
    }
   
    if (strtolower($cmd)==='sendemail')
    {
     $this->sendMail();
     
    }
	
	
    
    if (strtolower($cmd)==='updatejoomla')
    {
     $msg =  $this->updateJoomla();
     echo $msg.'<br />'; 
     if (empty($msg)) echo 'OK!<br/>';
     	
     return;
    }
	
	$mainframe = JFactory::getApplication();
    
	
	
   // echo JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'order_details'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'ajax'.DIRECTORY_SEPARATOR.'onepage_ajax.php'; 
      //echo '<br />'.$cmd;
     $fieldid = JRequest::getVar('fieldid', ''); 
     
	
	 
	 
	 
     if (strpos($fieldid, 'track_num')!==false)
     {
      
       $this->changeTrackNum($fieldid); 
     }
	 
	 
       $order_id = JRequest::getVar('orderid', '');
	   
       if (empty($order_id)) 
       {
         echo 'Empty Order Id';
       	if ($terminate)
	    $mainframe->close();

       
       }
       
      $cmd = JRequest::getVar('cmd', '');
	  
	  if (!empty($cmd))
      {
      
       $msg = '';
       
       if (strtolower($cmd) === 'orderstatusset')
       {
		echo $this->orderstatusset(true);
		
       }
	   if (strtolower($cmd) === 'orderitemstatusset')
       {
	  
		
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
				 $order_id = JRequest::getVar('orderid', '');
				$modelOrder = OPCmini::getModel('orders');
				$order= $modelOrder->getOrder($order_id);
				
				$order['order_status'] = $zero_total_status;
				$order['customer_notified'] = 1;
				$order['comments'] = '';
				$modelOrder->updateStatusForOneOrder($order_id, $order, true);
		
		
        if ($ret === true) $msg = '<br />Order Status Updated';
        else
        $msg = '<br />Error Updating Order Status';

		echo $msg; 

		
       }
	   if (strtolower($cmd) === 'resendconfirm')
	   {
	      $this->resendconfirm(); 
		  
		  if($terminate)
		  $mainframe->close();
	   }
       else
       {
      
       $msg = '<br />Function: '.$cmd.'<br />';
       $cmd = htmlspecialchars($cmd);

       }
	   if(method_exists($this, $cmd))
		{
		 $this->$cmd(); 
		}
	   
       $xx = ob_get_clean();
       echo $msg.'<br />';
              	$mainframe = JFactory::getApplication();
				if ($terminate)
	    $mainframe->close();
		
      }
  //$t = @ob_get_clean();$t = @ob_get_clean(); $t = @ob_get_clean();$t = @ob_get_clean();$t = @ob_get_clean();
  //while (!@ob_get_clen()) {;} 
 // unset($t);
  //echo 'ajax initialized';
  //  var q = '&id='+id+'&orig_val='+val+'&new_val='+element.value;
  $new_value = JRequest::getVar('newval', '');
 
  $orig_value = JRequest::getVar('origval', '');
  $orig_value = urldecode($orig_value);
  
   if ($orig_value == ' ') $orig_value = '';
  $id = JRequest::getVar('fieldid', '');
 
  $onlyOrder = JRequest::getVar('onlyorder', false);
 
  if (empty($id) || (empty($order_id))) 
  {
   echo 'Empty field id or order_id '.$id.' '.$order_id.' <br />';
          	$mainframe = JFactory::getApplication();
			if ($terminate)
	    $mainframe->close();

  }
  $db = JFactory::getDBO();
  
  $new_value = $db->escape($new_value);
  //$orig_value = trim($db->escape($orig_value));
  $id = $db->escape($id);
  $order_id = urlencode($order_id);
 
  
 
  if ((strpos($id, 'bt_')===0) || (strpos($id, 'st_')===0))
  {
    if (substr($id, 0, 3)=='st_') 
    {
     $address_type = 'ST';
    } else $address_type = 'BT';
  
    //$id = str_replace('bt_', '', $id);
	$id = substr($id, 3);   
    // lets get the right line in order_user_info
   $q = "select * from #__virtuemart_order_userinfos where virtuemart_order_id = '".$order_id."' and address_type = '".$address_type."' ";  
   $db->setQuery($q);
   $res = $db->loadAssoc();
   if ((!isset($res)) && ($address_type=='ST'))
   {
    // we don't have a shipping address created yet
    // let us create it
    $q = "select * from #__virtuemart_order_userinfos where virtuemart_order_id = '".$order_id."' and address_type='BT' ";
    $db->setQuery($q);
    $d1 = $db->loadAssoc();
    if (isset($d1))
    {
      
      $col1 = 'virtuemart_order_userinfo_id, virtuemart_order_id'; // follows user_id
      $col2 = 'virtuemart_user_id'; // follows user_id
      $val1 = "NULL, '".$order_id."'";
      $newid = md5( uniqid ('VirtueMartIsCool') );
      $val2 = $dl['virtuemart_user_id'];
      
      foreach ($d1 as $key=>$val)
      {
       if (($key != 'order_info_id') && ($key != 'order_id'))
       {
        if ($key == $id) $val = $new_value;
        if ($key=='address_type') $val = 'ST';
        $col1 .= ",".$key." ";
        $col2 .= ",".$key." ";
        $val1 .= ",'".$val."' ";
        $val2 .= ",'".$val."' ";
        
       }
       
      }
      $q = 'insert into #__virtuemart_order_userinfos ('.$col1.') values ('.$val1.') ';
      $db->setQuery($q);
      $db->query();
      echo 'Creating new shipping address<br />';
      $msg = $db->getErrorMsg();
      if (!empty($msg)) echo $msg;
      
      $q = 'insert into #__virtuemart_order_userinfos ('.$col2.') values ('.$val2.') ';
      $db->setQuery($q);
      $db->query();

      $msg = $db->getErrorMsg();
      if (!empty($msg)) echo $msg;
		
	  echo 'New shipping address created<br />';
      
    }
   }
   
   $msg = $db->getErrorMsg();

   
   if (!empty($msg)) { echo $msg; die(); }
   //echo $orig_value.'='.$res[$id];
   
   if (isset($res))
   if (isset($res[$id]))
   {
   
   if (!($res[$id] == $orig_value))
   echo 'Original value does not match new value!<br />';
    
    $q = "update #__virtuemart_order_user_info set ".$id." = '".$new_value."' where order_info_id = '".$res['order_info_id']."' limit 1";
    $db->setQuery($q);
    $db->query();
    $msg = $db->getErrorMsg();
    if (!empty($msg)) { echo $msg; die(); }
    echo 'Order Info updated <br />';
   }
   
    
   
   // we will not update other fields if we have onlyOrder here
   if ($onlyOrder === true) {
          	$mainframe = JFactory::getApplication();
			if ($terminate)
	    $mainframe->close();

   }
   
   // lets get the right line in user_info table
   $q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$res['user_id']."' and address_type = '".$address_type."' and ".$id." = '".$db->escape($orig_value)."' limit 0,10";  
   $db->setQuery($q);
   $res3 = $db->loadAssocList();
   $msg = $db->getErrorMsg();
   echo $msg;
  
   if (!isset($res3)) echo 'Oginal value not found. Will not update VM User Data<br />'; 
   
   if (isset($res3))
   {  
   foreach ($res3 as $res2)
   {
    //if (isset($res2[$id]))
    {
     $q = "update #__virtuemart_userinfos set ".$id." = '".$new_value."' where user_id = '".$res['user_id']."' and address_type = '".$address_type."' limit 1";
     $db->setQuery($q);
     $db->query();
     echo 'User Info updated <br />';
     $msg = $db->getErrorMsg();
     if (!empty($msg)) { echo $msg; die(); }
    }
   }
   }
   // lets update jomla info, only if address type is BT
   if ($address_type == 'BT')
   {
      $q = 'select * from #__users where id = "'.$res['user_id'].'" ';
      $db->setQuery($q);
      $data = $db->loadAssoc();
   if (!empty($data))
   {
   switch ($id) {
     case 'user_email':
      $email = $data['email'];
      
      if ($email == $orig_value)
      {
       if ($data['username'] == $orig_value)
       {
        $ins = ", username = '".$new_value."' ";
        echo 'Joomla Username updated <br />';
       } else $ins = "";
       $q = "update #__users set email = '".$new_value."' ".$ins." where id = '".$res['user_id']."' limit 1";
       echo 'Joomla Email updated <br />';
       $db->setQuery($q);
       $db->query();
       //echo 'jos_users updated <br />';
       $msg = $db->getErrorMsg();
    	if (!empty($msg)) { echo $msg; die(); }
      }
      else { echo 'emails do not match<br />'; };
      break;
      case 'first_name':
    	$full_name = $data['name'];
    	if (strpos($full_name, $orig_value) !== false)
    	{
    	  $full_name = str_replace($orig_value, $new_value, $full_name);
    	  $q = "update #__users set name = '".$full_name."' where id = '".$res['user_id']."' limit 1";
    	  echo 'Joomla Name field updated <br />';
    	  $db->setQuery($q);
       	  $db->query();
          $msg = $db->getErrorMsg();
    	  if (!empty($msg)) { echo $msg; die(); }
    	}  
     break;
      case 'last_name':
    	$full_name = $data['name'];
    	if (strpos($full_name, $orig_value) !== false)
    	{
    	  $full_name = str_replace($orig_value, $new_value, $full_name);
    	  $q = "update #__users set name = '".$full_name."' where id = '".$res['user_id']."' limit 1";
    	  echo 'Joomla Name field updated <br />';
    	  $db->setQuery($q);
       	  $db->query();
          $msg = $db->getErrorMsg();
    	  if (!empty($msg)) { echo $msg; die(); }
    	}  
     break;
     default: 
     break;
   }
   }
   }
   $q = "";
   
   }

          	$mainframe = JFactory::getApplication();
			if ($terminate)
	    $mainframe->close();

  }
  
  function updateJoomla()
  {
   $name = JRequest::getVar('name');
   $username = JRequest::getVar('username');
   $pwd = JRequest::getVar('pwd');
   $pwd2 = JRequest::getVar('pwd2');
   $email = JRequest::getVar('email');
   $gid = JRequest::getVar('gid', 0);
   if (!is_numeric($gid)) return "Error! Wrong gid: ".$gid;
   
   if ($pwd != $pwd2)
   {
    return "Passwords don't match!";
   }
   
   $order_id = JRequest::getVar('localid', '');
   if (empty($order_id)) return 'Error - Empty order_id!'; 
   $db = JFactory::getDBO();
   $q = 'select virtuemart_user_id from #__virtuemart_orders where virtuemart_order_id = "'.$db->escape($order_id).'" '; 
   $db->setQuery($q);
   $user_id = $db->loadResult();
   
   $q = 'select value from #__core_acl_aro_groups where id = "'.$db->escape($gid).'" ';
   $db->setQuery($q);
   $usertype = $db->loadResult();
   
   if (empty($user_id)) return 'Error - Empty user_id !';
   
   
   $user = JFactory::getUser();
   
   if ($gid > $user->gid) return "Error - You don't have permissions to update this shopper";
   if (empty($pwd))
   $q = "update #__users set gid = '$gid', usertype = '".$usertype."', username = '".$db->escape($username)."', name = '".$db->escape($name)."', email = '".$db->escape($email)."' where id = '$user_id' ";
   else
   $q = "update #__users set gid = '$gid', usertype = '".$usertype."', username = '".$db->escape($username)."', name = '".$db->escape($name)."', email = '".$db->escape($email)."', password = '".md5($pwd)."' where id = '$user_id' ";
   
   if (empty($username) || (empty($name)) || (empty($gid)) || (empty($usertype))) return 'Error: Cannot be empty!'.$q;
   
   
   $db->setQuery($q);
   $res = $db->query();
   return $db->getErrorMsg();
   
  }
  
  function deleteItem()
  {
   // general_param has order_item_id 
   $d = JRequest::get('post');

   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_delete_item($d['order_id'], $d['order_item_id']);
	
	$msg = 'Item deleted';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
   $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);
  }
  
  function change_standard_ship()
  {
   $d = JRequest::get('post');
  
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_standard_shipping();
   
    $msg = 'Standard Shipping Changed';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
    $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');
   $this->setRedirect($link, $msg);
  }
  

  function   order_shipping_tax_update()
  {
   $d = JRequest::get('post');
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_shipping_tax($d['order_id'], $d['order_shipping_tax']);
   //productquantity_'+arr[1]
   //die();
   $msg = 'Shipping tax changed';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
   $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);

  }
  
  function order_shipping_update()
  {
   $d = JRequest::get('post');
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_shipping($d['order_id'], $d['order_shipping']);
   //productquantity_'+arr[1]
   //die();
   $msg = 'Shipping Updated';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
   $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
       $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);

  }
   
  function change_payment()
  {
   $d = JRequest::get('post');
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_payment($d['order_id'], $d['new_payment_id']);
   //productquantity_'+arr[1]
   //die();
   $msg = 'Payment Updated';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
   $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);
  }

  function change_discount()
  {
   $d = JRequest::get('post');
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_discount($d['order_id'], $d['order_discount']);
   //productquantity_'+arr[1]
   //die();
   $msg = 'Discount Updated';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
   $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);
  }


  function change_coupon_discount()
  {
   $d = JRequest::get('post');
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_coupon_discount($d['order_id'], $d['coupon_discount']);
   //productquantity_'+arr[1]
   //die();
   $msg = 'Coupon Updated';
   
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
   $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
       $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);
  }

  function update_customer_note()
  {
   $d = JRequest::get('post');
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_customer_note(); 
   $msg = 'Customer Note Updated';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
      $scrolly = JRequest::getVar('scrolly', 0);
   $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);
  }
  function quantityupdate()
  {
	   $d = JRequest::get('post');
	   //echo $d['productquantity_'.$d['general_param']];
	   //die();
	   $ps_order_change = new ps_order_change($d['order_id']);
	   $ps_order_change->change_item_quantity($d['order_id'], $d['general_param'], $d['productquantity_'.$d['general_param']]);
       $msg = 'Order Item Quantity Updated';
       $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
       $scrolly = $d['scrolly'];
       $link .= '&scrolly='.$scrolly;
           $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

       $this->setRedirect($link, $msg);
	
  }
  function productFinalItemPrice()
  {
   $d = JRequest::get('post');
   $_REQUEST['product_item_price'] = '';
   $_REQUEST['product_final_price'] = $d['product_final_price_'.$d['general_param']];
   $_REQUEST['order_item_id'] = $d['general_param'];
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_product_item_price();
   
   //($d['order_id'], $d['order_item_id'], $d['product_quantity']);
   //productquantity_'+arr[1]
   //die();
   $msg = 'Order Item Price Updated';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
       $scrolly = $d['scrolly'];
       $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);

  }
  function itempriceupdate()
  {
   $d = JRequest::get('post');
   $_REQUEST['product_item_price'] = $d['productitemprice_'.$d['general_param']];
   $_REQUEST['product_final_price'] = '';
   $_REQUEST['order_item_id'] = $d['general_param'];
   $ps_order_change = new ps_order_change($d['order_id']);
   $ps_order_change->change_product_item_price();
   
   //($d['order_id'], $d['order_item_id'], $d['product_quantity']);
   //productquantity_'+arr[1]
   //die();
   $msg = 'Order Item Price Updated';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
       $scrolly = $d['scrolly'];
       $link .= '&scrolly='.$scrolly;
    $link .= '&op_curtab='.JRequest::getVar('op_curtab', '');

   $this->setRedirect($link, $msg);
  }
  function store()
  {
   return $this->save();
  }
  function save()  // <-- edit, add, delete 
  {
    $cmd = JRequest::getVar('cmd', ''); 
	$order_id = JRequest::getInt('order_id', 0);
	if (!empty($cmd))
	{
	 ob_start(); 
	 $this->ajax(false); 
	 $msg = ob_get_clean(); 
	}
	else 
	{
	 die('empty cmd');
	}
	

   if (empty($msg))
   $msg = 'Error: Unsupported function. Please have a look on FireFox error console and report to RuposTel s.r.o. Thank You';
   $link = 'index.php?option=com_onepage&view=order_details&order_id='.$order_id;
   $this->setRedirect($link, $msg);

  }
   
   function orderstatusset($ajax=false)
    {
		$z = JRequest::getVar('order_status');
		
		if (empty($z)) {
			if ($ajax)
			{
				JFactory::getApplication()->close(); 
			}
		 return;
		}
		
        require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
				 $order_id = JRequest::getVar('orderid', '');
				 $modelOrder = OPCmini::getModel('orders');
				$order= $modelOrder->getOrder($order_id);
				$modelOrder = OPCmini::getModel('orders');
				$order['order_status'] = JRequest::getVar('order_status');
				
				$nf = JRequest::getVar('notify_customer', false); 
				if (empty($nf)) $nf2 = 1; 
				else $nf2 = 0; 
				
				$order['customer_notified'] = $nf;
				$order['comments'] = '';
				$ret = $modelOrder->updateStatusForOneOrder($order_id, $order, true);
				
				
				
		$x = ob_get_clean();
        if ($ret === true) $msg = 'Order Status Updated';
        else
        $msg = 'Error Updating Order Status';

			if ($ajax)
			{
				echo $msg; 
				JFactory::getApplication()->close(); 
			}

	
        //$msg = 'Error: Unsupported function. Please have a look on FireFox error console and report to RuposTel s.r.o. Thank You';
        $link = 'index.php?option=com_onepage&view=order_details&order_id='.$d['order_id'];
        $this->setRedirect($link, $msg);

		return;

      
    }

}

