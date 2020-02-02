<?php
/**
 * @package     CSVI
 * @subpackage  Template
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

if ($this->action === 'export')
{
	if (isset($this->forms->form))
	{
		echo $this->forms->form;
	}

	?>
	<div class="pull-left">
		<?php echo JHtml::_(
			'link',
			'index.php?option=com_csvi&view=templatefields&csvi_template_id=' . $this->item->csvi_template_id,
			JText::_('COM_CSVI_EDIT_TEMPLATEFIELDS'),
			'class="btn btn-primary" target="_new"');
		?>
	</div>
	<?php
}
else
{
	if (!$this->item->options->get('use_column_headers'))
	{
		?>
		<div class="step_info">
			<?php echo JText::_('COM_CSVI_WIZARD_IMPORT_FIELDS_NEEDED'); ?>
		</div>

		<div class="pull-left">
			<?php echo JHtml::_(
				'link',
				'index.php?option=com_csvi&view=templatefields&csvi_template_id=' . $this->item->csvi_template_id,
				JText::_('COM_CSVI_EDIT_TEMPLATEFIELDS'),
				'class="btn btn-primary" target="_new"');
		?>
		</div>
		<br/>
		<br/>
		<hr>
		<?php echo JText::_('COM_CSVI_WIZARD_MAP_FIELDS');

		// Do not show for CSVI because we don't know which table the import is for
		if ($this->component === 'com_csvi')
		{
			return;
		}

		?>
            <div class="fiedlHeaders">
                <?php echo $this->forms->form;?>
                <div id="dropzoneField">
                    <div class="dz-message">
		                <div class="well"><?php echo JText::_('COM_CSVI_WIZARD_DRAG_N_DROP'); ?></div>
                        <div class="btn btn-primary"><?php echo JText::_('COM_CSVI_WIZARD_UPLOAD'); ?></div>
                    </div>
                </div>
            </div>

        <div class="clearfix"></div>
		<div class="fallback">
		</div>
		<input type="hidden" name="csvi_template_id" id="csvi_template_id" value="<?php echo $this->item->csvi_template_id; ?>">
		<div>
			<input type="button" name="readfields" value="<?php echo JText::_('COM_CSVI_EDIT_READFIELDS'); ?>" onclick="Csvi.readTemplateFields();">
		</div>
		<div>&nbsp;</div>
		<div id="mapTemplateFields"></div>
		<input type="hidden" name="mappedfields" id="mappedfields" value="">
		<?php
	}
	else
	{
		echo JText::_('COM_CSVI_WIZARD_IMPORT_FIELDS_NOT_NEEDED');
	}
	?>
<?php
}
