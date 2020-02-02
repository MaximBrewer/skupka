<?php
/*
 * @package Joomla 3.x
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('jquery.framework');
JHtml::_('formbehavior.chosen', 'select');
$r 			=  new MapYandexRenderAdminView();
$app		= JFactory::getApplication();
$option 	= $app->input->get('option');
$OPT		= strtoupper($option);
echo '<form action="index.php" method="post" name="adminForm" id="adminForm">';
echo '<div class="row-fluid">';
echo '<div class="span10 form-horizontal">';
echo '<fieldset>';

?>
  

	<?php $document = JFactory::getDocument();?>


<?php

$this->editmarker=$this->editmarker[0];

$longitude = '';
$latitude = '';
$stylead = '';
$stylecoo='';
if($this->editmarker->yandexcoord == 1) {
	$stylecoo='style="display:none;"';
	$valone = 'var valone = "'.$this->editmarker->city_map_yandex.', '.$this->editmarker->street_map_yandex.'"';
} else if ($this->editmarker->yandexcoord == 2){
	$stylead = 'style="display:none;"';
	$parsejson = json_decode($this->editmarker->lng);
	$longitude = $parsejson->longitude_map_yandex;
	$latitude = $parsejson->latitude_map_yandex;
	$valone = 'var valone = "'.$latitude.', '.$longitude.'"';
} else {
	$stylecoo='style="display:none;"';
	$valone = 'var valone = "'.$this->editmarker->city_map_yandex.', '.$this->editmarker->street_map_yandex.'"';
}

$script ='	


	$j = jQuery;
	$j(function(){
	$j(\'[name="yandexcoord"]\').change(function(){
		v = $j(this).children("option:selected").val()
		if(v == 2) {
			$j(".dispcoords").fadeIn("slow",function(){
				$j(".dispadres").fadeOut("slow");
			});
			
		}
		else {
			$j(".dispadres").fadeIn("slow",function(){
				$j(".dispcoords").fadeOut("slow");
			});
		}
	});
	$j(".deletefile").click(function(e){
		mid = '.$this->editmarker->id.';
		senddata = {mid:mid}
			e.preventDefault();
				$j.ajax({
						data:senddata,
						url: \''.JURI::root(true).'/administrator/index.php?option=com_mapyandex&task=ajaxdelete&format=row\',
						success: function(data) {
							$j(".loader_img_for_input").hide();
							$j.each(data,function(i,p){
							
									$j.each(p.num,function(numi,e){
										
										if(e == 0) {
											$j.each(p.info,function(i,e){
												if(numi == i) {
							
													$j(".error_delete_file:eq("+i+")").empty("");
													$j(".error_delete_file:eq("+i+")").text("'.JText::_('COM_ERROR_NO_FILE').' "+e);
												}
											});
										} else if(e == 2) {
											$j.each(p.info,function(i,e){
												if(numi == i) {
							
													$j(".error_delete_file:eq("+i+")").empty("");
													$j(".error_delete_file:eq("+i+")").text("'.JText::_('COM_ERROR_NO_PERMS').' "+e);
												}
											});
										} else {
											$j(".imguser img,.deletefile").fadeOut("slow");
											
											$j.each(p.info,function(i,e){
												if(numi == i) {

													$j(".error_delete_file:eq("+i+")").empty("");
													$j(".error_delete_file:eq("+i+")").text("'.JText::_('COM_SUCCESS_DELETE_FILE').' "+e);
												}

											});
										}
									});
									

									


				
								

								
							});
							
						},
						error:function(){
					
						},
						type:\'POST\',
						dataType:\'json\'
				
				});
	});
});
		<!--
		function changeDisplayImage() {
			if (document.adminForm.imageurl.value !="") {
				document.adminForm.imagelib.src="../images/banners/" + document.adminForm.imageurl.value;
			} else {
				document.adminForm.imagelib.src="images/blank.png";
			}
		}
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == "cancel") {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.name_marker.value == "") {
				alert( "'.JText::_( "ERROR_NAME_MAP", true ).'" );
			}
			else if (form.city_map_yandex.value == "") {
				alert( "'.JText::_( "ERROR_CITY", true ).'" );
			
			} else if (form.street_map_yandex.value == "") {
				alert( "'.JText::_( "ВERROR_STREET", true ).'" );
			}
	
			else {
				submitform( pressbutton );
			}
		}
		//-->';
	
$document->addScriptDeclaration($script);
$tabs = array (
'general' 		=> JText::_($OPT.'_GENERAL_OPTIONS'),

);

echo $r->navigation($tabs);

echo '<div class="tab-content">'. "\n";

echo '<div class="tab-pane active" id="general">'."\n";

?>

		
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMARKER' ); ?>:
				</div>
				<div class="controls">
			 <input type="text" name="name_marker" id="keyword" value="<?php echo $this->editmarker->name_marker;?>" />
				</div>
			</div>
			
			<div class="control-group">
				<div class="control-label">
						<?php echo JText::_( 'COM_MAPYANDEX_MARKER_BALOON' ); ?>:
				</div>
				<div class="controls">
			<?php 

				$baloon = array();
				$baloon[] = JHTML::_('select.option','1', JText::_( 'COM_MAPYANDEX_MARKER_BALOON' ));
				$baloon[] = JHTML::_('select.option','2', JText::_( 'COM_MAPYANDEX_MARKER_BALOON_NOT' ));

				echo JHTML::_('select.genericlist',  $baloon, $name = 'baloon', $attribs = 'autocomplete="off"', $key = 'value', $text = 'text', $selected = $wih[1], $idtag = false, $translate = false );
			?>
				</div>
			</div>		

					<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMAP' ); ?>:
				</div>
				<div class="controls">
			<?php echo $this->allmaps?>
				</div>
			</div>	
		


					<div class="control-group">
				<div class="control-label">
						<?php echo JText::_( 'COM_MAPYANDEX_SEACRHMETHOD' ); ?>
				</div>
				<div class="controls">
				<?php 

				$statecoord[] = JHTML::_('select.option','1', JText::_( 'По адресу' ) );
				$statecoord[] = JHTML::_('select.option','2', JText::_( 'По координатам' ) );
				echo JHTML::_('select.genericlist',  $statecoord, $name = 'yandexcoord', $attribs = null, $key = 'value', $text = 'text', $selected = $this->editmarker->yandexcoord, $idtag = false, $translate = false );
			?>
				</div>
			</div>

					<div class="control-group dispadres" <?php echo $stylead;?>>
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_CITY' ); ?>:
				</div>
				<div class="controls">
					<input type="text" name="city_map_yandex" value="<?php echo $this->editmarker->city_map_yandex;?>">
				</div>
			</div>

					<div class="control-group dispadres" <?php echo $stylead;?>>
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_STREET' ); ?>
				</div>
				<div class="controls">
					 <input type="text" name="street_map_yandex" value="<?php echo $this->editmarker->street_map_yandex;?>">
				</div>
			</div>
			
					<div class="control-group dispcoords" <?php echo $stylecoo;?>>
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_COORD' ); ?>:
				</div>
				<div class="controls">
					 <?php
					// define modal options
					$modalOptions = array (
					'size' => array('x' => 700, 'y' => 700)
					
					);
					// load modal JavaScript
					JHTML::_('behavior.modal', 'a.modal', $modalOptions);
				?>
				<div style="display:inline" class="button2-left"><div class="image"><a href="<?php echo 'index.php?option=com_mapyandex&view=mapyandexajax&tmpl=component&cid[]='.$this->editmarker->id; ?>" class="modal"
				rel = "{
						handler: 'iframe'
						
						}">
						<?php echo JText::_( 'COM_MAPYANDEX_ONLY_OPENMODAL' ); ?>
						</a></div></div>

				</div>
			</div>
		

			<div class="control-group dispcoords" <?php echo $stylecoo;?>>
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_LNG' ); ?>
				</div>
				<div class="controls">
					 <input type="text" id="latitude" name="latitude_map_yandex" value="<?php echo $latitude;?>">
				 <input type="text" id="longitude" name="longitude_map_yandex" value="<?php echo $longitude;?>">
				</div>
			</div>
			

					<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_MARKER_ICON_WIDTH' ); ?>:
				</div>
				<div class="controls">
					<?php 
						$wih = json_decode($this->editmarker->wih);
					
						$width = array();
						$width[] = JHTML::_('select.option','100',100);
						$width[] = JHTML::_('select.option','150',150);
						$width[] = JHTML::_('select.option','200',200);
						$width[] = JHTML::_('select.option','250',250);
						$width[] = JHTML::_('select.option','350',350);
						$width[] = JHTML::_('select.option','400',400);
						echo JHTML::_('select.genericlist',  $width, $name = 'width', $attribs = 'autocomplete="off"', $key = 'value', $text = 'text', $selected = $wih[0], $idtag = false, $translate = false );
						
						
					?>
				</div>
			</div>

							<div class="control-group">
				<div class="control-label">
						<?php echo JText::_( 'COM_MAPYANDEX_MARKER_ICON_HEIGHT' ); ?>:
				</div>
				<div class="controls">
			<?php 

				$height = array();
				$height[] = JHTML::_('select.option','100', 100);
				$height[] = JHTML::_('select.option','150', 150);
				$height[] = JHTML::_('select.option','200', 200);
				$height[] = JHTML::_('select.option','250', 250);
				$height[] = JHTML::_('select.option','350', 350);
				$height[] = JHTML::_('select.option','400', 400);
				echo JHTML::_('select.genericlist',  $height, $name = 'height', $attribs = 'autocomplete="off"', $key = 'value', $text = 'text', $selected = $wih[1], $idtag = false, $translate = false );
			?>
				</div>
			</div>
		

							<div class="control-group">
				<div class="control-label">
						<?php echo JText::_( 'COM_MAPYANDEX_TEXTMAR' ); ?>:
				</div>
				<div class="controls">
<textarea name="misil" rows="10" cols="50"><?php echo $this->editmarker->misil;?></textarea>
				</div>
			</div>

								<div class="control-group">
				<div class="control-label">
						<?php echo JText::_( 'COM_MAPYANDEX_TEXTMARONCLICK' ); ?>:
				</div>
				<div class="controls">
<textarea name="misilonclick" rows="10" cols="50"><?php echo $this->editmarker->misilonclick;?></textarea>
				</div>
			</div>		
			
	
			<?php 
			$userpath = $this->params->get('userpathtoimg');
					
			if(empty($userpath)) {

				$userpath = '/images/sampledata/1/';
			}
			$imgarr = json_decode($this->editmarker->userimg);
			
			if (!empty($this->editmarker->userimg) && is_file($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$imgarr->startfile)) {
			
			?>
			<div class="control-group">
		
				
					<?php echo JText::_( 'COM_MAPYANDEX_USER_IMG' ); ?>:
					<br />
					<?php

					if(!empty($this->editmarker->userimg)) {
						$imgarr = json_decode($this->editmarker->userimg);
						$startfile = '<img src="'.JURI::root(true).$userpath.$imgarr->startfile.'">';
						$smallfile = '<img src="'.JURI::root(true).$userpath.$imgarr->smallfile.'">';
					} else {
						$startfile = '';
						$smallfile = '';
					}
					
					echo $startfile.'<br /><br />';
					echo $smallfile;
					
					?>
	

	
			
			</div>
			<?php } ?> 
			

			<?php if(!empty($this->editmarker->userimg) && is_file($_SERVER['DOCUMENT_ROOT'].JURI::root(true).$userpath.$imgarr->startfile)) { ?>
			
						<div class="control-group">

	
				
			<div class="key">			
				<div class="loader_img_for_input"></div>
				<div class="error_delete_file"></div>
				<div class="error_delete_file"></div>
				<button class="deletefile btn btn-small"><?php echo JText::_('COM_MAPYANDEX_USER_IMG_DELETE'); ?></button>
			</div>
				
	</div>
			<?php } ?> 





<?php

//Иконки и дизайн маркера

		echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table"><tbody>';
$option = array(
		0 => 'lightblueSmallPoint', 1 => 'whiteSmallPoint', 2 => 'greenSmallPoint', 3 => 'redSmallPoint', 4 => 'yellowSmallPoint', 
		5 => 'darkblueSmallPoint', 6 => 'nightSmallPoint', 7 => 'greySmallPoint', 8 => 'blueSmallPoint', 9 => 'orangeSmallPoint',
		10 => 'darkorangeSmallPoint', 11 => 'pinkSmallPoint', 12 => 'violetSmallPoint', 13 => 'airplaneIcon', 14 => 'arrowDownRightIcon',
		15 => 'arrowUpIcon', 16 => 'bankIcon', 17 => 'bicycleIcon', 18 => 'busIcon', 19 => 'carIcon', 20 => 'downhillSkiingIcon',
		21 => 'electricTrainIcon', 22 => 'gasStationIcon', 23 => 'houseIcon', 24 => 'metroKievIcon', 25 => 'metroYekaterinburgIcon',
		26 => 'phoneIcon', 27 => 'restaurauntIcon', 28 => 'skatingIcon', 29 => 'stadiumIcon', 30 => 'tailorShopIcon',
		31 => 'tireIcon', 32 => 'trolleybusIcon', 33 => 'turnRightIcon', 34 => 'workshopIcon', 
		35 => 'anchorIcon', 36 => 'arrowLeftIcon', 37 => 'attentionIcon', 38 => 'barIcon', 39 => 'bowlingIcon', 
		40 => 'cafeIcon', 41 => 'cellularIcon', 42 => 'dpsIcon', 43 => 'factoryIcon', 44 => 'gymIcon', 45 => 'keyMasterIcon', 
		46 => 'metroMoscowIcon', 47 => 'motobikeIcon', 48 => 'photographerIcon', 49 => 'shipIcon', 50 => 'skiingIcon',
		51 => 'storehouseIcon', 52 => 'theaterIcon', 53 => 'trainIcon', 54 => 'truckIcon', 55 => 'wifiIcon', 56 => 'arrowDownLeftIcon', 
		57 => 'arrowRightIcon', 58 => 'badmintonIcon', 59 => 'barberShopIcon', 60 => 'buildingsIcon', 61 => 'campingIcon', 
		62 => 'cinemaIcon', 63 => 'dryCleanerIcon', 64 => 'fishingIcon', 65 => 'hospitalIcon', 66 => 'mailPostIcon', 
		67 => 'metroStPetersburgIcon', 68 => 'mushroomIcon', 69 => 'pingPongIcon', 70 => 'shopIcon', 71 => 'smartphoneIcon', 
		72 => 'swimmingIcon', 73 => 'tennisIcon', 74 => 'tramwayIcon', 75 => 'turnLeftIcon', 76 => 'wifiLogoIcon' );



$this->editmarker->deficon = str_replace('Old','',$this->editmarker->deficon);

for($i=0; $i<count($option); $i++) {

if(($i % 10)==0) {
	echo '<tr valign="top">';
	}
$s = '';
if($option[$i]==$this->editmarker->deficon) {
	$s  = 'style="border-color:red;border:2px dotted red;" ';
}
    echo '<td align="center" '.$s.' colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:19px; height:20px; margin-bottom: 3px;"');
	
	echo '</td>';
if($option[$i]==$this->editmarker->deficon) {
	echo '<td align="center" width="" style="border-color:red;border:2px dotted red;border-left:none;" colname="col2"><input type="radio" checked value="'.$option[$i].'Old" id="deficon0" name="deficon" class="text_area"></td>'; 
}
else {
	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'Old" id="deficon0" name="deficon" class="text_area"></td>'; 
	//Old - старый стиль отображается как изображение
}
}


echo '</tr><tr valign="top">
        <td align="center" width="" colname="col1">
         
        </td>
        <td  colname="col2"></td>
        <td width="" colname="col3"></td>
        <td width="" colname="col4"></td>
        <td width="" colname="col5"></td>
        <td width="" colname="col6"></td>
      </tr></tbody></table>';

	  
echo '<h3>Значки для меток с текстом и изображениями</h3>';


echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table"><tbody><tr valign="top">
      </tr>';
$option = array(
		0 => 'blackStretchyIcon', 1 => 'brownStretchyIcon', 2 => 'yellowStretchyIcon', 3 => 'yellowStretchyIcon', 4 => 'whiteStretchyIcon', 
		5 => 'violetStretchyIcon', 6 => 'redStretchyIcon', 7 => 'pinkStretchyIcon', 8 => 'orangeStretchyIcon', 9 => 'nightStretchyIcon',
		10 => 'lightblueStretchyIcon', 11 => 'greyStretchyIcon', 12 => 'greenStretchyIcon', 13 => 'darkorangeStretchyIcon', 14 => 'darkgreenStretchyIcon' , 15 => 'darkblueStretchyIcon', 16 => 'blueStretchyIcon' );



for($i=0; $i<count($option); $i++) {

if(!preg_match('@Stretchy@s',$option[$i],$m) && preg_match('@Old@s',$option[$i],$m)) {
	
}


if(($i % 10)==0) {
	echo '<tr valign="top">';
	}
$s = '';	
if($option[$i]==$this->editmarker->deficon) {
	$s  = 'style="border-color:red;border:2px dotted red;" ';
}
    echo '<td '.$s.' align="center" width="" colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:78px; height:40px; margin-bottom: 3px;"');
	
	echo '</td>';
if($option[$i]==$this->editmarker->deficon) {
	echo '<td align="center" width="" style="border-color:red;border:2px dotted red;border-left:none;" colname="col2"><input type="radio" checked value="'.$option[$i].'" class="deficon" name="deficon"></td>'; 
}
else {
	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'" class="deficon" name="deficon"></td>'; 
}
  


	}

		

		
echo '</tr><tr valign="top">
        <td align="center" width="" colname="col1">
         
        </td>
        <td align="center" width="" colname="col2"></td>
        <td width="" colname="col3"></td>
        <td width="" colname="col4"></td>
        <td width="" colname="col5"></td>
        <td width="" colname="col6"></td>
        <td align="center" width="" colname="col1">
         
        </td>
        <td align="center" width="" colname="col2"></td>
        <td width="" colname="col3"></td>
        <td width="" colname="col4"></td>
        <td width="" colname="col5"></td>
        <td width="" colname="col6"></td>
      </tr></tbody></table>';


echo '<h3>дополнительные значки в которые не рекомендуется вставлять изображения</h3>';
		echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table">
		<tbody>';
$option = array(
		0 => 'blueAirportIcon', 1 => 'blueAttentionIcon', 2 => 'blueAutoIcon', 3 => 'blueBarIcon', 4 => 'blueBarberIcon', 
		5 => 'blueBeachIcon', 6 => 'blueBicycleIcon', 7 => 'blueBicycle2Icon', 8 => 'blueBookIcon', 9 => 'blueCarWashIcon',
		10 => 'blueChristianIcon', 11 => 'blueCinemaIcon', 12 => 'blueCircusIcon', 13 => 'blueCourtIcon', 14 => 'blueDeliveryIcon',
		15 => 'blueDiscountIcon', 16 => 'blueDogIcon', 17 => 'blueEducationIcon', 18 => 'blueEntertainmentCenterIcon', 19 => 'blueFactoryIcon', 20 => 'blueFamilyIcon',
		21 => 'blueFashionIcon', 22 => 'blueFoodIcon', 23 => 'blueFuelStationIcon', 24 => 'blueFuelStationIcon', 25 => 'blueGovernmentIcon',
		26 => 'blueHeartIcon', 27 => 'blueHomeIcon', 28 => 'blueHotelIcon', 29 => 'blueHydroIcon', 30 => 'blueInfoIcon',
		31 => 'blueLaundryIcon', 32 => 'blueLeisureIcon', 33 => 'blueMassTransitIcon', 34 => 'blueMedicalIcon', 
		35 => 'blueMoneyIcon', 36 => 'blueParkIcon', 37 => 'blueParkingIcon', 38 => 'bluePersonIcon', 39 => 'bluePocketIcon', 
		40 => 'bluePoolIcon', 41 => 'bluePostIcon', 42 => 'blueRailwayIcon', 43 => 'blueRapidTransitIcon', 44 => 'blueRepairShopIcon', 45 => 'blueRunIcon', 
		46 => 'blueScienceIcon', 47 => 'blueShoppingIcon', 48 => 'blueSouvenirsIcon', 49 => 'blueSportIcon', 50 => 'blueStarIcon',
		51 => 'blueTheaterIcon', 52 => 'blueToiletIcon', 53 => 'blueUnderpassIcon', 54 => 'blueVegetationIcon', 55 => 'blueVideoIcon', 56 => 'blueWasteIcon', 
		57 => 'blueWaterParkIcon', 58 => 'blueWaterwayIcon', 59 => 'blueWorshipIcon', 60 => 'blueZooIcon');



for($i=0; $i<count($option); $i++) {

if(($i % 10)==0) {
	echo '<tr valign="top">';
	}
$s = '';	
if($option[$i]==$this->editmarker->deficon) {
	$s  = 'style="border-color:red;border:2px dotted red;border-right:none;" ';
}
    echo '<td align="center" '.$s.' colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:51px; height:62px;max-width: none; margin-bottom: 3px;"');
	
	echo '</td>';
	
	if($option[$i]==$this->editmarker->deficon) {
		echo '<td align="center" style="border-color:red;border:2px dotted red;border-left:none;" width="" colname="col2"><input type="radio" checked value="'.$option[$i].'" class="deficon" name="deficon"></td>'; 
	}
	else {
		echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'" class="deficon" name="deficon"></td>'; 
	}

  


	}

		
echo '</tr></tbody></table>';

		
echo '</div>';
?>

          </fieldset>
        </div>

  
  
<input type="hidden" name="option" value="com_mapyandex" />
<input type="hidden" name="id" value="<?php echo $this->editmarker->id;?>" />
<input type="hidden" name="editmarket" value="1" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="controller" value="mapyandexmetki" />
<input type="hidden" name="view" value="mapyandexmetki" />
</form>