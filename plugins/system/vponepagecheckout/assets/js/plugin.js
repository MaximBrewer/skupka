/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 4 $
 * $LastChangedDate: 2017-09-20 20:02:29 +0530 (Wed, 20 Sep 2017) $
 * $Id: plugin.js 4 2017-09-20 14:32:29Z Abhshek Das $
 * --------------------------------------------------------------------------------------
 */
VPOPC = window.VPOPC || {};

ProOPC = window.ProOPC || {};

ProOPC.jQuery = jQuery.noConflict();

(function($, win, doc) {
	ProOPC.ajaxPool = {};
	
	ProOPC.cache = {};

	VPOPC.options = {};

	VPOPC.loadOptions = function(options) {
		if (!options) {
			var elements = document.querySelectorAll('.vpopc-script-options.new'),
				str, element, option;

			for (var i = 0, l = elements.length; i < l; i++) {
				element = elements[i];
				str     = element.text || element.textContent;
				option  = JSON.parse(str);

				option ? VPOPC.loadOptions(option) : null;

				element.className = element.className.replace(' new', ' loaded');
			}

			return;
		}
		
		if($.isEmptyObject(VPOPC.options)) {
			for (var t in options) {
				this.options[t] = options[t];
			}
		} else {
			for (var t in options) {
				if(options.hasOwnProperty(t)) {
					this.options[t] = options[t];
				}
			}
		}

		return this;
	};
	
	VPOPC.setOption = function(option, value) {
		this.options[option] = value;
	};

	VPOPC._ = function(e, t) {
		if($.isEmptyObject(VPOPC.options)) {
			VPOPC.loadOptions();
		}
		t = (typeof t === typeof undefined) || (typeof t === 'undefined') ? false : t;
		return (typeof this.options[e.toUpperCase()] !== 'undefined') && (typeof this.options[e.toUpperCase()] !== typeof undefined) ? this.options[e.toUpperCase()] : t;
	}

	VPOPC.JText = {
		strings: {},
		_: function(e, t) {
			if($.isEmptyObject(this.strings)) {
				VPOPC.JText.load();
			}
			t = (typeof t === typeof undefined) || (typeof t === 'undefined') ? null : t;
			return (typeof this.strings[e.toUpperCase()] !== 'undefined') && (typeof this.strings[e.toUpperCase()] != typeof undefined) ? this.strings[e.toUpperCase()] : t;
		},
		sprintf: function(e, t) {
			if($.isEmptyObject(this.strings)) {
				VPOPC.JText.load();
			}
			t = (typeof t === typeof undefined) || (typeof t === 'undefined') ? null : t;
			var n = (typeof this.strings[e.toUpperCase()] !== 'undefined') && (typeof this.strings[e.toUpperCase()] != typeof undefined) ? this.strings[e.toUpperCase()] : '';
			return n.replace('%s', t);
		},
		load: function(e) {
			if (!e) {
				var elements = document.querySelectorAll('.vpopc-script-strings.new'),
					str, element, option;

				for (var i = 0, l = elements.length; i < l; i++) {
					element = elements[i];
					str     = element.text || element.textContent;
					option  = JSON.parse(str);

					option ? VPOPC.JText.load(option) : null;

					element.className = element.className.replace(' new', ' loaded');
				}

				return;
			}
			
			if($.isEmptyObject(this.strings)) {
				for (var t in e) {
					this.strings[t.toUpperCase()] = e[t];
				}
			} else {
				for (var t in e) {
					if(e.hasOwnProperty(t)) {
						this.strings[t.toUpperCase()] = e[t];
					}
				}
			}
			
			return this;
		}
	};

	ProOPC.init = function(firstLoad) {
		firstLoad = ProOPC.hasValue(firstLoad);

		if (firstLoad && navigator.userAgent.match(/IEMobile\/10\.0/)) {
			var msViewportStyle = document.createElement("style");
			msViewportStyle.appendChild(
				document.createTextNode(
					"@-ms-viewport{width:auto!important}"
				));
			document.getElementsByTagName("head")[0].appendChild(msViewportStyle);
		}

		$('#ProOPC .proopc-btn').each(function() {
			var onclick = $(this).attr('onclick'),
				id = $(this).attr('id'),
				id = id ? $.trim(id) : '',
				ready = $(this).data('proopcReady');

			if (onclick && onclick.indexOf('ProOPC.') >= 0 && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					if (onclick.indexOf('updateproductqty') >= 0) {
						return ProOPC.updateproductqty(this);
					} else if (onclick.indexOf('deleteproduct') >= 0) {
						return ProOPC.deleteproduct(this);
					} else if (onclick.indexOf('guestcheckout') >= 0) {
						return ProOPC.guestcheckout(this);
					} else if (onclick.indexOf('registerCheckout') >= 0) {
						return ProOPC.registerCheckout(this);
					} else if (onclick.indexOf('loginAjax') >= 0) {
						return ProOPC.loginAjax(this);
					} else if (onclick.indexOf('submitOrder') >= 0) {
						return ProOPC.submitOrder(this);
					} else if (onclick.indexOf('savecoupon') >= 0) {
						return ProOPC.savecoupon(this);
					}
				}).data('proopcReady', true).removeAttr('onclick');

				if (firstLoad || onclick.indexOf('submitOrder') < 0) {
					$(this).removeAttr('disabled');
				}

				ready = $(this).data('proopcReady');
			}

			if (id == 'proopc-task-guestcheckout' && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					return ProOPC.guestcheckout(this);
				}).data('proopcReady', true).removeAttr('disabled');
			} else if (id == 'proopc-task-registercheckout' && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					return ProOPC.registerCheckout(this);
				}).data('proopcReady', true).removeAttr('disabled');
			} else if (id == 'proopc-task-loginajax' && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					return ProOPC.loginAjax(this);
				}).data('proopcReady', true).removeAttr('disabled');
			} else if (id == 'proopc-order-submit' && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					return ProOPC.submitOrder(this);
				}).data('proopcReady', true);
				if (firstLoad) {
					$(this).removeAttr('disabled');
				}
			} else if (id == 'proopc-task-savecoupon' && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					return ProOPC.savecoupon(this);
				}).data('proopcReady', true).removeAttr('disabled');
			} else if ($(this).hasClass('proopc-task-updateqty') && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					return ProOPC.updateproductqty(this);
				}).data('proopcReady', true).removeAttr('disabled');
			} else if ($(this).hasClass('proopc-task-deleteproduct') && !ready) {
				$(this).click(function(e) {
					e.preventDefault();
					return ProOPC.deleteproduct(this);
				}).data('proopcReady', true).removeAttr('disabled');
			}
		});

		var $btForm = $('form#EditBTAddres'),
			$stForm = $('form#EditSTAddres'),
			$shipmentForm = $('form#proopc-shipment-form'),
			$paymentForm = $('form#proopc-payment-form'),
			onchange,
			onclick;

		// For older versions of VirtueMart
		$('select#virtuemart_state_id', $btForm).attr('id', 'virtuemart_state_id_field');
		$('select#shipto_virtuemart_state_id', $stForm).attr('id', 'shipto_virtuemart_state_id_field');

		$('#city_field, #zip_field, #virtuemart_country_id_field, #virtuemart_state_id_field', $btForm).each(function() {
			if (!$(this).data('proopcReady')) {
				$(this).change(function() {
					ProOPC.updateBTaddress(this);
				}).data('proopcReady', true);
			}

			onchange = $(this).attr('onchange');
			if (onchange && onchange.indexOf('ProOPC') >= 0) {
				$(this).removeAttr('onchange');
			}
		});

		$('#shipto_city_field, #shipto_zip_field, #shipto_virtuemart_country_id_field, #shipto_virtuemart_state_id_field', $stForm).each(function() {
			if (!$(this).data('proopcReady')) {
				$(this).change(function() {
					ProOPC.updateSTaddress(this);
				}).data('proopcReady', true);
			}

			onchange = $(this).attr('onchange');
			if (onchange && onchange.indexOf('ProOPC') >= 0) {
				$(this).removeAttr('onchange');
			}
		});
		
		// Remember the original required attribute of state fields
		$('select#virtuemart_state_id_field, select#shipto_virtuemart_state_id_field').each(function() {
			if(($(this).hasClass('required') || $(this).attr('required')) && $(this).data('frequired') !== false) {
				$(this).data('frequired', true);
			} else if($(this).data('frequired') !== true) {
				$(this).data('frequired', false);
			}
		});
		
		$('select#proopc-select-st', $stForm).each(function() {
			if (!$(this).data('proopcReady')) {
				$(this).change(function() {
					ProOPC.selectSTAddress(this);
				}).data('proopcReady', true);
			}

			onchange = $(this).attr('onchange');
			if (onchange && onchange.indexOf('ProOPC') >= 0) {
				$(this).removeAttr('onchange');
			}
		});

		$('input[name="virtuemart_shipmentmethod_id"][type="radio"]', $shipmentForm).each(function() {
			if (!$(this).data('proopcReady')) {
				$(this).click(function() {
					return ProOPC.setshipment(this);
				}).data('proopcReady', true);
			}

			onclick = $(this).attr('onclick');
			if (onclick && onclick.indexOf('ProOPC') >= 0) {
				$(this).removeAttr('onclick');
			}
		});

		$('input[name="virtuemart_paymentmethod_id"][type="radio"]', $paymentForm).each(function() {
			if (!$(this).data('proopcReady')) {
				$(this).click(function() {
					return ProOPC.setpayment(this);
				}).data('proopcReady', true);
			}

			onclick = $(this).attr('onclick');
			if (onclick && onclick.indexOf('ProOPC') >= 0) {
				$(this).removeAttr('onclick');
			}
		});

		// Auto submit shipment data if a shipment method has select field and when that is changed
		$('select', $shipmentForm).each(function() {
			var $fieldSet = $(this).closest('fieldset');
			if ($fieldSet.length) {
				$(this).width($fieldSet.width());
			}

			if (!$(this).data('proopcReady')) {
				$(this).change(function() {
					var $selectedRadio = $('form#proopc-shipment-form').find('input[type="radio"][name="virtuemart_shipmentmethod_id"]:checked');
					if ($selectedRadio.length) {
						ProOPC.setshipment($selectedRadio);
					}
				}).data('proopcReady', true);
			}
		});

		// Auto submit payment data for the following payment methods and when their select field is changed.
		$('select', $paymentForm).each(function() {
			if (!$(this).data('proopcReady')) {
				$(this).change(function() {
					var $selectedRadio = $('form#proopc-payment-form').find('input[type="radio"][name="virtuemart_paymentmethod_id"]:checked'),
						pmtype = $selectedRadio.data('pmtype'),
						paymentsWithSelect = ['sisowideal', 'buckaroo', 'piraeus'];

					if ($selectedRadio.length && pmtype && $.inArray(pmtype, paymentsWithSelect) >= 0) {
						ProOPC.setpayment($selectedRadio);
					}
				}).data('proopcReady', true);
			}
		});

		var $methodRadio = $('input[name="proopc-method"][type="radio"]');
		if ($methodRadio.length && !$methodRadio.data('proopcReady')) {
			$methodRadio.change(function() {
				ProOPC.opcmethod();
			}).removeAttr('onchange').data('proopcReady', true);
		}

		var custom_bt_update_fields = VPOPC._('BT_UPDATE_FIELDS'),
			custom_st_update_fields = VPOPC._('ST_UPDATE_FIELDS');

		if (custom_bt_update_fields && $.type(custom_bt_update_fields) === 'array') {
			$.each(custom_bt_update_fields, function(key, name) {
				if (name) {
					var $field = $('[name="' + name + '"]', $btForm);

					if ($field.length && !$field.data('proopcReady')) {
						$field.change(function() {
							ProOPC.updateBTaddress(this);
						}).data('proopcReady', true);
					}

				}
			})
		}

		if (custom_st_update_fields && $.type(custom_st_update_fields) === 'array') {
			$.each(custom_st_update_fields, function(key, name) {
				if (name) {
					var $field = $('[name="' + name + '"]', $stForm);

					if ($field.length && !$field.data('proopcReady')) {
						$field.change(function() {
							ProOPC.updateSTaddress(this);
						}).data('proopcReady', true);
					}

				}
			})
		}

		$('input.datepicker-db:hidden').each(function() {
			if (!$(this).data('proopcDateReady')) {
				var that = this,
					dateInterval = $(that).data('dateInterval');
				if (dateInterval) clearInterval(dateInterval);
				dateInterval = setInterval(function() {
					if ($(that).val() != $(that).data('oldValue')) {
						$(that).data('oldValue', $(that).val()).trigger('change');
					}
				}, 200);

				$(that).data('dateInterval', dateInterval).data('proopcDateReady', true);
			}
		});
	};

	ProOPC.initSpinner = function() {
		var e = {
			lines: 13,
			length: 3,
			width: 2,
			radius: 5,
			corners: 1,
			rotate: 0,
			direction: 1,
			color: "#FFF",
			speed: 1.5,
			trail: 60,
			shadow: false,
			hwaccel: false,
			className: "proopc-spinner",
			zIndex: 2e9,
			top: "auto",
			left: "auto"
		};

		ProOPC.cache.spinner = ProOPC.cache.spinner || (new VPSpinner(e)).spin();

		var t = {
			lines: 10,
			length: 10,
			width: 4,
			radius: 15,
			corners: 1,
			rotate: 0,
			direction: 1,
			color: VPOPC._('SPINNER_COLOR'),
			speed: 1.5,
			trail: 60,
			shadow: false,
			hwaccel: true,
			className: "proopc-page-loader",
			zIndex: 2e9,
			top: 20,
			left: 14
		};

		ProOPC.cache.loader = ProOPC.cache.loader || (new VPSpinner(t)).spin();

		var n = {
			lines: 10,
			length: 5,
			width: 3,
			radius: 8,
			corners: 1,
			rotate: 0,
			direction: 1,
			color: VPOPC._('SPINNER_COLOR'),
			speed: 1.5,
			trail: 40,
			shadow: false,
			hwaccel: true,
			className: "proopc-area-loader",
			zIndex: 2e9,
			top: 20,
			left: 14
		};

		ProOPC.cache.area_loader = ProOPC.cache.area_loader || (new VPSpinner(n)).spin();
	};

	ProOPC.ajaxManager = (function() {
		var requests = [],
			continuous = false,
			running = false;

		return {
			tid: null,
			opt: {
				type: 'POST',
				dataType: 'JSON',
				url: null,
				cache: false
			},
			setContinuous: function(cont) {
				continuous = !cont ? false : true;
			},
			isRunning: function() {
				return running;
			},
			addReq: function(opt, opcTask) {
				opt.opcTask = !opcTask ? null : opcTask;
				requests.push(opt);
			},
			removeReq: function(opt) {
				if ($.inArray(opt, requests) > -1)
					requests.splice($.inArray(opt, requests), 1);
			},
			run: function() {
				var self = this,
					timeOut = !continuous ? 1000 : 500,
					onComplete,
					onError,
					taskName;

				running = true;

				if (requests.length) {
					onComplete = requests[0].hasOwnProperty('complete') ? requests[0].complete : null;
					onError = requests[0].hasOwnProperty('error') ? requests[0].error : null;
					taskName = requests[0].hasOwnProperty('opcTask') && typeof requests[0].opcTask !== typeof undefined && requests[0].opcTask ? 'ajaxManager task ' + requests[0].opcTask + '.' : 'ajaxManager.';

					requests[0].url = requests[0].hasOwnProperty('url') ? requests[0].url : VPOPC._('URI');
					requests[0] = $.extend(true, {}, self.opt, requests[0]);

					requests[0].complete = function(e, t) {
						if (typeof(onComplete) === 'function') onComplete.call(this, e, t);
						requests.shift();
						self.run.apply(self, []);
					};

					requests[0].error = function(e, t, n) {
						if (typeof(onError) === 'function') onError.call(this, e, t, n);
						console.warn('Ajax error is generated running ' + taskName);

						if (t && t != 'abort' && n && n != 'abort') {
							console.log(e);
							if (t == 'parsererror' && e.responseText) {
								console.log(e.responseText);
							}
							console.log(t);
							console.log(n);
						}
					};

					$.ajax(requests[0]);
				} else {
					self.tid = setTimeout(function() {
						self.run.apply(self, []);
					}, timeOut);
				}
			},
			stop: function() {
				requests = [];
				if (this.tid) clearTimeout(this.tid);
				running = false;
			}
		};
	}());

	ProOPC.opcmethod = function() {
		var e = $('input[type="radio"][name="proopc-method"]:checked').val();
		if (e == "guest") {
			$(".proopc-reg-form, .proopc-login-form").addClass('soft-hide').css("opacity", 0);
			$(".proopc-reg-advantages, .proopc-guest-form").removeClass('soft-hide').css("opacity", 0);
			setTimeout(function() {
				$(".proopc-reg-advantages, .proopc-guest-form").animate({
					opacity: 1
				}, 500);
				ProOPC.inputwidth();
				ProOPC.selectwidth();
			}, 50);
		} else {
			if (e == "login") {
				$(".proopc-reg-form, .proopc-reg-advantages, .proopc-guest-form").addClass('soft-hide').css("opacity", 0);
				$(".proopc-login-form").removeClass('soft-hide').css("opacity", 0);
				setTimeout(function() {
					$(".proopc-login-form").animate({
						opacity: 1
					}, 500);
					ProOPC.inputwidth();
					ProOPC.selectwidth();
				}, 50);
			} else {
				$(".proopc-reg-advantages, .proopc-guest-form, .proopc-login-form").addClass('soft-hide').css("opacity", 0);
				$(".proopc-reg-form").removeClass('soft-hide').css("opacity", 0);
				setTimeout(function() {
					$(".proopc-reg-form").animate({
						opacity: 1
					}, 500, function() {
						if ($('#UserRegistration #dynamic_recaptcha_1').length) {
							ProOPC.setStyle();
						}
					});
					ProOPC.inputwidth();
					ProOPC.selectwidth();
				}, 50);
			}
		}
	};

	ProOPC._updateFieldState = function(valid, showValidStatus, message) {
		var $status = $(this).siblings('.status');
		if (valid) {
			$(this).removeClass('invalid').addClass('valid').removeClass('hasFieldTip').removeAttr('title');
			$status.removeClass('invalid').removeClass('validating');
			if (ProOPC.hasValue(showValidStatus)) {
				$status.addClass('valid');
			}
		} else {
			if (valid === null) {
				$(this).removeClass('valid').removeClass('invalid');
				$status.removeClass('invalid').removeClass('valid').addClass('validating');
			} else {
				$(this).removeClass('valid').addClass('invalid');
				$status.addClass('invalid').removeClass('valid').removeClass('validating');
			}
		}

		if (!ProOPC.hasValue(message)) {
			$status.removeAttr('title');
		} else {
			$status.attr('title', VPOPC.JText._(message));
		}

		ProOPC.tooltip();
	};

	ProOPC.hasValue = function(variable) {
		return (typeof variable === typeof undefined || !variable || variable == '0') ? false : variable;
	};

	ProOPC._checkAjaxPool = function(method) {
		if (ProOPC.hasValue(method) && ProOPC.ajaxPool.hasOwnProperty(method) && ProOPC.ajaxPool[method].readystate != 4) {
			ProOPC.ajaxPool[method].abort();
		}
	};

	ProOPC._triggerEvent = function(data) {
		data = (ProOPC.hasValue(data) && $.type(data) === 'string') ? [data] : data;
		$(doc).trigger('vpopc.event', data);
	};

	ProOPC._reload = function() {
		var $reloadForm = $('form#proopc-reload-form');

		if ($reloadForm.length) {
			$reloadForm.submit();
		} else {
			ProOPC._reload();
		}
	};

	ProOPC.ajax = function(ctask, options) {
		ctask = ProOPC.hasValue(ctask) ? ctask : 'dummy';

		var defaults = {
			type: 'POST',
			dataType: 'JSON',
			url: VPOPC._('URI'),
			data: 'ctask=' + ctask,
			cache: false,
			async: true,
			error: function(e, t, n) {
				if (t != 'abort' && n != 'abort') {
					console.warn('Ajax error is generated executing ctask ' + ctask + '.');
					if (t == 'parsererror' && e.responseText) {
						console.log(e.responseText);
					}
					console.log(e);
					console.log(t);
					console.log(n);
				}
				if (typeof options.errorCallback !== typeof undefined && $.type(options.errorCallback) == 'function') {
					options.errorCallback.call(this);
				}
			}
		};

		options = $.extend(true, {}, defaults, options);

		// Check and abort any incomplete Ajax call
		ProOPC._checkAjaxPool(ctask);

		// Execute Ajax
		ProOPC.ajaxPool[ctask] = $.ajax(options);

		return ProOPC.ajaxPool[ctask];
	};

	ProOPC.bindFormValidator = function() {
		var $loginForm = $('form#UserLogin'),
			$guestForm = $('form#GuestUser'),
			$registrationForm = $('form#UserRegistration'),
			triggers = 'change blur keyup';

		$('input[type="text"]:not(#proopc-secretkey), input[type="password"], input[type="email"]', $loginForm).on(triggers, function(e) {
			if (!$(this).val()) {
				ProOPC._updateFieldState.call(this, false, false, 'PLG_VPONEPAGECHECKOUT_REQUIRED_FIELD');
			} else {
				ProOPC._updateFieldState.call(this, true, false);
			}
		});

		$('input[type="text"], input[type="email"]', $guestForm).on(triggers, function(e) {
			if ($(this).attr("id") != "email_field") return false;
			var n = $(this).val();
			if (n && n.length) {
				n = $.trim(n);
				$(this).val(n);
			}
			if (ProOPC.validateEmail(n)) {
				ProOPC._updateFieldState.call(this, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
			} else {
				ProOPC._updateFieldState.call(this, false, true, 'PLG_VPONEPAGECHECKOUT_EMAIL_INVALID');
			}
		});

		$('input[type="text"], input[type="email"], input[type="password"], input[type="checkbox"], select, textarea', $registrationForm).on(triggers, function(e) {
			var that = this,
				$form = $(this).closest('form'),
				$parent = $(this).parent('.proopc-input'),
				required = $(that).attr('required'),
				field_id = $(that).attr('id'),
				value = $(that).val();

			if (!$(that).hasClass('required') && !ProOPC.hasValue(required) && !$parent.data('required')) {
				return true;
			}

			$(that).attr('required', true);

			switch (field_id) {
				case 'email_field':
					if (value && value.length) {
						value = $.trim(value);
						$(that).val(value);
					}
					if (!ProOPC.validateEmail.call(that, value)) {
						ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_EMAIL_INVALID');
					} else {
						$form.find('input#verify_email_field').trigger('change');
						if (!VPOPC._('AJAXVALIDATION')) {
							ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
						} else {
							if (typeof ProOPC.cache.emails === typeof undefined || $.type(ProOPC.cache.emails) !== 'object') {
								ProOPC.cache.emails = {};
							}

							if (ProOPC.cache.emails.hasOwnProperty(value)) {
								if (ProOPC.cache.emails[value]) {
									ProOPC.removeFieldTip(that, false);
									ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
								} else {
									ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
									$(that).addClass('hasFieldTip').attr('title', VPOPC.JText._('COM_USERS_PROFILE_EMAIL1_MESSAGE'));
									ProOPC.fieldTip();
									ProOPC.showFieldTip(that);
								}
							} else {
								ProOPC.ajax('checkemail', {
									beforeSend: function(e) {
										ProOPC._updateFieldState.call(that, null, false, null);
									},
									data: 'ctask=checkemail&email=' + value,
									success: function(e) {
										if (typeof e.valid === typeof undefined || e.valid) {
											ProOPC.removeFieldTip(that, false);
											ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
											ProOPC.cache.emails[value] = true;
										} else {
											ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
											$(that).addClass('hasFieldTip').attr('title', VPOPC.JText._('COM_USERS_PROFILE_EMAIL1_MESSAGE'));
											ProOPC.fieldTip();
											ProOPC.showFieldTip(that);
											ProOPC.cache.emails[value] = false;
										}
									}
								});
							}
						}
					}
					break;
				case 'verify_email_field':
					if (value && value.length) {
						value = $.trim(value);
						$(that).val(value);
					}
					var oValue = $form.find('input#email_field').val();
					if (value != oValue || !value) {
						ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
					} else {
						ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
					}
					break;
				case 'username_field':
					if (!ProOPC.validateUsername.call(that, value)) {
						ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_USERNAME_INVALID');
					} else {
						if (!VPOPC._('AJAXVALIDATION')) {
							ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
						} else {
							if (typeof ProOPC.cache.usernames === typeof undefined || $.type(ProOPC.cache.usernames) !== 'object') {
								ProOPC.cache.usernames = {};
							}

							if (ProOPC.cache.usernames.hasOwnProperty(value)) {
								if (ProOPC.cache.usernames[value]) {
									ProOPC.removeFieldTip(that, false);
									ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
								} else {
									ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
									$(that).addClass('hasFieldTip').attr('title', VPOPC.JText._('COM_USERS_PROFILE_USERNAME_MESSAGE'));
									ProOPC.fieldTip();
									ProOPC.showFieldTip(that);
								}
							} else {
								ProOPC.ajax('checkusername', {
									beforeSend: function(e) {
										ProOPC._updateFieldState.call(that, null, false, null);
									},
									data: 'ctask=checkuser&username=' + value,
									success: function(e) {
										if (typeof e.valid === typeof undefined || e.valid) {
											ProOPC.removeFieldTip(that, false);
											ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
											ProOPC.cache.usernames[value] = true;
										} else {
											ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
											$(that).addClass('hasFieldTip').attr('title', VPOPC.JText._('COM_USERS_PROFILE_USERNAME_MESSAGE'));
											ProOPC.fieldTip();
											ProOPC.showFieldTip(that);
											ProOPC.cache.usernames[value] = false;
										}
									}
								});
							}
						}
					}
					break;
				case 'password_field':
					var $pass2 = $form.find('input#password2_field'),
						$strength = $form.find('#password-stregth'),
						$meter = $form.find('#meter-status');
					$pass2.removeClass('valid').siblings('.status').removeClass('valid');
					ProOPC.removeFieldTip(that, false);
					if (!value || !ProOPC.validatePassword.call(that, value)) {
						$('#password-stregth, #meter-status', $form).removeAttr('class').addClass('invalid');
						ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
						$strength.text('');
					} else {
						ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
						ProOPC.checkStrength.call(that, value);
					}
					break;
				case 'password2_field':
					var oValue = $form.find('input#password_field').val();
					if (value != oValue || !value) {
						ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
					} else {
						ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
					}
					break;
				case 'name_field':
				default:
					if (!value) {
						ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
					} else {
						ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
					}
					break;
			}
		})
	};

	ProOPC.validateForm = function() {
		$(this).find('input, select, textarea').trigger('blur');

		if ($(this).find('.invalid').length) {
			ProOPC.setmsg(1, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_REQUIRED_FIELDS_MISSING'));
			return false;
		}

		return true;
	};

	ProOPC.validateEmail = function(e) {
		if (VPOPC._('DISABLELIVEVALIDATION')) {
			return true;
		}
		var t = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		return (!t.test(e) || e.length < 5) ? false : true;
	};

	ProOPC.validateUsername = function(e) {
		if (VPOPC._('DISABLELIVEVALIDATION')) {
			return true;
		}
		var t = /^[a-zA-Z0-9]+$/;
		return (!t.test(e)) ? false : true;
	};

	ProOPC.validatePassword = function(e) {
		if (VPOPC._('DISABLELIVEVALIDATION')) {
			return true;
		}

		var that = this,
			hasError = false;

		ProOPC.removeFieldTip(that, false);
		$(this).removeAttr('title').removeClass('hasFieldTip');

		if (e.indexOf(' ') >= 0) {
			$(that).addClass('hasFieldTip').attr('title', VPOPC.JText._('COM_USERS_MSG_SPACES_IN_PASSWORD'));
			r = true
		} else if (VPOPC._('PASSWORD_INTEGERS') || VPOPC._('PASSWORD_SYMBOLS') || VPOPC._('PASSWORD_UPPERCASE')) {
			var i = e.match(/\d/g);
			var s = e.match(/\W/g);
			var o = e.match(/[A-Z]/g);
			if (VPOPC._('PASSWORD_INTEGERS') > 0 && (!i || i.length < VPOPC._('PASSWORD_INTEGERS'))) {
				if (VPOPC._('PASSWORD_INTEGERS') == 1) {
					$(that).addClass('hasFieldTip').attr('title', VPOPC.JText.sprintf('COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N_1', VPOPC._('PASSWORD_INTEGERS')))
				} else {
					$(that).addClass('hasFieldTip').attr('title', VPOPC.JText.sprintf('COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N', VPOPC._('PASSWORD_INTEGERS')))
				}
				hasError = true;
			} else if (VPOPC._('PASSWORD_SYMBOLS') > 0 && (!s || s.length < VPOPC._('PASSWORD_SYMBOLS'))) {
				if (VPOPC._('PASSWORD_SYMBOLS') == 1) {
					$(that).addClass('hasFieldTip').attr('title', VPOPC.JText.sprintf('COM_USERS_MSG_NOT_ENOUGH_SYMBOLS_N_1', VPOPC._('PASSWORD_SYMBOLS')))
				} else {
					$(that).addClass('hasFieldTip').attr('title', VPOPC.JText.sprintf('COM_USERS_MSG_NOT_ENOUGH_SYMBOLS_N', VPOPC._('PASSWORD_SYMBOLS')))
				}
				hasError = true;
			} else if (VPOPC._('PASSWORD_UPPERCASE') > 0 && (!o || o.length < VPOPC._('PASSWORD_UPPERCASE'))) {
				if (VPOPC._('PASSWORD_UPPERCASE') == 1) {
					$(that).addClass('hasFieldTip').attr('title', VPOPC.JText.sprintf('COM_USERS_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N_1', VPOPC._('PASSWORD_UPPERCASE')))
				} else {
					$(that).addClass('hasFieldTip').attr('title', VPOPC.JText.sprintf('COM_USERS_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N', VPOPC._('PASSWORD_UPPERCASE')))
				}
				hasError = true;
			}
		} else if (VPOPC._('PASSWORD_LENGTH') > 0 && e.length < VPOPC._('PASSWORD_LENGTH')) {
			$(that).addClass('hasFieldTip').attr('title', VPOPC.JText.sprintf('COM_USERS_MSG_PASSWORD_TOO_SHORT_N', VPOPC._('PASSWORD_LENGTH')));
			hasError = true;
		}

		if (hasError) {
			ProOPC.fieldTip();
			ProOPC.showFieldTip(this);
			return false;
		}

		return true;
	};

	ProOPC.checkStrength = function(e) {
		if (VPOPC._('DISABLELIVEVALIDATION')) {
			return true;
		}
		var that = this,
			$form = $(that).closest('form'),
			$both = $('#password-stregth, #meter-status', $form),
			$strength = $form.find('#password-stregth'),
			$meter = $form.find('#meter-status'),
			t = 0;

		if (e.length < 4) {
			$both.removeAttr('class').addClass('short');
			$strength.text(VPOPC.JText._('PLG_VPONEPAGECHECKOUT_TOO_SHORT'));
			ProOPC._updateFieldState.call(that, false, true, 'PLG_VPONEPAGECHECKOUT_INVALID');
			return false;
		}

		if (e.length > 4) {
			t += 1
		}
		if (e.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
			t += 1
		}
		if (e.match(/([a-zA-Z])/) && e.match(/([0-9])/)) {
			t += 1
		}
		if (e.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) {
			t += 1
		}
		if (e.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/)) {
			t += 1
		}

		if (t < 2) {
			$both.removeAttr('class').addClass('weak');
			$strength.text(VPOPC.JText._('PLG_VPONEPAGECHECKOUT_WEAK'));
			ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
		} else if (t == 2) {
			$both.removeAttr('class').addClass('good');
			$strength.text(VPOPC.JText._('PLG_VPONEPAGECHECKOUT_GOOD'));
			ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
		} else {
			$both.removeAttr('class').addClass('strong');
			$strength.text(VPOPC.JText._('PLG_VPONEPAGECHECKOUT_STRONG'));
			ProOPC._updateFieldState.call(that, true, true, 'PLG_VPONEPAGECHECKOUT_VALIDATED');
		}
	};

	ProOPC.guestcheckout = function() {
		var $form = $('form#GuestUser'),
			$btn = $('button#proopc-task-guestcheckout', $form),
			$process = $('#proopc-guest-process', $form);

		ProOPC.ajax('guestcheckout', {
			dataType: 'HTML',
			data: $form.serialize(),
			beforeSend: function() {
				if (!ProOPC.validateForm.call($form)) {
					return false;
				}
				$process.append(ProOPC.cache.spinner.el);
				$btn.attr('disabled', true);
			},
			success: function(e) {
				$process.find('.proopc-spinner').remove();
				$btn.removeAttr('disabled');
				ProOPC.setmsg('info', VPOPC.JText._('PLG_VPONEPAGECHECKOUT_EMAIL_SAVED'), false);
				ProOPC.processCheckout({
					error: 0
				});
				ProOPC._triggerEvent(['guestcheckout.initiated']);
			},
			errorCallback: function() {
				$process.find('.proopc-spinner').remove();
				$btn.removeAttr('disabled');
				ProOPC.setmsg('1', 'Some error occurred. Check console log for more details.');
			}
		});

		return false;
	};

	ProOPC.registerCheckout = function() {
		var $form = $('form#UserRegistration'),
			$btn = $('button#proopc-task-registercheckout', $form),
			$process = $('#proopc-register-process', $form);

		ProOPC.ajax('register', {
			data: $form.serialize() + '&ctask=register',
			beforeSend: function() {
				if (!ProOPC.validateForm.call($form)) {
					return false;
				}
				$btn.attr('disabled', true);
				$process.append(ProOPC.cache.spinner.el);
			},
			success: function(e) {
				$btn.removeAttr('disabled');
				$process.find('.proopc-spinner').remove();
				if ($.type(e) == 'object' && e.msg) {
					if (e.error == 1) {
						ProOPC.setmsg('1', e.msg);
						if (e.reload) {
							ProOPC._reload();
							return false;
						}
						if (typeof Recaptcha !== "undefined") {
							Recaptcha.reload()
						}
						ProOPC._triggerEvent(['registration.failed']);
					} else {
						ProOPC.setmsg(3, e.msg);
						ProOPC._triggerEvent(['registration.success']);
						ProOPC._onRegistrationSuccess();
						if (!e.stop) {
							if (VPOPC._('RELOAD')) {
								ProOPC._reload()
							} else {
								setTimeout(function() {
									ProOPC.processCheckout(e)
								}, 100);
							}
						}
					}
				} else {
					ProOPC._triggerEvent(['registration.success']);
					ProOPC._onRegistrationSuccess();
					if (e == "1" || e == 1) {
						ProOPC.setmsg(3, VPOPC.JText._('COM_VIRTUEMART_REG_COMPLETE'));
						if (VPOPC._('RELOAD')) {
							ProOPC._reload()
						} else {
							setTimeout(function() {
								ProOPC.processCheckout(e)
							}, 100);
						}
					} else if ($.type(e) === 'string' && e.indexOf('</head>') == -1) {
						ProOPC.setmsg(3, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_REGISTRATION_NEED_LOGIN'));
					} else {
						ProOPC.setmsg(3, VPOPC.JText._('COM_VIRTUEMART_REG_COMPLETE'));
						if (VPOPC._('RELOAD')) {
							ProOPC._reload()
						} else {
							setTimeout(function() {
								ProOPC.processCheckout({
									error: 0
								});
							}, 100);
						}
					}
				}
			},
			errorCallback: function() {
				$btn.removeAttr('disabled');
				$process.find('.proopc-spinner').remove();
				ProOPC.setmsg(1, 'Error submiting registration form. Check console log for more details.');
			}
		});

		return false;
	};
	
	ProOPC._onRegistrationSuccess = function() {
		var $form = $('form#UserRegistration'),
			$loginRadio = $('input[name="proopc-method"][value="login"]'),
			$passStregth = $('#password-stregth', $form),
			$passMeter = $('#meter-status', $form);
			
		$form[0].reset();
		$form.find('i.status').removeClass('valid').removeClass('invalid');
		$passStregth.removeAttr('class').text('');
		$passMeter.removeAttr('class');
		if($loginRadio.length) {
			$loginRadio.prop('checked', true).trigger('change');
		}
	};
	
	ProOPC.disableSubmit = function() {
		$('button#proopc-order-submit').attr('disabled', 'disabled').trigger('disabled');
	};

	ProOPC.enableSubmit = function() {
		var $button = $('button#proopc-order-submit');
		if (ProOPC.cache.submitTimeout && $button.attr('disabled')) clearTimeout(ProOPC.cache.submitTimeout);

		ProOPC.cache.submitTimeout = setTimeout(function() {
			$button.removeAttr('disabled').trigger('enabled');
		}, 1000);
	};
	
	ProOPC.disableMethodSelection = function() {
		$('input[name="virtuemart_paymentmethod_id"][type="radio"], input[name="virtuemart_shipmentmethod_id"][type="radio"]').attr('disabled', 'disabled');
	};
	
	ProOPC.enableMethodSelection = function() {
		$('input[name="virtuemart_paymentmethod_id"][type="radio"], input[name="virtuemart_shipmentmethod_id"][type="radio"]').removeAttr('disabled');
	};

	ProOPC.addloader = function(sections) {
		var loader = '<div class="proopc-loader-overlay"></div><div class="proopc-area-loader"><span></span></div>';

		$(sections).each(function() {
			if (!$(this).find('.proopc-loader-overlay').length) {
				$(this).append(loader);
				$('.proopc-area-loader > span').html(ProOPC.cache.area_loader.el);
			}
		});

		ProOPC.disableSubmit();
		$('body').addClass('proopc-loading');
	};

	ProOPC.removeloader = function(e) {
		$(e).each(function() {
			$('.proopc-loader-overlay, .proopc-area-loader', this).remove();
		});

		ProOPC.enableSubmit();
		$('body').removeClass('proopc-loading');
	};

	ProOPC.addPageLoader = function(message) {
		if (!$('#proopc-page-overlay', doc).length) {
			var $overlay = $('<div />', {
					'id': 'proopc-page-overlay'
				}),
				$span = $('<span />').append(ProOPC.cache.loader.el),
				$spinner = $('<div />', {
					'id': 'proopc-page-spinner'
				}).append($span);

			$('body').append($overlay).append($spinner);
			$('#proopc-page-overlay').css({
				display: 'block',
				height: $('body').outerHeight()
			}).animate({
				opacity: 0.7
			}, 300);
		}

		if (ProOPC.hasValue(message)) {
			var $messageBox = $('#proopc-order-process');
			message = VPOPC.JText._(message, message);

			if (message) {
				if (!$messageBox.length) {
					$messageBox = $('<div />', {
						'id': 'proopc-order-process'
					}).appendTo('body');
				}

				$messageBox.text(message);
			}
		}

		$('body').addClass('proopc-page-loading');
	};

	ProOPC.removePageLoader = function() {
		$('#proopc-page-overlay, #proopc-page-spinner, #proopc-order-process', doc).remove();
		$('body').removeClass('proopc-page-loading');
	};

	ProOPC.getToken = function() {
		return $('#formToken input:hidden').attr('name') + '=1';
	};

	ProOPC.pluginLoaded = function(plugin_name) {
		if ($.hasOwnProperty(plugin_name)) {
			return true;
		}

		if ($.fn.hasOwnProperty(plugin_name)) {
			return true;
		}

		return false;
	};

	ProOPC.setmsg = function(e, t, n) {
		n = (typeof n === "undefined" || typeof n === undefined) ? true : n;
		var $close = $('<button />', {
				'type': 'button',
				'class': 'close'
			}).attr('onclick', 'ProOPC.close(this);').html('&times;'),
			$span = $('<span />').html(t),
			$box = $('<div />', {
				'class': 'proopc-alert'
			}).append($close).append($span),
			msgClass = 'proopc-warning-msg';

		if (e == '1' || e == 'error') {
			msgClass = 'proopc-error-msg';
		} else if (e == '2' || e == 'warning') {
			msgClass = 'proopc-warning-msg';
		} else if (e == '3' || e == 'success') {
			msgClass = 'proopc-success-msg';
		} else if (e == '4' || e == 'info') {
			msgClass = 'proopc-info-msg';
		}

		$box.addClass(msgClass);
		$('#proopc-system-message').html($box);

		if (n) {
			$('html,body').animate({
				scrollTop: $('#proopc-system-message').offset().top - VPOPC._('SYSTEM_MESSAGE_OFFSET', 100)
			}, 500);
		}
	};

	ProOPC.initStateField = function() {
		if (!ProOPC.pluginLoaded('vm2front')) {
			console.warn('jQuery.vm2front plugin not loaded. Please enable "Using the Script ajax Countries / Regions" option in VirtueMart Configuration.');
			return false;
		}

		var saved_bt_state_id = $('input#BTStateID').val(),
			saved_st_state_id = $('input#STStateID').val(),
			options = {
				dest: '#virtuemart_state_id_field',
				ids: saved_bt_state_id,
				prefiks: ''
			};

		if ($('select#virtuemart_state_id_field').length) {
			$('select#virtuemart_country_id_field').vm2front('list', options);
		}

		options.ids = saved_st_state_id;
		options.prefiks = 'shipto_';

		if ($('select#shipto_virtuemart_state_id_field').length) {
			options.dest = '#shipto_virtuemart_state_id_field';
			$('select#shipto_virtuemart_country_id_field').vm2front('list', options);
		}
	};

	ProOPC.processCheckout = function(e) {
		if (!ProOPC.hasValue(e) || $.type(e) !== 'object') {
			e = {
				error: 0,
				msg: []
			};
		}

		if (e.error && e.msg.length) {
			var msg = '';
			$.each(e.msg, function(e, t) {
				msg = msg + '<div class="error-msg">' + t + '</div>';
			});
			ProOPC.setmsg(1, msg);
			return false;
		}

		ProOPC.ajax('procheckout', {
			dataType: 'HTML',
			data: 'task=procheckout',
			beforeSend: function() {
				ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_PLEASE_WAIT');
				$('.proopc-tooltip, .proopc-fieldtip').remove();
			},
			success: function(result) {
				var html = $(result).find('div#ProOPC').html();
				if (!html) {
					html = result;
				}
				$('div#ProOPC').html(html);
				if (!VPOPC._('RELOAD')) {
					$('html, body').animate({
						scrollTop: $('#proopc-system-message').offset().top - 100
					}, 500);
				}
				ProOPC._triggerEvent(['checkout.finalstage']);
			},
			complete: function() {
				ProOPC.initStateField();
				ProOPC.setStyle();
				ProOPC.tooltip();
				ProOPC.inputwidth();
				ProOPC.selectwidth();
				ProOPC.bindFormValidator();
				ProOPC.productdetails();
				ProOPC.enableSubmit();
				ProOPC.defaultSP();
				ProOPC.removePageLoader();
				if (typeof klarna !== typeof undefined || typeof klarna !== 'undefined') {
					ProOPC.loadPaymentScripts();
				}
				ProOPC.loadShipmentScripts();
			}
		});
	};

	ProOPC.loginAjax = function() {
		var $form = $('form#UserLogin'),
			$btn = $('button#proopc-task-loginajax', $form),
			$ctask = $form.find('input[name="ctask"]:hidden'),
			$process = $form.find('#proopc-login-process');

		if (!ProOPC.validateForm.call($form)) {
			return false
		}

		if ($ctask.val() != 'login') {
			$ctask.val('login')
		}

		ProOPC.ajax('login', {
			data: $form.serialize(),
			beforeSend: function() {
				$process.append(ProOPC.cache.spinner.el);
				$btn.attr('disabled', true);
			},
			success: function(e) {
				$process.find('.proopc-spinner').remove();
				$btn.removeAttr('disabled');
				var success = true;
				if (e == '1' || e == 1) {
					success = true;
				} else if (!e || e.indexOf("</head>") == -1) {
					success = false;
				}
				if (success) {
					ProOPC.setmsg('success', VPOPC.JText._('PLG_VPONEPAGECHECKOUT_LOGIN_COMPLETED'));
					ProOPC._triggerEvent(['login.success']);
					if (VPOPC._('RELOAD')) {
						ProOPC._reload()
					} else {
						ProOPC.processCheckout()
					}
				} else {
					ProOPC.setmsg('warning', VPOPC.JText._('JLIB_LOGIN_AUTHENTICATE'));
					ProOPC._triggerEvent(['login.failed']);
				}
			},
			errorCallback: function() {
				$process.find('.proopc-spinner').remove();
				$btn.removeAttr('disabled');
				ProOPC.setmsg(1, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_SYSTEM_ERROR_JS'));
			}
		});

		return false;
	};

	ProOPC.updateBTaddress = function(field) {
		ProOPC.ajaxManager.addReq({
			data: $('form#EditBTAddres').serialize() + '&ctask=savebtaddress&' + ProOPC.getToken(),
			beforeSend: function() {
				ProOPC.disableSubmit();
			},
			success: function(e) {
				ProOPC._triggerEvent(['checkout.bt.updated']);
				var keepMessage = false;
				if(e.vat_error && e.msg) {
					ProOPC.setmsg('warning', e.msg);
					keepMessage = true;
					ProOPC.updateVATFieldState(false, e.info);
				} else {
					ProOPC.updateVATFieldState(true, e.info);
				}
				ProOPC.getshipmentpaymentcartlist(keepMessage);
			}
		}, 'updateBTaddress');
	};

	ProOPC.updateSTaddress = function(e) {
		ProOPC.ajaxManager.addReq({
			data: $('form#EditSTAddres').serialize() + '&ctask=savestaddress&' + ProOPC.getToken(),
			beforeSend: function() {
				ProOPC.disableSubmit();
			},
			success: function(e) {
				ProOPC._triggerEvent(['checkout.st.updated']);
				var keepMessage = false;
				if(e.vat_error && e.msg) {
					ProOPC.setmsg('warning', e.msg);
					keepMessage = true;
					ProOPC.updateVATFieldState(false, e.info, 'shipto_');
				} else {
					ProOPC.updateVATFieldState(true, e.info, 'shipto_');
				}
				ProOPC.getshipmentpaymentcartlist(keepMessage);
				if (typeof e.userinfo_id !== typeof undefined) {
					$('input#shipto_virtuemart_userinfo_id').val(e.userinfo_id);
				}
			}
		}, 'updateSTaddress');
	};

	ProOPC.selectSTAddress = function(e) {
		ProOPC.ajax('selectstaddress', {
			data: 'ctask=selectstaddress&shipto_virtuemart_userinfo_id=' + $(e).val(),
			beforeSend: function() {
				ProOPC.addloader('#proopc-st-address');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) !== 'object') {
					console.warn('Invalid data received');
					console.log(e);
				}

				$('#proopc-st-address').html(e.editst);

				if (!ProOPC.pluginLoaded('vm2front')) {
					console.warn('jQuery.vm2front plugin not loaded. Please enable "Using the Script ajax Countries / Regions" option in VirtueMart Configuration.');
				} else {
					if (e.stateid > 0) {
						$('input#STStateID').val(e.stateid);
					} else {
						$('input#STStateID').val('');
					}
					$('#shipto_virtuemart_country_id_field').vm2front('list', {
						dest: '#shipto_virtuemart_state_id_field',
						ids: e.stateid,
						prefiks: 'shipto_'
					});
				}

				var keepMessage = false;
				if(e.vat_error && e.msg) {
					ProOPC.setmsg('warning', e.msg);
					keepMessage = true;
					ProOPC.updateVATFieldState(false, e.info, 'shipto_');
				} else {
					ProOPC.updateVATFieldState(true, e.info, 'shipto_');
				}

				ProOPC.setStyle();
				$('#shipto_virtuemart_state_id_field').trigger('liszt:updated');
				ProOPC.inputwidth();
				ProOPC.selectwidth();
				ProOPC.removeloader('#proopc-st-address');
				ProOPC._triggerEvent(['checkout.st.selected']);
				ProOPC.getshipmentpaymentcartlist(keepMessage);
			}
		})
	};

	ProOPC.setst = function(e) {
		if (!e || !$(e).length) {
			return;
		}

		ProOPC.disableSubmit();

		if (e.checked) {
			ProOPC.ajax('btasst', {
				data: 'ctask=btasst',
				beforeSend: function() {
					$('.proopc-st-address .edit-address').slideUp();
				},
				success: function(e) {
					ProOPC._triggerEvent(['checkout.btasst']);
					var keepMessage = false;
					if(e.vat_error && e.msg) {
						ProOPC.setmsg('warning', e.msg);
						keepMessage = true;
						ProOPC.updateVATFieldState(false, e.info, 'shipto_');
					} else {
						ProOPC.updateVATFieldState(true, e.info, 'shipto_');
					}
					ProOPC.getshipmentpaymentcartlist(keepMessage);
				}
			});
		} else {
			ProOPC.ajax('btasst', {
				data: 'ctask=btnotasst',
				beforeSend: function() {
					$('.proopc-st-address .edit-address').slideDown();
					ProOPC.inputwidth();
					ProOPC.selectwidth();
				},
				success: function() {
					ProOPC._triggerEvent(['checkout.btnotasst']);
					var $selection = $('select#proopc-select-st');
					if ($selection.length && $selection.val() > 0) {
						ProOPC.selectSTAddress($selection[0]);
					} else {
						ProOPC.updateSTaddress();
					}
				}
			});
		}
	};

	ProOPC.getshipmentpaymentcartlist = function(keepMessage) {
		if (!keepMessage) {
			$('#proopc-system-message').html('');
		}
		ProOPC.ajax('getshipmentpaymentcartlist', {
			beforeSend: function() {
				ProOPC.addloader('#proopc-pricelist, #proopc-payments, #proopc-shipments');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) !== 'object') {
					ProOPC._reload();
					return false;
				}

				if (e.pqty == 0) {
					ProOPC._reload();
					return false;
				}

				if (e.msg) {
					ProOPC.setmsg(e.msg_type, e.msg);
				}

				$('#proopc-shipments').html(e.shipments);
				$('#proopc-payments').html(e.payments);
				$('#proopc-pricelist').html(e.cartlist);
				$('#proopc-cart-totalqty').text(e.pqty);

				if (e.payment_scripts.length > 0 && e.payment_scripts[0] !== '') {
					var payment_scripts = e.payment_scripts;
				}

				if (e.payment_script[0] !== '') {
					$.each(e.payment_script, function(e, t) {
						$.getScript(t, function(e, t, n) {
							if (typeof payment_scripts !== typeof undefined) {
								$.each(payment_scripts, function(e, t) {
									t
								})
							}
						})
					})
				}

				if (e.shipment_scripts.length > 0 && e.shipment_scripts[0] !== '') {
					$.each(e.shipment_scripts, function(e, t) {
						$('head').append('<script type="text/javascript">' + t + '</script>');
					})
				}

				ProOPC._triggerEvent(['checkout.updated.shipmentpaymentcartlist']);
				ProOPC.productdetails();
				ProOPC.setStyle();
				ProOPC.tooltip();
				ProOPC.removeloader('#proopc-pricelist, #proopc-payments, #proopc-shipments');
				ProOPC.enableSubmit();
				ProOPC.defaultSP();
			},
			errorCallback: function() {
				ProOPC.removeloader('#proopc-pricelist, #proopc-payments, #proopc-shipments');
				ProOPC.enableSubmit();
			}
		});
	};

	ProOPC.getcartlist = function(redirect_url, keepMessage) {
		if (!keepMessage) {
			$('#proopc-system-message').html('');
		}

		ProOPC.ajax('getcartlist', {
			beforeSend: function() {
				ProOPC.addloader('#proopc-pricelist');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) !== 'object') {
					ProOPC._reload();
					return false;
				}

				if (e.pqty == 0) {
					ProOPC._reload();
					return false;
				}

				if (e.msg) {
					ProOPC.setmsg(e.msg_type, e.msg);
				}

				$('#proopc-pricelist').html(e.cartlist);
				$('#proopc-cart-totalqty').text(e.pqty);
				
				if(!ProOPC.hasValue(e.selected_shipment)) {
					$('form#proopc-shipment-form input[type="radio"][name="virtuemart_shipmentmethod_id"]:checked').prop('checked', false);
				}
				
				if(!ProOPC.hasValue(e.selected_payment)) {
					$('form#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked').prop('checked', false);
				} 

				ProOPC._triggerEvent(['checkout.updated.cartlist']);
				ProOPC.productdetails();
				ProOPC.setStyle();
				ProOPC.removeloader('#proopc-pricelist');
				ProOPC.enableSubmit();

				if (typeof redirect_url !== 'undefined' && redirect_url) {
					win.location = redirect_url;
				}
			},
			errorCallback: function() {
				ProOPC.removeloader('#proopc-pricelist');
				ProOPC.enableSubmit();
			}
		});
	};

	ProOPC.getcartsummery = function() {
		ProOPC.ajax('getcartsummery', {
			beforeSend: function() {
				ProOPC.addloader('#proopc-pricelist');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) !== 'object') {
					ProOPC._reload();
					return false;
				}

				if (e.pqty == 0) {
					ProOPC._reload();
					return false;
				}

				if (e.msg) {
					ProOPC.setmsg(e.msg_type, e.msg);
				}

				$('#proopc-cart-totalqty').text(e.pqty);
				$('#proopc-pricelist').html(e.cartsummery);

				ProOPC._triggerEvent(['checkout.updated.cartlist']);
				ProOPC.setStyle();
				ProOPC.removeloader('#proopc-pricelist');
				ProOPC.enableSubmit();
			},
			errorCallback: function() {
				ProOPC.removeloader('#proopc-pricelist');
				ProOPC.enableSubmit();
			}
		});
	};

	ProOPC.getpayments = function() {
		ProOPC.ajax('getpaymentlist', {
			beforeSend: function() {
				ProOPC.addloader('#proopc-payments');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) !== 'object') {
					ProOPC._reload();
					return false;
				}

				$('#proopc-payments').html(e.payments);

				if (e.payment_scripts.length > 0 && e.payment_scripts[0] !== "") {
					var payment_scripts = e.payment_scripts;
				}

				if (e.payment_script[0] !== "") {
					$.each(e.payment_script, function(e, t) {
						$.getScript(t, function(e, t, n) {
							if (typeof payment_scripts !== "undefined") {
								$.each(payment_scripts, function(e, t) {
									t
								})
							}
						})
					})
				}

				ProOPC._triggerEvent(['checkout.updated.paymentlist']);
				ProOPC.setStyle();
				ProOPC.tooltip();
				ProOPC.removeloader('#proopc-payments');
				ProOPC.enableSubmit();
				ProOPC.defaultSP();
			},
			errorCallback: function() {
				ProOPC.removeloader('#proopc-payments');
				ProOPC.enableSubmit();
			}
		});
	};

	ProOPC.getPaymentData = function(saveAll) {
		saveAll = ProOPC.hasValue(saveAll);

		$('.klarna_box_bottom').hide();
		$('.vmpayment_cardinfo').removeClass('show').addClass('hide');

		var $form = $('form#proopc-payment-form'),
			$payment = $form.find('input[type="radio"][name="virtuemart_paymentmethod_id"]:checked');

		if (!$payment.length) {
			return false;
		}

		var pmtype = $payment.data('pmtype'),
			paypalProduct = $payment.data('paypalproduct'),
			savecc = '0',
			payment_data = '0';


		if (paypalProduct) {
			pmtype = pmtype + paypalProduct;
		}

		if (pmtype && $('.vmpayment_cardinfo.' + pmtype).length) {
			$('.vmpayment_cardinfo.' + pmtype).removeClass('hide').addClass('show');
			payment_data = '1';
		}

		if (pmtype == 'sisowideal') {
			payment_data = '1';
		}

		if (saveAll) {
			savecc = '1';
			payment_data = '1';
		}

		if ($payment.hasClass('klarnaPayment')) {
			ProOPC.klarnaOpenClose.call($payment[0]);
		}

		ProOPC._triggerEvent(['prepare.data.payment', $payment[0]]);

		return $form.serialize() + '&ajax=1&savecc=' + savecc + '&payment_data=' + payment_data;
	};

	ProOPC.getShipmentData = function() {
		var formSelector = 'form#proopc-shipment-form',
			$form = $(formSelector),
			$shipment = $form.find('input[type="radio"][name="virtuemart_shipmentmethod_id"]:checked');

		if (!$shipment.length) {
			return false;
		}

		var shipment_id = $shipment.val(),
			usps = $shipment.data('usps'),
			ups = $shipment.data('ups'),
			upsrates = $shipment.data('upsrates');

		if ($('#usps_name-' + shipment_id, $form).length && usps) {
			$('#usps_name-' + shipment_id, $form).val(usps.service);
		}

		if ($('#usps_rate-' + shipment_id, $form).length && usps) {
			$('#usps_rate-' + shipment_id, $form).val(usps.rate);
		}

		if ($('#ups_rate-' + shipment_id, $form).length && ups) {
			$('#ups_rate-' + shipment_id, $form).val(ups.id);
		}

		if ($('#upsrates-' + shipment_id, $form).length && upsrates) {
			$('#upsrates-' + shipment_id, $form).val(upsrates);
		}

		ProOPC._triggerEvent(['prepare.data.shipment', $form[0], $shipment[0]]);

		return $(formSelector).serialize();
	};

	ProOPC.loadPaymentScripts = function() {
		ProOPC.ajax('getpaymentscripts', {
			beforeSend: function() {
				ProOPC.addloader('#proopc-payments');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) === 'object') {
					$('#proopc-payments').html(e.payments);
					if (e.payment_scripts.length && e.payment_scripts[0] !== '') {
						payment_scripts = e.payment_scripts
					}
					if (e.payment_script.length && e.payment_script[0] !== '') {
						$.each(e.payment_script, function(e, t) {
							$.getScript(t, function(e, t, n) {
								if (typeof payment_scripts !== 'undefined') {
									$.each(payment_scripts, function(e, t) {
										t
									})
								}
							})
						})
					}
				}
				ProOPC.setStyle();
				ProOPC.tooltip();
				ProOPC.removeloader('#proopc-payments');
				ProOPC.defaultSP();
				ProOPC.enableSubmit();
			},
			errorCallback: function() {
				ProOPC.removeloader('#proopc-payments');
				ProOPC.enableSubmit();
			}
		});
	};

	ProOPC.loadShipmentScripts = function() {
		if (!$('form#proopc-shipment-form select').length) {
			return false;
		}

		ProOPC.ajax('getshipmentscripts', {
			beforeSend: function() {
				ProOPC.addloader('#proopc-shipments');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) === 'object') {
					$('#proopc-shipments').html(e.shipments);
					if (e.shipment_scripts.length && e.shipment_scripts[0]) {
						$.each(e.shipment_scripts, function(index, scriptTag) {
							$('head, body').find('script[type="text/javascript"]').each(function() {
								var src = $(this).attr('src');
								if (typeof src === "undefined" || src === false) {
									$(this).append(scriptTag);
									return false;
								}
							})
						})
					}
				}
				ProOPC.setStyle();
				ProOPC.tooltip();
				ProOPC.removeloader('#proopc-shipments');
				ProOPC.defaultSP();
				ProOPC.enableSubmit();
			},
			errorCallback: function() {
				ProOPC.removeloader('#proopc-shipments');
				ProOPC.enableSubmit();
			}
		});
	};


	ProOPC.defaultSP = function() {
		var $shipment_form = $('form#proopc-shipment-form'),
			$payment_form = $('form#proopc-payment-form'),
			$method,
			shipment_selector = 'input[type="radio"][name="virtuemart_shipmentmethod_id"]',
			payment_selector = 'input[type="radio"][name="virtuemart_paymentmethod_id"]',
			auto_shipment = VPOPC._('AUTOSHIPMENT'),
			auto_payment = VPOPC._('AUTOPAYMENT'),
			data = '';

		if (auto_shipment >= 0 && !$shipment_form.find(shipment_selector + ':checked').length && $shipment_form.find(shipment_selector).length) {
			if (auto_shipment == 0) {
				auto_shipment = $shipment_form.find(shipment_selector + ':first').val();
			}

			$method = $shipment_form.find(shipment_selector + '[value="' + auto_shipment + '"]');

			if (auto_shipment > 0 && $method.length) {
				$method.attr('checked', true);
				data += ProOPC.getShipmentData();
			}
		}

		if (auto_payment >= 0 && !$payment_form.find(payment_selector + ':checked').length && $payment_form.find(payment_selector).length) {
			if (auto_payment == 0) {
				auto_payment = $payment_form.find(payment_selector + ':first').val();
			}

			$method = $payment_form.find(payment_selector + '[value="' + auto_payment + '"]');

			if (auto_payment > 0 && $method.length && $method.data('paypalproduct') != 'exp') {
				$method.attr('checked', true);
				if (data) data += '&';
				data += ProOPC.getPaymentData();
			}
		}

		if (data && data !== '') {
			ProOPC.ajax('setdefaultsp', {
				data: 'ctask=setdefaultsp&' + data,
				beforeSend: function() {
					ProOPC.addloader('#proopc-shipments, #proopc-payments');
					ProOPC.disableSubmit();
				},
				success: function(e) {
					if(!ProOPC.hasValue(e.selected_shipment)) {
						$('form#proopc-shipment-form input[type="radio"][name="virtuemart_shipmentmethod_id"]:checked').prop('checked', false);
					}
					if(!ProOPC.hasValue(e.selected_payment)) {
						$('form#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked').prop('checked', false);
					}
					ProOPC.enableSubmit();
					ProOPC.removeloader('#proopc-shipments, #proopc-payments');
					if (typeof e.error !== typeof undefined && typeof e.error !== 'undefined' && e.error) {
						ProOPC._triggerEvent(['checkout.defaultsp.failed']);
					} else {
						ProOPC._triggerEvent(['checkout.defaultsp.success']);
						ProOPC.getcartlist();
					}
				},
				errorCallback: function() {
					$('form#proopc-shipment-form input[type="radio"][name="virtuemart_shipmentmethod_id"]:checked').prop('checked', false);
					$('form#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked').prop('checked', false);
					ProOPC.enableSubmit();
					ProOPC.removeloader('#proopc-shipments, #proopc-payments');
				}
			});
		}
	};

	ProOPC.setshipment = function(radio) {
		var formData = ProOPC.getShipmentData(),
			$form = $('form#proopc-shipment-form'),
			$shipment = $form.find('input[type="radio"][name="virtuemart_shipmentmethod_id"]:checked'),
			shipment_id = $shipment.val();

		if (!formData) {
			return false;
		}

		ProOPC.ajax('setshipment', {
			data: 'ctask=setshipments&' + formData,
			beforeSend: function() {
				ProOPC.disableSubmit();
				ProOPC.disableMethodSelection();
			},
			success: function(e) {
				if(!ProOPC.hasValue(e.selected)) {
					$shipment.prop('checked', false);
					e.selected = 0;
				}
				
				$('input#proopc-savedShipment:hidden').val(e.selected);
				
				if (e.error) {
					ProOPC.setmsg('2', e.msg);
					ProOPC._triggerEvent(['checkout.shipmentselection.failed', shipment_id]);
					ProOPC.getcartlist(false, true);
				} else {
					ProOPC._triggerEvent(['checkout.shipmentselection.success', shipment_id]);
					ProOPC.getcartlist();
					if (VPOPC._('RELOADPAYMENTS')) {
						ProOPC.getpayments()
					}
				}
			},
			errorCallback: function() {
				$shipment.prop('checked', false);
				ProOPC.enableSubmit();
			}
		});
	};

	ProOPC.setpayment = function(radio) {
		var saveAll = ProOPC.hasValue(radio) && $(radio).length && $(radio).hasClass('btn-additional-klarna'),
			async = saveAll ? false : true,
			formData = ProOPC.getPaymentData(saveAll),
			$form = $('form#proopc-payment-form'),
			$payment = $form.find('input[type="radio"][name="virtuemart_paymentmethod_id"]:checked'),
			payment_id = $payment.val();

		if (!formData) {
			return false;
		}

		ProOPC.ajax('setpayment', {
			data: 'ctask=setpayment&' + formData,
			async: async,
			beforeSend: function() {
				ProOPC.disableSubmit();
				ProOPC.disableMethodSelection();
			},
			success: function(e, t, n) {
				if (n && n.getResponseHeader("content-type").indexOf("text/html") >= 0 && typeof klarna !== "undefined") {
					$('#proopc-order-submit').removeAttr('disabled');
					$('<div />', {
						id: 'proopc-temp',
						style: 'display:none'
					}).appendTo('body');
					$('#proopc-temp').append(e);
					var message = $('#proopc-temp').find('div#system-message-container').html();
					ProOPC.setmsg(1, r);
					ProOPC.enableSubmit();
				}

				if ($.type(e) === 'string' || typeof e === 'string') {
					e = $.parseJSON(e)
				}

				e.redirect = !e.redirect ? 0 : e.redirect;
				
				if(!ProOPC.hasValue(e.selected)) {
					$payment.prop('checked', false);
				}

				if (e.error) {
					ProOPC.setmsg(2, e.msg);
					ProOPC._triggerEvent(['checkout.paymentselection.failed', payment_id]);
					ProOPC.getcartlist(false, true);
				} else if (e.redirect) {
					ProOPC.setmsg(4, VPOPC.JText._('VMPAYMENT_PAYPAL_REDIRECT_MESSAGE'));
					ProOPC._triggerEvent(['checkout.paymentselection.successs', payment_id]);
					ProOPC.getcartlist(e.redirect, true);
				} else {
					if (saveAll) {
						ProOPC._triggerEvent(['checkout.paymentmethod.additionaldata.saved', payment_id]);
						ProOPC.setmsg(4, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_CREDIT_CARD_SAVED'));
						ProOPC.enableSubmit();
					} else {
						var keepMessage = false;
						if (e.msg && ($payment.hasClass('klarnaPayment') || !ProOPC.hasValue(e.selected))) {
							ProOPC.setmsg(2, e.msg);
							keepMessage = true;
						}
						ProOPC._triggerEvent(['checkout.paymentselection.successs', payment_id]);
						ProOPC.getcartlist(false, keepMessage);
					}
				}
			},
			errorCallback: function() {
				$payment.prop('checked', false);
				ProOPC.enableSubmit();
			}
		});
	};


	ProOPC.deleteproduct = function(product) {
		ProOPC.ajax('deleteproduct', {
			data: 'ctask=deleteproduct&id=' + $(product).data('vpid'),
			beforeSend: function() {
				ProOPC.addloader('#proopc-pricelist, #proopc-payments, #proopc-shipments');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				ProOPC._triggerEvent(['checkout.products.deleted', $(product).data('vpid')]);
				$('#proopc-system-message').html('');
				$('.proopc-product-hover').addClass('hide');

				if (e.pqty == 0 || !e.pqty) {
					ProOPC._reload();
					return false;
				}

				if ($('input#proopc-cart-summery').length > 0) {
					ProOPC.getcartsummery();
				} else {
					ProOPC.getshipmentpaymentcartlist();
				}

				$('body').trigger('updateVirtueMartCartModule');
			},
			errorCallback: function() {
				ProOPC.enableSubmit();
			}
		});

		return false;
	};

	ProOPC.updateproductqty = function(e) {
		var updated = false;

		$('.proopc-qty-input').each(function() {
			if ($(this).data('quantity') != $(this).val()) {
				updated = true;
				return false;
			}
		});

		if (!updated) {
			return false;
		}

		ProOPC.ajax('updateProductQty', {
			data: $('#proopc-carttable-form').serialize(),
			beforeSend: function() {
				ProOPC.addloader('#proopc-pricelist, #proopc-payments, #proopc-shipments');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) !== 'object') {
					ProOPC._reload();
					return false;
				}

				if (e.error) {
					ProOPC.setmsg(2, e.msg);
				} else {
					ProOPC._triggerEvent(['checkout.products.updated', e.pqty]);
					$('#proopc-system-message').html('');
				}

				$('.proopc-product-hover').addClass('hide');

				if (e.pqty == 0 || !e.pqty) {
					ProOPC._reload();
					return false;
				}

				if ($('input#proopc-cart-summery').length > 0) {
					ProOPC.getcartsummery();
				} else {
					ProOPC.getshipmentpaymentcartlist();
				}

				$('body').trigger('updateVirtueMartCartModule');
			},
			errorCallback: function() {
				ProOPC.enableSubmit();
			}
		});

		return false;
	};

	ProOPC.savecoupon = function(e) {
		var $couponField = $("input#proopc-coupon-code"),
			couponCode = $couponField.val(),
			defaultValue = $couponField.data('default');

		$('#proopc-system-message').html('');

		if (!couponCode || couponCode == defaultValue) {
			ProOPC.setmsg(2, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_COUPON_EMPTY'));
			return false;
		}

		ProOPC.ajax('setcoupon', {
			data: 'ctask=setcoupon&coupon_code=' + encodeURIComponent(couponCode),
			beforeSend: function() {
				ProOPC.addloader('#proopc-coupon');
				ProOPC.disableSubmit();
			},
			success: function(e) {
				if ($.type(e) !== 'object') {
					ProOPC._reload();
					return false;
				}

				ProOPC._triggerEvent(['checkout.coupon.submitted', couponCode, e.msg]);
				var msgType = !e.error ? 'success' : e.error;

				$couponField.val(defaultValue);

				if (!e.wait && e.msg) {
					ProOPC.setmsg(msgType, e.msg);
				}

				ProOPC.removeloader('#proopc-coupon');

				if (VPOPC._('RELOADALLFORCOUPON')) {
					ProOPC.getshipmentpaymentcartlist(true)
				} else {
					ProOPC.getcartlist(null, true)
				}

				$('body').trigger('updateVirtueMartCartModule');
			},
			errorCallback: function() {
				ProOPC.setmsg(1, 'An error has occurred while processing coupon code. See console log for more details.');
				ProOPC.removeloader('#proopc-coupon');
				ProOPC.enableSubmit();
			}
		});

		return false;
	};

	ProOPC.fieldGroups = [
		['title', 'first_name'],
		['middle_name', 'last_name'],
		['zip', 'city'],
		['shipto_middle_name', 'shipto_last_name'],
		['shipto_zip', 'shipto_city']
	];

	ProOPC.inputwidth = function() {
		$('form#EditBTAddres').children().each(function(i) {
			$(this).data('pos', i);
		});

		$('form#EditSTAddres').children().each(function(i) {
			$(this).data('pos', i);
		});

		if (VPOPC._('GROUPING') && $.type(ProOPC.fieldGroups) === 'array' && ProOPC.fieldGroups.length) {
			var $field_1, $field_2;

			$.each(ProOPC.fieldGroups, function(key, fields) {
				if ($.type(fields) === 'array' && fields.length == 2) {
					$field_1 = $('.' + fields[0] + '-group');
					$field_2 = $('.' + fields[1] + '-group');

					if ($field_1.length && $field_2.length && ($field_1.data('pos') + 1) == $field_2.data('pos')) {
						$('.' + fields[0] + '-group, .' + fields[1] + '-group').wrapAll('<div class="proopc-row group-enabled" />');
					}
				}
			});
		}

		$('.proopc-bt-address input[type="text"], .proopc-bt-address input[type="email"], .proopc-st-address input[type="text"], .proopc-st-address input[type="email"], .proopc-additional-info input[type="text"], .proopc-confirm-order input[type="text"]').each(function() {
			$(this).css('width', '');
			var width = $(this).parent('.inner').width();
			$(this).width(width - 15);
		});

		$('.proopc-register-login input[type="text"], .proopc-register-login input[type="password"], .proopc-register-login input[type="email"]').each(function() {
			$(this).css('width', '');
			var width = $(this).parent('.proopc-input').width();
			$(this).width(width - 27);
		});

		$('.proopc-register-login button').each(function() {
			$(this).css('width', '');
			var width = $(this).parent('.proopc-input').outerWidth(true);
			$(this).width(width);
		});

		var e = $('#proopc-coupon .proopc-input-append').width(),
			t = $('#proopc-coupon').find('button.proopc-btn').outerWidth(true);
		$('#proopc-coupon-code').width(e - t - 20).css('margin-right', 5);
	};

	ProOPC.selectwidth = function() {
		$('.proopc-bt-address select, .proopc-st-address select, .proopc-additional-info select, .proopc-confirm-order select').each(function() {
			$(this).css('width', '');
			var width = $(this).parent('.inner').width();
			$(this).outerWidth(width - 3);
		});
		$('.proopc-register-login select').each(function() {
			$(this).css('width', '');
			var width = $(this).parent('.proopc-input').width();
			$(this).outerWidth(width - 27);
		});
	};

	ProOPC.productdetails = function() {
		if (!$('.proopc-cart-product').length) {
			return false;
		}

		if (!ProOPC.pluginLoaded('hoverIntent')) {
			console.warn('jQuery.hoverIntent plugin is not loaded. Product mouseover effect is being disabled to avoid errors.');
			return false;
		}

		$('.proopc-cart-product').each(function() {
			var that = this,
				target = $(that).data('details'),
				target = $('#' + target),
				targetTable = $('table.proopc-p-info-table', target);

			$(that).hoverIntent({
				interval: 100,
				sensitivity: 6,
				timeout: 100,
				over: function() {
					$(that).addClass('open');
					$(targetTable).width($(that).width());
					$(target).show().css('top', $(that).position().top).animate({
						opacity: 1,
						top: $(that).position().top + $(that).height()
					}, 150, 'linear');
				},
				out: function() {
					$(that).removeClass('open');
					$(target).animate({
						opacity: 0
					}, 100, 'linear', function() {
						$(this).hide().css('top', 0);
					});
				}
			});
		});
	};

	ProOPC.tooltip = function() {
		var timestamp = $.now(),
			$title = $('<div />', {
				'class': 'tooltip-title'
			}),
			$body = $('<div />', {
				'class': 'tooltip-body'
			}),
			$tooltip = $('<p />', {
				'class': 'proopc-tooltip'
			});

		$('.hover-tootip').each(function(key) {
			var title = $(this).attr('title');

			if (title) {
				$(this).attr('data-tiptext', title).removeAttr('title');
				$(this).data('uniqueid', 'proopc-tip-' + timestamp + '-key-' + key);
			}

			$(this).hover(function(e) {
					e.stopPropagation();
					var id = $(this).data('uniqueid'),
						tip = $(this).attr('data-tiptext'),
						title = null,
						body = null;

					if (id && tip) {
						if (!$('#' + id).length) {
							if (tip.indexOf('::') >= 0) {
								var parts = tip.split('::');
								title = parts[0];
								body = parts[1];
							} else {
								title = null;
								body = tip;
							}

							if (body) {
								$tooltip.html('').attr('id', id);

								if (title) {
									$tooltip.append($title.html(title));
								}

								$tooltip.append($body.html(body)).appendTo('body');
							}
						}

						$('#' + id).addClass('show');
					}
				},
				function() {
					var id = $(this).data('uniqueid');
					if (id && $('#' + id).length) {
						$('#' + id).removeClass('show');
						setTimeout(function() {
							$('#' + id).remove();
						}, 180);
					}
				}).mousemove(function(e) {
				var id = $(this).data('uniqueid');
				if (id && $('#' + id).length) {
					var $tip = $('#' + id),
						width = $tip.outerWidth(),
						height = $tip.outerHeight(),
						triggerHeight = $(this).outerHeight();

					$tip.css({
						top: e.pageY - (height + triggerHeight + 2),
						left: e.pageX - (width / 2)
					})
				}

			});
		});
	};

	ProOPC.fieldTip = function() {
		$('.hasFieldTip').hover(function() {
			if (!$(this).attr('title')) return;
			ProOPC.showFieldTip(this);
		}, function() {
			ProOPC.removeFieldTip(this, true);
		});
	};

	ProOPC.showFieldTip = function(field) {
		var $that = $(field),
			id = 'field-tip-' + $.now(),
			tip = $(field).attr('title'),
			width = $(field).outerWidth(),
			offset = $(field).offset();

		if ($that.data('tip')) return;

		$that.data('tipText', tip).data('tip', '#' + id).removeAttr('title');

		var $fieldTip = $('<p />', {
				'id': id,
				'class': 'proopc-fieldtip',
				'style': 'margin: 0px;'
			}),
			$body = $('<div />', {
				'class': 'fieldtip-body'
			}).html(tip);

		$fieldTip.html($body).css('visibility', 'hidden').appendTo('body');

		var tipWidth = $fieldTip.outerWidth(),
			tipHeight = $fieldTip.outerHeight(),
			top = offset.top - (tipHeight + 6),
			left = offset.left + (width - tipWidth) / 2;

		$fieldTip.css({
			top: top,
			left: left,
			visibility: ''
		}).fadeIn(200);

		$(win).resize(function() {
			var timeOut;
			if (timeOut) clearTimeout(timeOut);
			timeOut = setTimeout(function() {
				width = $that.outerWidth();
				offset = $that.offset();
				tipWidth = $fieldTip.outerWidth();
				tipHeight = $fieldTip.outerHeight();
				top = offset.top - (tipHeight + 6);
				left = offset.left + (width - tipWidth) / 2;

				$fieldTip.css({
					top: top,
					left: left,
				});
			}, 200);
		});

		if ($that.is(':hidden')) {
			ProOPC.removeFieldTip(field, true);
		}

		$('input[name="proopc-method"][type="radio"]').change(function() {
			ProOPC.removeFieldTip(field, true);
		});
	};

	ProOPC.removeFieldTip = function(field, restoreTitle) {
		var $that = $(field),
			tipId = $that.data('tip');

		if (!tipId || !$(tipId).length) return;

		if (restoreTitle) {
			$that.attr('title', $that.data('tipText'));
		}

		$that.data('tip', null);

		$(tipId).animate({
			opacity: 0
		}, 200, function() {
			$(this).remove();
		});
	};

	ProOPC.setStyle = function() {
		// Set equal height
		var maxHeight = 0,
			selector = '.proopc-register > .proopc-inner, .proopc-login > .proopc-inner';

		$(selector).css('min-height', '').each(function() {
			if ($(this).outerHeight() > maxHeight) {
				maxHeight = $(this).outerHeight();
			}
		});

		if (maxHeight > 0) {
			$(selector).css('min-height', maxHeight);
		}

		// Manage old reCaptcha version
		var $reCaptchaTable = $('#ProOPC #dynamic_recaptcha_1 table:visible');

		if ($reCaptchaTable.length) {
			var reCaptchaWidth = $reCaptchaTable.width(),
				$wrapper = $reCaptchaTable.closest('.proopc-input'),
				$label = $wrapper.siblings('.proopc-input-group-level');

			$wrapper.width(reCaptchaWidth);

			var totalWidth = $('.proopc-register > .proopc-inner').width() - 35;
			$label.width(totalWidth - reCaptchaWidth);
		}

		// Initialize common variables;
		var text, html;

		// Mark empty cells in tables
		$('table.cart-summary.proopc-table-striped td').each(function() {
			text = $(this).text();
			text = text ? $.trim(text) : '';
			html = $(this).html();
			html = html ? $.trim(html) : '';

			if ((!text || !text.length) && (!html || !html.length) || $(this).is(':empty')) {
				$(this).addClass('cell-empty');
			}
		});

		// Hide empty price description span elements for proper rendering of responsive tables
		$('table.cart-summary.proopc-table-striped span.vm-price-desc').each(function() {
			text = $(this).text();
			text = text ? $.trim(text) : '';
			html = $(this).html();
			html = html ? $.trim(html) : '';

			if ((!text || !text.length) && (!html || !html.length) || $(this).is(':empty')) {
				$(this).hide();
			}
		});

		// Add inline display style to price div element
		$('.proopc-p-price > div, .proopc-taxcomponent > div, .proopc-p-discount > div').each(function() {
			if ($(this).is(':visible')) {
				$(this).css('display', 'inline');
			}
		});

		// Logout mouseover effect
		$('.proopc-login-message-cont').hover(function() {
			$('.proopc-logout-cont').removeClass('hide')
		}, function() {
			$('.proopc-logout-cont').addClass('hide')
		});

		$('.proopc-logout-cont').css('min-width', $('.proopc-loggedin-user').width());

		// Internal redirect mechanism
		$('[data-vpopc="redirect"]').off('click').on('click', function(e) {
			e.preventDefault();

			var href = $(this).attr('href'),
				href = href ? href.replace('#', '') : null,
				vphref = $(this).data('vphref');

			if (href) {
				var $dummyInput = $('<input />', {
					'type': 'hidden',
					'name': 'ctask',
					'value': href
				});

				$('<form />', {
					'action': VPOPC._('URI'),
					'method': 'post'
				}).html($dummyInput).appendTo('body').submit();
			} else if (vphref) {
				win.location.href = vphref;
			}

			return false;
		});

		// To support Bonus cart extension
		if (typeof win.bonusCartItemIds !== typeof undefined && typeof win.bonusCartItemIds !== 'undefined') {
			$.each(win.bonusCartItemIds, function(e, t) {
				if (!t.userCanUpdateQuantity) {
					var updateform = $('input[data-vpid="' + t.cartItemId + '"]').parent('.proopc-input-append');
					var n = $(updateform).children('input[name="quantity"]').val();
					if (!$(updateform).hasClass('bonusSet')) {
						updateform.before(n);
						$(updateform).addClass('bonusSet')
					}
					updateform.hide();
					updateform.parent().find('button.remove_from_cart').hide();
				}
			});
		}

		// Format radio and checkbox display
		if (VPOPC._('STYLERADIOCHEBOX')) {
			var id;

			$('#UserRegistration input[type="radio"], #EditBTAddres input[type="radio"], #EditSTAddres input[type="radio"]').each(function() {
				$(this).css({
					'width': 'auto',
					'float': 'left'
				});
				id = $(this).attr('id');
				if (id) {
					$('label[for="' + id + '"]').addClass('proopc-radio-label');
				}
			});

			$('#UserRegistration input[type="checkbox"], #EditBTAddres input[type="checkbox"], #EditSTAddres input[type="checkbox"]').each(function() {
				$(this).css({
					'width': 'auto',
					'float': 'left',
					'margin': '4px 5px 0 0'
				}).parent('.proopc-input-append').css('padding-top', '4px').siblings('br').remove();

				id = $(this).attr('id');

				if (id) {
					$('label[for="' + id + '"]').css({
						'float': 'left',
						'padding-left': '10px',
						'padding-right': '10px'
					}).insertAfter(this);
				}
			});
		}

		// Show/hide credit card details section for a payment method
		$('.proopc-creditcard-info').each(function() {
			if (!$(this).closest('.vmpayment_cardinfo').length) {
				var $selector = $(this).prevAll('input[name="virtuemart_paymentmethod_id"]');

				if ($selector.length) {
					var pmtype = $selector.data('pmtype'),
						paypalType = $selector.data('paypalproduct'),
						cssClass = ' hide';

					if (pmtype) {
						if (paypalType) pmtype += paypalType;
						if ($selector.is(':checked')) cssClass = ' show';

						$(this).wrap('<span class="vmpayment_cardinfo additional-payment-info ' + pmtype + cssClass + '" />');
					}
				}
			}
		});

		// Initialise Klarna payment
		if (typeof klarna !== typeof undefined && typeof klarna !== 'undefined') {
			ProOPC.callKlarna();
		}

		// Improve few payment methods display
		if ($('#sisow_bank').closest('fieldset').length) {
			$('#sisow_bank').width($('#sisow_bank').closest('fieldset').width());
		}

		if ($('#monthinstallments').closest('.proopc-row').length) {
			$('#monthinstallments').width($('#monthinstallments').closest('.proopc-row').width());
		}

		if ($('.vmpayment_cardinfo').parent().is('div')) {
			$('.vmpayment_cardinfo').unwrap()
		}

		// Easy validate state fields
		$('#ProOPC select[name="virtuemart_state_id"], #ProOPC select[name="shipto_virtuemart_state_id"]').on('liszt:updated', function() {
			var id = $(this).attr('id'),
				that = id && $('select#' + id).length ? $('select#' + id)[0] : this,
				$form = $(that).closest('form'),
				$countryField = $('select[name="virtuemart_country_id"]', $form),
				$label = id ? $('label[for="' + id + '"]') : null,
				$asterisk = $label && $label.length ? $label.find('.asterisk') : null,
				$optgroups = $(that).find('optgroup'),
				$select = $(that).find('option:first'),
				$select = $select.length && !$select.val() ? $select : null,
				selectText = VPOPC.JText._('COM_VIRTUEMART_LIST_EMPTY_OPTION'),
				oldValue = $('input#STStateID').val(),
				selectedCountry;

			if ($select) {
				if (!$select.data('selectText')) {
					var selectText = $select.text();
					$select.data('selectText', selectText);
				} else {
					var selectText = $select.data('selectText');
				}
			}

			if (!$countryField.length) $countryField = $('select[name="shipto_virtuemart_country_id"]', $form);

			selectedCountry = $countryField.val();

			if (selectedCountry > 0) {
				$optgroups.each(function() {
					if ($(this).data('forcountry') && $(this).data('forcountry') != selectedCountry) {
						$(this).remove();
					} else if (!$('option', this).length) {
						$(this).remove();
					} else {
						$(this).data('forcountry', selectedCountry);
					}
				});
			}

			$(that).removeClass('required');

			if ($optgroups && $optgroups.length) {
				$(that).removeAttr('readonly')
				
				if($(that).data('frequired')) {
					$(that).attr('required', true);
					if ($asterisk) $asterisk.show();
				}
				
				if ($select) $select.text(selectText);

				if (id == 'shipto_virtuemart_state_id' && oldValue > 0 && !$(that).val() && $optgroups.find('option[value="' + oldValue + '"]').length) {
					$optgroups.find('option[value="' + oldValue + '"]').attr('selected', true);
					$(that).val(oldValue);
				}

			} else {
				$(that).removeAttr('required').attr('readonly', true);
				if ($asterisk) $asterisk.hide();
				if ($select) $select.text(VPOPC.JText._('COM_VIRTUEMART_NONE'));
			}

			$(that).trigger('opcvalidate');
		});

		// Remove all links with editpayment page uri
		if (VPOPC._('REMOVEUNNECESSARYLINKS') && VPOPC._('EDITPAYMENTURI')) {
			$('span.vmpayment a').each(function() {
				var $cardInfo = $(this).closest('label').siblings('.vmpayment_cardinfo');

				if ($cardInfo.length) {
					var $textDiv = $('<div />', {
						'class': 'proopc-payment-text'
					}).text($(this).text()).prependTo($cardInfo);

					$(this).remove();
				}
			});

			$('#ProOPC a').each(function() {
				if ($(this).attr('href') == VPOPC._('EDITPAYMENTURI')) {
					$(this).remove();
				}
			});
		}

		// Show TOS with Fancybox
		if (VPOPC._('TOSFANCY')) {
			if (!ProOPC.pluginLoaded('fancybox')) {
				console.warn('jQuery.fancybox plugin is not loaded. Terms of Service may not display properly.');
				return false;
			}

			var $content = $('div#proopc-tos-fancy'),
				$wrapper = $content.parent(),
				easing = $.easing && $.easing.hasOwnProperty('elastic') ? 'elastic' : 'fade';

			$('[data-tos="fancybox"]').fancybox({
				titlePosition: 'inside',
				padding: 0,
				showCloseButton: false,
				centerOnScroll: true,
				transitionIn: 'fade',
				transitionOut: easing,
				overlayOpacity: 0.8,
				overlayColor: '#000',
				onClosed: function() {
					if (!$(this.href).length) {
						$wrapper.html($content);
						$('div#proopc-tos-fancy button.fancy-close').click(function(e) {
							e.preventDefault();
							$.fancybox.close();
						})
					}
				}
			});

			$('div#proopc-tos-fancy button.fancy-close').click(function(e) {
				e.preventDefault();
				$.fancybox.close();
			});
		}

		// Auto scroll to top when page is reloaded after login/registration
		if (VPOPC._('RELOAD') && !$('#ProOPC').hasClass('loaded') && $('.proopc-reload').length) {
			$('html,body').animate({
				scrollTop: $('#proopc-system-message').offset().top - 100
			}, 500, function() {
				$('#ProOPC').addClass('loaded')
			})
		}

		// For Klarna payment
		var $klarnaRadio = $('input.klarnaPayment[type="radio"]'),
			$selectedRadio = $('form#proopc-payment-form').find('input[type="radio"][name="virtuemart_paymentmethod_id"]:checked');

		if ($klarnaRadio.length) {
			$klarnaRadio.attr('onclick', 'return ProOPC.setpayment(this);').closest('table').addClass('proopc-klarna-payment');
			if ($selectedRadio.length) ProOPC.klarnaOpenClose.call($selectedRadio[0]);
		}
		
		// Form PayPal Express
		$('.checkout-advertise .pp-credit a, .checkout-advertise .pp-express a').removeAttr('target');
	};

	ProOPC.isMobile = function() {
		var agent = navigator.userAgent.toLowerCase(),
			device = 'PC';

		if (agent.match(/android/i)) {
			device = 'Android';
		} else if (agent.match(/blackberry/i)) {
			device = 'BlackBerry';
		} else if (agent.match(/iphone/i)) {
			device = 'iPhone';
		} else if (agent.match(/ipad/i)) {
			device = 'iPad';
		} else if (agent.match(/ipod/i)) {
			device = 'iPod';
		} else if (agent.match(/opera mini/i)) {
			device = 'Opera Mini';
		} else if (agent.match(/iemobile/i)) {
			device = 'IEMobile';
		} else if (agent.match(/fban|fbav/i)) {
			device = 'Facebook';
		}

		if (device == 'PC') {
			$('html').addClass('device-is-pc').removeClass('device-is-handheld');
			return false;
		}

		$('html').addClass('device-is-handheld').removeClass('device-is-pc');
		return device;
	};

	ProOPC.klarnaOpenClose = function(e) {
		if (!$(this).hasClass('klarnaPayment')) return false;

		var self = this,
			$klarnaWraper = $(self).closest('proopc-klarna-payment'),
			$klarnaButton = $('.klarna_box_bottom_right .klarna_box_bottom_content .btn-additional-klarna', $klarnaWraper),
			width = 0;

		$klarnaWraper.siblings().find('.klarna_box_bottom:visible').hide();
		$('.proopc-klarna-payment input[name="klarna_paymentmethod"]').attr('disabled', true);
		$('input[name="klarna_paymentmethod"]', $klarnaWraper).removeAttr('disabled');
		$('.klarna_box_bottom:hidden', $klarnaWraper).css('opacity', 0).show();
		$('input[type="text"]', $klarnaWraper).width('auto');

		$('input[type="text"]:visible').each(function() {
			width = $(this).parent('div').width();
			if (width) $(this).css('max-width', width - 13);
		});

		$('.proopc-klarna-payment .klarna_box_bottom_input_combo').each(function() {
			$(this).children('div').width('100%')
		});

		if (!$klarnaButton.length) {
			$klarnaButton = $('<button />', {
				type: 'button',
				class: 'proopc-btn btn-additional-klarna',
				onclick: 'return ProOPC.setpayment(this);',
				style: 'margin-top: 15px;'
			}).text(VPOPC.JText._('COM_VIRTUEMART_SAVE'));

			$('.klarna_box_bottom_right .klarna_box_bottom_content', $klarnaWraper).append($klarnaButton);
		}

		$('div.klarna_box_bottom_title:visible, div.klarna_box_bottom_title:visible', $klarnaWraper).removeAttr('style');
		$('#box_klarna_consent_invoice:visible', $klarnaWraper).parent('div').addClass('proopc-klarna-consent-container').width('auto');
		$('#box_klarna_consent_part:visible', $klarnaWraper).parent('div').addClass('proopc-klarna-consent-container').width('auto');
		$('.klarna_box_bottom:visible', $klarnaWraper).animate({
			opacity: 1
		}, 300);
	};

	ProOPC.callKlarna = function() {
		var $body = $('body'),
			$baloon = $('#klarna_baloon').clone(),
			$blueBaloon = $('#klarna_blue_baloon').clone();

		$('.klarna_baloon, .klarna_blue_baloon', $body).remove();

		if (typeof klarna !== typeof undefined && typeof klarna !== 'undefined') {
			if (klarna.doDocumentIsReady !== typeof undefined && $.type(klarna.doDocumentIsReady) === 'function') {
				klarna.doDocumentIsReady($('.klarna_box'));
			}

			if (!klarna.unary_checkout) {
				var $selectedPayment = $('#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked');

				klarna.gChoice = '';

				if ($selectedPayment.hasClass('klarnaPayment')) {
					klarna.stype = $selectedPayment.data('stype');
					klarna.gChoice = $selectedPayment.attr('id');
				}

				if ($selectedPayment.length) ProOPC.klarnaOpenClose.call($selectedPayment[0]);
			}

			klarna.baloons_moved = true;
		}
	};

	ProOPC.getKlarnaForm = function() {
		var $selectedPayment = $('#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked');

		if (!$selectedPayment.hasClass('klarnaPayment')) {
			return false;
		}

		var $klarnaTable = $selectedPayment.closest('table'),
			data = $klarnaTable.find('*').serializeArray(),
			$form = $('<form />'),
			$inputField;

		data.push({
			name: "task",
			value: "setpayment"
		}).push({
			name: "view",
			value: "cart"
		}).push({
			name: "klarna_paymentmethod",
			value: selectedPayment.next("input").val()
		});

		$.each(data, function(key, object) {
			$inputField = $('<input />', {
				type: 'hidden',
				name: object.name,
				value: object.value
			});

			$form.append($inputField);
		});

		return $form;
	};

	ProOPC.setPreloaderState = function(state) {
		var preloader = $('#proopc-preloader');

		if (state == 'init') {
			$(preloader).addClass('proopc-started');
		} else {
			setTimeout(function() {
				$(preloader).addClass('proopc-loaded').removeClass('proopc-started');
			}, 100);
			setTimeout(function() {
				$(preloader).remove();
			}, 600);
		}
	};

	ProOPC.canCheckout = function() {
		if (!$('#ProOPC').length) return false;
		var that = $('#ProOPC')[0];

		if (!$(that).hasClass('emptyCart-view')) {
			ProOPC._canCheckoutTask.call(that, true);
			setInterval(function() {
				ProOPC._canCheckoutTask.call(that, false);
			}, 9e5);
		} else {
			ProOPC.setPreloaderState('done');
		}
	};

	ProOPC._canCheckoutTask = function(firstCall) {
		var that = this;

		ProOPC.ajax('cancheckout', {
			beforeSend: function() {
				if (firstCall) {
					ProOPC.setPreloaderState('init');
				}
			},
			success: function(data) {
				if ($.type(data) !== 'object') {
					$(that).removeClass('canCheckout');
					win.location.reload(true);
					return false;
				}

				if (firstCall) {
					ProOPC.setPreloaderState('done');
				}

				if (data.error) {
					$(that).removeClass('canCheckout');
					if (data.reload) ProOPC._reload();
				} else {
					$(that).addClass('canCheckout');
				}
			},
			errorCallback: function() {
				if (firstCall) {
					ProOPC.setPreloaderState('done');
				}
			}
		});
	};

	ProOPC.validateAllForms = function() {
		var $btForm = $('form#EditBTAddres'),
			$stForm = $('form#EditSTAddres'),
			$shipments = $('#proopc-shipments'),
			$payments = $('#proopc-payments'),
			$paymentsForm = $('form#proopc-payment-form'),
			$cartForm = $('form#cartFormFields'),
			$cartForm = (!$cartForm || !$cartForm.length) ? $('form#checkoutForm') : $cartForm,
			$tosField = $cartForm.find('input[name="tos"][type="checkbox"]'),
			$singleScreenSection = $('#proopc-entry-single'),
			BTasST = $('input#STsameAsBT[type="checkbox"]').length && $('input#STsameAsBT[type="checkbox"]').is(':checked');

		// Special checking for single screen layout
		if ($singleScreenSection.length) {
			var $typeSwitch = $('input[name="proopc-method"][type="radio"]', $singleScreenSection),
				$guestSwitch = $('input[name="proopc-method"][value="guest"][type="radio"]', $singleScreenSection);

			if ($typeSwitch.length) {
				if ($guestSwitch.length) {
					// Must be checking out as guest
					$guestSwitch.attr('checked', true).trigger('change');
				} else {
					ProOPC.setmsg(1, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_ONLY_REGISTERED_USER_CAN_CHECKOUT'));
					return false;
				}
			} else {
				ProOPC.setmsg(1, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_LOGIN_NEEDED'));
				return false;
			}
		}

		var vat_field = VPOPC._('EU_VAT_FIELD');
		
		if(vat_field && $.type(vat_field) === 'string' && $('input#' + vat_field + '_field').length) {
			if($('input#' + vat_field + '_field').hasClass('invalid')) {
				ProOPC.setmsg(1, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_EU_VAT_INVALID'));
				return false;
			}
		}
		
		var $BTasST = $('#ProOPC input#STsameAsBT[type="checkbox"]');
		
		if($BTasST.length && !$BTasST.is(':checked'))
		{
			var shipto_vat_field = 'shipto_' + vat_field;

			if(shipto_vat_field && $.type(shipto_vat_field) === 'string' && $('input#' + shipto_vat_field + '_field').length) {
				if($('input#' + shipto_vat_field + '_field').hasClass('invalid')) {
					ProOPC.setmsg(1, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_EU_VAT_INVALID'));
					return false;
				}
			}
		}

		if (!ProOPC._validateAddressForms.call($btForm)) {
			ProOPC.setmsg(1, VPOPC.JText._('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS'));
			return false;
		}

		if (!BTasST && !ProOPC._validateAddressForms.call($stForm)) {
			ProOPC.setmsg(1, VPOPC.JText._('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS'));
			return false;
		}

		if ($shipments.length && !$('input[name="virtuemart_shipmentmethod_id"][type="radio"]', $shipments).is(':checked')) {
			ProOPC.setmsg(1, VPOPC.JText._('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED'));
			return false;
		}

		if ($payments.length && !$('input[name="virtuemart_paymentmethod_id"][type="radio"]', $payments).is(':checked') && $('input#vpopc_pp_express_selected').val() <= 0) {
			ProOPC.setmsg(1, VPOPC.JText._('COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED'));
			return false;
		}

		var $selectedPayment = $('input[type="radio"][name="virtuemart_paymentmethod_id"]:checked', $paymentsForm);

		if ($selectedPayment.length && $selectedPayment.hasClass('klarnaPayment')) {
			var $klarnaTable = $selectedPayment.closest('table'),
				hasError = false,
				missingFields = [],
				text;

			$('input, select, textarea, .klarna_box_bottom_title', $klarnaTable).removeClass('invalid');

			$('input:visible', $klarnaTable).not(':checkbox').each(function() {
				if (!$(this).val()) {
					$(this).addClass('invalid').prev('.klarna_box_bottom_title').addClass('invalid');
					text = $(this).prev('.klarna_box_bottom_title').text();
					if (text) missingFields.push(text);
					hasError = true
				}
			});

			var errorAdded = false;

			$('select', $klarnaTable).each(function() {
				var value = $(this).val();
				if (!value || parseInt(value) == "" || parseInt(value) == 0 || isNaN(parseInt(value))) {
					$(this).addClass('invalid');
					$(this).closest('.klarna_box_bottom_input_combo').prev('.klarna_box_bottom_title').addClass('invalid');
					text = $(this).closest('.klarna_box_bottom_input_combo').prev('.klarna_box_bottom_title').text();
					if (text && !errorAdded) {
						missingFields.push(text);
						errorAdded = true;
					}
					hasError = true;
				}
			});

			$('input[type="checkbox"]', $klarnaTable).each(function() {
				if (!$(this).is(':checked')) {
					$(this).addClass('invalid').next('.klarna_box_bottom_title').addClass('invalid');
					missingFields.push('Klarna: ' + VPOPC.JText._('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS'));
					hasError = true;
				}
			});

			$('input, select, textarea', $klarnaTable).change(function() {
				if ($(this).is('input') && $(this).val() != '') {
					$(this).removeClass('invalid').prev('.klarna_box_bottom_title').removeClass('invalid');
				}
				if ($(this).is('select') && ($(this).val() != '' || $(this).val() != '0')) {
					$(this).removeClass('invalid').closest('.klarna_box_bottom_input_combo').prev('.klarna_box_bottom_title').removeClass('invalid');
				}
				if ($(this).is('input[type="checkbox"], input[type="radio"]') && $(this).is(':checked')) {
					$(this).removeClass('invalid').next('.klarna_box_bottom_title').removeClass('invalid');
				}
			});

			if (hasError && missingFields.length) {
				ProOPC.setmsg(1, VPOPC.JText._('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS') + ': ' + missingFields.join(', ') + '.');
				return false;
			}
		}

		if ($tosField.length && $tosField.attr('required') && !$tosField.is(':checked')) {
			var errorMessage = $tosField.data('label'),
				errorMessage = !errorMessage ? VPOPC.JText._('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS') : errorMessage;
			ProOPC.setmsg(1, errorMessage);
			return false;
		}

		if (!ProOPC._validateAddressForms.call($cartForm)) {
			ProOPC.setmsg(1, VPOPC.JText._('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS'));
			return false;
		}

		return true;
	}

	ProOPC._validateAddressForms = function() {
		$('input, select, textarea', this).each(function() {
			var id = $(this).attr('id');

			$(this).off('opcvalidate').on('change opcvalidate', function() {
				if ($(this).attr('required') || $(this).hasClass('required')) {
					$(this).attr('required', true);

					var value = $(this).val(),
						name = $(this).attr('name'),
						$form = $(this).closest('form');

					if (!value) {
						ProOPC._updateAddressFieldState.call(this, false);
					} else {
						ProOPC._updateAddressFieldState.call(this, true);
					}

					if (value && value.length && (name == 'email' || name == 'shipto_email' || name == 'verify_email')) {

						value = $.trim(value);
						$(this).val(value);

						if (name == 'email' || name == 'shipto_email') {
							if (!ProOPC.validateEmail(value)) {
								ProOPC._updateAddressFieldState.call(this, false);
							} else {
								$form.find('input[name="verify_email"]').trigger('change');
							}
						}

						if (name == 'verify_email') {
							var oEmail = $form.find('input[name="email"]').val();

							if (oEmail && value != oEmail) {
								ProOPC._updateAddressFieldState.call(this, false);
							}
						}
					}
				} else {
					ProOPC._updateAddressFieldState.call(this, true);
				}
			});
		}).trigger('opcvalidate');

		if ($(this).find('input.invalid').length || $(this).find('select.invalid').length || $(this).find('textarea.invalid').length) {
			return false;
		}

		return true;
	};

	ProOPC._updateAddressFieldState = function(valid) {
		var id = $(this).attr('id'),
			form = $(this).closest('form'),
			valid = ProOPC.hasValue(valid);

		if (valid) {
			$(this).removeClass('invalid');

			if (id && $('label[for="' + id + '"]', form).length) {
				$('label[for="' + id + '"]', form).removeClass('invalid');
			}
		} else {
			$(this).addClass('invalid');

			if (id && $('label[for="' + id + '"]', form).length) {
				$('label[for="' + id + '"]', form).addClass('invalid');
			}
		}
	};

	ProOPC.close = function(alert) {
		$(alert).parent('.proopc-alert').remove();
	};

	ProOPC.checkForError = function(data) {
		if (data && $.type(data) === 'object') {
			if (typeof data.error !== typeof undefined && data.error) {
				ProOPC.getcartlist();
				ProOPC.removePageLoader();
				ProOPC.enableSubmit();

				var message = VPOPC.JText._('PLG_VPONEPAGECHECKOUT_SYSTEM_ERROR_JS');

				if (typeof data.msg !== typeof undefined && data.msg) {
					message = data.msg;
				}

				// Restore normal ajaManager
				ProOPC.ajaxManager.setContinuous(false);
				ProOPC.setmsg(1, message);
			} else {
				return true;
			}
		} else {
			var error = this.hasOwnProperty('error') ? this.error : null;

			// Restore normal ajaManager
			ProOPC.ajaxManager.setContinuous(false);

			if (error && $.type(error) === 'function') {
				error.call(this);
			}
		}

		return false;
	};
	
	ProOPC.updateVATFieldState = function(valid, data, prefix) {
		var vat_field = VPOPC._('EU_VAT_FIELD');
		
		if(prefix && vat_field)
		{
			vat_field = prefix + vat_field;
		}
		
		if(vat_field && $.type(vat_field) === 'string' && $('input#' + vat_field + '_field').length) {
			if(valid) {
				$('input#' + vat_field + '_field, label[for="' + vat_field + '_field"]').removeClass('invalid');
				if($.type(data) === 'object' && data.hasOwnProperty(vat_field)) {
					$('input#' + vat_field + '_field').val(data[vat_field]);
				}
			} else {
				$('input#' + vat_field + '_field, label[for="' + vat_field + '_field"]').addClass('invalid');
			}
		}
	};

	ProOPC.commonError = function() {
		ProOPC.removePageLoader();
		ProOPC.enableSubmit();
		ProOPC.setmsg(1, VPOPC.JText._('PLG_VPONEPAGECHECKOUT_SYSTEM_ERROR_JS'));
	};

	ProOPC.submitOrder = function(button, step) {
		step = ProOPC.hasValue(step);

		if (!step || step == 1) {
			// Clear all old system messages
			$('#proopc-system-message').html('');

			if (!ProOPC.validateAllForms()) {
				return false;
			}

			// Prepare ajaxManager for order submit
			ProOPC.ajaxManager.setContinuous(true);

			// beforeSend Ajax call may be delayed.
			ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_SAVING_BILLING_ADDRESS');
			ProOPC.disableSubmit();
			
			var $BTasST = $('#ProOPC input#STsameAsBT[type="checkbox"]'),
				STsameAsBT = 0;
			
			if(!$BTasST.length || $BTasST.is(':checked'))
			{
				STsameAsBT = 1;
			}
			
			ProOPC.ajaxManager.addReq({
				data: 'ctask=savebtaddress&stage=final&STsameAsBT=' + STsameAsBT + '&' + $('form#EditBTAddres').serialize() + '&' + ProOPC.getToken(),
				beforeSend: function() {
					ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_SAVING_BILLING_ADDRESS');
					ProOPC.disableSubmit();
				},
				success: function(result) {
					if (!ProOPC.checkForError.call(this, result)) return false;
					ProOPC._triggerEvent('checkout.bt.saved');
					ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_BILLING_ADDRESS_SAVED');
					ProOPC.submitOrder(button, 2);
				},
				error: ProOPC.commonError
			}, 'savebtaddress');
		}

		if (step == 2) {
			var $BTasST = $('#ProOPC input#STsameAsBT[type="checkbox"]'),
				STsameAsBT = $('#proopc-hidden-confirm input[name="STsameAsBT"]:hidden'),
				shipTo = $('#proopc-hidden-confirm input[name="shipto"]:hidden');

			if (!$BTasST.length || $BTasST.is(':checked')) {
				$(STsameAsBT).val(1);
				$(shipTo).val(-1);
				ProOPC.submitOrder(button, 3);
				return false;
			} else {
				$(STsameAsBT).val(0);
				$(shipTo).val(0);
			}

			ProOPC.ajaxManager.addReq({
				data: 'ctask=savestaddress&stage=final&' + $('form#EditSTAddres').serialize() + '&' + ProOPC.getToken(),
				beforeSend: function() {
					ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_SAVING_SHIPPING_ADDRESS');
					ProOPC.disableSubmit();
				},
				success: function(result) {
					if (!ProOPC.checkForError.call(this, result)) return false;
					$(shipTo).val(result.userinfo_id);
					ProOPC._triggerEvent('checkout.st.saved');
					ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_SHIPPING_ADDRESS_SAVED');
					ProOPC.submitOrder(button, 3);
				},
				error: ProOPC.commonError
			}, 'savestaddress');
		}

		if (step == 3) {
			if($('input#vpopc_pp_express_selected').val() > 0) {
				ProOPC.submitOrder(button, 4);
			} else {
				ProOPC.ajaxManager.addReq({
					data: 'ctask=setpayment&ajax=1&savecc=1&finalise=1&' + $('form#proopc-payment-form').serialize() + '&' + ProOPC.getToken(),
					beforeSend: function() {
						ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_SAVING_CREDIT_CARD');
						ProOPC.disableSubmit();
					},
					success: function(result, textStatus, jqXHR) {
						if (jqXHR.getResponseHeader('content-type').indexOf('text/html') >= 0 && typeof klarna !== 'undefined') {
							ProOPC.removePageLoader();
							$('#proopc-order-process').remove();
							$('<div />', {
								id: 'proopc-temp',
								style: 'display:none'
							}).appendTo('body');
							$('#proopc-temp').append(result);
							var message = $('#proopc-temp').find('div#system-message-container').html();
							ProOPC.setmsg(1, message);
							ProOPC.enableSubmit();
							return false
						} else {
							if (typeof result === 'string') {
								result = $.parseJSON(result)
							}
							if (!ProOPC.checkForError.call(this, result)) return false;
							if (result.redirect != false) {
								win.location = result.redirect;
							} else {
								ProOPC._triggerEvent('checkout.paymentmethod.saved');
								ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_CREDIT_CARD_SAVED');
								ProOPC.submitOrder(button, 4);
							}
						}
					},
					error: ProOPC.commonError
				}, 'setpayment');
			}
		}

		if (step == 4) {
			var dataString = $('form#checkoutForm').serialize();
			if ($('form#cartFormFields').length) {
				dataString += ('&' + $('form#cartFormFields').serialize());
			}

			ProOPC.ajaxManager.addReq({
				data: 'ctask=savecartfields&' + dataString,
				beforeSend: function() {
					ProOPC.disableSubmit();
				},
				success: function(result, textStatus, jqXHR) {
					if (!ProOPC.checkForError.call(this, result)) return false;
					ProOPC._triggerEvent('checkout.cartfields.saved');
					ProOPC.submitOrder(button, 5);
				},
				error: ProOPC.commonError
			}, 'savecartfields');
		}

		if (step == 5) {
			ProOPC.ajaxManager.addReq({
				data: 'ctask=verifycheckout',
				beforeSend: function() {
					ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_VERIFYING_ORDER');
					ProOPC.disableSubmit();
				},
				success: function(result, textStatus, jqXHR) {
					if (!ProOPC.checkForError.call(this, result)) {
						ProOPC._triggerEvent('checkout.orderVerification.failed');
						return false;
					}

					ProOPC._triggerEvent('checkout.orderVerification.success');
					ProOPC.submitOrder(button, 6);
				},
				error: ProOPC.commonError
			}, 'verifycheckout');
		}

		if (step == 6 || step == 'confirm') {
			ProOPC.addPageLoader('PLG_VPONEPAGECHECKOUT_PLACING_ORDER');
			var confirmationForm = $('form#checkoutForm');

			/*
			if ($('form#cartFormFields').length) {
				$(confirmationForm).append($('form#cartFormFields').html());
			} */

			$(confirmationForm).append($('#proopc-hidden-confirm').html());

			setTimeout(function() {
				$(confirmationForm).submit();
			}, 100);
		}

		return false;
	};

	$(function() {
		ProOPC.isMobile();
		ProOPC.init(true);
		ProOPC.initSpinner();
		ProOPC.ajaxManager.run();
		ProOPC.canCheckout();
		ProOPC.setStyle();
		ProOPC.inputwidth();
		ProOPC.selectwidth();
		ProOPC.bindFormValidator();
		ProOPC.productdetails();
		ProOPC.tooltip();
		ProOPC.fieldTip();
		ProOPC.defaultSP();

		$(win).on('proopc:saferesize', function(event, type) {
			ProOPC.setStyle();
			ProOPC.inputwidth();
			ProOPC.selectwidth();
		});

		$(doc).ajaxStop(function() {
			ProOPC.init();
			ProOPC.enableMethodSelection();
		});
		
		$(doc).ajaxError(function() {
			ProOPC.enableMethodSelection();
		});

		$(win).load(function() {
			ProOPC.setStyle();
		});
	});

	win.addEventListener('orientationchange', function() {
		$(win).trigger('proopc:checkorientation');
	}, false);

	$(win).on('resize proopc:checkorientation', function(event) {
		if ((event.type && event.type === 'proopc:checkorientation') || !ProOPC.isMobile()) {
			if (ProOPC.cache.resizeTimeout) clearTimeout(ProOPC.cache.resizeTimeout);

			ProOPC.cache.resizeTimeout = setTimeout(function() {
				$(win).trigger('proopc:saferesize', [event]);
			}, 100);
		}
	});
})(ProOPC.jQuery, window, document);