/*
* This is main JavaScript file to handle registration, shipping, payment and other functions of checkout
* 
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
var Onepage = {
    clearChromeAutocomplete: function () {

        // not possible, let's try:
        if (navigator.userAgent.toLowerCase().indexOf('chrome') >= 0) {
            af = Onepage.getElementById('adminForm');
            if (af != null) {
                af.setAttribute('autocomplete', 'off');
                setTimeout(function () {
                    Onepage.getElementById('adminForm').setAttribute('autocomplete', 'on');
                }, 1500);
            }
        }
    },
    getForm: function () {
        if (typeof document.adminForm != 'undefined') return document.adminForm;
        var d = Onepage.getElementById('adminForm');
        if (d != null) return d;


    },
    /*
* This function is ran by AJAX to generate shipping methods and tax information
*
*/
    op_runSS: function (elopc, onlyShipping, force, cmd, sync, custom_callback) {


        Onepage.defineGlobals();
        //reset form submission
        var dtx = Onepage.getElementById('form_submitted');
        if (dtx != null) dtx.value = '0';

        Onepage.getBusinessState();
        //deprecated attribute:
        sync = false;

        //
        if (typeof custom_callback == 'undefined') custom_callback = null;


        if (typeof callBeforeAjax != 'undefined')
            if (callBeforeAjax != null && callBeforeAjax.length > 0) {
                for (var x = 0; x < callBeforeAjax.length; x++) {
                    eval(callBeforeAjax[x]);
                }
            }

        var payment_id = Onepage.getPaymentId();
        var qfirst_run = false;

        op_saved_payment = payment_id;
        if ((typeof elopc != 'undefined') && (elopc != null && (elopc == 'init'))) {
            Onepage.clearChromeAutocomplete();
            // first run
            var elopc = Onepage.getElementById('virtuemart_country_id');
//	if ((el != null) && (el.value != ''))

            var ship_id = Onepage.getInputIDShippingRate();

            if (typeof opc_clickShippingAddress != 'undefined')
                if (opc_clickShippingAddress == true)
                    if (clickShippingAddress() == true) {
                        if ((typeof custom_callback != 'undefined') && (custom_callback)) {
                            custom_callback();
                        }
                        return true;
                    }


            var isReg = Onepage.isRegistration();

            if (isReg) {

                Onepage.ga('Registration Form Initialized', 'OPC Registration');
            }
            else {
                Onepage.ga('Checkout Form Initialized', 'Checkout General');
                Onepage.getTotals(ship_id, payment_id);
            }

            qfirst_run = true;
        }


        var delay_ship = '';
        if (!(cmd != null)) {


            if (typeof op_autosubmit == 'undefined') return;

            if (force == null && (!op_autosubmit))
                if (typeof(elopc) != 'undefined') {
                    if (op_delay && op_last_field) {
                        if (elopc != null)
                            if (elopc.name != null)
                                if (!(elopc.name == op_last1 || elopc.name == op_last2)) {
                                    Onepage.resetShipping();
                                    Onepage.showcheckout();
                                    delay_ship = '&delay_ship=delay_ship';

                                }
                                else {

                                }
                    }
                }
            if (typeof(elopc) == 'undefined' && (force == null) && (op_delay) && (!op_autosubmit)) {
                Onepage.resetShipping();
                delay_ship = '&delay_ship=delay_ship';
            }
            // op_last_field = false
            // force = false
            // op_delay = true
            // if delay is on, but we don't use last field, we will not load shipping
            if (op_delay && (!op_last_field) && (force != true)) {
                Onepage.resetShipping();
                delay_ship = '&delay_ship=delay_ship';
            }


            if (op_autosubmit) {
                if (document.adminForm != null) {
                    document.adminForm.submit();
                    return true;
                }
            }
            if (op_dontloadajax) {
                Onepage.showcheckout();
                Onepage.op_hidePayments();
                Onepage.runPay();
                return true;
            }


            //if ((op_noshipping == false) || (op_noshipping == null))

        }

        var ui = Onepage.getElementById('user_id');
        var user_id = 0;
        if (ui != null) {
            user_id = ui.value;
        }


        // if shipping section
        var country = '';
        var zip = '';
        var state = '';
        var address_1 = '';
        var address_2 = '';
        var onlyS = 'false';
        if (onlyShipping != null)
            if (onlyShipping == true) {
                onlyS = 'true';
            }
            else {
                onlyS = 'false';
            }
        shipping_open = Onepage.shippingOpen();


        /*
    addressq = Onepage.op_getaddress();
    country = Onepage.op_getSelectedCountry();
    country = Onepage.op_escape(country);
    zip = Onepage.op_getZip();
    zip = Onepage.op_escape(zip);
    state = Onepage.op_getSelectedState();
    state = Onepage.op_escape(state);
	*/


        var ship_to_info_id = 0;


        // if we are logged in
        if (!(op_logged_in != '1')) {

            ship_to_info_id = Onepage.getShipToId();


        }

        shipping_open = shipping_open.toString();
        var coupon_code = Onepage.getCouponCode();
        coupon_code = Onepage.op_escape(coupon_code);
        var sPayment = Onepage.getValueOfSPaymentMethod();
        sPayment = Onepage.op_escape(sPayment);
        var sShipping = "";

        if ((op_noshipping == false) || (op_noshipping == null)) {
            sShipping = Onepage.getVShippingRate();
            sShipping = Onepage.op_escape(sShipping);
            if (sShipping != '')
                op_saved_shipping_vmid = sShipping;
        }

        op_saved_shipping_local = op_saved_shipping;
        var op_saved_shipping2 = Onepage.getInputIDShippingRate();
        if (op_saved_shipping2 != 0)
            if (op_saved_shipping2 != "")
                op_saved_shipping = op_saved_shipping2;
        var op_saved_shipping_escaped = Onepage.op_escape(op_saved_shipping);


        //var query = 'coupon_code='+coupon_code+delay_ship+'&shiptoopen='+shipping_open+'&stopen='+shipping_open;
        var query = delay_ship + '&shiptoopen=' + shipping_open + '&stopen=' + shipping_open;

        if (opc_theme != null)
            if (opc_theme != '')
                query += '&opc_theme=' + opc_theme;

        var isB = Onepage.isBusinessCustomer();
        if (isB)
            query += '&opc_is_business=' + isB;

        query += Onepage.updateCheckboxProducts();

        if (qfirst_run) {
            query += '&first_run=1';
        }

        //var url = op_securl+"?option=com_onepage&view=ajax&format=raw&tmpl=component&op_onlyd="+op_onlydownloadable;


        if (!op_onlydownloadable) op_onlydownloadable = 0;

        var url = op_securl + "&nosef=1&task=opc&view=opc&format=opchtml&tmpl=component&op_onlyd=" + op_onlydownloadable;

        if (op_lang != '')
            url = url + "&lang=" + op_lang;


        var ajaxquery = '';

        if (typeof opc_ajax_fields != 'undefined') {
            ajaxquery = Onepage.buildExtra(opc_ajax_fields, false);
        }
        var extraquery = Onepage.buildExtra(op_userfields, true);

        var rde = Onepage.getElementById('third_address_opened');
        if (rde != null) rd = 1;
        else rd = 0;

        if (rd !== 0) {
            query += '&third_address_opened=1';
        }

        var paymentQ = Onepage.getPaymentExtras();


        if ((op_noshipping == false) || (op_noshipping == null)) {

            query += "&ship_to_info_id=" + ship_to_info_id + "&payment_method_id=" + sPayment + "&os=" + onlyS + "&user_id=" + user_id;
            query2 = "&selectedshipping=" + op_saved_shipping_escaped + "&shipping_rate_id=" + op_saved_shipping_vmid;
        }
        else {
            // no shipping section

            query += "&no_shipping=1&ship_to_info_id=" + ship_to_info_id + "&payment_method_id=" + sPayment + "&os=" + onlyS + "&user_id=" + user_id;
            query2 = '';
        }


        if (cmd != null)
            query += "&cmd=" + cmd;

        if ((op_virtuemart_currency_id != '0') && (op_virtuemart_currency_id != ''))
            query += '&virtuemart_currency_id=' + op_virtuemart_currency_id;
        else {
            if (typeof op_currency_id !== 'undefined')
                query += "&virtuemart_currency_id=" + op_currency_id;
        }

        query += ajaxquery;
        Onepage.op_log('ajaxquery: ' + ajaxquery);

        // dont do duplicit requests when updated from onblur or onchange due to compatiblity
        if (cmd != null) {

            // if we have a runpay request, check if the shipping really changed
            if (force != true)
                if (op_lastq == query && (op_saved_shipping_local == op_saved_shipping)) {
                    if (opc_debug) {
                        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                            Onepage.op_log('query not executed: ' + query);
                    }
                    if ((typeof custom_callback != 'undefined') && (custom_callback)) {
                        custom_callback();
                    }

                    return true;
                }
        }
        else if (op_lastq == query && (force != true)) {
            if (opc_debug) {
                if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                    Onepage.op_log('query not executed: ' + query);
            }
            if ((typeof custom_callback != 'undefined') && (custom_callback)) {
                custom_callback();
            }
            return true;
        }

        op_lastq = query;


        if (typeof paymentQ != 'undefined') {
            query += paymentQ;
            Onepage.op_log('paymentQ:' + paymentQ);
        }

        query += query2 + extraquery;

        Onepage.op_log('extraquery:' + extraquery);
        Onepage.op_log('query2:' + query2);


        if (typeof cmd == 'undefined') {
            cmd = '';
        }
        else if (cmd == 'runpay') {
            var stophere = true;
        }
        else if (!(cmd != null)) {
            cmd = '';
        }


        if (cmd != '')
            url = url + '&cmd=' + cmd;

        var myurl = url;

        Onepage.op_log('url:' + myurl);

        Onepage.showLoader(cmd);

        var type = 'POST';
        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
            type = 'POST';

        return Onepage.ajaxCall(type, myurl, query, sync, "application/x-www-form-urlencoded; charset=utf-8", Onepage.op_get_SS_response, custom_callback);

    },
    defineGlobals: function () {
        if (typeof opc_debug == 'undefined') opc_debug = false;
        if (typeof op_noshipping == 'undefined') op_noshipping = false;
        if (typeof op_saved_shipping == 'undefined') op_saved_shipping = 0;
        if (typeof opc_theme == 'undefined') opc_theme = '';
        if (typeof virtuemart_currency_id == 'undefined') virtuemart_currency_id = '';
        if (typeof op_onlydownloadable == 'undefined') op_onlydownloadable = 0;
        if (typeof op_securl == 'undefined') op_securl = 'index.php?option=com_onepage';
        if (typeof op_lang == 'undefined') op_lang = '';
        if (typeof op_subtotal_txt == 'undefined') op_subtotal_txt = '';
        if (typeof op_textinclship == 'undefined') op_textinclship = '';
        if (typeof op_shipping_txt == 'undefined') op_shipping_txt = '';
        if (typeof op_payment_fee_txt == 'undefined') op_payment_fee_txt = '';
        if (typeof op_coupon_discount_txt == 'undefined') op_coupon_discount_txt = '';
        if (typeof op_other_discount_txt == 'undefined') op_other_discount_txt = '';
        if (typeof op_min_pov_reached == 'undefined') op_min_pov_reached = true;


    },
    getElementById: function (selector) {
        return document.getElementById(selector);
    },
    getShipToId: function () {
        var ship_to_info_id = 0;
        //if (!shipping_open)
        {
            var d = Onepage.getElementById('ship_to_info_id_bt');
            if (d != null)
                ship_to_info_id = d.value;
        }
        //else
        {
            var st = document.getElementsByName('ship_to_info_id');

            if (st != null)
                for (var u = 0; u < st.length; u++) {

                    var ste = st[u];

                    if (ste.type == 'select-one') {
                        if ((ste.options != null) && (ste.selectedIndex != null))
                            ship_to_info_id = ste.options[ste.selectedIndex].value;
                    }
                    else if (ste.type == 'radio')
                        for (i = 0; i < ste.length; i++) {
                            if (ste[i].checked)
                                ship_to_info_id = Onepage.op_escape(ste[i].value);
                        }
                    break;
                }
        }

        return ship_to_info_id;
    },

    ajaxCall: function (type, url, query, sync, contentType, callBack, custom_callback) {

        if (opc_debug) {
            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                Onepage.op_log('current query data: ' + query);
        }

        if (typeof jQuery == 'undefined') {
            if ((typeof xmlhttp2 != 'undefined') && (xmlhttp2 != null)) {

            }
            else {
                if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp2 = new XMLHttpRequest();
                }
                else {// code for IE6, IE5
                    xmlhttp2 = new ActiveXObject("Microsoft.XMLHTTP");
                }
            }
            if (xmlhttp2 != null) {


                xmlhttp2.open(type, url, true);

                //Send the proper header information along with the request
                xmlhttp2.setRequestHeader("Content-type", contentType);
                //xmlhttp2.setRequestHeader("Content-length", query.length);
                //xmlhttp2.setRequestHeader("Connection", "close");
                if (!sync)
                    xmlhttp2.onreadystatechange = Onepage.op_get_SS_response;


                op_isrunning = true;
                xmlhttp2.send(query);


            }
        }
        else {


            jQuery.ajax({
                type: type,
                url: url,
                data: query,
                cache: false,
                complete: function (jqXHR, textStatus) {
                    callBack(jqXHR, false, custom_callback);
                },
                async: true

            });


        }


        return false;
    },

    getPaymentExtras: function () {
        if (typeof jQuery != 'undefined') {
            //ebrinstalments
            var p = jQuery('#payment_html');
            if (p != null) {

                var myInputs = p.clone()
                //myInputs = myInputs.filter("input:not[name*='cc']");
                var col = jQuery('<form>').append(myInputs);
                //var cc = jQuery(col).jQuery("input[name*='cc']");
                //if (typeof cc != 'undefined') cc.remove();
                //var data = col.serialize()

                var fields = col.serializeArray();
                var result = '';
                jQuery.each(fields, function (i, field) {
                    //Onepage.op_log(field);
                    // this code prevents ajax from sending CC data in any case, as far as the field name includes cc within it's name
                    if (field.name.indexOf('cc') < 0) {
                        result += '&' + field.name + '=' + Onepage.op_escape(field.value);
                    }


                });

                return result;

                //return '&'+data;
            }
        }
        else {
            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                Onepage.op_log('OPC: JQuery would be nice');
            var d = Onepage.getElementById('ebrinstalments');
            if (d != null)
                return '&ebrinstalments=' + d.value;
        }
        return '';
    },


    /*
* This is response function of AJAX
* Response is HTML code to be used inside noshippingheremsg DIV
*/
    op_get_SS_response: function (rawData, async, custom_callback) {

        if ((typeof rawData != 'undefined') && ((typeof rawData == 'XMLHttpRequest') || (typeof rawData.readyState != 'undefined')))
            var xmlhttp2_local = rawData;
        else if (typeof xmlhttp2 != 'undefined')
            var xmlhttp2_local = xmlhttp2;
        else return;

        var returnB = true;

        if ((typeof xmlhttp2_local != 'undefined') && (xmlhttp2_local != null)) {
            //if (opc_debug)
            if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                Onepage.op_log(xmlhttp2_local);
                if (typeof xmlhttp2 != 'undefined')
                    Onepage.op_log(xmlhttp2);
            }
        }

        var runjavascript = new Array();

        if (xmlhttp2_local.readyState == 4 && xmlhttp2_local.status == 200) {
            // here is the response from request
            var resp = xmlhttp2_local.responseText;
            if (resp != null) {
                //if (opc_debug)
                //Onepage.op_log(resp);
                // lets clear notices, etc...
                //try
                {
                    var part = resp.indexOf('{"cmd":');
                    if (part >= 0) {
                        if (part !== 0)
                            resp = resp.substr(part);

                        if (typeof resp.lastIndexOf != 'undefined') {
                            var last = resp.lastIndexOf('}');
                            if (last > 0) {
                                resp = resp.substr(0, last + 1);

                            }
                        }

                        if ((JSON != null) && (typeof JSON.parse != 'undefined')) {
                            try {
                                var reta = JSON.parse(resp);
                                Onepage.op_log('Using browsers JSON library');
                            }
                            catch (e) {
                                if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                                    Onepage.op_log(e);
                                    Onepage.op_log('Error in Json data');
                                    Onepage.op_log(resp);
                                    Onepage.op_log(xmlhttp2);
                                }

                                Onepage.ga('Error in Json data', 'Checkout Internal Error');
                            }
                        }
                        if ((typeof reta == 'undefined') || (!(reta != null))) {
                            try {
                                var reta = eval("(" + resp + ")");

                                Onepage.op_log('Using eval for JSON parsing');
                            } catch (e) {
                                Onepage.op_log(e);
                            }
                        }


                        if (reta.cart_empty) {
                            return Onepage.emptycart();
                        }


                        if (typeof reta.sthtml != 'undefined') {
                            Onepage.setSTAddressHtml(reta.sthtml);
                        }


                        if (typeof reta.min_pov != 'undefined') {
                            Onepage.setMinPov(reta.min_pov);
                        }


                        if ((reta.shipping != null) || (typeof reta == 'undefined')) {

                            var shippinghtml = reta.shipping;
                        }
                        else {
                            var shippinghtml = resp;
                            return;
                        }

                        if (typeof reta.couponcode != 'undefined')
                            var coupon_code = reta.couponcode;
                        else
                            var coupon_code = '';

                        var coupon_percent = 0;
                        if (typeof reta.couponpercent != 'undefined')
                            coupon_percent = reta.couponpercent;


                        if (typeof reta.inform_html != 'undefined')
                            if (reta.inform_html != null)
                                if (reta.inform_html != "") {
                                    var d = Onepage.getElementById('inform_html');
                                    if (d != null)
                                        Onepage.setInnerHtml(d, reta.inform_html);
                                    //d.innerHTML = reta.inform_html;
                                }

                        if (reta.username_exists != null) {
                            Onepage.username_check_return(reta.username_exists);
                        }

                        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email !== true))
                            if (reta.email_exists != null) {

                                Onepage.email_check_return(reta.email_exists);
                            }

                        if ((reta.totals_html != null) && (reta.totals_html != '')) {

                            dx = Onepage.getElementById('opc_totals_hash');
                            if (dx != null)
                                Onepage.setInnerHtml(dx, reta.totals_html);
                            //dx.innerHTML = reta.totals_html;
                        }

                        if (typeof reta.msgs != 'undefined')
                            if (reta.msgs != null)
                                Onepage.setMsgs(reta.msgs);


                        if (typeof reta.clear_msgs !== 'undefined')
                            if (reta.clear_msgs) {
                                var de = Onepage.getElementById('opc_error_msgs');
                                var sys = Onepage.getElementById('system-message');
                                if (typeof jQuery !== 'undefined') {
                                    if (de != null) {
                                        jQuery(de).fadeOut('fast');
                                    }

                                    if (sys != null) {
                                        jQuery(sys).fadeOut('fast');

                                    }
                                }
                            }


                        if (typeof reta.debug_msgs != 'undefined')
                            if (reta.debug_msgs != null)
                                Onepage.printDebugInfo(reta.debug_msgs);


                        //else alert('error');

                        if (reta.javascript != null)
                            runjavascript = reta.javascript;

                        if (typeof reta.opcplugins != 'undefined')
                            if (reta.opcplugins != null) {
                                Onepage.processPlugins(reta.opcplugins);
                            }

                        var vatmsg = '';

                        if (typeof reta.checkvat != 'undefined')
                            if (reta.checkvat != null) {
                                vatmsg = reta.checkvat;

                            }
                        Onepage.showVatStatus(vatmsg);

                        if (typeof reta.new_vat != 'undefined')
                            if (reta.new_vat != null) {

                                if (typeof opc_vat_field === 'undefined') opc_vat_field = 'opc_vat';

                                var dd = Onepage.getElementById(opc_vat_field + '_field');
                                if (dd != null) {
                                    dd.value = reta.new_vat;
                                }
                                else {
                                    var dd = Onepage.getElementById(opc_vat_field);
                                    if (dd != null)
                                        dd.value = reta.new_vat;
                                }
                            }


                        if (reta.klarna != null) {
                            //Onepage.op_log(reta.klarna);
                            Onepage.setKlarnaAddress(reta.klarna);
                        }

                        var payment_extra = '';
                        if (typeof reta.payment_extra != 'undefined')
                            if (reta.payment_extra != null)
                                payment_extra = reta.payment_extra;


                        if (reta.payment != null)
                            paymenthtml = reta.payment;


                        //END of valid json response

                    }
                    else {
                        var shippinghtml = resp;
                    }


                }


                d2 = Onepage.getElementById('op_last_field_msg');
                if (d2 != null) {
                    //d2.innerHTML = '';
                    Onepage.setInnerHtml(d2, '');
                }


                if (resp.indexOf('payment_inner_html') > 0)
                    if ((typeof paymenthtml != 'undefined') && (paymenthtml != null))
                        Onepage.setPaymentHtml(paymenthtml, payment_extra);

                // != 'opc_do_not_update')
                if (shippinghtml.indexOf('opc_do_not_update') < 0)
                    Onepage.setShippingHtml(shippinghtml);

                //after basket is shown:
                if (typeof reta != 'undefined')
                    if (typeof reta.basket != 'undefined') {
                        if (reta.basket != '')
                            Onepage.showNewBasket(reta.basket);
                    }
                Onepage.showCouponCode(coupon_code, coupon_percent);


                Onepage.showcheckout();
            }
            else {

            }

            if ((op_saved_shipping != null) && (op_saved_shipping != "")) {
                var ss = Onepage.getElementById(op_saved_shipping);
                if (ss != null) {
                    //Onepage.op_log(op_saved_shipping);
                    // we use try and catch here because we don't know if what type of html element is the shipping
                    try {
                        // for option
                        ss.selected = true;
                        // for checkbox and radio
                        ss.checked = true;
                    }
                    catch (e) {
                        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                            Onepage.op_log(e);

                        Onepage.ga(e.toString(), 'Checkout Internal Error');

                    }
                }
            }

            if ((op_saved_payment != null) && ((op_saved_payment != "") && (op_saved_payment != 0))) {
                var ss = Onepage.getElementById(op_saved_payment);
                if (ss != null) {
                    //Onepage.op_log(op_saved_payment);
                    // we use try and catch here because we don't know if what type of html element is the shipping
                    try {
                        // for option
                        ss.selected = true;
                        // for checkbox and radio
                        ss.checked = true;
                    }
                    catch (e) {
                        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                            Onepage.op_log(e);

                        Onepage.ga(e.toString(), 'Checkout Internal Error');
                    }
                }
            }

            Onepage.op_resizeIframe();

            Onepage.op_hidePayments();
            Onepage.runPay();

            if (typeof callAfterAjax != 'undefined')
                if (callAfterAjax != null && callAfterAjax.length > 0) {
                    for (var x = 0; x < callAfterAjax.length; x++) {
                        eval(callAfterAjax[x]);
                    }
                }


        }
        else {
            if (xmlhttp2_local.readyState == 4 && ((xmlhttp2_local.status >= 499))) {
                // here is the response from request
                var resp = xmlhttp2_local.responseText;
                // changed in 2.0.227
                if (resp != null) {
                    resp = '<input type="hidden" name="invalid_country" value="invalid_country" />';
                    resp += '<br style="clear: both;"/><div class="opcstatustext" style="clear: both;">' + JERROR_AN_ERROR_HAS_OCCURRED + '</div><br style="clear: both;"/><div style="clear: both;"> <a href="#" onclick="return Onepage.refreshShippingRates();" >' + COM_ONEPAGE_CLICK_HERE_TO_REFRESH_SHIPPING + '</a></div>';
                    if (typeof xmlhttp2_local.statusText != 'undefined') {
                        resp += '<br style="clear: both;"/><div style="clear: both; color:red;" class="statustext">' + xmlhttp2_local.statusText + '</div>';
                        Onepage.ga(xmlhttp2_local.statusText, 'OPC Registration');

                    }
                    Onepage.setShippingHtml(resp);
                }
            }
        }// end response is ok

        if (typeof custom_callback != 'undefined')
            if (custom_callback != null) custom_callback();

        // run shipping and payment javascript

        if (typeof callAfterRender != 'undefined')
            if (callAfterRender != null && callAfterRender.length > 0) {
                for (var x = 0; x < callAfterRender.length; x++) {
                    eval(callAfterRender[x]);
                }
            }

        if (runjavascript.length > 0) {
            for (var s = 0; s < runjavascript.length; s++) {
                try {
                    eval(runjavascript[s]);
                }
                catch (e) {
                    if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                        Onepage.op_log(e);

                    Onepage.ga(e.toString(), 'Checkout Internal Error');
                }
            }
        }
        var dtx = Onepage.getElementById('form_submitted');
        if (dtx != null) dtx.value = '0';

        return true;
    },
    emptycart: function () {
        location.reload();
    },
    checkMinPov: function () {

        if (typeof opc_logic != 'undefined')
            if (opc_logic == 'opcregister') return true;

        var d = Onepage.getElementById('opc_min_pov');
        if (d != null) {
            msg = d.value;
            if ((msg !== '') || (msg.length > 0)) {
                alert(msg);
                Onepage.ga(msg, 'Checkout Error');
                return false;
            }
        }
        return true;
    },

    setMinPov: function (msg) {
        var d = Onepage.getElementById('opc_min_pov');
        if (d != null) {
            d.value = msg;
        }
    },

    setInnerHtml: function (el, html) {

        if (typeof jQuery != 'undefined') {
            var el = jQuery(el);
            el.html(html);
            if (typeof el.trigger != 'undefined') {
                el.trigger('refresh');
                el.trigger('create');
            }

            var parent = el.parent();

            if (typeof el.parent != 'undefined') {
                el.parent('create');
                if (typeof el.trigger != 'undefined')
                    el.parent().trigger('create');
            }

        }
        else {
            /*
	 var d = Onepage.getElementById(el.id);
	 if (d != null)
	 */
            el.innerHTML = html;
        }

    },
    doublemail_checkMail: function () {
        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) {
            return true;
        }
        msg = Onepage.getElementById('email2_info');
        if (!doubleEmailCheck())
            msg.style.display = 'block';
        else
            msg.style.display = 'none';

        return true;
    },

    setSTAddressHtml: function (html) {
        var d = Onepage.getElementById('edit_address_list_st_section');
        if (d != null)
            Onepage.setInnerHtml(d, html);
        //d.innerHTML = html;

        var e1 = Onepage.getElementById('ship_to_info_id_bt');
        if (e1 != null) {
            var bt_id = e1.value;
            var el = Onepage.getElementById('id' + bt_id);
            Onepage.changeST(el);
        }
    },
    changeSTajax: function (el) {

        if (typeof el.options != 'undefined')
            if (typeof el.selectedIndex != 'undefined')
                if (el.selectedIndex >= 0) {
                    var stID = el.options[el.selectedIndex].value;
                    var e1 = Onepage.getElementById('ship_to_info_id_bt');
                    if (e1 != null) {
                        var bt_id = e1.value;
                        if (bt_id == stID) {
                            return Onepage.setSTAddressHtml('');
                        }
                    }
                    Onepage.op_runSS(el, false, true, 'getST', false);
                }
    },

    doubleEmailCheck: function (useAlert) {
        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) {
            return true;
        }

        e1 = Onepage.getElementById('email_field');
        e2 = Onepage.getElementById('email2_field');
        msg = Onepage.getElementById('email2_info');
        if (e1 !== null && e2 != null) {
            if (e1.value != e2.value) {
                if (useAlert != null && useAlert == true) {
                    msg_txt = msg.innerHTML;
                    Onepage.ga(msg_txt, 'Checkout Error');
                    alert(msg_txt);
                }
                return false;
            }
            else {
                return true;
            }
        }
        return true;
    },
    refreshShipping: function (elopc) {
        Onepage.resetShipping();
        Onepage.op_runSS(null, null, true);
        elopc.href = "#";
        return false;
    },

    resetShipping: function () {
        d = Onepage.getElementById('shipping_goes_here');

        if (d != null) {
            if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
                d.style.minHeight = d.style.height;
            //d.innerHTML = '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" />';
            var invH = '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" />';
            Onepage.setInnerHtml(d, invH);
        }


        d2 = Onepage.getElementById('op_last_field_msg');
        if (d2 != null) {
            if (op_refresh_html != '')
                Onepage.setInnerHtml(d2, '');
            //d2.innerHTML = '';
        }
        return false;
    },

    setShippingHtml: function (html) {
        if ((typeof op_dontrefresh_shipping != 'undefined') && (op_dontrefresh_shipping == true)) return;

        if (html === '') return;
        //if (op_shipping_div == null)
        {
            var sdiv = null;
            sdiv = Onepage.getElementById('ajaxshipping');
            var sib = Onepage.getElementById('shipping_inside_basket');
            var sib2 = Onepage.getElementById('shipping_goes_here');
            if ((typeof(sib) != 'undefined') && (sib != null)) {
                sdiv = sib;
            }

            if (sib2 != null) {
                sdiv = sib2;
            }
            var op_shipping_div = sdiv;
        }

        if ((typeof callAfterResponse !== 'undefined') && (callAfterResponse != null && callAfterResponse.length > 0)) {
            for (var x = 0; x < callAfterResponse.length; x++) {
                eval(callAfterResponse[x]);
            }
        }

        if (op_shipping_div != null) {
            if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
                op_shipping_div.style.minHeight = op_shipping_div.style.height;
            var savedHeight = op_shipping_div.clientHeight;

            //op_shipping_div.innerHTML = html;
            Onepage.setInnerHtml(op_shipping_div, html);
            if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
                op_shipping_div.style.minHeight = savedHeight + 'px';

        }


    },


    showLoader: function (cmd) {
        if ((typeof op_loader == 'undefined') || (!op_loader)) return;
        if (callBeforeLoader != null && callBeforeLoader.length > 0) {
            for (var x = 0; x < callBeforeLoader.length; x++) {
                ret = eval(callBeforeLoader[x]);
                if (ret == 2) {
                    return;
                }

            }
        }

        if (cmd != null) {
            if (cmd == 'runpay') {
                var pp = Onepage.getElementById('payment_html');
                if ((pp != null) && ((typeof opc_payment_refresh == 'undefined') || (opc_payment_refresh == false))) {
                    var savedHeight = pp.clientHeight;
                    if (typeof op_loader_img != 'undefined') {
                        //pp.innerHTML = '<img src="'+op_loader_img+'" title="Loading..." alt="Loading..." /><span class="payment_loader_msg">'+COM_ONEPAGE_PLEASE_WAIT_LOADING+'</span>';
                        var html = '<img class="opc_loader_img" src="' + op_loader_img + '" title="Loading..." alt="Loading..." /><span class="payment_loader_msg">' + COM_ONEPAGE_PLEASE_WAIT_LOADING + '</span><input type="hidden" name="virtuemart_paymentmethod_id" id="payment_id_0" value="0" not_a_valid_payment="not_a_valid_payment" />';
                        Onepage.setInnerHtml(pp, html);
                        if ((typeof opc_autoresize == 'undefined') || ((typeof opc_autoresize != 'undefined') && (opc_autoresize != false)))
                            pp.style.minHeight = savedHeight + 'px';
                    }
                }
            }
            if ((cmd === '') || (cmd.indexOf('shipping') >= 0)) {
                if (op_delay) Onepage.resetShipping();
                if (op_loader)
                    if (typeof op_loader_img != 'undefined')
                        Onepage.setShippingHtml('<img class="opc_loader_img" src="' + op_loader_img + '" title="Loading..." alt="Loading..." /><input type="hidden" name="please_wait_fox_ajax" id="please_wait_fox_ajax" value="please_wait_fox_ajax" />' + '<span class="shipping_loader_msg">' + COM_ONEPAGE_PLEASE_WAIT_LOADING + '</span>');
            }

        }
        else {
            if (op_delay) Onepage.resetShipping();
            if (op_loader) {

                Onepage.setShippingHtml('<img class="opc_loader_img" src="' + op_loader_img + '" title="Loading..." alt="Loading..." /><input type="hidden" name="please_wait_fox_ajax" id="please_wait_fox_ajax" value="please_wait_fox_ajax" />');


            }
        }
    },

    getCouponCode: function () {
        var x = Onepage.getElementById('op_coupon_code');
        if (typeof x != 'undefined' && (x != null)) {
            return Onepage.op_escape(x.value);
        }
        var x = document.getElementsByName('coupon_code');
        if (x != null)
            for (var j = 0; j < x.length; j++) {
                if (typeof x[j].value != 'undefined')
                    if (x[j].value != '')
                        return x[j].value;
            }

        return "";
    },

    showcheckout: function () {
        var op_div = Onepage.getElementById("onepage_main_div");
        if ((op_div != null) && (op_min_pov_reached == true)) {
            if (op_div.style != null)
                if (op_div.style.display == 'none') {
                    //will show OPC if javascript and ajax test OK


                    op_div.style.display = '';
                }

        }


    },


    togglePaymentDisplay: function (show, foceOff) {
        if ((typeof force_zero_paymentmethod !== 'undefined') && (force_zero_paymentmethod)) {
            show = true;
            foceOff = false;
        }

        var d = Onepage.getElementById('payment_top_wrapper');
        if ((typeof d != 'undefined') && (d != null)) {

            if (typeof jQuery != 'undefined')
                if (typeof default_payment_zero_total != 'undefined') {

                    if (!show) {
                        var jel = jQuery(d);
                        var hf = jQuery('#vmMainPageOPC');
                        var test = hf.data('storedpayment');
                        var storedHtml = jel.clone();
                        if (storedHtml.attr('rel') !== 'defualt_payment_wrap')
                            if ((typeof test == 'undefined') || (!test)) {
                                hf.data('storedpayment', storedHtml);
                            }
                        var zeroPayment = jQuery('<div id="payment_top_wrapper"><input type="hidden" value="' + default_payment_zero_total + '" name="virtuemart_paymentmethod_id" id="payment_id_' + default_payment_zero_total + '" /></div>');
                        jel.replaceWith(zeroPayment);
                    }
                    else {
                        var jel = jQuery(d);
                        var hf = jQuery('#vmMainPageOPC');
                        var stored = hf.data('storedpayment');
                        if ((typeof stored !== 'undefined') && (stored))
                            jel.replaceWith(stored);
                    }
                }


            if ((typeof foceOff != 'undefined') && (foceOff != null) && (foceOff == true)) {
                d.foceOff = true;
            }

            if ((typeof d.foceOff != 'undefined') && (d.foceOff != null) && (d.foceOff == true)) {
                d.style.display = 'none';
                return;
            }


            if (!show) {
                d.style.display = 'none';
            }
            else {
                d.style.display = 'block';
            }
        }


    },
    setPaymentHtml: function (html, extra) {
        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
            Onepage.op_log('setPaymentHtml');
        if (html.indexOf('force_show_payments') >= 0) {
            Onepage.togglePaymentDisplay(true);
        }
        else if (html.indexOf('force_hide_payments') >= 0) {
            Onepage.togglePaymentDisplay(false, true);

        }


        var insertHtml = '';
        var appendExtra = true;

        if (extra != '') {
            for (var id in extra) {
                var myid = parseInt(id);
                if (myid != 'NaN')
                    if (typeof extra[id] != 'undefined')
                        if (extra[id] != '') {

                            var d = Onepage.getElementById('extra_payment_' + myid);
                            // only if it doesn't exists
                            if (!(d != null)) {
                                insertHtml += extra[id];
                            }
                        }
            }

            var d = Onepage.getElementById('payment_extra_outside_basket');

            if (d != null) {



                //update opc 2.0.226: d.innerHTML = extra;
                var extras = document.createElement('div');
                //extras.innerHTML = insertHtml;
                Onepage.setInnerHtml(extras, insertHtml);
                d.appendChild(extras);
                appendExtra = false;

            }
        }

        //if (appendExtra) html = html+insertHtml;

        var d = Onepage.getElementById('payment_html');
        //


        if (d != null) {
            var htmltest = d.innerHTML;
            if (htmltest.indexOf('canvas') < 0) {
                // d.innerHTML = html;
                if ((d.innerHTML == '') || ((typeof opc_payment_refresh == 'undefined') || (opc_payment_refresh == false)))
                    Onepage.setInnerHtml(d, html);
            }
        }

        var myTotals = Onepage.getTotalsObj();
        if (myTotals.totalsset) {
            myTotals.order_total.value = parseFloat(myTotals.order_total.value);
            if ((myTotals.order_total.value === 0) || (myTotals.order_total.value < 0.01)) {
                var dn = Onepage.getElementById('virtuemart_paymentmethod_id_0');
                if (dn != null) {
                    if (typeof dn.checked !== 'undefined') dn.checked = true;
                    if (typeof dn.selected !== 'undefined') dn.selected = true;
                }
            }
        }

        return true;
    },

    validateVat: function (el) {
        if (typeof checkVATNumber == 'undefined') return;

        var newVATNumber = checkVATNumber(el.value);
        if (newVATNumber) {
            if (el.value != newVATNumber)
                el.value = newVATNumber;
            el.className.split('invalid').join('');
        }
        else {
            el.className += ' invalid';
        }

        return Onepage.op_runSS(this);
    },

    validateOpcEuVat: function (el) {
        if (typeof op_loader_img != 'undefined') {
            var loader = '<img class="opc_loader_img" src="' + op_loader_img + '" title="Loading..." alt="Loading..." />';
            Onepage.showVatStatus(loader);

        }
        Onepage.op_runSS(el, false, true, 'checkvatopc');
        return true;
    },
    validateBitVat: function (el) {
        Onepage.op_runSS(el, false, true, 'checkvat');
    },
    showVatStatus: function (vat) {

        var d = Onepage.getElementById('vat_info');


        if (d != null) {
            //d.innerHTML = vat;
            Onepage.setInnerHtml(d, vat);
            if (vat == '') {
                d.style.display = 'none';
            }
            else {

                d.style.display = '';


            }
        }

        if (vat != '') {


            var d2 = Onepage.getElementById('opc_vat_info_field');
            if (typeof d2 != 'undefined')
                if (d2 != null)
                    d2.value = vat;
        }

        //Onepage.op_log('VAT:'+vat);
    },
    printDebugInfo: function (msgs) {
        if (typeof msgs.length != 'undefined') {
            for (var i = 0; i < msgs.length; i++) {

                Onepage.op_log(msgs[i]);
            }
        }
        else {
            Onepage.op_log(msgs);
        }
    },

    removeCoupon: function () {
        Onepage.op_runSS(null, false, true, 'removecoupon');
        return false;
    },

    showNewBasket: function (html) {


        var d = Onepage.getElementById('opc_basket');
        if (d != null) {
            //d.innerHTML = html;
            Onepage.setInnerHtml(d, html);
        }


        if (typeof jQuery != 'undefined') {

            if (typeof opc_basket_wrap_id != 'undefined') {
                var b = Onepage.getElementById(opc_basket_wrap_id);
            }
            else
                var b = Onepage.getElementById('opc_basket');

            if (b != null)
                Onepage.jQueryLoader(b, true);
        }

        Onepage.getTotals();


    },

    jQueryLoader: function (el, hide) {

        if (typeof jQuery != 'undefined') {

            if (typeof jQuery.mobile != 'undefined') {
                el = jQuery(el);
                if (!hide) {
                    var $this = jQuery(el),
                        theme = $this.jqmData("theme") || jQuery.mobile.loader.prototype.options.theme,
                        msgText = $this.jqmData("msgtext") || jQuery.mobile.loader.prototype.options.text,
                        textVisible = $this.jqmData("textvisible") || jQuery.mobile.loader.prototype.options.textVisible,
                        textonly = !!$this.jqmData("textonly");
                    html = $this.jqmData("html") || "";


                    jQuery.mobile.loading('show', {
                        text: msgText,
                        textVisible: textVisible,
                        theme: theme,
                        textonly: textonly,
                        html: html
                    });
                    return;
                }
                else {
                    if (typeof jQuery.mobile != 'undefined')
                        jQuery.mobile.loading("hide");
                    return;
                }
            }

            if (typeof el != 'undefined')
                if (el != null) {
                    if (typeof el.style.opacity !== 'undefined') {
                        if (!hide) {
                            el.style.opacity = 0.5;
                        }
                        else {
                            el.style.opacity = 1;
                        }
                    }
                }
        }

    },
    showCouponCode: function (couponcode, couponpercent) {

        if (typeof couponcode == 'undefined') return;

        // remove coupon html:
        var r = '<a href="#" onclick="javascript: return Onepage.removeCoupon()" class="remove_coupon">X</a>';
        var dt = Onepage.getElementById('tt_order_discount_after_txt_basket_code');
        if (parseInt(couponpercent) == 0) couponpercent = '';

        var html = '';
        if (couponcode.indexOf('awocoupons') >= 0) {
            var html = couponcode;
        }

        if (dt != null) {
            if (couponcode == '') {
                //dt.innerHTML = '';
                Onepage.setInnerHtml(dt, '');
                var d2 = Onepage.getElementById('tt_order_discount_after_div_basket');
                if (d2 != null)
                    d2.style.display = 'none';
            }
            else {

                //dt.innerHTML = '('+couponcode+' '+couponpercent+' '+r+')';
                if (html == '')
                    html = '(' + couponcode + ' ' + couponpercent + ' ' + r + ')';
                Onepage.setInnerHtml(dt, html);
            }
        }
        else {
            var d1 = Onepage.getElementById('tt_order_discount_after_txt_basket');
            var newd = document.createElement('span');
            newd.id = 'tt_order_discount_after_txt_basket_code';
            if (couponcode == '') {
                //newd.innerHTML = '';
                Onepage.setInnerHtml(newd, '');
                var d2 = Onepage.getElementById('tt_order_discount_after_div_basket');
                if (d2 != null)
                    d2.style.display = 'none';

            }
            else {


                if (html == '') {
                    //newd.innerHTML = '('+couponcode+' '+couponpercent+' '+r+')';
                    Onepage.setInnerHtml(newd, '(' + couponcode + ' ' + couponpercent + ' ' + r + ')');
                }
                else {
                    newd.innerHTML = html;
                    Onepage.setInnerHtml(newd, html);
                }

            }
            if (d1 != null)
                d1.appendChild(newd);
        }

        //tt_order_discount_after_txt
        var dt = Onepage.getElementById('tt_order_discount_after_txt_code');
        if (dt != null) {
            if (couponcode == '') {
                var d2 = Onepage.getElementById('tt_order_discount_after_div_basket');
                if (d2 != null)
                    d2.style.display = 'none';

                dt.innerHTML = '';
            }
            else {
                if (html == '')
                    dt.innerHTML = '(' + couponcode + ' ' + couponpercent + ' ' + r + ')';
                else dt.innerHTML = html;
            }
        }
        else {
            var d1 = Onepage.getElementById('tt_order_discount_after_txt');
            if (d1 != null)
                if (d1.innerHTML == '')
                    d1.innerHTML = op_coupon_discount_txt;
            var newd = document.createElement('span');
            newd.id = 'tt_order_discount_after_txt_code';
            if (couponcode == '') {
                var d2 = Onepage.getElementById('tt_order_discount_after_div_basket');
                if (d2 != null)
                    d2.style.display = 'none';

                newd.innerHTML = '';
            }
            else {
                if (html === '')
                    newd.innerHTML = ' (' + couponcode + ')';
                else newd.innerHTML = html;

            }
            if (d1 != null)
                d1.appendChild(newd);
        }

    },
    refreshShippingRates: function () {
        Onepage.op_runSS(null, false, true);
        return false;

    },
    setMsgs: function (msgs) {


        var isReg = Onepage.isRegistration();
        var de = Onepage.getElementById('opc_error_msgs');
        var sys = Onepage.getElementById('system-message');
        if (msgs.length > 0) {

            Onepage.errorMsgTimer = null;

            for (var i = 0; i < msgs.length; i++) {
                if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                    Onepage.op_log('OPC Alert: ' + msgs[i]);
                //if (!no_alerts)
                Onepage.opc_error(msgs[i]);
            }
            if (typeof jQuery != 'undefined')
                if (jQuery != null)
                    if (typeof jQuery.scrollTo != 'undefined')
                        jQuery.scrollTo('#opc_error_msgs'); //, 800, {easing:'elasout'} );

            if (!isReg) {

                if (sys != null)
                    if (typeof jQuery != 'undefined') {
                        var jd = jQuery(sys);
                        if (!jQuery.contains(sys, de)) {
                            Onepage.hideIn10Seconds(sys);
                        }
                        else {
                            jd.show();
                        }
                    }
                    else {
                        sys.style.display = 'none';
                    }
            }


        }
        else {


            if (de != null) {
                Onepage.hideIn10Seconds(sys);
                Onepage.hideIn10Seconds(de);

            }
        }


    },
    hideIn10Seconds: function (el) {
        Onepage.errorMsgTimer = setTimeout(function () {
            jQuery(el).fadeOut('fast');
        }, 10000);
    },
    opc_error: function (msg) {
        /*var d = Onepage.getElementById('system-message');
   if (d != null) {
	   var htest = d.innerHTML;
	   if (htest.indexOf(msg)>=0) return;
   }*/

        var d = Onepage.getElementById('opc_error_msgs');
        if (d != null) {
            if (d.innerHTML.toString().indexOf(msg) < 0) {
                d.innerHTML += msg + '<br />';

            }
            d.style.display = 'block';
        }
        else {
            if (typeof jQuery !== 'undefined') {
                var o = jQuery('#vmMainPageOPC');
                var html = '<div class="opc_errors" id="opc_error_msgs" style="display: block; width: 100%; clear: both; border: 1px solid red;">' + msg + '</div>';
                o.prepend(html);
            }
        }

    },

    changeTextOnePage3: function (op_textinclship, op_currency, op_ordertotal) {

        Onepage.op_hidePayments();

        // disabled here 17.oct 2011
        // it should not be needed as it is fetched before ajax call

        Onepage.changeTextOnePage(op_textinclship, op_currency, op_ordertotal);
        op_saved_shipping = Onepage.getInputIDShippingRate();
    },
    getSPaymentElement: function (element) {
        if (typeof element != 'undefined')
            if (element != null) {
                if (typeof element.value != 'undefined')
                    if (element.value != null) return element;

                if (element.type == 'select-one')
                    return element.options[element.selectedIndex];

            }
        // get active shipping rate
        var e = document.getElementsByName("virtuemart_paymentmethod_id");

        //var e = document.getElementsByName("payment_method_id");


        var svalue = "";

        if (typeof e.type != 'undefined')
            if (e.type == 'select-one') {
                var ind = e.selectedIndex;

                if (ind < 0) ind = 0;
                var value = e.options[ind];
                return value;
            }


        if (e)
            if (typeof e.checked != 'undefined')
                if (e.checked) {
                    svalue = e.value;
                    return e;
                }

        if (!svalue) {

            for (i = 0; i < e.length; i++) {
                if (e[i].type == 'select-one') {
                    if (e[i].options.length <= 0) return;
                    var ind = e[i].selectedIndex;
                    if (ind < 0) ind = 0;
                    var value = e[i].options[ind].value;
                    return e[i].options[ind];
                }

                if (e[i].checked == true) {
                    var svalue = e[i].value;
                    return e[i];
                }
            }
        }
        //if (svalue) return svalue;

        // last resort for hidden and not empty values of payment methods:
        for (i = 0; i < e.length; i++) {
            if (e[i].value != '') {
                if (e[i].id != null && (e[i].id != 'payment_method_id_coupon'))
                    return e[i];
            }
        }

        return;


    },
    // returns value of selected payment method
    getValueOfSPaymentMethod: function (element) {
        var e = Onepage.getSPaymentElement(element);
        if (e != null) return e.value;
        return "";


    },

    // returns amount of payment discout withou tax
    op_getPaymentDiscount: function () {

        var id = Onepage.getValueOfSPaymentMethod();
        if ((id) && (id != "")) {
            if (typeof(pdisc) !== 'undefined')
                if (pdisc[id]) {
                    if (typeof(op_payment_discount) !== 'undefined') op_payment_discount = pdisc[id];
                    return pdisc[id];
                }
        }
        return 0;
    },


    // returns value of selected shipping method
    getVShippingRate: function (getfullvalue) {
        // get active shipping rate


        var svalue = "";
        {
            e = document.getElementsByName("virtuemart_shipmentmethod_id");
            if (e != null) {
                for (i = 0; i < e.length; i++) {
                    if (e[i].type == 'select-one') {
                        if (e[i].options.length <= 0) return "";
                        index = e[i].selectedIndex;
                        if (index < 0) index = 0;
                        var val = e[i].options[index].value;

                        if (getfullvalue != null)
                            if (getfullvalue == true)
                                return val;

                        var ee = val.split('|');
                        if (ee.length > 1) return ee[0];
                        return val;
                    }
                    else if ((e[i].checked == true) && (e[i].style.display != 'none')) {
                        var val = e[i].value;

                        if (getfullvalue != null)
                            if (getfullvalue == true)
                                return val;

                        var ee = val.split('|');
                        if (ee.length > 1) return ee[0];
                        return val;

                    }
                }
            }
        }

        if (svalue) {
            return svalue;
        }
        return "";

    },
    // returns input id of selected shipping method
    //note: for checkout return attribute saved_id
    // rel_id has higher priority then id
    // if above tests fail, return just normal id
    // if nothing found returns value


    getInputIDShippingRate: function (fromSaved) {


        var e = document.getElementsByName("virtuemart_shipmentmethod_id");

        var id = "";
        for (i = 0; i < e.length; i++) {
            if (typeof e[i] == 'undefined') continue;

            if (e[i].type == 'select-one') {
                if (e[i].options.length <= 0) return "";
                index = e[i].selectedIndex;
                if (index < 0) index = 0;
                if (fromSaved != null)
                    if (fromSaved) {
                        var saved_id = Onepage.getAttr(e[i].options[index], 'saved_id');
                        if (saved_id != null) {
                            return saved_id;
                        }
                    }
                var rel_id = Onepage.getAttr(e[i].options[index], 'rel_id');
                if (rel_id != null) {

                    return rel_id;
                }
                if (typeof e[i].options[index].id != 'undefined') return e[i].options[index].id;

            }
            else if ((e[i].checked == true) && (e[i].style.display != 'none')) {
                if (fromSaved != null)
                    if (fromSaved) {
                        var saved_id = Onepage.getAttr(e[i], 'saved_id');
                        if (saved_id != null) {
                            return saved_id;
                        }
                    }
                var rel_id = Onepage.getAttr(e[i], 'rel_id');
                if (rel_id != null) {

                    return rel_id;
                }

                // if you marked your shipping radio with multielement="id_of_the_select_drop_down"
                var multi = e[i].getAttribute('multielement', false);
                if (multi != null) {
                    var test = Onepage.getElementById(multi);
                    if (test != null) {
                        if ((test.options != null) && (test.selectedIndex != null)) {
                            if (test.options[test.selectedIndex] != null)
                                if (test.options[test.selectedIndex].getAttribute('multi_id') != null) {
                                    return test.options[test.selectedIndex].getAttribute('multi_id');
                                }
                        }
                    }
                    var test2 = document.getElementsByName(multi);
                    if (test2 != null) {
                        for (var i2 = 0; i2 < test2.length; i2++) {
                            if (test2[i2].checked != null)
                                if (test2[i2].checked)
                                    if (test2[i2].id != null) {
                                        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                                            Onepage.op_log('cpsol: ' + test2[i2].id);

                                        return test2[i2].id;
                                    }
                        }
                    }

                }

                if (typeof e[i] != 'undefined') {
                    if (e[i].id != null)
                        return e[i].id;
                    else return e[i].value;
                }
                else {
                    Onepage.op_log(e);
                }
            }
            else if (e[i].type == "hidden") {
                if ((e[i].value.indexOf('free_shipping') >= 0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon') < 0))) return e[i].id;
                if ((e[i].value.indexOf('choose_shipping') >= 0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon') < 0))) {
                    return e[i].id;
                }
            }
        }

        return "";


    },


    formatCurrency: function (total) {

        if ((total == 0) || (isNaN(parseFloat(total)))) total = '0.00';
        var arr = op_vendor_style.split('|');

        if (arr.length > 6) {
            var sep = arr[3];
            var tsep = arr[4];
            var dec = arr[2];
            var stylep = arr[5];
            // 0 = '00Symb';
            // 1 = '00 Symb'
            // 2 = 'Symb00'
            // 3 = 'Symb 00';
            var stylen = arr[6];
            // 0 = (Symb00)
            // 1 = -Symb00
            // 2 = Symb-00
            // 3 = Symb00-
            // 4 = (00Symb)
            // 5 = -00Symb
            // 6 = 00-Symb
            // 7 = 00Symb-
            // 8 = -00 Symb
            // 9 = -Symb 00
            // 10 = 00 Symb-
            // 11 = Symb 00-
            // 12 = Symb -00
            // 13 = 00- Symb
            // 14 = (Symb 00)
            // 15 = (00 Symb)

            // arr[8] = positive
            // arr[9] = negative

            // format the number:
            //total = parseFloat(total.toString()).toFixed(dec);
            //totalstr = '';
            //mTotal = total;

            // ok, in vm2 we've got:
            // arr[8] = positive
            // arr[9] = negative
            if (arr[8] != null) {
                stylepvm2 = arr[8];
                stylenvm2 = arr[9];
            }
            else {
                stylepvm2 = null;
                stylenvm2 = null;
            }
            return Onepage.FormatNum2Currency(total, sep, tsep, stylep, stylen, op_currency, dec, stylepvm2, stylenvm2);

        }
        else {
            var dec = 2;
            if ((op_no_decimals != null) && (op_no_decimals == true)) dec = 0;
            if ((op_curr_after != null) && (op_curr_after == true)) {
                total = parseFloat(total.toString()).toFixed(dec) + ' ' + op_currency;
            }
            else
                total = op_currency + ' ' + parseFloat(total.toString()).toFixed(dec);
            return total;
        }


    },


    op_validateCountryOp2: function (b1, b2, elopc) {
        Onepage.changeStateList(elopc);
        if (typeof opc_bit_check_vatid != 'undefined') {
            var el = Onepage.getElementById(bit_euvatid_field_name + '_field');
            if (el != null)
                if (el.value != '')
                    opc_bit_check_vatid(el);
        }
        Onepage.validateCountryOp(false, b2, elopc);


        if (typeof jQuery != 'undefined') {
            var r = jQuery(elopc);
            if (typeof r.trigger != 'undefined') {
                r.trigger('refresh');
            }

        }

        return "";
    },

// aboo is whether to alert user
    // runCh is boolean whether to change stateList
    validateCountryOp: function (runCh, aboo, elopc) {
        Onepage.op_runSS(elopc);
    },


    changeStateList: function (elopc) {
        var prefix = Onepage.getAttr(elopc, 'prefix');


        var st = false;
        if (elopc.id != null) {
            if (elopc.id.toString().indexOf('shipto_') > -1) {
                st = true;
            }
        }
        else return;

        if (elopc.selectedIndex != null) {
        }
        else {
            //alert('err');
            return;
        }

        if (elopc.options != null)
            var value = elopc.options[elopc.selectedIndex].value;
        else if (elopc.value != null)
            var value = elopc.value;
        else
            return;

        if (typeof OPCStates == 'undefined') return;

        var statefor = eval('OPCStates.state_for_' + value);


        //'state_for_'+value;
        if ((prefix != null) && (prefix != '')) {
            var st2 = Onepage.getElementById(prefix + 'virtuemart_state_id');
            if (st2 != null) {
                if (op_lastcountry != value)
                    if (typeof statefor == 'undefined') {
                        Onepage.op_replace_select(prefix + 'virtuemart_state_id', 'no_state');


                        if (typeof callWhenNoStates != 'undefined')
                            if (callWhenNoStates != null && callWhenNoStates.length > 0) {
                                for (var x = 0; x < callWhenNoStates.length; x++) {
                                    eval(callWhenNoStates[x]);
                                }
                            }
                    }
                    else {
                        // dynamic list
                        Onepage.op_replace_select_dynamic(prefix + 'virtuemart_state_id', statefor);


                        if (typeof callWhenHasStates != 'undefined')
                            if (callWhenHasStates != null && callWhenHasStates.length > 0) {
                                for (var x = 0; x < callWhenHasStates.length; x++) {
                                    eval(callWhenHasStates[x]);
                                }
                            }


                    }
                op_lastcountry = value;

            }
        }
        else if (!st) {

            var st2 = Onepage.getElementById('virtuemart_state_id');
            if (st2 != null) {
                if (op_lastcountry != value)
                    if (typeof statefor == 'undefined') {
                        Onepage.op_replace_select('virtuemart_state_id', 'no_state');


                        if (typeof callWhenNoStates != 'undefined')
                            if (callWhenNoStates != null && callWhenNoStates.length > 0) {
                                for (var x = 0; x < callWhenNoStates.length; x++) {
                                    eval(callWhenNoStates[x]);
                                }
                            }
                    }
                    else {
                        // dynamic list
                        Onepage.op_replace_select_dynamic('virtuemart_state_id', statefor);


                        if (typeof callWhenHasStates != 'undefined')
                            if (callWhenHasStates != null && callWhenHasStates.length > 0) {
                                for (var x = 0; x < callWhenHasStates.length; x++) {
                                    eval(callWhenHasStates[x]);
                                }
                            }


                    }
                op_lastcountry = value;

            }
        }
        else {

            var st2 = Onepage.getElementById('shipto_virtuemart_state_id');
            if (st2 != null) {
                if (op_lastcountryst != value)
                    if (typeof statefor == 'undefined') {
                        Onepage.op_replace_select('shipto_virtuemart_state_id', 'no_state');

                        if (typeof callWhenNoStates != 'undefined')
                            if (callWhenNoStates != null && callWhenNoStates.length > 0) {
                                for (var x = 0; x < callWhenNoStates.length; x++) {
                                    eval(callWhenNoStates[x]);
                                }
                            }

                    }
                    else {
                        // dymaic list
                        Onepage.op_replace_select_dynamic('shipto_virtuemart_state_id', statefor);

                        if (typeof callWhenHasStates != 'undefined')
                            if (callWhenHasStates != null && callWhenHasStates.length > 0) {
                                for (var x = 0; x < callWhenHasStates.length; x++) {
                                    eval(callWhenHasStates[x]);
                                }
                            }

                    }
                op_lastcountryst = value;
            }

        }

        if (typeof st2 != 'undefined')
            if (st2 != null) {
                if (typeof jQuery != 'undefined') {
                    var r = jQuery(st2);
                    if (typeof r.trigger != 'undefined') {
                        r.trigger('refresh');
                    }

                }
            }


    },


    reverseString: function (str) {
        var splittext = str.toString().split("");
        var revertext = splittext.reverse();
        return revertext.join("");
    },

    op_escape: function (str) {
        if ((typeof(str) != 'undefined') && (str != null)) {
            if (str === "") return "";
            if (!isNaN(str)) return str;
            var x = str.split('%').join('%25').split(' ').join('%20').split('$').join('%24').split('`').join('%60').split(':').join('%3A').split('[').join('%5B').split(']').join('%5D').split('+').join('%2B').split("&").join("%26").split("#").join("%23");

            return x;
        }
        else
            return "";
    },
    /*
	Author: Robert Hashemian
	http://www.hashemian.com/
	Modified by stAn www.rupostel.com - Feb 2011
	You can use this code in any manner so long as the author's
	name, Web address and this disclaimer is kept intact.
	********************************************************
  */
    FormatNum2Currency: function (num, decpoint, sep, stylep, stylen, curr, decnum, stylepvm2, stylenvm2) {
        // check for missing parameters and use defaults if so

        // vm2:
        //'1|€|2|,||3|8|8|{number} {symbol}|{sign}{number} {symbol}'
        var isPos = true;
        if (parseFloat(num) >= 0) isPos = true;
        else isPos = false;

        num = Math.round(num * Math.pow(10, decnum)) / Math.pow(10, decnum);
        if (isPos == false) num = num * (-1);
        num = num.toString();

        a = num.split('.');
        x = a[0];
        if (a.length > 1)
            y = a[1];
        else y = '00';
        var z = "";


        if ((typeof(x) != "undefined") && (x != null)) {
            // reverse the digits. regexp works from left to right.
            z = Onepage.reverseString(x);

            // add seperators. but undo the trailing one, if there
            z = z.replace(/(\d{3})/g, "$1" + sep);
            if (z.slice(-sep.length) == sep)
                z = z.slice(0, -sep.length);

            x = Onepage.reverseString(z);
            // add the fraction back in, if it was there
            if (decnum > 0) {
                if (typeof(y) != "undefined" && y.length > 0) {
                    if (y.length > decnum) y = y.toString().substr(0, decnum);
                    if (y.length < decnum) {
                        var missing = decnum - y.length;
                        for (var u = 0; u < missing; u++) {
                            y += '0';
                        }
                    }
                    x += decpoint + y;
                }
            }
        }

        if (isPos == true) {
            // 0 = '00Symb';
            // 1 = '00 Symb'
            // 2 = 'Symb00'
            // 3 = 'Symb 00';
            if (stylepvm2 != null) {
                if (curr.length > 0) {
                    //stylepvm2 = stylepvm2.split('{number}').join(x).split('{symbol}').join(curr);
                    stylepvm2 = stylepvm2.split('{number}').join(x).split('{symbol}').join('<span class="currency_format">' + curr + '</span>');
                }
                else
                    stylepvm2 = stylepvm2.split('{number}').join(x);

                if (stylepvm2.indexOf('sign') >= 0)
                    stylepvm2 = stylepvm2.split('{sign}').join('+');

                x = stylepvm2;
            }
            else
                switch (parseInt(stylep)) {
                    case 0:
                        x = x + curr;
                        break;
                    case 1:
                        x = x + ' ' + curr;
                        break;
                    case 2:
                        x = curr + x;
                        break;
                    default:
                        x = curr + ' ' + x;
                }
        }
        else {
            if (stylenvm2 != null) {
                if (curr.length > 0)
                    stylenvm2 = stylenvm2.split('{number}').join(x).split('{symbol}').join(curr);
                else
                    stylenvm2 = stylenvm2.split('{number}').join(x);

                if (stylenvm2.indexOf('sign') >= 0)
                    stylenvm2 = stylenvm2.split('{sign}').join('-');

                x = stylenvm2;
            }
            else
                switch (parseInt(stylen)) {
                    // 0 = (Symb00)
                    // 1 = -Symb00
                    // 2 = Symb-00
                    // 3 = Symb00-
                    // 4 = (00Symb)
                    // 5 = -00Symb
                    // 6 = 00-Symb
                    // 7 = 00Symb-
                    // 8 = -00 Symb
                    // 9 = -Symb 00
                    // 10 = 00 Symb-
                    // 11 = Symb 00-
                    // 12 = Symb -00
                    // 13 = 00- Symb
                    // 14 = (Symb 00)
                    // 15 = (00 Symb)
                    case 0:
                        x = '(' + curr + x + ')';
                        break;
                    case 1:
                        x = '-' + curr + x;
                        break;
                    case 2:
                        x = curr + '-' + x;
                        break;
                    case 3:
                        x = curr + x + '-';
                        break;
                    case 4:
                        x = '(' + x + curr + ')';
                        break;
                    case 5:
                        x = '-' + x + curr;
                        break;
                    case 6:
                        x = x + '-' + curr;
                        break;
                    case 7:
                        x = x + curr + '-';
                        break;
                    case 8:
                        x = '-' + x + ' ' + curr;
                        break;
                    case 9:
                        x = '-' + curr + ' ' + x;
                        break;
                    case 10:
                        x = x + ' ' + curr + '-';
                        break;
                    case 11:
                        x = curr + x + ' -';
                        break;
                    case 12:
                        x = curr + ' -' + x;
                        break;
                    case 13:
                        x = x + '- ' + curr;
                        break;
                    case 14:
                        x = '(' + curr + ' ' + x + ')';
                        break;
                    case 15:
                        x = '(' + x + ' ' + curr + ')';
                        break;
                    default:
                        x = '-' + x + ' ' + curr;
                }

        }

        return x;
    },


    /*
  * This function disables payment methods for a selected shipping method
  * or implicitly disabled payments
  * THIS FUNCTION MIGHT GET RENAMED TO: op_onShippingChanged
  */
    op_hidePayments: function () {
        // check if shipping had changed:
        var op_saved_shipping2 = Onepage.getInputIDShippingRate();

        if ((op_saved_shipping2 == '') && (!(op_saved_shipping != null))) return;

        if ((typeof(op_saved_shipping) == 'undefined' || op_saved_shipping == null) || (op_saved_shipping != op_saved_shipping2) || (op_firstrun)) {
            op_firstrun = false;

            // check if the feature is enabled
            // if (op_payment_disabling_disabled) return "";
            // event handler for AfterShippingSelect
            if (callAfterShippingSelect != null && callAfterShippingSelect.length > 0) {
                for (var x = 0; x < callAfterShippingSelect.length; x++) {
                    if (typeof callAfterShippingSelect[x] == 'function') {
                        callAfterShippingSelect[x]();
                    }
                    else {
                        eval(callAfterShippingSelect[x]);
                    }
                }
            }
        }
    },
    dynamicLines: function (startid) {
        if (opc_dynamic_lines == false) return;
        /* remove last dynamic lines
  */
        if (typeof Onepage.last_dymamic != 'undefined')
            for (var i = 0; i < Onepage.last_dymamic.length; i++) {
                if (Onepage.last_dymamic[i] != null) {
                    var d = Onepage.getElementById(Onepage.last_dymamic[i]);
                    if (d != null)
                        if (typeof d.parentNode != 'undefined')
                            if (d.parentNode != null) {
                                d.parentNode.removeChild(d);
                                Onepage.last_dymamic[i] = null;
                            }
                }
            }

        var d = document.getElementsByName(startid + '_dynamic');
        var d2 = Onepage.getElementById('tt_genericwrapper_basket');
        if (d2 != null)
            if (d != null)
                for (var i = 0; i < d.length; i++) {
                    var d3 = d2.cloneNode(true);
                    var value = d[i].value;
                    var id = d[i].getAttribute('rel', 0);
                    var name = d[i].getAttribute('stringname', '');

                    if ((id == 0) || (value == '')) continue;
                    value = Onepage.formatCurrency(parseFloat(value));

                    var html = d3.innerHTML.split('{dynamic_name}').join(name).split('{dynamic_value}').join(value);

                    d3.style.display = '';
                    var new_id = d[i].id + '_basket';
                    d3.id = new_id;

                    Onepage.setInnerHtml(d3, html);
                    if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                        Onepage.op_log('dynamic line bottom');
                        Onepage.op_log(html);
                    }
                    // double line test:
                    var dd = Onepage.getElementById(new_id);
                    if (dd != null) {

                        Onepage.setInnerHtml(dd, html);


                    }
                    else {
                        if (typeof d2.parentNode != 'undefined')
                            if (d2.parentNode != null) {
                                d2.parentNode.insertBefore(d3, d2.nextSibling);
                                Onepage.last_dymamic.push(d3.id);
                            }
                    }


                }
        //tt_genericwrapper_bottom


        var d2 = Onepage.getElementById('tt_genericwrapper_bottom');
        if (d != null)
            if (d2 != null)
                for (var i = 0; i < d.length; i++) {
                    var d3 = d2.cloneNode(true);
                    var value = d[i].value;
                    var id = d[i].getAttribute('rel', 0);
                    var name = d[i].getAttribute('stringname', '');

                    if ((id == 0) || (value == '')) continue;
                    value = Onepage.formatCurrency(parseFloat(value));
                    //d3.innerHTML = d3.innerHTML.split('{dynamic_name}').join(name).split('{dynamic_value}').join(value);
                    var html = d3.innerHTML.split('{dynamic_name}').join(name).split('{dynamic_value}').join(value);
                    Onepage.setInnerHtml(d3, html);
                    d3.style.display = '';
                    var new_id = d[i].id + '_bottom';
                    d3.id = new_id;
                    Onepage.setInnerHtml(d3, html);
                    if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                        Onepage.op_log('dynamic line bottom');
                        Onepage.op_log(html);
                    }
                    var dd = Onepage.getElementById(new_id);
                    if (dd != null) {

                        Onepage.setInnerHtml(dd, html);


                    }
                    else {

                        if (typeof d2.parentNode != 'undefined')
                            if (d2.parentNode != null) {
                                d2.parentNode.insertBefore(d3, d2.nextSibling);
                                Onepage.last_dymamic.push(d3.id);
                            }
                    }


                }


    },
    updateSelectedClasses: function () {
        // get VM ids here:
        var payment_id = Onepage.getValueOfSPaymentMethod();
        var shipping_id = Onepage.getVShippingRate();
        if (typeof jQuery == 'undefined') return;
        var el = jQuery('.opc_payment_wrap');
        if (typeof el.each != 'undefined')
            el.each(function () {
                var e = jQuery(this);
                if (e.attr('id') == 'opc_payment_wrap_' + payment_id) {
                    if (!e.hasClass('selected')) {
                        e.addClass('selected');
                    }
                }
                else {
                    e.removeClass('selected');
                }


            });


        var el = jQuery('.opc_ship_wrap');
        if (typeof el.each != 'undefined')
            el.each(function () {
                var e = jQuery(this);
                if (e.attr('id') == 'opc_ship_wrap_' + shipping_id) {
                    if (!e.hasClass('selected')) {
                        e.addClass('selected');
                    }
                }
                else {
                    e.removeClass('selected');
                }


            });

    },
    getTotalsObj: function (shipping_id, payment_id) {
        if (typeof shipping_id == 'undefined')
            var shipping_id = Onepage.getInputIDShippingRate();

        if (typeof payment_id == 'undefined')
            var payment_id = Onepage.getPaymentId();


        if (shipping_id == "") shipping_id = 'shipment_id_0';
        if (payment_id === "") {
            payment_id = 0;
        }

        var ret = {
            subtotal: {name: op_subtotal_txt, value: 0, valuetxt: ''},
            order_total: {name: op_textinclship, value: 0, valuetxt: ''},
            order_shipping: {name: op_shipping_txt, value: 0, valuetxt: ''},
            payment_discount: {name: op_payment_fee_txt, value: 0, valuetxt: ''},
            coupon_discount: {name: op_coupon_discount_txt, value: 0, valuetxt: ''},
            coupon_discount2: {name: op_other_discount_txt, value: 0, valuetxt: ''},
            tax_data: [],
            totalsset: false,


        };

        var x = Onepage.getElementById(shipping_id + '_' + payment_id + '_subtotal');
        if (!(x != null)) {
            ret.totalsset = false;
            return ret;
        }


        ret.subtotal.value = x.value;
        if ((!isNaN(parseFloat(x.value))) && (parseFloat(x.value) != 0))
            ret.subtotal.valuetxt = Onepage.formatCurrency(parseFloat(ret.subtotal.value));

        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_payment_discount');

        ret.payment_discount.value = x.value;
        if ((!isNaN(parseFloat(x.value))) && (parseFloat(x.value) > 0))
            ret.payment_discount.name = op_payment_discount_txt;

        if ((!isNaN(parseFloat(x.value))) && (parseFloat(x.value) != 0))
            ret.payment_discount.valuetxt = Onepage.formatCurrency((-1) * parseFloat(ret.payment_discount.value));

        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_coupon_discount');


        ret.coupon_discount.value = x.value;
        if ((!isNaN(parseFloat(x.value))) && (parseFloat(x.value) != 0))
            ret.coupon_discount.valuetxt = Onepage.formatCurrency(parseFloat(ret.coupon_discount.value));


        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_order_shipping');


        ret.order_shipping.value = x.value;
        if ((!isNaN(parseFloat(x.value))) && (parseFloat(x.value) != 0))
            ret.order_shipping.valuetxt = Onepage.formatCurrency(parseFloat(ret.order_shipping.value));


        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_order_total');


        ret.order_total.value = x.value;
        if ((!isNaN(parseFloat(x.value))) && (parseFloat(x.value) >= 0)) {
            ret.order_total.valuetxt = Onepage.formatCurrency(parseFloat(ret.order_total.value));
            ret.totalsset = true;
        }


        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_coupon_discount2');

        ret.coupon_discount2.value = x.value;
        if ((!isNaN(parseFloat(x.value))) && (parseFloat(x.value) != 0))
            ret.coupon_discount2.valuetxt = Onepage.formatCurrency(parseFloat(ret.coupon_discount2.value));

        x = document.getElementsByName(shipping_id + '_' + payment_id + '_tax');
        var xall = document.getElementsByName(shipping_id + '_' + payment_id + '_tax_all');

        var xname = document.getElementsByName(shipping_id + '_' + payment_id + '_taxname');
        var tax_data = [];
        if (!opc_dynamic_lines) {

            for (i = 0; i < x.length; i++) {
                tax_data[i] = {name: op_tax_txt, value: 0, txtvalue: ''}

                var arr = x[i].value.split("|");
                var tax = 0;
                if (arr.length == 2) tax = arr[1];
                else tax = x[i].value;


                if (!isNaN(parseFloat(tax))) {
                    tax_data[i].value = x[i].value;
                }
                else
                    tax_data[i].value = 0;

                if (typeof xname[i] != 'undefined' && (xname[i] != null))
                    if (xname[i].value != null) {
                        tax_data[i].name = xname[i].value;
                    }
                    else
                        tax_name[i].name = '';
                if ((!isNaN(parseFloat(tax_data[i].value))) && (parseFloat(tax_data[i].value) != 0))
                    tax_data[i].valuetxt = Onepage.formatCurrency(parseFloat(tax_data[i].value));


            }

        }
        else {


            var d = document.getElementsByName(shipping_id + '_' + payment_id + '_dynamic');


            if (d != null)
                for (var i = 0; i < d.length; i++) {
                    tax_data[i] = {name: op_tax_txt, value: '', txtvalue: ''}

                    var value = d[i].value;
                    var id = d[i].getAttribute('rel', 0);
                    var name = d[i].getAttribute('stringname', '');


                    tax_data[i].name = name;
                    tax_data[i].value = value;


                    if ((!isNaN(parseFloat(tax_data[i].value))) && (parseFloat(tax_data[i].value) != 0))
                        tax_data[i].valuetxt = Onepage.formatCurrency(parseFloat(value));


                }


        }

        ret.tax_data = tax_data;

        return ret;
    },
    /*
   * This function fetches totals array from ajax data
   */
    getTotals: function (shipping_id, payment_id) {

        Onepage.updateSelectedClasses();

        if (typeof shipping_id == 'undefined')
            var shipping_id = Onepage.getInputIDShippingRate();

        Onepage.op_log('shipping_id: ' + shipping_id);

        if (typeof payment_id == 'undefined')
            var payment_id = Onepage.getPaymentId();

        if (shipping_id == "") shipping_id = 'shipment_id_0';
        if (payment_id === "") {
            payment_id = 0;
        }
        var po = Onepage.getElementById('payment_id_override_' + payment_id);
        if (po != null) {
            var e = Onepage.getSPaymentElement();
            if (e != null)
                if (e.id != null)
                    payment_id = e.id;


        }


        var x = Onepage.getElementById(shipping_id + '_' + payment_id + '_subtotal');
        if (x == null) {
            return "";
        }

        //var myTotals = Onepage.getTotalsObj();

        if (typeof opc_dynamic_lines != 'undefined')
            if (opc_dynamic_lines != null) {
                Onepage.dynamicLines(shipping_id + '_' + payment_id);
            }

        var subtotal = x.value;

        d = Onepage.getElementById('opc_coupon_code_returned');

        if (d != null)
            var coupon_code_returned = d.value;
        else
            var coupon_code_returned = '';


        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_payment_discount');
        var payment_discount = x.value;

        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_coupon_discount');
        var coupon_discount = x.value;


        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_order_shipping');
        var order_shipping = x.value;

        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_order_shipping_tax');
        var order_shipping_tax = x.value;

        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_order_total');
        var order_total = x.value;

        var zzOrder_total = parseFloat(order_total);
        if (zzOrder_total === 0) {
            Onepage.togglePaymentDisplay(false);
        }
        else {
            Onepage.togglePaymentDisplay(true);
        }

        x = Onepage.getElementById(shipping_id + '_' + payment_id + '_coupon_discount2');
        var coupon_discount2 = x.value;

        x = document.getElementsByName(shipping_id + '_' + payment_id + '_tax');
        xall = document.getElementsByName(shipping_id + '_' + payment_id + '_tax_all');

        xname = document.getElementsByName(shipping_id + '_' + payment_id + '_taxname');

        // check if we have shipping inside basket
        var sib = Onepage.getElementById('shipping_inside_basket_cost');


        if ((sib != null)) {
            if (Onepage.isNotAShippingMethod()) {

                sib.innerHTML = op_lang_select;
            }
            else {
                var scost = parseFloat(order_shipping);
                if (op_show_prices_including_tax == '1')
                    var total_s = Onepage.formatCurrency(parseFloat(order_shipping) + parseFloat(order_shipping_tax));
                else
                    var total_s = Onepage.formatCurrency(scost);
                if ((scost == 0) && (use_free_text))
                    sib.innerHTML = opc_free_text;
                else
                    sib.innerHTML = total_s;
                if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                    Onepage.op_log(total_s);
            }
        }


        if (true) {
            if (op_fix_payment_vat == true) {
                // tax rate calculaction
                if (isNaN(parseFloat(op_detected_tax_rate)) || parseFloat(op_detected_tax_rate) == 0.00)
                    taxr = parseFloat(op_custom_tax_rate);
                else
                    taxr = parseFloat(op_detected_tax_rate);

                //else taxr = parseFloat(op_custom_tax_rate);

                p_disc = (-1) * (1 + taxr) * parseFloat(payment_discount);

            }
            else {
                p_disc = (-1) * parseFloat(payment_discount);
            }

            var total_s = Onepage.formatCurrency(parseFloat(p_disc));

            var sib = Onepage.getElementById('payment_inside_basket_cost');


            var pe = Onepage.getPaymentElement();
            var invalid_payment = false;
            if (pe != null) {
                var atr = pe.getAttribute('not_a_valid_payment', false);
                if (atr != null)
                    if (atr != false) {
                        var invalid_payment = true;

                    }
            }

            if (sib != null) {
                if (invalid_payment) {
                    sib.innerHTML = op_lang_select;
                }
                else {
                    sib.innerHTML = total_s;
                }
            }
        }


        op_tax_total = parseFloat(0.0);
        //tax_data = new Array(x.length);
        tax_data = new Array(1);
        tax_data[0] = "|0";

        tax_name = new Array();

        //opc update 2.0.108: disable for
        // opc update 2.0.127: if (x.length > 0) , reenabled for
        if (!opc_dynamic_lines)
            for (i = 0; i < x.length; i++) {
                //i = 0;
                //var y = x.value;
                //var arr = y.split("|");
                var arr = x[i].value.split("|");
                var tax = 0;
                if (arr.length == 2) tax = arr[1];
                else tax = x[i].value;

                tax_data[i] = x[i].value;
                if (!isNaN(parseFloat(tax)))
                    op_tax_total += parseFloat(tax);

                if (typeof xname[i] != 'undefined' && (xname[i] != null))
                    if (xname[i].value != null) {
                        tax_name[i] = xname[i].value;
                    }
                    else
                        tax_name[i] = '';

            }

        if (x.length == 0) {
            x2 = Onepage.getElementById(shipping_id + '_' + payment_id + '_tax_all');
            if (x2 != null) {
                tax_data = new Array(1);
                tax_data[0] = x2.value;
                tax_name[0] = op_shipping_tax_txt;


            }
            else
                tax_name[0] = op_shipping_tax_txt;
        }

        var taxx = '';

        // init taxes first by hiding them
        for (i = x.length; i <= 4; i++) {
            taxx = Onepage.getElementById('tt_tax_total_' + i + '_div');
            taxx2 = Onepage.getElementById('tt_tax_total_' + i + '_div_basket');
            if (typeof taxx != 'undefined' && (taxx != null)) {
                taxx.style.display = 'none';
            }
            if (typeof taxx2 != 'undefined' && (taxx2 != null)) {
                taxx2.style.display = 'none';

            }
        }

        if (opc_dynamic_lines) {
            tax_data = new Array(1);
            tax_data[0] = "|0";
        }
        // formatting totals here:
        /*
    var t = Onepage.getElementById('totalam');
	if (!(t != null)) {
	  insertHtml = '<div id="totalam"><div id="tt_order_subtotal_div"><span id="tt_order_subtotal_txt" class="bottom_totals_txt"></span><span id="tt_order_subtotal" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_order_payment_discount_before_div"><span id="tt_order_payment_discount_before_txt" class="bottom_totals_txt"></span><span class="bottom_totals" id="tt_order_payment_discount_before"></span><br class="op_clear"/></div><div id="tt_order_discount_before_div"><span id="tt_order_discount_before_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_before" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_shipping_rate_div"><span id="tt_shipping_rate_txt" class="bottom_totals_txt"></span><span id="tt_shipping_rate" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_shipping_tax_div"><span id="tt_shipping_tax_txt" class="bottom_totals_txt"></span><span id="tt_shipping_tax" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_0_div"><span id="tt_tax_total_0_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_0" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_1_div"><span id="tt_tax_total_1_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_1" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_2_div"><span id="tt_tax_total_2_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_2" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_3_div"><span id="tt_tax_total_3_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_3" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_tax_total_4_div"><span id="tt_tax_total_4_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_4" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_order_payment_discount_after_div"><span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_payment_discount_after" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_order_discount_after_div"><span id="tt_order_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_after" class="bottom_totals"></span><br class="op_clear"/></div><div id="tt_total_div"><span id="tt_total_txt" class="bottom_totals_txt"></span><span id="tt_total" class="bottom_totals"></span><br class="op_clear"/></div></div>';
	  f = Onepage.getElementById('vmMainPageOPC');
	  dv = document.createElement("div");
	  dv.style.display = 'none';
	  dv.innerHTML = insertHtml;
	  t = dv;
	  f.appendChild(dv);
	}
	*/
        var t1 = Onepage.getElementById('tt_order_subtotal_txt');

        // for google ecommerce: op_total_total, op_tax_total, op_ship_total
        // with VAT
        op_total_total = order_total;
        // only VAT
        op_tax_total += parseFloat(order_shipping_tax);
        // without VAT
        op_ship_total = order_shipping;


        if (never_show_total == true) {
            t.style.display = 'none';
        }


        if ((op_show_only_total != null) && (op_show_only_total == true)) {
            stru = Onepage.getElementById('tt_total_txt')
            if (stru != null)
                str = srtu.innerHTML;
            else str = '';
            if (str == '') {
                d1 = Onepage.getElementById('tt_total_txt');
                if (d1 != null)
                    d1.innerHTML = op_textinclship;
            }
            if ((op_custom_tax_rate != null) && (op_add_tax != null) && (op_custom_tax_rate != '') && (op_add_tax == true)) {
                d1 = Onepage.getElementById('tt_total');
                if (d1 != null)
                    d1.innerHTML = Onepage.formatCurrency((1 + parseFloat(op_custom_tax_rate)) * parseFloat(order_total));
            }
            else {
                d1 = Onepage.getElementById('tt_total');
                if (d1 != null)
                    d1.innerHTML = Onepage.formatCurrency(order_total);
            }

            d1 = Onepage.getElementById('tt_order_payment_discount_before_div');
            if (d1 != null)
                d1.style.display = "none";
            d1 = Onepage.getElementById('tt_order_discount_before_div');
            if (d1 != null) d1.style.display = "none";
            d1 = Onepage.getElementById('tt_order_subtotal_div');
            if (d1 != null) d1.style.display = 'none';
            d1 = Onepage.getElementById('tt_shipping_rate_div');
            if (d1 != null) d1.style.display = 'none';
            d1 = Onepage.getElementById('tt_shipping_tax_div');
            if (d1 != null) d1.style.display = 'none';
            return true;
        }

        // add tax to payment discount
        /*
	  if (false)
   	  if (op_fix_payment_vat == true)
   	  if ((op_no_taxes == true) || (op_no_taxes_show == true) || (op_show_andrea_view == true) || ((payment_discount_before == '1') && (op_show_prices_including_tax == '1')))
      {
          if (isNaN(parseFloat(op_detected_tax_rate)) || parseFloat(op_detected_tax_rate)==0.00)
          taxr = parseFloat(op_custom_tax_rate);
          else
          taxr = parseFloat(op_detected_tax_rate);

          p_disc =  (1 + taxr) * parseFloat(payment_discount);
          payment_discount = parseFloat(p_disc);
      }
	  */


        var locp = 'after';
        if (payment_discount_before == '1') {
            locp = 'before';
        }
        else locp = 'after';


        if (payment_discount > 0) {

            stru = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt');
            if (stru != null)
                str = stru.innerHTML;
            else str = '';

            if (str == '' || str == op_payment_fee_txt) {
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt');
                if (d1 != null) d1.innerHTML = op_payment_discount_txt;
            }
            d1 = Onepage.getElementById('tt_order_payment_discount_' + locp);
            if (d1 != null) d1.innerHTML = Onepage.formatCurrency((-1) * payment_discount);
            if (op_override_basket) {
                e1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_basket');
                if (e1 != null)
                    e1.innerHTML = Onepage.formatCurrency((-1) * parseFloat(payment_discount));
                e2 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt_basket');
                if (e2 != null)
                    e2.innerHTML = op_payment_discount_txt;
                if (!op_payment_inside_basket) {
                    e3 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_div_basket');
                    if (e3 != null)
                        e3.style.display = "";
                }
            }
            d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_div');
            if (d1 != null) d1.style.display = "block";
        }
        else if (payment_discount < 0) {
            stru = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt');
            if (stru != null)
                str = stru.innerHTML;
            else str = '';
            if (str == '' || (str == op_payment_discount_txt)) {
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt');
                if (d1 != null)
                    d1.innerHTML = op_payment_fee_txt;
            }
            d1 = Onepage.getElementById('tt_order_payment_discount_' + locp);
            if (d1 != null) d1.innerHTML = Onepage.formatCurrency((-1) * parseFloat(payment_discount));
            d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_div');
            if (d1 != null) d1.style.display = "block";
            if (op_override_basket) {
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_basket');
                if (d1 != null) d1.innerHTML = Onepage.formatCurrency((-1) * parseFloat(payment_discount));
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt_basket');
                if (d1 != null) d1.innerHTML = op_payment_fee_txt;
                if (!op_payment_inside_basket) {
                    d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_div_basket');
                    if (d1 != null) d1.style.display = "";
                }
            }


        }
        else {
            stru = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt');
            if (stru != null) str = stru.innerHTML;
            else str = '';

            if (str == '') {
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt');
                if (d1 != null) d1.innerHTML = "";
            }
            d1 = Onepage.getElementById('tt_order_payment_discount_' + locp);
            if (d1 != null) d1.innerHTML = "";
            d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_div');
            if (d1 != null) d1.style.display = "none";
            if (op_override_basket) {
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_basket');
                if (d1 != null) d1.innerHTML = ""
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_txt_basket');
                if (d1 != null) d1.innerHTML = "";
                d1 = Onepage.getElementById('tt_order_payment_discount_' + locp + '_div_basket');
                if (d1 != null) d1.style.display = "none";
            }

        }


        //odl: if (Math.abs(coupon_discount) > 0)


        locp = 'after';
        //(coupon_code_returned != '') ||
        if ((Math.abs(coupon_discount) > 0) || (coupon_code_returned != '')) {
            if (coupon_discount < 0) coupon_discount = parseFloat(coupon_discount) * (-1);
            stru = Onepage.getElementById('tt_order_discount_' + locp + '_txt');
            if (stru != null) str = stru.innerHTML;
            else str = '';
            if (str == '') {
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_txt');
                if (d1 != null) d1.innerHTML = op_coupon_discount_txt;
            }
            d1 = Onepage.getElementById('tt_order_discount_' + locp);
            if (d1 != null) d1.innerHTML = Onepage.formatCurrency((-1) * parseFloat(coupon_discount));
            d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div');
            if (d1 != null) d1.style.display = "block";
            if (op_override_basket) {
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_basket');
                if (d1 != null) {
                    d1.innerHTML = Onepage.formatCurrency((-1) * parseFloat(coupon_discount));
                    d1.style.display = 'block';
                }
                //tt_order_discount_after_txt_basket
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div_basket');
                if (d1 != null) d1.style.visibility = 'visible';
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div_basket');
                if (d1 != null) {
                    d1.style.display = '';
                    /*
		if (d1.nodeName.toLowerCase() != 'tr')
		d1.style.display = 'block';
		else d1.style.display = 'table-row';
		  */
                }
                //tt_order_discount_after_basket

            }
        }
        else {

            stru = Onepage.getElementById('tt_order_discount_' + locp + '_txt');
            if (stru != null)
                str = stru.innerHTML;
            else str = '';

            if (str == '') {
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_txt');
                if (d1 != null) d1.innerHTML = "";
            }

            d1 = Onepage.getElementById('tt_order_discount_' + locp + '');
            if (d1 != null) d1.innerHTML = "";

            d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div');
            if (d1 != null) d1.style.display = "none";
            if (op_override_basket) {
                e3 = Onepage.getElementById('tt_order_discount_' + locp + '_div_basket');
                if (e3 != null) {

                    e3.style.display = "none";
                }
            }
        }

        locp = 'before';
        if (Math.abs(coupon_discount2) > 0) {

            //if (coupon_discount2 < 0) coupon_discount2 = parseFloat(coupon_discount2) * (-1);

            d1 = Onepage.getElementById('tt_order_discount_' + locp + '_txt');
            if (d1 != null) d1.innerHTML = op_other_discount_txt;

            d1 = Onepage.getElementById('tt_order_discount_' + locp);
            if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(coupon_discount2));
            d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div');
            if (d1 != null) d1.style.display = "block";

            if (op_override_basket) {
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_basket');
                if (d1 != null) {
                    d1.innerHTML = Onepage.formatCurrency(parseFloat(coupon_discount));
                    d1.style.display = 'block';
                }
                //tt_order_discount_after_txt_basket
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div_basket');
                if (d1 != null) d1.style.visibility = 'visible';
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div_basket');
                if (d1 != null) {
                    if (d1 != null) {
                        d1.style.display = '';
                        /*
		if (d1.nodeName.toLowerCase() != 'tr')
		d1.style.display = 'block';
		else d1.style.display = 'table-row';
		*/
                    }
                    //d1.style.display = 'block';
                }
                //tt_order_discount_after_basket
                d1 = Onepage.getElementById('tt_order_discount_' + locp + '_basket');
                if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(coupon_discount2));

            }

        }
        else {


            d1 = Onepage.getElementById('tt_order_discount_' + locp + '_txt');
            if (d1 != null) d1.innerHTML = "";
            d1 = Onepage.getElementById('tt_order_discount_' + locp + '');
            if (d1 != null) d1.innerHTML = "";
            d1 = Onepage.getElementById('tt_order_discount_' + locp + '_div');
            if (d1 != null) d1.style.display = "none";
            if (op_override_basket) {
                e3 = Onepage.getElementById('tt_order_discount_' + locp + '_div_basket');
                if (e3 != null)
                    e3.style.display = "none";
            }
        }
        locp = 'after';

        if ((op_no_taxes != true) && (op_no_taxes_show != true)) {
            stru = Onepage.getElementById('tt_order_subtotal_txt');
            if (stru != null) str = stru.innerHTML;
            else str = '';
            if (str == '') {
                d1 = Onepage.getElementById('tt_order_subtotal_txt');
                if (d1 != null) d1.innerHTML = op_subtotal_txt;
            }
            if (op_show_andrea_view == true) {
                d1 = Onepage.getElementById('tt_order_subtotal');
                if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(subtotal) + parseFloat(op_basket_subtotal_items_tax_only));
            }
            else {
                d1 = Onepage.getElementById('tt_order_subtotal');
                if (d1 != null) d1.innerHTML = Onepage.formatCurrency(subtotal);
            }
            d1 = Onepage.getElementById('tt_order_subtotal_div');
            if (d1 != null) d1.style.display = 'block';
            if (op_override_basket) {
                stru = Onepage.getElementById('tt_order_subtotal_txt_basket');
                if (stru != null) str = stru.innerHTML;
                else str = '';
                if (str == '') {

                    d1 = Onepage.getElementById('tt_order_subtotal_txt_basket');
                    if (d1 != null) d1.innerHTML = op_subtotal_txt;
                }
                if (op_show_andrea_view == true) {
                    d1 = Onepage.getElementById('tt_order_subtotal_basket');
                    if (d1 != null) d1.innerHTML = Onepage.formatCurrency(parseFloat(subtotal) + parseFloat(op_basket_subtotal_items_tax_only));
                }
                else {
                    d1 = Onepage.getElementById('tt_order_subtotal_basket');
                    if (d1 != null) d1.innerHTML = Onepage.formatCurrency(subtotal);
                }
                d1 = Onepage.getElementById('tt_order_subtotal_div_basket');
                if (d1 != null) d1.style.display = '';

            }
        }
        else {
            d1 = Onepage.getElementById('tt_order_subtotal_div');
            if (d1 != null) d1.style.display = 'none';
            if (op_override_basket) {
                d1 = Onepage.getElementById('tt_order_subtotal_div_basket');
                if (d1 != null) d1.style.display = 'none';

            }

        }

        if (op_noshipping == false) {
            stru = Onepage.getElementById('tt_shipping_rate_txt');

            if (stru != null) str = stru.innerHTML;
            else str = '';

            if (str == '') {

                d1 = Onepage.getElementById('tt_shipping_rate_txt');
                if (d1 != null) d1.innerHTML = op_shipping_txt;
            }

            if (Onepage.isNotAShippingMethod()) {
                if (op_override_basket) {
                    d1 = Onepage.getElementById('tt_shipping_rate_basket');
                    if (d1 != null) d1.innerHTML = op_lang_select;
                }
                d1 = Onepage.getElementById('tt_shipping_rate');
                if (d1 != null) d1.innerHTML = op_lang_select;


            }
            else if (op_no_taxes_show != true && op_show_andrea_view != true) {
                if (op_show_prices_including_tax == '1') {
                    d1 = Onepage.getElementById('tt_shipping_rate');
                    if (d1 != null) {
                        var opcs = parseFloat(order_shipping) + parseFloat(order_shipping_tax);
                        if ((opcs == 0) && (use_free_text))
                            d1.innerHTML = opc_free_text;
                        else
                            d1.innerHTML = Onepage.formatCurrency(opcs);

                        // d1.innerHTML = Onepage.formatCurrency(parseFloat(order_shipping)+parseFloat(order_shipping_tax));
                    }
                }
                else {
                    d1 = Onepage.getElementById('tt_shipping_rate');
                    if (d1 != null) {
                        var opcs = parseFloat(order_shipping);
                        if ((opcs == 0) && (use_free_text))
                            d1.innerHTML = opc_free_text;
                        else
                            d1.innerHTML = Onepage.formatCurrency(opcs);


                    }
                }
                if (op_override_basket) {
                    if (Onepage.isNotAShippingMethod()) {
                        d1 = Onepage.getElementById('tt_shipping_rate_basket');
                        if (d1 != null) d1.innerHTML = op_lang_select;
                    }
                    else {
                        if (op_show_prices_including_tax == '1') {
                            d1 = Onepage.getElementById('tt_shipping_rate_basket');
                            if (d1 != null) {
                                var opcs = parseFloat(order_shipping) + parseFloat(order_shipping_tax);
                                if ((opcs == 0) && (use_free_text))
                                    d1.innerHTML = opc_free_text;
                                else
                                    d1.innerHTML = Onepage.formatCurrency(opcs);
                            }
                        }
                        else {
                            d1 = Onepage.getElementById('tt_shipping_rate_basket');
                            if (d1 != null) {
                                var opcs = parseFloat(order_shipping);
                                if ((opcs == 0) && (use_free_text))
                                    d1.innerHTML = opc_free_text;
                                else
                                    d1.innerHTML = Onepage.formatCurrency(opcs);

                            }
                        }
                    }
                }
            }
            else {
                d1 = Onepage.getElementById('tt_shipping_rate');
                if (d1 != null) {
                    var opcs = parseFloat(order_shipping) + parseFloat(order_shipping_tax);
                    if ((opcs == 0) && (use_free_text))
                        d1.innerHTML = opc_free_text;
                    else
                        d1.innerHTML = Onepage.formatCurrency(opcs);

                }
                if (op_override_basket) {
                    d1 = Onepage.getElementById('tt_shipping_rate_basket');
                    if (d1 != null) {
                        var opcs = parseFloat(order_shipping) + parseFloat(order_shipping_tax);
                        if ((opcs == 0) && (use_free_text))
                            d1.innerHTML = opc_free_text;
                        else
                            d1.innerHTML = Onepage.formatCurrency(opcs);

                    }
                }
            }
            d1 = Onepage.getElementById('tt_shipping_rate_div');
            if (d1 != null) d1.style.display = 'block';

            if (op_override_basket) {
                if (!op_shipping_inside_basket) {
                    d1 = Onepage.getElementById('tt_shipping_rate_div_basket');
                    if (d1 != null) d1.style.display = '';


                }
                else {
                    d1 = Onepage.getElementById('tt_shipping_rate_div_basket');
                    if (d1 != null) d1.style.display = 'none';
                }
            }


            if ((order_shipping_tax > 0) && (op_sum_tax != true) && (op_no_taxes != true) && (op_no_taxes_show != true) && (op_show_andrea_view != true) && (op_show_prices_including_tax != '1')) {
                stru = Onepage.getElementById('tt_shipping_tax_txt');
                if (stru != null) str = stru.innerHTML;
                else str = '';

                if (str == '') {

                    d1 = Onepage.getElementById('tt_shipping_tax_txt');
                    if (d1 != null) d1.innerHTML = op_shipping_tax_txt;
                }
                d1 = Onepage.getElementById('tt_shipping_tax');
                if (d1 != null) d1.innerHTML = Onepage.formatCurrency(order_shipping_tax);

                d1 = Onepage.getElementById('tt_shipping_tax_div');
                if (d1 != null) d1.style.display = "block";
            }
            else {
                stru = Onepage.getElementById('tt_shipping_tax_txt');
                if (stru != null) str = stru.innerHTML;
                else str = '';

                if (str == '') {
                    d1 = Onepage.getElementById('tt_shipping_tax_txt');
                    if (d1 != null) d1.innerHTML = "";
                }
                d1 = Onepage.getElementById('tt_shipping_tax');
                if (d1 != null) d1.innerHTML = "";

                d1 = Onepage.getElementById('tt_shipping_tax_div');
                if (d1 != null) d1.style.display = "none";
            }
        }
        else {
            d1 = Onepage.getElementById('tt_shipping_rate_div');
            if (d1 != null) d1.style.display = 'none';
            d1 = Onepage.getElementById('tt_shipping_tax_div');
            if (d1 != null) d1.style.display = "none";
        }

        if ((op_no_taxes != true) && (op_no_taxes_show != true) && (op_show_andrea_view != true)) {

            for (i = 0; i < tax_data.length; i++) {
                var tx = Onepage.getElementById('tt_tax_total_' + i);
                var tx_txt = Onepage.getElementById('tt_tax_total_' + i + '_txt');
                var txt_div = Onepage.getElementById('tt_tax_total_' + i + '_div');


                {
                    rate_arr = tax_data[i].split('|');


                    {
                        if (rate_arr[1] > 0) {

                            test1 = parseFloat(rate_arr[0]) * 100;
                            test2 = Math.round(parseFloat(rate_arr[0]) * 100);
                            if (test1 != test2)
                                test2 = Math.round(parseFloat(rate_arr[0]) * 1000) / 10;
                            if (test2 != test1)
                                test2 = Math.round(parseFloat(rate_arr[0]) * 10000) / 100;

                            taxr = test2 + '%';

                            if (rate_arr[0] != '') {
                                if (tx_txt != null) {
                                    if ((tax_name[i] != null) && (tax_name[i] != ''))
                                        tx_txt.innerHTML = tax_name[i];
                                    else
                                        tx_txt.innerHTML = op_tax_txt + '(' + taxr + ')';
                                }
                                if (op_basket_override) {
                                    tx_txt2 = Onepage.getElementById('tt_tax_total_' + i + '_txt_basket');

                                    if (tx_txt2 != null) {
                                        if ((tax_name[i] != null) && (tax_name[i] != ''))
                                            tx_txt2.innerHTML = tax_name[i];
                                        else
                                            tx_txt2.innerHTML = op_tax_txt + '(' + taxr + ')';
                                    }
                                    else {
                                        // if the template does not have the posisions for all of the tax rates, it won't be shown !
                                    }


                                }
                            }
                            else {
                                if (tx_txt != null)
                                    tx_txt.innerHTML = op_tax_txt;
                                if (op_basket_override) {
                                    d1 = Onepage.getElementById('tt_tax_total_' + i + '_txt_basket');
                                    if (d1 != null) d1.innerHTML = op_tax_txt;
                                }

                            }

                            if (typeof tx != 'undefined')
                                if (tx != null)
                                    if ((tax_data.length == 1) && (op_sum_tax == true)) {
                                        tx.innerHTML = Onepage.formatCurrency(parseFloat(rate_arr[1]) + parseFloat(order_shipping_tax));
                                    }
                                    else {
                                        if (tx != null)
                                            tx.innerHTML = Onepage.formatCurrency(rate_arr[1]);
                                    }
                            if (typeof txt_div != 'undefined')
                                if (txt_div != null)
                                    txt_div.style.display = 'block';

                            if (op_basket_override) {

                                if ((tax_data.length == 1) && (op_sum_tax == true)) {
                                    Onepage.getElementById('tt_tax_total_' + i + '_basket').innerHTML = Onepage.formatCurrency(parseFloat(rate_arr[1]) + parseFloat(order_shipping_tax));
                                    Onepage.getElementById('tt_tax_total_' + i + '_div_basket').style.display = '';
                                }
                                else {
                                    d1 = Onepage.getElementById('tt_tax_total_' + i + '_basket');
                                    if (d1 != null) d1.innerHTML = Onepage.formatCurrency(rate_arr[1]);
                                    d1 = Onepage.getElementById('tt_tax_total_' + i + '_div_basket');
                                    if (d1 != null) d1.style.display = '';
                                }
                            }

                        }
                        else {
                            if (typeof tx_txt != 'undefined')
                                if (tx_txt != null)
                                    tx_txt.innerHTML = "";
                            if (typeof tx != 'undefined')
                                if (tx != null)
                                    tx.innerHTML = "";
                            if (typeof txt_div != 'undefined')
                                if (txt_div != null)
                                    txt_div.style.display = 'none';
                            if (op_basket_override) {
                                d1 = Onepage.getElementById('tt_tax_total_' + i + '_div_basket');
                                if (d1 != null) d1.style.display = 'none';
                            }
                        }
                    }
                }
            }
        }
        stru = Onepage.getElementById('tt_total_txt');
        if (stru != null) str = stru.innerHTML;
        else str = '';
        if (str == '') {
            d1 = Onepage.getElementById('tt_total_txt');
            if (d1 != null) d1.innerHTML = op_textinclship;
        }
        d1 = Onepage.getElementById('tt_total');
        if (d1 != null) d1.innerHTML = Onepage.formatCurrency(order_total);
        if (op_basket_override) {
            d1 = Onepage.getElementById('tt_total_basket');
            if (d1 != null) d1.innerHTML = Onepage.formatCurrency(order_total);
        }
        return "";
    },

    syncShippingAndPayment: function (paymentelement) {
        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
            Onepage.op_log('syncShippingAndPayment');

        if (op_noshipping == false) {
            val = Onepage.getVShippingRate();

            if (op_shipping_inside_basket) {
                var d = Onepage.getElementById('new_shipping');
                if (d != null)
                    d.value = val;
            }
            var s = Onepage.getElementById('shipping_rate_id_coupon');
            if (s != null) {
                s.value = val;
            }

        }
        valp = Onepage.getValueOfSPaymentMethod();
        if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
            Onepage.op_log('payment:');
            Onepage.op_log(valp);
        }
        if (op_payment_inside_basket) {
            var df = Onepage.getElementById('new_payment');
            if (df != null)
                df.value = valp;
        }

        if ((Onepage.last_payment_extra != '') && (Onepage.last_payment_extra != 'extra_payment_' + valp)) {
            var d = Onepage.getElementById(Onepage.last_payment_extra);
            if (d != null)
                d.style.display = 'none';
        }
        //extra_payment_5
        var d = Onepage.getElementById('extra_payment_' + valp);
        var extraShown = false;
        if (d != null) {

            Onepage.last_payment_extra = 'extra_payment_' + valp;
            d.style.display = 'block';
            extraShown = true;
            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                Onepage.op_log('showing extra');
        }
        else {
            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                Onepage.op_log('extra not found for' + valp);
        }

        if ((!extraShown) && (op_payment_inside_basket)) {

            Onepage.togglePaymentDisplay(false);
        }
        if ((extraShown) && (op_payment_inside_basket)) {
            Onepage.togglePaymentDisplay(true);

        }


        dd = Onepage.getElementById('paypalExpress_ecm');
        if (dd != null && (typeof(dd) != 'undefined'))
            if (valp == op_paypal_id) {
                // last test:
                // direct payments use payment_method_id_(PAYPALID)
                //
                if (op_paypal_direct == true) {
                    xx = Onepage.getElementById('payment_method_id_' + valp);
                    if (xx.checked != true)
                        dd.value = '2';
                    else
                        dd.value = '';
                }
                else {
                    dd.value = '2';
                }


            }
            else {
                dd.value = '';
            }

        var p = Onepage.getElementById('payment_method_id_coupon');

        if (p != null) {
            p.value = valp;
        }


    },

    /* changes text of Order total
	*   msg3 is the "Order total: "
	*   curr is currency symbol html encoded
	*   order_total is VM order total (for US tax system it is generated in shippnig methods)
  */
    changeTextOnePage: function (msg3, curr, order_total) {

        Onepage.syncShippingAndPayment();


        /*
	  if (op_payment_inside_basket || op_shipping_inside_basket)
	  {
	   syncShippingAndPayment();
	  }
	  */
        if ((never_show_total != null) && (never_show_total == true) && (!op_override_basket)) return true;
        var op_ship_base = 0;
        // new in version 2

        var ship_id = Onepage.getInputIDShippingRate(true);

        sd = Onepage.getElementById('saved_shipping_id');
        if (sd != null)
            sd.value = ship_id;

        var ship_id = Onepage.getInputIDShippingRate(false);

        var payment_id = Onepage.getPaymentId();

        return Onepage.getTotals(ship_id, payment_id);

    },

    op_show_all_including: function (msg3, strtotal, curr, tax_base, tax, tax_rate, op_ship_base, payment_discount) {
        var ship_info = '';
        var payment_info = '';
        var product_grand = '';

        if (op_always_show_all) {
            ship_info = '<span style="font-size: 100%">' + op_shipping_txt + ': ' + Onepage.formatCurrency(op_ship_base) + "</span><br />";
            if ((payment_discount != null) && (payment_discount != '') && (payment_discount != 0))
                payment_info = '<span style="font-size: 100%">Payment discount & fees: ' + Onepage.formatCurrency((-1) * parseFloat(payment_discount.toString())) + "</span><br />";
            //if (op_show_prices_including_tax=='1')
            //var op_grand_subtotal2 = parseFloat(op_grand_subtotal - parseFloat(parseFloat(op_grand_subtotal) / (1+parseFloat(tax_rate))));
            if (op_show_prices_including_tax == '1')
                product_grand = '<span style="font-size: 100%">Product grand subtotal: ' + Onepage.formatCurrency(op_grand_subtotal / (1 + tax_rate)) + "</span><br />";
            else
                product_grand = '<span style="font-size: 100%">Product grand subtotal: ' + Onepage.formatCurrency(op_grand_subtotal) + "</span><br />";
        }

        op_total_total = strtotal;


        var tax_rate_perc = parseFloat(tax_rate) * 100;

        if (Math.round(tax_rate_perc) == tax_rate_perc)
            tax_rate_perc = tax_rate_perc.toFixed(0).toString();
        else
            tax_rate_perc = tax_rate_perc.toFixed(1);

        var cup_txt = '';
        if (op_coupon_amount != null)
            if (op_coupon_amount != 0) {
                cup_txt = '<span style="font-size: 100%">' + op_coupon_discount_txt + ': -' + Onepage.formatCurrency(op_coupon_amount.toString()) + "</span><br />";
            }
        var show_text = msg3 + "<span style='font-size:200%;'>" + Onepage.formatCurrency(op_total_total) + " </span>";

        if (((tax > 0) && (tax_rate > 0)) && (op_dont_show_taxes != '1')) {

            // tax_base = curr+Onepage.formatCurrency(tax_base);
            //  tax = tax);
            //tax_rate = (parseFloat(tax_rate.toString()) * 100).toFixed(2);
            show_text = product_grand + ship_info + payment_info + "<span style='font-size: 100%'>" + op_subtotal_txt + ": " + Onepage.formatCurrency(tax_base) + "</span><br /><span style='font-size: 100%;'>" + op_tax_txt + " (" + tax_rate_perc + "%): " + Onepage.formatCurrency(tax) + "</span><br />" + cup_txt + show_text;
        }

        d1 = Onepage.getElementById("totalam");
        if (d1 != null) d1.innerHTML = show_text;

    },


    /* This function alters visibility of shipping address
	 *
	 */
    showSA: function (chk, divid) {

        if (!(chk != null)) chk = Onepage.getElementById('sachone');
        if (!(divid != null)) divid = 'idsa';

        var d = Onepage.getElementById(divid);
        if (d != null) {
            d.style.display = chk.checked ? '' : 'none';
            if (typeof jQuery != 'undefined') {
                var r = jQuery(d);

                if (typeof r.trigger != 'undefined') {
                    r.trigger('refresh');
                }

            }
        }

        if (typeof jQuery != 'undefined') {
            var r = jQuery(chk);
            if (typeof r.trigger != 'undefined') {
                r.trigger('refresh');
            }

        }


        if (chk.checked) {
            var elopc = Onepage.getElementById('shipto_virtuemart_country_id');
            /*
	   jQuery('#opcform').('input,textarea,select,button').each(function(el){
			if (el.hasClass('opcrequired')) {
				el.attr('class', 'required');
			}

	   });
	   */
        }
        else {
            var elopc = Onepage.getElementById('virtuemart_country_id');
            /*
	     jQuery('#opcform').('input,textarea,select,button').each(function(el){
			if (el.hasClass('required')) {
				el.attr('class', 'opcrequired');
			}

	   });
	   */
        }


        // if we have a new country in shipping fields, let's update it
        if (elopc != null) {

            Onepage.op_runSS(elopc);
        }

    },


    // this function is used when using select box for payment methods
    runPaySelect: function (element) {
        ind = element.selectedIndex;
        value = element.options[ind].value;

        hasExtra = Onepage.getAttr(element.options[ind], 'rel');
        /*
		if (hasExtra != null)
		if (hasExtra != 0)
		{
			d = Onepage.getElementById('extra_payment_'+hasExtra);
			if (d != null)
			{
				d.style.display = 'block';
				op_last_payment_extra = d;
			}
		}
		else
		{
			if (op_last_payment_extra != null)
				op_last_payment_extra.style.display = 'none';
		}
		*/
        Onepage.runPay(value, value, op_textinclship, op_currency, op_ordertotal, element);
    },
    /*
	 * This function is triggered when clicked on payment methods when CC payments are NOT there
	 */
    runPay: function (msg_info, msg_text, msg3, curr, order_total, element) {
        if (Onepage.isRegistration()) return true;
        Onepage.setOpcId();

        if (typeof(msg_info) == 'undefined' || msg_info == null || msg_info == '') {
            var p = Onepage.getValueOfSPaymentMethod(element);
            msg_info = p;
            msg_text = p;
            msg3 = op_textinclship;
            curr = op_currency;
            order_total = op_ordertotal;
        }

        if (typeof(pay_btn[msg_info]) != 'undefined' && pay_msg[msg_info] != null) msg_info = pay_msg[msg_info];
        else msg_info = pay_msg['default'];

        if (typeof(pay_btn[msg_text]) != 'undefined' && pay_btn[msg_text] != null) msg_text = pay_btn[msg_text];
        else msg_text = pay_btn['default'];

        dp = Onepage.getElementById("payment_info");
        if (dp != null)
            dp.innerHTML = msg_info;
        cbt = Onepage.getElementById("confirmbtn");
        if (cbt != null) {

            if (cbt.tagName.toLowerCase() == 'input')
                cbt.value = msg_text;
            else cbt.innerHTML = msg_text;
        }

        Onepage.changeTextOnePage(msg3, curr, order_total);
        for (var x = 0; x < callAfterPaymentSelect.length; x++) {
            eval(callAfterPaymentSelect[x]);
        }

        return true;
    },

    // gets value of selected payment method
    getPaymentId: function () {
        return Onepage.getValueOfSPaymentMethod();
    },

// return address query as &address_1=xyz&address_2=yyy
    op_getaddress: function () {
        var ret = '';
        if (Onepage.shippingOpen()) {
            // different shipping address is activated

            {
                a1 = Onepage.getElementById('shipto_address_1_field');
                if (a1 != null) {
                    ret += '&address_1=' + Onepage.op_escape(a1.value);
                }
                a2 = Onepage.getElementById('shipto_address_2_field');
                if (a2 != null) {
                    ret += '&address_2=' + Onepage.op_escape(a2.value);
                }
            }
        }
        if (ret == '') {
            a1 = Onepage.getElementById('address_1_field');
            if (a1 != null) {
                ret += '&address_1=' + Onepage.op_escape(a1.value);
            }
            a2 = Onepage.getElementById('address_2_field');
            if (a2 != null) {
                ret += '&address_2=' + Onepage.op_escape(a2.value);
            }

        }

        return ret;

    },
    checkAdminForm: function (el) {

        // check if we have form
        if (typeof el.form != 'undefined')
            if (el.form != null)
                if (typeof el.form.id != 'undefined')
                    if (el.form.id == 'adminForm') return true;
                    else {
                        if (typeof el.form.name != 'undefined')
                            if (el.form.name == 'adminForm') return true;

                        return false;
                    }


        // if an element is not part of any form
        if (typeof el.form != 'undefined')
            if (!(el.form != null)) return false;

        // for the most intelligent browsers:
        return true;
    },
    buildExtra: function (op_userfields, inclPlugins) {
        var eq = '';
        var elopc = null;
        isSh = Onepage.shippingOpen();

        for (var i = 0; i < op_userfields.length; i++) {
            // filter only needed
            //if (((op_userfields[i].indexOf('shipto_')!=0) && (!isSh))  || (op_userfields[i].indexOf('shipto_')==0) && (isSh))

            if ((((op_userfields[i].indexOf('shipto_') == 0) && (isSh)) || (op_userfields[i].indexOf('shipto_') != 0))) {
                /*
		     if (typeof eval('document.adminForm.'+op_userfields[i]) != 'undefined')
			 {
			 elopc = eval('document.adminForm.'+op_userfields[i]);
			 }
			 */
                //else
                elopc = document.getElementsByName(op_userfields[i]);

                if (elopc != null)
                    for (var j = 0; j < elopc.length; j++) {
                        // ie9 bug:
                        if (elopc[j].name != op_userfields[i]) continue;
                        if (!Onepage.checkAdminForm(elopc[j])) {
                            continue;
                        }


                        switch (elopc[j].type) {
                            case 'button':
                                break;
                            case 'password':
                                break;
                            case 'number':
                            case 'email':
                            case 'text':
                                eq += '&' + op_userfields[i] + '=' + Onepage.op_escape(elopc[j].value);
                                break;
                            case 'select-one':
                                if ((typeof elopc[j].value != 'undefined') && ((elopc[j].value != null)))
                                    eq += '&' + op_userfields[i] + '=' + Onepage.op_escape(elopc[j].value);
                                else {
                                    if ((typeof elopc[j].options != 'undefined') && (elopc[j].options != null) && (typeof elopc[j].selectedIndex != 'undefined') && (elopc[j].selectedIndex != null))
                                        eq += '&' + op_userfields[i] + '=' + Onepage.op_escape(elopc[j].options[elopc[j].selectedIndex].value);

                                }
                                break;
                            case 'radio':
                                if ((elopc[j].checked == true) && (elopc[j].value != null))
                                    eq += '&' + op_userfields[i] + '=' + Onepage.op_escape(elopc[j].value);
                                else if (elopc[j].checked == true)
                                    eq += '&' + op_userfields[i] + '=1';
                                break;
                            case 'hidden':
                                if ((typeof elopc[j].value != 'undefined') && (elopc[j].value != null))
                                    eq += '&' + op_userfields[i] + '=' + Onepage.op_escape(elopc[j].value);
                                break;
                            case 'checkbox':
                                if ((typeof elopc[j].checked != 'undefined') && (elopc[j].value != null))
                                    if (elopc[j].checked)
                                        eq += '&' + op_userfields[i] + '=' + Onepage.op_escape(elopc[j].value);
                                    else
                                        eq += '&' + op_userfields[i] + '=0';
                                break;
                            default:
                                if ((typeof elopc[j].value != 'undefined') && (elopc[j].value != null))
                                    eq += '&' + op_userfields[i] + '=' + Onepage.op_escape(elopc[j].value);
                                break;
                        }
                    }
                elopc = document.getElementsByName(op_userfields[i] + '[]');
                if (elopc != null)
                    for (var j = 0; j < elopc.length; j++) {
                        if (!Onepage.checkAdminForm(elopc[j])) continue;
                        if ((typeof elopc[j].value != 'undefined') && (elopc[j].value != null))
                            if (((typeof elopc[j].checked != 'undefined') && (elopc[j].checked)) ||
                                (typeof elopc[j].selected != 'undefined') && (elopc[j].selected))
                                eq += '&' + op_userfields[i] + '[]=' + Onepage.op_escape(elopc[j].value);

                    }
            }
        }

        if (inclPlugins) {
            eq += Onepage.getShippingExtras();
            Onepage.getaddressedchanged();
            eq += Onepage.address_changed;
        }
        return eq;
    },
    getShippingExtras: function (fromSaved) {

        var e = document.getElementsByName("virtuemart_shipmentmethod_id");

        var id = "";

        var myElement = null;

        for (i = 0; i < e.length; i++) {
            if (typeof e[i] == 'undefined') continue;

            if (e[i].type == 'select-one') {
                if (e[i].options.length <= 0) return "";
                index = e[i].selectedIndex;
                if (index < 0) index = 0;
                if (fromSaved != null)
                    if (fromSaved) {
                        var saved_id = Onepage.getAttr(e[i].options[index], 'saved_id');
                        if (saved_id != null) {
                            return saved_id;
                        }
                    }
                var rel_id = Onepage.getAttr(e[i].options[index], 'rel_id');
                if (rel_id != null) {

                    myElement = e[i].options[index];

                    break;

                }
                if (typeof e[i].options[index].id != 'undefined') {
                    myElement = e[i].options[index];
                    break;

                }

            }
            else if ((e[i].checked == true) && (e[i].style.display != 'none')) {
                if (fromSaved != null)
                    if (fromSaved) {
                        var saved_id = Onepage.getAttr(e[i], 'saved_id');
                        if (saved_id != null) {
                            myElement = e[i];
                            break;
                        }
                    }
                var rel_id = Onepage.getAttr(e[i], 'rel_id');
                if (rel_id != null) {
                    myElement = e[i];
                    break;

                }

                // if you marked your shipping radio with multielement="id_of_the_select_drop_down"
                var multi = e[i].getAttribute('multielement', false);
                if (multi != null) {
                    var test = Onepage.getElementById(multi);
                    if (test != null) {
                        if ((test.options != null) && (test.selectedIndex != null)) {
                            if (test.options[test.selectedIndex] != null)
                                if (test.options[test.selectedIndex].getAttribute('multi_id') != null) {
                                    myElement = test.options[test.selectedIndex];
                                    break;
                                }
                        }
                    }
                    var test2 = document.getElementsByName(multi);
                    if (test2 != null) {
                        for (var i2 = 0; i2 < test2.length; i2++) {
                            if (test2[i2].checked != null)
                                if (test2[i2].checked)
                                    if (test2[i2].id != null) {
                                        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                                            Onepage.op_log('cpsol: ' + test2[i2].id);

                                        myElement = test2[i2];
                                        break;

                                    }
                        }
                    }

                }

                if (typeof e[i] != 'undefined') {
                    if (e[i].id != null) {
                        myElement = e[i];
                        break;
                    }
                    else {
                        myElement = e[i];
                        break;
                    }
                }
                else {
                    Onepage.op_log(e);
                }
            }
            else {
                if (e[i].type == "hidden") {
                    if ((e[i].value.indexOf('free_shipping') >= 0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon') < 0))) return e[i].id;
                    if ((e[i].value.indexOf('choose_shipping') >= 0) && ((typeof(e[i].id) != 'undefined') && (e[i].id.indexOf('_coupon') < 0))) {
                        myElement = e[i];
                        break;
                    }
                }
            }
        }

        if (myElement != null) {
            if (typeof myElement.getAttribute != 'undefined') {
                var jsond = myElement.getAttribute('data-json', '');
                if (jsond != null)
                    if (jsond != '') {

                        var q = '&extra_json=' + Onepage.op_escape(jsond);

                        return q;
                    }
            }
        }

        return "";
    },
    op_getSelectedCountry: function () {

        var sel_country = "";
        if (Onepage.shippingOpen()) {
            // different shipping address is activated

            var sa = Onepage.getElementById("sa_yrtnuoc_field");
            if (sa != null)
                sel_country = sa.value;
            else {

                sa = Onepage.getElementById('shipto_virtuemart_country_id');
                if (sa != null)
                    if ((typeof sa.options != 'undefined') && (sa.options != null))
                        sel_country = sa.options[sa.selectedIndex].value;
                    else if ((sa != null) && (sa.value != null)) sel_country = sa.value;

                //sel_country = sa.value;
            }
        }

        // we will get country from bill to
        if (sel_country == "") {
            var ba = Onepage.getElementById("country_field");
            if (ba != null)
                sel_country = ba.value;
            else {
                ba = Onepage.getElementById('virtuemart_country_id');
                if (ba != null) {
                    if ((typeof ba.options != 'undefined') && (ba.options != null))
                        sel_country = ba.options[ba.selectedIndex].value;
                    else if ((ba != null) && (ba.value != null)) sel_country = ba.value;
                }
            }

        }

        return sel_country;

    },

    op_getSelectedState: function () {
        sel_state = '';
        if (Onepage.shippingOpen()) {

            var sa = Onepage.getElementById("shipto_virtuemart_state_id");
            if (sa != null) {
                if (((typeof sa.options != 'undefined') && (sa.options != null)) && (sa.selectedIndex != null)) {
                    if (typeof sa.options[sa.selectedIndex] != 'undefined')
                        sel_state = sa.options[sa.selectedIndex].value;
                }
                else {
                    // maybe it's hidden
                    if ((typeof sa.value != 'undefined') && (sa.value != null))
                        sel_state = sa.value;
                }
            }

        }
        if (sel_state == '') {
            var c2 = Onepage.getElementById("virtuemart_state_id");
            if (c2 != null) {
                if ((typeof c2.options != 'undefined') && (c2.options != null)) {
                    if (typeof c2.options[c2.selectedIndex] != 'undefined')
                        sel_state = c2.options[c2.selectedIndex].value;
                }
                else {


                    // maybe it's hidden
                    if ((typeof c2.value != 'undefined') && (c2.value != null))
                        sel_state = c2.value;

                }
            }
        }
        return sel_state;
    },

// return true if the shipping fields are open
    shippingOpen: function () {

        if (typeof shippingOpenStatus == 'undefined')
            shippingOpenStatus = false;

        var sc = Onepage.getElementById("sachone");
        if (sc != null) {
            if ((typeof(sc.checked) != 'undefined' && sc.checked) || (typeof(sc.selected) != 'undefined' && sc.selected)) {

                if (!shippingOpenStatus)
                    for (var i = 0; i < shipping_obligatory_fields.length; i++) {
                        d = Onepage.getElementById('shipto_' + shipping_obligatory_fields[i] + '_field');
                        if ((typeof d != 'undefined') && (d != null)) {
                            d.setAttribute('required', 'required');
                            d.setAttribute('aria-required', 'true');
                            if (d.className.indexOf('opcrequired') >= 0) d.className = d.className.split('opcrequired').join('');
                            if (d.className.indexOf('required') < 0) d.className += ' required';


                        }


                    }

                //shippingOpenStatus = true;
                return true;
            }

            if ((typeof(sc.checked) != 'undefined' && (!sc.checked)) || (typeof(sc.selected) != 'undefined' && (!sc.selected))) {
                //shippingOpenStatus = false;
                if (typeof op_logged_in != 'undefined')
                    if (!op_logged_in) {
                        return false;
                    }
            }


        }


        if (typeof op_logged_in != 'undefined')
            if (op_logged_in) {
                var e = Onepage.getElementById('ship_to_info_id_bt');
                if (e != null) {
                    var bt_id = e.value;
                    var st_id = Onepage.getShipToId();
                    if (st_id == bt_id) {
                        //shippingOpenStatus = false;
                        return false;
                    }

                }
            }

        if ((typeof shippingOpenStatus != 'undefined') && (shippingOpenStatus)) {
            for (var i = 0; i < shipping_obligatory_fields.length; i++) {
                d = Onepage.getElementById('shipto_' + shipping_obligatory_fields[i] + '_field');
                if ((typeof d != 'undefined') && (d != null)) {
                    d.removeAttribute('required');
                    d.removeAttribute('aria-required');
                    d.removeAttribute('aria-invalid');
                    d.className = d.className.split('required').join('');
                    d.className = d.className.split('invalid').join('');

                }
                //
            }
            //...
            return true;
        }
        //shippingOpenStatus = false;
        return false;

    },

    doublemail_checkMailTwo: function (el) {
        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) {
            return true;
        }
        Onepage.email_check(el);
        // loaded from double.js
        Onepage.doublemail_checkMail();
    },

    op_log: function (msg) {

        // change in 293: CONSOLE LOGGING IS ONLY ENABLED IF GLOBAL DEBUG IN OPC IS ENABLED
        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
            if (typeof msg != 'undefined')
                if (msg != null)
                    if ((typeof console != 'undefined') && (console != null))
                        if (typeof console.log != 'undefined') {
                            if (msg != '')
                                console.log(msg);
                        }
    },

    op_getZip: function () {
        var sel_zip = '';

        if (Onepage.shippingOpen()) {
            {
                var sa = Onepage.getElementById("shipto_zip_field");
                if (sa)
                    sel_zip = sa.value;
            }
        }
        if (sel_zip == '') {
            var c2 = Onepage.getElementById("zip_field");
            if (c2 != null) {
                sel_zip = c2.value;
            }
        }
        return sel_zip;
    },


    isNotAPaymentMethod: function (invalidation) {


        if ((typeof invalidation != 'undefined') && (invalidation != null) && (invalidation == true))
            invalidation = true;
        else invalidation = false;

        var invalid_payment = false;
        if (pe != null) {
            var atr = pe.getAttribute('not_a_valid_payment', false);
            if (atr != null)
                if (atr != false) {
                    var invalid_payment = true;
                    return true;
                }
        }


        return false;


    },
// return true if problem
    isNotAShippingMethod: function (invalidation) {

        if (op_noshipping == true) return false;


        if ((typeof invalidation != 'undefined') && (invalidation != null) && (invalidation == true))
            invalidation = true;
        else invalidation = false;


        var sh = Onepage.getVShippingRate(invalidation);


        if (sh === '') return true;

        if (sh.toString().indexOf('choose_shipping') >= 0) {
            return true;
        }


        var ship_id = Onepage.getInputIDShippingRate(true);

        var sd = Onepage.getElementById('saved_shipping_id');
        if (sd != null)
            sd.value = ship_id;

        return false;
    },


// failsafe function to unblock the button in case of any problems
    unblockButton: function () {
        var so = Onepage.getElementById('confirmbtn_button');
        if (so != null) {
            so.disabled = false;
            //alert('ok');
        }
        else {
            so = Onepage.getElementById('confirmbtn');
            if (so != null)
                so.disabled = false;
        }
    },
// will disable the submit button so it cannot be pressed twice
    startValidation: function () {


        // to prevend double clicking, we are using both button and input
        so = Onepage.getElementById('confirmbtn_button');
        if (so != null) {
            so.disabled = true;
            //alert('ok');
        }
        else {
            so = Onepage.getElementById('confirmbtn');
            if (so != null)
                so.disabled = true;
        }

        if (so != null) {
            var inserting = Onepage.getElementById('checkout_loader');
            if (inserting != null) {
                inserting.style.display = 'block';
            }
            else {
                var inserting = document.createElement("div");
                inserting.id = 'checkout_loader';
                inserting.innerHTML = '<img class="opc_loader_img" src="' + op_loader_img + '" title="Loading..." alt="Loading..." />';
                if (typeof so.parentNode.insertBefore != 'undefined')
                    so.parentNode.insertBefore(inserting, so);
            }
        }
        // IE8 and IE7 check:
        if (window.attachEvent && !window.addEventListener) {
            //return true;
        }
        else
            opcsubmittimer = setTimeout('Onepage.unblockButton()', 10000);
        // if any of javascript processes take more than 10 seconds, the button will get unblocked
        // the delay can occur on google ecommerce tracking, OPC tracking or huge DOM, or maybe a long insert query


    },

// submit the form or unblock the button
    endValidation: function (retVal, dosubmit) {
        var typeB = 'submit';
        {
            // unblock the submit button
            // to prevend double clicking, we are using both button and input
            // confirmbtn_button
            so = Onepage.getElementById('confirmbtn_button');
            if (so != null) {
                so.disabled = false;
                typeB = so.type;
                //alert('ok');
            }
            else {
                so = Onepage.getElementById('confirmbtn');
                if (so != null) {
                    so.disabled = false;
                    typeB = so.type;
                }
            }
            // the form will not be sumbmitted
            if (!retVal) {


                var inserting = Onepage.getElementById('checkout_loader');
                if (inserting != null) {
                    inserting.style.display = 'none';
                }


                return false;
            }
        }


        if (!(window.attachEvent && !window.addEventListener))
            if (typeof opcsubmittimer != 'undefined')
                if (opcsubmittimer != null)
                    clearTimeout(opcsubmittimer);

        Onepage.ga('Checkout Form Submitted', 'Checkout General');

        var fs = Onepage.getElementById('form_submitted');
        if (fs != null)
            fs.value = '1';
        // updated code:
        if ((typeB == 'submit') && ((typeof dosubmit == 'undefined') || (!dosubmit)))
            return true;


        try {
            // submit the form by javascript
            document.adminForm.submit();
            return false;
        }
        catch (e) {
            // submit the form by returning true
            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                Onepage.op_log(e);

            Onepage.ga(e.toString(), 'Checkout Internal Error');

            return true;
        }

    },
// opc1 double click prevention end
    opc_checkUsername: function (wasValid) {

        if (!opc_no_duplicit_username) return wasValid;
        if (!Onepage.getRegisterAccount()) return wasValid;
        // there has not been the ajax check yet


        if (!last_username_check) {
            if (Onepage.getRegisterAccount()) {

                return false;
            }
            else {

                return wasValid;
            }

        }
        return wasValid;

    },
    opc_valid_username: function (wasValid) {
        if (!Onepage.getRegisterAccount()) return wasValid;
        var pattern = "^[^#%&*:<>?/{|}\"';()]+$";
        var regExp = new RegExp(pattern, "");
        var usd = Onepage.getElementById('username_field');
        if (usd != null) {
            if (usd.value == '') {

                var msg = op_general_error;

                if (typeof op_userfields_named['username'] != 'undefined') {
                    msg += ' ' + op_userfields_named['username'];
                }
                alert(msg);
                Onepage.ga(msg, 'Checkout Error');
                usd.className += ' invalid';
                return false;


            }
            if (!regExp.test(usd.value)) {
                usd.className += ' invalid';
                alert(JERROR_AN_ERROR_HAS_OCCURRED);
                Onepage.ga(JERROR_AN_ERROR_HAS_OCCURRED, 'Checkout Error');
                return false;
            }
            else
                usd.className = usd.className.split('invalid').join('');
        }
        return true;
    },
    opc_checkEmail: function (wasValid) {
        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) {
            return wasValid;
        }
        if (op_logged_in_joomla) return wasValid;
        if (!Onepage.emailCheckReg(true)) return false;
        if (!opc_no_duplicit_email) return wasValid;

        // there has not been the ajax check yet


        if (!last_email_check) {
            alert(email_error);
            Onepage.ga(email_error, 'Checkout Error');
            return false;


        }
        return wasValid;

    },


    getRegisterAccount: function () {
        //do not validate registration fields when the user is logged in
        if ((typeof op_logged_in != 'undefined') && (op_logged_in)) return false;
        // double check in case google has autofill
        var el2 = Onepage.getElementById('register_account');
        if (el2 != null) {

            if (el2.type == 'hidden') {
                if (el2.value == '1') return true;
                else return false;
            }


            if (el2.checked == true) return true;
            else return false;
        }
        else {
            // if we have not register account check if password exists
            d = Onepage.getElementById('opc_password_field');
            if (d != null)
                return true;
            else
                return false;
        }
        el = document.getElementsByName('register_account');
        if (el != null)
            if (el.length > 0) {
                if (typeof el.checked != 'undefined') {
                    if (el.checked) return true;
                    if (el.checked == false) return false;

                }
                if (typeof el.type != 'undefined') {
                    if (el.type != null)
                        if (el.type == 'hidden') {
                            if (el.value != '1') return false;
                        }
                }
                else {
                    if (el[0].type != null) {
                        if (el[0].type == 'hidden') {
                            if (el[0].value != '1') return false;

                        }

                        if (el[0].type == 'checkbox') {
                            if (el[0].value != '1') return false;

                        }


                    }
                    if (el[0].checked != null) {
                        if (el[0].checked) return true;
                        else return false;
                    }

                }


            }
            else {
                // if we have not register account check if password exists
                d = Onepage.getElementById('opc_password_field');
                if (d != null)
                    return true;
                else
                    return false;

            }
        // by default register account
        return true;

    },

    business2field: function (el, ajax) {

        Onepage.getBusinessState(el);

        if (ajax) {
            return Onepage.op_runSS(el);
        }
        return;

    },
    isBusinessCustomer: function () {
        var is_b = false;
        d = Onepage.getElementById('opc_is_business');
        if (d != null) {
            if (d.value == 1) {
                return 1;
            }
            else {
                return 0;
            }
        }
        return -1;
    },
    toggleFields: function (arr, suffix, hide, prefix) {
        if (typeof prefix == 'undefined') prefix = '';
        if (!(prefix != null)) prefix = '';
        if (arr != null)
            if (arr.length > 0) {
                for (var i = 0; i < arr.length; i++) {
                    var d = Onepage.getElementById(prefix + arr[i] + suffix);

                    if (d != null) {

                        if (hide) {

                            if (d.style.display != 'none')
                                if (typeof d.setAttribute != 'undefined')
                                    d.setAttribute('display_style', d.style.display);

                            d.style.display = 'none';
                        }
                        else {
                            if (typeof d.getAttribute != 'undefined')
                                var display_style = d.getAttribute('display_style', 'block');
                            else
                                var display_style = 'block';

                            d.style.display = display_style;
                        }

                    }
                }
            }
    },
    getBusinessState: function () {
        var is_b = false;
        var d = Onepage.getElementById('opc_is_business');
        if (d != null) {
            if (d.value == 1)
                var is_b = true;
        }

        if (typeof business_selector != 'undefined')
            if (business_selector != '') {

                var el = document.getElementsByName(business_selector);

                if (typeof business_fields2 != 'undefined')
                    if (business_fields2.length > 0)
                        if (el != null)
                            if (el.length > 0) {
                                for (var i = 0; i < el.length; i++) {
                                    if (Onepage.checkAdminForm(el[i])) {
                                        if (typeof el[i].checked != 'undefined') {
                                            if (el[i].checked) {
                                                //show
                                                Onepage.toggleFields(business_fields2, '_div', false);
                                                Onepage.toggleFields(business_fields2, '_input', false);
                                                Onepage.toggleFields(business_fields2, '', false, 'opc_business_');
                                                if ((typeof business_obligatory_fields != 'undefined') && (business_obligatory_fields != null) && (business_obligatory_fields.length > 0)) {
                                                    Onepage.toggleRequired(business_obligatory_fields, false, '', '_field');
                                                }
                                                is_b = true;
                                            }
                                            else {
                                                //hide
                                                Onepage.toggleFields(business_fields2, '_div', true);
                                                Onepage.toggleFields(business_fields2, '_input', true);
                                                Onepage.toggleFields(business_fields2, '', true, 'opc_business_');

                                                if ((typeof business_obligatory_fields != 'undefined') && (business_obligatory_fields != null) && (business_obligatory_fields.length > 0)) {
                                                    Onepage.toggleRequired(business_obligatory_fields, true, '', '_field');
                                                }

                                                is_b = false;


                                            }
                                        }
                                        else if (typeof el[i].selectedIndex != 'undefined') {
                                            if (typeof el[i].options != 'undefined') {
                                                var val = el[i].options[el[i].selectedIndex].value;
                                                if (val === business2_value) {
                                                    //show
                                                    Onepage.toggleFields(business_fields2, '_div', false);
                                                    Onepage.toggleFields(business_fields2, '_input', false);
                                                    Onepage.toggleFields(business_fields2, '', false, 'opc_business_');
                                                    if ((typeof business_obligatory_fields != 'undefined') && (business_obligatory_fields != null) && (business_obligatory_fields.length > 0)) {
                                                        Onepage.toggleRequired(business_obligatory_fields, false, '', '_field');
                                                    }

                                                    is_b = true;
                                                }
                                                else {
                                                    //hide
                                                    Onepage.toggleFields(business_fields2, '_div', true);
                                                    Onepage.toggleFields(business_fields2, '_input', true);
                                                    Onepage.toggleFields(business_fields2, '', true, 'opc_business_');

                                                    if ((typeof business_obligatory_fields != 'undefined') && (business_obligatory_fields != null) && (business_obligatory_fields.length > 0)) {
                                                        Onepage.toggleRequired(business_obligatory_fields, true, '', '_field');
                                                    }

                                                    is_b = false;

                                                }
                                            }
                                        }
                                        else {
                                            if (typeof el[i].value != 'undefined')
                                                if (el[i].value == business2_value) {
                                                    //show
                                                    Onepage.toggleFields(business_fields2, '_div', false);
                                                    Onepage.toggleFields(business_fields2, '_input', false);
                                                    Onepage.toggleFields(business_fields2, '', false, 'opc_business_');

                                                    if ((typeof business_obligatory_fields != 'undefined') && (business_obligatory_fields != null) && (business_obligatory_fields.length > 0)) {
                                                        Onepage.toggleRequired(business_obligatory_fields, true, '', '_field');
                                                    }

                                                    is_b = true;

                                                }
                                                else {
                                                    //hide
                                                    Onepage.toggleFields(business_fields2, '_div', true);
                                                    Onepage.toggleFields(business_fields2, '_input', true);
                                                    Onepage.toggleFields(business_fields2, '', true, 'opc_business_');

                                                    if ((typeof business_obligatory_fields != 'undefined') && (business_obligatory_fields != null) && (business_obligatory_fields.length > 0)) {
                                                        Onepage.toggleRequired(business_obligatory_fields, true, '', '_field');
                                                    }

                                                    is_b = false;

                                                }
                                        }

                                        // just one field is enough...
                                        break;
                                    }
                                }

                                // if the toggler is of a business type:
                                if (typeof window.is_business2 != 'undefined')
                                    if (window.is_business2 === true)
                                        if (is_b) {
                                            if (d != null) d.value = 1;

                                        }
                                        else {
                                            if (d != null) d.value = 0;
                                        }


                            }
            }

        return is_b;
        /*
		if (is_b === true)
		{
			if (d != null) d.value = 1;

		}
		*/

        /*
	if (!is_b)
	{
		if ((typeof business_fields != 'undefined') && (business_fields != null))
		for (var i=0; i<business_fields.length; i++)
		{
			d2 = Onepage.getElementById(business_fields[i]+'_field');
			if (d2 != null)
			d2.className = d2.className.split('required').join('notequired');


			d2 = Onepage.getElementById('shipto_'+business_fields[i]+'_field');
			if (d2 != null)
			d2.className = d2.className.split('required').join('notequired');

		}
	}
    else
	{
		if ((typeof business_fields != 'undefined') && (business_fields != null))
		for (var i=0; i<business_fields.length; i++)
		{
			d2 = Onepage.getElementById(business_fields[i]+'_field');
			if (d2 != null)
			d2.className = d2.className.split('notequired').join('required');

			d2 = Onepage.getElementById('shipto_'+business_fields[i]+'_field');
			if (d2 != null)
			d2.className = d2.className.split('notequired').join('required');

		}
	}
	*/


    },


    processPlaceholders: function () {
        var msg = '';
        // support of placeholders and browsers that do not support them
        var invalid = false;
        for (var i = 0; i < op_userfields.length; i++) {
            var d = document.getElementsByName(op_userfields[i]);
            if (d != null)
                if (d.length > 0) {

                    var title = Onepage.getAttr(d[0], 'placeholder');


                    if (title != null)
                        if (title != 0)
                            if (d[0].className.indexOf('notrequired') < 0)
                                if (d[0].className.indexOf('required') >= 0)
                                    if (typeof d[0].value != 'undefined')
                                        if (d[0].value == title) {
                                            invalid = true;
                                            d[0].className = d[0].className.split('invalid').join('').concat(' invalid ');

                                            if (msg == '') msg = title;
                                            else
                                                msg += ', ' + title;


                                        }
                }

        }
        if (invalid) return msg;
        return false;
    },
    emailCheckReg: function (showalert, id) {
        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) {
            return true;
        }
        var found = false;
        if (id != null) {
            var em = Onepage.getElementById(id);
            found = true;
        }
        else {
            var dd = Onepage.getElementById('guest_email');
            if (dd != null)
                if (dd.value != '') {
                    var em = dd;
                    found = true;
                }
        }
        if (!found)
            var em = Onepage.getElementById('email_field');
        if (em != null) {
            var emv = em.value;
            //var pattern = "[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])";
            var pattern = "[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])";


            //var pattern =/^[a-zA-Z0-9._-]+(\+[a-zA-Z0-9._-]+)*@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
            var regExp = new RegExp(pattern, "");
            if (!regExp.test(emv)) {
                em.className += ' invalid';
                if (showalert) {
                    alert(op_email_error);
                }
                Onepage.ga(op_email_error, 'Checkout Error');
                return false;
            }
            else {
                em.className = em.className.split('invalid').join('');
                return true;
            }
        }
        return true;
    },

    removePlaceholders: function () {

        for (var i = 0; i < op_userfields.length; i++) {
            d = document.getElementsByName(op_userfields[i]);
            if (d != null)
                if (d.length > 0) {

                    var title = Onepage.getAttr(d[0], 'placeholder');
                    if (title != null)
                        if (title != 0)
                            if (typeof d[0].value != 'undefined')
                                if (d[0].value == title) {
                                    d[0].value = '';
                                }
                }

        }
    },
    formSubmit: function (event, el) {
        if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
            Onepage.op_log(event);
            Onepage.op_log(el);
        }
        return false;
    },
    confirmDialog: function () {
        if (typeof jQueryOPC !== 'undefined') {
            var mJ = jQueryOPC;
        }
        else if (typeof jQuery !== 'undefined') {
            var mJ = jQuery;
        }
        else {
            return Onepage.endValidation(true);
        }

        if (typeof mJ === 'undefined') return Onepage.endValidation(true);

        var md = mJ('#opc_cf_dialog');
        if (typeof md.dialog === 'undefined') {
            return Onepage.endValidation(true);
        }

        var ins = Onepage.getElementById('opc_cf_totals');

        var myTotals = Onepage.getTotalsObj();
        if (myTotals.totalsset) {
            mJ('.dynamic_row_dialog').remove();

            var d3 = ins.cloneNode(true);
            d4 = ins.cloneNode(true);


            d3.style.display = '';
            d3.className = 'dynamic_row_dialog ' + d3.className;
            d3.id = 'rand' + Math.floor((Math.random() * 100000) + 1).toString();
            ;
            var html = d3.innerHTML.split('{row_name}').join(myTotals.order_total.name).split('{row_value}').join(myTotals.order_total.valuetxt);

            Onepage.setInnerHtml(d3, html);
            if (typeof ins.parentNode != 'undefined')
                if (ins.parentNode != null) {
                    ins.parentNode.insertBefore(d3, ins.nextSibling);
                }

            if (myTotals.tax_data.length > 0) {
                for (var i = 0; i < myTotals.tax_data.length; i++) {
                    if (myTotals.tax_data[i].valuetxt != '') {
                        var row_name = myTotals.tax_data[i].name;
                        var row_value = myTotals.tax_data[i].valuetxt;
                        d3 = d4.cloneNode(true);
                        d3.style.display = '';
                        d3.className += ' dynamic_row_dialog';
                        d3.id = 'rand' + Math.floor((Math.random() * 100000) + 1).toString();
                        var html = d3.innerHTML.split('{row_name}').join(row_name).split('{row_value}').join(row_value);

                        Onepage.setInnerHtml(d3, html);
                        if (typeof ins.parentNode != 'undefined')
                            if (ins.parentNode != null) {
                                ins.parentNode.insertBefore(d3, ins.nextSibling);
                            }
                    }
                }
            }

            if (myTotals.order_shipping.valuetxt != '') {
                var row_name = myTotals.order_shipping.name;
                var row_value = myTotals.order_shipping.valuetxt;
                d3 = d4.cloneNode(true);
                d3.style.display = '';
                d3.className += ' dynamic_row_dialog';
                d3.id = 'rand' + Math.floor((Math.random() * 100000) + 1).toString();
                var html = d3.innerHTML.split('{row_name}').join(row_name).split('{row_value}').join(row_value);

                Onepage.setInnerHtml(d3, html);
                if (typeof ins.parentNode != 'undefined')
                    if (ins.parentNode != null) {
                        ins.parentNode.insertBefore(d3, ins.nextSibling);
                    }
            }

            if (myTotals.payment_discount.valuetxt != '') {
                var row_name = myTotals.payment_discount.name;
                var row_value = myTotals.payment_discount.valuetxt;
                d3 = d4.cloneNode(true);
                d3.style.display = '';
                d3.className += ' dynamic_row_dialog';
                d3.id = 'rand' + Math.floor((Math.random() * 100000) + 1).toString();
                var html = d3.innerHTML.split('{row_name}').join(row_name).split('{row_value}').join(row_value);

                Onepage.setInnerHtml(d3, html);
                if (typeof ins.parentNode != 'undefined')
                    if (ins.parentNode != null) {
                        ins.parentNode.insertBefore(d3, ins.nextSibling);
                    }
            }

            if (myTotals.coupon_discount.valuetxt != '') {
                var row_name = myTotals.coupon_discount.name;
                var row_value = myTotals.coupon_discount.valuetxt;
                d3 = d4.cloneNode(true);
                d3.style.display = '';
                d3.className += ' dynamic_row_dialog';
                d3.id = 'rand' + Math.floor((Math.random() * 100000) + 1).toString();
                var html = d3.innerHTML.split('{row_name}').join(row_name).split('{row_value}').join(row_value);

                Onepage.setInnerHtml(d3, html);
                if (typeof ins.parentNode != 'undefined')
                    if (ins.parentNode != null) {
                        ins.parentNode.insertBefore(d3, ins.nextSibling);
                    }
            }

            if (myTotals.coupon_discount2.valuetxt != '') {
                var row_name = myTotals.coupon_discount2.name;
                var row_value = myTotals.coupon_discount2.valuetxt;
                d3 = d4.cloneNode(true);
                d3.style.display = '';
                d3.className += ' dynamic_row_dialog';
                d3.id = 'rand' + Math.floor((Math.random() * 100000) + 1).toString();
                var html = d3.innerHTML.split('{row_name}').join(row_name).split('{row_value}').join(row_value);

                Onepage.setInnerHtml(d3, html);
                if (typeof ins.parentNode != 'undefined')
                    if (ins.parentNode != null) {
                        ins.parentNode.insertBefore(d3, ins.nextSibling);
                    }
            }


            if (myTotals.subtotal.valuetxt != '') {
                var row_name = myTotals.subtotal.name;
                var row_value = myTotals.subtotal.valuetxt;
                d3 = d4.cloneNode(true);
                d3.style.display = '';
                d3.className += ' dynamic_row_dialog';
                d3.id = 'rand' + Math.floor((Math.random() * 100000) + 1).toString();
                var html = d3.innerHTML.split('{row_name}').join(row_name).split('{row_value}').join(row_value);

                Onepage.setInnerHtml(d3, html);
                if (typeof ins.parentNode != 'undefined')
                    if (ins.parentNode != null) {
                        ins.parentNode.insertBefore(d3, ins.nextSibling);
                    }
            }

        }

        if (typeof window.opc_cf_dialogMinWidth == 'undefined') {
            window.opc_cf_dialogMinWidth = 450;
        }

        mJ('#opc_cf_dialog').dialog({
            dialogClass: "no-close opc_cf_dialog",
            title: payment_button_def,
            autoOpen: true,
            draggable: false,
            resizable: false,
            modal: true,
            minWidth: window.opc_cf_dialogMinWidth,
            buttons: [

                {
                    text: opc_cancel,
                    class: 'cancel_button btn danger btn-danger',
                    classses: 'cancel_button btn danger btn-danger',
                    click: function () {

                        mJ(this).dialog("close");
                        return Onepage.endValidation(false);
                    }
                },
                {
                    text: payment_button_def,
                    class: 'ok_button btn primary btn-success',
                    classses: 'ok_button btn primary btn-success',
                    click: function () {
                        mJ(this).dialog("close");
                        return Onepage.endValidation(true, true);
                    }
                },
            ]
        });
        return false;
    },

    addStyle: function (path) {
        // path cannot start with slash !
        var resource = document.createElement('style');
        resource.type = "text/css";
        resource.src = op_relative_url + path;

        if (typeof jQuery != 'undefined') {
            jQuery("head").append(resource);
        }
        else {
            if (typeof document.getElementsByTagName == 'undefined') return false;

            var scripts = document.getElementsByTagName('style');
            var script = scripts[scripts.length - 1];
            script.parentNode.insertBefore(resource, script);
        }
        return true;
    },
    addScript: function (path) {
        // path cannot start with slash !
        var resource = document.createElement('script');
        resource.type = "text/javascript";
        resource.src = op_relative_url + path;

        if (typeof jQuery != 'undefined') {
            jQuery("head").append(resource);
        }
        else {
            if (typeof document.getElementsByTagName == 'undefined') return false;

            var scripts = document.getElementsByTagName('script');
            var script = scripts[scripts.length - 1];
            script.parentNode.insertBefore(resource, script);
        }
        return true;
    },

    validateFormOnePage: function (event, el, wasValid) {

        try {
            if (typeof jQuery != 'undefined') {
                var af = jQuery('#adminForm');
                if (typeof af.unbind != 'undefined') af.unbind();
                if (typeof af.die != 'undefined') af.die();
            }
        }
        catch (e) {
            Onepage.ga(e.toString(), 'Checkout Internal Error');
            Onepage.op_log(e);
        }


        if (!opc_debug) {
            try {
                var ret = Onepage.validateFormOnePagePrivate(wasValid, event);
                if (!ret) return ret;


                var valid2 = true;
                for (var i = 0; i < callAfterConfirmed.length; i++) {
                    try {
                        if (callAfterConfirmed[i] != null)
                            if (typeof callAfterConfirmed[i] == 'function') {
                                var valid3 = callAfterConfirmed[i](event);
                                if (valid3 != null)
                                    if (!valid3) valid2 = false;

                            }
                            else {
                                Onepage.op_log('Deprecated usage of callAfterConfirmed triggerer');
                            }
                    }
                    catch (e) {
                        Onepage.ga(e.toString(), 'Checkout Internal Error');
                        Onepage.op_log(e);
                    }
                }
                //if any of the callAfterConfirmed returns false, then the checkout is blocked
                if (valid2 == false) return false;


                if ((typeof document.adminForm != 'undefined') && (typeof document.adminForm.submit != 'undefined')) {
                    document.adminForm.submit();
                    return false;
                }
                else {
                    return true;
                }

                return true;

            }
            catch (e) {
                //if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                Onepage.op_log(e);
                Onepage.ga(e.toString(), 'Checkout Internal Error');
                if ((typeof document.adminForm != 'undefined') && (typeof document.adminForm.submit != 'undefined')) {
                    document.adminForm.submit();
                    return false;
                }
                else {
                    return true;
                }

                return true;
            }
        }
        else {
            var ret = Onepage.validateFormOnePagePrivate(wasValid, event);
            if (!ret) return ret;
            if ((typeof document.adminForm != 'undefined') && (typeof document.adminForm.submit != 'undefined')) {
                document.adminForm.submit();
                return false;
            }
            else {
                return true;
            }

            return true;

        }
        return true;
    },
    isRegistration: function () {
        var isRegistration = false;
        if (typeof opc_default_task != 'undefined')
            if (opc_default_task === 'opcregister') {
                return true;
            }
            else if (opc_default_task === 'opcthird') {
                return true;
            }
            else if (opc_default_task === 'add_shopper') {
                return true;
            }
        return false;


    },
    validateFormOnePagePrivate: function (wasValid, event) {

        var isRegistration = Onepage.isRegistration();


        if ((typeof document.adminForm != 'undefined') && (typeof opc_default_option != 'undefined')) {
            document.adminForm.option.value = opc_default_option;
            document.adminForm.task.value = opc_default_task;
            document.adminForm.action = opc_action_url;

            if (opc_default_task === 'opcregister') {
                isRegistration = true;
                Onepage.ga('Registration Form Submitted', 'OPC Registration');
            }
            else {
                Onepage.ga('Checkout Form Submit Clicked', 'Checkout General');
            }
        }


        // prevent double submission:
        var fs = Onepage.getElementById('form_submitted');
        if (fs != null)
            if (fs.value == '1') return false;


        if (wasValid != null) {
            ;
        }
        else wasValid = true;

        if (wasValid) Onepage.startValidation();

        if (!isRegistration)
            if (!Onepage.checkMinPov()) {
                Onepage.ga('Minimum order value not reached', 'Checkout Error');
                return Onepage.endValidation(false);
            }

        if (op_logged_in != '1') {
            if (!Onepage.opc_checkUsername(wasValid)) {
                Onepage.ga(username_error, 'Checkout Error');
                alert(username_error);

                return Onepage.endValidation(false);
            }

            if (!Onepage.opc_valid_username(wasValid)) {
                Onepage.ga('Invalid Username', 'Checkout Error');
                return Onepage.endValidation(false);
            }
        }

        var isGuest = false;
        var dg = Onepage.getElementById('guest_email');
        if (dg != null)
            if (dg.value != '')
                isGuest = true;

        if (!isGuest)
            if (op_logged_in != '1')
            /*if (!Onepage.opc_checkEmail(wasValid))
   {
	   Onepage.ga('Email already in use', 'Checkout Error');
	   return Onepage.endValidation(false);
   }*/

                Onepage.getBusinessState();
        var invalidf = new Array();
        // registration validation
        // var elem = jQuery('#name_field');
        //	elem.attr('class', "required");
        if (op_logged_in != '1') {
            d = Onepage.getElementById('name_field');
            var name_f_v = null;
            if (d != null) {
                if (Onepage.getRegisterAccount()) {
                    d.className += ' required';
                    var nameD = d;
                    Onepage.makedValidated(nameD);
                    name_f_v = d.value;
                }
                else {
                    d.className = d.className.split('notrequired').join('').split('required').join('');
                    var nameD = d;
                    Onepage.makedValidated(nameD);
                }
            }


            var pwd_value = null;
            d = Onepage.getElementById('register_account');
            if (!isGuest)
                if (d != null && (typeof d != 'undefined')) {
                    //if ((d.checked) || ((!(d.checked != null)) && d.value=='1'))
                    if (Onepage.getRegisterAccount()) {

                        if (!op_usernameisemail) {
                            // if register account checked, make sure username, pwd1 and pwd2 are required
                            var d2 = Onepage.getElementById('username_field');
                            if (d2 != null) {
                                d2.className += " required";
                            }
                        }

                        var d2 = Onepage.getElementById('opc_password_field');
                        if (d2 != null) {
                            d2.classnName += " required";
                            pwd_value = d2.value;
                            var pwdD = d2;
                        }

                        d2 = Onepage.getElementById('opc_password2_field');
                        if (d2 != null) {
                            d2.className += ' required';
                            pwd_value = d2.value;
                            var pwdD = d2;

                        }
                    }
                    else {
                        if (!op_usernameisemail) {
                            // unset required for username, pwd1 and pwd2
                            var d2 = Onepage.getElementById('username_field');
                            if (d2 != null) {


                                d2.className = d2.className.split('notrequired').join('').split('required').join('');
                            }
                        }
                        d2 = Onepage.getElementById('opc_password_field');
                        if (d2 != null) {


                            d2.className = d2.className.split('notrequired').join('').split('required').join('');
                        }
                        var d2 = Onepage.getElementById('opc_password2_field');
                        if (d2 != null) {
                            d2.className = d2.className.split('notrequired').join('').split('required').join('');
                        }
                    }
                }
                else {
                    {

                        var d2 = Onepage.getElementById('username_field');
                        if (d2 != null) {
                            d2.className += ' required';

                        }
                        var d2 = Onepage.getElementById('password_field');
                        if (d2 != null) {
                            d2.className += ' required';
                            pwd_value = d2.value;
                            var pwdD = d2;
                        }
                        var d2 = Onepage.getElementById('opc_password_field');
                        if (d2 != null) {
                            d2.className += ' required';
                            pwd_value = d2.value;
                            var pwdD = d2;

                        }
                        var d2 = Onepage.getElementById('opc_password2_field');
                        if (d2 != null) {
                            var pwdD = d2;
                            d2.className += ' required';
                            pwd_value = d2.value;
                        }


                    }
                }


            if (name_f_v != null)
                if (name_f_v == '') {
                    Onepage.inValidate(nameD);
                    invalidf.push('name');
                }


            if (pwd_value != null)
                if (pwd_value == '') {
                    Onepage.inValidate(pwdD);
                    invalidf.push('password');
                    //alert(op_general_error+' '+op_userfields_named['password']);
                    //return Onepage.endValidation(false);
                }
        }
        var test = document.createElement('input');

        // before we proceed with validation we have to check placeholders:
        if (!('placeholder' in test)) {
            var isInvalid = Onepage.processPlaceholders();

            if (isInvalid) {
                alert(op_general_error + ' ' + isInvalid);
                Onepage.ga(op_general_error + ' ' + isInvalid, 'Checkout Error');
                return Onepage.endValidation(false);
            }
            else {
                Onepage.removePlaceholders();
            }
        }

        // passwords dont' match error
        if (!isGuest)
            if (Onepage.getRegisterAccount()) {
                var p = Onepage.getElementById('opc_password_field');
                if ((typeof p != 'undefined') && (p != null)) {
                    var p2 = Onepage.getElementById('opc_password2_field');
                    if (p2 != null) {
                        if (p.value != p2.value) {
                            Onepage.inValidate(p);
                            Onepage.inValidate(p2);

                            alert(op_pwderror);
                            Onepage.ga(op_pwderror, 'Checkout Error');
                            return Onepage.endValidation(false);
                        }
                        else {
                            Onepage.makedValidated(p);
                            Onepage.makedValidated(p2);
                        }
                    }
                }
            }
        // op_pwderror
        if (!isRegistration)
            if (Onepage.isNotAShippingMethod(true)) {
                alert(shipChangeCountry);
                Onepage.ga(shipChangeCountry, 'Checkout Error');
                return Onepage.endValidation(false);
            }
        var invalid_c = Onepage.getElementById('invalid_country');
        if (invalid_c != null) {
            alert(noshiptocmsg);
            Onepage.ga(noshiptocmsg, 'Checkout Error');
            return Onepage.endValidation(false);
        }

        var agreed = Onepage.getElementById('agreed_field');
        if (agreed != null)
            if (agreed.checked != null)
                if (agreed.checked != true) {
                    alert(agreedmsg);
                    Onepage.ga(agreedmsg, 'Checkout Error');
                    return Onepage.endValidation(false);
                }

        if (!isRegistration) {
            var payment_id = Onepage.getPaymentId();
            if (payment_id == 0) {
                var dd = Onepage.getElementById('opc_missing_payment');
                if (dd != null) {
                    alert(NO_PAYMENT_ERROR);
                    Onepage.ga(NO_PAYMENT_ERROR, 'Checkout Error');
                    return Onepage.endValidation(false);
                }

                var pe = Onepage.getPaymentElement();
                if (pe != null) {
                    var atr = pe.getAttribute('not_a_valid_payment', false);
                    if (atr != null)
                        if (atr != false) {
                            alert(NO_PAYMENT_ERROR);
                            Onepage.ga(NO_PAYMENT_ERROR, 'Checkout Error');
                            return Onepage.endValidation(false);

                        }
                }


            }
        }

        if (!isRegistration)
            if (op_logged_in == '1') {
                if (!isRegistration)
                    if (shipping_obligatory_fields.length > 0) {
                        var dd = document.getElementsByName('ship_to_info_id');
                        if (dd != null)
                            if (typeof dd.value != 'undefined')
                                if (dd.value != null)
                                    if ((dd.value == 'new') || ((typeof shipping_always_open != 'undefined') && (shipping_always_open == true))) {
                                        invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf, true, false);

                                    }

                        if (dd.length > 0)
                            if (dd[0] != null)
                                if (typeof dd[0].options != 'undefined') {

                                    var myval = dd[0].options[dd[0].selectedIndex].value;
                                    if (myval == 'new')
                                        invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf, true, false);
                                    else {
                                        var dx = Onepage.getElementById('ship_to_info_id_bt');
                                        var bt = '';
                                        if (dx != null) {
                                            var bt = dx.value;
                                        }
                                        if (myval != bt) {
                                            var d = Onepage.getElementById('opc_st_changed_' + myval);
                                            if (d != null)
                                                if (d.value != null)
                                                    if (d.value == '1') {
                                                        invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf, true, false);
                                                    }
                                        }
                                    }


                                }
                                else {
                                    // we do not have options
                                    if ((typeof shipping_always_open != 'undefined') && (shipping_always_open == true)) {
                                        invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf, true, false);
                                    }
                                }
                    }
            }
            else if (Onepage.shippingOpen()) {
                invalidf = Onepage.fastValidation('shipto_', shipping_obligatory_fields, wasValid, invalidf, true, false);
            }

        if ((op_logged_in != '1')) {
            invalidf = Onepage.fastValidation('', op_userfields, wasValid, invalidf, true, false);
            if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                Onepage.op_log('fields valid: ');
                Onepage.op_log(wasValid);
            }
        }
        else if (!isRegistration) {
            var dx = Onepage.getElementById('ship_to_info_id_bt');
            var bt = '';
            if (dx != null) {
                var bt = dx.value;
            }
            var d = Onepage.getElementById('opc_st_changed_' + bt);
            if (d != null)
                if (d.value != null)
                    if (d.value == '1') {
                        invalidf = Onepage.fastValidation('', op_userfields, wasValid, invalidf, true, false);
                    }

        }
        if (invalidf.length > 0) {
            var msg = op_general_error;
            for (var i = 0; i < invalidf.length; i++) {
                var dmsg = document.getElementsByName(invalidf[i]);
                var con = false;
                if (dmsg != null)
                    for (var zj = 0; zj < dmsg.length; zj++) {
                        if (typeof dmsg[zj].getAttribute != 'undefined') {
                            var tmsg = dmsg[zj].getAttribute('onerrormsg', '');
                            if (tmsg) {
                                if (msg != op_general_error)
                                    msg += ', ' + tmsg;
                                else msg += ' ' + tmsg;

                                con = true;
                            }
                        }
                    }

                if (con) continue;


                if (typeof op_userfields_named[invalidf[i]] != 'undefined') {
                    if (msg != op_general_error)
                        msg += ', ' + op_userfields_named[invalidf[i]];
                    else msg += ' ' + op_userfields_named[invalidf[i]];
                }
            }
            //we don't break validation once the name is not found as it may mean, it's not our field
            if (msg != op_general_error) {
                alert(msg);
                Onepage.ga(msg, 'Checkout Error');
                return Onepage.endValidation(false);
            }

        }

        if (!wasValid) {
            alert(op_general_error);
            Onepage.ga(op_general_error, 'Checkout Error');
            return Onepage.endValidation(false);
        }
        // we need to check email particularly
        /*if (!Onepage.emailCheckReg(true))
 {
 			     return Onepage.endValidation(false);
 }*/

        // need to check state also
        var em = Onepage.getElementById('virtuemart_state_id');
        if (em != null) {
            if ((em.className.indexOf('required') >= 0) && ((em.className.indexOf('notrequired') < 0))) {
                if (em.options != null)
                    var val = em.options[em.selectedIndex].value;
                else if (em.value != null)
                    var val = em.value
                else
                    var val = '';

                if ((val == '') || (val == 'none')) {
                    // we need to check if an empty state value is valid

                    // country:
                    var elopc = Onepage.getElementById('virtuemart_country_id');
                    if (elopc.options != null)
                        var value = elopc.options[elopc.selectedIndex].value;
                    else if (elopc.value != null)
                        var value = elopc.value;


                    if (typeof OPCStates != 'undefined') {
                        var statefor = eval('OPCStates.state_for_' + value);

                        if (typeof statefor != 'undefined') {
                            em.className += ' invalid';

                            var sMsg = '';
                            if (typeof op_userfields_named['virtuemart_state_id'] != 'undefined') {
                                sMsg += ': ' + op_userfields_named['virtuemart_state_id'];
                            }

                            alert(op_general_error + sMsg);
                            Onepage.ga(op_general_error + sMsg, 'Checkout Error');
                            return Onepage.endValidation(false);
                        }
                    }

                    em.className = em.className.split('invalid').join('');
                    /*
				var dtest = Onepage.getElementById('state_for_'+value);
				 if (!(dtest != null))
						{
						  // validation is okay
						   em.className = em.className.split('invalid').join('');
						}
						else
						{

						   em.className += ' invalid';

						   var sMsg = '';
						   if (typeof op_userfields_named['virtuemart_state_id'] != 'undefined')
								{
								   sMsg += ': '+op_userfields_named['virtuemart_state_id'];
								}

						   alert(op_general_error+sMsg);
						   return Onepage.endValidation(false);

						}
				 */
                }
            }
        }
        if (Onepage.shippingOpen()) {
            em = Onepage.getElementById('shipto_virtuemart_state_id');
            if (em != null) {
                if ((em.className.indexOf('required') >= 0) && ((em.className.indexOf('notrequired') < 0))) {
                    if (em.options != null)
                        var val = em.options[em.selectedIndex].value;
                    else if (em.value != null)
                        var val = em.value
                    else
                        var val = '';

                    if ((val == '') || (val == 'none')) {
                        // we need to check if an empty state value is valid

                        // country:
                        var elopc = Onepage.getElementById('shipto_virtuemart_country_id');
                        if (elopc.options != null)
                            var value = elopc.options[elopc.selectedIndex].value;
                        else if (elopc.value != null)
                            var value = elopc.value;


                        if (typeof OPCStates != 'undefined') {
                            var statefor = eval('OPCStates.state_for_' + value);

                            if (typeof statefor != 'undefined') {
                                em.className += ' invalid';

                                var sMsg = '';
                                if (typeof op_userfields_named['virtuemart_state_id'] != 'undefined') {
                                    sMsg += ': ' + op_userfields_named['virtuemart_state_id'];
                                }

                                alert(op_general_error + sMsg);
                                Onepage.ga(op_general_error + sMsg, 'Checkout Error');
                                return Onepage.endValidation(false);
                            }
                        }

                        em.className = em.className.split('invalid').join('');
                        /*
				var dtest = Onepage.getElementById('state_for_'+value);
				 if (!(dtest != null))
						{
						  // validation is okay
						   em.className = em.className.split('invalid').join('');
						}
						else
						{
						 var sMsg = '';
						 if (typeof op_userfields_named['virtuemart_state_id'] != 'undefined')
								{
								   sMsg += ': '+op_userfields_named['virtuemart_state_id'];
								}

						   em.className += ' invalid';
						   alert(op_general_error+' '+sMsg);
						   return Onepage.endValidation(false);

						}

					*/
                    }
                }
            }
        }

        var valid2 = true;
        // checks extensions functions
        if (callSubmitFunct != null)
            if (callSubmitFunct.length > 0) {
                for (var i = 0; i < callSubmitFunct.length; i++) {
                    if (callSubmitFunct[i] != null) {
                        // due to leagacy reasons, this can be added with addOpcTriggerer('callSubmitFunct', 'doubleEmailCheck2');
                        if (typeof callSubmitFunct[i] == 'string' &&
                            eval('typeof ' + callSubmitFunct[i]) == 'function') {
                            var valid3 = eval(callSubmitFunct[i] + '(true)');

                            if (valid3 != null)
                                if (!valid3) valid2 = false;
                        }
                        else {
                            // and it also supports: addOpcTriggerer(\'callSubmitFunct\', \'doubleEmailCheck2(\'EpostProsjekt_field\', \'field_EpostProsjekt_field\', \'email2_info2\', true)\');
                            var valid3 = eval(callSubmitFunct[x]);
                            if (valid3 != null)
                                if (!valid3) valid2 = false;
                        }
                    }
                }
            }

        //return false;


        if (valid2 != true) return Onepage.endValidation(false);
        if (wasValid != true) return Onepage.endValidation(false);


        if (!isRegistration)
            if (typeof opc_confirm_dialog != 'undefined')
                if (opc_confirm_dialog === true) {
                    return Onepage.confirmDialog();
                }


        //
        var invalid_w = Onepage.getElementById('please_wait_fox_ajax');
        if (!isRegistration)
            if (invalid_w != null) {
                alert(COM_ONEPAGE_PLEASE_WAIT);
                Onepage.ga(COM_ONEPAGE_PLEASE_WAIT, 'Checkout Error');
                return Onepage.endValidation(false);
            }


        return Onepage.endValidation(true);

    },
    op_replace_select_dynamic: function (dest, srcObj, useVals) {
        var destel = Onepage.getElementById(dest);
        if (destel != null) {
            destel.options.length = 0;

            var empty_state = document.createElement("OPTION");
            empty_state.value = 'none';
            if (typeof COM_VIRTUEMART_LIST_EMPTY_OPTION == 'undefined') COM_VIRTUEMART_LIST_EMPTY_OPTION = ' - ';
            empty_state.text = COM_VIRTUEMART_LIST_EMPTY_OPTION;
            destel.options.add(empty_state);


            for (var key in srcObj) {
                if (srcObj.hasOwnProperty(key)) {


                    var oOption = document.createElement("OPTION");
                    //o = new Option(srcel.options[i].value, srcel.options[i].text);
                    if ((typeof useVals != 'undefined') && (useVals != null) && (useVals))
                        oOption.value = srcObj[key];
                    else
                        oOption.value = key;
                    oOption.text = srcObj[key];
                    destel.options.add(oOption);

                }
            }


            if (typeof jQuery != 'undefined') {
                var r = jQuery(destel);
                if (typeof r.trigger != 'undefined') {
                    r.trigger('refresh');
                }

            }

        }
    },
    op_replace_select: function (dest, src) {
        var destel = Onepage.getElementById(dest);
        if (destel != null) {
            destel.options.length = 0;
            srcel = Onepage.getElementById(src);
            if (srcel != null) {
                for (var i = 0; i < srcel.options.length; i++) {
                    var oOption = document.createElement("OPTION");
                    //o = new Option(srcel.options[i].value, srcel.options[i].text);
                    oOption.value = srcel.options[i].value;
                    oOption.text = srcel.options[i].text;
                    destel.options.add(oOption);
                }
            }
            else {
                var oOption = document.createElement("OPTION");
                //o = new Option(srcel.options[i].value, srcel.options[i].text);
                oOption.value = '';
                oOption.text = ' - ';
                destel.options.add(oOption);

            }
        }
    },


    trackGoogleOrder: function () {

        if (op_run_google == true) {
            var c1 = Onepage.getElementById("city_field");
            var city = '';
            if (c1 != null) {
                city = c1.value;
            }
            var c2 = Onepage.getElementById("virtuemart_state_id");
            var state = '';
            if (c2 != null) {
                if (c2.selectedIndex != null) {
                    var w = c2.selectedIndex;
                    if (w != null)
                        if (w > -1)
                            state = c2.options[w].text;
                }
                else
                    state = c2.value;
            }
            var c3 = Onepage.getElementById("virtuemart_country_id");
            var country = '';
            if (c3 != null) {
                if (c3.selectedIndex != null) {
                    var w = c3.selectedIndex;
                    if (w != null)
                        if (w > -1)
                            country = c3.options[w].text;
                }
                else {
                    country = c3.value;
                }
            }
            if (state == ' - ') state = '';
            if (state == ' -= Select =- ') state = '';
            if (state == 'none') state = '';
            if (state == '-') state = '';
            // this function is not implemented
            if (!isNaN(parseFloat(op_tax_total)))
                op_tax_total = parseFloat(op_tax_total).toFixed(2);
            try {

                if (!((typeof pageTracker != 'undefined') && (pageTracker != null))) {
                    if (typeof _gat != 'undefined') {
                        pageTracker = _gat._getTrackerByName();
                        window.pageTracker = pageTracker;
                    }
                }

                if (typeof(window.pageTracker) == 'object') {

                    //alert(g_order_id+" "+op_vendor_name+" "+op_total_total+" "+op_tax_total+" "+op_ship_total+" "+city+" "+state+" "+country);
                    if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                        Onepage.op_log(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country);
                    }
                    pageTracker._addTrans(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country);
                    var ps = document.getElementsByName("prod_id");
                    if (ps != null) {
                        for (i = 0; i < ps.length; i++) {
                            var pid = ps[i].value;
                            var sku = Onepage.getElementById("prodsku_" + pid);
                            var name = Onepage.getElementById("prodname_" + pid);
                            var cat = Onepage.getElementById("prodcat_" + pid);
                            var qu = Onepage.getElementById("prodq_" + pid);
                            var pp = Onepage.getElementById("produprice_" + pid);
                            if ((sku != null) && (name != null) && (cat != null) && (qu != null) && (pp != null)) {
                                if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                                    Onepage.op_log(g_order_id, sku.value, name.value, cat.value, pp.value, qu.value);
                                }
//	alert (g_order_id+" "+sku.value+" "+name.value+" "+cat.value+" "+pp.value+" "+qu.value);
                                pageTracker._addItem(g_order_id, sku.value, name.value, cat.value, pp.value, qu.value);
                            }
                        }
                        pageTracker._trackTrans();
                    }
                }

                else {

                    if (window._gat && window._gat._getTracker) {
                        if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                            Onepage.op_log(g_order_id, op_vendor_name, op_total_total, op_tax_total, op_ship_total, city, state, country);
                        }

                        _gaq.push(['_addTrans',
                            g_order_id,           // order ID - required
                            op_vendor_name,  // affiliation or store name
                            op_total_total,          // total - required
                            op_tax_total,           // tax
                            op_ship_total,              // shipping
                            city,       // city
                            state,     // state or province
                            country             // country
                        ]);
                        var ps = document.getElementsByName("prod_id");
                        if (ps != null) {
                            for (i = 0; i < ps.length; i++) {
                                var pid = ps[i].value;
                                var sku = Onepage.getElementById("prodsku_" + pid);
                                var name = Onepage.getElementById("prodname_" + pid);
                                var cat = Onepage.getElementById("prodcat_" + pid);
                                var qu = Onepage.getElementById("prodq_" + pid);
                                var pp = Onepage.getElementById("produprice_" + pid);

                                if ((sku != null) && (name != null) && (cat != null) && (qu != null) && (pp != null)) {
                                    if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
                                        Onepage.op_log(g_order_id, sku.value, name.value, cat.value, pp.value, qu.value);
                                    }

                                    _gaq.push(['_addItem',
                                        g_order_id,           // order ID - required
                                        sku.value,           // SKU/code - required
                                        name.value,        // product name
                                        cat.value,   // category or variation
                                        pp.value,          // unit price - required
                                        qu.value               // quantity - required
                                    ]);

                                }
                            }

                        }
                        _gaq.push(['_trackTrans']);

                    }

                }
            }
            catch (e) {
                if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                    Onepage.op_log(e);
                Onepage.ga(e.toString(), 'Checkout Internal Error');
            }
        }
        // ok, lets track tracking code here
        //var td = Onepage.getElementById('tracking_div');
        if (typeof(acode) != 'undefined')
            if (acode != null)
                if (acode == '1') {
                    var tr_id = Onepage.getElementById('tracking_div');
                    if (typeof(tr_id) !== 'undefined' && tr_id != null) {
                        var html = '<iframe id="trackingIFrame" name="trackingFrame" src="' + op_securl + '?option=com_onepage&nosef=1&task=tracker&view=opc&format=opchtml&tmpl=component&controller=opc&amount=' + op_total_total + '" height="50" width="400" frameborder="0"></iframe>';
                        tr_id.innerHTML = html;

                    }
                }
        return true;
    },


    checkIframeLoading: function () {
        var date = new Date();
        if (date - op_timeout > op_maxtimeout) op_semafor = true;
        if (op_semafor == true) {
            return Onepage.endValidation(true);
        }
        if (window.attachEvent && !window.addEventListener) {
            return Onepage.endValidation(true);
            //return true;
        }
        else
            window.setTimeout('Onepage.checkIframeLoading()', 300);
        return true;
    },

// sets style.display to block for id
// and hide id2, id3, id4... etc...
    op_unhide: function (id) {
        var x = Onepage.getElementById(id);
        if (x != null) {
            if (x.style != null)
                if (x.style.display != null)
                    x.style.display = 'block';
        }

        for (var i = 1; i < arguments.length; i++) {

            id2 = arguments[i];
            if (id2 != null) {
                x = Onepage.getElementById(id2);
                if (x != null) {
                    if (x.style != null)
                        if (x.style.display != null)
                            x.style.display = 'none';
                }
            }


        }

        // if we use it in a href we don't want to click on it, just unhide stuff

        return false;
    },
// will unhide the first two
    op_unhide2: function (id, id2) {
        var x = Onepage.getElementById(id);
        if (x != null) {
            if (x.style != null)
                if (x.style.display != null)
                    x.style.display = 'block';
        }
        var x = Onepage.getElementById(id2);
        if (x != null) {
            if (x.style != null)
                if (x.style.display != null)
                    x.style.display = 'block';
        }

        for (var i = 2; i < arguments.length; i++) {

            var id2 = arguments[i];

            if (id2 != null) {
                x = Onepage.getElementById(id2);
                if (x != null) {
                    if (x.style != null)
                        if (x.style.display != null)
                            x.style.display = 'none';
                }
            }


        }

        // if we use it in a href we don't want to click on it, just unhide stuff

        return false;
    },


    inValidate: function (el) {
        el.className = el.className += ' invalid';
        if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
            if (typeof el.name != 'undefined')
                if (el.name != null)
                    Onepage.op_log(el.name);
        }
    },
    makedValidated: function (el) {
        el.className = el.className.split('invalid').join('');
    },

    checkEmpty: function (el) {
        if (el.disabled) return true;
        if (el.type == 'radio') {
            var col = document.getElementsByName(el.name);
            for (var i = 0; i < col.length; i++) {
                if (typeof col[i].checked != 'undefined')
                    if (col[i].checked) return false;
                if (typeof col[i].checked != 'undefined')
                    if (col[i].selected) return false;
            }
            return true;
        }

        if (el.name.indexOf('virtuemart_state') >= 0) return false;

        if (el.type == 'checkbox') {
            if (el.checked) return false;
            return true;
        }


        if (typeof el.value != 'undefined')
            if (el.value != null) {
                if (el.value == '') return true;
                placeholder = Onepage.getAttr(el, 'placeholder');
                if (placeholder != null)
                    if (el.value == placeholder) return true;
            }
        if (typeof el.options != 'undefined')
            if (typeof el.selectedIndex != 'undefined') {
                if (el.options.length <= 1) return false;
                if (el.selectedIndex < 0) return true;
                if (el.options[el.selectedIndex] == '') return true;
            }
        return false;
    },

    fastValidation: function (type, fields, valid, invalidf, setInvalid, ignoreClass) {
        if (typeof setInvalid == 'undefined') setInvalid = true;
        if (typeof ignoreClass == 'undefined') ignoreClass = false;
        if (!setInvalid) {
            var z = 1;
        }

        if (!(fields != null)) fields = op_userfields;
        if (type != null) {
            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                Onepage.op_log('entering validation...');

            for (var i = 0; i < fields.length; i++) {
                // filter only needed
                //if (((op_userfields[i].indexOf('shipto_')!=0) && (!isSh))  || (op_userfields[i].indexOf('shipto_')==0) && (isSh))

                // special case, shiping fields are not validated by opc:
                if ((type == '') && (fields[i].indexOf('shipto_') >= 0)) continue;

                if ((fields[i].indexOf(type) == 0) || (type == '')) var cF = fields[i];
                else var cF = type + fields[i];


                {

                    var elopc = document.getElementsByName(cF);

                    if (elopc != null)
                        for (var j = 0; j < elopc.length; j++)
                            if (((elopc[j].className.indexOf('required') >= 0) && ((elopc[j].className.indexOf('notrequired') < 0))) || (ignoreClass === true)) {
                                // ie9 bug:
                                if (elopc[j].name != cF) continue;
                                if (elopc[j].name == 'name') continue;
                                if (elopc[j].name == 'username') continue;
                                if (elopc[j].name == 'password1') continue;
                                if (elopc[j].name == 'password2') continue;
                                if (elopc[j].name == 'email') continue;

                                if (!Onepage.doValidityElement(elopc[j], elopc[j].name)) {
                                    if (setInvalid) {
                                        Onepage.inValidate(elopc[j]);
                                    }

                                    invalidf.push(elopc[j].name);
                                    valid = false;
                                }

                                switch (elopc[j].type) {
                                    case 'password':
                                        break;
                                    case 'text':
                                        if (Onepage.checkEmpty(elopc[j])) {
                                            if (setInvalid) {
                                                Onepage.inValidate(elopc[j]);
                                            }

                                            invalidf.push(elopc[j].name);
                                            valid = false;
                                        }
                                        else {
                                            if (setInvalid)
                                                Onepage.makedValidated(elopc[j]);
                                        }
                                        break;
                                    case 'select-one':

                                        if (Onepage.checkEmpty(elopc[j])) {
                                            if (setInvalid) {
                                                Onepage.inValidate(elopc[j]);
                                            }
                                            invalidf.push(elopc[j].name);
                                            valid = false;
                                        }
                                        else {
                                            if (setInvalid) {
                                                Onepage.makedValidated(elopc[j]);
                                            }
                                        }
                                        break;

                                    case 'radio':
                                        if (Onepage.checkEmpty(elopc[j])) {
                                            if (setInvalid) {
                                                Onepage.inValidate(elopc[j]);
                                            }
                                            invalidf.push(elopc[j].name);
                                            valid = false;
                                        }
                                        else {
                                            if (setInvalid)
                                                Onepage.makedValidated(elopc[j]);
                                        }
                                        break;

                                    case 'hidden':

                                        break;

                                    default:
                                        if (Onepage.checkEmpty(elopc[j])) {
                                            if (setInvalid) {
                                                Onepage.inValidate(elopc[j]);
                                            }
                                            invalidf.push(elopc[j].name);
                                            valid = false;
                                        }
                                        else {
                                            if (setInvalid)
                                                Onepage.makedValidated(elopc[j]);
                                        }
                                        break;
                                }
                                Onepage.op_log('Validating: ' + elopc[j].name + ': ' + valid.toString());

                            }

                    var elopc = document.getElementsByName(op_userfields[i] + '[]');

                    var localtest = false;
                    var sum = 0;
                    if (elopc != null)
                        if (elopc.length > 0) {
                            for (var j = 0; j < elopc.length; j++)
                                if (((elopc[j].className.indexOf('required') >= 0) && ((elopc[j].className.indexOf('notrequired') < 0))) || (ignoreClass === true)) {
                                    // at least one from array must be selected

                                    if (!Onepage.checkEmpty(elopc[j])) {
                                        sum++;


                                    }

                                }

                            if (elopc != null)
                                for (var j = 0; j < elopc.length; j++)
                                    if (((elopc[j].className.indexOf('required') >= 0) && ((elopc[j].className.indexOf('notrequired') < 0))) || (ignoreClass === true))
                                        if (sum == 0) {
                                            var divd = Onepage.getElementById(cF + '_div');
                                            if (setInvalid) {
                                                if (divd != null) {
                                                    Onepage.inValidate(divd);
                                                }
                                                Onepage.inValidate(elopc[j]);
                                            }
                                            invalidf.push(elopc[j].name);
                                            valid = false;
                                        }
                                        else {
                                            var divd = Onepage.getElementById(cF + '_div');
                                            if (setInvalid) {
                                                if (divd != null) {
                                                    Onepage.makedValidated(divd);
                                                }

                                                Onepage.makedValidated(elopc[j]);
                                            }
                                        }
                        }

                }
            }
        }
        return invalidf;
        /*
  if (valid != true)
  return valid;
  */
    },


    processPlugins: function (data) {

        if (data.length == 0) return;
        for (var i = 0; i < data.length; i++) {
            var id = data[i].id;

            if (typeof data[i].data == 'undefined') data[i].data = '';
            if (!(data[i].data != null)) data[i].data = '';

            if (typeof data[i].where == 'undefined') data[i].where = id;
            if (!(data[i].where != null)) data[i].where = id;


            var html = data[i].data;
            if (html == '') {

                var d = Onepage.getElementById(id);
                if (d != null) {
                    d.style.display = 'none';
                }

            }
            else {
                var d = Onepage.getElementById(id);

                if (d != null) {

                    d.style.display = 'block';

                    d2 = Onepage.getElementById(data[i].where);
                    d2.innerHTML = html;
                }

            }

        }


    },

    clickSemafor: false,
    op_openlink: function (el) {
        if (typeof jQuery == 'undefined') {
            window.open(el.href, '', 'scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
            return false;
        }
        // should be binded with JHTMLOPC::_('behaviour.modal');
        if (el.className.indexOf('modal') >= 0) return false;
        // check for legacy:
        if (typeof SqueezeBox != 'undefined')
            if (typeof $ != 'undefined')
                if (typeof document.id != 'undefined') {
                    // if we got mootools, do nothing...
                    if (el.className.indexOf('modal') >= 0) return false;
                    var options = {};
                    var rel = Onepage.getAttr(el, 'rel');
                    if (rel != null) {
                        try {
                            var str = ' options = ' + rel + ';';
                            eval(str);
                        }
                        catch (e) {
                        }

                        options.parse = 'rel';
                    }
                    else {
                        el.setAttribute('rel', '');
                        options.parse = '';
                    }
                    if (typeof options.size == 'undefined') {
                        options.size = {};
                        options.size.x = 500;
                        options.size.y = 400;
                    }
                    if (typeof options.handler == 'undefined') options.handler = 'iframe';

                    var me = document.id(el);
                    SqueezeBox.fromElement(me, options);
                    return false;
                }

        if (typeof el == 'undefined')
            if (!el) return false;
        if (Onepage.clickSemafor) {
            Onepage.clickSemafor = true;
            return false;
        }


        if ((typeof jQuery != 'undefined') && (typeof jQuery.fancybox != 'undefined')) {
            var e = jQuery(el);
            if (typeof e.fancybox == 'undefined') return false;

            if (typeof e.data != 'undefined')
                if (e.data('fancybox') === true) return false;

            if (typeof bindFancyBox != 'undefined') {
                bindFancyBox(e);
            }
            else {
                e.fancybox({
                    type: 'iframe',
                    href: e.attr('href')
                });
            }
            e.attr('rel', '');

            {

                Onepage.clickSemafor = true;
                e.trigger('click');


            }
        }
        else if ((typeof jQuery != 'undefined') && (typeof jQuery.facebox != 'undefined')) {
            var e = jQuery(el);
            if (typeof e.facebox == 'undefined') return false;

            if (typeof e.data != 'undefined')
                if (e.data('facebox') === true) return false;

            if (typeof bindFancyBox != 'undefined') {
                faceboxBinder(e);
            }
            else {
                e.facebox({
                    type: 'iframe',
                    href: e.attr('href'),
                    ajax: e.attr('href')
                });
            }
            e.attr('rel', '');

            {

                Onepage.clickSemafor = true;
                e.trigger('click');


            }
        }

        if (el.className.indexOf('modal') >= 0) return false;


        return false;
    },

    op_resizeIframe: function () {


        if ((typeof parent != 'undefined') && (parent != null)) {
            if (typeof parent.resizeIframe != 'undefined') {
                parent.resizeIframe(document.body.scrollHeight);
            }
        }
    },

    checkBoxProduct: function (el) {


        var cmd = 'update_product';
        var u = Onepage.updateCheckboxProducts();
        if (u === '') return false;
        cmd = cmd + u;

        if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
            Onepage.op_log(cmd);
        }

        Onepage.op_runSS(this, false, true, cmd);
        // if needed, it can stop the event:
        return false;


    },
    updateCheckboxProducts: function () {
        var e = document.getElementsByName('checkbox_products[]');
        var q = '';
        if (e != null)
            if (e.length == 0) {
                // other browser support:
                e = document.getElementsByName('checkbox_products');
            }


        if (e != null)
            if (e.length >= 0) {
                for (var i = 0; i < e.length; i++) {
                    // these are select drop downs
                    if ((e[i] != null) && (e[i].type == 'select-one') && (typeof e[i].options !== 'undefined')) {
                        var ind = e[i].selectedIndex;
                        var opt = e[i].options;
                        if (ind < 0) ind = 0;

                        var toShow = new Array();
                        var toHide = new Array();
                        for (var j = 0; j < e[i].options.length; j++) {

                            if (typeof opt[j].value == 'undefined') continue;
                            if (opt[j].value == '') continue;
                            if (opt[j].value == '0') continue;

                            q += '&new_virtuemart_product_id[' + opt[j].value + ']=' + opt[j].value;
                            if (ind === j) {
                                q += '&new_quantity[' + opt[j].value + ']=1';
                                toShow.push('checkbox_product_desc_' + opt[j].value);
                            }
                            else {
                                q += '&new_quantity[' + opt[j].value + ']=0';
                                toHide.push('checkbox_product_desc_' + opt[j].value);
                            }


                        }

                        Onepage.toggleFields(toShow, '', false, '');
                        Onepage.toggleFields(toHide, '', true, '');


                    }
                    else {


                        // these are checkboxes

                        q += '&new_virtuemart_product_id[' + e[i].value + ']=' + e[i].value;
                        if (e[i].checked)
                            q += '&new_quantity[' + e[i].value + ']=1';
                        else
                            q += '&new_quantity[' + e[i].value + ']=0';

                    }

                }
                return q;
            }


        return '';
    },
    addVmProduct: function (product_id) {
        var cmd = 'update_product&new_virtuemart_product_id=' + product_id + '&quantity=' + 1;

        if ((typeof opc_debug != 'undefined') && (opc_debug === true)) {
            Onepage.op_log(cmd);
        }
        Onepage.op_runSS(this, false, true, cmd);
        // if needed, it can stop the event:
        return false;
    },
    removeVmProduct: function (product_id) {
        var cmd = 'update_product&new_virtuemart_product_id=' + product_id + '&quantity=' + 0;

        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
            Onepage.op_log(cmd);

        Onepage.op_runSS(this, false, true, cmd);
        // if needed, it can stop the event:
        return false;
    },
    plusQuantity: function (el) {
        var qe = Onepage.getQuantityBox(el);

        if (typeof qe.options == 'undefined') {
            if (typeof qe.value != 'undefined') {
                var q = qe.value;
                q = parseFloat(q);
                if (!isNaN(q)) {
                    q = q + 1;
                    qe.value = q;

                    if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                        Onepage.op_log('new quantity: ' + q);

                    return Onepage.updateProduct(el, q);
                }
            }
        }
    },
    minusQuantity: function (el) {
        var qe = Onepage.getQuantityBox(el);
        if (typeof qe.options == 'undefined') {
            if (typeof qe.value != 'undefined') {
                var q = qe.value;
                q = parseFloat(q);
                if (!isNaN(q)) {
                    q = q - 1;
                    if (q <= 0) {
                        q = 0;
                        qe.value = q;
                        Onepage.deleteProduct(el);
                    }
                    else {
                        qe.value = q;
                        return Onepage.updateProduct(el, q);
                    }
                }
            }
        }
    },
    deleteQuantity: function (el) {
        var e = Onepage.getQuantityBox(el);
        if (e != null)
            if (typeof e.value != 'undefined') {
                e.value = 0;
            }
        Onepage.deleteProduct(el);
    },
    getQuantityBox: function (el) {
        //returns quantity text element associated with proper REL attribute

        var rel = Onepage.getAttr(el, 'rel');
        var ret = el;


        var hash = '';
        var cart_id = '';

        if (rel != null) {
            if (rel.toString().indexOf('|') >= 0) {
                var arr = rel.split('|');
                cart_id = arr[0];
                hash = arr[1];
            }
            else {
                cart_id = rel;
            }

            if (hash != '') {
                // element change:
                var d = Onepage.getElementById('quantity_for_' + hash);
                if (d != null) {
                    ret = d;
                    return ret;
                }
            }
        }
        return;
    },

    updateProductAttributes: function (query) {

        if (typeof jQuery != 'undefined') {


            if (typeof opc_basket_wrap_id != 'undefined') {
                var b = Onepage.getElementById(opc_basket_wrap_id);
            }
            else
                var b = Onepage.getElementById('opc_basket');

            if (b != null)
                Onepage.jQueryLoader(b, false);

        }


        if (query == '') return;


        var cmd = 'update_product' + query;

        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
            Onepage.op_log(cmd);

        Onepage.op_runSS(this, false, true, cmd);

        return false;
    },

    updateProduct: function (el, newQuantity) {


        if (typeof jQuery != 'undefined') {


            if (typeof opc_basket_wrap_id != 'undefined') {
                var b = Onepage.getElementById(opc_basket_wrap_id);
            }
            else
                var b = Onepage.getElementById('opc_basket');

            if (b != null)
                Onepage.jQueryLoader(b, false);

        }
        var rel = Onepage.getAttr(el, 'rel');


        var quantity = 0;

        var hash = '';
        var cart_id = '';

        if (rel != null) {
            if (rel.toString().indexOf('|') >= 0) {
                var arr = rel.split('|');
                cart_id = arr[0];
                hash = arr[1];
            }
            else {
                cart_id = rel;
            }

            if (hash != '') {
                // element change:
                var d = Onepage.getElementById('quantity_for_' + hash);
                if (d != null)
                    el = d;

            }
            if (typeof newQuantity == 'undefined') {
                if (typeof el.options != 'undefined')
                    if (typeof el.selectedIndex != 'undefined')
                        quantity = el.options[el.selectedIndex].value;

                if (quantity == 0)
                    if (typeof el.value != 'undefined') {
                        quantity = el.value;
                    }
            }
            else {
                quantity = newQuantity;
            }
        }

        /* example of an ajax update input for
  \components\com_onepage\themes\icetheme_thestore_custom\overrides\update_form_ajax.tpl.php

  <input id="quantity_for_<?php echo md5($product->cart_item_id); ?>" value="<?php echo $product->quantity; ?>" type="text" onchange="Onepage.qChange(this);" name="quantity" rel="<?php echo $product->cart_item_id; ?>" id="stepper1" class="quantity" min="0" max="999999" size="2" data-role="none" />

  */
        if (cart_id == '') return;

        cart_id = Onepage.op_escape(cart_id);
        var cmd = 'update_product&cart_virtuemart_product_id=' + cart_id + '&quantity=' + quantity;

        if ((typeof opc_debug != 'undefined') && (opc_debug === true))
            Onepage.op_log(cmd);

        Onepage.op_runSS(this, false, true, cmd);

        return false;

    },


    setCouponAjax: function (el) {
        var coupon_code = Onepage.getCouponCode();
        if (coupon_code != '') {
            var code = Onepage.op_escape(coupon_code);

            Onepage.op_runSS(el, false, true, 'process_coupon&new_coupon=' + code);
        }
        return false;
    },


    deleteProduct: function (el) {

        return Onepage.updateProduct(el, 0);

    },
    op_login: function () {

        if (document.adminForm.username != null) {

            if (typeof document.adminForm.username_login != 'undefined') {
                document.adminForm.username.value = document.adminForm.username_login.value;
                uname = document.adminForm.username_login.value;
            }
            else {
                var d = Onepage.getElementById('username_login');
                if (d != null) {
                    document.adminForm.username.value = d.value;
                    uname = d.value;
                }
            }

        }
        else {
            var usern = document.createElement('input');
            usern.setAttribute('type', 'hidden');
            usern.setAttribute('name', 'username');
            usern.setAttribute('value', Onepage.getElementById('username_login').value);
            uname = Onepage.getElementById('username_login').value;
            document.adminForm.appendChild(usern);
        }

        pwde = Onepage.getElementById('passwd_login');
        if (pwde != null)
            pwd = pwde.value;
        else {
            if (typeof document.adminForm.password != 'undefined') {
                pwd = document.adminForm.password.value;
            }
            else if (typeof document.adminForm.passwd != 'undefined') {
                pwd = document.adminForm.passwd.value;
            }
            else
                pwd = '';
        }
        if ((pwd.split(' ').join() == '') || (uname.split(' ').join() == '')) {

            var sMsg = '';
            if (typeof op_userfields_named['password'] != 'undefined') {
                sMsg += ': ' + op_userfields_named['password'];
            }

            alert(op_general_error + sMsg);
            Onepage.ga(op_general_error + sMsg, 'Checkout Error');
            return false;
        }
        Onepage.getElementById('opc_option').value = op_com_user;
        //document.adminForm.task.value = op_com_user_task;
        Onepage.getElementById('opc_task').value = op_com_user_task;

        document.adminForm.action = op_com_user_action;
        document.adminForm.controller.value = 'user';
        document.adminForm.view.value = '';

        document.adminForm.submit();
        return false;
    },
    submitenter: function (el, e) {
        var charCode;

        if (e && e.which) {
            charCode = e.which;
        } else if (window.event) {
            e = window.event;
            charCode = e.keyCode;
        }


        if (charCode == 13) {
            Onepage.op_login();
            return false;
        }
        else
            return true;
    },


    op_showEditST2: function () {
        // edit_address_list_st_section
        // edit_address_st_section
        d1 = Onepage.getElementById('edit_address_list_st_section');
        if (d1 != null)
            d1.style.display = 'none';

        d2 = Onepage.getElementById('edit_address_st_section');
        if (d2 != null)
            d2.style.display = 'block';

        return false;
    },

    op_showEditST: function (id) {
        var d = Onepage.getElementById('opc_st_' + id);
        if (d != null)
            d.style.display = 'none';

        var els = document.getElementsByName('st_complete_list');
        for (var i = 0; i < els.length; i++) {
            var lid = els[i].value;
            if (lid == id) continue;
            d = Onepage.getElementById('opc_stedit_' + lid);
            if (d != null)
                d.style.display = 'none';
        }
        d = Onepage.getElementById('opc_stedit_' + id);
        if (d != null)
            d.style.display = 'block';

        d = Onepage.getElementById('opc_st_changed_' + id);
        if (d != null)
            if (d.value != null) {
                if (d.value != '1') {
                    var b_ide = Onepage.getElementById('ship_to_info_id_bt');
                    if (b_ide != null) {
                        Onepage.address_changed += '&opc_st_changed_' + id + '=1' + '&ship_to_info_id_bt=' + b_ide.value;
                    }
                }
                d.value = '1';
            }

        return false;

    },
    getaddressedchanged: function () {

        var els = document.getElementsByName('st_complete_list');
        var lid = 0;
        if (els != null)
            for (var i = 0; i < els.length; i++) {
                if (typeof els[i].value === 'undefined') continue;
                lid = els[i].value;
                d = Onepage.getElementById('opc_st_changed_' + lid);
                if (d != null) {
                    if (d.value != 0) {
                        Onepage.address_changed += '&opc_st_changed_' + lid + '=1';
                    }
                }
            }

        var b_ide = Onepage.getElementById('ship_to_info_id_bt');
        if (b_ide != null) {
            var bid = b_ide.value;
            Onepage.address_changed += '&opc_st_changed_' + bid + '=1' + '&ship_to_info_id_bt=' + bid;
        }
    },
    clickShippingAddress: function () {
        // global var:
        opc_clickShippingAddress = false;
        var el = Onepage.getElementById('sachone');
        if (typeof jQuery != 'undefined') {
            jQuery(el).click();
            return true;
        }
        if (typeof op_logged_in != 'undefined')
            if (op_logged_in) {

            }

    },

    changeST: function (el) {
        if (el.options != null)
            if (el.selectedIndex != null) {
                var user_info_id = el.options[el.selectedIndex].value;
                var d = Onepage.getElementById('hidden_st_' + user_info_id);
                var changed = Onepage.getElementById('opc_st_changed_' + user_info_id);
                var bt = Onepage.getElementById('ship_to_info_id_bt');
                var sa = Onepage.getElementById('sachone');
                if (bt != null)
                    if (bt.value == user_info_id) {
                        // the selected ship to is bt address

                        sa.value = '';
                        sa.setAttribute('checked', false);
                        eval('sa.checked=false');


                    }
                    else {
                        sa.value = 'adresaina';
                        sa.setAttribute('checked', true);
                        eval('sa.checked=true');
                    }


                if (d != null) {
                    var d2 = Onepage.getElementById('edit_address_list_st_section');
                    html = d.innerHTML;
                    html = html.split('REPLACE' + user_info_id + 'REPLACE').join('');
                    //d2.innerHTML = html;
                    Onepage.setInnerHtml(d2, html);

                    if (changed.value == 1) {
                        // Onepage.op_showEditST(user_info_id);
                    }

                }
            }
        Onepage.op_runSS(el);
    },

    send_special_cmd: function (el, cmd) {

        Onepage.op_runSS(el, false, true, cmd);
        return false;
    },

    refreshPayment: function () {
        Onepage.op_runSS(null, false, false, 'runpay');
    },

    setKlarnaAddress: function (address, prefix, suffix) {
        if (typeof prefix === 'undefined') prefix = '';
        if (!(prefix != null)) prefix = '';

        if (typeof suffix === 'undefined') suffix = '_field';
        if (!(suffix != null)) suffix = '_field';


        if (address != null) {
            ;
            ;
        } else return;


        d = Onepage.getElementById(prefix + 'email' + suffix);
        if (d != null)
            if (address.email != null)
                if (address.email != '')
                //if (d.value == '')
                    d.value = address.email;

        d = Onepage.getElementById(prefix + 'phone_1' + suffix);
        if (d != null)
            if (address.telno != null)
                if (address.telno != '')
                //if (d.value == '')
                    d.value = address.telno;

        d = Onepage.getElementById(prefix + 'first_name' + suffix);
        if (d != null)
            if (address.fname != null)
                if (address.fname != '')
                //if (d.value == '')
                    d.value = address.fname;

        d = Onepage.getElementById(prefix + 'company_name' + suffix);
        if (d != null)
            if (address.company != null)
                if (address.company != '')
                //if (d.value == '')
                    d.value = address.company;

        d = Onepage.getElementById(prefix + 'last_name' + suffix);
        if (d != null)
            if (address.lname != null)
                if (address.lname != '')
                //if (d.value == '')
                    d.value = address.lname;

        d = Onepage.getElementById(prefix + 'zip' + suffix);
        if (d != null)
            if (address.zip != null)
                if (address.zip != '')
                //if (d.value == '')
                    d.value = address.zip;

        d = Onepage.getElementById(prefix + 'city' + suffix);
        if (d != null)
            if (address.city != null)
                if (address.city != '')
                //if (d.value == '')
                    d.value = address.city;

        d = Onepage.getElementById(prefix + 'address_1' + suffix);
        if (d != null)
            if (address.street != null)
                if (address.street != '')
                //if (d.value == '')
                {
                    d.value = address.street;
                    if (address.house_number != null)
                        if (address.house_number != '')
                            d.value += ' ' + address.house_number;

                    if (address.house_extension != null)
                        if (address.house_extension != '')
                            d.value += ' ' + address.house_extension;

                }

        if (typeof address.address_2 != 'undefined')
            if (address.address_2 != '') {
                d = Onepage.getElementById(prefix + 'address_2' + suffix);
                d.value = address.address_2;
            }


    },
// this function is used when you need to get rid of a javascript within opc's themes and you are using $html = str_replace('op_runSS', 'op_doNothing', $html);
// returns false so there is no form submission or action
// use op_doNothing2 to allow return action such as link redirect or similar
    op_doNothing: function () {
        return false;
    },
    op_doNothing2: function () {
        return true;
    },
    showFields: function (show, fields) {

        if (fields) {
            var d = null;
            var found = false;
            for (i = 0; i < fields.length; i++) {
                if (show) {
                    d = Onepage.getElementById(fields[i] + '_div');
                    if (d != null) {
                        found = true;
                        d.style.display = '';
                    }
                    d = Onepage.getElementById(fields[i] + '_input');
                    if (d != null)
                        d.style.display = '';

                    if (!found) {
                        // registration page, not opc:
                        var d = Onepage.getElementById(fields[i] + '_field');
                        if (d != null)
                            if (typeof d.parentNode != 'undefined') {
                                var p1 = d.parentNode;
                                if (p1 != null) {
                                    if (typeof p1.parentNode != 'undefined')
                                        if (p1.parentNode != null) {
                                            var p2 = p1.parentNode;
                                            if (p2 != null) {
                                                p2.style.display = '';
                                            }
                                        }
                                }
                            }
                    }

                } else {
                    d = Onepage.getElementById(fields[i] + '_div');

                    if (d != null) {
                        found = true;
                        d.style.display = 'none';
                    }
                    d = Onepage.getElementById(fields[i] + '_input');
                    if (d != null)
                        d.style.display = 'none';

                    if (!found) {
                        // registration page, not opc:
                        var d = Onepage.getElementById(fields[i] + '_field');
                        if (d != null)
                            if (typeof d.parentNode != 'undefined') {
                                var p1 = d.parentNode;
                                if (p1 != null) {
                                    if (typeof p1.parentNode != 'undefined')
                                        if (p1.parentNode != null) {
                                            var p2 = p1.parentNode;
                                            if (p2 != null) {
                                                p2.style.display = 'none';
                                            }
                                        }
                                }
                            }
                    }
                }
            }
        }
        return true;
    },
    ga: function (msg, cat) {
        if (typeof ga != 'undefined')
            if (typeof ga === 'function') {
                if (typeof cat == 'undefined')
                    if (!(cat != null)) cat = 'OPC Event';
                //ga('send', 'event', 'button', msg, cat);
                ga('send', {
                    hitType: 'event',
                    eventCategory: cat,
                    eventAction: 'button',
                    eventLabel: msg
                });

            }

        if (typeof onOpcErrorMessage != 'undefined')
            if (onOpcErrorMessage != null && onOpcErrorMessage.length > 0) {
                for (var x = 0; x < onOpcErrorMessage.length; x++) {
                    if (typeof onOpcErrorMessage[x] == "function") {
                        onOpcErrorMessage[x](msg, cat);
                    }
                    else {
                        eval(onOpcErrorMessage[x]);
                    }
                }
            }


    },
    getPaymentElement: function () {
        // get active shipping rate
        var e = document.getElementsByName("virtuemart_paymentmethod_id");
        if (!(e != null)) return;

        //var e = document.getElementsByName("payment_method_id");


        var svalue = "";


        if (e.type == 'select-one') {
            ind = e.selectedIndex;
            if (ind < 0) ind = 0;
            value = e.options[ind].value;
            return e.options[ind];
        }


        if ((typeof e.checked != 'undefined') && (e.checked)) {
            return e;
        }
        else {

            for (i = 0; i < e.length; i++) {
                if (e[i].type == 'select-one') {
                    if (e[i].options.length <= 0) return "";
                    ind = e[i].selectedIndex;
                    if (ind < 0) ind = 0;
                    value = e[i].options[ind].value;
                    return e[i].options[ind];
                }

                if (e[i].checked == true)
                    return e[i];
            }
        }


        // last resort for hidden and not empty values of payment methods:
        for (i = 0; i < e.length; i++) {
            if (e[i].value != '') {
                if (e[i].id != null && (e[i].id != 'payment_method_id_coupon'))
                    return e[i];
            }
        }

        return null

    },
    setOpcId: function () {
        el = Onepage.getPaymentElement();
        if (!(el != null)) return;

        d = Onepage.getElementById('opc_payment_method_id');

        atr = Onepage.getAttr(el, 'id');
        if ((atr != null) && (atr != "") && (atr != 0)) {

            if (d != null)
                d.value = atr;
            return;
        }
        if (d != null)
            d.value = '';
        return;

    },
    username_check_return: function (exists) {
        op_user_name_checked = true;
        d = Onepage.getElementById('username_already_exists');
        if (d != null)
            if (exists) {
                if (opc_no_duplicit_username) last_username_check = false;
                d.style.display = 'block';
            }
            else {
                if (opc_no_duplicit_username) last_username_check = true;
                d.style.display = 'none';
            }
    },
    email_check_return: function (exists) {
        op_email_checked = true;
        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) {
            return;
        }

        d = Onepage.getElementById('email_already_exists');
        if (d != null)
            if ((exists === true) || (exists === 'true')) {
                if (opc_no_duplicit_email) last_email_check = false;
                d.style.display = 'block';
            }
            else {
                if (opc_no_duplicit_email) last_email_check = true;
                d.style.display = 'none';
            }

    },
    username_check: function (el) {
        //username_already_exists
        Onepage.op_runSS(el, false, true, 'checkusername');
        return true;
    },
    email_check: function (el) {
        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) return true;
        //email_already_exists
        Onepage.op_runSS(el, false, true, 'checkemail');
        return true;
    },

    getAttr: function (ele, attr) {
        if (typeof ele == 'undefined') return '';
        var result = (ele.getAttribute && ele.getAttribute(attr)) || null;
        if (!result) {
            var attrs = ele.attributes;
            var length = attrs.length;
            for (var i = 0; i < length; i++)
                if (attrs[i].nodeName === attr)
                    result = attrs[i].nodeValue;
        }
        return result;
    },


    qChange: function (el) {
        return Onepage.updateProduct(el);
    },

    syncEmails: function (el) {

        if ((typeof opc_disable_customer_email !== 'undefined') && (opc_disable_customer_email === true)) return;

        var email = el.value;
        var d = Onepage.getElementById('email_field');
        if (d != null) {
            d.value = email;
        }
    },


    addSpecialProduct: function (el) {
        // NOT IMLEMENTED YET !

        // if checked, let's add the product to the cart:
        if (el.checked) {
            if (typeof jQuery != 'undefined') {
                // add your virtuemart_product_id here:
                var product_id = 1;
                // add your directory path here:
                var my_site = '/';
                // dynamically calculated:
                var url = 'index.php?option=com_virtuemart&view=cart&lang=en&add_id[]=' + product_id + '&qadd_' + product_id + '=1';
                var ret = jQuery.ajax({
                    type: "GET",
                    url: url,
                    data: query,
                    cache: false,
                    async: false,
                    complete: function () {
                        // this will refresh todals and on german themes also the basket contents:
                        Onepage.op_runSS(null, false, true);
                    }


                });
            }
        }
        else {

            Onepage.updateProduct(el, 0);
        }

    },
    toggleRequired: function (fields, isNotRequired, prefix, suffix) {
        for (var i = 0; i < fields.length; i++) {
            var d = Onepage.getElementById(prefix + fields[i] + suffix);
            if (d != null) {
                if (!isNotRequired) {
                    if (d.className.indexOf('notrequired') > -1)
                        d.className = d.className.split('notrequired').join(' required ');
                    else d.className += ' required ';
                }
                else {
                    d.className = d.className.split('notrequired').join('').split('required').join('');
                    d.className += ' notrequired';
                }

            }
        }
    },
    checkBinders: function () {
        try {
            var eq = jQuery('#adminForm [onblur],#adminForm [onchange],#adminForm [onclick]');
            if (eq.length > 0) {
                eq.each(function () {
                    var what = this;
                    var ew = jQuery(what);
                    if (typeof what.onblur != 'undefined' && what.onblur != null) {
                        var z = what.onblur.toString();
                        var xj = ew.attr('onblur');
                        if (typeof xj != 'undefined') {
                            xj = xj.split('javascript:').join('');

                            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                                Onepage.op_log('Re-attaching event: ' + xj);

                            if (z.indexOf(xj) < 0) {
                                ew.blur(function () {

                                    try {
                                        eval(xj);
                                    } catch (e) {
                                        ;
                                    }
                                });
                            }
                        }

                    }

                    if (typeof what.onchange != 'undefined' && what.onchange != null) {
                        var z = what.onchange.toString();
                        var xj = ew.attr('onchange');
                        if (typeof xj != 'undefined') {
                            xj = xj.split('javascript:').join('');

                            if ((typeof opc_debug != 'undefined') && (opc_debug === true))
                                Onepage.op_log('Re-attaching event: ' + xj);

                            if (z.indexOf(xj) < 0) {
                                ew.blur(function () {

                                    try {
                                        eval(xj);
                                    } catch (e) {
                                        ;
                                    }
                                });
                            }
                        }

                    }

                    if (typeof what.onclick != 'undefined' && what.onclick != null) {
                        var z = what.onclick.toString();
                        var xj = ew.attr('onclick');
                        if (typeof xj != 'undefined') {
                            xj = xj.split('javascript:').join('');
                            Onepage.op_log('Re-attaching event: ' + xj);
                            if (z.indexOf(xj) < 0) {
                                ew.blur(function () {

                                    try {
                                        eval(xj);
                                    } catch (e) {
                                        ;
                                    }
                                });
                            }
                        }

                    }


                });
            }
        }
        catch (e) {

        }

        Onepage.assignValidity('', op_userfields);
        Onepage.assignValidity('shipto_', op_userfields);
        Onepage.assignRefresh();


    },
    assignRefresh: function () {
        if (typeof jQuery === 'undefined') return;
        var d = Onepage.getElementById('edit_address_list_st_section');
        if (d != null) {
            var el = jQuery(d);
            el.on('refresh', function () {
                Onepage.assignValidity('shipto_', op_userfields);
            });
        }
    },
    doValidityElement: function (domE, fname) {
        if (typeof domE.checkValidity == 'undefined') return true;
        if (domE.checkValidity && !domE.checkValidity()) {

            if ((typeof domE.value !== 'undefined') && (domE.value == '')) return true;
            //this part of the code is not to validate empty values
            if (domE.validity.valueMissing) return true;

            if (typeof domE.className != 'undefined')
                domE.className += ' invalid';
            var dx = Onepage.getElementById(fname + '_erromsg');
            if (!dx) {
                var msg = domE.getAttribute('onerrormsg', '');
                if (msg) {
                    var html = '<span class="email_already_exist" style="display: block; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0px; margin: 0px;" id="' + fname + '_erromsg">' + msg + '</span>';
                    jQuery(html).insertAfter(domE);


                }

            }
            return false;
        }
        else {
            if (typeof domE.className != 'undefined') {
                domE.className = domE.className.toString().split('invalid').join('');
            }


            var ee = '#' + fname + '_erromsg';
            var et = jQuery(ee);
            if (et.length > 0) et.remove();

            return true;
        }
        //for non-html5 browsers
        return true;
    },

    assignValidity: function (prefix, op_userfields) {
        if (typeof jQuery !== 'undefined')
            for (var i = 0; i < op_userfields.length; i++) {

                var selector = '#' + prefix + op_userfields[i] + '_field';
                var fname = prefix + op_userfields[i];
                var selectedel = jQuery(selector);
                if (selectedel.length > 0) {
                    selectedel.each(function () {
                        var domE = this;
                        var el2 = jQuery(domE);
                        if (typeof domE.checkValidity !== 'undefined')
                            el2.change(function () {

                                Onepage.doValidityElement(this, this.name);

                            });

                        Onepage.doValidityElement(domE, fname);


                    })
                }
            }
    },
    last_payment_extra: '',
    address_changed: '',
    last_dymamic: new Array(),
    errorMsgTimer: null

}


/* support for async loading */
if (typeof jQuery != 'undefined' && (jQuery != null)) {
    jQuery(document).ready(function () {

        if (typeof Onepage.op_runSS == 'undefined') return;
        Onepage.op_runSS('init');

        window.setTimeout(function () {
            Onepage.checkBinders();
        }, 2000);
        //reattach all events:


    });

}
else {
    if ((typeof window != 'undefined') && (typeof window.addEvent != 'undefined')) {
        window.addEvent('domready', function () {

            Onepage.op_runSS('init');
            window.setTimeout(function () {
                Onepage.checkBinders();
            }, 2000);
        });
    }
    else {
        if (window.addEventListener) { // Mozilla, Netscape, Firefox
            window.addEventListener("load", function () {
                Onepage.op_runSS('init', false, true, null);
                window.setTimeout(function () {
                    Onepage.checkBinders();
                }, 2000);

            }, false);
        } else { // IE
            window.attachEvent("onload", function () {
                Onepage.op_runSS('init', false, true, null);
                window.setTimeout(function () {
                    Onepage.checkBinders();
                }, 2000);
            });
        }
    }
}

//set basic variables:
if (typeof op_logged_in == 'undefined') op_logged_in = false;
if (typeof op_userfields == 'undefined') op_userfields = []; 



