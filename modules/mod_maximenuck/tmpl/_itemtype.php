<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
$access_key = (isset($item->access_key) && $item->access_key) ? ' accesskey="' . $item->access_key . '"' : '';
switch ($item->type) :
	default:
		echo $opentag . '<a' . $linkrollover . $access_key . ' ' . $datahover . ' class="maximenuck ' . $item->anchor_css . '" href="' . $item->flink . '"' . $title . $item->rel . '>' . $linktype . '</a>' . $closetag;
		break;
	case 'separator':
		echo $opentag . '<span' . $linkrollover . ' ' . $datahover . ' class="separator ' . $item->anchor_css . '">' . $linktype . '</span>' . $closetag;
		break;
	case 'heading':
		echo $opentag . '<span' . $linkrollover . ' ' . $datahover . ' class="nav-header ' . $item->anchor_css . '">' . $linktype . '</span>' . $closetag;
		break;
	case 'url':
	case 'component':
		if ($item->type == 'url' && $item->flink == '') {
			$item->flink = 'javascript:void(0);';
		}
		switch ($item->browserNav) :
			default:
			case 0:
				echo $opentag . '<a' . $linkrollover . $access_key . ' ' . $datahover . ' class="maximenuck ' . $item->anchor_css . '" href="' . $item->flink . '"' . $title . $item->rel . '>' . $linktype . '</a>' . $closetag;
				break;
			case 1:
				// _blank
				echo $opentag . '<a' . $linkrollover . $access_key . ' ' . $datahover . ' class="maximenuck ' . $item->anchor_css . '" href="' . $item->flink . '" target="_blank" ' . $title . $item->rel . '>' . $linktype . '</a>' . $closetag;
				break;
			case 2:
				// window.open
				echo $opentag . '<a' . $linkrollover . $access_key . ' ' . $datahover . ' class="maximenuck ' . $item->anchor_css . '" href="' . $item->flink . '" onclick="window.open(this.href,\'targetWindow\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\');return false;" ' . $title . $item->rel . '>' . $linktype . '</a>' . $closetag;
				break;
		endswitch;
		break;
endswitch;