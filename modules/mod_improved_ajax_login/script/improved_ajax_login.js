;(function($, undefined) {

$.extend( $.createOOPlugin = function(plgName, extend, proto) {
  var Class = $.createOOPlugin.Class, Super = $.createOOPlugin.Super;
  if ($.fn[plgName]) return console.error(plgName+" plugin already exists!");
  if (Class[extend]) {  // extend class
    var sup = Class[Super[plgName] = extend].prototype;
    extend = $.extend(true, {_stack: {}}, sup);
    for (var prop in sup) if (typeof sup[prop] === "function")
      extend[prop] = new Function("return this.Super('"+prop+"', arguments)");
    extend.Super = function(fn, args) {
      var ret, stack = this._stack[fn] = this._stack[fn] || [],
          parent = Super[stack[stack.length-1] || this.plugin];
      stack.push(parent);
      if (Class[parent].prototype[fn])
        ret = Class[parent].prototype[fn].apply(this, args);
      else console.error(fn, " method not exists!");
      stack.pop();
      return ret;
    };
  }
  Class[plgName] = function() {this.Constructor.apply(this, arguments)};
  Class[plgName].prototype = $.extend(extend, proto, {plugin: plgName});
  $.fn[plgName] = function() {  // create plugin
    var args = arguments, arg0 = Array.prototype.shift.call(args) || {};
    return this.length == 1? iterator.call(this) : this.each(iterator);
    function iterator() {
      var $this = $(this), instance = $this.data(plgName);
      if (instance)  // call method
        if (instance[arg0] && instance[arg0].apply)
          return instance[arg0].apply(instance, args);
        else return console.error(arg0, " method not exists!");
      if (typeof arg0 === "object") {  // create instance
        arg0.id = plgName + $.createOOPlugin.Id++;
        arg0.$node = $this;
        var plg = plgName, obj = new Class[plg](arg0);
        do $this.data(plg, obj);
        while (plg = Super[plg]);
      }
      return $this;
    }
  };
}, {Class: {}, Super: {}, Id: 0});

$.fn.absolute = function() {
  var pos = this.offset();
  if ($().jquery.split(".")[1] < 10) {
    if ($.fn.absolute.bt === undefined) {
      var $body = $(document.body);
      $.fn.absolute.bt = parseInt($body.css("borderTopWidth")) || 0;
      $.fn.absolute.bl = parseInt($body.css("borderLeftWidth")) || 0;
    }
    pos.top += $.fn.absolute.bt;
    pos.left += $.fn.absolute.bl;
  }
  return pos;
};

if (window.___grecaptcha_cfg) {
  if (___grecaptcha_cfg.onload) {
    window[___grecaptcha_cfg.onload] = (function(parent) {
      return function() {
        parent.apply(this, arguments);
        $(___grecaptcha_cfg).trigger('load');
      };
    })(window[___grecaptcha_cfg.onload]);
  } else {
    window.ialInitReCaptcha2 = function() {
      $(___grecaptcha_cfg).trigger('load');
    };
    ___grecaptcha_cfg.onload = 'ialInitReCaptcha2';
  }
}

$.fn.ialReCaptcha = function(method) {
  var that = this;
  $(window.___grecaptcha_cfg).one('load.ial', function() {
    that.each(function() {
      if (method == 'reset') {
        grecaptcha.reset($(this).data('clientId'));
      } else {
        var clientId = grecaptcha.render(this, { sitekey: this.getAttribute('data-sitekey') });
        $(this).data('clientId', clientId);
      }
    });
  });
  window.grecaptcha && $(___grecaptcha_cfg).trigger('load.ial');
  return this;
};

$.createOOPlugin("ialCheckBox", {
  activeClass: "ial-active",

  Constructor: function(params) {
    $.extend(this, params);
    this.$box = $('<div class="ial-checkbox">').insertBefore(this.$node);
    if (this.$node.prop("checked")) this.$box.addClass(this.activeClass);
    var css = isIE && isIE[1] < 9?
      {position: "absolute", top: "-1000px"} : {display: "none"};
    this.$node.css(css)
      .on("change", $.proxy(this, "onChange"));
  },

  onChange: function(e) {
    if (window.JBackend && e.originalEvent) return;
    this.$box[this.$node.prop("checked")?
      "addClass" : "removeClass"](this.activeClass);
  }
});

$.createOOPlugin("ialMsg", {
  activeClass: "ial-active",
  margin: 10,
  timeout: 0,
  dur: 300,
  pos: "",  // "r" - right, "l" - left
  msg: "",

  create: function() {
    if (this.$msg) return;
    // fix for select field
    if (this.$node[0].tagName == "SELECT") this.$node = this.$node.parent();
    var abs = this.$node.absolute(),
        pos = this.pos ||
          this.$node.offset().left > $(window).width()/2 ? "l" : "r";
    this.$msg = $(
      '<div class="ial-msg '+this.ico+'">'+
        '<span class="ial-'+this.ico+'">'+
          '<div class="ial-arrow-'+pos+'" />'+
          '<div class="ial-icon-'+this.ico+'">&nbsp;</div> '+this.msg+
        '</span>'+
      '</div>')
      .appendTo(document.body)
      .addClass("ial-trans-gpu ial-trans-"+pos);
    var msgW = this.$msg.outerWidth();
    if (abs.left - msgW - this.margin < 0) {  // if there is no place
      this.$msg.find(".ial-arrow-r").attr("class", "ial-arrow-"+(pos = "b"));
      abs.top += this.$node.outerHeight() + this.margin;
    }
    if (pos == "r") abs.left -= msgW + this.margin;
    if (pos == "l") abs.left += this.$node.outerWidth() + this.margin;
    this.$msg.css(abs);
    $(window)
      .on("resize."+this.id, $.proxy(this, "hide"))
      .on("scroll."+this.id, $.proxy(this, "onScroll"));
  },

  onScroll: function() {
    var abs = this.$node.absolute();
    if (this.$msg.find(".ial-arrow-b").length) {
      abs.top += this.$node.outerHeight() + this.margin;
    }
    this.$msg.css("top", abs.top);
  },

  show: function() {
    if (this.timeout) this.timeout = clearTimeout(this.timeout);
    else this.create();
    this.$msg.addClass(this.activeClass);
  },

  hide: function() {
    if (this.timeout || !this.$msg) return;
    this.$msg.removeClass(this.activeClass);
    this.timeout = setTimeout($.proxy(this, "destroy"), this.dur);
  },

  destroy: function() {
    $(window).off("."+this.id);
    this.$msg.off("."+this.id).remove();
    delete this.$msg;
    delete this.timeout;
  }
});

$.createOOPlugin("ialInfoMsg", "ialMsg", {
  ico: "inf",

  Constructor: function(params) {
    $.extend(this, params);
    this.msg = this.$node.prop("title").replace(/([\-\.] )/, "$1<br />");
    this.$node
      .removeAttr("title")
      .on("focus."+this.id, $.proxy(this, "show"))
      .on("blur."+this.id, $.proxy(this, "hide"));
  }
});

$.createOOPlugin("ialErrorMsg", "ialMsg", {
  ico: "err",

  Constructor: function(params) {
    $.extend(this, params);
    this.$node.attr("data-"+this.plugin, "on");
    var msg = this.msg.split(":&nbsp;");  // remove ":" from "invalid field:"
    if (msg[1] === "") this.msg = msg[0];
    this.show();
    this.$node.on("focus."+this.id+" click."+this.id, $.proxy(this, "hide"));
    this.$msg.on("click."+this.id, $.proxy(this, "hide"));
  },

  destroy: function() {
    this.Super("destroy", arguments);
    this.$node
      .off("."+this.id)
      .removeAttr("data-"+this.plugin)
      .removeData(this.plugin);
  }
});

$.createOOPlugin("ialElem", {
  nodeClass: "gi-elem",
  validClass: "ial-correct",
  reqClass: "req",
  $input: undefined,
  $error: undefined,

  Constructor: function(params) {
    $.extend(this, params);
    this.$node.addClass(this.nodeClass);
    $(this.tmpl).appendTo(this.$node);
    this.init();
    this.$error = this.$node.find("[data-attr=error]");
    this.$input = this.$node.find("[name]").prop("id", this.id);
    this.$node.find("label").prop("for", this.id);
    if (ologin.showHint && this.$input.prop("title"))
      this.$input.ialInfoMsg();
  },

  init: function() {
    var name, obj;
    for (name in this.jfo) {
      obj = this.jfo[name];
      if (typeof obj === "object")
        this.setAttr(name, obj.checked || obj.value, obj.placeholder || "");
    }
  },

  setAttr: function(name, value, placeholder) {
    var attr = name.match(/_(.*)]/)[1],
        $node = this.$node.find("[data-attr*="+attr+"]");
    this.jfo[name][placeholder == "CHK"? "checked" : "value"] = value;
    switch (attr) {
      case "prefix":
        attr = "name";
        $node = this.$node.find("[name]");
        value = this.jfo["jform[elem_name]"].value;
      case "name":
        var prefix = this.jfo["jform[elem_prefix]"];
        if (prefix) prefix = prefix.value || prefix.placeholder;
        else if (this.jfo[name].prefix) // compatibility fix for old versions
          prefix = this.jfo[name].prefix.replace(/\[$/, "");
        if (prefix) value = prefix + "["+value+"]";
      case "value":
      case "title":
      case "placeholder":
        return $node.prop(attr, value || placeholder);
      case "class":
        if (this.jfo["jform[elem_wide]"].checked) value += " gi-wide";
        return this.$node.prop("class", this.nodeClass).addClass(value);
      case "subtitle":
      case "label":
        $node[(value || placeholder).match(/\S/)?
          "removeClass" : "addClass"]("hidden");
      case "error":
        return $node.html(value || placeholder);
      case "select":
        return value && $node.html(value);
      case "required":
        return $node[value? "addClass" : "removeClass"](this.reqClass);
      case "wide":
        this.$node[value? "addClass" : "removeClass"]("gi-"+attr);
        return $node;
      case "checked":
        return $node.attr("checked", value).trigger("change");
      case "article":
        var $article = $("input[id$=article_name]");
        if ($article.val())
          this.jfo["jform[elem_article_name]"].value = $article.val();
        if (value) $node.attr("href", ologin.base+"index.php?option=com_content&view=article&tmpl=component&id="+value);
        return $node.html(this.jfo["jform[elem_article_name]"].value
          || $article.attr("placeholder"));
    }
  }
});

$.createOOPlugin("ialHidden", "ialElem", {
  tmpl: '<input data-attr="name value" type="hidden" />'
});

$.createOOPlugin("ialTextfieldBase", "ialElem", {
  $load: undefined,

  Constructor: function(params) {
    this.Super("Constructor", arguments);
    if (this.jfo["jform[elem_type]"].defaultValue == 'hidden') {
      this.tmpl = '<input data-attr="name value" type="hidden" />';
    }
    if (this.jfo["jform[elem_autoCompOff]"])
      this.$input.attr("autocomplete", "off");
    if (this.jfo["jform[elem_pattern]"])
      this.$input.on("blur", $.proxy(this, "onBlur"));
    if (this.jfo["jform[elem_ajax]"])
      this.$load = $('<i class="ial-load" />')
        .insertBefore(this.$input)
        .ialLoad();
  },

  onBlur: function() {
    var value = this.$input.val();
    this.$input.removeClass(this.validClass);
    if (!value) return;
    var rege = new RegExp(this.jfo["jform[elem_pattern]"].value),
        result = rege.test(value);
    if (result && this.$load) {
      if (this.$load.ialLoad("playing")) return;
      this.$input
        .prop("disabled", true)
        .css("background", "none");
      this.$load.ialLoad("play");
      $.ajax({
        type: "POST",
        dataType: "json",
        url: ologin.base + "index.php",
        data: {
          ialCheck: this.$input.attr("name"),
          value: value,
          lang: (document.ialLogin || document.ialRegister).lang.value
        },
        success: $.proxy(this, "onLoadSuccess"),
        error: $.proxy(this, "onLoadError"),
        timeout: ologin.timeout,
        cache: false
      });
    } else this.onLoadSuccess({
      error: !result,
      msg: this.$error.html()
    });
  },

  onLoadSuccess: function(resp) {
    if (resp.error) {
      if (!this.$input.data("ialErrorMsg"))
        this.$input.ialErrorMsg({msg: resp.msg});
    } else this.$input.addClass(this.validClass);
    if (this.$load) {
      this.$load.ialLoad("stop");
      this.$input
        .prop("disabled", false)
        .removeAttr("style");
    }
  },

  onLoadError: function(error) {
    this.$load.ialLoad("stop");
    this.$input
      .prop("disabled", false)
      .removeAttr("style");
    console.log(error);
  }
});

$.createOOPlugin("ialPassword1Base", "ialElem", {
  min: 4,
  passCat: ['',
    'IAL_VERY_WEAK',
    'IAL_WEAK',
    'IAL_REASONABLE',
    'IAL_STRONG',
    'IAL_VERY_STRONG'],
  $strong: undefined,
  $sfs: undefined,

  Constructor: function(params) {
    this.Super("Constructor", arguments);
    this.$strong = this.$node.children(".passStrongness");
    this.$sfs = this.$node.find(".strongField");
    this.$input
      .on("keyup", $.proxy(this, "onKeyUp"))
      .on("blur", $.proxy(this, "onBlur"));
  },

  onKeyUp: function(e) {
    var pass = this.$input.val(),
        strong = 0;
    if (pass.length >= this.min) {
      strong++;
      if (pass.length >= 2*this.min) strong++;
      if (pass.match(/\d/)) strong++;
      if (pass.match(/[A-Z]/)) strong++;
      if (pass.match(/\W/)) strong++;
    }
    this.$strong.html(this.passCat[strong]
      && ialText[this.passCat[strong]].value);
    for (var i=0; i<this.$sfs.length; i++)
      $(this.$sfs[i])[strong > i? "removeClass" : "addClass"]("empty");
  },

  onBlur: function() {
    var value = this.$input.val();
    this.$input.removeClass(this.validClass);
    if (!value) return;
    if (value.length < this.min)
      this.$input.ialErrorMsg({msg: this.$error.html()});
    else this.$input.addClass(this.validClass);
  }
});

$.createOOPlugin("ialCaptchaBase", "ialElem", {
  url: location.protocol+"//www.google.com/recaptcha/api/",
  $img: undefined,
  $refresh: undefined,

  Constructor: function(params) {
    this.Super("Constructor", arguments);
    if (!ologin.captcha) alert("Captcha - ReCaptcha plugin:\n"+
      "Public / Private key isn't correct at the ReCaptcha plugin option.");
    if (ologin.captchaVer == "2.0") this.initCaptcha2();
    else this.initCaptcha();
  },

  initCaptcha: function() {
    this.$input.val("");
    this.$img = this.$node.find("img");
    this.$img.on("load", $.proxy(this.$img, "addClass", "fadeIn"));
    this.$refresh = this.$node.find(".loginBtn")
      .on("click", $.proxy(this, "reLoad"));
    $(".regBtn").one(ologin.openEvent == "onclick"? "click" : "mouseenter",
      $.proxy(this, "firstLoad")).length || this.firstLoad();
  },

  firstLoad: function() {
    Recaptcha = $.extend(window.Recaptcha, {
      finish_reload: $.proxy(this, "onLoad"),
      challenge_callback: $.noop
    });
    $.getScript(this.url+"challenge?ajax=1&k="+ologin.captcha+
      "&_="+new Date().getTime(), Recaptcha.finish_reload);
  },

  reLoad: function(e) {
    if (window.JBackend) return;
    this.$img.removeClass("fadeIn");
    $(this.$input.prop("form").elements["recaptcha_response_field"])
      [e? "focus" : "blur"]().val("");
    Recaptcha.noclick = true;
    $.getScript(this.url+"reload?type=image&k="+ologin.captcha+
      "&c="+RecaptchaState.challenge);
  },

  onLoad: function() {
    var c = arguments[0] || RecaptchaState.challenge;
    this.$input.val(c);
    this.$img.prop("src", this.url+"image?c="+c);
    Recaptcha.noclick = false;
  },

  initCaptcha2: function() {
    if (window.JBackend) {
      this.$node.html('<img class="grecaptcha-img" src="components/com_improved_ajax_login/assets/img/notrobot.png">');
    } else {
      this.$node.html('<div id="registerCaptcha" class="ial-recaptcha" data-sitekey="'+ologin.captcha+'"></div>');
      $(window).on('load', $.proxy(this, "onReady"));
    }
  },

  onReady: function() {
    $(".ial-captcha").remove();
  },
});

$.createOOPlugin("ialTosBase", "ialElem", {
  Constructor: function(params) {
    this.Super("Constructor", arguments);
    this.$link = this.$node.find("a");
    var art = this.jfo["jform[elem_article_name]"];
    if (art) this.$link.html(art.value || art.placeholder)
    if (window.JBackend) return this.$modal = $();
    this.$modal = this.$node.find(".ial-modal").ialWindow({
      $bg: ologin.$bg,
      popupCenter: true,
      border: parseInt(ologin.border)
    });
    this.$frame = this.$modal.find('.ial-frame');
    this.$link.on('click', $.proxy(this, 'open'));
    this.$modal.find('.ial-close').on('click', $.proxy(this, 'close'));
  },

  open: function() {
    if (this.$frame.contents()[0].location.href != this.$link.prop('href')) {
      this.$frame.prop('src', this.$link.prop('href'));
    }
    ologin.$bg.on('click.ialModal', $.proxy(this, 'close'));
  },

  close: function() {
    this.$node.closest('.ial-window').ialWindow('open');
    ologin.$bg.off('click.ialModal');
  }
});

$.createOOPlugin("ialConfirm", {
  validClass: "ial-correct",
  $orig: undefined,

  Constructor: function(params) {
    $.extend(this, params);
    var name = this.$node.prop("name"),
        elem = this.$node.prop("form").elements,
        m = name.match(/2|_confirm/)[0];
    this.$orig = $(elem[name.replace(m, "1")] || elem[name.replace(m, "")])
      .on("focus", $.proxy(this.$node, "removeClass", this.validClass))
      .on("blur", $.proxy(this, "onBlurOrig"));
    this.$node
      .on("focus", $.proxy(this, "onFocus"))
      .on("blur", $.proxy(this, "onBlur"));
    this.$error = this.$node.next();
  },

  onFocus: function() {
    if (!this.$orig.val()) this.$orig.focus();
    else this.$node.removeClass(this.validClass);
  },

  onBlur: function() {
    var nodeVal = this.$node.val();
    if (!nodeVal) return;
    if (nodeVal == this.$orig.val()) {
      if (this.$orig.hasClass(this.validClass))
        this.$node.addClass(this.validClass);
    } else this.$node.ialErrorMsg({msg: this.$error.html()});
  },

  onBlurOrig: function() {
    var nodeVal = this.$node.val(),
        origVal = this.$orig.val();
    if (!nodeVal || !origVal) return;
    if (nodeVal == origVal) {
      if (this.$orig.hasClass(this.validClass))
        this.$node.addClass(this.validClass);
    } else this.$node.ialErrorMsg({msg: this.$error.html()})
  }
});

$.createOOPlugin("ialSubmit", {
  validClass: "ial-correct",
  reloadDelay: 1000,
  $form: undefined,
  $load: undefined,

  Constructor: function(params) {
    $.extend(this, params);
    var form = this.$node.prop("form"), url = form.action;
    if (form.ctrl) url += (url.indexOf('?') < 0 ? '?' : '&') + 'ctrl=' + form.ctrl.value;
    if (form.task) url += (url.indexOf('?') < 0 ? '?' : '&') + 'task=' + form.task.value;
    this.$load = this.$node.find(".ial-load").ialLoad();
    this.$form = $(form).attr("action", url)
      .on("submit", $.proxy(this, "onSubmit"));
    if (isIE && isIE[1] < 8) {
      this.$node.wrapInner('<div class="loginBtn ial-submit">')
        .children().unwrap();
      $('.loginBtn', form).on("click", function() {form.submit()});
    }
  },

  onSubmit: function(e) {
    function required($input) {
      if (!$input.data("ialErrorMsg"))
        $input.ialErrorMsg({msg: ialText.COM_USERS_REGISTER_REQUIRED.value});
    }

    if (e) $(document.activeElement).blur();
    if (this.$load.ialLoad("playing")) return;
    var $input, $elem, $elems = this.$form.children(".gi-elem");
    for (var i = 0; i < $elems.length; i++) {
      $elem = $($elems[i]);
      if ($elem.find(".req").length) {
        var $load = $elem.find(".ial-load");
        if ($load.ialLoad("playing") === true) {
          if (e) e.preventDefault();
          return $load.ialLoad("onEnd", $.proxy(this, "onSubmit"));
        }
        // check required elems
        $input = $elem.find("input, textarea, select");
        if (!$input.hasClass(this.validClass)) {
          if ($input.prop("tagName") == "SELECT" && $input.val()) continue;
          if ($input.prop("type") == "checkbox")
            if ($input.prop("checked")) continue;
            else return required($input.parent(), e && e.preventDefault());
          if ($input.val()) $input.blur();
          return required($input, e && e.preventDefault());
        }
      }
    }
    this.$load.ialLoad("play");
    if (~this.$form.attr("name").indexOf("ialLogin") || ologin.regPage == 'joomla' || ologin.regPage == 'jomsocial') {
      e && e.preventDefault();
      var lang = (document.ialLogin || document.ialRegister).lang.value;
      $.ajax({
        type: "POST",
        dataType: "json",
        url: this.$form.prop("action"),
        data: this.$form.serialize()+"&ialCheck="+this.$form.attr("name")+"&lang="+lang,
        success: $.proxy(this, "onSubmitSuccess"),
        error: $.proxy(this, "onSubmitError"),
        timeout: ologin.timeout,
        cache: false
      });
    }
  },

  onSubmitSuccess: function(resp) {
    if (resp.error) {
      var $wrong = $(this.$form.prop("elements")[resp.field]);
      if (!$wrong.length) $wrong = this.$form.find('#'+resp.field);
      this.$load.ialLoad("stop");
      if (!$wrong.data("ialErrorMsg")) $wrong.ialErrorMsg({
        msg: resp.msg
      });
      if (this.$form.find('.g-recaptcha-response').val()) {
        this.$form.find('.ial-recaptcha').ialReCaptcha("reset");
      }
      // if token error, reload page
      if (resp.error == "JINVALID_TOKEN") setTimeout(function() {
        location.href = location.href;
      }, this.reloadDelay);
    }
  },

  onSubmitError: function(error) {
    this.$load.ialLoad("stop");
    console.log(error);
  }
});

$.createOOPlugin("ialSubmitLogin", "ialSubmit", {
  onSubmitSuccess: function(resp) {
    this.Super("onSubmitSuccess", arguments);
    if (!resp.error) {
      $('<input type="hidden" name="username" />')
        .val(resp.username)
        .appendTo(this.$form);
      this.$form.off("submit");
      this.$form.submit();
    }
  }
});

$.createOOPlugin("ialSubmitRegister", "ialSubmit", {
  onSubmitSuccess: function(resp) {
    this.Super("onSubmitSuccess", arguments);
    if (resp.error) {
      if (resp.field == "recaptcha_response_field") {
        this.$form.find('[name=recaptcha_challenge_field]').parent()
          .ialCaptcha("reLoad");
      }
    } else {
      if (resp.autologin) {
        this.$load.ialLoad("play");
        var $login = $('<form>').attr({
          style: "display:none",
          method: "post",
          action: "?option=com_users&task=user.login"
        }).appendTo(document.body);
        var pass = this.$form.find("[name*=password]").val();
        $('<input name="option" value="com_users">').appendTo($login);
        $('<input name="task"  value="user.login">').appendTo($login);
        $('<input name="username">').val(resp.username).appendTo($login);
        $('<input name="password">').val(pass).appendTo($login);
        $('.ial-login [name=return]').clone().appendTo($login);
        $(resp.autologin).appendTo($login);
        $login.submit();
        return;
      }
      this.$form.children().remove();
      this.$form.siblings(".ial-socials, .loginBrd").remove();
      $('<div>').ialHeader({
        jfo: {
          "jform[elem_label]": {value: '<i class="ial-correct"/>'+ialText.COM_USERS_REGISTRATION.value},
          "jform[elem_wide]": {checked: true}
        }
      }).appendTo(this.$form);
      $('<div>').ialLabel({
        jfo: {
          "jform[elem_label]": {value: resp.msg},
          "jform[elem_wide]": {checked: true}
        }
      }).appendTo(this.$form);
      if (ologin.regPopup) $('<div>').ialButton({
        jfo: {
          "jform[elem_label]": {value: "OK"},
          "jform[elem_subtitle]": {value: "&nbsp"},
          "jform[elem_wide]": {checked: true}
        }
      }).appendTo(this.$form)
        .on("click", $.proxy(this.$form.closest(".ial-window"), "ialWindow", "close"))
        .find('button').attr("type", "button");
      this.$form.ialForm("initCSS");
      $(window).trigger("resize");
    }
  }
});

$.createOOPlugin("ialSubmitReset", "ialSubmit", {
  onSubmitSuccess: function(resp) {
    this.Super("onSubmitSuccess", arguments);
    if (!resp.error) {
      location.href = resp.redirect;
    }
  }
});

$.createOOPlugin("ialSubmitRemind", "ialSubmit", {
  onSubmitSuccess: function(resp) {
    this.Super("onSubmitSuccess", arguments);
    if (!resp.error) {
      this.$form.children(":not(:first)").remove();
      $('<div>').ialLabel({
        jfo: {
          "jform[elem_label]": {value: resp.msg},
          "jform[elem_wide]": {checked: true}
        }
      }).appendTo(this.$form);
      $('<div>').ialButton({
        jfo: {
          "jform[elem_label]": {value: "OK"},
          "jform[elem_subtitle]": {value: "&nbsp"},
          "jform[elem_wide]": {checked: true}
        }
      }).appendTo(this.$form)
        .on("click", $.proxy(this.$form.closest(".ial-window"), "ialWindow", "close"))
        .find('button').attr("type", "button");
      this.$form.ialForm("initCSS");
      $(window).trigger("resize");
    }
  }
});

$.createOOPlugin("ialForm", {
  layout: {},

  Constructor: function(params) {
    $.extend(this, params);
    this.initElems();
    this.initProps();
    this.initCSS();
    if (this.$node.closest('#loginComp').length) {
      this.$node.find('.ial-recaptcha').ialReCaptcha();
    }
  },

  initElems: function() {
    var elems = ialFields.page[0].elem;
    $.fn.ialSelection = $.fn.ialSelect; // hikashop fix o_O?
    for (var i in elems) {
      // get plugin name (question marks removed for language debug)
      var plg = elems[i]["jform[elem_type]"].value.replace(/\?/g, '');
      plg = "ial" + plg.charAt(0).toUpperCase() + plg.slice(1);
      $('<div>')[plg]({ jfo: elems[i] }).appendTo(this.$node);
    }
    this.$node.find('[name="jform[captcha]"]')
      .attr("name", "recaptcha_response_field"); // compatibility with old vers
    this.$node.find('[name*=password2], [name*=email2], [name*=_confirm]')
      .ialConfirm();
    this.$node.find("button.ial-submit").ialSubmitRegister();
    // virtuemart state field
    this.$node.find("select[name=virtuemart_country_id]")
      .on("change", function(e) {
      var id = e.currentTarget.options[e.currentTarget.selectedIndex].value;
      $.ajax({
        type: "GET",
        url: ologin.base + "index.php?option=com_virtuemart&view=state" +
          "&format=json&virtuemart_country_id=" + id,
        dataType: "json",
        success: function(data) {
          var i, opt, opts = "";
          if (data[id]) for (i = 0; i < data[id].length; i++) {
            otp = data[id][i];
            opts += '<option value="' + otp.virtuemart_state_id + '">' +
              otp.state_name + '</option>';
          }
          if (!opts) opts = '<option value="0">&nbsp</option>';
          $(".loginTxt[name=virtuemart_state_id]").html(opts);
        }
      });
    }).trigger("change");
    // hikashop state field
    this.$node.find('select[name="data[address][address_country]"]')
      .on("change", function(e) {
      var val = e.currentTarget.options[e.currentTarget.selectedIndex].value;
      $.ajax({
        type: "GET",
        url: ologin.base + "index.php?option=com_hikashop&ctrl=checkout" +
          "&task=state&tmpl=component&field_id=data_address_address_state" +
          "&field_type=address&field_namekey=address_state&namekey=" + val,
        success: function(html) {
          var $res = $(html),
              $state = $('.loginTxt[name="data[address][address_state]"]');
          if ($res.hasClass("state_no_country"))
            $state.html('<option value="">'+$res.html()+'<option>');
          else $state.html($res.html());
        }
      });
    }).trigger("change");
    if (navigator.geolocation && ologin.geolocation)
      $(".regBtn").one(ologin.openEvent == "onclick"? "click" : "mouseenter",
        $.proxy(this, "getLocation")).length || this.getLocation();
  },

  initProps: function() {
    var props = ialProps;
    for (var prop in props.layout) {
      this.layout[ prop.match(/_(.*)]/)[1] ] = parseInt(props.layout[prop]);
    }
    if (ologin.theme == "flat") {
      if (this.layout.width < 230) this.layout.width = 230;
      if (this.layout.margin < 25) this.layout.margin = 25;
    }
  },

  initCSS: function() {
    this.$node.css("width",
      this.layout.columns * (2*this.layout.margin + this.layout.width));
    this.$node.children(":not(.gi-wide)").css({
      width: this.layout.width,
      margin: "0 "+this.layout.margin+"px"
    });
    this.$node.children(".gi-wide")
      .css("padding", "0 "+this.layout.margin+"px");
    this.$node.parent().children(":not(form, button, br)").css({
      marginLeft: this.layout.margin,
      marginRight: this.layout.margin
    });
    var $oauths = this.$node.parent().children(".ial-oauths")
      .css("margin", "0 0 10px");
    $oauths.children().css({
      width: this.layout.width,
      margin: "5px "+this.layout.margin+"px",
      float: "left"
    });
    $oauths.children(":nth-child("+this.layout.columns+"n+1)")
      .css("clear", "both");
    $('<br style="clear:both" />').appendTo($oauths);
  },

  getLocation: function() {
    var $input = this.$node.find("input[name*=address],"+
      "input[name*=city], input[name*=region],"+
      "input[name*=postal_code], input[name*=country]");
    if (!$input.length) return;
    navigator.geolocation.getCurrentPosition(function(pos) {
      window._mapApiLoaded = function() {
        var geocoder = new google.maps.Geocoder();
            latLng = new google.maps.LatLng(
              pos.coords.latitude, pos.coords.longitude);
        geocoder.geocode({latLng: latLng}, function(results, status) {
          if (status != "OK") return;
          var address = results[0].address_components,
              type, i = address.length,
              name = {
                administrative_area_level_1: "region",
                administrative_area_level_2: "city",
                locality: "city",
                route: "address1"
              };
          do switch (type = address[--i].types[0]) {
            case "street_number":
              var $addr = $input.filter("[name*=address]");
              if (!$addr.length) break;
              if ($addr.length > 1) $addr[1].value = address[i].long_name;
              else $addr[0].value += " "+address[i].long_name;
              $addr.blur();
              break;
            default:
              $input.filter("[name*="+(name[type] || type)+"]")
                .val(address[i].long_name).blur();
          } while (i);
        });
      };
      $.getScript(location.protocol+"//maps.googleapis.com/maps/api/js"+
        "?v=3.exp&sensor=true&callback=_mapApiLoaded");
    });
  }
});


$.createOOPlugin("ialWindowBase", {
  nodeClass: "ial-window",
  activeClass: "ial-active",
  popupCenter: false,
  border: 3,
  $bg: $(),
  $btn: undefined,
  $close: undefined,
  $arrow: undefined,

  Constructor: function(params) {
    $.extend(this, params);
    if (!this.$node.find('form, iframe').length) return;
    this.$node.addClass(this.nodeClass+" mod-"+ologin.id);
    this.$btn = this.$node.prev();
    this.$arrow = this.$node.find(".ial-arrow-up");
    this.$close = this.$node.find(".ial-close");
    if (!this.$btn.hasClass("selectBtn")) {
      this.$arrow.css("display", "none");
      this.$close.css("display", "none");
      return;
    }
    // init events
    this.$btn.on("click", $.proxy(this, "onClickBtn"));
    if (ologin.openEvent != "onclick")
      this.$btn.on("mouseenter", $.proxy(this, "onClickBtn"));
    this.$close.on("click", $.proxy(this, "close"));
    this.$bg.on("click", $.proxy(this, "close"));
    // init node
    if (!this.popupCenter) this.$node.children()
      .css("minWidth", this.$btn.outerWidth() - 2*this.border);
    if (!this.$arrow.length && !this.$btn.hasClass('userBtn')) this.popupCenter = true;
    this.$node.appendTo(document.body);
    //Call initPosition at the first run to avoid horizontal scrolling glitch on mobile devices
    this.initPosition();
  },

  initPosition: function() {
    var pos = {},
        $win = $(window),
        wndW = this.$node.outerWidth(),
        wndH = this.$node.outerHeight(),
        winW = $win.width(),
        winH = $win.height();
    if (this.popupCenter || wndW > winW || wndH > winH) {
      // popup center
      pos.marginTop = 0;
      this.$arrow.css("display", "none");
      if (wndW <= winW) {
        pos.left = (winW - wndW) / 2;
      } else {
        pos.left = 0;
        var padding = ologin.theme == "flat"? 0 : 2*(ologin.padding+11),
            $form = this.$node.find("form").css("width", winW-padding);
        $form.find(".gi-elem")
          .attr("style","padding:0px "+$form.data("ialForm").layout.margin+"px")
          .addClass("gi-wide");
        wndH = this.$node.outerHeight();
      }
      if (wndH <= winH) {
        this.$node.css("position", "fixed");
        pos.top = (winH - wndH) / 2;
      } else {
        this.$node.css("position", "absolute");
        pos.top = $(document).scrollTop();
      }
    } else {
      // popup under button
      var btnW = this.$btn.outerWidth(),
          btnH = this.$btn.outerHeight(),
          btnP = this.$btn.absolute();
      pos.top = btnP.top + btnH;
      this.leftSide = btnP.left + btnW/2 < $win.width()/2;
      if (this.leftSide) {
        // float left
        pos.left = btnP.left - this.border;
        this.$arrow.css("left", btnW/2 - 20);
      } else {
        // float right
        pos.left = btnP.left + this.border + btnW - wndW;
        this.$arrow.css("left", wndW - 3*this.border - btnW/2 - 20);
      }
    }
    this.$node.css(pos);
  },

  onKeyPress: function(e) {
    if (e.keyCode == 27) this.close();
  },

  onClickBtn: function(e) {
    e.preventDefault();
    if (this.$node.hasClass(this.activeClass)) this.close();
    else this.open();
  },

  open: function() {
    var $openWnd = $('.'+this.nodeClass+'.'+this.activeClass);
    // close other window
    if ($openWnd.length && $openWnd[0] != this.$node[0])
      $openWnd.ialWindow("close");
    // add event
    $(document).on("keypress."+this.id, $.proxy(this, "onKeyPress"));
    $(window).on("resize."+this.id, $.proxy(this, "initPosition"));
    // open window
    this.initPosition();
    $('.selectBtn').css("position", "relative");
    if (ologin.windowAnim > 16) this.$bg.css("height", $(document).height());
    else this.$bg.css("position", 'fixed');
    this.$bg.addClass(this.activeClass);
    this.$btn.addClass(this.activeClass);
    this.$node.addClass(this.activeClass);
    if (!this.recaptcha) {
      this.recaptcha = true;
      this.$node.find('.ial-recaptcha').ialReCaptcha();
    }
  },

  close: function(e) {
    if (!this.$node.hasClass(this.activeClass)) return;
    // remove event
    if($("#tosModal").hasClass("in")) $(".tosClose").click();
    $(document).off("keypress."+this.id);
    $(window).off("resize."+this.id);
    this.$node.find("[data-ialErrorMsg]").ialErrorMsg("hide");
    // close window
    $('.selectBtn').css("position", "static");
    this.$bg.removeClass(this.activeClass);
    this.$btn.removeClass(this.activeClass);
    this.$node.removeClass(this.activeClass);
  }
});

$.createOOPlugin("ialOAuth", {
  alias: "",
  url: "",
  delay: 300,

  Constructor: function(params) {
    $.extend(this, params);
    this.alias = this.$node.data("oauth");
    this.url = ologin.oauth[this.alias];
    this.$node.on("click", $.proxy(this, "open"));
  },

  open: function() {
    // OAuth fix for Microsoft redirect URI
    document.cookie = "oauth_task=" + this.url.match(/task(=|%3D)(.*)$/)[2];
    this.url = this.url.replace(/%3Foption%3Dcom_improved_ajax_login%26task%3D(windows|twitter|vk)/, "");
    var sw = window.open(this.url, "Login", "width=450,height=500,"+
      "screenX="+(screen.width/2 - 225)+","+
      "screenY="+(screen.height/2 - (this.alias == "twitter"? 450 : 250)));
    sw.focus();
    ologin.$oauthBtn = this.$node;
  }
});

ImprovedAJAXLogin = function(params) {
  $.extend(this, params);
  window.ologin = this;
  isIE = navigator.userAgent.match(/MSIE (\d+)/) ||
    navigator.userAgent.match(/\.NET.*rv:(\d+)/);
  if (isIE) $(document.body).addClass('gi-ie-'+isIE[1]);
  this.$bg = $('<div class="ial-bg ial-trans-gpu" />').appendTo(document.body);
  $(".ial-window").ialWindow({
    $bg: isIE && isIE[1] < 9? $() : this.$bg,
    popupCenter: params.wndCenter,
    border: parseInt(params.border)
  });
  $(".ial-form").ialForm();
  $(".ial-login").ialLoginForm();
  $(".ial-usermenu").ialUsermenu({
    $bg: isIE && isIE[1] < 9? $() : this.$bg,
    popupCenter: false,
    border: parseInt(params.border)
  });
  $(".ial-login .ial-submit").ialSubmitLogin();
  //reset password
  $(".ial-reset").ialSubmitReset();
  $(".ial-remind").ialSubmitRemind();
  $("[data-oauth]").ialOAuth();
  //disable chosen js
  $(function($) {
    $(".ial-form select.chzn-done")
      .removeClass("chzn-done")
      .removeAttr("style")
      .removeData("chosen")
      .next().remove();
  });
};

// trigger popups & logout
  $(function() {
    $(".log-popup").click(function(e) {
      e.preventDefault();
      $(".logBtn").click();
    });
    $(".reg-popup").click(function(e) {
      e.preventDefault();
      $(".regBtn").click();
    });
    $(".logout-link").click(function(e) {
      e.preventDefault();
      $(".logout").click();
    });

   $(".frg_reset").on("click", function(e) {
      e.preventDefault();
      $(".resetBtn").click();
    });

   $(".frg_remind").on("click", function(e) {
      e.preventDefault();
      $(".remindBtn").click();
    });
  });

  if (window.Calendar) {
    Calendar.setup = (function(setup) {
      return function patch(args) {
        return $('#'+args.inputField).length ? setup.apply(this, arguments) : null;
      };
    })(Calendar.setup);
  }

})(window.jq183 || jQuery);