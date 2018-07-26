(function($, Drupal){
  Drupal.behaviors.color_picker = {
    attach: function(){
      $('input.color').once('colorpicker').each(function(){
        var $this = $(this);
        //var picker = $('<span class="color-picker"></span>');
        $(this).colorPicker({
          renderCallback: function($elm, toggled) {
            if($elm.val() != ''){
              $this.val(this.color.toString('HEX'));
            }
          }
        });
      });
    }
  };
})(jQuery, Drupal);