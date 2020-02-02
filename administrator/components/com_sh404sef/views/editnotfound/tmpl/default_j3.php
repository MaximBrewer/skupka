<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

?>
<div id="sh404sef-popup" class="sh404sef-popup">

	<div class="shmodal-toolbar row-fluid wbl-theme-default" id="shmodal-toolbar">
		<div class="shmodal-toolbar-wrapper">
			<div class="shmodal-toolbar-text">
				<?php
				$title = $this->escape(Sh404sefHelperHtml::abridge($this->url->oldurl, 'editurl'));
				echo JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_FOR') . ' ' . ShlHtmlBs_Helper::label($title, 'info', $dismiss = false, 'label-large') ;
				?>
			</div>
			<div class="shmodal-toolbar-buttons" id="shmodal-toolbar-buttons">
				<button class="btn btn-primary" type="button" onclick="Joomla.submitform('save', document.adminForm);">
					<i class="icon-apply icon-white"> </i>
					<?php echo JText::_('JSAVE'); ?>
				</button>
				<button class="btn" type="button" onclick="<?php echo JRequest::getBool('refresh', 0)
					? 'window.parent.location.href=window.parent.location.href;' : '';
				?>  window.parent.shlBootstrap.closeModal();">
					<?php echo JText::_('JCANCEL'); ?>
				</button>
			</div>
		</div>
	</div>

	<div class="shmodal-content wbl-theme-default" id="shmodal-content">

		<?php
		echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $this);
		?>

		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

			<div id="editurl-container" class="row-fluid">

				<?php
				$data = new stdClass();
				$data->label = JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_LABEL');
				$data->input = '<input class="text_area" type="text" name="newurl" id="newurl" size="120" value="' . $this->escape($this->url->get('newurl')) . '" />';
				$data->tip = JText::_('COM_SH404SEF_TT_ENTER_REDIRECT');
				$data->name = "newurl";
				echo ShlMvcLayout_Helper::render('com_sh404sef.form.fields.custom', $data);
				?>

				<div>
					<input type="hidden" name="id" value="<?php echo $this->url->get('id'); ?>"/>
					<input type="hidden" name="c" value="editnotfound"/>
					<input type="hidden" name="view" value="editnotfound"/>
					<input type="hidden" name="option" value="com_sh404sef"/>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="tmpl" value="component"/>
					<?php echo JHTML::_('form.token'); ?>
				</div>
			</div>
		</form>
	</div>
</div>
