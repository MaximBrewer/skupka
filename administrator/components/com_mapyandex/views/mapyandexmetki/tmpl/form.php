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
?>

  	<form action="index.php" method="post" name="adminForm" id="adminForm">	
	<?php $document = JFactory::getDocument();?>


<?php
	$lineika = '';
	$minimap = '';
	$sputnik = '';
	$search = '';
	$scale = '';
	$styleel = '';
	$stylecoo = '';
	$stylead = '';
	$element = '';
	
?>

<?php

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
			$j(\'[name="yandexbutton"]\').change(function(){
		
		v = $j(this).children("option:selected").val()
		if(v == 2) {

			$j(".elyandex").fadeOut("slow");
			
		}
		else {
			$j(".elyandex").fadeIn("slow");
		}
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
Joomla.submitbutton = function(task)
{

	var vt = $j(\'[name="yandexcoord"] option:selected\').val();

		
        if (task == \'\')
        {
                return false;
        }
        else
        {
		
			if(task == \'mapyandexmetki.save\') {
						var isValid=true;
					
						var form = document.adminForm;


						if(vt == 1) {
							if (form.city_map_yandex.value == "") {
								alert( "'.JText::_( "COM_MAPYANDEX_ERROR_CITY", true ).'" );
							
							} else if (form.street_map_yandex.value == "") {
								alert( "'.JText::_( "COM_MAPYANDEX_ERROR_STREET", true ).'" );
							}
					
							else {
								Joomla.submitform(task);
							}
						} else if(vt == 2) {
							if (form.latitude_map_yandex.value == "") {
								alert( "'.JText::_( "COM_MAPYANDEX_ERROR_LATITUDE", true ).'" );
							
							} else if (form.longitude_map_yandex.value == "") {
								alert( "'.JText::_( "COM_MAPYANDEX_ERROR_LONGITUDE", true ).'" );
							}
					
							else {
								Joomla.submitform(task);
							}
						
						} 
			} else {
							Joomla.submitform(task);
			}

        }
}
		//-->';
	
$document->addScriptDeclaration($script);
?>
<script type="text/javascript">
function addfiletolist(startfile,smallfile) {
	$j = jQuery.noConflict();

	$j(function(){
		$j('#smallfile,#startfile').remove();

		
		var addimgsm = $j("<input>");
		var addimgst = $j("<input>");
		addimgsm.attr({
				name:"smallfile",
				id:"smallfile",
				type:"hidden",
				value:smallfile
		});
		addimgst.attr({
				name:"startfile",
				id:"startfile",
				type:"hidden",
				value:startfile
		});

			
		$j("#adminForm").append(addimgsm);
		$j("#adminForm").append(addimgst);
		
	});
}


function getElementsByClassName(node,classname) {
  if (node.getElementsByClassName) { // use native implementation if available
    return node.getElementsByClassName(classname);
  } else {
    return (function getElementsByClass(searchClass,node) {
        if ( node == null )
          node = document;
        var classElements = [],
            els = document.getElementsByTagName("*"),
            elsLen = els.length,
            pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)"), i, j;

        for (i = 0, j = 0; i < elsLen; i++) {
          if ( pattern.test(els[i].className) ) {
              classElements[j] = els[i];
              j++;
          }
        }
        return classElements;
    })(classname, node);
  }
}


$j = jQuery.noConflict();

$j(function(){
// для файлов к предложению 
	
	var loaderfilemessage;
	var videoFileMessage;

	// загружаем файл по событию submit
		videoFileMessage = document.getElementById("uploadfile");
		if(videoFileMessage) {
			videoFileMessage.onchange = function () {
				$j('.dialogsend .file_name_error').hide();
				$j('.dialogsend .myfile').show().text('('+this.value+')');
			}
		}
		
	$j("#uploadMessageFile").submit(function(e){
		
		var checked = $j('input[name="deficon"]:checked');
		if(!checked.hasClass('deficon')) {
		
			$j('.myfile + div.error').remove();
			$j('.myfile').after('<div class="error" style="display:none;"><?php echo JText::_( "COM_MAPYANDEX_ERROR_TYPE_MARKER_FOR_IMG", true ); ?></div>');
			$j('.myfile + div.error').fadeIn(200);
			return false;
		} else {
			$j('.myfile + div.error').fadeOut(200).remove();
		}
		
		if (videoFileMessage) {

		if($j('.dialogsend #videoname').val() == '') {
			$j('.dialogsend #videoname_error').show();
			return false;
		}
		else if($j('textarea[name="videodescription"]').val() == '') {
			$j('#videodescription_error').show();
			return false;
		}
			if(!$j.browser.msie) {
				var fileToUpload = videoFileMessage.files[0]; 
				
				if(!fileToUpload) {

					$j('.dialogsend .file_name_error').show();
					return false;
				}
				$j('.loader_img,.desc_load').show();
				$j('.loader_img_success,.submitb').hide();
				var fs = fileToUpload.size/(1024 * 1024);

				type = fileToUpload.type.split("/");
				/*type2 = fileToUpload.name.split('.').pop();
				console.log(fileToUpload.name);
				console.log(type2);
				console.log(type[1]);*/
				switch(type[1]) {
						case "jpeg":
						break;
						case "jpg":
						break;
						case "png":
						break;
						case "gif":
						default:
						$j('.dialogsend #file_type_error').show();
						return false;
						break;
				}

				if(fs > 10) {
					$j('.dialogsend #file_size_error').show();
					return false;
				}
				var fn = fileToUpload.name;

			}
		} 
		
		$j('.dialogsend #file_size_error,.dialogsend #file_name_error,.dialogsend #videodescription_error,.dialogsend #videoname_error').hide();
		
		var randIDS = Math.random();
		var p = $j(this);

		p.attr('target','filemessage');
		// в этот же файл(index.php) загружаем наш файл
		p.attr('action','<?php echo JURI::root(true);?>/administrator/index.php?option=com_mapyandex&task=loadimg&format=row');
		id_mess = $j('.zend_form').attr('data-idmess');
		
		$j('#chernovikupdate').val(id_mess);
		
		// если первый раз, то создаём нужные элементы 
		if($j("#filemessage").length == 0){
	

			
			// if chernovik update file
			var id_mess_hidden = $j("<input>");
			id_mess_hidden.attr({
				name:"chernovikupdate",
				id:"chernovikupdate",
				type:"hidden",
				value:id_mess
			});
			$j("#uploadMessageFile").prepend(id_mess_hidden);
			
			u = $j('#tabs').attr('data-sid');
			
			var sid = $j("<input>");
			sid.attr({
				name:"sid",
				id:"sid",
				type:"hidden",
				value:u
			});
			$j("#uploadMessageFile").prepend(sid);
			
			var frame = $j("<iframe>");
			frame.attr({
			name:'filemessage',
			id:'filemessage',
			action:'about:blank',
			border:0
			}).css('display','none');
			p.after(frame);
		} else {

			
		
		}
		
		$j('.dialogsend .avatar-buttons').hide('slow');
		$j('.dialogsend .myvideo').show().text(fn);
		

	});

});
</script>
<div>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_MAPYANDEX_NEWYMAR' ); ?></legend>
				
		<div class="span5" style="margin-left:0px;">
		<table class="admintable"  style="width: 90%;">

				
			<div class="control-group">
				<div class="control-label">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMARKER' ); ?>:
				</div>
				<div class="controls">
			<input type="text" name="name_marker" id="keyword" value="" style="width: 70%;"/>
				</div>
			</div>

					<td align="left" colspan="2">
				<label for="editmarker" style="width:100%;min-width:100%;max-width:100%">
					<?php echo JText::_( 'COM_MAPYANDEX_NAMEMAP' ); ?>:
				</label>

			 <?php echo $this->allmaps;?>
			</td>
			</tr>


						<tr>
			<td align="left" colspan="2">
				<label for="foobar" style="width:100%;min-width:100%;max-width:100%">
					<?php echo JText::_( 'COM_MAPYANDEX_SEACRHMETHOD' ); ?>:
				</label>


			<?php 
			
				$statecoord[] = JHTML::_('select.option','1', JText::_( 'По адресу' ) );
				$statecoord[] = JHTML::_('select.option','2', JText::_( 'По координатам' ) );
				echo JHTML::_('select.genericlist',  $statecoord, $name = 'yandexcoord', $attribs = 'autocomplete = "off"', $key = 'value', $text = 'text', $selected = 1, $idtag = false, $translate = false );
			?>
			</td>
		</tr>

		<tr class="dispadres">
			<td width="100" align="left" colspan="2">
				<label for="foobar" style="width:100%;min-width:100%;max-width:100%">
					<?php echo JText::_( 'COM_MAPYANDEX_CITY' ); ?>:
				</label>

				 <input type="text" name="city_map_yandex" value="" style="width: 70%;">
			</td>
		</tr>
			
			
		<tr class="dispadres">
			<td width="100" align="left" colspan="2">
				<label for="foobar" style="width:100%;min-width:100%;max-width:100%">
					<?php echo JText::_( 'COM_MAPYANDEX_STREET' ); ?>:
				</label>

				 <input type="text" name="street_map_yandex" value="" style="width: 70%;">
			</td>
		</tr>
		
			<tr class="dispcoords" style="display:none;">
					<td align="left" colspan="2">
				<label for="foobar">
					<?php echo JText::_( 'COM_MAPYANDEX_COORD' ); ?>:
				</label>

				<?php
					// define modal options
					$modalOptions = array (
					'size' => array('x' => 500, 'y' => 500)
					
					);
					// load modal JavaScript
					JHTML::_('behavior.modal', 'a.modal', $modalOptions);
				?>
				<div style="display:inline" class="button2-left"><div class="image"><a href="<?php echo 'index.php?option=com_mapyandex&view=mapyandexajax&tmpl=component&cid[]=1'; ?>" class="modal"
				rel = "{
						handler: 'iframe'
						
						}">
						<?php echo JText::_( 'COM_MAPYANDEX_ONLY_OPENMODAL' ); ?>
						</a></div></div>
				
			</td>
			</tr>
			
			

			<tr class="dispcoords" style="display:none;">
					<td width="100" align="left" colspan="2">
				<label for="foobar" style="width:100%;min-width:100%;max-width:100%">
					<?php echo JText::_( 'COM_MAPYANDEX_LNG' ); ?>:
				</label>

				 <input type="text" id="latitude" name="latitude_map_yandex" value="" style="width: 70%;">
				 <input type="text" id="longitude" name="longitude_map_yandex" value="" style="width: 70%;">
			</td>
			</tr>

		
			</table>
			</div>
			<div class="span6 fltlft">
				<table>
						<tr>
				<td width="100" align="left" colspan="2">
					<label for="foobar" style="width:100%;min-width:100%;max-width:100%">
						<?php echo JText::_( 'COM_MAPYANDEX_TEXTMAR' ); ?>:
					</label>


				<textarea name="misil" rows="5" cols="50"></textarea>

				</td>
			</tr>
					<tr>
				<td width="100" align="left" colspan="2">
					<label for="foobar" style="width:100%;min-width:100%;max-width:100%">
						<?php echo JText::_( 'COM_MAPYANDEX_TEXTMARONCLICK' ); ?>:
					</label>
	

				<textarea name="misilonclick" rows="5" cols="50"></textarea>

				</td>
			</tr>

			
				</table>
			</div>
	</fieldset>

<div class="clr"></div>

 </div>
<div class="span12" style="margin-left:0px;">
          <fieldset class="adminform">
   
		  
<?php



echo '<h3>Значки для меток с текстом и изображениями</h3>';


echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table"><tbody><tr valign="top">
      </tr>';
$option = array(
		0 => 'blackStretchyIcon', 1 => 'brownStretchyIcon', 2 => 'yellowStretchyIcon', 3 => 'yellowStretchyIcon', 4 => 'whiteStretchyIcon', 
		5 => 'violetStretchyIcon', 6 => 'redStretchyIcon', 7 => 'pinkStretchyIcon', 8 => 'orangeStretchyIcon', 9 => 'nightStretchyIcon',
		10 => 'lightblueStretchyIcon', 11 => 'greyStretchyIcon', 12 => 'greenStretchyIcon', 13 => 'darkorangeStretchyIcon', 14 => 'darkgreenStretchyIcon' , 15 => 'darkblueStretchyIcon', 16 => 'blueStretchyIcon' );



for($i=0; $i<count($option); $i++) {

if(($i % 10)==0) {
	echo '<tr valign="top">';
	}
    echo '<td align="center" width="" colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:78px; height:40px; margin-bottom: 3px;"');
	
	echo '</td>';

	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'" class="deficon" name="deficon"></td>'; 

  


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

	
  
echo '<h3>Значки в которые не рекомендуется вставлять изображения</h3>';
		echo '<table cellspacing="3" cellpadding="0" border="0" style="background-color:white" class="table">
		<tbody>';
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



for($i=0; $i<count($option); $i++) {

if(($i % 5)==0) {
	echo '<tr valign="top">';
	}
    echo '<td align="center" width="" colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:27px; height:26px;max-width:27px; margin-bottom: 3px;"');
	
	echo '</td>';

	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'Old" id="deficon0" name="deficon" class="text_area"></td>'; 

	//Old - старый стиль отображается как изображение


	}

		
echo '</tr></tbody></table>';

 
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
    echo '<td align="center" width="" colname="col1">';
	echo JHTML::_('image', 'administrator/components/com_mapyandex/assets/images/deficon/'.$option[$i].'.png','','style="width:51px; height:62px;max-width: none; margin-bottom: 3px;"');
	
	echo '</td>';

	echo '<td align="center" width="" colname="col2"><input type="radio" value="'.$option[$i].'" id="deficon0" name="deficon" class="text_area"></td>'; 

  


	}

		

		
echo '</tr></tbody></table>';
	  
?>

          </fieldset>
        </div>
		
<div class="span11" style="margin-left:0px;">
          <fieldset class="adminform">


<?php

if($this->canwrite['perms'] !==1) {

if(!empty($this->canwrite['notperms'])) {
JError::raiseWarning( 100, $this->canwrite['notperms'] );

}
if($this->canwrite['isconnectedftp']) {

JFactory::getApplication()->enqueueMessage('Имеется правильное подключение по FTP, - FTP будет использоваться для загрузки изображений к меткам!', 'Success');
} else {
JError::raiseWarning( 100, $this->canwrite['noftp'] );
}


}


$scriptjs = '
function getElementsByClassName(node,classname) {
  if (node.getElementsByClassName) { // use native implementation if available
    return node.getElementsByClassName(classname);
  } else {
    return (function getElementsByClass(searchClass,node) {
        if ( node == null )
          node = document;
        var classElements = [],
            els = document.getElementsByTagName("*"),
            elsLen = els.length,
            pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)"), i, j;

        for (i = 0, j = 0; i < elsLen; i++) {
          if ( pattern.test(els[i].className) ) {
              classElements[j] = els[i];
              j++;
          }
        }
        return classElements;
    })(classname, node);
  }
}
';

$document->addScriptDeclaration($scriptjs);
?>
<input type="hidden" name="option" value="com_mapyandex" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="savenew" />
<input type="hidden" name="view" value="mapyandexmetki" />
<input type="hidden" name="controller" value="mapyandexmetki" />
</form>		


			<div class="clrline" style="clear:both;margin:10px 0 10px 0;"></div>
			<div class="loader_img"></div>
			<div class="loader_img_success">Загрузка успешно завершена!</div>
			<div class="desc_load">Идёт загрузка... Дождитесь загрузки файла!</div>


			<form action="/user/loadfile"  enctype="multipart/form-data" method="post" id="uploadMessageFile" target="filemessage">

					<div class="dialogsend" style="margin:10px;">
						<div class="myfile"></div>
						<div class="file_name_error" style="display:none;">Вы не выбрали файл!</div>
						<div id="derectorynoperms" style="display:none;"><?php echo JText::_('COM_MAPYANDEX_NO_PERMS_FILE');?></div>


							<div class="upload">
								Выбрать изображение 

								<input autocomplete="off" type="file" id="uploadfile" name="userVideo" value="Выберите файл" />
								
								</div>
							<input type="hidden" id="chernovikupdate" name="chernovikupdate" value="1" />
							<div class="upload">
								Загрузить...
								<input type="submit" id="uploadfilewithmessage" class="upload" name="upload-btn" value="Загрузить..." />
							</div>

						
					</div>
			  
			</form>	
          </fieldset>
        </div>


<div class="clr"></div>