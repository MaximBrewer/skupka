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

defined('JPATH_BASE') or die;

/**
 * Displays a search input box with a search and a clear button
 */

$formName = empty($displayData->customFormName) ? 'adminForm' : $displayData->customFormName;
?>
<div class="filter-search btn-group pull-left hidden-phone hidden-tablet">
		<label for="search_pageid" class="element-invisible"><?php echo JText::_('COM_SH404SEF_PAGE_ID'); ?></label>
		<input type="text" name="search_pageid" id="search_pageid" placeholder="<?php echo JText::_('COM_SH404SEF_PAGE_ID'); ?>" value="<?php echo $this
		->escape($displayData->search_pageid);?>" onchange="document.<?php echo $formName; ?>.limitstart.value=0;document.<?php echo $formName; ?>.submit();"/>
</div>
<div class="btn-group pull-left hidden-phone hidden-tablet">
		<button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
		<button class="btn tip hasTooltip" type="button" onclick="document.<?php echo $formName; ?>.search_pageid.value='';document.<?php echo $formName; ?>.submit();" title="<?php echo JText::_(
		'JSEARCH_FILTER_CLEAR');?>"><i class="icon-remove"></i></button>
</div>
