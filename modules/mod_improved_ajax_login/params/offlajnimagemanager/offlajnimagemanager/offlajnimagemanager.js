
dojo.declare("OfflajnImagemanager", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.map = {};
    this.init();
  },


  init: function() {
    this.btn = dojo.byId('offlajnimagemanager'+this.id);
    dojo.connect(this.btn, 'onclick', this, 'showWindow');

    this.selectedImage = "";
    this.hidden = dojo.byId(this.id);
    dojo.connect(this.hidden, 'change', this, 'reset');


    var path = this.hidden.value.split(this.folder);
    if (path[1]) {
      dojo.attr(this.hidden, 'value', this.folder + path[1]);
    }

    this.imgprev = dojo.query('.offlajnimagemanagerimg div', this.btn)[0];
    if(this.hidden.value != "") dojo.style(this.imgprev,'backgroundImage','url("'+this.siteurl+this.hidden.value+'")');
    this.images = new Array();
  },

  reset: function(){
    if(this.hidden.value != this.selectedImage){
      // fix for default value when Joomla is in a subfolder && param is in combine
      if (this.hidden.value.indexOf(this.siteurl) < 0) {
        dojo.attr(this.hidden, 'value', this.siteurl + this.hidden.value);
      }

      this.selectedImage = this.hidden.value;
      if(this.selectedImage == '') this.selectedImage = this.folder;
      this.saveImage();
      OfflajnFireEvent(this.hidden, 'change');
    }
  },

  showOverlay: function(){
    if(!this.overlayBG){
      this.overlayBG = dojo.create('div',{'class': 'blackBg'}, dojo.body());
    }
    dojo.removeClass(this.overlayBG, 'hide');
    dojo.style(this.overlayBG,{
      'opacity': 0.3
    });
  },

  showWindow: function(){
    this.showOverlay();
    if(!this.window){
      this.window = dojo.create('div', {'class': 'OfflajnWindow'}, dojo.body());
      var closeBtn = dojo.create('div', {'class': 'OfflajnWindowClose'}, this.window);
      dojo.connect(closeBtn, 'onclick', this, 'closeWindow');
      var inner = dojo.create('div', {'class': 'OfflajnWindowInner'}, this.window);
      dojo.create('h3', {'innerHTML': 'Image Manager'}, inner);
      dojo.create('div', {'class': 'OfflajnWindowLine'}, inner);
      var imgAreaOuter = dojo.create('div', {'class': 'OfflajnWindowImgAreaOuter'}, inner);
      this.imgArea = dojo.create('div', {'class': 'OfflajnWindowImgArea'}, imgAreaOuter);

      dojo.place(this.createFrame(''), this.imgArea);

      for(var i in this.imgs){
        if(i >=0 )
          dojo.place(this.createFrame(this.imgs[i]), this.imgArea);
      }

      var left = dojo.create('div', {'class': 'OfflajnWindowLeftContainer'}, inner);
      var right = dojo.create('div', {'class': 'OfflajnWindowRightContainer'}, inner);

      dojo.create('h4', {'innerHTML': 'Upload Your Image'}, left);

      this.uploadArea = dojo.create('form', {
        'action': 'index.php?option=offlajnupload&identifier='+this.identifier,
        'enctype': 'multipart/form-data',
        'method': 'post',
        'target': 'uploadiframe',
        'class': 'OfflajnWindowUploadareaForm',
        'innerHTML': 'Drag images here or<br />'
      }, left);
      this.input = dojo.create('input', {'name': 'img', 'type': 'file'}, this.uploadArea);
      dojo.create('button', {'innerHTML': 'Upload', 'type': 'submit'}, this.uploadArea);
      dojo.connect(this.input, 'onchange', this, 'submitUpload');

      dojo.create('h4', {'innerHTML': 'Currently Selected Image'}, right);

      this.selectedframe = dojo.create('div', {'class': 'OfflajnWindowImgFrame'}, right);
      this.selectedframe.img1 = dojo.create('div', {'class': 'OfflajnWindowImgFrameImg'}, this.selectedframe);
      this.selectedframe.img2 = dojo.create('img', {}, this.selectedframe);

      dojo.connect(this.selectedframe, 'onmouseenter', dojo.hitch(this,function(img){dojo.addClass(img, 'show');}, this.selectedframe.img2));
      dojo.connect(this.selectedframe, 'onmouseleave', dojo.hitch(this,function(img){dojo.removeClass(img, 'show');}, this.selectedframe.img2));

      this.desc = dojo.create('div', {'class': 'OfflajnWindowDescription', 'innerHTML': this.description}, right);

      var saveCont = dojo.create('div', {'class': 'OfflajnWindowSaveContainer'}, right);
      var savebtn = dojo.create('div', {'class': 'OfflajnWindowSave', 'innerHTML': 'SAVE'}, saveCont);
      dojo.connect(savebtn, 'onclick', this, 'saveImage');

      this.initUploadArea();

      this.scrollbar = new OfflajnScroller({
        'extraClass': 'multi-select',
        'selectbox': this.imgArea.parentNode,
        'content': this.imgArea,
        'scrollspeed' : 30
      });
    }

    var active = this.hidden.value.match(/[^\/]+\.(jpe?g|png|gif|bmp|svg)$/i);
    this.active = active ? active[0] : '';
    this.select({currentTarget: this.map[this.active]}); // init selected img on first open

    dojo.removeClass(this.window, 'hide');
    this.exit = dojo.connect(document, "onkeypress", this, "keyPressed");
    this.loadSavedImage();
  },

  submitUpload: function() {
    dojo.removeClass(this.uploadArea, 'over');

    if (this.input.files[0]) {
      this.uploadiframe = dojo.create('iframe', {'name': 'uploadiframe', 'style': 'display:none;'}, this.uploadArea);
      dojo.connect(this.uploadiframe, 'onload', this, 'alterUpload');
      this.uploadArea.submit();
    }
  },

  loadSavedImage: function() {
    var val = this.hidden.value;
    if(val == "") val = this.folder;
    val = val.replace(this.siteurl, "");
    if(val == '' || this.images[val] == undefined) return;
    var el = this.images[val];
    el.currentTarget = el.parentNode;
    this.select(el);
  },

  closeWindow: function(){
    dojo.addClass(this.window, 'hide');
    dojo.addClass(this.overlayBG, 'hide');
  },

  createFrame: function(im, folder){
    if(!folder) folder = this.folder;
    if(this.map[im]){
      dojo.place(this.map[im], this.map[im].parentNode, 'last');
      return this.map[im];
    }
    var frame = dojo.create('div', {'class': 'OfflajnWindowImgFrame'});
    dojo.create('div', {'class': 'OfflajnWindowImgFrameImg', 'style': (im != '' ? {
      'backgroundImage': 'url("'+this.root+folder+im+'")'
    }:{}) }, frame);
    if(im != '')
      var img = dojo.create('img', {'src': this.root+folder+im}, frame);

    var caption = im != '' ? im.replace(/^.*[\\\/]/, '') : 'No image';
    dojo.create('div', {'class': 'OfflajnWindowImgFrameCaption', 'innerHTML': "<span>"+caption+"</span>"}, frame);

    frame.selected = dojo.create('div', {'class': 'OfflajnWindowImgFrameSelected'}, frame);
    frame.img = im;

    this.map[im] = frame;
    if(im != ''){
      dojo.connect(frame, 'onmouseenter', dojo.hitch(this,function(img){dojo.addClass(img, 'show');}, img));
      dojo.connect(frame, 'onmouseleave', dojo.hitch(this,function(img){dojo.removeClass(img, 'show');}, img));
      this.images[folder+im] = img;
    }
    dojo.connect(frame, 'onclick', this, 'select');
    return frame;
  },

  select: function(e){
    var el = e.currentTarget;
    jQuery(el).addClass('active').siblings('.active').removeClass('active');
    this.active = el.img;
    dojo.style(this.selectedframe.img1, 'backgroundImage', 'url("'+this.root+this.folder+this.active+'")');
    dojo.attr(this.selectedframe.img2, 'src', this.root+this.folder+this.active);
    if (this.active) {
      if (this.selectedframe.img2.naturalWidth) this.updateDescription();
      else dojo.connect(this.selectedframe.img2, 'onload', this, 'updateDescription');
    } else {
      this.desc.innerHTML = '<h5>No image</h5>';
    }
    this.selectedImage = this.folder+this.active;
    dojo.addClass(this.selectedframe, 'active');
  },

  updateDescription: function() {
    this.desc.innerHTML = '<h5>'+ this.active +'</h5>'+
      'width: ' + this.selectedframe.img2.naturalWidth + 'px<br>' +
      'height: ' + this.selectedframe.img2.naturalHeight + 'px<br>';
  },

  initUploadArea: function(){
    dojo.connect(this.window, "ondragenter", this, function(e){
      jQuery(this.uploadArea).toggleClass('over', jQuery(e.target).closest('.OfflajnWindowUploadareaForm').length > 0);
    });
  },

  changeFrameImg: function(frame, im, folder){
    if(!folder) folder = this.folder;
    dojo.attr(dojo.query("img", frame)[0], 'src', this.root+folder+im+"?"+new Date().getTime());
    dojo.style(dojo.query(".OfflajnWindowImgFrameImg", frame)[0], {
      'backgroundImage': 'url("'+this.root+folder+im+"?"+new Date().getTime()+'")'
    });
  },

  alterUpload: function(){
    var data = jQuery(this.uploadiframe).contents().find('body').html();
    jQuery(this.uploadiframe).remove();
    if (!data) return;
    var r = JSON.parse(data);
    if(r.err){
      alert(r.err);
      return;
    }else if(r.name){
      var frame = this.createFrame(r.name);
      var caption = dojo.query('.OfflajnWindowImgFrameCaption', frame)[0];
      frame.progress = dojo.create('div', {'class':'progress', 'style' : {'width':(dojo.position(caption).w-2)+'px'} }, caption, 'first');
      dojo.place(frame, this.imgArea);
      this.scrollbar.scrollReInit();
      this.scrollbar.goToBottom();
      setTimeout(dojo.hitch(this,function(p){
        dojo.animateProperty({
          node: p,
          duration: 300,
          properties: {
            opacity : 0
          }
        }).play();
      },frame.progress),1000);
    }
  },

  keyPressed: function(e) {
    if(e.keyCode == 27) {
      this.closeWindow();
      dojo.disconnect(this.exit);
    }
  },

  saveImage: function() {
    //dojo.style(this.imgprev,'backgroundImage', 'url("'+this.root+this.selectedImage+'")');
    dojo.style(this.imgprev,'backgroundImage', 'url("'+this.selectedImage+'")');
    if(this.selectedImage != this.hidden.value) {
      this.closeWindow();
      if(this.folder == this.selectedImage) this.selectedImage = "";
      this.hidden.value = this.siteurl + this.selectedImage;
      OfflajnFireEvent(this.hidden, 'change');
    }
  }

});