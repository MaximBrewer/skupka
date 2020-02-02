<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * [member=126442]copyright[/member]   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

// Create a shortcut for params.
$params = $displayData->params;
$canEdit = $displayData->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$myimage=$displayData->introtext;
preg_match_all('/<img[^>]+>/i', $myimage, $result);
if(!empty($timag)){
 $timage=str_replace("\"","",$timag);}
 else
{
  $timage= '/images/images/a_images_wablon/1.png';
}
$timage=JURI::base().$timage;
$size = getimagesize($timage);
?>

<?php if ($params->get('show_title') || $displayData->state == 0 || ($params->get('show_author') && !empty($displayData->author ))) : ?>
<div class="page-header">
<meta itemscope itemprop="mainEntityOfPage"  itemtype="https://schema.org/WebPage" itemid="<?php echo JRoute::_(
ContentHelperRoute::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)
); ?>"/>
<span style="display:none;" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
<img itemprop="url" src="<?php echo $timage;?>">
<meta itemprop="image" content="<?php echo $timage;?>">
<meta itemprop="width" content="<?php echo $size[0];?>">
<meta itemprop="height" content="<?php echo $size[1];?>">
</span>
<span itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
<meta itemprop="name" content="Скупка техники в СПБ - БУ  за деньги">
    <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
        <img itemprop="url" src="/images/images/a_images_wablon/1.png" style="display:none;"/>
        <meta itemprop="image" content="/images/images/a_images_wablon/1.png">
        <meta itemprop="width" content="220">
        <meta itemprop="height" content="93">
    </span>
</span>
<?php if ($params->get('show_title')) : ?>
<h2 itemprop="headline">
<?php if ($params->get('link_titles') && ($params->get('access-view') || $params->get('show_noauth', '0') == '1')) : ?>
<a href="<?php echo JRoute::_(
ContentHelperRoute::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)
); ?>" itemprop="url">
<?php echo $this->escape($displayData->title); ?>
</a>
<?php else : ?>
<?php echo $this->escape($displayData->title); ?>
<?php endif; ?>
</h2>
<?php endif; ?>

<?php if ($displayData->state == 0) : ?>
<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
<?php endif; ?>

<?php if (strtotime($displayData->publish_up) > strtotime(JFactory::getDate())) : ?>
<span class="label label-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></span>
<?php endif; ?>

<?php if ((strtotime($displayData->publish_down) < strtotime(JFactory::getDate()))
&& $displayData->publish_down != JFactory::getDbo()->getNullDate()) : ?>
<span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
<?php endif; ?>
</div>
<?php endif; ?>