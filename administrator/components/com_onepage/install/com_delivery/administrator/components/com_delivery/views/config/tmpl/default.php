<?php
/**
 * @version		$Id: default.php 21837 2011-07-12 18:12:35Z  $
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

include(JPATH_SITE.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php");

$method =& $this->shipment->opcref->methods[0]; 

$pfClass =&  $this->shipment->opcref; 

if ((!empty($pfClass)) && (method_exists($pfClass, 'getRoutes'))) {
$routes = $pfClass->getRoutes($method); 
$vehicles = $pfClass->getVehicles($method); 
$slots = $pfClass->getSlots($method); 



echo $this->loadTemplate('includes'); 
echo $this->loadTemplate('header'); 

$config = new stdClass(); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'config.php'); 
foreach ($vehicles as $vh=>$k)
{
  $config->vehicles[$vh] = OPCconfig::getValue('opc_delivery', 'vehicle', $vh, 400); 
}

//$config->vehicles[0] = 400; 
//$config->vehicles[1] = 400; 

?><form action="index.php" id="adminForm" method="post">

<?php

 $days = array(
    'Routes',
	JText::_('MONDAY'), 
	JText::_('TUESDAY'), 
	JText::_('WEDNESDAY'), 
	JText::_('THURSDAY'), 
	JText::_('FRIDAY'), 
	JText::_('SATURDAY'), 
	JText::_('SUNDAY'), 
	);
	$this->setLayout('default'); 
	echo $this->loadTemplate('links'); 
  ?>
  <p>Note: Configure default delivery below and adjust per current week. Set N/A to clear/suppress default behavior.</p>
  
  
   <fieldset><legend>Defaults for Delivery</legend>
  <?php
 $table = '<table style="width: 100%;">';
 $table .= '<tr>'; 
 
 $ct = time(); 
 $d = date('N', $ct); 
 
 foreach ($days as $k=>$v)
 {
 $t  = $ct - (($d - $k) * 24*60*60);
 if (($d - $k) == 0) $cl = ' '; 
 else $cl = ''; 
 
 $table .= '<th class="day '.$cl.'" style="text-align: left;">'; 
 $table .= $v.'<br />'; 
 
 //$table .= date('jS \of F Y', $t);
 $table .= '</th>'; 
 
 }
 $table .= '</tr>'; 
 
 $rswitch = 0; 
 foreach ($routes as $k2=>$r)
 {
 if (empty($rswitch)) $rswitch = 1; 
 else $rswitch = 0; 
 
 foreach ($slots as $sk=>$slot)
 {
 $table .= '<tr class="rswitch_'.$rswitch.'">'; 
 
 foreach ($days as $k=>$v)
 {
 if ($k == 0)
 {
  
   $table .= '<td class="day">'; 
  $table .= $r.' Time slot: '.$slot; ; 
  $table .= '</td>'; 
 }
 else
 {
    if (($d - $k) == 0) $cl = ' '; 
   else $cl = ''; 
  $table .= '<td class="day '.$cl.'">'; 
  $name2 = 'vehicle_day_'.$k.'_route_'.$k2.'_slot_'.$sk; 
   $cfg2 = $this->model->getConfig($name2); 
   
  $table .= '<select class="vm-chzn-select" multiple="multiple" name="'.$name2.'[]">'; 
  //$table .= '<option value="0">N/A</option>'; 
  foreach ($vehicles as $vk=>$kk)
  {
    $table .= '<option value="'.$vk.'"'; 
   if (in_array($vk, $cfg2))
   $table .= ' selected="selected" '; 
   $table .= '>'.$kk.'</option>'; 
  }
  $table .= '</select>'; 
  $table .= '</td>'; 
 }
 }
 $table .= '</tr>'; 
 }
 
 }
 $table .= '</table>'; 
 echo $table; 
 
?>
  </fieldset>
  
  <fieldset><legend>Current Week Delivery</legend>
  <?php
 $table = '<table style="width: 100%;">';
 $table .= '<tr>'; 
 
 $ct = time(); 
 $d = date('N', $ct); 
 
 foreach ($days as $k=>$v)
 {
 $t  = $ct - (($d - $k) * 24*60*60);
 if (($d - $k) == 0) $cl = ' today'; 
 else $cl = ''; 
 
 $table .= '<th class="day '.$cl.'" style="text-align: left;">'; 
 $table .= $v.'<br />'; 
 if ($k !== 0)
 $table .= date('jS \of F Y', $t);
 $table .= '</th>'; 
 
 }
 $table .= '</tr>'; 
 
 

 foreach ($routes as $k2=>$r)
 {
 if (empty($rswitch)) $rswitch = 1; 
 else $rswitch = 0; 
 foreach ($slots as $sk=>$slot)
 {
 $table .= '<tr class="rswitch_'.$rswitch.'">'; 
 
 foreach ($days as $k=>$v)
 {
 if ($k == 0)
 {
  
  $table .= '<td class="day">'; 
  $table .= $r.' Time slot: '.$slot; 
  $table .= '</td>'; 
 }
 else
 {
     $tt  = $ct - (($d - $k) * 24*60*60);
    if (($d - $k) == 0) $cl = ' today'; 
   else $cl = ''; 
  $table .= '<td class="day '.$cl.'">'; 
  $key = date('Y_m_d', $tt); 
  $name = 'vehicle_cday_'.$key.'_route_'.$k2.'_slot_'.$sk; 
  $cfg = $this->model->getConfig($name); 
   
   $name2 = 'vehicle_day_'.$k.'_route_'.$k2.'_slot_'.$sk; 
   $cfg2 = $this->model->getConfig($name2); 
   
  $table .= '<select class="vm-chzn-select cws" multiple="multiple" name="'.$name.'[]" id="'.$name.'">'; 
  if (!empty($cfg2))
  {
  $table .= '<option value="-2"'; 
  if (in_array(-2, $cfg))
  $table .= ' selected="selected" '; 
  $table .= ' >N/A</option>'; 
  
  
  
  
  }
  
  foreach ($vehicles as $vk=>$kk)
  {
   
   $table .= '<option value="'.$vk.'"'; 
   if (!empty($cfg))
   if (in_array($vk, $cfg))
   {
   $table .= ' selected="selected" '; 
   
   }
   
   $table .= '>'.$kk.'</option>'; 
  }
  $table .= '</select>'; 
  $table .= '</td>'; 
 }
 }
 $table .= '</tr>'; 
 }
 }
 
 $table .= '</table>'; 
 echo $table; 
 
?>
  </fieldset>
  
  
   <fieldset><legend>Next Week Delivery</legend>
  <?php
 $table = '<table style="width: 100%;">';
 $table .= '<tr>'; 
 
 $ct = time(); 
 $ct = $ct + 24*7*60*60;
 $d = date('N', $ct); 
 
 foreach ($days as $k=>$v)
 {
 $t  = $ct - (($d - $k) * 24*60*60);
 //if (($d - $k) == 0) $cl = ' today'; 
 $cl = ''; 
 
 $table .= '<th class="day '.$cl.'" style="text-align: left;">'; 
 $table .= $v.'<br />'; 
 if ($k !== 0)
 $table .= date('jS \of F Y', $t);
 $table .= '</th>'; 
 
 }
 $table .= '</tr>'; 
 
 

 foreach ($routes as $k2=>$r)
 {
 if (empty($rswitch)) $rswitch = 1; 
 else $rswitch = 0; 
 foreach ($slots as $sk=>$slot)
 {
 $table .= '<tr class="rswitch_'.$rswitch.'">'; 
 
 foreach ($days as $k=>$v)
 {
 if ($k == 0)
 {
  
  $table .= '<td class="day">'; 
  $table .= $r.' Time slot: '.$slot; 
  $table .= '</td>'; 
 }
 else
 {
     $tt  = $ct - (($d - $k) * 24*60*60);
   // if (($d - $k) == 0) $cl = ' today'; 
   $cl = ''; 
  $table .= '<td class="day '.$cl.'">'; 
  $key = date('Y_m_d', $tt); 
  $name = 'vehicle_cday_'.$key.'_route_'.$k2.'_slot_'.$sk; 
  $cfg = $this->model->getConfig($name); 
   
   $name2 = 'vehicle_day_'.$k.'_route_'.$k2.'_slot_'.$sk; 
   $cfg2 = $this->model->getConfig($name2); 
   
  $table .= '<select class="vm-chzn-select cws" multiple="multiple" name="'.$name.'[]" id="'.$name.'">'; 
  if (!empty($cfg2))
  {
  $table .= '<option value="-2"'; 
  if (in_array(-2, $cfg))
  $table .= ' selected="selected" '; 
  $table .= ' >N/A</option>'; 
  
  
  
  
  }
  
  foreach ($vehicles as $vk=>$kk)
  {
   
   $table .= '<option value="'.$vk.'"'; 
   if (!empty($cfg))
   if (in_array($vk, $cfg))
   {
   $table .= ' selected="selected" '; 
   
   }
   
   $table .= '>'.$kk.'</option>'; 
  }
  $table .= '</select>'; 
  $table .= '</td>'; 
 }
 }
 $table .= '</tr>'; 
 }
 }
 
 $table .= '</table>'; 
 echo $table; 
 
?>
  </fieldset>
  
  
  <fieldset><legend>Vehicle configuration</legend>
<table>
<tr><th>Vehicle name</th><th>Capacity in KG</th></tr>
<?php

foreach ($vehicles as $k=>$v)
{
  echo ' <tr><th>'.$v.'</th><th>'; 
  echo '<input type="text" value="'.$config->vehicles[$k].'" name="config[vehicles]['.$k.']" />'; 
  echo '</th></tr>  '; 
}

?>
</table>
</fieldset>
  <input type="hidden" name="task" value="save" />
  <input type="hidden" name="option" value="com_delivery" />
  <input type="hidden" name="view" value="config" />

<?php
if (!empty($j15)) echo '</div>'; 

?>
<fieldset><legend>Custom Pickup in Store</legend>
<table>

<?php

 $table = '<table style="width: 100%;">';
 $table .= '<tr>'; 
 $slots = $pfClass->getPickupSlots($method); 
 $ct = time(); 
 $d = date('N', $ct); 
 $pickuproute = $k2 = 999; 
 $r = 'Shop'; ; 
 
 
 foreach ($days as $k=>$v)
 {
 $t  = $ct - (($d - $k) * 24*60*60);
  
	 $cl = ''; 
 
 $table .= '<th class="day '.$cl.'" style="text-align: left;">'; 
 $table .= $v; 
 
 
 $table .= '</th>'; 
 
 }
 $table .= '</tr>'; 
 
 

 
 {
 if (empty($rswitch)) $rswitch = 1; 
 else $rswitch = 0; 
 foreach ($slots as $sk=>$slot)
 {
 $table .= '<tr class="rswitch_'.$rswitch.'">'; 
 
 foreach ($days as $k=>$v)
 {
 if ($k == 0)
 {
  
  $table .= '<td class="day">'; 
  $table .= $r.' Time slot: '.$slot; 
  $table .= '</td>'; 
 }
 else
 {
     $tt  = $ct - (($d - $k) * 24*60*60);
    
   $cl = ''; 
  $table .= '<td class="day '.$cl.'">'; 
  $key = date('Y_m_d', $tt); 
  
   
   $name = 'vehicle_day_'.$k.'_route_'.$k2.'_slot_'.$sk; 
   $cfg2 = $this->model->getConfig($name); 
   
   
  $table .= '<select class="vm-chzn-select cws" multiple="multiple" name="'.$name.'[]" id="'.$name.'">'; 
  $table .= '<option value="-3"'; 
  if (!empty($cfg2))
  {
  
  if (in_array('-3', $cfg2))
  $table .= ' selected="selected" '; 
  
  
  
  
  
  }

  
  $table .= ' >(X)</option>'; 
  
  
  if (!empty($cfg2))
  {
  $table .= '<option value="-2"'; 
  
  
  $table .= ' >N/A</option>'; 
  
  
  
  
  }
  
  
  $table .= '</select>'; 
  $table .= '</td>'; 
 }
 }
 $table .= '</tr>'; 
 }
 }
 
 $table .= '</table>'; 
 echo $table; 
 
?>
  </fieldset>
<p>Notes: X in Pickup section means that shop accepts Pickup orders at the selected slot and day. </p>

</form> 
<?php 
}