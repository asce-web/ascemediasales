(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.inv_builder_stats = {
    attach: function () {
      $('.inv-stats').once('shortcode').each(function () {
        var $this = $(this);
        $(this).appear(function () {
          $this.find('.stats-count').animate({'number': $this.data('number')}, {
            step: function (n) {
              var text = parseInt(n);
              $(this).text(text);
            },
            duration: $this.data('duration'),
            easing: 'linear'
          });
        }, {
          accX: 0,
          accY: 0,
          one: true
        });
      });
    }
  };
})(jQuery, Drupal);
