<?php
// no direct access
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::root().'modules/mod_specfsp/style/style.css');
	echo '<div class="mod_spec_all">';
		for ($i=0, $n=count($rows); $i < $n; $i++) {
		echo '<div class="mod_spec">';
		$row = $rows[$i];
		$sprspecname='';		
		$img = ''; 
		if (!$row->offphoto && $row->photo && $photo){
			$img = '<div class="spec_photo">';
			$files = explode(';', $row->photo);
			$img .= '
				<img class="photo_img" src="'.$url_site.$files[0].'" />
				';
			$img .= '</div>';
		}
		$link = 'index.php?option=com_ttfsp&idspec='.$row->id;
		$slink = 'index.php?option=com_ttfsp&view=detail&idspec='.$row->id;
		$row->desc = str_replace(chr(13),'<br />',$row->desc);
		for($s=0;$s<count($rowspec);$s++){
			$myvalue = $rowspec[$s]->id;
			if ( strpos( ' '.$row->idsprspec, ','.$myvalue.',' )){
				$sprspecname .= $sprspecname ? ', '.$rowspec[$s]->name : $rowspec[$s]->name;		
			}
		}
		$sprspecname = str_replace(chr(13),'<br />',$sprspecname);
		$link=JRoute::_($link);
		$slink=JRoute::_($slink);	
		$fio = $fiolink ? '<a href="'.$link.'">'.$row->name.'</a>' : $row->name;
		$link = '<a href="'.$link.'">'.$myparams['title_btn'].'</a>';
			echo $img; // фото
			echo '<div>';
			echo '
			<div class="mod_fiospec">'.$fio.'</div>
			'; // ФИО
			
			if ($row->desc && $desc)	
				echo '<div class="mod_descspec">'.$row->desc.'</div>'; // Описание специалиста
			
			if ($myparams['jcomment'] && $comment){					
				echo '<div class="mod_jcomment_btn"><a href="'.$slink.'">'; // Кнопка отзывы
				echo $myparams['tjcomment'];
				echo '</a></div>';
			}
			echo '</div>';
			if ($sprspecname)
				echo '<div class="mod_specs">'.$sprspecname.'</div>'; //Специализации	
				if ($button)	
				echo '<div class="mod_buttonvs">'.$link.'</div>'; // Кнопка запись на прием
	
		echo '</div>';
		}
		if ($text_add_link && $add_link)
			echo '<div class="mod_add_link"><a href="'.JRoute::_($add_link).'">'.$text_add_link.'</a></div>'; // Произвольная ссылка
	echo '</div>';	

?>