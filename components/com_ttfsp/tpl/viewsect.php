<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class viewsect_ttfsp 
	{
	public static function viewsecttfsp($rows, $params){
		echo '<div class="ttfspsect"><table class="tablevt offtbl">';
		$url_site = $params['url_site'];
		for ($i=0, $n=count($rows); $i < $n; $i++) {
		$row = $rows[$i];
		$sprsectname='';		
		$sprsectdesc='';		
		$img = ''; 
		if (!$row->offphoto && $row->photo){
			$img = '<div class="sect_images">';
			$files = explode(';', $row->photo);
				$img .= '<span>
				<img class="photovt" src="'.$url_site.$files[0].'" />
				</span>
				';
	
			$img .= '</div>';
		}
		$link = 'index.php?option=com_ttfsp&view=office&office='.$row->id;
		$row->desc = str_replace(chr(13),'<br />',$row->desc);
		$sprsectname = str_replace(chr(13),'<br />',$sprsectname);
		if (JVERSION=="1.0"){
			$link=sefRelToAbs($link);
		} else {
			$link=JRoute::_($link);
		}
		?>
		<tr class="offblock">
		 <td class="tdvs" valign="top">
			<?php echo '<a href="'.$link.'">'.$img; ?></a>
		 </td>
		 <td class="tdvs" valign="top">
			<div class="fiosect">
			<?php echo '<a href="'.$link.'">'.$row->name; ?></a></div> 
			<div class="descsect">
				<?php echo $row->desc; ?>
			</div>
			
			<?php if ($row->address) { ?>
			<div class="adrsssect">
				<div class="adrtl">
					<?php echo _ttfsp_adress_title; ?>:
				</div>
				<div class="adrtxt">
					<?php echo $row->address; ?>
				</div>
			</div>
			<div class="splist">
				<?php echo '<a class="splistlink" href="'.$link.'">'._ttfsp_specialis_list; ?></a></div> 
			</div>
			<?php } ?>
			
		 </td>
		 <td class="tdvs" valign="top">
		 <div class="sect">
			<?php echo $sprsectname; ?></div>
		 </td>
		</tr>
		<?php
		}
		echo '</table></div>';
	}
}

?>