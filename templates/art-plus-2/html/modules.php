<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

function modChrome_moduletable($module, &$params, &$attribs)
{
	$headerLevel = isset($attribs['headerLevel'])? (int) $attribs['headerLevel'] : 3;
	if (!empty ($module->content)) : ?>
		<div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
			<?php if ($module->showtitle) : ?>
				<div class="moduletabletitle"><h3><?php echo $module->title; ?></h3></div>
			<?php endif; ?>
			<?php echo $module->content; ?>
		</div>
	<?php endif;
}