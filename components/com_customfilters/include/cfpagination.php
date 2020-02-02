<?php
/**
 *
 * Customfilters pagination class
 *
 * @package		customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: cfpagination.php 1 2015-03-03 18:50:00Z sakis $
 */

defined('_JEXEC') or die;
jimport('joomla.html.pagination');

/**
 * The class that extends the JPagination
 * Since VM does not allow to use the default JPagination in the layout - Should be extended
 *
 * @package customfilters
 * @author Sakis Terz
 */
class cfPagination extends JPagination
{
	protected $menuparams;
	protected $_perRow;

	/**
	 *
	 * @param int $total
	 * @param int $limitstart
	 * @param int $limit
	 * @param int $perRow
	 */
	public function __construct($total, $limitstart, $limit, $perRow=3)
	{
		$this->prefix='com_customfilters';
		$app=JFactory::getApplication();
		$jinput=$app->input;
		$option=$jinput->get('option','','cmd');
		$current_itemId=$jinput->get('Itemid','0','int');
		$this->menuparams=cftools::getMenuparams();
		$this->cfinputs=CfInput::getInputs();
		$this->_perRow = $this->menuparams->get('prod_per_row',3);


		parent::__construct($total, $limitstart, $limit);

		//ItemId
		if($option=='com_customfilters' && !empty($current_itemId))$itemId=$current_itemId; //valid also to the ajax requests

		if(!empty($itemId))$this->setAdditionalUrlParam('Itemid',$itemId);
		$vars=$this->getVarsArray();
		if(count($vars)>0){
			$vars['option']= 'com_customfilters';
			$vars['view']= 'products';
		}
		foreach ($vars as $key=>$var){
			if(is_array($var)){
				for($i=0; $i<count($var); $i++){
					$var_name=$key."[$i]";
					if(isset($var[$i]))$this->setAdditionalUrlParam($var_name,$var[$i]);
				}
			}else $this->setAdditionalUrlParam($key,$var);

		}
		$this->setAdditionalUrlParam('tmpl',''); //reset the tmpl as it comes from the ajax requests
	}

	/**
	 * (non-PHPdoc)
	 * @see JPagination::getLimitBox()
	 */
	public function getLimitBox()
	{

		$url=$this->getStatURI();
		$url=JRoute::_($url);

		$myURI=JURI::getInstance($url);
		//if(!empty($itemId))$myURI->setVar('Itemid', $itemId);
		if($myURI->getQuery())$wildcard='&';
		else $wildcard='?';
		$url.=$wildcard;

		$limits = array ();
		$pagination_seq=$this->menuparams->get('pagination_list_sequence','12,24,36,48,60,72');
		$pagination_seq_array=explode(',', $pagination_seq);

		// Generate the options list.
		foreach ($pagination_seq_array as $seq) {
			$seq=(int)trim($seq);
			if($seq< $this->_perRow)continue; //it should be higher than the per row elements
			$limits[] = JHtml::_('select.option', 'limit='.$seq,$seq);
		}

		$js='onchange="window.top.location=\''.$url.'\'+this.options[this.selectedIndex].value"';
		$selected ='limit='.$this->limit;
		$html = JHtml::_('select.genericlist',  $limits,  'limit', 'class="inputbox" size="1"'.$js, 'value', 'text', $selected);

		return $html;
	}

	/**
	 * Creates the static part of the uri where the limit var will be added
	 *
	 * @package customfilters
	 * @since 1.0
	 * @author Sakis Terz
	 */
	public function getStatURI()
	{
		$jinput=JFactory::getApplication()->input;
		$query_ar=$this->getVarsArray();
		//print_r($query_ar);
		if(count($query_ar)>0){
			$query_ar['option']= 'com_customfilters';
			$query_ar['view']= $jinput->getCmd('view','');
		}
		$u=JFactory::getURI();
		$query=$u->buildQuery($query_ar);
		$uri='index.php?'.$query;
		return $uri;
	}

	/**
	 * Creates and array with the vars that will be used
	 *
	 * @return array
	 */
	public function getVarsArray()
	{
		$jinput=JFactory::getApplication()->input;
		$query_ar=array();
		$inputs=CfInput::getInputs();
		$query_ar=CfOutput::getOutput($inputs);

		$itemId=$jinput->get('Itemid',0,'int');
		if(!empty($itemId))$query_ar['Itemid']=$itemId;
		//print_r($query_ar);
		return $query_ar;
	}

	/**
	 * Get the Order By Select List
	 * Overrides the function originaly written by Kohl Patrick (Virtuemart parent class)
	 *
	 * @author 	Sakis Terz
	 * @access	public
	 * @param 	int	    The category id
	 *
	 * @return 	array	the orderBy HTML List and the manufacturers list
	 **/
	public function getOrderByList($virtuemart_category_id=false, $default_order_by, $order_by, $order_dir='ASC')
	{
	    $app = JFactory::getApplication() ;
	    $jinput=$app->input;

	    //load the virtuemart language files
	    if(method_exists('VmConfig', 'loadJLang'))VmConfig::loadJLang('com_virtuemart',true);
	    else{
	        $language=JFactory::getLanguage();
	        $language->load('com_virtuemart');
	    }

	    $orderTxt ='';
	    $orderByLinks='';
	    $first_optLink='';

	    $orderDirTxt=JText::_('COM_VIRTUEMART_'.$order_dir);

	    /* order by link list*/
	    $fields = VmConfig::get('browse_orderby_fields');

	    if(!in_array($default_order_by, $fields))$fields[]=$default_order_by;

	    if (count($fields)>0) {
	        foreach ($fields as $field) {
                // indicates if this is the current option
                $stripped_field = str_replace('`', '', $field);
                if ($field == $order_by || $stripped_field == $order_by) {
                    $selected = true;
                }
                else {
                    $selected = false;
                }

	            //remove the dot from the string in order to use it as lang string
	            $dotps = strrpos($field, '.');
	            if($dotps!==false){
	                $prefix = substr ($field, 0, $dotps + 1);
				    $fieldWithoutPrefix = substr ($field, $dotps + 1);
	            } else {
	                $prefix = '';
	                $fieldWithoutPrefix = $field;
	            }
	            $fieldWithoutPrefix_tmp=$fieldWithoutPrefix;

	            $text = JText::_('COM_VIRTUEMART_'.strtoupper (str_replace(array(',',' '),array('_',''),$fieldWithoutPrefix)));
	            $link = $this->getOrderURI($fieldWithoutPrefix_tmp, $selected, $order_dir);
	            if(!$selected) {
	                $orderByLinks .='<div><a title="'.$text.'" href="'.$link.'" rel="nofollow">'.$text.'</a></div>';
	            }
	            else {
	                $first_optLink='<div class="activeOrder"><a title="'.$orderDirTxt.'" href="'.$link.'" rel="nofollow">'.$text.' '.$orderDirTxt.'</a></div>';
	            }
	        }
	    }

	    //format the final html
	    $orderByHtml='<div class="orderlist">'.$orderByLinks.'</div>';

	    $orderHtml ='
		<div class="orderlistcontainer">
			<div class="title">'.JText::_('COM_VIRTUEMART_ORDERBY').'</div>'
				    .$first_optLink
				    .$orderByHtml
				    .'</div>';

	    //in case of ajax we want the script to be triggered after the results loading
	    $orderHtml .="
			<script type=\"text/javascript\">
		jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()});
		</script>";

	    return array('orderby'=>$orderHtml, 'manufacturer'=>'');
	}

	/**
	 * Creates the href in which each "order by" option should point to
	 *
	 * @author	Sakis Terz
	 * @return	String	The URL
	 * @since 	1.0
	 */
	private function getOrderURI($orderBy,$selected=false, $orderDir='ASC')
	{
	    $u=JFactory::getURI();
	    $input=JFactory::getApplication()->input;
	    $Itemid=$input->get('Itemid');

	    /*
	     * get the inputs
	     * these are validated and sanitized
	     */
	    $input=CfInput::getInputs();

	    /*
	     * Generate the output vars
	     */
	    $output=CfOutput::getOutput($input);

	    $output['option']='com_customfilters';
	    $output['view']='products';

	    if(isset($Itemid)) $output['Itemid']=(int)$Itemid;

	    //add order by var in the query
	    $output['orderby']=$orderBy;
	    //if selected add the order Direction
	    if($selected and $orderDir=='ASC')$output['order']='DESC';
	    else $output['order']='ASC';

	    $query=$u->buildQuery($output);
	    $uri='index.php?'.$query;
	    return JRoute::_($uri);
	}
}