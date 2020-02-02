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
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

if (!empty($this->redirectTo))
{

	// render a refresh_parent layout
	/**
	 * This layout only insert javascript to close a modal windows
	 */
	$displayData = new stdClass();
	$displayData->refreshAfter = 0;
	$displayData->refreshTo = '"' . $this->redirectTo . '"';
	ShlMvcLayout_Helper::render('com_sh404sef.general.refresh_parent', $displayData);
	return;
}

?>
<div class="sh404sef-popup" id="sh404sef-popup">
<?php

echo ShlMvcLayout_Helper::render('com_sh404sef.general.message_block', $this);

?>

<form action="index.php"
  <?php if (!empty($this->setFormEncType))
{
	echo ' enctype="' . $this->setFormEncType . '" ';
}
  ?>method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

  <div class="row-fluid">

  <?php

if (!empty($this->mainText))
{
	echo $this->mainText;
}

  ?>
  </div>

  <div class="row-fluid">
  <?php
if (!empty($this->toolbar))
{
	echo '<hr class="hr-condensed" /><span class="pull-right">' . $this->toolbar . '</span>';
}
  ?>
  </div>

  <div>
  <input type="hidden" name="c" value="<?php echo $this->actionController; ?>" />
  <input type="hidden" name="option" value="com_sh404sef" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="tmpl" value="component" />
  <input type="hidden" name="optype" value="<?php echo $this->opType; ?>" />
  <input type="hidden" name="opsubject" value="<?php echo $this->opSubject; ?>" />
    <?php
// optional elements to pass to the action controller if action is confirmed
foreach ($this->buttonsList as $button)
{
	echo '  <input type="hidden" name="' . $button . '" value="' . intval($this->$button) . '" />' . "\n";
}

// option hidden text as provided by the calling controller
if (!empty($this->hiddenText))
{
	echo $this->hiddenText;
}
	?>
    <?php echo JHTML::_('form.token'); ?>
  </div>
</form>
<?php
if (!empty($this->continue))
{
	$js = '
        <script type="text/javascript">
      window.location = "' . $this->continue . '"
    </script>
    ';
	echo $js;
}
?>

</div>
