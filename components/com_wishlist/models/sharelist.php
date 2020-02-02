<?php
/**
 * Favorites Model for Favorites Component
 * 
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public License
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
VmConfig::loadConfig();

// Load the language file of com_virtuemart.
JFactory::getLanguage()->load('com_virtuemart');
if (!class_exists('calculationHelper')) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/calculationh.php');
if (!class_exists('CurrencyDisplay')) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/currencydisplay.php');
if (!class_exists('VirtueMartModelVendor')) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/models/vendor.php');
if (!class_exists('VmImage')) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/image.php');
if (!class_exists('shopFunctionsF')) require(JPATH_SITE.'/components/com_virtuemart/helpers/shopfunctionsf.php');
if (!class_exists('VirtueMartCart')) require(JPATH_SITE .'/components/com_virtuemart/helpers/cart.php');
if (!class_exists('VirtueMartModelProduct')){ JLoader::import('product', JPATH_ADMINISTRATOR .'/components/com_virtuemart/models');}

/**
 * Favorites Model
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesModelSharelist extends JModelList{
	
	/**
	 * Favoriteslist data array for tmp store
	 *
	 * @var array
	 */
	private $_data;

	/**
	* Pagination object
	* @var object
	*/
	private $_pagination = null;

	/*
	 * Constructor
	 *
	 */
	function __construct(){
		parent::__construct();

		$app =& JFactory::getApplication();

        // Get pagination request variables
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
	}
	
	/**
	 * Gets the data
	 * @return mixed The data to be displayed to the user
	 */
	public function getData(){		
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )){
			$user_id = JRequest::getInt('user_id',  0);
			$recordSet =& $this->getTable('favorites');
			$db =& JFactory::getDBO();
			$query = 'SELECT u.name, fs.share_date, fs.share_title, fs.share_desc, fs.isWishList, fs.share_pass, fp.fav_id, fp.product_id, fp.product_qty, fp.user_id, fp.fav_date FROM #__users u, #__virtuemart_favorites_sh fs, #__virtuemart_favorites fp WHERE ' . (isset($recordSet->published)?'`published`':'1') . ' = 1 AND fp.user_id=' . $user_id .' AND fp.user_id=fs.user_id AND fp.user_id=u.id GROUP BY fp.product_id ORDER BY fp.fav_date DESC';
			
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	/**
	 * Gets the number of published records
	 * @return int
	 */
	public function getTotal(){
		$user_id = JRequest::getInt('user_id',  0);
		$db =& JFactory::getDBO();
		$recordSet =& $this->getTable('favorites');
		$db->setQuery( 'SELECT COUNT(*) FROM `#__virtuemart_favorites` WHERE ' . (isset($recordSet->published)?'`published`':'1') . ' = 1 AND `user_id` =' . $user_id);
		return $db->loadResult();
	}		
	
	/**
	 * Generate Virtuemart Addtocart Form
	 * @output: HTML 
	 */
	 function addtocart($product,$button_text,$is_wishlist,$own_wishlist) {
            if (!VmConfig::get('use_as_catalog',0)) { ?>
                <div class="fav_addtocart-area">
				
		<form method="post" class="product" action="index.php">
                    <?php
					$user_id = JRequest::getInt('user_id',  0);
					//Loading Layout Options
					$params = &JComponentHelper::getParams( 'com_wishlist' );
					$tooltip_enabled = $params->get( 'wish_tooltip_enabled' );
					$qty_enabled = $params->get( 'wish_qty_enabled' );
					$txt_btn_enabled = $params->get( 'wish_txt_btn_enabled' );
		
                    // Product custom_fields
                    if (!empty($product->customfieldsCart)) {  ?>
						<div class="product-fields">
							<?php foreach ($product->customfieldsCart as $field)
						{ ?>
							<div style="display:inline-block;" class="product-field-type-<?php echo $field->field_type ?>">
								<span class="product-fields-title" ><?php echo  JText::_($field->custom_title) ?></span>
								<?php if ($tooltip_enabled) echo JHTML::tooltip($field->custom_tip, $field->custom_title, 'tooltip.png'); ?>
								<span class="product-field-display"><?php echo $field->display ?></span>
							</div>
							<?php
						}
						?>
					</div>	
                    <?php } 
					//Product custom_childs
					if (!empty($product->customsChilds)) {  ?>
						<div class="product-fields">
							<?php foreach ($product->customsChilds as $field) {  ?>
							<div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field->field_type ?>">
								<span class="product-fields-title" ><b><?php echo JText::_($field->field->custom_title) ?></b></span>
								<span class="product-field-desc"><?php echo JText::_($field->field->custom_value) ?></span>
								<span class="product-field-display"><?php echo $field->display ?></span>
							</div>
							<br />
							<?php
							} ?>
						</div>
					<?php } ?>
            <div class="fav_addtocart-bar">
			<!-- Display the quantity box -->
			<!-- <label for="quantity<?php echo $product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> -->
			<?php $type='text'; if (!$qty_enabled) $type='hidden'; ?>
            <span class="quantity-box">
			<input type="<?php echo $type ?>" class="quantity-input" name="quantity[]" value="1" />
			</span>
            <?php if ($qty_enabled) { ?>
			<span class="quantity-controls">
			<input type="button" class="quantity-controls quantity-plus" value="<?php if ($txt_btn_enabled) echo JText::_('VM_FAVORITE_QTYBOX_UP'); ?>" />
			<input type="button" class="quantity-controls quantity-minus" value="<?php if ($txt_btn_enabled) echo JText::_('VM_FAVORITE_QTYBOX_DOWN'); ?>" />
			</span>
            <?php } ?>

			<?php
            // Add the button
			$button_lbl = $button_text;
			$button_cls = ''; //$button_cls = 'addtocart_button';
			/*			if (VmConfig::get('show_products_out_of_stock') == '1' && !$product->product_in_stock) {
							$button_lbl = JText::_('COM_VIRTUEMART_CART_NOTIFY');
							$button_cls = 'notify-button';
						} */
			 // Display the add to cart button 			 
			 ?>
			<span class="addtocart-button">
				<input type="submit" name="addtocart"  class="addtocart-button" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
			</span>

                        <div class="clear"></div>
                    </div>
                    <input type="hidden" class="pname" value="<?php echo $product->product_name ?>" />
                    <input type="hidden" name="option" value="com_virtuemart" />
                    <input type="hidden" name="view" value="cart" />
                    <noscript><input type="hidden" name="task" value="add" /></noscript>
                    <input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
                    <input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
                    <?php if ($is_wishlist) {?>
                    <input class="" type="hidden" name="customPlugin[9999][textinput][comment]" size="0" value="<?php echo '<b>'.$own_wishlist.' - '.JText::_( 'VM_WISHLIST_PRODUCT' ).'</b>'; ?>">
					<input class="" type="hidden" name="isWishlist" value="1">
                    <input class="" type="hidden" name="user_id" value="<?php echo $user_id; ?>">
					<?php } ?>
                </form>
		<div class="clear"></div>
            </div>
        <?php }
     }
	 
	/**
	 * Gets the Pagination Object
	 * @return object JPagination
	 */
	public function getPagination(){
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	
}
