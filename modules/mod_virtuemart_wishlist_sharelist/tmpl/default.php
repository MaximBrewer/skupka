<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$tt_item=0;
$i = 0;
$fav_sharelists = mod_virtuemart_wishlist_sharelist::getsharelist($num_lists);
if (count($fav_sharelists) == 0) { echo JText::_('VM_SHARED_NOSH');}
else {
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php
  foreach ($fav_sharelists as $fav_sharelist) {
      if ($i == 0) {
          $sectioncolor = "sectiontableentry2";
          $i += 1;
      }
      else {
          $sectioncolor = "sectiontableentry1";
          $i -= 1;
      }
      $tt_item++;
      $uid = $fav_sharelist->user_id;
	  $uname = $fav_sharelist->name;
	  $shtitle = $fav_sharelist->share_title;
	  $iswishlist = $fav_sharelist->isWishList;
      ?>
    <tr class="<?php echo $sectioncolor ?>">
      <td width="15%"><?php printf("%02d", $tt_item); ?></td>
      <td width="85%">
       <?php echo $uname; ?><br />
        <a href="<?php  echo JRoute::_("index.php?option=com_wishlist&view=sharelist&user_id={$uid}"); ?>">
            <?php echo $shtitle; ?>
        </a><br />
        <?php if ($iswishlist) echo '<font color="red">'. JText::_('VM_WISHLIST') . '</font>';?>
      </td>
    </tr>
    <?php 
  } ?>
</table>
<br />
<a href="<?php echo JRoute::_("index.php?option=com_wishlist&view=favoritesshlist"); ?>"> <?php echo JText::_('VM_ALL_SHARED_LISTS') ?></a>
<?php } ?>
