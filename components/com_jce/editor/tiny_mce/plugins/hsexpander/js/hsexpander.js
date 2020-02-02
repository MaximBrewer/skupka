var HsExpanderDialog = {

    settings : {},

	preInit : function() {
		tinyMCEPopup.requireLangPack();
	},

	init : function() {
		var self = this, ed = tinyMCEPopup.editor, se = ed.selection, action = 'insert', rel = "", el;

    	$('button#insert').click(function(e) {
    		self.insert();
    		e.preventDefault();
    	});

    	$('button#help').click(function(e) {
    		self.openHelp();
    		e.preventDefault();
    	});

		tinyMCEPopup.resizeToInnerSize();

		n = se.getNode();

		var im = null;

		if (n != null && n.nodeName == 'IMG') {
			im = n;
		}
		el = ed.dom.getParent(n, 'A');
		if (el != null && el.nodeName == 'A'){
			action = "update";
		}

		// Init plugin
		$.Plugin.init();


		var target = '';/*HsExpander.getParam('target');*/
		if(target == 'default') target = '';

		if (action == "update")
		{
			$('#insert').button('option','label',tinyMCEPopup.getLang('update','Update',true));
			var href=decodeURIComponent(ed.convertURL(ed.dom.getAttrib(el,'href')));
			// Setup form data
			$('#href').val(href);
			$('#title').val(ed.dom.getAttrib( el, 'title' ));
			$('#id').val(ed.dom.getAttrib( el, 'id' ));
			$('#style').val(ed.dom.getAttrib( el, 'style' ));
			$('#onclick').val(ed.dom.getAttrib( el, 'onclick' ));
			if ($('#onclick').val() == "")
			{
				$('#onclick').val( ed.dom.getAttrib(el,'data-mce-onclick'));
			}
			$('#onmouseover').val(ed.dom.getAttrib( el, 'onmouseover' ));
			rel = ed.dom.getAttrib(el,'rel');
			if (rel == 'highslide')
			{
				$('#unobtrusive').attr('checked', true );
			}
		}
		else
		{
			$.Plugin.setDefaults(this.settings.defaults)
		}

		if (im != null) {
			$('#src').val( ed.dom.getAttrib(im,'src'));
			$('#imgtitle').val( ed.dom.getAttrib( im,'title' ));
			$('#imgstyle').val( ed.dom.getAttrib( im,'style' ));
			$('#imgclass').val( ed.dom.getAttrib( im,'class' ));
			$('#imgid').val( ed.dom.getAttrib( im,'id' ));
			$('#thumbid').val( ed.dom.getAttrib( im,'id' ));
			$('#alt').val( ed.dom.getAttrib( im,'alt' ));
			$('#height').val( ed.dom.getAttrib( im,'height' ));
			$('#width').val( ed.dom.getAttrib( im,'width' ));
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
				   || rel == 'highslide-iframe'
				   || rel == 'highslide-ajax'
				   || rel == 'highslide-swf'
				   )
				{
					$.Dialog.alert(tinyMCEPopup.getLang('hsexpander_dlg.is_htmlexpander', 'This expander was created by the HsHtmlExpander plugin. It must be used for updates.'));
					$('#insert').button('disable');
					return;
				}
			}
		}

		if ($('#captionid').val() != '') {
			var divobj = ed.dom.get($('#captionid').val());
			if (divobj != null && divobj.nodeName == 'DIV' && ed.dom.getAttrib( divobj, 'class') == 'highslide-caption') {
				$('#caption').val( divobj.innerHTML );
				$('#captionstyle').val( ed.dom.getAttrib( divobj, 'style' ));
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
			$('#tabs').tabs('disable', 1 );
			$('#tabs').tabs('disable', 2 );
			$('#tabs').tabs('disable', 3 );
			$('#tabs').tabs('disable', 4 );
		}
		else
		{
			$('#tabs').tabs('enable', 1 );
			$('#tabs').tabs('enable', 2 );
			$('#tabs').tabs('enable', 3 );
			$('#tabs').tabs('enable', 4 );
		}
	},

	setHsValues : function(){
		var onclick = $('#onclick').val();
		var onmouseover = $('#onmouseover').val();

		if (onclick != null && onclick.indexOf('return hs.expand') != -1)
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
		if ( onclick != null && onclick.indexOf( 'return hs.htmlExpand' ) != -1)
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
					case "outlineWhileAnimating":
						$('#outlinewhileanimating').val( (propvalu ? 'true' : 'false') );
						break;
					case "useBox":
						$('#usebox').val( (propvalu ? 'true' : 'false') );
						break;
					case "autoplay":
						$('#autoplay').val( (propvalu ? 'true' : 'false') );
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
					case "src":
						$('#psrc').val( propvalu );
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
					case "customOverlayId":
						$('#overlayid').val( propvalu );
						break;
					case "hsjcustomOverlay":
						$('#overlay').val( unescape( propvalu ));
						break;
					case "hsjcustomOverlayStyle":
						$('#overlaystyle').val( propvalu );
						break;
					case "slideshowGroup":
						$('#slideshowgroup').val( propvalu );
						break;
					case "thumbnailId":
						$('#thumbnailid').val( propvalu );
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

					case "width":
						$('#cbwidth').val( propvalu );
						break;
					case "height":
						$('#cbheight').val( propvalu );
						break;
					case "dimmingOpacity":
						$('#dimmingopacity').val( propvalu );
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
					default:
						break;
				}
			}
		}
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
									$('#covposition').val( posar[i] );
									break;
								case 'horizontal':
									$('#cohposition').val(  posar[i] );
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
									$('#hovposition').val( posar[i] );
									break;
								case 'horizontal':
									$('#hohposition').val( posar[i] );
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
		var onclick = "return hs.expand(this";
		var onclickopts = "";
		var onclickcustomopts = "";
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
		if ((v=$('#outlinewhileanimating').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "outlineWhileAnimating: " + v;
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
		if ((v=$('#autoplay').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "autoplay: " + v;
		}
		if ((v=$('#usebox').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "useBox: " + v;
		}
		if ((v=$('#cbwidth').val()) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "width: " + v;
		}
		if ((v=$('#cbheight').val()) != "" && !isNaN(v))
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "height: " + v;
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
		if ((v=$('#psrc').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			v	= new tinymce.util.URI(ed.getParam('document_base_url')).toAbsolute(v,true);
			onclickopts += "src: '" + v + "'";
		}
		if ((v=$('#slideshowgroup').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "slideshowGroup: '" + v + "'";
		}
		if ((v=$('#thumbnailid').val()) != "")
		{
			onclickopts += (onclickopts.length > 0) ? ", " : "";
			onclickopts += "thumbnailId: '" + v + "'";
		}
		if ((v=$('#dimmingopacity').val()) != "" && !isNaN(v))
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
			onclickopts += " fade: ";
			if ((v=$('#ovfade').val()) != "")
			{
				onclickopts += v;
			}
			else
			{
				onclickopts += 0;
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

		if (onclickopts != "")
			onclick += ",{" + onclickopts + "}";

		onclick += ")"
		$('#onclick').val( onclick );
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
		var n = ed.selection.getNode(), imargs = {}, br = '';
		var hsrel = '';

		if ($('#unobtrusive').is(':checked'))
		{
			hsrel = 'highslide';
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

		if ($('#alt').val() == "")
		{
			$('#alt').val( 'Thumbnail image' );
		}
		tinymce.extend(imargs, {
			src 		: $('#src').val(),
			title 		: $('#imgtitle').val(),
			'class'		: $('#imgclass').val(),
			id 			: $('#imgid').val(),
			style 		: $('#imgstyle').val(),
			alt			: $('#alt').val(),
			height		: $('#height').val(),
			width		: $('#width').val()
		});

		n = ed.selection.getNode();
		if (n)
		{
			el = ed.dom.getParent(n, "A");
		}
		tinyMCEPopup.execCommand("mceBeginUndoLevel");
		if (n && n.nodeName == 'IMG')
		{
			//	update img
			ed.dom.setAttribs(n, imargs);
		}
		else
		if (n && ed.selection.getContent() != "")
		{
			// update/insert a link
		}
		else
		if (el != null)
		{
			// update the link
		}
		else
		{
			var content = '<a href=#mce_temp_url#><img id="__mce_tmp" src="javascript:;" /></a>';

			//	insert anchor/img
			ed.execCommand('mceInsertContent', false, content);
			elementArray = tinymce.grep(ed.dom.select("img"), function(n) {return ed.dom.getAttrib(n, 'id') == '__mce_tmp';});
			for (i=0; i<elementArray.length; i++)
			{
				n = elementArray[i];

				// Make active selection
				try
				{
					tinyMCEPopup.editor.selection.collapse(false);
				}
				catch (ex)
				{
					// Ignore
				}
				ed.dom.setAttribs( n, imargs);
			}
			el = ed.dom.getParent(n, "A");
		}

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
		if ($('#captionid').val() != '') {
			var divobj = ed.dom.get($('#captionid').val());
			if (divobj != null && divobj.nodeName == "DIV" && ed.dom.getAttrib( divobj, 'class') == 'highslide-caption') {
				var captionid = $('#captionid').val();
				ed.dom.remove( captionid );
			}
		}

		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
	},

    openHelp : function() {
        $.Plugin.help('hsexpander');
    }
}

HsExpanderDialog.preInit();
tinyMCEPopup.onInit.add(HsExpanderDialog.init, HsExpanderDialog);