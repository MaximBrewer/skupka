
<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 8811 2015-03-30 23:11:08Z Milbo $
 */

defined ('_JEXEC') or die('Restricted access');

?> 
<div class="category_zagolovok">
  <h1><?php echo vmText::_($this->category->category_name); ?></h1>
</div>

<div class="category-view"> <?php
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";
vmJsApi::addJScript('vm.hover',$js);

if (empty($this->keyword) AND !empty($this->category) AND !empty($this->category->category_description) ) {
	?></div>
<div class="category_description">
	<?php echo $this->category->category_description; ?>
</div>

  <?php
}
?>
<div class="cat_child">
<?php
// Show child categories
if (VmConfig::get ('showCategory', 1) AND empty($this->keyword) AND 1 == JRequest::getInt('showcategories',1 ) ) {
	if ($this->category->haschildren && !JRequest::getBool('search')) {
		
		echo ShopFunctionsF::renderVmSubLayout('categories',array('categories'=>$this->category->children));

	}
}

// get manufacturer models
	if (0 != JRequest::getInt('virtuemart_manufacturer_id',0 ) AND 1 == JRequest::getInt('showdescription',1 ) ) {
		$model = VmModel::getModel('manufacturer');

		$manufacturer = $model->getManufacturer();
		$model->addImages($manufacturer,1);

		$manufacturerImage = $manufacturer->images[0]->displayMediaThumb('class="manufacturer-image"',false);
//		echo '<span style="float:right">'.$manufacturerImage.'</span><br />';
		echo $manufacturer->mf_desc.'<br />';
	}


if($this->showproducts){
?>
</div>
<div class="browse-view">
<?php

if (!empty($this->keyword)) {
	//id taken in the view.html.php could be modified
	$category_id  = vRequest::getInt ('virtuemart_category_id', 0); ?>
  <h3><?php echo $this->keyword; ?></h3>

	<form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0', FALSE); ?>" method="get">
	</form>
		
<?php  } ?>

<?php // Show child categories

	?>
<?php  if (count($this->products)>=1) { ?>
<div class="orderby-displaynumber">

        
          <div class="floatleft vm-order-list">
            <?php
                   echo $this->orderByList['orderby'];
            ?>
            <?php /*<?php echo $this->orderByList['manufacturer']; ?> */?>
  </div>
       
          <div class="display-number"><?php /*?><?php echo $this->vmPagination->getResultsCounter ();?><?php */?>
               <?php
                     echo $this->vmPagination->getLimitBox ($this->category->limit_list_step);                             
                ?>                       
          </div>
           <?php
    // получаем ссылку для количества записей
    $getArray = vRequest::getGet();
    $link = '';
    unset ($getArray['limit']);
    foreach ($getArray as $key => $value) {
        if (is_array ($value)) {
            foreach ($value as $k => $v) {
                $link .= '&' . urlencode($key) . '[' . urlencode($k) . ']' . '=' . urlencode($v);
            }
        }
        else {

            $link .= '&' . urlencode($key) . '=' . urlencode($value);
        }
    }
    $link = 'index.php?'. ltrim( $link, '&' );
    ?>
    <script>
        window.globalLinklimit='<?php echo JRoute::_( $link, false ); ?>'
    </script>
           <!-- <div class="product-view-button">
    <span>Вид: </span>
    <div class="button_fi"> <a href="#" class="grid active">
      <div class="setkoi"></div>
      </a>
      <a href="#" class="list">
      <div class="spiskom"></div>
      </a></div>
  </div>-->
</div>

<div class="cl_bo"></div>
<div class="vm-pagination vm-pagination-top">
		<?php echo $this->vmPagination->getPagesLinks (); ?>
		<span class="vm-page-counter"><?php echo $this->vmPagination->getPagesCounter (); ?></span>
</div>
 <?php }?>
 <!-- end of orderby-displaynumber -->

	<?php
	if (!empty($this->products)) {
	$products = array();
	$products[0] = $this->products;
	echo shopFunctionsF::renderVmSubLayout($this->productsLayout,array('products'=>$products,'currency'=>$this->currency,'products_per_row'=>$this->perRow,'showRating'=>$this->showRating));

	?>

<div class="vm-pagination vm-pagination-bottom"><?php echo $this->vmPagination->getPagesLinks (); ?><span class="vm-page-counter"><?php echo $this->vmPagination->getPagesCounter (); ?></span></div>

	<?php
} elseif (!empty($this->keyword)) {
	echo vmText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
}
elseif(JRequest::getBool('search')){
echo 'К сожалению товар по вашим критериям не найден.'; 
}
?>


<?php } ?>


<?php
$j = "Virtuemart.container = jQuery('.category-view');
Virtuemart.containerSelector = '.category-view';";

vmJsApi::addJScript('ajaxContent',$j);
?>
</div></div>
<!-- end browse-view -->

<script>
    var productView = localStorage.getItem('productView');
    if(productView == 'list'){
        jQuery('.product-view-button .grid').removeClass('active');
        jQuery('.product-view-button .list').addClass('active');
        jQuery('.category-view .browse-view .row').removeClass('grid-view').addClass('list-view');    
    }
    jQuery('.product-view-button .grid').click(function(){
        localStorage.removeItem('productView');
        localStorage.setItem('productView', 'grid');
        jQuery('.product-view-button .list').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.category-view .browse-view .row').removeClass('list-view').addClass('grid-view');
        return false;
    });
    jQuery('.product-view-button .list').click(function(){
        localStorage.removeItem('productView');
        localStorage.setItem('productView', 'list');
        jQuery('.product-view-button .grid').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.category-view .browse-view .row').removeClass('grid-view').addClass('list-view');
        return false;
    });
</script>