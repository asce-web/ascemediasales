(function ($, Drupal, drupalSettings) {
  "use strict";
  Drupal.behaviors.inv_layerslider = {
    attach: function (context, settings) {
      $('.layerslider-banner').once('revolution').each(function () {
        settings.inv_layerslider_google_fonts = settings.inv_layerslider_google_fonts || {};
        var slider_settings = settings.inv_layerslider_settings[$(this).attr('id')];
        $.each(settings.inv_layerslider_google_fonts, function () {
          if (this.family === '')
            return;
          this.variant = this.variant || 'regular';
          var apiUrl = [];
          apiUrl.push('//fonts.googleapis.com/css?family=');
          apiUrl.push(this.family.replace(/ /g, '+'));
          apiUrl.push(':');
          apiUrl.push(this.variant);
          var url = apiUrl.join('');
          $('head').append('<style media="all">@import url("' + url + '");</style>');
        });
        slider_settings.timer = slider_settings.timer || '';
        if(slider_settings.timer === ''){
          slider_settings.hideTimerBar = 'on';
        }
        //slider_settings.minFullScreenHeight = slider_settings.startheight;
        //slider_settings.minHeight = slider_settings.startheight;
        var api = $(this).css('opacity',1).revolution(slider_settings);
        $(this).find('.tp-caption[data-link]').click(function () {
          $(this).css({cursor: 'pointer'});
          if ($(this).data('link').indexOf('slide-') === 0) {
            api.revshowslide($(this).data('link').toString().replace(/[a-z]/g, ''));
          } else if ($(this).data('link') === 'revnext') {
            api.revnext();
          } else if ($(this).data('link') === 'revprev') {
            api.revprev();
          } else if ($(this).data('link') === 'revscroll') {
            api.revscroll();
          } else {
            window.location = $(this).data('link');
          }
        });
        $(this).data('revslider', api);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);