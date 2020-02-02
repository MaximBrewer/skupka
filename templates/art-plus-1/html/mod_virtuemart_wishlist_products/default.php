<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$tt_item=0;
$i = 0;
if($user->guest) {
   if (!isset($_COOKIE['virtuemart_wish_session'])) {
		$session =& JFactory::getSession();
		setcookie('virtuemart_wish_session',$session->getId(),2592000 + time(),'/');
		$_COOKIE['virtuemart_wish_session'] = $session->getId();
			}
		$user_id = $_COOKIE['virtuemart_wish_session'];
}
else $user_id = $user->id;
$fav_products = mod_virtuemart_wishlist_products::getfavorites($user_id,$num_favorites);

if($guest_enabled || !$user->guest)
{
	echo '<div id="mod_virtuemart_wishlist_products_cont">';
	echo '<a class="link_wishlist" href="/spisok-sravneniya.html">В избранном ('.count($fav_products).')</a>';
	/*
	if (count($fav_products) == 0) {
	    echo JText::_('VM_FAVORITE_NOFAV');}
	else {
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<?php
	  $productModel = new VirtueMartModelProduct();
	  foreach ($fav_products as $fav_product) {
		  $product = $productModel->getProduct($fav_product->product_id);
		  $productModel->addImages($product);
		  if ($i == 0) {
			  $sectioncolor = "sectiontableentry2";
			  $i += 1;
		  }
		  else {
			  $sectioncolor = "sectiontableentry1";
			  $i -= 1;
		  }
		  if( !$fav_product->category_layout ) {
			$category_layout = "default";
		  }
		  else {
			$category_layout = $fav_product->category_layout;
		  }
		  $tt_item++;
		  $pid = $fav_product->product_parent_id ? $fav_product->product_parent_id : $fav_product->product_id;
		  $link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$pid.'&virtuemart_category_id='.
	      $fav_product->virtuemart_category_id);
		  ?>
		<tr class="<?php echo $sectioncolor ?>">
		  <td width="15%">
		  	<?php if($image_enabled && !empty($product->images[0])) {
		  			$image = $product->images[0]->displayMediaThumb('width="'.$image_size.'" border="0"',false);
		  	?>
		  			<a href="<?php echo $link; ?>" title="<?php echo $fav_product->product_name; ?>" alt="<?php echo $fav_product->product_name; ?>"><?php echo $image; ?></a>
		  	<?php
		  	}
		  	else printf("%02d", $tt_item); ?>
		  </td>
		  <td width="85%" style="vertical-align:middle">
			<a href="<?php echo $link; ?>"><?php echo $fav_product->product_name; ?></a>
		  </td>
		</tr>
		<?php
	  } ?>
	</table><br />
	 <a href="<?php echo JRoute::_("index.php?option=com_wishlist&view=favoriteslist"); ?>"> <?php echo JText::_('VM_ALL_FAVORITE_PRODUCTS') ?></a>
	 <?php if($share_enabled && !$user->guest) { ?>
		 <br />
		 <a href="<?php echo JRoute::_("index.php?option=com_wishlist&view=favoritessh"); ?>"> <?php echo JText::_('VM_SHARE_FAVORITES') ?></a>
	<?php
	}}
	*/
	echo '</div>';
}
else
{
	$redirectUrl = JURI::current();
	$redirectUrl = urlencode(base64_encode($redirectUrl));
	$redirectUrl = '&return='.$redirectUrl;
	$joomlaLoginUrl = 'index.php?option=com_users&view=login';
    $finalUrl = $joomlaLoginUrl . $redirectUrl;
	$addtofavorites = '<a href="'.$finalUrl.'" alt="Login" title="Login">'.JText::_('VM_FAVORITE_LOGIN').'</a>';
	echo $addtofavorites;
}
?>
