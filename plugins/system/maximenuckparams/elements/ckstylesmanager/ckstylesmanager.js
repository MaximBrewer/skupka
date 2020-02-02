/**
 * @copyright	Copyright (C) 2012 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Slideshow CK
 * @license		GNU/GPL
 * */

//function addfromfolderck() {
//
//}

function loadStylesCK(popup, fieldName) {
	if (popup.getParent('.accordion-body'))
		popup.getParent('.accordion-body').setStyle('overflow', 'visible');
	popup.setStyle('display', 'block');
	fieldName = fieldName.value.replace(/\|qq\|/g, "\"");
	var fields = JSON.decode(fieldName);
	fields.each(function(field) {

		var fieldobj = $(field['id']);

		// pour type radio
		if (fieldobj.getProperty('isradio')) {
			fieldobj.getParent().getElements('input[type=radio]').each(function(el) {
				elparent = el.getParent();
				labelbtn = elparent.getElement('label[for=' + el.id + ']');

				if (el.value == field['value']) {
					el.setProperty('checked', 'checked');
					if (el.getParent().hasClass('boutonRadio'))
						el.getParent().addClass('coche');
					if (elparent.hasClass('btn-group')) {
						labelbtn.addClass('active');
						if (el.value == 1)
							labelbtn.removeClass('btn-danger').addClass('btn-success');
						if (el.value == 0)
							labelbtn.removeClass('btn-success').addClass('btn-danger');
					}
				}
				else {
					el.removeProperty('checked');
					if (el.getParent().hasClass('boutonRadio'))
						el.getParent().removeClass('coche');
					if (elparent.hasClass('btn-group')) {
						labelbtn.removeClass('active');
						labelbtn.removeClass('btn-danger').removeClass('btn-success');
					}
				}
			});
		}
		
		// pour liste déroulante bootstrap
		if (fieldobj.tagName.toLowerCase() == 'select') { 
			if (BS_field = fieldobj.getNext('.chzn-container')) {
				BS_fieldtext =  field['value'] == 0 ? 'Default' : field['value'];
				BS_field.getElement('.chzn-single span').set('text', BS_fieldtext);
			}
		}

		fieldobj.value = field['value'];
	});
}

function saveStylesCK(popup, fieldName, identifier) {
	popup.setStyle('display', 'none');
	if (popup.getParent('.accordion-body'))
		popup.getParent('.accordion-body').setStyle('overflow', '');
//    alert(identifier);
	var fields = JSON.encode($$("." + identifier).getProperties('id', 'value'));
//    alert(fields);
	fields = fields.replace(/"/g, "|qq|");
	$(fieldName).value = fields;
	// alert($(fieldName).value);
}

function closeStylesCK(popup) {
	popup.setStyle('display', 'none');
	if (popup.getParent('.accordion-body'))
		popup.getParent('.accordion-body').setStyle('overflow', '');
}

// pour gestion editeur d'images
function jInsertEditorText(text, editor) {
	var newEl = new Element('span').set('html', text);
	var valeur = newEl.getChildren()[0].getAttribute('src');
	$(editor).value = valeur;
	addthumbnail(valeur, editor);
}

function addthumbnail(imgsrc, editor) {
	var slideimg = $(editor).getParent().getElement('img');
	var testurl = 'http';
	if (imgsrc.toLowerCase().indexOf(testurl.toLowerCase()) != -1) {
		slideimg.src = imgsrc;
	} else {
		slideimg.src = JURI + imgsrc;
	}

	slideimg.setProperty('width', '64px');
	slideimg.setProperty('height', '64px');
}

/*
 function addslideck(imgname, imgcaption, imgthumb, imglink, imgtarget, imgvideo, slideselect, imgalignment) {
 if (!imgname) imgname = '';
 if (!imgthumb) imgthumb = '../modules/mod_slideshowck/elements/ckslidesmanager/unknown.png';
 if (!imgcaption) imgcaption = '';
 imgcaption = imgcaption.replace(/\|dq\|/g,"&quot;");
 if (!imglink) imglink = '';
 if (!imgvideo) imgvideo = '';
 if (!imgtarget) {
 imgtarget = '';
 imgtargetoption = '<option value="_parent" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_SAMEWINDOW', 'same window')+'</option><option value="_blank">'+Joomla.JText._('MOD_SLIDESHOWCK_NEWWINDOW', 'new window')+'</option>';
 } else {
 if (imgtarget == '_parent') {
 imgtargetoption = '<option value="_parent" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_SAMEWINDOW', 'same window')+'</option><option value="_blank">'+Joomla.JText._('MOD_SLIDESHOWCK_NEWWINDOW', 'new window')+'</option>';
 } else {
 imgtargetoption = '<option value="_parent">'+Joomla.JText._('MOD_SLIDESHOWCK_SAMEWINDOW', 'same window')+'</option><option value="_blank" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_NEWWINDOW', 'new window')+'</option>';
 }
 }
 if (!slideselect) {
 slideselect = '';
 slideselectoption = '<option value="image" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_IMAGE', 'Image')+'</option><option value="video">'+Joomla.JText._('MOD_SLIDESHOWCK_VIDEO', 'Video')+'</option>';
 } else {
 if (slideselect == 'image') {
 slideselectoption = '<option value="image" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_IMAGE', 'Image')+'</option><option value="video">'+Joomla.JText._('MOD_SLIDESHOWCK_VIDEO', 'Video')+'</option>';
 } else {
 slideselectoption = '<option value="image">'+Joomla.JText._('MOD_SLIDESHOWCK_IMAGE', 'Image')+'</option><option value="video" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_VIDEO', 'Video')+'</option>';
 }
 }
 
 if (!imgalignment) {
 imgalignment = '';
 imgdataalignmentoption = '<option value="default" selected="selected">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else {
 if (imgalignment == 'topLeft') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'topCenter') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'topRight') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'centerLeft') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'center') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'centerRight') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'bottomLeft') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'bottomCenter') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else if (imgalignment == 'bottomRight') {
 imgdataalignmentoption = '<option value="default">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight" selected="selected">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 } else {
 imgdataalignmentoption = '<option value="default" selected="selected">Default</option>'
 +'<option value="topLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPLEFT', 'top left')+'</option>'
 +'<option value="topCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPCENTER', 'top center')+'</option>'
 +'<option value="topRight">'+Joomla.JText._('MOD_SLIDESHOWCK_TOPRIGHT', 'top right')+'</option>'
 +'<option value="centerLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLELEFT', 'center left')+'</option>'
 +'<option value="center">'+Joomla.JText._('MOD_SLIDESHOWCK_CENTER', 'center')+'</option>'
 +'<option value="centerRight">'+Joomla.JText._('MOD_SLIDESHOWCK_MIDDLERIGHT', 'center right')+'</option>'
 +'<option value="bottomLeft">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMLEFT', 'bottom left')+'</option>'
 +'<option value="bottomCenter">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMCENTER', 'bottom center')+'</option>'
 +'<option value="bottomRight">'+Joomla.JText._('MOD_SLIDESHOWCK_BOTTOMRIGHT', 'bottom right')+'</option>';
 }
 }
 
 index = checkIndex(0);
 var ckslide = new Element('li', {
 'class': 'ckslide',
 'id': 'ckslide'+index
 });
 ckslide.set('html','<div class="ckslidehandle"><div class="ckslidenumber">'+index+'</div></div><div class="ckslidecontainer">'
 + '<input name="ckslidedelete'+index+'" class="ckslidedelete" type="button" value="'+Joomla.JText._('MOD_SLIDESHOWCK_REMOVE2', '')+'" onclick="javascript:removeslide(this.getParent().getParent());" />'
 + '<div class="cksliderow"><div class="ckslideimgcontainer"><img src="'+imgthumb+'" width="64" height="64"/></div>'
 
 + '<input name="ckslideimgname'+index+'" id="ckslideimgname'+index+'" class="ckslideimgname hasTip" title="Image::This is the main image for the slide, it will also be used to create the thumbnail" type="text" value="'+imgname+'" onchange="javascript:addthumbnail(this.value, this);" />'
 
 + '<a class="modal ckselectimg" href="index.php?option=com_media&view=images&tmpl=component&e_name=ckslideimgname'+index+'" rel="{handler: \'iframe\', size: {x: 570, y: 400}}" >'+Joomla.JText._('MOD_SLIDESHOWCK_SELECTIMAGE', 'select image')+'</a></div>'
 + '<div class="cksliderow2"><span class="ckslidelabel">'+Joomla.JText._('MOD_SLIDESHOWCK_USETOSHOW', 'Display')+'</span><select class="ckslideselect">'+slideselectoption+'</select></div>'
 + '<div class="cksliderow"><span class="ckslidelabel">'+Joomla.JText._('MOD_SLIDESHOWCK_CAPTION', 'Caption')+'</span><input name="ckslidecaptiontext'+index+'" class="ckslidecaptiontext" type="text" value="'+imgcaption+'" onchange="javascript:storesetwarning();" /></div>'
 
 + '<div class="cksliderow"><div id="ckslideaccordion'+index+'">'
 + '<span class="ckslideaccordeonbutton">'+Joomla.JText._('MOD_SLIDESHOWCK_IMAGEOPTIONS', 'Image options')+'</span>'
 + '<span class="ckslideaccordeonbutton">'+Joomla.JText._('MOD_SLIDESHOWCK_LINKOPTIONS', 'Link options')+'</span>'
 + '<span class="ckslideaccordeonbutton">'+Joomla.JText._('MOD_SLIDESHOWCK_VIDEOOPTIONS', 'Video options')+'</span>'
 + '<div style="clear:both;"></div>'
 + '<div class="ckslideaccordeoncontent">'
 + '<div class="cksliderow"><span class="ckslidelabel">'+Joomla.JText._('MOD_SLIDESHOWCK_ALIGNEMENT_LABEL', 'Image alignment')+'</span><select name="ckslidedataalignmenttext'+index+'" class="ckslidedataalignmenttext" >'+imgdataalignmentoption+'</select></div>'
 + '</div>'
 + '<div class="ckslideaccordeoncontent">'
 + '<div class="cksliderow"><span class="ckslidelabel">'+Joomla.JText._('MOD_SLIDESHOWCK_LINK', 'Link url')+'</span><input name="ckslidelinktext'+index+'" class="ckslidelinktext" type="text" value="'+imglink+'" onchange="javascript:storesetwarning();" /></div>'
 + '<div class="cksliderow"><span class="ckslidelabel">'+Joomla.JText._('MOD_SLIDESHOWCK_TARGET', 'Target')+'</span><select name="ckslidetargettext'+index+'" class="ckslidetargettext" >'+imgtargetoption+'</select></div>'
 + '</div>'
 + '<div class="ckslideaccordeoncontent">'
 + '<div class="cksliderow"><span class="ckslidelabel">'+Joomla.JText._('MOD_SLIDESHOWCK_VIDEOURL', 'Video url')+'</span><input name="ckslidevideotext'+index+'" class="ckslidevideotext" type="text" value="'+imgvideo+'" onchange="javascript:storesetwarning();" /></div>'
 + '</div>'
 + '</div></div>'
 + '</div><div style="clear:both;"></div>');
 
 document.id('ckslideslist').adopt(ckslide);
 
 storeslideck();
 makesortables();
 SqueezeBox.initialize({});
 SqueezeBox.assign(ckslide.getElement('a.modal'), {
 parse: 'rel'
 });
 new Fx.Accordion($('accordion'+index), '#ckslideaccordion'+index+' .ckslideaccordeonbutton', '#ckslideaccordion'+index+' .ckslideaccordeoncontent',
 {
 onActive: function(toggler, content) {
 toggler.addClass('open');
 },
 onBackground: function(toggler, content) {
 toggler.removeClass('open');
 }
 });
 }
 */

function checkIndex(i) {
	while ($('ckslide' + i))
		i++;
	return i;
}


function removeslide(slide) {
	if (confirm(Joomla.JText._('MOD_SLIDESHOWCK_REMOVE', 'Remove this slide') + ' ?')) {
		slide.destroy();
		storeslideck();
	}
}

function storesetwarning() {
// $('ckstoreslide').setStyle('background-color', 'red');
}

function storeremovewarning() {
// $('ckstoreslide').setStyle('background-color', 'white');
}

function storeslideck() {
	var i = 0;
	var slides = new Array();
	document.id('ckslideslist').getElements('.ckslide').each(function(el) {
		slide = new Object();
		slide['imgname'] = el.getElement('.ckslideimgname').value;
		slide['imgcaption'] = el.getElement('.ckslidecaptiontext').value;
		slide['imgcaption'] = slide['imgcaption'].replace(/"/g, "|dq|");
		slide['imgthumb'] = el.getElement('img').src;
		slide['imglink'] = el.getElement('.ckslidelinktext').value;
		slide['imglink'] = slide['imglink'].replace(/"/g, "|dq|");
		slide['imgtarget'] = el.getElement('.ckslidetargettext').value;
		slide['imgalignment'] = el.getElement('.ckslidedataalignmenttext').value;
		slide['imgvideo'] = el.getElement('.ckslidevideotext').value;
		slide['slideselect'] = el.getElement('.ckslideselect').value;
		slides[i] = slide;
		i++;
	});

	slides = JSON.encode(slides);
	slides = slides.replace(/"/g, "|qq|");
	document.id('ckslides').value = slides;
	storeremovewarning();

}

function callslides() {
	// alert(document.id('ckslides').value);
	var slides = JSON.decode(document.id('ckslides').value.replace(/\|qq\|/g, "\""));
	if (slides) {
		slides.each(function(slide) {
			addslideck(slide['imgname'],
					slide['imgcaption'],
					slide['imgthumb'],
					slide['imglink'],
					slide['imgtarget'],
					slide['imgvideo'],
					slide['slideselect'],
					slide['imgalignment']
					);
		});
		storeremovewarning();
	}
}


function makesortables() {
	var sb = new Sortables('ckslideslist', {
		/* set options */
		clone: true,
		revert: true,
		handle: '.ckslidehandle',
		/* initialization stuff here */
		initialize: function() {

		},
		/* once an item is selected */
		onStart: function(el) {
			el.setStyle('background', '#add8e6');
		},
		/* when a drag is complete */
		onComplete: function(el) {
			el.setStyle('background', '#fff');
			storesetwarning();
		},
		onSort: function(el, clone) {

		}
	});
}

/*function ckremove(selection){
 selection.parentNode.removeChild(selection);
 }*/

window.addEvent('domready', function() {
	//callslides();		

	var script = document.createElement("script");
	script.setAttribute('type', 'text/javascript');
	script.text = "Joomla.submitbutton = function(task){"
			//+"storeslideck();"
			+ "$$('.ckpopup').destroy();"
			+ "if (task == 'module.cancel' || document.formvalidator.isValid(document.id('module-form'))) {	Joomla.submitform(task, document.getElementById('module-form'));"
			+ "if (self != top) {"
			+ "window.top.setTimeout('window.parent.SqueezeBox.close()', 1000);"
			+ "}"
			+ "} else {"
			+ "alert('Invalid Form');"
			+ "}}";
	document.body.appendChild(script);
});




    