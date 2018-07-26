(function($, Drupal){
  Drupal.behaviors.custom_css = {
    attach: function(){
      $('.custom-css-button').once('click').each(function(){
        $(this).click(function(e){
          e.preventDefault();
          Drupal.inv_builder.ajax({
            url: Drupal.url('inv_builder/custom_css'),
            dialogType: 'modal',
            dialog: {
              width: '80%',
              selector: '#custom_css'
            }
          }).execute();
        });
      });
    }
  };
})(jQuery, Drupal);