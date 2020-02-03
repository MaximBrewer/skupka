<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access

defined('_JEXEC') or die('Restricted access');

$linkrollover = '';
$itemicon = '';
// manage icon
if ($item->params->get('maximenu_icon', '')) {
	$loadfontawesome = true;
	$itemicon = '<span class="maximenuiconck ' . $item->params->get('maximenu_icon', '') . '"></span>';
}
$datahover = $params->get('datahover', '1') == '1' ? ' data-hover="' . addslashes($item->ftitle) . '"' : '';
// manage image
if ($item->menu_image) {
	// manage image rollover
	$menu_image_split = explode('.', $item->menu_image);

	if (isset($menu_image_split[1])) {
		// manage active image
		if (isset($item->active) AND $item->active) {
			$menu_image_active = $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . '.' . $menu_image_split[1];
			if (JFile::exists(JPATH_ROOT . '/' . $menu_image_active)) {
				$item->menu_image = $menu_image_active;
			}
		}
		// manage hover image
		$menu_image_hover = $menu_image_split[0] . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1];
		if (isset($item->active) AND $item->active AND JFile::exists(JPATH_ROOT . '/' . $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1])) {
			$linkrollover = ' onmouseover="javascript:this.querySelector(\'img\').src=\'' . JURI::base(true) . '/' . $menu_image_split[0] . $params->get('imageactiveprefix', '_active') . $params->get('imagerollprefix', '_hover') . '.' . $menu_image_split[1] . '\'" onmouseout="javascript:this.querySelector(\'img\').src=\'' . JURI::base(true) . '/' . $item->menu_image . '\'"';
		} else if (JFile::exists(JPATH_ROOT . '/' . $menu_image_hover)) {
			$linkrollover = ' onmouseover="javascript:this.querySelector(\'img\').src=\'' . JURI::base(true) . '/' . $menu_image_hover . '\'" onmouseout="javascript:this.querySelector(\'img\').src=\'' . JURI::base(true) . '/' . $item->menu_image . '\'"';
		}
	}

	$imagesalign = ($item->params->get('maximenu_images_align', 'moduledefault') != 'moduledefault') ? $item->params->get('maximenu_images_align', 'top') : $params->get('menu_images_align', 'top');
	$image_dimensions = ( $item->params->get('maximenuparams_imgwidth', '') != '' && ($item->params->get('maximenuparams_imgheight', '') != '') ) ? ' width="' . $item->params->get('maximenuparams_imgwidth', '') . '" height="' . $item->params->get('maximenuparams_imgheight', '') . '"' : '';

	if ($item->params->get('menu_text', 1) AND !$params->get('imageonly', '0')) {
		switch ($imagesalign) :
			default:
			case 'default':
				$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" align="left"' . $image_dimensions . '/><span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span> ';
				break;
			case 'bottom':
				$linktype = '<span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span><img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" style="display: block; margin: 0 auto;"' . $image_dimensions . ' /> ';
				break;
			case 'top':
				$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" style="display: block; margin: 0 auto;"' . $image_dimensions . ' /><span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span> ';
				break;
			case 'rightbottom':
				$linktype = '<span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span><img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" align="top"' . $image_dimensions . '/> ';
				break;
			case 'rightmiddle':
				$linktype = '<span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span><img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" align="middle"' . $image_dimensions . '/> ';
				break;
			case 'righttop':
				$linktype = '<span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span><img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" align="bottom"' . $image_dimensions . '/> ';
				break;
			case 'leftbottom':
				$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" align="top"' . $image_dimensions . '/><span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span> ';
				break;
			case 'leftmiddle':
				$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" align="middle"' . $image_dimensions . '/><span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span> ';
				break;
			case 'lefttop':
				$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->ftitle . '" align="bottom"' . $image_dimensions . '/><span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span> ';
				break;
		endswitch;
	} else {
		$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->ftitle . '"' . $image_dimensions . '/>';
	}
} else {
	$linktype = '<span class="titreck" ' . $datahover . '>' . $itemicon . $item->ftitle . $description . '</span>';
}
