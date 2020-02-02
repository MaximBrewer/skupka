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
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');
?>
<div class="container-fluid">
	<?php

	//old url
	$data = new stdClass();
	$data->name = 'oldurl';
	$data->label = JText::_('COM_SH404SEF_OLDURL');
	$oldUrl = $this->url->get('oldurl');
	if ($this->noUrlEditing || ($this->canEditNewUrl && !empty($oldUrl)))
	{
		$data->input = $this->escape($oldUrl);
	}
	else
	{
		$data->input = '<input maxlength="' . sh404SEF_MAX_SEF_URL_LENGTH . '" type="text" name="oldurl" id="oldurl" size="90" value="'
			. $this->escape($oldUrl) . '" />';
	}
	$data->tip = $this->noUrlEditing || ($this->canEditNewUrl && !empty($oldUrl)) ? '' : JText::_('COM_SH404SEF_TT_OLDURL');
	echo $this->layoutRenderer['custom']->render($data);

	// new url
	$data = new stdClass();
	$data->name = 'newurl';
	$data->label = JText::_('COM_SH404SEF_NEWURL');
	$newUrl = $this->url->get('newurl');
	if (!$this->canEditNewUrl || $this->noUrlEditing)
	{
		$data->input = $this->escape($newUrl);
	}
	else
	{
		$data->input = '<input type="text" name="newurl" id="newurl" size="90" value="' . $this->escape($newUrl) . '" />';
	}
	$data->tip = !$this->canEditNewUrl || $this->noUrlEditing ? '' : JText::_('COM_SH404SEF_TT_NEWURL');
	echo $this->layoutRenderer['custom']->render($data);

	// canonical
	$data = new stdClass();
	$data->name = 'canonical';
	$data->label = JText::_('COM_SH404SEF_CANONICAL');
	if ($this->noUrlEditing)
	{
		$data->input = $this->escape($this->meta->canonical);
	}
	else
	{
		$data->input = '<input type="text" name="canonical" id="canonical" size="90" value="' . $this->escape($this->meta->canonical)
			. '" />';
	}
	$data->tip = $this->noUrlEditing ? '' : JText::_('COM_SH404SEF_TT_CANONICAL');
	echo $this->layoutRenderer['custom']->render($data);

	// shurl
	$data = new stdClass();
	$data->name = 'shurl';
	$data->label = JText::_('COM_SH404SEF_PAGE_ID');
	$data->input = ShlHtmlBs_Helper::badge($this->escape($this->pageid->pageid), 'info');
	$data->tip = '';
	echo $this->layoutRenderer['custom']->render($data);

	// QR code
	$data = new stdClass();
	$data->name = 'qrcode';
	$data->label = 'QR code';
	$data->input = '<img src="https://zxing.org/w/chart?chs=130x130&cht=qr&chld=L&choe=UTF-8&chl=' . urlencode($this->qrCodeUrl)
		. '" alt="QR code" width="130" height="130">';
	$data->tip = '';
	echo $this->layoutRenderer['custom']->render($data);
	?>
</div>
