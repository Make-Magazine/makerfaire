(function ($) {

    "use strict";

    var File = function (options) {
        this.init('file', options, File.defaults);
    };

    //inherit from Abstract input
    $.fn.editableutils.inherit(File, $.fn.editabletypes.abstractinput);

    $.extend(File.prototype, {
        /**
         Renders input from tpl

         @method render()
         **/
         render: function() {
            this.$input = this.$tpl.parent();
            var field = this.options.scope;
            $( this.$input ).find( 'input' ).attr( 'multiple', $( field ).data( 'multiple' ) );
        },

        /**
         Default method to show value in element. Can be overwritten by display option.

         @method value2html(value, element)
         **/
        value2html: function(value, element) {
            if(!value) {
                $(element).empty();
                return;
            }
        },


        /**
         Returns value of input.

         @method input2value()
         **/
        input2value: function() {
            return {
                file: this.$input.filter('[name="file"]').val(),
            };
        },

        /**
         Activates input: sets focus on the first field.

         @method activate()
         **/
         activate: function() {
            var self = this;
            $(this.options.scope).editable('option', 'savenochange', true );
            $(this.options.scope).editable('option', 'ajaxOptions', {
                dataType: 'json',
                contentType: false,
                processData: false,
                type: 'POST',
                success:function(response) {
                    if ( response.success === false ) {
                        $( self.options.scope ).html( response.data[ 0 ].message );
                        return;
                    }

                    if ( response.data && response.data.output ) {
                        $( self.options.scope ).html( response.data.output );
                    }

                    if ( response.data.removed ) {
                        $( self.options.scope ).html( response.data.message );
                    }
                },
            })
            $(this.options.scope).editable('option', 'params', function(p){
                var data = new FormData();

                $.each(self.$tpl[0].files, function(i, file) { i++;
                    data.append('input_'+i, file);
                });

                data.append( 'nonce', gv_inline_x.nonce );
                data.append('action', 'gv_inline_upload_file');
                data.append('view_id', $(self.options.scope).attr('data-viewid'));
                data.append('entry_id', $(self.options.scope).attr('data-entryid'));
                data.append('form_id', $(self.options.scope).attr('data-formid'));
                data.append('field_id', $(self.options.scope).attr('data-fieldid'));

                return data;
            })

            this.$tpl.focus();
        },
        /**
         Attaches handler to submit form in case of 'showbuttons=false' mode

         @method autosubmit()
         **/
        autosubmit: function() {
            this.$input.keydown(function (e) {
                if (e.which === 13) {
                    $(this).closest('form').submit();
                }
            });
        }
    });

    File.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
        tpl: '<input type="file">',
        inputclass: null,

    });

    $.fn.editabletypes.file = File;

}(window.jQuery));
