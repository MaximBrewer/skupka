<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<table class="adminlist">
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3">OpenGraph</th>
    </tr>
  </thead>
  <?php
  
  $x = 1;
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_DATA_ENABLED_BY_URL'),
  JText::_('COM_SH404SEF_TT_OG_DATA_ENABLED_BY_URL'),
  $this->ogData['og_enable'] );
  
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_REQUIRED_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_TYPE_SELECT'),
  JText::_('COM_SH404SEF_TT_OG_TYPE_SELECT'),
  $this->ogData['og_type'] );
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_IMAGE_PATH'),
  JText::_('COM_SH404SEF_TT_OG_IMAGE_PATH'),
                'og_image',
  $this->ogData['og_image'], 50, 255 );
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_OPTIONAL_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_DESCRIPTION'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_DESCRIPTION'),
  $this->ogData['og_enable_description'] );
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_SITE_NAME'),
  $this->ogData['og_enable_site_name'] );
  
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_SITE_NAME'),
                'og_site_name',
  $this->ogData['og_site_name'], 50, 255 );
  
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_ENABLE_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_OG_ENABLE_FB_ADMIN_IDS'),
  $this->ogData['og_enable_fb_admin_ids']);
  
  echo Sh404sefHelperView::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_FB_ADMIN_IDS'),
                'fb_admin_ids',
  $this->ogData['fb_admin_ids'], 50, 255 );
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_TWITTER_CARDS_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  echo '<tr><td colspan="3">' . JText::_('COM_SH404SEF_SOCIAL_TWITTER_CARDS_USE_OG_IMAGE') . '</td></tr>';
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  		JText::_('COM_SH404SEF_SOCIAL_ENABLE_TWITTER_CARDS'),
  		JText::_('COM_SH404SEF_SOCIAL_ENABLE_TWITTER_CARDS_DESC_PER_URL'),
  		$this->twCardsData['twittercards_enable'] );
  echo Sh404sefHelperView::shTextParamHTML( $x,
		JText::_('COM_SH404SEF_SOCIAL_TWITTER_CARDS_SITE_ACCOUNT'),
		JText::_('COM_SH404SEF_SOCIAL_TWITTER_CARDS_SITE_ACCOUNT_DESC'),
		'twittercards_site_account',
		$this->twCardsData['twittercards_site_account'], 50, 100 );
  echo Sh404sefHelperView::shTextParamHTML( $x,
		JText::_('COM_SH404SEF_SOCIAL_TWITTER_CARDS_CREATOR_ACCOUNT'),
		JText::_('COM_SH404SEF_SOCIAL_TWITTER_CARDS_CREATOR_ACCOUNT_DESC'),
		'twittercards_creator_account',
		$this->twCardsData['twittercards_creator_account'], 50, 100 );

  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_GOOGLE_AUTHORSHIP_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  echo Sh404sefHelperView::shYesNoParamHTML( $x,
  		JText::_('COM_SH404SEF_SOCIAL_ENABLE_GOOGLE_AUTHORSHIP'),
  		JText::_('COM_SH404SEF_SOCIAL_ENABLE_GOOGLE_AUTHORSHIP_DESC'),
  		$this->googleAuthorshipData['google_authorship_enable'] );
  echo Sh404sefHelperView::shTextParamHTML( $x,
		JText::_('COM_SH404SEF_GOOGLE_AUTHORSHIP_PROFILE'),
		JText::_('COM_SH404SEF_GOOGLE_AUTHORSHIP_PROFILE_DESC'),
		'google_authorship_author_profile',
		$this->googleAuthorshipData['google_authorship_author_profile'], 50, 100 );
  echo Sh404sefHelperView::shTextParamHTML( $x,
		JText::_('COM_SH404SEF_GOOGLE_AUTHORSHIP_AUTHOR_NAME'),
		JText::_('COM_SH404SEF_GOOGLE_AUTHORSHIP_AUTHOR_NAME_DESC'),
		'google_authorship_author_name',
		$this->googleAuthorshipData['google_authorship_author_name'], 50, 100 );

  echo Sh404sefHelperView::shYesNoParamHTML( $x,
		JText::_('COM_SH404SEF_SOCIAL_ENABLE_GOOGLE_PUBLISHER'),
		JText::_('COM_SH404SEF_SOCIAL_ENABLE_GOOGLE_PUBLISHER'),
		$this->googlePublisherData['google_publisher_enable'] );
  echo Sh404sefHelperView::shTextParamHTML( $x,
		JText::_('COM_SH404SEF_GOOGLE_PUBLISHER_URL'),
		JText::_('COM_SH404SEF_GOOGLE_PUBLISHER_URL_DESC'),
		'google_publisher_url',
		$this->googlePublisherData['google_publisher_url'], 40, 255 );
?>
</table>
