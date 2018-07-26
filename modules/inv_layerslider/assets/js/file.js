(function ($, Drupal) {
  "use strict";
  Drupal.FileDialog = function (data) {
    console.log(data);
    Drupal.ajax({
      dialog: {
        width: 500
      },
      dialogType: 'modal',
      selector: 'body',
      url: Drupal.url('admin/content/inv_layerslider/file_upload'),
      progress: {
        type: "throbber"
      },
      submit: data
    }).execute();
  };

  Drupal.FileHandle = null;

  Drupal.behaviors.inv_layerslider_file = {
    attach: function (context) {
      $('.file-upload', context).once('FileDialog').each(function () {
        $(this).click(function () {
          Drupal.FileHandle = $(this);
          Drupal.FileDialog({});
        });
      });
      $('.form-image-browser').once('image-browser').each(function(){
        $(this).on('update', function(){
          var url = $(this).data('url') || '';
          $('input[name="' + $(this).attr('name') + '"].image-browser-js').val($(this).val());//.trigger('change');
          $('input[name="' + $(this).attr('name') + '"].image-browser-js').data('url', url).trigger('change');
        });
      });
      $('.image-browser-js').once('image-browser').each(function(){
        $(this).on('update change image-browser-updated', function(){
          var id = 'image-' + Math.round(new Date().getTime() + (Math.random() * 100));
          $(this).attr('id', id);
          $(this).next('div').remove();
          Drupal.ajax({
            url: Drupal.url('admin/content/inv_layserslider/image_browser'),
            submit: {
              name: $(this).attr('name'),
              default_value: $(this).val(),
              selector: "#" + id
            }
          }).execute();
        }).trigger('update');
      });
    }
  };

  Drupal.AjaxCommands.prototype.UpdateImage = function (ajax, response, status) {
    Drupal.FileHandle.val(response.url);
    Drupal.FileHandle.data({fid: response.fid});
    Drupal.FileHandle.trigger('change');
  };
})(jQuery, Drupal);