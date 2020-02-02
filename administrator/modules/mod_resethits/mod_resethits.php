<?php
/**
 * Reset Hits module for Joomla
 *
 * @author Joomill (info@joomill.nl)
 * @copyright Copyright (C) 2015 Joomill
 * @license GNU Public License version 2 or later
 * @link http://www.joomill-extensions.com/
 */

defined('_JEXEC') or die;
?>

<?php
$msg = '';
$reset = JRequest::getVar('reset', '', '', 'WORD');
// Article Hits Queries
if ($reset && ($reset == 'hits')) {
	$db = JFactory::getDBO();
	$db->setQuery( 'UPDATE #__content SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Article Revision Queries
if ($reset && ($reset == 'revision')) {
	$db = JFactory::getDBO();
	$db->setQuery( 'UPDATE #__content SET version=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Banner Impression Queries
if ($reset && ($reset == 'bannerimp')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__banners SET impmade=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Banner Clicks Queries
if ($reset && ($reset == 'bannerclicks')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__banners SET clicks=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Weblinks Hits Queries
if ($reset && ($reset == 'weblinkshits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__weblinks SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// User Password Reset Counter Queries
if ($reset && ($reset == 'passreset')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__users SET resetCount=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Fastw3b Real Estate Queries
if ($reset && ($reset == 'fwrehits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__fw_realestate_property SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Fastw3b Gallery Queries
if ($reset && ($reset == 'fwgalhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__fwg_files SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Ignite Gallery Queries
if ($reset && ($reset == 'igalcathits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__igallery SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'igalimghits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__igallery_img SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Rockettheme Gallery Queries
if ($reset && ($reset == 'rokgalviews')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__rokgallery_file_views SET kount=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'rokgalloves')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__rokgallery_file_loves SET kount=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Yootheme ZOO Queries
if ($reset && ($reset == 'zoohits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__zoo_item SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// K2 Queries
if ($reset && ($reset == 'ktwoitemhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__k2_items SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'ktwoattachhits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__k2_attachments SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Virtuemart2 Queries
if ($reset && ($reset == 'vmcathits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__virtuemart_categories SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'vmproducthits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__virtuemart_products SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Jomsocial Queries
if ($reset && ($reset == 'jsprofile')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_users SET view=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsevents')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_events SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsphotos')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_photos SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsalbums')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__photos_albums SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'jsvideos')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__community_videos SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// Akeeba Release System Queries
if ($reset && ($reset == 'arsreleases')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__ars_releases SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'arsitems')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__ars_items SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// EasyDiscuss
if ($reset && ($reset == 'easydiscusshits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__discuss_posts SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'easydiscusslikes')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__discuss_posts SET num_likes=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
if ($reset && ($reset == 'easydiscussvotes')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__discuss_posts SET sum_totalvote=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
// EasyBlog
if ($reset && ($reset == 'easybloghits')) {
	$db =& JFactory::getDBO();
	$db->setQuery( 'UPDATE #__easyblog_post SET hits=0' );
	$db->query();
	$msg = JText::_('RESETDONE');
}
?>

<div class="sidebar-nav quick-icons">
<?php 
// Succesfull Message
if ($msg) {
	JFactory::getApplication()->enqueueMessage('Hits successfull set to 0', 'message'); 
}?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post">
    <H2 class="nav-header"> Reset Joomla Core </h2> 
	<ul class="j-links-group nav nav-list" style="list-style-type: none;">
	<li><input type="radio" name="reset" value="hits"/> <?php echo JText::_('Article Hits'); ?></li>
 	<li><input type="radio" name="reset" value="revision" /> <?php echo JText::_('Article Revision'); ?></li>
	<li><input type="radio" name="reset" value="bannerimp" /> <?php echo JText::_('Banner Impressions'); ?></li>
	<li><input type="radio" name="reset" value="bannerclicks" /> <?php echo JText::_('Banner Hits'); ?></li>
    <?php 	$target_weblinks = (JPATH_BASE .'/components/com_weblinks/weblinks.php');
			if (file_exists($target_weblinks)) {
	?> 
	<li><input type="radio" name="reset" value="weblinkshits" /> <?php echo JText::_('Weblinks Hits'); ?></li>
    <?php 	} ?>
    <li><input type="radio" name="reset" value="passreset" /> <?php echo JText::_('User Password Reset Counter'); ?></li>
    </ul>  
    <H2 class="nav-header"> Reset 3rd Party Components</h2>
    <ul class="j-links-group nav nav-list" style="list-style-type: none;">
    <?php 	$target_fwrealestate = (JPATH_BASE .'/components/com_fwrealestate/fwrealestate.php');
			if (file_exists($target_fwrealestate)) {
	?> 
    <li><input type="radio" name="reset" value="fwrehits" /> <?php echo JText::_('Fastw3b Real Estate Hits'); ?></li>
    <?php 	}
			$target_fwgallery = (JPATH_BASE .'/components/com_fwgallery/fwgallery.php');
			if (file_exists($target_fwgallery)) {
	?> 
    <li><input type="radio" name="reset" value="fwgalhits" /> <?php echo JText::_('Fastw3b Gallery Hits'); ?></li>
    <?php 	}
			$target_igallery = (JPATH_BASE .'/components/com_igallery/igallery.php');
			if (file_exists($target_igallery)) {
	?> 
    <li><input type="radio" name="reset" value="igalcathits" /> <?php echo JText::_('Ignite Gallery Category Hits'); ?></li>
    <li><input type="radio" name="reset" value="igalimghits" /> <?php echo JText::_('Ignite Gallery Images Hits'); ?></li>
    <?php 	}
			$target_rokgallery = (JPATH_BASE .'/components/com_rokgallery/rokgallery.php');
			if (file_exists($target_rokgallery)) {
	?> 
    <li><input type="radio" name="reset" value="rokgalviews" /> <?php echo JText::_('RokGallery Images Views'); ?></li>
    <li><input type="radio" name="reset" value="rokgalloves" /> <?php echo JText::_('RokGallery Images Loves'); ?></li>
     <?php 	}
			$target_zoo = (JPATH_BASE .'/components/com_zoo/zoo.php');
			if (file_exists($target_zoo)) {
	?> 
    <li><input type="radio" name="reset" value="zoohits" /> <?php echo JText::_('Yootheme ZOO Items Hits'); ?></li>
    <?php 	}
			$target_k2 = (JPATH_BASE .'/components/com_k2/k2.php');
			if (file_exists($target_k2)) {
	?> 
    <li><input type="radio" name="reset" value="ktwoitemhits" /> <?php echo JText::_('K2 Items Hits'); ?></li>
    <li><input type="radio" name="reset" value="ktwoattachhits" /> <?php echo JText::_('K2 Attachments Hits'); ?></li>
    <?php 	}
			$target_vm = (JPATH_BASE .'/components/com_virtuemart/virtuemart.php');
			if (file_exists($target_vm)) {
	?>  
    <li><input type="radio" name="reset" value="vmcathits" /> <?php echo JText::_('Virtuemart Category Hits'); ?></li>
   	<li><input type="radio" name="reset" value="vmproducthits" /> <?php echo JText::_('Virtuemart Product Hits'); ?></li>  
    <?php 	}
			$target_js = (JPATH_BASE .'/components/com_community/community.php');
			if (file_exists($target_js)) {
	?>  
    <li><input type="radio" name="reset" value="jsprofile" /> <?php echo JText::_('Jomsocial Profile Views'); ?></li>
   	<li><input type="radio" name="reset" value="jsevents" /> <?php echo JText::_('Jomsocial Events Hits'); ?></li>
    <li><input type="radio" name="reset" value="jsphotos" /> <?php echo JText::_('Jomsocial Photo Hits'); ?></li>
    <li><input type="radio" name="reset" value="jsalbums" /> <?php echo JText::_('Jomsocial Photo Album Hits'); ?></li>
    <li><input type="radio" name="reset" value="jsvideos" /> <?php echo JText::_('Jomsocial Video Hits'); ?></li>
    <?php 	}
			$target_ars = (JPATH_BASE .'/components/com_ars/ars.php');
			if (file_exists($target_ars)) {
	?>  
    <li><input type="radio" name="reset" value="arsreleases" /> <?php echo JText::_('Akeeba Release System - Releases'); ?></li>
    <li><input type="radio" name="reset" value="arsitems" /> <?php echo JText::_('Akeeba Release System - Items'); ?></li>
    <?php 	}
			$target_ars = (JPATH_BASE .'/components/com_easydiscuss/easydiscuss.php');
			if (file_exists($target_ars)) {
	?>  
    <li><input type="radio" name="reset" onclick="javascript:yesnoCheck();" value="easydiscusshits" /> <?php echo JText::_('EasyDiscuss Posts Hits'); ?></li>
    <li><input type="radio" name="reset" onclick="javascript:yesnoCheck();" value="easydiscusslikes" /> <?php echo JText::_('EasyDiscuss Posts Likes'); ?></li>
    <li><input type="radio" name="reset" onclick="javascript:yesnoCheck();" value="easydiscussvotes" /> <?php echo JText::_('EasyDiscuss Posts Votes'); ?></li>
     <?php 	}
			$target_ars = (JPATH_BASE .'/components/com_easyblog/easyblog.php');
			if (file_exists($target_ars)) {
	?>  
    <li><input type="radio" name="reset" onclick="javascript:yesnoCheck();" value="easybloghits" /> <?php echo JText::_('Easyblog Posts Hits'); ?></li>
    <?php } ?>
    </ul>
    <br/>
	<input class="btn btn-block btn-danger" type="submit" value="Reset Hits">
</form>
</div>
