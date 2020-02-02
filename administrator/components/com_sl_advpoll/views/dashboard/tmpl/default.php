<?php
/**
 * @version		$Id$
 * @author		Pham Minh Tuan (admin@extstore.com) (admin@extstore.com)
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

$xml	= simplexml_load_file(JPATH_ROOT . '/administrator/components/com_sl_advpoll/sl_advpoll.xml');
?>

<?php if(!empty( $this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
	<?php //var_dump($this->sidebar); exit; ?>
	<?php echo $this->sidebar; ?>
</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<div class="adminform">
	<div class="span6">
		<div class="well well-small">
			<div class="module-title nav-header">
				<?php echo JText::_('COM_SL_ADVPOLL_SUBMENU_POLLS_DASHBOARD'); ?>
			</div>

			<div class="row-striped">
				<div id="cpanel">
					<?php
					$this->_quickIcon('index.php?option=com_sl_advpoll&view=polls', 'icon-64-polls.png', 'COM_SL_ADVPOLL_SUBMENU_POLLS');
					$this->_quickIcon('index.php?option=com_sl_advpoll&view=poll&layout=edit', 'icon-64-poll-add.png', 'COM_SL_ADVPOLL_SUBMENU_POLL_ADD');
					$this->_quickIcon('index.php?option=com_categories&extension=com_sl_advpoll', 'icon-64-categories.png', 'COM_SL_ADVPOLL_SUBMENU_CATEGORIES');
					$this->_quickIcon('index.php?option=com_config&view=component&component=com_sl_advpoll&return=' . urlencode(base64_encode(JUri::getInstance())), 'icon-64-config.png', 'COM_SL_ADVPOLL_SUBMENU_CONFIG');
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<?php echo $xml->description; ?>
	</div>

	<div class="span6">

		<?php echo JHtml::_('bootstrap.startAccordion', 'menuOptions', array('active' => 'collapse0')); ?>
		<?php
		echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_('COM_SL_POLL_DASHBOARD_LATEST_POLLS'), 'latest');
		echo $this->loadTemplate('latest');
		echo JHtml::_('bootstrap.endSlide');
		?>

		<?php
		echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_('COM_SL_POLL_DASHBOARD_LAST_VOTED_POLLS'), 'lastvoted');
		echo $this->loadTemplate('lastvoted');
		echo JHtml::_('bootstrap.endSlide');
		?>

		<?php
		echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_('COM_SL_POLL_DASHBOARD_TOP_VOTED_POLLS'), 'topvoted');
		echo $this->loadTemplate('topvoted');
		echo JHtml::_('bootstrap.endSlide');
		?>

		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</div>
</div>

<?php
echo SL_AdvPollFactory::getFooter();