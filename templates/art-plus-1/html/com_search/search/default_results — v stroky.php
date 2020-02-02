<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_search
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<style type="text/css">
<!--
.searotp {
	padding-left: 7px;
}
-->
</style>


<dl class="search-results<?php echo $this->pageclass_sfx; ?>">
<?php foreach($this->results as $result) : ?>
	<dt class="result-title">
		<?php echo $this->pagination->limitstart + $result->count.'. ';?>
		<?php if ($result->href) :?>
			<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
				<?php echo $this->escape($result->title);?>
			</a>
		<?php else:?>
			<?php echo $this->escape($result->title);?>
		<?php endif; ?>
	</dt>
	<?php if ($result->section) : ?>
		<span class="small<?php echo $this->pageclass_sfx; ?>">
			(<?php echo $this->escape($result->section); ?>)
		</span>
	<?php endif; ?>
<br />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="1">
    	<?php if ($result->image_file_url) { 
    		if ($result->image_file_url_thumb) {
    			$image_src = $result->image_file_url_thumb;
    		} else {
    			$image_src = $result->image_file_url;
    		}   		
    	?>
    	  <img src="<?php echo $image_src; ?>" alt="<?php echo $result->title ?>" width="120" align="top" class="browseProductImage" />
      <?php } ?>
    </td>
    <td valign="top" class="searotp"><?php echo $result->text; ?></td>
  </tr>
</table>

<br /><br />

<?php endforeach; ?>

</dl>

<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>