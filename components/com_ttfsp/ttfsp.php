<?php
/**
*Time-Table FS+ - Joomla Component
* @package TT FS+
* @Copyright (C) 2010 FomSoft Plus
* @ All rights reserved
* @ Time-Table FS+ is Commercial Software
**/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
// Error_Reporting(E_ALL & ~E_NOTICE);
global $mosConfig_lang, $mosConfig_absolute_path;
if (!defined('JVERSION')) {
		define( 'JVERSION', '1.0' ); 
}
if (!defined( 'DS' )) {
	define( 'DS', DIRECTORY_SEPARATOR );
}


if (!defined('JPATH_LIVE_SITE')) {
	if (JVERSION=='1.0'){
	global $mosConfig_live_site;
	define( 'JPATH_LIVE_SITE', $mosConfig_live_site.'/'); 
	} else {
		define( 'JPATH_LIVE_SITE', JURI::base()); 
	}
}

if (!defined('JPATH_ROOT_SITE')) {
	if (JVERSION=='1.0'){
	global $mosConfig_absolute_path;
	define( 'JPATH_ROOT_SITE', $mosConfig_absolute_path); 
	define( 'JPATH_ROOT', $mosConfig_absolute_path); 
	} else {
		define( 'JPATH_ROOT_SITE', JPATH_ROOT); 
	}
}
require_once(JPATH_ROOT.DS."components".DS."com_ttfsp".DS."ttfsp.html.php");
// require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_virtuemart".DS."yandex_money_notify.php"); // Временное подключение Яндекс Кассы

///
if (!isset($option)){
	$option = 'com_ttfsp';
}
if (!isset($task)){
	$task	= JRequest::getCmd( 'task', '' );
}
if (JVERSION== '1.0'){
	require_once(JPATH_ROOT.DS."components".DS."com_ttfsp".DS."legacy.php" );
	global $mosConfig_lang;
	if ( file_exists( JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS."$mosConfig_lang.php") ) {
		include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS."$mosConfig_lang.php");
		$langfsp=$mosConfig_lang.'-';
	} else {
		include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS."russian.php");
		$langfsp='russian-';
	}
} else {
	if ( substr(JVERSION,0,1)=='3'){
		$lng = JFactory::getLanguage()->getTag();
	} else {
		$conf =JFactory::getConfig();
		$lng = $conf->getValue('config.language');
	}
	$lngfile=$lng.'.php';
	if ( file_exists( JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS.$lngfile) ) {
		$filelang=JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS.$lngfile;
		include_once($filelang);
		$langfsp=$lng.'-';
	} else {
		include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS."ru-RU.php");
		$langfsp='ru-RU-';
	}
}
$idspec =  JRequest::getCmd(  'idspec', 0 );
$view =  JRequest::getCmd(  'view', 0 );
if ($view == 'offices' && !$idspec && !$task)
	$task = 'viewsect';
	
	
if ($view == 'successpage') {
	
	$task = 'successpage';
	
	}
	
	
if ($view == 'checkyandexkassa') {
	
	$task = 'checkyandexkassa';
	
	}
	
	
	
if ($view == 'avisoyandexkassa') {
	
	$task = 'avisoyandexkassa';
	
	}
	
	
if ($view == 'cancelyandexkassa') {
	
	$task = 'cancelyandexkassa';
	
	}
	
if ($view == 'successyandexkassa') {
	
	$task = 'successyandexkassa';
	
	}
	
if ($view == 'paymentpageopen') {
	
	$task = 'paymentpageopen';
	
	}
	
if ($view=='user'){
			$task = 'usercabinet';	
	}
	
switch ( $task ) {
		
		case 'usercabinet':
		usercabinet ();
		break;
		case 'paymentpageopen':
		paymentpageopen ();
		break;
		case 'successyandexkassa':
		successyandexkassa ();
		break;
		case 'cancelyandexkassa':
		cancelyandexkassa ();
		break;
		case 'avisoyandexkassa':
		avisoyandexkassa ();
		break;
		case 'checkyandexkassa':
		checkyandexkassa ();
		break;
		case 'successpage':
		successpage();
		break;
		case 'resultx':
		resultx();
		break;
		case 'freepayment':
		freepayment();
		break;
		case 'successx':
		successx();
		break;
		case 'failx':
		failx();
		break;
		case 'viewsect': 
		viewsect();
		break;
		case 'sms':
		$key	 = JRequest::getCmd(  'key', 0 ) ;
		mess_client((int)$key);
		break;
		case 'edit': 
		edit();
		break;
		case 'addclient': 
			$sid =  JRequest::getCmd(  'sid', 0 );			
		addclient($sid);
		break;
		case 'addtm': 
			addtm();
		break;	
		case 'adddt': 
			adddt($option);
		break;
		case 'savedt': 
			savedt($option);
		break;		
		case 'moditm': 
			savetm();
		break;			
	default:
	
	
			if ($idspec){
				if ($view=='detail'){
					viewspec_all($idspec, 1);
				} else {
					$sid =  JRequest::getCmd(  'sid', 0 );		
					viewtime($idspec, $sid, 0);
				}
			} else {
				if ( $view=='Specializations' ||  $view=='officespec') {
					$sid =  JRequest::getCmd(  'sid', 0 );
					viewspec_all($sid);
				} else {
					viewspec_all();
				}
			}
				
	break;
}
//////////////////////////////////////////////////////////////////// отправка смс-напоминаний клиентам
function mess_client($key){
	if ($key>0){
		$params = getparams();
		if ((int)$params['cronkey']==$key && (int)$params['qtsms_on'] && (int)$params['sms_hour'] && $params['sms_message']!=''){
			if ($params['qtsms_login'] && $params['qtsms_password'] && $params['qtsms_host'] ){
				include_once(JPATH_ROOT.DS."components".DS."com_ttfsp".DS."includes".DS."QTSMS.class.php");
				$sms= new QTSMS($params['qtsms_login'],$params['qtsms_password'],$params['qtsms_host']);
				$sender_name=$params['qtsms_sender'];
				$period=600;
				$cortime = (int) $params['cortime'];
				$time = time()+$cortime;
				$startdate = date('Y-m-d',$time);
				$time += (int)$params['sms_hour']*3600;
				$enddate = date('Y-m-d',$time);			
				$database = JFactory::getDBO();		
				$query = "SELECT * "
				. "\n FROM #__ttfsp_dop "
				. "\n WHERE rphone !='' AND sms_send=0 AND date>='$startdate' AND date<='$enddate'"
				;
				$database->setQuery( $query);
				$rows = $database->loadObjectList();
				$test_phone= explode("," ,$params['test_phone']);
				$ids=array();	
				for ($i=0; $i<count($rows);$i++){
					$h = (int)$rows[$i]->hours;
					$m = (int)$rows[$i]->minutes;
					$d = explode('-',$rows[$i]->date);					
					$rectime  = @mktime($h, $m, 0, $d[1]  , $d[2], $d[0]);	
					if ($rectime<=$time){
						$ids[] = $rows[$i]->id;
						$sms_phone = preg_replace('/[^\d]/','',$rows[$i]->rphone);
						$tphone = substr($sms_phone,0,3);
						
						$mobile='';
						if (!in_array($tphone,$test_phone)){
							$tphone = substr($sms_phone,1,3);
							if (in_array($tphone,$test_phone))	
								$mobile= substr($sms_phone,1);
								
						} else {	
							$mobile= $sms_phone;
							
						}	
						if ($mobile){
							$sms_text=$params['sms_message'].' '.$rows[$i]->date.' '.$rows[$i]->hours.':'.$rows[$i]->minutes;
							$result=$sms->post_message($sms_text, '+7'.$mobile, $sender_name,'x124127456',$period);
							echo $result;
						}
					}
				}
				if (count($ids)){
					$cids = 'id=' . implode( ' OR id=', $ids );
					$dbt = '#__ttfsp_dop';
					$query = "UPDATE $dbt"
					. "\n SET sms_send = 1" 
					. "\n WHERE ( $cids )"
					;
					$database->setQuery( $query );
					$database->query();
				}
			}	
		}
	}
}
///////////////////////////////////////////////////////////////////// сохранение времени приема
function savetm(){
	$my	= JFactory::getUser();
	if ((int)$my->id && test_fld($my->id,'addtm')){	
		$errmsg = '';	
		$rowtimehm = JRequest::getString(  'timehm', '' );
		$id =  JRequest::getCmd(  'id', 0 );		
		$timehm = str_replace("\r","",$rowtimehm);
		$atimehm = explode("\n" ,$timehm);
		if (count($atimehm)){
			$new_timehm = array();
			for ($i=0; $i<count($atimehm); $i++){
				$hm = $atimehm[$i];
				$hm = str_replace(" ","",$hm);
				if ($hm){
					$pos = strpos($hm, ':');
					if ($pos){
						$h = (int)(substr($hm,0,$pos));
						$m = (int)(substr($hm,$pos+1,2));
						if ($h<0 || $h>23 || $m<0 || $m>59){
							$errmsg .= '<br />'._ttfsp_lang_171.$hm;
						} else {
							$ch = $h<10 ? '0'.$h : $h;
							$cm = $m<10 ? '0'.$m : $m;
							$chm = $ch.':'.$cm;
							if (in_array($chm, $new_timehm))
								$errmsg .= '<br />'._ttfsp_lang_172.$hm;							
							else
								$new_timehm[] = $chm;
						}
					} else {
						$errmsg .= '<br />'._ttfsp_lang_170.$hm;
					}
				}
			}
		asort($new_timehm);
		$rowtimehm = implode(chr(13), $new_timehm);
		}
		if (!$errmsg){
			$errmsg = _ttfsp_lang_164;
			$database = JFactory::getDBO();
			$database->setQuery("UPDATE `#__ttfsp_sprtime` SET `timehm` = '".$rowtimehm."' WHERE id=".$id);
			$database->query();	
		}		
			$link = 'index.php?option=com_ttfsp&task=addtm';
			if (JVERSION=="1.0"){
				$link=sefRelToAbs($link);
			} else {
				$link=JRoute::_($link);
			}
			myRedirect($link, $errmsg);
	}
}
///////////////////////////////////////////////////////////////////////изменение времени приема
function addtm(){
	$my	= JFactory::getUser();
	$allspec = '';
	if ((int)$my->id && test_fld($my->id,'addtm')){
		$database = JFactory::getDBO();
		$query = "SELECT tm.timehm, tm.id"
		. "\n FROM #__ttfsp_spec AS spec"
		. "\n LEFT JOIN #__ttfsp_sprtime AS tm ON tm.id = spec.idsprtime" 	
		. "\n  WHERE spec.idusr=".$my->id
		;
		$database->setQuery( $query);
		$rows = $database->loadObjectList();
		$tm = $rows[0]->timehm;
		$id = $rows[0]->id;	

		$query = "SELECT spec.name"
		. "\n FROM #__ttfsp_spec AS spec"
		. "\n  WHERE spec.idsprtime=".$id
		;
		$database->setQuery( $query);
		$arows = $database->loadResultArray();
		if ( count($arows)>1)		
			$allspec = _ttfsp_lang_212. implode(', ',$arows);
		$link = 'index.php?option=com_ttfsp';
		if (JVERSION=="1.0"){
			$link=sefRelToAbs($link);
		} else {
			$link=JRoute::_($link);
		}		
		HTML_ttfsp::addtm($id, $tm, $link, $allspec);
	}
}
///////////////////////////////////////////////////////////////////////ввод времени приема
function adddt($option){
	$my	= JFactory::getUser();
	if ((int)$my->id && test_fld($my->id,'adddt')){
		include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."addtm.php");
		$tm = new addtm_ttfsp;
		$tm->showAddtm( $option, (int)$my->id );	
	}
}
////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////ввод времени приема
function savedt($option){
	$my	= JFactory::getUser();
	if ((int)$my->id && test_fld($my->id,'adddt')){
		include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."addtm.php");
		$tm = new addtm_ttfsp;
		$tm->savetm( $option,  (int)$my->id);
		myRedirect('index.php?option=com_ttfsp&task=adddt', _ttfsp_lang_72);
	}
}
////////////////////////////////////////////////////////////
function test_fld($idusr, $fld){
	$database = JFactory::getDBO();
	$query = "SELECT $fld"
	. "\n FROM #__ttfsp_spec"
	. "\n  WHERE idusr=".$idusr
	;
	$database->setQuery( $query);
	return $database->loadResult();
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function edit(){
	$my	= JFactory::getUser();
	$id =  JRequest::getCmd(  'id', 0 );
	$publ =  JRequest::getCmd(  'publ', 0 );
	$vl =  JRequest::getCmd(  'vl', 0 );
	$cdate =  JRequest::getCmd(  'cdate', 0 );
	$publ =  JRequest::getCmd(  'publ', 0 );
	$specid =  JRequest::getCmd(  'specid', 0 );
	$idusr =  JRequest::getCmd(  'idusr', 0 );
	$rmail =  JRequest::getString(  'rmail', '' );	
	$msg = '';
	if (!$my->id  || $cdate<100000 || !$id || !$specid)
		return;
	$params = getparams();
	$editspec = $params['editspec'];	
	$moderators = '  ,'.$params['moderators'].',';	
	$md = (int)$my->id;
	$mdr = strpos($moderators,','.$md.',');
	$panel = 0;
	if ($md){
		if (($md==$idusr && $params['editspec']) || $mdr)
			$panel = 1;		
	}			
	if ($panel){
		if ($publ<2){
			$database = JFactory::getDBO();
			if ($publ)
				$database->setQuery("UPDATE `#__ttfsp` SET `published` = '".$vl."' WHERE id=".$id);
			else 
				$database->setQuery("UPDATE `#__ttfsp` SET `reception` = '".$vl."' WHERE id=".$id);
			$database->query();
		} else {
			$mainframe = JFactory::getApplication();
			if (!isset($mosConfig_mailfrom)) $mosConfig_mailfrom=$mainframe->getCfg('mailfrom');
			if (!isset($mosConfig_fromname)) $mosConfig_fromname=$mainframe->getCfg('fromname');
			$mess = $vl == 1 ? $params['yesrecept'] : $params['norecept'];
			JUtilityFSP::sendMail( $mosConfig_mailfrom, $mosConfig_fromname, $rmail, _ttfsp_lang_191, $mess, 1);
			$msg = _ttfsp_lang_194;
		}
	}
	$link = 'index.php?option=com_ttfsp&idspec='.$specid.'&cdate='.$cdate;
	if (JVERSION=="1.0"){
		$link=sefRelToAbs($link);
	} else {
		$link=JRoute::_($link);
	}
	myRedirect($link, $msg);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function addclient($sid){
$my	= JFactory::getUser();

$input = JFactory::getApplication()->input;


$id =  $input->getCmd('id', 0);
$addtest =  $input->getCmd('addtest', 0);
$cdate = $input->getCmd('cdate', 0);

$payment_status = 0; // Статус платежа

if (!$cdate) {
	$cdate = $input->getCmd('amp;cdate', '');
}
if (!$id) {
	
	$id = $input->getCmd('amp;id', 0);
}

if (!$addtest) {
	
	$addtest = $input->getCmd('amp;addtest', 0);

}
$params = getparams();

if ((!$my->id && $params['reguser']) || $cdate<100000){
	echo _ttfsp_lang_58;
	return;
}
if ((int) $id==0){
	echo 'error';
	return;
	}
	$where1 = $sid ? ' AND id='.$sid : '';	
	$database = JFactory::getDBO();
	$query = "SELECT * "
	. "\n FROM  #__ttfsp_sprspec "
	. "\n where published=1 ".$where1
	;
	$database->setQuery( $query );
	$rowspec = $database->loadObjectList();

	$query = "SELECT tt.*, spec.name, spec.desc, spec.photo, spec.specphone, spec.offphoto, spec.specmail, spec.idsprspec, spec.idsprsect, spec.number_cabinet"
	. "\n FROM #__ttfsp AS tt"	
	. "\n LEFT JOIN #__ttfsp_spec AS spec ON spec.id = tt.idspec" 
	. "\n WHERE tt.id=".$id." GROUP BY tt.id";
	$database->setQuery( $query);
	$rows = $database->loadObjectList();
	
	/// Количество записанных людей на одно время
	
	$query = "SELECT COUNT(*) FROM #__ttfsp_dop WHERE idrec='".$rows[0]->id."' AND payment_status <> 3";
	$database->setQuery( $query);
	$peoples = $database->loadResult();
	
	///
	
	if (!count($rows)) return;
		$sprspecname='';
		$sprspecdesc='';
		$aspec = array();
		for($s=0;$s<count($rowspec);$s++){
			$myvalue= $rowspec[$s]->id;
			if ( strpos( ' '.$rows[0]->idsprspec, ','.$myvalue.',' )){
				$sprspecname .= $sprspecname ? ', '.$rowspec[$s]->name : $rowspec[$s]->name; // Специализации
				$desc = $rowspec[$s]->desc ? ': '.$rowspec[$s]->desc : '';	
				$sprspecdesc .= $sprspecdesc ? '<br />'.$rowspec[$s]->name.$desc : $rowspec[$s]->name.$desc;
				$aspec[] = $rowspec[$s]->id;
			}
		}	
	$spridspec = $rows[0]->idsprspec;
	$iduchrejd = $rows[0]->idsprsect;
	$msgspec = $sprspecname.': '.$rows[0]->name.'<br /><br />';
	
	$specialist_name = $rows[0]->name; // Имя специалиста
	
	$number_cabinet = $rows[0]->number_cabinet; // Номер кабинета
	
	
	$specid = $rows[0]->idspec;
		$query = "SELECT * "
	. "\n FROM #__ttfsp_el "
	. "\n WHERE published=1 "
	. "\n ORDER BY ordering "
	;
	$database->setQuery( $query);
	$rowel = $database->loadObjectList();
	$link = 'index.php?option=com_ttfsp&idspec='.$rows[0]->idspec.'&cdate='.$cdate;
	if (JVERSION=="1.0"){
		$link=sefRelToAbs($link);
	} else {
		$link=JRoute::_($link);
	}
	if ($rows[0]->reception){
		myRedirect($link,_ttfsp_lang_59);
	}
	if ((int) $addtest > 0){
		$add =  JRequest::getVar(  'add', 0 );
		$fio =  JRequest::getVar(  'fio', 0 );
		$email =  JRequest::getVar(  'ttfsp_email', '' );		
		$phone =  JRequest::getVar(  'phone', 0 );
	 	if ((int) $add==1 && $fio && $phone){
			$mess = $rows[0]->dttime.' '.$rows[0]->hrtime.':'.$rows[0]->mntime;
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
						if ($elem->type<>4){
							if ($elem->fname){
								if ($elem->fname=='fio') $mess .= '<br /><u>'.$elem->title.':</u>'.$fio;
								if ($elem->fname=='phone') $mess .= '<br /><u>'.$elem->title.':</u>'.$phone;
							} else {
								if ($elem->name=='email')
									$field = 'ttfsp_email';
								else
									$field = 'ttfsp_'.$elem->id;
								if ($elem->type==2 && $elem->multisel){
									$vl 	= JRequest::getVar( $field, array(0), 'post' );									
									$val =  htmlspecialchars(implode( ',', $vl ), ENT_QUOTES);								
								} else {
									if ($elem->type==5 || $elem->type==6){
										$vl 	= JRequest::getVar( $field, array(0), 'post' );									
										$val =  htmlspecialchars(implode( ',', $vl ), ENT_QUOTES);								
									} else {
										$val =  htmlspecialchars(JRequest::getString(  $field, 0 ), ENT_QUOTES);
									}
								}
								if (!$val && $elem->required) myRedirect($link,_ttfsp_lang_117);
								if ($elem->type==3 && $val!=$elem->value){
									echo "<script> alert('"._ttfsp_lang_121."'); window.history.go(-1); </script>\n";
									return;
								}			
								if ($val && $elem->mask){
									for($s=0;$s<strlen($val);$s++){
										$ch = substr($val,$s,1);
										if (!strpos(' '.$elem->mask, $ch))
										myRedirect($link,_ttfsp_lang_118);
									}
								}	
								if ($val && $elem->maxlength){
									if (strlen($val)>$elem->maxlength)
									myRedirect($link,_ttfsp_lang_119);
								}
								if ($val && $elem->type<>3) $mess .= '<br /><u>'.$elem->title.':</u>'.$val;			
							}
						}
					}	
				}
			} else {
				$mess .= '<br />'.$fio;
				$mess .= '<br />'.$phone;
			}
			$ip =getenv('REMOTE_ADDR');	
			
			
			//// Проверка , если на одно время несколько записей
			
			
			$rcp = 1;
			
			if ($rows[0]->plimit){
				
				if (($rows[0]->plimit-1)>$peoples) {
					
					$rcp = 0;
					
							
					
				}
			}
			
		
			
			
			///////////
			
			
			
			
			
			$order_password = rand_number (6);
			
			$summ_oplata = (int) ($params['sposob_oplaty_1_on'] + $params['sposob_oplaty_0_on'] + $params['sposob_oplaty_2_on']); // Все ли способы оплаты включены
			
			// Специализации - выбраны на странице заказа или нет
			
			$specialization_select = JRequest::getVar(  'type_spec', '' );
			
			if ($params["specialization_select_on"] == 1 && $specialization_select != '') {
				$sprspecname = $specialization_select;
			}
			
			// Получаем учреждение
	
	
			if ($iduchrejd) {
				
				$query = "SELECT * FROM `#__ttfsp_sprsect` WHERE id = ".$iduchrejd;
				$database->setQuery( $query);
				$ucrows = $database->loadObjectList();
				
				if (count ($ucrows) != 0) {
					$uchrejdenie = $ucrows[0]->name;
					$uchrejdenie_address = $ucrows[0]->address;
					
				}
				
				else {
					
					$uchrejdenie = 0;
					$uchrejdenie_address = 0;
					
				}
			
			}
	
			else  {
				
				$uchrejdenie = 0;
				$uchrejdenie_address = 0;
			}
			
			
			// Номер заказа
		
			$number_of_order = number_order ($params['type_number_order']);
			
			//
			
			$database->setQuery("INSERT INTO `#__ttfsp_dop` (`rfio`, `rphone`, `info`, `ipuser`,`iduser`,`idrec`, `summa`, `payment_status`, `rmail`, `number_order`, `cdate`, `office_name`, `specializations_name`, `specialist_name`, `id_specialist`, `specialist_email`, `specialist_phone`, `hours`, `minutes`, `order_password`, `office_address`, `number_cabinet`, `date` ) VALUES ('".$fio."','".$phone."','".$mess."','".$ip."','".$my->id."','".$id."','".$rows[0]->pricezap."','".$payment_status."','".$email."','".$number_of_order."','".$cdate."','".$uchrejdenie."','".$sprspecname."','".$specialist_name."','".$specid."','".$rows[0]->specmail."','".$rows[0]->specphone."','".$rows[0]->hrtime."','".$rows[0]->mntime."','".$order_password."','".$uchrejdenie_address."','".$number_cabinet."','".$rows[0]->dttime."');");
			$database->query();
			
			// Получаем данные заказа
			
			$textinfo = '';
			
			$query = "SELECT * FROM #__ttfsp_dop WHERE idrec='".$id."'";
			
			$database->setQuery($query);
			
			$zakaz_row = $database->loadObjectList();
		
			for($s=0;$s<count($zakaz_row);$s++){
				
				$myvalue = $zakaz_row[$s];
				
				if ($myvalue->number_order == $number_of_order) {
					
					$zakaz_info = $myvalue;
					
				}
				
				if ($myvalue->payment_status != 3) {
					
					$textinfo = $textinfo.$myvalue->info.'<br><br>';
					
				}
				
						
			}
			
			if ((int) $my->id>0) {

					$database->setQuery("UPDATE `#__ttfsp` SET `reception` = '".$rcp."', `rmail` = '".$email."', `iduser`='".$my->id."', `rfio` = '".$fio."', `rphone`='".$phone."', `info`='".$textinfo."', `ipuser`='".$ip."' WHERE id=".$id);
					$database->query();
					$database->setQuery("UPDATE `#__users` SET `fio` = '".$fio."', `phone`='".$phone."' WHERE id=".$my->id);
					$database->query();
				
			} else {
					$database->setQuery("UPDATE `#__ttfsp` SET `reception` = '".$rcp."', `rmail` = '".$email."', `rfio` = '".$fio."', `rphone`='".$phone."', `info`='".$textinfo."', `ipuser`='".$ip."' WHERE id=".$id);
					$database->query();
			}
			
			

			

				
				final_sms ($zakaz_info, _ttfsp_lang_71); // Отпраавка смс
				
				final_mail ($zakaz_info, _ttfsp_lang_71); // Вызываем отправку почтовых уведомлений
			
			
			
			/// Если оплата через сайт используется
			
			if ($params["billing_on"] == 1 && $summ_oplata > 0 &&  (int) $rows[0]->pricezap > 0) {
				
				
				
				if ($email != '') {
				
					
					HTML_ttfsp::paymentpage($zakaz_info, $params);  // Вызываем страницу выбора платежных систем
				
				
				}
				
				else {
					
					if ((int) $my->id>0) {

					$database->setQuery("UPDATE `#__ttfsp` SET `reception` = 0, `rmail` = '".$email."', `iduser`='".$my->id."', `rfio` = '".$fio."', `rphone`='".$phone."', `info`='".$mess."', `ipuser`='".$ip."' WHERE id=".$id);
					$database->query();
					
				
					} else {
						
					$database->setQuery("UPDATE `#__ttfsp` SET `reception` = 0, `rmail` = '".$email."', `rfio` = '".$fio."', `rphone`='".$phone."', `info`='".$mess."', `ipuser`='".$ip."' WHERE id=".$id);
					$database->query();
					
					}	
					
					
					
					myRedirect($link, _ttfsp_error_email, 'error');
					
					
				}
				
				
			}
			
			
		}
	}
	
	
	if ((int) $addtest == 0){
		
		$rowuser = 0;
			
		if ($my->id){
			
				$query = "SELECT * "
				. "\n FROM #__users "
				. "\n WHERE id=".$my->id;
				$database->setQuery( $query);
				$rowuser = $database->loadObjectList();
				$rowuser = $rowuser[0];
			
		}
		
		
		//// Вызов

		HTML_ttfsp::addclient($rows[0], $rowuser, $cdate, $rowel, $params, $sprspecdesc, $aspec);
		
		
	}
	
	
}


//////////// Отправка СМС сообщения по списку адресатов

function final_sms ($zakaz_row, $subject) {
				
			$params = getparams();
			
			if ($params['qtsms_on'] && $params['qtsms_login'] && $params['qtsms_password'] && $params['qtsms_host'] && $params['qtsms_message'] && ($zakaz_row->specialist_phone || $params['qtsms_phone']) ){

				header("Content-Type: text/html; charset=UTF-8");

				include_once(JPATH_ROOT.DS."components".DS."com_ttfsp".DS."includes".DS."QTSMS.class.php");
				$sms= new QTSMS($params['qtsms_login'],$params['qtsms_password'],$params['qtsms_host']);
				$sms_phone = $params['qtsms_phone'] ? $params['qtsms_phone'].','.$zakaz_row->specialist_phone : $zakaz_row->specialist_phone;
				$sms_text=$params['qtsms_message'].' '.$zakaz_row->date.' '.$zakaz_row->hours.':'.$zakaz_row->minutes.' '.$zakaz_row->rfio;
				$sender_name=$params['qtsms_sender'];
				$period=600;
				$result=$sms->post_message($sms_text, $sms_phone, $sender_name,'x124127456',$period);
				
			echo $result;

			
					
			}	
			
			
			
}

////////// Отправка Почты 

function final_mail ($zakaz_row, $subjectmail) {
	

			$session = JFactory::getSession();
			
			$psws_sess = $session->getId();
			
			$link = 'index.php?option=com_ttfsp&view=successpage&number_order='.$zakaz_row->number_order.'&psws_sess='.$psws_sess;
			
	
			$params = getparams();
			
			$summ_oplata = (int) ($params['sposob_oplaty_1_on'] + $params['sposob_oplaty_0_on'] + $params['sposob_oplaty_2_on']);
	
			$date_time = date('Y-m-d',$zakaz_row->cdate);
			
			
			
			If ($zakaz_row->payment_status == 0) {
				$payment_status = '<h2 style="font-size: 16px; color: red;">'._ttfsp_payment_status_0.'</h2>';
			}
			
			If ($zakaz_row->payment_status == 1) {
				$payment_status = '<h2 style="font-size: 16px; color: green;">'._ttfsp_payment_status_1.'</h2>';
			}
			
			If ($zakaz_row->payment_status == 2) {
				$payment_status = '<h2 style="font-size: 16px; color: #89498d;">'._ttfsp_payment_status_2.'</h2>';
			}
			
			If ($zakaz_row->payment_status == 3) {
				$payment_status = '<h2 style="font-size: 16px; color: #fff; background: #000; padding: 3px;">'._ttfsp_payment_status_3.'</h2>';
			}
			
			
		
	
			// Подключение шаблона письма
	
			$mailtemplateurl = 'mail.php';
			
			if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$mailtemplateurl) ) {
				require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$mailtemplateurl);
			} else {
			echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$mailtemplateurl;
			return;
			}
			
			// Отправка уведомлений на почтовые ящики
			
			
			
			$textmail = mailtemplate($zakaz_row->specialist_name, $zakaz_row->specializations_name, $zakaz_row->info, $zakaz_row->id_specialist, $zakaz_row->office_name, $params, $payment_status, $zakaz_row->summa, $zakaz_row->number_order,$zakaz_row->order_password, 0);
			
			$mainframe = JFactory::getApplication();
			
			if (!isset($mosConfig_mailfrom)) $mosConfig_mailfrom=$mainframe->getCfg('mailfrom');
			if (!isset($mosConfig_fromname)) $mosConfig_fromname=$mainframe->getCfg('fromname');
			if ($params['email']) {
				JUtilityFSP::sendMail( $mosConfig_mailfrom, $mosConfig_fromname, $params['email'], $subjectmail, $textmail, 1);
			}
			
			if ($params['offemail']==0 && $zakaz_row->specialist_email) {
				JUtilityFSP::sendMail( $mosConfig_mailfrom, $mosConfig_fromname, $zakaz_row->specialist_email, $subjectmail, $textmail, 1);
			}
			
			if ($zakaz_row->rmail && $params['createmsg'] && $params['onmsg']){
				
				$textmail = mailtemplate($zakaz_row->specialist_name, $zakaz_row->specializations_name, $zakaz_row->info, $zakaz_row->id_specialist, $zakaz_row->office_name, $params, $payment_status, $zakaz_row->summa, $zakaz_row->number_order, $zakaz_row->order_password, 1);
				
				JUtilityFSP::sendMail( $mosConfig_mailfrom, $mosConfig_fromname, $zakaz_row->rmail, $subjectmail, $textmail, 1);
				
				// Если оплата не используется
				
				if ($params["billing_on"] == 0 || $summ_oplata < 1 || (int) $zakaz_row->summa <= 0 )  {
					myRedirect($link,$params['createmsg']);	
				}
							
			} else {
				
				// Если оплата не используется
				
				if ($params["billing_on"] == 0 || $summ_oplata < 1 || (int) $zakaz_row->summa <= 0) {	
					myRedirect($link,_ttfsp_lang_67);
				}
			}	

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function viewtime($idspec, $sid, $user_rec=0){
	$cdate =  JRequest::getCmd(  'cdate', 0 );
	$idDelRec = JRequest::getCmd('idDelRec', 0);
	$database = JFactory::getDBO();
	if ($user_rec==0){
		$where1 = '';	
		if ($sid){
			$where1 = ' AND id='.$sid;
		} 	
		$w = date('w');
		$w = $w ==0 ? 6 : $w-1;
		$curdate  = mktime(0, 0, 0, date("m")  , date("d")-$w, date("Y"));
		if ($curdate>132004800){
			$curdate = $curdate+3660;
		}		
		if (!$cdate) $cdate=$curdate;

			$database->setQuery( "SELECT * FROM #__ttfsp_sprspec WHERE published=1 ".$where1 );
			$rowspec = $database->loadObjectList();
			$query = "SELECT spec.*"
			. "\n FROM #__ttfsp_spec AS spec"
			. "\n  WHERE spec.published=1 AND spec.id=".$idspec
			;
		$database->setQuery( $query);
		$rows = $database->loadObjectList();
		if ($rows){
			$sprspecname='';
			$sprspecdesc='';
			for($s=0;$s<count($rowspec);$s++){
				$myvalue= $rowspec[$s]->id;
				if ( strpos( ' '.$rows[0]->idsprspec, ','.$myvalue.',' )){
					$sprspecname .= $sprspecname ? ', '.$rowspec[$s]->name : $rowspec[$s]->name;
					$desc = $rowspec[$s]->desc ? ': '.$rowspec[$s]->desc : '';	
					$sprspecdesc .= $sprspecdesc ? '<br />'.$rowspec[$s]->name.$desc : $rowspec[$s]->name.$desc;
				}
			}	
			if (JVERSION != '1.0'){
				$mainframe = JFactory::getApplication();
				$pathway	= $mainframe->getPathway();
				$pathway->addItem($sprspecname.' '.$rows[0]->name, 'index.php?option=com_ttfsp');
			}
		}
	}
	$my	= JFactory::getUser();	
	$params = getparams();
	if ($my->id && $params['modiuser'] && $idDelRec){
			$query = "SELECT tt.*, sp.specmail "
		. "\n FROM #__ttfsp AS tt"
		. "\n LEFT JOIN #__ttfsp_spec AS sp ON sp.id = tt.idspec" 		
		. "\n WHERE tt.iduser=".$my->id." AND tt.id=".$idDelRec
		;
		$database->setQuery( $query);
		$rowdel = $database->loadObjectList();
		
		
		// Удаление заказа со страницы отображения времени
		
		
		if (count($rowdel)){
			
			$database->setQuery("UPDATE `#__ttfsp` SET `reception` = '0', `iduser` = '0', `ipuser` = '', `info` = '', `rfio` = '', `rphone` = '', `rmail` = ''  WHERE iduser=".$my->id." AND id=".$idDelRec);
		
			$database->query();
			
			
			if (!$rowdel[0]->plimit || $rowdel[0]->plimit == 0) {
			
				$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = 3 WHERE idrec='".$idDelRec."' AND `date` = '".$rowdel[0]->dttime."' AND `hours` = '".$rowdel[0]->hrtime."' AND `minutes` = '".$rowdel[0]->mntime."'";
			
				$database->setQuery($query);
		
				$database->query();
			
			} else  {
				
				$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = 3 WHERE idrec='".$idDelRec."' AND `date` = '".$rowdel[0]->dttime."' AND `hours` = '".$rowdel[0]->hrtime."' AND `minutes` = '".$rowdel[0]->mntime."' ORDER BY id DESC LIMIT 1";
			
				$database->setQuery($query);
		
				$database->query();
				
			}
			
			
			
			
			$query = "SELECT * FROM #__ttfsp_dop WHERE idrec='".$idDelRec."' AND `date` = '".$rowdel[0]->dttime."' AND `hours` = '".$rowdel[0]->hrtime."' AND `minutes` = '".$rowdel[0]->mntime."' ORDER BY id DESC LIMIT 1";
			
			$database->setQuery($query);
			
			$zakaz_row = $database->loadObjectList();
			
			/// Отправка сообщения об отмене заказа
			
			if ($zakaz_row) {
			
				$title_mail = _ttfsp_status_order_subject.$zakaz_row[0]->number_order;
			
				final_mail ($zakaz_row[0], $title_mail);
				
			}
		
			

			
		}
	}
	if ($user_rec==0){
		$moderators = '  ,'.$params['moderators'].',';		
		$md = (int)$my->id;
		$whr = " AND tt.published=1 AND tt.idspec=".$idspec;
		if ($md){
			if (($md==$rows[0]->idusr && $params['editspec']) || strpos($moderators,','.$md.','))
				$whr = " AND tt.idspec=".$idspec;		
		}
		$startdate = date('Y-m-d',$cdate);
		$enddate = date('Y-m-d',$cdate+518400);
		$query = "SELECT tt.*, COUNT(dop.id) AS peoples "
		. "\n FROM #__ttfsp AS tt"
		. "\n LEFT JOIN #__ttfsp_dop AS dop ON dop.idrec = tt.id AND payment_status <> 3" 		
		. "\n WHERE tt.dttime>='".$startdate."' AND tt.dttime <='".$enddate."' ".$whr." GROUP BY tt.id ORDER BY tt.dttime,tt.hrtime,tt.mntime,tt.idspec ASC"
		;
		$database->setQuery( $query);
		$rowstime = $database->loadObjectList();
		$ttime = array ();
		$ttime[0]='';
		$ttime[1]='';
		$ttime[2]='';
		$ttime[3]='';
		$ttime[4]='';
		$ttime[5]='';
		$ttime[6]='';
		if (count($rowstime)){
			$templuser = 'addtime.php';
			if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser) ) {
				require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser);
			} else {
				echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser;
				return;
			}
			$cortime = (int) $params['cortime'];
			$time = time()+$cortime;
			$date = date('Y-m-d',$time);
			$time = (int) $params['notime']+$time;
			addtime_ttfsp::addtimettfsp($rowstime, $date, $ttime, $cdate, $rows[0]->idusr, $my->id, $params, $time, $idspec, $sid);
		}
		if (!$ttime[0]) $ttime[0]='<div class="offrecept">'.$params["title_nor"].'</div>';
		if (!$ttime[1]) $ttime[1]='<div class="offrecept">'.$params["title_nor"].'</div>';
		if (!$ttime[2]) $ttime[2]='<div class="offrecept">'.$params["title_nor"].'</div>';
		if (!$ttime[3]) $ttime[3]='<div class="offrecept">'.$params["title_nor"].'</div>';
		if (!$ttime[4]) $ttime[4]='<div class="offrecept">'.$params["title_nor"].'</div>';
		if (!$ttime[5]) $ttime[5]='<div class="offrecept">'.$params["title_nor"].'</div>';
		if (!$ttime[6]) $ttime[6]='<div class="offrecept">'.$params["title_nor"].'</div>';
		HTML_ttfsp::viewtime($rows, $rowstime, $cdate, $curdate, $ttime,$params, $sprspecdesc, $sid);
	} else {
		if ($my->id && $params['viewuser']){
			$database->setQuery( "SELECT id, name FROM #__ttfsp_sprspec WHERE published=1 ");
			$rowspec = $database->loadObjectList();	
			if (!count($rowspec))
				$rowspec=0;
				
			$query = "SELECT tt.*, spec.name AS specname, spec.idsprspec, sect.name AS sprsect "
			. "\n FROM #__ttfsp AS tt"
			. "\n LEFT JOIN #__ttfsp_spec AS spec ON spec.id = tt.idspec"
			. "\n LEFT JOIN #__ttfsp_sprsect AS sect ON sect.id = spec.idsprsect"			
			. "\n WHERE iduser=".$my->id." GROUP BY tt.id ORDER BY tt.dttime,tt.hrtime,tt.mntime,tt.idspec ASC"
			;
			$database->setQuery( $query);
			$rowstime = $database->loadObjectList();
			if (count($rowstime))
				HTML_ttfsp::viewdetail($rowstime, $rowspec, $params);
			
			else 
				echo _ttfsp_lang_205;			
		} else {
			echo _ttfsp_lang_204;
		}
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function viewsect(){
	$database = JFactory::getDBO();
	$database->setQuery( "SELECT * FROM #__ttfsp_sprsect WHERE published=1 ");
	$rows = $database->loadObjectList();	
	$params = getparams();	
	HTML_ttfsp::viewsect($rows, $params);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function viewspec_all($id=0, $jcmt=0){
$my	= JFactory::getUser();
$where = '';
$where1 = '';
$office =  JRequest::getCmd(  'office', 0 );
if ($id && !$jcmt){
 $where .= " AND spec.idsprspec LIKE '%,".$id.",%' ";
 $where1 = ' AND id='.$id;
}
if ($id && $jcmt){
 $where .= " AND spec.id = ".$id;
} 
 
	if ($office) $where .= " AND spec.idsprsect = ".$office;
	$database = JFactory::getDBO();
	$database->setQuery( "SELECT * FROM #__ttfsp_sprspec WHERE published=1 ".$where1 );
	$rowspec = $database->loadObjectList();	
	$query = "SELECT spec.*"
		. "\n FROM #__ttfsp_spec AS spec"
		. "\n  WHERE spec.published=1 ".$where." ORDER BY spec.ordering ASC"
		;
	$database->setQuery( $query);
	$rows = $database->loadObjectList();
	if ($id && $jcmt){
		$rowspec=array();
	} 	
	$params = getparams();	
	HTML_ttfsp::viewspec($rows, $params, $rowspec, $id, $jcmt, $my, $office);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getparams(){
$database = JFactory::getDBO();
	$query = "SELECT name AS text, value FROM #__ttfsp_set";
	$database->setQuery( $query );
	$params = array();
	$params = $database->loadObjectList();
		if ( !count($params) ) {
			echo "<script> alert('Error load configuration'); window.history.go(-1); </script>\n";
			exit();
		}
		foreach($params as $param) {
		$params[$param->text] = $param->value;
		}
return $params;
}
//////////////////////////////////////////////////////////////////////////////////////////////////
function utf8_win ($s){
if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false) {
$out="";
$c1="";
$byte2=false;
for ($c=0;$c<strlen($s);$c++){
$i=ord($s[$c]);
if ($i<=127) $out.=$s[$c];
if ($byte2){
$new_c2=($c1&3)*64+($i&63);
$new_c1=($c1>>2)&5;
$new_i=$new_c1*256+$new_c2;
if ($new_i==1025){
$out_i=168;
}else{
if ($new_i==1105){
$out_i=184;
}else {
$out_i=$new_i-848;
}
}
$out.=chr($out_i);
$byte2=false;
}
if (($i>>5)==6) {
$c1=$i;
$byte2=true;
}
}
return $out;
} else {
return $s;
}
} 
//////////////////////////////////////////////////////////////////////////////////////////////////
function AlertMsg( $text, $act=0){
	$action=$act ? '' : 'window.history.go(-1);';
	echo "<script>alert('$text'); $action</script> \n";
	echo '<noscript>';
	echo "$text\n";
	echo '</noscript>';
}
///////////////////////////////////////////////////////////////////////////////////////////////////
function myRedirect($url='',$msg='', $err=''){
if ($url=='') $url=$_SERVER["REQUEST_URI"];
	
	$app = JFactory::getApplication();
	$app->redirect( $url, $msg, $err );
	

}
/////////////////////////////////////////////////////////////////////////////////////////////////
	function selPages( $total, $limitstart, $limit, $docform="ttfspviewForm" ) {
		$txt = '';
		$displayed_pages = 10;
		$total_pages = $limit ? ceil( $total / $limit ) : 0;
if ($total_pages <2) return '';
		$this_page = $limit ? ceil( ($limitstart+1) / $limit ) : 1;
		$start_loop = (floor(($this_page-1)/$displayed_pages))*$displayed_pages+1;
		if ($start_loop + $displayed_pages - 1 < $total_pages) {
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else {
			$stop_loop = $total_pages;
		}


        if (!defined( '_PN_LT' ) || !defined( '_PN_RT' ) ) {
            DEFINE('_PN_LT','&lt;');
            DEFINE('_PN_RT','&gt;');
        }

		$pnSpace = '';
		if (_PN_LT || _PN_RT) $pnSpace = "&nbsp;";

		if ($this_page > 1) {
			$page = ($this_page - 2) * $limit;
	$txt .= '<span class="pagenav" onclick="document.'.$docform.'.limitstart.value=0;document.'.$docform.'.submit();">'. _PN_LT . _PN_LT . $pnSpace  .'</span> ';
	$txt .= '<span class="pagenav" onclick="document.'.$docform.'.limitstart.value='.$page.';document.'.$docform.'.submit();">'. _PN_LT . $pnSpace  .'</span> ';

		} else {
			$txt .= '<span class="pagenavdisab">'. _PN_LT . _PN_LT . $pnSpace  .'</span> ';
			$txt .= '<span class="pagenavdisab">'. _PN_LT . $pnSpace  .'</span> ';
		}

		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $limit;
			if ($i == $this_page) {
				$txt .= '<span class="pagenavsel">'. $i .'</span> ';
			} else {
$txt .= '<span class="pagenav" onclick="document.'.$docform.'.limitstart.value='.$page.';document.'.$docform.'.submit();"><strong>'. $i .'</strong></span> ';
			}
		}

		if ($this_page < $total_pages) {
			$page = $this_page * $limit;
			$end_page = ($total_pages-1) * $limit;
$txt .= '<span class="pagenav" onclick="document.'.$docform.'.limitstart.value='.$page.';document.'.$docform.'.submit();">'.  $pnSpace . _PN_RT .'</span> ';
$txt .= '<span class="pagenav" onclick="document.'.$docform.'.limitstart.value='.$end_page.';document.'.$docform.'.submit();">'.  $pnSpace . _PN_RT . _PN_RT .'</span>';
		} else {
			$txt .= '<span class="pagenavdisab">' . $pnSpace . _PN_RT .'</span> ';
			$txt .= '<span class="pagenavdisab">'. $pnSpace . _PN_RT . _PN_RT .'</span>';
		}
		return $txt;
	}
////////////////////////////////////////////////////
function monthrus($mn){
	switch ( $mn ) {
		case '01': 
		return _ttfsp_lang_76;
		break;
		case '02': 
		return _ttfsp_lang_77;
		break;
		case '03': 
		return _ttfsp_lang_78;
		break;
		case '04': 
		return _ttfsp_lang_79;
		break;
		case '05': 
		return _ttfsp_lang_80;
		break;
		case '06': 
		return _ttfsp_lang_81;
		break;
		case '07': 
		return _ttfsp_lang_82;
		break;
		case '08': 
		return _ttfsp_lang_83;
		break;
		case '09': 
		return _ttfsp_lang_84;
		break;
		case '10': 
		return _ttfsp_lang_85;
		break;
		case '11': 
		return _ttfsp_lang_86;
		break;
		case '12': 
		return _ttfsp_lang_87;
		break;
	}
}

function resultx () {
	
		$params = getparams();	
		

	    if($_REQUEST['LMI_PREREQUEST']==1) {
		    echo 'YES';
		    die();
        }
		$LMI_PAYEE_PURSE = $_REQUEST['LMI_PAYEE_PURSE'];
		$LMI_PAYMENT_AMOUNT = $_REQUEST['LMI_PAYMENT_AMOUNT'];
		$LMI_PAYMENT_NO = trim($_REQUEST['LMI_PAYMENT_NO']);
		
		$LMI_SECRET_KEY = $_REQUEST['LMI_SECRET_KEY'];
		$LMI_MODE = $_REQUEST['LMI_MODE'];
		$LMI_SYS_INVS_NO = $_REQUEST['LMI_SYS_INVS_NO'];
		$LMI_SYS_TRANS_NO = $_REQUEST['LMI_SYS_TRANS_NO'];
		$LMI_SYS_TRANS_DATE = $_REQUEST['LMI_SYS_TRANS_DATE'];
		$LMI_PAYER_PURSE = $_REQUEST['LMI_PAYER_PURSE'];
		$LMI_PAYER_WM = $_REQUEST['LMI_PAYER_WM'];
		$LMI_HASH = $_REQUEST['LMI_HASH'];	

		//Проверяем номер магазина 
		
		if($LMI_PAYEE_PURSE!= $params['lang_zpayment_id']) {
			
			die("ERR: Id магазина не соответсвует настройкам сайта!");
		}

		$CalcHash = md5($LMI_PAYEE_PURSE.$LMI_PAYMENT_AMOUNT.$LMI_PAYMENT_NO.$LMI_MODE.$LMI_SYS_INVS_NO.$LMI_SYS_TRANS_NO.$LMI_SYS_TRANS_DATE.$params['merchant_key_zpayment'].$LMI_PAYER_PURSE.$LMI_PAYER_WM);

		if($LMI_HASH == strtoupper($CalcHash)) {
			
	
			$database 	= JFactory::getDBO();
			$inv_id = $_REQUEST["LMI_PAYMENT_NO"];
			
			
			
			$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$inv_id."'";
			$database->setQuery($query);
			
			$zakaz_row = $database->loadObjectList();
			
			if ($zakaz_row[0]->payment_status == 0) {
			
				$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = 1 WHERE `number_order` ='".$inv_id."' LIMIT 1" ;
				$database->setQuery( $query);
				$database->query();
			
				$zakaz_row[0]->payment_status = 1;
			
				final_mail ($zakaz_row[0], _ttfsp_payment_mail_success.$zakaz_row[0]->number_order); // Вызываем отправку смс и почтовых уведомлений
			
			}


			return true;
		}
		else {
			exit();
		}
}

function successx() {
	

		$LMI_PAYMENT_NO = trim($_REQUEST['LMI_PAYMENT_NO']);

		
		if($_REQUEST["LMI_PAYMENT_NO"]) {
			
			$database 	= JFactory::getDBO();
			$inv_id = $_REQUEST["LMI_PAYMENT_NO"];
			

			
			$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$inv_id."'";
			$database->setQuery($query);
			
			$zakaz_row = $database->loadObjectList();
			
			$session = JFactory::getSession();
			
			$psws_sess = $session->getId();
			
			$link = 'index.php?option=com_ttfsp&view=successpage&number_order='.$zakaz_row[0]->number_order.'&psws_sess='.$psws_sess;
			
			myRedirect($link,_ttfsp_payment_success);
			
			
		}
		
		
	
	
}

function failx () {
			
		$LMI_PAYMENT_NO = trim($_REQUEST['LMI_PAYMENT_NO']);

		
		if($_REQUEST["LMI_PAYMENT_NO"]) {
			
			$database 	= JFactory::getDBO();
			$inv_id = $_REQUEST["LMI_PAYMENT_NO"];
			

			
			$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$inv_id."'";
			$database->setQuery($query);
			
			$zakaz_row = $database->loadObjectList();
			
			$link = 'index.php?option=com_ttfsp&idspec='.$zakaz_row[0]->id_specialist.'&cdate='.$zakaz_row[0]->cdate;
			
			myRedirect($link,_ttfsp_payment_failed, 'error');
			
			
		}
}

function number_order ($var) {
	
	$params = getparams();
	
	$count_symbols = (int) $params['count_symbols'];
	
	$x = 0;
	
	if ($count_symbols < 1 || !$count_symbols) {
		
		$count_symbols = 1;
	} 
	
	$database 	= JFactory::getDBO();

	
	if ($var == 0) {
		
			
		
			$query = "SELECT id FROM #__ttfsp_dop ORDER BY id DESC LIMIT 1";
			$database->setQuery($query);
			$data = $database->loadResult();
			
			$data = (int) $data + 1;
			
			while ($x == 0) {
				
				$rand = strtoupper(substr(uniqid(sha1(time())),0,$count_symbols));
				
				$data_final = $data.'-'.$rand;
			
				$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$data_final."'";
				$database->setQuery($query);
				$count = $database->loadResult();
			
			if (!$count) {
				
				$x = 1;
				
			}

		}

	
			return $data_final;
		
	}
	
	
	
	if ($var == 1) {
	
		while ($x == 0) {
	
			$rand = strtoupper(substr(uniqid(sha1(time())),0,$count_symbols));
			
			$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$rand."'";
			$database->setQuery($query);
			$count = $database->loadResult();
			
			if (!$count) {
				
				$x = 1;
				
			}

		}
	}
	
	return $rand;
}

function rand_number ($max) {
		
	$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; 

	$size=StrLen($chars)-1; 

	$password=null; 

    while($max--) 
    $password.= $chars[rand(0,$size)]; 
    
    return $password;
	
}

function freepayment () {
	
	$link = JURI::base() .'index.php?option=com_ttfsp';
	
	$input = JFactory::getApplication()->input;

	$number_order =  $input->getCmd('order_number_freepayment', 0);
	
	if (!$number_order) {
		return;
	}
	
	$database 	= JFactory::getDBO();
	
	$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$number_order."'";
			
	$database->setQuery($query);
			
	$zakaz_row = $database->loadObjectList();
	
	/// Заказ отсутствует в базе данных
	
	if (!$zakaz_row) {
		
		myRedirect($link, _ttfsp_error_noorder, 'error');
		
	}
	
	/// Ваш заказ уже оплачен
	
	if ($zakaz_row[0]->payment_status == 1) {
		
		myRedirect($link, _ttfsp_order_lod_paym, 'error');
	}
	
	if ($zakaz_row[0]->payment_status == 0) {
		
		$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = 2 WHERE `number_order` ='".$number_order."' LIMIT 1" ;
		$database->setQuery( $query);
		$database->query();
	
		$zakaz_row[0]->payment_status = 2;
			
		$session = JFactory::getSession();
			
		$psws_sess = $session->getId();
			
		$link = JURI::base() .'index.php?option=com_ttfsp&view=successpage&number_order='.$zakaz_row[0]->number_order.'&psws_sess='.$psws_sess;
	
		final_mail ($zakaz_row[0], _ttfsp_payment_mail_confirmed.$zakaz_row[0]->number_order);
		
		myRedirect($link, _ttfsp_payment_success_confirmed);
		
	}
	
	if ($zakaz_row[0]->payment_status == 2) {
		
		$session = JFactory::getSession();
			
		$psws_sess = $session->getId();
			
		$link = JURI::base() .'index.php?option=com_ttfsp&view=successpage&number_order='.$zakaz_row[0]->number_order.'&psws_sess='.$psws_sess;
		
		myRedirect($link, _ttfsp_payment_success_confirmed_later);
		
	}
	
	/// Ваш заказ отменен
	
	if ($zakaz_row[0]->payment_status == 3) {
		
		myRedirect($link, _ttfsp_order_lod_paym_cancel, 'error');
	}
	
	myRedirect($link, _ttfsp_error_final, 'error');

	
}

function successpage () {
	
	$doc = JFactory::getDocument();
	$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
	$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');
	
	$session = JFactory::getSession();
	
	$database 	= JFactory::getDBO();
			
	$psws_sess_input = $session->getId();
	
	
	
	$componentlink = JURI::base() .'index.php?option=com_ttfsp';
	
	$params = getparams();
	
	$input = JFactory::getApplication()->input;

	$number_order =  $input->getCmd("number_order");
	
	$psws_sess = $input->getCmd('psws_sess', 0);
	
	$paswd_order = $input->get('paswd_order', null, 'RAW');
	
	$simbols = strlen ($paswd_order);
	
	
	
	if (!$number_order) {
		
		myRedirect($componentlink, _ttfsp_error_final, 'error');
		
		
	}
	
	else {
		
		$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$number_order."'";
			
		$database->setQuery($query);
			
		$zakaz_row = $database->loadObjectList();
		
	}
	
	if (!$zakaz_row) {
		
		myRedirect($componentlink, _ttfsp_error_noorder, 'error');
		
	}
	
	if ($paswd_order != $zakaz_row[0]->order_password && $simbols > 0) {
		
		echo '<script> alert ("'._ttfsp_error_password.'"); </script>';
	}

	
	if ($psws_sess == $psws_sess_input || $paswd_order == $zakaz_row[0]->order_password) {
		
		HTML_ttfsp::successpage($zakaz_row[0], $params);

	}
	
	else {
		
		echo '
		<div class="form_paswwd">
		
		<div class="psfd_form_title"><h3>'._ttfsp_pswd_form_title_0.'</h3><span>'._ttfsp_pswd_form_title.'</span></div>
	
			<form action="'.$componentlink.'&view=successpage&number_order='.$number_order.'" method="post" name="ttfspForm" id="ttfspForm">
		
				<input class="pswd_text_inp" type="text" id="paswd_order" name="paswd_order" value="">
				<input class="pswd_send" type="submit">
		
			</form>
	
		</div>
		
		';
		
	}

	
}

function checkyandexkassa () {
	
	$link = JURI::base() .'index.php?option=com_ttfsp';
	
	file_put_contents('log.txt', '\n[' . date("Y-m-d H:i:s") . '] ' .$_REQUEST['action'], FILE_APPEND);
	
	if ($_REQUEST['action'] == 'checkOrder') {
	
		$params = getparams();
		
		require_once(JPATH_ROOT.DS."components".DS."com_ttfsp".DS."includes".DS."yandexkassa.php");
		
		$response = checkMD5 ($_REQUEST, $params);
				
				
				if ($response) {
					
					$response = buildResponse($params, 'checkOrder', $_REQUEST['invoiceId'], 0);
					
					sendResponse($response);
					
				}

		
		
	} else {
		
		myRedirect($link, _ttfsp_error_final);
		
		
	}
	
}

function cancelyandexkassa () {
	
	$link = JURI::base() .'index.php?option=com_ttfsp';
	
	$params = getparams();
	
	$number_order = $_REQUEST['orderNumber'];
	
	$session = JFactory::getSession();
			
	$psws_sess = $session->getId();
			
	$linksucces = JURI::base() . 'index.php?option=com_ttfsp&view=successpage&number_order='.$number_order.'&psws_sess='.$psws_sess; // Переход на страницу просмотра заказа
	
	if ($_REQUEST['shopId'] == $params['yandex_kassa_shopid']) {
		
		myRedirect($linksucces, _ttfsp_error_cancel_oplata, 'error');
		
		
		
		
	} else {
		
		myRedirect($link, _ttfsp_error_final);
		
		
		
	}
	
}

function avisoyandexkassa () {
	
	$params = getparams();
	
	$link = JURI::base() .'index.php?option=com_ttfsp';
	
	$number_order = $_REQUEST['orderNumber'];
	
	$database 	= JFactory::getDBO();
	

	
	if ($_REQUEST['action'] == 'paymentAviso' && $_REQUEST['shopId'] == $params['yandex_kassa_shopid']) {
		
		
		
		$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$number_order."' ORDER BY id DESC LIMIT 1";
		$database->setQuery($query);
			
		$zakaz_row = $database->loadObjectList();
		
		require_once(JPATH_ROOT.DS."components".DS."com_ttfsp".DS."includes".DS."yandexkassa.php");
		
		$response = checkMD5 ($_REQUEST, $params);
				
				
				if ($response) {
					
						if ($zakaz_row[0]->payment_status == 0 || $zakaz_row[0]->payment_status == 2) {
								
					
			
								$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = 1 WHERE `number_order` ='".$number_order."' LIMIT 1" ;
								$database->setQuery( $query);
								$database->query();
			
								$zakaz_row[0]->payment_status = 1;
			
								final_mail ($zakaz_row[0], _ttfsp_payment_mail_success.$zakaz_row[0]->number_order); // Вызываем отправку  почтовых уведомлений
			
						}
					
					$response = buildResponse($params, 'paymentAviso', $_REQUEST['invoiceId'], 0);
					
					sendResponse($response);
					
					

					
					
				}

	
		
	} else {
		
		myRedirect($link, _ttfsp_error_final);
		
		
	}
	
	
	
	
	
}

function successyandexkassa () {
	
	$link = JURI::base() .'index.php?option=com_ttfsp';
	
	$params = getparams();
	
	$number_order = $_REQUEST['orderNumber'];
	
	$session = JFactory::getSession();
			
	$psws_sess = $session->getId();
			
	$linksucces = JURI::base() . 'index.php?option=com_ttfsp&view=successpage&number_order='.$number_order.'&psws_sess='.$psws_sess; // Переход на страницу просмотра заказа
	
	if ($_REQUEST['shopId'] == $params['yandex_kassa_shopid']) {
		
		myRedirect($linksucces, _ttfsp_payment_success);
		
		
		
		
	} else {
		
		myRedirect($link, _ttfsp_error_final);
		
		
		
	}
	
}

function paymentpageopen () {
	
	$params = getparams();
	
	$session = JFactory::getSession();
	
	$database 	= JFactory::getDBO();
			
	$psws_sess_input = $session->getId();
	
	$input = JFactory::getApplication()->input;

	$number_order =  $input->getCmd("number_order");
	
	$psws_sess = $input->getCmd('psws_sess', 0);
	
	$paswd_order = $input->get('paswd_order', null, 'RAW');
	
	$simbols = strlen ($paswd_order);
	
	$componentlink = JURI::root() .'index.php?option=com_ttfsp';
	
	
	
	if (!$number_order) {
		
		myRedirect($componentlink, _ttfsp_error_final, 'error');
		
		
	}
	
	else {
		
		$query = "SELECT * FROM #__ttfsp_dop WHERE number_order='".$number_order."'";
			
		$database->setQuery($query);
			
		$zakaz_row = $database->loadObjectList();
		
	}
	
	/// Заказ отсутствует в базе данных
	
	if (!$zakaz_row) {
		
		myRedirect($componentlink, _ttfsp_error_noorder, 'error');
		
	}
	
	/// Ваш заказ уже оплачен
	
	if ($zakaz_row[0]->payment_status == 1) {
		
		myRedirect($componentlink, _ttfsp_order_lod_paym, 'error');
	}
	
	/// Ваш заказ отменен
	
	if ($zakaz_row[0]->payment_status == 3) {
		
		myRedirect($componentlink, _ttfsp_order_lod_paym_cancel, 'error');
	}
	
	/// Неверный пароль для просмотра заказа
	
	if ($paswd_order != $zakaz_row[0]->order_password && $simbols > 0) {
		
		echo '<script> alert ("'._ttfsp_error_password.'"); </script>';
	}

	
	if ($psws_sess == $psws_sess_input || $paswd_order == $zakaz_row[0]->order_password) {
		
		HTML_ttfsp::paymentpage($zakaz_row[0], $params);

	}
	
	else {
		
		echo '
		<div class="form_paswwd">
		
		<div class="psfd_form_title"><h3>'._ttfsp_pswd_form_title_0.'</h3><span>'._ttfsp_pswd_form_title.'</span></div>
	
			<form action="'.$componentlink.'&view=paymentpageopen&number_order='.$number_order.'" method="post" name="ttfspForm" id="ttfspForm">
		
				<input class="pswd_text_inp" type="text" id="paswd_order" name="paswd_order" value="">
				<input class="pswd_send" type="submit">
		
			</form>
	
		</div>
		
		';
		
	}

	
	
	
	
}

function usercabinet () {
	
	$params = getparams();
	$user = JFactory::getUser();
	$register_link = JURI::root() .'index.php?option=com_users&view=registration';
	
	$input = JFactory::getApplication()->input;
	
	$delete_order_id = (int) $input->get('IdOrdRec', null, 'RAW');
	$delete_order_number = $input->get('NumOrdRec', null, 'RAW');
	
	if ($user->guest) {
		
		jimport( 'joomla.application.module.helper' );
		$module = JModuleHelper::getModule( 'login' );
		
		echo '<div class="messusr_cab">
		<h4 class="mess_h4">'._ttfsp_user_cabinet_auth.'</h4>
		<h5 class="mess_h5">'._ttfsp_user_cabinet_auth_2.'<a href="'.$register_link.'" class="ref_lnk_ttfsp">'._ttfsp_user_cabinet_auth_3.'</a></h5>
		</div>
		';
		
		$attribs  = array();
		
		echo JModuleHelper::renderModule( $module, $attribs );
		
	}
	
	else {
		
		if ($delete_order_id != 0 && $delete_order_number != '') {
			
			delete_order_and_time ($delete_order_id, $delete_order_number);
			
		}
		
		$database 	= JFactory::getDBO();
		
		$query = "SELECT * FROM #__ttfsp_dop WHERE iduser='".$user->id."' AND payment_status <> 3";
			
		$database->setQuery($query);
			
		$zakaz_row = $database->loadObjectList();
		

		
		HTML_ttfsp::viewdetail($zakaz_row, $params);
		
	}
}

function delete_order_and_time ($delete_order_id, $delete_order_number) {
	
		$database 	= JFactory::getDBO();
		
		$textinfo = '';
		
		$peoples = 0;
		
		$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = 3 WHERE `number_order` ='".$delete_order_number."' LIMIT 1" ;
			
		$database->setQuery($query);
		
		$database->query();
		
		
		/// Отправка уведомления о смене статуса заказа

		
		$query = "SELECT * FROM #__ttfsp WHERE id='".$delete_order_id."'";
			
		$database->setQuery($query);
		
		$zapis = $database->loadObjectList();
		
		
		if (!$zapis[0]->plimit || $zapis[0]->plimit == 0) {
			

			
			$query = "SELECT * FROM #__ttfsp_dop WHERE `number_order` ='".$delete_order_number."' LIMIT 1" ;
			
			$database->setQuery($query);
			
			$zakaz_row = $database->loadObjectList();
			
			$zakaz_info = $zakaz_row[0];
			
			
			if ($zakaz_info->payment_status ==  3) {
					
				$query = "UPDATE `#__ttfsp` SET `reception` = 0, `info` = '".$zakaz_info->rphone."' WHERE id='".$delete_order_id."'";
			
				$database->setQuery($query);
		
				$database->query();


			
			} else {
				
				
				$query = "UPDATE `#__ttfsp` SET `reception` = 1, `rmail` = '".$zakaz_info->rmail."', `iduser`='".$zakaz_info->iduser."', `rfio` = '".$zakaz_info->rfio."', `rphone`='".$zakaz_info->rphone."', `info`='".$zakaz_info->info."', WHERE id=".$delete_order_id;
			
				$database->setQuery($query);
		
				$database->query();
				
			}
			
			
		} else {
			
			$query = "SELECT * FROM #__ttfsp_dop WHERE idrec='".$delete_order_id."'";
			
			$database->setQuery($query);
			
			$zakaz_row = $database->loadObjectList();
			
			
			for($s=0;$s<count($zakaz_row);$s++){
				
				$myvalue = $zakaz_row[$s];
				
				
				if ($myvalue->number_order == $delete_order_number) {
					
					$zakaz_info = $myvalue;
					
				}
				
				if ($myvalue->payment_status != 3) {
					
					$textinfo = $textinfo.$myvalue->info.'<br><br>';
					$peoples++;
					
				}
				
						
			}
			
			
			if ( $peoples < $zapis[0]->plimit) {
				
				$query = "UPDATE `#__ttfsp` SET `reception` = 0, `info` = '".$textinfo."' WHERE id='".$delete_order_id."'";
			
				$database->setQuery($query);
		
				$database->query();
				
			} else {
				
				$query = "UPDATE `#__ttfsp` SET `reception` = 1, `info` = '".$textinfo."' WHERE id='".$delete_order_id."'";
			
				$database->setQuery($query);
		
				$database->query();
				
			}
			
		
			
		}
		
		
		

		
		
		$title_mail = _ttfsp_status_order_subject.$zakaz_row[0]->number_order;
			
		final_mail ($zakaz_info, $title_mail);
		
		
		///
		

		
		

		
		
		
		

	
}