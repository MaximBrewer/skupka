<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date  2016-03-15
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html.bootstrap');
JHtml::_('formbehavior.chosen', 'select');

?>

<div class="shmodal-toolbar row-fluid wbl-theme-default" id="shmodal-toolbar">
	<div class="shmodal-toolbar-wrapper">
		<div class="shmodal-toolbar-text">
			<?php
			echo ShlHtmlBs_Helper::label(Sh404sefHelperHtml::abridge(JText::_('COM_SH404SEF_TITLE_CONFIG'), 'configuration'), 'info', $dismiss = false, 'label-large');
			?>
		</div>

		<div class="shmodal-toolbar-buttons" id="shmodal-toolbar-buttons">
			<button class="btn btn-primary" type="button"
			        onclick="Joomla.submitform('saveconfiguration', document.adminForm);">
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

	<form action="<?php echo JRoute::_('index.php'); ?>" id="adminForm" method="post" name="adminForm"
	      autocomplete="off" class="form-validate form-horizontal">

		<div class="row-fluid">
			<?php
			$fieldSets = $this->form->getFieldsets();

			// let's group the fieldsets by the group attribute
			$groupnames = array();
			$joomlaVersion = JVERSION;
			foreach ($fieldSets as $fieldSet)
			{
				if (!empty($fieldSet->minJoomlaVersion))
				{
					if (version_compare($joomlaVersion, $fieldSet->minJoomlaVersion, '<'))
					{
						continue;
					}
				}
				if (!empty($fieldSet->maxJoomlaVersion))
				{
					if (version_compare($joomlaVersion, $fieldSet->maxJoomlaVersion, 'ge'))
					{
						continue;
					}
				}
				$groupnames[$fieldSet->groupname][] = $fieldSet;
			}

			echo '<ul class="nav nav-tabs" id="config-tabs">';
			$active = true;
			foreach ($groupnames as $groupKey => $group)
			{
				echo '<li' . ($active ? ' class="active"' : '') . '><a data-toggle="tab" href="#' . $groupKey . '">' . JText::_($groupKey) . '</a></li>';
				if ($active)
				{
					$activePanelId = $groupKey;
					$active = false;
				}
			}
			echo '</ul>';

			echo JHtml::_('bootstrap.startPane', 'config-tabs', array('active' => $activePanelId));

			// Iterate over a param group
			foreach ($groupnames as $groupKey => $group)
			{
				echo JHtml::_('bootstrap.addPanel', 'config-tabs', $groupKey);

				$hasSubTabs = count($group) > 1;
				if ($hasSubTabs)
				{
					// echo tabs for subgroups, inside sub tabs
					echo '<ul class="nav nav-pills" id="config-sub-tabs-' . $groupKey . '-list">';
					$active = true;
					foreach ($group as $name => $fieldSet)
					{
						$panelId = empty($fieldSet->label) ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL' : $fieldSet->label;
						echo '<li' . ($active ? ' class="active"' : '') . '><a data-toggle="tab" href="#' . str_replace(' ', '_', $panelId) . '">' . JText::_($panelId) . '</a></li>';
						if ($active)
						{
							$activePanelId = $panelId;
							$active = false;
						}
					}
					echo '</ul>';

					// start sub tab
					echo JHtml::_('bootstrap.startPane', 'config-sub-tabs-' . $groupKey, array('active' => $activePanelId));
				}

				// output fields in the sub tab
				foreach ($group as $name => $fieldSet)
				{
					if (!empty($fieldSet->minJoomlaVersion))
					{
						if (version_compare($joomlaVersion, $fieldSet->minJoomlaVersion, '<'))
						{
							continue;
						}
					}
					if (!empty($fieldSet->maxJoomlaVersion))
					{
						if (version_compare($joomlaVersion, $fieldSet->maxJoomlaVersion, 'ge'))
						{
							continue;
						}
					}

					// store curent name and fiedset, so they can be accessed by sub-layouts
					$this->currentName = $name;
					$this->currentFieldset = $fieldSet;

					$panelId = empty($fieldSet->label) ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL' : $fieldSet->label;

					if ($hasSubTabs)
					{
						echo JHtml::_('bootstrap.addPanel', 'config-sub-tabs-' . $groupKey, str_replace(' ', '_', $panelId));
					}

					if (isset($fieldSet->description) && !empty($fieldSet->description))
					{
						echo '<div class="alert">' . JText::_($fieldSet->description) . '</div>';
					}

					switch ($panelId)
					{
						case 'JCONFIG_PERMISSIONS_LABEL':
							echo $this->loadTemplate($this->joomlaVersionPrefix . '_permissions');
							break;
						case 'COM_SH404SEF_CONF_TAB_BY_COMPONENT':
							echo $this->loadTemplate($this->joomlaVersionPrefix . '_by_component');
							break;
						default:
							echo $this->loadTemplate($this->joomlaVersionPrefix . '_default');
							break;
					}
					if ($hasSubTabs)
					{
						echo JHtml::_('bootstrap.endPanel');
					}
				}

				if ($hasSubTabs)
				{
					echo JHtml::_('bootstrap.endPane');
				}

				echo JHtml::_('bootstrap.endPanel');
			}
			echo JHtml::_('bootstrap.endPane');
			?>

		</div>
		<div>
			<input type="hidden" name="option" value="com_sh404sef"/>
			<input type="hidden" name="component" value="com_sh404sef"/>
			<input type="hidden" name="tmpl" value="component"/>
			<input type="hidden" name="view" value="configuration"/>
			<input type="hidden" name="c" value="configuration"/>
			<input type="hidden" name="task" value="saveconfiguration"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>

</div>
