/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

var $ck = jQuery.noConflict();

$ck(document).ready(function(){
	initMenusManagerck();
	$ck('#adminForm').append('<div id="ckoverlay"></div>');
	initTogglers();
	setParentClass();
	$ck( ".cktip" ).tooltip({ tooltipClass: "cktooltip", position: { my: "left+15 center", at: "right center" }, "container": "body" });
});

function jInsertFieldValue(value, id) {
	$ck('#'+id).val(value).trigger('change');
}

function initTogglers() {
	$ck('.itemtoggler').not('.togglerdone').each(function() {
		$ck(this).click(function() {
			$ck($ck($ck(this).parents('li')[0]).find('.togglecontent')[0]).toggle('fast');
			$ck(this).toggleClass('opened');
		});
		$ck(this).addClass('togglerdone');
	});
}

function initMenusManagerck() {
		$ck('ol#sortable').nestedSortable({
			forcePlaceholderSize: true,
			handle: 'div .icon-move',
			helper:	'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tolerance: 'pointer',
			toleranceElement: '> div',
			maxLevels: 0,
			isTree: true,
			expandOnHover: 900,
			startCollapsed: true,
			rtl: false,
			update: function( event, ui ) {
				if ($ck(ui.item).attr('data-valid') == 'true' && !$ck(ui.sender).hasClass('ckmenuselectsortable'))
					updateNestedItems();
			},
			complete: function( event, ui ) {

			}
		});
		
		initSelectMenu();

		$ck('.disclose').on('click', function() {
			$ck(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
		});

		$ck( document ).tooltip({
			track: true,
			position: {
				my: "center bottom-20",
				at: "center top",
				using: function( position, feedback ) {
				$ck( this ).css( position );
				$ck( "<div>" )
				.addClass( "arrow" )
				.addClass( feedback.vertical )
				.addClass( feedback.horizontal )
				.appendTo( this );
				}
			},
			"container": "body"
		});
}

	function initSelectMenu() {
		$ck('ol.ckmenuselectsortable').nestedSortable({
			connectWith: 'ol#sortable',
			forcePlaceholderSize: true,
			helper:	'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			maxLevels: 0,
			isTree: true
		});
	}

function ajaxSetStype(title, type, component, view, parentId, parentLevel, prevItemId, alias, dataId, dataLayout, dataController, category_id, dataAttribs) {
	addckwaitlayer();
	if (!alias) alias = '';
	var myurl = "index.php?option=com_accordeonck&task=item.setType";
	jQuery.ajax({
		type: "POST",
		url: myurl,
		data: {
			title: title,
			type: type,
			component: component,
			view: view,
			dataId: dataId,
			layout: dataLayout,
			dataController: dataController,
			category_id: category_id,
			dataAttribs: dataAttribs
			}
	}).done(function(data64) {
		ajaxSaveItem(data64, title, type, component, view, alias, parentId, parentLevel, prevItemId, dataId);
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED_SET_TYPE', 'CK_FAILED_SET_TYPE'));
	});
}

function ajaxSaveItem(data64, title, type, component, view, alias, parentId, parentLevel, prevItemId, dataId) {
	if (!alias) alias = '';
	var myurl = "index.php?option=com_accordeonck&task=item.save";
	jQuery.ajax({
		type: "POST",
		url: myurl,
		data: {
			data64: data64,
			title: title,
			alias: alias,
			menutype: $ck('#menutype').val(),
			parentId: parentId,
			parentLevel: parentLevel,
			prevItemId: prevItemId,
			dataId: dataId,
			component: component,
			view: view,
			type: type
			}
	}).done(function(code) {
		var result = false;
		if (code != 'menutypeerror' && code != 'selectalias' && code != 'aliasmissing') result = jQuery.parseJSON(code)
		if (code == 'menutypeerror') {
			alert(Joomla.JText._('CK_FAILED_SAVE_ITEM_ERRORMENUTYPE','CK_FAILED_SAVE_ITEM_ERRORMENUTYPE'));
			removeckwaitlayer();
		} else if (code == 'selectalias') {
			var newalias = prompt(Joomla.JText._('CK_ALIAS_EXISTS_CHOOSE_ANOTHER','CK_ALIAS_EXISTS_CHOOSE_ANOTHER'),'');
			if (newalias != null) ajaxSaveItem(data64, title, type, component, view, newalias, parentId, parentLevel, prevItemId);
			removeckwaitlayer();
		} else if (result && result[0] == '1') {
			createItem(result[1], title, type, parentId, parentLevel, prevItemId);
		} else {
			alert(code);
		}
	}).fail(function(code) {
		if (code['status'] == '500') {
			alert(Joomla.JText._('CK_FAILED_SAVE_ITEM_ERROR500','CK_FAILED_SAVE_ITEM_ERROR500'));
			removeckwaitlayer();
		} else {
			alert(Joomla.JText._('CK_FAILED_SAVE_ITEM', 'CK_FAILED_SAVE_ITEM'));
		}
	});
}

function ajaxTrashItem(id) {
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;

	var idsToDel = new Array();
	idsToDel.push(id);
	$ck('ol#sortable li[data-id='+id+'] li').each(function() {
		idsToDel.push($ck(this).attr('data-id'));
	});
	addckwaiticon($ck('#ckmessage'));
	var myurl = "index.php?option=com_accordeonck&task=item.delete";
	jQuery.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: idsToDel
			}
	}).done(function(code) {
		if (code == '1') {
			$ck('ol#sortable li[data-id='+id+']').css('overflow', 'hidden').animate({'width': 0, 'height': 0}, function() {$ck(this).remove();setParentClass();});
			removeckwaiticon($ck('#ckmessage'));
			$ck('#ckmessage > div').empty().append('<div class="alert alert-success">Item trashed with success</div>').fadeOut(3000);
		} else {
			alert(code);
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED_TRASH_ITEM','CK_FAILED_TRASH_ITEM'));
	});
}

function createItem(id, title, type, parentId, parentLevel, prevItemId) {
	var myurl = "index.php?option=com_accordeonck&view=item&layout=additem";
	jQuery.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: id,
			type: type
			}
	}).done(function(code) {
		removeckwaitlayer();
		if (prevItemId) {
			$ck('#sortable li[data-id=' + prevItemId + ']').after(code);
		} else if(parentId) {
			if (!$ck('#sortable li[data-id=' + parentId + '] > ol').length) {
				$ck('#sortable li[data-id=' + parentId + ']').append('<ol />');
				$ck('#sortable li[data-id=' + parentId + '] > ol').append(code);
			} else {
				$ck('#sortable li[data-id=' + parentId + '] > ol').prepend(code);
			}
		} else {
			$ck('#sortable').prepend(code);
		}
		updateNestedItems();
		initTogglers();
		SqueezeBox.assign(document.getElements('#sortable li[data-id=' + id + '] a.modal'), {
			parse: 'rel'
		});
	}).fail(function() {
		alert(Joomla.JText._('CK_FAILED_CREATE_ITEM','CK_FAILED_CREATE_ITEM'));
	});
}

function ajaxPublish(button, id, state) {
	if ($ck($ck(button).parents('li')[0]).attr('data-home') == '1') {
		alert(Joomla.JText._('CK_UNABLE_UNPUBLISH_HOME','CK_UNABLE_UNPUBLISH_HOME'));
		return;
	}
	button = $ck('#publish'+id);
	if (button.hasClass('ckwait')) return;
	addckwaiticonpublish($ck('#publish'+id));
	var myurl = "index.php?option=com_accordeonck&task=item.publish";
	jQuery.ajax({
		type: "POST",
		url: myurl,
		data: {
			id: id,
			state: state
			}
	}).done(function(pks) {
		if (!pks) {
			removeckwaiticonpublish($ck('#publish'+id));
			updatepublishicon(id, state);
		} else {
			pks = pks.split(',');
			state = updatepublishstate(id);
			pks.each(function(pk) {
				removeckwaiticonpublish($ck('#publish'+pk));
				updatepublishicon(pk, state);
			});
		}
	}).fail(function() {
		removeckwaiticonpublish($ck('#publish'+id));
	});
}

function addckwaiticonpublish(button) {
	button.find('i').attr('data-class',button.attr('class')).attr('class', 'icon-');
	button.addClass('ckwait');
}

function removeckwaiticonpublish(button, failed) {
	if (failed) {
		button.attr('class','icon-warning');
	} else {
		button.removeClass('ckwait').attr('class',button.attr('data-class'));
	}
}

function updatepublishicon(id, state) {
	button = $ck('#publish'+id);
	var buttonclass = (state == 0) ? 'icon-unpublish' : 'icon-publish';
	button.find('i').attr('class', buttonclass);
}

function updatepublishstate(id, state) {
	button = $ck('#publish'+id);
	state = 1 - parseInt(button.attr('data-state'));
	button.attr('data-state', state);
	return state;
}

function editTitle(editbutton) {
	el = $ck('> div .cktitle', $ck($ck(editbutton).parents('li')[0]));
	txt = $ck(el).text();
	$ck(el).html("<input type=\"text\" value=\""+txt+"\" style=\"width:150px;\"/>");
	$ck(el).attr('text-origin', txt);
	$ck('input', $ck(el)).focus();
	$ck('.exittitle', $ck(el).parent()).show();
	$ck('.edittitle', $ck(el).parent()).hide();
}


function saveTitle(el) {
	txt = $ck('input', el).val();
	if (txt && txt != $ck(el).attr('text-origin')) {
		ajaxSaveTitle($ck(el).attr('data-id'), txt, el);
	}
	if (txt) {
		$ck(el).html(txt);
		$ck('.exittitle', $ck(el).parent()).hide();
		$ck('.edittitle', $ck(el).parent()).show();
	}
}

function exitTitle(el) {
	txt = $ck(el).attr('text-origin');
	if (txt) {
		$ck(el).html(txt);
		$ck('.exittitle', $ck(el).parent()).hide();
		$ck('.edittitle', $ck(el).parent()).show();
	}
}

function ajaxSaveTitle(id, title, el) {
	addckwaiticon($ck('.edittitle', $ck(el).parent()));
	addckwaiticon($ck('#ckmessage'));
	var myurl = "index.php?option=com_accordeonck&task=item.saveTitleAjax";
	jQuery.ajax({
	type: "POST",
	url: myurl,
	data: {
		id: id,
		title: title
		}
	}).done(function(code) {
		if (code == '1') {
			removeckwaiticon($ck('.edittitle', $ck(el).parent()));
		} else {
			removeckwaiticon($ck('.edittitle', $ck(el).parent()), true);
			removeckwaiticon($ck('#ckmessage'));
			$ck('#ckmessage').append(code);
			alert(Joomla.JText._('CK_TITLE_NOT_UPDATED', 'CK_TITLE_NOT_UPDATED')+code);
		}
	}).fail(function() {
		removeckwaiticon($ck('.edittitle', $ck(el).parent()), true);
	});
}

function addckwaiticon(button) {
	button.addClass('ckwait');
	button.find('i').css('visibility', 'hidden');
}

function addckwaitlayer() {
	$ck('#ckoverlay').show();
}

function removeckwaitlayer() {
	$ck('#ckoverlay').hide();
}

function removeckwaiticon(button, failed) {
	if (failed) {
		button.attr('class','icon-warning');
	} else {
		button.removeClass('ckwait');
		button.find('i').css('visibility', 'visible');
	}
}

function updateNestedItems() {
	var ajaxSaveLevelLaunched = 0;
	$ck('ol#sortable li').attr('data-leveltmp', '1').attr('data-parenttmp','1');
	$ck('ol#sortable li').each(function() {
		var item = $ck(this);
		if (item.children('ol').length) {
			$ck('li', item).attr('data-leveltmp', parseInt(item.attr('data-leveltmp'))+1).attr('data-parenttmp', item.attr('data-id'));
		}
		if (item.attr('data-leveltmp') != item.attr('data-level')
			|| item.attr('data-parenttmp') != item.attr('data-parent')) {
			ajaxSaveLevel(item.attr('data-id'), item.attr('data-leveltmp'), item.attr('data-parenttmp'));
			ajaxSaveLevelLaunched = 1;
		}
	});
	ajaxSaveOrderCK();
}

function ajaxSaveLevel(id, level, parentid) {
	addckwaitlayer();
	var myurl = "index.php?option=com_accordeonck&task=item.saveLevelAjax";
	jQuery.ajax({
	type: "POST",
	url: myurl,
	async: true,
	data: {
		id: id,
		level: level,
		parentid: parentid
		}
	}).done(function(code) {
		if (code == '1') {
			$ck('#ckmessage > div').empty().append('<div class="alert alert-success">Items order saved with success</div>').fadeOut(3000);
			$ck('ol#sortable li[data-id='+id+']').attr('data-level', level).attr('data-parent', parentid);
		} else {
			alert(Joomla.JText._('CK_LEVEL_NOT_UPDATED', 'CK_LEVEL_NOT_UPDATED')+code);
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_SAVE_LEVEL_FAILED', 'CK_SAVE_LEVEL_FAILED'));
	});
}

function ajaxSaveOrderCK() {
	addckwaitlayer();
	var cid_array = new Array();
	var order_array = new Array();
	var lft_array = new Array();
	var rgt_array = new Array();
	var lft = 1, rgt = 2;
	$ck('ol#sortable li:first-child').attr('lft', lft).attr('rgt', rgt);
	$ck('ol#sortable li').each(function(k, item) {
		item = $ck(item);
		if (item.prev('li').length) {
			item.attr('lft', parseInt(item.prev('li').attr('rgt'))+1).attr('rgt', parseInt(item.prev('li').attr('rgt'))+2);
		}
		if (item.children('ol').length) {
			item.attr('rgt', parseInt(item.attr('lft'))+$ck('li', item).length*2+1);
			$ck('> ol > li', item).attr('lft', parseInt(item.attr('lft'))+1).attr('rgt', parseInt(item.attr('lft'))+2);
		}
		cid_array.push($ck(this).attr('data-id'));
		order_array.push(k);
		lft_array.push($ck(this).attr('lft'));
		rgt_array.push($ck(this).attr('rgt'));
	});

	var myurl = "index.php?option=com_accordeonck&task=item.saveOrderAjax";
	jQuery.ajax({
	type: "POST",
	url: myurl,
	data: {
		cid: cid_array,
		order: order_array,
		lft: lft_array,
		rgt: rgt_array
		}
	}).done(function(code) {
		if (code == '1') {
			removeckwaitlayer($ck('#ckmessage'));
			$ck('#ckmessage > div').empty().show().append('<div class="alert alert-success">Items order saved with success</div>').fadeOut(3000);
		} else {
			removeckwaitlayer($ck('#ckmessage'));
			alert(Joomla.JText._('CK_SAVE_ORDER_FAILED', 'CK_SAVE_ORDER_FAILED')+code);
		}
		setParentClass();
	}).fail(function() {
		removeckwaitlayer($ck('#ckmessage'));
	});
}

function setParentClass() {
	$ck('ol#sortable li').each(function(i, item) {
		item = $ck(item);
		if (item.children('ol').children('li').length) {
			item.addClass('parent');
		} else {
			item.removeClass('parent');
		}
	});
}

function ajaxCheckin(el, id) {
	addckwaiticon($ck('.checkedouticon', $ck(el).parent()));
	var myurl = "index.php?option=com_accordeonck&task=item.checkinAjax";
	jQuery.ajax({
	type: "POST",
	url: myurl,
	data: {
		id: id
		}
	}).done(function(code) {
		if (code == '1') {
			removeckwaiticon($ck('.checkedouticon', $ck(el).parent()));
			$(el).remove();
		} else {
			removeckwaiticon($ck('.checkedouticon', $ck(el).parent()), true);
			alert(Joomla.JText._('CK_CHECKIN_NOT_UPDATED', 'CK_CHECKIN_NOT_UPDATED')+code);
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_CHECKIN_FAILED', 'CK_CHECKIN_FAILED'));
		removeckwaiticon($ck('.checkedouticon', $ck(el).parent()), true);
	});
}

function saveparam(id, param, value, btn, waiticon, callback, args ) {
	if (!waiticon) waiticon = false;
	if (waiticon) addckwaiticon($ck(btn));
	var myurl = "index.php?option=com_accordeonck&task=item.saveParam";
	jQuery.ajax({
	type: "POST",
	url: myurl,
	data: {
		id: id,
		param: param,
		value:value
		}
	}).done(function(code) {
		if (code == '1') {
			if (waiticon) removeckwaiticon($ck(btn), false);
			if (callback && typeof window[callback] == 'function') { if (!args) args = null;window[callback](args); }
		} else {
			if (waiticon) removeckwaiticon($ck(btn), true);
			alert(Joomla.JText._('CK_PARAM_NOT_UPDATED', 'CK_PARAM_NOT_UPDATED')+code);
		}
	}).fail(function() {
		alert(Joomla.JText._('CK_PARAM_UPDATE_FAILED', 'CK_PARAM_UPDATE_FAILED'));
		if (waiticon) removeckwaiticon($ck(btn), true);
	});
}

function togglecolumn(btn) {
	$ck(btn).toggleClass('active').toggleClass('btn-primary');
	$ck(btn).parent().toggleClass('active');
	var item = $ck($ck(btn).parents('li')[0]);

	if (item.attr('data-level') == '1') {
		alert(Joomla.JText._('CK_NO_COLUMN_ON_ROOT_ITEM', 'WARNING : You can not create a column on a root item, this will kill your layout !'));
	}
	saveparam(item.attr('data-id'), 'maximenu_createcolumn', btn.hasClass('active') ? '1' : '0', btn, true);
}

function changecolwidth(btn) {
	var colwidth = prompt('Column width', $ck(btn).text());
	if (colwidth == null) return;
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_colwidth', colwidth, btn, true);
	$ck(btn).text(colwidth);
}

function createnewrow(btn) {
//	if (!$ck(btn).hasClass('active') && !$ck(btn).parent().find('.createcolumn').hasClass('active')) {
//		alert(Joomla.JText._('CK_FIRST_CREATE_ROW', 'CK_FIRST_CREATE_ROW'));
//		return;
//		$ck(btn).parent().find('.createcolumn').addClass('active').addClass('btn-primary');
//	}
	$ck(btn).toggleClass('active').toggleClass('btn-primary');
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_createnewrow', btn.hasClass('active') ? '1' : '0', btn, true);
}

function togglesubmenuwidth(btn) {
	if ( $ck($ck(btn).parents('li')[0]).find('.submenuwidth').find('.valuetxt').html().length
		|| $ck($ck(btn).parents('li')[0]).find('.submenuheight').find('.valuetxt').html().length
		|| $ck($ck(btn).parents('li')[0]).find('.submenuleftmargin').find('.valuetxt').html().length
		|| $ck($ck(btn).parents('li')[0]).find('.submenutopmargin').find('.valuetxt').html().length		) {
		alert(Joomla.JText._('CK_FIRST_CLEAR_VALUE', 'CK_FIRST_CLEAR_VALUE'));
		return;
	}

	$ck(btn).toggleClass('active');
	$ck(btn).parent().toggleClass('active');
}

function changesubmenuwidth(btn) {
	var submenuwidth = prompt('Submenu width', $ck(btn).find('.valuetxt').text());
	if (submenuwidth == null) return;
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_submenucontainerwidth', submenuwidth, btn, true);
	$ck(btn).find('.valuetxt').text(submenuwidth);
}

function changesubmenuheight(btn) {
	var submenuheight = prompt('Submenu height', $ck(btn).find('.valuetxt').text());
	if (submenuheight == null) return;
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_submenucontainerheight', submenuheight, btn, true);
	$ck(btn).find('.valuetxt').text(submenuheight);
}

function changefullwidthclass(btn) {
	$ck(btn).toggleClass('active').toggleClass('btn-primary');
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_liclass', btn.hasClass('active') ? '1' : '0', btn, true);
}

function submenuleftmargin(btn) {
	var submenuleftmargin = prompt('Submenu left margin', $ck(btn).find('.valuetxt').text());
	if (submenuleftmargin == null) return;
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_leftmargin', submenuleftmargin, btn, true);
	$ck(btn).find('.valuetxt').text(submenuleftmargin);
}

function submenutopmargin(btn) {
	var submenutopmargin = prompt('Submenu top margin', $ck(btn).find('.valuetxt').text());
	if (submenutopmargin == null) return;
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_topmargin', submenutopmargin, btn, true);
	$ck(btn).find('.valuetxt').text(submenutopmargin);
}

function saveDescription(btn) {
	var item = $ck($ck(btn).parents('li')[0]);
	var txt = btn.value;
	if (txt != $ck(btn).attr('text-origin')) {
		saveparam(item.attr('data-id'), 'maximenu_desc', txt, btn, true, 'success_description', Array(btn, txt));
	}

}

function success_description(params) {
	$ck(params[0]).attr('text-origin', params[1]);
}

function toggledesktopstate(btn) {
	$ck(btn).toggleClass('disable');
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_disabledesktop', $ck(btn).hasClass('disable') ? '1' : '0', btn, true);
}

function togglemobilestate(btn) {
	console.log($ck(btn).hasClass('disable'));
	$ck(btn).toggleClass('disable');
	var item = $ck($ck(btn).parents('li')[0]);
console.log($ck(btn).hasClass('disable'));
	saveparam(item.attr('data-id'), 'maximenu_disablemobile', $ck(btn).hasClass('disable') ? '1' : '0', btn, true);

}

function call_icons_popup(id) {
	var BSmodal = jQuery('#accordeonckModalIcons');
	BSmodal.attr('data-fieldid', id);
	BSmodal.modal('show');
}
	
function select_fa_icon(iclass) {
	jQuery('#accordeonckModalIconsFieldid').val(iclass).trigger('change');
}

function set_fa_icon(iclass, fieldid) {
	var btn = '#'+fieldid;
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_icon', iclass, btn, true, 'success_fa_icon', Array(fieldid, iclass));

	jQuery('#accordeonckModalIcons').modal('hide');
}

function success_fa_icon(params) {
	jQuery('#'+params[0]).addClass('active').find('i').attr('class', params[1]);
}

function remove_fa_icon(btn) {
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;

	var item = $ck($ck(btn).parents('li')[0]);
	
	saveparam(item.attr('data-id'), 'maximenu_icon', '', $ck(btn).prev(), true);
	$ck(btn).prev().removeClass('active').find('i').removeAttr('class');
}

function call_image_popup(id) {
	var BSmodal = jQuery('#accordeonckModalImagemanager');
	BSmodal.attr('data-fieldid', id);
	BSmodal.modal('show');
}

function set_image(image_url, fieldid) {
	var btn = '#'+fieldid;
	var item = $ck($ck(btn).parents('li')[0]);
	var title = $ck(btn).attr('data-original-title');
	var tmp = $ck('<div />').append(title);
	var rep = tmp.find('img').attr('src');
	if (tmp.find('img').length) {
		$ck(btn).attr('data-original-title', $ck(btn).attr('data-original-title').replace(rep,URIROOT + '/' + image_url));
	} else {
		$ck(btn).attr('data-original-title', title + '<br /><img src="' + URIROOT + '/' + image_url + '" style="max-width:200px;max-height:200px;"/>');
	}
	saveparam(item.attr('data-id'), 'menu_image', image_url, btn, true);
	$ck(btn).addClass('active');
	jQuery('#accordeonckModalIcons').modal('hide');
}

function remove_image(btn) {
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;

	var item = $ck($ck(btn).parents('li')[0]);
	var title = $ck(btn).prev().attr('data-original-title');
	var pos = title.indexOf('<br />');
	
	saveparam(item.attr('data-id'), 'menu_image', '', $ck(btn).prev(), true);
	$ck(btn).prev().attr('data-original-title', title.substring(0, pos)).removeClass('active');
}

function call_modules_popup(id) {
	var BSmodal = jQuery('#accordeonckModalModules');
	BSmodal.attr('data-fieldid', id);
	BSmodal.modal('show');
}

function ck_select_module(id, title, type) {
	var jsonvalue = JSON.stringify(Array(id,title,type));
	jQuery('#accordeonckModalModulesfieldid').val(jsonvalue).trigger('change');
}

function set_module(jsonvalue, fieldid) {
	// maximenu_insertmodule à mettre à 1
	// maximenu_module = id
	var btn = '#'+fieldid;
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_insertmodule', '1', btn, true, 'set_module_id', Array(jsonvalue, fieldid));

	jQuery('#accordeonckModalModules').modal('hide');
}

function set_module_id(params) {
	var module = jQuery.parseJSON(params[0]);
	// maximenu_insertmodule à mettre à 1
	// maximenu_module = id
	var btn = '#'+params[1];
	var item = $ck($ck(btn).parents('li')[0]);

	saveparam(item.attr('data-id'), 'maximenu_module', module[0], btn, true, 'success_module', Array(params[1], module[0], module[1]));

	jQuery('#accordeonckModalModules').modal('hide');
}

function success_module(params) {
//	var module = jQuery.parseJSON(params[1]);
	jQuery('#'+params[0]).find('.modulename').html('<span class="moduleid label label-inverse">' + params[1] + '</span>&nbsp;' + params[2]);
	jQuery('#'+params[0]).addClass('active').addClass('btn-info');
}

function remove_module_icon(btn) {
	if (!confirm(Joomla.JText._('CK_CONFIRM_DELETE','CK_CONFIRM_DELETE'))) return;

	var item = $ck($ck(btn).parents('li')[0]);
	
	saveparam(item.attr('data-id'), 'maximenu_insertmodule', '', $ck(btn).prev(), true);
	$ck(btn).prev().removeClass('active').removeClass('btn-info').find('.modulename').text('');
}

function editItem(btn) {
	var item = $ck($ck(btn).parents('li')[0]);
	var boxfooterhtml = '<a class="ckboxmodal-button" href="javascript:void(0);" onclick="ckSaveIframe(this, '+item.attr('data-id')+');CKBox.close(this, \'1\')">' + Joomla.JText._('CK_SAVE') + '</a>';
	CKBox.open({handler: 'iframe', id: 'accordeonckparamsItem', footerHtml:  boxfooterhtml, style: {padding: '10px'}, onCKBoxLoaded: function() {addSaveBtntoIframe('accordeonckparamsItem')}, url: URIROOT + '/administrator/index.php?option=com_menus&view=item&layout=edit&id='+item.attr('data-id')+'&tmpl=component'});
}

function addSaveBtntoIframe(boxId) {
	var iframe = $ck('#' + boxId).find('iframe');
	iframe.load(function() {
		var iframeForm = iframe.contents().find('form');
		if (! iframeForm.find('#saveBtn').length) {
			var saveHtml = '<button id="saveBtn" style="display:none;" onclick="Joomla.submitbutton(\'item.apply\');" type="button"></button>';
			iframe.contents().find('form').prepend(saveHtml);
		}
	});
}

function ckSaveIframe(btn, id) {
	var iframe = $ck('iframe', $ck($ck(btn).parents('.ckboxmodal')[0])).contents();
	ckUdpateModuleTitle(id, iframe.find('#jform_title').val());
	iframe.find('#saveBtn').click();
}

function ckUdpateModuleTitle(id, title) {
	var el = $ck('#sortable li[data-id="'+id+'"]');
	el.find('.cktitle').text(title);
}