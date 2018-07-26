(function ($, Drupal, settings) {
  "use strict";

  Drupal.behaviors.inv_block_settings_animate = {
    attach: function () {
        $(".inv-animate").once('animate').each(function () {
          var $this = $(this);
          var animate = $(this).data('animate');
          var delay = $(this).data('animate-delay') || 0;
          $this.appear(function () {
            setTimeout(function () {
              $this.addClass(animate).addClass('inv-animated');
            }, delay);
          }, {
            accX: 0,
            accY: 0,
            one: true
          });
        });
    }
  };
})(jQuery, Drupal, drupalSettings);