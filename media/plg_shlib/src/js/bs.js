/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2015
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.487
 * @date         2016-03-15
 */

/*! Copyright Weeblr llc @_YEAR_@ - Licence: http://www.gnu.org/copyleft/gpl.html GNU/GPL */

var shlBootstrap = function (e) {
    var t = {
        updateBootstrap: function () {
            e("*[rel=tooltip]").tooltip(), e("select").chosen({
                disable_search_threshold: 10,
                allow_single_deselect: !0
            }), e(".radio.btn-group label").addClass("btn"), e(".btn-group label:not(.active)").click(function () {
                var t = e(this), r = e("#" + t.attr("for"));
                r.prop("checked") || (t.closest(".btn-group").find("label").removeClass("active btn-success btn-danger btn-primary"), "" == r.val() ? t.addClass("active btn-primary") : 0 == r.val() ? t.addClass("active btn-danger") : t.addClass("active btn-success"), r.prop("checked", !0))
            }), e(".btn-group input[checked=checked]").each(function () {
                "" == e(this).val() ? e("label[for=" + e(this).attr("id") + "]").addClass("active btn-primary") : 0 == e(this).val() ? e("label[for=" + e(this).attr("id") + "]").addClass("active btn-danger") : e("label[for=" + e(this).attr("id") + "]").addClass("active btn-success")
            })
        },
        canOpenModal: !0,
        modals: {},
        modalTemplate: "<div class='shmodal hide ' id='{%selector%}'><div class='shmodal-header'><button type='button' class='close' data-dismiss='modal'>×</button>{%title%}</div><div id='{%selector%}-container'></div></div>",
        selectedIdsUrl: "",
        setSelectedIdsUrl: function (e) {
            shlBootstrap.selectedIdsUrl = e
        },
        getModalUrl: function (e) {
            return e + shlBootstrap.selectedIdsUrl
        },
        closeModal: function () {
            var t = e("div.shmodal-header button.close");
            t.click()
        },
        setModalTitleFromModal: function (e) {
            var t = window.parent.jQuery("div.shmodal-header:visible");
            t.html("<h3>" + e + "</h3>")
        },
        registerModal: function (t) {
            var r = {};
            r = e.extend({
                selector: "",
                title: "",
                url: "",
                width: .5,
                height: .5,
                onclose: "",
                footer: "",
                backdrop: !0,
                keyboard: !1
            }, t), shlBootstrap.modals[r.selector] = r
        },
        renderModal: function (t, r) {
            e(shlBootstrap.modalTemplate.replace(new RegExp("{%selector%}", "g"), r.selector).replace("{%title%}", r.title ? "<h3>" + r.title + "</h3>" : "&nbsp;")).appendTo("#shl-modals-container"), e("#" + r.selector).on("show", function () {
                if (!shlBootstrap.canOpenModal)return !1;
                var t = shlBootstrap.modals[this.id], r = t.width < 1 ? e(window).width() * t.width : t.width, a = t.height < 1 ? e(window).height() * t.height : t.height, o = shlBootstrap.getModalUrl(t.url), n = jQuery("#" + t.selector);
                jQuery("#" + t.selector + "-container").html('<div class="shmodal-body" style="height:' + a + "px; width:" + r + 'px;"><iframe class="iframe" src="' + o + '" height="' + a + '" width="' + r + '" ></iframe></div>' + t.footer);
                var l = n.height(), s = n.width(), i = jQuery(window).height(), d = jQuery(window).width(), c = (d - s) / 2, h = (i - l) / 2;
                jQuery("#" + t.selector).css({
                    "margin-top": h,
                    top: "0"
                }), jQuery("#" + t.selector).css({"margin-left": c, left: "0"})
            }), e("#" + r.selector).on("hide", function () {
                var e = shlBootstrap.modals[this.id];
                e.onclose && e.onclose(), jQuery("#" + this.id + "-container").innerHTML = ""
            }), e("#" + r.selector).modal({keyboard: r.keyboard, backdrop: r.backdrop, show: !1})
        },
        renderModals: function () {
            e.each(shlBootstrap.modals, shlBootstrap.renderModal)
        },
        inputCounters: {},
        registerInputCounter: function (t) {
            var r = {
                maxCharacterSize: -1,
                originalStyle: "badge-success",
                warningStyle: "badge-warning",
                errorStyle: "badge-important",
                warningNumber: 20,
                errorNumber: 40,
                displayFormat: "#left",
                style: "shl-char-counter",
                title: ""
            };
            t = e.extend(r, t), shlBootstrap.inputCounters[t.selector] = t
        },
        renderInputCounters: function () {
            e.each(shlBootstrap.inputCounters, shlBootstrap.renderInputCounter)
        },
        renderInputCounter: function (t, r) {
            e("#" + r.selector).textareaCount(r)
        },
        onReady: function () {
            e("<div id='shl-modals-container'></div>").appendTo("body"), shlBootstrap.renderModals(), shlBootstrap.renderInputCounters()
        }
    };
    return jQuery(document).ready(t.onReady), t
}(jQuery);
!function (e) {
    e.fn.textareaCount = function (t, r) {
        function a() {
            return v.html(o()), "undefined" != typeof r && r.call(this, l()), !0
        }

        function o() {
            var e = p.val(), r = e.length;
            if (t.maxCharacterSize > 0) {
                r >= t.maxCharacterSize && (e = e.substring(0, t.maxCharacterSize));
                var a = d(e), o = t.maxCharacterSize - a;
                if (i() || (o = t.maxCharacterSize), r > o) {
                    var l = this.scrollTop;
                    p.val(e.substring(0, o)), this.scrollTop = l
                }
                v.removeClass(t.warningStyle), v.removeClass(t.originalStyle), r > t.errorNumber ? v.addClass(t.errorStyle) : r > t.warningNumber ? v.addClass(t.warningStyle) : v.addClass(t.originalStyle), f = p.val().length + a, i() || (f = p.val().length), b = h(c(p.val())), g = t.errorNumber - f
            } else {
                var a = d(e);
                f = p.val().length + a, i() || (f = p.val().length), b = h(c(p.val()))
            }
            return n()
        }

        function n() {
            var e = t.displayFormat;
            return e = e.replace("#input", f), e = e.replace("#words", b), m > 0 && (e = e.replace("#max", m), e = e.replace("#left", g)), e
        }

        function l() {
            var e = {input: f, max: m, left: g, words: b};
            return e
        }

        function s(e) {
            return e.next(".charleft")
        }

        function i() {
            var e = navigator.appVersion;
            return -1 != e.toLowerCase().indexOf("win") ? !0 : !1
        }

        function d(e) {
            for (var t = 0, r = 0; r < e.length; r++)"\n" == e.charAt(r) && t++;
            return t
        }

        function c(e) {
            var t = e + " ", r = /^[^A-Za-z0-9]+/gi, a = t.replace(r, ""), o = rExp = /[^A-Za-z0-9]+/gi, n = a.replace(o, " "), l = n.split(" ");
            return l
        }

        function h(e) {
            var t = e.length - 1;
            return t
        }

        var u = {
            maxCharacterSize: -1,
            originalStyle: "originalTextareaInfo",
            warningStyle: "warningTextareaInfo",
            errorStyle: "errorTextareaInfo",
            warningNumber: 20,
            errorNumber: 40,
            displayFormat: "#input characters | #words words",
            title: ""
        }, t = e.extend(u, t), p = e(this);
        p.wrap("<div class='shl-char-counter-wrapper'></div>"), e("<div class='charleft badge " + t.style + "' " + (t.title ? "title='" + t.title + "'" : "") + ">&nbsp;</div>").insertAfter(p);
        var v = s(p);
        v.addClass(t.originalStyle);
        var f = 0, m = t.maxCharacterSize, g = 0, b = 0;
        p.bind("keyup", function (e) {
            a()
        }).bind("mouseover", function (e) {
            setTimeout(function () {
                a()
            }, 10)
        }).bind("paste", function (e) {
            setTimeout(function () {
                a()
            }, 10)
        }), a()
    }
}(jQuery);
