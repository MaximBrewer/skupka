<?php // no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere

vmJsApi::jPrice();
$col = 1;
$pwidth = ' width' . floor( 100 / $products_per_row );
if ( $products_per_row > 1 )
{
	$float = "floatleft";
}
else
{
	$float = "center";
}
$ar_compare = array();
if ( ! empty( $_SESSION['compare'] ) )
{
	foreach ( $_SESSION['compare'] as $key => $product_id )
	{
		array_push( $ar_compare, $product_id );
	}
}
//получаем пользователя
$user    =& JFactory::getUser();
$user_id = $user->guest ? $_COOKIE['virtuemart_wish_session'] : $user->id;
$db =& JFactory::getDBO();
$language =& JFactory::getLanguage();
$language_tag = $language->getTag();
JFactory::getLanguage()->load('com_wishlist', JPATH_SITE, $language_tag, true);
?>

<div class="linelabgroup vmgroup<?php echo $params->get( 'moduleclass_sfx' ) ?>">

	<?php if ( $headerText ) { ?>

        <div class="vmheader"><?php echo $headerText ?></div>

		<?php

	}

	if ( $display_style == "div" )
	{

		?>

        <div class="vmproduct<?php echo $params->get( 'moduleclass_sfx' ); ?> ">
			<?php foreach ( $products as $product ) { ?>
                <div class="<?php echo $pwidth ?> <?php echo $float ?> productdetails">

                    <div class="spacer">
                        <div class="wmvo_otstyp">
                            <div class="wmvo_vnewnaa_granica">
                                <div class="vm-product-media-container">
                                    
                                    <?php include JPATH_PLUGINS.'/system/vmquickview/tmpl/vmquickview-button.php'; ?>
                                    <div class="wmvo_izobrajenie">
                                        <div class="wmvo_izobrajenie2">
											<?php

											if ( ! empty( $product->images[0] ) )
											{

												$image = $product->images[0]->displayMediaThumb( 'class="featuredProductImage" border="0"', false );

											}
											else
											{

												$image = '';

											}

											echo JHTML::_( 'link', JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ), $image, array( 'title' => $product->product_name ) );

											echo '<div class="clear"></div>';

											$url = JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .

											                  $product->virtuemart_category_id ); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="vm-product-descr-container">
                                    <div class="wmvo_nazvanie_kategoria">
                                        <div class="wmvo_nazvanie_kategoria_2">
                                            <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>
                                        </div>
                                    </div>
                                    <div class="ma_kat">
  <?php echo $product->mf_name; ?>&nbsp;\&nbsp;<?php echo $product->category_name ?>
</div>
                                    <div class="product_sku_cat"><?php echo 'Артикул: ', $product->product_sku; ?></div>

                                    <div class="wmvo_kratkoe_opisanie">

										<?php

										if ( ! empty( $product->product_s_desc ) )
										{

											echo shopFunctionsF::limitStringByWord( $product->product_s_desc, 9999, ' ...' ) ?>

										<?php } ?>

                                    </div>
                                </div>
                                <div class="vm-product-detail-container">
                                    <div class="wmvo_cena">

										<?php
										//цена
										echo shopFunctionsF::renderVmSubLayout( 'prices', array(
											'product' => $product,
											'currency' => $currency
										) ); ?>

                                    </div>
                                    <div class="vm-product-rating-container">
                    <?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
				if ( VmConfig::get ('display_stock', 1)) { ?>
                    <span><?php echo $product->stock->stock_tip ?></span>
                    <?php if (($product->product_in_stock - $product->product_ordered) > 1) {
                             echo '('.$product->product_in_stock.')';
                           }
                    ?>
                    <?php }
				        echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
				    ?>
                    </div>
									<?php
									//добавить в корзину
									if ( $show_addtocart )
									{
										echo shopFunctionsF::renderVmSubLayout( 'addtocart', array( 'product' => $product ) );
									}
									?>
                                    <!-- добавить сравнение start -->
                                    <div class="wmvo_sravnit">
										<?php
										$cl = "";
										if ( in_array( $product->virtuemart_product_id, $ar_compare ) )
										{
											$cl = "in-comparison";
										}
										?>
                                        <span data-id="<?= $product->virtuemart_product_id ?>"
                                              class="btn-compare fa fa-random <?= $cl ?> compa<?= $product->virtuemart_product_id ?>"
                                              data-link="/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=<?php echo $product->virtuemart_product_id; ?>&compare=1&tack=add">
                                        </span>
                                    </div>
                                    <!-- добавить сравнение end -->
                                    <!--купить в один клик -->
                                    <div class="wmvo_bistraj_pokypka">
										<?php $link_ = JURI::base() . JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ); ?>
                                        {popup url="index.php?option=com_rsform&formId=22&tmpl=component" }{/popup}
                                        <a data-link="<?php echo $link_; ?>" data-name="<?= $product->product_name ?>"
                                           class="tovar_zakaz"
                                           href="index.php?option=com_rsform&formId=22&tmpl=component">
                                            купить в 1 клик
                                        </a>
                                    </div>
                                    <!--купить в один клик end-->
                                    <!-- произвольные поля  -->
									<?php
									if ( $customfields )
									{
										echo shopFunctionsF::renderVmSubLayout( 'customfields',array( 'product'  => $product, 'position' => $customfields	) );
									}
									?>
                                    <!-- добавить в избранное -->
                                    <div class="wishlist">
                                        <!-- добавить в избранное -->
                                        <div class="wishlist">
		                                    <?php
		                                    $form_favorite = '<form class="form_wishlist_products" style="display: inline-block; text-align: center; margin:0px" ';
		                                    $form_favorite .=' method="POST" name="deletefavo" id="'. uniqid('deletefavo_') .'">
						                                        <input type="hidden" name="option" value="com_ajax">
					                                            <input type="hidden" name="module" value="virtuemart_wishlist_products">
					                                             <input type="hidden" name="format" value="json">';


		                                    $q = "SELECT COUNT(*) FROM #__virtuemart_favorites WHERE user_id ='".$user_id."' AND product_id=".$product->virtuemart_product_id;
		                                    $db->setQuery($q);
		                                    $result = $db->loadResult();
		                                    if ($result > 0 ){
			                                    $form_favorite .= '<button class="modns button art-button art-button del" title="'.JText::_('').'" >';
			                                    $form_favorite .= JText::_('VM_REMOVE_FAVORITE').'</button>';
			                                    $form_favorite .= '<input type="hidden" name="mode" value="fav_del" />';
		                                    }else{
			                                    $form_favorite .= '<button class="modns button art-button art-button" title="'.JText::_('').'" >';
			                                    $form_favorite .= JText::_('VM_ADD_TO_FAVORITES').'</button>';
			                                    $form_favorite .= '<input type="hidden" name="mode" value="fav_add" />';
		                                    }
		                                    $form_favorite .= '<input type="hidden" name="favorite_id" value="'. $product->virtuemart_product_id .'" />';
		                                    $form_favorite .= '</form>';
		                                    echo $form_favorite;
		                                    ?>
                                        </div>
                                        <!-- добавить в избранное end -->
                                    </div>
                                    <!-- добавить в избранное end -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

				<?php
				if ( $col == $products_per_row && $products_per_row && $col < $totalProd )
				{
					echo "	</div><div style='clear:both;'>";
					$col = 1;
				}
				else
				{
					$col ++;
				}
			} ?>

        </div>

        <br style='clear:both;'/>
		<?php
	}
	else
	{
		$last = count( $products ) - 1;
		?>
        <ul class="vmproduct<?php echo $params->get( 'moduleclass_sfx' ); ?> productdetails">
			<?php foreach ( $products as $product ) : ?>
                <li class="<?php echo $pwidth ?> <?php echo $float ?>" style="list-style: none;">
					<?php
					if ( ! empty( $product->images[0] ) )
					{
						$image = $product->images[0]->displayMediaThumb( 'class="featuredProductImage" border="0"', false );
					}
					else
					{
						$image = '';
					}
					echo JHTML::_( 'link', JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ), $image, array( 'title' => $product->product_name ) );
					echo '<div class="clear"></div>';
					$url = JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id ); ?>

                    <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a> <?php echo '<div class="clear"></div>';

					// $product->prices is not set when show_prices in config is unchecked

					if ( $show_price and isset( $product->prices ) )
					{

						echo '<div class="product-price">' . $currency->createPriceDiv( 'salesPrice', '', $product->prices, false, false, 1.0, true );

						if ( $product->prices['salesPriceWithDiscount'] > 0 )
						{

							echo $currency->createPriceDiv( 'salesPriceWithDiscount', '', $product->prices, false, false, 1.0, true );

						}

						echo '</div>';

					}

					if ( $show_addtocart )
					{

						echo shopFunctionsF::renderVmSubLayout( 'addtocart', array( 'product' => $product ) );

					}

					?>

                </li>

				<?php

				if ( $col == $products_per_row && $products_per_row && $last )
				{

					echo '

		</ul><div class="clear"></div>

		<ul  class="vmproduct' . $params->get( 'moduleclass_sfx' ) . ' productdetails">';

					$col = 1;

				}
				else
				{

					$col ++;

				}

				$last --;

			endforeach; ?>

        </ul>

        <div class="clear"></div>


		<?php

	}

	if ( $footerText ) : ?>

        <div class="vmfooter<?php echo $params->get( 'moduleclass_sfx' ) ?>">

			<?php echo $footerText ?>

        </div>

	<?php endif; ?>

</div>