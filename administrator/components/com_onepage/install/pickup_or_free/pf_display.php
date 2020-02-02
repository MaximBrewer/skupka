<?php  defined ('_JEXEC') or die('Restricted access');
/**
 * pickup or delivery plugin
 * license - commercial
 * @author RuposTel.com
 *
 */

class pfDisplay {
  
    
  public function display(&$cart, $selected, &$htmlIn, &$ref, &$methods, $tableName)
  {
  	
		$db = JFactory::getDBO(); 
		
		
		
						
		// let's get yesterday's date due to various timezones
		$date = date('Y-m-d H:i:s', time()-3600*24);
		/*
		$query = "SELECT * FROM ".$tableName." WHERE date >= '".$date."'";
		$db->setQuery($query); 
		$res = $db->loadAssocList(); 
		*/
				
		
		
		//$e = $db->getErrorMsg(); echo $e; 
		//var_dump($res); die(); 
	    $lang     = JFactory::getLanguage();
		$tag = $lang->getTag(); 
		$filename = 'com_virtuemart';
		$lang->load($filename, JPATH_ADMINISTRATOR, $tag, true);
		$vendorId = 0;
		
		if (!class_exists('vmJsApi'))
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		$arr = array(); 
		
		$shiptoopen = JRequest::getVar('shiptoopen', 0); 
		
		if (strtolower($shiptoopen)=='true')
		 {
		   $m = reset($methods);
		   $cond = $ref->free_checkConditions ($cart, $m, null); 
		   if (!$cond)
		   {
		   
		    $m->free_text = str_replace('color:', 'invalidproperty:', $m->free_text); 
			$text = $m->free_text; 
			$res = $ref->_checkMyCoupons($cart, $m); 
			// if proper code is used, but didnt pass the validation
			if (!empty($res))
			 {
			   $text = $m->coupon_error_text; 
			   
			 }
			
		    $html = '<div style="color: red;">'.$text; 
			$html .= '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" /></div>
			<script type="text/javascript">
			//<![CDATA[
			  zipf = document.getElementById(\'shipto_zip_field\'); 
			  
			  if (zipf != null)
			   {
			     if (zipf.className.indexOf(\'invalid\')<0)
			     zipf.className += \' invalid\'; 
			   }
			  
			   ';
			
			 
			$html .= '
//]]> 
			</script>
			
			
			';
			$a1 = array(); 
			$a1[] = $html; 
			//var_dump($html); die(); 
			$htmlIn[] = $a1; 		 
		   }
		   else
		   {
		     $txt = '						<script type="text/javascript">
//<![CDATA[  
			  zipf = document.getElementById(\'shipto_zip_field\'); 
			  if (zipf != null)
			   {
			    zipf.className = zipf.className.split(\'invalid\').join(\'\'); 
			   }
//]]> 
			</script>

'; 
		     $htmlIn[] = array($txt); 
		   }
		   
		  //return true;
		 }
		 $js = ''; 
		foreach ($methods as $method)
		{		
		$disabled_days = array(); 			
		$js .= ' var disabled_days = new Array(); var disabled_days_pickup = new Array(); '; 
		
		foreach ($method as $key=>$val)			 
		{			    
		if (stripos($key, 'day_')!==false)				 
		{				    
		if (!empty($val))					  
		{					     
		$day = str_replace('day_', '', $key); 						 
		$day = (int)$day; 						 
		$disabled_days[$day] = (int)$day; 	
		$js .= ' disabled_days.push('.(int)$day.'); '; 
		}				 
		}			 
		
		if (stripos($key, 'dayp_')!==false)				 
		{				    
		if (!empty($val))					  
		{					     
		$day = str_replace('dayp_', '', $key); 						 
		$day = (int)$day; 						 
		//$disabled_days[$day] = (int)$day; 	
		$js .= ' disabled_days_pickup.push('.(int)$day.'); '; 
		}				 
		}
		
		
		}


		
		    $html =  '<div style="display: none;"><input type="radio" autocomplete="off" data-pickup=\'{"service":"pickup"}\' name="virtuemart_shipmentmethod_id" id="pickup_shipping"   value="' . $method->virtuemart_shipmentmethod_id .'" '; 
			if (empty($selected))
			{
			 $html .= '  '; 
			 $selected = $method->virtuemart_shipmentmethod_id; 
			}
			if (empty($method->default_selected))
			$html .= ' checked="checked" '; 
			//die('ok');
		
			
			$html .= '/><input type="radio" autocomplete="off" data-pickup=\'{"service":"free"}\' name="virtuemart_shipmentmethod_id" '; 
			if (!empty($method->default_selected))
			$html .= ' checked="checked" '; 
			
			$html .= '			id="free_shipping_method"   value="' . $method->virtuemart_shipmentmethod_id .'" /><input type="hidden" name="selected_method" id="free_or_pickup_selector" value="';
			if (empty($method->default_selected))
			$html .= 'pickup" />'; 
			else 
			$html .= 'free" />'; 
			
			$html .= '</div>'; 
			 if (!empty($cart->couponCode))
			 {
			 $msg = ''; 
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free))
			 $msg = $method->coupon_free_text; 
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free5))
			 $msg = $method->coupon_free5_text; 
			 else
			 if (strtolower($cart->couponCode) == strtolower($method->coupon_free10))
			 $msg = $method->coupon_free10_text; 
			 if (!empty($msg))
			 {
			  $html .= '<div class="coupon_desc">'.$msg.'</div>'; 
			 }
			 }
			 
			 $cond = $ref->free_checkConditions ($cart, $method, array(), true); 
			if (!$cond)
			$method->default_selected = 0; 
			
			if (empty($method->default_selected))
					{
					$isselected = 'isselected'; 
					$isselected2 = 'isselected2'; 
					$disabled = '';
					$opcdatepickerdisabled = 'opcdatepickerdisabled'; 
					$opcdatepicker = 'opcdatepicker'; 
					  $isselected = 'isselected'; 
					  $isselected2 = 'isselected2'; 
					  $inactive = 'inactive'; 
					  $inactive2 = 'inactive2'; 
					  $disabled = 'disabled="disabled"'; 
					  $button_checkbox_uned = ' button_checkbox_uned'; 
					$button_checkbox_ed = 'button_checkbox_ed'; 
					
					}
					else
					{
					  $isselected = 'inactive'; 
					  $isselected2 = 'inactive2'; 
					  $disabled = 'disabled="disabled"'; 
					  $isselected = 'inactive'; 
					  $button_checkbox_ed = 'button_checkbox_uned'; 
					$isselected2 = 'inactive2'; 
					$disabled = ''; 
					$inactive = 'isselected'; 
					$inactive2 = 'isselected2'; 
					$button_checkbox_uned = ' button_checkbox_ed'; 
					$opcdatepickerdisabled = 'opcdatepicker'; 
					  $opcdatepicker = 'opcdatepickerdisabled'; 
					  
					}
			
			
			// jumphere
			
			$html .= '
			  <div class="pf_shipping">
			  <div class="pf_pickup">
			  <div class="opc_heading"><button class="pickup_checkbox '.$button_checkbox_ed.'" name="pickup_checkbox" onclick="javascript:return pf_checkbox(\'pickup\', \'orfree\');" id="pickup_checkbox"><div>&nbsp;</div><span class="opc_title">'.JText::_($method->pickup_label).'</span></button></div>
				<div class="opc_inside">
				<div>
				  <div class="field_wrapper">
				    <div class="formLabel">';
					
				  
					
					
					$html .= '
					  <label for="pickup_date" class="'.$isselected.'" id="p_item1">'.JText::_('COM_VIRTUEMART_DATE').':</label>
					</div>
				    <div class="formFieldShipping">
					
					  ';
					  //$html .= JHTML::calendar($date='',$name="date",$id=NULL,$resetBt = TRUE, $yearRange='');

					  $cal = vmJsApi::jDate('', 'my_date', 'pickup', true, ''); 
					  
					   $f1 = $method->pickup_start_time; 					   
					   $f2 = explode(':', $f1); 
					   $from = $f2[0]; 

					   $f1 = $method->pickup_end_time; 
					   $f2 = explode(':', $f1); 
					   $to = $f2[0]; 
					   $to_min = $f2[1]; 
					   $it = (int)$method->time_period; 
					   	$iplus = 15; 
					
						$nowtime = time(); 
					   // test sunday: 
						//$nowtime += 5*(3600*24)-8*3600; 
					//echo date('l jS \of F Y h:i:s A', $nowtime); 
					//die(); 
					
					   $h = (int)date('G', $nowtime); 
					   $m = (int)date('i', $nowtime); 

					   
					
					   //
					   //die();  
					  //test
					  //$h = 19; 
					  //$m = 12; 
					   // iplus
					   $tt1 = $h*60+$m; 
					   $tt2 = $to*60+$to_min; 
					   
					   // sunday special case: 
					   
					   $dw = (int)date( "w", $nowtime); 					   					   
					   /*					   
					   $yy = in_array ($dw, $disabled_days); 					   
					   if (!empty($yy))					   
					   {					   					     
					   // today is a disabled day!						 
					   $p = 1; 						 
					   for ($i=0; $i<=6; $i++ )						  
					   {						  						    
					   $q = $i + $dw + 1; 							
					   if ($q>7)							 
					   {							   
					   //$z = {$q%7}; 							 
					   }							
					   //if (in_array($q, $disabled_days))							 
					   {							 
					   //  $p++; 							 
					   }							 
					   //else break; 														 						  }						  					   
					   }					   
					   */					   					   					   					   					   
					   if (empty($dw))
					   {
					     $h = 0; 
						 $m = 0; 
						 $del = false; 
						 // 
						 $ct = $nowtime+(60*24*60);
					   }
					   else
					   if (($h*60+$m + $iplus > ($to*60+$to_min)) || ($h*60+$m  < ($from*60+$iplus)))
					   {
					   if ($h*60+$m + $iplus > ($to*60+$to_min))
					   {
					   $ct = $nowtime+(60*24*60);
					   // BUT if the ct is sunday... 
					   $dw = date( "w", $ct);
					   if (empty($dw))
					   $ct += (60*24*60); 
					   
					   }
					   else $ct = $nowtime; 
					   
					   $del = false; 
					   }
					   else 
					   {
					  // $html .= "<script>console.log('ok".$tt1.' '.$tt2.' '.$to."')</script>"; 
					   $del = true; 
					   $ct =$nowtime; 
					   }
					  
					  if ($ct != $nowtime)
					   {
					    // we start from tommorow
						$shift = (int)($ct - $nowtime)/(60*24*60); 
						$extra = '<input type="hidden" name="pick_shift" value="'.$shift.'" id="pick_shift" />'; 
					   }
					   else $extra = '<input type="hidden" name="pick_shift" value="0" id="pick_shift" />'; 
					  
					  $translate = $ref->formatDate($ct, false); 
					  $cal = str_replace(JText::_('COM_VIRTUEMART_NEVER'), $translate, $cal); 
					  $cal = str_replace('vmicon vmicon-16-logout icon-nofloat js-date-reset', 'jdateimg', $cal); 
					  
					  $cal = str_replace('class="datepicker"', 'class="'.$opcdatepicker.'"', $cal); 
					  $dn = date('Y-m-d', $ct); 
					  $cal = str_replace('value=""', 'value="'.$dn.'"', $cal); 
					  //vmJsApi::jDate($_return['fields'][$_fld->name]['value'],  $_prefix.$_fld->name,$_prefix.$_fld->name . '_field',false,($currentYear-100).':'.$currentYear);
					  //$cal = JHTML::calendar('', 'my_calendar', 'pickup_date_input', '%d-%m-%Y', ''); 
					  $html .= $cal;
					  //$html .= '<div style="margin-left: 90%;position:absolute;left:0;top:3px;z-index:0;"><img src="/templates/beez_20/images/system/calendar.png" onclick="return jQuery( function($) { $(\'.datepicker\').datepicker(\'show\'); } );" alt="Calendar" class="calendar" id="_img"></div>';
					$html.= '</div>
					</div>
					'; 
					
					if (empty($method->disable_pickup_time))
					{
					$html .= ' 
					<div class="field_wrapper">
					<div class="formLabel">
					  <label for="pickup_date" class="'.$isselected.'" id="p_item2">'.JText::_('COM_VIRTUEMART_TIME').':</label>
					</div>
				    <div class="formFieldShipping">'; 
					  
					  
					  {
					 
					  $html .= '<select class="'.$isselected2.'" name="pickup_time" id="pickup_time">';
					   
					  
					   
					   $c = 60 / $it; 
					   
					 
						// and hidden_free_time
					   $hidden_select2 = '<select name="hidden_pickup_time" id="hidden_pickup_time" style="display: none;">'; 
					   $today_pickup = '<select name="today_pickup_time" id="today_pickup_time" style="display: none;">'; 
					   // stAn test
					   /* test start section 1 
					   for ($h = 7; $h<22; $h++)
					   for ($m = 0; $m<59; $m++)
					   {
					    $html = '<select name="test">'; 
					   test start section 1 */
					  //all options here: 
					  $all_free = ''; 
					  for ($i = $from; $i<=$to; $i++)
					  {
					    $option = ''; 
						  $option .= '<option value="'.$i.':00"';
						  $option .= '>'.$i.':00</option>';
						  $all_free .= $option;
						  if ($i!=$to)
						  for ($q = 1; $q<$c; $q++)
						  {
						    $option = ''; 
						    $j = $q*$it;
							$option = '<option value="'.$i.':'.$j.'">'.$i.':'.$j.'</option>';
							$all_free .= $option;
						   }
					  }
					  
					  $hidden_select2 .= $all_free; 
					  
					  
					   //var_dump($h); var_dump($m); die(); 
					   for ($i = $from; $i<=$to; $i++)
					    {
						  $option = ''; 
						  $option .= '<option value="'.$i.':00"';
						  
						   $j = 0;
							
							// current time + 15 minutes in minutes from midnight
						
							$mins = ($h*60 + $m) + $iplus; 
							// itenerator: 
							$mini = ($i*60)+$j; 
						
						if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it)))))					  
						  {
						  
						  $option .= ' selected="selected" '; 
						  }
						  
						  $option .= '>'.$i.':00</option>';
						 // $hidden_select .= '<option value="'.$i.':00">'.$i.':00</option>';
						  
					if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it)))))					  
						  {
						  
						  $html .= $option; 
						  $today_pickup .= $option; 
						  $del = false; 
						  }
						  else
						  if (!$del)
						  {
						  $html .= $option; 
						  $today_pickup .= $option; 
						  }
						  for ($q = 1; $q<$c; $q++)
						  {
						    $option = ''; 
						    $j = $q*$it;
							
							// current time + 15 minutes in minutes from midnight
							
							$mins = ($h*60 + $m) + $iplus; 
							// itenerator: 
							$mini = ($i*60)+$j; 
							
							if (($i == $to) && ($j > $to_min)) continue; 
							
						    $option .= ' <option '; 
							//$hidden_select .= ' <option '; 
							
							// 15 > 1 && 15 < 30
							// 75 > 60 && 75 < 
					if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it)))))					  
					{
					$option .= ' selected="selected" '; 
					}
							$option .= ' value="'.$i.':'.$j.'">'.$i.':'.$j.'</option>';
							//$hidden_select .= ' value="'.$i.':'.$j.'">'.$i.':'.$j.'</option>';
							
							if (($i==$to) && ($j>$to_min)) continue; 
							
							// one specific time
					if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it)))))					  
							 {
							   
								$html .= $option; 
								$today_pickup .= $option; 
								$del = false; 
							 }
							 else
							 if (!$del)
							 {
							 $html .= $option; 
							 $today_pickup .= $option; 
							 }

							
						  }
						  
						}
					    
					  $html .= '
					  </select>';
					  /* test start section 2 
					  echo $h.':'.$m.$html.'<br />'; 
					  }
					  */
					  $hidden_select = '';  
					  $hidden_select2 .= '</select>'; 
					  $today_pickup .= '</select>'; 
					  
					  $html .= $today_pickup.$hidden_select2; 
					  }
					  $html .= '
					  
					</div>
					
					
				  </div>';
				  }
				  
				  
				  $html .= '
					
				
				</div>
				</div>
			  
			  </div>
			  <div class="pf_free">
			  <div class="opc_heading"><button ';
			  
			  if (!empty($method->default_selected))
					{
					$isselected = 'inactive'; 
					$isselected2 = 'inactive2'; 
					$disabled = ''; 
					$inactive = 'isselected'; 
					$inactive2 = 'isselected2'; 
					$button_checkbox_uned = ' button_checkbox_ed'; 
					$opcdatepickerdisabled = 'opcdatepicker'; 
					}
					else
					{
					  $opcdatepickerdisabled = 'opcdatepickerdisabled'; 
					  $isselected = 'isselected'; 
					  $isselected2 = 'isselected2'; 
					  $inactive = 'inactive'; 
					  $inactive2 = 'inactive2'; 
					  $disabled = 'disabled="disabled"'; 
					  $button_checkbox_uned = ' button_checkbox_uned'; 
					}
			  
			  
			  if (!$cond) $html .= ' disabled="disabled" '; 
			  $html .= ' class="pickup_checkbox '.$button_checkbox_uned.'" name="free_checkbox" onclick="javascript:return pf_checkbox(\'orfree\', \'pickup\');" id="free_checkbox"><div>&nbsp;</div><span class="opc_title">'.JText::_($method->free_label).'</span></button></div>
				<div class="opc_inside">
				<div>';
				
				$mode = $method->custom_slots;
				
				
	if (empty($mode)) $mode = 0; 
				
				 $js .= '
   var disabled_times = new Array(); 
   var custom_slots = '.$mode.';
   '; 
   
   $js .= ' var already_reserved_error = "'.$method->error_delivery_text.'"; '; 
	
			if (!empty($method->reservations)) {
				if (empty($mode))
				{
				
				
							$q = 'select `delivery_date`, `delivery_time`, `route` from `'.$tableName.'` where `delivery_stamp` > '.time()." and `shipment_type`='free' "; 
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$resd = $db->loadAssocList(); 
			 }
			 else
			 {
			 
			 
			   $todayMorning = strtotime('today midnight');
			   $q = 'select `delivery_date`, `delivery_time`, `route`, `order_weight` from `'.$tableName.'` where `delivery_stamp` > '.$todayMorning." and `shipment_type`='free' and order_weight > 0 "; 
			   
				$db->setQuery($q);
				$resd = $db->loadAssocList(); 
				$dates = array(); 
				if (!empty($resd))
				foreach ($resd as $k=>$row)
				{
				  //if (empty($dates[$row['delivery_date']])) $dates[$row['delivery_date']] = array(); 
				  if (empty($dates[$row['delivery_date'].'_SEP_'.$row['route'].'_SEP_'.$row['delivery_time']])) $dates[$row['delivery_date'].'_SEP_'.$row['route'].'_SEP_'.$row['delivery_time']] = 0; 
				  
				  $dates[$row['delivery_date'].'_SEP_'.$row['route'].'_SEP_'.$row['delivery_time']] += $row['order_weight']; 
				  
				  //var_dump($dates); die(); 
				}
				
				$disabled = array(); 
				$weight =  $ref->_getOrderWeight ($cart, $method->weight_unit);
				$maxweight = (float)$method->max_slot_weight; 
				
				$disabled_slots = array(); 
				
				
				
				foreach ($dates as $key=>$val)
				{
				//val = 500
				// weight = 100
				// max 400
				// if already ordered is larger then max weight
				// if already ordered plus current weight is larger then max weight
				// buf if the current weight is larger than max weight do not fail
				   if (($val >= $maxweight) || ( (($val + $weight) > $maxweight) && ($weight <= $maxweight))) 
				    {
					  $a = explode('_SEP_', $key); 
				      $slot = $a[2]; 
					  
					  $slot = $ref->getSlotId($slot, $method); 	  
					  $route = $ref->getRouteId($a[1], $method); 
					  
					  if (!isset($disabled_slots[$a[0]]))
					  {
					  $js .= " disabled_times['".$a[0]."'] = new Array();  disabled_times['".$a[0]."'].push('".$slot.'_'.$route."'); "."\n";
					  if (empty($disabled_slots[$a[0]])) $disabled_slots[$a[0]] = array(); 
					  $disabled_slots[$a[0]][] = $slot.'_'.$route; 
					  }
					  else
					  {
					  $js.= " disabled_times['".$a[0]."'].push('".$slot.'_'.$route."'); "."\n";
					   $disabled_slots[$a[0]][] = $slot.'_'.$route; 
					  }
					 
					 // $disabled[$a[0]] = $a[1]; 
					}
				}
				
				
				
				
				
				
				
			 }
			
			
			
			
			
			//$e = $db->getErrorMsg(); { echo $e; die(); } 
			$tarr = array(); 
	
	/*
	if ($mode)
	{
	  foreach ($disabled as $key=>$val)
	  {
	   $js .= " disabled_times['".$key."'] = new Array();  disabled_times['".$key."'].push('".$val."'); "."\n";
	  }
	   
	}
	*/
	
    
			if (empty($mode))
			foreach ($resd as $k=>$row)
			 {
			   if (!isset($tarr[$row['delivery_date']]))
			   {
			   $tarr[$row['delivery_date']] = array(); 
			   $tarr[$row['delivery_date']][] = $row['delivery_time']; 
			   $js .= " disabled_times['".$row['delivery_date']."'] = new Array();  disabled_times['".$row['delivery_date']."'].push('".$row['delivery_time']."'); "."\n";
			   /*
			    $time = $row['delivery_time']; 
				 $a = explode(':', $time); 
				 // disabled time: 
				 $t = $a[0]*60+$a[1]; 
				 $upto = $a[0]*60+$a[1]+(int)$method->free_disable;
				 var_dump($t); var_dump($upto); 
				 for ($ia=$t;$ia<=$upto; $ia+=$iplus)
				  {
				  
				    echo 'ia '.$ia.' '; 
				    $ih = (int)floor($ia/60); 
					$im = (int)($ia - ($ih*60));
					if (!empty($im))
				    $tarr[$row['delivery_date']][] = $ih.':'.$im; 
					else
					$tarr[$row['delivery_date']][] = $ih.':00'; 
				  }
				 */
			   }
			   else
			   {
			     $tarr[$row['delivery_date']][] = $row['delivery_time']; 
				 /*
				 $time = $row['delivery_time']; 
				 $a = explode(':', $time); 
				 // disabled time: 
				 $t = $a[0]*60+$a[1]; 
				 $upto = $a[0]*60+$a[1]+(int)$method->free_disable;
				 var_dump($ia); var_dump($upto); 
				 for ($ia=$t;$ia<=$upto; $ia+=$iplus)
				  {
				   
				   
				    $ih = (int)floor($ia/60); 
					$im = (int)($ia - ($ih*60));
				    $tarr[$row['delivery_date']][] = $ih.':'.$im; 
				  }
				  */
				 $js.= " disabled_times['".$row['delivery_date']."'].push('".$row['delivery_time']."'); "."\n";
			   }
			 }
			}
				
				if ($cond)
					{
					
					
					$a = explode(';', $method->routes); 					  					  
				  
				  $routes = $ref->getRoutes($method); 
				  
				  $r = $method->routes; 
				  // ROUTE SECTION //
				  if (!empty($r))
				  {
				  $route_hidden = '<select name="route_name_hidden" id="route_name_hidden" style="display: none;" class="'.$inactive2.'" onchange="javascript: return updateTime(this);">'; 
				  
				  
				  $html .= '
				  <div class="field_wrapper pf_field_wrapper">					
				  <div class="formLabel">					  
				  <label for="free_date" id="r_item" class="'.$inactive.'">'.JText::_($method->route_label).':</label>	
				  </div>
				  <div class="formFieldShipping pf_formField">					  
				  <select name="route_name" id="free_route" class="'.$inactive2.'" onchange="javascript: return updateTimeRVS(this);"> ';
				  
				  
				   if (!empty($routes))
				  {
				  foreach ($routes as $key=>$r)
				  if (!empty($r))					    
				  {
				  $html .= '<option value="'.htmlentities($key).'">'.htmlentities($r).'</option>';
				  $route_hidden  .= '<option value="'.htmlentities($key).'">'.htmlentities($r).'</option>';
				  }	 
				  }
				  else
				  {
				  $html .= '<option value="'.htmlentities($r).'">'.htmlentities($r).'</option>';
				  $route_hidden  .= '<option value="'.htmlentities($r).'">'.htmlentities($r).'</option>';
				  }
				  $route_hidden .= '</select>'; 
				  $html .= '</select>					
				  </div></div>';
				  }
					// ROUTE SECTION END //
					
					
				$html .='<div class="field_wrapper">
				    <div class="formLabel">
					  <label for="free_date" id="d_item1" class="'.$inactive.'" >'.JText::_('COM_VIRTUEMART_DATE').':</label>
					</div>
				    <div class="formFieldShipping">
					
					  ';
					  // jDate($date='',$name="date",$id=NULL,$resetBt = TRUE, $yearRange='')
						$f1 = $method->free_start_time; 
					    $f2 = explode(':', $f1); 
					    $from = $f2[0]; 

					    $f1 = $method->free_end_time; 
						//var_dump($method->free_end_time); die(); 
					    $f2 = explode(':', $f1); 
					    $to = $f2[0]; 
						$to_min = $f2[1]; 
						
						$nowtime = time(); 
						// test sunday: 
						//$nowtime += 5*(3600*24)+12*3600; 
						
					  $h = (int)date('G', $nowtime); 
					   $m = (int)date('i', $nowtime); 
					   // stAn test
					   //$h = 16; 
					   //$m = 16; 
					   // stAn test end
					   
					   
					   //$h = 19; 
					   //$m = 1; 
					   if (empty($method->time_ahead)) $method->time_ahead = 45; 
					   $iplus = (int)$method->time_ahead; //45; 
					   
					   // last order time is 16.15
					   //stAn update: $tx = $to*60+60-$iplus; 
					   $tx = $to*60+$to_min; //-$iplus; 
					   // now
					   $nx = $h*60+$m; 
					
					$newtime = $nowtime; 	


 // sunday special case: 
					   
					   $dw = date( "w", $nowtime);
					   if (empty($dw))
					   {
					     $h = 0; 
						 $m = 0; 
						 $del = false; 
						 $astart = true; 
						 // 
						 $newtime = $nowtime+(60*24*60);
					   }
					   else					
					 if ($nx + $iplus > $tx) 
					 {
					 $newtime += 60*24*60;
					
					// sunday handling: 
					  $dw = date( "w", $newtime);
					   if (empty($dw))
					   $newtime += (60*24*60); 
					 
					 $astart = true; 
					 $del = false; 
					 }
					 else 
					 {
					 $astart = false; 
					 $del = true; 
					 }
					
					$ind = date('Y-m-d', $newtime); 
					
					if ($method->custom_slots)
					{
					$ind = $ref->getNextOpeningDeliveryDate($method, false); 
					
					$newtime = $ref->getNextOpeningDeliveryDate($method, true); 
					}
					
					 if (($h*60+$m + $iplus > ($to*60+$to_min)) || ($h*60+$m  < ($from*60+$iplus)))
					   {
					   if ($h*60+$m + $iplus > ($to*60+$to_min))
					   $ct = $nowtime+(60*24*60);
					   else $ct = $nowtime; 
					   
					   $dw = date( "w", $ct);
					   if (empty($dw))
					   $ct += (60*24*60);    
					   
					   $del = false; 
					   }
					   else 
					   {
					   //$html .= "<script>console.log('ok".$tt1.' '.$tt2.' '.$to."')</script>"; 
					   $del = true; 
					   $ct = $nowtime; 
					   }
					
					
					 
					  $cal = vmJsApi::jDate('', 'free_date', 'free_date', true, ''); 
					  $translate = $ref->formatDate($newtime, false); 
					  
					  if ($ct != $nowtime)
					   {
					    // we start from tommorow
						$shift = (int)($ct - $nowtime)/(60*24*60); 
						$extra .= '<input type="hidden" name="free_shift" value="'.$shift.'" id="free_shift" />'; 
					   }
					   else $extra .= '<input type="hidden" name="free_shift" value="0" id="free_shift" />'; 
					  
					  
					  $cal = str_replace(JText::_('COM_VIRTUEMART_NEVER'), $translate, $cal); 
					  $cal = str_replace('id="free_date_text"', ' style="" id="free_date_text" ', $cal); 
					  $cal = str_replace('vmicon vmicon-16-logout icon-nofloat js-date-reset', 'jdateimg', $cal); 
					  $cal = str_replace('class="datepicker"', 'class="'.$opcdatepickerdisabled.'"', $cal); 
					  $dn = date('Y-m-d', $newtime); 
					  $cal = str_replace('value=""', 'value="'.$dn.'"', $cal); 
					  //vmJsApi::jDate($_return['fields'][$_fld->name]['value'],  $_prefix.$_fld->name,$_prefix.$_fld->name . '_field',false,($currentYear-100).':'.$currentYear);
					  //$cal = JHTML::calendar('', 'my_calendar', 'pickup_date_input', '%d-%m-%Y', ''); 
					  $html .= $cal;
					$html.= '</div>
					</div>';
					
					
					$html .= '<div class="field_wrapper pf_field_wrapper">'; 
					
					
					
					$html .= '										
					<div class="formLabel pf_formLabel">
					  <label for="free_time" id="d_item2" class="'.$inactive.'" >'.JText::_('COM_VIRTUEMART_TIME').':</label>
					</div>
				    <div class="formFieldShipping pf_formField">
					';
					
		
			 //var_dump($tarr); die(); 
					$it = (int)$method->time_period; 
					if (empty($it)) $it = 30; 
					 $c = 60 / $it; 
					if (!empty($method->custom_slots))
					  {
					  
					    $html .= $ref->getSlotsRendered($method, $disabled_slots); 
					  }
					  else
					  {
					$hidden_select = '<select name="hidden_free_time" id="hidden_free_time" style="display: none;">';
					
					  $all_free = ''; 
					  for ($i = $from; $i<=$to; $i++)
					  {
					    $option = ''; 
						  $option .= '<option value="'.$i.':00"';
						  $option .= '>'.$i.':00</option>';
						  $all_free .= $option;
						  if ($i != $to)
						  for ($q = 1; $q<$c; $q++)
						  {
						    $option = ''; 
						    $j = $q*$it;
							$option = '<option value="'.$i.':'.$j.'">'.$i.':'.$j.'</option>';
							$all_free .= $option;
						   }
					  }
					  
					  $hidden_select .= $all_free; 
					
					
					$today_free = '<select name="today_free_time" id="today_free_time" style="display: none;">';
					$html .= '
					  <select name="free_time" id="free_time" class="'.$inactive2.'" >';
					   
					   
					   $it = (int)$method->time_period; 
					   $c = 60 / $it; 
					
					   
					  
					   for ($i = $from; $i<=$to; $i++)
					    {
						  $option = ''; 
						  //$hidden_select .= ' <option value="'.$i.':00"';
						  //$hidden_select .= '>'.$i.':00</option>'; 
						  
						  
						  $option .= ' <option value="'.$i.':00"';
						  
						  $j = 0; 
						  $mins = ($h*60 + $m) + $iplus; 
							// itenerator: 
						  $mini = ($i*60)+$j; 
						  
						  // itenerator >= (current time +45) and itenerator < (current time + 45 + 15)
						 if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it))))) $option .= ' selected="selected" '; 
						 $option .= '>'.$i.':00</option>';
						  
						 
						 
						if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it))))) 
						  {
						//free_disable
						if ((((!empty($tarr[$ind]))) && (!in_array($i.':00', $tarr[$ind]))) || empty($tarr[$ind]))
						
						  $html .= $option; 
						  $today_free .= $option; 
						  $del = false; 
						  }
						  else
						  if (!$del)
						   if ((((!empty($tarr[$ind]))) && (!in_array($i.':00', $tarr[$ind]))) || empty($tarr[$ind]))
						   {
						  $html .= $option; 
						  $today_free .= $option; 
						  }
						  for ($q = 1; $q<$c; $q++)
						  {
						    $option = ''; 
						  
						    $j = $q*$it;
							
							
							// current time + 15 minutes in minutes from midnight
							
							$mins = ($h*60 + $m) + $iplus; 
							// itenerator: 
							$mini = ($i*60)+$j; 
							
							
						  
						    $option .= ' <option ';
							//$hidden_select .= ' <option ';


							if (!$astart)
							if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it)))))  $option .= ' selected="selected" '; 
							$option .= ' value="'.$i.':'.$j.'">'.$i.':'.$j.'</option>';
							//$hidden_select .= ' value="'.$i.':'.$j.'">'.$i.':'.$j.'</option>';
							
							if (($i == $to) && ($j > $to_min)) continue; 
							
							// one specific time
							if (($mini >= $mins) && (($mini < ($mins + ($it))) && (($mini > ($mins - $it))))) 
							 {
							    if ((((!empty($tarr[$ind]))) && (!in_array($i.':'.$j, $tarr[$ind]))) || empty($tarr[$ind]))
								{
								$html .= $option; 
								$today_free .= $option; 
								//$hidden_select .= $option;
								}
								$del = false; 
							 }
							 else
							 if (!$del)
							 if ((((!empty($tarr[$ind]))) && (!in_array($i.':'.$j, $tarr[$ind]))) || empty($tarr[$ind]))
							 {
							 $html .= $option; 
							 $today_free .= $option; 
							 //$hidden_select .= $option;
							 }
							
						  }
						}
					    
					  $html .= '</select>';
					  $today_free .= '</select>';
					  $hidden_select .= '</select>';
						$hidden_select = str_replace('selected="selected"', '', $hidden_select); 
					  //var_dump($today_free); 
					  //var_dump($hidden_select); die(); 
					   $html .= $today_free.$hidden_select; 
					   
					   }
					   $html .= '
					</div>
										
					
				  </div>				  
				  '; 
				  
				  if (empty($r)) 
				  $html .= '<input type="hidden" name="route_name" value="none" />'; 
				$html .= '					  
				  
									  
					
				
				</div>';
					}
					else
					{
					  if ($ref->_checkMyCoupons($cart, $method))
					  $html .= $method->coupon_error_text; 
					  else
					  $html .= $method->free_text; 
					}
					
					$html .= '
					<div class="details"><a target="_blank" class="pfdmod" rel="{handler: \'iframe\', size: {x: 800, y: 400}}" href="'.JRoute::_('index.php?option=com_delivery&view=timetable&tmpl=component').'" onclick="javascript: return Onepage.op_openlink(this); ">View timetable</a></div>
				</div>
			   
			  </div>
			 </div>';
			 $html .= '
			 <div class="clear" style="width: 100%; float: none; clear: both;">&nbsp;</div>
			
			';
			
			if (empty($method->custom_slots))
			{
		
					    $f1 = $method->free_start_time; 
					    $f2 = explode(':', $f1); 
					    $from = $f2[0]; 
						
					    $f1 = $method->free_end_time; 
					    $f2 = explode(':', $f1); 
					    $to = $f2[0]; 
						$to_min = $f2[1]; 
						$j = 0; 
					   $it = (int)$method->time_period; 
					   $c = 60 / $it; 
					
					   $js .= ' var pickup_times = Array(); '; 
					   $js .= ' var free_disable_min = '.(int)$method->free_disable.'; '; 
					   for ($i = $from; $i<=$to; $i++)
					    {
		
						  $js .= ' pickup_times.push(\''.$i.':00\'); '; 
						  $last = ' var pickup_last = \''.$i.':'.$j.'\'; '; 
						  for ($q = 1; $q<$c; $q++)
						  {
						     
						    $j = $q*$it;
							if (($i == $to) && ($j > $to_min)) continue; 
							$js .= ' pickup_times.push(\''.$i.':'.$j.'\'); ';
							$last = ' var pickup_last = \''.$i.':'.$j.'\'; '; 
						  }
						}
					    $js .= $last; 
		  }
		  
		  $js .= ' var pf_mode = '.(int)$method->mode.'; '; 
		  
		   $root = Juri::root(true); 
		 if (substr($root, -1, 1)!=='/') $root .= '/';
		  
		  $js .= " var getDateUrl = '".$root."index.php?option=com_delivery&task=checkdate&view=ajax&format=opchtml'; "; 
		  
		 require_once(JPATH_SITE.DS.'plugins'.DS.'vmshipment'.DS.'pickup_or_free'.DS.'helper.php'); 
		
		$vehicles = $ref->getVehicles($method); 
		$routes = $ref->getRoutes($method); 
		$slots = $ref->getSlots($method); 
		
		$default = array(); 
		$current_week = array(); 
		$rconfig = pfHelper::getActiveDays($vehicles, $routes, $slots, $cw, $default);
		
		//var_dump($vehicles); var_dump($routes); var_dump($slots); 
		
		//var_dump($current_week); 
		//var_dump($default); die(); 
		  
		$html .= $extra; 
		$html .= $ref->renderByLayout('javascript_after', array('js'=>$js)); 
			
			$html = '<div id="opc_pfw">'.$html.'</div>'; 
			$arr[] = $html; 
		}
		
		
		$htmlIn[] = $arr; 
		return true; 
	
  }
 
}

