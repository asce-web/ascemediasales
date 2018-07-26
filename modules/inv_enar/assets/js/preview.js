(function($, Drupal){
   "use strict";
    Drupal.behaviors.preview = {
        attach: function (context, settings) {
            var $predefine = settings.preview;
            $("select[name='box_style']").change(function(){
                if ($(this).val() == "") {
                    $(".preview-box-style").attr('src',"");
                } else {
                   $(".preview-box-style").attr('src',$predefine[$(this).val()]);
                }
            });

			$("select[name='title_style']").change(function(){
                if ($(this).val() == "") {
                    $(".preview-title-style").attr('src',"");
                } else {
                   $(".preview-title-style").attr('src',$predefine[$(this).val()]);
                }
            });
			
			$("select[name='icon_style']").change(function(){
                if ($(this).val() == "") {
                    $(".preview-icon-style").attr('src',"");
                } else {
                   $(".preview-icon-style").attr('src',$predefine[$(this).val()]);
                }
            });
			
			$("select[name='button_style']").change(function(){
                if ($(this).val() == "") {
                    $(".preview-button-style").attr('src',"");
                } else {
                   $(".preview-button-style").attr('src',$predefine[$(this).val()]);
                }
            });
			
			$("select[name='skillbar_style']").change(function(){
                if ($(this).val() == "") {
                    $(".preview-progress-style").attr('src',"");
                } else {
                   $(".preview-progress-style").attr('src',$predefine[$(this).val()]);
                }
            });
			$("select[name='tab_style']").change(function(){
                if ($(this).val() == "") {
                    $(".preview-tab-style").attr('src',"");
                } else {
                   $(".preview-tab-style").attr('src',$predefine[$(this).val()]);
                }
            });
        }
    };
})(jQuery, Drupal);

