<?php
/**
 * @package     CSVI
 * @subpackage  Replacement
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen');
$db = JFactory::getDbo();
?>
<form action="<?php echo JRoute::_('index.php?option=com_csvi&view=rule&id=' . $this->item->csvi_rule_id); ?>" method="post" name="adminForm"  id="adminForm" class="form-horizontal form-validate">
	<div class="row-fluid">
		<?php echo $this->form->renderFieldset('rule'); ?>

		<div id="pluginfields">
			<?php
				// Load the plugin helper
				$dispatcher = new RantaiPluginDispatcher;
				$dispatcher->importPlugins('csvirules', $db);
				$output = $dispatcher->trigger('getForm', array('id' => $this->item->plugin, $this->item->pluginform));

				// Output the form
				if (array_key_exists(0, $output))
				{
					echo $output[0];
				}
			?>
		</div>
	</div>
	<input type="hidden" name="csvi_rule_id" value="<?php echo $this->item->csvi_rule_id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="1" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<!-- The Linked Templates List -->
<?php if ($this->item->csvi_rule_id && $this->templates) : ?>
    <div class="modal hide fade" id="linkedTemplates" style="width: 30%; left: 70%;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&#215;</button>
            <h3><?php echo JText::_('COM_CSVI_LINKED_TEMPLATES'); ?></h3>
        </div>
        <div class="modal-body modal-batch" style="overflow-y: scroll; max-height: 400px;">
            <table class="order-table table table-striped table-hover">
                <?php foreach ($this->templates as $template) : ?>
                    <tr><td>
                        <?php
                            echo JHtml::_(
                                'link',
                                JRoute::_('index.php?option=com_csvi&view=templatefields&csvi_template_id=' . $template->template_id),
                                $template->template_name,
                                'target="_blank"'
                            );
                        ?>
                    </td></tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn" type="button" data-dismiss="modal">
                <?php echo JText::_('COM_CSVI_CLOSE_DIALOG'); ?>
            </button>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript">
	jQuery(document).ready(function ()
	{
		// Turn off the help texts
		jQuery('.help-block').hide();
	});

	Joomla.submitbutton = function(task)
	{
		if (task === 'templates')
		{
			jQuery('#linkedTemplates').modal('show');
		}
		else if (task === 'hidetips')
		{
			jQuery('.help-block').toggle();
			return false;
		}
		else {
			if (task === 'rule.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
				Joomla.submitform(task, document.getElementById('adminForm'));
			} else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
			}
		}
	}
</script>
