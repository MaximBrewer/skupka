<?php

/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* OPC ADS plugin is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 



// no direct access
defined('_JEXEC') or die;
$root = Juri::root(true); 
?><!DOCTYPE html>
<html>
    <head>
        <title></title>

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/css/uikit.min.css" />
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css" />
		
        <script
			src="https://code.jquery.com/jquery-3.1.1.min.js"
			integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
			crossorigin="anonymous"></script>
			
		<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.js"></script>
        
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/js/uikit.min.js"></script>
		
		<script>
		  var rooturl = '<?php echo $root; ?>'; 
		</script>
    </head>
    <body>
	<?php 
	$cp = 0; 
	$last = 0; 
	foreach ($data as $row) { $cp++; if ($row === $last) continue; ?>
	
	<div>
	<?php echo $row['_SERVER']['HTTP_HOST'].$row['_SERVER']['REQUEST_URI']; ?> 
	 <a target="_blank" href="http://whatismyipaddress.com/ip/<?php echo $row['_SERVER']['REMOTE_ADDR']; ?>">IP information.... </a>
	 <button uk-toggle="target: #id_<?php echo $cp; ?>" type="button" class="<?php if ($row['_SERVER']['REQUEST_METHOD'] == 'POST') { echo 'uk-button-danger'; } else { echo 'uk-button-primary'; } ?>">Expand (GET <?php echo count($row['_GET']); ?> POST <?php echo count($row['_POST']); ?> COOKIE <?php echo count($row['_COOKIE']); ?>)...</button>
	 
	</div>
	
	<div id="id_<?php echo $cp; ?>" hidden="hidden" uk-grid  class="uk-grid-small uk-child-width-expand@s">
	
	<div>
		<table class="uk-table">
		<caption class="uk-background-secondary"><div class="uk-light">_SERVER</div></caption>
    <thead>
        <tr>
            <th>Key</th>
			<th>Value</th>
        </tr>
    </thead>
	 <tbody>
		 <?php foreach ($row['_SERVER'] as $key=>$vl) { ?>
            <tr>
			<td ><?php echo htmlentities($key); ?></td>
            <td class="uk-table-shrink"><?php echo htmlentities($vl); ?></td>
			</tr>
		 <?php } ?>
        </table>
		</tbody>
	</div>
	
	<div>
		<table class="uk-table">
		 <caption class="uk-background-secondary"><div class="uk-light">_GET</div></caption>
    <thead>
        <tr>
            <th>Key</th>
			<th>Value</th>
        </tr>
    </thead>
	 <tbody>
		 <?php foreach ($row['_GET'] as $key=>$vl) { ?>
            <tr>
			<td><?php echo htmlentities($key); ?></td>
            <td><?php echo htmlentities($vl); ?></td>
			</tr>
		 <?php } ?>
		 </tbody>
        </table>
	</div>
	
	<div>
		<table class="uk-table uk-table-striped">
		 <caption class="uk-background-secondary"><div class="uk-light">_POST</div></caption>
    <thead>
        <tr>
            <th>Key</th>
			<th>Value</th>
        </tr>
    </thead>
	 <tbody>
		 <?php foreach ($row['_POST'] as $key=>$vl) { ?>
            <tr>
			<td><?php echo htmlentities($key); ?></td>
            <td><?php echo htmlentities($vl); ?></td>
			</tr>
		 <?php } ?>
		  </tbody>
        </table>
	</div>
	
	<div>
		<table class="uk-table">
		<caption class="uk-background-secondary"><div class="uk-light">_COOKIE</div></caption>
    <thead>
        <tr>
            <th>Key</th>
			<th>Value</th>
        </tr>
    </thead>
	 <tbody>
		 <?php foreach ($row['_COOKIE'] as $key=>$vl) { ?>
            <tr>
			<td><?php echo htmlentities($key); ?></td>
            <td><?php echo htmlentities($vl); ?></td>
			</tr>
		 <?php } ?>
        </table>
		</tbody>
	</div>
		
   
	

	
	
	</div><br style="clear:both;" />
	<?php $last = $row; } ?>
	</body>
</html>