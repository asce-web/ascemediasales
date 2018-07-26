(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.inv_builder_skillbar = {
    attach: function () {
      $('.inv-builder-skill-bar').once('shortcode').each(function () {
        var percent = $(this).data('percent');
        $(this).appear(function () {
          $(this).find('.progress-bar').css({width: percent});
        }, {
          accX: 0,
          accY: 0,
          one: true
        });
      });
    }
  };
})(jQuery, Drupal);