(function ($, Drupal, drupalSettings) {
  Drupal.variants = {
    '100': 'Thin',
    '200': 'Extra-Light',
    '300': 'Light',
    '400': 'Regular',
    '500': 'Medium',
    '600': 'Semi-Bold',
    '700': 'Bold',
    '800': 'Extra-Bold',
    '900': 'Black'
  };

  Drupal.behaviors.inv_layerslider_font = {
    attach: function (context, settings) {
      $('input[name=google_font]').once('autocomplete').each(function () {
        $(this).autocomplete({
          source: function (request, response) {
            var result = $.grep(drupalSettings.google_fonts.items, function (e) {
              e.value = e.family;
              return e.family.toLowerCase().indexOf(request.term.toLowerCase()) !== -1;
            });
            response(result);
          },
          minLength: 1,
          select: function (event, ui) {
            $('select[name=font_style] option').remove();
            $(ui.item.variants).each(function () {
              thenum = this.replace(/[a-z]/g, '');
              alphabet = this.replace(/[0-9]/g, '');
              thenum = isNaN(parseInt(thenum)) ? 400 : parseInt(thenum);
              str = Drupal.variants[thenum.toString()] + " " + thenum.toString();
              str += alphabet === 'italic' ? ' Italic' : '';
              $('select[name=font_style]')[0].add(new Option(str, this.toString()));
            });
            setTimeout(function(){$('[name=custom_css]').trigger('keyup');},500);
          }
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);