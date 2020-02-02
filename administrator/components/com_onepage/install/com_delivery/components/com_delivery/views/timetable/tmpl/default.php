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
  
  <?php
  foreach ($routes as $k2=>$r)
  {
  $table = '<fieldset><legend class="mylegend">'.$r.'</legend>'; 
 $table .= '<table class="current_week_table" style="width: 100%;">';
 $table .= '<tr >'; 
 
 $ct = time(); 
 $d = date('N', $ct); 
 
 
 $new2d = array(); 
 
 foreach ($days as $k=>$v)
 {
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
 }
 
 
 
 foreach ($days as $k=>$v)
 {
 
 if ($k == 0) continue; 
 
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
  continue; 
  $table .= '<td class="day"><div class="time_slot_name">'; 
  $table .= $slot; 
  $table .= '</div></td>'; 
 }
 else
 {
  $table .= '<td class="mycell"><span></span>';
  if (isset($new2d[$k][$sk]))
  foreach ($new2d[$k] as $sk22=>$data)
  $table .=   '<span>'.$slots[$sk22]."</span><br />\n"; 
  $table .= '</td>'; 
 }
 }
 $table .= '</tr>'; 
 }
 
 
 $table .= '</table></fieldset>'; 
 
 echo $table; 
 }
?>
  
 
    
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
