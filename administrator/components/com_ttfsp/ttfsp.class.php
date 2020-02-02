<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
if (JVERSION== '1.0'){
	class mosttfsp_el extends mosDBTable {
	var $id				= null;
	var $idsspec		= null;	
	var $name			= null;
	var $title			= null;
	var $published		= null;
	var $type			= null;
	var $value			= null;
	var $required			= null;
	var $readonly			= null;
	var $maxlength		= null;
	var $css			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $mask			= null;
	var $size			= null;
	var $fname			= null;	
	var $multisel			= null;		
		function mosttfsp_el() {
			global $database;
			$this->mosDBTable( '#__ttfsp_el', 'id', $database );
		}

		function check() {
		return true;
		}
	} //mosttfsp_el
	


class mosttfsp extends mosDBTable {
	var $id				= null;
	var $name			= null;
	var $published		= null;
	var $desc			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $iduser			= null;
	var $idspec			= null;
	var $dttime			= null;
	var $hrtime			= null;
	var $mntime			= null;
	var $reception		= null;
	var $rfio			= null;
	var $rphone			= null;
	var $info			= null;	
	var $ipuser			= null;
	var $plimit			= null;
	var $sms			= null;	
		function mosttfsp() {
			global $database;
			$this->mosDBTable( '#__ttfsp', 'id', $database );
		}

		function check() {
		return true;
		}
	} //mosttfsp
class mosttfsp_tspec extends mosDBTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $photo			= null;
	var $offphoto		= null;
	var $idsprspec		= null;
	var $idsprsect		= null;
	var $specmail		= null;
	var $idusr			= null;	
	var $specphone		= null;
	var $number_cabinet	= null;		
	var $idsprtime		= null;	
	var $adddt			= null;	
	var $addtm			= null;			
		function mosttfsp_tspec() {
			global $database;
			$this->mosDBTable( '#__ttfsp_spec', 'id', $database );
		}

		function check() {
		return true;
		}
	} //mosttfsp_tspec
class mosttfsp_sspec extends mosDBTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $photo			= null;
	var $offphoto		= null;
		function mosttfsp_sspec() {
			global $database;
			$this->mosDBTable( '#__ttfsp_sprspec', 'id', $database );
		}
		function check() {
		return true;
		}
	} //mosttfsp_sspec
class mosttfsp_stime extends mosDBTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $timehm			= null;	
		function mosttfsp_stime() {
			global $database;
			$this->mosDBTable( '#__ttfsp_sprtime', 'id', $database );
		}
		function check() {
		return true;
		}
	} //mosttfsp_stime
	
class mosttfsp_ssect extends mosDBTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $photo			= null;
	var $offphoto		= null;
	var $latitude		= null;
	var $longitude		= null;
	var $yamap			= null;
		function mosttfsp_ssect() {
			global $database;
			$this->mosDBTable( '#__ttfsp_sprsect', 'id', $database );
		}
		function check() {
		return true;
		}
	} //mosttfsp_ssect	
class mosttfsp_addtime extends mosDBTable {
	var $id				= null;
	var $published		= null;
	var $addspec			= null;
	var $adddate			= null;
	var $addhr1			= null;
	var $addhr2			= null;
	var $addhr3			= null;
	var $addhr4			= null;
	var $addhr5			= null;
	var $addhr6			= null;
	var $addhr7			= null;
	var $addhr8			= null;
	var $addhr9			= null;
	var $addhr10			= null;
	var $addhr11			= null;
	var $addhr12			= null;
	var $addhr13			= null;
	var $addhr14			= null;
	var $addhr15			= null;
	var $addhr16			= null;
	var $addhr17			= null;
	var $addhr18			= null;
	var $addhr19			= null;
	var $addhr20			= null;
	var $addhr21			= null;
	var $addhr22			= null;
	var $addhr23			= null;
	var $addhr24			= null;
	var $addhr25			= null;
	var $addhr26			= null;
	var $addhr27			= null;
	var $addhr28			= null;
	var $addmn1			= null;
	var $addmn2			= null;
	var $addmn3			= null;
	var $addmn4			= null;
	var $addmn5			= null;
	var $addmn6			= null;
	var $addmn7			= null;
	var $addmn8			= null;
	var $addmn9			= null;
	var $addmn10			= null;
	var $addmn11			= null;
	var $addmn12			= null;
	var $addmn13			= null;
	var $addmn14			= null;
	var $addmn15			= null;
	var $addmn16			= null;
	var $addmn17			= null;
	var $addmn18			= null;
	var $addmn19			= null;
	var $addmn20			= null;
	var $addmn21			= null;
	var $addmn22			= null;
	var $addmn23			= null;
	var $addmn24			= null;
	var $addmn25			= null;
	var $addmn26			= null;
	var $addmn27			= null;
	var $addmn28			= null;
	var $pricezap			= null;	
	var $plimit				= null;	
	var $idspec				= null;			
		function mosttfsp_addtime() {
			global $database;
			$this->mosDBTable( '#__ttfsp_addtime', 'id', $database );
		}

		function check() {
		return true;
		}
	} //mosttfsp_addtime

} else {
	class mosttfsp_el extends JTable {
	var $id				= null;
	var $idsspec		= null;	
	var $name			= null;
	var $title			= null;
	var $published		= null;
	var $type			= null;
	var $value			= null;
	var $required			= null;
	var $readonly			= null;
	var $maxlength		= null;
	var $css			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $mask			= null;
	var $size			= null;
	var $fname			= null;	
	var $multisel			= null;			
	function mosttfsp_el( &$db) {
		parent::__construct( '#__ttfsp_el', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} // mosttfsp_el

class mosttfsp extends JTable {
	var $id				= null;
	var $name			= null;
	var $published		= null;
	var $desc			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $iduser			= null;
	var $idspec			= null;
	var $dttime			= null;
	var $hrtime			= null;
	var $mntime			= null;
	var $reception			= null;
	var $rfio			= null;
	var $rphone			= null;
	var $info			= null;
	var $ipuser			= null;
	var $plimit			= null;
	var $sms			= null;	
		function mosttfsp( &$db) {
		parent::__construct( '#__ttfsp', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} // mosttfsp
class mosttfsp_tspec extends JTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $photo			= null;
	var $offphoto			= null;
	var $idsprspec		= null;
	var $idsprsect		= null;
	var $specmail			= null;
	var $idusr				= null;
	var $specphone		= null;	
	var $idsprtime		= null;	
	var $adddt			= null;	
	var $addtm			= null;		
		function mosttfsp_tspec(&$db) {
		parent::__construct( '#__ttfsp_spec', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} //mosttfsp_tspec

class mosttfsp_sspec extends JTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $photo			= null;
	var $offphoto		= null;
		function mosttfsp_sspec(&$db) {
		parent::__construct( '#__ttfsp_sprspec', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} //mosttfsp_sspec
	
class mosttfsp_stime extends JTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $timehm			= null;	
	function mosttfsp_stime(&$db) {
		parent::__construct( '#__ttfsp_sprtime', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} //mosttfsp_stime	
class mosttfsp_ssect extends JTable {
	var $id				= null;
	var $name			= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $published		= null;
	var $desc			= null;
	var $photo			= null;
	var $offphoto		= null;
	var $latitude		= null;
	var $longitude		= null;
	var $yamap			= null;
	
		function mosttfsp_ssect(&$db) {
		parent::__construct( '#__ttfsp_sprsect', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} //mosttfsp_ssect	
class mosttfsp_addtime extends JTable {
	var $id				= null;
	var $published		= null;
	var $addspec			= null;
	var $adddate			= null;
	var $addhr1			= null;
	var $addhr2			= null;
	var $addhr3			= null;
	var $addhr4			= null;
	var $addhr5			= null;
	var $addhr6			= null;
	var $addhr7			= null;
	var $addhr8			= null;
	var $addhr9			= null;
	var $addhr10			= null;
	var $addhr11			= null;
	var $addhr12			= null;
	var $addhr13			= null;
	var $addhr14			= null;
	var $addhr15			= null;
	var $addhr16			= null;
	var $addhr17			= null;
	var $addhr18			= null;
	var $addhr19			= null;
	var $addhr20			= null;
	var $addhr21			= null;
	var $addhr22			= null;
	var $addhr23			= null;
	var $addhr24			= null;
	var $addhr25			= null;
	var $addhr26			= null;
	var $addhr27			= null;
	var $addhr28			= null;
	var $addmn1			= null;
	var $addmn2			= null;
	var $addmn3			= null;
	var $addmn4			= null;
	var $addmn5			= null;
	var $addmn6			= null;
	var $addmn7			= null;
	var $addmn8			= null;
	var $addmn9			= null;
	var $addmn10			= null;
	var $addmn11			= null;
	var $addmn12			= null;
	var $addmn13			= null;
	var $addmn14			= null;
	var $addmn15			= null;
	var $addmn16			= null;
	var $addmn17			= null;
	var $addmn18			= null;
	var $addmn19			= null;
	var $addmn20			= null;
	var $addmn21			= null;
	var $addmn22			= null;
	var $addmn23			= null;
	var $addmn24			= null;
	var $addmn25			= null;
	var $addmn26			= null;
	var $addmn27			= null;
	var $addmn28			= null;
	var $plimit				= null;	
	var $idspec				= null;			
		function mosttfsp_addtime(&$db) {
		parent::__construct( '#__ttfsp_addtime', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} //mosttfsp_addtime
}
class mosttfsp_order extends JTable {
	var $id				= null;
	var $summa			= null;	
	var $idrec			= null;
	var $iduser			= null;
	var $published		= null;
	var $ordering			= null;
	var $checked_out		= null;
	var $checked_out_time	= null;
	var $rfio			= null;
	var $rphone			= null;
	var $info			= null;
	var $ipuser			= null;
	var $rmail		= null;
	var $payment_status			= null;
	var $id_specialist			= null;
	var $cdate			= null;
	var $hours			= null;	
	var $minutes			= null;
	var $office_name			= null;	
	var $specializations_name			= null;
	var $specialist_name			= null;
	var $specialist_email			= null;
	var $specialist_phone			= null;
	var $number_order			= null;
	var $order_password			= null;
	var $date			= null;
	var $office_desc			= null;
	var $office_address			= null;
	var $number_cabinet			= null;
	var $sms_send			= null;
					
		function mosttfsp_order(&$db) {
		parent::__construct( '#__ttfsp_dop', 'id', $db );
		}
		function check() {
		return true;
		}
		function updateOrder( $where='' )
		{
		return $this->reorder( $where );
		}
	} //mosttfsp_el	
	
	


?>
