<?php
	function mailtemplate ($specialist_name, $sprspecname, $mess, $specid, $uchrejdenie, $params, $payment_status, $summa, $number_order, $order_password, $type) {
		$orderlink = JURI::root() .'index.php?option=com_ttfsp&view=successpage&number_order='.$number_order;
		$urlspec = JURI::root().'index.php?option=com_ttfsp&idspec='.$specid;
		if ($type == 0) {
			$title = _ttfsp_lang_71;
		}
		
		else {
			$title = $params['createmsg'];
		}
		$template = '
<html>
    <head>

    </head>

<div style="width: 100%; background-color: #f6f6f6; font-family: sans-serif;">
<div style="padding: 40px; background: #fff;">
	<div class="zakazbody">
		<div class="zakazheader">
		<h1 style="background: #3472a7; color: #fff; text-transform: uppercase; font-weight: normal; text-align: center; padding: 5px; font-size: 20px;"> <b>'.$title.' </b> № '.$number_order.'</h1>
		</div>
		<div style="padding: 5px 40px 10px 40px;">
			<table style="width: 90%; margin-left: 5%; font-size: 12px; border-collapse:initial;">
				<tbody>
					
					<tr style="margin-top: 2px;">
						<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;"><h4 style="font-size: 12px;">'._ttfsp_lang_44.':</h4><br><h2 style="font-size: 16px;">'.$specialist_name.'</h2></td>
						<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;">
							<a style="text-decoration: none; padding: 5px; border: solid #3472a7 1px; background: #3472a7; color: #fff; display: block; width: 250px;" href="'.$urlspec.'">'._ttfsp_lang_spec_url.'</a>
						</td>
					</tr>';
					
					if ($params['mail_spetialisations_on'] == 1 && $sprspecname != '') {
					
						$template .= '<tr style="margin-top: 2px;">
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;"><h2 style="font-size: 16px;">'._ttfsp_lang_21.':</h2></td>
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;">'.$sprspecname.'</td>
							</tr>';
					
					}
					if ($params['mail_uchrejdeniya_on'] == 1 && $uchrejdenie != '0') {
					
						$template .= '<tr style="margin-top: 2px;">
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;"><h2 style="font-size: 16px;">'._ttfsp_lang_167.'</h2></td>
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;">'.$uchrejdenie.'</td>
					</tr>';
					
					}
					
					if ($params["billing_on"] == 1) {
						
						$template .= '<tr style="margin-top: 2px;">
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;"><h2 style="font-size: 16px;">'._ttfsp_lang_summ_spec.'</h2></td>
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;">'.$summa.' '.$params['valuta_name'].'</td>
					</tr>';
					
						$template .= '<tr style="margin-top: 2px;">
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;"><h2 style="font-size: 16px;">'._ttfsp_payment_status_title.'</h2></td>
							<td style="background: #f7f7f7; padding: 5px 5px 5px 20px;">'.$payment_status.'</td>
					</tr>';
					

					
					}
					
		$template .=  '</tbody>
			</table>



			<table style="width: 90%; margin-left: 5%; font-size: 12px; border-collapse:initial; margin-top: 10px;">
				<tbody>
				<tr>
						<td style="background: #f7f7f7; padding: 5px 5px 5px 20px; font-weight: bold;">'._ttfsp_lang_information.'</td>
						<td style="background: #f7f7f7; padding: 5px 5px 5px 20px; font-weight: bold;">'._ttfsp_pswd_mail_title.'</td>
						
						
				</tr>
					
				<tr>
						<td style="background: #f7f7f7; padding: 5px 5px 5px 20px; font-size: 14px;">'.$mess.'</td>
						<td style="background: #f7f7f7; padding: 5px 5px 5px 20px; font-size: 14px;"><h3>'.$order_password.'</h3>
						<br>
						<br>
						<a style="text-decoration: none; padding: 5px; border: solid #3472a7 1px; background: green; color: #fff; display: block; width: 250px;" href="'.$orderlink.'">'._ttfsp_order_mail_link.'</a>
						</td>
						
						
				</tr>
										
				</tbody>
			</table>
						

			
		</div>
		<div class="zakazheader bottomcopy">
		<h1 style="background: #3472a7; color: #fff; text-transform: uppercase; font-weight: normal; text-align: center; padding: 5px; font-size: 20px;">'._ttfsp_lang_thanks.'
		<a style="text-decoration: none; color: #fff; line-height: 26px;" href="'.JURI::base().'">'._ttfsp_lang_thanks2.'</a>
		</h1>
		
		</div>
	</div>
	
</div>
    </div>
</html>';
return $template;
	}
?>