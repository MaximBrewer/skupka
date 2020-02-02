;(function($, undefined) {

$.createOOPlugin("ialWindow", "ialWindowBase", {
  Constructor: function() {
    this.Super("Constructor", arguments);
    this.$node.addClass("ial-trans-gpu ial-effect-"+ologin.windowAnim);
  },

  open: function() {
    this.Super("open", arguments);
    var node = this.$node[0],
        ialBg = this.$bg[0];
    if (ologin.windowAnim == 17 || ologin.windowAnim == 18) {
      var $body = $(document.body),
          $fake = $('#fake-offlajn-body'),
          scroll = $(document).scrollTop();
      var paddingB = $body.css('padding-top')+' '+$body.css('padding-right')+
        ' '+$body.css('padding-bottom')+' '+$body.css('padding-left');
      var marginB = $body.css('margin-top')+' '+$body.css('margin-right')+
        ' '+$body.css('margin-bottom')+' '+$body.css('margin-left');

      $('.selectBtn').css("position", "static");
      $fake.css({
        "display": "block",
        "position": "fixed",
        "width": "100%",
        "height": "100%",
        "-moz-box-sizing": "border-box",
        "-webkit-box-sizing": "border-box",
        "box-sizing":" border-box",
        "margin": marginB,
        "padding": paddingB,
        "overflow": "hidden"
      });
      $body.css({margin: 0, padding: 0});
      $body.children().each(function() {
        if (this != node && this != $fake[0]) $(this).appendTo($fake);
      });
      $fake.addClass("go-to-back-"+ologin.windowAnim);
      $fake.scrollTop(scroll);
    }
    if (ologin.windowAnim == 19)
      $(document.body).children().each(function() {
        if ((this != node) && (this != ialBg))
          $(this).css({
            "-webkit-filter": "blur(3px)",
            "-moz-filter": "url('#blur')",
            "-ms-filter": "url('#blur')",
            "-o-filter": "url('#blur')",
            "filter": "url('#blur')"
          });
      });
    if (ologin.windowAnim == 20)
      $(document.body).children().each(function() {
        if ((this != node) && (this != ialBg))
          $(this).css({
            "-webkit-filter": "grayscale(100%)",
            "-moz-filter": "url('#grayscale')",
            "-ms-filter": "url('#grayscale')",
            "-o-filter": "url('#grayscale')",
            "filter": "url('#grayscale')"
          });
      });
  },

  close: function() {
    if (!this.$node.hasClass(this.activeClass)) return;
    this.Super("close", arguments);
    if (ologin.windowAnim == 17 || ologin.windowAnim == 18) {
      var $fake = $('#fake-offlajn-body'),
          scroll = $fake.scrollTop();

      $(document.body).css({margin:"", padding:""});
      $fake.children().each(function() {
        $(this).appendTo(document.body);
      });
      $fake.removeAttr("style").removeClass("go-to-back-"+ologin.windowAnim);
      $(document).scrollTop(scroll);
    }
    if (ologin.windowAnim == 19 || ologin.windowAnim == 20) {
      $(document.body).children().each(function() {
        $(this).css({
          "-webkit-filter": "url()",
          "-moz-filter": "none",
          "-ms-filter": "none",
          "-o-filter": "none",
          "filter": "none"
        });
      });
    }
  }
});

$.createOOPlugin("ialUsermenu", "ialWindowBase", {
  Constructor: function(params) {
    this.Super("Constructor", arguments);
    this.$node
      .addClass("ial-trans-gpu ial-effect-"+ologin.windowAnim);

    $('<div class="ial-load" />').insertBefore($("a", this.$node));
    $(".ial-load", this.$node).ialLoad();

    $("a", this.$node).on("click", $.proxy(this, "onClickMenuItem"));
    $(".logout", this.node).on("click", $.proxy(this, "logout"));
  },

  initPosition: function() {
    this.Super("initPosition", arguments);
    this.$node.css("marginLeft", this.leftSide? "3px" : "-3px");
  },

  logout: function() {
    $(".ial-logout:first").submit();
  },

  onClickMenuItem: function(e) {
	if($(e.currentTarget).attr('target')) return;
    $(e.currentTarget).css("background", "none").prev().ialLoad("play");
  }
});

$.createOOPlugin("ialLoginForm", "ialForm", {
  min: {
    width: 250,
    margin: 25
  },

  initProps: function() {
    var $form = $(".ial-form");
    this.layout = $form.length? $form.data("ialForm").layout : this.min;
    if (this.layout.width < this.min.width)
      this.layout.width = this.min.width;
    if (this.layout.margin < this.min.margin)
      this.layout.margin = this.min.margin;
    this.layout.columns = 1;
  },

  initElems: function() {
    if (ologin.windowAnim == 17 || ologin.windowAnim == 18)
      $('<div id="fake-offlajn-body" />').prependTo(document.body);
    if (ologin.windowAnim == 19)
      $('<svg width="0" height="0" style="position:absolute">'+
          '<filter id="blur">'+
            '<feGaussianBlur in="SourceGraphic" stdDeviation="3"/>'+
          '</filter>'+
        '</svg>').prependTo(document.body);
    if (ologin.windowAnim == 20)
      $('<svg width="0" height="0" style="position:absolute">'+
          '<filter id="grayscale">'+
            '<feColorMatrix type="saturate" values="0"/>'+
          '</filter>'+
        '</svg>').prependTo(document.body);
    // cube rotating
    $(document.body).on("focus blur", ".loginTxt", function(e) {
      $(this.parentNode)
        [e.type == "focusin"? "addClass" : "removeClass"]("ial-active");
    });
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
    '<div class="gi-field-out">'+
      '<div class="gi-field-icon gi-user">'+
        '<div class="gi-field-icon-hover gi-user"/>'+
      '</div>'+
    '</div>'+
    '<input data-attr="id name title placeholder pattern value"'+
    ' class="loginTxt regTxt" type="text" />'+
    '<div data-attr="error" class="hidden" />',

  Constructor: function(params) {
    this.Super("Constructor", arguments);
    this.$node.find(".gi-field-icon")
      .addClass("gi-ial-"+this.jfo["jform[elem_name]"].value);
    this.$node.find(".gi-field-icon-hover")
      .addClass("gi-ial-"+this.jfo["jform[elem_name]"].value);
  }
});

$.createOOPlugin("ialPassword1", "ialPassword1Base", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<label class="smallTxt passStrongness" />'+
    '<div class="gi-field-out">'+
      '<div class="gi-field-icon gi-passw">'+
        '<div class="gi-field-icon-hover gi-passw"/>'+
      '</div>'+
    '</div>'+
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
    '<div class="gi-field-out">'+
      '<div class="gi-field-icon gi-passw">'+
        '<div class="gi-field-icon-hover gi-passw"/>'+
      '</div>'+
    '</div>'+
    '<input data-attr="id name title placeholder value"'+
    ' class="loginTxt regTxt" type="password" autocomplete="off" />'+
    '<div data-attr="error" class="hidden" />'
});

$.createOOPlugin("ialTextarea", "ialTextfieldBase", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<div class="gi-field-out">'+
      '<div class="gi-field-icon gi-ial-textarea">'+
        '<div class="gi-field-icon-hover gi-ial-textarea"/>'+
      '</div>'+
    '</div>'+
    '<textarea data-attr="name title value placeholder"'+
    ' class="loginTxt regTxt" />'
});

$.createOOPlugin("ialCaptcha", "ialCaptchaBase", {
  tmpl:
    '<input name="recaptcha_challenge_field" type="hidden" />'+
    '<div class="captchaCnt">'+
      '<span class="ial-reload loginBtn">'+
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
    '<div class="ial-modal"><div class="loginWndInside"><iframe class="ial-frame"/><span class="ial-close loginBtn">',

  Constructor: function() {
    this.Super("Constructor", arguments);
    this.$input.ialCheckBox();
  }
});

$.createOOPlugin("ialSelect", "ialElem", {
  tmpl:
    '<label data-attr="label required" class="smallTxt" />'+
    '<label class="ial-select">'+
      '<select data-attr="id name title select" class="loginTxt" />'+
    '</label>'
});

$.createOOPlugin("ialLoad", {
  Constructor: function(params) {
    $.extend(this, params);
    this.$node.css("visibility", "hidden");
    this.$box = this.$node.prev().children(".gi-field-icon");
    if (this.autoplay) this.play();
  },

  playing: function() {
    return this.$node.css("visibility") == "visible";
  },

  play: function() {
    this.$box.css("background-image", "none");
    this.$node.css("visibility", "visible");
    return true;
  },

  stop: function() {
    this.$node.css("visibility", "hidden");
    this.$box.removeAttr("style");
    if (this.onEndCallback) {
      this.$node.next().attr("disabled", false);
      this.onEndCallback();
      delete this.onEndCallback;
    }
    return false;
  },

  onEnd: function(callback) {
    this.onEndCallback = callback;
  }
});

})(window.jq183 || jQuery);