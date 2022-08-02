(function ($, Drupal) {
  "use strict";
  Drupal.behaviors.innovation_sticky = {
    attach: function (context) {
      $('.inv-sticky', context).once('sticky').each(function () {
        var $this = $(this);
        var $stick_wrapper = $('<div>').addClass('sticky-wrapper');
        $this.wrap($stick_wrapper);
        $this.affix({
          offset: {
            top: function () {
              var $return = $this.parent().offset().top - parseInt($('body').css('paddingTop')) + 1;
              $('.inv-sticky.affix').not($this).each(function () {
                if (($(this).hasClass('unsticky-mobile') && $(window).width() < 992) === false) {
                  $return = $return - $(this).parent().height();
                }
              });
              return $return;
            }
          }
        });
        $this.parent().height($this.height());
        $this.on('affixed.bs.affix', function () {
          $this.parent().height($this.height());
          $this.css({
            top: function () {
              var $return = parseInt($('body').css('paddingTop'));
              $('.inv-sticky.affix').not($this).each(function () {
                if (($(this).hasClass('unsticky-mobile') && $(window).width() < 992) === false) {
                  $return = $return + $(this).parent().height();
                }
              });
              return $return;
            }
          });
        });
        setTimeout(function () {
          if ($this.hasClass('affix')) {
            $this.trigger('affixed.bs.affix');
          }
        }, 1000);
      });
    }
  };
})(jQuery, Drupal);
