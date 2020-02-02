<?php
/**
*Time-Table FS+ - Joomla Component
* @package TT FS+
* @Copyright (C) 2010 FomSoft Plus
* @ All rights reserved
* @ Time-Table FS+ is Commercial Software
**/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
//Error_Reporting(E_ALL & ~E_NOTICE);
if (!defined( 'DS' )) {
	define( 'DS', DIRECTORY_SEPARATOR );
}
if (!defined( 'SITE_NAME' )) {
		define( 'SITE_NAME', JURI::root()); 
}
if (!defined('JVERSION')) {
		define( 'JVERSION', '1.0' ); 
}
if (!defined('INDURL')) {
	if (JVERSION== '1.0'){
		define( 'INDURL', 'index2.php' ); 
	} else {
		define( 'INDURL', 'index.php' ); 
	}
}
if (!isset($option)){
	$option = 'com_ttfsp';
}
if (!isset($task)){
	$task	= JRequest::getCmd( 'task', '' );
}
if (!defined('JPATH_ROOT')) {
global $mosConfig_absolute_path;
	define( 'JPATH_ROOT', $mosConfig_absolute_path); 
}
if (!defined('JPATH_LIVE_SITE')) {
if (JVERSION=='1.0'){
global $mosConfig_live_site;
	define( 'JPATH_LIVE_SITE', $mosConfig_live_site.'/'); 
} else {
	define( 'JPATH_LIVE_SITE', JURI::base()); 
}
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
require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."toolbar.ttfsp.php" );	
		$lng = JFactory::getLanguage()->getTag();
	} else {
		$conf = JFactory::getConfig();
		$lng = $conf->getValue('config.language');
	}
	$lngfile=$lng.'.php';
	if ( file_exists( JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS.$lngfile) ) {
		include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS.$lngfile);
		$langfsp=$lng.'-';
	} else {
		include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."language".DS."ru-RU.php");
		$langfsp='ru-RU-';
	}
}
$document = JFactory::getDocument();
$url = JURI::base() .'components/com_ttfsp/css/style.css';
$document->addStyleSheet($url);
define( 'JPATH_ROOT_SITE', JPATH_ROOT); 
$act = JRequest::getCmd( 'act', 'ttimes' );
//////////////////////////////////////////////
if (JVERSION== '1.0'){
$cid = josGetArrayInts( 'cid' );
mosArrayToInts( $cid );
} else {
$id 		= JRequest::getCmd( 'id', null );
$cid 		= JRequest::getVar( 'cid', array(0), 'post' );
}
require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."admin.ttfsp.html.php" );
require_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."ttfsp.class.php" );
$outxml		= JRequest::getCmd( 'outxml', 0 );
$outcsv		= JRequest::getCmd( 'outcsv', 0 );
$changeprice = JRequest::getCmd( 'changeprice', 0 );
if ($outxml){
	outxmlcsv(0);
	return;
}
if ($outcsv){
	outxmlcsv(1);
	return;
}
if ($changeprice){
	outxmlcsv(2);
	return;
}

/////////////////////////////////////////////////////////////////////
switch ($act) {
//////////////////////////////////////////////////////////////////////
	case 'install':
	ttfsp_inst();
	break;
//////////////////////////////////////////////////////////////////////
	case 'addtimes':
	switch ($task) {
	case 'savetimes':
		saveTimes( $option );
		break;
	default:
		showAddtimes( $option );
		break;
}
break;
//////////////////////////////////////////////////////////////////////
	case 'addtm':
	switch ($task) {
	case 'savetm':
		savetm( $option );
		break;
	default:
		showAddtm( $option );
		break;
}
break;
///////////////////////////////////////////////////////////////////////
case 'elems':
	switch ($task) {
	case 'copyselect':
		copyElemsSelect($cid, $option, $act);
		break;
	case 'saveforms':
		generateforms( $option, $act );
		break;
	case 'edit':
	editElems( intval($cid[0] ), $option, $act );
		break;
	case 'editA':
		editElems( $id, $option, $act );
		break;
	case 'new':
		editElems( 0, $option, $act );
		break;
	case 'remove':
		removesspec( $cid, $option, $act );
		break;
	case 'save':
		saveelem($option, $act , 0);
		break;
	case 'apply':
		saveelem($option, $act , 1);
		break;
	case 'cancel':
		cancelsspec( $option, $act);
		break;
	case 'publish':
		changesspec( $cid, 1, $option, $act );
		break;
	case 'unpublish':
		changesspec( $cid, 0, $option, $act );
		break;
	case 'orderup':
		ordersspec( intval( $cid[0] ), -1, $act );
		break;
	case 'orderdown':
		ordersspec( intval( $cid[0] ), 1, $act );
		break;
	default:
		showElems( $option );
		break;
}
break;

//////////////////////////////////////////////////////////////////////
	case 'config':
	switch ($task) {
	case 'saveconfig':
		saveSettings( $option );
		break;
	case 'cancelconfig':
		cancelSettings( $option );
		break;
	default:
		showConfig( $option );
		break;
}
break;
//////////////////////////////////////////////////////////////////////
	default:
	switch ($task) {
	case 'edit':
		editsspec( intval( $cid[0] ), $option, $act );
		break;
	case 'editA':
		editsspec( $id, $option, $act );
		break;
	case 'new':
		editsspec( 0, $option, $act );
		break;
	case 'remove':
		removesspec( $cid, $option, $act );
		break;
	case 'save':
		savesspec($option, $act, $cid , 0);
		break;
	case 'apply':
		savesspec($option, $act, $cid , 1);
		break;
	case 'cancel':
		cancelsspec( $option, $act);
		break;
	case 'publish':
		changesspec( $cid, 1, $option, $act );
		break;
	case 'unpublish':
		changesspec( $cid, 0, $option, $act );
		break;
	case 'reception':
		changerec( $cid, 1, $option, $act );
		break;
	case 'unreception':
		changerec( $cid, 0, $option, $act );
		break;
	case 'orderup':
		ordersspec( intval( $cid[0] ), -1, $act );
		break;
	case 'orderdown':
		ordersspec( intval( $cid[0] ), 1, $act );
		break;
	default:
		showsspec( $option, $act );
		break;
}
break;
}
class JUtilityFSP {
	public static function sendMail($pr1='', $pr2='', $recipient='', $subject='', $body='', $pr6=''){
		
		if (!$recipient) return; 
	
		
		$mailer = JFactory::getMailer();
		
		$config = JFactory::getConfig();
		$sender = array( 
		$pr1, $pr2
		);
 
		$mailer->setSender($sender);

		$mailer->addRecipient($recipient);
		
		$mailer->setSubject($subject);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($body);
		
		$send = $mailer->Send();
	
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
			
			$summ_oplata = (int) ($params['sposob_oplaty_1_on'] + $params['sposob_oplaty_0_on']);
	
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
				
			
			}	

}
////////////////////////////////////////////////////////////////////////////////////////////////
function ttfsp_inst(){
	$database =  JFactory::getDBO();
	$dbt = '#__components';
	$query = "UPDATE ".$dbt
	. "\n SET name = '" . _ttfsp_lang_47 . "'" 
	. "\n WHERE name='timetable'"
	;
	$database->setQuery( $query );
	if (!$database->query()){
		$dbt = '#__extensions';	
		$query = "UPDATE ".$dbt
		. "\n SET name = '" . _ttfsp_lang_47 . "'" 
		. "\n WHERE name='timetable'"
		;
		$database->setQuery( $query );
		$database->query();
	}
	if (JVERSION== '1.0'){
		mosRedirect('index2.php?option=com_ttfsp&act=config',_ttfsp_lang_160);
	} else {
		$mainframe = JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_ttfsp&act=config', _ttfsp_lang_160 );
	}	
}
////////////////////////////////////////////////////////////////////////////////////////////////
function editElems($id, $option, $act) {
	$database = JFactory::getDBO();
	$lists=array();
	$row = new mosttfsp_el( $database );
	$row->load( (int)$id );
	$table_name ="#__ttfsp_sprspec";
	$query = "SELECT * "
	. "\n FROM  ".$table_name
	. "\n where published=1 ORDER BY ordering"
	;
	$database->setQuery( $query );
	$elements = $database->loadObjectList();
	$sel = '';
	$htmlel =	'
	<select id="asspec" name="asspec[]"  multiple="multiple" style="width:320px;height:150px;">'; 
		if ( strpos( ' '.$row->idsspec, ',0,' ) || $row->idsspec == '')
		$sel = ' selected="selected" ';		
	$htmlel .= '
	<option '.$sel.' value="0">'._ttfsp_lang_162.'</option>';
	for($i=0;$i<count($elements);$i++){
		$sel = '';
		$label = htmlspecialchars($elements[$i]->name, ENT_QUOTES);
		$myvalue= $elements[$i]->id;
		if ( strpos( ' '.$row->idsspec, ','.$myvalue.',' ))
		$sel = ' selected="selected" ';	
		$htmlel .= '<option '.$sel.' value="'.$myvalue.'" >'.$label.'</option>';
	}	
	$htmlel .= '
				</select>';	
  	$elemtype[] 		= JHTML::_('select.option','0', _ttfsp_lang_104);
 	$elemtype[] 		= JHTML::_('select.option','1', _ttfsp_lang_105);
  	$elemtype[] 		= JHTML::_('select.option','2', _ttfsp_lang_106);
  	$elemtype[] 		= JHTML::_('select.option','5', _ttfsp_lang_182);
 	$elemtype[] 		= JHTML::_('select.option','6', _ttfsp_lang_183);
   	$elemtype[] 		= JHTML::_('select.option','3', _ttfsp_lang_107);
  	$elemtype[] 		= JHTML::_('select.option','4', _ttfsp_lang_108);
	
	$lists['selspec']	= _ttfsp_lang_161;
	if ($row->fname=='fio' || $row->fname=='phone') {
		$htmlel = '';
		$lists['selspec']	= '';	
		$lists['eltype']	= _ttfsp_lang_104;
		$lists['published'] = _ttfsp_lang_12;
		$lists['required'] 	= _ttfsp_lang_12;
		$lists['readonly'] 	= _ttfsp_lang_11;
	} else {
		$lists['eltype']	= JHTML::_('select.genericlist',$elemtype, 'type', 'class="inputbox" size="1" ', 'value', 'text', $row->type);
		$lists['published'] 	= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published );
	$lists['required'] 	= JHTML::_('select.booleanlist', 'required', 'class="inputbox"', $row->required );
	$lists['readonly'] 	= JHTML::_('select.booleanlist', 'readonly', 'class="inputbox"', $row->readonly );
	$lists['multisel'] 	= JHTML::_('select.booleanlist', 'multisel', 'class="inputbox"', $row->multisel );
	}
	HTML_ttfsp::editelem( $row, $option, $lists, $act, $htmlel);
}
//////////////////////////////////////////////////////////////////////
function saveelem($option, $act, $apply=0){
	$database =  JFactory::getDBO();
	$row = new mosttfsp_el( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$asspec 	= JRequest::getVar( 'asspec', array(0), 'post' );
	if (count($asspec))
		$row->idsspec = ','.implode( ',', $asspec ).',';
	else 
		$row->idsspec = '';
		
	$row->title = str_replace("\'","'",$row->title);
	$row->value = str_replace("\'","'",$row->value);
	$row->title = str_replace('\"','"',$row->title);
	$row->value = str_replace('\"','"',$row->value);
	if ($row->createdate=='0000-00-00 00:00:00' || !$row->createdate){
 		$row->createdate = date( 'Y-m-d H:i:s', time());
	}
	if (!$row->ordering){
		$row->ordering=9999999;
	}
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->UpdateOrder();
	if ($apply){
    myRedirect( INDURL."?option=$option&act=$act&task=editA&id=$row->id&hidemainmenu=1");
	} else {
    myRedirect( INDURL."?option=$option&act=$act" ,_ttfsp_lang_25);
	}
}

/////////////////////////////////////////////////////////////////////
function copyElemsSelect( $cid, $option, $act )
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	JArrayHelper::toInteger($cid);
	if (count( $cid ) < 1) {
		JError::raiseError(500, JText::_( 'Select an item to move', true ));
	}
	$cids = implode( ',', $cid );
	$query = 'SELECT * '
	. ' FROM #__ttfsp_el AS el'
	. ' WHERE el.id IN ( '.$cids.' )'
	;
	$database->setQuery( $query );
	$items = $database->loadObjectList();
	for( $i=0; $i < count($items); $i++ ) {
		$row = new mosttfsp_el( $database );
		$row->name		 = 'Copy '.$items[$i]->name;
		$row->title		 = $items[$i]->title;
 		$row->createdate 	= date( 'Y-m-d H:i:s', time());
		$row->published	 = 0;
		$row->ordering	=9999999;
		$row->type		= $items[$i]->type;
		$row->value		= $items[$i]->value;
		$row->required	= $items[$i]->required;
		$row->readonly	= $items[$i]->readonly;
		$row->maxlength	= $items[$i]->maxlength;
		$row->size		= $items[$i]->size;
		$row->mask		= $items[$i]->mask;
		$row->css		= $items[$i]->css;
		$row->multisel	= $items[$i]->multisel;		
		if (!$row->check()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$row->checkin();
		$row->UpdateOrder();		
	}
    myRedirect( INDURL."?option=$option&act=$act" ,_ttfsp_lang_25);
}
/////////////////////////////////////////////////////////////////////
function showElems( $option ) {
	$database =  JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	global  $mosConfig_list_limit;
	
	$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$search =  trim( $search  ) ;
	if ($search) {
		$where[] = "  name LIKE '%$search%' ";
	}
	if ( isset( $where ) ) {
		$where = "\n WHERE ". implode( ' AND ', $where );
	} else {
		$where = '';
	}
	$database->setQuery( "SELECT COUNT(*) FROM #__ttfsp_el  $where" );
	$total = $database->loadResult();
	if (!$where && !$total){
	$database->setQuery("INSERT INTO `#__ttfsp_el` (`fname`,`ordering`, `title`, `name`,`required`,`published`,`maxlength` ) VALUES ('fio','1','"._ttfsp_lang_25."','"._ttfsp_lang_25."','1','1','50');");
	$database->query();
	$database->setQuery("INSERT INTO `#__ttfsp_el` (`fname`,`ordering`, `title`, `name`,`required`,`published`,`maxlength`,`mask` ) VALUES ('phone','2','"._ttfsp_lang_64."','"._ttfsp_lang_64."','1','1','20','1234567890-');");
	$database->query();
		$total = 2;
	}
if (JVERSION=='1.0'){
	require_once( JPATH_ROOT . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit  );
} else {
	jimport('joomla.html.pagination');
	$pageNav = new JPagination( $total, $limitstart, $limit );
}
	$query = "SELECT * "
	. "\n FROM #__ttfsp_el "
	. "\n $where ORDER BY ordering"
	;
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	HTML_ttfsp::showelems( $rows, $pageNav, $search, $option );
}

// =============================================================================================
function showAddtm( $option ){
include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."addtm.php");
$tm = new addtm_ttfsp;
$tm->showAddtm( $option );
}
// ==================================================Сохранение времени приема по графику времени
function savetm( $option ){
include_once(JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."addtm.php");
$tm = new addtm_ttfsp;
$tm->savetm( $option );
myRedirect( INDURL."?option=$option&act=addtm", _ttfsp_lang_72);	
}
// =============================================================================================
function showAddtimes( $option ){
	$idspec =  JRequest::getVar(  'idspec', 0 );
	$database = JFactory::getDBO();
	$database->setQuery( "SELECT * FROM #__ttfsp_addtime " );
	$rows = $database->loadObjectList();
	if (!count($rows)){
	$database->setQuery("INSERT INTO `#__ttfsp_addtime` (`idspec` ) VALUES ('0');");
	$database->query();
	$database->setQuery( "SELECT * FROM #__ttfsp_addtime " );
	$rows = $database->loadObjectList();
	}
	$row = $rows[0];
	$database->setQuery( "SELECT id, name FROM #__ttfsp_spec WHERE published=1" );
	$rowspec = $database->loadObjectList();
	$spec = array();
	$spec[0] = '';   
	$spec = array_merge( $spec, $rowspec);
	$lists['published'] 	= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published );
	$lists['spec']	 = JHTML::_('select.genericlist', $spec, 'addspec', 'size="1" class="inputbox"', 'id', 'name',$row->addspec);
	HTML_ttfsp::addtimes($row, $option, $lists );
}
// =============================================================================================
function deltime(){
	$params = getparams();
	if ($params['del_hist']){
		$database =  JFactory::getDBO();
		$deltime = time()-($params['del_hist']*86400);
		$database->setQuery( "SELECT id FROM #__ttfsp WHERE ttime>0 AND ttime< ".$deltime );
		$rowdel = $database->loadResultArray();
		if (count($rowdel)){
			$dl = 'id=' . implode( ' OR id=', $rowdel );
			$dl_dop = 'idrec=' . implode( ' OR idrec=', $rowdel );	
		//	$query = "DELETE FROM #__ttfsp_dop "
		//	. "\n WHERE ( $dl_dop )"
		//	;
		//	$database->setQuery( $query );
		//	$database->query();		
			$query = "DELETE FROM #__ttfsp "
			. "\n WHERE ( $dl )"
			;
			$database->setQuery( $query );
			$database->query();		
		}
	}
}
// =============================================================================================
function savetimes( $option ){
	$plimit =  JRequest::getVar(  'plimit', 0 );
	$plimit = $plimit==1 ? 0 : $plimit;
	$database =  JFactory::getDBO();
	deltime();
	$row = new mosttfsp_addtime( $database );
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store($row->id)) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$pricezap = $row->addspec;
	
addtime($row->addhr1, $row->addmn1, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr2, $row->addmn2, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr3, $row->addmn3, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr4, $row->addmn4, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr5, $row->addmn5, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr6, $row->addmn6, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr7, $row->addmn7, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr8, $row->addmn8, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr9, $row->addmn9, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr10, $row->addmn10, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr11, $row->addmn11, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr12, $row->addmn12, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr13, $row->addmn13, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr14, $row->addmn14, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr15, $row->addmn15, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr16, $row->addmn16, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr17, $row->addmn17, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr18, $row->addmn18, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr19, $row->addmn19, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr20, $row->addmn20, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr21, $row->addmn21, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr22, $row->addmn22, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr23, $row->addmn23, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr24, $row->addmn24, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr25, $row->addmn25, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr26, $row->addmn26, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr27, $row->addmn27, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
addtime($row->addhr28, $row->addmn28, $row->adddate, $row->addspec, $row->published, $plimit, $pricezap);
myRedirect( INDURL."?option=$option&act=addtimes", _ttfsp_lang_72);
}
// ============================================================================================
function addtime($rowhr, $rowmn, $rowdate, $rowspec, $rowpubl,  $plimit, $pricezap){
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
	$database =  JFactory::getDBO();
	$database->setQuery( "SELECT COUNT(*) FROM #__ttfsp ".$where);
	$total = $database->loadResult();
	if ($total) return;
	$pricezap = $rowspec;
	$database->setQuery("INSERT INTO `#__ttfsp`  (`idspec` ,`published` ,`dttime` ,`hrtime`,`mntime`, `plimit`, `ttime`, `pricezap`) VALUES ('".$rowspec."','".$rowpubl."','".$rowdate."','".$rowhr."','".$rowmn."','".$plimit."','".$ttime."','".$pricezap."');");
	$database->query();
}
// =============================================================================================
function AlertMsg( $text, $act=0){
	$action=$act ? '' : 'window.history.go(-1);';
	echo "<script>alert('$text'); $action</script> \n";
	echo '<noscript>';
	echo "$text\n";
	echo '</noscript>';
}
//==============================================================================================
function showsspec( $option, $act) { 			// Просмотр списком
	
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	global  $mosConfig_list_limit;
	
	
	
	$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

	$limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
	
	$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$num_order = $mainframe->getUserStateFromRequest( "num_order{$option}", 'num_order', '' );
	$searchd = $mainframe->getUserStateFromRequest( "searchd{$option}", 'searchd', '' );
	$searcht = $mainframe->getUserStateFromRequest( "searcht{$option}", 'searcht', '' );
	$sztime = (int) $mainframe->getUserStateFromRequest( "sztime{$option}", 'sztime', '' );
	$payment_st = $mainframe->getUserStateFromRequest( "payment_status{$option}", 'payment_status', '' );
	$hours_value = $mainframe->getUserStateFromRequest( "hours_select{$option}", 'hours_select', 777 );
	$minutes_value = $mainframe->getUserStateFromRequest( "minutes_select{$option}", 'minutes_select', 777 );
	
	
	$search =  trim( $search  ) ;
	
	/// Фильтр имени специалиста
	
	if ($search) {
		
		switch ($act) {
			case 'ttimes':
			
			$where[] = "  spec.name LIKE '%$search%' ";
			break;
			
			case 'torders': 
			
			$where[] = "  specialist_name LIKE '%$search%' ";
			break;
			
			default: 
			$where[] = "  gtt.name LIKE '%$search%' ";
			
		}
		
	}
	
	if ($num_order != '' || $num_order ) {
		
		switch ($act) {
		case 'torders': 
		$where[] = " number_order LIKE '%$num_order%' ";
		break;
		
		}
		
	}
	
	if ((int) $payment_st != 4) {
		
		
		switch ($act) {
						
			case 'torders': 
			
			$where[] = "  payment_status LIKE '%$payment_st%' ";
			break;
			
			
		}
		
	}
	
	if ((int) $hours_value != 777) {
		
		
		switch ($act) {
						
			case 'torders': 
			
			$where[] = "  hours LIKE  '%$hours_value%' ";
			break;
			
			
		}
		
	}
	
	if ((int) $minutes_value != 777) {
		
		
		switch ($act) {
						
			case 'torders': 
			
			$where[] = "  minutes LIKE  '%$minutes_value%' ";
			break;
			
			
		}
		
	}
	
	//////////////
	
	if ($sztime > 0 && $act=='ttimes') {
		
		$szt = 	$sztime-1;
		$where[] = "  gtt.reception = ".$szt;
		
	}
		
	
	
	if ($searcht && $act=='ttimes') {
		
			$where[] = "  gtt.dttime >= '$searcht' ";
	}
	
	if ($searcht && $act=='torders') {
		
			$where[] = "  date >= '$searcht' ";
	}

	
	
	if ($searchd && $act=='ttimes') {
			
			$where[] = "  gtt.dttime <= '$searchd' ";
			
	}
	
	if ($searchd && $act=='torders') {
			
			$where[] = "  date <= '$searchd' ";
			
	}
	
	
	
	if ( isset( $where ) ) {
		
		$where = "\n WHERE ". implode( ' AND ', $where );
		
	} else {
		
		$where = '';
		
	}
	
	
	switch ($act) {
	case 'proftime':
	
		$dbt = '#__ttfsp_sprtime';
		break;	
		
	case 'sspec':
	
		$dbt = '#__ttfsp_sprspec';
		break;
		
	case 'ssect':
	
		$dbt = '#__ttfsp_sprsect';
		break;
		
	case 'tspec':
	
		$dbt = '#__ttfsp_spec';
		break;
		
	case 'torders':
	
		$dbt = '#__ttfsp_dop';
		break;
		
	default:
		$dbt = '#__ttfsp';
		break;
	}
	
	
	
	switch ($act) {
		case 'ttimes':
		
		$database->setQuery( "SELECT COUNT(*) FROM $dbt AS gtt  LEFT JOIN #__users AS usr ON usr.id = gtt.iduser LEFT JOIN #__ttfsp_spec AS spec ON spec.id = gtt.idspec $where" );
		$total = $database->loadResult();
	
		jimport('joomla.html.pagination');
		$pageNav 	= new JPagination($total, $limitstart, $limit);

	
	
	
		$query = "SELECT gtt.*,spec.name AS name, usr.fio, usr.phone "
			. "\n FROM $dbt AS gtt"
			. "\n LEFT JOIN #__users AS usr ON usr.id = gtt.iduser" 
			. "\n LEFT JOIN #__ttfsp_spec AS spec ON spec.id = gtt.idspec" 
			. "\n $where ORDER BY dttime,hrtime,mntime,idspec ASC"
			;
		break;
		
		case 'torders':
		
			$database->setQuery( "SELECT COUNT(*) FROM $dbt $where" );
			$total = $database->loadResult();
	
			jimport('joomla.html.pagination');
			$pageNav 	= new JPagination($total, $limitstart, $limit);

	
	
	
		$query = "SELECT * "
			. "\n FROM $dbt AS gtt"
			. "\n $where ORDER BY id DESC"
			;
			
		break;

		
		
		
	 default:
		
		$database->setQuery( "SELECT COUNT(*) FROM $dbt AS gtt $where" );
		$total = $database->loadResult();
		
			
		jimport('joomla.html.pagination');
		$pageNav 	= new JPagination($total, $limitstart, $limit);

	
		$query = "SELECT * "
			. "\n FROM $dbt AS gtt"
			. "\n $where ORDER BY ordering ASC"
			;
	}
	
	
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();
	
	switch ($act) {
	case 'proftime':	
	case 'sspec':
	case 'ssect':
		HTML_ttfsp::showsprspec( $rows, $pageNav, $search, $option, $act );
		break;
	case 'tspec':
		HTML_ttfsp::showspec( $rows, $pageNav, $search, $option, $act );
		break;
	case 'torders':
	
		$database->setQuery( "SELECT id, name FROM #__ttfsp_spec WHERE published=1" );
		$rowspec = $database->loadObjectList();
		
		$lists = array();
		$payment_status = array();
		
		$payment_status[] = JHTML::_('select.option','4', _ttfsp_payment_status_all);
		$payment_status[] = JHTML::_('select.option','0', _ttfsp_payment_status_0);
		$payment_status[] = JHTML::_('select.option','1', _ttfsp_payment_status_1);
		$payment_status[] = JHTML::_('select.option','2', _ttfsp_payment_status_2);
		$payment_status[] = JHTML::_('select.option','3', _ttfsp_payment_status_3);
		
	
		$database->setQuery( "SELECT DISTINCT hours FROM #__ttfsp_dop WHERE hours  <> '' ORDER BY hours ASC" );
		$hours_basa = $database->loadColumn();
		
		$database->setQuery( "SELECT DISTINCT minutes FROM #__ttfsp_dop WHERE minutes <> '' ORDER BY minutes ASC" );
		$minutes_basa = $database->loadColumn();
		
		
		
		// Часы
		
		// $hours_list = min_and_hours_for_orders(24);
		
		
		
		$hours_list = min_and_hours_for_orders_basa($hours_basa);

		// Минуты
		
		$minutes_list = min_and_hours_for_orders(60);
		
		$minutes_list = min_and_hours_for_orders_basa($minutes_basa);
		
		
		if (!$payment_st && $payment_st != 0) {
			
			$payment_st = 4;
		}
		$sprspec = array();
		$sprspec[0] = '';   
		$sprspec = array_merge( $sprspec, $rowspec);
		
		
		$lists['order_status_adminlist'] = JHTML::_('select.genericlist',$payment_status, 'payment_status', 'class="inputbox" size="1"', 'value', 'text', $payment_st);
		
		$lists['hours_oders']	 = JHTML::_('select.genericlist', $hours_list, 'hours_select', 'size="1" class="inputbox"', 'value', 'text',$hours_value);
		
		$lists['minutes_oders']	 = JHTML::_('select.genericlist', $minutes_list, 'minutes_select', 'size="1" class="inputbox"', 'value', 'text',$minutes_value);
		
		$lists['sprspec']	 = JHTML::_('select.genericlist', $sprspec, 'search', 'size="1" class="inputbox"', 'name', 'name',$search);
		
	
		HTML_ttfsp::orders_list( $rows, $pageNav, $lists, $option, $act, $searchd, $searcht, $num_order );
		break;
	default:
		$database->setQuery( "SELECT id, name FROM #__ttfsp_spec WHERE published=1" );
		$rowspec = $database->loadObjectList();
		$lists = array();
		$asztime = array();
		$asztime[] = JHTML::_('select.option','0', _ttfsp_lang_130);
		$asztime[] = JHTML::_('select.option','1', _ttfsp_lang_43);
		$asztime[] = JHTML::_('select.option','2', _ttfsp_lang_42);
		$sprspec = array();
		$sprspec[0] = '';   
		$sprspec = array_merge( $sprspec, $rowspec);
		$lists['sprspec']	 = JHTML::_('select.genericlist', $sprspec, 'search', 'size="1" class="inputbox"', 'name', 'name',$search);
		$lists['sztime']	 = JHTML::_('select.genericlist', $asztime, 'sztime', 'size="1" class="inputbox"', 'value', 'text',$sztime);
		HTML_ttfsp::showtime( $rows, $pageNav, $lists, $option, $act, $searchd, $searcht );
		break;
	}
}
//===================================================== Функция часы и минуты для списков

function min_and_hours_for_orders ($hours) {
		
			$massive = array();
	
			while ($hours >= 0) :
		
			$x_title =  $hours;
		
			if ($hours < 10) {
				$x_title = '0'.$hours;
				
			}
			
			$massive[] = JHTML::_('select.option',$x_title, $x_title);
			
			$hours = $hours-1;
			
			endwhile;
			
			$massive[] =  JHTML::_('select.option','777', _ttfsp_lang_130);
			
			return $massive;
	
}
function min_and_hours_for_orders_basa ($data) {
		
			
	
			foreach($data as $key) {

			$x_title =  $key;
			
			$massive[] = JHTML::_('select.option',$x_title, $x_title);
			
			
			}
			
			$massive[] =  JHTML::_('select.option','777', _ttfsp_lang_130);
			
			return $massive;
	
}


//=============================================================================================
function outxmlcsv($typefile){
	$database =  JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
	$searchd = $mainframe->getUserStateFromRequest( "searchd{$option}", 'searchd', '' );
	$searcht = $mainframe->getUserStateFromRequest( "searcht{$option}", 'searcht', '' );
	$sztime = (int) $mainframe->getUserStateFromRequest( "sztime{$option}", 'sztime', '' );
	
	$input = JFactory::getApplication()->input;
	
	
	$summ = $input->getCmd('summvalue', 0);

	$search =  trim( $search  ) ;
	$params = getparams();
	$decode_in = $params['decode_in'];
	$decode = $decode_in==2 ? 1: 0;
	
	
		
		if ($search) {
			$where[] = "  spec.name LIKE '%$search%' ";
		}
		if ($sztime > 0 ) {
			$szt = 	$sztime-1;
			$where[] = "  gtt.reception = ".$szt;
		}	
		if ($searcht ) {
			$where[] = "  gtt.dttime >= '$searcht' ";
		}

		if ($searchd ) {
			$where[] = "  gtt.dttime <= '$searchd' ";
		}
		if ( isset( $where ) ) {
			$where = "\n WHERE ". implode( ' AND ', $where );
		} else {
			$where = '';
		}
	
	
	$dbt = '#__ttfsp';
	
	
	$query = "SELECT gtt.*,spec.name AS name, usr.fio, usr.phone "
		. "\n FROM $dbt AS gtt"
		. "\n LEFT JOIN #__users AS usr ON usr.id = gtt.iduser" 
		. "\n LEFT JOIN #__ttfsp_spec AS spec ON spec.id = gtt.idspec" 
		. "\n $where ORDER BY dttime,hrtime,mntime,idspec ASC"
		;
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	$row = $rows[0];
	
	/// Изменение цены
	
	if ($typefile == 2) {
		$where = '';
		
		if (!count($rows)){
			echo '<script>alert("'._ttfsp_lang_131.'");window.history.go(-1);</script>';
		}
		if (count($rows) && $search != '0'){
			$where[] = " idspec = $row->idspec";
		} 


		if ($sztime > 0 ) {
			$szt = 	$sztime-1;
			$where[] = "  reception = ".$szt;
		}	
		if ($searcht ) {
			$where[] = "  dttime >= '$searcht' ";
		}

		if ($searchd ) {
			$where[] = "  dttime <= '$searchd' ";
		}
		if ( $where != '') {
			$where = "\n WHERE ". implode( ' AND ', $where );
		} else {
			$where = '';
		}

		
		$query = "UPDATE #__ttfsp SET pricezap = $summ  $where";
	
		$database->setQuery( $query );
		$database->query();	
		
		$messgoreturn = _ttfsp_lang_price_change_messga.'<br>'._ttfsp_lang_price_change_messga_kolv.count($rows)._ttfsp_lang_price_change_messga_zapisam;
		
		echo '<script>alert("'.$messgoreturn.'");window.history.go(-1);</script>';
		myRedirect( INDURL."?option=com_ttfsp&act=ttimes" , $messgoreturn);
	
	
	}
	
	////
	
	if ($typefile !=2) {
	
	if (!count($rows)){
		echo '<script>alert("'._ttfsp_lang_131.'");window.history.go(-1);</script>';
	} else {
	if ($typefile==0){
		$fname = 'ttfsp.xml';
		$outcontent = '<?xml version="1.0" encoding="utf-8"?>'.chr(13);
		for ($i=0; $i<count($rows);$i++){
			$row = $rows[$i];
			$row->info = str_replace('<br />',' ',$row->info);
			$n++;
			$outcontent .= '<record'.$n.'>'.chr(13);
			$outcontent .= '<date>'.$row->dttime.'</date>'.chr(13);	
			$outcontent .= '<hour>'.$row->hrtime.'</hour>'.chr(13);		
			$outcontent .= '<minute>'.$row->mntime.'</minute>'.chr(13);		
			$outcontent .= '<spec>'.$row->name.'</spec>'.chr(13);		
			$outcontent .= '<fio>'.$row->rfio.'</fio>'.chr(13);		
			$outcontent .= '<phone>'.$row->rphone.'</phone>'.chr(13);		
			$outcontent .= '<info>'.addcdata(strip_tags($row->info)).'</info>'.chr(13);		
			$outcontent .= '</record'.$n.'>'.chr(13);			
		}
		header("Cache-Control: ");
		header("Pragma: ");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$fname);
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.strlen($outcontent));
	} else {
		$fname = 'ttfsp.csv';
		$outcontent = '';
			$outcontent .= 'date;';	
			$outcontent .= 'hour;';		
			$outcontent .= 'minute;';		
			$outcontent .= 'spec;';		
			$outcontent .= 'fio;';		
			$outcontent .= 'phone;';		
			$outcontent .= 'info'.chr(13);				
		for ($i=0; $i<count($rows);$i++){
			$row = $rows[$i];
			$row->info = str_replace('<br />',' ',$row->info);
			$outcontent .= $row->dttime.';';	
			$outcontent .= $row->hrtime.';';		
			$outcontent .= $row->mntime.';';		
			$outcontent .= cp1251_utf8($row->name,$decode).';';		
			$outcontent .= cp1251_utf8($row->rfio,$decode).';';		
			$outcontent .= $row->rphone.';';		
			$outcontent .= cp1251_utf8(strip_tags($row->info),$decode).chr(13);		
		}
		header("Cache-Control: ");
		header("Pragma: ");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$fname);
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.strlen($outcontent));		
	}
		echo $outcontent;	
	}
	}
}
//=============================================================================================
function addcdata($val){
if ($val) $val='<![CDATA['.$val.']]>';
return $val;
}
//========================================================================================
function cancelsspec( $option, $act) {		// Отказ от сохранения записи
    myRedirect( INDURL."?option=$option&act=".$act );
}
//
	function reformatFilesArray($name, $type, $tmp_name, $error, $size)
	{
			jimport('joomla.filesystem.file');
			$name = JFile::makeSafe($name);
		return array(
			'name'		=> $name,
			'type'		=> $type,
			'tmp_name'	=> $tmp_name,
			'error'		=> $error,
			'size'		=> $size
		);
	}


//========================================================================================
function savesspec( $option, $act, $cid, $apply=0) { 			// Сохранение записи
	$database =  JFactory::getDBO();
	
	switch ($act) {
	case 'torders': // Сохранение заказа
	
		$row = new mosttfsp_order( $database );
		
		$input = JFactory::getApplication()->input;
		$mailto_order =  $input->getCmd("sendto_mail");
		$id_zakaz =  $input->getCmd("id");
	
		break;
	case 'proftime':
		$row = new mosttfsp_stime( $database );
		break;
	case 'sspec':
	case 'ssect':
	case 'tspec':
		$old_avatar 	= JRequest::getVar( 'old_avatar', array(0), 'post' );
		$rowphoto 	= JRequest::getVar( 'rowphoto', '', 'post' );		
		if ($act=='sspec'){
		$row = new mosttfsp_sspec( $database );
		} else {
		if ($act=='ssect'){
			$row = new mosttfsp_ssect( $database );
			} else {
				$row = new mosttfsp_tspec( $database );
			}
		}
		if ($rowphoto){
			$aphoto = explode(';', $rowphoto);
			for ($a=0; $a<count($aphoto); $a++){
				if (!in_array($aphoto[$a], $old_avatar)){
						@unlink( JPATH_ROOT.DS.$aphoto[$a]);
				}
			}
		}
		$files	= JRequest::getVar('avatar', '', 'files', 'array');	
		if (count($old_avatar) && $old_avatar)	
			$row->photo = implode(';', $old_avatar);
		$row->photo = $row->photo=='0' ? '':$row->photo;			
		if (count($files)){
			$files = array_map( 'reformatFilesArray',
			(array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['error'], (array) $files['size']);
			$params = getparams();
			$ext_images=explode(",","png,gif,jpg,jpeg"); 			
			$mainframe = JFactory::getApplication();
			$fdir =  trim($params['avatarspath']);
			foreach ($files as &$file){
				$ext = strtolower(substr($file['name'], 1 + strrpos($file['name'], ".")));				
				if (in_array($ext,$ext_images)) {
					$newfilename = mt_rand(100,1000) + time().'.'.$ext;
					$newfile=JPATH_ROOT.DS.$fdir.DS.$newfilename;
					$filename = $file['tmp_name'];
					if (is_uploaded_file($filename)) {
						@move_uploaded_file($filename, $newfile);
						if (loadimg($newfilename,$params)){
							$row->photo .=	$row->photo ? ';'.$fdir.'/'.$newfilename:$fdir.'/'.$newfilename;
						}
					}
				}		

			}
		}
		break;
	default:
		$row = new mosttfsp( $database );
		break;
	}
	if (!$row->bind( $_POST )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if ($act=='tspec'){
		$idsprspec 	= JRequest::getVar( 'idsprspec', array(0), 'post' );
		if (count($idsprspec))
			$row->idsprspec = ','.implode( ',', $idsprspec ).',';
		else 
			$row->idsprspec = '';
	}
	if ($act=='ttimes'){
		$row->info = str_replace('<span>',' ',$row->info);
		$row->info = str_replace('</span>',' ',$row->info);
	}
$errmsg = ' ';	
	if ($act=='proftime'){
		$timehm = str_replace("\r","",$row->timehm);
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
		$row->timehm = implode(chr(13), $new_timehm);
		}
	}	
	
	if (isset($row->dttime))
		$row->ttime = strtotime($row->dttime);	
	if (!$row->check()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->checkin();
	$row->updateOrder();
	
	if ($act == 'torders') {
		
		$database->setQuery( "SELECT * FROM #__ttfsp_dop WHERE id = ".$id_zakaz );
		$zakaz_row = $database->loadObjectList();
		
		
		$query = "SELECT * FROM #__ttfsp WHERE id='".$zakaz_row[0]->idrec."'";
			
		$database->setQuery($query);
			
		$zapis_na_priem = $database->loadObjectList();
		

		ifpayment_cancel ($zapis_na_priem[0], $zakaz_row[0], 0);
		

		
		$title_mail = _ttfsp_status_order_subject.$zakaz_row[0]->number_order;
		
		if ($mailto_order == 1) {
			
			final_mail ($zakaz_row[0], $title_mail.$row->payment_status);
		 }

		
	}
	
	if ($act=='ttimes'){
		if (count($cid)){	
			$dbt_dop = '#__ttfsp_dop';
			$cids_dop = 'id=' . implode( ' OR id=', $cid );
			$query = "DELETE FROM $dbt_dop"
			. "\n WHERE ( $cids_dop )"
			;
			$database->setQuery( $query );
			$database->query();	
		}	
	}
	if ($apply)
    myRedirect( INDURL."?option=$option&act=$act&task=editA&id=$row->id&hidemainmenu=1",_ttfsp_lang_164.$errmsg);
	 else 
    myRedirect( INDURL."?option=$option&act=$act",_ttfsp_lang_164.$errmsg);
}
//=================================================================================================
function changerec( $cid=null, $state=0, $option, $act ) {	// Прием
	$database =  JFactory::getDBO();
	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $reception ? 'reception' : 'unreception';
		echo "<script> alert('Select for $action'); window.history.go(-1);</script>\n";
		exit();
	}
	$cids = 'id=' . implode( ' OR id=', $cid );
		$dbt = '#__ttfsp';
		$row = new mosttfsp( $database );
		
	$query = "UPDATE $dbt"
	. "\n SET reception = " . (int) $state
	. "\n WHERE ( $cids )"
	;
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row->checkin( intval( $cid[0] ) );
	}
    myRedirect( INDURL."?option=$option&act=".$act );
}
//=================================================================================================
function changesspec( $cid=null, $state=0, $option, $act ) {	// Публикация записей
	$database =  JFactory::getDBO();
	if (!is_array( $cid ) || count( $cid ) < 1) {
		$action = $state ? 'publish' : 'unpublish';
		echo "<script> alert('Select for $action'); window.history.go(-1);</script>\n";
		exit();
	}
	$cids = 'id=' . implode( ' OR id=', $cid );
	switch ($act) {
	case 'elems':
		$dbt = '#__ttfsp_el';
		$row = new mosttfsp_el( $database );
		break;
	case 'proftime':
		$dbt = '#__ttfsp_sprtime';
		$row = new mosttfsp_stime( $database );
		break;
	case 'sspec':
		$dbt = '#__ttfsp_sprspec';
		$row = new mosttfsp_sspec( $database );
		break;
	case 'ssect':
		$dbt = '#__ttfsp_sprsect';
		$row = new mosttfsp_ssect( $database );
		break;
	case 'tspec':
		$dbt = '#__ttfsp_spec';
		$row = new mosttfsp_tspec( $database );
		break;
	default:
		$dbt = '#__ttfsp';
		$row = new mosttfsp( $database );
		break;
	}
	if ($act=='elems' && $state==0){
	$query = "UPDATE $dbt"
	. "\n SET published = " . (int) $state
	. "\n WHERE ( $cids ) AND fname<>'fio' AND fname<>'phone' "
	;	 
	} else {
	$query = "UPDATE $dbt"
	. "\n SET published = " . (int) $state
	. "\n WHERE ( $cids )"
	;
	}
	$database->setQuery( $query );
	if (!$database->query()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if (count( $cid ) == 1) {
		$row->checkin( intval( $cid[0] ) );
	}
    myRedirect( INDURL."?option=$option&act=".$act );
}
//==================================================================================================
function removesspec( &$cid, $option, $act ) { 	// Удаление записи
	$database =  JFactory::getDBO();
	if (count( $cid )) {
		$cids = 'id=' . implode( ' OR id=', $cid );
	switch ($act) {
	case 'elems':
		$dbt = '#__ttfsp_el';
		break;
	case 'proftime':
		$dbt = '#__ttfsp_sprtime';
		break;
	case 'sspec':
		$dbt = '#__ttfsp_sprspec';
		break;
	case 'ssect':
		$dbt = '#__ttfsp_sprsect';
		break;
	case 'tspec':
		$dbt = '#__ttfsp_spec';
		break;
	case 'torders':
	
		$dbt = '#__ttfsp_dop';
		
		
		$query = "SELECT DISTINCT idrec FROM #__ttfsp_dop WHERE ( $cids ) ";
			
		$database->setQuery($query);
			
		$idrecs = $database->loadColumn();
		
		break;
	default:
		$dbt = '#__ttfsp';
		if (count($cid)){
		$dbt_dop = '#__ttfsp_dop';		
		$cids_dop = 'idrec=' . implode( ' OR idrec=', $cid );
	//	$query = "DELETE FROM $dbt_dop"
	//	. "\n WHERE ( $cids_dop )"
	//	;
	//	$database->setQuery( $query );
	//	$database->query();	
		}	
		break;
	}
	if ($act == 'elems'){
		$query = "DELETE FROM $dbt"
		. "\n WHERE ( $cids ) AND fname<>'fio' AND fname<>'phone' "
		;
	} else {
		$query = "DELETE FROM $dbt"
		. "\n WHERE ( $cids )"
		;
	}	
	
		$database->setQuery( $query );
	

		if ($database->query()) {
			
			if ($act == 'torders')	 {
		
				delete_orders ($idrecs);
		
			}
			
			
		} else {
			
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			
		}
	}
    myRedirect( INDURL."?option=$option&act=".$act );
}
//=====================================================================================================
function ordersspec( $uid, $inc, $act ) { 		// Сортировка записей
	$database =  JFactory::getDBO();
	switch ($act) {
	case 'elems':
		$row = new mosttfsp_el( $database );
		break;
	case 'proftime':
		$row = new mosttfsp_stime( $database );
		break;
	case 'sspec':
		$row = new mosttfsp_sspec( $database );
		break;
	case 'ssect':
		$row = new mosttfsp_ssect( $database );
		break;
	case 'tspec':
		$row = new mosttfsp_tspec( $database );
		break;
	default:
		$row = new mosttfsp( $database );
		break;
	}
	$row->load( (int)$uid );
	$row->updateOrder();
	$row->move( $inc, "published >= 0" );
	$row->updateOrder();
	if (JVERSION== '1.0'){
		mosCache::cleanCache( 'com_ttfsp' );
	}
	myRedirect( INDURL.'?option=com_ttfsp&act='.$act );
}
//=====================================================================================================
function editsspec( $id, $option, $act ) {		// Редактирование записи
	$database =  JFactory::getDBO();
	$params = getparams();
	$noyes = array ();
  	$noyes[] 		= JHTML::_('select.option','0', _ttfsp_lang_11);
  	$noyes[] 		= JHTML::_('select.option','1', _ttfsp_lang_12);
  	
  	$payment_status[] = JHTML::_('select.option','0', _ttfsp_payment_status_0);
  	$payment_status[] = JHTML::_('select.option','1', _ttfsp_payment_status_1);
  	$payment_status[] = JHTML::_('select.option','2', _ttfsp_payment_status_2);
  	$payment_status[] = JHTML::_('select.option','3', _ttfsp_payment_status_3);
  	
  	
	switch ($act) {
	case 'elems':
		$row = new mosttfsp_el( $database );
		break;	
	case 'proftime':
		$row = new mosttfsp_stime( $database );
		break;		
	case 'sspec':
		$row = new mosttfsp_sspec( $database );
		break;
	case 'ssect':
		$row = new mosttfsp_ssect( $database );
		break;
	case 'tspec':
		$row = new mosttfsp_tspec( $database );
		break;
	case 'torders':
		$row = new mosttfsp_order ( $database );
		break;
	default:
		$row = new mosttfsp( $database );
		break;
	}
	$row->load( (int)$id );
	$lists['published'] 	= JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $row->published );
	switch ($act) {

	
	case 'proftime':	
	case 'ssect':
	case 'sspec':
		if ($act != 'proftime')
			$lists['offphoto'] 	= JHTML::_('select.booleanlist', 'offphoto', 'class="inputbox"', $row->offphoto );
		HTML_ttfsp::editsprspec( $row, $option, $lists,$params, $act);
		break;
		
	case 'torders': // Вызываем окно редактирования заказа
		
		$database->setQuery( "SELECT id, payment_status FROM #__ttfsp_dop WHERE id = ".$id );
		$paystat = $database->loadObjectList();
		
		
		$lists['order_status_admin'] = JHTML::_('select.genericlist',$payment_status, 'payment_status', 'class="inputbox" size="1"', 'value', 'text', $paystat[0]->payment_status);
		HTML_ttfsp::editorder( $row, $option, $lists,$params, $act);
		break;
		
		
	case 'tspec':
		$database->setQuery( "SELECT id, name FROM #__ttfsp_sprsect WHERE published=1 ORDER BY ordering" );
		$rowsect = $database->loadObjectList();
		$sprsect = array();
		$sprsect[0] = '';   
		$sprsect = array_merge( $sprsect, $rowsect);
		$database->setQuery( "SELECT id, name FROM #__ttfsp_sprspec WHERE published=1 ORDER BY ordering" );
		$rowspec = $database->loadObjectList();
		$sprspec = array();
		$sprspec[0] = '';   
		$sprspec = array_merge( $sprspec, $rowspec);
		$database->setQuery( "SELECT id, name FROM #__ttfsp_sprtime WHERE published=1 ORDER BY ordering" );
		$rowtime = $database->loadObjectList();
		$sprtime = array();
		$sprtime[0] = '';   
		$sprtime = array_merge( $sprtime, $rowtime);
		$lists['sprtime']	 = JHTML::_('select.genericlist', $sprtime, 'idsprtime', 'size="1" class="inputbox"', 'id', 'name',$row->idsprtime);
//		$lists['sprspec']	 = JHTML::_('select.genericlist', $sprspec, 'idsprspec', 'size="1" class="inputbox"', 'id', 'name',$row->idsprspec);
		$lists['sprsect']	 = JHTML::_('select.genericlist', $sprsect, 'idsprsect', 'size="1" class="inputbox"', 'id', 'name',$row->idsprsect);
		$lists['offphoto'] 	= JHTML::_('select.booleanlist', 'offphoto', 'class="inputbox"', $row->offphoto );
		$lists['adddt'] 	= JHTML::_('select.booleanlist', 'adddt', 'class="inputbox"', $row->adddt );
		$lists['addtm'] 	= JHTML::_('select.booleanlist', 'addtm', 'class="inputbox"', $row->addtm );

//
	$table_name ="#__ttfsp_sprspec";
	$query = "SELECT * "
	. "\n FROM  ".$table_name
	. "\n where published=1 ORDER BY ordering"
	;
	$database->setQuery( $query );
	$elements = $database->loadObjectList();
	$sel = '';
	$idsprspec = ','.$row->idsprspec.',';
	$htmlel =	'
	<select id="idsprspec" name="idsprspec[]"  multiple="multiple" style="width:320px;height:150px;">'; 
	for($i=0;$i<count($elements);$i++){
		$sel = '';
		$label = htmlspecialchars($elements[$i]->name, ENT_QUOTES);
		$myvalue= $elements[$i]->id;
		if ( strpos( ' '.$idsprspec, ','.$myvalue.',' ))
		$sel = ' selected="selected" ';	
		$htmlel .= '<option '.$sel.' value="'.$myvalue.'" >'.$label.'</option>';
	}	
	$htmlel .= '
				</select>';	
		$lists['sprspec']	 = $htmlel;
//
		HTML_ttfsp::editspec( $row, $option, $lists,$params, $act);
		break;
	default:
	if (isset($row->id) && $row->id){
	$database->setQuery( "SELECT * FROM #__ttfsp_dop WHERE payment_status <> 3 AND idrec=".$row->id );
	$rowdop = $database->loadObjectList();
	} else {
		$rowdop = NULL;
	}
	$database->setQuery( "SELECT id, name FROM #__ttfsp_spec WHERE published=1" );
		$rowspec = $database->loadObjectList();
		$sprspec = array();
		$sprspec[0] = '';   
		$sprspec = array_merge( $sprspec, $rowspec);
		$lists['sprspec']	 = JHTML::_('select.genericlist', $sprspec, 'idspec', 'size="1" class="inputbox"', 'id', 'name',$row->idspec);
		$lists['reception'] 	= JHTML::_('select.booleanlist', 'reception', 'class="inputbox"', $row->reception );
		$lists['sms'] 	= JHTML::_('select.booleanlist', 'sms', 'class="inputbox"', $row->sms );
		HTML_ttfsp::edittime( $row, $option, $lists,$params, $act, $rowdop);
		break;
	}
}
////////////////////////////////////////////////////////////////////////////////////////
function getparams(){
$database =  JFactory::getDBO();
	$query = "SELECT name AS text, value FROM #__ttfsp_set";
	$database->setQuery( $query );
	$params = array();
	$params = $database->loadObjectList();
		foreach($params as $param) {
		$params[$param->text] = $param->value;
		}
return $params;
}
////////////////////////////////////////////////////////////////////////////////////////////
function showConfig( $option) {
	$params = getparams();
	$lists = array();
	$noyes = array ();
	$decode = array ();
	$decode[] = JHTML::_('select.option', 0,'-');
  	$decode[] = JHTML::_('select.option', 1,'cp1251 -> UTF8');
  	$decode[] = JHTML::_('select.option', 2,'UTF8 -> cp1251');
  	$noyes[] = JHTML::_('select.option','0', _ttfsp_lang_11);
  	$noyes[] = JHTML::_('select.option','1', _ttfsp_lang_12);
  	$yandexkassaselect[] = JHTML::_('select.option','1',_ttfsp_yandex_kassa_up);
  	$yandexkassaselect[] = JHTML::_('select.option','0',_ttfsp_yandex_kassa_card);
  	$number_order_type =  array();
  	
  	$number_order_type[] = JHTML::_('select.option','0', _ttfsp_type_number_order_0);
  	$number_order_type[] = JHTML::_('select.option','1', _ttfsp_type_number_order_1);
	$lists['decode_in'] = JHTML::_('select.genericlist',$decode, 'params[decode_in]', 'class="inputbox" size="1"', 'value', 'text', $params['decode_in']);
	$lists['del_db'] = JHTML::_('select.genericlist',$noyes, 'params[del_db]', 'class="inputbox" size="1"', 'value', 'text', $params['del_db']);
	$lists['offemail'] = JHTML::_('select.genericlist',$noyes, 'params[offemail]', 'class="inputbox" size="1"', 'value', 'text', $params['offemail']);
	$lists['reguser'] = JHTML::_('select.genericlist',$noyes, 'params[reguser]', 'class="inputbox" size="1"', 'value', 'text', $params['reguser']);
	$lists['viewspec'] = JHTML::_('select.genericlist',$noyes, 'params[viewspec]', 'class="inputbox" size="1"', 'value', 'text', $params['viewspec']);
	$lists['editspec'] = JHTML::_('select.genericlist',$noyes, 'params[editspec]', 'class="inputbox" size="1"', 'value', 'text', $params['editspec']);
	$lists['onespec'] = JHTML::_('select.genericlist',$noyes, 'params[onespec]', 'class="inputbox" size="1"', 'value', 'text', $params['onespec']);
	$lists['qtsms_on'] = JHTML::_('select.genericlist',$noyes, 'params[qtsms_on]', 'class="inputbox" size="1"', 'value', 'text', $params['qtsms_on']);
	$lists['onmsg'] = JHTML::_('select.genericlist',$noyes, 'params[onmsg]', 'class="inputbox" size="1"', 'value', 'text', $params['onmsg']);
	if ( file_exists( JPATH_ROOT.DS."administrator".DS."components".DS."com_jcomments".DS."admin.jcomments.php") ||  file_exists( JPATH_ROOT.DS."administrator".DS."components".DS."com_jcomments".DS."jcomments.php") ) {
	$lists['jcomment'] = JHTML::_('select.genericlist', $noyes, 'params[jcomment]', 'class="inputbox" size="1"', 'value', 'text', $params['jcomment']);
	} else {
	$lists['jcomment'] = JHTML::_('select.genericlist', $noyes, 'params[jcomment]', 'class="inputbox" size="1" style="display:none;"', 'value', 'text', 0)._ttfsp_lang_197;
	}
	$lists['viewuser'] = JHTML::_('select.genericlist',$noyes, 'params[viewuser]', 'class="inputbox" size="1"', 'value', 'text', $params['viewuser']);
	$lists['modiuser'] = JHTML::_('select.genericlist',$noyes, 'params[modiuser]', 'class="inputbox" size="1"', 'value', 'text', $params['modiuser']);
	$lists['billing_on'] = JHTML::_('select.genericlist',$noyes, 'params[billing_on]', 'class="inputbox" size="1"', 'value', 'text', $params['billing_on']);
	$lists['billing_on_title'] = JHTML::_('select.genericlist',$noyes, 'params[billing_on_title]', 'class="inputbox" size="1"', 'value', 'text', $params['billing_on_title']);
	$lists['billing_on_title_2'] = JHTML::_('select.genericlist',$noyes, 'params[billing_on_title_2]', 'class="inputbox" size="1"', 'value', 'text', $params['billing_on_title_2']);
	$lists['sposob_oplaty_0_on'] = JHTML::_('select.genericlist',$noyes, 'params[sposob_oplaty_0_on]', 'class="inputbox" size="1"', 'value', 'text', $params['sposob_oplaty_0_on']);
	$lists['sposob_oplaty_1_on'] = JHTML::_('select.genericlist',$noyes, 'params[sposob_oplaty_1_on]', 'class="inputbox" size="1"', 'value', 'text', $params['sposob_oplaty_1_on']);
	$lists['sposob_oplaty_2_on'] = JHTML::_('select.genericlist',$noyes, 'params[sposob_oplaty_2_on]', 'class="inputbox" size="1"', 'value', 'text', $params['sposob_oplaty_2_on']);
	$lists['mail_spetialisations_on'] = JHTML::_('select.genericlist',$noyes, 'params[mail_spetialisations_on]', 'class="inputbox" size="1"', 'value', 'text', $params['mail_spetialisations_on']);
	$lists['mail_uchrejdeniya_on'] = JHTML::_('select.genericlist',$noyes, 'params[mail_uchrejdeniya_on]', 'class="inputbox" size="1"', 'value', 'text', $params['mail_uchrejdeniya_on']);
	$lists['type_number_order'] = JHTML::_('select.genericlist',$number_order_type, 'params[type_number_order]', 'class="inputbox" size="1"', 'value', 'text', $params['type_number_order']);
	$lists['specialization_select_on'] = JHTML::_('select.genericlist',$noyes, 'params[specialization_select_on]', 'class="inputbox" size="1"', 'value', 'text', $params['specialization_select_on']);
	$lists['yandex_test_mode'] = JHTML::_('select.genericlist',$noyes, 'params[yandex_test_mode]', 'class="inputbox" size="1"', 'value', 'text', $params['yandex_test_mode']);
	$lists['yandex_kassa_select'] = JHTML::_('select.genericlist',$yandexkassaselect, 'params[yandex_kassa_select]', 'class="inputbox" size="1"', 'value', 'text', $params['yandex_kassa_select']);

	

	HTML_ttfsp::settings( $option, $params, $lists );
}
//////////////////////////////////////////////////////////////////////////////////////////////////
function saveSettings( $option ) {
$database =  JFactory::getDBO();
$post	= JRequest::get( 'post' );
$params = JArrayHelper::getValue( $post, 'params', array(), 'array');
	if (is_array( $params )) {
		foreach ($params as $k=>$v) {
			$v = trim($v);
			if ($k=='moderators')
				$v = str_replace(' ','',$v);
			$query = "UPDATE #__ttfsp_set"
			. "\n SET `value` = '" . $v . "'"
			. "\n WHERE `name` = '" . $k . "'"
			;
				$database->setQuery( $query );
				$database->query();

		}
	}

$deldb  =   $params['del_db'];
$flagDel = JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."dflag.txt";
if ( file_exists($flagDel) && $deldb==0) unlink( $flagDel);
if ($deldb==1) {
$fp = @fopen($flagDel,"wb");
fclose($fp);
}
	if ( file_exists( JPATH_ROOT.DS."components".DS."com_jcomments".DS."plugins".DS."com_content.plugin.php") && !file_exists( JPATH_ROOT.DS."components".DS."com_jcomments".DS."plugins".DS."com_ttfsp.plugin.php")) {
		$ifile = JPATH_ROOT.DS."components".DS."com_ttfsp".DS."jcomments".DS."com_ttfsp.plugin.php";
		$cfile = JPATH_ROOT.DS."components".DS."com_jcomments".DS."plugins".DS."com_ttfsp.plugin.php";
		if (!@copy($ifile, $cfile))
			echo 'Error. Copy file '.$ifile;
	}
	$msg =_ttfsp_lang_13;
	myRedirect( INDURL."?option=$option&act=config" ,$msg);
}

////////////////////////////////////////////////////////////////////////////////////////////////////
function cancelSettings( $option='com_ttfsp'){
	myRedirect( INDURL."?option=$option&act=config" );
}
///////////////////////////////////////////////////////////////////////////////////////////////////
function myRedirect($url,$msg="", $err=''){
if (JVERSION== '1.0'){
mosRedirect($url,$msg);
} else {
	$mainframe = JFactory::getApplication();
	$mainframe->redirect( $url, $msg, $err );
}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function loadimg($newfilename, $params){
$fdir = $params['avatarspath'];
$filename = JPATH_ROOT.DS.$fdir.DS.$newfilename;
	list($oldwidth, $oldheight, $oldtype) = getimagesize($filename);
	switch ( $oldtype) {
  	case 1:
 	$source = imagecreatefromgif($filename);
 	break;
  	case 2: 
	$source = imagecreatefromjpeg($filename);
 	break;
  	case 3: 
	$source = imagecreatefrompng($filename); 
 	break;
	}
$H_width = $params['avatarw']  ? $params['avatarw'] : 180; // ширина 
$H_height = $params['avatarh'] ? $params['avatarh'] : 180; // высота
if($oldwidth > $H_width || $oldheight > $H_height){
$val_resize = $oldwidth > $oldheight ? $oldwidth/$H_width : $oldheight/$H_height;
$new_width=$oldwidth/$val_resize;
$new_height= $oldheight/$val_resize;
} else {
$new_width=$oldwidth;
$new_height= $oldheight;
}
$image = ImageCreateTrueColor( $new_width, $new_height);
		if( function_exists( "imageAntiAlias" )) {
			imageAntiAlias($image,true);
		}
	    imagealphablending($image, false);
	    if( function_exists( "imagesavealpha")) {
	    	imagesavealpha($image,true);
	    }
	    if( function_exists( "imagecolorallocatealpha")) {
	    	$transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
	    }
            // GD Lib 
        if(function_exists('ImageCopyResampled')){
            ImageCopyResampled( $image, $source, 0, 0, 0,0,$new_width,$new_height,$oldwidth,$oldheight);
        } else {
         ImageCopyResized( $image, $source, 0, 0, 0, 0, $new_width,$new_height,$oldwidth,$oldheight);
        }
		$td=80;
		switch ( $oldtype) {
			case 1:
				imagegif($image, $filename, $td);
			break;
			case 2:
				imagejpeg($image, $filename, $td);
			break;
			case 3:
				imagepng($image, $filename);
			break;
		}				
            imagedestroy( $image);
            imagedestroy( $source);
	return true;
}

function ifpayment_cancel ($zapis_na_priem, $zakaz_row_send, $del_ne_del) {
	
		$peoples = -1;
	
		$database =  JFactory::getDBO();
		
		$textinfo = '';
		
		
	
		$query = "SELECT * FROM #__ttfsp_dop WHERE idrec='".$zapis_na_priem->id."'";
			
		$database->setQuery($query);
			
		$zakaz_row = $database->loadObjectList(); /// Все заказы по данной записи
		
		
		$query = "SELECT COUNT(*) FROM #__ttfsp_dop WHERE idrec='".$zapis_na_priem->id."' AND payment_status <> 3";
			
		$database->setQuery($query);
			
		$countpay = $database->loadResult(); /// Количество заказов где статус платежа не отменен
		
		
		
		/// Если на одну дату один человек
		
		if (!$zapis_na_priem->plimit || $zapis_na_priem->plimit == 0) {
						
				
				
				if ($zakaz_row_send->payment_status != 3) {		
							
							
							if ($countpay >= 2 && $del_ne_del == 0) {
								
								$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = '3' WHERE number_order='".$zakaz_row_send->number_order."'";
			
								$database->setQuery($query);
		
								$database->query();
					
								myRedirect( INDURL."?option=com_ttfsp&act=torders&task=editA&id=$zakaz_row_send->id&hidemainmenu=1",_ttfsp_error_one_user, 'error');
							}
			
							$query = "UPDATE `#__ttfsp` SET `reception` = '1', `rmail` = '".$zakaz_row_send->rmail."', `iduser`='".$zakaz_row_send->iduser."', `rfio` = '".$zakaz_row_send->rfio."', `rphone`='".$zakaz_row_send->rphone."', `info`='".$zakaz_row_send->info."' WHERE id='".$zakaz_row_send->idrec."'";
			
							$database->setQuery($query);
		
							$database->query();
							
							return;
							
				}
				
				if ($zakaz_row_send->payment_status == 3) {
					
					
			
							$query = "UPDATE `#__ttfsp` SET `reception` = 0, `info` = '', `rfio` = '', `rphone` = ''  WHERE id='".$zakaz_row_send->idrec."'";
			
							$database->setQuery($query);
		
							$database->query();
							
							return;

				}

		}
					

		/////////
		
		
		for($s=0;$s<count($zakaz_row);$s++){
				
				$myvalue = $zakaz_row[$s];
				
				
				if ($myvalue->payment_status != 3) {
					
					$textinfo = $textinfo.$myvalue->info.'<br><br>';
					$peoples = $peoples+1;
					
				}
						
						
		}
		
		
		if ($zapis_na_priem->plimit && $zapis_na_priem->plimit != 0) {
			
			if ($countpay > $zapis_na_priem->plimit) {
				
				
				$query = "UPDATE `#__ttfsp_dop` SET `payment_status` = '3' WHERE number_order='".$zakaz_row_send->number_order."'";
			
				$database->setQuery($query);
		
				$database->query();
					
				myRedirect( INDURL."?option=com_ttfsp&act=torders&task=editA&id=$zakaz_row_send->id&hidemainmenu=1",_ttfsp_error_one_user, 'error');
				
			}
			
			if (($zapis_na_priem->plimit-1) <= $peoples) {
				
				$query = "UPDATE `#__ttfsp` SET `reception` = 1,  `info` = '".$textinfo."' WHERE id='".$zapis_na_priem->id."'";
			
				$database->setQuery($query);
		
				$database->query();
				
			} 
			
			if (($zapis_na_priem->plimit-1) > $peoples) {
				
				$query = "UPDATE `#__ttfsp`  SET `reception` = 0, `info` = '".$textinfo."' WHERE id='".$zapis_na_priem->id."'";
			
				$database->setQuery($query);
		
				$database->query();
				
			}
			
			
		}
	
	
}

function delete_orders ($idrecs) {
		
		$database =  JFactory::getDBO();
		
		$cids = 'id=' . implode( ' OR id=', $idrecs );

	
		$query = "SELECT * FROM #__ttfsp WHERE ( $cids )";
			
		$database->setQuery($query);
			
		$zapis_id = $database->loadObjectList();
		
		if ($zapis_id) {
			

			
			for($s=0;$s<count($zapis_id);$s++){
				
				$myvalue = $zapis_id[$s];
				
				
				
				$query = "SELECT * FROM #__ttfsp_dop WHERE idrec='".$myvalue->id."'";
			
				$database->setQuery($query);
			
				$zakaz_row = $database->loadObjectList();
				
				
				
				if ($zakaz_row) {
				
					ifpayment_cancel ($myvalue, $zakaz_row[0], 1);
					
				} else {
					
					$query = "UPDATE `#__ttfsp`  SET `reception` = 0, `info` = '', `rfio` = '', `rphone` = '' WHERE id='".$myvalue->id."'";
			
					$database->setQuery($query);
		
					$database->query();
					
				}
										
						
			}
			
		}
		
		
		

	
}
////////////////////////////////////////////////////////////
// Преобразование строки UTF8  -  WIN-1251 
function cp1251_utf8 ($s, $decode=0){
    $in_arr = array (
chr(208), chr(192), chr(193), chr(194),
chr(195), chr(196), chr(197), chr(168),
chr(198), chr(199), chr(200), chr(201),
chr(202), chr(203), chr(204), chr(205),
chr(206), chr(207), chr(209), chr(210),
chr(211), chr(212), chr(213), chr(214),
chr(215), chr(216), chr(217), chr(218),
chr(219), chr(220), chr(221), chr(222),
chr(223), chr(224), chr(225), chr(226),
chr(227), chr(228), chr(229), chr(184),
chr(230), chr(231), chr(232), chr(233),
chr(234), chr(235), chr(236), chr(237),
chr(238), chr(239), chr(240), chr(241),
chr(242), chr(243), chr(244), chr(245),
chr(246), chr(247), chr(248), chr(249),
chr(250), chr(251), chr(252), chr(253),
chr(254), chr(255)
);  
$out_arr = array (
chr(208).chr(160), chr(208).chr(144), chr(208).chr(145),
chr(208).chr(146), chr(208).chr(147), chr(208).chr(148),
chr(208).chr(149), chr(208).chr(129), chr(208).chr(150),
chr(208).chr(151), chr(208).chr(152), chr(208).chr(153),
chr(208).chr(154), chr(208).chr(155), chr(208).chr(156),
chr(208).chr(157), chr(208).chr(158), chr(208).chr(159),
chr(208).chr(161), chr(208).chr(162), chr(208).chr(163),
chr(208).chr(164), chr(208).chr(165), chr(208).chr(166),
chr(208).chr(167), chr(208).chr(168), chr(208).chr(169),
chr(208).chr(170), chr(208).chr(171), chr(208).chr(172),
chr(208).chr(173), chr(208).chr(174), chr(208).chr(175),
chr(208).chr(176), chr(208).chr(177), chr(208).chr(178),
chr(208).chr(179), chr(208).chr(180), chr(208).chr(181),
chr(209).chr(145), chr(208).chr(182), chr(208).chr(183),
chr(208).chr(184), chr(208).chr(185), chr(208).chr(186),
chr(208).chr(187), chr(208).chr(188), chr(208).chr(189),
chr(208).chr(190), chr(208).chr(191), chr(209).chr(128),
chr(209).chr(129), chr(209).chr(130), chr(209).chr(131),
chr(209).chr(132), chr(209).chr(133), chr(209).chr(134),
chr(209).chr(135), chr(209).chr(136), chr(209).chr(137),
chr(209).chr(138), chr(209).chr(139), chr(209).chr(140),
chr(209).chr(141), chr(209).chr(142), chr(209).chr(143)
 );  
if ($decode){
	$s = str_replace($out_arr,$in_arr,$s);
} else { 
	$s = str_replace($in_arr,$out_arr,$s);
}
return $s;
} 
//


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


?>