<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
class paymentpage_ttfsp 
	{
	public static function paymentpagettfsp($zakaz_row, $params){
		
		$number_zakaz = $zakaz_row->number_order;
		
		$date_zakaz = $zakaz_row->date.' '.$zakaz_row->hours.' '.$zakaz_row->minutes;
		
		$aboutzakaz = _ttfsp_about_zakaz_text1._ttfsp_lang_159.': '.$date_zakaz.'. '._ttfsp_number_zakaz.' '.$number_zakaz;

		$totalInPaymentCurrency =  number_format($zakaz_row->summa, 2, '.', '');  // Сумма для z-payment
		
		$fioclient = $zakaz_row->rfio;
		
		if ($fio = '') {
			
			$fio = $zakaz_row->number_order;
		}
		
		$paymentType_yandexkassa = '';
		
		if ($params['yandex_kassa_select'] == 0 ){
			
			$paymentType_yandexkassa = 'AC';
		}
		
		$succes_url = JURI::root() .'index.php?option=com_ttfsp&view=successyandexkassa';
		
		$cancel_url = JURI::root() .'index.php?option=com_ttfsp&view=cancelyandexkassa';
		
		/// Адрес страницы для Яндекс Кассы
		
		if ($params['yandex_test_mode'] == 1 ){
			
			 $url_yandexkassa = 'https://demomoney.yandex.ru/eshop.xml';
			 
		} else {
			
			 $url_yandexkassa = 'https://money.yandex.ru/eshop.xml';
		}
		
		///
				
		$hash = md5($params['lang_zpayment_id'].$number_zakaz.$totalInPaymentCurrency.$params['password_ini_zpayment']); // Хеш для z-payment
	
	?>
	
	<div class="paymentpage">
		
		<div class="info_paymentpage">
			<div class="numberzakz"><?php echo _ttfsp_number_zakaz.' '.$number_zakaz ?></div>
			<div class="summazakaz"><?php echo _ttfsp_lang_summ_spec.' '.$zakaz_row->summa.' '.$params['valuta_name'] ?></div>
		</div>
		
		<div class="payment_buttons">
			
			<?php if ($params['sposob_oplaty_2_on'] == 1) { ?>
			
			<div class="yandexkassa buttonblock">
	
				
				<form action="<?php echo $url_yandexkassa; ?>" method="post">
					<input name="paymentType" value="<?php echo $paymentType_yandexkassa; ?>" type="hidden">
					<input name="shopId" value="<?php echo $params['yandex_kassa_shopid'];?>" type="hidden"/>
					<input name="scid" value="<?php echo $params['yandex_kassa_scid'];?>" type="hidden"/>
					<input name="sum" value="<?php echo $zakaz_row->summa;?>" type="hidden">
					<input name="customerNumber" value="<?php echo $fioclient; ?>" type="hidden"/>
					<input name="orderNumber" value="<?php echo $zakaz_row->number_order;?>" type="hidden"/>
					<input name="cps_phone" value="<?php echo $zakaz_row->rphone;?>" type="hidden"/>
					<input name="cps_email" value="<?php echo $zakaz_row->rmail;?>" type="hidden"/>
					<input type="hidden" name="shopSuccessURL" value="<?php echo $succes_url; ?>" >
					<input type="hidden" name="shopFailURL" value="<?php echo $cancel_url; ?>" >
					<input type="hidden" name="cms_name" value="joomla_com_ttfsp" />
					<input type="submit" value="<?php echo $params['title_oplata_var3']; ?>"/>
					
				</form>
				
			</div>
			
			<?php } ?>
			
			<?php if ($params['sposob_oplaty_1_on'] == 1) { ?>
			
			<div class="zpayment buttonblock">
				
				<form id="pay_zpayment" name="pay_zpayment" method="post" action="https://z-payment.com/merchant.php">
					
					<input type="hidden" name="LMI_PAYEE_PURSE" value="<?php echo $params['lang_zpayment_id']; ?>"/>
					<input type="hidden" name="LMI_PAYMENT_NO" value="<?php echo $number_zakaz; ?>"/>
					<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?php echo $totalInPaymentCurrency; ?>"/>
					<input type="hidden" name="LMI_PAYMENT_DESC" value="<?php echo $aboutzakaz; ?>"/>
					<input name="CLIENT_MAIL" type="hidden" value="<?php $zakaz_row->rmail; ?>"/>
					<input name="ZP_SIGN" type="hidden" value="<?php echo $hash; ?>"/>
					<input class="zapaymentbutt" type="submit" value="<?php echo $params['title_oplata_var2']; ?>"/>
					
				</form>
			
			</div>
			
			<?php } ?>
			
			<?php if ($params['sposob_oplaty_0_on'] == 1) { ?>
			
			<div class="freepayment buttonblock">
				
				<form id="pay_freepayment" name="pay_freepayment" method="post" action="<?php echo JURI::base() . 'index.php?option=com_ttfsp&task=freepayment&order_number_freepayment='.$number_zakaz; ?>">
					
					
					<input class="zapaymentbutt" type="submit" value="<?php echo $params['title_oplata_var1']; ?>"/>
					
				</form>
			
			</div>
			
			<?php } ?>

			
		</div>
		
	</div>

		<?php
	}
}

?>