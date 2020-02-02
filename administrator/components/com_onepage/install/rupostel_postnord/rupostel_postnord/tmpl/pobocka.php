<?php
defined('_JEXEC') or die('Restricted access');
?>

<?php
  $branch = $viewData['pobocka'];  
  
?>

<div class="zasielka_div1" style="padding-top: 8px; clear:both; <?php if ($viewData['sind'] != $branch->id) echo ' display: none;'; ?>" id="postnord_branch_<?php echo $branch->id; ?>">
 <div class="zas_image" style="float: left; max-width: 50%; margin:0; padding:0;">
 <?php if (!empty($branch->obrazek)) { ?>
 <a class="opcmodal" rel="{handler: \'iframe\', size: {x: 500, y: 400}}" href="<?php echo $branch->obrazek ?>">
 <img style="border:1px solid black; margin-right: 8px; float: left; " src="<?php echo $branch->obrazek ?>" width="160" height="120" /></a>
 <?php }
	else { ?>
	 
	<?php } ?>
 </div>
<div class="zasielka_div2"  style="float: left; clear:right; max-width: 50%;margin:0; padding:0;">
  <strong><?php echo $branch->nazev ?></strong><br/>
  <?php echo $branch->ulice; ?><br/>
  <?php echo $branch->psc.' '; 
  echo $branch->obec; ?><br />
  
  <div style="margin-top: 8px;">
   <div style="float: left; clear:both;">
    <em style="clear: both;"></em>
   </div>
   <br style="clear:both;"/>
   <?php echo $branch->provoz; ?>
   <br />
   <?php 
   $url = $detail_url = JURI::root().'plugins/vmshipment/postnord/detail_pobocky.php?id='.(string)$branch->id;
   if (!empty($branch->odkaz)) 
   {
   ?>
   <a href="<?php echo $branch->odkaz; ?>" class="opcmodal" target="_blank">Detail...</a>
   <?php } ?>
   </div>
  
</div>
 
</div>
