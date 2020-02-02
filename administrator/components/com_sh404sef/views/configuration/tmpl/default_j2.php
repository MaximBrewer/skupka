<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date  2016-03-15
 */

// No direct access
defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (document.formvalidator.isValid(document.id('component-form'))) {
			Joomla.submitform(task, document.getElementById('component-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php'); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate">
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="Joomla.submitform('saveconfiguration', this.form);">
				<?php echo JText::_('JSAVE'); ?></button>
			<button type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;'
	: '';
										   ?>  window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL'); ?></button>
		</div>
		<div class="configuration" >
			<?php echo JText::_('COM_SH404SEF_CONFIGURATION') ?>
		</div>
	</fieldset>

	<?php
echo JHtml::_('tabs.start', 'config-tabs-com_sh404sef_configuration', array('useCookie' => 1));
$fieldSets = $this->form->getFieldsets();

//Let's group the fieldsets by the group attribute
$groupnames = array();
foreach ($fieldSets as $fieldSet)
{
	$groupnames[$fieldSet->groupname][] = $fieldSet;
}

//fieldsets are now grouped
foreach ($groupnames as $key => $groupname)
{
	echo JHtml::_('tabs.panel', strip_tags(JText::_($key)), 'publishing-details big');
	echo JHtml::_('tabs.start', 'config-subtabs-com_sh404sef_configuration', array('useCookie' => 1));
	foreach ($groupname as $name => $fieldSet) :
		$label = empty($fieldSet->label) ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL' : $fieldSet->label;
		echo JHtml::_('tabs.panel', JText::_($label), 'publishing-details');
		if (isset($fieldSet->description) && !empty($fieldSet->description)) :
			echo '<p class="tab-description">' . JText::_($fieldSet->description) . '</p>';
		endif;
	?>
    <ul class="config-option-list <?php echo ($label == 'COM_SH404SEF_CONF_TAB_BY_COMPONENT') ? 'listBycomponent' : ''; ?>">
    <?php
		$i = 0;
		$x = 0;
		foreach ($this->form->getFieldset($fieldSet->name) as $field) :
			if ($label == 'COM_SH404SEF_CONF_TAB_BY_COMPONENT')
			{
				$class = 'class="listsrow' . $x . '';
				$class .= ($x % 2 == 0) ? ' listsrowodd' : ' listsroweven';
				$class .= '"';
			}
			else if ($label == 'COM_SH404SEF_DEF_404_PAGE')
			{
				$class = 'class="errorpage errorpage' . $i . '"';
			}
			else if ($label == 'COM_SH404SEF_CONF_TAB_ADVANCED')
			{
				$class = 'class="advancedtab"';
			}
			else
			{
				$class = 'class="' . (($x % 2 == 0) ? ' listsrowodd' : ' listsroweven') . '"';
			}

			echo '<li ' . $class . '>';
			if (!$field->hidden)
			{
				// on by-component page, output actual tooltips
				if ($label == 'COM_SH404SEF_CONF_TAB_BY_COMPONENT' && strtolower($field->type) == 'spacer')
				{
					echo JHTML::tooltip(JText::_($field->description), strip_tags($field->label));
				}

				// continue displaying field
				echo $field->label;
			}
			echo $field->input;
			$element = $field->element;
			if (!empty($element['additionaltext']))
			{
				echo '<span class = "sh404sef-additionaltext">' . (string) $element['additionaltext'] . '</span>';
			}
			echo '<div class="clr"></div>';
			echo '</li>';
			$i++;
			if ($i % ($this->byComponentItemsCount - 1) == 0 and $i > 0)
			{
				$x++;
			}
		endforeach;
	?>
    </ul>
	<div class="clr"></div>
    <?php endforeach; ?>
	<?php echo JHtml::_('tabs.end'); ?>
	<div class="clr"></div>
	<?php
}
echo JHtml::_('tabs.end');
	?>
	<div>
		<input type="hidden" name="option" value="com_sh404sef" />
		<input type="hidden" name="component" value="com_sh404sef" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="view" value="configuration" />
		<input type="hidden" name="c" value="configuration" />
		<input type="hidden" name="task" value="saveconfiguration" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
