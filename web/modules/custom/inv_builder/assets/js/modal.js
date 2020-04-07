(function ($, Drupal) {
  Drupal.inv_builder = Drupal.inv_builder || {};
  Drupal.inv_builder.ajax = function (settings) {
    var dialogType = settings.dialogType || '';
    if (dialogType == 'modal') {
      settings.success = function (response, status) {
        // Remove the progress element.
        if (this.progress.element) {
          $(this.progress.element).remove();
        }
        if (this.progress.object) {
          this.progress.object.stopMonitoring();
        }
        $(this.element).prop('disabled', false);

        // Save element's ancestors tree so if the element is removed from the dom
        // we can try to refocus one of its parents. Using addBack reverse the
        // result array, meaning that index 0 is the highest parent in the hierarchy
        // in this situation it is usually a <form> element.
        var elementParents = $(this.element).parents('[data-drupal-selector]').addBack().toArray();

        // Track if any command is altering the focus so we can avoid changing the
        // focus set by the Ajax command.
        var focusChanged = false;
        for (var i in response) {
          if (response.hasOwnProperty(i) && response[i].command && this.commands[response[i].command]) {
            if (response[i].command == 'openDialog') {
              response[i].selector = this.dialog.selector || '#inv-builder-modal';
            }
            this.commands[response[i].command](this, response[i], status);
            if (response[i].command === 'invoke' && response[i].method === 'focus') {
              focusChanged = true;
            }
          }
        }

        // If the focus hasn't be changed by the ajax commands, try to refocus the
        // triggering element or one of its parents if that element does not exist
        // anymore.
        if (!focusChanged && this.element && !$(this.element).data('disable-refocus')) {
          var target = false;

          for (var n = elementParents.length - 1; !target && n > 0; n--) {
            target = document.querySelector('[data-drupal-selector="' + elementParents[n].getAttribute('data-drupal-selector') + '"]');
          }

          if (target) {
            $(target).trigger('focus');
          }
        }

        // Reattach behaviors, if they were detached in beforeSerialize(). The
        // attachBehaviors() called on the new content from processing the response
        // commands is not sufficient, because behaviors from the entire form need
        // to be reattached.
        if (this.$form) {
          var settings = this.settings || drupalSettings;
          Drupal.attachBehaviors(this.$form.get(0), settings);
        }

        // Remove any response-specific settings so they don't get used on the next
        // call by mistake.
        this.settings = null;
      };
    }
    return Drupal.ajax(settings);
  };
  
  Drupal.behaviors.inv_builder_modal = {
    attach: function (context, settings) {
      $('.inv-builder-modal').once('click').each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          Drupal.inv_builder.ajax({
            url: $(this).attr('href'),
            dialog: {
              width: '80%'
            },
            dialogType: 'modal',
            progress: {
              type: "fullscreen"
            }
          }).execute();
        });
      });
      
      $('.icon-select').once('js').each(function(){
        $('<div class="icon-selector"><span class="selected-icon"><i class="' + $(this).val() +'"></i></span><span class="selector-button"><i class="fa-arrow-down fip-fa fa"></i></span></div> <a class="remove-icon" href="#"><small>'+Drupal.t('Remove')+'</small></a>').insertAfter(this);
        $('.selector-button').click(function(e){
          e.preventDefault();
          Drupal.inv_builder.ajax({
            url: Drupal.url('inv_builder/icons'),
            dialog: {
              width: '60%',
              selector: '#inv-builder-icon-select'
            },
			submit:{
              icon_library: $('input[name=icon_library]').val()
            },
            dialogType: 'modal',
            progress: {
              type: "fullscreen"
            }
          }).execute();
        });
        $('a.remove-icon').click(function(e){
          e.preventDefault();
          $(this).closest('.form-item').find('input[name=icon]').val('');
          $(this).closest('.form-item').find('.selected-icon').html('');
        });
      });
     $('input[data-drupal-selector=edit-search]').once('keyup').each(function(){
        $(this).keyup(function(){
          var val = $.trim($(this).val());
          if(val !== ''){
            $('.icon-button').hide();
            $('.icon-button[data-class*="' + val + '"]').show();
          }else{
            $('.icon-button').show();
          }
        });
      });
      
      $('select[data-drupal-selector=edit-library]').once('change').each(function(){
        $(this).change(function(){
          $('input[data-drupal-selector=edit-search]').val('').trigger('keyup');
        });
      });

      $('.icon-button').once('click').each(function(){
        $(this).click(function(){
          $(this).closest('form').find('input[name=icon]').val($(this).data('class'));
          $(this).closest('form').find('input[type=submit]').trigger('click');
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);