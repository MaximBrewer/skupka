<?php
/**
* @version $Id: link.php 2008-02-20 Ryan Demmer $
* @package JCE
* @copyright Copyright (C) 2006-2007 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined('WF_EDITOR') or die( 'RESTRICTED' );

$tabs = WFTabs::getInstance();

?>
<form action="#">
	<!-- Render Tabs -->
	<?php $tabs->render();?>
	<!-- Token -->
	<input type="hidden" id="token" name="<?php echo WFToken::getToken();?>" value="1" />
	<input type="hidden" id="onclick" value="" />
	<input type="hidden" id="onmouseover" value="" />
</form>
<div class="actionPanel">
	<button class="button" id="insert"><?php echo WFText::_('WF_LABEL_INSERT')?></button>
	<button class="button" id="help"><?php echo WFText::_('WF_LABEL_HELP')?></button>
	<button class="button" id="cancel"><?php echo WFText::_('WF_LABEL_CANCEL')?></button>
</div>