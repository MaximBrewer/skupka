<?php
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');

class viewspec_ttfsp 
	{
	public static function viewspecttfsp($rows, $params, $rowspec, $sid, $jcmt, $my, $office){
		$wr = $sid ? '&sid='.$sid : '';
		$office = '&office='.$office;
		echo '<div class="ttfspspec"><table class="tablevs">';
		$url_site = $params['url_site'];
		for ($i=0, $n=count($rows); $i < $n; $i++) {
		$row = $rows[$i];
		$sprspecname='';		
		$sprspecdesc='';		
		$img = ''; 
		if (!$row->offphoto && $row->photo){
			$img = '<div class="spec_images">';
			$files = explode(';', $row->photo);
			if (!$params['onespec']){
				for ($p=0;$p<count($files);$p++){	
					$img .= '<span>
					<img class="photovt" src="'.$url_site.$files[$p].'" />
					</span>
					';
				}
			} else {
				$img .= '<span>
				<img class="photovt" src="'.$url_site.$files[0].'" />
				</span>
				';
			}
			$img .= '</div>';
		}
		
		
		$link = 'index.php?option=com_ttfsp&idspec='.$row->id.$wr.$office;
		$slink = 'index.php?option=com_ttfsp&view=detail&idspec='.$row->id;
		$row->desc = str_replace(chr(13),'<br />',$row->desc);
		for($s=0;$s<count($rowspec);$s++){
			$myvalue = $rowspec[$s]->id;
			if ( strpos( ' '.$row->idsprspec, ','.$myvalue.',' )){
				$sprspecname .= $sprspecname ? '<br />'.$rowspec[$s]->name : $rowspec[$s]->name;		
				$sprspecname .= $rowspec[$s]->desc ? ': '.$rowspec[$s]->desc : '';
			}
		}
		$sprspecname = str_replace(chr(13),'<br />',$sprspecname);
		if (JVERSION=="1.0"){
			$link=sefRelToAbs($link);
			$slink=sefRelToAbs($slink);			
		} else {
			$link=JRoute::_($link);
			$slink=JRoute::_($slink);			
		}
		$link = '<a href="'.$link.'">'.$params['title_btn'].'</a>';
		?>
		<tr>
		 <td class="tdvs" valign="top">
			<?php echo $img; ?>
		 </td>
		 <td class="tdvs" valign="top">
			<div class="fiospec">
			<?php echo $row->name; ?></div>
			<div class="descspec">
			<?php echo $row->desc; ?></div>
			<?php 
				if ($params['jcomment'] && !$jcmt){
					echo '<div class="jcomm_btn"><a href="'.$slink.'">';
					echo $params['tjcomment'];
					echo '</a></div>';
				}
			?>
			<?php 
				if ($row->idusr == $my->id && (int)$my->id>0){
					if ($row->addtm){
						$linkt = 'index.php?option=com_ttfsp&task=addtm';
						echo '<div class="addtm_btn"><a href="'.$linkt.'">';
						echo _ttfsp_lang_209;
						echo '</a></div>';
					}
					if ($row->adddt){
						$linkd = 'index.php?option=com_ttfsp&task=adddt';
						echo '<div class="adddt_btn"><a href="'.$linkd.'">';
						echo _ttfsp_lang_210;
						echo '</a></div>';
					}
				}
			?>			
		 </td>
		 <td class="tdvs" valign="top">
		 <div class="spec">
			<?php echo $sprspecname; ?></div>
		 </td>
		 <td class="tdvs" valign="top">
			<div class="buttonvs"><?php echo $link; ?></div>
		 </td>
		</tr>
		<?php
		}
		echo '</table></div>';
			if ($params['jcomment'] && $jcmt){		
				$comments =  JPATH_ROOT_SITE.'/components/com_jcomments/jcomments.php';
				if (file_exists($comments)) {
					require_once($comments);
					echo '<div class="commentstt"> <br />';
					echo JComments::showComments($row->id, 'com_ttfsp',$row->name);
					echo '</div>';
				}
			}
		
		
		
		
		
	}
}

?>