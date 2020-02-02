<?php

	class UniteAdminTable{
		
		const FILTER_TYPE_PUBLISHED = "published";
		
		const COL_TYPE_CHECKBOX = "checkbox";
		const COL_TYPE_TEXT = "text";
		
		private $view; 
		private $state;
		private $arrFilters = array();
		private $self;
		private $arrCols = array();
		
		
		
		/**
		 * 
		 * constuctor, take the view
		 */
		public function __construct($state){
			$this->state = $state;
			$this->self = $_SERVER["PHP_SELF"];
		}
		
		/**
		 * 
		 * add column type and name
		 */
		private function addCol($type=null,$name=null,$title=null,$sort_value = null){
			$col = array();
			$col["type"] = $type;
			$col["name"] = $name;
			$col["title"] = $title;
			$col["sort_value"] = $sort_value;
			$this->arrCols[] = $col;
		}
		
		/**
		 * 
		 * add "regular" text column
		 */
		public function addCol_text($name,$title,$sort_value=null){
			$this->addCol(self::COL_TYPE_TEXT,$name,$title,$sort_value);
		}
		
		
		/**
		 * 
		 * add checkboxes column
		 */
		public function addCol_checkbox(){
			$this->addCol(self::COL_TYPE_CHECKBOX);
		}
		
		
		/**
		 * 
		 * add some filter
		 */
		public function addFilter($type){
			$filter = array();
			$filter["type"] = $type;
			$this->arrFilters[] = $filter;
		}
		
		/**
		 * 
		 * add "published" filter
		 */
		public function addFilterPublished(){
			$this->addFilter(self::FILTER_TYPE_PUBLISHED);
		}
		
		
		/**
		 * ===========================================================
		 */
		
		/**
		 * 
		 * draw some filter
		 */
		private function putFilter($filter){
			
			$type = $filter["type"];
			
			switch($filter["type"]){
				case self::FILTER_TYPE_PUBLISHED:
					?>
						<select name="filter_published" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
							<?php echo JHtml::_('select.options', array(JHtml::_('select.option', '1', 'JPUBLISHED'),JHtml::_('select.option', '0', 'JUNPUBLISHED')), 'value', 'text', $this->state->get('filter.published'), true);?>
						</select>
					<?php 
				break;
				default:
					UniteFunctions::throwError("Wrong filter type: ".$type);
				break;
			}
			
		}
		
		
		/**
		 * 
		 * put filter bar
		 */
		public function putFilterBar(){
			
			if(empty($this->arrFilters))
				return(false);
			?>
			
			<fieldset id="filter-bar">		
				<div class="filter-select fltrt">
				
				<?php
					 foreach($this->arrFilters as $filter)
					 		$this->putFilter($filter);
				?>
		
				</div>
			</fieldset>
			
			<?php 
		}
		
		
		/**
		 * 
		 * put the table
		 */
		public function putTable(){
			
		}
		
	}

?>