(function($){
    function SbspfAdmin(plugin,$adminEl) {
        this.plugin = plugin;
        this.$adminEl = $adminEl;
        this.accesstokenSplitter = 'access_token=';
    }

    SbspfAdmin.prototype = {
        init: function() {
            var self = this,
                id = '#'+this.plugin,
                cla = '.'+this.plugin;
            this.addAccessTokenListener();
            $('.'+this.plugin +'_connected_accounts_wrap .'+this.plugin +'_connected_account').each(function() {
                self.initClickRemove($(this));
                self.initInfoToggle($('.'+self.plugin +'_connected_accounts_wrap').last());
            });
            this.$adminEl.find('.sbspf_type_input').change(function() {
                self.updateOnSelect($(this));
            });self.updateOnSelect();


            self.initAppCredToggle();

            self.initWidthResponsiveToggle();
            self.initActionButtons();

            this.addManualAccessTokenListener();

            $(id + '_search_submit').click(function(event) {
                event.preventDefault();

                var submitData = {
                    'term' : $(id + '_channel_search').val(),
                    'action' : self.plugin + '_account_search',
                    'sbspf_nonce' : sbspf.nonce
                };
                var onSuccess = function (data) {
                    if (data.trim().indexOf('{') === 0) {
                        var returnObj = JSON.parse(data.trim());

                        var html = '';
                        $.each(returnObj.items,function(index,value){
                        });
                    }
                };
                sbAjax(submitData,onSuccess);
            });

            // color picker
            var $ctfColorpicker = $(cla+'_colorpicker');

            if($ctfColorpicker.length > 0){
                $ctfColorpicker.wpColorPicker();
            }

            // shortcode tooltips
            var $adminLabel = $(id +'_admin label');

            $adminLabel.click(function(){
                var $shortcode = $(this).siblings(cla + '_shortcode');
                if($shortcode.is(':visible')){
                    $shortcode.hide();
                } else {
                    $shortcode.show();
                }
            });
            $adminLabel.hover(function(){
                if($(this).siblings(cla + '_shortcode').length && ! $(this).find(cla + '_shortcode_symbol').length){
                    $(this).append('<code class="'+self.plugin+'_shortcode_symbol">[]</code>');
                }
            }, function(){
                $(this).find(cla + '_shortcode_symbol').remove();
            });
            $(cla + '_shortcode').hide();

            //Scroll to hash for quick links
            $(id + '_admin a').click(function() {
                if(location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : this.hash.slice(1);
                    if(target.length) {
                        $('html,body').animate({
                            scrollTop: target.offset().top
                        }, 500);
                        return false;
                    }
                }
            });

            //Caching options
            if( $(id+'_caching_type_page').is(':checked') ) {
                $(cla+'-caching-cron-options').hide();
                $(cla+'-caching-page-options').show();
            } else {
                $(cla+'-caching-page-options').hide();
                $(cla+'-caching-cron-options').show();
            }

            $('.'+self.plugin+'_caching_type_input').change(function() {
                if (this.value == 'page') {
                    $(cla+'-caching-cron-options').slideUp();
                    $(cla+'-caching-page-options').slideDown();
                }
                else if (this.value == 'background') {
                    $(cla+'-caching-page-options').slideUp();
                    $(cla+'-caching-cron-options').slideDown();
                }
            });

            //Should we show the caching time settings?
            var sbspf_cache_cron_interval = $(id+'_cache_cron_interval').val(),
                $sbspf_caching_time_settings = $(id+'-caching-time-settings');

            //Should we show anything initially?
            if(sbspf_cache_cron_interval == '30mins' || sbspf_cache_cron_interval == '1hour') $sbspf_caching_time_settings.hide();

            $(id+'_cache_cron_interval').change(function(){
                sbspf_cache_cron_interval = $(id+'_cache_cron_interval').val();

                if(sbspf_cache_cron_interval == '30mins' || sbspf_cache_cron_interval == '1hour'){
                    $sbspf_caching_time_settings.hide();
                } else {
                    $sbspf_caching_time_settings.show();
                }
            });
            sbspf_cache_cron_interval = $(id+'_cache_cron_interval').val();

            if(sbspf_cache_cron_interval == '30mins' || sbspf_cache_cron_interval == '1hour'){
                $sbspf_caching_time_settings.hide();
            } else {
                $sbspf_caching_time_settings.show();
            }

            self.updateLayoutOptionsDisplay();
            $(cla + '_layout_type').change(function() {
                self.updateLayoutOptionsDisplay()
            });
            $(cla + '_sub_option_type').change(function() {
                self.updateBoxSelectionDisplay()
            });
            self.updateBoxSelectionDisplay();

            // tooltips
            $(id +'_admin '+ cla + '_tooltip_link').click(function(){
                $(this).closest('tr, h3, '+ cla + '_tooltip_wrap').find(cla + '_tooltip').slideToggle();
            });

            $(id +'_admin '+ cla + '_type_tooltip_link').click(function(){
                $(this).closest(cla + '_row').find(cla + '_tooltip').slideToggle();
            });

            //Mobile width
            var $feedWidth = $(id+'_admin '+id+'_settings_width'),
                $widthUnit = $(id+'_admin '+id+'_settings_width_unit');
            if ($feedWidth.length) {
                $feedWidth.change(function(){
                    self.updateFeedWidthDisplay();
                });
                $widthUnit.change(function(){
                    self.updateFeedWidthDisplay();
                });
                self.updateFeedWidthDisplay();
            }

            this.afterInit();
        },
        afterInit: function() {

        },
        addAccessTokenListener: function() {
            var self = this;
            if (window.location.hash.length > 5 && window.location.hash.indexOf(this.accesstokenSplitter) > -1) {
                var accessToken = window.location.hash.split(this.accesstokenSplitter);
                // clear access token from hash
                window.location.hash = '';
                var submitData = {
                    'access_token' : accessToken[1],
                    'action' : this.plugin + '_process_access_token',
                    'sbspf_nonce' : sbspf.nonce
                };
                var onSuccess = function (data) {
                    if (data.trim().indexOf('{') === 0) {
                        var returnObj = JSON.parse(data.trim());
                        $('.'+self.plugin +'_connected_accounts_wrap').prepend(returnObj.html);
                        self.initClickRemove($('.'+self.plugin +'_connected_accounts_wrap').last());
                        self.initInfoToggle($('.'+self.plugin +'_connected_accounts_wrap').last());
                    }
                };
                sbAjax(submitData,onSuccess);
            }
        },
        initClickRemove: function(el) {
            var self = this;
            el.find('.'+this.plugin +'_delete_account').click(function() {
                if (!$(this).closest('.'+self.plugin +'_connected_accounts_wrap').hasClass(self.plugin +'-waiting')) {
                    $(this).closest('.'+self.plugin +'_connected_accounts_wrap').addClass(self.plugin +'-waiting');
                    var $connectedAccount = $(this).closest('.'+self.plugin +'_connected_account'),
                        accountID = $connectedAccount.attr('data-userid');

                    if (window.confirm("Delete this connected account?")) {
                        $('#'+self.plugin +'_user_feed_id_' + accountID).remove();
                        $('#'+self.plugin +'_connected_account_' + accountID).append('<div class="spinner" style="margin-top: -10px;visibility: visible;top: 50%;position: absolute;right: 50%;"></div>').find('.'+self.plugin +'_ca_info').css('opacity','.5');

                        var submitData = {
                            'account_id' : accountID,
                            'action' : self.getAction( 'ca_after_remove_clicked' ),
                            'sbspf_nonce' : sbspf.nonce
                        };
                        var onSuccess = function (data) {
                            if (data.trim().indexOf('{') === 0) {
                                var returnObj = JSON.parse(data.trim());
                                $('.'+self.plugin +'-waiting').removeClass(self.plugin +'-waiting');
                                $connectedAccount.fadeOut(300, function() { $(this).remove(); });
                                self.afterConnectedAccountRemoved(accountID);
                            }
                        };
                        sbAjax(submitData,onSuccess);
                    } else {
                        $('.'+self.plugin +'-waiting').removeClass(self.plugin +'-waiting');
                    }
                }

            });
        },
        initInfoToggle: function(el) {
            var self = this;
            el.find('.'+self.plugin +'_ca_show_token').off().click(function() {
                $(this).closest('.'+self.plugin +'_ca_info').find('.'+self.plugin +'_ca_accesstoken').slideToggle(200);
            });

            el.find('.'+self.plugin +'_ca_token_shortcode').off().click(function() {
                $(this).closest('.'+self.plugin +'_ca_info').find('.'+self.plugin +'_ca_shortcode').slideToggle(200);
            });
        },
        initAppCredToggle: function() {
            var self = this;
            $('#'+self.plugin +'_have_own_tokens').click(function() {
                if ($(this).is(':checked')) {
                    $(this).closest('form').find('.'+self.plugin +'_own_credentials_wrap').slideDown();
                } else {
                    $(this).closest('form').find('.'+self.plugin +'_own_credentials_wrap').slideUp();
                }
            });

            if ($('#'+self.plugin +'_have_own_tokens').is(':checked')) {
                $('#'+self.plugin +'_have_own_tokens').closest('form').find('.'+self.plugin +'_own_credentials_wrap').slideDown();
            } else {
                $('#'+self.plugin +'_have_own_tokens').closest('form').find('.'+self.plugin +'_own_credentials_wrap').slideUp();
            }
        },
        initWidthResponsiveToggle: function() {
            //Mobile width
            var feedWidth = $('#sby_settings_width').length ? $('#sby_settings_width').val() : '100',
                widthUnit = $('#sby_settings_widthunit').length ? $('#sby_settings_widthunit').val() : '%',
                $widthOptions = $('#sbspf_width_options');

            if ($('#sby_settings_widthunit').length) {

                //Show initially if a width is set
                if (feedWidth !== '100' && widthUnit === '%') {
                    $widthOptions.slideDown();
                } else {
                    $widthOptions.slideUp();
                }

                $('#sby_settings_width_unit, #sby_settings_width').change(function(){
                    feedWidth = $('#sby_settings_width').length ? $('#sby_settings_width').val() : '100';
                    widthUnit = $('#sby_settings_widthunit').length ? $('#sby_settings_widthunit').val() : '%';

                    if (feedWidth !== '100' && widthUnit === '%') {
                        $widthOptions.slideDown();
                    } else {
                        $widthOptions.slideUp();
                    }

                });

            }
        },
        initActionButtons: function() {
            $('#sbspf_admin .sbspf-button-action').each(function(){
                $(this).click(function() {
                    event.preventDefault();
                    $(this).next('.sbspf_success').remove();

                    var doAction = typeof $(this).attr('data-sby-action') !== 'undefined' ? $(this).attr('data-sby-action') : '',
                        confirmMessage = typeof $(this).attr('data-sby-confirm') !== 'undefined' ? $(this).attr('data-sby-confirm') : false,
                        $targetWaitingEl = typeof $(this).attr('data-sby-waiter') !== 'undefined' ? $($(this).attr('data-sby-waiter')) : $(this),
                        $self = $(this);
                    if (!confirmMessage || window.confirm(confirmMessage)) {
                        $(this).attr('disabled',true);
                        $targetWaitingEl.after('<div class="spinner sbspf_spinner" style="display:inline-block;visibility: visible;"></div>');

                        var submitData = {
                            'action' : doAction,
                            'sbspf_nonce' : sbspf.nonce
                        };
                        var onSuccess = function (data) {
                            $self.removeAttr('disabled');
                            $targetWaitingEl.next('.spinner').fadeOut('slow',function(){
                                $targetWaitingEl.after('<span class="sbspf_success"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-check-circle fa-w-16"><path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" class=""></path></svg></span>');
                            });

                            if (data.trim().indexOf('{') === 0) {
                                var returnObj = JSON.parse(data.trim());
                                console.log(returnObj);
                            }
                        };
                        sbAjax(submitData,onSuccess);
                    } else {
                        $('.'+self.plugin +'-waiting').removeClass(self.plugin +'-waiting');
                    }
                });
            });
        },
        getAction(action) {
            return self.plugin + '_' + action
        },
        addManualAccessTokenListener: function() {
            var self = this,
                id = '#'+this.plugin,
                cla = '.'+this.plugin;

            $(cla+'_manually_connect_wrap').hide();
            $(cla+'_manually_connect').click(function(event) {
                event.preventDefault();
                if ( $(cla+'_manually_connect_wrap').is(':visible') ) {
                    $(cla+'_manually_connect_wrap').slideUp(200);
                } else {
                    $(cla+'_manually_connect_wrap').slideDown(200);
                    $(id+'_manual_at').focus();
                }
            });

            $(id+'_manual_submit').click(function(event) {
                event.preventDefault();
                var $self = $(this);
                var accessToken = $(id+'_manual_at').val(),
                    error = false;

                if (accessToken.length < 15) {
                    if (!$(cla+'_manually_connect_wrap').find(cla+'_user_id_error').length) {
                        $(cla+'_manually_connect_wrap').show().prepend('<div class="'+self.plugin+'_user_id_error" style="display:block;">Please enter a valid access token</div>');
                    }
                } else if (! error) {
                    $(this).attr('disabled',true);
                    $(this).closest(cla+'_manually_connect_wrap').fadeOut();
                    $(cla+'_connected_accounts_wrap').fadeTo("slow" , 0.5).find(cla+'_user_id_error').remove();

                    var submitData = {
                        'access_token' : accessToken,
                        'action' : self.plugin + '_process_access_token',
                        'sbspf_nonce' : sbspf.nonce
                    };
                    var onSuccess = function (data) {
                        $(cla+'_connected_accounts_wrap').fadeTo("slow" , 1);
                        $self.removeAttr('disabled');
                        var returnObj = JSON.parse(data.trim());
                        $('.'+self.plugin +'_connected_accounts_wrap').prepend(returnObj.html);
                        self.initClickRemove($('.'+self.plugin +'_connected_accounts_wrap').last());
                        self.initInfoToggle($('.'+self.plugin +'_connected_accounts_wrap').last());
                    };
                    sbAjax(submitData,onSuccess);
                }

            });
        },
        afterConnectedAccountRemoved: function(accountID) {

        },
        updateLayoutOptionsDisplay: function() {
            self = this;
            setTimeout(function(){
                $('.'+self.plugin+'_layout_settings').hide();
                $('.'+self.plugin+'_layout_settings.'+self.plugin+'_layout_type_'+$('.'+self.plugin+'_layout_type:checked').val()).show();
            }, 1);
        },
        updateBoxSelectionDisplay: function() {
            self = this;
            setTimeout(function(){
                $('.'+self.plugin+'_sub_option_settings').hide();
                $('.'+self.plugin+'_sub_option_settings.'+self.plugin+'_sub_option_type_'+$('.'+self.plugin+'_sub_option_type:checked').val()).show();
            }, 1);
        },
        updateFeedWidthDisplay: function() {
            self = this;
            var sbspfFeedWidth = $('#'+self.plugin+'_admin '+'#'+self.plugin+'_settings_width').val(),
                sbspfWidthUnit = $('#'+self.plugin+'_admin '+'#'+self.plugin+'_settings_width_unit').val(),
                $sbspfWidthOptions = $('#'+self.plugin+'_admin '+'#'+self.plugin+'_width_options');

            if( sbspfFeedWidth.length < 2 || (sbspfFeedWidth == '100' && sbspfWidthUnit == '%') ) {
                $sbspfWidthOptions.slideUp();
            } else {
                $sbspfWidthOptions.slideDown();
            }
        },
        updateOnSelect: function($changed) {
            this.$adminEl.find('.sbspf_type_input').each(function() {
                if ($(this).is(':checked')) {
                    $(this).closest('.sbspf_type_row').find('.sbspf_onselect').show();
                } else {
                    $(this).closest('.sbspf_type_row').find('.sbspf_onselect').hide();
                }
            });
        },
        encodeHTML: function(raw) {
            // make sure passed variable is defined
            if (typeof raw === 'undefined') {
                return '';
            }
            // replace greater than and less than symbols with html entity to disallow html in comments
            var encoded = raw.replace(/(>)/g,'&gt;'),
                encoded = encoded.replace(/(<)/g,'&lt;');
            encoded = encoded.replace(/(&lt;br\/&gt;)/g,'<br>');
            encoded = encoded.replace(/(&lt;br&gt;)/g,'<br>');

            return encoded;
        },
    };

    window.sbspf_admin_init = function() {
        var plugin = typeof $('.sbspf-admin').attr('data-sb-plugin') !== 'undefined' ? $('.sbspf-admin').attr('data-sb-plugin') : 'sbspf',
            $adminEl = $('#sbspf_admin.sby_admin');
        window.sb = new SbspfAdmin(plugin,$adminEl);
        window.sb.init();
    };

    function sbAjax(submitData,onSuccess) {
        $.ajax({
            url: sbspf.ajaxUrl,
            type: 'post',
            data: submitData,
            success: onSuccess
        });
    }

    function SbYoutubeAdmin(plugin,$adminEl) {
        SbspfAdmin.call(this, plugin,$adminEl);
        this.afterInit = function() {
            var self = this,
                id = '#'+this.plugin,
                cla = '.'+this.plugin;

            $('#sbspf_usecustomsearch').change(function() {
                if ($(this).is(':checked')) {
                    $('#sbspf_usecustomsearch_reveal').show();
                } else {
                    $('#sbspf_usecustomsearch_reveal').hide();
                }
            });

            if ($('#sbspf_usecustomsearch').is(':checked')) {
                $('#sbspf_usecustomsearch_reveal').show();
            } else {
                $('#sbspf_usecustomsearch_reveal').hide();
            }

            $('#sby_api_key').change(function() {
                self.toggleAPIKeyWarnings();
            });this.toggleAPIKeyWarnings();

            this.toggleAccessTokenDisclaimer();

            $('#sbsw_settings_dateformat').on('input',function() {
                self.toggleCustomDateField();
            });this.toggleCustomDateField();

            $('.sbspf_dismiss_button').click(function() {
                event.preventDefault();
                $('#sbspf_modal_overlay').remove();
                var submitData = {
                    'action' : $(this).attr('data-action')
                };
                sbAjax(submitData,function() {});
            });

            $('.sbspf_dismiss_at_warning_button').click(function() {
                event.preventDefault();
                $('#sbspf_modal_overlay').remove();
                var submitData = {
                    'action' : $(this).attr('data-action')
                };
                sbAjax(submitData,function() {});
            });

            //

            $('.sby_api_key_needed').each(function() {
                $(this).find('label').append('<span class="sby_api_key_needed_message"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="key" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-key fa-w-16"><path fill="currentColor" d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z" class=""></path></svg> API Key Needed</span>');
            });

            if (typeof $('#sbspf_get_token').attr('data-show-warning') !== 'undefined') {
                $('#sbspf_get_token').click(function(event) {
                    event.preventDefault();
                    var html = self.getModal();
                    $('#sbspf_admin').append(html);
                    $('#sbspf_admin').find('.sbspf_modal_close').click(function() {
                        $('#sbspf_admin').find('#sbspf_modal_overlay').remove();
                    });

                    var submitData = {
                        'action' : 'sby_dismiss_connect_warning_button'
                    };
                    sbAjax(submitData,function() {});
                })
            }

        };
        
        this.getModal = function () {

            var modal = '<div id="sbspf_modal_overlay">' +
            '<div class="sbspf_modal">' +
            '<div class="sbspf_modal_message">' +
            '            <div class="sby_before_connection">' +
            '                <p>The Feed for YouTube plugin requires "read only" access to your YouTube account in order to retrieve data from the YouTube API.</p>' +
            '                <p><strong>Please note:</strong> This plugin and the permissions granted to it through your access token cannot be used to edit or write to your YouTube account in any way.</p>' +
            '                <p class="sbspf_submit">' +
            '                    <a href="'+$('#sbspf_get_token').attr('href')+'" class="button button-secondary sbspf_dismiss_connect_warning_button" data-action="sby_dismiss_connect_warning_notice">Continue</a>' +
            '                </p>' +
            '                <a href="JavaScript:void(0);" class="sbspf_modal_close sbspf_dismiss_connect_warning_button" data-action="sby_dismiss_connect_warning_notice"><i class="fa fa-times"></i></a>' +
            '' +
            '            </div>' +
            '</div>' +
            '' +
            '</div>' +
            '</div>';

            return modal;
        };

        this.toggleCustomDateField = function() {
            if ($('#sbsw_settings_dateformat').val() === 'custom') {
                $('.sbsw_relativetext_wrap').slideUp();
                $('.sbsw_customdate_wrap').slideDown();

            } else {
                $('.sbsw_relativetext_wrap').slideDown();
                $('.sbsw_customdate_wrap').slideUp();
            }
        };

        this.toggleAPIKeyWarnings = function() {
            if ($('#sby_api_key').val() !== '') {
                if ($('.sby_disabled_wrap').length) {
                    var $closestTD = $('.sbspf_row.sbspf_type_row').first().closest('td');
                    $('.sbspf_type_row').each(function() {
                        if ($(this).find('.sbspf_type_input').attr('value') !== 'channel') {
                            $closestTD.append($(this));
                            $(this).find('input').removeAttr('disabled');
                        }
                    });
                    $('.sby_disabled_wrap').remove();
                }


            } else if (!$('.sby_disabled_wrap').length) {
                var $closestTD = $('.sbspf_row.sbspf_type_row').first().closest('td');
                $closestTD.append('<div class="sby_disabled_wrap sbspf_fade"><div class="sbspf_lock"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="key" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-key fa-w-16"><path fill="currentColor" d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z" class=""></path></svg>API Key Needed</div></div>');

                $('.sbspf_type_row').each(function() {
                    if ($(this).find('.sbspf_type_input').attr('value') !== 'channel') {
                        $('.sby_disabled_wrap').append($(this));
                        $(this).find('input').attr('disabled',true);
                    } else {
                        $(this).find('input').removeAttr('disabled');
                    }
                });
            }

        };

        this.toggleAccessTokenDisclaimer = function () {
            var self = this;
            if ($('.sby_account_just_added').length) {
                $('.sby_api_needed').remove();
                $('.sby_after_connection').show();
            } else {
                $('.sby_after_connection').remove();
                $('.sby_api_needed').show();
            }
        };

        this.addAccessTokenListener = function () {
            var self = this;
            if (window.location.hash.length > 5 && window.location.hash.indexOf(this.accesstokenSplitter) > -1) {
                var accessToken = window.location.hash.split(this.accesstokenSplitter);
                // clear access token from hash
                window.location.hash = '';
                var submitData = {
                    'access_token' : accessToken[1],
                    'action' : 'sby_process_access_token',
                    'sbspf_nonce' : sbspf.nonce
                };
                var onSuccess = function (data) {
                    if (data.trim().indexOf('{') === 0) {
                        var returnObj = JSON.parse(data.trim());
                        $('.'+self.plugin +'_connected_accounts_wrap').prepend(returnObj.html);
                        self.initClickRemove($('.'+self.plugin +'_connected_accounts_wrap').last());
                        self.initInfoToggle($('.'+self.plugin +'_connected_accounts_wrap').last());
                    }
                };
                sbAjax(submitData,onSuccess);
            }
        };

        this.addManualAccessTokenListener = function() {
            var self = this,
                id = '#'+this.plugin,
                cla = '.'+this.plugin;

            $(cla+'_manually_connect_wrap').hide();
            $(cla+'_manually_connect').click(function(event) {
                event.preventDefault();
                if ( $(cla+'_manually_connect_wrap').is(':visible') ) {
                    $(cla+'_manually_connect_wrap').slideUp(200);
                } else {
                    $(cla+'_manually_connect_wrap').slideDown(200);
                    $(id+'_manual_at').focus();
                }
            });

            $(id+'_manual_submit').click(function(event) {
                event.preventDefault();
                var $self = $(this);
                var accessToken = $(id+'_manual_at').val(),
                    refreshToken = $(id+'_manual_rt').val(),
                    error = false;

                if (accessToken.length < 15) {
                    if (!$(cla+'_manually_connect_wrap').find(cla+'_user_id_error').length) {
                        $(cla+'_manually_connect_wrap').show().prepend('<div class="'+self.plugin+'_user_id_error" style="display:block;">Please enter a valid access token</div>');
                    }
                } else if (! error) {
                    $(this).attr('disabled',true);
                    $(this).closest(cla+'_manually_connect_wrap').fadeOut();
                    $(cla+'_connected_accounts_wrap').fadeTo("slow" , 0.5).find(cla+'_user_id_error').remove();

                    var submitData = {
                        'sby_access_token' : accessToken,
                        'sby_refresh_token' : refreshToken,
                        'action' : 'sby_process_access_token',
                        'sbspf_nonce' : sbspf.nonce
                    };
                    var onSuccess = function (data) {
                        $(cla+'_connected_accounts_wrap').fadeTo("slow" , 1);
                        $self.removeAttr('disabled');
                        if (data.trim().indexOf('{') === 0) {
                            var returnObj = JSON.parse(data.trim());
                            if (typeof returnObj.error === 'undefined') {
                                if (!$('#sbspf_connected_account_'+returnObj.account_id).length) {
                                    $('.'+self.plugin +'_connected_accounts_wrap').prepend(returnObj.html);
                                    self.initClickRemove($('.'+self.plugin +'_connected_accounts_wrap').last());
                                    self.initInfoToggle($('.'+self.plugin +'_connected_accounts_wrap').last());
                                } else {
                                    $('#sbspf_connected_account_'+returnObj.account_id).replaceWith(returnObj.html);
                                    self.initClickRemove($('#sbspf_connected_account_'+returnObj.account_id));
                                    self.initInfoToggle($('#sbspf_connected_account_'+returnObj.account_id));
                                }
                            } else {
                                alert(returnObj.error);
                            }
                        }

                        self.toggleAccessTokenDisclaimer();
                        $('.sbspf_dismiss_at_warning_button').click(function() {
                            event.preventDefault();
                            $('#sbspf_modal_overlay').remove();
                            var submitData = {
                                'action' : $(this).attr('data-action')
                            };
                            sbAjax(submitData,function() {});
                        });

                    };
                    sbAjax(submitData,onSuccess);
                }

            });
        };
        this.getAction = function(action) {
            return 'sby_' + action;
        };

    }

    SbYoutubeAdmin.prototype = Object.create(SbspfAdmin.prototype);


    function SbYoutubeAdmin(plugin,$adminEl) {
        SbspfAdmin.call(this, plugin,$adminEl);
        this.afterInit = function() {
            var self = this,
                id = '#'+this.plugin,
                cla = '.'+this.plugin;

            $('#sbspf_usecustomsearch').change(function() {
                if ($(this).is(':checked')) {
                    $('#sbspf_usecustomsearch_reveal').show();
                } else {
                    $('#sbspf_usecustomsearch_reveal').hide();
                }
            });

            if ($('#sbspf_usecustomsearch').is(':checked')) {
                $('#sbspf_usecustomsearch_reveal').show();
            } else {
                $('#sbspf_usecustomsearch_reveal').hide();
            }

            $('#sby_api_key').change(function() {
                self.toggleAPIKeyWarnings();
            });this.toggleAPIKeyWarnings();

            this.toggleAccessTokenDisclaimer();

            $('#sbsw_settings_dateformat').on('input',function() {
                self.toggleCustomDateField();
            });this.toggleCustomDateField();

            $('.sbspf_dismiss_button').click(function() {
                event.preventDefault();
                $('#sbspf_modal_overlay').remove();
                var submitData = {
                    'action' : $(this).attr('data-action')
                };
                sbAjax(submitData,function() {});
            });

            $('.sbspf_dismiss_at_warning_button').click(function() {
                event.preventDefault();
                $('#sbspf_modal_overlay').remove();
                var submitData = {
                    'action' : $(this).attr('data-action')
                };
                sbAjax(submitData,function() {});
            });

            //

            $('.sby_api_key_needed').each(function() {
                $(this).find('label').append('<span class="sby_api_key_needed_message"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="key" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-key fa-w-16"><path fill="currentColor" d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z" class=""></path></svg> API Key Needed</span>');
            });

            if (typeof $('#sbspf_get_token').attr('data-show-warning') !== 'undefined') {
                $('#sbspf_get_token').click(function(event) {
                    event.preventDefault();
                    var html = self.getModal();
                    $('#sbspf_admin').append(html);
                    $('#sbspf_admin').find('.sbspf_modal_close').click(function() {
                        $('#sbspf_admin').find('#sbspf_modal_overlay').remove();
                    });

                    var submitData = {
                        'action' : 'sby_dismiss_connect_warning_button'
                    };
                    sbAjax(submitData,function() {});
                })
            }

        };

        this.getModal = function () {

            var modal = '<div id="sbspf_modal_overlay">' +
                '<div class="sbspf_modal">' +
                '<div class="sbspf_modal_message">' +
                '            <div class="sby_before_connection">' +
                '                <p>The Feed for YouTube plugin requires "read only" access to your YouTube account in order to retrieve data from the YouTube API.</p>' +
                '                <p><strong>Please note:</strong> This plugin and the permissions granted to it through your access token cannot be used to edit or write to your YouTube account in any way.</p>' +
                '                <p class="sbspf_submit">' +
                '                    <a href="'+$('#sbspf_get_token').attr('href')+'" class="button button-secondary sbspf_dismiss_connect_warning_button" data-action="sby_dismiss_connect_warning_notice">Continue</a>' +
                '                </p>' +
                '                <a href="JavaScript:void(0);" class="sbspf_modal_close sbspf_dismiss_connect_warning_button" data-action="sby_dismiss_connect_warning_notice"><i class="fa fa-times"></i></a>' +
                '' +
                '            </div>' +
                '</div>' +
                '' +
                '</div>' +
                '</div>';

            return modal;
        };

        this.toggleCustomDateField = function() {
            if ($('#sbsw_settings_dateformat').val() === 'custom') {
                $('.sbsw_relativetext_wrap').slideUp();
                $('.sbsw_customdate_wrap').slideDown();

            } else {
                $('.sbsw_relativetext_wrap').slideDown();
                $('.sbsw_customdate_wrap').slideUp();
            }
        };

        this.toggleAPIKeyWarnings = function() {
            if ($('#sby_api_key').val() !== '') {
                if ($('.sby_disabled_wrap').length) {
                    var $closestTD = $('.sbspf_row.sbspf_type_row').first().closest('td');
                    $('.sbspf_type_row').each(function() {
                        if ($(this).find('.sbspf_type_input').attr('value') !== 'channel') {
                            $closestTD.append($(this));
                            $(this).find('input').removeAttr('disabled');
                        }
                    });
                    $('.sby_disabled_wrap').remove();
                }


            } else if (!$('.sby_disabled_wrap').length) {
                var $closestTD = $('.sbspf_row.sbspf_type_row').first().closest('td');
                $closestTD.append('<div class="sby_disabled_wrap sbspf_fade"><div class="sbspf_lock"><svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="key" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-key fa-w-16"><path fill="currentColor" d="M512 176.001C512 273.203 433.202 352 336 352c-11.22 0-22.19-1.062-32.827-3.069l-24.012 27.014A23.999 23.999 0 0 1 261.223 384H224v40c0 13.255-10.745 24-24 24h-40v40c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-78.059c0-6.365 2.529-12.47 7.029-16.971l161.802-161.802C163.108 213.814 160 195.271 160 176 160 78.798 238.797.001 335.999 0 433.488-.001 512 78.511 512 176.001zM336 128c0 26.51 21.49 48 48 48s48-21.49 48-48-21.49-48-48-48-48 21.49-48 48z" class=""></path></svg>API Key Needed</div></div>');

                $('.sbspf_type_row').each(function() {
                    if ($(this).find('.sbspf_type_input').attr('value') !== 'channel') {
                        $('.sby_disabled_wrap').append($(this));
                        $(this).find('input').attr('disabled',true);
                    } else {
                        $(this).find('input').removeAttr('disabled');
                    }
                });
            }

        };

        this.toggleAccessTokenDisclaimer = function () {
            var self = this;
            if ($('.sby_account_just_added').length) {
                $('.sby_api_needed').remove();
                $('.sby_after_connection').show();
            } else {
                $('.sby_after_connection').remove();
                $('.sby_api_needed').show();
            }
        };

        this.addAccessTokenListener = function () {
            var self = this;
            if (window.location.hash.length > 5 && window.location.hash.indexOf(this.accesstokenSplitter) > -1) {
                var accessToken = window.location.hash.split(this.accesstokenSplitter);
                // clear access token from hash
                window.location.hash = '';
                var submitData = {
                    'access_token' : accessToken[1],
                    'action' : 'sby_process_access_token',
                    'sbspf_nonce' : sbspf.nonce
                };
                var onSuccess = function (data) {
                    if (data.trim().indexOf('{') === 0) {
                        var returnObj = JSON.parse(data.trim());
                        $('.'+self.plugin +'_connected_accounts_wrap').prepend(returnObj.html);
                        self.initClickRemove($('.'+self.plugin +'_connected_accounts_wrap').last());
                        self.initInfoToggle($('.'+self.plugin +'_connected_accounts_wrap').last());
                    }
                };
                sbAjax(submitData,onSuccess);
            }
        };

        this.addManualAccessTokenListener = function() {
            var self = this,
                id = '#'+this.plugin,
                cla = '.'+this.plugin;

            $(cla+'_manually_connect_wrap').hide();
            $(cla+'_manually_connect').click(function(event) {
                event.preventDefault();
                if ( $(cla+'_manually_connect_wrap').is(':visible') ) {
                    $(cla+'_manually_connect_wrap').slideUp(200);
                } else {
                    $(cla+'_manually_connect_wrap').slideDown(200);
                    $(id+'_manual_at').focus();
                }
            });

            $(id+'_manual_submit').click(function(event) {
                event.preventDefault();
                var $self = $(this);
                var accessToken = $(id+'_manual_at').val(),
                    refreshToken = $(id+'_manual_rt').val(),
                    error = false;

                if (accessToken.length < 15) {
                    if (!$(cla+'_manually_connect_wrap').find(cla+'_user_id_error').length) {
                        $(cla+'_manually_connect_wrap').show().prepend('<div class="'+self.plugin+'_user_id_error" style="display:block;">Please enter a valid access token</div>');
                    }
                } else if (! error) {
                    $(this).attr('disabled',true);
                    $(this).closest(cla+'_manually_connect_wrap').fadeOut();
                    $(cla+'_connected_accounts_wrap').fadeTo("slow" , 0.5).find(cla+'_user_id_error').remove();

                    var submitData = {
                        'sby_access_token' : accessToken,
                        'sby_refresh_token' : refreshToken,
                        'action' : 'sby_process_access_token',
                        'sbspf_nonce' : sbspf.nonce
                    };
                    var onSuccess = function (data) {
                        $(cla+'_connected_accounts_wrap').fadeTo("slow" , 1);
                        $self.removeAttr('disabled');
                        if (data.trim().indexOf('{') === 0) {
                            var returnObj = JSON.parse(data.trim());
                            if (typeof returnObj.error === 'undefined') {
                                if (!$('#sbspf_connected_account_'+returnObj.account_id).length) {
                                    $('.'+self.plugin +'_connected_accounts_wrap').prepend(returnObj.html);
                                    self.initClickRemove($('.'+self.plugin +'_connected_accounts_wrap').last());
                                    self.initInfoToggle($('.'+self.plugin +'_connected_accounts_wrap').last());
                                } else {
                                    $('#sbspf_connected_account_'+returnObj.account_id).replaceWith(returnObj.html);
                                    self.initClickRemove($('#sbspf_connected_account_'+returnObj.account_id));
                                    self.initInfoToggle($('#sbspf_connected_account_'+returnObj.account_id));
                                }
                            } else {
                                alert(returnObj.error);
                            }
                        }

                        self.toggleAccessTokenDisclaimer();
                        $('.sbspf_dismiss_at_warning_button').click(function() {
                            event.preventDefault();
                            $('#sbspf_modal_overlay').remove();
                            var submitData = {
                                'action' : $(this).attr('data-action')
                            };
                            sbAjax(submitData,function() {});
                        });

                    };
                    sbAjax(submitData,onSuccess);
                }

            });
        };
        this.getAction = function(action) {
            return 'sby_' + action;
        };

    }

    function SWshortcodeManager() {}
    SWshortcodeManager.prototype = {
        state: {},
        init: function () {
            this.state = JSON.parse($('#sbsw-account-json').attr('data-json'));
            this.updateShortcode();
            this.initListeners();
            this.updatePluginSections();
        },
        initListeners: function() {
            var manager = this;


            $('.sbsw-default-feed-wrap').each(function() {
                var $pluginWrap = $(this),
                    plugin = $(this).attr('data-plugin');

                $pluginWrap.find('.sbsw-add-remove-plugin').click(function() {
                    if ($(this).hasClass('sbsw-exclude')) {
                        $(this).add($pluginWrap).removeClass('sbsw-exclude');
                        manager.state[plugin].exclude = false;
                        $(this).html(sbspf.remove_text);

                    } else {
                        $(this).add($pluginWrap).addClass('sbsw-exclude');
                        $(this).html(sbspf.add_text);
                        manager.state[plugin].exclude = true;
                    }
                    manager.updateShortcode();

                });

                $pluginWrap.find('.sbsw-type-select').change(function() {
                    if (typeof manager.state[plugin] !== 'undefined') {
                        var pluginData = manager.state[plugin];

                        pluginData.current.type = $(this).val();
                        var type = pluginData.current.type,
                            type = type === 'channel' ? 'channels' : type,
                            inputType = pluginData.available_types[type].input;

                        if (inputType === 'connected') {
                            var selected = [];
                            $pluginWrap.find('.sbsw-selected').each(function() {
                                selected.push($(this).attr('data-user'));
                            });
                            pluginData.current.term = selected.join(',');
                        } else {
                            pluginData.current.term = $pluginWrap.find('.sbsw-text-input-wrap input').val();
                        }
                    }
                    manager.updatePluginSections();
                    manager.updateShortcode();
                });

                $pluginWrap.find('.sbsw-add-remove-account').click(function() {
                    if (typeof manager.state[plugin] !== 'undefined') {
                        var pluginData = manager.state[plugin];

                        if ($(this).closest('.sbsw-connected-account').hasClass('sbsw-selected')) {
                            $(this).closest('.sbsw-connected-account').removeClass('sbsw-selected');
                            $(this).html(sbspf.add_text);
                        } else {
                            $(this).closest('.sbsw-connected-account').addClass('sbsw-selected');
                            $(this).html(sbspf.remove_text);

                        }

                        var selected = [];
                        $pluginWrap.find('.sbsw-selected').each(function() {
                            selected.push($(this).attr('data-user'));
                        });
                        pluginData.current.term = selected.join(',');


                        manager.updateShortcode();

                    }
                });
                $pluginWrap.find('.sbsw-text-input-wrap input').on('input',function() {
                    if (typeof manager.state[plugin] !== 'undefined') {
                        manager.state[plugin].current.term = $(this).val();
                        manager.updateShortcode();
                        manager.updatePluginSections();
                    }
                });
                $pluginWrap.find('.sbsw-types-checkbox-wrap input').change(function() {
                    if (typeof manager.state[plugin] !== 'undefined') {
                        manager.state[plugin].current.type = '';
                        $pluginWrap.find('.sbsw-types-checkbox-wrap input:checked').each(function() {
                            manager.state[plugin].current.type += $(this).val() + ',';
                        });
                        manager.updateShortcode();
                    }
                });
            });
        },
        updatePluginSections : function() {
            var manager = this;
            $('.sbsw-default-feed-wrap').each(function() {
                var plugin = $(this).attr('data-plugin');

                if (typeof manager.state[plugin] !== 'undefined') {
                    var pluginData = manager.state[plugin];

                    if (plugin === 'instagram') {
                        var type = pluginData.current.type,
                            inputType = pluginData.available_types[type].input,
                            instructions = typeof pluginData.available_types[type].instructions !== 'undefined' ? pluginData.available_types[type].instructions : '';

                        if (inputType === 'connected') {
                            $(this).find('.sbsw-connected-accounts-wrap').show();
                            $(this).find('.sbsw-text-input-wrap').hide();
                        } else {
                            $(this).find('.sbsw-connected-accounts-wrap').hide();
                            $(this).find('.sbsw-text-input-wrap').show();
                            $(this).find('.sbsw-text-input-wrap .sbsw-text-input-instructions').html(instructions);
                        }
                    } else if (plugin === 'facebook') {
                        $(this).find('.sbsw-connected-accounts-wrap').show();
                        $(this).find('.sbsw-text-input-wrap').hide();
                    } else if (plugin === 'twitter') {
                        var type = pluginData.current.type,
                            inputType = pluginData.available_types[type].input,
                            instructions = typeof pluginData.available_types[type].instructions !== 'undefined' ? pluginData.available_types[type].instructions : '';
                        $(this).find('.sbsw-connected-accounts-wrap').hide();
                        if (inputType === 'message') {
                            $(this).find('.sbsw-message-wrap').show();
                            $(this).find('.sbsw-text-input-wrap').hide();

                        } else {
                            $(this).find('.sbsw-message-wrap').hide();
                            $(this).find('.sbsw-text-input-wrap').show();
                            $(this).find('.sbsw-text-input-wrap .sbsw-text-input-instructions').html(instructions);
                        }
                    } else if (plugin === 'youtube') {
                        var type = pluginData.current.type,
                            type = type === 'channel' ? 'channels' : type,
                            inputType = pluginData.available_types[type].input,
                            instructions = typeof pluginData.available_types[type].instructions !== 'undefined' ? pluginData.available_types[type].instructions : '';
                        $(this).find('.sbsw-connected-accounts-wrap').hide();
                        if (inputType === 'message') {
                            $(this).find('.sbsw-message-wrap').show();
                            $(this).find('.sbsw-text-input-wrap').hide();

                        } else {
                            $(this).find('.sbsw-message-wrap').hide();
                            $(this).find('.sbsw-text-input-wrap').show();
                            $(this).find('.sbsw-text-input-wrap .sbsw-text-input-instructions').html(instructions);
                            if (pluginData.available_types[type].term_shortcode === 'channel' && pluginData.channel_ids_names[ pluginData.current.term ] !== 'undefined') {
                                $(this).find('.sbsw-text-input-wrap .sbsw-text-input-identity').html(pluginData.channel_ids_names[ pluginData.current.term ]);
                            } else {
                                $(this).find('.sbsw-text-input-wrap .sbsw-text-input-identity').html('');
                            }
                        }
                    }

                }
            });

        },
        updateShortcode : function() {

            var ifShortcode = '',
                fbShortcode = '',
                twShortcode = '',
                ytShortcode = '';

            if (typeof this.state.instagram !== 'undefined'
                && (typeof this.state.instagram.exclude === 'undefined' || ! this.state.instagram.exclude)) {
                var type = this.state.instagram.current.type,
                    term = this.state.instagram.current.term,
                    shortcodeType = '';
                if (typeof this.state.instagram.available_types[type] !== 'undefined'
                    && this.state.instagram.current.term !== '') {
                    shortcodeType = ' ' + this.state.instagram.settings.type + '="' + type +'"' + ' ' + this.state.instagram.available_types[type].term_shortcode + '="' + this.state.instagram.current.term +'"';
                }

                ifShortcode = '    [instagram-feed'+shortcodeType+']\n';
            }

            if (typeof this.state.facebook !== 'undefined'
                && (typeof this.state.facebook.exclude === 'undefined' || ! this.state.facebook.exclude)) {
                var type = this.state.facebook.current.type,
                    term = this.state.facebook.current.term,
                    shortcodeType = '';

                var splitUpType = type.split(',');


                var filtered = splitUpType.filter(function (el) {
                    return el != '';
                });

                if (filtered.length > 0
                    && this.state.facebook.current.term !== '') {
                    shortcodeType = ' ' + this.state.facebook.settings.type + '="' + filtered.join(',') +'"' + ' ' + this.state.facebook.available_types[ splitUpType[0] ].term_shortcode + '="' + this.state.facebook.current.term +'"';
                }

                fbShortcode = '    [custom-facebook-feed'+shortcodeType+']\n';

                $('.sbsw-connected-account[data-id="'+term+'"]').addClass('sbsw-selected').find('.sbsw-add-remove-account').html(sbspf.remove_text);

            }

            if (typeof this.state.twitter !== 'undefined'
                && (typeof this.state.twitter.exclude === 'undefined' || ! this.state.twitter.exclude)) {
                var type = this.state.twitter.current.type,
                    term = this.state.twitter.current.term,
                    shortcodeType = '';
                if (typeof this.state.twitter.available_types[type] !== 'undefined'
                    && this.state.twitter.current.term !== '') {
                    var term = this.state.twitter.current.term;

                    if (this.state.twitter.available_types[type].term_shortcode === 'hometimeline'
                        || this.state.twitter.available_types[type].term_shortcode === 'mentionstimeline' ) {
                        term = true;
                    }
                    shortcodeType = ' ' + this.state.twitter.available_types[type].term_shortcode + '="' + term +'"';
                }

                twShortcode = '    [custom-twitter-feeds'+shortcodeType+']\n';
            }

            if (typeof this.state.youtube !== 'undefined'
                && (typeof this.state.youtube.exclude === 'undefined' || ! this.state.youtube.exclude)) {
                var type = this.state.youtube.current.type,
                    type = type === 'channel' ? 'channels' : type,
                    term = this.state.youtube.current.term,
                    shortcodeType = '';
                if (typeof this.state.youtube.available_types[type] !== 'undefined'
                    && this.state.youtube.current.term !== '') {
                    var term = this.state.youtube.current.term;

                    if (this.state.youtube.available_types[type].term_shortcode === 'hometimeline'
                        || this.state.youtube.available_types[type].term_shortcode === 'mentionstimeline' ) {
                        term = true;
                    }
                    shortcodeType = ' ' + this.state.youtube.settings.type + '="' + this.state.youtube.available_types[type].term_shortcode +'"' + ' ' + this.state.youtube.available_types[type].term_shortcode + '="' + term +'"';
                }

                ytShortcode = '    [youtube-feed'+shortcodeType+']\n';
            }

            $('.sbsw-sc-generator-wrap textarea').val('[social-wall]\n' +
                ifShortcode +
                fbShortcode +
                twShortcode +
                ytShortcode +
                '[/social-wall]\n').addClass('sbsw-updated-field');

            //Green flash when updating shortcode
            setTimeout(function(){
                $('.sbsw-sc-generator-wrap textarea').removeClass('sbsw-updated-field');
            }, 500);
        }

    };

    window.sbsw_admin_init = function() {
        var plugin = typeof $('.sbspf-admin').attr('data-sb-plugin') !== 'undefined' ? $('.sbspf-admin').attr('data-sb-plugin') : 'sbspf',
            $adminEl = $('#sbspf_admin.sby_admin');
        window.sb = new SbYoutubeAdmin(plugin,$adminEl);
        window.sb.init();
        if ($('.sbsw-sc-generator-wrap').length) {
            var shortcodeManager = new SWshortcodeManager();
            shortcodeManager.init();
        }

    };

})(jQuery);



jQuery(document).ready(function($) {
    sbsw_admin_init();

    window.sbswCASubmit = function(connected_accounts) {
        $('#sbsw-shortcode-generator').css('opacity', .5);

        var selected = [];
        $('.cff-managed-page').each(function() {
            if ($(this).hasClass('cff-page-selected')){
                selected.push($(this).attr('data-page-id'));
            }
        });


        var submitData = {
            'sbsw_connected_accounts' : connected_accounts,
            'selected' : selected,
            'action' : 'sbsw_connect_accounts',
        };
        var onSuccess = function (data) {
            setTimeout(function() {
                var reloadUrl = $('.sbsw-reload').length ? $('.sbsw-reload').attr('data-reload') : window.location.href;
                if (window.location.href !== reloadUrl) {
                    window.location.href = reloadUrl;
                } else {
                    window.location.reload();
                }
            },3000);

        };
        sbAjax(submitData,onSuccess);
    };

    window.sbAjax = function(submitData,onSuccess) {
        $.ajax({
            url: sbspf.ajaxUrl,
            type: 'post',
            data: submitData,
            success: onSuccess
        });
    }
});