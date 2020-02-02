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
error_reporting(0);
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
AdminUIHelper::startAdminArea($this);

include_once("components/com_netbasevm_extend/helpers/statistics/createbox.php");
include_once("components/com_netbasevm_extend/helpers/statistics/config.php");
include_once("components/com_netbasevm_extend/helpers/statistics/config1.php");


define("CMSMART", "cms_extend");

$doc = JFactory::getDocument();

$viewObjects = array();
foreach ($this->displayClasses as $class => $title) {
    //echo $class . "<br/>";
    $viewObjects[] = new $class(CMSMART, $class);
}


$imgPath = JUri::root() . 'administrator/components/com_netbasevm_extend/assets/images/statistics/';

//echo JURI::base();

$doc->addStyleSheet(JURI::base(). 'components/com_netbasevm_extend/assets/css/styles.css');
$doc->addStyleSheet(JURI::base(). 'components/com_netbasevm_extend/assets/css/jquery-ui.css');

$doc->addScript(JURI::base(). 'components/com_netbasevm_extend/assets/js/statistics/cms_totalOrders.js');
$doc->addScript(JURI::base(). 'components/com_netbasevm_extend/assets/js/statistics/cms_getDateTime.js');
$doc->addScript(JURI::base(). 'components/com_netbasevm_extend/assets/js/statistics/cms_Orders.js');
$doc->addScript(JURI::base(). 'components/com_netbasevm_extend/assets/js/statistics/cms_session.js');
$doc->addScript(JURI::base(). '/components/com_netbasevm_extend/assets/js/statistics/jquery-ui-1.8.23.min.js');  
    
$doc->addScript('http://www.google.com/jsapi');
?>

<script type="text/javascript">
<?php
//load all Google visualization packages as defined in the view classes
$packages = array();
foreach ($viewObjects as $viewObj) {
    $package = $viewObj->getVis1ToInclude();
    if ($package != "")
        $packages[] = "'" . $package . "'";
    $package = $viewObj->getVis2ToInclude();
    if ($package != "")
        $packages[] = "'" . $package . "'";
}
$packages = array_unique($packages);
//echo implode(",",$packages);

echo "google.load('visualization', '1', {packages: [" . implode(",", $packages) . "]});";
?>

    var totalOrAvgLife = 't';
    var totalOrAvgPeriod = 't';
    var fromDate;
    var toDate;
    var presetDate;

//main draw visualization function
    function drawVisualization()
    {
        fromDate = document.getElementById('cms_fromdate').value;

        toDate = document.getElementById('cms_todate').value;
        presetDate = document.getElementById('cms_typedate').options[document.getElementById('cms_typedate').selectedIndex].value;
        cms_getsecs();
        cms_getTotalOrders();
        cms_getOrders();
<?php
foreach ($viewObjects as $viewObj)
    echo $viewObj->getDrawFuctionCall();
?>
    }


<?php
foreach ($viewObjects as $viewObj)
    echo $viewObj->getDrawFunction();
?>


//////////////////////////////////////////////////////////////////
<?php
foreach ($viewObjects as $viewObj)
    echo $viewObj->getHandleFunctions();
?>
//////////////////////////////////////////////////////////////////

//set the callback function that will be called on load
    google.setOnLoadCallback(drawVisualization);

    function getCurrtenDate(offset)
    {
        // create Date object for current location
        var d = new Date();

        // convert to msec
        // add local time zone offset
        // get UTC time in msec
        var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

        // create new Date object for different city
        // using supplied offset
        var nd = new Date(utc + (3600000 * offset));
        var strDate = nd.getFullYear() + '-' + nd.getMonth() + '-' + nd.getDate();
        return strDate;
    }



    (function ($) {
        $(document).ready(function () {
            $('#cms_fromdate').val('all');
            $('#cms_todate').val('all');
            $("#cms_typedate option[value=a]").attr('selected', 'selected');
            //date picker
            $('#fromdate_picker').datepicker({
                firstDay: 1,
                changeFirstDay: false,
                dateFormat: "yy-mm-dd",
                onSelect: function (dateText) {
                    $('#cms_fromdate').val(dateText);
                    $('#cms_fromdate').css('background', '#77bbf3');
                }
            });

            $('#todate_picker').datepicker({
                firstDay: 1,
                changeFirstDay: false,
                dateFormat: "yy-mm-dd",
                onSelect: function (dateText) {
                    $('#cms_todate').val(dateText);
                    $('#cms_todate').css('background', '#77bbf3');
                }
            });

            $('#cms_submitdate').click(function ()
            {
                fromDate = $('#cms_fromdate').val();
                toDate = $('#cms_todate').val();
                presetDate = $('#cms_typedate option').val();

                cms_getsecs();
                cms_getTotalOrders();
<?php
foreach ($viewObjects as $viewObj)
    echo $viewObj->getRefreshFunctionCall();
?>
            });

            $('#cms_typedate').change(function () {
                cms_getDateTime($('#cms_typedate option:selected').val());

            });

            $('.cms_totalpritimeleft').after(jQuery('#box_3'));

            $('span[class="hasTip"]').remove();
        });
    })(jQuery)

</script>

<div id="container">
    <div class="header-title"><?php echo JText::_('COM_NETBASE_HEADER_TITLE') ?></div>
    <div id="currency_symbol"></div>
    <div class="cms_datepciker">
        <span><?php echo JText::_('COM_NETBASE_FROMDATE'); ?> :</span>
        <input type="text" name="cms_fromdate" id="cms_fromdate" value="" readonly="readonly" style="width: 130px;height: 20px;"/>
        <input id="fromdate_picker">

        <span><?php echo JText::_('COM_NETBASE_TODATE'); ?> :</span>
        <input type="text" name="cms_todate" id="cms_todate" value="" readonly="readonly" style="width: 130px;height: 20px;"/>
        <input id="todate_picker">

        <span><?php echo JText::_('COM_NETBASE_OR'); ?>:</span>
        <select id="cms_typedate">
            <option value="n"><?php echo JText::_('COM_NETBASE_TODAY'); ?></option>
            <option value="d"><?php echo JText::_('COM_NETBASE_YESTERDAY'); ?></option>
            <option value="w"><?php echo JText::_('COM_NETBASE_WEEKAGO'); ?></option>
            <option value="m"><?php echo JText::_('COM_NETBASE_MONTHAGO'); ?></option>
            <option value="3m"><?php echo JText::_('COM_NETBASE_3MONTHAGO'); ?></option>
            <option value="6m"><?php echo JText::_('COM_NETBASE_6MONTHAGO'); ?></option>
            <option value="y"><?php echo JText::_('COM_NETBASE_YEARAGO'); ?></option>
            <option value="a"><?php echo JText::_('COM_NETBASE_ALLTIME'); ?></option>
        </select>

        <input id="cms_submitdate" type="button" value="<?php echo JText::_('COM_NETBASE_SUBMIT') ?>"/>

    </div>
    <div class="clear"></div>

    <div id="container_left">
        <div class="top"></div>
        <div class='cms_totalpritimeleft'><h2><?php echo JText::_('COM_NETBASE_TOTAL_ALLTIME') ?></h2>
            <div id="cms_totalalltime"></div>
        </div>
<?php
box::resetInstanceCounter();
//Life total orders
unset($viewObjects[5]);

foreach ($viewObjects as $viewObj) {
    if (!$viewObj->isDateFilter())
        $viewObj->createViewFrame();
}
?>
    </div>
    <div id="container_right">
        <div class='cms_totalpritimeright'><h2><?php echo JText::_('COM_NETBASE_TOTAL_PERIOD') ?></h2>
            <div id="cms_totalpritime"></div>
        </div>
        <?php
        //echo "<pre>".print_r($viewObjects,1)."</pre>";
        foreach ($viewObjects as $viewObj) {

            if ($viewObj->isDateFilter())
                $viewObj->createViewFrame();
        }
        ?>
    </div>
</div>

        <?php AdminUIHelper::endAdminArea(); ?>