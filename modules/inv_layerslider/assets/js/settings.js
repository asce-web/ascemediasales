(function ($, Drupal) {
  "use strict";
  Drupal.behaviors.inv_layerslider_settings = {
    attach: function () {
      $('form#inv-layerslider-settings').once('inv-submit').each(function () {
        $(this).submit(function () {
          var settings = {};
          $('.setting-option').each(function(){
            settings[$(this).attr('name')] = $(this).val();
          });
          $('input[name=settings]').val(JSON.stringify(settings));
        });
      });
    }
  };
})(jQuery, Drupal);