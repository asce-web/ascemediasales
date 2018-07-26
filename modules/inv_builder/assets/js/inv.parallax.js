(function ($, Drupal, drupalSettings) {
  "use strict";
  Drupal.behaviors.inv_block_settings_parallax = {
    attach: function (context, settings) {
      $(window).on('load', function () {
        $('[data-stellar-background-ratio]').once('parallax').each(function () {
          var $this = $(this);
          var bgWidth = $this.data('bg-width');
          var bgHeight = $this.data('bg-height');
          var ratio = $this.data('stellar-background-ratio');
          var backgroundHeight = ($(window).height()) * (1 - ratio) + $this.outerHeight(true);
          var backgroundWidth = backgroundHeight * bgWidth / bgHeight;
          if (backgroundWidth < $this.width()) {
            backgroundWidth = $this.width();
            backgroundHeight = backgroundWidth * bgHeight / bgWidth;
          }
          $this.css({
            backgroundPosition: '50% ' + backgroundHeight + 'px',
            backgroundSize: backgroundWidth + 'px ' + backgroundHeight + 'px'
          });
          $this.data('stellar-vertical-offset', backgroundHeight / (1 - ratio) + $(window).height());
        });

        setTimeout(function () {
          $(window).once('parallax').stellar({
            responsive: true,
            horizontalScrolling: false,
            parallaxElements: false
          });
        }, 500);
      });
    }
  };
})(jQuery, Drupal, drupalSettings);