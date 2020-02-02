<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

?>
<style>
.container {
	color: #333;
}
</style>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true) ?>/components/com_accordeonck/assets/jquery-noconflict.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/modules/mod_accordeonck/assets/font-awesome.min.css" type="text/css" />
<link rel="stylesheet" href="<?php echo JUri::root(true) ?>/administrator/components/com_accordeonck/assets/bootstrap.min.css" type="text/css" />

<div class="container-fluid">
	<h3><?php echo JText::_('CK_MODULE') ?></h3>
	<div id="search-title" class="filter-parent" style="margin: 10px;">
		<div style="position: relative; height: 46px; display: inline-block;" class="">
			<input type="text" tabindex="1" class="" id="filter-by-title" placeholder="Search by title" onchange="searchby('title')" style="height:auto;margin:0;background-color: transparent; position: relative;">
			<span class="btn-group">
				<button class="btn fa fa-search" id="filter-by-title-submit" onclick="searchby('title')"></button>
				<button class="btn fa fa-times" id="filter-by-title-clear" onclick="clearsearch('title')"></button>
			</span>
		</div>

		<div style="position: relative; height: 46px; display: inline-block;" class="">
			<input type="text" tabindex="1" class="" id="filter-by-module" placeholder="Search by type" onchange="searchby('module')" style="height:auto;margin:0;background-color: transparent; position: relative;">
			<span class="btn-group">
				<button class="btn fa fa-search" id="filter-by-module-submit" onclick="searchby('module')"></button>
				<button class="btn fa fa-times" id="filter-by-module-clear" onclick="clearsearch('module')"></button>
			</span>
		</div>

		<div style="position: relative; height: 46px; display: inline-block;" class="">
			<input type="text" tabindex="1" class="" id="filter-by-position" placeholder="Search by position" onchange="searchby('position')" style="height:auto;margin:0;background-color: transparent; position: relative;">
			<span class="btn-group">
				<button class="btn fa fa-search" id="filter-by-position-submit" onclick="searchby('position')"></button>
				<button class="btn fa fa-times" id="filter-by-position-clear" onclick="clearsearch('position')"></button>
			</span>
		</div>
	</div>
<script>
function searchby(type) {
	if (jQuery('#filter-by-'+type).val() == '') return;
	jQuery('.modulerow:not([data-'+type+'*=' + jQuery('#filter-by-'+type).val().toLowerCase() + '])').addClass('filteredck').hide();
	if (jQuery('.filteredck').length) {
		jQuery('.modulerow[data-'+type+'*=' + jQuery('#filter-by-'+type).val().toLowerCase() + ']:not(.filteredck)').show();
	} else {
		jQuery('.modulerow[data-'+type+'*=' + jQuery('#filter-by-'+type).val().toLowerCase() + ']').addClass('filteredck').show();
	}
}

function clearsearch(type) {
	jQuery('.modulerow').removeClass('filteredck').show();
	jQuery('#filter-by-' + type).val('');
	if (jQuery('#filter-by-title').val()) searchby('title');
	if (jQuery('#filter-by-module').val()) searchby('module');
	if (jQuery('#filter-by-position').val()) searchby('position');
	
}

jQuery(document).ready(function() {
	jQuery('.modulerow').click(function(e) {
		e.preventDefault();
		window.parent.ck_select_module(jQuery(this).attr('data-id'), jQuery(this).attr('data-title'), jQuery(this).attr('data-module'));
		window.parent.jModalClose();
	});
});
</script>
<table class="table table-striped table-hover">
<thead>
	<tr>
		<th class="" style="width:20px;"><?php echo JText::_('CK_ID') ?></th>
		<th class="" style="min-width:200px;text-align:left;"><?php echo JText::_('CK_TITLE') ?></th>
		<th class="" style="min-width:200px;"><?php echo JText::_('CK_TYPE') ?></th>
		<th class="" style="min-width:200px;"><?php echo JText::_('CK_POSITION') ?></th>
	</tr>
</thead>
<?php foreach($this->modules as $module) { ?>
	<tr class="modulerow" style="cursor:pointer;" data-id="<?php echo strtolower($module->id) ?>" data-title="<?php echo strtolower($module->title) ?>" data-module="<?php echo strtolower($module->module) ?>" data-position="<?php echo strtolower($module->position) ?>">
		<td class="" style="width:20px;"><?php echo $module->id ?></td>
		<td class="" style="min-width:200px;text-align:left;color:#3071a9;"><?php echo $module->title ?></td>
		<td class="" style="min-width:200px;"><?php echo $module->module ?></td>
		<td class="" style="min-width:200px;"><?php echo $module->position ?></td>
	</tr>
<?php } ?>
<table>
</div>

