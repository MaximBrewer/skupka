<?php
/**
*Time-Table FS+ - Joomla Component
* @package TT FS+
* @Copyright (C) 2010 FomSoft Plus
* @ All rights reserved
* @ Time-Table FS+ is Commercial Software
**/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

//======================================================== 
class HTML_ttfsp {
//========================================================
public static function ttfspmenu($act){
$active=' class="fsp-active" ';
$noactive=' class="fsp-noactive" ';
?>
<style>
.ttfspmenu  {
	float:left;
	width:160px;
   margin-right: 20px;	
}
.fspcnt {
    margin: 0;
    padding: 0;
	float:left;
	position:relative;
	width:80%;
}
#fspsubmenu {
    list-style: none outside none;
    margin: 0;
    padding: 0;
}
#fspsubmenu li {
    float: left;
    padding: 5px;
	width:160px;
}
.fsp-active {
	font-weight:bold;
	border-left:1px solid #ccc;
	border-top:1px solid #ccc;	
	border-bottom:1px solid #ccc;	
}
.fsp-noactive {
	border-right:1px solid #ccc;
}
.summpr {
	text-align: right;
}
input.inputbox.summchng {
	padding: 10px;
	margin: 0px 0 10px 0;
	background: green;
	color: #fff;
	border: 0;
}
input.inputbox.summchng:hover {
	background: red;
}
div.linebd {
	display: block;
	margin: 1px 0 2px 0;
	border: solid #f5f5f5 1px;
}
#fspsubmenu li a, #fspsubmenu span.nolink {
    color: #0B55C4;
	font-size:11px;
    cursor: pointer;
    height: 12px;
    line-height: 12px;
	white-space:nowrap;
	width:180px;	
}
a.linkorder {
	margin-left: 5px;
}
div.fspcnt.ordtbl table td {
	
	padding: 5px 40px 5px 5px;
	border-bottom: solid #f5f5f5 3px;
	
}
div.fspcnt.ordtbl table.adminlist {
	border-collapse: inherit;
	border-spacing: 2px;
}
div.fspcnt.ordtbl th.title {
	text-align: left;
	padding: 5px;
}
td.checkord input {
	
	margin: 0 0 0 4px;
	
}
input.chkallord {
	margin: 0 0 0 4px;
}
</style>
<script>
	function checkAll( n, fldName ) {
  if (!fldName) {
     fldName = 'cb';
  }
	var f = document.adminForm;
	var c = f.toggle.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
	}
	if (c) {
		document.adminForm.boxchecked.value = n2;
	} else {
		document.adminForm.boxchecked.value = 0;
	}
}
</script>
<div class="ttfspmenu">
<ul id="fspsubmenu">
<li <?php echo $act=="ttimes" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=ttimes"><img src="components/com_ttfsp/imgs/clock.png" /> <?php echo _ttfsp_lang_159; ?></a>
</li>
<li <?php echo $act=="torders" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=torders"><img src="components/com_ttfsp/imgs/orders.png" /> <?php echo _ttfsp_orders; ?></a>
</li>
<li <?php echo $act=="tspec" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=tspec"><img src="components/com_ttfsp/imgs/vcard.png" /> <?php echo _ttfsp_lang_27; ?></a>
</li>
<li <?php echo $act=="sspec" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=sspec"><img src="components/com_ttfsp/imgs/tags.png" /> <?php echo _ttfsp_lang_28; ?></a>
</li>
<li <?php echo $act=="ssect" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=ssect"><img src="components/com_ttfsp/imgs/home.png" /> <?php echo _ttfsp_lang_165; ?></a>
</li>
<li <?php echo $act=="addtimes" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=addtimes"><img src="components/com_ttfsp/imgs/clock_add.png" /> <?php echo _ttfsp_lang_73; ?> 1</a>
</li>
<li <?php echo $act=="addtm" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=addtm"><img src="components/com_ttfsp/imgs/clock_add.png" /> <?php echo _ttfsp_lang_73; ?> 2</a>
</li>
<li <?php echo $act=="elems" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=elems"><img src="components/com_ttfsp/imgs/plug.png" /> <?php echo _ttfsp_lang_92; ?></a>
</li>
<li <?php echo $act=="proftime" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=proftime"><img src="components/com_ttfsp/imgs/clock_edit.png" /> <?php echo _ttfsp_lang_168; ?></a>
</li>
<li <?php echo $act=="config" ? $active : $noactive; ?>>
<a href="<?php echo INDURL; ?>?option=com_ttfsp&amp;act=config"><img src="components/com_ttfsp/imgs/settings.png" /> <?php echo _ttfsp_lang_14; ?></a>
</li>
</ul>
</div>

<?php
}
////////////////////////////////////////////////////////// Формирование списка элементов формы
public static function showelems( $rows, $pageNav, $search, $option ){
		HTML_ttfsp::ttfspmenu("elems");
$act="elems";
		?>
		<div class="fspcnt">
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th width="100%" align="left">
		<?php echo _ttfsp_lang_92; ?>
			</th>
			<td>
		<?php echo _ttfsp_lang_3; ?>
			</td>
			<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="1%">
			#
			</th>
			<th width="1%" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title" width="20%">
		<?php echo _ttfsp_lang_3; ?>
			</th>
			<th class="title" width="50%">
		<?php echo _ttfsp_lang_93; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="5%">
		<?php echo _ttfsp_lang_5; ?>
			</th>
			<th colspan="2" width="5%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_6; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="5%">
		ID
			</th>
		</tr>
		<?php 

		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$link 		= INDURL.'?option=com_ttfsp&amp;act='.$act.'&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1';
			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? _ttfsp_lang_17 : _ttfsp_lang_18;
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$rowNumber = JVERSION=="1.0" ? $pageNav->rowNumber( $i ) : $pageNav->getRowOffset( $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $rowNumber; ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td  align="left">
				<a href="<?php echo $link; ?>" >
				<?php echo $row->name; ?>
				</a>
				</td>
				<td  align="left">
				<a href="<?php echo $link; ?>" >
				<?php echo $row->title; ?>
				</a>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="components/com_ttfsp/imgs/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
					<td>
					<?php 
					if ($row->ordering>1)	echo $pageNav->orderUpIcon( $i ); 
					?>
					</td>
					<td>
					<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
					</td>
				<td align="left">
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="act" value="elems" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
</div>	
<div style="float:none;clear:both;"></div>			
		<?php
}
////////////////////////////////////////////////////////////////////////////////// Редактирование страницы заказа
public static function editorder( $row,  $option, $lists, $params, $act ) {
	
				switch ((int) $row->payment_status) {
				case 0:
					$payment_status = '<span style="color: red;">'._ttfsp_payment_status_0.'</span>';
					break;
				case 1:
					$payment_status = '<span style="color: green;">'._ttfsp_payment_status_1.'</span>';
					break;
				case 2:
					$payment_status = '<span style="color: #89498d;">'._ttfsp_payment_status_2.'</span>';
					break;
				case 3:
					$payment_status = '<span style="color: #fff; background: #000; padding: 3px;">'._ttfsp_payment_status_3.'</span>';
					break;
			}
			
			$date_zakaz = $row->date.' '.$row->hours.':'.$row->minutes;

		?>
		<style>
			div.pmnt_sts {
				margin-top: 30px;
			}
			input.inpch_ml {
				margin: 0 4px 0 0;
			}
		</style>
		
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm" enctype="multipart/form-data" >
			
			<div class="pmnt_sts"><?php echo _ttfsp_status_order_change.'<br>'.$lists['order_status_admin']; ?></div>
			<div class="ckmail"> <input class="inpch_ml" type="checkbox" name="sendto_mail" value="1"><span><?php echo _ttfsp_status_order_tomail; ?></span></div>
			
			
			<div id="successpagettfsp" style="padding: 5px;">
	
					<div class="mainpage_successpagettfsp" style="border: dotted #ccc 3px; padding: 20px;">
		
						<div class="successpagettfsp_title">
			
							<h2><?php echo  _ttfsp_order_number_num ?>  <?php echo $row->number_order;?> </h2> 
			
						</div>
		
						<?php if ($row->office_name != '0') { ?>
		
						<div class="office_success block_s">
			
							<div class="office_success_title">
				
								<p> <?php echo _ttfsp_lang_167 ?> 
				
								<span> <?php echo $row->office_name; ?> </span>
				
								</p> 
				
							</div>
			
							<div class="office_success_adress">
				
								<p> <?php echo _ttfsp_adress_title ?>:
				
								<span> <?php echo $row->office_address ?></span>
				
								</p>
				
							</div>
			
						</div>
		
						<?php } ?>
		
						<div class="specialist_name_success block_s">
			
								<div class="specialist_success_title">
				
									<p class="specialist_fio_success"> <?php echo _ttfsp_lang_44 ?>: <span><?php echo $row->specialist_name; ?></span></p> 
				
								</div>
			
								<div class="specialist_success_cab_spec">
				
								<?php if ($row->number_cabinet) { ?>
				
									<p> <?php echo _ttfsp_number_cabinet ?>:
				
									<strong><?php echo $row->number_cabinet ?></strong>
				
									</p>
				
								<?php } ?>
				
									<p class="specialisations_name_success"> <?php echo _ttfsp_lang_21 ?>: 
				
									<span><?php echo $row->specializations_name; ?></span>
				
									</p>
				
								</div>
			
						</div>
		
					<div class="maininfo_success block_s">
			
						<div class="maininfo_success_title">
				
							<p class="info_success"> <strong><?php echo _ttfsp_lang_information ?>: </strong> </p>
				
							<p> <?php echo $row->info; ?> </p> 
				
						</div>
			
					</div>
		
				<?php if ($params["billing_on"] == 1) { ?>
		
					<div class="payment_success block_s">
			
						<div class="payment_success_status">
				
							<h4 class="status_1"> <?php echo _ttfsp_payment_status_title ?> <?php echo $payment_status ?> </h4>
				
							<h4 class="summa_succ"> <?php echo _ttfsp_lang_summ_spec ?> <span> <?php echo $row->summa.' '.$params['valuta_name'] ?>  </span></h4>

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

		
			<input type="hidden" name="id" value=<?php echo $row->id; ?>>
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="task" value="">
			<input type="hidden" name="act" value="<?php echo $act; ?>">
			
		</form>
	<?php
}

/////////////////////////////////////////// Редактирование свойств элемента
public static function editelem( $row, $option, $lists, $act, $htmlel) {
$title='';
$ronly='';
if ($row->fname=='fio' || $row->fname=='phone') $ronly='readonly="readonly"';
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (form.name.value == '') {
				alert( "<?php echo _ttfsp_lang_103; ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th class="edit">
			 <small><?php echo  _ttfsp_lang_94; ?> [ <?php echo $row->name;?> ]</small> 
			</th>
		</tr>
		</table>

		<table class="adminform" border=1>
		<tr>
			<th colspan="2">
				<?php echo $row->name; ?>
			</th>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_3; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="140" name="name" value="<?php echo $row->name; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_93; ?>
			</td>
			<td>
				<textarea class="inputbox" name="title"  cols="80" rows="2"><?php echo $row->title; ?></textarea>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_95; ?>
			</td>
			<td>
			<?php echo $lists['eltype']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo $lists['selspec']; ?>
			</td>
			<td>
			<?php echo $htmlel; ?>
			</td>
		</tr>		
		<tr>
			<td >
			<?php echo _ttfsp_lang_5; ?>
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_96; ?>
			</td>
			<td>
			<?php echo $lists['required']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_97; ?>
			</td>
			<td>
			<?php echo $lists['readonly']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_181; ?>
			</td>
			<td>
			<?php echo $lists['multisel']; ?>
			</td>
		</tr>		
		<tr>
			<td >
			<?php echo _ttfsp_lang_98; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="14" name="maxlength" <?php echo $ronly; ?> value="<?php echo $row->maxlength; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_99; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="14" name="size"  value="<?php echo $row->size; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_100; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="14" name="css"  value="<?php echo $row->css; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_101; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="140" name="mask" value="<?php echo $row->mask; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_102; ?>
			</td>
			<td>
			<textarea class="inputbox" name="value"  cols="80" rows="7"><?php echo $row->value; ?></textarea>
			</td>
		</tr>
		</table>
		<input type="hidden" name="createdate"  id="createdate" value="<?php echo $row->createdate; ?>" />
		<input type="hidden" name="ordering"  id="ordering" value="<?php echo $row->ordering; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="elems" />
		</form>
		<?php
}

////////////////////////////////////////////////////////////////////////////////// Карточка времени приема
public static function edittime( $row,  $option, $lists, $params, $act, $rowdop ) {
		if (JVERSION=='1.0'){
		mosCommonHTML::loadOverlib();
		mosCommonHTML::loadCalendar();
		} else {
		JHTML::_('behavior.calendar');
		}
	$title = _ttfsp_lang_38;
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (form.dttime.value == '') {
				alert( "<?php echo _ttfsp_lang_39; ?>" );
				return;
			}
			if (form.hrtime.value == '') {
				alert( "<?php echo _ttfsp_lang_40; ?>" );
				return;
			}
			if (form.mntime.value == '') {
				alert( "<?php echo _ttfsp_lang_41; ?>" );
				return;
			}
				submitform( pressbutton );
		}
		</script>
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<table class="adminform" border=1>
		<tr>
			<th colspan="2">
<?php echo $title; ?>
			</th>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_44; ?>
			</td>
			<td>
			<?php echo $lists['sprspec']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_35; ?>
			</td>
			<td>
			<?php 	if (substr(JVERSION,0,3)=='1.5' || substr(JVERSION,0,3)=='1.6' || substr(JVERSION,0,3)=='1.7' || substr(JVERSION,0,3)=='2.5' || substr(JVERSION,0,1)=='3'){
			 echo JHtml::_('calendar',$row->dttime, 'dttime','dttime','%Y-%m-%d' , array('size'=>10));		
			} else { 
			?>
			<input class="inputbox" type="text" size="15" id="dttime" name="dttime" value="<?php echo $row->dttime; ?>">
			 <input type="reset" class="calendar" value="..." onclick="return showCalendar('dttime');" />
			 <?php } ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_36; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="hrtime" value="<?php echo $row->hrtime; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_37; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="mntime" value="<?php echo $row->mntime; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_5; ?>
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_42; ?>
			</td>
			<td>
			<?php echo $lists['reception']; ?>
			</td>
		</tr>
		<tr>
			<td >
			sms
			</td>
			<td>
			<?php echo $lists['sms']; ?>
			</td>
		</tr>		
		<tr>
			<td >
			<?php echo _ttfsp_lang_88; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="150" name="rfio" value="<?php echo $row->rfio; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_89; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="rphone" value="<?php echo $row->rphone; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_109; ?>
			</td>
			<td>
		<?php // echo $row->info; ?>
			<?php
			if (JVERSION=='1.0'){	
				editorArea( 'editor1', $row->info, 'info', '100%;', '600', '75', '30' ) ; 
			} else {	
				$editor = &JFactory::getEditor();
				echo $editor->display( 'info',  $row->info , '100%', '300', '75', '20', true) ;
			}
				?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_142; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="plimit" value="<?php echo $row->plimit; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_summ_spec; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="pricezap" value="<?php echo $row->pricezap; ?>">
			</td>
		</tr>		
		<tr>
			<td >
			<?php echo _ttfsp_lang_110; ?>
			</td>
			<td>
			<?php echo $row->ipuser; ?>
			</td>
		</tr>
		
		</table>
		<?php
		if (count($rowdop)){
		echo '<table class="adminform" border=1>';
		?>
		<tr>
			<th width="1%">
			#
			</th>
			<th class="title" width="1%">
		<?php echo _ttfsp_lang_156; ?>
			</th>
			<th width="25%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_25; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="5%">
		<?php echo _ttfsp_lang_64; ?>
			</th>
			<th width="68%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_109; ?>
			</th>
		</tr>
		
		<?php
		for ($i=0, $n=count($rowdop); $i < $n; $i++) {
			
			$rowd = $rowdop[$i];
			$checked 	= JHTML::_('grid.checkedout',   $rowd, $i );
			?>
			<tr >
				<td>
				<?php echo $i+1; ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td>
				<?php echo $rowd->rfio; ?>
				</td>
				<td>
				<?php echo $rowd->rphone; ?>
				</td>
				<td>
				<?php echo $rowd->info; ?>
				</td>
				</tr>
			<?php
		}	
		echo '</table>';	
		}
		?>
		<input type="hidden" name="id" value=<?php echo $row->id; ?>>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="act" value="<?php echo $act; ?>">
		</form>

	<?php
}
////////////////////////////////////////////////////////////////////////////////// Карточка специалиста
public static function editspec( $row,  $option, $lists, $params, $act ) {
	$title = _ttfsp_lang_20;
		?>
		<style>
	.old_avatars {
		float:left;
		margin-right:20px;		
	}
	.del_avatar {
		cursor:pointer;
		padding:8px;
		background:url(<?php echo SITE_NAME; ?>administrator/components/com_ttfsp/imgs/close.png) no-repeat; 
		width:16px;
		height:16px;
		margin-left:-15px;
	}
	</style>
		<script language="javascript" type="text/javascript">
		function delavatar(num){
			var id="old_avatar"+num;
			document.getElementById(id).innerHTML='';
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (form.name.value == '') {
				alert( "<?php echo _ttfsp_lang_29; ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm" enctype="multipart/form-data" >
		<table class="adminheading">
		<tr>
			<th class="edit">
			 <small><?php echo  _ttfsp_lang_25 ?> [ <?php echo $row->name;?> ]</small> 
			</th>
		</tr>
		</table>
		<table class="adminform" border=1>
		<tr>
			<th colspan="2">
<?php echo $title; ?>
			</th>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_25; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="140" name="name" value="<?php echo $row->name; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_69; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="140" name="specmail" value="<?php echo $row->specmail; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_132; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="140" name="specphone" value="<?php echo $row->specphone; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_number_cabinet; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="140" name="number_cabinet" value="<?php echo $row->number_cabinet; ?>">
			</td>
		</tr>			
		<tr>
			<td >
			<?php echo _ttfsp_lang_5; ?>
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_167; ?>
			</td>
			<td>
			<?php echo $lists['sprsect']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_21._ttfsp_lang_195; ?>
			</td>
			<td>
			<?php echo $lists['sprspec']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_168; ?>
			</td>
			<td>
			<?php echo $lists['sprtime']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_4; ?>
			</td>
			<td>
		<textarea class="inputbox" name="desc"  cols="80" rows="5"><?php echo $row->desc; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_22; ?>
			</td>
			<td>
			<div class="images">
			<?php 
				if ($row->photo){
					$files = explode(';', $row->photo);
					for ($i=0;$i<count($files);$i++){	
						echo '<div class="old_avatars" id="old_avatar'.$i.'"><img src="'.$params["url_site"].$files[$i].'" />  <span class="del_avatar" onclick="delavatar('.$i.');"></span> <input type="hidden" name="old_avatar[]" value="'.$files[$i].'"></div>';
					}
				}
			?>
			</div>
			
			<div style="clear:both;"><br />
			<input type="file" id="avatar" name="avatar[]" multiple="" value="" size="30"/>
			</div>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_24; ?>
			</td>
			<td>
			<?php echo $lists['offphoto']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_207; ?>
			</td>
			<td>
			<?php echo $lists['adddt']; ?>
			</td>
		</tr>	
		<tr>
			<td >
			<?php echo _ttfsp_lang_208; ?>
			</td>
			<td>
			<?php echo $lists['addtm']; ?>
			</td>
		</tr>		
		<tr>
			<td >
			<?php echo _ttfsp_lang_111; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="idusr" value="<?php echo $row->idusr; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_summ_spec; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="pricespec" value="<?php echo $row->pricespec; ?>">
			</td>
		</tr>

		</table>
		<input type="hidden" name="rowphoto" value=<?php echo $row->photo; ?>>		
		<input type="hidden" name="id" value=<?php echo $row->id; ?>>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="act" value="<?php echo $act; ?>">
		</form>

	<?php
}
////////////////////////////////////////////////////////////////////////////////// Карточка справочника специализаций
public static function editsprspec( $row,  $option, $lists, $params, $act ) {
$title=$act=='sspec' ? _ttfsp_lang_26:_ttfsp_lang_166;
$title=$act=='proftime' ? _ttfsp_lang_168:$title;
		?>
		<style>
	.old_avatars {
		float:left;
		margin-right:20px;		
	}
	.del_avatar {
		cursor:pointer;
		padding:8px;
		background:url(<?php echo SITE_NAME; ?>administrator/components/com_ttfsp/imgs/close.png) no-repeat; 
		width:16px;
		height:16px;
		margin-left:-15px;
	}
	</style>
		<script language="javascript" type="text/javascript">
		function delavatar(num){
			var id="old_avatar"+num;
			document.getElementById(id).innerHTML='';
		}		
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (form.name.value == '') {
				alert( "<?php echo _ttfsp_lang_19; ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm" enctype="multipart/form-data" >
		<table class="adminheading">
		<tr>
			<th class="edit">
			 <small><?php echo  _ttfsp_lang_3 ?> [ <?php echo $row->name;?> ]</small> 
			</th>
		</tr>
		</table>
		<table class="adminform" border=1>
		<tr>
			<th colspan="2">
<?php echo $title; ?>
			</th>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_3; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="140" name="name" value="<?php echo $row->name; ?>">
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_5; ?>
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_4; ?>
			</td>
			<td>
		<textarea class="inputbox" name="desc"  cols="80" rows="5"><?php echo $row->desc; ?></textarea>
			</td>
		</tr>
<?php if ($act=='proftime') { ?>		
		<tr>
			<td>
			<?php echo _ttfsp_lang_169; ?>
			</td>
			<td>
		<textarea class="inputbox" name="timehm"  cols="40" rows="15"><?php echo $row->timehm; ?></textarea>
			</td>
		</tr>		
<?php } else {
if ($act=='ssect') { ?>	
		<tr>
			<td><?php echo _ttfsp_adress_title; ?></td>
			<td><textarea class="inputbox" name="address"  cols="40" rows="5"><?php echo $row->address; ?></textarea></td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_22; ?>
			</td>
			<td>
			<div class="images">
			<?php 
				if ($row->photo){
					$files = explode(';', $row->photo);
					for ($i=0;$i<count($files);$i++){	
						echo '<div class="old_avatars" id="old_avatar'.$i.'"><img src="'.$params["url_site"].$files[$i].'" />  <span class="del_avatar" onclick="delavatar('.$i.');"></span> <input type="hidden" name="old_avatar[]" value="'.$files[$i].'"></div>';
					}
				}
			?>
			</div>
			<div style="clear:both;"><br />
			<input type="file" id="avatar" name="avatar[]" multiple="" value="" size="30"/>
			</div>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_24; ?>
			</td>
			<td>
			<?php echo $lists['offphoto']; ?>
			</td>
		</tr>	

		<input type="hidden" name="rowphoto" value="<?php echo $row->photo; ?>">	
<?php } } ?>			
		</table>

		<input type="hidden" name="id" value=<?php echo $row->id; ?>>
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="act" value="<?php echo $act; ?>">
		</form>
	<?php
}
////////////////////////////////////////////////////////////////////////////////// Таблица  времени приема
public static function showtime( $rows, $pageNav, $lists, $option, $act, $searchd, $searcht ){
		HTML_ttfsp::ttfspmenu($act);
$title=_ttfsp_lang_32;
		if (JVERSION=='1.0'){
		mosCommonHTML::loadOverlib();
		mosCommonHTML::loadCalendar();
		} else {
		JHTML::_('behavior.calendar');
		}
		?>
	<div class="fspcnt">		
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th width="100%" align="left">
		<?php echo $title; ?>
			</th>
			<td>
			<?php echo $lists['sztime']; ?>
			</td>
			<td nowrap="nowrap">
		<?php echo _ttfsp_lang_66; ?>
			</td>
			<td nowrap="nowrap">
			<?php 	if (substr(JVERSION,0,3)=='1.5' || substr(JVERSION,0,3)=='1.6' || substr(JVERSION,0,3)=='1.7' || substr(JVERSION,0,3)=='2.5' || substr(JVERSION,0,1)=='3'){
			 echo _ttfsp_lang_128.JHtml::_('calendar',$searcht, 'searcht','searcht','%Y-%m-%d' , array('size'=>10));		
			 echo _ttfsp_lang_129.JHtml::_('calendar',$searchd, 'searchd','searchd','%Y-%m-%d' , array('size'=>10));
			} else { 
			echo _ttfsp_lang_128;
			?>
			<input class="inputbox" type="text" size="15" id="searcht" name="searcht" value="<?php echo $searcht; ?>">
			 <input type="reset" class="calendar" value="..." onclick="return showCalendar('searcht');" />
			<?php echo _ttfsp_lang_129; ?> 
			<input class="inputbox" type="text" size="15" id="searchd" name="searchd" value="<?php echo $searchd; ?>">
			 <input type="reset" class="calendar" value="..." onclick="return showCalendar('searchd');" />
			<?php } ?> 
			</td>			
			<td nowrap="nowrap">
		<?php echo _ttfsp_lang_44; ?>
			</td>
			<td>
			<?php echo $lists['sprspec']; ?>
			</td>
			<td>
				<input type="button" value="Ok" class="inputbox" onClick="document.adminForm.action='<?php echo INDURL; ?>';document.adminForm.submit();" />
			</td>
			<td>
				<?php $ahref = INDURL."?option=com_ttfsp&format=row&outcsv=1"; ?>
				<input type="button" value="CSV" class="inputbox" onClick="document.adminForm.action='<?php echo $ahref; ?>';document.adminForm.submit();"/>
			</td>
			<td>
				<?php $ahref = INDURL."?option=com_ttfsp&format=row&outxml=1"; ?>
				<input type="button" value="XML" class="inputbox" onClick="document.adminForm.action='<?php echo $ahref; ?>';document.adminForm.submit();"/>
			</td>
			</tr>
			<tr>

				<td class="summpr" colspan="9"> <?php echo _ttfsp_lang_summ_spec ?>
				<?php $ahref = INDURL."?option=com_ttfsp&format=row&changeprice=1"; ?>
				<input type="text" value="0" class="summvalue" id="summvalue" name = "summvalue"/>
				<input type="button" value="<?php echo _ttfsp_lang_summ_change ?>" class="inputbox summchng" onClick="document.adminForm.action='<?php echo $ahref; ?>';document.adminForm.submit();"/></td>

			</tr>
		</table>
		<div class="linebd"></div>
		<table class="adminlist">
		<tr>
			<th width="1%">
			#
			</th>
			<th width="1%" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title" width="5%">
		<?php echo _ttfsp_lang_35; ?>
			</th>
			<th width="5%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_36; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="5%">
		<?php echo _ttfsp_lang_37; ?>
			</th>
			<th width="3%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_43; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="30%">
		<?php echo _ttfsp_lang_27; ?>
			</th>
			<th width="30%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_45; ?>
			</th>
			<th width="3%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_5; ?>
			</th>
			<th width="1%" class="title" nowrap="nowrap" title="<?php echo _ttfsp_lang_142._ttfsp_lang_143; ?>">
		<?php echo _ttfsp_lang_144; ?>
			</th>	
			<th  nowrap="nowrap" class="title" width="1%">
		ID
			</th>
			<th  nowrap="nowrap" class="title" width="1%">
		sms
			</th>		
		</tr>
		<?php 

		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$links 		= INDURL.'?option=com_ttfsp&amp;act=tspec&amp;task=editA&amp;id='. $row->idspec. '&amp;hidemainmenu=1';
			$link 		= INDURL.'?option=com_ttfsp&amp;act='.$act.'&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1';
			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? _ttfsp_lang_17 : _ttfsp_lang_18;
			$img1 	= $row->reception ? 'publish_x.png' : 'tick.png';
			$task1 	= $row->reception ? 'unreception' : 'reception';
			$alt1 	= $row->reception ? _ttfsp_lang_42 : _ttfsp_lang_43;
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$rowNumber = JVERSION=="1.0" ? $pageNav->rowNumber( $i ) : $pageNav->getRowOffset( $i );
			$sms = $row->sms ? 'V':'';
			if ($row->iduser){
			if (JVERSION== '1.0'){
			$user_link = '<a href="'.INDURL.'?option=com_users&task=editA&hidemainmenu=1&id='. $row->iduser.'" target="blank"> '.$row->iduser.'</a>';
			} else {
				if (substr(JVERSION,0,3)=='1.6' || substr(JVERSION,0,3)=='1.7' || substr(JVERSION,0,3)=='2.5' || substr(JVERSION,0,1)=='3'){
					$user_link = '<a href="'.INDURL.'?option=com_users&task=user.edit&id='. $row->iduser.'" target="blank"> '.$row->iduser.'</a>';
				} else {
					$user_link = '<a href="'.INDURL.'?option=com_users&view=user&task=edit&cid='. $row->iduser.'" target="blank"> '.$row->iduser.'</a>';
				}
			}
			} else {
				$user_link ='';
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $rowNumber; ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td  align="left">
				<a href="<?php echo $link; ?>" >
				<?php echo $row->dttime; ?>
				</a>
				</td>
				<td align="left">
					<?php echo $row->hrtime; ?>
				</td>
				<td align="left">
					<?php echo $row->mntime; ?>
				</td>

				<td align="center">
				<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task1;?>')">
				<img src="components/com_ttfsp/imgs/<?php echo $img1;?>" width="12" height="12" border="0" alt="<?php echo $alt1; ?>" />
				</a>
				</td>
				<td align="left">
				<a href="<?php echo $links; ?>" target="_blank">
					<?php echo $row->name; ?>
				</a>	
				</td>
				<td align="left">
					<?php 
					if ($row->rfio){
					echo $row->rfio.' '.$row->rphone; 
					} else {
					echo $user_link.' '.$row->fio.' '.$row->phone; 
					}
					?>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="components/com_ttfsp/imgs/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
				<td align="left">
					<?php echo $row->plimit; ?>
				</td>				
				<td align="left">
					<?php echo $row->id; ?>
				</td>
				<td align="center">
					<?php echo $sms; ?>
				</td>				
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="<?php echo $act; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
</div>	
<div style="float:none;clear:both;"></div>			
<?php
}
////////////////////////////////////////////////////////////////////////////////// Таблица  специалистов
public static function showspec( $rows, $pageNav, $search, $option, $act ){
		HTML_ttfsp::ttfspmenu($act);
$title=_ttfsp_lang_27;
		?>
	<div class="fspcnt">		
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th width="100%" align="left">
		<?php echo $title; ?>
			</th>
			<td>
		<?php echo _ttfsp_lang_2; ?>
			</td>
			<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title" width="20%">
		<?php echo _ttfsp_lang_25; ?>
			</th>
			<th width="3%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_5; ?>
			</th>
			<th colspan="2" width="5%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_6; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="80%">
		<?php echo _ttfsp_lang_4; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="1%">
		ID
			</th>
		</tr>
		<?php 

		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$link 		= INDURL.'?option=com_ttfsp&amp;act='.$act.'&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1';
			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? _ttfsp_lang_17 : _ttfsp_lang_18;
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$rowNumber = JVERSION=="1.0" ? $pageNav->rowNumber( $i ) : $pageNav->getRowOffset( $i );

			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $rowNumber; ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td  align="left">
				<a href="<?php echo $link; ?>" >
				<?php echo $row->name; ?>
				</a>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="components/com_ttfsp/imgs/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
					<td>
					<?php echo $pageNav->orderUpIcon( $i ); ?>
					</td>
					<td>
					<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
					</td>
				<td align="left">
					<?php echo $row->desc; ?>
				</td>
			<?php
			if ($row->idusr){
			
			if (JVERSION== '1.0'){
					$user_link = '<a href="'.INDURL.'?option=com_users&task=edit&hidemainmenu=1&cid[]='.$row->idusr.'" target="blank">'.$row->idusr.'</a>';			
			} else {
				if (substr(JVERSION,0,3)=='1.6' || substr(JVERSION,0,3)=='1.7' || substr(JVERSION,0,3)=='2.5' || substr(JVERSION,0,1)=='3'){
					$user_link = '<a href="'.INDURL.'?option=com_users&task=user.edit&id='. $row->idusr.'" target="blank"> '.$row->idusr.'</a>';
				} else {
					$user_link = '<a href="'.INDURL.'?option=com_users&view=user&task=edit&hidemainmenu=1&cid='.$row->idusr.'" target="blank">'.$row->idusr.'</a>';			
				}
			}	
			echo '<td align="left">
				'.$user_link.'
				</td></tr>';
			} else {
				echo '<td></td></tr>';
			}	
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="<?php echo $act; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
</div>	
<div style="float:none;clear:both;"></div>			
<?php
}
////////////////////////////////////////////////////////////////////////////////// Таблица справочника специализаций
public static function showsprspec( $rows, $pageNav, $search, $option, $act ){
		HTML_ttfsp::ttfspmenu($act);
$title=$act=='sspec' ? _ttfsp_lang_28:_ttfsp_lang_165;
$title=$act=='proftime' ? _ttfsp_lang_168:$title;
		?>
	<div class="fspcnt">		
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th width="100%" align="left">
		<?php echo $title; ?>
			</th>
			<td>
		<?php echo _ttfsp_lang_2; ?>
			</td>
			<td>
				<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20" class="title">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title" width="20%">
		<?php echo _ttfsp_lang_3; ?>
			</th>
			<th width="3%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_5; ?>
			</th>
			<th colspan="2" width="5%" class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_6; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="80%">
		<?php echo _ttfsp_lang_4; ?>
			</th>
			<th  nowrap="nowrap" class="title" width="1%">
		ID
			</th>
		</tr>
		<?php 

		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$link 		= INDURL.'?option=com_ttfsp&amp;act='.$act.'&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1';
			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? _ttfsp_lang_17 : _ttfsp_lang_18;
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$rowNumber = JVERSION=="1.0" ? $pageNav->rowNumber( $i ) : $pageNav->getRowOffset( $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $rowNumber; ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td  align="left">
				<a href="<?php echo $link; ?>" >
				<?php echo $row->name; ?>
				</a>
				</td>
				<td align="center">
				<a href="javascript: void(0);" onClick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
				<img src="components/com_ttfsp/imgs/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt; ?>" />
				</a>
				</td>
					<td>
					<?php echo $pageNav->orderUpIcon( $i ); ?>
					</td>
					<td>
					<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
					</td>
				<td align="left">
					<?php echo $row->desc; ?>
				</td>
				<td align="left">
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="<?php echo $act; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
</div>	
<div style="float:none;clear:both;"></div>			
<?php
}
////////////////////////////////////////////////////////////////////////////////// Вывод списка заказов
public static function orders_list( $rows, $pageNav, $lists, $option, $act, $searchd, $searcht, $num_order ){
		HTML_ttfsp::ttfspmenu($act);
		
		$link_cancel = JURI::base() .'index.php?option=com_ttfsp&act=torders';
		
		?>


		
<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">		
	<div class="fspcnt ordtbl">		
		
			
		<div class="select_ord">
			
			<div class="top_ordtbl">
			
				<div class="tlordfltr">
					<?php echo _ttfsp_status_order_num_filter; ?>
					<br>
					<input type="text" name="num_order" id="num_order" value = "<?php echo $num_order; ?>">
			
				</div>
			
				<div class="top_tlord">
					<?php echo _ttfsp_payment_status_title; ?>
					<br>
					<?php echo $lists['order_status_adminlist']; ?>
				</div>
				
				<div class="top_tlord">
					
					
					<?php echo _ttfsp_lang_44; ?>
					<br>
					<?php echo $lists['sprspec']; ?>
				
				</div>
			
			</div>
			
			<div class="bottom_ordtbl">	
			
				<div class="tlord">
				
					<?php echo _ttfsp_lang_66.'('._ttfsp_lang_128.')'; ?>
				
					<br>
				
					<?php 
						echo JHtml::_('calendar',$searcht, 'searcht','searcht','%Y-%m-%d' , array('size'=>10));		
					
					?> 
				 
				</div>
			
				<div class="tlord">
				
				
					 <?php echo _ttfsp_lang_66.'('._ttfsp_lang_129.')'; ?>
				 
					 <br>
				
					 <?php echo JHtml::_('calendar',$searchd, 'searchd','searchd','%Y-%m-%d' , array('size'=>10)); ?>
				
				</div>
				
				<div class="tlord">
				
				
					 <?php echo _ttfsp_lang_36; ?>
				 
					 <br>
				
					 <?php echo $lists['hours_oders']; ?>
				
				</div>
				
				<div class="tlord">
				
				
					 <?php echo _ttfsp_lang_37; ?>
				 
					 <br>
				
					 <?php echo $lists['minutes_oders']; ?>
				
				</div>		
			
				<div class="tlord">
				
					<input class="submorders" type="button" value="Ok" onClick="document.adminForm.action='<?php echo INDURL; ?>';document.adminForm.submit();" />
					<input class="submorders" type="button" value="<?php echo _ttfsp_reset; ?>" onClick="resetbuttons ();" />
					<script>
		
					function resetbuttons () {
						
						document.getElementById('searcht').value = '';
						document.getElementById('searchd').value = '';
						document.getElementById('num_order').value = '';
						document.getElementById('hours_select').value = '777';
						document.getElementById('minutes_select').value = '777';
						document.getElementById("payment_status").options[0].selected=true;
						document.getElementById("search").options[0].selected=true;
						document.adminForm.action='<?php echo INDURL; ?>';document.adminForm.submit();
			
					}
		
					</script>
				</div>	
				
			</div>
			
		</div>
		
		
		<table class="adminheading">
		<tr>
			<th width="100%" align="left">
		<?php echo _ttfsp_orders; ?>
			</th>
		</tr>
		</table>
		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20" class="title">
			<input class="chkallord" type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
			</th>
			<th class="title">
		<?php echo _ttfsp_number_zakaz_admin; ?>
			</th>
			<th class="title" nowrap="nowrap">
		<?php echo _ttfsp_status_order_adm; ?>
			</th>
			<th class="title" nowrap="nowrap">
		<?php echo _ttfsp_lang_159; ?>
			</th>
			<th  nowrap="nowrap" class="title">
		<?php echo _ttfsp_lang_information; ?>
			</th>
			<th  nowrap="nowrap" class="title">
		<?php echo _ttfsp_lang_44 ?>
			</th>
			<th  nowrap="nowrap" class="title">
		<?php echo _ttfsp_lang_167 ?>
			</th>
		</tr>
		<?php 

		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			
			$row = $rows[$i];
			$link 		= INDURL.'?option=com_ttfsp&amp;act='.$act.'&amp;task=editA&amp;id='. $row->id. '&amp;hidemainmenu=1';
			$img 	= $row->published ? 'tick.png' : 'publish_x.png';
			$task 	= $row->published ? 'unpublish' : 'publish';
			$alt 	= $row->published ? _ttfsp_lang_17 : _ttfsp_lang_18;
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );
			$rowNumber = JVERSION=="1.0" ? $pageNav->rowNumber( $i ) : $pageNav->getRowOffset( $i );
			
			switch ((int) $row->payment_status) {
				case 0:
					$payment_status = '<span style="color: red;">'._ttfsp_payment_status_0.'</span>';
					break;
				case 1:
					$payment_status = '<span style="color: green;">'._ttfsp_payment_status_1.'</span>';
					break;
				case 2:
					$payment_status = '<span style="color: #89498d;">'._ttfsp_payment_status_2.'</span>';
					break;
				case 3:
					$payment_status = '<span style="color: #fff; background: #000; padding: 3px;">'._ttfsp_payment_status_3.'</span>';
					break;
			}
			
			$time_order = $row->date. ' '. $row->hours. ' ' . $row->minutes;
			
			?>
			<tr class="<?php echo "row$k"; ?>">
				
				<td>
					
					<?php echo $rowNumber; ?>
				
				</td>
				<td class="checkord">
					
					<?php echo $checked; ?>
				
				</td>
				<td  align="left" class="infoord">
				<a class="linkorder" href="<?php echo $link; ?>" >
				
				<?php 
					
					if ($row->number_order) {
					
						echo _ttfsp_order_number_num.$row->number_order;
					
					}
					
					else {
						
						echo _ttfsp_no_number_order;
						
					}
					
				?>
				
				</a>
				</td>
				<td align="center" class="infoord">
					
					<?php echo $payment_status; ?>
				
				</td>
				
				<td class="infoord">
					
					<?php echo $time_order; ?>
					
				</td>
				
				<td align="left" class="infoord">
					
					<?php echo $row->info; ?>
					
				</td>
				
				<td align="left" class="infoord">
					
					<?php echo $row->specialist_name; ?>
					
				</td>
				
				<td align="left" class="infoord">
					
					<?php echo $row->office_name; ?>
					
				</td>
				
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="<?php echo $act; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0">
		</form>
</div>	
<div style="float:none;clear:both;"></div>	
<?php
	}
////////////////////////////////////////////////////////////////////////////////// Настройки
	public static function settings( $option, &$params, &$lists ) {
			HTML_ttfsp::ttfspmenu("config");
		?>
	<div class="fspcnt">	
		<style type="text/css">
		table.adminform {
			margin: 0!important;
		}		
		.pg {
		float:left;
		cursor:pointer;
		padding:3px;
		margin:-1px 3px;
		background:#ffffff!important;
		border-top:1px solid #ccc;
		border-left:1px solid #ccc;
		border-right:1px solid #ccc;
		border-bottom:1px solid #ccc;
		background:#fff;
		font-size:11px;
		color:#999;	
		}
		.pga {
		float:left;
		cursor:pointer;
		padding:3px;
		margin:-1px 3px;
		background:#F9F9F9!important;
		border-top:1px solid #ccc;
		border-left:1px solid #ccc;
		border-right:1px solid #ccc;
		border-bottom:1px solid #F9F9F9;
		background:#fff;
		font-size:11px;
		color:#0B55C4;	
		}
		</style>
		<script language="javascript" type="text/javascript">
		function page(n) {
		document.getElementById("mypageconf").value=n;
		for (i=1; i<6; i++){
		document.getElementById("page"+i).style.display="none";		
		document.getElementById("pg"+i).className="pg";
		}
		document.getElementById("page"+n).style.display="block";
		document.getElementById("pg"+n).className="pga";			
		}
		</script>
		
		
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<div style="position;relative;padding-top:10px;">
			<div id="pg1" class="pga"  onclick="page(1);"><?php echo  _ttfsp_lang_14; ?></div>
			 <div id="pg2"  class="pg" onclick="page(2);"><?php echo  _ttfsp_lang_150; ?></div> 
			 <div id="pg3"  class="pg" onclick="page(3);"><?php echo  _ttfsp_lang_186; ?></div> 
			 <div id="pg4"  class="pg" onclick="page(4);"><?php echo  _ttfsp_lang_billing_settings; ?></div>
			 <div id="pg5"  class="pg" onclick="page(5);"><?php echo  _ttfsp_oder_page; ?></div>			 
		 </div>
		<div style="float:none;clear:both;"></div>
		<div class="page">
		<div id="page1" style="display:block;">		

		<table style="width:100%;" class="adminform">
		<tr>
			<h3 class="titlttab">
				<?php echo _ttfsp_lang_14; ?>
			</h3>
		</tr>
		<tr>
			<td>

			<table width="100%" class="paramlist">
		<tr>
			<td>
			<?php echo _ttfsp_lang_124; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="120" name="params[url_site]" value="<?php echo $params['url_site']; ?>">
			</td>
		</tr>	
		<tr>
			<td>
				<?php echo _ttfsp_lang_16; ?>
			</td>
			<td>
			<?php echo $lists['del_db']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo _ttfsp_lang_90; ?>
			</td>
			<td>
			<?php echo $lists['reguser']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_7; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="120" name="params[avatarspath]" value="<?php echo $params['avatarspath']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_68; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="120" name="params[email]" value="<?php echo $params['email']; ?>">
			</td>
		</tr>
		<tr>
			<td>
				<?php echo _ttfsp_lang_70; ?>
			</td>
			<td>
			<?php echo $lists['offemail']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_185; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="120" name="params[moderators]" value="<?php echo $params['moderators']; ?>">
			</td>
		</tr>		
		<tr>
			<td>
				<?php echo _ttfsp_lang_120; ?>
			</td>
			<td>
			<?php echo $lists['viewspec']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo _ttfsp_lang_184; ?>
			</td>
			<td>
			<?php echo $lists['editspec']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo _ttfsp_lang_216; ?>
			</td>
			<td>
			<?php echo $lists['onespec']; ?>
			</td>
		</tr>			
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_122.date( 'Y-m-d H:i:s', time());
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[cortime]" value="<?php echo $params['cortime']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_123;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[notime]" value="<?php echo $params['notime']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_145;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[del_hist]" value="<?php echo $params['del_hist']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_163; ?>
			</td>
			<td>
			<?php echo $lists['decode_in']; ?>
			</td>
		</tr>	
		<tr>
			<td>
			<?php echo _ttfsp_lang_196; ?>
			</td>
			<td>
			<?php echo $lists['jcomment'] ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_201;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="120" name="params[tjcomment]" value="<?php echo $params['tjcomment']; ?>">
			</td>
		</tr>		
		<tr>
			<td>
			<?php echo _ttfsp_lang_198; ?>
			</td>
			<td>
			<?php echo $lists['viewuser'] ?>
			</td>
		</tr>	
		<tr>
			<td>
			<?php echo _ttfsp_lang_199; ?>
			</td>
			<td>
			<?php echo $lists['modiuser'] ?>
			</td>
		</tr>			
		<tr>
			<td colspan="2">
			<?php
			echo _ttfsp_lang_133;
			?>
			</td>
		</tr>				
		<tr>
			<td>
				<?php echo _ttfsp_lang_137; ?>
			</td>
			<td>
			<?php echo $lists['qtsms_on']; ?>
			</td>
		</tr>		
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_134;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[qtsms_login]" value="<?php echo $params['qtsms_login']; ?>">
			</td>
		</tr>				
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_135;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[qtsms_password]" value="<?php echo $params['qtsms_password']; ?>">
			</td>
		</tr>					
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_136;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[qtsms_host]" value="<?php echo $params['qtsms_host']; ?>">
			</td>
		</tr>	
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_138;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[qtsms_phone]" value="<?php echo $params['qtsms_phone']; ?>">
			</td>
		</tr>	
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_140;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[qtsms_sender]" value="<?php echo $params['qtsms_sender']; ?>">
			</td>
		</tr>	
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_139;
			?>
			</td>
			<td>
				<textarea class="inputbox" name="params[qtsms_message]"  cols="80" rows="2"><?php echo $params['qtsms_message']; ?></textarea>			
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_139a;
			?>
			</td>
			<td>
				<textarea class="inputbox" name="params[sms_message]"  cols="80" rows="2"><?php echo $params['sms_message']; ?></textarea>			
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_215;
			?>
			</td>
			<td>
				<textarea class="inputbox" name="params[test_phone]"  cols="80" rows="2"><?php echo $params['test_phone']; ?></textarea>			
			</td>
		</tr>
		
		<tr>
			<td>
				<?php echo _ttfsp_lang_214; ?>
			</td>
			<td>
			<input type="text" name="params[sms_hour]" value="<?php echo $params['sms_hour']; ?>" class="text_area" size="15" />
			</td>
		</tr>			
		<tr>
			<td>
				<?php echo _ttfsp_lang_213; ?>
			</td>
			<td>
			<input type="text" name="params[cronkey]" value="<?php echo $params['cronkey']; ?>" class="text_area" size="115" />
			</td>
		</tr>		
			</table>
			</td>
		</tr>
		</table>
		</div>
		
		<div id="page2" style="display:none;">		

		<table style="width:100%;" class="adminform">
		<tr>
			<h3 class="titlttab">
				<?php echo _ttfsp_lang_150; ?>
			</h3>
		</tr>
		<tr>
			<td>
			<table width="100%" class="paramlist">
		<tr>
			<td>
			<?php echo _ttfsp_lang_10; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="10" name="params[avatarw]" value="<?php echo $params['avatarw']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_15; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="10" name="params[avatarh]" value="<?php echo $params['avatarh']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_141;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[title_btn]" value="<?php echo $params['title_btn']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_146;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[title_nor]" value="<?php echo $params['title_nor']; ?>">
			</td>
		</tr>			
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_147;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[title_sav]" value="<?php echo $params['title_sav']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_title_button_save2;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="80" name="params[title_sav2]" value="<?php echo $params['title_sav2']; ?>">
			</td>
		</tr>			
		<tr>
			<td colspan="2">
			<?php
			echo _ttfsp_lang_125;
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<?php
			echo _ttfsp_lang_151;
			?>
			</td>
		</tr>		
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_126;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[bgcolor]" value="<?php echo $params['bgcolor']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_127;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[fontcolor]" value="<?php echo $params['fontcolor']; ?>">
			</td>
		</tr>	
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_157;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="32" name="params[dop_text]" value="<?php echo $params['dop_text']; ?>">
			</td>
		</tr>			
		<tr>
			<td colspan="2">
			<?php
			echo _ttfsp_lang_152;
			?>
			</td>
		</tr>		
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_126;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[bgcolor1]" value="<?php echo $params['bgcolor1']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_127;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[fontcolor1]" value="<?php echo $params['fontcolor1']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_157;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="32" name="params[dop_text1]" value="<?php echo $params['dop_text1']; ?>">
			</td>
		</tr>			
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_158;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="32" name="params[dop_text2]" value="<?php echo $params['dop_text2']; ?>">
			</td>
		</tr>			
		<tr>
			<td colspan="2">
			<?php
			echo _ttfsp_lang_153;
			?>
			</td>
		</tr>		
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_126;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[bgcolor2]" value="<?php echo $params['bgcolor2']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_127;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[fontcolor2]" value="<?php echo $params['fontcolor2']; ?>">
			</td>
		</tr>	
		<tr>
			<td colspan="2">
			<?php
			echo _ttfsp_lang_154;
			?>
			</td>
		</tr>		
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_126;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[bgcolor3]" value="<?php echo $params['bgcolor3']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_127;
			?>
			</td>
			<td>
			<input class="inputbox" type="text" size="12" name="params[fontcolor3]" value="<?php echo $params['fontcolor3']; ?>">
			</td>
		</tr>	
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_155;
			?>
			</td>
			<td>
				<textarea class="inputbox" name="params[user_css]"  cols="80" rows="20"><?php echo $params['user_css']; ?></textarea>			
			</td>
		</tr>	
		
			</table>
			</td>
		</tr>
		</table>
		</div>



		<div id="page3" style="display:none;">		

		<table style="width:100%;" class="adminform">
		<tr>
			<h3 class="titlttab">
				<?php echo _ttfsp_lang_186; ?>
			</h3>
		</tr>
		<tr>
			<td>
			<table width="100%" class="paramlist">
			
		<tr>
			<td>
			<?php echo _ttfsp_lang_mail_spetialisations; ?>
			</td>
			<td>
		<?php echo $lists['mail_spetialisations_on']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_mail_uchrejdeniya; ?>
			</td>
			<td>
		<?php echo $lists['mail_uchrejdeniya_on']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_187; ?>
			</td>
			<td>
		<?php echo $lists['onmsg']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_188;
			?>
			</td>
			<td>
				<textarea class="inputbox" name="params[createmsg]"  cols="80" rows="10"><?php echo $params['createmsg']; ?></textarea>			
			</td>
		</tr>			
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_189;
			?>
			</td>
			<td>
				<textarea class="inputbox" name="params[yesrecept]"  cols="80" rows="10"><?php echo $params['yesrecept']; ?></textarea>			
			</td>
		</tr>			
		<tr>
			<td>
			<?php
			echo _ttfsp_lang_190;
			?>
			</td>
			<td>
				<textarea class="inputbox" name="params[norecept]"  cols="80" rows="10"><?php echo $params['norecept']; ?></textarea>			
			</td>
		</tr>			
		<tr>
			<td colspan="2">
			<?php
			echo _ttfsp_lang_192;
			?>
			</td>
		</tr>					

		
		</table>
			</td>
		</tr>
		</table>		
		</div>
		<div id="page4" style="display:none;">
			
		<table style="width:100%;" class="adminform">
		<tr>
			<h3 class="titlttab">
				<?php echo _ttfsp_lang_billing_settings; ?>
			</h3>
		</tr>
		<tr>
			<td>
			<table width="100%" class="paramlist">
		<tr>
			<td>
			<?php echo _ttfsp_lang_billing_on; ?>
			</td>
			<td>
		<?php echo $lists['billing_on']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_billing_title_on; ?>
			</td>
			<td>
		<?php echo $lists['billing_on_title']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_billing_title_on_2; ?>
			</td>
			<td>
		<?php echo $lists['billing_on_title_2']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_type_number_order; ?>
			</td>
			<td>
		<?php echo $lists['type_number_order']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_simbols_count; ?>
			</td>
			<td>
		<input class="inputbox" type="text" size="12" name="params[count_symbols]" value="<?php echo $params['count_symbols']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_valuta; ?>
			</td>
			<td>
		<input class="inputbox" type="text" size="12" name="params[valuta_name]" value="<?php echo $params['valuta_name']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<h3 class="titlttab">
				<?php echo _ttfsp_lang_acceces ?>
			</h3>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<h4 class="subtl">
				<?php echo _ttfsp_lang_order_on_place ?>
			</h4>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_order_on_place_title; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[title_oplata_var1]" value="<?php echo $params['title_oplata_var1']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_on; ?>
			</td>
			<td>
				<?php echo $lists['sposob_oplaty_0_on']; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<h4 class="subtl">
				<?php echo 'Z-PAYMENT' ?>
			</h4>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_order_on_place_title; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[title_oplata_var2]" value="<?php echo $params['title_oplata_var2']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_on; ?>
			</td>
			<td>
				<?php echo $lists['sposob_oplaty_1_on']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_zpayment_id; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[lang_zpayment_id]" value="<?php echo $params['lang_zpayment_id']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_merchant_key; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[merchant_key_zpayment]" value="<?php echo $params['merchant_key_zpayment']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_password_ini; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[password_ini_zpayment]" value="<?php echo $params['password_ini_zpayment']; ?>">
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<h4 class="subtl">
				<?php echo _ttfsp_yandex_kassa_h2 ?>
			</h4>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_order_on_place_title; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[title_oplata_var3]" value="<?php echo $params['title_oplata_var3']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_lang_on; ?>
			</td>
			<td>
				<?php echo $lists['sposob_oplaty_2_on']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_yandex_kassa_shopid; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[yandex_kassa_shopid]" value="<?php echo $params['yandex_kassa_shopid']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_yandex_kassa_scid; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[yandex_kassa_scid]" value="<?php echo $params['yandex_kassa_scid']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_yandex_kassa_password; ?>
			</td>
			<td>
		<input  class="inputbox" type="text" size="12" name="params[yandex_kassa_password]" value="<?php echo $params['yandex_kassa_password']; ?>">
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_yandex_kassa_select; ?>
			</td>
			<td>
				<?php echo $lists['yandex_kassa_select']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _ttfsp_test_mode; ?>
			</td>
			<td>
				<?php echo $lists['yandex_test_mode']; ?>
			</td>
		</tr>
		</table>
			</td>
		</tr>

		</table>
			
		</div>
		<div id="page5" style="display:none;">
			
		<table style="width:100%;" class="adminform">
		<tr>
			<h3 class="titlttab">
				<?php echo _ttfsp_oder_page; ?>
			</h3>
		</tr>
		<tr>
			<td>
			<table width="100%" class="paramlist">
		<tr>
			<td>
			<?php echo _ttfsp_specialization_select; ?>
			</td>
			<td>
		<?php echo $lists['specialization_select_on']; ?>
			</td>
		</tr>
		</table>
			</td>
		</tr>

		</table>
			
		</div>

<style>
	
	h3.titlttab {
		text-transform: uppercase;
		margin: 15px 0 15px 0;
		display: block;
		padding: 5px 0 5px 0;
		border-bottom: solid #f5f5f5 2px;
	}
	h4.subtl {
		background: #f5f5f5;
		padding: 5px 0 5px 0;
	}
</style>

		
		
</div>		
		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="config">
		<input type="hidden" id="mypageconf" name="mypageconf" value=0 />		
		</form>
</div>	
<div style="float:none;clear:both;"></div>	
		<?php
		echo _ttfsp_lang_9;
	}
		
		

////////////////////////////////////////////////////////////////////////////////// Ввод времени приема
	public static function addtimes( $row, $option, $lists ) {
			HTML_ttfsp::ttfspmenu("addtimes");
			if (JVERSION=='1.0'){
		mosCommonHTML::loadOverlib();
		mosCommonHTML::loadCalendar();
		} else {
		JHTML::_('behavior.calendar');
		}
		?>
<style>
.paramlisttime td {
	white-space:nowrap;
	font-size:12px;
}
.paramlisttime input {
	width:20px;
}
</style>	
		<div class="fspcnt">
		<form id="adminForm" action="<?php echo INDURL; ?>" method="post" name="adminForm">
		<table style="width:100%;" class="adminheading">
		<tr>
			<th class="config">
				<?php echo _ttfsp_lang_73; ?>
			</th>
		</tr>
		</table>

		<table style="width:100%;" class="adminform">
		<tr>
			<td>

			<table width="100%" class="paramlist">

		<tr>
			<td>
				<?php echo _ttfsp_lang_44; ?>
			</td>
			<td>
			<?php echo $lists['spec']; ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo _ttfsp_lang_74; ?>
			</td>
			<td>
			<?php 	if (substr(JVERSION,0,3)=='1.5' || substr(JVERSION,0,3)=='1.6' || substr(JVERSION,0,3)=='1.7' || substr(JVERSION,0,3)=='2.5' || substr(JVERSION,0,1)=='3'){
			 echo JHtml::_('calendar',$row->adddate, 'adddate','adddate','%Y-%m-%d' , array('size'=>10));		
			} else { ?>
			<input class="inputbox" type="text" size="15" name="adddate" id="adddate" value="<?php echo $row->adddate ?>">
			 <input type="reset" class="calendar" value="..." onclick="return showCalendar('adddate');" />
			<?php } ?> 
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_5; ?>
			</td>
			<td>
			<?php echo $lists['published']; ?>
			</td>
		</tr>
		<tr>
			<td >
			<?php echo _ttfsp_lang_142.'<br />'._ttfsp_lang_143; ?>
			</td>
			<td>
			<input class="inputbox" type="text" size="15" name="plimit" value="<?php echo $row->plimit ?>">
			</td>
		</tr>		
			</table>

			<br />
			<br />	
			<?php echo _ttfsp_lang_75; ?>
			<table width="100%" class="paramlisttime">
		<tr>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr1" value="<?php echo $row->addhr1 ?>">:<input class="inputbox" type="text" size="5" name="addmn1" value="<?php echo $row->addmn1 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr2" value="<?php echo $row->addhr2 ?>">:<input class="inputbox" type="text" size="5" name="addmn2" value="<?php echo $row->addmn2 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr3" value="<?php echo $row->addhr3 ?>">:<input class="inputbox" type="text" size="5" name="addmn3" value="<?php echo $row->addmn3 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr4" value="<?php echo $row->addhr4 ?>">:<input class="inputbox" type="text" size="5" name="addmn4" value="<?php echo $row->addmn4 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr5" value="<?php echo $row->addhr5 ?>">:<input class="inputbox" type="text" size="5" name="addmn5" value="<?php echo $row->addmn5 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr6" value="<?php echo $row->addhr6 ?>">:<input class="inputbox" type="text" size="5" name="addmn6" value="<?php echo $row->addmn6 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr7" value="<?php echo $row->addhr7 ?>">:<input class="inputbox" type="text" size="5" name="addmn7" value="<?php echo $row->addmn7 ?>">
			</td>
		</tr>
		<tr>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr8" value="<?php echo $row->addhr8 ?>">:<input class="inputbox" type="text" size="5" name="addmn8" value="<?php echo $row->addmn8 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr9" value="<?php echo $row->addhr9 ?>">:<input class="inputbox" type="text" size="5" name="addmn9" value="<?php echo $row->addmn9 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr10" value="<?php echo $row->addhr10 ?>">:<input class="inputbox" type="text" size="5" name="addmn10" value="<?php echo $row->addmn10 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr11" value="<?php echo $row->addhr11 ?>">:<input class="inputbox" type="text" size="5" name="addmn11" value="<?php echo $row->addmn11 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr12" value="<?php echo $row->addhr12 ?>">:<input class="inputbox" type="text" size="5" name="addmn12" value="<?php echo $row->addmn12 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr13" value="<?php echo $row->addhr13 ?>">:<input class="inputbox" type="text" size="5" name="addmn13" value="<?php echo $row->addmn13 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr14" value="<?php echo $row->addhr14 ?>">:<input class="inputbox" type="text" size="5" name="addmn14" value="<?php echo $row->addmn14 ?>">
			</td>
		</tr>
		<tr>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr15" value="<?php echo $row->addhr15 ?>">:<input class="inputbox" type="text" size="5" name="addmn15" value="<?php echo $row->addmn15 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr16" value="<?php echo $row->addhr16 ?>">:<input class="inputbox" type="text" size="5" name="addmn16" value="<?php echo $row->addmn16 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr17" value="<?php echo $row->addhr17 ?>">:<input class="inputbox" type="text" size="5" name="addmn17" value="<?php echo $row->addmn17 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr18" value="<?php echo $row->addhr18 ?>">:<input class="inputbox" type="text" size="5" name="addmn18" value="<?php echo $row->addmn18 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr19" value="<?php echo $row->addhr19 ?>">:<input class="inputbox" type="text" size="5" name="addmn19" value="<?php echo $row->addmn19 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr20" value="<?php echo $row->addhr20 ?>">:<input class="inputbox" type="text" size="5" name="addmn20" value="<?php echo $row->addmn20 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr21" value="<?php echo $row->addhr21 ?>">:<input class="inputbox" type="text" size="5" name="addmn21" value="<?php echo $row->addmn21 ?>">
			</td>
		</tr>
		<tr>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr22" value="<?php echo $row->addhr22 ?>">:<input class="inputbox" type="text" size="5" name="addmn22" value="<?php echo $row->addmn22 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr23" value="<?php echo $row->addhr23 ?>">:<input class="inputbox" type="text" size="5" name="addmn23" value="<?php echo $row->addmn23 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr24" value="<?php echo $row->addhr24 ?>">:<input class="inputbox" type="text" size="5" name="addmn24" value="<?php echo $row->addmn24 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr25" value="<?php echo $row->addhr25 ?>">:<input class="inputbox" type="text" size="5" name="addmn25" value="<?php echo $row->addmn25 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr26" value="<?php echo $row->addhr26 ?>">:<input class="inputbox" type="text" size="5" name="addmn26" value="<?php echo $row->addmn26 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr27" value="<?php echo $row->addhr27 ?>">:<input class="inputbox" type="text" size="5" name="addmn27" value="<?php echo $row->addmn27 ?>">
			</td>
			<td>
			<input class="inputbox" type="text" size="5" name="addhr28" value="<?php echo $row->addhr28 ?>">:<input class="inputbox" type="text" size="5" name="addmn28" value="<?php echo $row->addmn28 ?>">
			</td>
		</tr>
			</table>
			</td>
		</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $row->id ?>" />
		<input type="hidden" name="option" value="<?php echo $option ?>" />
		<input type="hidden" name="task" value="editA" />
		<input type="hidden" name="act" value="addtimes">
		</form>
</div>	
<div style="float:none;clear:both;"></div>			
		<?php
	}
}
?>