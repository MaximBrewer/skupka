;(function($, undefined) {

$.createOOPlugin("ialWindow", "ialWindowBase", {
  Constructor: function() {
    this.Super("Constructor", arguments);
    this.$node
      .addClass("ial-trans-gpu ial-trans-"+(this.popupCenter? 't' : 'b'));
  }
});

$.createOOPlugin("ialUsermenu", "ialWindowBase", {
  Constructor: function(params) {
    this.Super("Constructor", arguments);
    this.$node
      .addClass("ial-trans-gpu ial-trans-"+(this.popupCenter? 't' : 'b'));
    $("a", this.$node).on("click", $.proxy(this, "onClickMenuItem"));
    $(".logout", this.node).on("click", $.proxy(this, "logout"));
  },

  logout: function() {
    $(".ial-logout:first").submit();
  },

  onClickMenuItem: function(e) {
	if($(e.currentTarget).attr('target')) return;
    $('<div class="ial-load" />')
      .ialLoad({autoplay: true})
      .insertBefore($(e.currentTarget).css("background", "none"));
  }
});

$.createOOPlugin("ialLoginForm", "ialForm", {
  initProps: function() {
    var $form = $(".ial-form");
    this.layout = $form.length? $form.data("ialForm").layout : {margin: 10};
    if (this.$node.parent().hasClass("loginWndInside")) {
      var $check = this.$node.find(".ial-check-lbl"),
          width = $check.outerWidth(false) + $check.next().outerWidth(false) + 14;
      this.layout.width = (width < $(document.body).width() && width > 14) ? width : 225;
    } else {
      this.layout.margin = 0;
      this.layout.width = "100%";
    }
    this.layout.columns = 1;
  },

  initElems: function() {
    this.$node.find("input.ial-checkbox").ialCheckBox();
    if (this.$node.prop('name') == 'ialLogin') {
      $(window).on('load', function() {
        $([document.ialLogin.username, document.ialLogin.email]).val($('#saveduser').val());
        $(document.ialLogin.password).val($('#savedpass').val());
        $(document.saved).remove();
      });
    }
  }
});

$.createOOPlugin("ialLoad", {
  // default params
  fps: 20,
  bgPos: 0,
  bgHeight: 14,
  autoplay: false,
  interval: undefined,

  Constructor: function(params) {
    $.extend(this, params);
    this.$node.css("visibility", "hidden");
    if (this.autoplay) this.play();
  },

  playing: function() {
    return this.interval? true : false;
  },

  play: function() {
    if (!this.interval) {
      this.interval = setInterval($.proxy(this, "onAnimate"), 1000/this.fps);
      this.$node.css("visibility", "visible");
    }
    return true;
  },

  stop: function() {
    this.interval = clearInterval(this.interval);
    this.$node.css("visibility", "hidden");
    this.bgPos = 0;
    if (this.onEndCallback) {
      this.$node.next().attr("disabled", false);
      this.onEndCallback();
      delete this.onEndCallback;
    }
    return false;
  },

  onEnd: function(callback) {
    this.onEndCallback = callback;
  },

  onAnimate: function() {
    this.$node.css("backgroundPosition", "0 "+this.bgPos+"px");
    this.bgPos -= this.bgHeight;
  }
});

$.createOOPlugin("ialHeader", "ialElem", {
  tmpl:
    '<h3 class="loginH3">'+
      '<span data-attr="label" />'+
      '<span data-attr="subtitle" class="smallTxt regRequired" />'+
    '</h3>'
});

$.createOOPlugin("ialTextfield", "ialTextfieldBase", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<input data-attr="id name title placeholder pattern value"'+
    ' class="loginTxt regTxt" type="text" />'+
    '<div data-attr="error" class="hidden" />'
});

$.createOOPlugin("ialPassword1", "ialPassword1Base", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<label class="smallTxt passStrongness" />'+
    '<input data-attr="id name title placeholder"'+
    ' class="loginTxt regTxt" type="password" autocomplete="off" />'+
    '<div data-attr="error" class="hidden" />'+
    '<label class="strongFields">'+
      '<i class="empty strongField" /><i class="empty strongField" />'+
      '<i class="empty strongField" /><i class="empty strongField" />'+
      '<i class="empty strongField" />'+
    '</label>'
});

$.createOOPlugin("ialPassword2", "ialTextfieldBase", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<input data-attr="id name title placeholder value"'+
    ' class="loginTxt regTxt" type="password" autocomplete="off" />'+
    '<div data-attr="error" class="hidden" />'
});

$.createOOPlugin("ialTextarea", "ialTextfieldBase", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<textarea data-attr="name title value placeholder"'+
    ' class="loginTxt regTxt" />'
});

$.createOOPlugin("ialCaptcha", "ialCaptchaBase", {
  tmpl:
    '<input name="recaptcha_challenge_field" type="hidden" />'+
    '<div class="captchaCnt">'+
      '<span class="ial-close loginBtn">'+
        '<i class="ial-icon-refr" />'+
      '</span>'+
      '<img class="ial-captcha" />'+
    '</div>'
});

$.createOOPlugin("ialButton", "ialElem", {
  tmpl:
    '<label data-attr="subtitle" class="smallTxt" />'+
    '<button class="loginBtn ial-submit" name="submit">'+
      '<span>'+
        '<i class="ial-load" />'+
        '<span data-attr="label" />'+
      '</span>'+
    '</button>'
});

$.createOOPlugin("ialLabel", "ialElem", {
  tmpl: '<span data-attr="label" class="smallTxt" />'
});

$.createOOPlugin("ialCheckbox", "ialElem", {
  tmpl:
    '<label data-attr="title required" class="ial-check-lbl smallTxt">'+
      '<input data-attr="id name checked value"'+
      ' type="checkbox" class="ial-checkbox" />'+
      '<span data-attr="label" />'+
    '</label>',

  Constructor: function() {
    this.Super("Constructor", arguments);
    this.$input.ialCheckBox();
  }
});

$.createOOPlugin("ialTos", "ialTosBase", {
  tmpl:
    '<label data-attr="title required" class="ial-check-lbl smallTxt">'+
      '<input data-attr="id name checked"'+
      ' type="checkbox" class="ial-checkbox" />'+
      '<span data-attr="label" />'+
    '</label>'+
    '<a data-attr="article" class="forgetLnk selectBtn" href="javascript:;" onclick="return false" />'+
    '<div class="ial-modal"><div class="loginWndInside"><iframe class="ial-frame" /><span class="ial-close loginBtn" />',

  Constructor: function() {
    this.Super("Constructor", arguments);
    this.$input.ialCheckBox();
    var closeImg = ologin.base+'modules/mod_improved_ajax_login/themes/elegant/images/x.png';
    this.$modal.find('.ial-close').html('<img src="'+closeImg+'" alt="x">');
  }
});

$.createOOPlugin("ialSelect", "ialElem", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<label class="ial-select">'+
      '<select data-attr="id name title select" class="loginTxt" />'+
    '</label>'
});

})(window.jq183 || jQuery);