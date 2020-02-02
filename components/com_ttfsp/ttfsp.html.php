<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
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

class HTML_ttfsp {
///////////////////////////////////////////
public static function addtm($id, $tm, $link, $allspec){
echo $allspec;
?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.tmForm;
			if (pressbutton == 1) {
				form.task.value = 'moditm';
			}
			form.submit();
		}
		</script>
		<form id="tmForm" action="<?php echo $link; ?>" method="post" name="tmForm">
		<table>
		<tr>
			<td>
			<?php echo _ttfsp_lang_169; ?>
			</td>
			<td>
			<textarea class="inputbox" name="timehm"  cols="40" rows="15"><?php echo $tm; ?></textarea>
			<input type="hidden" name="task" value="">
			<input type="hidden" name="id" value="<?php echo $id; ?>">			
			</td>
		</tr>	
		</table>
		<input type="button" value="<?php echo _ttfsp_lang_211; ?>" class="inputbox" onClick="submitbutton(1)" />
		<input type="button" value="<?php echo _ttfsp_lang_63; ?>" class="inputbox" onClick="submitbutton(0)" />		
		</form>		
<?php		
}
////////////////////////////////////////////////////////////////////////////////
public static function paymentpage($zakaz_row, $params) {

	$doc = JFactory::getDocument();
	$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
	$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');

	if ($params['user_css']){
		$mycss ='<style type="text/css">
		'.$params["user_css"].'
		</style>
	';
	
	echo $mycss;
	
	}
	
	
	$templuser = 'paymentpage.php';
	if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser) ) {
		require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser);
	} else {
		echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser;
		return;
	}
	
	
	paymentpage_ttfsp::paymentpagettfsp($zakaz_row, $params);
}
////////////////////////////////////////////////////////////////////////////////


public static function successpage ($zakaz_row, $params) {
	
	$doc = JFactory::getDocument();
	$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
	$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');

	if ($params['user_css']){
		$mycss ='<style type="text/css">
		'.$params["user_css"].'
		</style>
	';
	
	echo $mycss;
	
	}
	
	$tempsuccesspage = 'successpage.php';
	if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$tempsuccesspage) ) {
		require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$tempsuccesspage);
	} else {
		echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$tempsuccesspage;
		return;
	}
	
	successpage_ttfsp::successpagettfsp($zakaz_row, $params);
	
}


public static function addclient($row, $rowuser, $cdate, $rowel, $params, $sprspecdesc, $aspec) {
if (JVERSION=='1.0'){
	global $mainframe;
	$mainframe->addCustomHeadTag('<script type="text/javascript" src="'.JPATH_LIVE_SITE.'components/com_ttfsp/ttfsp.js"></script>');
	$mainframe->addCustomHeadTag('<link  href="'.JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css"  rel="stylesheet" type="text/css" media="all" />');
} else {
	$doc = JFactory::getDocument();
	$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
	$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');
}
	if ($params['user_css']){
$mycss ='<style type="text/css">
'.$params["user_css"].'
</style>
';
echo $mycss;
	}
	$templuser = 'addclient.php';
	if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser) ) {
		require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser);
	} else {
		echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser;
		return;
	}
	addclient_ttfsp::addclientttfsp($row, $rowuser, $cdate, $rowel, $params, $sprspecdesc, $aspec);
}
///////////////////////////////////////////////////////////////////////////////////////////
public static function viewsect($rows, $params){
if (JVERSION=='1.0'){
	global $mainframe;
	$mainframe->addCustomHeadTag('<script type="text/javascript" src="'.JPATH_LIVE_SITE.'components/com_ttfsp/ttfsp.js"></script>');
	$mainframe->addCustomHeadTag('<link  href="'.JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css"  rel="stylesheet" type="text/css" media="all" />');
} else {
	$doc 		= JFactory::getDocument();
	$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
	$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');
}
	if ($params['user_css']){
$mycss ='<style type="text/css">
'.$params["user_css"].'
</style>
';
echo $mycss;
	}
	$templuser = 'viewsect.php';
	if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser) ) {
		require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser);
	} else {
		echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser;
		return;
	}
	viewsect_ttfsp::viewsecttfsp($rows, $params);
}
////////////////////////////////////////////////////////////////////////////////////////////
public static function viewspec($rows, $params, $rowspec, $sid, $jcmt, $my, $office){
if (JVERSION=='1.0'){
	global $mainframe;
	$mainframe->addCustomHeadTag('<script type="text/javascript" src="'.JPATH_LIVE_SITE.'components/com_ttfsp/ttfsp.js"></script>');
	$mainframe->addCustomHeadTag('<link  href="'.JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css"  rel="stylesheet" type="text/css" media="all" />');
} else {
	$doc 		= JFactory::getDocument();
	$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
	$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');
}
	if ($params['user_css']){
$mycss ='<style type="text/css">
'.$params["user_css"].'
</style>
';
echo $mycss;
	}
	$templuser = 'viewspec.php';
	if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser) ) {
		require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser);
	} else {
		echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser;
		return;
	}
	viewspec_ttfsp::viewspecttfsp($rows, $params, $rowspec, $sid, $jcmt, $my, $office);
}


///////////////////////////////////////////////////////////////////Личный кабинет пользователя
public static function viewdetail($zakaz_row, $params){  

		$doc = JFactory::getDocument();
		$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
		$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');

	if ($params['user_css']){
		$mycss ='<style type="text/css">
		'.$params["user_css"].'
		</style>
		';
		echo $mycss;
	}
	
	
	echo '
	<script type="text/javascript">
		var yesrecept="'._ttfsp_lang_193._ttfsp_lang_189.'?";
		var norecept="'._ttfsp_lang_193._ttfsp_lang_190.'?";
		var delorder="'._ttfsp_lang_203.'?";
	</script>
	<form id="delNumOrdRec" action="#" method="post" name="delNumOrdRec">
		<input type="hidden" id="IdOrdRec" name="IdOrdRec" value="0" />	
		<input type="hidden" id="NumOrdRec" name="NumOrdRec" value="" />	
		<input type="hidden" id="OrderDelRec" name="OrderDelRec" value="'.$_SERVER["REQUEST_URI"].'" />				
	</form>
	';
	
	
	$templuser = 'viewuser.php';
	
	if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser) ) {
		require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser);
	} else {
		echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser;
		return;
	}
	
	viewuser_ttfsp::viewuserttfsp($zakaz_row, $params);	
}


/////////////////////////////////////////////////////////////////////////////////////
public static function viewtime($rows, $rowstime, $cdate, $curdate, $ttime, $params, $sprspecdesc, $sid){
	if (JVERSION=='1.0'){
		global $mainframe;
		$mainframe->addCustomHeadTag('<script type="text/javascript" src="'.JPATH_LIVE_SITE.'components/com_ttfsp/ttfsp.js"></script>');
		$mainframe->addCustomHeadTag('<link  href="'.JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css"  rel="stylesheet" type="text/css" media="all" />');
	} else {
		$doc = JFactory::getDocument();
		$doc->addScript(JPATH_LIVE_SITE."components/com_ttfsp/ttfsp.js");
		$doc->addStyleSheet(JPATH_LIVE_SITE.'components/com_ttfsp/css/ttfsp.css');
	}
	if ($params['user_css']){
$mycss ='<style type="text/css">
'.$params["user_css"].'
</style>
';
echo $mycss;
	}
echo '
<script type="text/javascript">
var yesrecept="'._ttfsp_lang_193._ttfsp_lang_189.'?";
var norecept="'._ttfsp_lang_193._ttfsp_lang_190.'?";
var delrecept="'._ttfsp_lang_203.'?";
</script>
	<form id="delRec" action="#" method="post" name="delRec">
		<input type="hidden" id="idDelRec" name="idDelRec" value="0" />	
		<input type="hidden" id="urlDelRec" name="urlDelRec" value="'.$_SERVER["REQUEST_URI"].'" />				
	</form>
';
	
	$templuser = 'viewtime.php';
	if ( file_exists( JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser) ) {
		require_once(JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser);
	} else {
		echo 'Error. Not found file '.JPATH_ROOT_SITE.DS."components".DS."com_ttfsp".DS."tpl".DS.$templuser;
		return;
	}
	viewtime_ttfsp::viewtimettfsp($rows, $rowstime, $cdate, $curdate, $ttime, $params, $sprspecdesc, $sid);
}
//////////////////////////////////////////////////////////////////////////////////
}
?>