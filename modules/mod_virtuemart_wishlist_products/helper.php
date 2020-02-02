<?php
/*
* Module Helper
*
* @package VirtueMart
* Serjoka serjoka@gmail.com
* @package VirtueMart
* @subpackage classes
* @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
* This program is distributed under the terms of the GNU General Public License
*/

defined( '_JEXEC' ) or die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' );
if ( ! class_exists( 'VmConfig' ) )
{
	require( JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php' );
}
VmConfig::loadConfig();

// Load the language file of com_virtuemart.
JFactory::getLanguage()->load( 'com_virtuemart' );
if ( ! class_exists( 'calculationHelper' ) )
{
	require( JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/calculationh.php' );
}
if ( ! class_exists( 'CurrencyDisplay' ) )
{
	require( JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/currencydisplay.php' );
}
if ( ! class_exists( 'VirtueMartModelVendor' ) )
{
	require( JPATH_ADMINISTRATOR . '/components/com_virtuemart/models/vendor.php' );
}
if ( ! class_exists( 'VmImage' ) )
{
	require( JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/image.php' );
}
if ( ! class_exists( 'shopFunctionsF' ) )
{
	require( JPATH_SITE . '/components/com_virtuemart/helpers/shopfunctionsf.php' );
}
if ( ! class_exists( 'calculationHelper' ) )
{
	require( JPATH_COMPONENT_SITE . '/helpers/cart.php' );
}
if ( ! class_exists( 'VirtueMartModelProduct' ) )
{
	JLoader::import( 'product', JPATH_ADMINISTRATOR . '/components/com_virtuemart/models' );
}

class mod_virtuemart_wishlist_products {

	function getfavorites( $user_id, $num_favorites ) {
		// getting the language tag for virtuemart_products table
		$siteLang = JFactory::getLanguage()->getTag();
		$lang     = strtolower( strtr( $siteLang, '-', '_' ) );

		$list = "SELECT f.product_id, f.user_id, p.product_parent_id, pl.product_name, p.published, pc.virtuemart_product_id, c.virtuemart_category_id, c.category_layout ";
		$list .= "FROM #__virtuemart_favorites f, #__virtuemart_products p, #__virtuemart_products_" . $lang . " pl, #__virtuemart_product_categories pc, #__virtuemart_categories c WHERE ";
		$q    = "f.user_id = '" . $user_id . "' AND ";
		$q    .= "p.virtuemart_product_id = f.product_id AND ";
		$q    .= "p.published ='1' AND ";
		$q    .= "pc.virtuemart_product_id = IF (p.product_parent_id=0, p.virtuemart_product_id, p.product_parent_id) AND ";
		$q    .= "pc.virtuemart_category_id = c.virtuemart_category_id AND ";
		$q    .= "pl.virtuemart_product_id = p.virtuemart_product_id ";
		$q    .= "GROUP BY p.virtuemart_product_id ";
		$q    .= "ORDER BY pl.product_name ";
		$list .= $q . " LIMIT 0," . $num_favorites;

		$db =& JFactory::getDBO();
		$db->setQuery( $list );
		$result = $db->loadObjectList();

		return $result;
	}
}

class modVirtuemartWishlistProductsHelper {
	public static function getAjax() {

		if ( ! class_exists( 'VirtueMartModelProduct' ) )
		{
			JLoader::import( 'product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
		}

		$com_params    = &JComponentHelper::getParams( 'com_wishlist' );
		$guest_enabled = $com_params->get( 'tmpl_guest_enabled' );

		$app    = JFactory::getApplication();
		$module = JModuleHelper::getModule( 'mod_virtuemart_wishlist_products' );
		$params = new JRegistry( $module->params );

		$share_enabled = $params->get( 'share_enabled', 1 );
		$image_enabled = $params->get( 'image_enabled', 1 );
		$image_size    = $params->get( 'image_size', 30 );
		$num_favorites = $params->get( 'num_favorites', 10 );

		$language     =& JFactory::getLanguage();
		$language_tag = $language->getTag();
		JFactory::getLanguage()->load( 'com_wishlist', JPATH_SITE, $language_tag, true );

		$input   = JFactory::getApplication()->input;

		$user    =& JFactory::getUser();
		$user_id = $user->guest ? $_COOKIE['virtuemart_wish_session'] : $user->id;

		$mode       = $input->get( 'mode' );
		$product_id = $input->get( 'favorite_id' );

		$quantity = 1;
		$db       =& JFactory::getDBO();

		$res = [];
		if ( $mode == "fav_del" )
		{
			$Sql = "DELETE FROM #__virtuemart_favorites ";
			$Sql .= "WHERE product_id='$product_id' AND user_id='$user_id'";
			$db->setQuery( $Sql );
			$db->query();
			$result = 0;

			$res['but']  =  JText::_( 'VM_ADD_TO_FAVORITES' );
//			$res['but']  = '<i class="fa fa-star"></i>' . JText::_( 'VM_ADD_TO_FAVORITES' );
			$res['mode'] = "fav_add";
		}
		if ( $mode == "fav_add" )
		{
			$Sql = "INSERT INTO #__virtuemart_favorites ";
			$Sql .= "SET product_id='$product_id', product_qty='$quantity', user_id='$user_id', fav_date=NOW(), isGuest=" . $user->guest;
			$db->setQuery( $Sql );
			$db->query();
			$res['but']  =  JText::_( 'VM_REMOVE_FAVORITE' );
//			$res['but']  = '<i class="fa fa-star"></i>' . JText::_( 'VM_REMOVE_FAVORITE' );
			$res['mode'] = "fav_del";
		}

		$fav_products_html = '';
		$fav_products      = mod_virtuemart_wishlist_products::getfavorites( $user_id, $num_favorites );

		$tt_item=0;
		$i = 0;

		if ($guest_enabled || !$user->guest )
		{
			$fav_products_html.= '<a class="link_wishlist" href="/spisok-sravneniya.html">В избранном ('.count($fav_products).')</a>';

			/*
			if ( count( $fav_products ) == 0 )
			{
				$fav_products_html .= JText::_( 'VM_FAVORITE_NOFAV' );
			}
			else
			{
				ob_start();
				?>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<?php
					$productModel = new VirtueMartModelProduct();
					foreach ( $fav_products as $fav_product )
					{
						$product = $productModel->getProduct( $fav_product->product_id );
						$productModel->addImages( $product );
						if ( $i == 0 )
						{
							$sectioncolor = "sectiontableentry2";
							$i            += 1;
						}
						else
						{
							$sectioncolor = "sectiontableentry1";
							$i            -= 1;
						}
						if ( ! $fav_product->category_layout )
						{
							$category_layout = "default";
						}
						else
						{
							$category_layout = $fav_product->category_layout;
						}
						$tt_item ++;
						$pid  = $fav_product->product_parent_id ? $fav_product->product_parent_id : $fav_product->product_id;
						$link = JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $pid . '&virtuemart_category_id=' .
										   $fav_product->virtuemart_category_id );
						?>
						<tr class="<?php echo $sectioncolor ?>">
							<td width="15%">
								<?php if ( $image_enabled && ! empty( $product->images[0] ) )
								{
									$image = $product->images[0]->displayMediaThumb( 'width="' . $image_size . '" border="0"', false );
									?>
									<a href="<?php echo $link; ?>" title="<?php echo $fav_product->product_name; ?>"
									   alt="<?php echo $fav_product->product_name; ?>"><?php echo $image; ?></a>
									<?php
								}
								else
								{
									printf( "%02d", $tt_item );
								} ?>
							</td>
							<td width="85%" style="vertical-align:middle">
								<a href="<?php echo $link; ?>"><?php echo $fav_product->product_name; ?></a>
							</td>
						</tr>
						<?php
					} ?>
				</table><br/>
				<a href="<?php echo JRoute::_( "index.php?option=com_wishlist&view=favoriteslist" ); ?>">
					<?php echo JText::_( 'VM_ALL_FAVORITE_PRODUCTS' ) ?>
				</a>
				<?php if ( $share_enabled && ! $user->guest ) { ?>
				<br/>
				 <a href="<?php echo JRoute::_( "index.php?option=com_wishlist&view=favoritessh" ); ?>"> <?php echo JText::_( 'VM_SHARE_FAVORITES' ) ?></a>
				<?php
			}
				$fav_products_html .= ob_get_clean();
			}
			*/
		}
		$res['fav_products'] = $fav_products_html;

		return $res;
	}
}
