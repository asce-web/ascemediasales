(function ($, Drupal, settings) {
  "use strict";
  
  Drupal.behaviors.inv_image_browser = {
    attach: function (context, settings) {
      $('.image-browser', context).find('input.image-selector').once('click').each(function(){
        $(this).click(function(e){
          e.preventDefault();
          $('.image-browser').removeClass('active');
          $(this).closest('.image-browser').addClass('active');
          Drupal.ajax({
            url: Drupal.url('inv_image_browser/image_browser'),
            dialog: {
              width: '80%',
              height: 'auto',
              title: 'Choose Image'
            },
            dialogType: 'modal'
          }).execute();
        });
      });
      $('.image-browser', context).find('input.image-remove').once('click').each(function(){
        $(this).click(function(e){
          e.preventDefault();
          $(this).closest('.image-browser').find('input.form-image-browser').val('file:0').trigger('update');
          $(this).closest('.image-browser').removeClass('has-image');
        });
      });
      $('ul.inv-image-browser-tabs').find('a:first').once('click').trigger('click');
    }
  };
})(jQuery, Drupal, drupalSettings);
