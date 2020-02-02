<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function emailValidate($emailString) {
	$emailValidExpr="/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9]+[a-zA-Z0-9_-]*)+$/";
	return preg_match($emailValidExpr, $emailString);
}

function listInt($liststring) {
	$liststring = preg_replace('/[^,0-9]/', '', $liststring); // удалить всё кроме цифр и запятой
	$liststring = str_replace(" ", "", $liststring); // удалить пробелы
	$liststring = str_replace(",,", ",", $liststring); // удалить удвоенные запятые
	if (substr($liststring,0,1)==',') $liststring = substr_replace($liststring,"",0,1); // удалить запятую в начале строки
	if (substr($liststring,-1)==',') $liststring = substr_replace($liststring,"",-1,1); // удалить запятую в конце строки
	return $liststring;
}

function clearProductDescription($descrString) {
	$descrString = preg_replace('/{youtube}(.*){\/youtube}/', '', $descrString); // удалить видео-теги
	$descrString = preg_replace('/\[widgetkit id=\d*\]/', '', $descrString); // удалить ссылки на widgetkit	
	$descrString = preg_replace('/<p>|<li>|<td>|<br>|<\/br>/', ' ', $descrString); // заменить эти теги на пробел
	$descrString = str_replace('\r\n',' ', strip_tags($descrString)); // удалить остальные теги
	$descrString = htmlspecialchars(str_replace("&nbsp;"," ",$descrString));
	$descrString = preg_replace('/(\s{2,})/', ' ', $descrString); // два и больше пробелов заменить на один
	$descrString = preg_replace('/(\s\.)/', ' ', $descrString); // удалить оторванные от слов точки
	$descrString = JHtml::_('string.truncate', $descrString, 490);
	return $descrString;
}

function clearVendorModel($nameString, $vendorString) {
	if ( !(strpos(strtolower($nameString), strtolower($vendorString)) === false) 
		and (substr_count (strtolower($nameString), strtolower($vendorString).'-') == 0 ) ) {
			$nameString = str_ireplace( $vendorString, '', $nameString );
			$nameString = str_replace( '«»', '', $nameString);
			$nameString = str_replace( '""', '', $nameString);
			$nameString = trim( str_replace( "''", '', $nameString));
	}
	$nameString = htmlspecialchars($nameString);
	$nameString = JHtml::_('string.truncate', $nameString, 250);
	return $nameString;
}

?>