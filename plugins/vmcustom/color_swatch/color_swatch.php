<?php
error_reporting(E_ALL & ~E_NOTICE);
/*------------------------------------------------------------------------
* Color Swatch Plugin for Virtuemart
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 2.1.0
-------------------------------------------------------------------------*/

defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

if (!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');

class plgVmCustomColor_swatch extends vmCustomPlugin {

	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

		$varsToPush = array(	'name'=>array('','char'),
								'title'=>array('','char'),
						    	'status'=>array('','int'),
								'order_image'=>array('','int'),
								'thumbnail'=>array('','char'),
								'tooltip'=>array('','char'),
								'images'=>array('','char'),
								'product_name'=>array('', 'char'),
								
								'style_thumbnail'=>array('', 'int'),
								'widthth'=>array('', 'char'),
								'heightth'=>array('', 'char'),
								'loadjs'=>array('', 'int'),
								'widths'=>array('', 'char'),
								'heights'=>array('', 'char'),
								'show_type'=>array('', 'int'),
								'show_title_cs'=>array('', 'int'),
								'show_tooltip'=>array('', 'int'),
								'usezoom'=>array('', 'int'),
								'widthp'=>array('', 'char'),
								'heightp'=>array('', 'char')
		);

		$this->setConfigParameterable('customfield_params',$varsToPush);

	}
	
	function yt_image_resize($url, $width = NULL, $height = NULL, $crop = false, $quality=95, $folder) {

		//if gd library doesn't exists - output normal image without resizing.
		if (function_exists("gd_info") == false) {
			$image_array = array(
				'url'    => $url,
				'width'  => $width,
				'height' => $height,
				'type'   => ''
			);
			return $image_array;
		}
		
		$thumb_folder = $folder;
		if (!is_dir(JPATH_SITE .'/'. $thumb_folder)) {
			mkdir(JPATH_SITE .'/'. $thumb_folder, 0777);
		}

		$fileExtension = strrchr($url, ".");

		$thumb_width = $width;
		$thumb_height = $height;

		if ($url!=null) {
			$url = $url;
		} else {
			$image_array = array(
				'url'    => $url,
				'width'  => $width,
				'height' => $height,
				'type'   => ''
			);
			
			return $image_array;
		}
		
		$imageData = @getimagesize($url);
		
		$owidth    = $imageData[0];
		$oheight   = $imageData[1];

		if ( $imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg') {
			$image = @imagecreatefromjpeg($url);
		} elseif ($imageData['mime'] == 'image/gif') {
			$image = @imagecreatefromgif($url);
		} else {
			$image = @imagecreatefrompng($url);
		}
		
		// check if the proper image resource was created
		if (!$image) {
			
			$image_array = array(
				'url'    => $url,
				'width'  => $thumb_width,
				'height' => $thumb_height,
				'type'   => $fileExtension
			);
			return $image_array;
		}
		
		$original_aspect = $owidth / $oheight;
		$thumb_aspect = $thumb_width / $thumb_height;
		
		if ($crop) {
			$thumb_path = basename($url, $fileExtension) . $fileExtension; // $file is set to "index";
			$thumb_path = $thumb_folder .'/'. $thumb_path;
			
			if ($original_aspect >= $thumb_aspect) {
				// If image is wider than thumbnail (in aspect ratio sense)
				$new_height = $thumb_height;
				$new_width = $owidth / ($oheight / $thumb_height);
			} else {
				// If the thumbnail is wider than the image
				$new_width = $thumb_width;
				$new_height = $oheight / ($owidth / $thumb_width);
			}
			$thumb = imagecreatetruecolor($thumb_width, $thumb_height);
			$color = imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 255, 255, 255, 127));
			imagefill($thumb, 0, 0, $color);
			imagesavealpha($thumb, true);
			// Resize and crop
			imagecopyresampled($thumb, $image, 0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
					0 - ($new_height - $thumb_height) / 2, // Center the image vertically
					0, 0, $new_width, $new_height, $owidth, $oheight);
		} else {
			$new_width = $thumb_width;
			$new_height = (int) ( 1 / $original_aspect * $new_width);
			$thumb_path = basename($url, $fileExtension) . $fileExtension; // $file is set to "index";
			$thumb_path = $thumb_folder .'/'. $thumb_path;
			
			$thumb = imagecreatetruecolor($new_width, $new_height);
			$color = imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 255, 255, 255, 127));
			imagefill($thumb, 0, 0, $color);
			imagesavealpha($thumb, true);
			// Resize and crop
			imagecopyresampled($thumb, $image, 0, // Center the image horizontally
					0, // Center the image vertically
					0, 0, $new_width, $new_height, $owidth, $oheight);
		}
		if ($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg') {
			imagejpeg($thumb, $thumb_path, $quality);
		} elseif ($imageData['mime'] == 'image/gif') {
			imagegif($thumb, $thumb_path, $quality);
		} else {
			imagepng($thumb, $thumb_path, 9);
		}
		$thumb_url = $thumb_folder.'/' . basename($thumb_path, $fileExtension) . $fileExtension; // $file is set to "index";
		
		$image_array = array(
			'url' => $thumb_url,
			'width' => $thumb_width,
			'height' => $thumb_height,
			'type' => $fileExtension
		);
		return $image_array;
	}

	// get product param for this plugin on edit
	function plgVmOnProductEdit($field, $product_id, &$row,&$retValue) {
		
		if ($field->custom_element != $this->_name) return '';
		if(!defined('VM_VERSION') or VM_VERSION < 3){
		  $this->parseCustomParams ($field);
		  $paramName = 'custom_param';
		} else {
		  $paramName = 'customfield_params';
		}
		$customId = $field->virtuemart_customfield_id;

		$model = VmModel::getModel('product');
		$product = $model->getProduct($product_id);
		
		$path = '../images/stories/virtuemart/color_swatch/thumbnail';
		$pathimages = '../images/stories/virtuemart/color_swatch/images/'.$customId;
		if(!is_dir($pathimages))
			mkdir($pathimages, 0777, true);
		
		$images_arr = $this->list_files($pathimages, $field->order_image);
		$images = array();
		foreach($images_arr as $key => $value ){
			if(@is_array(getimagesize($pathimages.'/'.$value))){
				$images[$key] = $value;
			} 
		}
		
		// Check checked thumbnail type @Ken
		$typethumb = 'color';
		if( strpos($field->thumbnail, 'thumbnail_') !== false ){
			$typethumb = 'image';
		}
		// create thumbnail get by width,height @Ken
		$path_thumb = JPATH_SITE.'/images/stories/virtuemart/color_swatch/images/'.$customId.'/thumbnail';
		$path_image = JPATH_SITE.'/images/stories/virtuemart/color_swatch/images/'.$customId.'/'.$field->thumbnail;
		
		if($images){
			if(is_dir($pathimages)){
				if(!is_dir($path_thumb)){
					mkdir($path_thumb, 07777, true);
					foreach($images as $key=>$value){
						$path_image = JPATH_SITE.'/images/stories/virtuemart/color_swatch/images/'.$customId.'/'.$value;
						$this->yt_image_resize($path_image, $field->widthth, $field->heightth, true, 95, $path_thumb);
					}
				}else{
					foreach($images as $key=>$value){
						$path_image = JPATH_SITE.'/images/stories/virtuemart/color_swatch/images/'.$customId.'/'.$value;
						$this->yt_image_resize($path_image, $field->widthth, $field->heightth, true, 95, $path_thumb);
					}
				}
			}
		}
		
		
		$key = 0;
		$array = array();
		if($customId){
		$html ='
				<fieldset style="width: 550px">
				<legend>'.JText::_("CMS_COLOR_INPUTDATA").'</legend>
					<div id="colorswatch_'.$customId.'" style="float: left; width:50%">
					<label>'.JText::_('CMS_COLOR_TITLE').'</label>
					
					<input style="border: 1px solid #1E90FF;height: 19px;width: 100px" type="text" value="'.$field->title.'" size="10" name="customfield_params['.$row.'][title]"><br>

					<label>'.JText::_('CMS_COLOR_STATUS').'</label>
					<input id="instock" type="radio" value="1" size="10" name="status_'.$customId.'">'.JText::_('CMS_COLOR_INSTOCK').'
					<input id="outstock" type="radio" value="0" size="10" name="status_'.$customId.'">'.JText::_('CMS_COLOR_OUTSTOCK').'
					<input id="status" type="hidden" value="'.$field->status.'" size="10" name="customfield_params['.$row.'][status]"><br>
												
					<label>'.JText::_('CMS_COLOR_THUMBNAIL').'</label>
					<input id="typethumbimg" class="typethumbimg" type="radio" name="thumbnail_type_'.$customId.'" value="image" size="10">'.JText::_('CMS_COLOR_IMAGE').'
					<input id="typethumbcolor" type="radio" name="thumbnail_type_'.$customId.'" value="pickcolor" size="10">'.JText::_('CMS_COLOR_PICKCOLOR').'<br>
							
					<input id="thumbnailimg" type="hidden" name="customfield_params['.$row.'][thumbnail]" value="'.$field->thumbnail.'">	
					<input id="thumbstype_'.$customId.'" type="hidden" value="'.$typethumb.'">
					
					<div id="thimage">
						<form method="post" enctype="multipart/form-data" action="uploads/upload.php">
				    		<input type="file" name="thimages_'.$customId.'" id="thimages_'.$customId.'"  />
				    		<button type="submit" id="thbtn_'.$customId.'">Upload Files!</button>
				    	</form>
					
						<div id="thresponse_'.$customId.'">
						</div>
						<div id="thshow-images_'.$customId.'">
						</div>
					</div>
					';
					if (!empty($images)){
						foreach ($images as $image)
						{	if(substr($image,0,6)=='thumbn'){
							$key++;
							array_push($array, $key);
							$html .='<span id="cms-list-thumb">
									<input type="hidden" value="'.$image.'" name="delete_file" id="delete_file_'.$key.$customId.'" />
								<label id="cms-img_'.$key.$customId.'" class="keys">
									<img style="width:60px;height:60px" width="60px" height="60px" src="../images/stories/virtuemart/color_swatch/images/'.$customId.'/'.$image.'">
								</label>
								<input type="button" value="Delete image" id="delete_'.$key.$customId.'" class="delete_button"/></span>';
							}
						}
					}
					
					// Order images check
					$order_images_check = array('1'=>'', '2'=>'', '3'=>'');
					if ($field->order_image == 2) {
						$order_images_check[2] = 'checked';
					} elseif ($field->order_image == 3) {
						$order_images_check[3] = 'checked';
					} else {
						$order_images_check[1] = 'checked';
					}
					
					$html .= '
					<input id="thpickcolor" type="text" value="'.$field->thumbnail.'" size="10" name="thpickcolor"><br>
					
					<label>'.JText::_('CMS_COLOR_TOOLTIP').'</label><br>
					<textarea col="10" style="width: 230px;" value="'.$field->tooltip.'" row="5" name="customfield_params['.$row.'][tooltip]">'.$field->tooltip.'</textarea><br><br><br><br>

					<input type="hidden" value="'.$pathimages.'" size="10" name="customfield_params['.$row.'][images]">
			
					<div id="main_'.$customId.'">
						<span>'.JText::_("CMS_COLOR_UPLOAD_IMAGES").'</span>
						<form method="post" enctype="multipart/form-data" action="uploads/upload.php">
				    		<input type="file" name="images_'.$customId.'" id="images_'.$customId.'" multiple />
				    		<button type="submit" id="btn_'.$customId.'">Upload Files!</button>
				    	</form>
					
						<div id="response_'.$customId.'">
						</div>
						<div id="show-images_'.$customId.'">
						</div>
					</div>
					<input type="hidden" value="'.$product->product_name.'" name="customfield_params['.$row.'][product_name]">
					</div>
					<div style="float: right; border: 1px solid #1E90FF; width: 49%; padding-left: 3px;">
						<p style="margin:5px 0; font-weight:bold;">'.JText::_("CMS_COLOR_GALLERY").'</p>
						<div>
							<label style="margin-bottom:5px;">'.JText::_('CMS_COLOR_ORDER').'</label>
							<input id="order_name" type="radio" value="1" size="10" name="order_image_'.$customId.'" ' . $order_images_check[1] . '>' . JText::_('CMS_COLOR_ORDER_NAME').'
							<input id="order_time" type="radio" value="2" size="10" name="order_image_'.$customId.'" ' . $order_images_check[2] . '>' . JText::_('CMS_COLOR_ORDER_TIME').'
							<input id="order_random" type="radio" value="3" size="10" name="order_image_'.$customId.'"' . $order_images_check[3] . '>' . JText::_('CMS_COLOR_ORDER_RANDOM').'
							<input id="order_image_'.$customId.'" type="hidden" value="'.$field->order_image.'" size="10" name="customfield_params['.$row.'][order_image]"><br>
						</div>
						';
						if (!empty($images)){
							foreach ($images as $image)
							{	if(substr($image,0,6)!='thumbn'){
								$key++;
								array_push($array, $key);
								$html .='<span id="cms-list-image">
										<input type="hidden" value="'.$image.'" name="delete_file" id="delete_file_'.$key.$customId.'" />
									<label id="cms-img_'.$key.$customId.'" class="keys">
										<img style="width: 60px;height: 60px" src="../images/stories/virtuemart/color_swatch/images/'.$customId.'/'.$image.'">
									</label>
									<input type="button" value="Delete image" id="delete_'.$key.$customId.'" class="delete_button"/></span>';
								}
							}
						}
						$html.='		
					</div>
				</fieldset>
				
				<script src="../plugins/vmcustom/color_swatch/assets/js/colpick.js"></script>
				<link rel="stylesheet" href="../plugins/vmcustom/color_swatch/assets/css/colpick.css">
				<script>
					(function ($) {
						
						// process status ---------------------------------------------------------------
						var vlstatus = $("#colorswatch_'.$customId.' > #status").val();
						if(vlstatus == 1) $("#colorswatch_'.$customId.' > #instock").attr("checked", true);
						else{ 
							$("#colorswatch_'.$customId.' > #outstock").attr("checked", true);
							$("#colorswatch_'.$customId.' > #status").val("0");
						}
						$("#colorswatch_'.$customId.' > input[name=status_'.$customId.']").change(function(){ 
					        if( $(this).is(":checked") ){ 
					            var val = $(this).val();
								$("#colorswatch_'.$customId.' > #status").val(val);
					        }
					    });
						
						// process oder_image ---------------------------------------------------------------
						var vlorder_image = $("#order_image_'.$customId.'").val();
						if (!vlorder_image) {
							$("#order_image_'.$customId.'").val("1");
						}
						$("input[name=order_image_'.$customId.']").change(function(){
					        if( $(this).is(":checked") ){ 
					            var val = $(this).val();
								$("#order_image_'.$customId.'").val(val);
					        }
					    });
							
						// process thumbnail ---------------------------------------------------------------
						var vlthumb = $("#colorswatch_'.$customId.' > #thumbnailimg").val();
								
						var vlthumb2 = vlthumb.substr(0,6);

						if(vlthumb2 == "" || vlthumb2 == "thumbn") {
							$("#colorswatch_'.$customId.' > #thimage").show();
							$("#colorswatch_'.$customId.' > #cms-list-thumb").show();
							$("#colorswatch_'.$customId.' > #thpickcolor").hide();
							$("#colorswatch_'.$customId.' > .typethumbimg").prop("checked", true);
						} else {
							$("#colorswatch_'.$customId.' > #thimage").hide();
							$("#colorswatch_'.$customId.' > #thpickcolor").show();
							$("#colorswatch_'.$customId.' > #cms-list-thumb").hide();
							$("#colorswatch_'.$customId.'").find("#typethumbcolor").prop("checked", true);	
						}
						
						// check thumb @ken
						var thumb_vl = $("#colorswatch_'.$customId.' > #thumbstype_'.$customId.'").val();
						if(thumb_vl == "image"){
							$("#colorswatch_'.$customId.' > #typethumbimg").attr("checked", true);
							$("#colorswatch_'.$customId.' > #thimage").show();
							$("#colorswatch_'.$customId.' > #thpickcolor").hide();
							$("#colorswatch_'.$customId.' > #cms-list-thumb").show();
									 
						}else{ 
							$("#colorswatch_'.$customId.' > #typethumbcolor").attr("checked", true);
							$("#colorswatch_'.$customId.' > #thimage").hide();
							$("#colorswatch_'.$customId.' > #cms-list-thumb").hide();
							$("#colorswatch_'.$customId.' > #thpickcolor").show();	
						}	
						
												
						
						$("#colorswatch_'.$customId.' > input[name=thumbnail_type_'.$customId.']").change(function(){ 
							
					        if( $(this).is(":checked") ){ 
					            var val = $(this).val();
								if(val == "image"){
									 $("#colorswatch_'.$customId.' > #thimage").show();
									 $("#colorswatch_'.$customId.' > #thpickcolor").hide();
									 $("#cms-list-thumb").show();
									 var valueimg = $("#colorswatch_'.$customId.' > #cms-list-thumb").find("img").size();
									 if(valueimg){
										var thumbname = $("#colorswatch_'.$customId.' > #cms-list-thumb > input:eq(0)").val();
										$("#colorswatch_'.$customId.' > #thumbnailimg").val(thumbname);
									 }
								} else {
									 $("#colorswatch_'.$customId.' > #thimage").hide();
									 $("#colorswatch_'.$customId.' > #cms-list-thumb").hide();
									 $("#colorswatch_'.$customId.' > #thpickcolor").show().val("").css({
									 	"border-right":"20px solid #444",
									 	"border-left": "1px solid #444",
									 	"border-top": "1px solid #444",
									 	"border-bottom" : "1px solid #444"
									 });	
					        	}	
							}
					    });
									 		
									 		
						// process pick color ---------------------------------------------------------------
						$("#colorswatch_'.$customId.' > #thpickcolor").colpick({
							layout:"hex",
							submit:0,
							colorScheme:"dark",
							onChange:function(hsb,hex,rgb,el,bySetColor) {
								$(el).css("border-color","#"+hex);
								if(!bySetColor) $(el).val(hex);
								$("#colorswatch_'.$customId.' > #thumbnailimg").val(hex);
							}
						}).keyup(function(){
							$(this).colpickSetColor(this.value);
						});
								
						// process upload ajax ---------------------------------------------------------------
						var formdata = false,
						formdata2 = false,
						path = "'.$pathimages.'",
						folder = "'.$field->widthth.'x'.$field->heightth.'",
						input = document.getElementById("images_'.$customId.'"),
						input2 = document.getElementById("thimages_'.$customId.'"),
						key = '.json_encode($array).';
								
						$.each( key, function (key, val){
							
							$("#delete_"+val+'.$customId.').click(function (){
								var file = $("#delete_file_"+val+'.$customId.').val();
								$.ajax({
							      type:"POST",
							      url:"../plugins/vmcustom/color_swatch/uploads/del.php?path="+path,
							      data:{file:file, folder:folder},
							      success: function (res){
										$("#delete_"+val+'.$customId.').after("<span style=color:red>Deleted</span>");
										$("#delete_"+val+'.$customId.').remove();
										$("#cms-img_"+val+'.$customId.').remove();
										
							      }
							    });
							});
						});
						function showUploadedItem(source){
					  		var list = document.getElementById("image-list"),
						  		li   = document.createElement("li"),
						  		img  = document.createElement("img"),
								name  = document.createElement("span");
					  		img.src = source;
					  		li.appendChild(img);
							list.appendChild(li);
						}   
					
						if(window.FormData){
					  		formdata = new FormData();
							formdata2 = new FormData();
					  		document.getElementById("btn_'.$customId.'").style.display = "none";
					  		document.getElementById("thbtn_'.$customId.'").style.display = "none";
						}
						
					 	input.addEventListener("change", function (evt) {
					 		document.getElementById("response_'.$customId.'").innerHTML = "Uploading . . ."
					 		var i = 0, len = this.files.length, img, reader, file;
						
							for ( ; i < len; i++ ) {
								file = this.files[i];
						
								if (!!file.type.match(/image.*/)) {
									if ( window.FileReader ) {
										reader = new FileReader();
										reader.onloadend = function (e) { 
											//showUploadedItem(e.target.result, file.fileName);
										};
										reader.readAsDataURL(file);
									}
									if (formdata) {
										formdata.append("images[]", file);
									}
								}	
							}
						
							if (formdata) {
								$.ajax({
									url: "../plugins/vmcustom/color_swatch/uploads/upload.php?path="+path,
									type: "POST",
									data: formdata,
									processData: false,
									contentType: false,
									success: function (res) {
										document.getElementById("response_'.$customId.'").innerHTML = res; 
										//jQuery("#images_'.$customId.'").val("");
										//jQuery("#images_'.$customId.'").parent("form").reset();
									}
								});
							}
						}, false);
												
						input2.addEventListener("change", function (evt) {
					 		document.getElementById("thresponse_'.$customId.'").innerHTML = "Uploading . . ."
					 		var i = 0, len = this.files.length, img, reader, file2;
						
							for ( ; i < len; i++ ) {
								file2 = this.files[i];
						
								if (!!file2.type.match(/image.*/)) {
									if ( window.FileReader ) {
										reader = new FileReader();
										reader.onloadend = function (e) { 
											//showUploadedItem(e.target.result, file2.fileName);
										};
										reader.readAsDataURL(file2);
									}
									if (formdata2) {
										formdata2.append("images[]", file2);
									}
								}	
							}
						
							if (formdata) {
								$.ajax({
									url: "../plugins/vmcustom/color_swatch/uploads/upload.php?path="+path+"&type=thumbnail&cid='.$customId.'",
									type: "POST",
									data: formdata2,
									processData: false,
									contentType: false,
									success: function (res) {
										document.getElementById("thresponse_'.$customId.'").innerHTML = res; 
										var thumb = $("#resultthum'.$customId.'").text();
										$("#colorswatch_'.$customId.' > #thumbnailimg").val(thumb);
										$("#colorswatch_'.$customId.' > .typethumbimg").prop("checked", true);
										$("#colorswatch_'.$customId.' > #thpickcolor").hide();
										//jQuery("#thimages_'.$customId.'").val("");
										//jQuery("#thimages_'.$customId.'").parent("form").replaceWith(jQuery("#thimages_'.$customId.'").parent("form").clone());
									}
								});
							}
						}, false);
							
					})(jQuery);
				</script>
				<style>
				#colorswatch_'.$customId.' > #thpickcolor {
					border-right:20px solid #'.(!empty($field->thumbnail) ? $field->thumbnail : '444').';
					border-top : 1px solid #'.(!empty($field->thumbnail) ? $field->thumbnail : '444').';
					border-left : 1px solid #'.(!empty($field->thumbnail) ? $field->thumbnail : '444').';
					border-bottom : 1px solid #'.(!empty($field->thumbnail) ? $field->thumbnail : '444').';
				}
				.delete_button{
					background: url(../plugins/vmcustom/color_swatch/assets/images/remove.png);
					height: 20px;
				    text-indent: -99em;
				    width: 20px !important;
					cursor: pointer;
					border: medium none !important;
					margin-left: -14%;
				}
				</style>	
						
		';
		}
		else {
			$html = '
				<label>'.JText::_('CMS_COLOR_TITLE').'</label>
				<input style="border: 1px solid #1E90FF;height: 19px;width: 100px" type="text" value="'.$field->title.'" size="10" name="customfield_params['.$row.'][title]"><br>	
			';
		}
		$retValue .= $html;
		$row++;
		return true ;
	}

	/**
	 * @ idx plugin index
	 * @see components/com_virtuemart/helpers/vmCustomPlugin::onDisplayProductFE()
	 * @author Patrick Kohl
	 * eg. name="customPlugin['.$idx.'][comment] save the comment in the cart & order
	 */
	function plgVmOnDisplayProductFEVM3(&$product,&$group) {
	
		if ($group->custom_element != $this->_name) return '';
		$group->display = $this->renderByLayout('default',array($this->params,&$product,&$group) );
		
		return true;
	}

	//function plgVmOnDisplayProductFE( $product, &$idx,&$group){}
	/**
	 * @see components/com_virtuemart/helpers/vmCustomPlugin::plgVmOnViewCartModule()
	 * @author Patrick Kohl
	 */
	function plgVmOnViewCartModuleVM3( &$product, &$productCustom, &$html) {
		return $this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}

	/**
	 * @see components/com_virtuemart/helpers/vmCustomPlugin::plgVmOnViewCart()
	 * @author Patrick Kohl
	 */
	
    function plgVmOnViewCartVM3(&$product,&$productCustom,&$html) {
    	if (empty($productCustom->custom_element) or $productCustom->custom_element != $this->_name) return false;

    	$params = $this->params;
    	$params = json_decode($params,true);
    	$type = $productCustom->style_thumbnail;
    	$widthth = $productCustom->widthth;
    	$heightth = $productCustom->heightth;
    	$array = $product->param;
    	$widthbor = $widthth/2;
    	$document = JFactory::getDocument();
		$pathimages = JPATH_SITE.'/images/stories/virtuemart/color_swatch/images/'.$productCustom->virtuemart_customfield_id;
		$imagesCartArr = $this->list_files( dirname(dirname(dirname(__DIR__))).'/images/stories/virtuemart/color_swatch/images/'.$productCustom->virtuemart_customfield_id, $productCustom->order_image);
		$imagesCart = array();
		foreach($imagesCartArr as $key => $value ){
			if(@is_array(getimagesize($pathimages.'/'.$value))){ 
				$imagesCart[$key] = $value;
			} 
		}
		
    	$cId = $product->productCustom->virtuemart_customfield_id;
    
		$ic = $productCustom->thumbnail;
			
		$itemc = substr($ic, 0, 6);
		
		if(!empty($productCustom->thumbnail) ){
			$html .= '<div style="width:100%;float:left;margin:5px 0;">';
			if($itemc != 'thumbn'){
				if($type == 'square')
					$html .='<span id="'.$productCustom->virtuemart_customfield_id.'" class="hasColor" style="display:none">'.array_values($imagesCart)[0].'</span><span><span style="font-size: 12px;">'.JText::_($product->productCustom->custom_title).'</span><span style="float:left;border: 1px solid #DEDEDE;display: block;width:'.$widthth.'px ;height:'.$heightth.'px ;text-indent: -99em;background:#'.$productCustom->thumbnail.'"></span></span>';
				else
					$html .='<span id="'.$productCustom->virtuemart_customfield_id.'" class="hasColor" style="display:none">'.array_values($imagesCart)[0].'</span><span><span style="font-size: 12px;">'.JText::_($product->productCustom->custom_title).'</span><span style="float:left;border: 1px solid #DEDEDE;border-radius:'.$widthbor.'px;-moz-border-radius:'.$widthbor.'px;-webkit-border-radius: '.$widthbor.'; display: block;width:'.$widthth.'px ;height:'.$heightth.'px ;text-indent: -99em;background:#'.$productCustom->thumbnail.'"></span></span>';
			}
			else
			{

				if($type == 'circle'){
					if(JFactory::getApplication()->isAdmin()) {
						$html .='<span id="'.$productCustom->virtuemart_customfield_id.'" class="hasColor" style="display:none">'.array_values($imagesCart)[0].'</span><span>'.JText::_($product->productCustom->custom_title).'<img style="float:left;width:'.$widthth.'px ;height:'.$heightth.'px ;display: block;border-radius: '.$widthbor.'px;-moz-border-radius: '.$widthbor.'px;-webkit-border-radius: '.$widthbor.';border: 1px solid #DEDEDE;" src="../images/stories/virtuemart/color_swatch/images/'.$productCustom->virtuemart_customfield_id.'/'.$productCustom->thumbnail.'"></span>';
					}else
						$html .='<span id="'.$productCustom->virtuemart_customfield_id.'" class="hasColor" style="display:none">'.array_values($imagesCart)[0].'</span><span>'.JText::_($product->productCustom->custom_title).'<img style="float:left;width:'.$widthth.'px ;height:'.$heightth.'px ;display: block;border-radius: '.$widthbor.'px;-moz-border-radius: '.$widthbor.'px;-webkit-border-radius: '.$widthbor.';border: 1px solid #DEDEDE;" src="images/stories/virtuemart/color_swatch/images/'.$productCustom->virtuemart_customfield_id.'/'.$productCustom->thumbnail.'"></span>';
				}
				else {
					if(JFactory::getApplication()->isAdmin())
						$html .='<span id="'.$productCustom->virtuemart_customfield_id.'" class="hasColor" style="display:none">'.array_values($imagesCart)[0].'</span><span>'.JText::_($product->productCustom->custom_title).'<img style="float:left;width:'.$widthth.'px ;height:'.$heightth.'px ;display: block;border: 1px solid #DEDEDE;" src="../images/stories/virtuemart/color_swatch/images/'.$productCustom->virtuemart_customfield_id.'/'.$productCustom->thumbnail.'"></span>';
					else
						$html .='<span id="'.$productCustom->virtuemart_customfield_id.'" class="hasColor" style="display:none">'.array_values($imagesCart)[0].'</span><span>'.JText::_($product->productCustom->custom_title).'<img style="float:left;width:'.$widthth.'px ;height:'.$heightth.'px ;display: block;border: 1px solid #DEDEDE;" src="images/stories/virtuemart/color_swatch/images/'.$productCustom->virtuemart_customfield_id.'/'.$productCustom->thumbnail.'"></span>';					}
			}
			//if ($imagesCart) {
			//	$html .= '<span><img style="float:left;border:1px solid #DEDEDE;width:' . $widthth . 'px;height:' . $heightth . 'px;margin-left:5px;" src="' . JURI::root() . substr($productCustom->images, 3) . '/' . array_values($imagesCart)[0] . '"></span>';
			//}
			$html .= '</div>';
			$html .= '<div style="clear:both;"></div>';
		}

    	$document->addScriptDeclaration("
			jQuery(document).ready(function () {
				jQuery('.vm-customfield-cart > .product-field-type-E').each(function () {
					var vl = jQuery(this).html();
					if(vl == ''){
						jQuery(this).remove();
					}
					jQuery(this).parent('div').children('br').remove();
					
					jQuery('.hasColor').each(function (key, val) {
						var vl = jQuery(this).text(),
						vl2 = jQuery(this).attr('id');
						jQuery(this).parents('tr').find('.cart-images > img').attr('src', '".JURI::base()."images/stories/virtuemart/color_swatch/images/'+vl2+'/'+vl);
					});
				});
			});
		");
    	return true;
    }

	function plgVmDisplayInOrderBEVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}

	function plgVmDisplayInOrderFEVM3( &$product, &$productCustom, &$html) {
		$this->plgVmOnViewCartVM3($product,$productCustom,$html);
	}

	public function plgVmOnStoreInstallPluginTable($psType,$data,$table) {
		if(empty($table->custom_element) or (!empty($table->custom_element) and $table->custom_element!=$this->_name) ){
			return false;
		}
		if(empty($table->is_input)){
			vmInfo('COM_VIRTUEMART_CUSTOM_IS_CART_INPUT_SET');
			$table->is_input = 1;
			$table->store();
		}
	}

	function plgVmDeclarePluginParamsCustomVM3(&$data){
		return $this->declarePluginParams('custom', $data);
	}
	
	function plgVmGetTablePluginParams($psType, $name, $id, &$xParams, &$varsToPush){
		return $this->getTablePluginParams($psType, $name, $id, $xParams, $varsToPush);
	}

	function plgVmSetOnTablePluginParamsCustom($name, $id, &$table,$xParams){
		return $this->setOnTablePluginParams($name, $id, $table,$xParams);
	}

	/**
	 * Custom triggers note by Max Milbers
	 */
	function plgVmOnDisplayEdit($virtuemart_custom_id,&$customPlugin){
		return $this->onDisplayEditBECustom($virtuemart_custom_id,$customPlugin);
	}

	public function plgVmPrepareCartProduct(&$product, &$customfield,$selected,&$modificatorSum){
		
		if ($customfield->custom_element !==$this->_name) return ;
		if (!empty($selected['comment'])) {
			$modificatorSum += $customfield->customfield_price ;
		} else {
			$modificatorSum += 0.0;
		}
		
		return true;
	}

	public function plgVmDisplayInOrderCustom(&$html,$item, $param,$productCustom, $row ,$view='FE'){
		$this->plgVmDisplayInOrderCustom($html,$item, $param,$productCustom, $row ,$view);
	}

	public function plgVmCreateOrderLinesCustom(&$html,$item,$productCustom, $row ){
// 		$this->createOrderLinesCustom($html,$item,$productCustom, $row );
	}
	function plgVmOnSelfCallFE($type,$name,&$render) {
		$render->html = '';
	}

	function list_files($directory = '.', $order = 1) {
		$array = array();
		if ($directory != '.') {
			$directory = rtrim($directory, '/') . '/';
		}

		$dir = opendir($directory);
		$list = array();
		while($file = readdir($dir)){
			if ($file != '.' and $file != '..') {
				$ctime = filectime($directory . $file) . ',' . $file;
				$list[$ctime] = $file;
			}
		}
		closedir($dir);
		
		if ($order == 1) { // Name
			asort($list);
		} elseif ($order == 2) { // Time
			ksort($list);
		} elseif ($order == 3) { // Random
			shuffle($list);
		}
				
		return $list;
	}
}
// No closing tag