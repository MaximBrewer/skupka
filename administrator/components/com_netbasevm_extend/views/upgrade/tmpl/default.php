<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//$vmiConfig =  VMIConfig::getConfig();

if( (trim($this->downloadId) != '') && (is_null($this->regInfo) || ($this->regInfo->code != 10)) ) {
    $needConfirm = true;
}
else {
    $needConfirm = false;
}

if( (trim($this->downloadId) == '') || is_null($this->regInfo) || ($this->regInfo->code != 10) ) {
    $downloadPaid = false;
}
else {
    $downloadPaid = true;
}

?>

<script language="javascript" type="text/javascript">
<!--
	function submitbutton3(pressbutton) {
		var form = document.adminForm;

		var sendOk = true;

		if( sendOk ) {
    		form.fromserver.value = '1';
    		form.submit();
		}
	}
	
	function submitbuttonext(extension) {
		var form = document.adminForm;

		form.fromserver.value = '1';
		form.ext.value = extension;
		form.submit();	    
	}

//-->
</script>

<fieldset class="adminform">
<legend>ARTIO VM Invoice</legend>
<table class="adminform">
<tr>
    <th colspan="2"><?php echo JText::_('COM_VMINVOICE_VERSION_INFO'); ?></th>
</tr>
<tr>
    <td width="20%"><?php echo JText::_('COM_VMINVOICE_INSTALLED_VERSION').':'; ?></td>
    <td><?php echo $this->oldVer; ?></td>
</tr>
<tr>
    <td><?php echo JText::_('COM_VMINVOICE_NEWEST_VERSION').':'; ?></td>
    <td><?php echo $this->newVer; ?></td>
</tr>
</table>

<?php
if( trim($this->downloadId) != '' ) {
    ?>
    <table class="adminform">
    <tr>
        <th colspan="2"><?php echo JText::_('COM_VMINVOICE_REGISTRATION_INFO'); ?></th>
    </tr>
    <?php
    if( is_null($this->regInfo) ) {
        ?>
        <tr>
            <td colspan="2"><?php echo JText::_('COM_VMINVOICE_COULD_NOT_RETRIEVE_REGISTRATION_INFORMATION'); ?></td>
        </tr>
        <?php
    }
    else if( $this->regInfo->code == 90 ) {
        ?>
        <tr>
            <td colspan="2"><?php echo JText::_('COM_VMINVOICE_DOWNLOAD_ID_WAS_NOT_FOUND_IN_OUR_DATABASE'); ?></td>
        </tr>
        <?php
    }
    else {
        $regTo = $this->regInfo->name;
        if( !empty($this->regInfo->company) ) {
            $regTo .= ', ' . $this->regInfo->company;
        }
        ?>
        <tr>
            <td width="20%""><?php echo JText::_('COM_VMINVOICE_REGISTERED_TO'); ?>:</td>
            <td><?php echo $regTo; ?></td>
        </tr>
        <?php
        if ($this->regInfo->code == 10 || $this->regInfo->code == 30) {
            $dateText = JText::_('COM_VMINVOICE_FREE_UPGRADES_AVAILABLE_UNTIL');
        }
        elseif ($this->regInfo->code == 20) {
            $dateText = JText::_('COM_VMINVOICE_FREE_UPGRADES_EXPIRED');
        }
        ?>
        <tr>
            <td><?php echo $dateText; ?>:</td>
            <td><?php echo $this->regInfo->date; ?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
} // Download ID set
?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">
<?php
$available = false;
if ((strnatcasecmp($this->newVer, $this->oldVer) > 0) ||
(strnatcasecmp($this->newVer, substr($this->oldVer, 0, strpos($this->oldVer, '-'))) == 0) ||
($this->newVer == "?.?.?") )
{
    $available = true;

    if (!$this->isPaidVersion && $downloadPaid) {
        $btnText = JText::_('COM_VMINVOICE_ONLINE_UPGRADE_TO_PAID_VERSION');
    } else {
        $btnText = JText::_('COM_VMINVOICE_UPGRADE_FROM_ARTIO_SERVER');
    }
}
elseif (($this->newVer == $this->oldVer)) {
    $available = true;
    if (!$this->isPaidVersion && $downloadPaid) {
    	$btnText = JText::_('COM_VMINVOICE_ONLINE_MIGRATE_TO_PAID_VERSION');
    } else {
    	$btnText = JText::_('COM_VMINVOICE_REINSTALL_FROM_ARTIO_SERVER');
    }
}

if( $available && $downloadPaid )
{
?>
    <table class="adminform">
        <tr>
            <th><?php echo $btnText; ?></th>
        </tr>
        <tr>
            <td>
                   <?php
                   if( $this->newVer == '?.?.?' ) {
                       echo JText::_('COM_VMINVOICE_SERVER_NOT_AVAILABLE');
                   }
                   else
                   {
                       ?>
                       <input class="button" type="button" value="<?php echo $btnText; ?>" onclick="submitbutton3()" />
                       <?php
                   }
                   ?>
            </td>
        </tr>
    </table>
<?php
} elseif ( !$downloadPaid ) {
?>
    <table class="adminform">
        <tr>
            <th><?php echo JText::_('COM_VMINVOICE_YOUR_VM_INVOICE_IS_NOT_REGISTERED'); ?></th>
        </tr>
    </table>
<?php
} else {
?>
    <table class="adminform">
        <tr>
            <th><?php echo JText::_('COM_VMINVOICE_YOUR_VM_INVOICE_IS_UP_TO_DATE'); ?></th>
        </tr>
    </table>
<?php } ?>

<table class="adminform">
<tr>
    <th colspan="2"><?php echo JText::_('COM_VMINVOICE_UPLOAD_PACKAGE_FILE'); ?></th>
</tr>
<tr>
    <td width="120">
        <label for="install_package"><?php echo JText::_('COM_VMINVOICE_PACKAGE_FILE'); ?>:</label>
    </td>
    <td>
        <input class="input_box" id="install_package" name="install_package" type="file" size="57" />
        <input class="button" type="submit" value="<?php echo JText::_('COM_VMINVOICE_UPLOAD_FILE'); ?> &amp; <?php echo JText::_('COM_VMINVOICE_INSTALL'); ?>" />
    </td>
</tr>
</table>
</fieldset>

<input type="hidden" name="option" value="com_vminvoice" />
<input type="hidden" name="task" value="doUpgrade" />
<input type="hidden" name="controller" value="upgrade" />
<input type="hidden" name="fromserver" value="0" />
<?php echo JHTML::_('form.token'); ?>
</form>
