<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');
// $close = '<span class="maxiclose">' . JText::_('MAXICLOSE') . '</span>';
$orientation_class = ( $params->get('orientation', 'horizontal') == 'vertical' ) ? 'maximenuckv' : 'maximenuckh';
$direction = $langdirection == 'rtl' ? 'right' : 'left';
$start = (int) $params->get('startLevel');
?>
<div class="<?php echo $orientation_class . ' ' . $langdirection ?>" id="<?php echo $params->get('menuid', 'maximenuck'); ?>" >
<ul class="menu<?php echo $params->get('moduleclass_sfx'); ?>"<?php
	$tag = '';
	if ($params->get('tag_id')!=NULL) {
		$tag = $params->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
<?php
if ($logoimage) {
	$logoheight = $logoheight ? ' height="' . $logoheight . '"' : '';
	$logowidth = $logowidth ? ' width="' . $logowidth . '"' : '';
	$logofloat = ($params->get('orientation', 'horizontal') == 'vertical') ? '' : 'float: ' . $params->get('logoposition', 'left') . ';';
	$styles = 'style="' .$logofloat . 'margin: '.$params->get('logomargintop','0').'px '.$params->get('logomarginright','0').'px '.$params->get('logomarginbottom','0').'px '.$params->get('logomarginleft','0').'px' . '"';
	$logolinkstart = $logolink  ? '<a href="'. JRoute::_($logolink).'" style="margin-bottom: 0 !important;margin-left: 0 !important;margin-right: 0 !important;margin-top: 0 !important;padding-bottom: 0 !important;padding-left: 0 !important;padding-right: 0 !important;padding-top: 0 !important;background: none !important;">' : '';
	$logolinkend = $logolink  ? '</a>' : '';
	?>
	<li class="maximenucklogo" style="margin-bottom: 0 !important;margin-left: 0 !important;margin-right: 0 !important;margin-top: 0 !important;">
		<?php echo $logolinkstart ?><img src="<?php echo $logoimage ?>" alt="<?php echo $params->get('logoalt','') ?>" <?php echo $logowidth.$logoheight.$styles ?> /><?php echo $logolinkend ?>
	</li>
<?php } ?>
<?php if ($params->get('maximenumobile_enable') === '1') {
	echo '<label for="' . $params->get('menuid', 'maximenuck') . '-maximenumobiletogglerck" class="maximenumobiletogglericonck" style="display:none;">&#x2261;</label>'
			. '<input id="' . $params->get('menuid', 'maximenuck') . '-maximenumobiletogglerck" class="maximenumobiletogglerck" type="checkbox" style="display:none;"/>';
} ?>
<?php
$zindex = 12000;

foreach ($items as $i => &$item) :
	$item->mobile_data = isset($item->mobile_data) ? $item->mobile_data : '';
	$itemlevel = ($start > 1) ? $item->level - $start + 1 : $item->level;
	// load a module
	if (isset($item->content) AND $item->content) {
		echo '<li data-level="' . $itemlevel . '" class="maximenuck maximenuckmodule' . $item->classe . ' level' . $item->level .' '.$item->liclass . '" ' . $item->mobile_data . '>' . $item->content;
		$item->ftitle = '';
	}
	if ($item->ftitle != "") {
		$title = $item->anchor_title ? ' title="'.$item->anchor_title.'"' : '';
		$description = $item->desc ? '<span class="descck">' . $item->desc . '</span>' : '';
		// manage HTML encapsulation
		$item->tagcoltitle = $item->params->get('maximenu_tagcoltitle', 'none');
		$classcoltitle = $item->params->get('maximenu_classcoltitle', '') ? ' class="'.$item->params->get('maximenu_classcoltitle', '').'"' : '';
		$opentag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '<'.$item->tagcoltitle.$classcoltitle.'>' : '';
		$closetag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '</'.$item->tagcoltitle.'>' : '';

		// manage image
		require dirname(__FILE__) . '/_image.php';

		if ($params->get('imageonly', '0') == '1')
			$item->ftitle = '';
		echo '<li data-level="' . $itemlevel . '" class="maximenuck ' . $item->classe . ' level' . $item->level .' '.$item->liclass . '" style="z-index : ' . $zindex . ';" ' . $item->mobile_data . '>';
		require dirname(__FILE__) . '/_itemtype.php';
	}

	// The next item is deeper.
	if ($item->deeper) {
		echo '<ul>';
	}
	// The next item is shallower.
	elseif ($item->shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul></div>
