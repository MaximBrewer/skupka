<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
class successpage_ttfsp 
	{
	public static function successpagettfsp($zakaz_row, $params){
		
			$session = JFactory::getSession();
			
			$psws_sess = $session->getId();
		
			$print_css_url = '<link rel="stylesheet" href="'.JURI::base().'components/com_ttfsp/css/print.css" type="text/css" />';
		
			If ($zakaz_row->payment_status == 0) {
				$payment_status = '<h4 style="font-size: 16px; color: red;">'._ttfsp_payment_status_0.'</h4>';
			}
			
			If ($zakaz_row->payment_status == 1) {
				$payment_status = '<h4 style="font-size: 16px; color: green;">'._ttfsp_payment_status_1.'</h4>';
			}
			
			If ($zakaz_row->payment_status == 2) {
				$payment_status = '<h4 style="font-size: 16px; color: #89498d;">'._ttfsp_payment_status_2.'</h4>';
			}
			
			If ($zakaz_row->payment_status == 3) {
				$payment_status = '<h4 style="font-size: 16px; color: #fff; background: #000; padding: 3px;">'._ttfsp_payment_status_3.'</h4>';
			}
			
			$date_zakaz = $zakaz_row->date.' '.$zakaz_row->hours.':'.$zakaz_row->minutes;
			
			$link_return = JURI::base() .'index.php?option=com_ttfsp';
			
			$payment_link = JURI::base() .'index.php?option=com_ttfsp&view=paymentpageopen&number_order='.$zakaz_row->number_order.'&psws_sess='.$psws_sess;		
		
	?>
	
<?php if ($params["billing_on"] == 1) { ?>
	
	<?php if ($zakaz_row->payment_status == 0 || $zakaz_row->payment_status == 2) { ?>
		
		<div class="retpaym">
			
			<div class="retpm_butt">
		
				<span class="retpm_butt_text retbuts"><?php echo _ttfsp_order_no_paym; ?></span>
				
				<a href="<?php echo $payment_link ?>" class="retpm_butt_link retbuts"><?php echo _ttfsp_lang_button3; ?></a>
		
			</div>
			
		</div>
		
	<?php } ?>
		
<?php } ?>
	
<div id="successpagettfsp" style="padding: 5px;">
	
	<div class="mainpage_successpagettfsp" style="border: dotted #ccc 3px; padding: 20px;">
				
		<div class="successpagettfsp_title">
			
			<h1 class="tln_success" style="border-top: solid #ccc 2px;padding: 10px;"><?php echo _ttfsp_talon ?></h1>
			<h2 class="number_success_title" style="border-bottom: solid #ccc 2px; padding: 10px;"><?php echo _ttfsp_lang_47 ?> № <?php echo $zakaz_row->number_order ?></h2>
			
		</div>
		
		<?php if ($zakaz_row->office_name != '0') { ?>
		
		<div class="office_success block_s">
			
			<div class="office_success_title">
				
				<p> <?php echo _ttfsp_lang_167 ?> 
				
				<span> <?php echo $zakaz_row->office_name; ?> </span>
				
				</p> 
				
			</div>
			
			<div class="office_success_adress">
				
				<p> <?php echo _ttfsp_adress_title ?>:
				
				<span> <?php echo $zakaz_row->office_address ?></span>
				
				</p>
				
			</div>
			
		</div>
		
		<?php } ?>
		
		<div class="specialist_name_success block_s">
			
			<div class="specialist_success_title">
				
				<p class="specialist_fio_success"> <?php echo _ttfsp_lang_44 ?>: <span><?php echo $zakaz_row->specialist_name; ?></span></p> 
				
			</div>
			
			<div class="specialist_success_cab_spec">
				
				<?php if ($zakaz_row->number_cabinet) { ?>
				
					<p> <?php echo _ttfsp_number_cabinet ?>:
				
					<strong><?php echo $zakaz_row->number_cabinet ?></strong>
				
					</p>
				
				<?php } ?>
				
				<p class="specialisations_name_success"> <?php echo _ttfsp_lang_21 ?>: 
				
				<span><?php echo $zakaz_row->specializations_name; ?></span>
				
				</p>
				
			</div>
			
		</div>
		
		<div class="maininfo_success block_s">
			
			<div class="maininfo_success_title">
				
				<p class="info_success"> <strong><?php echo _ttfsp_lang_information ?>: </strong> </p>
				
				<p> <?php echo $zakaz_row->info; ?> </p> 
				
			</div>
			
		</div>
		
		<?php if ($params["billing_on"] == 1) { ?>
		
		<div class="payment_success block_s">
			
			<div class="payment_success_status">
				
				<h4 class="status_1"> <?php echo _ttfsp_payment_status_title ?> <?php echo $payment_status ?> </h4>
				
				<h4 class="summa_succ"> <?php echo _ttfsp_lang_summ_spec ?> <span> <?php echo $zakaz_row->summa.' '.$params['valuta_name'] ?>  </span></h4>

			</div>
			
		</div>
		
		<?php } ?>
		
		<div class="final_success block_s">
			
			<div class="final_success_block">
				
				<h2 class="time_dadta_s" style="border-top: solid #ccc 2px;padding: 10px; border-bottom: solid #ccc 2px;padding: 10px; padding: 10px;"> <?php echo _ttfsp_lang_159 ?>: <?php echo $date_zakaz ?> </h4>
				

			</div>
			
		</div>
		
		
	</div>
	
</div>
	
<div id="buttons_success">
	
	<div class="field_buttons">
		
		<script language="javascript">

			function CallPrint(strid) {
				var prtContent = document.getElementById(strid);
				var WinPrint = window.open('','','left=50,top=50,width=800,height=640,toolbar=0,scrollbars=1,status=0');
				WinPrint.document.write('<div id="print_success" class="contentpane">');
				WinPrint.document.write(prtContent.innerHTML);
				WinPrint.document.write('</div>');
				WinPrint.document.close();
				WinPrint.focus();
				WinPrint.print();
				WinPrint.close();
				prtContent.innerHTML=strOldOne;
			}
		</script>
		
		<a href="<?php echo $link_return; ?>" class="return_component link_succc"><?php echo _ttfsp_return_to_component; ?></a>
		<a onClick="javascript:CallPrint('successpagettfsp');" href="#" class="print_talon link_succc"><?php echo _ttfsp_print_talon; ?></a>
		
		
	</div>
	
</div>
	
		<?php
	}
}

?>