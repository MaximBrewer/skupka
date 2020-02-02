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
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/config.php');
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
class FavoritesModelFavoriteslist extends JModelList{
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
	 * Method to delete record(s)
	 *
	 * @access	private
	 */
	private function deleteRecord($fav_id, $user_id){
				$db =& JFactory::getDBO();
				$db->setQuery('DELETE FROM #__virtuemart_favorites WHERE `fav_id` ='.$fav_id.' AND `user_id` ="'.$user_id.'"');
				$db->query();
			}

	/**
	 * Method to send email(s)
	 *
	 * @access	private
	 */
	private function sendmail($to, $subject, $body){
		$user =& JFactory::getUser();
		if ($user->email != "") {
			$mailer =& JFactory::getMailer();
			$sender = array( 
				$user->email,
				$user->name);
			$mailer->setSender($sender);
			$mailer->addRecipient($to);		
			$mailer->isHTML(true);
			$mailer->Encoding = 'base64';
			$mailer->setSubject($subject.$user->name);
			$mailer->setBody($body.JText::_('VM_AFAQ_THANKYOU'));
			$send = $mailer->Send();
			if ($send == "1") echo JFactory::getApplication()->enqueueMessage(JText::_('VM_AFAQ_SENT'));
		}
	}
	
	/**
	 * Gets the data
	 * @return mixed The data to be displayed to the user
	 */
	public function getData(){
		$user =& JFactory::getUser();
		if($user->guest) {
			if (!isset($_COOKIE['virtuemart_wish_session'])) {
				$session =& JFactory::getSession();
				setcookie('virtuemart_wish_session',$session->getId(),2592000 + time(),'/');
				$_COOKIE['virtuemart_wish_session'] = $session->getId();
			}
			$user_id = $_COOKIE['virtuemart_wish_session'];
		}
		else $user_id = $user->id;
		//checking if a request of delete records have been posted
		$mode = JRequest::getCmd('mode');
		if ($mode == "delete") {
			$fav_id = JRequest::getInt('fav_id',  0);
			if ($fav_id != 0){
				$this->deleteRecord($fav_id, $user_id);
			}
		}
		//checking if a request of delete records have been posted
		$mode = JRequest::getCmd('mode');
		if ($mode == "delete") {
			$fav_id = JRequest::getInt('fav_id',  0);
			if ($fav_id != 0){
				$this->deleteRecord($fav_id, $user_id);
			}
		}
		if ($mode == "sendmail" || $mode == "sendafaq") {
			$email_to = JRequest::getString('email_to',  "");
			$email_subj = JRequest::getString('email_subj',  "");
			if ($mode == "sendafaq") $email_body = JRequest::getString('afaq_desc',  "") . "<br />";
			$email_body .= JRequest::getString('email_body',  "");
			//Demo System no email will be sent
			//JError::raiseNotice(100, 'This is a demo system, no Email will be sent from here');
			$this->sendmail($email_to,$email_subj,$email_body);
		}
		
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )){
			$recordSet =& $this->getTable('favorites');
			
			$db =& JFactory::getDBO();
			$query = 'SELECT f.fav_id, f.product_id, f.product_qty, f.user_id, f.fav_date FROM #__virtuemart_favorites f WHERE '
			.(isset($recordSet->published)?'`published`':'1').'=1 AND f.user_id="'.$user_id.'" ORDER BY f.fav_date DESC';
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	/**
	 * Gets the number of published records
	 * @return int
	 */
	public function getTotal(){
		$user =& JFactory::getUser();
		if($user->guest) {
			if (!isset($_COOKIE['virtuemart_wish_session'])) {
				$session =& JFactory::getSession();
				setcookie('virtuemart_wish_session',$session->getId(),2592000 + time(),'/');
				$_COOKIE['virtuemart_wish_session'] = $session->getId();
			}
			$user_id = $_COOKIE['virtuemart_wish_session'];
		}
		else $user_id = $user->id;
		//checking if a request of delete records have been posted
		$mode = JRequest::getCmd('mode');
		if ($mode == "delete") {
			$fav_id = JRequest::getInt('fav_id',  0);
			if ($fav_id != 0){
				$this->deleteRecord($fav_id, $user_id);
			}
		}
		$db =& JFactory::getDBO();
		$recordSet =& $this->getTable('favorites');
		$db->setQuery('SELECT COUNT(*) FROM `#__virtuemart_favorites` WHERE '
		.(isset($recordSet->published)?'`published`':'1').'=1 AND `user_id` ="'. $user_id.'"');
		return $db->loadResult();
	}
	
	/**
	 * Generate Virtuemart Addtocart Form
	 * @output: HTML 
	 */
	 function addtocart($product, $button_text, $product_ord) {
            if (!VmConfig::get('use_as_catalog',0)) { ?>
                <div class="fav_addtocart-area">
				
		<form method="post" class="product" action="index.php">
                    <?php
					//Loading Layout Options
					$params = &JComponentHelper::getParams( 'com_wishlist' );
					$tooltip_enabled = $params->get( 'fav_tooltip_enabled' );
					$tooltip_enabled = $params->get( 'fav_tooltip_enabled' );
					$qty_enabled = $params->get( 'fav_qty_enabled' );
					$txt_btn_enabled = $params->get( 'fav_txt_btn_enabled' );
		
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
			<?php $type='text'; 
			if (!$qty_enabled) {
			$type='hidden'; 
			$product_ord = 1;
			}
			?>
            <span class="quantity-box">
			<input type="<?php echo $type ?>" class="quantity-input" name="quantity[]" value="<?php echo $product_ord; ?>" />
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
