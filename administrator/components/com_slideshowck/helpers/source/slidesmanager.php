<?php
/**
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_slideshowck/helpers/helper.php';

/**
 * Helper Class.
 */
class SlideshowckHelpersourceSlidesmanager {

	private static $params;

	/*
	 * Get the items from the source
	 */
	public static function getItems($params, $folder = null) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		// load the items from the module settings
		$items = json_decode(str_replace("|qq|", "\"", $params->get('slides')));
		foreach ($items as $i => $item) {
			if (!$item->imgname) {
				unset($items[$i]);
				continue;
			}

			// check if the slide is published
			if (isset($item->state) && $item->state == '0') {
				unset($items[$i]);
				continue;
			}

			// check the slide start date
			if (isset($item->startdate) && $item->startdate) {
				// if (date("d M Y") < $item->startdate) {
				if (time() < strtotime($item->startdate)) {
					unset($items[$i]);
					continue;
				}
			}

			// check the slide end date
			if (isset($item->enddate) && $item->enddate) {
				// if (date("d M Y") > $item->enddate) {
				if (time() > strtotime($item->enddate)) {
					unset($items[$i]);
					continue;
				}
			}

			if (stristr($item->imgname, "http")) {
				$item->imgthumb = $item->imgname;
			} else {
				// renomme le fichier
				$thumbext = explode(".", $item->imgname);
				$thumbext = end($thumbext);
				// crée la miniature
				if ($params->get('thumbnails', '1') == '1' && $params->get('autocreatethumbs','1')) {
					if ($params->get('autocreatethumbs','1'))
						$item->imgthumb = JURI::base(true) . '/' . SlideshowckHelper::resizeImage($item->imgname, $params->get('thumbnailwidth', '182'), $params->get('thumbnailheight', '187'));
				} else {
					$thumbfile = str_replace(basename($item->imgname), "th/" . basename($item->imgname), $item->imgname);
					$thumbfile = str_replace("." . $thumbext, "_th." . $thumbext, $thumbfile);
					$item->imgthumb = JURI::base(true) . '/' . $thumbfile;
				}
				$item->imgname = JURI::base(true) . '/' . $item->imgname;
			}

			// set the videolink
			if ($item->imgvideo)
				$item->imgvideo = SlideshowckHelper::setVideolink($item->imgvideo);

			// for B/C
			if (! isset($item->texttype)) {
				$item->texttype = (isset($item->slidearticleid) && $item->slidearticleid) ? 'article' : 'custom';
			}
			if ($item->texttype == 'article') {
				if (isset($item->slidearticleid) && $item->slidearticleid) {
					$item = SlideshowckHelper::getArticle($item);
				} else {
					$item->link = '';
					$item->title = '';
					$item->text = '';
				}
			} else {
				// manage the title and description - LEGACY
				if (stristr($item->imgcaption, "||")) {
					$splitcaption = explode("||", $item->imgcaption);
					$item->imgtitle = $splitcaption[0];
					$item->imgcaption = $splitcaption[1];
				}

				// route the url
				if (strcasecmp(substr($item->imglink, 0, 4), 'http') && (strpos($item->imglink, 'index.php?') !== false)) {
					$item->imglink = JRoute::_($item->imglink, true, false);
				} else {
					$item->imglink = JRoute::_($item->imglink);
				}

				if (! isset($item->imgtitle)) $item->imgtitle = '';

				// convert legacy to new standard
				$item->link = $item->imglink;
				$item->title = $item->imgtitle;
				$item->text = $item->imgcaption;
			}
			// convert legacy to new standard
			$item->image = $item->imgname;
			$item->time = $item->imgtime;
			$item->target = $item->imgtarget;
			$item->alignment = $item->imgalignment;
			$item->video = $item->imgvideo;
		}

		return $items;
	}
}
