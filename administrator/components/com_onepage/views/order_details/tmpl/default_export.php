<?php
/*
*
* @copyright Copyright (C) 2007 - 2014 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

	
	$order_id = $this->orderID; 
	echo '<div>';
	// Ernest toto je tvoja cast, ak potrebuje upravit javascript, tak ho najdes v /ajax/onepage_ajax.js
	// k datam sa je mozne dostat dvoma sposobmi:
	// 1 - cez model a referenciu v view.html, kde su akurat order_data a templates
	// 2 - cez ehelper referenciu v view.html cize
	// $ehelper->funkcia($tid...   order_id ako parameter uz nemusi byt kedze je uz asociovany v view.html
	
	// extra: $sql = "ALTER TABLE `jos_onepage_exported` ADD `specials` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT \'\' AFTER `ai`";
	//$data = $this->order_data;
	$templates = $this->templates;
	$first = reset($templates);
	$runTimer = false;
	$mosConfig_live_site = $this->getUrl();
	if (count($templates)>0)
	$max_specials = $first['max_specials'];
	else $max_specials = 0;
	?>
	<table class="table">
	<thead>
	<tr><th><?php echo JText::_('COM_ONEPAGE_DOCUEMNT_NAME'); ?></th><th style="width: 50px;"><?php echo JText::_('COM_ONEPAGE_DOCUEMNT_COMMAND'); ?></th>
	<?php for ($i = 0; $i<$max_specials; $i++) {
	if ($i==0) echo '<th>AI Special Entry</th>';
	else { ?>
	<th><?php echo JText::_('COM_ONEPAGE_MANUAL_ENTRY');  ?><?php echo $i; ?></th>
	<?php } } ?>
	</tr>
	</thead>
	 <tbody>
	<?php
	foreach ($templates as $t)
	{
	 if (empty($t['tid_enabled'])) continue;
	 ?>
	 <tr><td>
	 <?php echo $t['tid_name'];
	 ?>
	 </td>
	 <td>
	 <?php
	 $status = $this->ehelper->getStatus($t['tid'], $order_id);
	 if ($status == 'AUTOPROCESSING') $status = 'PROCESSING';
	 if ($status == 'PROCESSING') $runTimer = true;
	 $status_txt = $this->ehelper->getStatusTxt($t['tid'], $order_id);
	 $specials = $this->ehelper->getSpecials($t['tid'], $order_id);
	 //echo $this->ehelper->getXml($t['tid'], $order_id);
	 ?>
	 <div id="tid_<?php echo $t['tid'] ?>_div">
	 <?php
	  $lin = '<a href="'.$this->ehelper->getExportItemLink($t['tid'], $order_id).'" id="tid_'.$t['tid'].'" onclick="'."javascript:return op_runCmd('sendXml', this);".'" >';
	  $plin = '<a href="#" id="tid_'.$t['tid'].'" >';
	  // status: NONE
	  ?><div id="tiddiv_<?php echo $t['tid'] ?>_NONE" <?php if ($status != 'NONE') echo ' style="display: none;" '; ?>><?php
 	  echo '<a href="'.$this->ehelper->getExportItemLink($t['tid'], $order_id).'" id="tid_'.$t['tid'].'_none" onclick="'."javascript:return op_runCmd('sendXml', this);".'" >'.'<img src="'.$this->getUrl().'components/com_onepage/assets/img/new.png" alt="'.$status_txt.'" title="'.$status_txt.'" /></a>';
	  ?></div><?php
	  $item = $this->ehelper->getExportItem($t['tid'], $order_id);
	  $link = $this->ehelper->getPdfLink($item);
	  if (empty($link)) $link = '#';
	  $created_html = '<a href="'.$link.'" id="tidpdf_'.$t['tid'].'" target="_blank"'." ><img src='".$this->getUrl()."components/com_onepage/assets/img/pdf_button.png' alt='".$status_txt."' title='".$status_txt."' />".'</a>';
	  $processing_html2 = '<a href="#" id="tid_'.$t['tid'].'_2" onclick="javascript:return op_runCmd('."'sendXml'".', this);"'." ><img src='".$this->getUrl()."components/com_onepage/assets/img/process.png' alt='RECREATE' title='RECREATE' /></a>";
	  $email = '   <a href="#" id="tid_'.$t['tid'].'_email" onclick="javascript:return op_runCmd('."'sendEmail'".', this);"><img src="'.$this->getUrl().'components/com_onepage/assets/img/emailButton.png" /></a>';
	  if (empty($t['tid_email'])) $email = '';
	  $created_html = $created_html.$processing_html2.$email;
   	  ?><div id="tiddiv_<?php echo $t['tid'] ?>_CREATED" <?php if ($status != 'CREATED') echo ' style="display: none;" '; ?>><?php
	  echo $created_html;
	  ?></div><?php
	  $processing_html = '<a href="'.$this->ehelper->getExportItemLink($t['tid'], $order_id).'" id="tid_'.$t['tid'].'_proc" onclick="'."javascript:return op_runCmd('sendXml', this);".'" >'."<img src='".$this->getUrl()."components/com_onepage/assets/img/mootree_loader.gif' alt='".$status_txt."' title='".$status_txt."' /></a>";
	  ?><div id="tiddiv_<?php echo $t['tid'] ?>_PROCESSING" <?php if ($status != 'PROCESSING') echo ' style="display: none;" '; ?>><?php
	  echo $processing_html;
	  ?></div><?php
      $error_html = '<a href="'.$this->ehelper->getExportItemLink($t['tid'], $order_id).'" id="tid_'.$t['tid'].'_err" onclick="'."javascript:return op_runCmd('sendXml', this);".'" >'."<img  src='".$this->getUrl()."components/com_onepage/assets/img/remove.png' alt='".$status_txt."' title='".$status_txt."' /></a>";
	  ?><div id="tiddiv_<?php echo $t['tid'] ?>_ERROR" <?php if ($status != 'ERROR') echo ' style="display: none;" '; ?>><?php
	  echo $error_html;
	  ?></div>
	  
	  
	  
	  <?php
	 ?>
	 
	 </div>
	 </td>
	 <?php for ($i = 0; $i<$max_specials; $i++) { ?>
	 <td>
	 <?php //echo $this->ehelper->getXml($t['tid']);

	 if (!empty($t['tid_special']))
	 {
	 ?>
	 <input type="text" style="background-color: #ffaacc;" id="specialentry_<?php echo $t['tid'].'_'.$i; ?>" name="specialentry_<?php echo $t['tid']; ?>" size="12" <?php 
	 	 if (!empty($t['tid_ai']) || (!empty($t['tid_special'])))
	 	 {
	 	  if (empty($specials[$i])) $specials[$i] = "";
	 	  echo ' value="'.$specials[$i].'" ';
	 	 }
	 	 
	 	?>/>
	 <?php 
	 }
	 ?>
	 </td>
	 <?php 
	 }
	 ?>	 
	 </tr>
	 <?php
	}
    ?>
	</tbody>
    </table>
    <?php
	echo '</div>';
