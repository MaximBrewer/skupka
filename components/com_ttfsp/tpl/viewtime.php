<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
class viewtime_ttfsp 
	{
	public static function viewtimettfsp(&$rows, &$rowstime, $tdate, $curdate, $ttime, $params, $sprspecdesc, $sid){
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
	$url_site = $params['url_site'];
	$pdate  = $tdate-604800;
	if (!count($rows)) return;
	$sdate  = $tdate+604800;

	$cdate = $curdate;
	$mtdate = $tdate;
/*
// ¬˚‚Ó‰ ‰ÂÌ¸ ÏÂÒˇˆ „Ó‰
	$tdate1= '<br />'.date('d',$mtdate).'<br />'.date('m',$mtdate).'<br />'.date('Y',$mtdate);
	$mtdate = $tdate+86400;
	$tdate2= '<br />'.date('d',$mtdate).'<br />'.date('m',$mtdate).'<br />'.date('Y',$mtdate);
	$mtdate = $tdate+86400*2;
	$tdate3= '<br />'.date('d',$mtdate).'<br />'.date('m',$mtdate).'<br />'.date('Y',$mtdate);
	$mtdate = $tdate+86400*3;
	$tdate4= '<br />'.date('d',$mtdate).'<br />'.date('m',$mtdate).'<br />'.date('Y',$mtdate);
	$mtdate = $tdate+86400*4;
	$tdate5= '<br />'.date('d',$mtdate).'<br />'.date('m',$mtdate).'<br />'.date('Y',$mtdate);
	$mtdate = $tdate+86400*5;
	$tdate6= '<br />'.date('d',$mtdate).'<br />'.date('m',$mtdate).'<br />'.date('Y',$mtdate);
	$mtdate = $tdate+86400*6;
	$tdate7= '<br />'.date('d',$mtdate).'<br />'.date('m',$mtdate).'<br />'.date('Y',$mtdate);
*/
// ¬˚‚Ó‰ ‰ÂÌ¸ ÏÂÒˇˆ
	$tdate1= '<div class="day day1">'.date('d',$mtdate).'</div><div class="month">'.monthrus(date('m',$mtdate)).'</div>';
	$mtdate = $tdate+86400;
	$tdate2= '<div class="day day2">'.date('d',$mtdate).'</div><div class="month">'.monthrus(date('m',$mtdate)).'</div>';
	$mtdate = $tdate+86400*2;
	$tdate3= '<div class="day day3">'.date('d',$mtdate).'</div><div class="month">'.monthrus(date('m',$mtdate)).'</div>';
	$mtdate = $tdate+86400*3;
	$tdate4= '<div class="day day4">'.date('d',$mtdate).'</div><div class="month">'.monthrus(date('m',$mtdate)).'</div>';
	$mtdate = $tdate+86400*4;
	$tdate5= '<div class="day day5">'.date('d',$mtdate).'</div><div class="month">'.monthrus(date('m',$mtdate)).'</div>';
	$mtdate = $tdate+86400*5;
	$tdate6= '<div class="day day6">'.date('d',$mtdate).'</div><div class="month">'.monthrus(date('m',$mtdate)).'</div>';
	$mtdate = $tdate+86400*6;
	$tdate7= '<div class="day day7">'.date('d',$mtdate).'</div><div class="month">'.monthrus(date('m',$mtdate)).'</div>';
		$row = $rows[0];
		$cdate = 'index.php?option=com_ttfsp&idspec='.$row->id.'&cdate='.$cdate;
		$pdate = 'index.php?option=com_ttfsp&idspec='.$row->id.'&cdate='.$pdate;
		$sdate = 'index.php?option=com_ttfsp&idspec='.$row->id.'&cdate='.$sdate;
		$slink = 'index.php?option=com_ttfsp&view=detail&idspec='.$row->id;		
		if (JVERSION=="1.0"){
			$cdate=sefRelToAbs($cdate);
			$pdate=sefRelToAbs($pdate);
			$sdate=sefRelToAbs($sdate);
			$slink=sefRelToAbs($slink);				
		} else {
			$cdate=JRoute::_($cdate);
			$pdate=JRoute::_($pdate);
			$sdate=JRoute::_($sdate);
			$slink=JRoute::_($slink);			
		}
		$row->desc = str_replace(chr(13),'<br />',$row->desc);
		$sprspecdesc = str_replace(chr(13),'<br />',$sprspecdesc);
//		$img = $row->photo && !$row->offphoto ? '<img class="photovt" src="'.$url_site.$row->photo.'"/>' : ''; 

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
		
		$id_specialist = JRequest::getCmd(  'idspec', 0 );
		
		$database = JFactory::getDBO();
		
		$query = "SELECT idsprsect FROM #__ttfsp_spec WHERE id='".$id_specialist."' ORDER BY id DESC LIMIT 1";
		
		$database->setQuery($query);
		
		/// Ссылка вернуться к просмотру специалистов
		
		$office =  $database->loadResult();
		
		if (!$office) {
			$office = 0;
		}
		
		$returnlink = 'index.php?option=com_ttfsp';
		$returnlink = JRoute::_($returnlink);
		
		if ($sid) {
			
			if ($office > 0) {
				$returnlink = 'index.php?option=com_ttfsp&view=officespec&office='.$office.'&sid='.$sid;
				$returnlink = JRoute::_($returnlink);
			}
			else {
				$returnlink = 'index.php?option=com_ttfsp&view=Specializations&sid='.$sid;
				$returnlink = JRoute::_($returnlink);
			}
		}
		if (!$sid) {
			
		
			$returnlink = 'index.php?option=com_ttfsp&view=office&office='.$office;
			$returnlink = JRoute::_($returnlink);
	
	
		}
		?>
		<div class="ttfsptime">
			<div class="buttret">
				 <a class="btnrtn" href="<?php echo $returnlink ?>"><?php echo _ttfsp_button_spec_return ?></a>
			</div>
		<table class="tblion" width="100%">
		<tr>
		 <td>
			<?php echo $img; ?>
		 </td>
		 <td>
		 <div class="fiospec">
			<?php echo $row->name; ?></div>
			<div class="descspec">
			<?php echo $row->desc; ?></div>
			
			
			<?php 
				if ($params['jcomment']){
					echo '<div class="jcomm_btn"><a href="'.$slink.'">';
					echo $params['tjcomment'];
					echo '</a></div>';
				}
			?>			
			
			
		 </td>
		 <td>

			<div class="spec">
			<?php echo $sprspecdesc; ?></div>
		 </td>
		</tr>
		</table>	
		<table class="tbltw" width="100%">
		<tr>
		<td align="left" class="tdvw" valign="top">
			<span class="nav_week">
			<?php echo '<a href="'.$pdate.'">'._ttfsp_lang_48.'</a>'; ?> 
			</span>
		</td>
		<td align="center" class="tdvw" valign="top">
			<span class="nav_week">		
			<?php echo '<a href="'.$cdate.'">'._ttfsp_lang_49.'</a>'; ?> 
			</span>
		</td>
		<td align="right" class="tdvw" valign="top">
				<span class="nav_week">
			<?php echo '<a href="'.$sdate.'">'._ttfsp_lang_50.'</a>'; ?>
				</span>
		</td>
		</tr>
		</table>

		<table class="tbltree" width="100%">
		<tr>
		<td align="center" class="tdvd" valign="top">
			<?php echo '<div class="dayname">'._ttfsp_lang_51.'</div>'.$tdate1; ?> 
		</td>
		<td align="center" class="tdvd" valign="top">
			<?php echo '<div class="dayname">'._ttfsp_lang_52.'</div>'.$tdate2; ?> 
		</td>
		<td align="center" class="tdvd" valign="top">
			<?php echo '<div class="dayname">'._ttfsp_lang_53.'</div>'.$tdate3; ?> 
		</td>
		<td align="center" class="tdvd" valign="top">
			<?php echo '<div class="dayname">'._ttfsp_lang_54.'</div>'.$tdate4; ?> 
		</td>
		<td align="center" class="tdvd" valign="top">
			<?php echo '<div class="dayname">'._ttfsp_lang_55.'</div>'.$tdate5; ?> 
		</td>
		<td align="center" class="tdvd" valign="top">
			<?php echo '<div class="dayname">'._ttfsp_lang_56.'</div>'.$tdate6; ?> 
		</td>
		<td align="center" class="tdvd" valign="top">
			<?php echo '<div class="dayname">'._ttfsp_lang_57.'</div>'.$tdate7; ?> 
		</td>
		</tr>
		<tr>
		<td align="center" class="tdvt" valign="top">
			<?php echo $ttime[0]; ?> 
		</td>
		<td align="center" class="tdvt" valign="top">
			<?php echo $ttime[1]; ?> 
		</td>
		<td align="center" class="tdvt" valign="top">
			<?php echo $ttime[2]; ?> 
		</td>
		<td align="center" class="tdvt" valign="top">
			<?php echo $ttime[3]; ?> 
		</td>
		<td align="center" class="tdvt" valign="top">
			<?php echo $ttime[4]; ?> 
		</td>
		<td align="center" class="tdvt" valign="top">
			<?php echo $ttime[5]; ?> 
		</td>
		<td align="center" class="tdvt" valign="top">
			<?php echo $ttime[6]; ?> 
		</td>
		</tr>
		</table>
		</div>
	<?php
	}
}

?>