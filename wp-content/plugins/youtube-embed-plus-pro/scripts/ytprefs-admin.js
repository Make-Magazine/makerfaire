(function (window, $)
{
    window._EPYTA_.widen_ytprefs_wiz = window._EPYTA_.widen_ytprefs_wiz || function ()
    {
        setTimeout(function ()
        {
            var tbWidth = Math.min(950, $(window).width() - 100);
            var tbMargin = -1 * tbWidth / 2;
            $("#TB_window").addClass('epyt-thickbox').animate({marginLeft: tbMargin, width: tbWidth}, 150, 'swing', function ()
            {
                $("#TB_window").get(0).style.setProperty('width', tbWidth, 'important');
            });

            $("#TB_overlay").addClass('epyt-thickbox');

            $("#TB_window iframe").animate({width: tbWidth}, 150);
        }, 750);
    };

    window._EPYTA_.onboardNext = function ($step)
    {
        $('.ytprefs-ob-step').removeClass('active-step');
        setTimeout(function ()
        {
            window.scrollTo(0, 0);
            $step.next().addClass('active-step');
        }, 600);
    };

    window._EPYTA_.onboardPrev = function ($step)
    {
        $('.ytprefs-ob-step').removeClass('active-step');
        setTimeout(function ()
        {
            window.scrollTo(0, 0);
            $step.prev().addClass('active-step');
        }, 600);
    };

    window._EPYTA_.selectText = function (ele)
    {
        if (document.selection)
        {
            var range = document.body.createTextRange();
            range.moveToElementText(ele);
            range.select();
        }
        else if (window.getSelection)
        {
            var selection = window.getSelection();
            var range = document.createRange();
            range.selectNode(ele);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    };

    window._EPYTA_.gbPreviewSetup = function ()
    {
        window._EPADashboard_.loadYTAPI();
        window._EPADashboard_.apiInit();
        window._EPADashboard_.log("YT API GB");
        window._EPADashboard_.pageReady();
        if (typeof $.fn.fitVidsEP !== 'undefined')
        {
            $('body').fitVidsEP();
        }
        if (typeof (window._EPADashboard_.embdyn) !== 'undefined')
        {
            window._EPADashboard_.embdyn();
        }
    };

    $.fn.ytprefsFormJSON = function ()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function ()
        {
            if (o[this.name])
            {
                if (!o[this.name].push)
                {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            }
            else
            {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $(document).ready(function ()
    {

        if (window.location.toString().indexOf('https://') === 0)
        {
            window._EPYTA_.wpajaxurl = window._EPYTA_.wpajaxurl.replace("http://", "https://");
        }

        // Create IE + others compatible event handler
        var epeventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
        var epeventer = window[epeventMethod];
        var epmessageEvent = epeventMethod == "attachEvent" ? "onmessage" : "message";

        // Listen to message from child window
        epeventer(epmessageEvent, function (e)
        {
            var embedcode = "";
            try
            {
                if (e.data.indexOf("youtubeembedplus") === 0 && e.data.indexOf('clientId=') < 0)
                {
                    embedcode = e.data.split("|")[1];
                    if (embedcode.indexOf("[") !== 0)
                    {
                        embedcode = "<p>" + embedcode + "</p>";
                    }

                    if (window.tinyMCE !== null && window.tinyMCE.activeEditor !== null && !window.tinyMCE.activeEditor.isHidden())
                    {
                        if (window._EPYTA_.mceBookmark)
                        {
                            try
                            {
                                window.tinyMCE.activeEditor.selection.moveToBookmark(window._EPYTA_.mceBookmark);
                            }
                            catch (err)
                            {
                            }
                        }

                        if (typeof window.tinyMCE.execInstanceCommand !== 'undefined')
                        {
                            window.tinyMCE.execInstanceCommand(
                                    window.tinyMCE.activeEditor.id,
                                    'mceInsertContent',
                                    false,
                                    embedcode);
                        }
                        else
                        {
                            send_to_editor(embedcode);
                        }

                        setTimeout(function ()
                        {
                            window._EPYTA_.mceBookmark = null;
                        }, 500);
                    }
                    else
                    {
                        embedcode = embedcode.replace('<p>', '\n').replace('</p>', '\n');
                        if (typeof QTags.insertContent === 'function')
                        {
                            QTags.insertContent(embedcode);
                        }
                        else
                        {
                            send_to_editor(embedcode);
                        }
                    }
                    tb_remove();
                }
                else if (e.data.indexOf("youtubeextprop") === 0)
                {
                    var extprop = e.data.split("|")[1];
                    var extpropval = extprop === 'xdash1' ? 1 : 0;
                    if (extpropval != window._EPYTA_.dashpre)
                    {
                        $.ajax({
                            type: "post",
                            dataType: "json",
                            timeout: 30000,
                            url: window._EPYTA_.wpajaxurl,
                            data: {action: 'my_embedplus_dashpre', dashpre: extpropval},
                            success: function (response)
                            {
                            },
                            error: function (xhr, ajaxOptions, thrownError)
                            {
                            },
                            complete: function ()
                            {
                            }
                        });

                    }


                }

            }
            catch (err)
            {

            }


        }, false);

        $('body').on('click.tbyt', "#ytprefs_wiz_button, .ytprefs_wiz_button_widget_text, .ytprefs-onboarding-launch", function ()
        {
            window._EPYTA_.widen_ytprefs_wiz();
        });

        $(window).on('resize', window._EPYTA_.widen_ytprefs_wiz);

        $(document).on('wp-before-tinymce-init.ytprefs-media_button', function (event, init)
        {
            var $media_buttons = $(init.selector).closest('.wp-editor-wrap').find('.wp-media-buttons');
            if (!$media_buttons.find('.ytprefs_media_link').length)
            {
                $media_buttons.append('<a href="' + encodeURI(window._EPYTA_.wizhref) + '" class="thickbox button ytprefs_media_link ytprefs_wiz_button_widget_text" title="Visual YouTube Search Tool and Wizard - For easier embedding"><span></span> YouTube</a>');
            }
        });



        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $('.wrap section a[href^="#jump"]').on('click', function (e)
        {
            e.preventDefault();
            var tab = $(this).attr('href');
            $('.nav-tab-wrapper a[href="' + tab + '"], .nav-tab-wrapper a[rel="' + tab + '"]').trigger('click');
        });


        $('.ytprefs-ajax-form').on('keypress', function (ev)
        {
            if (ev.which == 13)
            {
                ev.preventDefault();
                $(this).find('.ytprefs-ajax-form--submit:not([disabled])').trigger('click');
            }
        });

        /////////////////////////////// onboarding
        if ($('.wrap-ytprefs-onboarding').length)
        {
            // global
            $('.ytprefs-ob-nav-close').on('click', function ()
            {
                window.parent.tb_remove();
                window.top.location.reload();
            });

            $('.ytprefs-ob-nav-prev').on('click', function ()
            {
                window._EPYTA_.onboardPrev($(this).closest('.ytprefs-ob-step'));
            });

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////// step 1
            var prokeyval;
            var mydomain = escape("http://" + window.location.host.toString());
            $('#prokeysubmit').on('click', function ()
            {
                $(this).prop('disabled', true);
                $('#prokeyfailed').hide();
                $('#prokeysuccess').hide();
                $('#prokeyloading').show();
                prokeyval = $('#opt_pro').val();
                var tempscript = document.createElement("script");
                tempscript.src = window._EPYTA_.epbase + "/dashboard/wordpress-pro-validatejp.aspx?simple=1&prokey=" + prokeyval + "&domain=" + mydomain;
                var n = document.getElementsByTagName("head")[0].appendChild(tempscript);
                setTimeout(function ()
                {
                    n.parentNode.removeChild(n);
                }, 500);
                return false;
            });
            window.embedplus_record_prokey = function (good)
            {

                $.ajax({
                    type: "post",
                    dataType: "json",
                    timeout: 30000,
                    url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                    data: {action: 'my_embedplus_pro_record', pro: (good ? prokeyval : "")},
                    success: function (response)
                    {
                        if (response.type == "success")
                        {
                            $("#prokeysuccess").show();
                            setTimeout(function ()
                            {
                                window.location.reload();
                            }, 3000);
                            //$(".ytprefs-ob-content1").show();

                        }
                        else
                        {
                            $("#prokeyfailed").show();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError)
                    {
                        $('#prokeyfailed').show();
                    },
                    complete: function ()
                    {
                        $('#prokeyloading').hide();
                        $('#prokeysubmit').prop('disabled', false);
                    }

                });
            };

            $('.ytprefs-ob-filter li').hover(function ()
            {
                var sel = '.' + $(this).find('input[type="checkbox"]').data('obfilter') + '-icon';
                $(sel).addClass('yob-icon-visible');
            }, function ()
            {
                var sel = '.' + $(this).find('input[type="checkbox"]').data('obfilter') + '-icon';
                $(sel).removeClass('yob-icon-visible');
            });


            $('.ytprefs-ob-filter input[type="checkbox"]').on('change', function ()
            {
                var $chk = $(this);
                var obfilter = $chk.data('obfilter');

                var $allChecked = $('.ytprefs-ob-filter input[type="checkbox"]:checked');
                if ($allChecked.length && !($allChecked.length === 1 && $allChecked.is('[data-obfilter="yob-monetize"]')))
                {
                    $('.ytprefs-ob-step1 .ytprefs-ob-nav-next').prop('disabled', false);
                }
                else
                {
                    $('.ytprefs-ob-step1 .ytprefs-ob-nav-next').prop('disabled', true);
                }

                if (obfilter == 'yob-monetize')
                {
                    $('.ytprefs-ob-step3 .ytprefs-ob-nav-ultimate, .ytprefs-ob-step3 .ytprefs-ob-nav-penultimate').toggleClass('ytprefs-ob-nav-hide');
                }
                else
                {
                    if ($chk.is(":checked"))
                    {
                        $('.ytprefs-ob-step2 .' + obfilter).addClass(obfilter + '-visible');
                    }
                    else
                    {
                        $('.ytprefs-ob-step2 .' + obfilter).removeClass(obfilter + '-visible');
                    }
                }

            });


            $('.ytprefs-ob-step1 .ytprefs-ob-nav-next').on('click', function ()
            {
                window._EPYTA_.onboardNext($(this).closest('.ytprefs-ob-step'));
            });


            //////////////////////////////////////////////////////////////////////////////////////////////////////////////// step 2
            $('#form-onboarding').on('submit', function (e)
            {
                e.preventDefault();
                (window.tinyMCE || window.tinymce).triggerSave();
                var $formOnboarding = $(this);
                $formOnboarding.find('.ytprefs-ob-nav-next').prop('disabled', true);

                var formData = $formOnboarding.ytprefsFormJSON();
                formData.security = window._EPYTA_.security;

                $.ajax({
                    type: "post",
                    dataType: "json",
                    timeout: 30000,
                    url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                    data: formData,
                    success: function (response)
                    {
                        if (response.type == "success")
                        {

                            window._EPYTA_.onboardNext($formOnboarding.closest('.ytprefs-ob-step'));
                        }
                        else
                        {
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError)
                    {
                    },
                    complete: function ()
                    {
                        $formOnboarding.find('.ytprefs-ob-nav-next').prop('disabled', false);
                    }

                });

            });

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////// step 3
            $('.ytprefs-ob-step3 .ytprefs-ob-nav-skip').on('click', function ()
            {
                window._EPYTA_.onboardNext($(this).closest('.ytprefs-ob-step'));
            });

            $('#form-onboarding-apikey').on('submit', function (e)
            {
                e.preventDefault();
                var $formOnboarding = $(this);
                $formOnboarding.find('.ytprefs-ob-nav-next').prop('disabled', true);

                var formData = $formOnboarding.ytprefsFormJSON();
                formData.security = window._EPYTA_.security;

                $.ajax({
                    type: "post",
                    dataType: "json",
                    timeout: 30000,
                    url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                    data: formData,
                    success: function (response)
                    {
                        if (response.type == "success")
                        {
                            if ($formOnboarding.find('.ytprefs-ob-nav-ultimate').hasClass('ytprefs-ob-nav-hide'))
                            {
                                window._EPYTA_.onboardNext($formOnboarding.closest('.ytprefs-ob-step'));
                            }
                            else
                            {
                                window.parent.tb_remove();
                                window.top.location.reload();
                            }

                        }
                        else
                        {
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError)
                    {
                    },
                    complete: function ()
                    {
                        $formOnboarding.find('.ytprefs-ob-nav-next').prop('disabled', false);
                    }

                });

            });



        } // end onboarding

        // dashboard
        $('.epyt-load-dashboard-confirm').on('change', function ()
        {
            $('.epyt-load-dashboard').prop('disabled', !$('.epyt-load-dashboard').prop('disabled'));
        });
        $('.epyt-load-dashboard').on('click', function ()
        {
            $('.epyt-dashboard-content').attr('src', $('.epyt-dashboard-content').data('src') + encodeURIComponent(window.location.protocol + '//' + window.location.hostname))
                    .css('height', 'calc(100vh - 30px)');
        });

    }); // end ready
    $(window).on('load', function ()
    {
        if (_EPYTA_.onboarded != '1')
        {
            $('.ytprefs-onboarding-launch').trigger('click');
        }
    }); // end onload
})(window, jQuery);
