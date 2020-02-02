<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
class viewuser_ttfsp {
	public static function viewuserttfsp($zakaz_row, $params){
		$cortime = (int) $params['cortime'];
		$time = time()+$cortime;
		$time = (int) $params['notime']+$time;
		echo '<div class="user_rec"><table  class="usr_cabinet_ttfsp" width="100%">';
		echo '<tr><th colspan="2">'._ttfsp_lang_206.'</th></tr>';
		for ($i=0; $i<count($zakaz_row); $i++){
			$row=$zakaz_row[$i];		
			$timeprm = strtotime($row->date)+((int)$row->hours*3600)+((int)$row->minutes*60);	
			echo '<tr><td class="info_list_user_cab">';
			echo '<h2 class="number_ord_list_title">'. _ttfsp_lang_47.' № '. $row->number_order.'</h2>';
			echo $row->info;
			echo '<br />';
			echo '<br />';
			echo _ttfsp_lang_167.$row->office_name;
			echo '<br />';
			echo _ttfsp_lang_44.': '.$row->specialist_name;
			echo '<br />';
			echo _ttfsp_lang_21.': '.$row->specializations_name;
			echo '</td><td width="10%" align="center">';
			
			if ($params['modiuser'])
					echo '<script>var number_order_notire = "'.$row->number_order.'"</script>';
					echo '<span style="cursor:pointer;"><img src="components/com_ttfsp/images/del.png" title="'._ttfsp_lang_202.'" onclick="del_order('.$row->idrec.', number_order_notire);" /></span>';
			echo '</td></tr>';	

		}
		echo '</table></div>';	
	}
}

?>