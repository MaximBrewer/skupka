<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class addtime_ttfsp 
	{
	public static function addtimettfsp(&$rows, $date, &$ttime, $curdate, $idusr, $myid, $params, $time, $specid, $sid){
		$mycss ='';
		if ($params['bgcolor'])
			$mycss .= '.yesrecept, .yesrecept a {background-color:'.$params['bgcolor'].'!important;}';
		if ($params['fontcolor'])
			$mycss .= '.yesrecept, .yesrecept a {color:'.$params['fontcolor'].'!important;}';
		if ($params['bgcolor1'])
			$mycss .= '.recept, .recept a {background-color:'.$params['bgcolor1'].'!important;}';
		if ($params['fontcolor1'])
			$mycss .= '.recept, .recept a {color:'.$params['fontcolor1'].'!important;}';
		if ($params['bgcolor2'])
			$mycss .= '.offrecept {background-color:'.$params['bgcolor2'].'!important;}';
		if ($params['fontcolor2'])
			$mycss .= '.offrecept {color:'.$params['fontcolor2'].'!important;}';
		if ($params['bgcolor3'])
			$mycss .= '.nav_week, .nav_week a {background-color:'.$params['bgcolor3'].'!important;}';
		if ($params['fontcolor3'])
			$mycss .= '.nav_week, .nav_week a {color:'.$params['fontcolor3'].'!important;}';
//		if ($params['user_css'])
//			$mycss .= $params['user_css'];
	if ($mycss){
$mycss ='<style type="text/css">
'.$mycss.'
</style>
';
		echo $mycss;
	}
	$panel = 0;
	$viewspec = $params['viewspec'];
	$editspec = $params['editspec'];	
	$moderators = '  ,'.$params['moderators'].',';	
	$md = (int)$myid;
	$mdr = strpos($moderators,','.$md.',');
	if ($md){
		if (($md==$idusr && $params['editspec']) || $mdr)
			$panel = 1;		
	}	
	$wr = $sid ? '&sid='.$sid : '';
	$date0 = date('Y-m-d',$curdate);
	$date1 = date('Y-m-d',$curdate+86400);
	$date2 = date('Y-m-d',$curdate+86400*2);
	$date3 = date('Y-m-d',$curdate+86400*3);
	$date4 = date('Y-m-d',$curdate+86400*4);
	$date5 = date('Y-m-d',$curdate+86400*5);
	$date6 = date('Y-m-d',$curdate+86400*6);
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$pan = '';
			$link = 'index.php?option=com_ttfsp&task=addclient&cdate='.$curdate.'&id='.$row->id.$wr; //  Создание ячеек со временем
			if (JVERSION=="1.0"){
				$link=sefRelToAbs($link);
			} else {
				$link=JRoute::_($link);
			}
			if ($panel){
				$pan = '<div class="panel">';
				if ($row->reception && $params['onmsg'] && $params['createmsg'] && $params['yesrecept'] && $params['norecept'] && $row->rmail){	
					$pan .= '<span class="panel_yes" title="'._ttfsp_lang_189.'" onclick="editrec(2, 1, '.$row->id.',\''.$row->rmail.'\')"></span>';
					$pan .= '<span class="panel_no" title="'._ttfsp_lang_190.'" onclick="editrec(2, 0, '.$row->id.',\''.$row->rmail.'\')"></span>';
				}	
				$pan .= $row->reception ?  '<span class="panel_close" title="'._ttfsp_lang_42.'" onclick="editrec(0, 0, '.$row->id.',\'\')"></span>' :  '<span class="panel_open" title="'._ttfsp_lang_43.'" onclick="editrec(0, 1, '.$row->id.',\'\')"></span>';
				$pan .= $row->published ?  '<span class="panel_publ" title="'._ttfsp_lang_17.'" onclick="editrec(1, 0, '.$row->id.', \'\')"></span>' :  '<span class="panel_nopubl" title="'._ttfsp_lang_18.'" onclick="editrec(1, 1, '.$row->id.',\'\')"></span>';
				$pan .= '</div>';
				
			}
		$timeprm = strtotime($row->dttime)+((int)$row->hrtime*3600)+((int)$row->mntime*60);		
			if ($row->reception || $timeprm<=$time){
				$rstyle = $row->reception ? ' yesrecept' : '';
					$dop_text = '';
					$norec = '';
					if ($params['dop_text'] && $timeprm>$time){
						$dop_text = '<div class="dop_text">'.$params['dop_text'].'</div>';
						if ($row->iduser==$myid && $params['modiuser'] && $row->iduser>0)
							$norec = '<span style="cursor:pointer;"><img src="components/com_ttfsp/images/del.png" title="'._ttfsp_lang_202.'" onclick="del_recept('.$row->id.');" /></span>';
					}	
				if (($viewspec && $myid == $idusr && $row->reception && $myid && $idusr) || ($mdr && $row->reception) || ($row->iduser==$myid && $params['viewuser'] && $row->iduser>0)){
					if ($row->rfio)
					$ltime = '<div id="del_recept'.$row->id.'" class="norecept'.$rstyle.'">'.$norec.'<a class="urltooltip color_tooltip" href="javascript:void(0);">'.$row->hrtime.':'.$row->mntime.'<span>'._ttfsp_lang_25.': '.$row->rfio.'<br />'._ttfsp_lang_64.': '.$row->rphone.'<br />'.$row->info.'</span></a>'.$dop_text.$pan.'</div>';
					else
					$ltime = '<div id="del_recept'.$row->id.'" class="norecept'.$rstyle.'">'.$norec.'<a class="urltooltip color_tooltip" href="javascript:void(0);">'.$row->hrtime.':'.$row->mntime.'<span>'.$row->info.'<br />IP:'.$row->ipuser.'</span></a>'.$dop_text.$pan.'</div>';
					} else {
					$ltime = '<div class="norecept'.$rstyle.'">'.$row->hrtime.':'.$row->mntime.$dop_text.$pan.'</div>';
				}	
			} else {
				$tit ='';
				$dop_text = '';
				if ($row->plimit>0){
					if ($params['dop_text2'])
						$dop_text = '<div class="dop_text">'.$params['dop_text2'].'<p>'.$row->peoples._ttfsp_lang_149.$row->plimit.'</p></div>';
					else
						$tit = _ttfsp_lang_148.$row->peoples._ttfsp_lang_149.$row->plimit;
				} else {	
					if ($params['dop_text1'])
					$dop_text = '<div class="dop_text">'.$params['dop_text1'].'</div>';
				}
				$pricezap = '';
				
				if ($params['billing_on_title_2'] ==  1 && $row->pricezap > 0) {
					$pricezap = '<div class="pricezap">
					<p class="nameprice">'._ttfsp_lang_summ_spec.'</p>
					<span class="pricenum">'.$row->pricezap.'</span>
					<span class ="valt">'.$params['valuta_name'].'</span>
					</div>
					';
				}
				if ($params['billing_on_title_2'] ==  1 && $row->pricezap <= 0) {
					$pricezap = '<div class="pricezap">
					<span class="pricenum">'._ttfsp_lang_free.'</span>
					</div>
					';;
				}
				
				$ltime = '<div class="recept" title="'.$tit.'"><a href="'.$link.'">'.$row->hrtime.':'.$row->mntime.$pricezap.'</a>'.$dop_text.$pan.'</div>'; 
			}
			if ($row->dttime == $date0){
				$ttime[0] .= $ltime; 
			}
			if ($row->dttime == $date1){
				$ttime[1] .= $ltime; 
			}
			if ($row->dttime == $date2){
				$ttime[2] .= $ltime; 
			}
			if ($row->dttime == $date3){
				$ttime[3] .= $ltime; 
			}
			if ($row->dttime == $date4){
				$ttime[4] .= $ltime; 
			}
			if ($row->dttime == $date5){
				$ttime[5] .= $ltime; 
			}
			if ($row->dttime == $date6){
				$ttime[6] .= $ltime; 
			}
		}
		if ($panel){
			$link = 'index.php?option=com_ttfsp&task=edit&cdate='.$curdate;		
			?>
				<form action="<?php echo $link; ?>" method="post" name="ttfspedForm" id="ttfspedForm">
					<input type="hidden" name="publ" value="1">
					<input type="hidden" name="vl" value="0">	
					<input type="hidden" name="id" value="0">
					<input type="hidden" name="specid" value="<?php echo $specid; ?>">	
					<input type="hidden" name="idusr" value="<?php echo $idusr; ?>">	
					<input type="hidden" name="rmail" value="">						
				</form>
			<?php
			
		}
	}
}

?>