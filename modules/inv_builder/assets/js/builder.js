(function ($, Drupal, settings) {

  Drupal.behaviors.inv_builder = {
    attach: function (context, settings) {
      // Check to enable builder
      $('select.filter-list', context).once('inv_builder_change').each(function () {
        $(this).data('previous', $(this).val());
        $(this).on('focus', function(){
          $(this).data('previous', $(this).val());
        }).on('change', function () {
          var $this = $(this);
          if ($(this).val().toString() === 'innovation_builder') {
            $(this).closest('.text-format-wrapper').addClass('inv-builder-enable');
            var format = $(this).val();
            $('.inv-builder').each(function(){
              Drupal.ajax({
                url: Drupal.url('inv_builder/parse'),
                submit: {
                  format: format,
                  text: $('[id=' + $(this).data('id') + ']').val(),
                  selector: '#' + $(this).attr('id') + ' .inv-builder-inner'
                }
              }).execute();
            });
          } else {
            if($(this).data('previous') === 'innovation_builder'){
              //Export builder to textarea
              $('.inv-builder').each(function () {
              var builder = $(this);
                try{
                  var output = Drupal.builderExport(builder.find('>.inv-builder-inner').find('.builder-element:first').parent());
                  $('#' + builder.data('id')).val(output);
                }catch(e){
                  alert('Failed');
                  console.log(e);
                  return false;
                }
              });
            }
            $(this).closest('.text-format-wrapper').removeClass('inv-builder-enable');
          }
        }).trigger('change');
      });
      
      $('.text-summary-wrapper').once('trigger').each(function(){
        var $this = $(this);
        setInterval(function(){
          if($this.is(':visible')){
            $this.next('.inv-builder').attr('css','');
          }else{
            $this.next('.inv-builder').hide();
          }
        }, 500);
      });
      
      //Add element
      $('.add-element').once('click').each(function () {
        $(this).click(function () {
          var $this = $(this);
          var builder = $(this).closest('.inv-builder');
          $('.active-element-content').removeClass('active-element-content');
          if ($(this).closest('.builder-element').length === 0) {
            if($('.inv-builder-inner', builder).find('.builder-element:first').length > 0){
              $('.inv-builder-inner', builder).find('.builder-element:first').parent().addClass('active-element-content');
            }else if($('.inv-builder-inner', builder).find('>div').length > 0){
              $('.inv-builder-inner', builder).find('>div').addClass('active-element-content');
            }else{
              $('.inv-builder-inner', builder).addClass('active-element-content');
            }
          } else {
            $(this).closest('.builder-element').find('.element-content:first').addClass('active-element-content');
          }
          Drupal.inv_builder.ajax({
            url: Drupal.url('inv_builder/shortcode_list'),
            dialog: {
              width: '80%',
            },
            dialogType: 'modal',
            progress: {
              type: "fullscreen"
            },
            selector: 'body',
            submit: {
              format: $(this).closest('.text-format-wrapper').find('select.filter-list').val(),
              action: 'add',
              text: '',
              parent: $(this).closest('.builder-element').data('shortcodeId') || ''
            }
          }).execute();
        });
      });

      //Edit element
      $('.edit-element').once('click').each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $('.builder-element').removeClass('active-element');
          var element = $(this).closest('.builder-element');
          element.addClass('active-element');
          Drupal.inv_builder.ajax({
            url: Drupal.url('inv_builder/shortcode_settings/' + element.data('shortcode-id') + '/edit'),
            dialog: {
              width: '80%'
            },
            progress: {
              type: "fullscreen"
            },
            dialogType: 'modal',
            submit: {
			  shortcode_id: element.data('shortcode-id'),
              format: $(this).closest('.text-format-wrapper').find('select.filter-list').val(),
              selector: '.element-content.active-element',
              attr: element.data('attr'),
              text: Drupal.builderExport(element.find('.element-content:first'))
            }
          }).execute();
        });
      });
      
      //Clone element
      $('.clone-element').once('click').each(function(){
        $(this).click(function(){
          var newElement = $(this).closest('.builder-element').clone();
          newElement.appendTo($(this).closest('.builder-element').parent());
          Drupal.attachBehaviors($(this).closest('.builder-element').parent().get(0), settings);
        });
      });
      
      //Delete element
      $('.delete-element').once('click').each(function(){
        $(this).click(function(){
          if(confirm(Drupal.t('Delete element?'))){
            $(this).closest('.builder-element').remove();
          }
        });
      });
      
      //Toggle element
      $('.toggle-element').once('click').each(function(){
        $(this).click(function(){
          $(this).toggleClass('fa-caret-left fa-sort-desc');
          $(this).closest('.builder-element').toggleClass('collapse');
          var attr = $(this).closest('.builder-element').data('attr');
          attr.collapse = $(this).closest('.builder-element').hasClass('collapse');
          $(this).closest('.builder-element').data('attr', attr);
        });
      });
      
      //Element sort
      $('.element-content.has-child').sortable({
        placeholder: 'inv-builder-sortable-placeholder',
        connectWith: '.element-content, .inv-builder-inner > div',
        handle: '.element-toolbar .fa-arrows',
        start: function(e, ui){
          var data = ui.item.data('attr');
          if(data.hasOwnProperty('lg')){
            ui.placeholder.addClass('col-lg-'+data.lg);
          }
          if(data.hasOwnProperty('md')){
            ui.placeholder.addClass('col-md-'+data.md);
          }
          if(data.hasOwnProperty('sm')){
            ui.placeholder.addClass('col-sm-'+data.sm);
          }
          if(data.hasOwnProperty('xs')){
            ui.placeholder.addClass('col-xs-'+data.xs);
          }
        }
      });
      $('.inv-builder-inner').find('.builder-element:first').parent().sortable({
        placeholder: 'inv-builder-sortable-placeholder',
        connectWith: '.element-content',
        handle: '.element-toolbar .fa-arrows',
        start: function(e, ui){
          var data = ui.item.data('attr');
          if(data.hasOwnProperty('lg')){
            ui.placeholder.addClass('col-lg-'+data.lg);
          }
          if(data.hasOwnProperty('md')){
            ui.placeholder.addClass('col-md-'+data.md);
          }
          if(data.hasOwnProperty('sm')){
            ui.placeholder.addClass('col-sm-'+data.sm);
          }
          if(data.hasOwnProperty('xs')){
            ui.placeholder.addClass('col-xs-'+data.xs);
          }
        }
      });
      
      //Export builder
      $('.inv-builder').once('form-submit').each(function () {
        var builder = $(this);
        builder.closest('form').on('submit', function(){
          if(builder.closest('.text-format-wrapper').hasClass('inv-builder-enable') === false)
            return true;
          if(builder.closest('.text-format-wrapper').hasClass('inv-builder-enable')){
            try{
              var output = Drupal.builderExport(builder.find('>.inv-builder-inner').find('.builder-element:first').parent());
              $('#' + builder.data('id')).val(output);
            }catch(e){
              alert('Failed');
              console.log(e);
              return false;
            }
          }
        });
      });
    }
  };

  Drupal.builderExport = function (element) {
    var output = [];
    var content = [];
    var shortcode_id = '';
    if ($(element).is('.builder-element')) {
      shortcode_id = $(element).data('shortcodeId');
      $.each($(element).data('attr'), function (key, value) {
        if (key === 'html_content') {
          var is_shortcode = false;
          try{
            is_shortcode = $(value).is('[data-shortcode-id]');
          }catch(e){
            is_shortcode = false;
          }
          if(is_shortcode === false){
            content.push(value);
          }
        } else {
          output.push(key + '=\'' + value + '\'');
        }
      });
      $(element).find('.element-content:first').find('>.builder-element').each(function (index) {
        if (index === 0) {
          content = [];
        }
        content.push(Drupal.builderExport(this));
      });
      return '[' + shortcode_id + ' ' + output.join(' ') + ']' + content.join('') + '[/' + shortcode_id + ']';
    } else {
      var result = '';
      $(element).find('>.builder-element').each(function () {
        result += Drupal.builderExport(this);
      });
      return result;
    }
  };

})(jQuery, Drupal, drupalSettings);