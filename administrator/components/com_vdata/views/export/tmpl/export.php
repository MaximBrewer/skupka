<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');

?>

<div id="vdatapanel">
<div id="hd_progress"></div>
<form action="index.php?option=com_vdata&view=export" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <div class="exportdatablock">
        <?php echo $this->contents; ?>
    </div>

<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_vdata" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="export" />
<input type="hidden" name="profileid" value="<?php if(!empty($this->profile->id)) echo $this->profile->id; ?>" />
</form>

</div>