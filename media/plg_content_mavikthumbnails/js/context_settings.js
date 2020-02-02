/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

jQuery(document).ready(function ($) {

    var params = [
        'thumbnails_for', 'class', 'resize_type',  'in_link',
        'default_size', 'default_width', 'default_height',
        'gallery_resize_type', 'gallery_width', 'gallery_height',
        'article_images_intro_width', 'article_images_intro_height',
        'article_images_use_intro', 'article_images_full_width',
        'article_images_full_height', 'popuptype', 'move_style'
    ];

    $.MavikthumbnailsContextsettings = function (elid, field) {
        var win = null; // window

        var paramFields = getParamFields();

        // Gray background for PopUp window
        var mask = $('<div>');
        mask.css({
            'background-color': '#000',
            'opacity': 0.4,
            'z-index': 9998,
            'position': 'fixed',
            'left': 0,
            'top': 0,
            'height': '100%',
            'width': '100%'}
        ).hide();
        mask.appendTo('body');

        /**
         * Open the window
         */
        var openWindow = function () {
            makeWin();
            win.show();
            resizeWin();
            mask.show();
        };

        /**
         * Build the window
         */
        var makeWin = function () {
            if (win) {
                return;
            }
            win = $('<div/>');

            win.append(makeForm());
            win.append(makePanel());
            win.appendTo('body');
            win.css({
                'padding': '5px',
                'background-color': '#fff',
                'display': 'none',
                'z-index': 9999,
                'position': 'absolute',
                'left': '50%',
                'top': ($(document).scrollTop() + ($(window).height() / 2)) - (win.outerHeight() / 2)
            });
            win.find('input, textarea').placeholderSimulate();
        };

        // Panel with buttons Save and Cancel
        var makePanel = function () {
            // Button SAVE
            var applyButton = $('<button class="btn button btn-primary"/>').text(Joomla.JText._('JAPPLY'));
            applyButton.on('click', function (e) {
                e.stopPropagation();
                store();
                close();
            });

            // Button CANCEL
            var cancelButton = $('<button class="btn button btn-link"/>').text(Joomla.JText._('JCANCEL'));
            cancelButton.on('click', function (e) {
                e.stopPropagation();
                close();
                win.empty();
                win = null;
            });

            // Panel with buttons
            var panel = $('<div class="controls form-actions"/>').css({
                'text-align': 'right',
                'margin-bottom': 0
            }).append([cancelButton, applyButton]);

            return panel;
        }

        /**
         * Add new Content to list
         */
        var makeContext = function (i, contextSettings) {

            var group = $('<div/>', {
                'class': 'accordion-group'
            });

            var heading = makeContextHeading(i, contextSettings);
            group.append(heading);

            var body = $('<div/>', {
                'id': 'collapse' + i,
                'class': 'accordion-body collapse'
            }).appendTo(group);
            body.on('show', function () {
                heading.link.css('display', 'none');
                heading.inputName.css('display', 'inline');
                heading.addClass('opened');
            });
            body.on('hideme', function () {
                heading.link.css('display', 'block');
                heading.inputName.css('display', 'none');
                heading.removeClass('opened');
                heading.inputName.blur();
            });

            var bodyIn = $('<div/>', {
                'class': 'accordion-inner'
            }).appendTo(body);

            bodyIn.append(makeConditions(contextSettings));
            bodyIn.append(makeSettings(contextSettings));

            group.open = function () {
                heading.link.click();
            };

            return group;
        };

        var makeContextHeading = function (i, contextSettings) {
            var heading = $('<div/>', {
                'class': 'accordion-heading',
                'style': 'background-color: #f5f5f5;'
            });

            $('<i/>', {
                'class': 'icon-move pull-left',
                'style': 'margin: 9px 10px; cursor: pointer;'
            }).appendTo(heading);

            $('<a/>', {
                'class': 'icon-slide-resize pull-right',
                'style': 'margin: 9px 10px; cursor: pointer;',
                'click': function() { heading.link.click(); return false; }
            }).appendTo(heading);

            $('<a/>', {
                'href': '#',
                'style': 'margin: 5px 10px;',
                'class': 'btn btn-mini btn-danger pull-right',
                'click': function () {
                    $(this).parents('.accordion-group')[0].remove();
                    return false;
                }
            }).append('<i class="icon-minus"></i>').appendTo(heading);

            heading.link = $('<a/>', {
                'class': 'accordion-toggle',
                'data-toggle': 'collapse',
                'data-parent': '#jform_params_context_settings_accordion',
                'href': '#collapse' + i
            }).append(contextSettings.title).appendTo(heading);

            heading.inputName = $('<input/>', {
                'type': 'text',
                'placeholder': Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_NAME'),
                'style': 'display: none; margin: 4px 10px;',
                'value': heading.link.text(),
                'change': function () {
                    heading.link.text($(this).val());
                },
                'blur': function() {
                    var el = $(this);
                    if (el.val().trim() === '') {
                        el.val('Context ' + i);
                        el.change();
                    }
                }
            }).appendTo(heading);

            return heading;
        };

        /**
         * Add conditions to content 
         */
        var makeConditions = function (contextSettings) {
            var conditions = $('<fieldset/>', {
                'class': 'adminform'
            });

            $('<legend/>', {
                'style': 'font-size: 1em;'
            }).append(Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_CONDITIONS')).appendTo(conditions);

            var conditionList = $('<ul/>', {
                'style': 'list-style: none; margin: 0;',
                'class': 'conditions'
            }).appendTo(conditions)

            $(contextSettings.conditions).each(function (i, condition) {
                conditionList.append(makeConditionItem(condition));
            })

            $('<a/>', {
                'href': '#',
                'style': 'margin: 4px 5px; vertical-align: top;',
                'class': 'btn btn-mini btn-success',
                'click': function () {
                    conditionList.append(makeConditionItem({}));
                    return false;
                }
            }).append('<i class="icon-plus"></i>').appendTo(conditions);

            return conditions;
        }

        /**
         * Add new item to condition
         */
        var makeConditionItem = function (condition) {

            var item = $('<li/>');
            var type = $('<select/>', {
                'style': 'width: 170px;',
                'class': 'type',
                'change': function () {
                    value.empty();
                    var nameType = 'text';
                    var namePlaceholder = '';
                    var nameStyle = 'width: 170px';
                    var valueStyle = 'width: 170px';
                    var valuePlaceholder = '';
                    switch (type.children('option:selected').val()) {
                        case 'context':
                            nameType = 'hidden';
                            valuePlaceholder = Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_CONTEXT_VALUE_LABEL');
                            valueStyle = 'width: 370px';
                            break;
                        case 'property':
                            namePlaceholder = Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_PROPERTY_NAME_LABEL');
                            valuePlaceholder = Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_VALUE_LABEL');
                            break;
                        case 'request':
                            namePlaceholder = Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_REQUEST_NAME_LABEL');
                            valuePlaceholder = Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_VALUE_LABEL');
                            break;
                    }
                    $('<input/>', {
                        type: nameType,
                        class: 'name',
                        'style': nameStyle,
                        placeholder: namePlaceholder,
                        value: condition.name
                    }).appendTo(value);
                    value.append(' in ');
                    $('<input/>', {
                        type: 'text',
                        class: 'value',
                        'style': valueStyle,
                        placeholder: valuePlaceholder,
                        value: condition.value
                    }).appendTo(value);
                }
            }).appendTo(item).append(
                '<option value="context" ' + (condition.type === 'context' ? 'selected="selected"' : "") + '>' + Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_CONTEXT') + '</option>\n\
                <option value="property" ' + (condition.type === 'property' ? 'selected="selected"' : "") + '>' + Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_PROPERTY') + '</option>\n\
                <option value="request" ' + (condition.type === 'request' ? 'selected="selected"' : "") + '>' + Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_REQUEST') + '</option>'
            );

            var value = $('<span/>').insertAfter(type);

            $('<a/>', {
                'href': '#',
                'style': 'margin: 4px 5px; vertical-align: top;',
                'class': 'btn btn-mini btn-danger',
                'click': function () {
                    item.remove();
                    return false;
                }
            }).append('<i class="icon-minus"></i>').appendTo(item);

            type.change();

            return item;
        }

        var makeSettingsItem = function (setting) {
            var field;
            var item = $('<li/>');
            // Make select Setting
            var param = $('<select/>', {
                'style': 'width: 350px;',
                'class': 'name',
                'change': function () {
                    // Make setting value
                    value.empty();
                    field = $(paramFields[param.val()].field.clone(true));
                    value.append(field);
                }
            }).appendTo(item);
            // Make options
            for (var i = 0; i < paramFields.length; ++i) {
                var option = $('<option/>', {'value': i});
                if (setting.name === paramFields[i].name) {
                    option.attr('selected', 'selected');
                }
                option.append(paramFields[i].tabLabel + ': ' + paramFields[i].label);
                param.append(option);
            }

            var value = $('<span/>').insertAfter(param);
            $('<a/>', {
                'href': '#',
                'style': 'margin: 4px 5px; vertical-align: top;',
                'class': 'btn btn-mini btn-danger',
                'click': function () {
                    heading
                    item.remove();
                    return false;
                }
            }).append('<i class="icon-minus"></i>').appendTo(item);

            param.change();

            // Set setting value
            if (field.attr('type') === 'text') {
                field.val(setting.value);
            } else if (field.prop('tagName') === 'SELECT') {
                field.find('option').filter(function () {
                    return $(this).val() === setting.value;
                }).attr('selected', true);
            } else if (field.prop('tagName') === 'FIELDSET') {
                field.find('input').each(function (i, input) {
                    input = $(input);
                    if (input.val() === setting.value) {
                        input.attr('checked', 'checked');
                    }
                });
            }

            return item;
        }

        /**
         * Add settings to content 
         */
        var makeSettings = function (contextSettings) {
            var settings = $('<fieldset/>', {
                'class': 'adminform'
            });

            var legend = $('<legend/>', {
                'style': 'font-size: 1em;'
            }).append(Joomla.JText._('PLG_MAVIKTHUMBNAILS_CONTEXT_SETTINGS')).appendTo(settings);

            var settingsList = $('<ul/>', {
                'style': 'list-style: none; margin: 0;',
                'class': 'settings'
            }).appendTo(settings)

            $(contextSettings.settings).each(function (i, setting) {
                settingsList.append(makeSettingsItem(setting));
            });

            $('<a/>', {
                'href': '#',
                'style': 'margin: 4px 5px; vertical-align: top;',
                'class': 'btn btn-mini btn-success',
                'click': function () {
                    settingsList.append(makeSettingsItem({}));
                    return false;
                }
            }).append('<i class="icon-plus"></i>').appendTo(settings);

            return settings;
        }

        /**
         * Make Form
         */
        var makeForm = function () {

            // Get settings from hidden field
            var settings = $.parseJSON(field.val());

            // Container
            var container = $('<div/>', {
                'style': 'display: inline-block; width: 670px; max-width: 100%;'
            });

            // List of contexts
            var list = $('<div/>', {
                'class': 'accordion',
                'id': 'jform_params_context_settings_accordion'
            }).appendTo(container);
            field.css('display', 'none');
            list.sortable();

            var index = 0;
            $(settings).each(function (i, contextSetings) {
                list.append(makeContext(i, contextSetings));
                index = i;
            });

            var addContentLink = $(document.createElement('a'));
            addContentLink.attr({
                'href': '#',
                'class': 'btn btn-success'
            });
            addContentLink.append('<i class="icon-plus"></i>');
            addContentLink.click(function () {
                var context = makeContext(++index, {
                    'name': 'new',
                    'conditions': [{}],
                    'settings': [{}]
                });
                list.append(context);
                context.open();
                return false;
            });
            list.after(addContentLink);

            return container;
        }

        /**
         * Re-center the window
         */
        var resizeWin = function () {
            var l = -1 * (win.width() / 2);

            win.css({'margin-left': l});
        };

        /**
         * Close the window
         */
        var close = function () {
            win.hide();
            mask.hide();
        };

        /**
         * Save the window fields back to the hidden element field (stored as JSON)
         */
        var store = function () {
            var fieldValue = [];
            $('#jform_params_context_settings_accordion .accordion-group').each(function (i, elContext) {
                elContext = $(elContext);
                var context = {};
                var elTitle = $(elContext.find('.accordion-heading input')[0]);
                elTitle.blur();
                context.title = elTitle.val();
                context.conditions = [];
                elContext.find('ul.conditions li').each(function (i, elCondition) {
                    elCondition = $(elCondition);
                    context.conditions[context.conditions.length] = {
                        type: $(elCondition.find('select.type')[0]).val(),
                        name: $(elCondition.find('input.name')[0]).val(),
                        value: $(elCondition.find('input.value')[0]).val()
                    };
                });
                context.settings = [];
                elContext.find('ul.settings li').each(function (i, elSetting) {
                    elSetting = $(elSetting);
                    var setting = {
                        name: paramFields[$(elSetting.find('select.name')[0]).val()].name,
                    };
                    var value;
                    elSetting.find('span select, span input').each(function (i, elInput) {
                        elInput = $(elInput);
                        var type = elInput.attr('type');
                        if (type !== 'radio' || elInput.attr('checked')) {
                            value = elInput.val();
                        }
                        ;
                    });
                    setting.value = value;
                    context.settings[context.settings.length] = setting;
                });
                fieldValue[fieldValue.length] = context;
            });

            field.val(JSON.stringify(fieldValue));
        };

        /**
         * Main click event on 'Settings' button to open the window.
         */
        $(document).on('click', '*[data-modal="' + elid + '"]', function (e, target) {
            field = $(this).next('input');
            origContainer = $(this).closest('div.control-group');
            openWindow();
            return false;
        });
    }

    var getParamFields = function () {
        var fields = [];
        for (var i = 0; i < params.length; ++i) {
            var oroginalField = $('#jform_params_' + params[i]);
            var label = $($(oroginalField.parents('.control-group')[0]).find('label')[0]).text();
            var field = oroginalField.clone();
            var tabName = oroginalField.parents('.tab-pane')[0].id;
            var tabLabel = $($('#myTabTabs').find("a[href='#" + tabName + "']")[0]).text();

            field.attr('id', '');
            field.css({'display': 'inline'})
            if (field.prop('tagName') == 'FIELDSET') {
                field.removeClass('radio btn-group btn-group-yesno');
                field.find('.btn').removeClass('btn btn-success btn-danger active');
                field.find('label,input').css({
                    'float': 'none',
                    'margin-left': '5px',
                    'display': 'inline'
                });
            }
            fields[i] = {
                'field': field,
                'label': label,
                'tabLabel': tabLabel,
                'name': params[i]
            };
        }
        return fields;
    }
});
