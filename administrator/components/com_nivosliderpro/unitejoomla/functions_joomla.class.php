<?php

/**
 * file version: 1.0.1
 */


	class UniteFunctionJoomla{
		
		public static $componentName;	//current component name. have to be set on include.
		public static $app;
		
		
		/**
		 * 
		 * put the label and the input of some form field
		 */
		public static function putFormField($form,$name,$group=null){
			echo $form->getLabel($name,$group);
			echo $form->getInput($name,$group);
		}
		
		
		/**
		 * 
		 * print fieldset box
		 */
		public static function putHtmlFieldsetBox($form,$name,$boxTitle="Settings"){

			if(empty($form))
				UniteFunctions::throwError("Form not found!!!");
				
			?>
			
				<fieldset class="adminform">
					<legend><?php echo $boxTitle?></legend>
					<ul class="adminformlist">
					<?php
						$fieldset = $form->getFieldset($name);
						if(empty($fieldset))
							UniteFunctions::throwError("fieldset with name: $name not found.");
							
						foreach($fieldset as $key=>$field){
							?>
								<li><?php echo $field->label; ?>
								<?php echo $field->input; ?></li>
							<?php 
						}
					?>
					</ul>
				</fieldset>
			<?php
		}
		
		/**
		 * 
		 * add script declaration wrapper
		 */
		public static function addScriptDeclaration($script){
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($script);
		}
		
		
		/**
		 * 
		 * encode array to registry (json) for saving, in some array of items
		 * 
		 */		
		public static function encodeArrayToRegistry($arr,$field){
 
			if(!isset($arr[$field]))
				return("");
				
			if(!is_array($arr[$field]))
				return($arr[$field]);
			
			$registry = new JRegistry();
			$registry->loadArray($arr[$field]);
			$value = $registry->toString('JSON');
			
			return($value);
		}
		
		/**
		 * 
		 * decode some array item to registry
		 */
		public static function decodeRegistryToArray($arr,$field){
			
			$output = array();
			if(!isset($arr[$field]))
				return($output);
				
			$value = $arr[$field];	
			if(is_array($value))
				return($value);
			
			$registry = new JRegistry();
			$registry->loadString($value,'JSON');
			$output = $registry->toArray();
			
			return($output);
		}
		
		/**
		 * 
		 * get form field value by types.
		 */
		public static function getFormFieldValue($form,$field,$group=null){
			$objField = $form->getField($field,$group);
			
			$value = $objField->value;
			$type = strtolower($objField->type);
			
			switch($type){
				case "mycheckbox":						
					$value = $objField->isChecked();
				break;
			}
			
			return($value);
		}
		
		
		/**
		 * 
		 * hide some form field
		 * @param $form
		 */
		public static function hideFormField(JForm $form,$field, $group=""){
			$class = $form->getFieldAttribute($field, "class","",$group);
			if(!empty($class))
				$class .= " hidden";
			else
				$class == "hidden";
			
			$form->setFieldAttribute($field, "hidden", "true",$group);
			$form->setFieldAttribute($field, "class", $class,$group);
		}
		
		
		/**
		 * 
		 * set alias from title
		 */
		public static function normalizeAlias($alias){
			$alias = JFilterOutput::stringURLSafe($alias);
			
			if(trim(str_replace('-','',$alias)) == '')
				$alias = JFactory::getDate()->format("Y-m-d-H-i-s");
			
			return($alias);				
		}	
		
		/**
		 * 
		 * put multiple html option boxes
		 */
		public static function putHtmlFieldsetBoxes($form,$name){
			
			$arrfieldsets = $form->getFieldsets($name);
		
			foreach($arrfieldsets as $arrFieldset)
				self::putHtmlFieldsetBox($form,$arrFieldset->name,$arrFieldset->label);
		}
		
		/**
		 * 
		 * give joomla order to hide main menu. 
		 * this must be used on view.html.php
		 */
		public static function hideMainMenu(){
			JRequest::setVar('hidemainmenu', true);
		}
		
		/**
		 * 
		 * get current component
		 */
		public static function getCurrentComponent(){
			$component = JRequest::getVar("option");
			return($component);
		}
		
		
		/**
		 * 
		 * get component url - site side
		 * 
		 */
		public static function getUrlComponent($args,$component=""){
			if(empty($component))
				$component = self::$componentName;
			$url = juri::root()."index.php?option=".$component."&amp;".$args;
			
			return($url);
		}
		
		
		/**
		 * 
		 * get view url (admin side)
		 */
		public static function getViewUrl($view,$layout="default",$args="",$component=""){
			
			if(empty($component))
				$component = self::$componentName;
				
			$url = "index.php?option=".$component;
			$url .= "&view=$view";
			
			//add layout
			if(!empty($layout))
				$url .= "&layout=".$layout; 
			
			//add additional arguments
			if(!empty($args))
				$url .= "&".$args;
			
			//$url = JURI::root().$url;
			
			$url = JRoute::_($url,false);
			
			return($url);
		}
		
		/**
		 * Get url of image for output
		 */
		public static function getImageOutputUrl($filename,$width=0,$height=0,$exact=false,$encode=true){
						
			//exact validation:
			if(($exact == "true" || $exact == true) && (empty($width) || empty($height) ))
				self::throwError("Exact must have both - width and height");
						
			if($encode == true)
				$filename = base64_encode($filename);
			
			$url = "index.php?option=".self::$componentName."&task=showimage&img=$filename";
			
			if(!empty($width))
				$url .= "&w=".$width;
		
			if(!empty($height))
				$url .= "&h=".$height;
		
			if($exact == true)
				$url .= "&t=exact";
		
			if($encode == false)
				$url .= "&noencode=true";
			
			$url = str_replace("&", "&amp;", $url);
				
			return($url);
		}
		
		
		/**
		 * 
		 * get image url from filename
		 */
		public static function getImageUrl($filename){
			$urlImage = JURI::root().$filename;
			return($urlImage);
		}
		
		
		/**
		 * get cache path. if not exists - try to crate it
		 */
		private static function getPathCache(){
			
			//set cache path
			$component = self::$componentName;
			
			$pathCache = JPATH_SITE."/cache/".$component."/";
			if(is_dir($pathCache))
				return($pathCache);
			
			@mkdir($pathCache);
			
			if(is_dir($pathCache))
				return($pathCache);
			
			//make media cache path
			$pathCache = JPATH_SITE."/media/".$component."/cache/";
			if(is_dir($pathCache))
				return($pathCache);
			
			@mkdir($pathCache);
			
			if(is_dir($pathCache))
				return($pathCache);
			
			//make component cache path
			$pathCache = JPATH_COMPONENT_SITE."/cache/";
			
			return($pathCache);
		}
		
		
		/**
		 * show image from request
		 */
		public static function showImageFromRequest(){
			
			$pathCache = self::getPathCache();
			$pathImages = JPATH_SITE."/";
			$urlImages = JURI::root();
			$pathEmptyImage = JPATH_COMPONENT_ADMINISTRATOR."/assets/resizer/empty_image.jpg";
			
			$imageView = new UniteImageView($pathCache, $pathImages, $urlImages, $pathEmptyImage);
			$imageView->showImageFromGet();
			exit();
		}		

		/**
		 * 
		 * get post or get application
		 */
		public static function getPostGetVar($name,$default="",$filter="STRING"){
			if(empty(self::$app))
				self::$app = JFactory::getApplication();
			
			$jinput = self::$app->input;
			$var = $jinput->get($name,$default,$filter);
			return($var);
		}

		
		
	}

?>