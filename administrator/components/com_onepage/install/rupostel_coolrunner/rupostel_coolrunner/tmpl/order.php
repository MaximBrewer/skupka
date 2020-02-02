<?php
$b = $viewData['data']['branch']; 
$b = (object)$b; 
$b->address = (object)$b->address; 


?>
<table class="adminlist">
 <tr><td>ID:</td>   <td><?php echo $b->droppoint_id; ?></td></tr>
 <tr><td>Packshop:</td>   <td><?php echo $b->name; ?></td></tr>
 <tr><td>Address</td>    <td><?php echo $b->address->street.','.$b->address->postal_code.' '.$b->address->city; ?></td></tr>
 <tr><td>Carier</td>    <td><?php 
 $c = JFile::makeSafe($b->carrier); 
 if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
 $file = JPATH_SITE.DS.'modules'.DS.'mod_coolrunner'.DS.'media'.DS.'theme1'.DS.$c.'.png';
 if (file_exists($file))
 {
	 echo '<img style="width: 100px;" src="data:image/png;base64,'.base64_encode(file_get_contents($file)).'" alt="'.addslashes($c).'"/>'; 
 }
 else
 {
	 echo $b->carrier; 
 }
 
 
 ?></td></tr>
</table>