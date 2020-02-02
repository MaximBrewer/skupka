var UniteNivoPro = new function() {

    var t = this;
    var containerID = "slider_container";
    var container, arrow_left, arrow_right, bullets_container;
    var caption_back, caption_text;
    var bulletsRelativeY = "";

    /**
     * show slider view error, hide all the elements
     */
    t.showSliderViewError = function(errorMessage) {
	jQuery("#config-document").hide();
	UniteAdmin.showErrorMessage(errorMessage);
    }

    /**
     * main init of the object
     */
    var init = function() {
	container = jQuery("#" + containerID);
	arrow_left = jQuery("#arrow_left");
	arrow_right = jQuery("#arrow_right");
	bullets_container = jQuery("#bullets_wrapper");
	caption_back = jQuery("#caption_back");
	caption_text = jQuery("#caption_text");

	UniteAdmin.hideSystemMessageDelay();
    }

    /**
     * init visual form width
     */
    t.initSliderView = function() {
	init();	//init the object - must call

	//update menu event
	jQuery("#visual").click(function() {
	    setTimeout("UniteNivoPro.initAfterDelay()", 300);
	});


	//update visual from fields
	updateFromFields();

	initSliderEvents();
	initVisualTabs();

	//update arrows fields ) if not set
	setTimeout("UniteNivoPro.initAfterDelay()", 1000);

    }

    /* ===================== Item View Section =================== */

    /**
     * init item view
     */
    t.initItemView = function() {

	//operate on slide image change
	var obj = document.getElementById("jform_params_image");
	obj.addEvent('change', function() {

	    var urlImage = g_urlRoot + this.value;
	    //trace(urlImage);
	    jQuery("#image_preview_wrapper").show().css("background-image", "url(\"" + urlImage + "\")");
	});
    }

    /* ===================== Item View End =================== */

    /**
     * on visual tab display event. update some fields
     */
    t.onVisualDisplay = function() {
	updateArrowPosFields();
	bulletsAlignCenter();
    }

    /**
     * init elements after delay
     */
    t.initAfterDelay = function() {
	updateArrowPosFields();
	initBullets();
    }

    /**
     * align the bullets to center
     */
    var bulletsAlignCenter = function() {
	var align = jQuery("#jform_visual_bullets_align").val();

	if (align != "center")
	    return(true);

	var data = getElementsData();

	var bulletsX = Math.round((data.sliderWidth - data.bulletsWidth) / 2);
	bullets_container.css("left", bulletsX + "px");

	updateBulletFields("center");
    }

    /**
     * set bullets position by relative.
     */
    var bulletsSetRelativeYPos = function() {
	var data = getElementsData();
	if (data.bulletsWidth == 0)
	    return(true);

	if (bulletsRelativeY == "")
	    return(true);

	var bulletsPosY = data.sliderHeight + bulletsRelativeY;
	bullets_container.css("top", bulletsPosY + "px");

	updateBulletFields("relative");
    }


    /**
     * do some actions on bullets align change
     */
    var onBulletsAlignChange = function(align) {

	switch (align) {
	    case "center":
		bullets_container.draggable("option", "axis", "y");
		bulletsAlignCenter();
		UniteAdmin.hideFormField("jform_visual_bullets_xleft");
		UniteAdmin.hideFormField("jform_visual_bullets_xright");
		break;
	    case "left":
		bullets_container.draggable("option", "axis", "");
		UniteAdmin.showFormField("jform_visual_bullets_xleft");
		UniteAdmin.hideFormField("jform_visual_bullets_xright");
		jQuery("#jform_visual_bullets_xleft-lbl").css('display', 'inline');
		break;
	    case "right":
		bullets_container.draggable("option", "axis", "");
		UniteAdmin.hideFormField("jform_visual_bullets_xleft");
		UniteAdmin.showFormField("jform_visual_bullets_xright");
		jQuery("#jform_visual_bullets_xright-lbl").css('display', 'inline');
		break;
	}
    }


    /**
     * on bullets drag event, update fields.
     */
    var onBulletsDrag = function() {
	updateBulletFields("drag");
    }

    /**
     * update relative y
     */
    var updateBulletsRelativeY = function() {
	var data = getElementsData();

	bulletsRelativeY = data.bulletsY - data.sliderHeight;
    }


    /**
     * init the bullets position
     */
    var initBullets = function() {
	var selectorBulletsAlign = "#jform_visual_bullets_align";
	var selectorBulletReverse = "#jform_visual_reverse_bullets";
	var align = jQuery(selectorBulletsAlign).val();

	//set bullets draggable
	var drag_options = {drag: onBulletsDrag};

	if (align == "center") {
	    bulletsAlignCenter();

	    //set draggable only y axis
	    drag_options.axis = "y";
	}
	jQuery(selectorBulletReverse).change(function() {
	    reverse_bullets();
	})
	//set select event
	jQuery(selectorBulletsAlign).change(function() {
	    onBulletsAlignChange(this.value);
	});

	//set draggable event
	bullets_container.draggable(drag_options);

	//show the bullets (if hidden)
	bullets_container.removeClass("invisible");

	updateBulletsRelativeY();
    }


    /**
     * show some tab, set it selecetd class, and hide the others.
     */
    var showVisualTab = function(linkID) {

	if (linkID.indexOf("#") == -1)
	    linkID = "#" + linkID;

	var link = jQuery(linkID);
	if (!link.length)
	    return(false);

	var tabID = link.data("tab");

	//set togler selected
	jQuery("#tabs_visual li a").removeClass("selected");
	link.addClass("selected");

	//show panel 
	jQuery(".visual_panel").removeClass("hidden").hide();
	jQuery(tabID).show();

    }


    /**
     * init visual tabs functionality
     */
    var initVisualTabs = function() {

	/*
	 var hash = location.hash;
	 if (hash) {
	 var linkID = hash.replace("#tab-", "");
	 showVisualTab(linkID);
	 }
	 */

	//set event
	jQuery("#tabs_visual li a").click(function() {
	    showVisualTab(this.id);
	});

    }

    /**
     * on slider resize event. update all elements and fields accordingly
     */
    var onSliderResize = function(event, ui) {

	//update fields widht / height
	if (event) {	//only if came from event
	    jQuery("#jform_visual_width").val(container.width());
	    jQuery("#jform_visual_height").val(container.height());
	}

	checkArrowsConnection("arrow_left");
	bulletsSetRelativeYPos();
	bulletsAlignCenter();
    }


    /**
     * init slider view onchange events
     */
    var initSliderEvents = function() {

	//set fields onchange events
	var fields = "#visual_wrapper input";
	jQuery(fields).blur(updateFromFields).click(updateFromFields);
	jQuery(fields).keypress(function(event) {
	    if (event.keyCode == 13)
		updateFromFields(this);
	});
	jQuery("#visual_wrapper select").change(updateFromFields);


	//set resizible event:
	container.resizable({resize: onSliderResize});

	//set on color picker move event:
	UniteAdmin.onColorPickerMove(function() {
	    updateFromFields();
	});

	//set arrows draggable
	jQuery("#arrow_left,#arrow_right").draggable({
	    drag: onArrowsDrag
	});

	jQuery("#arrows_gocenter").click(arrowsToCenter);

    }

    /**
     * get all element sizes and positions.
     */
    var getElementsData = function() {
	var data = {};

	//slider data
	data.sliderWidth = Number(jQuery("#jform_visual_width").val());
	data.sliderHeight = Number(jQuery("#jform_visual_height").val());

	//arrows data
	var arrowLeftPos = arrow_left.position();
	var arrowRightPos = arrow_right.position();

	data.arrowLeftX = Math.round(arrowLeftPos.left);
	data.arrowLeftY = Math.round(arrowLeftPos.top);

	data.arrowRightX = Math.round(arrowRightPos.left);
	data.arrowRightY = Math.round(arrowRightPos.top);

	data.arrowLeftWidth = arrow_left.width();
	data.arrowLeftHeight = arrow_left.height();
	data.arrowRightWidth = arrow_right.width();
	data.arrowRightHeight = arrow_right.height();

	//bullets data:
	var bulletsPos = bullets_container.position();

	data.bulletsWidth = bullets_container.width();
	data.bulletsHeight = bullets_container.height();
	data.bulletsX = Math.round(bulletsPos.left);
	data.bulletsY = Math.round(bulletsPos.top);

	return(data);
    }

    /**
     * get the arrows to center of the banner (y axes)
     */
    var arrowsToCenter = function() {
	var data = getElementsData();
	var arrowsNewY = Math.round(data.sliderHeight - data.arrowLeftHeight) / 2;
	arrow_right.css("top", arrowsNewY + "px");
	arrow_left.css("top", arrowsNewY + "px");

	//update position fields on the panel
	updateArrowPosFields();
    }


    /**
     * check arrows connection, and 
     */
    var checkArrowsConnection = function(arrowID) {

	var freeDrag = jQuery("#jform_visual_arrows_free_drag").is(":checked");

	if (freeDrag == true) {
	    updateArrowPosFields();
	    return(false);
	}

	var data = getElementsData();

	if (arrowID == "arrow_left") {	//left arrow is main.
//            var arrowRightNewX = data.sliderWidth - data.arrowLeftX - data.arrowRightWidth;
// version 3.0.7
	    var arrowRightNewX = data.arrowLeftX;
	    //set right arrow position
	    arrow_right.css({"top": data.arrowLeftY + "px",
		"right": arrowRightNewX + "px", "left": "auto"});

	} else if (arrowID == "arrow_right") {		//right arrow is main
	    var arrowLeftNewX = data.sliderWidth - data.arrowRightX - data.arrowRightWidth;
	    //set left arrow position
	    arrow_left.css({"top": data.arrowRightY + "px",
		"left": arrowLeftNewX + "px"});
	}

	updateArrowPosFields();
    }


    /**
     * on arrows drag event. update form fields, and operate arrow connections. 
     */
    var onArrowsDrag = function() {
	var arrowID = this.id;

	checkArrowsConnection(arrowID);

    }

    /**
     * 
     * update bullets position from the bullets
     */
    var updateBulletFields = function(fromWhere) {

	//trace("update fields "+fromWhere);return(false);

	if (bullets_container.is(":visible") == false)
	    return(true);

	var data = getElementsData();

	//update fields:
	var bulletsRightX = data.sliderWidth - data.bulletsX - data.bulletsWidth;

	jQuery("#jform_visual_bullets_y").val(data.bulletsY);
	jQuery("#jform_visual_bullets_xleft").val(data.bulletsX);
	jQuery("#jform_visual_bullets_xright").val(bulletsRightX);

	//update relative option
	updateBulletsRelativeY();
    }


    /**
     * update arrows positions from the arrows
     */
    var updateArrowPosFields = function() {

	//don't update if the container not visible
	if (container.is(':visible') == false)
	    return(true);

	if (arrow_left.is(':visible') == false)
	    return(true);

	var data = getElementsData();

	//set values trung truong
	var arrowRightNewX = data.sliderWidth - data.arrowRightX - data.arrowRightWidth;
	jQuery("#jform_visual_arrow_left_x").val(data.arrowLeftX);
	jQuery("#jform_visual_arrow_left_y").val(data.arrowLeftY);
	jQuery("#jform_visual_arrow_right_x").val(arrowRightNewX);
	jQuery("#jform_visual_arrow_right_y").val(data.arrowRightY);
    }

    /**
     * hide arrows and disable panel elements
     */
    var hideArrows = function() {
	if (arrow_left.is(':visible') == false)
	    return(true);

	arrow_left.hide();
	arrow_right.hide();

	//hide arrow fields
	jQuery("#section_arrows").hide();
    }


    /**
     * show arrows and enable panel elements
     */
    var showArrows = function() {
	if (arrow_left.is(':visible') == true)
	    return(true);

	arrow_left.show();
	arrow_right.show();

	//show arrow fields
	jQuery("#section_arrows").show();
    }


    /**
     * update the container from fields.
     */
    var updateFromFields = function(element) {

	if (element == undefined || element.id == undefined)
	    element = this;

	var elemID = null;
	if (element.id != undefined)
	    elemID = element.id;


	//---- update width / height
	var width = jQuery("#jform_visual_width").val();
	var height = jQuery("#jform_visual_height").val();
	container.width(width).height(height);

	//width / heigth event
	switch (elemID) {
	    case "jform_visual_width":
	    case "jform_visual_height":
		onSliderResize();
		break;
	}

	//update border
	updateFromFields_border();

	//update shadow
	updateFromFields_shadow();

	//update arrows
	updateFromFields_arrows(elemID);

	//update bullets
	updateFromFields_bullets(elemID);

	//set the bullets according the resize
	bulletsSetRelativeYPos();

	//update captions
	updateFromFields_caption(elemID);

	//update caption text
	updateFromFields_captionText();
    }


    /**
     * update caption text
     */
    var updateFromFields_captionText = function() {

	var css = {};

	if (caption_back.is(":visible") == false)
	    return(true);

	//set color
	var textColor = jQuery("#jform_visual_text_color").val();
	css["color"] = textColor;

	//set text align
	var textAlign = jQuery("#jform_visual_text_align").val();
	css["text-align"] = textAlign;

	//set padding
	var textPadding = jQuery("#jform_visual_text_padding").val();
	css["padding"] = textPadding + "px";

	var fontSize = jQuery("#jform_visual_font_size").val();
	css["font-size"] = fontSize + "px";

	//set the css
	caption_text.css(css);
    }


    /**
     * show the caption
     */
    var showCaption = function() {
	if (caption_back.is(":visible") == true)
	    return(false);

	caption_back.show();
    }

    /**
     * hide the caption
     */
    var hideCaption = function() {
	if (caption_back.is(":visible") == false)
	    return(false);

	caption_back.hide();
    }


    /**
     * update captions fields
     */
    var updateFromFields_caption = function(elemID) {

	var css = {};

	if (elemID == "jform_visual_has_caption") {
	    var hasCaption = jQuery("#jform_visual_has_caption").is(":checked");
	    if (hasCaption == true)
		showCaption();
	    else
		hideCaption();
	}

	if (caption_back.is(":visible") == false)
	    return(true);

	//set back color
	var backColor = jQuery("#jform_visual_caption_back_color").val();
	css["background-color"] = backColor;

	//set alpha
	var alpha = jQuery("#jform_visual_caption_back_alpha").val();
	var alpha = Number(alpha) / 100;
	caption_back.fadeTo(0, alpha);

	//set position:
	var position = jQuery("#jform_visual_caption_position").val();
	if (position == "top") {
	    css["bottom"] = "";		//set to top
	    css["top"] = "0px";
	} else {
	    css["bottom"] = "0px";		//set to bottom
	    css["top"] = "";
	}

	//set the css
	caption_back.css(css);
    }

    /**
     * hide the bullets
     */
    var hideBullets = function() {
	if (bullets_container.is(":visible") == false)
	    return(true);

	//hide fields
	jQuery("#section_bullets").hide();

	//hide bullets
	bullets_container.hide();
    }

    /**
     * show the bullets
     */
    var showBullets = function() {
	if (bullets_container.is(":visible") == true)
	    return(true);

	//show fields
	jQuery("#section_bullets").show();

	//show bullets
	bullets_container.removeClass("invisible").show();
    }

    /**
     * update bullets fields
     */
    var reverse_bullets = function() {
	var reverseChecked = jQuery("#jform_visual_reverse_bullets").is(":checked");
	var normal = 'normal';
	var active = 'active';
	if (reverseChecked) {
	    normal = 'active';
	    active = 'normal';
	}
	jQuery("#bullets_wrapper img").each(function(index, value) {
	    if (index == 1) {
		jQuery(this).attr('src', jQuery(this).attr('src').replace(normal, active));
	    } else {
		jQuery(this).attr('src', jQuery(this).attr('src').replace(active, normal));
	    }

	});
    }
    /**
     * update bullets fields
     */
    var updateFromFields_bullets = function(elemID) {

	//trace("update bullets");return(false);		

	switch (elemID) {
	    case "jform_visual_has_bullets":
		var showBulletsCheck = jQuery("#jform_visual_has_bullets").is(":checked");
		if (showBulletsCheck)
		    showBullets();
		else
		    hideBullets();
		break;
	}

	//skip invisible container
	if (bullets_container.is(':visible') == false)
	    return(true);

	var bulletsY = jQuery("#jform_visual_bullets_y").val();
	switch (elemID) {
	    default:
	    case "jform_visual_bullets_xleft":
		var bulletsX = jQuery("#jform_visual_bullets_xleft").val();

		break;
	    case "jform_visual_bullets_xright":
		var data = getElementsData();
		var bulletsRightX = jQuery("#jform_visual_bullets_xright").val();
		var bulletsX = data.sliderWidth - data.bulletsWidth - bulletsRightX;
		break;
	    case "jform_visual_bullets_spacing":	//set spacing
		var spacing = jQuery("#jform_visual_bullets_spacing").val();
		bullets_container.find("ul li:not(:first-child)").css("margin-left", spacing + "px");
		bulletsAlignCenter();
		break;
	}

	bullets_container.css({"top": bulletsY + "px", "left": bulletsX + "px"});
	updateBulletFields("fields");
    }



    /**
     * update border
     */
    var updateFromFields_border = function() {
	var has_border = jQuery("#jform_visual_has_border").is(':checked');

	if (has_border == true) {
	    var border_color = jQuery("#jform_visual_border_color").val();
	    var border_size = jQuery("#jform_visual_border_size").val();

	    container.css({"border-style": "solid",
		"border-width": border_size + "px",
		"border-color": border_color
	    });

	} else
	    container.css({"border-style": "none"});
    }


    /**
     * update shadow from fields
     */
    var updateFromFields_shadow = function() {

	//----update shadow:
	var has_shadow = jQuery("#jform_visual_has_shadow").is(':checked');
	if (has_shadow == true) {
	    var shadow_color = jQuery("#jform_visual_shadow_color").val();
	    var shadowProps = "0px 1px 5px 0px " + shadow_color;
	    container.css({"box-shadow": shadowProps,
		"-moz-box-shadow": shadowProps,
		"-webkit-box-shadow": shadowProps});
	} else
	    container.css({"box-shadow": "none", "-moz-box-shadow": "none", "-webkit-box-shadow": "none"});

    }


    /**
     * set arrows in the place of the fields
     */
    var updateFromFields_arrows = function(elemID) {

	var showArrows_check = jQuery("#jform_visual_has_arrows").is(":checked");

	//check arrows hide / show
	if (elemID == "jform_visual_has_arrows") {
	    if (showArrows_check == false)
		hideArrows();
	    else
		showArrows();
	}

	//position arrows
	if (arrow_left.is(':visible') == false)
	    return(true);

	var arrowLeftX = jQuery("#jform_visual_arrow_left_x").val();
	var arrowLeftY = jQuery("#jform_visual_arrow_left_y").val();
	var arrowRightX = jQuery("#jform_visual_arrow_right_x").val();
	var arrowRightY = jQuery("#jform_visual_arrow_right_y").val();

	arrow_left.css({"top": arrowLeftY + "px", "left": arrowLeftX + "px"});
	arrow_right.css({"top": arrowRightY + "px", "right": arrowRightX + "px", "left": "auto"});

	//operate errors connection:
	switch (elemID) {
	    case "jform_visual_has_arrows":
	    case "jform_visual_arrow_left_y":
	    case "jform_visual_arrow_left_x":
		checkArrowsConnection("arrow_left");
		break;
	    case "jform_visual_arrow_right_y":
	    case "jform_visual_arrow_right_x":
		checkArrowsConnection("arrow_right");
		break;
	}

    }


    /**
     * on arrows select event - update arrow pictures and change arrows set
     */
    t.onArrowsSelect = function(data) {
	jQuery("#arrow_left").attr("src", data.url_left);
	jQuery("#arrow_right").attr("src", data.url_right);
	jQuery("#jform_visual_arrows_set").val(data.arrowName);

	//align arrows
	setTimeout("UniteNivoPro.operationDelay('checkArrowsConnection')", 500);
    }


    /**
     * on bullets select - take bullets html by ajax, and change the bullets.
     */
    t.onBulletsSelect = function(setName) {
	var data = {setName: setName};

	UniteAdmin.ajaxRequest("get_bullets_html", data, function(response) {
	    jQuery("#bullets_wrapper").html(response.bullets_html);
	    jQuery("#jform_visual_bullets_set").val(setName);

	    //align center after delay
	    setTimeout("UniteNivoPro.operationDelay('bulletsAlignCenter')", 500);
	    bulletsAlignCenter();
	    reverse_bullets();

	});
    }

    /**
     * align center after delay function
     */
    t.operationDelay = function(operation) {

	switch (operation) {
	    case "bulletsAlignCenter":
		bulletsAlignCenter();
		break;
	    case "checkArrowsConnection":
		checkArrowsConnection("arrow_left");
		break;
	}
    }

}
	