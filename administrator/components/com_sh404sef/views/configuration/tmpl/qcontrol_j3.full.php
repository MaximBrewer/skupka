<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<div class="sh404sef-qcontrol" id="sh404sef-qcontrol">

<!-- start quick control panel markup -->
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

  <div id="qcontrol-editcell">
<!-- start of configuration html -->

<div class="control-group">
	<div class="control-label">
	<div rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_SEF_ENABLED'), JText::_( 'COM_SH404SEF_TT_SEF_ENABLED'));?>><?php echo JText::_('COM_SH404SEF_SEF_ENABLED'); ?></div>
	</div>
	<div class="controls">
		<fieldset id="Enabled" class="radio btn-group">
	    	<input type="radio" id="Enabled0" name="Enabled" value="0" <?php echo $this->sefConfig->Enabled ? '' : 'checked="checked"'; ?>/>
	    	<label for="Enabled0"><?php echo JText::_('COM_SH404SEF_NO'); ?></label>
	    	<input type="radio" id="Enabled1" name="Enabled" value="1" <?php echo $this->sefConfig->Enabled ? 'checked="checked"' : ''; ?>/>
	    	<label for="Enabled1"><?php echo JText::_('COM_SH404SEF_YES'); ?></label>
	    </fieldset>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
	<div rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_CAN_READ_REMOTE_CONFIG'), JText::_( 'COM_SH404SEF_TT_CAN_READ_REMOTE_CONFIG'));?>><?php echo JText::_('COM_SH404SEF_CAN_READ_REMOTE_CONFIG'); ?></div>
	</div>
	<div class="controls">
		<fieldset id="canReadRemoteConfig" class="radio btn-group" >
		    <input type="radio" name="canReadRemoteConfig" id="canReadRemoteConfig0" value="0" <?php echo $this->sefConfig->canReadRemoteConfig ? '' : 'checked="checked"'; ?> class="inputbox" size="2" />
		    <label for="canReadRemoteConfig0"><?php echo Jtext::_('COM_SH404SEF_NO'); ?></label>
		    <input type="radio" name="canReadRemoteConfig" id="canReadRemoteConfig1" value="1" <?php echo !$this->sefConfig->canReadRemoteConfig ? '' : 'checked="checked"'; ?> class="inputbox" size="2" />
		    <label for="canReadRemoteConfig1"><?php echo Jtext::_('COM_SH404SEF_YES'); ?></label>
	    </fieldset>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
	<div rel="tooltip" <?php echo Sh404sefHelperHtml::wrapBootstrapTipTitle(JText::_('COM_SH404SEF_ACTIVATE_SECURITY'), JText::_( 'COM_SH404SEF_TT_ACTIVATE_SECURITY'));?>><?php echo JText::_('COM_SH404SEF_ACTIVATE_SECURITY'); ?></div>
	</div>
	<div class="controls">
		<fieldset id="Enabled" class="radio btn-group">
        <input type="radio" name="shSecEnableSecurity" id="shSecEnableSecurity0" value="0"
        <?php echo $this->sefConfig->shSecEnableSecurity ? '' : 'checked="checked"'; ?> class="inputbox" size="2" />
        <label for="shSecEnableSecurity0"><?php echo JText::_('COM_SH404SEF_NO'); ?></label>
        <input type="radio" name="shSecEnableSecurity"  id="shSecEnableSecurity1" value="1"
        <?php echo !$this->sefConfig->shSecEnableSecurity ? '' : 'checked="checked"'; ?> class="inputbox" size="2" />
        <label for="shSecEnableSecurity1"><?php echo JText::_('COM_SH404SEF_YES'); ?></label>
        </fieldset>
	</div>
</div>

<div class="control-group">
	<div class="span4">
		<fieldset>
			<button href="javascript: void(0);" class="btn btn-primary btn-large" title="<?php echo JText::_('JAPPLY'); ?>" onclick="shSubmitQuickControl(); return false;" ><i class="icon-apply icon-white"> </i><?php echo ' ' . JText::_('JAPPLY'); ?></button>
		</fieldset>
	</div>
</div>

<!-- start quick control panel markup -->

    <input type="hidden" name="c" value="configuration" />
    <input type="hidden" name="view" value="configuration" />
    <input type="hidden" name="layout" value="qcontrol" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="saveqcontrol" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="format" value="raw" />

    <?php echo JHTML::_( 'form.token' ); ?>
  </div>
</form>
	<?php
	$displayData = new stdClass();
	$displayData->message = empty($this->message) ? '' : $this->message;
	$displayData->doNotDismissMessage = true;
	echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $displayData);
	?>
</div>
</div>
