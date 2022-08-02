"use strict";
var currentSlideIndex = 0;
var currentLayerIndex = 0;
//if ($slides == null) $slides = [{}];
var $slides = drupalSettings.inv_layerslider.slides || [{}];
//$slides.staticLayers = $slides.staticLayers || [];
var $settings = drupalSettings.inv_layerslider.settings || {};
//if ($settings == null) $settings = {};
var defaultSettings = {
  delay: 9000,
  startheight: 400,
  startwidth: 960,
  fullWidth: 'on',
  fullScreen: 'off',
  navigationType: 'bullet',
  navigationArrows: 'verticalcentered',
  navigationHAlign: 'center',
  navigationVAlign: 'bottom',
  navigationStyle: 'square',
  timer: 'bottom'
};
var defaultSlide = {
  title: 'New Slide',
  data_masterspeed: 500,
  layers: [],
  removed: 0,
  data_transition: 'fade',
  background_image_url: ''
};
var defaultLayer = {
  index: 10,
  title: '',
  type: 'text',
  text: 'New Text Layer',
  image: '',
  fid: '',
  video: '',
  top: 0,
  left: 0,
  data_speed: 500,
  incomingclasses: 'randomrotate',
  outgoingclasses: '',
  data_start: 1000,
  data_end: 0,
  data_easing: 'easeOutExpo',
  data_endeasing: '',
  removed: 0,
  video_width: 600,
  video_height: 400,
  video_volume: 'yes',
  video_autoplay: 1,
  video_fullwidth: 0,
  width: 200,
  height: 100,
  custom_css: '',
  google_font: '',
  font_style: ''
};
var $firsttime = true;
var $contenttypes = {
  text: 0,
  image: 1,
  video: 2
};

var InvCompare = function(a,b){
  if (a.index < b.index)
     return -1;
  if (a.index > b.index)
    return 1;
  return 0;
};

(function ($) {
  
  var Font = function(family, variant){
    this.family = family|| '';
    this.variant = variant || '';
    var weight = this.variant.replace(/[a-z]/g, '');
    var style = this.variant.replace(/[0-9]/g, '');
    this.weight = isNaN(parseInt(weight)) ? 400 : parseInt(weight);
    this.style = style === 'italic'?'italic':'normal';
  };
  
  Font.prototype.load = function(){
    if(this.family === '') return;
    var apiUrl = [];
    apiUrl.push('//fonts.googleapis.com/css?family=');
    apiUrl.push(this.family.replace(/ /g, '+'));
    apiUrl.push(':');
    apiUrl.push(this.variant);
    var url = apiUrl.join('');
    $('head').append('<style media="all">@import url("' + url + '");</style>');
  };
  
  Font.prototype.apply = function(element){
    this.load();
    $(element).css({
      fontFamily: '"' + this.family + '"',
      fontStyle: this.style,
      fontWeight: this.weight
    });
  };
  
  var currentLayer = function(){
    return $slides[currentSlideIndex].layers[currentLayerIndex];
  };
  
  var getCSS = function (layer){
    if(typeof layer === 'undefined'){
      layer = currentLayer();
    }
    var css = layer.custom_css.replace(/\r?\n|\r/g,'').split(';');
    layer.font_size = layer.font_size || '';
    layer.font_color = layer.font_color || '';
    if(layer.font_color.toString() !== '') css.push('color:'+ layer.font_color);
    if(layer.font_size.toString() !== '') css.push('font-size:'+ layer.font_size);
    for (var i = 0; i < css.length; i++) {
      if (css[i] === '') {         
        css.splice(i, 1);
        i--;
      }
    }
    return css.join(';');
  };
  
  var adjustPos = function ($element, top, left, voffset, hoffset) {
    var slider_design = $('#slide-design'),
            _left = 0,
            _top = 0;
    voffset = voffset || 0;
    hoffset = hoffset || 0;
    voffset = parseInt(voffset);
    hoffset = parseInt(hoffset);
    if (left === 'left') {
      _left = 0 + hoffset;
    } else if (left === 'center') {
      _left = (slider_design.width() - $element.width()) / 2 + hoffset;
    } else if (left === 'right') {
      _left = slider_design.width() - $element.width() + hoffset;
    } else {
      _left = parseInt(left) + hoffset;
    }
    if (top === 'top') {
      _top = 0 + voffset;
    } else if (top === 'center') {
      _top = (slider_design.height() - $element.height()) / 2 + voffset;
    } else if (top === 'bottom') {
      _top = slider_design.height() - $element.height() + voffset;
    } else {
      _top = parseInt(top) + voffset;
    }
    $element.css({
      left: _left + 'px',
      top: _top + 'px'
    });
  };
  
  var stripHTML = function (dirtyString) {
    return dirtyString.replace(/(<([^>]+)>)/ig, '');
    var container = document.createElement('div');
    var text = document.createTextNode(dirtyString);
    container.appendChild(text);
    return $(container).text(); // innerHTML will be a xss safe string
  };
  $(document).ready(function () {
    if ($slides.length === 0) {
      $('#inv_layerslider_main').hide(0);
    }
    init();
	var sliderListEle = document.getElementById('slideslist');
	Sortable.create(sliderListEle, {
		onUpdate: function (/**Event*/evt) {
			$('#slideslist').find('li').each(function (index) {
			  var sindex = $(this).attr('index');
			  $slides[sindex].index = index;
			  $slides.sort(InvCompare);
			  init();
			});
		},
	});
    $('.add-slide').click(function (e) {
      e.preventDefault();
      duplicateSlide(defaultSlide);
    });
    $('.add-layer').click(function (e) {
      e.preventDefault();
      defaultLayer.type = $(this).data('type');
      duplicateLayer(defaultLayer);
      return false;
    });
    $('form#inv-layerslider-slides-edit').on('submit', function (e) {
      //e.preventDefault();
      //return true;
      return saveLayerSlider(false);
    });
    
    $('textarea[name=text]').keyup(function () {
      $slides[currentSlideIndex].layers[currentLayerIndex].text = $(this).val();
      $('#' + currentSlideIndex + '-' + currentLayerIndex).find('.inner').html($(this).val());
      var layertitle = stripHTML($(this).val()).substring(0, 15) + '...';
      $('#layer-list li.active > span:first').text(layertitle);
      var $ele = $('#' + currentSlideIndex + '-' + currentLayerIndex),
              top = $('input[name=top]').val(),
              left = $('input[name=left]').val(),
              voffset = $('input[name=voffset]').val(),
              hoffset = $('input[name=hoffset]').val();
      adjustPos($ele, top, left, voffset, hoffset);
    });
    /**Custom css*/
    $('[name=custom_css]').keyup(function () {
      saveLayer();
      $('#' + currentSlideIndex + '-' + currentLayerIndex).find('.inner').attr('style', getCSS());
      var layer = currentLayer();
      var font = new Font(layer.google_font, layer.font_style);
      font.apply('.layer.selected .inner');
      var $ele = $('#' + currentSlideIndex + '-' + currentLayerIndex),
              top = $('input[name=top]').val(),
              left = $('input[name=left]').val(),
              voffset = $('input[name=voffset]').val(),
              hoffset = $('input[name=hoffset]').val();
      adjustPos($ele, top, left, voffset, hoffset);
    });
    /**/
    $('input[name=background_image]').on('image-browser-updated', function(){
      var file = $(this).data('file') || {url:''};
      $('#slide-design').css({backgroundImage:'url('+file.url+')'});
      $slides[currentSlideIndex].background_image_url = file.url;
    });
    $('input[name=background_image]').change(function(){
      $('#slide-design').css({backgroundImage:'url('+$(this).data('url')+')'});
      $slides[currentSlideIndex].background_image_url = $(this).data('url');
    });
    $('input[name=background_color]').on('update', function(){
      var color = $(this).val() == ''? '' : $(this).val();
      $slides[currentSlideIndex].background_color = color;
      $('#slide-design').css({backgroundColor: color});
    });
    $('[name=font_size]').change(function () {
      $('[name=custom_css]').trigger('keyup');
    });
    $('[name=font_color]').on('update change' ,function () {
      $('[name=custom_css]').trigger('keyup');
    });
    $('[name=font_style]').change(function () {
      $('[name=custom_css]').trigger('keyup');
    });
    /**Custom class*/
    $('[name=custom_class]').keyup(function () {
      $slides[currentSlideIndex].layers[currentLayerIndex].custom_class = $(this).val();
      $('#' + currentSlideIndex + '-' + currentLayerIndex).find('.inner').attr('class', 'inner').addClass($(this).val());
    });
    /*Global setiings*/
    $settings = $.extend(defaultSettings, $settings);
    $('input.global-settings, select.global-settings, textarea.global-settings').each(function (index) {
      $(this).val($settings[$(this).attr('name')]);
    });
    $('#slide-design').width($settings.startwidth).height($settings.startheight);

    $('input[name=left],input[name=top],input[name=voffset],input[name=hoffset]').change(function () {
      var $ele = $('#' + currentSlideIndex + '-' + currentLayerIndex),
              top = $('input[name=top]').val(),
              left = $('input[name=left]').val(),
              voffset = $('input[name=voffset]').val(),
              hoffset = $('input[name=hoffset]').val();
      adjustPos($ele, top, left, voffset, hoffset);
    });
    $('input[name=image]').on('change',function(){
      var url = $(this).data('url');
      $('#' + currentSlideIndex + '-' + currentLayerIndex).find('img').attr('src', url);
      $slides[currentSlideIndex].layers[currentLayerIndex].image_url = url;
      if(url !== ''){
        var image = new Image();
        image.onload = function () {
          $('#' + currentSlideIndex + '-' + currentLayerIndex).width(this.width);
          $('#' + currentSlideIndex + '-' + currentLayerIndex).height(this.height);
        };
        image.src = url;
      }
    });
    $('input[name=height]').change(function () {
      $('#' + currentSlideIndex + '-' + currentLayerIndex).css({
        height: $(this).val() + 'px'
      });
    });
    $('input[name=video_width]').change(function () {
      $('#' + currentSlideIndex + '-' + currentLayerIndex).css({
        width: $(this).val() + 'px'
      });
    });
    $('input[name=video_height]').change(function () {
      $('#' + currentSlideIndex + '-' + currentLayerIndex).css({
        height: $(this).val() + 'px'
      });
    });
    $('html').keyup(function (e) {
      if (e.keyCode == 46) {
        if ($('.layer-option:focus,.slide-option:focus').length === 0) {
          $('#layer-list').find('li.active').find('.remove-layer').trigger('click');
        }
      }
    });
    $('select[name=data_bgposition]').change(function () {
      $('#slide-design').css({
        backgroundPosition: $(this).val()
      });
    });
    $('#slide_title').keyup(function () {
      $('#slideslist li.active span:first').text($(this).val());
    });
    $('select[name=video_fullwidth]').change(function(){
      if($(this).val() == 1){
        $('.layer.selected').css({
          width: '100%',
          height: '100%',
          left: 0,
          top: 0
        });
      }else{
        $('.layer.selected').css({
          width: $('[name=video_width]').val(),
          height: $('[name=video_height]').val()
        });
        var top = $('input[name=top]').val(),
              left = $('input[name=left]').val(),
              voffset = $('input[name=voffset]').val(),
              hoffset = $('input[name=hoffset]').val();
        adjustPos($('.layer.selected'), top, left, voffset, hoffset);
      }
    });
  });

  function init() {
    $('#slideslist').find('li').remove();
    $($slides).each(function (slideIndex) {
      this.title = this.title || 'Slide #' + (slideIndex + 1);
      addSlideTab(this);
    });
    loadSlide(0);
  }
  /*Slide functions*/
  function addSlideTab(slide) {
    var slideIndex = $slides.indexOf(slide);
    var slideTab = $('<li>').attr('index', slideIndex);
    var slideTabTitle = '';
    if (slide.title == '') {
      slideTabTitle = $('<span>').text('Slide #' + (slideIndex + 1));
    } else {
      slideTabTitle = $('<span>').text(slide.title || 'Slide title');
    }
    slideTabTitle.click(function () {
      if ($(this).hasClass('active'))
        return;
      saveLayer();
      saveSlide();
      loadSlide(slideIndex);
    });
    var slideTabRemove = $('<span>').text('').addClass('remove-slide fa fa-times-circle');
    var slideTabDuplicate = $('<span>').text('').addClass('duplicate-slide fa fa-copy');
    slideTabRemove.click(function () {
      removeSlide(slide);
    });
    slideTabDuplicate.click(function (e) {
      e.preventDefault();
      duplicateSlide(slide);
    });
    slideTab.append(slideTabTitle).append(slideTabDuplicate).append(slideTabRemove);
    $('#slideslist').append(slideTab);
  }

  function loadSlide(slideIndex) {
    currentSlideIndex = slideIndex;
    $('ul#slideslist').find('li').removeClass('active');
    $('ul#slideslist').find('li[index=' + slideIndex + ']').addClass('active');
    if ($slides.length === 0)
      return;
    if (typeof $slides[slideIndex].background_image_url != 'undefined' && $slides[slideIndex].background_image_url != '') {
      $('#slide-design').css({
        backgroundImage: 'url(' + $slides[slideIndex].background_image_url + ')',
        backgroundPosition: $slides[slideIndex].data_bgposition || 'center center'
      });
    } else {
      $('#slide-design').css({
        backgroundImage: 'none'
      });
    }
    $slides[slideIndex].background_color = $slides[slideIndex].background_color || '';
    if($slides[slideIndex].background_color != ''){
      $('#slide-design').css({
        backgroundColor: $slides[slideIndex].background_color
      });
    }
    $slides[slideIndex].data_delay = $slides[slideIndex].data_delay || $settings.delay;
    $('.slide-option').each(function () {
      if (typeof $slides[slideIndex][jQuery(this).attr('name')] !== "undefined") {
        $(this).val($slides[slideIndex][jQuery(this).attr('name')]);
        if($(this).hasClass('image-browser-js')){
          $(this).trigger('update');
        }
      } else {
        $(this).val('');
      }
    });
    /**/
    loadLayers(slideIndex);
  }

  function saveSlide() {
    if ($slides.length === 0)
      return;
    jQuery('.slide-option').each(function () {
      $slides[currentSlideIndex][jQuery(this).attr('name')] = $(this).val();
      if($(this).hasClass('file-upload')){
        $slides[currentSlideIndex][jQuery(this).attr('name')+'_fid'] = $(this).data('fid');
      }
    });
    $slides[currentSlideIndex].layers.sort(InvCompare);
  }

  function removeSlide(slide) {
    var slideIndex = $slides.indexOf(slide);
    if (slideIndex > -1) {
      $slides.splice(slideIndex, 1);
      $('ul#slideslist').find('li').remove();
      $($slides).each(function () {
        addSlideTab(this);
      });
      loadSlide(0);
    }
  }

  /*Layer functions*/
  function loadLayers(slideIndex) {
    $('#slide-design').find('div').remove();
    currentSlideIndex = slideIndex;
    /*Remove all layer tabs*/
    $('#layer-list').find('li').remove();
    /*Load new layer tabs*/
    if (typeof $slides[currentSlideIndex].layers == 'undefined') {
      $slides[currentSlideIndex].layers = new Array();
    }
    $($slides[currentSlideIndex].layers).each(function (layerIndex) {
      this.index = layerIndex;
      if ($slides[currentSlideIndex].layers[layerIndex].removed != 1) {
        addLayerTab(this);
      }
    })
    /*Reset layer option value*/
    $('.layer-option').val('');
    $slides[currentSlideIndex].layers = $slides[currentSlideIndex].layers || [];
    if (typeof $slides[currentSlideIndex].layers[0] !== 'undefined') {
      loadLayer(0);
    }
  }

  function addLayerTab(layer) {
    var layerIndex = $slides[currentSlideIndex].layers.indexOf(layer);
    var layertype = layer.type;
    var layerTab = $('<li>').attr('index', layerIndex).addClass('vertical-tabs__menu-item').addClass(layertype);
    if (layertype == 'text') {
      layer.title = stripHTML(layer.text);
    } else if (layertype == 'image') {
      var m = layer.image.toString().match(/.*\/(.+?)\./);
      if (m && m.length > 1) {
        layer.title = m[1];
      }
    }
    layer.title = layer.title || 'Layer #' + (layerIndex + 1);
    var layerTabTitle = $('<a href="#"><span>').text(layer.title.substring(0, 15) + '...');
    layerTabTitle.wrap('<div>');
    var layerTabRemove = $('<span>').text('').addClass('remove-layer fa fa-times-circle');
    var layerTabDuplicate = $('<span>').attr('title', 'Duplicate this layer').text('').addClass('fa fa-copy');
    var layerTabMove = $('<span>').text('').addClass('move fa fa-arrows');
    layerTabTitle.click(function (e) {
      e.preventDefault();
      saveLayer();
      loadLayer(layerIndex);
    });
    layerTabDuplicate.click(function () {
      saveLayer();
      duplicateLayer(layer);
    });
    layerTabRemove.click(function () {
      if (confirm('Are you sure to remove this layer?')) {
        removeLayer(layer);
      }
    });
    layerTab.append(layerTabTitle);
    //layerTabTitle.wrap('<a href="#">');
    layerTab.append(layerTabRemove);
    layerTab.append(layerTabDuplicate);
    layerTab.append(layerTabMove);
    $('ul#layer-list').append(layerTab);
    var newLayerDesign = $('<div>').addClass('layer tp-caption').attr('id', currentSlideIndex + '-' + layerIndex);
    newLayerDesign.addClass('caption');
    if (typeof $slides[currentSlideIndex].layers[layerIndex].text_style == 'undefined') {
      $slides[currentSlideIndex].layers[layerIndex].text_style = 'text';
    }
    if ($slides[currentSlideIndex].layers[layerIndex].type == 'text') {
      newLayerDesign.addClass($slides[currentSlideIndex].layers[layerIndex].text_style);
    }
    var content = '';
    newLayerDesign.addClass($slides[currentSlideIndex].layers[layerIndex].type);
    switch ($slides[currentSlideIndex].layers[layerIndex].type) {
      case 'image':
        content = '<img src="' + $slides[currentSlideIndex].layers[layerIndex].image_url + '"/>';
        var img = new Image();
        img.onload = function () {
          newLayerDesign.width(this.width);
          newLayerDesign.height(this.height);
          newLayerDesign.data({
            imageWidth: this.width,
            imageHeight: this.height
          });
        };
        //console.log($slides[currentSlideIndex].layers[layerIndex].image_url);
        img.src = $slides[currentSlideIndex].layers[layerIndex].image_url;
        break;
      case 'video':
        newLayerDesign.addClass('layer-video');
        var video_width = layer.video_width + 'px',
                video_height = layer.video_height + 'px';
        if (layer.video_fullwidth == 1) {
          video_width = '100%';
          video_height = '100%';
        }
        newLayerDesign.css({
          width: video_width,
          height: video_height
        });
        content = '<div style="width:100%;height:100%;background-image:url(' + layer.html5_video_poster + ')"></div>';
        break;
      case 'text':
        content = '<p style="margin:0">' + $slides[currentSlideIndex].layers[layerIndex].text + '</p>';
    }
    var font = new Font($slides[currentSlideIndex].layers[layerIndex].google_font, $slides[currentSlideIndex].layers[layerIndex].font_style);
    var inner = $('<div>').addClass('inner');
    inner.attr('style', getCSS(layer));
    inner.html(content);
    font.apply(inner);
    newLayerDesign.append(inner);
    var zIndex = $slides[currentSlideIndex].layers[layerIndex].index + 1;
    newLayerDesign.mousedown(function () {
      saveLayer();
      loadLayer(layerIndex);
    }).draggable({
      //containment: "parent",
      drag: function (event, ui) {
        //setLayerPosition(layerIndex, ui.position.top + $(this).height()/2, ui.position.left+$(this).width()/2);
        setLayerPosition(layerIndex, ui.position.top, ui.position.left);
      },
      grid: [1, 1]
    }).click(function(e){
      e.preventDefault();
    });

    $('#slide-design').append(newLayerDesign);
    newLayerDesign.css({
      zIndex: zIndex
    });
    setTimeout(function () {
      adjustPos(newLayerDesign, $slides[currentSlideIndex].layers[layerIndex].top, $slides[currentSlideIndex].layers[layerIndex].left, $slides[currentSlideIndex].layers[layerIndex].voffset, $slides[currentSlideIndex].layers[layerIndex].hoffset);
    }, 500);

    $('#layeroptions').show(0);
    try {
    //  $('#layer-list').sortable('destroy');
    } catch (e) {
    }
	
	var layerListEle = document.getElementById('layer-list');
	Sortable.create(layerListEle, {
		handle: ".move",
		onUpdate: function (/**Event*/evt) {
			$('#layer-list').find('li').each(function (index) {
			  var lindex = $(this).attr('index');
			  $slides[currentSlideIndex].layers[lindex].index = index;
			});
			$slides[currentSlideIndex].layers.sort(InvCompare);
			//saveLayer();
			saveSlide();
			loadSlide(currentSlideIndex);
		},
	});
  }

  function duplicateSlide(slide) {
    saveSlide();
    var newSlideIndex = $slides.length;
    var newSlide = $.extend(true, {}, slide);
    newSlide.index = $slides.length;
    newSlide.title = 'Slide #' + ($slides.length + 1);
    $slides.push(newSlide);
    addSlideTab(newSlide);
    loadSlide(newSlideIndex);
  }

  function duplicateLayer(layer) {
    /*Save current layer*/
    saveLayer();
    var newlayer = $.extend(true, {}, layer);
    newlayer.index = $slides[currentSlideIndex].layers.length;
    $slides[currentSlideIndex].layers.push(newlayer);
    addLayerTab(newlayer);
    loadLayer($slides[currentSlideIndex].layers.indexOf(newlayer));
  }

  function loadLayer(layerIndex) {
    currentLayerIndex = layerIndex;
    $('.layer').removeClass('selected');
    $('#' + currentSlideIndex + '-' + layerIndex).addClass('selected');
    $('ul#layer-list').find('li').removeClass('is-selected');
    $('ul#layer-list').find('li[index=' + layerIndex + ']').addClass('is-selected');
    /*Bind layer data*/
    $('#edit-layer-settings').removeClass('text image video').addClass($slides[currentSlideIndex]['layers'][layerIndex].type);
    $('.layer-option').each(function () {
      if (typeof $slides[currentSlideIndex]['layers'][layerIndex][$(this).attr('name')] !== 'undefined') {
        $(this).val($slides[currentSlideIndex]['layers'][layerIndex][$(this).attr('name')]);
        if($(this).hasClass('image-browser-js')){
          $(this).trigger('update');
        }
      } else {
        $(this).val('');
      }
    });
    if ($slides[currentSlideIndex].layers[layerIndex].type === 'video') {
      $('select[name=video_type]').trigger('change');
      $('select[name=video_fullwidth]').trigger('change');
    }
    if ($slides[currentSlideIndex].layers[layerIndex].type === 'text') {
      var $google_font = $.grep(drupalSettings.google_fonts.items, function (e) {
        return e.family === $slides[currentSlideIndex].layers[layerIndex].google_font;
      })[0];
      $('select[name=font_style] option').remove();
      if(typeof $google_font !== 'undefined'){
        $($google_font.variants).each(function () {
          var thenum = this.replace(/[a-z]/g, '');
          var alphabet = this.replace(/[0-9]/g, '');
          thenum = isNaN(parseInt(thenum)) ? 400 : parseInt(thenum);
          var str = Drupal.variants[thenum.toString()] + " " + thenum.toString();
          str += alphabet === 'italic' ? ' Italic' : '';
          $('select[name=font_style]')[0].add(new Option(str, this.toString()));
        });
      }
      $('select[name=font_style]').val($slides[currentSlideIndex].layers[layerIndex].font_style);
    }
  }

  function setLayerPosition($layerIndex, top, left) {

    if($slides[currentSlideIndex].layers[$layerIndex].top == 'center'){
      var voffset = parseInt(top - ($('#slide-design').height() - $('.layer.selected').height())/2);
      $('input[name=voffset]').val(voffset);
    }else if($slides[currentSlideIndex].layers[$layerIndex].top == 'top'){
      $('input[name=voffset]').val(top);
    }else if($slides[currentSlideIndex].layers[$layerIndex].top == 'bottom'){
      //var voffset = 0 - top;
      var voffset = top - $('#slide-design').height() + $('.layer.selected').height();
      $('input[name=voffset]').val(voffset);
    }else{
      $('input[name=top]').val(top);
    }
    
    if($slides[currentSlideIndex].layers[$layerIndex].left == 'center'){
      var hoffset = parseInt(left - $('#slide-design').width()/2);
      $('input[name=hoffset]').val(hoffset);
    }else if($slides[currentSlideIndex].layers[$layerIndex].left == 'left'){
      $('input[name=hoffset]').val(left);
    }else if($slides[currentSlideIndex].layers[$layerIndex].left == 'right'){
      var hoffset = left + $('.layer.selected').width() - $('#slide-design').width();
      $('input[name=hoffset]').val(hoffset);
    }else{
      $('input[name=left]').val(left);
    }
  }

  function saveLayer() {
    if ($slides.length === 0) {
      return;
    }
    if ($slides[currentSlideIndex].layers.length === 0) {
      return;
    }
    $('.layer-option').each(function () {
      $slides[currentSlideIndex].layers[currentLayerIndex][$(this).attr('name')] = $(this).val();
      if($(this).hasClass('file-upload')){
        $slides[currentSlideIndex].layers[currentLayerIndex][$(this).attr('name')+'_fid'] = $(this).data('fid');
      }
    });
  }

  function removeLayer(layer) {
    var layerIndex = $slides[currentSlideIndex].layers.indexOf(layer);
    if (layerIndex > -1) {
      $slides[currentSlideIndex].layers.splice(layerIndex, 1);
      $('#' + currentSlideIndex + '-' + layerIndex).remove();
      currentLayerIndex = 0;
      loadSlide(currentSlideIndex);
    }
  }

  function saveLayerSlider(ajax) {
    saveSlide();
    saveLayer();
    var dataslides = JSON.stringify($slides);
    $('input[name=data]').val(dataslides);
    return true;
  }
})(jQuery, Drupal, drupalSettings);