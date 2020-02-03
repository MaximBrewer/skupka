/*global setTimeout,clearTimeout,jQuery,window,alert,document,jWait,confirm,ymaps,jAlert,FormData*/
(function ($) {
	'use strict';
	window.initRegistration = function () {
		var timerForErrorTooltip = 0,
			timerForCheckForm = 0,
			$form = $('#xdsoft_registration_organization_save'),
			validateRegistrationValue,
			dialog;
		// запоминашка форм
		if ($.fn.phoenix) {
			$('input:not([type=file]),textarea,select').phoenix();
		}
		// переключатели кнопок
		$('.btn-radio button').on('click', function () {
			var that = this;
			setTimeout(function () {
				$(that).find('input')[0].checked = !!$(that).hasClass('active');
				$(that).find('input').trigger('change');
			}, 100);
		});
		$('.btn-radio button').on('check', function () {
			var that = this;
			setTimeout(function () {
				$(that)[$(that).find('input')[0].checked ? 'addClass' : 'removeClass']('active');
			}, 100);
		})
			.trigger('check');

		$('#jform_organization_shedule_24').on('change', function () {
			$('#jform_organization_period_params')[this.checked ? 'hide' : 'show']();
		});

		$('#jform_organization_service_delivery').on('change', function () {
			$('#jform_organization_delivery_params')[!this.checked ? 'hide' : 'show']();
		});

		$('#jform_organization_acting_basis').on('change', function () {
			$('#jform_organization_acting_basis_params')[!parseInt($(this).val(), 10) ? 'hide' : 'show']();
		});

		$('#organization_fact_and_legal_equal').on('change', function () {
			$('#form_organization_address')[this.checked ? 'hide' : 'show']();
		});

		$('.maximum')
			.on('keydown change', function (e) {
				if ($(this).val().length > +$(this).attr('size')) {
					var pos = $(this).setCaret();
					$(this).val($(this).val().substr(0, (+$(this).attr('size') || 1500)));
					$(this).setCaret(pos);
				}
			});

		function makeAdresses(legal) {
			if (!$.kladr) {
				return false;
			}
			var $container = $('#form_organization_address' + legal),
				$zip = $container.find('#jform_organization_address' + legal + '_zip'),
				$city = $container.find('#jform_organization_address' + legal + '_city'),
				$street = $container.find('#jform_organization_address' + legal + '_street'),
				$building = $container.find('#jform_organization_address' + legal + '_building'),
				$tooltip = $container.find('.tooltip');

			function setLabel($input, text) {
				text = text.charAt(0).toUpperCase() + text.substr(1).toLowerCase();
				$input.parent().find('label').text(text);
			}

			function showError($input, message) {
				$tooltip.find('span').text(message);

				var inputOffset = $input.offset(),
					inputWidth = $input.outerWidth(),
					inputHeight = $input.outerHeight(),
					tooltipHeight = $tooltip.outerHeight();

				$tooltip.css({
					left: (inputOffset.left + inputWidth + 10) + 'px',
					top: (inputOffset.top + (inputHeight - tooltipHeight) / 2 - 1) + 'px'
				});

				$tooltip.show();
			}
			function coordinateUpdate() {
				var zoom = 4,
					geocode,
					address = $.kladr.getAddress('#form_organization_address' + legal, function (objs) {
						var result = '';
						$.each(objs, function (i, obj) {
							var name = '',
								type = '';

							if ($.type(obj) === 'object') {
								name = obj.name;
								type =  obj.type + ' ';

								switch (obj.contentType) {
								case $.kladr.type.region:
									zoom = 4;
									break;

								case $.kladr.type.district:
									zoom = 7;
									break;

								case $.kladr.type.city:
									zoom = 10;
									break;

								case $.kladr.type.street:
									zoom = 13;
									break;

								case $.kladr.type.building:
									zoom = 16;
									break;
								}
							} else {
								name = obj;
							}

							if (result) {
								result += ', ';
							}
							result += type + name;
						});

						return result;
					});

				if (address) {
					geocode = ymaps.geocode(address);
					geocode.then(function (res) {
						var position = res.geoObjects.get(0).geometry.getCoordinates();
						$('#jform_organization_address' + legal + '_lat').val(position[0]);
						$('#jform_organization_address' + legal + '_lan').val(position[1]);
					});
					$('#jform_organization_address' + legal + '_full').val($.kladr.getAddress('#form_organization_address' + legal));
					$('#jform_organization_address' + legal + '_zoom').val(zoom);
				}
			}
			$()
				.add($city)
				.add($street)
				.add($building)
				.kladr({
					parentInput: $container,
					verify: true,
					change: coordinateUpdate,
					select: function (obj) {
						setLabel($(this), obj.type);
						$tooltip.hide();
					},
					check: function (obj) {
						var $input = $(this);

						if (obj) {
							setLabel($input, obj.type);
							$tooltip.hide();
						} else {
							showError($input, 'Введено неверно');
						}
					},
					checkBefore: function () {
						var $input = $(this);

						if (!$.trim($input.val())) {
							$tooltip.hide();
							return false;
						}
					}
				});

			$city.kladr('type', $.kladr.type.city);
			$street.kladr('type', $.kladr.type.street);
			$building.kladr('type', $.kladr.type.building);

			$city.kladr('withParents', true);
			$street.kladr('withParents', true);

			// Отключаем проверку введённых данных для строений
			//$building.kladr('verify', false);

			// Подключаем плагин для почтового индекса
			$zip.kladrZip($container, {
				verify: true,
				select: coordinateUpdate
			});

		}

		makeAdresses('_legal');
		makeAdresses('');
		if (window.initAutoComplete !== undefined) {
			window.initAutoComplete();
		}
		if ($.fn.datetimepicker) {
			$('.date')
				.datetimepicker({
					onChangeDateTime: function (time, input) {
						input.removeClass('required');
					},
					timepicker: false,
					format: 'd.m.Y',
					allowEmpty: true
				});
		}
		if ($.fn.mask) {
			$('.date')
				.mask("##.##.####", {
					translation: {
						'#': {
							pattern: /[0-9]/,
							optional: true
						}
					},
					onComplete: function (cep, a, el) {
						$(el).removeClass('required');
					},
					onChange: function (val, a, el) {
						$(el).addClass('required');
					}
				});

			$(".phone")
                .each(function () {
                    $(this)
                        .mask($(this).data('format') || "+7 (9##) ###-##-##", {
                            translation: {
                                '#': {
                                    pattern: /[0-9]/,
                                    optional: true
                                }
                            },
                            onComplete: function (cep, a, el) {
                                $(el).removeClass('required');
                            },
                            onChange: function (val, a, el) {
                                $(el).addClass('required');
                            }
                        });
                });
		}
		// необходимо для сохраняшки
		setTimeout(function () {
			$('#organization_fact_and_legal_equal,#jform_organization_acting_basis,#jform_organization_service_delivery,#jform_organization_shedule_24,#jform_organization_icon').trigger('change');
			var e = $.Event("keyup");
			e.which = 50;
			$('.date, .phone').trigger(e);
		}, 100);

		window.hasError = false;

		$form
			.find('input:visible, select:visible, textarea:visible')
			.on('change keyup blur', function () {
				var that = this;
				clearTimeout(timerForCheckForm);
				timerForCheckForm = setTimeout(function () {
					if (that.id === 'jform_oferta') {
						$form.trigger('beforesubmit');
						if (that.checked) {
							that.checked = !window.hasError;
						}
					} else {
						$form.trigger('check');
					}
					if (!window.hasError) {
						$form.find('#submit_button').removeClass('disabled');
					} else {
						$form.find('#submit_button').addClass('disabled');
					}
				}, 300);
			});

		validateRegistrationValue = function (elm, value, required, nrules) {
			var rules = nrules.split(/\s/), i,
				urlexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
			for (i = 0; i < rules.length; i = i + 1) {
				switch (rules[i]) {
				case 'file':
					if (required && elm.files.length === 0) {
						return false;
					}
					break;
				case 'url':
					value = $.trim(value);
					if (!(!required && value === '') && (!urlexp.test(value))) {
						return false;
					}
					break;
				case 'email':
					value = $.trim(value);
					if (!(!required && value === '') && (!/^.+@.+$/.test(value))) {
						return false;
					}
					break;
				case 'license':
					value = $.trim(value);
					if (required && value === '') {
						return false;
					}
					break;
				case 'number':
					value = $.trim(value);
					if (!(!required && value === '') && (!/^[0-9]+$/.test(value))) {
						return false;
					}
					break;
				case 'string':
					value = $.trim(value);
					if (!(!required && value === '') && (value.length < 3)) {
						return false;
					}
					break;
				case 'bankname':
				case 'name':
				case 'short_string':
					value = $.trim(value);
					if (!(!required && value === '') && (value.length < 2 || ($(elm).attr('maxsize') !== undefined && value.length > $(elm).attr('maxsize')))) {
						return false;
					}
					break;
				case 'fio':
					value = $.trim(value);
					if (!(!required && value === '') && (value.length < 2 || ($(elm).attr('maxsize') !== undefined && value.length > $(elm).attr('maxsize')))) {
						return false;
					}
					break;
				case 'date':
				case 'phone':
					if (required) {
						return false;
					}
					break;
				case 'password':
					if (!(!required && value === '') && (value.length < 6 || value.length > $(elm).attr('maxsize'))) {
						return false;
					}
					break;
				case 'password2':
					if (!(!required && value === '') && (value !== $('#jform_organization_password1').val())) {
						return false;
					}
					break;
				case 'checkbox':
					if (!(!required && value === '') && !elm.checked) {
						return false;
					}
					break;
				}
			}
			return true;
		};

		$form.on('submit beforesubmit check', function (e) {
			var that;
			window.hasError = false;
			if (e.type !== 'check') {
				clearTimeout(timerForErrorTooltip);
			}
			$form.find('input:visible, select:visible, textarea:visible').each(function () {
				var that = this;
				if ($(that).hasClass('validate') && $(that).data('rules')) {
					if (!validateRegistrationValue(that, $(that).val(), $(that).hasClass('required'), $(that).data('rules'))) {
						if (e.type !== 'check') {
							$(that).closest('.control-group').addClass('error');
							that.focus();
							timerForErrorTooltip = setTimeout(function () {
								$(that).closest('.control-group').removeClass('error');
							}, 3000);
						}
						window.hasError = true;
						return false;
					}
					$(that).closest('.control-group').removeClass('error');
				}
			});

			// проверка выбранных дней недели
			if (!window.hasError && $form.find('input.organization_shedule_days.required').length) {
				that = $form.find('input.organization_shedule_days').eq(0)[0];
				if (!$form.find('input.organization_shedule_days:checked').length) {
					if (e.type !== 'check') {
						$(that).closest('.control-group').addClass('error');
						$('#jform_organization_shedule_24').focus();
						timerForErrorTooltip = setTimeout(function () {
							$(that).closest('.control-group').removeClass('error');
						}, 3000);
					}
					window.hasError = true;
				} else {
					$(that).closest('.control-group').removeClass('error');
				}
			}
			// проверка выбранных вариантов доставки
			if (!window.hasError && $form.find('#jform_organization_service_delivery.required').length) {
				that = $form.find('input.organization_service_delivery_variants').eq(0)[0];
				if (!$form.find('input.organization_service_delivery_variants:checked').length) {
					if (e.type !== 'check') {
						$(that).closest('.control-group').addClass('error');
						$('#jform_organization_service_delivery').focus();
						timerForErrorTooltip = setTimeout(function () {
							$(that).closest('.control-group').removeClass('error');
						}, 3000);
					}
					window.hasError = true;
				} else {
					$(that).closest('.control-group').removeClass('error');
				}
			}
			// проверка выбрано ли изображение иконки
			if (!window.hasError && $form.find('#jform_organization_icon.required').length) {
				that = $form.find('#jform_organization_icon.required').closest('.control-group');
				if (!$form.find('#jform_organization_icon.required').val()) {
					if (e.type !== 'check') {
						that.addClass('error');
						timerForErrorTooltip = setTimeout(function () {
							that.removeClass('error');
						}, 3000);
					}
					window.hasError = true;
				} else {
					that.removeClass('error');
				}
			}
			if (e.type === 'submit' && !window.hasError) {
				$(this).submitForm();
			}

			return false;
		})
			.on('reset', function (e) {
				if (confirm('Вы уверены?')) {
					return true;
				}
				return false;
			});
	};
	$(window.initRegistration);
	$.fn.setCaret = function (pos, a, b, c) {
		var target = this[0], range, re, rc, pos_start, pos_end;
		if (pos !== undefined) { //get
			if (target.selectionStart) { //DOM
				pos = target.selectionStart;
				return pos > 0 ? pos : 0;
			}
			if (target.createTextRange) { //IE
				target.focus();
				range = document.selection.createRange();
				if (range === null) {
					return '0';
				}
				re = target.createTextRange();
				rc = re.duplicate();
				re.moveToBookmark(range.getBookmark());
				rc.setEndPoint('EndToStart', re);
				return rc.text.length;
			}
			return 0;
		}

		//set
		pos_start = pos;
		pos_end = pos;

		if (a !== undefined) {
			pos_end = a;
		}

		if (target.setSelectionRange) {//DOM
			target.setSelectionRange(pos_start, pos_end);
		} else if (target.createTextRange) { //IE
			range = target.createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos_end);
			range.moveStart('character', pos_start);
			range.select();
		}
	};

	$.fn.submitForm = function () {
		return this.each(function () {
			var $that = $(this),
				data = new FormData(this),
				dialog = jWait('Пожалуйста подождите');

			$that.find('[type=submit]').attr('disabled', true);

            $.ajax({
				url: $(this).attr('action'),
				type: "POST",
				data: data,
				enctype: 'multipart/form-data',
				dataType: 'json',
				processData: false,  // tell jQuery not to process the data
				contentType: false   // tell jQuery not to set contentType
            })
				.done(function (resp) {
					if (!resp.status) {
						jAlert(resp.error_message);
					} else {
						jAlert(resp.error_message);
						$('body').trigger('successAddObject.xdsoft', [resp.object_id]);
					}
				})
				.always(function () {
					dialog.dialogBox('hide');
					$that.find('[type=submit]').removeAttr('disabled');
				});
		});
	};
	if (document.readyState !== 'complete') {
		window.initRegistration();
	}
}(window.XDjQuery || window.jQuery));
