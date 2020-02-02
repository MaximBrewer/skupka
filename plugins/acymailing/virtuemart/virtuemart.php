<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.9.4
 * @author	acyba.com
 * @copyright	(C) 2009-2015 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class plgAcymailingVirtuemart extends JPlugin
{
	var $version = false;
	var $lang = '';

	function plgAcymailingVirtuemart(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'virtuemart');
			$this->params = new acyParameter( $plugin->params );
		}

		$file = ACYMAILING_ROOT.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'version.php';
		if(!file_exists($file)) return false;
		include_once($file);
		$vmversion = new vmVersion();
		if(empty($vmversion->RELEASE)){
			$this->version = vmVersion::$RELEASE;
		}else{
			$this->version = $vmversion->RELEASE;
		}
		$params = JComponentHelper::getParams('com_languages');
		$this->lang = strtolower(str_replace('-','_',$params->get('site', 'en-GB')));
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');

		jimport('joomla.user.helper');
	}

	public function acymailing_getPluginType(){

		$app = JFactory::getApplication();
	 	if(!$this->version || ($this->params->get('frontendaccess') == 'none' && !$app->isAdmin())) return;

	 	$onePlugin = new stdClass();
	 	$onePlugin->name = JText::_('Virtuemart');
	 	$onePlugin->function = 'acymailingtagvirtuemart_show';
	 	$onePlugin->help = 'plugin-virtuemart';

	 	return $onePlugin;
	}

	public function acymailingtagvirtuemart_show(){
		$lang = JFactory::getLanguage();
		$lang->load('com_virtuemart',JPATH_ADMINISTRATOR.'/components/com_virtuemart');
		$lang->load('com_virtuemart',JPATH_ADMINISTRATOR);
		$lang->load('com_virtuemart_shoppers',JPATH_SITE.'/components/com_virtuemart');

		$app = JFactory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.tagvmproduct';
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $paramBase.".filter_order", 'filter_order',(version_compare($this->version,'2.0.0','<' ) ? 'a.product_id' : 'a.virtuemart_product_id'),'cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $paramBase.".filter_order_Dir", 'filter_order_Dir',	'desc',	'word' );
		$pageInfo->search = $app->getUserStateFromRequest( $paramBase.".search", 'search', '', 'string' );
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$pageInfo->lang = $app->getUserStateFromRequest( $paramBase.".lang", 'lang','','string' );
		$pageInfo->contenttype = $app->getUserStateFromRequest( $paramBase.".contenttype", 'contenttype','full','string' );
		$pageInfo->pictheight = $app->getUserStateFromRequest( $paramBase.".pictheight", 'pictheight','150','string' );
		$pageInfo->pictwidth = $app->getUserStateFromRequest( $paramBase.".pictwidth", 'pictwidth','150','string' );
		$pageInfo->pict = $app->getUserStateFromRequest( $paramBase.".pict", 'pict','1','string' );
		$pageInfo->autopict = $app->getUserStateFromRequest( $paramBase.".autopict", 'autopict','1','string' );
		$pageInfo->titlelink = $app->getUserStateFromRequest( $paramBase.".titlelink", 'titlelink','1','string' );
		$pageInfo->autotitlelink = $app->getUserStateFromRequest( $paramBase.".autotitlelink", 'autotitlelink','1','string' );
		$pageInfo->readmore = $app->getUserStateFromRequest( $paramBase.".readmore", 'readmore','1','string' );
		$pageInfo->readmoreauto = $app->getUserStateFromRequest( $paramBase.".readmoreauto", 'readmoreauto','1','string' );
		$pageInfo->wrap = $app->getUserStateFromRequest( $paramBase.".wrap", 'wrap','0','string' );
		$pageInfo->autowrap = $app->getUserStateFromRequest( $paramBase.".autowrap", 'autowrap','0','string' );

		$pageInfo->limit->value = $app->getUserStateFromRequest( $paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$pageInfo->limit->start = $app->getUserStateFromRequest( $paramBase.'.limitstart', 'limitstart', 0, 'int' );

		$db = JFactory::getDBO();

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search).'%\'';
			if(version_compare($this->version,'2.0.0','<' )){
				$filters[] = "a.product_id LIKE $searchVal OR a.product_s_desc LIKE $searchVal OR a.product_name LIKE $searchVal OR a.product_sku LIKE $searchVal";
			}else{
				$filters[] = "a.virtuemart_product_id LIKE $searchVal OR b.product_s_desc LIKE $searchVal OR b.product_name LIKE $searchVal OR a.product_sku LIKE $searchVal";
			}
		}

		$whereQuery = '';
		if(!empty($filters)){
			$whereQuery = ' WHERE ('.implode(') AND (',$filters).')';
		}

		if(version_compare($this->version,'2.0.0','<' )){
			$query = 'SELECT SQL_CALC_FOUND_ROWS a.product_id,a.product_s_desc,a.product_sku,a.product_name FROM #__vm_product as a';
		}else{
			$query = 'SELECT SQL_CALC_FOUND_ROWS a.virtuemart_product_id as \'product_id\' ,b.product_s_desc,a.product_sku,b.product_name FROM #__virtuemart_products as a';
			$query .= ' LEFT JOIN #__virtuemart_products_'.$this->lang.' as b ON a.virtuemart_product_id = b.virtuemart_product_id';
		}

		if(!empty($whereQuery)) $query.= $whereQuery;
		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$db->setQuery($query,$pageInfo->limit->start,$pageInfo->limit->value);
		$rows = $db->loadObjectList();

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search,$rows);
		}

		$db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value );

		$tabs = acymailing_get('helper.acytabs');
		echo $tabs->startPane( 'vmproduct_tab');
		echo $tabs->startPanel( JText::_( 'ACY_PRODUCTS' ), 'vm_product');

	?>
		<br style="font-size:1px"/>
		<script language="javascript" type="text/javascript">
		<!--
			var selectedContents = new Array();
			function applyContent(contentid,rowClass)
			{
				var tmp = selectedContents.indexOf(contentid)
				if(tmp != -1)
				{
					window.document.getElementById('content'+contentid).className = rowClass;
					delete selectedContents[tmp];
				}else{
					window.document.getElementById('content'+contentid).className = 'selectedrow';
					selectedContents.push(contentid);
				}
				updateTag();
			}

			function updateTag()
			{
				var tag = '';
				var otherinfo = '';
				for(var i=0; i < document.adminForm.contenttype.length; i++)
				{
					 if(document.adminForm.contenttype[i].checked)
					 {
					 	selectedtype = document.adminForm.contenttype[i].value;
					 	otherinfo += '| type:'+document.adminForm.contenttype[i].value;
					 }
				}

				if(document.adminForm.customfields && document.adminForm.customfields.length)
				{
					tmp=0;
					for(var i=0; i < document.adminForm.customfields.length; i++)
					{
						if (document.adminForm.customfields[i].checked)
						{
							 if(tmp==0)
							 {
								 tmp+=1;
								 otherinfo += "| custom:";
								 otherinfo += document.adminForm.customfields[i].value;
							 }else{
								 otherinfo += ",";
								 otherinfo += document.adminForm.customfields[i].value;
							 }
						}
					}
				}

				for(var i=0; i < document.adminForm.titlelink.length; i++)
				{
					 if(document.adminForm.titlelink[i].checked && document.adminForm.titlelink[i].value == '0')
					 {
					 	otherinfo += '| noclickable';
					 }
				}

				if(document.adminForm.wrap.value && document.adminForm.wrap.value != 0 && !isNaN(document.adminForm.wrap.value))
				{
					otherinfo += "| wrap:"+document.adminForm.wrap.value;
				}

				for(var i=0; i < document.adminForm.readmore.length; i++)
				{
					 if(document.adminForm.readmore[i].checked && document.adminForm.readmore[i].value == '1')
					 {
						 otherinfo += '| readmore';
					 }
				}

				for(var i=0 ; i < document.adminForm.pict.length ; i++)
				{
					if(document.adminForm.pict[i].checked)
					{
						if(document.adminForm.pict[i].value != '0')
							otherinfo += '| pict:'+document.adminForm.pict[i].value;

						if(document.adminForm.pict[i].value == 'resized')
						{
							document.getElementById('pictsize').style.display = '';
							if(document.adminForm.pictwidth.value) otherinfo += '| maxwidth:'+document.adminForm.pictwidth.value;
							if(document.adminForm.pictheight.value) otherinfo += '| maxheight:'+document.adminForm.pictheight.value;
						}else{
							document.getElementById('pictsize').style.display = 'none';
						}
					}
				}

				if(document.adminForm.shoppergroup && document.adminForm.shoppergroup.value && document.adminForm.shoppergroup.value!=0)
				{
					otherinfo += '| shoppergroup:'+document.adminForm.shoppergroup.value;
				}

				if(window.document.getElementById('jflang')  && window.document.getElementById('jflang').value != '')
				{
					otherinfo += '| lang:';
					otherinfo += window.document.getElementById('jflang').value;
				}

				for(var i in selectedContents)
				{
					if(selectedContents[i] && !isNaN(i))
					{
						tag = tag + '{vmproduct:'+selectedContents[i]+otherinfo+'}<br />';
					}
				}
				setTag(tag);
			}
		//-->
		</script>
		<?php
			$choice = array();
			$choice[] = JHTML::_('select.option', "1",JText::_('JOOMEXT_YES'));
			$choice[] = JHTML::_('select.option', "0",JText::_('JOOMEXT_NO'));

			$valImages = array();
			$valImages[] = JHTML::_('select.option', "1",JText::_('JOOMEXT_YES'));
			$valImages[] = JHTML::_('select.option', "resized",JText::_('RESIZED'));
			$valImages[] = JHTML::_('select.option', "0",JText::_('JOOMEXT_NO'));

			$contentType = array();
			$contentType[] = JHTML::_('select.option', "title",JText::_('TITLE_ONLY'));
			$contentType[] = JHTML::_('select.option', "intro",JText::_('INTRO_ONLY'));
			$contentType[] = JHTML::_('select.option', "full",JText::_('FULL_TEXT'));

			if(version_compare($this->version,'2.0.0', '>=')){
				$db->setQuery('SELECT virtuemart_custom_id, custom_title FROM #__virtuemart_customs WHERE published = 1 AND field_type IN ("B","I","S","D","M","X","Y")');
				$customFields = $db->loadObjectList();
			}
		?>
		<table width="100%" class="adminform">
			<tr>
				<td>
					<?php echo JText::_('DISPLAY');?>
				</td>
				<td colspan="2">
				<?php echo JHTML::_('acyselect.radiolist', $contentType, 'contenttype' , 'size="1" onclick="updateTag()"', 'value', 'text', $pageInfo->contenttype); ?>
				</td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
						$jflanguages->onclick = 'onclick="updateTag()"';
						echo $jflanguages->display('lang',$pageInfo->lang); ?>
				</td>
			</tr>
			<?php
				if(!empty($customFields)){
					echo '<tr><td>'.JText::_('EXTRA_FIELDS').'</td>';
					$i = 1;
					foreach($customFields as $oneParam){
						if($i == 4){
							$i = 1;
							echo '</tr><tr><td/>' ;
						}
						echo '<td><input value="'.$oneParam->virtuemart_custom_id.'" name="customfields" id="cf_'.$oneParam->virtuemart_custom_id.'" type="checkbox" onclick="updateTag();" /> ';
						echo '<label for="cf_'.$oneParam->virtuemart_custom_id.'">'.$oneParam->custom_title.'</label></td>';
						$i++;
					}
					while($i != 4){
						$i++;
						echo '<td/>';
					}
					echo '</tr>';
				}
				if(version_compare($this->version,'2.0.0', '>=')){
					$db->setQuery('SELECT virtuemart_shoppergroup_id, shopper_group_name FROM #__virtuemart_shoppergroups WHERE published = 1');
					$shoppergroups = $db->loadAssocList();
				 	if(empty($shoppergroups)) $shoppergroups = array();
				 	foreach($shoppergroups as &$oneShoppergroup){
				 		$oneShoppergroup['shopper_group_name'] = JText::_($oneShoppergroup['shopper_group_name']);
				 	}
				 	$firstVal = array('shopper_group_name' => ' - - - ','virtuemart_shoppergroup_id'=>0);
				 	array_unshift($shoppergroups,$firstVal);
				 	echo '<tr><td>'.JText::_('COM_VIRTUEMART_PAYMENTMETHOD_FORM_SHOPPER_GROUP').'</td><td colspan="3">'.JHTML::_('select.genericlist', $shoppergroups, 'shoppergroup' , 'size="1" style="width:150px" onchange="updateTag();"','virtuemart_shoppergroup_id','shopper_group_name').'</td></tr>';
				}
			?>
			<tr>
				<td nowrap="nowrap" valign="top"><?php echo JText::_('DISPLAY_PICTURES'); ?></td>
				<td nowrap="nowrap">
					<?php echo JHTML::_('acyselect.radiolist', $valImages, 'pict' , 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->pict); ?>
					<span id="pictsize" <?php if($pageInfo->pict != 'resized') echo 'style="display:none;"'; ?> ><br />
						<?php echo JText::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidth" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;" />
						x <?php echo JText::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheight" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictheight; ?>" style="width:30px;" />
					</span>
				</td>
				<td nowrap="nowrap" valign="top">
					<?php echo JText::_('CLICKABLE_TITLE');?>
				</td>
				<td nowrap="nowrap" valign="top">
					<?php echo JHTML::_('acyselect.radiolist', $choice, 'titlelink' , 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->titlelink);?>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap"><?php echo JText::_('JOOMEXT_READ_MORE'); ?></td>
				<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $choice, 'readmore' , 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->readmore); ?>
				<td nowrap="nowrap" colspan="2"><?php echo JText::sprintf('TRUNCATE_AFTER','<input type="text" name="wrap" style="width:50px" value="'.$pageInfo->wrap.'" onchange="updateTag();"/>'); ?></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width="100%">
					<input placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $pageInfo->search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'JOOMEXT_GO' ); ?></button>
					<button class="btn" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo JText::_( 'JOOMEXT_RESET' ); ?></button>
				</td>
			</tr>
		</table>

		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<thead>
				<tr>
					<th class="title"></th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_( 'FIELD_TITLE'), (version_compare($this->version,'2.0.0','<' ) ? 'a.product_name' : 'b.product_name'), $pageInfo->filter->order->dir,$pageInfo->filter->order->value ); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_( 'ACY_DESCRIPTION'), (version_compare($this->version,'2.0.0','<' ) ? 'a.product_s_desc' : 'b.product_s_desc'), $pageInfo->filter->order->dir,$pageInfo->filter->order->value ); ?>
					</th>
					<th class="title titleid">
						<?php echo JHTML::_('grid.sort',   JText::_( 'ACY_ID' ), (version_compare($this->version,'2.0.0','<' ) ? 'a.product_id' : 'a.virtuemart_product_id'), $pageInfo->filter->order->dir, $pageInfo->filter->order->value ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">
						<?php echo $pagination->getListFooter(); ?>
						<?php echo $pagination->getResultsCounter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					$k = 0;
					for($i = 0 ; $i<count($rows) ; $i++){
						$row =& $rows[$i];
				?>
					<tr id="content<?php echo $row->product_id?>" class="<?php echo "row$k"; ?>" onclick="applyContent(<?php echo $row->product_id.",'row$k'"?>);" style="cursor:pointer;">
						<td class="acytdcheckbox"></td>
						<td>
						<?php
							echo acymailing_tooltip('SKU : '.$row->product_sku,$row->product_name,'',$row->product_name);
						?>
						</td>
						<td>
						<?php
							echo strip_tags($row->product_s_desc,'<br><p>');
						?>
						</td>
						<td align="center" style="text-align:center" >
							<?php echo $row->product_id; ?>
						</td>
					</tr>
				<?php
						$k = 1-$k;
					}
				?>
			</tbody>
		</table>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>" />
	<?php

	echo $tabs->endPanel();
	echo $tabs->startPanel( JText::_( 'TAG_CATEGORIES' ), 'vm_auto');

	$type = JRequest::getString('type');

	if(version_compare($this->version,'2.0.0','<' )){
		$db->setQuery('SELECT a.*,b.* FROM `#__vm_category` as a LEFT JOIN `#__vm_category_xref` as b ON a.category_id = b.category_child_id ORDER BY `list_order`');
	}else{
		$db->setQuery('SELECT a.*,b.*,c.*,a.virtuemart_category_id as category_id FROM `#__virtuemart_categories` as a LEFT JOIN `#__virtuemart_category_categories` as b ON a.virtuemart_category_id = b.category_child_id LEFT JOIN `#__virtuemart_categories_'.$this->lang.'` as c ON a.virtuemart_category_id = c.virtuemart_category_id ORDER BY a.`ordering`');
	}

	$categories = $db->loadObjectList('category_id');

	$this->cats = array();
	if(!empty($categories)){
		foreach($categories as $oneCat){
			$this->cats[$oneCat->category_parent_id][] = $oneCat;
		}
	}

	?>
	<script language="javascript" type="text/javascript">
		<!--
			var selectedCat = new Array();
			function applyAutoProduct(catid,rowClass)
			{
				if(selectedCat[catid]){
					window.document.getElementById('product_cat'+catid).className = rowClass;
					delete selectedCat[catid];
				}else{
					window.document.getElementById('product_cat'+catid).className = 'selectedrow';
					selectedCat[catid] = 'product';
				}

				updateTagAuto();
			}

			function updateTagAuto()
			{
				tag = '{autovmproduct:';

				for(var icat in selectedCat)
				{
					if(selectedCat[icat] == 'product')
					{
						tag += icat+'-';
					}
				}

				for(var i=0; i < document.adminForm.contenttypeauto.length; i++)
				{
					 if (document.adminForm.contenttypeauto[i].checked)
					 {
					 	tag += '| type:'+document.adminForm.contenttypeauto[i].value;
					 }
				}

				if(document.adminForm.customfieldsauto && document.adminForm.customfieldsauto.length)
				{
					tmp=0;
					for(var i=0; i < document.adminForm.customfieldsauto.length; i++)
					{
						if (document.adminForm.customfieldsauto[i].checked)
						{
							 if(tmp==0)
							 {
								 tmp+=1;
								 tag += "| custom:";
								 tag += document.adminForm.customfieldsauto[i].value;
							 }else{
								 tag += ",";
								 tag += document.adminForm.customfieldsauto[i].value;
							 }
						}
					}
				}

				for(var i=0 ; i<document.adminForm.autotitlelink.length ; i++)
				{
					 if(document.adminForm.autotitlelink[i].checked && document.adminForm.autotitlelink[i].value == '0')
					 {
					 	tag += '| noclickable';
					 }
				}

				if(document.adminForm.autowrap.value && document.adminForm.autowrap.value != 0 && !isNaN(document.adminForm.autowrap.value))
				{
					tag += "| wrap:"+document.adminForm.autowrap.value;
				}

				for(var i=0; i < document.adminForm.readmoreauto.length; i++)
				{
					 if (document.adminForm.readmoreauto[i].checked && document.adminForm.readmoreauto[i].value == '1')
					 {
					 	tag += '| readmore';
					 }
				}

				for(var i=0; i < document.adminForm.autopict.length; i++)
				{
					 if (document.adminForm.autopict[i].checked)
					 {
					 	if(document.adminForm.autopict[i].value != '0')
					 		tag += '| pict:'+document.adminForm.autopict[i].value;
						if(document.adminForm.autopict[i].value == 'resized')
						{
					 		document.getElementById('pictsizeauto').style.display = '';
					 		if(document.adminForm.pictwidthauto.value) tag += '| maxwidth:'+document.adminForm.pictwidthauto.value;
					 		if(document.adminForm.pictheightauto.value) tag += '| maxheight:'+document.adminForm.pictheightauto.value;
					 	}else{
					 		document.getElementById('pictsizeauto').style.display = 'none';
					 	}
					 }
				}

				if(document.adminForm.shoppergroupauto && document.adminForm.shoppergroupauto.value && document.adminForm.shoppergroupauto.value!=0)
				{
					tag += '| shoppergroup:'+document.adminForm.shoppergroupauto.value;
				}

				if(document.adminForm.manufacturer && document.adminForm.manufacturer.value && document.adminForm.manufacturer.value!=0)
				{
					tag += '| manu:'+document.adminForm.manufacturer.value;
				}

				if(document.adminForm.min_article && document.adminForm.min_article.value && document.adminForm.min_article.value!=0)
				{
					tag += '| min:'+document.adminForm.min_article.value;
				}

				if(document.adminForm.max_article.value && document.adminForm.max_article.value!=0)
				{
					tag += '| max:'+document.adminForm.max_article.value;
				}

				if(document.adminForm.contentorder.value){
					tag += '| order:'+document.adminForm.contentorder.value+","+document.adminForm.contentorderdir.value;
				}

				if(document.adminForm.contentfilter && document.adminForm.contentfilter.value)
				{
					tag += document.adminForm.contentfilter.value;
				}

				if(window.document.getElementById('jflangvm')  && window.document.getElementById('jflangvm').value != '')
				{
					tag += '| lang:';
					tag += window.document.getElementById('jflangvm').value;
				}

				if(document.adminForm.cols.value > 1)
				{
					tag += '| cols:'+document.adminForm.cols.value;
				}

				tag += '}';

				setTag(tag);
			}
		-->
	</script>
	<table width="100%" class="adminform">
		<tr>
			<td>
				<?php echo JText::_('DISPLAY');?>
			</td>
			<td colspan="2">
			<?php echo JHTML::_('acyselect.radiolist', $contentType, 'contenttypeauto' , 'size="1" onclick="updateTagAuto();"', 'value', 'text', 'full'); ?>
			</td>
			<td>
				<?php $jflanguages = acymailing_get('type.jflanguages');
				if(!empty($jflanguages->values)){
					$jflanguages->id = 'jflangvm'; $jflanguages->onclick = 'onchange="updateTagAuto();"'; echo $jflanguages->display('language');
				}?>
			</td>
		</tr>
		<?php
			if(!empty($customFields))
			{
				echo '<tr><td>'.JText::_('EXTRA_FIELDS').'</td>';
				$i = 1;
				foreach($customFields as $oneParam)
				{
					if($i == 4)
					{
						$i = 1;
						echo '</tr><tr><td/>' ;
					}
					echo '<td><input value="'.$oneParam->virtuemart_custom_id.'" name="customfieldsauto" id="cf_'.$oneParam->virtuemart_custom_id.'auto" type="checkbox" onclick="updateTagAuto();" /> ';
					echo '<label for="cf_'.$oneParam->virtuemart_custom_id.'auto">'.$oneParam->custom_title.'</label></td>';
					$i++;
				}
				while($i != 4)
				{
					$i++;
					echo '<td/>';
				}
				echo '</tr>';
			}
			if(version_compare($this->version,'2.0.0', '>=')){
			 	echo '<tr><td>'.JText::_('COM_VIRTUEMART_PAYMENTMETHOD_FORM_SHOPPER_GROUP').'</td><td colspan="3">'.JHTML::_('select.genericlist', $shoppergroups, 'shoppergroupauto' , 'size="1" style="width:150px" onchange="updateTagAuto();"','virtuemart_shoppergroup_id','shopper_group_name').'</td></tr>';
			}
		?>
		<tr>
			<td valign="top" nowrap="nowrap">
				<?php echo JText::_('DISPLAY_PICTURES');?>
			</td>
			<td nowrap="nowrap">
				<?php echo JHTML::_('acyselect.radiolist', $valImages, 'autopict' , 'size="1" onclick="updateTagAuto();"', 'value', 'text', $pageInfo->autopict); ?>
				<span id="pictsizeauto" <?php if($pageInfo->autopict != 'resized') echo 'style="display:none;"'; ?> ><br />
					<?php echo JText::_('CAPTCHA_WIDTH') ?>
					<input name="pictwidthauto" type="text" onchange="updateTagAuto();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;" />
					x <?php echo JText::_('CAPTCHA_HEIGHT') ?>
					<input name="pictheightauto" type="text" onchange="updateTagAuto();" value="<?php echo $pageInfo->pictheight; ?>" style="width:30px;" />
				</span>
			</td>
			<td nowrap="nowrap" valign="top">
				<?php echo JText::_('CLICKABLE_TITLE');?>
			</td>
			<td nowrap="nowrap" valign="top">
				<?php echo JHTML::_('acyselect.radiolist', $choice, 'autotitlelink' , 'size="1" onclick="updateTagAuto();"', 'value', 'text', $pageInfo->autotitlelink);?>
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap" valign="top"><?php echo JText::_('JOOMEXT_READ_MORE'); ?></td>
			<td nowrap="nowrap"><?php echo JHTML::_('acyselect.radiolist', $choice, 'readmoreauto' , 'size="1" onclick="updateTagAuto();"', 'value', 'text', $pageInfo->readmoreauto); ?>
			<td nowrap="nowrap" colspan="2"><?php echo JText::sprintf('TRUNCATE_AFTER','<input type="text" name="autowrap" style="width:50px" value="'.$pageInfo->autowrap.'" onchange="updateTagAuto();"/>'); ?></td>
		</tr>
		<tr>
			<td>
			<?php echo JText::_('MAX_ARTICLE'); ?>
			 </td>
			 <td>
			 	<input name="max_article" type="text" style="width:50px" value="20" onchange="updateTagAuto();"/>
			</td>
			<td>
				<?php echo JText::_('ACY_ORDER'); ?>
			 </td>
			 <td>
			 	<?php
					if(version_compare($this->version,'2.0.0','<')){
						$values = array('product_id' => 'ACY_ID', 'cdate' => 'CREATED_DATE', 'mdate' => 'MODIFIED_DATE', 'product_name' => 'FIELD_TITLE', 'product_price' => 'Price');
					}else{
						$values = array('virtuemart_product_id' => 'ACY_ID', 'created_on' => 'CREATED_DATE', 'modified_on' => 'MODIFIED_DATE', 'product_price' => 'Price', 'product_name' => 'FIELD_TITLE');
					}
					echo $this->acypluginsHelper->getOrderingField($values, '', 'DESC');
				?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo 'Manufacturer'; ?>
			 </td>
			 <td>
			 	<?php
			 	if(version_compare($this->version,'2.0.0','<' )){
			 		$db->setQuery('SELECT mf_name, manufacturer_id FROM #__vm_manufacturer ORDER BY mf_name ASC');
			 	}else{
			 		$db->setQuery('SELECT mf_name, a.virtuemart_manufacturer_id as manufacturer_id FROM #__virtuemart_manufacturers as a LEFT JOIN #__virtuemart_manufacturers_'.$this->lang.' as b ON a.virtuemart_manufacturer_id = b.virtuemart_manufacturer_id ORDER BY mf_name ASC');
			 	}

			 	$manufacturers = $db->loadAssocList();
			 	if(empty($manufacturers)) $manufacturers = array();
			 	$firstVal = array('mf_name' => ' - - - ','manufacturer_id'=>0);
			 	array_unshift($manufacturers,$firstVal);
			 	echo JHTML::_('select.genericlist', $manufacturers, 'manufacturer' , 'size="1" style="width:150px" onchange="updateTagAuto();"','manufacturer_id','mf_name'); ?>
			</td>
			<td>
				<?php echo JText::_('FIELD_COLUMNS'); ?>
			 </td>
			 <td>
			 	<select name="cols" style="width:150px" onchange="updateTagAuto();" size="1">
			 		<?php for($o = 1;$o<11;$o++) echo '<option value="'.$o.'">'.$o.'</option>'; ?>
			 	</select>
			</td>
		</tr>
		<?php if($type == 'autonews') { ?>
		<tr>
			<td>
			<?php 	echo JText::_('MIN_ARTICLE'); ?>
			 </td>
			 <td>
			 <input name="min_article" type="text" style="width:50px" value="1" onchange="updateTagAuto();"/>
			 </td>
			<td>
			<?php echo JText::_('ACY_FILTER'); ?>
			 </td>
			 <td>
			 	<?php $filter = acymailing_get('type.contentfilter'); $filter->onclick = 'updateTagAuto();'; echo $filter->display('contentfilter','|filter:created'); ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
	<?php $k=0; echo $this->displayChildren(0,$k); ?>
	</table>
	<?php

	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_('ACY_COUPON'),'vm_coupon');
	$value= array();
 	$value[] = JHTML::_('select.option', 'percent',JText::_('COUPON_PERCENT'));
 	$value[] = JHTML::_('select.option', 'total',JText::_('COUPON_TOTAL'));
 	$percent_total = JHTML::_('acyselect.radiolist', $value, 'coupon_percent' , 'onclick="updateTagCoupon();"', 'value', 'text', 'percent');

 	$value= array();
 	$value[] = JHTML::_('select.option', 'permanent',JText::_('COUPON_PERMANENT'));
 	$value[] = JHTML::_('select.option', 'gift',JText::_('COUPON_GIFT'));
 	$permanent = JHTML::_('acyselect.radiolist', $value, 'coupon_permanent' , 'onclick="updateTagCoupon();"', 'value', 'text', 'gift');

	?>
	<script language="javascript" type="text/javascript">
	<!--
		function updateTagCoupon(){
			tagname = '';

			tagname += document.adminForm.coupon_name.value;

			for(var i=0; i < document.adminForm.coupon_percent.length; i++){
				 if (document.adminForm.coupon_percent[i].checked){ tagname += '| tcoupon:'+document.adminForm.coupon_percent[i].value; }
			}

			for(var i=0; i < document.adminForm.coupon_permanent.length; i++){
				 if (document.adminForm.coupon_permanent[i].checked){ tagname += '| type:'+document.adminForm.coupon_permanent[i].value; }
			}

			tagname += '| value:'+document.adminForm.coupon_value.value;

			if(document.adminForm.coupon_min_order_value.value)
			{
				tagname += '| minordervalue:'+document.adminForm.coupon_min_order_value.value;
			}
	<?php
		if(!version_compare($this->version,'2.0.0','<' ))
		{
	?>
			for(var i=0; i < document.adminForm.expiry.length; i++)
			{
				if(document.adminForm.expiry[i].checked && document.adminForm.expiry[i].value != '0')
				{
					tagname += '| expiry:'+document.adminForm.expiry[i].value;
					if(document.adminForm.expiry[i].value == 'chosen')
					{
						tagname += '| valexpiry:'+document.adminForm.to_expiry_date.value;
					}else{
						tagname += '| valexpiry:'+document.adminForm.expiry_date.value;
					}
				}
			}

			for(var i=0; i < document.adminForm.start.length; i++)
			{
				if(document.adminForm.start[i].checked && document.adminForm.start[i].value != '0')
				{
					tagname += '| start:'+document.adminForm.start[i].value;
					if(document.adminForm.start[i].value == 'chosen')
					{
						tagname += '| valstart:'+document.adminForm.to_start_date.value;
					}else{
						tagname += '| valstart:'+document.adminForm.start_date.value;
					}
				}
			}
	<?php } ?>
			setTag('{vmcoupon:'+tagname+'}');
		}
	//-->
	</script>
	<table class="adminlist table table-striped table-hover" cellpadding="1">
	<tr><td><?php echo JText::_('COUPON_NAME'); ?></td><td><input id="coupon_name" onchange="updateTagCoupon();" type="text" style="width:160px" value="[name][key][value]"></td><td><?php echo $permanent; ?></td></tr>
	<tr><td><?php echo JText::_('COUPON_VALUE'); ?></td><td><input id="coupon_value" onchange="updateTagCoupon();" type="text" style="width:50px" value=""></td><td><?php echo $percent_total; ?></td></tr>
	<tr><td><?php echo JText::_('COM_VIRTUEMART_COUPON_VALUE_VALID_AT'); ?></td><td><input id="coupon_min_order_value" onchange="updateTagCoupon();" type="text" style="width:50px" value=""></td><td/></tr>
	<?php
		if(!version_compare($this->version,'2.0.0','<' ))
		{
	?>
		<tr>
			<td>
				<?php echo JText::_('COM_VIRTUEMART_COUPON_START'); ?>
			</td>
			<td>
				<input checked type="radio" name="start" id="start0" value="0" size="1" onclick="updateTagCoupon();">
				<label for="start0" id="start0-lbl" class="radiobtn"><?php echo JText::_('ACY_NONE'); ?></label><br />

				<input type="radio" name="start" id="startchosen" value="chosen" size="1" onclick="updateTagCoupon();">
				<?php
					echo JHTML::_('calendar', '', 'to_start_date','to_date','%Y-%m-%d',array('style'=>'width:80px', 'onchange'=>'updateTagCoupon()', 'onclick'=>'document.adminForm.start[1].checked = true;updateTagCoupon();'));
				?>
				<br />

				<input type="radio" name="start" id="startdelayed" value="delayed" size="1" onclick="updateTagCoupon();">
				<?php
					$delayType = acymailing_get('type.delay');
					$delayType->onChange = "document.adminForm.start[2].checked = true;updateTagCoupon();";
					echo $delayType->display('start_date',7776000,3);
				?>
			</td>
			<td/>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_VIRTUEMART_COUPON_EXPIRY'); ?>
			</td>
			<td>
				<input checked type="radio" name="expiry" id="expiry0" value="0" size="1" onclick="updateTagCoupon();">
				<label for="expiry0" id="expiry0-lbl" class="radiobtn"><?php echo JText::_('ACY_NONE'); ?></label><br />

				<input type="radio" name="expiry" id="expirychosen" value="chosen" size="1" onclick="updateTagCoupon();">
				<?php
					echo JHTML::_('calendar', '', 'to_expiry_date','from_date','%Y-%m-%d',array('style'=>'width:80px', 'onchange'=>'updateTagCoupon()', 'onclick'=>'document.adminForm.expiry[1].checked = true;updateTagCoupon();'));
				?>
				<br />

				<input type="radio" name="expiry" id="expirydelayed" value="delayed" size="1" onclick="updateTagCoupon();">
				<?php
					$delayType = acymailing_get('type.delay');
					$delayType->onChange = "document.adminForm.expiry[2].checked = true;updateTagCoupon();";
					echo $delayType->display('expiry_date',7776000,3);
				?>
			</td>
			<td/>
		</tr>
	<?php
		} ?>
	</table>

	<?php

	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_('RECEIVER_INFORMATION'),'vm_userinfo');
	echo '<br style="font-size:1px"/>';
	echo '<table class="adminlist table table-striped table-hover" cellpadding="1">';

	if(version_compare($this->version,'2.0.0','<' )){
		$fields = acymailing_getColumns('#__vm_user_info');
	}else{
		$fields = acymailing_getColumns('#__virtuemart_userinfos');
	}

	$k = 0;
	foreach($fields as $fieldname => $oneField){
		if(preg_match('#_(on|id|by)$#i',$fieldname)) continue;
		$type = '';
		if(strpos(strtolower($oneField),'date') !== false) $type = '|type:date';
		echo '<tr style="cursor:pointer" class="row'.$k.'" onclick="setTag(\'{vmfield:'.$fieldname.$type.'}\');insertTag();" ><td class="acytdcheckbox"></td><td>'.$fieldname.'</td></tr>';
		$k = 1-$k;
	}
	echo '</table>';

	echo $tabs->endPanel();
	echo $tabs->endPane();

	 }

	 private function displayChildren($parentid,&$k,$level = 0){
	 	if(empty($this->cats[$parentid])) return;

	 	foreach($this->cats[$parentid] as $oneCat){
	 		$k = 1 - $k;
	 		echo '<tr id="product_cat'.$oneCat->category_id.'" class="row'.$k.'" onclick="applyAutoProduct('.$oneCat->category_id.',\'row'.$k.'\');" style="cursor:pointer;"><td class="acytdcheckbox"></td><td>';
			echo str_repeat('- - ',$level).$oneCat->category_name.'</td></tr>';
	 		$this->displayChildren($oneCat->category_id,$k,$level+1);
		}
	 }

	 public function acymailing_replacetags(&$email,$send = true){
	 	if(!$this->version) return;

	 	$this->_replaceAuto($email);
	 	$this->_replaceProducts($email);
 	}

	private function _replaceAuto(&$email){
		$this->acymailing_generateautonews($email);

		if(!empty($this->tags)){
			$email->body = str_replace(array_keys($this->tags),$this->tags,$email->body);
			if(!empty($email->altbody)) $email->altbody = str_replace(array_keys($this->tags),$this->tags,$email->altbody);
		}
	}

	public function acymailing_replaceusertags(&$email,&$user,$send = true){
		if(!$this->version) return;
		$this->_replaceCoupon($email,$user,$send);
		$this->_replaceFields($email,$user,$send);
	}

	private function _replaceFields(&$email,&$user,$send){
		$match = '#{vmfield:(.*)}#Ui';
		$variables = array('subject','body','altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match,$email->$var,$results[$var]) || $found;
			if(empty($results[$var][0])) unset($results[$var]);
		}

		if(!$found) return;

		$pluginsHelper = acymailing_get('helper.acyplugins');
		$db= JFactory::getDBO();

		if(version_compare($this->version,'2.0.0','<')){
			$myquery = "SELECT * FROM #__vm_user_info WHERE user_email = ".$db->Quote($user->email);
		}else{
			$myquery = "SELECT * FROM #__virtuemart_userinfos WHERE virtuemart_user_id = ".intval($user->userid)." AND virtuemart_user_id > 0";
		}

		$db->setQuery($myquery);
		$vmuser = $db->loadObject();

		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($tags[$oneTag])) continue;

				$arguments = explode('|',strip_tags($allresults[1][$i]));
				$field = $arguments[0];
				unset($arguments[0]);
				$mytag = new stdClass();
				$mytag->default = $this->params->get('default_'.$field,'');
				if(!empty($arguments)){
					foreach($arguments as $onearg){
						$args = explode(':',$onearg);
						if(isset($args[1])){
							$mytag->$args[0] = $args[1];
						}else{
							$mytag->$args[0] = 1;
						}
					}
				}

				$tags[$oneTag] = (isset($vmuser->$field) && strlen($vmuser->$field) > 0) ? $vmuser->$field : $mytag->default;

				$pluginsHelper->formatString($tags[$oneTag],$mytag);
			}
		}

		foreach($results as $var => $allresults){
			$email->$var = str_replace(array_keys($tags),$tags,$email->$var);
		}
	}


	private function _replaceCoupon(&$email,&$user,$send){

		$match = '#{vmcoupon:(.*)}#Ui';
		$variables = array('subject','body','altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match,$email->$var,$results[$var]) || $found;
			if(empty($results[$var][0])) unset($results[$var]);
		}

		if(!$found) return;

		$tags = array();
		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($tags[$oneTag])) continue;
				if(empty($user->subid) || !$send){
					$tags[$oneTag] = '<i>Please check your e-mail to see the coupon</i>';
				}else{
					$tags[$oneTag] = $this->generateCoupon($allresults,$i,$user);
				}
			}
		}

		foreach(array_keys($results) as $var){
			$email->$var = str_replace(array_keys($tags),$tags,$email->$var);
		}
	}

	private function generateCoupon(&$allresults,$i,&$user)
	{
	 	$acypluginsHelper = acymailing_get('helper.acyplugins');
		$tag = $acypluginsHelper->extractTag($allresults[1][$i], 'vmcoupon');

		if(empty($tag->type))
		{
			list($percent,$gift,$value,$name) = explode('|',$allresults[1][$i]);
			$tag = new stdClass();
			$tag->value = $value;
			$tag->id = $name;
			$tag->tcoupon = $percent;
			$tag->type = $gift;
		}

		$db = JFactory::getDBO();
		$key = JUserHelper::genrandompassword(5);
		$tag->value = str_replace(',','.',$tag->value);
		$tag->id = str_replace(array('[name]','[subid]','[email]','[key]','[value]'),array($user->name,$user->subid,$user->email,$key,$tag->value),$tag->id);
		if(!empty($tag->expiry))
		{
			if($tag->expiry == "chosen" && !empty($tag->valexpiry))
			{
				$expiry = date( 'Y-m-d H:i:s',acymailing_getTime($tag->valexpiry));
			}
			if($tag->expiry == "delayed" && !empty($tag->valexpiry))
			{
				$expiry = date( 'Y-m-d H:i:s',time()+$tag->valexpiry);
			}
		}

		$start = '';
		if(!empty($tag->start))
		{
			if($tag->start == "chosen" && !empty($tag->valstart))
			{
				$start = date( 'Y-m-d H:i:s',acymailing_getTime($tag->valstart));
			}
			if($tag->start == "delayed" && !empty($tag->valstart))
			{
				$start = date( 'Y-m-d H:i:s',time()+$tag->valstart);
			}
		}

		if(version_compare($this->version,'2.0.0','<' ))
		{
			$db->setQuery('INSERT INTO #__vm_coupons (`coupon_code`,`percent_or_total`,`coupon_type`,`coupon_value`) VALUES ('.$db->Quote($tag->id).','.$db->Quote($tag->tcoupon).','.$db->Quote($tag->type).','.$db->Quote($tag->value).')');
		}else{
			if(!empty($expiry))
				$db->setQuery('INSERT INTO #__virtuemart_coupons (`coupon_code`,`percent_or_total`,`coupon_type`,`coupon_value`,`coupon_start_date`,`coupon_expiry_date`'.(empty($tag->minordervalue) ? '' : ',`coupon_value_valid`').') VALUES ('.$db->Quote($tag->id).','.$db->Quote($tag->tcoupon).','.$db->Quote($tag->type).','.$db->Quote($tag->value).','.$db->Quote(empty($start) ? date('Y-m-d') : $start).','.$db->Quote($expiry).(empty($tag->minordervalue) ? '' : ','.intval($tag->minordervalue)).')');
			else
				$db->setQuery('INSERT INTO #__virtuemart_coupons (`coupon_code`,`percent_or_total`,`coupon_type`,`coupon_value`,`coupon_start_date`'.(empty($tag->minordervalue) ? '' : ',`coupon_value_valid`').') VALUES ('.$db->Quote($tag->id).','.$db->Quote($tag->tcoupon).','.$db->Quote($tag->type).','.$db->Quote($tag->value).','.$db->Quote(empty($start) ? date('Y-m-d') : $start).(empty($tag->minordervalue) ? '' : ','.intval($tag->minordervalue)).')');
		}
		$db->query();

		return $tag->id;
	}

	public function acymailing_generateautonews(&$email)
	{
		if(!$this->version) return;

		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$db = JFactory::getDBO();
		$time = time();

		$tags = $acypluginsHelper->extractTags($email,'autovmproduct');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';
		$this->tags = array();
		$db = JFactory::getDBO();

		if(empty($tags)) return $return;

		foreach($tags as $oneTag => $parameter)
		{
			if(isset($this->tags[$oneTag])) continue;

			$allcats = explode('-',$parameter->id);
			$selectedArea = array();
			foreach($allcats as $oneCat){
				if(empty($oneCat)) continue;
				$selectedArea[] = (int) $oneCat;
			}

			if(version_compare($this->version,'2.0.0','<' )){
				$query = 'SELECT DISTINCT b.`product_id` FROM `#__vm_product_category_xref` as a LEFT JOIN `#__vm_product` as b ON a.product_id = b.product_id';
			}else{
				$query = 'SELECT DISTINCT b.`virtuemart_product_id` FROM `#__virtuemart_product_categories` as a LEFT JOIN `#__virtuemart_products` as b ON a.virtuemart_product_id = b.virtuemart_product_id';
			}
			$where = array();
			if(!empty($parameter->manu)){
				if(version_compare($this->version,'2.0.0','<' )){
					$query .= ' LEFT JOIN #__vm_product_mf_xref as c on c.product_id = a.product_id';
					$where[] = "c.manufacturer_id = ".intval($parameter->manu);
				}else{
					$query .= ' LEFT JOIN #__virtuemart_product_manufacturers as c on c.virtuemart_product_id = a.virtuemart_product_id';
					$where[] = "c.virtuemart_manufacturer_id = ".intval($parameter->manu);
				}
			}
			$orderBy = '';
			if(!empty($parameter->order))
			{
				$ordering = explode(',',$parameter->order);
				if($parameter->order == 'rand' || $ordering[0] == 'rand')
				{
					$orderBy = ' ORDER BY rand()';
				}else{
					$orderBy = ' ORDER BY b.`'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
				}
				if($ordering[0] == 'product_price'){
					if(version_compare($this->version,'2.0.0','<' )){
						$query .= ' LEFT JOIN #__vm_product_price as d on d.product_id = a.product_id';
						$orderBy = ' ORDER BY d.`'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
					}else{
						$query .= ' LEFT JOIN #__virtuemart_product_prices as d on d.virtuemart_product_id = a.virtuemart_product_id';
						$orderBy = ' ORDER BY d.`'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
					}
				}
				if($ordering[0] == 'product_name' && version_compare($this->version,'2.0.0','>=' )){
					$query .= ' LEFT JOIN #__virtuemart_products_'.$this->lang.' AS e ON e.virtuemart_product_id = a.virtuemart_product_id';
					$orderBy = ' ORDER BY e.`'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
				}
			}

			if($this->params->get('stock',0) == '1') $where[] = 'b.product_in_stock > 0';

			if(!empty($selectedArea)){
				if(version_compare($this->version,'2.0.0','<' )){
					$where[] = 'a.category_id IN ('.implode(',',$selectedArea).')';
				}else{
					$where[] = 'a.virtuemart_category_id IN ('.implode(',',$selectedArea).')';
				}
			}

			if(!empty($parameter->featured)) $where[] = version_compare($this->version,'2.0.0','<' ) ? "b.product_special='Y'" : "b.product_special=1";
			if(version_compare($this->version,'2.0.0','<' )){
				if(!empty($parameter->discounted)) $where[] = "b.product_discount_id > 0";
			}

			$where[] = version_compare($this->version,'2.0.0','<' ) ? "b.`product_publish` = 'Y'" : "b.`published` = 1";
			if(!empty($parameter->filter) AND !empty($email->params['lastgenerateddate'])){
				$condition = version_compare($this->version,'2.0.0','<' ) ? 'b.`cdate` >\''.$email->params['lastgenerateddate'].'\'' : "b.`created_on` > '".date( 'Y-m-d H:i:s',$email->params['lastgenerateddate'] - date('Z'))."'";
				if($parameter->filter == 'modify'){
					$condition .= version_compare($this->version,'2.0.0','<' ) ? ' OR b.`mdate` >\''.$email->params['lastgenerateddate'].'\'' : "OR b.`modified_on` > '".date( 'Y-m-d H:i:s',$email->params['lastgenerateddate'] - date('Z'))."'";
				}

				$where[] = $condition;
			}

			$query .= ' WHERE ('.implode(') AND (',$where).')';
			if(!empty($orderBy)){
				$query .= $orderBy;
			}
			if(!empty($parameter->max)) $query .= ' LIMIT '.(int) $parameter->max;

			$db->setQuery($query);
			$allArticles = acymailing_loadResultArray($db);

			if(!empty($parameter->min) AND count($allArticles)< $parameter->min){
				$return->status = false;
				$return->message = 'Not enough products for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min;
			}

			$stringTag = '';
			if(!empty($allArticles))
			{
				if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'autovmproduct.php'))
				{
					ob_start();
					require(ACYMAILING_MEDIA.'plugins'.DS.'autovmproduct.php');
					$stringTag = ob_get_clean();
				}else{
					$arrayElements = array();
					foreach($allArticles as $oneArticleId){
						$args = array();
						$args[] = 'vmproduct:'.$oneArticleId;
						if(!empty($parameter->type)) $args[] = 'type:'.$parameter->type;
						if(!empty($parameter->custom)) $args[] = 'custom:'.$parameter->custom;
						if(!empty($parameter->lang)) $args[] = 'lang:'.$parameter->lang;
						if(!empty($parameter->wrap)) $args[] = 'wrap:'.$parameter->wrap;
						if(!empty($parameter->wraptitle)) $args[] = 'wraptitle:'.$parameter->wraptitle;
						if(!empty($parameter->readmore)) $args[] = 'readmore';
						if(!empty($parameter->language)) $args[] = 'language:'.$parameter->language;
						if(isset($parameter->noprice)) $args[] = 'noprice';
						if(isset($parameter->onlyfinalprice)) $args[] = 'onlyfinalprice';
						if(!empty($parameter->itemid)) $args[] = 'itemid:'.$parameter->itemid;
						if(!empty($parameter->shoppergroup)) $args[] = 'shoppergroup:'.$parameter->shoppergroup;
						if(isset($parameter->pict)) $args[] = 'pict:'.$parameter->pict;
						if(!empty($parameter->maxwidth)) $args[] = 'maxwidth:'.$parameter->maxwidth;
						if(!empty($parameter->maxheight)) $args[] = 'maxheight:'.$parameter->maxheight;
						if(!empty($parameter->shoppergroup)) $args[] = 'shoppergroup:'.$parameter->shoppergroup;
						if(!empty($parameter->noclickable)) $args[] = 'noclickable';
						$arrayElements[] = '{'.implode('|',$args).'}';
					}
					$stringTag = $acypluginsHelper->getFormattedResult($arrayElements,$parameter);
				}
			}
			$this->tags[$oneTag] = $stringTag;
		}
		return $return;
	}

 	private function _replaceProducts(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$tags = $acypluginsHelper->extractTags($email,'vmproduct');
		if(empty($tags)) return;

		$this->newslanguage = new stdClass();
		if(!empty($email->language)){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT lang_id, lang_code FROM #__languages WHERE sef = '.$db->quote($email->language).' LIMIT 1');
			$emaillangcode = $db->loadObject();
			if(!empty($emaillangcode)) $this->newslanguage = $emaillangcode;
		}

		$this->mailerHelper = acymailing_get('helper.mailer');
		$lang = JFactory::getLanguage();
		$lang->load('com_virtuemart', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart');

		if(version_compare($this->version,'2.0.0','>=' )){
			include_once(ACYMAILING_ROOT.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
			include_once(ACYMAILING_ROOT.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'models'.DS.'product.php');
	 		vmConfig::loadConfig();
	 		if(method_exists('vmConfig', 'setdbLanguageTag')) vmConfig::setdbLanguageTag();
		}

		$this->readmore = empty($email->template->readmore) ? JText::_('JOOMEXT_READ_MORE') : '<img src="'.ACYMAILING_LIVE.$email->template->readmore.'" alt="'.JText::_('JOOMEXT_READ_MORE',true).'" />';

		$resultshtml = array();
		$resultstext = array();
		foreach($tags as $i => $params)
		{
			if(isset($htmlreplace[$i])) continue;
			$content = $this->_replaceProduct($tags[$i]);
			$resultshtml[$i] = $content;
			$resultstext[$i] = $this->mailerHelper->textVersion($content,true);
		}
		$email->body = str_replace(array_keys($resultshtml),$resultshtml,$email->body);
		$email->altbody = str_replace(array_keys($resultstext),$resultstext,$email->altbody);
	}

	private function _replaceProduct(&$tag){
	 	$tag->id = intval($tag->id);

	 	if(empty($tag->shoppergroup)) $tag->shoppergroup = intval($this->params->get('shopper_group_id'));
	 	if(empty($tag->itemid)) $tag->itemid = intval($this->params->get('itemid'));

		if(!empty($this->newslanguage) && !empty($this->newslanguage->lang_code) && (empty($tag->language) || empty($tag->lang))){
			if(empty($tag->lang)){
				$tag->lang = $this->newslanguage->lang_code;
				if(version_compare($this->version, '2.0.0', '<')) $tag->lang .= ','.$this->newslanguage->lang_id;
			}
			if(empty($tag->language) && !version_compare($this->version, '2.0.0', '<')) $tag->language = $this->newslanguage->lang_code;
		}

		if(version_compare($this->version,'2.0.0','<' )){
			$result = $this->_getProduct1($tag);
		}else{
			$result = $this->_getProduct2($tag);
		}

		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$result = $acypluginsHelper->managePicts($tag, $result);

		return $result;
	 }

	private function _getProduct2($tag){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$db = JFactory::getDBO();
	 	$tag->shoppergroup = empty($tag->shoppergroup) ? 0 : intval($tag->shoppergroup);
	 	$vmProductModel = new VirtueMartModelProduct();
		if(version_compare($this->version,'3.0.0','<')) $product = $vmProductModel->getProduct($tag->id,true,true,false,1,false,array($tag->shoppergroup));
	 	else $product = $vmProductModel->getProduct($tag->id,true,true,false);
		$vmProductModel->addImages($product);

		if(!empty($tag->language)){
			$tag->language = strtolower(preg_replace('#[^a-z]#i','_',trim($tag->language)));
			if(!empty($tag->language) && in_array($db->getPrefix().'virtuemart_products_'.$tag->language,$db->getTableList())){
				$db->setQuery("SELECT * FROM #__virtuemart_products_".$tag->language." WHERE virtuemart_product_id = ".intval($tag->id));
				$lproduct = $db->loadObject();

				if(!empty($lproduct)){
					foreach($lproduct as $var => $val){
						$product->$var = $val;
					}
				}
			}
		}

		$varFields = array();
		foreach($product as $fieldName => $oneField){
			if(!is_array($oneField) && !is_object($oneField)) $varFields['{'.$fieldName.'}'] = $oneField;
		}

		$description = (empty($tag->type) || $tag->type == 'full') ? $product->product_desc : $product->product_s_desc;
		$description = $acypluginsHelper->wrapText($description, $tag);
		$varFields['{description}'] = $description;

		$link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id;
		if(!empty($tag->lang)) $link .= '&lang='.$tag->lang;
		if(!empty($tag->itemid)) $link .= '&Itemid='.$tag->itemid;
		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		if(version_compare($this->version,'3.0.0','>=')){
			$product->product_currency = empty($product->allPrices[0]['product_currency']) ? '0' : $product->allPrices[0]['product_currency'];
			$product->product_price = empty($product->allPrices[0]['product_price']) ? 0 : $product->allPrices[0]['product_price'];
			$allPrices = $vmProductModel->loadProductPrices($product->virtuemart_product_id,array($tag->shoppergroup),true);
			if(!empty($allPrices)){
				$product->allPrices = $allPrices;
				$product->selectedPrice = 0;
				$lowestPrice = $allPrices[0]['override'] == '0' ? (float)$allPrices[0]['product_price'] : (float)$allPrices[0]['product_override_price'];
				foreach($allPrices as $i => $oneAvailablePrice){
					$thisPrice = $oneAvailablePrice['override'] == '0' ? (float)$oneAvailablePrice['product_price'] : (float)$oneAvailablePrice['product_override_price'];
					if($thisPrice >= $lowestPrice) continue;
					$product->selectedPrice = $i;
					$lowestPrice = $thisPrice;
				}

				$calculationHelper = calculationHelper::getInstance();
				$productPrice = $calculationHelper->getProductPrices($product);
				if(!empty($productPrice)){
					foreach($productPrice as $key => $oneValue){
						$product->$key = $oneValue;
						$product->prices[$key] = $oneValue;
					}

					$query = 'SELECT tax.*, taxsg.virtuemart_shoppergroup_id ' .
							'FROM #__virtuemart_calcs AS tax ' .
							'LEFT JOIN #__virtuemart_calc_shoppergroups AS taxsg ' .
								'ON taxsg.virtuemart_calc_id = tax.virtuemart_calc_id ' .
							'WHERE (tax.calc_kind = "VatTax" OR tax.calc_kind = "Tax" OR tax.calc_kind = "DBTax") ' .
								'AND (tax.publish_up = "0000-00-00 00:00:00" OR tax.publish_up < \''.date('Y-m-d H:i:s', time()).'\') ' .
								'AND (tax.publish_down = "0000-00-00 00:00:00" OR tax.publish_down > \''.date('Y-m-d H:i:s', time()).'\')';
					if(!empty($productPrice['product_tax_id'])) $query .= ' AND tax.virtuemart_calc_id = '.intval($productPrice['product_tax_id']);
					$db->setQuery($query);
					$productTax = $db->loadAssocList();
					$tax = array();
					$vatTax = array();
					$dbTax = array();
					if(!empty($productTax)){
						foreach($productTax as $i => $oneRule){
							if(!empty($oneRule['virtuemart_shoppergroup_id']) && $oneRule['virtuemart_shoppergroup_id'] != intval($tag->shoppergroup)){
								unset($productTax[$i]);
								continue;
							}
							if($oneRule['calc_kind'] == "Tax") $tax[] = $oneRule;
							if($oneRule['calc_kind'] == "VatTax") $vatTax[] = $oneRule;
							if($oneRule['calc_kind'] == "DBTax") $dbTax[] = $oneRule;
						}
					}
					$product->prices['discountedPriceWithoutTax'] = $product->prices['basePrice'];
					if(!empty($dbTax)) $product->prices['discountedPriceWithoutTax'] = $calculationHelper->roundInternal($calculationHelper->executeCalculation($dbTax, $product->prices['basePrice']),'discountedPriceWithoutTax');
					if(!empty($product->product_override_price) && $product->override) {
						$product->prices['discountedPriceWithoutTax'] = $product->product_override_price;
					}
					$product->prices['salesPrice'] = $product->prices['discountedPriceWithoutTax'];
					$product->prices['basePriceWithTax'] = $product->prices['basePrice'];
					if(!empty($tax)){
						$product->prices['basePriceWithTax'] = $calculationHelper->roundInternal($calculationHelper->executeCalculation($tax, $product->prices['basePriceWithTax'], true),'basePriceWithTax');
						$product->prices['salesPrice'] = $calculationHelper->roundInternal($calculationHelper->executeCalculation($tax, $product->prices['salesPrice'], true),'salesPrice');
					}
					if(!empty($vatTax)){
						$product->prices['basePriceWithTax'] = $calculationHelper->roundInternal($calculationHelper->executeCalculation($vatTax, $product->prices['basePriceWithTax'], true),'basePriceWithTax');
						$product->prices['salesPrice'] = $calculationHelper->roundInternal($calculationHelper->executeCalculation($vatTax, $product->prices['salesPrice'], true),'salesPrice');
					}
				}
			}

			if($this->params->get('vat',1)){
				$price1 = $product->prices['basePriceWithTax'];
				if(!empty($product->prices['salesPrice']) && number_format($price1, 2, ',', ' ') != number_format($product->prices['salesPrice'], 2, ',', ' ')) $price2 = $product->prices['salesPrice'];
			}else{
				$price1 = $product->prices['basePrice'];
				if(!empty($product->prices['discountedPriceWithoutTax']) && number_format($price1, 2, ',', ' ') != number_format($product->prices['discountedPriceWithoutTax'], 2, ',', ' ')) $price2 = $product->prices['discountedPriceWithoutTax'];
			}
		}else{
			$price1 = $product->product_price;
			if(!empty($product->product_override_price) && $product->override && $product->product_override_price != $product->product_price){
				$price2 = $product->product_override_price;
			}
			if($this->params->get('vat',1)){
				if(!empty($product->prices['basePriceWithTax'])) $price1 = $product->prices['basePriceWithTax'];
				if(!empty($product->prices['salesPrice']) && number_format($price1, 2, ',', ' ') != number_format($product->prices['salesPrice'], 2, ',', ' ')) $price2 = $product->prices['salesPrice'];
			}
		}

		$currencyHelper = CurrencyDisplay::getInstance($product->product_currency);
		$price = $currencyHelper->priceDisplay($price1,$product->product_currency);
		if(!empty($price2)) $price2 = $currencyHelper->priceDisplay($price2,$product->product_currency);

		if(!empty($tag->onlyfinalprice)){
			$finalPrice = empty($price2) ? $price : $price2;
		}else{
			$finalPrice = empty($price2) ? $price : '<strike>'.$price.'</strike> '.$price2;
		}

		$varFields['{finalPrice}'] = $finalPrice;
		$varFields['{price}'] = $price;
		$varFields['{price2}'] = empty($price2) ? 0 : $price2;
		$varFields['{productid}'] = $tag->id;

		$result = '';
		$astyle = '';
		if(empty($tag->type) || $tag->type != 'title'){
			$result .= '<div class="acymailing_content" style="clear:both">';
			$astyle = 'style="text-decoration:none;" name="product-'.$product->virtuemart_product_id.'"';
		}

		if(empty($tag->noclickable)) $result .= '<a '.$astyle.' target="_blank" href="'.$link.'">';

		if(empty($tag->type) || $tag->type != 'title') $result .= '<h2 class="acymailing_title">';
		if(!empty($tag->wraptitle)){
			$wrapTitle = new stdClass();
			$wrapTitle->wrap = intval($tag->wraptitle);
			$product->product_name = $acypluginsHelper->wrapText($product->product_name, $wrapTitle);
		}
		$result .= $product->product_name;
		if(empty($tag->noprice)) $result.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$finalPrice;
		if(empty($tag->type) || $tag->type != 'title') $result .= '</h2>';

		if(empty($tag->noclickable)) $result .= '</a>';

		if(empty($tag->type) || $tag->type != 'title'){
			if(!empty($product->images)){
				$mainPict = reset($product->images);
				$pictvar = (empty($tag->pict) || $tag->pict != 'resized' || empty($mainPict->file_url)) ? 'file_url_thumb' : 'file_url';
				if(!empty($mainPict->$pictvar) && file_exists(JPATH_SITE.DS.$mainPict->$pictvar)){
					$varFields['{mainpicture}'] = '<a target="_blank" style="text-decoration:none;border:0px" href="'.$link.'" ><img style="float:left;margin:5px;border:0px" alt="'.addslashes($product->product_name).'" src="'.$mainPict->$pictvar.'" /></a>';
					$result .= $varFields['{mainpicture}'];
				}
			}
			$result .= $description;
		}
		$varFields['{mainpicture}'] = empty($varFields['{mainpicture}']) ? '' : $varFields['{mainpicture}'];

		if(!empty($tag->custom)){
			$tag->custom = explode(',', $tag->custom);
			JArrayHelper::toInteger($tag->custom);
			$customFieldValue = version_compare($this->version,'3.0.0','<') ? 'custom_value' : 'customfield_value';
			$query = 'SELECT a.virtuemart_custom_id, a.custom_title, a.show_title, a.field_type, b.'.$customFieldValue.' ' .
					'FROM #__virtuemart_customs AS a ' .
					'JOIN #__virtuemart_product_customfields AS b ' .
					'ON a.virtuemart_custom_id = b.virtuemart_custom_id ' .
					'WHERE a.virtuemart_custom_id IN ('.implode(',', $tag->custom).') AND b.virtuemart_product_id = '.intval($tag->id);
			$db->setQuery($query);
			$customFields = $db->loadObjectList();
			$allowedFields = array("B","I","S","D","M","X","Y");
			$fieldsValueList = array();
			foreach($customFields as $oneCustom){
				if(!in_array($oneCustom->field_type, $allowedFields)) continue;
				$fieldsValueList[$oneCustom->virtuemart_custom_id][] = $oneCustom;
			}
			$customFields = $fieldsValueList;
			foreach($customFields as $values){
				$oneCustom = $values[0];
				if(count($values) > 1){
					$val = $values[0]->$customFieldValue;
					foreach($values as $i => $oneValue) $val = empty($i) ? $oneValue->$customFieldValue : $val.', '.$oneValue->$customFieldValue;
					$oneCustom->$customFieldValue = $val;
				}

				if($oneCustom->show_title == 1) $title = '<strong>'.$oneCustom->custom_title.'&nbsp;</strong>';
				else $title = '';

				if($oneCustom->field_type == 'B'){
					$thisField = '<br />'.$title.($oneCustom->$customFieldValue == 1 ? JText::_('JOOMEXT_YES') : JText::_('JOOMEXT_No'));
				}elseif($oneCustom->field_type == 'D'){
					$thisField = '<br />'.$title.acymailing_getDate(acymailing_getTime($oneCustom->$customFieldValue), JText::_('DATE_FORMAT_LC'));
				}elseif($oneCustom->field_type == 'M'){
					$db->setQuery('SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = '.intval($oneCustom->$customFieldValue));
					$imagePath = $db->loadResult();
					if(!empty($imagePath)) $thisField = '<br />'.$title.'<img src="'.$imagePath.'" alt=""/>';
					else $thisField = '';
				}else{
					$thisField = '<br />'.$title.$oneCustom->$customFieldValue;
				}
				$varFields['{'.$oneCustom->custom_title.'}'] = $thisField;
				$result .= $varFields['{'.$oneCustom->custom_title.'}'];
			}
		}

		$varFields['{readmore}'] = '<a class="readmore_link" style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$this->readmore.'</span></a>';
		if(!empty($tag->readmore)) $result .= $varFields['{readmore}'];

		if(empty($tag->type) || $tag->type != 'title') $result .= '</div>';

		if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'tagvmproduct.php')){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins'.DS.'tagvmproduct.php');
			$result = ob_get_clean();
			$result = str_replace(array_keys($varFields),$varFields,$result);
		}

		return $result;

	}

	private function _getProduct1($tag){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
	 	$time = time();

		$query = 'SELECT d.*, c.*, b.*, a.* FROM '.acymailing_table('vm_product',false).' as a ';
		$query .= ' LEFT JOIN '.acymailing_table('vm_product_price',false).' as b on a.product_id = b.product_id';
		if(!empty($tag->shoppergroup)) $query .= ' AND b.shopper_group_id = '.$tag->shoppergroup;
		$query .= ' LEFT JOIN '.acymailing_table('vm_tax_rate',false).' as c on a.product_tax_id = c.tax_rate_id ';
		$query .= ' LEFT JOIN '.acymailing_table('vm_product_discount',false).' as d on a.`product_discount_id` = d.`discount_id` AND d.`start_date` < '.$time.' AND (d.`end_date` = 0 OR d.`end_date` > '.$time.') ';
		$query .= ' WHERE a.product_id = '.$tag->id.' LIMIT 1';

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$product = $db->loadObject();

		if(empty($product)){
			$app = JFactory::getApplication();
			if($app->isAdmin()) $app->enqueueMessage('The product "'.$tag->id.'" could not be loaded','notice');
			return '';
		}

		$acypluginsHelper->translateItem($product, $tag, 'vm_product');

		$varFields = array();
		foreach($product as $fieldName => $oneField){
			if(!is_array($oneField) && !is_object($oneField)) $varFields['{'.$fieldName.'}'] = $oneField;
		}

		if($this->params->get('vat',1) && !empty($product->tax_rate)) $product->product_price = $product->product_price * (1 + $product->tax_rate);

		$description = (empty($tag->type) || $tag->type == 'full') ? $product->product_desc : $product->product_s_desc;
		$description = $acypluginsHelper->wrapText($description, $tag);
		$varFields['{description}'] = $description;

		$link = 'index.php?option=com_virtuemart&page=shop.product_details&product_id='.$product->product_id;
		if(!empty($tag->lang)) $link.= '&lang='.substr($tag->lang, 0,strpos($tag->lang,','));
		if(!empty($tag->itemid)) $link .= '&Itemid='.$tag->itemid;
		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		if(!empty($product->amount)){
			$price2 = empty($product->is_percent) ? $product->product_price - $product->amount : $product->product_price - ($product->amount * $product->product_price / 100);
		}

		switch($product->product_currency){
			case 'USD': $product->product_currency='$';break;
			case 'EUR': $product->product_currency='€';break;
			case 'GBP': $product->product_currency='£';break;
			case 'JPY': $product->product_currency='¥';break;
			case 'AUD': $product->product_currency='AUD $';break;
			case 'CAD': $product->product_currency='CAD $';break;
			case 'HKD': $product->product_currency='HKD $';break;
			case 'NZD': $product->product_currency='NZD $';break;
			case 'SGD': $product->product_currency='SGD $';break;
			case 'RUB': $product->product_currency='руб.';break;
			case 'ZAR': $product->product_currency='R';break;
		}

		if($this->params->get('priceformat','english') == 'french'){
			$price = number_format($product->product_price, 2, ',', ' ').' '.$product->product_currency;
			if(!empty($price2)) $price2 = number_format($price2, 2, ',', ' ').' '.$product->product_currency;
		}else{
			$price = $product->product_currency.number_format($product->product_price, 2, '.', '');
			if(!empty($price2)) $price2 = $product->product_currency.number_format($price2, 2, '.', '');
		}

		if(!empty($tag->onlyfinalprice)){
			$finalPrice = empty($price2) ? $price : $price2;
		}else{
			$finalPrice = empty($price2) ? $price : '<strike>'.$price.'</strike> '.$price2;
		}

		$varFields['{finalPrice}'] = $finalPrice;
		$varFields['{price}'] = $price;
		$varFields['{price2}'] = empty($price2) ? 0 : $price2;
		$varFields['{productid}'] = $tag->id;

		$result = '';
		$astyle = '';
		if(empty($tag->type) || $tag->type != 'title'){
			$result .= '<div class="acymailing_content" style="clear:both">';
			$astyle = 'style="text-decoration:none;" name="product-'.$product->product_id.'"';
		}

		if(!empty($tag->link)) $result .= '<a '.$astyle.' target="_blank" href="'.$link.'">';
		if(empty($tag->type) || $tag->type != 'title') $result .= '<h2 class="acymailing_title">';
		if(!empty($tag->wraptitle)){
			$wrapTitle = new stdClass();
			$wrapTitle->wrap = intval($tag->wraptitle);
			$product->product_name = $acypluginsHelper->wrapText($product->product_name, $wrapTitle);
		}
		$result .= $product->product_name;
		if(empty($tag->noprice)) $result.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$finalPrice;
		if(empty($tag->type) || $tag->type != 'title') $result .= '</h2>';
		if(!empty($tag->link)) $result .= '</a>';

		if(empty($tag->type) || $tag->type != 'title'){
			if(!empty($product->product_thumb_image)){
				$img = $product->product_thumb_image;
				if(file_exists(ACYMAILING_ROOT.'components'.DS.'com_virtuemart'.DS.'shop_image'.DS.'product'.DS.'resized'.DS.substr($img,0,strrpos($img,'.')).'_90x90'.substr($img,strrpos($img,'.')))){
					$img = 'resized/'.substr($img,0,strrpos($img,'.')).'_90x90'.substr($img,strrpos($img,'.'));
				}
				$picturePath = (strpos($img,'http') === 0) ? $img : ACYMAILING_LIVE.'components/com_virtuemart/shop_image/product/'.$img;
				$varFields['{mainpicture}'] = '<a target="_blank" style="text-decoration:none;border:0px" href="'.$link.'" ><img style="float:left;margin:5px;border:0px" alt="'.$product->product_name.'" src="'.$picturePath.'" /></a>';
				$result .= $varFields['{mainpicture}'].$description;
			}else{
				$result .= $description;
			}
		}
		$varFields['{mainpicture}'] = empty($varFields['{mainpicture}']) ? '' : $varFields['{mainpicture}'];

		$varFields['{readmore}'] = '<a class="readmore_link" style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$this->readmore.'</span></a>';
		if(!empty($tag->readmore)) $result .= $varFields['{readmore}'];

		if(empty($tag->type) || $tag->type != 'title') $result .= '</div>';

		if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'tagvmproduct.php')){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins'.DS.'tagvmproduct.php');
			$result = ob_get_clean();
			$result = str_replace(array_keys($varFields),$varFields,$result);
		}

		return $result;
	}

	function onAcyDisplayFilters(&$type,$context="massactions"){
		if($this->params->get('displayfilter_'.$context,true) == false) return;
		if(!$this->version) return;

		$lang = JFactory::getLanguage();
		$lang->load('com_virtuemart_orders',JPATH_SITE);
		$lang->load('com_virtuemart_orders',JPATH_SITE.'/components/com_virtuemart');
		$lang->load('com_virtuemart_shoppers',JPATH_SITE.'/components/com_virtuemart');
		$lang->load('com_virtuemart',JPATH_ADMINISTRATOR.'/components/com_virtuemart');

		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		$vmbuy = array();
		$vmbuy[] = JHTML::_('select.option', '0', JText::_('ACY_DIDNOTBOUGHT') );
		$vmbuy[] = JHTML::_('select.option', '1', JText::_('ACY_BOUGHT') );


		$vmgroupsparams = acymailing_get('type.operatorsin');
		$vmgroupsparams->js = 'onchange="countresults(__num__)"';
		$operators = acymailing_get('type.operators');
		$operators->extra = 'onchange="countresults(__num__)"';
		if(version_compare($this->version,'2.0.0','<')){
			$db->setQuery('SELECT `shopper_group_id` as value, `shopper_group_name` as text FROM `#__vm_shopper_group` ORDER BY `shopper_group_name` ASC');
		}else{
			$db->setQuery('SELECT `virtuemart_shoppergroup_id` as value, `shopper_group_name` as text FROM `#__virtuemart_shoppergroups` ORDER BY `shopper_group_name` ASC');
		}

		$vmgroups = $db->loadObjectList();

		if(version_compare($this->version,'2.0.0','<')){
			$fields = acymailing_getColumns('#__vm_user_info');
		}else{
			$fields = acymailing_getColumns('#__virtuemart_userinfos');
		}
		$vmfield = array();
		foreach($fields as $oneField => $fieldType){
			$vmfield[] = JHTML::_('select.option',$oneField,$oneField);
		}

		if(version_compare($this->version,'2.0.0','<')){
			$db->setQuery('SELECT order_status_code as code, order_status_name as name FROM `#__vm_order_status` ORDER BY `list_order` ASC');
		}else{
			$db->setQuery('SELECT order_status_code as code, order_status_name as name FROM `#__virtuemart_orderstates` ORDER BY `ordering` ASC');
		}

		$allStatus = $db->loadObjectList();
		if(!empty($allStatus)){
			foreach($allStatus as $i => $j)
			{
				$allStatus[$i]->name = JText::_($j->name);
			}
			$firstStatus = new stdClass();
			$firstStatus->name = JText::_('ALL_STATUS');
			$firstStatus->code = '';
			array_unshift($allStatus,$firstStatus);
		}

		if(version_compare($this->version,'2.0.0','<')){
			$db->setQuery("SELECT `payment_method_name` as name, `payment_method_id` as id FROM `#__vm_payment_method` WHERE `payment_enabled` = 'Y' ORDER BY `list_order` ASC");
		}else{
			$db->setQuery("SELECT `payment_name` as name, a.`virtuemart_paymentmethod_id` as id FROM `#__virtuemart_paymentmethods` as a LEFT JOIN `#__virtuemart_paymentmethods_".$this->lang."` as b ON a.virtuemart_paymentmethod_id = b.virtuemart_paymentmethod_id WHERE `published` = 1 ORDER BY `ordering` ASC");
		}

		$allPayments = $db->loadObjectList();
		$firstPayment = new stdClass();
		$firstPayment->name = JText::_('ACY_ALL');
		$firstPayment->id = '';
		array_unshift($allPayments,$firstPayment);


		$return = '';
		if(!empty($allStatus)){
			$return .= '<div id="filter__num__vmallorders">'.$vmgroupsparams->display("filter[__num__][vmallorders][type]").' ';
			$return .= JHTML::_('select.genericlist',   $allStatus, "filter[__num__][vmallorders][status]", 'class="inputbox" onchange="countresults(__num__)" size="1"', 'code', 'name').' ';
			$return .= JHTML::_('select.genericlist',   $allPayments, "filter[__num__][vmallorders][payment]", 'class="inputbox" onchange="countresults(__num__)" size="1"', 'id', 'name');
			$return .= '<br /> <input type="text" name="filter[__num__][vmallorders][cdateinf]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /> < '.JText::_('CREATED_DATE').' < <input type="text" name="filter[__num__][vmallorders][cdatesup]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" />';
			$return .= '<br /> <input type="text" name="filter[__num__][vmallorders][mdateinf]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /> < '.JText::_('MODIFIED_DATE').' < <input type="text" name="filter[__num__][vmallorders][mdatesup]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" />';
			$return .= '</div>';
			$type['vmallorders'] = JText::_('Virtuemart').' : '.JText::_('ACY_ORDERS');
		}

		$controller = $app->isAdmin() ? 'cpanel' : 'fronttag';

		$return .= '<div id="filter__num__vmorder">'.JHTML::_('select.genericlist', $vmbuy, "filter[__num__][vmorder][type]", 'class="inputbox" onchange="countresults(__num__)" size="1" style="max-width:150px"', 'value', 'text',1).' ';
		$return .= '<input readonly type="text" name="filter[__num__][vmorder][product]" id="filter__num__vmorderproduct" class="inputbox" style="width:100px" size="1" value="'.JText::_('ACY_ONE_PRODUCT').'"/>' .
				'<input type="hidden" name="filter[__num__][vmorder][productid]" id="filter__num__vmorderproductid" value="0"/>' .
				'<a class="modal" onclick="window.acymailing_js.openBox(this,\'index.php?option=com_acymailing&tmpl=component&ctrl='.$controller.'&task=plgtrigger&plg=virtuemart&fctName=displayVMArticles&param=__num__\'); return false;" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">' .
				'<img class="icon16" src="'.ACYMAILING_IMAGES.'icons/icon-16-edit.png" />' .
				'</a>';
		$return .= '<input readonly type="text" name="filter[__num__][vmorder][cat]" id="filter__num__vmordercat" class="inputbox" style="width:100px" size="1" value="'.JText::_('ACY_ANY_CATEGORY').'"/>' .
				'<input type="hidden" name="filter[__num__][vmorder][catid]" id="filter__num__vmordercatid" value="0"/>' .
				'<a class="modal" onclick="window.acymailing_js.openBox(this,\'index.php?option=com_acymailing&tmpl=component&ctrl='.$controller.'&task=plgtrigger&plg=virtuemart&fctName=displayVMCategories&param=__num__\'); return false;" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">' .
				'<img class="icon16" src="'.ACYMAILING_IMAGES.'icons/icon-16-edit.png" />' .
				'</a>';
		$return .= ' '.JHTML::_('select.genericlist',   $allStatus, "filter[__num__][vmorder][status]", 'class="inputbox" onchange="countresults(__num__)" size="1"', 'code', 'name');
		$return .= '<br /> <input type="text" name="filter[__num__][vmorder][creationdateinf]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" /> < '.JText::_('CREATED_DATE').' < <input type="text" name="filter[__num__][vmorder][creationdatesup]" onclick="displayDatePicker(this,event)" onchange="countresults(__num__)" style="width:100px" />';
		$return .= '</div>';
		$type['vmorder'] = JText::_('Virtuemart').' : '.JText::_('ACY_PRODUCTS');

		if(!empty($vmgroups)){
			foreach($vmgroups as $i => $oneGroup)
				$vmgroups[$i]->text = JText::_($oneGroup->text);
			$return .= '<div id="filter__num__vmgroups">'.$vmgroupsparams->display("filter[__num__][vmgroups][type]").' ';
			$return .= JHTML::_('select.genericlist', $vmgroups, "filter[__num__][vmgroups][group]", 'class="inputbox" size="1" onchange="countresults(__num__)"', 'value', 'text');
			$return .= '</div>';
			$type['vmgroups'] = JText::_('Virtuemart').' : '.JText::_('SHOPPER_GROUP');
		}

		if(!empty($vmfield)){
			$return .= '<div id="filter__num__vmfield">'.JHTML::_('select.genericlist',   $vmfield, "filter[__num__][vmfield][map]", 'class="inputbox" size="1" onchange="countresults(__num__)" ', 'value', 'text');
			$return .= ' '.$operators->display("filter[__num__][vmfield][operator]").' <input class="inputbox" type="text" name="filter[__num__][vmfield][value]" onchange="countresults(__num__)" style="width:200px" value="" />';
			$return .= '</div>';
			$type['vmfield'] = JText::_('Virtuemart').' : '.JText::_('FIELD');
		}

		return $return;
	}

	function onAcyProcessFilterCount_vmfield(&$query,$filter,$num){
		$this->onAcyProcessFilter_vmfield($query,$filter,$num);
		return JText::sprintf('SELECTED_USERS',$query->count());
	}

	function onAcyProcessFilter_vmfield(&$query,$filter,$num){
		if(version_compare($this->version,'2.0.0','<')){
			$myquery = "SELECT DISTINCT a.user_email FROM #__vm_user_info as a WHERE ".$query->convertQuery('a',$filter['map'],$filter['operator'],$filter['value']);
		}else{
			$myquery = "SELECT DISTINCT a.virtuemart_user_id FROM #__virtuemart_userinfos as a WHERE ".$query->convertQuery('a',$filter['map'],$filter['operator'],$filter['value']);
		}
		$query->db->setQuery($myquery);
		$allEmails  = acymailing_loadResultArray($query->db);
		if(empty($allEmails)) $allEmails[] = '-1';
		if(version_compare($this->version,'2.0.0','<')){
			$query->where[] = "sub.email IN ('".implode("','",$allEmails)."')";
		}else{
			$query->where[] = "sub.userid IN ('".implode("','",$allEmails)."')";
		}
	}

	function onAcyProcessFilterCount_vmallorders(&$query,$filter,$num){
		$this->onAcyProcessFilter_vmallorders($query,$filter,$num);
		return JText::sprintf('SELECTED_USERS',$query->count());
	}

	function onAcyProcessFilter_vmallorders(&$query,$filter,$num){
		$db = JFactory::getDBO();
		if(version_compare($this->version,'2.0.0','<')){
	 		$lj = "`#__vm_orders` as vmallorders$num ON vmallorders$num.`user_id` = sub.`userid`";
		}else{
			$lj1 = "`#__virtuemart_order_userinfos` as vmuserinfos_$num ON vmuserinfos_$num.`email` = sub.`email`";
			$query->leftjoin['vmuserinfos_'.$num] = $lj1;
			$lj = "`#__virtuemart_orders` as vmallorders$num ON vmallorders$num.`virtuemart_order_id` = vmuserinfos_$num.`virtuemart_order_id`";
		}
	 	if(!empty($filter['status'])) $lj .= " AND vmallorders$num.`order_status` = ".$db->Quote($filter['status']);
	 	if(!empty($filter['cdateinf'])){
	 		$filter['cdateinf'] = acymailing_replaceDate($filter['cdateinf']);
	 		if(!is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strtotime($filter['cdateinf']);
	 		if(version_compare($this->version,'2.0.0','<')){
		 		$lj .= " AND vmallorders$num.`cdate` > ".$db->Quote($filter['cdateinf']);
	 		}else{
	 			if(is_numeric($filter['cdateinf'])) $filter['cdateinf'] = strftime('%Y-%m-%d %H:%M:%S',$filter['cdateinf']);
		 		$lj .= " AND vmallorders$num.`created_on` > ".$db->Quote($filter['cdateinf']);
	 		}
	 	}
	 	if(!empty($filter['cdatesup'])){
	 		$filter['cdatesup'] = acymailing_replaceDate($filter['cdatesup']);
	 		if(!is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strtotime($filter['cdatesup']);
	 		if(version_compare($this->version,'2.0.0','<')){
	 			$lj .= " AND vmallorders$num.`cdate` < ".$db->Quote($filter['cdatesup']);
	 		}else{
	 			if(is_numeric($filter['cdatesup'])) $filter['cdatesup'] = strftime('%Y-%m-%d %H:%M:%S',$filter['cdatesup']);
		 		$lj .= " AND vmallorders$num.`created_on` < ".$db->Quote($filter['cdateinf']);
	 		}
	 	}
	 	if(!empty($filter['mdateinf'])){
	 		$filter['mdateinf'] = acymailing_replaceDate($filter['mdateinf']);
	 		if(!is_numeric($filter['mdateinf'])) $filter['mdateinf'] = strtotime($filter['mdateinf']);
	 		if(version_compare($this->version,'2.0.0','<')){
	 			$lj .= " AND vmallorders$num.`mdate` > ".$db->Quote($filter['mdateinf']);
 			}else{
	 			if(is_numeric($filter['mdateinf'])) $filter['mdateinf'] = strftime('%Y-%m-%d %H:%M:%S',$filter['mdateinf']);
		 		$lj .= " AND vmallorders$num.`modified_on` > ".$db->Quote($filter['mdateinf']);
	 		}
	 	}
	 	if(!empty($filter['mdatesup'])){
	 		$filter['mdatesup'] = acymailing_replaceDate($filter['mdatesup']);
	 		if(!is_numeric($filter['mdatesup'])) $filter['mdatesup'] = strtotime($filter['mdatesup']);
	 		if(version_compare($this->version,'2.0.0','<')){
	 			$lj .= " AND vmallorders$num.`mdate` < ".$db->Quote($filter['mdatesup']);
	 		}else{
	 			if(is_numeric($filter['mdatesup'])) $filter['mdatesup'] = strftime('%Y-%m-%d %H:%M:%S',$filter['mdatesup']);
		 		$lj .= " AND vmallorders$num.`modified_on` < ".$db->Quote($filter['mdatesup']);
	 		}
	 	}

	 	if(!empty($filter['payment']) && version_compare($this->version,'2.0.0','>=')){
	 		$lj .= " AND vmallorders$num.`virtuemart_paymentmethod_id` = ".$db->Quote($filter['payment']);
	 	}

	 	$query->leftjoin['vmallorders_'.$num] = $lj;

		$operator = ($filter['type'] == 'IN') ? 'IS NOT NULL' : 'IS NULL';

	 	if(!empty($filter['payment']) && version_compare($this->version,'2.0.0','<')){
	 		$plj = "`#__vm_order_payment` as vmpay$num ON vmallorders$num.`order_id` = vmpay$num.`order_id` AND `payment_method_id` = ".intval($filter['payment']);
 			$query->leftjoin['vmpay_'.$num] = $plj;
 			$query->where[] = "vmpay$num.`order_id` ".$operator;
 			return;
	 	}else{
	 		if(version_compare($this->version,'2.0.0','<')){
	 			$query->where[] = "vmallorders$num.`user_id` ".$operator;
	 		}else{
	 			$query->where[] = "vmallorders$num.`virtuemart_order_id` ".$operator;
	 		}
	 	}

	}

	function onAcyProcessFilterCount_vmgroups(&$query,$filter,$num){
		$this->onAcyProcessFilter_vmgroups($query,$filter,$num);
		return JText::sprintf('SELECTED_USERS',$query->count());
	}

	function onAcyProcessFilter_vmgroups(&$query,$filter,$num){
		if(version_compare($this->version,'2.0.0','<')){
			$myquery = 'SELECT DISTINCT b.`user_email` FROM `#__vm_shopper_vendor_xref` as a LEFT JOIN `#__vm_user_info` as b on a.`user_id` = b.`user_id` WHERE a.`shopper_group_id` ';
			$myquery .= ($filter['type'] == 'IN') ? '= ' : "!= ";
			$myquery .= (int) $filter['group'];

			$query->db->setQuery($myquery);
			$allEmails  = acymailing_loadResultArray($query->db);
			if(empty($allEmails)) $allEmails[] = 'none';
			$query->where[] = "sub.email IN ('".implode("','",$allEmails)."')";
		}else{
			$query->leftjoin['vmshoppergroup_'.$num] = "#__virtuemart_vmuser_shoppergroups as vmshoppergroup_$num ON sub.userid = vmshoppergroup_$num.virtuemart_user_id AND vmshoppergroup_$num.virtuemart_shoppergroup_id = ".intval($filter['group']);
			$query->where[] = "vmshoppergroup_$num.virtuemart_user_id ".(($filter['type'] == 'IN') ? '> 0' : "IS NULL");
		}
	}

	function onAcyProcessFilterCount_vmorder(&$query,$filter,$num){
		$this->onAcyProcessFilter_vmorder($query,$filter,$num);
		return JText::sprintf('SELECTED_USERS',$query->count());
	}

	function onAcyProcessFilter_vmorder(&$query,$filter,$num){
		$datesVar = array('creationdatesup','creationdateinf');
		foreach($datesVar as $oneDate){
			if(empty($filter[$oneDate])) continue;
			$filter[$oneDate] = acymailing_replaceDate($filter[$oneDate]);
			if(!is_numeric($filter[$oneDate])) $filter[$oneDate] = strtotime($filter[$oneDate]);
			if(version_compare($this->version,'2.0.0','>=') && is_numeric($filter[$oneDate])) $filter[$oneDate] = strftime('%Y-%m-%d %H:%M:%S',$filter[$oneDate]);
		}

		if(isset($filter['productid']) && !is_numeric($filter['product'])) $filter['product'] = intval($filter['productid']);
		if(isset($filter['catid']) && !is_numeric($filter['cat'])) $filter['cat'] = intval($filter['catid']);
		$filter['product'] = intval($filter['product']);
		$filter['cat'] = intval($filter['cat']);

		if(version_compare($this->version,'2.0.0','<')){
			$orderFilter = empty($filter['status']) ? '' : " AND a.order_status = ".$query->db->Quote($filter['status']);
			$myquery = "SELECT DISTINCT b.user_email FROM #__vm_order_item as a LEFT JOIN #__vm_user_info as b on a.user_info_id = b.user_info_id WHERE b.user_id < 1".$orderFilter;
			$myqueryid = "SELECT DISTINCT b.user_id FROM #__vm_order_item as a LEFT JOIN #__vm_orders as b on a.order_id = b.order_id WHERE b.user_id > 0".$orderFilter;

			if(!empty($filter['product']) && is_numeric($filter['product'])){
				$myquery .= " AND a.product_id = ".(int) $filter['product'];
				$myqueryid .= " AND a.product_id = ".(int) $filter['product'];
			}elseif(!empty($filter['cat'])){
				$db = JFactory::getDBO();
				$db->setQuery("SELECT product_id FROM #__vm_product_category_xref WHERE category_id = ".intval($filter['cat']));
				$allIds = acymailing_loadResultArray($db);
				JArrayHelper::toInteger($allIds);
				$myquery .= " AND a.product_id IN (".implode(',', $allIds).")";
				$myqueryid .= " AND a.product_id IN (".implode(',', $allIds).")";
			}

			if(!empty($filter['creationdateinf'])) $myquery .= ' AND a.cdate > '.$filter['creationdateinf'];
			if(!empty($filter['creationdatesup'])) $myquery .= ' AND a.cdate < '.$filter['creationdatesup'];
			if(!empty($filter['creationdateinf'])) $myqueryid .= ' AND a.cdate > '.$filter['creationdateinf'];
			if(!empty($filter['creationdatesup'])) $myqueryid .= ' AND a.cdate < '.$filter['creationdatesup'];

			$query->db->setQuery($myquery);
			$allEmails  = acymailing_loadResultArray($query->db);
			$query->db->setQuery($myqueryid);
			$allIds  = acymailing_loadResultArray($query->db);
			if(empty($allEmails)) $allEmails[] = 'none';
			if(empty($allIds)) $allIds[] = '-1';
			if(empty($filter['type'])){
				$query->where[] = "sub.email NOT IN ('".implode("','",$allEmails)."') AND sub.userid NOT IN ('".implode("','",$allIds)."')";
			}else{
				$query->where[] = "sub.email IN ('".implode("','",$allEmails)."') OR sub.userid IN ('".implode("','",$allIds)."')";
			}
		}else{
			$lj = '#__virtuemart_order_items AS vmorderitem_'.$num.' ON vmorderitem_'.$num.'.virtuemart_order_id = vmorderuserinfos_'.$num.'.virtuemart_order_id ';
			if(!empty($filter['status'])) $lj .= ' AND vmorderitem_'.$num.'.order_status = '.$query->db->Quote($filter['status']);

			if(!empty($filter['product']))
				$lj .= ' AND vmorderitem_'.$num.'.virtuemart_product_id = '.$filter['product'];
			elseif(!empty($filter['cat']))
				$lj .= ' AND vmorderitem_'.$num.'.virtuemart_product_id IN (SELECT virtuemart_product_id FROM #__virtuemart_product_categories WHERE virtuemart_category_id = '.$filter['cat'].')';

			if(!empty($filter['creationdateinf'])) $lj .= ' AND vmorderitem_'.$num.'.created_on > '.$query->db->Quote($filter['creationdateinf']);
			if(!empty($filter['creationdatesup'])) $lj .= ' AND vmorderitem_'.$num.'.created_on < '.$query->db->Quote($filter['creationdatesup']);

			if(!empty($filter['type']))
			{
				$query->join['vmorderuserinfos_'.$num] = '#__virtuemart_order_userinfos AS vmorderuserinfos_'.$num.' ON vmorderuserinfos_'.$num.'.email = sub.email';
				$query->join['vmorderitem_'.$num] = $lj;
			}else{
				$query->where[] = 'sub.email NOT IN (SELECT vmorderuserinfos_'.$num.'.email FROM #__virtuemart_order_userinfos AS vmorderuserinfos_'.$num.' JOIN '.$lj.' WHERE vmorderuserinfos_'.$num.'.email IS NOT NULL)';
			}
		}
	}

	function onAcydisplayVMArticles($num){
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$db = JFactory::getDBO();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'virtuemartarts';
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $paramBase.".filter_order", 'filter_order', 'virtuemart_product_id','cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc',	'word' );
		$pageInfo->search = $app->getUserStateFromRequest( $paramBase.".search", 'search', '', 'string' );
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));

		$pageInfo->limit->value = $app->getUserStateFromRequest( $paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$pageInfo->limit->start = $app->getUserStateFromRequest( $paramBase.'.limitstart', 'limitstart', 0, 'int' );

		$searchMap = array('vmProductsTrad.product_name','vmProductsTrad.product_desc');
		if(version_compare($this->version,'2.0.0','<' )){
			$query = 'SELECT SQL_CALC_FOUND_ROWS vmProductsTrad.*, vmProductsTrad.product_id AS virtuemart_product_id, vmCategories.category_name ' .
					'FROM `#__vm_product` AS vmProductsTrad ' .
					'LEFT JOIN #__vm_product_category_xref AS vmCatRef ' .
					'ON vmProductsTrad.product_id = vmCatRef.product_id ' .
					'LEFT JOIN #__vm_category AS vmCategories ' .
					'ON vmCatRef.category_id = vmCategories.category_id ';
		}else{
			$query = 'SELECT SQL_CALC_FOUND_ROWS vmProductsTrad.virtuemart_product_id, vmProductsTrad.product_name, vmProductsTrad.product_desc, GROUP_CONCAT(distinct vmCategories.category_name) AS category_name
						FROM #__virtuemart_products_'.$this->lang.' AS vmProductsTrad
						LEFT JOIN #__virtuemart_product_categories AS vmProductCategories ON vmProductsTrad.virtuemart_product_id = vmProductCategories.virtuemart_product_id
						LEFT JOIN #__virtuemart_categories_'.$this->lang.' AS vmCategories ON vmProductCategories.virtuemart_category_id = vmCategories.virtuemart_category_id ';
			$searchMap[] = 'vmCategories.category_name';
		}

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search,true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ",$searchMap)." LIKE $searchVal";
		}
		if(!empty($filters)) $query .= ' WHERE ('.implode(') AND (',$filters).')';
		if(version_compare($this->version,'2.0.0','<')){
			$query .= ' GROUP BY vmProductsTrad.product_id';
		}else{
			$query .= ' GROUP BY vmProductsTrad.virtuemart_product_id';
		}
		if(!empty ($pageInfo->filter->order->value)) {
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$db->setQuery($query,$pageInfo->limit->start,$pageInfo->limit->value);
		$rows = $db->loadObjectList();

		$db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value );


		if(ACYMAILING_J30){
			$paginationNb = array();
			$paginationNb[] = JHTML::_('select.option', 5,5);
			$paginationNb[] = JHTML::_('select.option', 10,10);
			$paginationNb[] = JHTML::_('select.option', 15,15);
			$paginationNb[] = JHTML::_('select.option', 20,20);
			$paginationNb[] = JHTML::_('select.option', 25,25);
			$paginationNb[] = JHTML::_('select.option', 30,30);
			$paginationNb[] = JHTML::_('select.option', 50,50);
			$paginationNb[] = JHTML::_('select.option', 100,100);
			$paginationNb[] = JHTML::_('select.option', 0,JText::_('ACY_ALL'));
		}
	 ?>

	 <script language="javascript" type="text/javascript">
		function addProduct(selectedId, selectedName){
			parent.window.document.getElementById("filter<?php echo $num; ?>vmorderproduct").value = selectedName;
			parent.window.document.getElementById("filter<?php echo $num; ?>vmorderproductid").value = selectedId;

			parent.countresults(<?php echo $num; ?>);
			parent.acymailing_js.closeBox(true);
		}
	</script>


	 <div id="acy_content">
		 <div class="acytagpopup">
			 <form action="#" method="post" name="adminForm"  id="adminForm"  autocomplete="off">
			 	<table class="adminlist table table-striped table-hover adminform" cellpadding="1" width="100%">
					<tr>
						<td width="100%">
							<input placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" type="text" name="search" id="search" value="<?php echo htmlspecialchars($pageInfo->search, ENT_COMPAT, 'UTF-8');?>" class="text_area" />
							<button class="btn" onclick="document.adminForm.limitstart.value=0;document.adminForm.task.value='';this.form.submit();"><?php echo JText::_( 'JOOMEXT_GO' ); ?></button>
						</td>
					</tr>
				</table>
				<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
					<thead>
							<th/>
							<th class="title titlename">
								<?php echo JHTML::_('grid.sort', JText::_( 'ACY_NAME'), 'vmProductsTrad.product_name', $pageInfo->filter->order->dir,$pageInfo->filter->order->value ); ?>
							</th>
							<th class="title titledesc">
								<?php echo JHTML::_('grid.sort', JText::_( 'ACY_DESCRIPTION'), 'vmProductsTrad.product_desc', $pageInfo->filter->order->dir,$pageInfo->filter->order->value ); ?>
							</th>
							<th class="title titlecat">
								<?php echo JHTML::_('grid.sort',   JText::_( 'TAG_CATEGORIES' ), 'vmCategories.category_name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value ); ?>
							</th>
							<th class="title titleid">
								<?php echo JHTML::_('grid.sort',   JText::_( 'ACY_ID' ), 'vmProductsTrad.virtuemart_product_id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if(empty($pageInfo->limit->start)){ ?>
							<tr style="cursor:pointer;" class="<?php echo "row0"; ?>" onclick="addProduct(0,'<?php echo JText::_('ACY_ONE_PRODUCT'); ?>');">
								<td class="acytdcheckbox"></td>
								<td align="center">
									<?php
										echo JText::_('ACY_ONE_PRODUCT');
									?>
								</td>
								<td align="center">
									<?php
										echo JText::_('FIELD_DEFAULT');
									?>
								</td>
								<td/>
								<td align="center">0</td>
							</tr>
						<?php }
							$k = 1;
							for($i = 0,$a = count($rows);$i<$a;$i++){
								$row = $rows[$i];
						?>
							<tr style="cursor:pointer;" class="<?php echo "row$k"; ?>" onclick="addProduct(<?php echo $row->virtuemart_product_id; ?>,'<?php echo htmlspecialchars(str_replace("'", "", $row->product_name), ENT_COMPAT | ENT_IGNORE, 'UTF-8'); ?>');">
								<td class="acytdcheckbox"></td>
								<td align="center">
									<?php
										echo $row->product_name;
									?>
								</td>
								<td align="center">
									<?php
										if(!empty($row->product_desc)) echo substr(strip_tags(html_entity_decode($row->product_desc),'<br>'),0,200).'...';
									?>
								</td>
								<td align="center">
									<?php
										echo $row->category_name;
									?>
								</td>
								<td align="center">
									<?php
										echo $row->virtuemart_product_id;
									?>
								</td>
							</tr>
						<?php
								$k = 1-$k;
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<?php
									echo $pagination->getListFooter();
									if(ACYMAILING_J30) echo 'Display #'.JHTML::_('select.genericlist', $paginationNb, 'limit' , 'size="1" style="width:60px" onchange="Joomla.submitform();"', 'value', 'text', $pageInfo->limit->value).'<br />';
									echo $pagination->getResultsCounter();
								?>
							</td>
						</tr>
					</tfoot>
				</table>
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>" />
			</form>
		</div>
	</div>
	<?php
	}

	function onAcydisplayVMCategories($num){
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$db = JFactory::getDBO();
		$vmVersion1 = (version_compare($this->version,'2.0.0','<' )) ? true : false;
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'virtuemartcats';
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $paramBase.".filter_order", 'filter_order', $vmVersion1 ? 'a.category_id' : 'a.virtuemart_category_id','cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc',	'word' );
		$pageInfo->search = $app->getUserStateFromRequest( $paramBase.".search", 'search', '', 'string' );
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));

		$pageInfo->limit->value = $app->getUserStateFromRequest( $paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest( $paramBase.'.limitstart', 'limitstart', 0, 'int');

		if($vmVersion1){
			$query = 'SELECT SQL_CALC_FOUND_ROWS a.* FROM `#__vm_category` as a ';
		}else{
			$query = 'SELECT SQL_CALC_FOUND_ROWS a.*, b.*, a.virtuemart_category_id as category_id, b.category_description as category_desc ' .
						'FROM `#__virtuemart_categories` as a ' .
						'LEFT JOIN `#__virtuemart_categories_'.$this->lang.'` as b ' .
							'ON a.virtuemart_category_id = b.virtuemart_category_id ';
		}

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search,true).'%\'';
			$filters[] = ($vmVersion1 ? 'a.category_name' : 'b.category_name').' LIKE '.$searchVal;
		}

		if(!empty($filters)) $query .= 'WHERE ('.implode(') AND (',$filters).') ';

		if(!empty ($pageInfo->filter->order->value)) {
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}else{
			if($vmVersion1){
				$query .= 'ORDER BY `list_order`';
			}else{
				$query .= 'ORDER BY a.`ordering`';
			}
		}

		$db->setQuery($query,$pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $db->loadObjectList();

		$db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value );


		if(ACYMAILING_J30){
			$paginationNb = array();
			$paginationNb[] = JHTML::_('select.option', 5,5);
			$paginationNb[] = JHTML::_('select.option', 10,10);
			$paginationNb[] = JHTML::_('select.option', 15,15);
			$paginationNb[] = JHTML::_('select.option', 20,20);
			$paginationNb[] = JHTML::_('select.option', 25,25);
			$paginationNb[] = JHTML::_('select.option', 30,30);
			$paginationNb[] = JHTML::_('select.option', 50,50);
			$paginationNb[] = JHTML::_('select.option', 100,100);
			$paginationNb[] = JHTML::_('select.option', 0,JText::_('ACY_ALL'));
		}

	 ?>

	 <script language="javascript" type="text/javascript">
		function addCategory(selectedId, selectedName){
			parent.window.document.getElementById("filter<?php echo $num; ?>vmordercat").value = selectedName;
			parent.window.document.getElementById("filter<?php echo $num; ?>vmordercatid").value = selectedId;

			parent.countresults(<?php echo $num; ?>);
			parent.acymailing_js.closeBox(true);
		}
	</script>

	<div id="acy_content">
		<div class="acytagpopup">
			<form action="#" method="post" name="adminForm"  id="adminForm"  autocomplete="off">
			 	<table class="adminlist table table-striped table-hover adminform" cellpadding="1" width="100%">
					<tr>
						<td width="100%">
							<input placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" type="text" name="search" id="search" value="<?php echo htmlspecialchars($pageInfo->search, ENT_COMPAT, 'UTF-8');?>" class="text_area" />
							<button class="btn" onclick="document.adminForm.limitstart.value=0;document.adminForm.task.value='';this.form.submit();"><?php echo JText::_( 'JOOMEXT_GO' ); ?></button>
						</td>
					</tr>
				</table>
				<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
					<thead>
							<th/>
							<th class="title titlename">
								<?php echo JHTML::_('grid.sort', JText::_( 'ACY_NAME'), $vmVersion1 ? 'a.category_name' : 'b.category_name', $pageInfo->filter->order->dir,$pageInfo->filter->order->value ); ?>
							</th>
							<th class="title titledesc">
								<?php echo JHTML::_('grid.sort', JText::_( 'ACY_DESCRIPTION'), $vmVersion1 ? 'a.category_desc' : 'b.category_description', $pageInfo->filter->order->dir,$pageInfo->filter->order->value ); ?>
							</th>
							<th class="title titleid">
								<?php echo JHTML::_('grid.sort',   JText::_( 'ACY_ID' ), $vmVersion1 ? 'a.category_id' : 'a.virtuemart_category_id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if(empty($pageInfo->limit->start)){ ?>
							<tr style="cursor:pointer;" class="<?php echo "row0"; ?>" onclick="addCategory(0,'<?php echo JText::_('ACY_ANY_CATEGORY'); ?>');">
								<td class="acytdcheckbox"></td>
								<td align="center">
									<?php
										echo JText::_('ACY_ANY_CATEGORY');
									?>
								</td>
								<td align="center">
									<?php
										echo JText::_('FIELD_DEFAULT');
									?>
								</td>
								<td align="center">N/A</td>
							</tr>
						<?php }
							$k = 1;
							for($i = 0,$a = count($rows);$i<$a;$i++){
								$row = $rows[$i];
						?>
							<tr style="cursor:pointer;" class="<?php echo "row$k"; ?>" onclick="addCategory(<?php echo $row->category_id; ?>,'<?php echo htmlspecialchars(str_replace("'", "", $row->category_name), ENT_COMPAT | ENT_IGNORE, 'UTF-8'); ?>');">
								<td class="acytdcheckbox"></td>
								<td align="center">
									<?php
										echo $row->category_name;
									?>
								</td>
								<td align="center">
									<?php
										if(!empty($row->category_desc)) echo substr(strip_tags(html_entity_decode($row->category_desc),'<br>'),0,200).'...';
									?>
								</td>
								<td align="center">
									<?php
										echo $row->category_id;
									?>
								</td>
							</tr>
						<?php
								$k = 1-$k;
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<?php
									echo $pagination->getListFooter();
									if(ACYMAILING_J30) echo 'Display #'.JHTML::_('select.genericlist', $paginationNb, 'limit' , 'size="1" style="width:60px" onchange="Joomla.submitform();"', 'value', 'text', $pageInfo->limit->value).'<br />';
									echo $pagination->getResultsCounter();
								?>
							</td>
						</tr>
					</tfoot>
				</table>
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>" />
			</form>
		</div>
	</div>
	<?php
	}
}//endclass
