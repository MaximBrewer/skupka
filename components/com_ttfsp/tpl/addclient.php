<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
class addclient_ttfsp 
	{
	public static function addclientttfsp($row, $rowuser, $cdate,  $rowel, $params, $sprspecdesc, $aspec){
		
	$database 	= JFactory::getDBO();
		
	$spridspec = isset($row->spridspec) ? ','.$row->spridspec.',':'';
	$url_site = $params['url_site'];
	$fio='';
	$phone='';
	
	
	$cont_spezializations = count ($aspec);
	
	if ($cont_spezializations > 0) {
		
			$query = "SELECT * FROM #__ttfsp_sprspec WHERE id IN(".implode(',',$aspec).")";

			
			$database->setQuery($query);
			
			$spezializations = $database->loadObjectList();
		
	}
	
	
	if ($rowuser){
		$fio=$rowuser->fio;
		$phone=$rowuser->phone;
	}
		$link = JURI::base( true ).'/index.php?option=com_ttfsp&task=addclient&addtest=1&cdate='.$cdate.'&id='.$row->id;
		$link1 = JURI::base( true ).'/index.php?option=com_ttfsp&idspec='.$row->idspec.'&cdate='.$cdate;

//		$img = $row->photo && !$row->offphoto ? '<img class="photovt" src="'.$url_site.$row->photo.'"/>' : ''; 
		$row->desc = str_replace(chr(13),'<br />',$row->desc);
		$row->sprspecdesc = isset($row->sprspecdesc)? str_replace(chr(13),'<br />',$row->sprspecdesc):'';

		$img = ''; 
		if (!$row->offphoto && $row->photo){
			$img = '<div class="spec_images">';
			$files = explode(';', $row->photo);
			for ($p=0;$p<count($files);$p++){	
				$img .= '<span>
				<img class="photovt" src="'.$url_site.$files[$p].'" />
				</span>
				';
			}
			$img .= '</div>';
		}


	?>
	<?php		$pricezap = ''; // Вывод стоимости записи
				
				if ($params['billing_on_title'] ==  1 && $row->pricezap > 0) {
					$pricezap = '<div class="pricezap2">
					<span class="nameprice2">'._ttfsp_lang_summ_spec.'</span>
					<span class="pricenum2">'.$row->pricezap.'</span>
					<span class ="valt2">'.$params['valuta_name'].'</span>
					</div>
					';
				}
				if ($params['billing_on_title'] ==  1 && $row->pricezap <= 0) {
					$pricezap = '<div class="pricezap2">
					<span class="pricenum2">'._ttfsp_lang_free.'</span>
					</div>
					';;
				}
				
				echo $pricezap;
	?>


<div class="addclient_page">

	<div class="addclient info_order_block">
		
			<div class="cantd">
			
				<a class="canlink" href="<?php echo $link1; ?>"><input type="button" value="<?php echo _ttfsp_lang_return; ?>" class="button" /></a>
				
			</div>
			
		<?php if ($img !='') { ?>

			<div class="order_desc_block image_block">
				
							 
				<div  class="image_order">
				 
				 	<?php echo $img; ?>
			 	
				 </div>
			
			</div>
			
		<?php } ?>
			 
			<div class="order_desc_block">
			
			 
				<div class="fiospec">
			 
				 	<?php echo $row->name; ?>
			
				</div>
				
				<?php if ($row->desc != '') { ?>
		
				<div class="descspec">
			
					<?php echo $row->desc; ?>
			
				</div>
				
				<?php } ?>
			
		 
				<div class="spec">
					
					<h4 class="spec_list_title"> <?php echo _ttfsp_lang_28 ?> </h4>
					
					<ul class="spec_list">
			 
				<?php for ($x=0; $x < $cont_spezializations;) {
							
					
							echo '<li class="spec_name specvar'.$spezializations[$x]->id.'">'.$spezializations[$x]->name.'</li>';
							$x++;
							};
							
				?>
				
					</ul>
			
				</div>
			
			</div>
		 

	</div>
	
	<div class="order_page_block_time">
			
		<div class="order_page_dttime"> 
					
			<?php echo _ttfsp_lang_66; ?> 
			<?php echo $row->dttime;?> 
		</div>
		
	</div>
	
	
	<div class="order_page_block_time">
		
		<div class="order_page_dttime"> 

			<?php echo _ttfsp_lang_32; ?>  
			<?php echo $row->hrtime.':'.$row->mntime;?> 
			
		</div>	
	</div>	




	<script type="text/javascript">
		var oplata = 0;
		var noempty ="<?php echo _ttfsp_lang_112; ?> ";
		var nomask="<?php echo _ttfsp_lang_113; ?> ";
		var nolen="<?php echo _ttfsp_lang_114; ?> ";
		var nolimit="<?php echo _ttfsp_lang_115; ?> ";
		var nochar="<?php echo _ttfsp_lang_116; ?> ";
		function datatest(oplata) {
			var oplata_final = '<?php echo $link; ?>';
			
			if (actttfsp()){
				document.ttfspForm.add.value=1;
				document.ttfspForm.action="<?php echo $link; ?>";
				document.ttfspForm.submit();
			}
		}
	</script>
	
	<form action="<?php echo $link1; ?>" method="post" name="ttfspForm" id="ttfspForm">
	

	<?php	
	$fioout = 1;
	$phoneout = 1;
	if (count($rowel)){
		for ($e=0;$e<count($rowel);$e++){
			$elem = $rowel[$e];
			$crel = 0;
					if ($elem->idsspec=='' || strpos(' '.$elem->idsspec,',0,')){
						$crel = 1;
					} else {
						for($t=0;$t<count($aspec);$t++){
							$myvalue= $aspec[$t];
							if ( strpos( ' '.$elem->idsspec, ','.$myvalue.',' ))
								$crel = 1;
						}	
					}	
					
					if ($crel){	

				if ($elem->fname=='fio') $fioout = 0;
				if ($elem->fname=='phone') $phoneout = 0;
				$htmlelem = '
				<div class="order_page_block"><div>'.$elem->title.'</div><div>';
				$maxlength = $elem->maxlength ? 'maxlength="'.$elem->maxlength.'"' : '';
				$required = $elem->required ? ' required="1" ' : '';
				$readonly = $elem->readonly ? ' readonly="1" ' : '';
				$size = $elem->size ? ' size="'.$elem->size.'" ' : '';
				$mask = $elem->mask ? ' mask="'.$elem->mask.'" ' : '';
				$css = $elem->css ? 'class="'.$elem->css.'"' : 'class="inputbox"';
				$field = $elem->fname ? $elem->fname : 'ttfsp_'.$elem->id;
				$field = $elem->name=='email' ? 'ttfsp_email' : $field;					
				switch ( $elem->type ) {
				case 1: 
					$size = '';
					if (strpos($elem->size,',')){
						$size = ' rows="'.substr($elem->size,0,strpos($elem->size,',')).'" cols="'.substr($elem->size,strpos($elem->size,',')+1).'" ';
					}
					$htmlelem .= '<textarea name="'.$field.'" id="'.$field.'" '.$css.$maxlength.$required.$readonly.$size.$mask.' >'.$elem->value.'</textarea>';
				break;
				case 2: 
					if ($elem->value){
						if ($elem->multisel){
						$fldsel =  ' multiple="multiple" ';
						$arrfld ='[]';
						} else {
						$fldsel =  '';
						$arrfld ='';
						}
						$htmlelem .= '
						<select name="'.$field.$arrfld.'" id="'.$field.'" '.$css.$required.$size.$fldsel.' >'; 
						$value = str_replace ("<br />", "\n", $elem->value);
						$elements = explode("\n" ,$value);
						for($i=0;$i<count($elements);$i++){
							$label = htmlspecialchars($elements[$i], ENT_QUOTES);
							$myvalue= '';			
							$pos = strpos( $label, ',' );
							if ($pos>0) {
								$myvalue = 'value="'.substr($label,$pos+1).'"';
								$label = substr($label, 0, $pos);
							} else {
								$myvalue = 'value="'.$label.'"';
							}
							$htmlelem .= '<option '.$myvalue.' >'.$label.'</option>';
						}
						$htmlelem .= '
						</select>';
					}
				break;
				case 3:
					$htmlelem .= '<input type="text" value=""  required="1" name="'.$field.'" id="'.$field.'" '.$css.$maxlength.$size.$mask.' />';
				break;
				case 4: 
					$htmlelem .= $elem->value;	
				break;
				case 5: 
					if ($elem->value){
						$htmlelem .= '
						<ul style="list-style-type:none;">'; 
						$value = str_replace ("<br />", "\n", $elem->value);
						$elements = explode("\n" ,$value);
						for($i=0;$i<count($elements);$i++){
							$label = htmlspecialchars($elements[$i], ENT_QUOTES);
							$htmlelem .= '<li><input type="checkbox" name="'.$field.'[]" value="'.$label.'" >'.$label.'</li>';
						}
						$htmlelem .= '
						</ul>';
					}
				break;				
				case 6: 
					if ($elem->value){
						$htmlelem .= '
						<ul style="list-style-type:none;">'; 
						$value = str_replace ("<br />", "\n", $elem->value);
						$elements = explode("\n" ,$value);
						for($i=0;$i<count($elements);$i++){
							$label = htmlspecialchars($elements[$i], ENT_QUOTES);
							$htmlelem .= '<li><input type="radio" name="'.$field.'[]" value="'.$label.'" >'.$label.'</li>';
						}
						$htmlelem .= '
						</ul>';
					}
				break;					
	
				
				default:
					$htmlelem .= '<input type="text" value="'.$elem->value.'" name="'.$field.'" id="'.$field.'" '.$css.$maxlength.$required.$readonly.$size.$mask.' />';
				break;
				}
				$htmlelem .= '</div></div>';
				echo $htmlelem;
			}	
		}
	}
	if ($fioout){
	?>
	
	<div class="order_page_block">
		
		<div class="titl_inp_ord"><?php echo _ttfsp_lang_25; ?></div>
		<div><input type="text" maxlength="50" name="fio" value="<?php echo $fio;?>" required="1" class="inputbox" /></div>
		
	</div>
	
	
	<?php }
	if ($phoneout){ ?>
	
	
	<div class="order_page_block">
		
		<div class="titl_inp_ord"><?php echo _ttfsp_lang_64; ?></div>
		<div><input type="text" maxlength="20" name="phone" value="<?php echo $phone;?>" class="inputbox" /></div>
		
	</div>
	
	
	<?php } ?>
	
	
	<?php if ($params["specialization_select_on"] == 1) { ?>
	
	
	<div class="order_page_block select_specizalz">
		
			
			<p class="select_scpec"><?php echo _ttfsp_spezialization_select ?></p>
			
				<select name="spezialization_select" id = "spezialization_select" onchange="select_spec_var ();">
					
			
						<?php 	
			
				
							for ($x=0; $x < $cont_spezializations;) {
							
					
							echo '<option value="'.$spezializations[$x]->name.'">'.$spezializations[$x]->name.'</option>';
							$x++;
							};
				
						?>
			
				</select>
				
			<input id="type_spec" type="hidden" name="type_spec" value="<?php echo $spezializations[0]->name; ?>" />
				
			<script>
			
				select_spec_var ();
				
				function select_spec_var () {
					
					var spec_var = document.getElementById("spezialization_select"); // Определяем...
					var spec_var_select = spec_var.options[spec_var.selectedIndex].value; // выбранную специализацию
					document.getElementById('type_spec').value = spec_var_select;
							
				}
				
			</script>

			
	</div>

	<?php } ?>
	
	<input type="hidden" name="add" value="0" />
	
	<div class="order_page_block">
	
		<?php
			
			$summ_oplata = (int) ($params['sposob_oplaty_1_on'] + $params['sposob_oplaty_0_on'] + $params['sposob_oplaty_2_on']);
			
		?>
	
		<div>
			
			<?php if ($params["billing_on"] == 1 && $summ_oplata > 0 && (int) $row->pricezap > 0 ) { ?>
	
				<input type="button" class = "senbutt" value="<?php echo $params["title_sav2"]; ?>" class="button" onclick="datatest();" />
		
			<?php } else {  ?>
	
				<input type="button" class = "senbutt" value="<?php echo $params["title_sav"]; ?>" class="button" onclick="datatest();" />
				
			<?php } ?>
		

		</div>
		
	</div>
	

	</form>

	</div>
	<?php
	}
}

?>