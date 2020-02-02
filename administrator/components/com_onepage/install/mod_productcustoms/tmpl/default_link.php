<?php
defined('_JEXEC')or die;

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 


//tranformse current GET object into URL: 
require(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'getget.php');

if (empty($checked)) $checked = ''; 
?>
<label for="id_<?php echo $id; ?>"><input type="checkbox" <?php echo $checked; ?> class="productfilter_selector" value="<?php echo $id; ?>" data-name="virtuemart_custom_id" data-value="<?php echo $id; ?>"  onclick="return mod_productcustoms.goTo(this);" name="virtuemart_custom_id" />
<a class="filter_link" href="<?php echo PCH::getLink($get, array('virtuemart_custom_id'=>$id)); ?>" data-value="<?php echo $id; ?>" data-name="virtuemart_custom_id" rel="nofollow" onclick="return mod_productcustoms.goTo(this);"><?php echo $val; ?></a></label>