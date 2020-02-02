<?php

   class UniteProviderAdminUG extends UniteGalleryAdmin{
		

		/**
		 *
		 * the constructor
		 */
		public function __construct(){
		
			parent::__construct();
			
			$this->init();
		}		
		
		
		/**
		 * 
		 * init function
		 */
		public function init(){
			
			parent::init();
			
			//check if there is ajax action. If not, exit to admin pages
			self::onAjaxAction();
			
			self::onAddScripts();
			
			self::adminPages();
			
		}
		
		
	}

?>