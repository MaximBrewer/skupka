<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class addtm_ttfsp 
	{
	public function showAddtm( $option , $userid=0){
	$database = JFactory::getDBO();
	$database->setQuery( "SELECT COUNT(*) FROM #__ttfsp_sprtime WHERE published=1" );
	$cnts = $database->loadResult();
	if (!$cnts){
		if (!$userid)
			HTML_ttfsp::ttfspmenu("addtm");
			echo _ttfsp_lang_174.'
		<div style="color:#999;padding-top:30px;font-size:11px;">
		'. _ttfsp_lang_178.'
		</div>				
			';
			return;
	}
	$usrflt='';
	if ($userid)
		$usrflt='AND spec.idusr='.$userid;
	$database->setQuery( "SELECT id, name FROM #__ttfsp_spec AS spec WHERE spec.published=1 AND spec.idsprtime>0 ".$usrflt );
	$rowspec = $database->loadObjectList();
	if (!count($rowspec)){
			if (!$userid)
			HTML_ttfsp::ttfspmenu("addtm");
			echo _ttfsp_lang_175.'
		<div style="color:#999;padding-top:30px;font-size:11px;">
		'. _ttfsp_lang_178.'
		</div>				
			';
			return;
	}
	$lists = array();
	$row = $rowspec[0];
	$spec = array();
	$spec = array_merge( $spec, $rowspec);
	$lists['published'] 	= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', 1 );
	$lists['spec']	 = JHTML::_('select.genericlist', $spec, 'addspec[]', 'size="20" class="inputbox" multiple="multiple"', 'id', 'name',0);
	require_once dirname(__FILE__) . "/showtm.php";	
}	

public function savetm( $option, $userid=0 ){
	$plimit =  (int)(JRequest::getVar(  'plimit', 0 ));
	$plimit = $plimit==1 ? 0 : $plimit;
	$published =  (int)(JRequest::getVar(  'published', 0 ));
	$addspec = JRequest::getVar( 'addspec', array(0), 'post' );
	$adddate = JRequest::getVar( 'chkdate', array(0), 'post' );	
	if (!$userid && (!is_array( $addspec ) || count( $addspec ) < 1 || !is_array( $adddate ) || count( $adddate ) < 1)) {
			if (!$userid)
			HTML_ttfsp::ttfspmenu("addtm");
			echo _ttfsp_lang_174.'
		<div style="color:#999;padding-top:30px;font-size:11px;">
		'. _ttfsp_lang_178.'
		</div>				
			';
			return;
	}
	if (!$userid){
	if (count( $addspec ) == 1 && $addspec[0] == 0){
		echo "<script> alert('"._ttfsp_lang_179."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (count( $addspec ) == 1 && $adddate[0] == 0){
		echo "<script> alert('"._ttfsp_lang_180."'); window.history.go(-1); </script>\n";
		exit();
	}
	deltime();
	}	
	$database = JFactory::getDBO();
	if (!$userid)
		$cspec = 'spec.id=' . implode( ' OR spec.id=', $addspec );
	else 
		$cspec = 'spec.idusr='.$userid;
	$database->setQuery( "SELECT spec.id, spec.pricespec, tm.timehm FROM #__ttfsp_spec AS spec  LEFT JOIN #__ttfsp_sprtime AS tm ON tm.id = spec.idsprtime WHERE ".$cspec );
	$rows = $database->loadObjectList();

foreach ($adddate as $cdt){	
		if ($cdt){
		$ddd = date('Y-m-d',$cdt);	
		for ($i=0; $i<count($rows); $i++){
		$row = $rows[$i];
		if ($row->timehm){
			$atimehm = explode(chr(13) ,$row->timehm);
			for ($n=0; $n<count($atimehm); $n++){
				$hm = $atimehm[$n];
				$h = substr($hm,0,2);
				$m = substr($hm,3,2);
				$pricezap = $row->pricespec;
				if ($pricezap < 0 || !$pricezap) {
					$pricezap = 0;
				}
						$this->addtime($h, $m, $ddd, $row->id, $published, $plimit, $pricezap);
					}
				$n=0;	
				}
			}
		$i=0;	
		}
	}
unset($cdt); 
}

protected function addtime($rowhr, $rowmn, $rowdate, $rowspec, $rowpubl,  $plimit, $pricezap){
	if ((!$rowhr && !$rowmn) || !$rowdate || !$rowspec) return;
	$rowhr = (int)$rowhr > 23 ? '0' : $rowhr;
	$rowhr = (int)$rowhr<10 ? '0'.((int)$rowhr) : (int)$rowhr;
	$rowmn = (int)$rowmn > 59 ? '0' : $rowmn;
	$rowmn = (int)$rowmn<10 ? '0'.((int)$rowmn) : (int)$rowmn;
	$ttime = strtotime($rowdate);
		$where[] = "dttime = '$rowdate'";
		$where[] = "idspec=".$rowspec;
		$where[] = "hrtime = '$rowhr'";
		$where[] = "mntime = '$rowmn'";
		$where = "\n WHERE ". implode( ' AND ', $where );
	$database = & JFactory::getDBO();
	$database->setQuery( "SELECT COUNT(*) FROM #__ttfsp ".$where);
	$total = $database->loadResult();
	if ($total) return;
	$database->setQuery("INSERT INTO `#__ttfsp`  (`idspec` ,`published` ,`dttime` ,`hrtime`,`mntime`, `plimit`, `ttime` ,`pricezap`) VALUES ('".$rowspec."','".$rowpubl."','".$rowdate."','".$rowhr."','".$rowmn."','".$plimit."','".$ttime."','".$pricezap."');");
	$database->query();
}




	}
?>	