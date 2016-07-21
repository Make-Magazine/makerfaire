/*
 * Ajaxupload for Jeditable
 *
 * Copyright (c) 2008-2009 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Depends on Ajax fileupload jQuery plugin by PHPLetter guys:
 *   http://www.phpletter.com/Our-Projects/AjaxFileUpload/
 *
 * Project home:
 *   http://www.appelsiini.net/projects/jeditable
 *
 * Revision: $Id$
 *
 */

jQuery.editable.addInputType('ajaxupload', {
  /* create input element */
  element : function(settings,original) {
    settings.onblur = 'ignore';
    var input = jQuery('<input type="file" id="upload" name="upload" />');
    jQuery(this).append(input);
    return(input);
  },
  content : function(string, settings, original) {
    /* do nothing */
  },
  plugin : function(settings, original) {
    var form = this;
    form.attr("enctype", "multipart/form-data");
    jQuery(this).prepend('Upload New Image:');
    jQuery("button:submit", form).bind('click', function() {
      //jQuery(".message").show();
      var buttonType = jQuery(this).attr('type');
      if(buttonType=='submit') {
        // Modification to include original id and submitdata in target's querystring
        var queryString;
        if (jQuery.isFunction(settings.submitdata)) {
          queryString = jQuery.param(settings.submitdata.apply(self, [self.revert, settings]));
        } else {
          queryString = jQuery.param(settings.submitdata);
        }
        if (settings.target.indexOf('?') < 0)	{
          queryString = '?' + settings.id + '=' + jQuery(original).attr('id') + '&' + queryString;
        } else {
          queryString = '&' + settings.id + '=' + jQuery(original).attr('id') + '&' + queryString;
        }
        // End modification
        jQuery(original).find('form').hide();
        jQuery(original).append('<i class="fa fa-spinner fa-spin fa-4x"></i>');

        jQuery.ajaxFileUpload({
          url: settings.target + queryString,
          secureuri:false,
          fileElementId: 'upload',
          dataType: 'html',
          success: function (data, status) {
            if(jQuery(original).attr('id') != "proj_img"){
              var imgclass="col-md-3 pull-left img-responsive";
            }else{
              var imgclass="img-responsive";
            }
            var imgHtml = '<img class="'+imgclass+'" src="'+data+'" />';
            jQuery(original).html(imgHtml);
            original.editing = false;
          },
          error: function (data, status, e) {
              alert(e);
          }
        });
      }
      return(false);
    });
  }
});