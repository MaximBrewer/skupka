<?php
/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
// no direct access

defined('_JEXEC') or die('Restricted access');

if ($logoimage) {
	$logoheight = $logoheight ? ' height="' . $logoheight . '"' : '';
	$logowidth = $logowidth ? ' width="' . $logowidth . '"' : '';
	$logofloat = ($params->get('orientation', 'horizontal') == 'vertical') ? '' : 'float: ' . $params->get('logoposition', 'left') . ';';
	$styles = ' style="' . $logofloat . 'margin: ' . $params->get('logomargintop', '0') . 'px ' . $params->get('logomarginright', '0') . 'px ' . $params->get('logomarginbottom', '0') . 'px ' . $params->get('logomarginleft', '0') . 'px' . '"';
	$logolinkstart = $logolink ? '<a href="' . JRoute::_($logolink) . '" style="margin-bottom: 0 !important;margin-left: 0 !important;margin-right: 0 !important;margin-top: 0 !important;padding-bottom: 0 !important;padding-left: 0 !important;padding-right: 0 !important;padding-top: 0 !important;background: none !important;">' : '';
	$logolinkend = $logolink ? '</a>' : '';
	?>
	<li class="maximenucklogo" style="margin-bottom: 0 !important;margin-left: 0 !important;margin-right: 0 !important;margin-top: 0 !important;">
		<?php echo $logolinkstart ?><img src="<?php echo $logoimage ?>" alt="<?php echo $params->get('logoalt', '') ?>" <?php echo $logowidth . $logoheight . $styles ?> /><?php echo $logolinkend ?>
	</li>
<?php }