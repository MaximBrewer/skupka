<?php
//получаем пользователя
$user    =& JFactory::getUser();
$user_id = $user->guest ? $_COOKIE['virtuemart_wish_session'] : $user->id;
$db =& JFactory::getDBO();
$language =& JFactory::getLanguage();
$language_tag = $language->getTag();
JFactory::getLanguage()->load('com_wishlist', JPATH_SITE, $language_tag, true);
?>
<div class="wmvo_otstyp">
    <div class="wmvo_vnewnaa_granica">
        <div class="vm-product-media-container">
            <div class="wmvo_izobrajenie">
                <div class="wmvo_izobrajenie2">
					<?php defined( '_JEXEC' ) or die( 'Restricted access' );
					$related     = $viewData['related'];
					$customfield = $viewData['customfield'];
					$thumb       = $viewData['thumb'];
					$ar_compare  = array();
					if ( ! empty( $_SESSION['compare'] ) )
					{
						foreach ( $_SESSION['compare'] as $key => $product_id )
						{
							array_push( $ar_compare, $product_id );
						}
					}
					echo '<div class="related-product-image">' . JHtml::link( JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $related->virtuemart_product_id . '&virtuemart_category_id=' . $related->virtuemart_category_id ), $thumb, array( 'title' => $related->product_name, ) ) . '</div>'; ?>
                </div>
            </div>
        </div>
        <div class="vm-product-descr-container">
            <div class="wmvo_nazvanie_kategoria">
                <div class="wmvo_nazvanie_kategoria_2">
					<?php echo JHtml::link( JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $related->virtuemart_product_id . '&virtuemart_category_id=' . $related->virtuemart_category_id ), $related->product_name, array( 'title' => $related->product_name, ) );
					if ( $customfield->wPrice )
					{
					$currency = calculationHelper::getInstance()->_currencyDisplay;
					?></div>
           <div class="ma_kat">
                <?php echo $related->mf_name; ?>&nbsp;\&nbsp;<?php echo $related->category_name ?>
            </div>
            </div>
            <div class="product_sku_cat">Артикул: <?php echo $related->product_sku ?></div>
            <div class="wmvo_kratkoe_opisanie"><?php echo $related->product_s_desc ?></div>
        </div>
        <div class="vm-product-detail-container">
            <div class="wmvo_cena">
				<?php
				echo shopFunctionsF::renderVmSubLayout( 'prices', array( 'product'  => $related,
				                                                         'currency' => $currency
				) );
				?>
            </div>
			<?php
			//	$currency = calculationHelper::getInstance()->_currencyDisplay;
			//	echo $currency->createPriceDiv ('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $related->prices);
			//	echo $currency->createPriceDiv ('product_price', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $related->prices);
			}
			?>

			<?php
			// поля  metr_kv и  v_nalicie
			$customfields = $related->customfields;
			foreach ( $customfields as $customfield )
			{
				if ( $customfield->virtuemart_custom_id == 88 || $customfield->virtuemart_custom_id == 87 )
				{
					echo '<div class="customfield_related">' . $customfield->customfield_value . '</div>';
				}
			}
			?>
			<?php echo shopFunctionsF::renderVmSubLayout( 'addtocart', array( 'product' => $related, 'row' => 0 ) ); ?>

            <!-- добавить в сравнение -->
            <div class="wmvo_sravnit">
				<?php
				$cl = "";
				if ( in_array( $related->virtuemart_product_id, $ar_compare ) )
				{
					$cl = "in-comparison";
				}
				?>
                <span data-id="<?= $related->virtuemart_product_id ?>"  class="btn-compare fa fa-random <?= $cl ?> compa<?= $product->virtuemart_product_id ?>"
                      data-link="/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=<?php echo $related->virtuemart_product_id; ?>&compare=1&tack=add"></span>
            </div>
            <!-- добавить в сравнение -->
            <!--быстрая покупка -->
            <div class="wmvo_bistraj_pokypka">
				<?php $link_ = JURI::base() . JRoute::_( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $related->virtuemart_product_id . '&virtuemart_category_id=' . $related->virtuemart_category_id ); ?>
                {popup url="index.php?option=com_rsform&formId=22&tmpl=component" }{/popup}
                <a data-link="<?php echo $link_; ?>" data-name="<?= $related->product_name ?>" class="tovar_zakaz"
                   href="index.php?option=com_rsform&formId=22&tmpl=component">купить в 1 клик</a>
            </div>
            <!--быстрая покупка end -->

            <!-- добавить в избранное -->
           <?php /*?> <div class="wishlist">
		        <?php
		        $form_favorite = '<form class="form_wishlist_products" style="display: inline-block; text-align: center; margin:0px" ';
		        $form_favorite .=' method="POST" name="deletefavo" id="'. uniqid('deletefavo_') .'">
						                                        <input type="hidden" name="option" value="com_ajax">
					                                            <input type="hidden" name="module" value="virtuemart_wishlist_products">
					                                             <input type="hidden" name="format" value="json">';


		        $q = "SELECT COUNT(*) FROM #__virtuemart_favorites WHERE user_id ='".$user_id."' AND product_id=".$related->virtuemart_product_id;
		        $db->setQuery($q);
		        $result = $db->loadResult();
		        if ($result > 0 ){
			        $form_favorite .= '<button class="modns button art-button art-button" title="'.JText::_('').'" >';
			        $form_favorite .= JText::_('VM_REMOVE_FAVORITE').'</button>';
			        $form_favorite .= '<input type="hidden" name="mode" value="fav_del" />';
		        }else{
			        $form_favorite .= '<button class="modns button art-button art-button" title="'.JText::_('').'" >';
			        $form_favorite .= JText::_('VM_ADD_TO_FAVORITES').'</button>';
			        $form_favorite .= '<input type="hidden" name="mode" value="fav_add" />';
		        }
		        $form_favorite .= '<input type="hidden" name="favorite_id" value="'. $related->virtuemart_product_id .'" />';
		        $form_favorite .= '</form>';
		        echo $form_favorite;
		        ?>
            </div><?php */?>
            <!-- добавить в избранное end -->

        </div>
  <?php

        //выводим рейтинг ****************************************************************************************
        $query = 'select * from `#__content_vrvote` where content_id = '.$related->virtuemart_product_id.' ';
        $result = $db->setQuery($query);
        $result = $db->loadObject();
        $rand_conteer=rand();
        if ( empty($result) )
        {
            $result->rating_sum = 0;
            $result->rating_count = 0;
        }else{
            $percent = number_format((intval($result->rating_sum) / intval( $result->rating_count ))*20,2);
            $rating = $result->rating_sum/$result->rating_count;
        }
        echo '<div class="product-rating ">';
        echo '<div class="vrvote-body">
				<ul class="vrvote-ul">
					<li id="rating_'.$related->virtuemart_product_id.'" class="current-rating" style="width:'.$percent.'%;"></li>
					<li>
						<a class="vr-one-star" onclick="javascript:JSVRvote('.$related->virtuemart_product_id.',1,'.$result->rating_sum.','.$result->rating_count.',0,'.$rand_conteer.')" href="javascript:void(null)">1</a>
					</li>
					<li>
						<a class="vr-two-stars" onclick="javascript:JSVRvote('.$related->virtuemart_product_id.',2,'.$result->rating_sum.','.$result->rating_count.',0,'.$rand_conteer.')" href="javascript:void(null)">2</a>
					</li>
					<li>
						<a class="vr-three-stars" onclick="javascript:JSVRvote('.$related->virtuemart_product_id.',3,'.$result->rating_sum.','.$result->rating_count.',0,'.$rand_conteer.')" href="javascript:void(null)">3</a>
					</li>
					<li>
						<a class="vr-four-stars" onclick="javascript:JSVRvote('.$related->virtuemart_product_id.',4,'.$result->rating_sum.','.$result->rating_count.',0,'.$rand_conteer.')" href="javascript:void(null)">4</a>
					</li>
					<li>
						<a class="vr-five-stars" onclick="javascript:JSVRvote('.$related->virtuemart_product_id.',5,'.$result->rating_sum.','.$result->rating_count.',0,'.$rand_conteer.')" href="javascript:void(null)">5</a>
					</li>
				</ul>
			</div>
			<span   id="vrvote_'.$rand_conteer.'" class=" vrvote-count" ><small>';
        if ( $result->rating_count != -1 ) {
            if ( $result->rating_count != 0 ) {
                echo "(";
                if($result->rating_count!=1) {
                    echo round($rating, 2).' - '.$result->rating_count.' голосов';
                } else {
                    echo round($rating, 2).' - '.$result->rating_count.' голос';
                }
                echo ")";
            }
        }
        echo "</small></span>";
        echo '	</span>';
        echo '</div>';
        //выводим рейтинг  end****************************************************************************************

        ?>
    </div>
</div>