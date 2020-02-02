<?php
defined('_JEXEC')or die;

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 


//tranformse current GET object into URL: 
require(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'getget.php');


?>
<label for="catid_<?php echo $id; ?>"><input type="checkbox" <?php echo $checked; ?> class="productfilter_selector" value="<?php echo $id; ?>" data-value="<?php echo $id; ?>"  id="catid_<?php echo $id; ?>" onclick="return mod_productcustoms.goTo(this);" name="virtuemart_category_id" data-name="virtuemart_category_id" />
<a href="<?php echo PCH::getLink($get, array('virtuemart_category_id'=>$id)); ?>" data-value="<?php echo $id; ?>" data-name="virtuemart_category_id" rel="nofollow" onclick="return mod_productcustoms.goTo(this);"><?php echo $val; ?></a></label>