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
$routes = $pfClass->getRoutes($method); 
$vehicles = $pfClass->getVehicles($method); 
$slots = $pfClass->getSlots($method); 

echo $this->loadTemplate('header'); 
echo $this->loadTemplate('includes'); 

JHTMLOPC::stylesheet('print.css', 'administrator/components/com_delivery/views/config/tmpl/css/', false);

$config = new stdClass(); 
$config->vehicles[0] = 400; 
$config->vehicles[1] = 400; 

?><form action="index.php" id="adminForm" method="post">

<?php

 $days = array(
    'Time Slots',
	JText::_('MONDAY'), 
	JText::_('TUESDAY'), 
	JText::_('WEDNESDAY'), 
	JText::_('THURSDAY'), 
	JText::_('FRIDAY'), 
	JText::_('SATURDAY'), 
	JText::_('SUNDAY'), 
	);
	
  ?>
  <fieldset><legend class="mylegend">Current Week</legend>
  <?php
 $table = '<table class="current_week_table" style="width: 100%;">';
 $table .= '<tr >'; 
 
 $ct = time(); 
 $d = date('N', $ct); 
 
 
 $new2d = array(); 
 
 foreach ($days as $k=>$v)
 foreach ($routes as $k2=>$r)
 foreach ($slots as $sk=>$slot)
 {
    $tt  = $ct - (($d - $k) * 24*60*60);
    if (($d - $k) == 0) $cl = ' today'; 
   else $cl = ''; 
  
  $key = date('Y_m_d', $tt); 
   $name = 'vehicle_cday_'.$key.'_route_'.$k2.'_slot_'.$sk; 
  $cfg = $this->model->getConfig($name); 
   
   $name2 = 'vehicle_day_'.$k.'_route_'.$k2.'_slot_'.$sk; 
   $cfg2 = $this->model->getConfig($name); 
   if (!(in_array(-2, $cfg)))
   foreach ($vehicles as $vk=>$kk)
   {
      if (in_array($vk, $cfg))
      $new2d[$k][$sk][$vk] = $r.':'.$kk; 
   }
   
 }
 
 
 
 foreach ($days as $k=>$v)
 {
 $t  = $ct - (($d - $k) * 24*60*60);
 if (($d - $k) == 0) $cl = ' today'; 
 else $cl = ''; 
 
 $table .= '<th class="day '.$cl.'" style="text-align: left;">'; 
 $table .= $v.'<br />'; 
 if (!empty($k))
 $table .= date('jS \of F Y', $t);
 $table .= '</th>'; 
 
 }
 $table .= '</tr>'; 
 
 

 //foreach ($routes as $k2=>$r)
 foreach ($slots as $sk=>$slot)
 {
 $table .= '<tr class="mytr">'; 
 
 foreach ($days as $k=>$v)
 {
 if ($k == 0)
 {
  
  $table .= '<td class="day"><div class="time_slot_name">'; 
  $table .= $slot; 
  $table .= '</div></td>'; 
 }
 else
 {
  $table .= '<td class="mycell"><span></span>';
  if (isset($new2d[$k][$sk]))
  foreach ($new2d[$k][$sk] as $data)
  $table .=   '<span>'.$data."</span><br />\n"; 
  $table .= '</td>'; 
 }
 }
 $table .= '</tr>'; 
 }
 
 
 $table .= '</table>'; 
 echo $table; 
 
?>
  </fieldset>
 <?php
 if (false)
 {
 ?>
   <fieldset style="display: none;"><legend>Defaults</legend>
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
 
 foreach ($routes as $k2=>$r)
 foreach ($slots as $sk=>$slot)
 {
 $table .= '<tr>'; 
 
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
  }
  ?>
</form> 
<?php
if (!empty($j15)) echo '</div>'; 
?>
<script>

var maxHeight = null;
jQuery(document).ready( function(jQuery) {

jQuery('.mytr').each(function() {
    var thisHeight = jQuery(this).height();
    if(maxHeight == null || thisHeight > maxHeight) maxHeight = thisHeight;
}).height(maxHeight);



}); 
</script>
<style type="text/css" media="print">
    
</style>
