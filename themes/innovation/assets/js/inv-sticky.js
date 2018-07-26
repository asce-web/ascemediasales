(function($) {
    var mobile_sticky = false;
    $.fn.invsticky = function(e) {
        this.each(function() {
            var b = $(this);
            b.addClass('inv-sticky');
            b.data('offset-top', b.data('offset-top') || b.offset().top);
            b.data('max-height', b.outerHeight());
            var c = $('<div>').addClass('sticky-wrapper');
            b.wrap(c);
            var d = b.parents('.sticky-wrapper');
            b.width(d.width());
            setInterval(function() {
                d.height(b.outerHeight());
                b.width(d.width());
            }, 50);
            var scrollTimeout;
            var delay = 15;
            var scrollHandler = function() {
                if(mobile_sticky == false && $(window).width() < 992){
                    return;
                }
                if ($(window).scrollTop() > b.data('offset-top')) {
                    b.addClass('fixed');
                    setTimeout(function() {
                        b.addClass('fixed-transition');
                    }, 10);
                } else {
                    b.removeClass('fixed');
                    setTimeout(function() {
                        b.removeClass('fixed-transition');
                    }, 10);
                }
            };
            $(window).bind('scroll', function() {
                if(b.hasClass('fixed')){
                    delay = 50;
                }else{
                    delay = 10;
                }
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = null;
                }
                scrollTimeout = setTimeout(scrollHandler, 0);
            }).bind('resize', function() {
                b.removeClass('fixed fixed-transition').data('offset-top', b.offset().top);
                $(window).scroll();
            }).scroll();
        });
    };
    $(window).on('load', function() {
        if(!$('body').hasClass('menu-left') && !$('body').hasClass('menu-right')){
            $('.inv-sticky').invsticky();
        }
    });
}(jQuery));
