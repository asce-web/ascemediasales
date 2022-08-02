(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Registers behaviours related to view widget.
   */
  Drupal.behaviors.entityBrowserView = {
    attach: function (context) {
      // Add the AJAX to exposed forms.
      // We do this as the selector in core/modules/views/js/ajax_view.js
      // assumes that the form the exposed filters reside in has a
      // views-related ID, which ours does not.
      setTimeout(function () {
        if (typeof Drupal.views == 'undefined' || typeof Drupal.views.instances == 'undefined')
          return false;
      var instance = $.grep(Object.keys(Drupal.views.instances), function( instance ) {
        return (Drupal.views.instances[instance].$view.length == 1);
      })[0];
      var views_instance = Drupal.views.instances[instance];
      if (views_instance) {
        // Initialize the exposed form AJAX.
        views_instance.$exposed_form = $('div#views-exposed-form-' + views_instance.settings.view_name.replace(/_/g, '-') + '-' + views_instance.settings.view_display_id.replace(/_/g, '-'));
        views_instance.$exposed_form.once('exposed-form').each(jQuery.proxy(views_instance.attachExposedFormAjax, views_instance));

        // The form values form_id, form_token, and form_build_id will break
        // the exposed form. Remove them by splicing the end of form_values.
        if (views_instance.exposedFormAjax && views_instance.exposedFormAjax.length > 0) {
          var ajax = views_instance.exposedFormAjax[0];
          ajax.options.beforeSubmit = function (form_values, element_settings, options) {
            form_values = form_values.splice(form_values.length - 3, 3);
            ajax.ajaxing = true;
            return ajax.beforeSubmit(form_values, element_settings, options);
          };
        }

        // Handle Enter key press in the views exposed form text fields: ensure
        // that the correct button is used for the views exposed form submit.
        // The default browser behavior for the Enter key press is to click the
        // first found button. But there can be other buttons in the form, for
        // example, ones added by the Tabs widget selector plugin.
        views_instance.$exposed_form.once('submit-by-enter-key').find('input[type="text"]').each(function () {
          $(this).on('keypress', function (event) {
            if (event.keyCode == 13) {
              event.preventDefault();
              views_instance.$exposed_form.find('input[type="submit"]').first().click();
            }
          });
        });
      }
      }, 500);
    }
  };

}(jQuery, Drupal, drupalSettings));