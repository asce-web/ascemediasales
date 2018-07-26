(function($, Drupal){
  Drupal.behaviors.color_picker = {
    attach: function(){
      $('input.color').once('colorpicker').each(function(){
        var $this = $(this);
        $(this).colorPicker({
          renderCallback: function($elm, toggled) {
            if($elm.val() != ''){
              $this.val(this.color.toString('HEX'));
            }
            $this.trigger('update');
          }
        });
      });
    }
  };
})(jQuery, Drupal);