<?php
/**
 * @package		customfilters
 * @subpackage	mod_cf_filtering
 * @copyright	Copyright (C) 2012-2018 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Class responsible for the rendering of the filters
 *
 * @package	customfilters
 * @author Sakis Terz
 * @since  1.9
 *
 */
class ModCfilteringRender
{

    /**
     *
     * @var array
     */
    protected $fltSuffix = array(
        'q' => 'keyword_flt',
        'virtuemart_category_id' => 'category_flt',
        'virtuemart_manufacturer_id' => 'manuf_flt',
        'price' => 'price_flt',
        'custom_f' => 'custom_flt'
    );

    public $scriptProcesses = [];

    public $scriptFiles = [];

    public $scriptVars = [];

    public $direction = 'ltr';

    /**
     *
     * @var UrlHanlder
     */
    protected $UrlHanlder;

    /**
     * contains all the filters with their options and their vars as set in the helper
     *
     * @var array
     */
    protected $filters = [];

    /**
     * All the options in the selected filters
     *
     * @var array
     */
    protected $selected_flt = [];

    /**
     * the selectied options substracting those which are inactive
     *
     * @var array
     */
    protected $selected_flt_modif = [];

    /**
     * the selectied options used for each filter//used by top-to-bottom
     *
     * @var array
     */
    protected $selected_fl_per_flt = array();

	/**
	 * Constructor of the render Class
	 *
	 * @param object $module
	 * @param array $selected_flt
	 * @param array $filters
	 */
	public function __construct($module, $selected_flt, $filters)
	{
		$this->module=$module;
		$this->filters=$filters;
		$this->moduleparams=cftools::getModuleparams($module);
		$this->menu_params=cftools::getMenuparams();
		$this->UrlHanlder = new UrlHandler($module, $selected_flt);

		//get the outputs
		$this->selected_flt=$selected_flt['selected_flt'];
		$this->selected_flt_modif=$selected_flt['selected_flt_modif'];
		$this->selected_fl_per_flt=$selected_flt['selected_fl_per_flt'];

		$doc= JFactory::getDocument();
		$this->direction=$doc->getDirection();

		$this->results_trigger=$this->moduleparams->get('results_trigger','sel');
		$this->results_loading_mode=$this->moduleparams->get('results_loading_mode','http');

		//get currency
		$japplication=JFactory::getApplication();
		$jinput=$japplication->input;
		$vendor_currency=cftools::getVendorCurrency();
		$virtuemart_currency_id=$jinput->get('virtuemart_currency_id',$vendor_currency['vendor_currency'],'int');
		$currency_id=$japplication->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',$virtuemart_currency_id);
		$this->currency_info=cftools::getCurrencyInfo($currency_id);
	}

	/**
	 * This function generates the html code for every filter
	 *
	 * @author	Sakis Terz
	 * @return	array	An array with the inside html code of every filter
	 * @since 	1.0
	 * @internal disp_types->1.select, 2.radio btns, 3.checkboxes, 4.Links, 5.Input text, 6.Slider
	 */
	public function renderFilters()
	{
		/* -Display Types-
		 * A display type can contain 2 or more display types (e.g. '1,3') in case we want to display the options with different ways within the same filter
		 * See Price Filter with input texts and Slider
		 */
		$dateformats=cftools::getDateFormat();
		$dateFormat_php=$dateformats['dateFormat_php'];
		$dateFormat=$dateformats['dateFormat'];

		$nofollow=$this->moduleparams->get('indexfltrs_by_search_engines','0');
		$custom_flt_disp_empty=$this->moduleparams->get('custom_flt_disp_empty','0');
		$filters_html_array=array();
		$thereIsSelection=!empty($this->selected_flt);

		foreach($this->filters as $key=>$flt){
		    $active_tree=array();
			if(isset($flt['active_tree']) && is_array($flt['active_tree']))$active_tree=$flt['active_tree'];
			$display_key=$key.'_'.$this->module->id;
			$is_customfield=strpos($key, 'custom_f_');

			//used to fetch params from specific filters
			if($is_customfield!==false)$ar_key='custom_f';
			else $ar_key=$key;
			$display_empty_opt=$this->moduleparams->get($this->fltSuffix[$ar_key].'_disable_empty_filters','1');

			$dispType=!empty($flt['display'])?$flt['display']:'';
			$smartSearch=0;
			if(isset($flt['smartSearch']))$smartSearch=$flt['smartSearch'];
			if(isset($flt['dispCounter']))$dispCounter=$flt['dispCounter'];
			if(isset($flt['dispCounterDisabled']))$dispCounterDisabled=$flt['dispCounterDisabled'];
			$isexpanable_tree=0;

			if($key=='virtuemart_category_id'){
				$cat_ordering=$this->moduleparams->get('categories_disp_order','names');
				//theming
				$category_flt_tree_mode=$this->moduleparams->get('category_flt_tree_mode','0');
				$parent_link=$this->moduleparams->get('category_flt_parent_link','0');
				$current_tree_string=implode(',',$active_tree);
				if($current_tree_string)$current_tree_string.='-';//ending string
				if($cat_ordering=='tree' && $category_flt_tree_mode==0 && $parent_link==0)$isexpanable_tree=true;
			}

			//there is more than 1 display type for the filter using a comma separator
			if(strpos($dispType, ',')>0){
				$disp_typeArray=explode(',',$dispType);
			}
			else $disp_typeArray=array($dispType);

			$html='';
			$options_ar=!empty($flt['options'])?$flt['options']:array();
			$opt_found=false;

			if(count($options_ar)>0){

			    //different display types for that filter
				foreach($disp_typeArray as $disp_type){
					$ul_class='';
					if($disp_type==9 || $disp_type==10)$ul_class=' cf_colorbtn_list';
					elseif($disp_type==11 || $disp_type==12)$ul_class=' cf_btn_list';
					$innerHTML='';
					$innerHTML_clear='';
					$linkAttributes=array();

					//--select drop down--//
					if($disp_type==1){
						foreach($options_ar as $op){
							$opt=(object)$op;
							$js_trigger='';
							$select_flt='';
							$inactive='';
							$option_targ='';
							$url_set=false;
							if($opt->selected)$select_flt=' selected="selected"';

							//not active and disabled
							if(!$opt->active){

							    //empty should be disabled
								if($display_empty_opt==1){
								    $option_id='1';//for inactive
								    $inactive= 'disabled="disabled"';
								}
								//empty should be hidden
								else continue;
							}
							else{
								if($this->results_trigger=='btn')$option_id=$opt->id;
								else {
									$option_id=$opt->id;
									$option_targ=JRoute::_($this->UrlHanlder->getURL($flt,$opt->id));
									$url_set=true;
								}
								$opt_found=true;
							}

							$label=$opt->label;
							if($dispCounter && isset($opt->counter) && $opt->active){
								$label=$opt->label.'&nbsp;('.$opt->counter.')';
							}
							$innerHTML.='<option data-url="'.$option_targ.'" '.$inactive .'value="'.$option_id.'" '.$select_flt.'>'.$label.'</option>';

						}
						//generate the HTML
						if($innerHTML){
							if($this->results_trigger!='btn' && $this->results_loading_mode!='ajax')$js_trigger='onchange="window.top.location.href=this.options[this.selectedIndex].getAttribute(\'data-url\')"';

							$html.='<select name="'.$key.'[]" class="cf_flt" '.$js_trigger.'>';
							$html.=$innerHTML;
							$html.='</select>';
						}
						unset($opt);
					}

					//-- 2.radios, 3.checkboxes, 4.links, 7.image links, 9.color buttons single, 10.color buttons multi, 11.button single, 12. button multi
					elseif($disp_type==2 || $disp_type==3 || $disp_type==4 || $disp_type==7 || $disp_type==9 || $disp_type==10 || $disp_type==11 || $disp_type==12){
						$innerHTML='';

						foreach($options_ar as $op){
							$script='';
							$opt=(object)$op;
							$select_flt='';
							$opt_class="cf_option";
							$li_class='';
							$class_name='';
							$style='';
							$color='';
							$inactive='';
							$element_id=$display_key.'_elid'.$opt->id;

							//create classes for the category tree
							if($key=='virtuemart_category_id' && $cat_ordering=='tree' && $opt->id>0 && isset($opt->cat_tree)){
								$li_class='cf_catOption';
								$li_class.=" cfLiLevel$opt->level";

								if($category_flt_tree_mode==false){//tree mode collapsed
									if(!empty($opt->isparent)){
										$opt_class.=' cf_parentOpt';
										$li_class.=' cf_parentLi';

										if(strpos($current_tree_string,$opt->cat_tree.'-'.$opt->id.'-')!==false)$opt_state=' cf_expand';
										else $opt_state=' cf_unexpand';
										$opt_class.=$opt_state;
									}
									else {
										$opt_class.=' cf_childOpt';

									}
									$opt_class.=' tree_'.$opt->cat_tree;
									$li_class.=' li-tree_'.$opt->cat_tree;

									if((empty($current_tree_string) && $opt->level>0) || (!empty($current_tree_string) && strpos($current_tree_string,$opt->cat_tree.'-')===false)){
										$li_class.=" cf_invisible";
									}
								}
							}

							if($opt->selected){
								$select_flt=' checked="checked"';
								$class_name=' cf_sel_opt';
							}
							if($opt->type=='clear')$class_name.=' cf_clear';
							else{
								//colors
								if($disp_type=='9' || $disp_type=='10'){
									$colors_multi=explode('|', $opt->label);
									$nr_colors_multi=count($colors_multi);
									$color_btn_width=100/$nr_colors_multi;
									//check to see if the value is color
									if($nr_colors_multi==1){
										$color=cftools::checkNFormatColor($opt->label);
										//if no color go to the next option
										if(empty($color))continue;
									}
									$opt_class.="  cf_color_btn";
								}
								//buttons
								elseif($disp_type==11 || $disp_type==12)$opt_class.="  cf_button";
							}

							//generate the code for the active and inactive anchors
							if(!$opt->active){
								if($display_empty_opt==1){
									$opt_class.=" cf_disabled_opt";
									$inactive= 'disabled="disabled"';
									$style='';
									$anchor_label=$opt->label;

									//colors
									if($disp_type=='9' || $disp_type=='10' && $opt->type!='clear'){
										$clr_counter=0;
										$colors_html='';
										foreach($colors_multi as $clr){
											$color=cftools::checkNFormatColor($clr);
											if(empty($color))continue;
											$clr_counter++;
											$colors_html.='<span class="cf_color_inner" style="background-color:'.$color.'; width:'.$color_btn_width.'%;"></span>';
										}

										$option_anchor=$colors_html;

									}else $option_anchor='<span class="'.$opt_class.'" '.$style.'>'.$anchor_label.'</span>';
								}else{//hide if disabled
									$option_anchor='';
								}
							}
							else{
								$linkAttributes['class']="$opt_class$class_name";
								$linkAttributes['rel']='';
								if($this->moduleparams->get('indexfltrs_by_search_engines',0)==false || $opt->type=='clear')$linkAttributes['rel']="nofollow";
								//if we are using apply button for the inputs we should load only the option's id - urls won't used
								//For links and buttons the anchor should always created
								if(($this->results_trigger!='btn') || $disp_type=='4' || ($disp_type=='3' && $opt->type=='clear') || $disp_type=='7' || $disp_type=='9' || $disp_type=='10' || $disp_type=='11' || $disp_type=='12' ||!empty($opt->isparent)){
									$linkAttributes['data-module-id']=$this->module->id;
									$linkAttributes['id']=$element_id.'_a';
									$linkAttributes['style']=$style;

									$option_targ=JRoute::_($this->UrlHanlder->getURL($flt,$opt->id,$opt->type));
									if($this->results_trigger!='btn' && $this->results_loading_mode!='ajax')$script='onclick="window.top.location.href=\''.$option_targ.'\';"';
									$anchor_label=$opt->label;

									//colors
									if(($disp_type=='9' || $disp_type=='10') && $opt->type!='clear'){
										$clr_counter=0;
										$colors_html='';
										foreach($colors_multi as $clr){
											$color=cftools::checkNFormatColor($clr);
											if(empty($color))continue;
											$clr_counter++;
											$colors_html.='<span class="cf_color_inner" style="background-color:'.$color.'; width:'.$color_btn_width.'%;"></span>';
										}

										$option_anchor=JHtml::link($option_targ,$colors_html,$linkAttributes);
									}else $option_anchor=JHtml::link($option_targ,$anchor_label,$linkAttributes);

								}else {
									$option_targ=$opt->id;
									$anchor_label=$opt->label;
									$option_anchor='<span class="'.$opt_class.'">'.$anchor_label.'</span>';
								}
								$opt_found=true;
							}

							if($option_anchor){
								$disp_input=true;

								//radios or checkboxes
								//in case of categories, parent categories with childs cannot be radios or checkboxes (only links)
								if(($disp_type==2 || $disp_type==3) && empty($opt->isparent)){
									$type_str=($disp_type==2?'radio':'checkbox');


									//when checkboxes, hide the checkbox from the clear tool
									if($opt->type=='clear' && $type_str=='checkbox')$disp_input=false;
									$class_str='';
									if($li_class)$class_str=' class="'.$li_class.'"';
									$innerHTML.='<li '.$class_str.'>';
									if($disp_input){
										$innerHTML.='
										 <label class="'.$class_name.'" for="'.$element_id.'">'.
										'<input '.$script.' type="'.$type_str.'" name="'.$key.'[]" '. $inactive .'class="cf_flt" id="'.$element_id.'" value="'.($opt->id).'" '.$select_flt.'/>'.$option_anchor.'</label>';
										if($dispCounter && isset($opt->counter) && $opt->active){
											$innerHTML.='<span class="cf_flt_counter">('.$opt->counter.')</span>';
										}
									}else{
										$innerHTML.='<span class="'.$class_name.'">'.$option_anchor.'</span>';
									}
									$innerHTML.='</li>';
								}

								//links (image|simple links), buttons, or clears
								else{
									$class_str='';
									if($opt->type=='clear')$li_class='cf_li_clear';
									if($li_class)$class_str=' class="'.$li_class.'"';
									$innerHTML.='<li '.$class_str.'>';

									//image links
									if($disp_type==7){
										if(empty($opt->media_id))$media_id=0;
										else $media_id=$opt->media_id;
										$img=cftools::getMediaFile($media_id);

										if(!empty($img) && $opt->type!='clear'){

											//inactive images
											if(!$opt->active){
												if($display_empty_opt==1){
													$opt_class.=" cf_disabled_opt_image";
													$img_option_anchor='<img src="'.$img->url.'" alt="'.$opt->label.'"/>';
													$img_option_anchor.='<span class="cf_img_caption">'.$opt->label;
													if($dispCounter && isset($opt->counter) && $opt->active){
														$img_option_anchor.='<span class="cf_flt_counter">('.$opt->counter.')</span>';
													}
													$img_option_anchor.='</span>';
												}

												//hide if disabled
												else{
													$img_option_anchor='';
												}
											}else{
												$opt_class.=" cf_opt_image";
												$imgWrapper='<img src="'.$img->url.'" alt="'.$opt->label.'"/>';
												$imgWrapper.='<span class="cf_img_caption">'.$opt->label;
												if($dispCounter && isset($opt->counter) && $opt->active){
													$imgWrapper.='<span class="cf_flt_counter">('.$opt->counter.')</span>';
												}
												$imgWrapper.='</span>';
												$linkAttributes['data-module-id']=$this->module->id;
												$linkAttributes['id']=$element_id.'_a';
												$img_option_anchor=JHtml::link($option_targ,$imgWrapper,$linkAttributes);
											}
											$innerHTML.='<div class="cf_img_wrapper '.$opt_class.'" style="width:'.$img->width.'px;">'.
											$img_option_anchor.'</div>';
										}

										//simple links
										else{
											$innerHTML.=$option_anchor;
											if($dispCounter && isset($opt->counter) && $opt->active){
												$innerHTML.='<span class="cf_flt_counter">('.$opt->counter.')</span>';
											}
										}
									}

									//color buttons, buttons
									elseif($disp_type==9 || $disp_type==10 || $disp_type==11 || $disp_type==12){
										$innerHTML.=$option_anchor;
									}

									//simple links
									else {
										$innerHTML.=$option_anchor;
										if($dispCounter && isset($opt->counter) && $opt->active){
											$innerHTML.='<span class="cf_flt_counter">('.$opt->counter.')</span>';
										}
									}
									/* add a hidden input which holds the selected category.
									 * This way we can submit the form with a submit button too
									 */
									if($opt->selected){
										$innerHTML.='<input type="hidden" name="'.$key.'[]" value="'.($opt->id).'" />';
									}
									$innerHTML.='</li>';
								}
							}
						}//foreach(options)

						if($innerHTML){
							$list_id='cf_list_'.$key.'_'.$this->module->id;

							//smart search functionality
							//not images, color buttons
							if($smartSearch && $disp_type!=7 && $disp_type!=9 && $disp_type!=10){
								$smart_input_id='cf_smartSearch_'.$key.'_'.$this->module->id;
								$html.='<input type="text" class="cf_smart_search" id="'.$smart_input_id.'" placeholder="'.JText::_('MOD_CF_SEARCH').'"  maxlength="150"/>';
								$this->scriptProcesses[]="
								 var myFilter$key = new CfElementFilter('$smart_input_id', '#$list_id li',{
								  	module_id:{$this->module->id},
								  	isexpanable_tree:$isexpanable_tree,
								  	filter_key:'$key'
								  });
								";
							}
							$html.='<ul class="cf_filters_list'.$ul_class.'" id="'.$list_id.'">';
							$html.=$innerHTML;
							$html.='</ul>';
						}
						unset($opt);
					}

					//-- Inputs--//
					elseif($disp_type==5){
						$sliderExist=in_array('6', $disp_typeArray);
						$symbol_start='';
						$symbol_end='';
						$script='';
						$value_found=false;
						$clear_targ=JRoute::_($this->UrlHanlder->getURL($flt,'','clear'));
						$linkAttributes['class']='cf_option';
						$linkAttributes['id']=$display_key.'_clear';
						$linkAttributes['data-module-id']=$this->module->id;
						$linkAttributes['rel']="nofollow";

						/*Price Filter - get some more data*/
						if(!empty($this->currency_info) && $key=='price'){
							if($this->currency_info->currency_positive_style){
								if(strpos($this->currency_info->currency_positive_style,'{symbol}')==0){
									$symbol_start='&nbsp;'.$this->currency_info->currency_symbol;
								}
								else $symbol_end='<span class="cf_currency">'.$this->currency_info->currency_symbol.'&nbsp;'.'</span>';
							}else{
								$symbol_start='<span class="cf_currency">&nbsp;'.$this->currency_info->currency_symbol.'</span>';
								$symbol_end='';
							}
						}


						foreach ($options_ar as $index=>$option){
							$option=(object)$option;

							$size=!empty($option->size)?$option->size:7;
							$maxlength=!empty($option->maxlength)?$option->maxlength:7;
							$label=!empty($option->label)?$option->label:'';
							$placeholder=!empty($option->placeholder)?$option->placeholder:'';

							$innerHTML.='<label for="'.$display_key.'_'.$index.'">'.$label.'</label>'.$symbol_start;
							$innerHTML.='<input name="'.$option->name.'" value="'.$option->value.'" type="search" placeholder="'.$placeholder.'" size="'.$size.'" maxlength="'.$maxlength.'" id="'.$display_key.'_'.$index.'" '.$script.'>'.$symbol_end;
							if(!empty($option->value))$value_found=true;
						}


						if(!$sliderExist && $value_found){//generate the clear link
							$innerHTML_clear='<br/>'.JHtml::link($clear_targ,JText::_('MOD_CF_CLEAR'),$linkAttributes);
						}

						if($innerHTML){
							$input_class='cf_wrapper_input_text_'.count($options_ar);
							$html.='<div class="cf_wrapper_input_text '.$input_class.'" id="cf_wrapper_input_text_'.$display_key.'">';
							$html.='<div class="form-horizontal">';
							$html.=$innerHTML;

							$html.='
							<button type="submit" class="cf_search_button btn" id="'.$display_key.'_button'.'" title="'.JText::_('MOD_CF_APPLY').'">
							<i class="cficon-search"></i>
							</button>';
							$html.=$innerHTML_clear;
							//assigne event to the button
							$this->scriptProcesses[]="customFilters.assignEvents(".$this->module->id.");";

							$html.='</div>';
							$html.='<div class="cf_message" id="'.$display_key.'_message"></div>';
							$html.='<input type="hidden" value="'.$clear_targ.'" id="'.$display_key.'_url'.'"/>';
							$html.='</div>';
						}
					}

					//--Range dates--//
					elseif($disp_type==8){

					    //initialization of some vars
						$format = $dateFormat;
						$maxlength=10;
						$size=9;
						$script='';

						$clear_targ=JRoute::_($this->UrlHanlder->getURL($flt,'','clear'));
						$linkAttributes['class']='cf_option';
						$linkAttributes['id']=$key.'_clear';
						$linkAttributes['data-module-id']=$this->module->id;

						foreach ($options_ar as $index=>$option){
						    $option=(object)$option;

						    /*
						     * calendars accept only dates in format Y-m-d
						     * https://github.com/joomla/joomla-cms/issues/8636#issuecomment-163367948
						     */
						    $newDateString='';
						    if(!empty($option->value)){
						      $myDateTime = DateTime::createFromFormat(str_replace('%', '', $format), $option->value);
						      $newDateString = $myDateTime->format('Y-m-d');
						    }

						    $class_key=$index==0?'from':'to';
						    $innerHTML.='<label for="'.$display_key.'_'.$class_key.'"></label>';
						    $innerHTML.=
						    JHTML::calendar(
						        $newDateString,
						        $key.'['.$index.']',
						        $display_key.'_'.$index,
						        $format,
						        array('size'=>$size,'maxlength'=>$maxlength)
						    );
						}


						if($this->results_trigger!='btn' && (!empty($options_ar[0]['value']) || !empty($options_ar[1]['value']))){//generate the clear link
							$innerHTML_clear='<br/>'.JHtml::link($clear_targ,JText::_('MOD_CF_CLEAR'),$linkAttributes);
						}

						if($innerHTML){
							$this->setCalendarScripts($key,$dateFormat);
							$html.='<div class="cf_wrapper_input_text" id="cf_wrapper_input_text_'.$display_key.'">';
							$html.=$innerHTML;

							$html.='
								<button type="submit" class="cf_search_button btn" id="'.$display_key.'_button'.'" title="'.JText::_('MOD_CF_APPLY').'">
								<i class="cficon-search"></i>
								</button>';
							//assigne event to the button
							$this->scriptProcesses[]="customFilters.assignEvents(".$this->module->id.");";

							$html.='<div class="cf_message" id="'.$display_key.'_message"></div>';
							$html.='<input type="hidden" value="'.$clear_targ.'" id="'.$display_key.'_url'.'"/>';
							$html.=$innerHTML_clear;
							$html.='</div>';
						}
					}

					//--Range Slider--//
					elseif($disp_type==6){
						$knobInnerHTML='';
						$linkAttributes['class']='cf_option';
						$linkAttributes['id']=$key.'_clear';
						$linkAttributes['data-module-id']=$this->module->id;
						$clear_targ=JRoute::_($this->UrlHanlder->getURL($flt,'','clear'));
						$val_from=$options_ar[0]['value'];
						$val_to=$options_ar[1]['value'];


						//foreach($options_ar as $op){

						$inputTextExist=in_array('5', $disp_typeArray);
						//if there is an input text we don't have to generate the hidden inputs
						//otherwise we have to store the slider's value in a hidden input

						//from
						$opt_from=(object)$options_ar[0];
						$slider_min_value=$opt_from->slider_min_value;
						$knobInnerHTML.='<div class="cf_filtering_knob cf_filtering_knob_from" id="'.$display_key.'_knob_from" rel="'.$val_from.'"></div>';
						if($inputTextExist){//an input text exist which stores the slider values
						}else{
							$innerHTML.='<input name="'.$key.'[0]" value="'.$val_from.'" type="hidden" id="'.$display_key.'_0">';
						}

						//to
						$opt_to=(object)$options_ar[1];
						$slider_max_value=$opt_to->slider_max_value;
						$knobInnerHTML.='<div class="cf_filtering_knob cf_filtering_knob_to" id="'.$display_key.'_knob_to" rel="'.$val_to.'"></div>';
						if($inputTextExist){//an input text exist which stores the slider values
						}else{
							$innerHTML.='<input  name="'.$key.'[1]" value="'.$val_to.'" type="hidden" id="'.$display_key.'_1">';
						}

						if(($val_from || $val_to)){//generate the clear link
							$innerHTML_clear='<br/><div style="clear:both">'.JHtml::link($clear_targ,JText::_('MOD_CF_CLEAR'),$linkAttributes).'</div>';
						}

						if(!empty($knobInnerHTML)){
							$innerHTML.='<div class="knob_wrapper">'.$knobInnerHTML.'</div>';
						}

						if($innerHTML){
							$this->setSliderScripts($key,$inputTextExist,$slider_min_value,$slider_max_value);

							$html.='<div class="cf_filtering_slide_container" id="'.$display_key.'_slider">';
							$html.='<div class="cf_filtering_slider_gutter" id="'.$display_key.'_slider_gutter">';

							$html.='<div class="cf_filtering_slider_gutter_item cf_slider_gutter_l" id="'.$display_key.'_slider_gutter_edge_l"></div>';
							$html.='<div class="cf_filtering_slider_gutter_item cf_slider_gutter_m" id="'.$display_key.'_slider_gutter_m">';

							$html.='<div id="'.$display_key.'_slider_bkg_img" class="slider_bkg_img"></div>';
							$html.=$innerHTML;
							$html.='</div>';//cf_slider_gutter_m
							$html.='<div class="cf_filtering_slider_gutter_item cf_slider_gutter_r" id="'.$display_key.'_slider_gutter_edge_r"></div>';
							$html.='</div>';//cf_filtering_slider_gutter

							if(!$inputTextExist){
								$html.='<div class="cf_message" id="'.$display_key.'_message"></div>';
								$html.='<input type="hidden" value="'.$clear_targ.'" id="'.$display_key.'_url'.'"/>';

								if($this->results_trigger!='btn' && $this->results_loading_mode!='ajax'){
									$html.='
									<button type="submit" class="cf_search_button btn" id="'.$display_key.'_button'.'" title="'.JText::_('MOD_CF_APPLY').'">
									<i class="cficon-search"></i>
									</button>';
									//assign also event to the button
									$this->scriptProcesses[]="customFilters.assignEvents(".$this->module->id.");";
								}
							}
							if(!empty($innerHTML_clear))$html.=$innerHTML_clear;
							$html.='<div style="clear:both"></div>';
							$html.='</div>';
						}
					}
				}
			}
		 if(!empty($html))$filters_html_array[$key.'_'.$this->module->id]=$html;
		}
		return $filters_html_array;
	}

	/**
	 *
	 * Creates the necessary scripts for the calendars
	 * @param unknown_type $filter_key
	 * @param unknown_type $module_id
	 * @param unknown_type $format
	 */
	public function setCalendarScripts($filter_key, $format='%d-%m-%Y')
	{
		$module_id=$this->module->id;
		$japplication=JFactory::getApplication();
		$jinput=$japplication->input;
		//in case of ajax set the scripts to be reloaded
		if($jinput->get('view','')=='module' && $jinput->get('option','')=='com_customfilters'){
			$display_key=$filter_key.'_'.$module_id;
			$fields=array('0','1');
			foreach($fields as $fld){
				$this->scriptProcesses[]='
				Calendar.setup({
					// Id of the input field
					inputField: "' . $display_key.'_'.$fld . '",
					// Format of the input field
					ifFormat: "' . $format . '",
					// Trigger for the calendar (button ID)
					button: "' .$display_key.'_'.$fld. '_img",
					// Alignment (defaults to "Bl")
					align: "Tl",
					singleClick: true,
					firstDay: ' . JFactory::getLanguage()->getFirstDay() . '
					});';
			}
		}
	}

	/**
	 *
	 * Sets the scripts for creating a new slider
	 * @param 	string $filter_key
	 * @param 	int $module_id
	 * @param 	bolean $inputs - If there are text inputs too
	 * @param 	float $slider_min_value
	 * @param 	float $slider_max_value
	 * @since	1.6.1
	 * @author	Sakis Terz
	 */
	public function setSliderScripts($filter_key,$inputs=false,$slider_min_value=0,$slider_max_value=100)
	{
		$module_id=$this->module->id;
		$display_key=$filter_key.'_'.$module_id;
		if(!empty($this->selected_flt[$filter_key][0]))$setMin=$this->selected_flt[$filter_key][0];
		if(!empty($this->selected_flt[$filter_key][1]))$setMax=$this->selected_flt[$filter_key][1];

		//this will create the slider
		$this->scriptProcesses[]="
								elFrom=document.id('".$display_key."_knob_from');
								elTo=document.id('".$display_key."_knob_to');";

		//tooltips only if the inputs are not displayed
		if(!$inputs){
			$this->scriptProcesses[]="
					delete tipFrom$display_key;
					var fromTipInst=document.id('".$display_key."_knob_from_tooltip');
					if(fromTipInst)fromTipInst.dispose();
					tipFrom$display_key=new Tips(document.id('".$display_key."_knob_from'),{
					    fixed: false,
					    hideDelay: 10,
					    showDelay: 10,
					    offset: {x: 0, y: -30},
					    className: 'cftooltip',
					    id:'".$display_key."_knob_from_tooltip',
					});


					delete tipTo$display_key;
					var toTipInst=document.id('".$display_key."_knob_to_tooltip');
					if(toTipInst)toTipInst.dispose();

					tipTo$display_key=new Tips(document.id('".$display_key."_knob_to') ,{
					    fixed: false,
					    hideDelay: 10,
					    showDelay: 10,
					    offset: {x: 0, y: 22},
					    className: 'cftooltip',
					    id:'".$display_key."_knob_to_tooltip',
					});	";
		}

		$js_process="{$display_key}_sliderObj = new Cfslider('$display_key','$module_id', {
									start:".$slider_min_value.",
									end:".$slider_max_value.",
									offset:18,
									snap:false,
									direction:'".$this->direction."',
									onMouseMove:function(pos){
									if(pos.min_moved){
										document.id('".$display_key."_0').value=pos.minpos;
										elFrom.store('tip:text',pos.minpos.toString());
										var tipFromContent=$$('#".$display_key."_knob_from_tooltip .tip-text')[0];
										if(tipFromContent)tipFromContent.innerHTML=pos.minpos.toString();

										}
									if(pos.max_moved){
										document.id('".$display_key."_1').value=pos.maxpos;
										elTo.store('tip:text',pos.maxpos.toString());
										var tipToContent=$$('#".$display_key."_knob_to_tooltip .tip-text')[0];
										if(tipToContent)tipToContent.innerHTML=pos.maxpos.toString();
									}
									customFilters.validateRangeFlt($module_id,'$filter_key');
									}";

		if($this->results_trigger=='btn' || $this->results_loading_mode=='ajax'){
			$js_process.=",
									onComplete:function(e){
										customFilters.listen(e,this, $module_id);
									}";
		}
		$js_process.="});";

		$this->scriptProcesses[]=$js_process;

		$this->scriptVars['slider_min_value']=$slider_min_value;
		$this->scriptVars['slider_max_value']=$slider_max_value;

		if(!empty($setMin) && empty($setMax)){
			$this->scriptProcesses[]=" {$display_key}_sliderObj.setMin($setMin);";
			$this->scriptProcesses[]=" {$display_key}_sliderObj.setMax($slider_max_value);";
		}
		elseif(!empty($setMax) && empty($setMin)){
			$this->scriptProcesses[]=" {$display_key}_sliderObj.setMin($slider_min_value);";
			$this->scriptProcesses[]=" {$display_key}_sliderObj.setMax($setMax);";
		}elseif(!empty($setMin) && !empty($setMax)){
			$this->scriptProcesses[]=" {$display_key}_sliderObj.setMin($setMin);";
			$this->scriptProcesses[]=" {$display_key}_sliderObj.setMax($setMax);";
		}
	}

	/**
	 * Return any existing script process
	 * @since	1.9.0
	 * @author	Sakis Terz
	 */
	public function getScriptAssets()
	{
		$this->scriptFiles[]=JURI::root().'modules/mod_cf_filtering/assets/slider.js';
		$this->scriptFiles[]=JURI::root().'modules/mod_cf_filtering/assets/drag_refactor.js';
		$scriptAssets['scriptProcesses']=$this->scriptProcesses;
		$scriptAssets['scriptFiles']=$this->scriptFiles;
		$scriptAssets['scriptVars']=$this->scriptVars;
		return $scriptAssets;
	}
}
