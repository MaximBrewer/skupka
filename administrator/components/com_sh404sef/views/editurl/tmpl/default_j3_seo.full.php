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
<div class="container-fluid">
	<?php
	// attach an input counter to the title input boxes
	echo ShlHtmlBs_Helper::renderInputCounter( 'metatitle', Sh404sefFactory::getPConfig()->metaDataSpecs['metatitle-one-line']);
	echo ShlHtmlBs_Helper::renderInputCounter( 'metadesc', Sh404sefFactory::getPConfig()->metaDataSpecs['metadesc']);

	// metatitle
	$data = new stdClass();
	$data->name = 'metatitle';
	$data->label = JText::_('COM_SH404SEF_META_TITLE');
	$data->input = '<input type="text" name="metatitle" id="metatitle" size="90" value="'
						. $this->escape($this->meta->metatitle) . '" />';
	$data->tip = JText::_('COM_SH404SEF_TT_META_TITLE');
	echo $this->layoutRenderer['custom']->render($data);

	// metadesc
	$data = new stdClass();
	$data->name = 'metadesc';
	$data->label = JText::_('COM_SH404SEF_META_DESC');
	$data->input = '<textarea name="metadesc" id="metadesc" cols="51" rows="5">' . $this->escape($this->meta->metadesc) . '</textarea>';
	$data->tip = JText::_('COM_SH404SEF_TT_META_DESC');
	echo $this->layoutRenderer['custom']->render($data);

	// canonical
	if ($this->home)
	{
		$data = new stdClass();
		$data->name = 'canonical';
		$data->label = JText::_('COM_SH404SEF_CANONICAL');
		$data->input = '<input type="text" name="canonical" id="canonical" size="90" value="' . $this->escape($this->meta->canonical)
			. '" />';
		$data->tip = JText::_('COM_SH404SEF_TT_CANONICAL');
		echo $this->layoutRenderer['custom']->render($data);
	}

	// metakey
	$data = new stdClass();
	$data->name = 'metakey';
	$data->label = JText::_('COM_SH404SEF_META_KEYWORDS');
	$data->input = '<textarea name="metakey" id="metakey" cols="51" rows="3">' . $this->escape($this->meta->metakey) . '</textarea>';
	$data->tip = JText::_('COM_SH404SEF_TT_META_KEYWORDS');
	echo $this->layoutRenderer['custom']->render($data);

	// metarobots
	$data = new stdClass();
	$data->name = 'metarobots';
	$data->label = JText::_('COM_SH404SEF_META_ROBOTS');
	$data->input = '<input type="text" name="metarobots" id="metarobots" size="90" value="' . $this->escape($this->meta->metarobots) . '" />';
	$data->tip = JText::_('COM_SH404SEF_TT_META_ROBOTS');
	echo $this->layoutRenderer['custom']->render($data);

	// metalang
	$data = new stdClass();
	$data->name = 'metalang';
	$data->label = JText::_('COM_SH404SEF_META_LANG');
	$data->input = '<input type="text" name="metalang" id="metalang" size="90" value="'
					. $this->escape($this->meta->metalang) . '" />';
	$data->tip = JText::_('COM_SH404SEF_TT_META_LANG');
	echo $this->layoutRenderer['custom']->render($data);

?>
</div>
