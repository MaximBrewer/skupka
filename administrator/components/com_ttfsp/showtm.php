<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
			if (!$userid)
			HTML_ttfsp::ttfspmenu("addtm");
			if (substr(JVERSION,0,3)=='1.5'){	
		jimport('joomla.utilities.date');
			$date	= new JDate();
			}
		if (!defined('INDURL')) {
			define( 'INDURL', 'index.php?option=com_ttfsp' ); 
		}			
			if ($userid){
?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 1) {
				form.task.value = 'savedt';
			}
			form.submit();
		}
		</script>			
			<?php
			}
			?>
		<style type="text/css">
		.uldate {
			float:left;
			margin-right:10px;
			font-size:11px;
		}
		</style>		
		<div class="fspcnt">
			<h3><?php echo _ttfsp_lang_73._ttfsp_lang_173; ?></h3>
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<div style="width:100%;">
		<?php if (!$userid){ ?>
		<div style="float:left;">
		<fieldset>  
		<legend title="<?php echo _ttfsp_lang_176; ?>"> <?php echo _ttfsp_lang_27; ?> </legend>		
			<?php echo $lists['spec']; ?>		
		</fieldset>  
		</div>
		<?php } ?>
		<div style="float:left;">
		<fieldset>  
		<legend> <?php echo _ttfsp_lang_177; ?> </legend>		
<?php
		$w = 0;
		for ($i=0; $i<49; $i++){
			if ($w==0)
				echo '
<ul class="uldate"  style="list-style-type:none;">
				';
			$w++;
			$tm = time()+$i*86400;	
			$ctm = 	substr(JVERSION,0,3)=='1.5' ? $date->_strftime('%a, %d-%m-%Y', $tm) : JHtml::_('date', date($tm), JText::_('D, d-m-y'));
			echo '
<li>
<input type="checkbox"  value="'.$tm.'" name="chkdate[]"> '.$ctm.'
</li>
			';
			if ($w==7){
				$w=0;
				echo '
</ul>
				';
			}
			
		}
?>		
		</fieldset>  
		</div>
		
		</div> 

		<div style="clear:both;float:none;padding-top:10px;">
		<?php echo _ttfsp_lang_5; ?>
		<?php echo $lists['published']; ?>
		</div>
		<?php if (!$userid){ ?>
		<div style="padding-top:10px;">
		<?php echo _ttfsp_lang_142.'<br />'._ttfsp_lang_143; ?>
		<input class="inputbox" type="text" size="15" name="plimit" value="0">
		</div>
		<?php } ?>	
		<input type="hidden" name="task" value="editA" />
		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="act" value="addtm">
		<?php if ($userid){ ?>
		<div>
		<br />
		<input type="button" value="<?php echo _ttfsp_lang_211; ?>" class="inputbox" onClick="submitbutton(1)" />
		<input type="button" value="<?php echo _ttfsp_lang_63; ?>" class="inputbox" onClick="submitbutton(0)" />
		</div>	
		<?php } ?>
		</form>
		<div style="color:#999;padding-top:30px;font-size:11px;">
		<?php echo !$userid ? _ttfsp_lang_178 : ''; ?>
		</div>			
</div>	
<div style="float:none;clear:both;"></div>			
		