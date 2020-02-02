var HsHtmlExpanderDialog = {

    settings : {},

	preInit : function() {
		tinyMCEPopup.requireLangPack();
	},

	init : function() {

		tinyMCEPopup.restoreSelection();

		var self = this, ed = tinyMCEPopup.editor, se = ed.selection, action = 'insert', rel = "", el;

    	$('button#insert').click(function(e) {
    		self.insert();
    		e.preventDefault();
    	});

    	$('button#help').click(function(e) {
    		self.openHelp();
    		e.preventDefault();
    	});

		n = se.getNode();

		el = ed.dom.getParent(n, 'A');
		if (el != null && el.nodeName == 'A'){
			action = "update";
		}

		tinyMCEPopup.resizeToInnerSize();

		// Init plugin
		$.Plugin.init();

		var target = ''; /*HsHtmlExpander.getParam('target');*/
		if(target == 'default') target = '';

		if (action == "update")
		{
			$('#insert').button('option','label',tinyMCEPopup.getLang('update','Update',true));
			var href=decodeURIComponent(ed.convertURL(ed.dom.getAttrib(el,'href')));
			// Setup form data
			$('#href').val(href);
			$('#title').val(ed.dom.getAttrib( el, 'title' ));
			$('#id').val(ed.dom.getAttrib( el, 'id' ));
			$('#expanderid').val(ed.dom.getAttrib(el,'id' ));
			$('#style').val(ed.dom.getAttrib( el, 'style' ));
			$('#onclick').val(ed.dom.getAttrib( el, 'onclick' ));
			if ($('#onclick').val() == "")
			{
				$('#onclick').val( ed.dom.getAttrib(el,'data-mce-onclick'));
			}
			$('#onmouseover').val(ed.dom.getAttrib( el, 'onmouseover' ));
			rel = ed.dom.getAttrib(el,'rel');
			if ( rel == 'highslide-ajax'
			   ||rel == 'highslide-swf'
			   ||rel == 'highslide-iframe'
			   )
			{
				$('#unobtrusive').attr('checked', true );
				$('#objecttype').val( rel.substr( 10 ) );
			}
		}
		else
		{
			$.Plugin.setDefaults(this.settings.defaults)
		}

		if ($('#unobtrusive').is(':checked'))
		{
			this.setTabs();
		}
		else
		{
			var isExpander = this.setHsValues();
			if (action == 'update')
			{
				if (! isExpander
				   || rel == 'highslide'
				   )
				{
					$.Dialog.alert(tinyMCEPopup.getLang('hshtmlexpander_dlg.is_htmlexpander', 'This expander was created by the HsExpander plugin. It must be used for updates.'));
					$('#insert').button('disable');
					return;
				}
			}
		}

		if ($('#contentid').val() != '') {
			var divobj = ed.dom.get($('#contentid').val());
			if (divobj != null && divobj.nodeName == "DIV" && ed.dom.getAttrib( divobj, 'class') == 'highslide-html-content') {
				$('#content').val( divobj.innerHTML );
				$('#contentstyle').val( ed.dom.getAttrib( divobj, 'style' ));
				$('#origcontentid').val( $('#contentid'.val()));
			}
		}

		window.focus();
	},

	mirrorValue : function(ele, destid)
	{
		var el = document.getElementById( destid );
		if (el != null)
		{
			el.value = ele.value;
		}
		return true;
	},

	setTabs : function(){
		if ($('#unobtrusive').is(':checked'))
		{
			if ($('#objecttype').val() == "")
			{
				$.Dialog.alert(tinyMCEPopup.getLang('hshtmlexpander_dlg.need_objecttype', 'Object type must be set to ajax, iframe or flash for unobtrusive markup.'));
				$('#unobtrusive').attr('checked', false );
				return;
			}
			$('#tabs').tabs('disable', 1 );
			$('#tabs').tabs('disable', 2 );
			$('#tabs').tabs('disable', 3 );
			$('#tabs').tabs('disable', 4 );
			$('#tabs').tabs('disable', 5 );
			$('#tabs').tabs('disable', 6 );
		}
		else
		{
			$('#tabs').tabs('enable', 1 );
			$('#tabs').tabs('enable', 2 );
			$('#tabs').tabs('enable', 3 );
			$('#tabs').tabs('enable', 4 );
			$('#tabs').tabs('enable', 5 );
			$('#tabs').tabs('enable', 6 );
		}
	},

	setHsValues : function(){
		var onclick = $('#onclick').val();
		var onmouseover = $('#onmouseover').val();

		if (onclick != null && onclick.indexOf('return hs.htmlExpand') != -1)
		{
			var ndx = onclick.indexOf('{');
			var lndx = onclick.lastIndexOf('}');

			if (ndx != -1 && lndx != -1)
			{
				var argsstr = onclick.substring( ndx, lndx+1 );
				try
				{
					eval( "var onclickprop = [" + argsstr + "]" );
				}
				catch(ex)
				{
					//	ignore
				}
			}

			if (typeof onclickprop != 'undefined')
			{
				for (var i = 0; i < onclickprop.length; i++ )
				{
					var propobj = onclickprop[i];
					this.setValues( propobj );
				}
			}
			var openonhover = (onmouseover != null && onmouseover.indexOf('return this.onclick()') != -1);
			$('#openonhover').attr('checked', openonhover );
			return true;
		}
		if ( onclick != null && onclick.indexOf( 'return hs.expand' ) != -1)
		{
			return false;
		}
		return true;
	},

	setValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "align":
						$('#align').val( propvalu );
						break;
					case "anchor":
						$('#anchor').val( propvalu );
						break;
					case "easing":
						$('#easing').val( propvalu );
						break;
					case "easingClose":
						$('#easingclose').val( propvalu );
						break;
					case "allowSizeReduction":
						$('#allowsizereduction').val( (propvalu ? 'true' : 'false') );
						break;
					case "fadeInOut":
						$('#fadeinout').val( ( propvalu ? 'true' : 'false' ) );
						break;
					case "outlineWhileAnimating":
						$('#outlinewhileanimating').val( (propvalu ? 'true' : 'false') );
						break;
					case "outlineType":
						if (propvalu == null)
						{
							$('#outlinetype').val( 'no-border' );
						}
						else
						{
							$('#outlinetype').val( propvalu );
						}
						break;
					case "minWidth":
						$('#minwidth').val( propvalu );
						break;
					case "minHeight":
						$('#minheight').val( propvalu );
						break;
					case "targetX":
						$('#targetx').val( propvalu );
						break;
					case "targetY":
						$('#targety').val( propvalu );
						break;
					case "wrapperClassName":
						$('#wrapperclass').val( propvalu );
						break;
					case "thumbnailId":
						$('#thumbnailid').val( propvalu );
						break;
					case "contentId":
						$('#contentid').val( propvalu );
						break;
					case "slideshowGroup":
						$('#slideshowgroup').val( propvalu );
						break;
					case "src":
						$('#psrc').val( propvalu );
						break;
					case "width":
						$('#width').val( propvalu );
						break;
					case "height":
						$('#height').val( propvalu );
						break;
					case "allowWidthReduction":
						$('#allowwidthreduction').val( (propvalu ? 'true' : 'false') );
						break;
					case "allowHeightReduction":
						$('#allowheightwidthreduction').val( (propvalu ? 'true' : 'false') );
						break;
					case "objectType":
						$('#objecttype').val( propvalu );
						break;
					case "objectWidth":
						$('#objectwidth').val( propvalu );
						break;
					case "objectHeight":
						$('#objectheight').val( propvalu );
						break;
					case "preserveContent":
						$('#preservecontent').val( (propvalu ? 'true' : 'false') );
						break;
					case "cacheAjax":
						$('#cacheajax').val( (propvalu ? 'true' : 'false') );
						break;
					case "objectLoadTime":
						$('#objectloadtime').val( propvalu );
						break;
					case "swfOptions":
						this.setSwfOptions( propvalu );
						break;
					/* the following overlay elements remain here for compatability with previous version */
					case "overlayId":
						$('#overlayid').val( propvalu );
						break;
					case "fade":
						$('#ovfade').val( propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									$('#ovvposition').val( posar[i] );
									break;
								case 'horizontal':
									$('#ovhposition').val( posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						$('#ovhideonmouseout').val( (propvalu ? 'true' : 'false') );
						break;
					case "opacity":
						$('#ovopacity').val( propvalu );
						break;
					/* end of compatability elements */

					case "dragByHeading":
						$('#dragbyheading').val( ( propvalu ? 'true' : 'false' ) );
						break;
					case "numberPosition":
						if (propvalu == null)
						{
							$('#numberposition').val( 'null' );
						}
						else
						{
							$('#numberposition').val( propvalu );
						}
						break;
					case "dimmingOpacity":
						$('#dimmingopacity').val( propvalu );
						break;
					case "captionId":
						$('#captionid').val( propvalu );
						break;
					case "hsjcaption":
						$('#caption').val( unescape( propvalu ));
						break;
					case "hsjcaptionstyle":
						$('#captionstyle').val( propvalu );
						break;
					case "headingId":
						$('#headingid').val( propvalu );
						break;
					case "hsjheading":
						$('#heading').val( unescape( propvalu ));
						break;
					case "hsjheadingstyle":
						$('#headingstyle').val( propvalu );
						break;
					case "creditsPosition":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									$('#crvposition').val( posar[i] );
									break;
								case 'horizontal':
									$('#crhposition').val( posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "transitions":
						var str = "";
						var cm = "";
						for ( var i = 0; i < propvalu.length; i++ )
						{
							str += cm + "'" + propvalu[i] + "'";
							cm = ", ";
						}
						$('#transitions').val( str );
						break;
					case "captionText":
						$('#captiontext').val( propvalu );
						break;
					case "headingText":
						$('#headingtext').val( propvalu );
						break;
					case "captionOverlay":
						$('#coenableoverlay').attr('checked', true );
						this.setCaptionOverlayValues( propvalu );
						break;
					case "headingOverlay":
						$('#hoenableoverlay').attr('checked', true );
						this.setHeadingOverlayValues( propvalu );
						break;
					case "customOverlay":
						this.setCustomOverlayValues( propvalu );
						break;
					case "hsjcustomOverlay":
						$( '#overlay').val( unescape( propvalu ));
						break;
					case "hsjcustomOverlayStyle":
						$( '#overlaystyle').val( propvalu );
						break;
					case "hsjcontent":
						$( '#content').val( unescape( propvalu ));
						break;
					case "hsjcontentstyle":
						$( '#contentstyle').val( propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	setSwfOptions : function( propobj ) {
		for ( var prop in propobj )
		{
			var propvalu = propobj[prop];
			switch( prop )
			{
				case "version":
					$('#swfversion').val( propvalu );
					break;
				case "expressInstallSwfurl":
					$('#swfexpressinstallurl').val( propvalu );
					break;
				case "flashvars":
					$('#swfflashvars').val( this.dumpSwfVars( propvalu ));
					break;
				case "params":
					$('#swfparams').val( this.dumpSwfVars( propvalu ));
					break;
				case "attributes":
					$('#swfattributes').val( this.dumpSwfVars( propvalu ));
					break;
				default:
					break;
			}
		}
	},

	dumpSwfVars : function( propobj ) {
		var vars = "";
		var $c = "";

		for (var prop in propobj )
		{
			if (typeof prop != 'object')
			{
				vars += $c + prop + ": '" + propobj[prop] + "'"
				$c = ", ";
			}
		}
		return vars;
	},

	setCaptionOverlayValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "fade":
						$('#cofade').val( propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									$( '#covposition').val( posar[i] );
									break;
								case 'horizontal':
									$( '#cohposition').val( posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						$('#cohideonmouseout').val( (propvalu ? 'true' : 'false') );
						break;
					case "opacity":
						$('#coopacity').val( propvalu );
						break;
					case "width":
						$('#cowidth').val( propvalu );
						break;
					case "offsetX":
						$('#cooffsetx').val( propvalu );
						break;
					case "offsetY":
						$('#cooffsety').val( propvalu );
						break;
					case "relativeTo":
						$('#corelativeto').val( propvalu );
						break;
					case "className":
						$('#coclassname').val( propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	setHeadingOverlayValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "fade":
						$('#hofade').val( propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									$( '#hovposition').val( posar[i] );
									break;
								case 'horizontal':
									$( '#hohposition').val( posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						$('#hohideonmouseout').val( (propvalu ? 'true' : 'false') );
						break;
					case "opacity":
						$('#hoopacity').val( propvalu );
						break;
					case "width":
						$('#howidth').val( propvalu );
						break;
					case "offsetX":
						$('#hooffsetx').val( propvalu );
						break;
					case "offsetY":
						$('#hooffsety').val( propvalu );
						break;
					case "relativeTo":
						$('#horelativeto').val( propvalu );
						break;
					case "className":
						$('#hoclassname').val( propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	setCustomOverlayValues : function( propobj ) {
		for ( var prop in propobj )
		{
			if (typeof prop == 'object')
			{
				this.setValues( prop );
			}
			else
			{
				var propvalu = propobj[prop];
				switch( prop )
				{
					case "fade":
						$('#ovfade').val( propvalu );
						break;
					case "position":
						var posar = propvalu.split( ' ' );
						for ( var i = 0; i < posar.length; i++ )
						{
							switch( this.positionType( posar[i] ))
							{
								case 'vertical':
									$( '#ovvposition').val( posar[i] );
									break;
								case 'horizontal':
									$( '#ovhposition').val( posar[i] );
									break;
								default:
									break;
							} // switch
						}
						break;
					case "hideOnMouseOut":
						$('#ovhideonmouseout').val( (propvalu ? 'true' : 'false') );
						break;
					case "opacity":
						$('#ovopacity').val( propvalu );
						break;
					case "width":
						$('#ovwidth').val( propvalu );
						break;
					case "offsetX":
						$('#ovoffsetx').val( propvalu );
						break;
					case "offsetY":
						$('#ovoffsety').val( propvalu );
						break;
					case "relativeTo":
						$('#ovrelativeto').val( propvalu );
						break;
					case "className":
						$('#ovclassname').val( propvalu );
						break;
					default:
						break;
				}
			}
		}
	},

	positionType : function( position )
	{
		switch( position )
		{
			case 'above':
			case 'top':
			case 'middle':
			case 'bottom':
			case 'below':
				return "vertical";
				break;
			case 'leftpanel':
			case 'left':
			case 'center':
			case 'right':
			case 'rightpanel':
				return "horizontal";
				break;
			default:
				break;
		}
		return "";
	},

	buildHsOnMouseOver : function(){
		if ($('#openonhover').is(':checked'))
		{
			$('#onmouseover').val( 'return this.onclick()' );
		}
		else
		{
			$('#onmouseover').val( '' );
		}
	},

	buildHsOnClick : function(){
		var ed = tinyMCEPopup.editor;
		var onclick = "return hs.htmlExpand(this";
		var onclickopts = "";
		var onclickswfopts = "";
		var v;

		if ((v=$('#align').val()) != "")
		{
			onclickopts += "align: ";
			onclickopts += "'" + v + "'";
		}
		if ((v=$('#anchor').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "anchor: '" + v + "'";
		}
		if ((v=$('#easing').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "easing: '" + v + "'";
		}
		if ((v=$('#easingclose').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "easingClose: '" + v + "'";
		}
		if ((v=$('#allowsizereduction').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "allowSizeReduction: " + v;
		}
		if ((v=$('#fadeinout').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "fadeInOut: " + v;
		}
		if ((v=$('#dragbyheading').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "dragByHeading: " + v;
		}
		if ((v=$('#numberposition').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			if (v == 'null')
			{
				onclickopts += "numberPosition: " + v;
			}
			else
			{
				onclickopts += "numberPosition: '" + v + "'";
			}
		}
		if ((v=$('#outlinewhileanimating').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "outlineWhileAnimating: " + v;
		}
		if ((v=$('#outlinetype').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			if (v == 'no-border')
			{
				onclickopts += "outlineType: null";
			}
			else
			{
				onclickopts += "outlineType: '" + v + "'";
			}
		}
		if ((v=$('#minwidth').val()) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "minWidth: " + v;
		}
		if ((v=$('#minheight').val()) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "minHeight: " + v;
		}
		if ((v=$('#targetx').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "targetX: '" + v + "'";
		}
		if ((v=$('#targety').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "targetY: '" + v + "'";
		}
		if ((v=$('#wrapperclass').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "wrapperClassName: '" + v + "'";
		}
		if ((v=$('#thumbnailid').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "thumbnailId: '" + v + "'";
		}
		if ((v=$('#contentid').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "contentId: '" + v + "'";
			if ($('#objecttype').val() == 'ajax')
			{
				$( '#cacheajax').val( 'false' );
			}
		}
		if ((v=$('#content').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcontent: '" + this.escapeText(v) + "'";
		}
		if ((v=$('#contentstyle').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcontentstyle: '" + v + "'";
		}
		if ((v=$('#slideshowgroup').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "slideshowGroup: '" + v + "'";
		}
		if ((v=$('#psrc').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			v	= new tinymce.util.URI(ed.getParam('document_base_url')).toAbsolute(v,true);
			onclickopts += "src: '" + v + "'";
		}
		if ((v=$('#width').val()) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "width: " + v;
		}
		if ((v=$('#height').val()) != ""  && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "height: " + v;
		}
		if ((v=$('#allowwidthreduction').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "allowWidthReduction: " + v;
		}
		if ((v=$('#allowheightreduction').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "allowHeightReduction: " + v;
		}
		if ((v=$('#objecttype').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectType: '" + v + "'";
		}
		if ((v=$('#objectwidth').val()) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectWidth: " + v;
		}
		if ((v=$('#objectheight').val()) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectHeight: " + v;
		}
		if ((v=$('#preservecontent').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "preserveContent: " + v;
		}
		if ((v=$('#cacheajax').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "cacheAjax: " + v;
		}
		if ((v=$('#objectloadtime').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "objectLoadTime: '" + v + "'";
		}
		if ((v=$('#dimmingopacity').val()) != ""  && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "dimmingOpacity: " + v;
		}
		if ($('#crvposition').val() != "" || $('#crhposition').val() != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "creditsPosition: '";
			var spc = "";
			if ((v=$('#crvposition').val()) != "")
			{
				onclickopts += v;
				spc = " ";
			}
			if ((v=$('#crhposition').val()) != "")
			{
				onclickopts += spc + v;
			}
			onclickopts += "'";
		}
		if ((v=$('#transitions').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "transitions: [ " + v + " ]";
		}
		if ((v=$('#captiontext').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "captionText: '" + v + "'";
		}
		if ((v=$('#headingtext').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "headingText: '" + v + "'";
		}
		if ((v=$('#captionid').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "captionId: '" + v + "'";
		}
		if ((v=$('#caption').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcaption: '" + this.escapeText(v) + "'";
		}
		if ((v=$('#captionstyle').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcaptionstyle: '" + v + "'";
		}
		if ($('#coenableoverlay').is(':checked'))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "captionOverlay: {";
			onclickopts += " fade: ";
			if ((v=$('#cofade').val()) != "")
			{
				onclickopts += v;
			}
			else
			{
				onclickopts += 0;
			}
			if ($('#covposition').val() != "" || $('#cohposition').val() != "")
			{
				onclickopts += ", position: '";
				var spc = "";
				if ((v=$('#covposition').val()) != "")
				{
					onclickopts += v;
					spc = " ";
				}
				if ((v=$('#cohposition').val()) != "")
				{
					onclickopts += spc + v;
				}
				onclickopts += "'";
			}

			if ((v=$('#cohideonmouseout').val()) != "")
			{
				onclickopts += ", hideOnMouseOut: ";
				onclickopts += v;
			}

			if ((v=$('#coopacity').val()) != "" && !isNaN(v))
			{
				onclickopts += ", opacity: ";
				onclickopts += v;
			}
			if ((v=$('#cowidth').val()) != "")
			{
				onclickopts += ", width: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=$('#cooffsetx').val()) != "" && !isNaN(v))
			{
				onclickopts += ", offsetX: ";
				onclickopts += v;
			}
			if ((v=$('#cooffsety').val()) != "" && !isNaN(v))
			{
				onclickopts += ", offsetY: ";
				onclickopts += v;
			}
			if ((v=$('#corelativeto').val()) != "")
			{
				onclickopts += ", relativeTo: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=$('#coclassname').val()) != "")
			{
				onclickopts += ", className: ";
				onclickopts += "'" + v + "'";
			}
			onclickopts += " }";
		}

		if ((v=$('#headingid').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "headingId: '" + v + "'";
		}
		if ((v=$('#heading').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjheading: '" + this.escapeText(v) + "'";
		}
		if ((v=$('#headingstyle').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjheadingstyle: '" + v + "'";
		}
		if ($('#hoenableoverlay').is(':checked'))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "headingOverlay: {";
			onclickopts += " fade: ";
			if ((v=$('#hofade').val()) != "")
			{
				onclickopts += v;
			}
			else
			{
				onclickopts += 0;
			}
			if ($('#hovposition').val() != "" || $('#hohposition').val() != "")
			{
				onclickopts += ", position: '";
				var spc = "";
				if ((v=$('#hovposition').val()) != "")
				{
					onclickopts += v;
					spc = " ";
				}
				if ((v=$('#hohposition').val()) != "")
				{
					onclickopts += spc + v;
				}
				onclickopts += "'";
			}

			if ((v=$('#hohideonmouseout').val()) != "")
			{
				onclickopts += ", hideOnMouseOut: ";
				onclickopts += v;
			}

			if ((v=$('#hoopacity').val()) != "" && !isNaN(v))
			{
				onclickopts += ", opacity: ";
				onclickopts += v;
			}
			if ((v=$('#howidth').val()) != "")
			{
				onclickopts += ", width: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=$('#hooffsetx').val()) != "" && !isNaN(v))
			{
				onclickopts += ", offsetX: ";
				onclickopts += v;
			}
			if ((v=$('#hooffsety').val()) != "" && !isNaN(v))
			{
				onclickopts += ", offsetY: ";
				onclickopts += v;
			}
			if ((v=$('#horelativeto').val()) != "")
			{
				onclickopts += ", relativeTo: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=$('#hoclassname').val()) != "")
			{
				onclickopts += ", className: ";
				onclickopts += "'" + v + "'";
			}
			onclickopts += " }";
		}

		if ((v=$('#overlay').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcustomOverlay: '" + this.escapeText(v) + "'";
		}
		if ((v=$('#overlaystyle').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "hsjcustomOverlayStyle: '" + v + "'";
		}
		if ((v=$('#overlayid').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "overlayId: '" + v + "'";

			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "customOverlay: {";
			onclickopts += " useOnHtml: true";
			if ((v=$('#ovfade').val()) != "")
			{
				onclickopts += ",fade: ";
				onclickopts += v;
			}
			if ($('#ovvposition').val() != "" || $('#ovhposition').val() != "")
			{
				onclickopts += ", position: '";
				var spc = "";
				if ((v=$('#ovvposition').val()) != "")
				{
					onclickopts += v;
					spc = " ";
				}
				if ((v=$('#ovhposition').val()) != "")
				{
					onclickopts += spc + v;
				}
				onclickopts += "'";
			}

			if ((v=$('#ovhideonmouseout').val()) != "")
			{
				onclickopts += ", hideOnMouseOut: ";
				onclickopts += v;
			}

			if ((v=$('#ovopacity').val()) != "" && !isNaN(v))
			{
				onclickopts += ", opacity: ";
				onclickopts += v;
			}
			if ((v=$('#ovwidth').val()) != "")
			{
				onclickopts += ", width: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=$('#ovoffsetx').val()) != "" && !isNaN(v))
			{
				onclickopts += ", offsetX: ";
				onclickopts += v;
			}
			if ((v=$('#ovoffsety').val()) != "" && !isNaN(v))
			{
				onclickopts += ", offsetY: ";
				onclickopts += v;
			}
			if ((v=$('#ovrelativeto').val()) != "")
			{
				onclickopts += ", relativeTo: ";
				onclickopts += "'" + v + "'";
			}
			if ((v=$('#ovclassname').val()) != "")
			{
				onclickopts += ", className: ";
				onclickopts += "'" + v + "'";
			}
			onclickopts += " }";
		}

		if ( $('#swfversion').val() != ""
		   ||$('#swfexpressinstallurl').val() != ""
		   ||$('#swfflashvars').val() != ""
		   ||$('#swfparams').val() != ""
		   ||$('#swfattributes').val() != ""
		   )
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "swfOptions: {";

			if ((v=$('#swfversion').val()) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "version: '" + v + "'";
			}
			if ((v=$('#swfexpressinstallurl').val()) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "expressInstallSwfurl: '" + v + "'";
			}

			if ((v=$('#swfflashvars').val()) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "flashvars: { " + this.filterVars(v) + "} ";
			}
			if ((v=$('#swfparams').val()) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "params: { " + this.filterVars(v) + "} ";
			}
			if ((v=$('#swfattributes').val()) != "")
			{
				onclickswfopts += (onclickswfopts.length > 0) ? ", " : "";
				onclickswfopts += "attributes: { " + this.filterVars(v) + " }";
			}
			onclickopts += onclickswfopts + " }";
		}

		if (onclickopts != "")
			onclick += ",{" + onclickopts + "}";

		onclick += ")"
		$('#onclick').val( onclick );
	},

	filterVars : function( vars )
	{
		var arr = vars.match(/([^ :]*):\s*'([^\']*)'/g );
		var c = "";
		var str = "";
		for (var i = 0; i < arr.length; i++)
		{
			str += c + arr[i];
			c = ", ";
		}
		return str;
	},

	escapeText : function( text )
	{
		if ( /[&<>'\\"%\n\r]/.test( text ))
		{
			return escape(text);
		}
		return text;
	},

	insert : function(){
		var ed = tinyMCEPopup.editor, el = null, elementArray, i, args = {};
		var n = ed.selection.getNode(), br = '';
		var hsrel = '';

		if ($('#unobtrusive').is(':checked'))
		{
			hsrel = 'highslide-' + $('#objecttype' ).val();
			$('#onclick').val( '' );
			$('#onmouseover').val( '' );
		}
		else
		{
			this.buildHsOnClick();
			this.buildHsOnMouseOver();
		}

		tinymce.extend(args, {
			href 		: $('#href').val(),
			title 		: $('#title').val(),
			id 			: $('#id').val(),
			style 		: $('#style').val(),
			'class' 	: 'highslide',
			rel         : hsrel,
			onclick 	: $('#onclick').val(),
			'data-mce-onclick' 	: $('#onclick').val(),
			onmouseover : $('#onmouseover').val()
		});

		el = ed.dom.getParent(ed.selection.getNode(), "A");
		tinyMCEPopup.execCommand("mceBeginUndoLevel");

		// Create new anchor elements
		if (el == null)
		{
			tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#");
			elementArray = tinymce.grep(ed.dom.select("a"), function(n) {return ed.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
			for (i=0; i<elementArray.length; i++)
			{
				el = elementArray[i];

				// Move cursor to end
				try
				{
					tinyMCEPopup.editor.selection.collapse(false);
				}
				catch (ex)
				{
					// Ignore
				}
				ed.dom.setAttribs(el, args);
			}
		}
		else
		{
			ed.dom.setAttribs(el, args);
		}
		if (ed.dom.getAttrib(el, 'data-mce-onclick') != "")
		{
			ed.dom.setAttrib( el, 'data-mce-onclick', null );
		}

		if ($('#contentid').val() != '') {
			var divobj = ed.dom.get($('#contentid').val());
			if (divobj != null && divobj.nodeName == "DIV" && ed.dom.getAttrib( divobj, 'class') == 'highslide-html-content') {
				var contentid = $('#contentid' ).val();
				ed.dom.remove( contentid );
			}
		}
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
	},


    openHelp : function() {
        $.Plugin.help('hshtmlexpander');
    }
}

HsHtmlExpanderDialog.preInit();
tinyMCEPopup.onInit.add(HsHtmlExpanderDialog.init, HsHtmlExpanderDialog);