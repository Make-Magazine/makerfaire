window._EPYTWIZ_ = window._EPYTWIZ_ || {};
(function ($, angular)
{

    $.fn.followTo = function (pos, startTop)
    {
        var $this = this,
                $window = $(window);

        $window.on('scroll', function (e)
        {
            if ($window.scrollTop() > pos)
            {
                $this.css({
                    position: 'absolute',
                    top: pos
                });
            }
            else
            {
                $this.css({
                    position: 'fixed',
                    top: startTop
                });
            }
        });
    };

    window._EPYTWIZ_.getUrlParameter = function (name)
    {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(window.location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    };

    window._EPYTWIZ_.selectText = window._EPYTWIZ_.selectText || function (ele)
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


    window._EPYTWIZ_.loadmovieplain = window._EPYTWIZ_.loadmovieplain || function (vid)
    {
        var codetemplate = '<iframe width="600" height="368" src="//www.youtube.com/embed/~ytid?autoplay=1" frameborder="0" allowfullscreen ></iframe>';
        codetemplate = codetemplate.replace(/~ytid/g, vid);
        $("#watch" + vid).html(codetemplate);
        $('#closeme' + vid).css('display', 'inline');
        $("#moviecontainer" + vid).css('display', 'block');
        if (document.getElementById('scrollwatch' + vid))
        {
            setTimeout(function ()
            {
                $('html, body').animate({
                    scrollTop: $('#scrollwatch' + vid).offset().top - 50
                }, 250, function ()
                {
                });

            }, 800);
        }
    };


    window._EPYTWIZ_.closeme = window._EPYTWIZ_.closeme || function (vid)
    {
        $("#moviecontainer" + vid).css('display', 'none');
        $("#watch" + vid).html("");
    };

    $(document).ready(function ()
    {

        $('.wiz-accordion').accordion({
            header: "> h3",
            collapsible: true,
            active: false,
            icons: {
                header: "ui-icon-circle-arrow-e",
                activeHeader: "ui-icon-circle-arrow-s"
            },
            heightStyle: "content",
            autoHeight: false
        }).find('h3.header-go').on('click', function ()
        {
            window.open($(this).find('a').attr('href'), '_blank');
            return false;
        });

        $('.playlist-tabs').tabs();
        $('.livestream-tabs').tabs();

        if (window._EPYTWIZ_.acc_expand)
        {
            $('.wiz-accordion #' + window._EPYTWIZ_.acc_expand).trigger('click');
        }

        $('form.wizform').each(function ()
        {
            $thisForm = $(this);
            $thisForm.find('.txturlpastecustom').on('paste change', function ()
            {
                $thisTxtUrl = $(this);
                setTimeout(function ()
                {
                    var thepaste = $.trim($thisTxtUrl.val());
                    var badpaste = /<.*/i;
                    if (badpaste.test(thepaste))
                    {
                        var reg = new RegExp('(?:https?://)?(?:www\\.)?(?:youtu\\.be/|youtube\\.com(?:/embed/|/shorts/|/v/|/watch\\?v=))([\\w-]{10,12})', 'ig');
                        //get matches found for the regular expression
                        var matches = reg.exec(thepaste);
                        //check if we have found a match for a YouTube video
                        //will support legacy code, shortened urls and
                        if (matches)
                        {
                            var ytid = matches[1];
                            $thisTxtUrl.val('https://www.youtube.com/watch?v=' + ytid);
                        }
                        else
                        {
                            $thisTxtUrl.val('https://www.youtube.com/watch?v=');
                        }
                        $thisForm.find('.badpaste').show();

                    }
                    else
                    {
                        $thisForm.find('.badpaste').hide();
                    }

                }, 100);
            });
        });

        var $epyt_wiz_wrap = $('#epyt_wiz_wrap');

        $epyt_wiz_wrap.on('click', '.copycode', function ()
        {
            window._EPYTWIZ_.selectText(this);
        });

        $epyt_wiz_wrap.on('click', '.inserttopost', function ()
        {
            var targetdomain = window.location.toString().split("/")[0] + "//" + window.location.toString().split("/")[2];
            var embedline = $(this).attr("rel");
            var gbclientId = window._EPYTWIZ_.getUrlParameter('clientId');
            parent.postMessage("youtubeembedplus|" + embedline + (gbclientId ? '|clientId=' + gbclientId : ''), targetdomain);
        });

        $epyt_wiz_wrap.on('click', '.resultdiv .load-movie', function ()
        {
            window._EPYTWIZ_.loadmovieplain($(this).closest('.resultdiv').data('vid'));
            return false;
        });

        $epyt_wiz_wrap.on('click', '.moviecontainer a.closeme', function ()
        {
            window._EPYTWIZ_.closeme($(this).data('vid'));
        });

        $epyt_wiz_wrap.on('click', '.btn-customize-step', function ()
        {

            $epyt_wiz_wrap.addClass('animated fadeOutLeft');
            setTimeout(function ()
            {
                $epyt_wiz_wrap.hide(0, function ()
                {
                    $('#ep-wizard-customizer').css('display', 'block').addClass('animated fadeInUp');

                    var wizoptionboxHeight = $(".wizoptionbox").height();
                    $('#ytpreviewbox').followTo(wizoptionboxHeight - 250, 'auto');

                });
            }, 500);
        });

        $epyt_wiz_wrap.on('paste change input propertychange', '#txtUrlChannel', function ()
        {
            if ($(this).val().indexOf('/c/') > 0)
            {
                $('.error-channel-format').addClass('orange bold');
                $(this).val('');
            }
            else
            {
                $('.error-channel-format').removeClass('orange bold');
            }
        });
    });

    ////////////////////////////////////////////////////////////////////////////

    angular.module('YouTubeWizardApp', [])
            .directive('convertToNumber', function ()
            {
                return {
                    require: 'ngModel',
                    link: function (scope, element, attrs, ngModel)
                    {
                        ngModel.$parsers.push(function (val)
                        {
                            return val != null ? parseInt(val, 10) : null;
                        });
                        ngModel.$formatters.push(function (val)
                        {
                            return val != null ? '' + val : null;
                        });
                    }
                };
            })
            .controller('YouTubeWizardController', ['$scope', '$sce', function ($scope, $sce)
                {

                    $scope.canInputRange = function ()
                    {
                        var rangedetect = document.createElement("input");
                        rangedetect.setAttribute("type", "range");
                        return rangedetect.type !== "text";
                    };

                    $scope.selectAllText = function (e)
                    {
                        e.target.focus();
                        e.target.select();
                    };

                    $scope.selectHeight = function ()
                    {
                        if ($scope.model.selheight === '16-9')
                        {
                            $scope.model.height = Math.round((9.0 * $scope.model.width) / 16.0);
                        }
                        else if ($scope.model.selheight === '4-3')
                        {
                            $scope.model.height = Math.round((3.0 * $scope.model.width) / 4.0);
                        }
                        else if ($scope.model.selheight === 'best-fit')
                        {
                            $scope.model.height = Math.round($scope.model.aspect_ratio * $scope.model.width);
                        }
                    };

                    $scope.padZeroTime = function (num)
                    {
                        var s = "0000" + (num || 0);
                        return s.substr(s.length - 2);
                    };

                    $scope.timeColons = function (hours, mins, secs)
                    {
                        return $scope.padZeroTime(hours) + ":" + $scope.padZeroTime(mins) + ":" + $scope.padZeroTime(secs);
                    };

                    $scope.getSecsFromTimePicker = function (hours, mins, secs)
                    {
                        return ((hours ? hours : 0) * 3600) + ((mins ? mins : 0) * 60) + (secs ? secs : 0);
                    };

                    $scope.centervidChange = function ()
                    {
                        $scope.model.centervid = parseInt($scope.model.centervid) === 0 ? 1 : 0;
                    };

                    $scope.autoplayChange = function ()
                    {
                        $scope.model.autoplay = parseInt($scope.model.autoplay) === 0 ? 1 : 0;
                    };

                    $scope.cc_load_policyChange = function ()
                    {
                        $scope.model.cc_load_policy = parseInt($scope.model.cc_load_policy) === 0 ? 1 : 0;
                    };

                    $scope.iv_load_policyChange = function ()
                    {
                        $scope.model.iv_load_policy = parseInt($scope.model.iv_load_policy) === 3 ? 1 : 3;
                    };

                    $scope.loopChange = function ()
                    {
                        $scope.model.loop = parseInt($scope.model.loop) === 0 ? 1 : 0;
                    };

                    $scope.modestbrandingChange = function ()
                    {
                        $scope.model.modestbranding = parseInt($scope.model.modestbranding) === 0 ? 1 : 0;
                    };

                    $scope.fsChange = function ()
                    {
                        $scope.model.fs = parseInt($scope.model.fs) === 0 ? 1 : 0;
                    };

                    $scope.controlsChange = function ()
                    {
                        $scope.model.controls = parseInt($scope.model.controls) === 0 ? 1 : 0;
                    };

                    $scope.defaultvolChange = function ()
                    {
                        $scope.model.defaultvol = parseInt($scope.model.defaultvol) === 0 ? 1 : 0;
                    };


                    $scope.gallery_showtitleChange = function ()
                    {
                        $scope.model.gallery_showtitle = parseInt($scope.model.gallery_showtitle) === 0 ? 1 : 0;
                    };


                    $scope.gallery_showpagingChange = function ()
                    {
                        $scope.model.gallery_showpaging = parseInt($scope.model.gallery_showpaging) === 0 ? 1 : 0;
                    };


                    $scope.gallery_autonextChange = function ()
                    {
                        $scope.model.gallery_autonext = parseInt($scope.model.gallery_autonext) === 0 ? 1 : 0;
                    };


                    $scope.gallery_showdscChange = function ()
                    {
                        $scope.model.gallery_showdsc = parseInt($scope.model.gallery_showdsc) === 0 ? 1 : 0;
                    };

                    $scope.gallery_hidethumbimgChange = function ()
                    {
                        $scope.model.gallery_hidethumbimg = parseInt($scope.model.gallery_hidethumbimg) === 0 ? 1 : 0;
                    };

                    $scope.gallery_channelsubChange = function ()
                    {
                        $scope.model.gallery_channelsub = parseInt($scope.model.gallery_channelsub) === 0 ? 1 : 0;
                    };

                    $scope.live_chatChange = function ()
                    {
                        $scope.model.live_chat = parseInt($scope.model.live_chat) === 0 ? 1 : 0;
                    };

                    $scope.iframePreviewUrl = function ()
                    {
                        var previewUrl = '';
                        var vid = $scope.model.if_live_preview || $scope.model.theytid;
                        if ($scope.model.submit_type == "step1_channel")
                        {
                            vid = '';
                        }

                        if ($scope.model.submit_type == "step1_livechannel")
                        {
                            previewUrl = 'https://www.youtube.com/embed/live_stream?channel=' + $scope.model.thechannelid;
                        }
                        else
                        {
                            previewUrl = 'https://www.youtube.com/embed/'
                                    + (vid ? vid : '') + '?'
                                    + ($scope.model.theplaylistid ? 'list=' + $scope.model.theplaylistid : '');
                        }

                        return $sce.trustAsResourceUrl(previewUrl);
                    };


                    $scope.getCodeStep = function ()
                    {
                        var $stepCustomize = $('.container-step-customize');
                        $stepCustomize.addClass('animated fadeOutLeft');
                        setTimeout(function ()
                        {
                            $stepCustomize.hide(0, function ()
                            {
                                $('.container-step-get-code').css('display', 'block').addClass('animated fadeInUp');
                            });
                        }, 500);
                    };

                    $scope.editStep = function ()
                    {
                        var $stepGetCode = $('.container-step-get-code');
                        $stepGetCode.removeClass('fadeInUp');
                        setTimeout(function ()
                        {
                            $stepGetCode.hide(0, function ()
                            {
                                $('.container-step-customize').css('display', 'block').removeClass('animated fadeOutLeft');
                            });
                        }, 500);
                    };

                    $scope.finalTitle = function ()
                    {
                        return ($scope.model.final_title_prefix ? $scope.model.final_title_prefix +
                                ($scope.model.theplaylistid && $scope.model.gallery_style ? " (Gallery Layout)" : "") + " - " : "") +
                                $scope.model.final_title;
                    };

                    $scope.embedCode = function ()
                    {
                        var codetemplatewp = "https://www.youtube.com/watch?v=~ytid";
                        var paramsyt = '';

                        if ($scope.model.submit_type === 'step1_channel')
                        {
                            codetemplatewp = "https://www.youtube.com/embed?listType=playlist";
                        }
                        else if ($scope.model.submit_type === 'step1_live')
                        {
                            codetemplatewp = "https://www.youtube.com/embed?v=~ytid&live=1";
                        }
                        else if ($scope.model.submit_type === 'step1_livechannel')
                        {
                            codetemplatewp = "https://www.youtube.com/embed/live_stream?channel=" + $scope.model.thechannelid;
                        }


                        var starttime = $scope.getSecsFromTimePicker($scope.model.startHours, $scope.model.startMins, $scope.model.startSecs);
                        var stoptime = $scope.getSecsFromTimePicker($scope.model.stopHours, $scope.model.stopMins, $scope.model.stopSecs);

                        if (starttime > 0 || $scope.model.alsoStop || (stoptime > 0))
                        {
                            if (starttime > 0)
                            {
                                paramsyt += "&start=" + starttime;
                            }

                            if ($scope.model.alsoStop && stoptime > 0)
                            {
                                paramsyt += "&end=" + stoptime;
                            }
                        }

                        if ($scope.model.defaultvol)
                        {
                            paramsyt += "&vol=" + $scope.model.vol;
                        }

                        paramsyt += "&width=" + $scope.model.width;
                        paramsyt += "&height=" + $scope.model.height;

                        if ($scope.model.iv_load_policy != $scope.myytdefaults.iv_load_policy)
                        {
                            paramsyt += "&iv_load_policy=" + $scope.model.iv_load_policy;
                        }

                        if ($scope.model.autoplay != $scope.myytdefaults.autoplay)
                        {
                            paramsyt += "&autoplay=" + $scope.model.autoplay;
                        }

                        if ($scope.model.cc_load_policy != $scope.myytdefaults.cc_load_policy)
                        {
                            paramsyt += "&cc_load_policy=" + $scope.model.cc_load_policy;
                        }

                        if ($scope.model.centervid != $scope.myytdefaults.centervid)
                        {
                            paramsyt += "&centervid=" + $scope.model.centervid;
                        }

                        if ($scope.model.loop != $scope.myytdefaults.loop && !$scope.model.doing_live)
                        {
                            paramsyt += "&loop=" + $scope.model.loop;
                        }

                        if ($scope.model.rel != $scope.myytdefaults.rel)
                        {
                            paramsyt += "&rel=" + $scope.model.rel;
                        }

                        if ($scope.model.fs != $scope.myytdefaults.fs)
                        {
                            paramsyt += "&fs=" + $scope.model.fs;
                        }

                        if ($scope.model.modestbranding != $scope.myytdefaults.modestbranding)
                        {
                            paramsyt += "&modestbranding=" + $scope.model.modestbranding;
                        }

                        if ($scope.model.controls != $scope.myytdefaults.controls)
                        {
                            paramsyt += "&controls=" + $scope.model.controls;
                        }

                        if ($scope.model.theplaylistid)
                        {
                            if (codetemplatewp.indexOf('listType=playlist') < 0)
                            {
                                paramsyt += "&listType=playlist";
                            }
                            paramsyt += "&list=" + $scope.model.theplaylistid;

                            if ($scope.model.rblPlaylistStart == "0")
                            {
                                paramsyt += "&plindex=0";
                            }
                        }

                        if ($scope.model.live_chat != $scope.myytdefaults.live_chat)
                        {
                            paramsyt += "&live_chat=" + $scope.model.live_chat;
                        }

                        if ($scope.model.gallery_style != "")
                        {
                            paramsyt += "&layout=gallery";
                            if ($scope.model.gallery_style != "grid" || $scope.myytdefaults.gallery_style != "grid")
                            {
                                paramsyt += "&gallery_style=" + $scope.model.gallery_style;
                            }

                            if ($scope.model.gallery_pagesize != $scope.myytdefaults.gallery_pagesize)
                            {
                                paramsyt += "&gallery_pagesize=" + $scope.model.gallery_pagesize;
                            }

                            if ($scope.model.gallery_disptype != $scope.myytdefaults.gallery_disptype)
                            {
                                paramsyt += "&gallery_disptype=" + $scope.model.gallery_disptype;
                            }

                            if ($scope.model.gallery_thumbcrop != $scope.myytdefaults.gallery_thumbcrop)
                            {
                                paramsyt += "&gallery_thumbcrop=" + $scope.model.gallery_thumbcrop;
                            }

                            if ($scope.model.gallery_showtitle != $scope.myytdefaults.gallery_showtitle)
                            {
                                paramsyt += "&gallery_showtitle=" + $scope.model.gallery_showtitle;
                            }

                            if ($scope.model.gallery_autonext != $scope.myytdefaults.gallery_autonext)
                            {
                                paramsyt += "&gallery_autonext=" + $scope.model.gallery_autonext;
                            }

                            if ($scope.model.gallery_showpaging != $scope.myytdefaults.gallery_showpaging)
                            {
                                paramsyt += "&gallery_showpaging=" + $scope.model.gallery_showpaging;
                            }

                            if ($scope.model.gallery_style == "listview")
                            {
                                if ($scope.model.gallery_showdsc != $scope.myytdefaults.gallery_showdsc)
                                {
                                    paramsyt += "&gallery_showdsc=" + $scope.model.gallery_showdsc;
                                }
                            }

                            if ($scope.model.gallery_style != "carousel")
                            {
                                if ($scope.model.gallery_hidethumbimg != $scope.myytdefaults.gallery_hidethumbimg)
                                {
                                    paramsyt += "&gallery_hidethumbimg=" + $scope.model.gallery_hidethumbimg;
                                }
                            }

                            if ($scope.model.gallery_channelsub != $scope.myytdefaults.gallery_channelsub)
                            {
                                paramsyt += "&gallery_channelsub=" + $scope.model.gallery_channelsub;
                            }

                            if ($scope.model.gallery_columns != $scope.myytdefaults.gallery_columns)
                            {
                                paramsyt += "&gallery_columns=" + $scope.model.gallery_columns;
                            }
                        }

                        codetemplatewp = codetemplatewp.replace("v=~ytid", ($scope.model.theytid ? "v=" + $scope.model.theytid : ""));
                        var wpcode = codetemplatewp + paramsyt;
                        return '[embedyt] #[/embedyt]'.replace("#", wpcode);
                    };

                    $scope.insertFinalCode = function ()
                    {
                        var ec = $scope.embedCode();
                        var embedline = ec.replace(/[&]/ig, '&amp;');
                        var targetdomain = window.location.toString().split("/")[0] + "//" + window.location.toString().split("/")[2];
                        var gbclientId = window._EPYTWIZ_.getUrlParameter('clientId');
                        parent.postMessage("youtubeembedplus|" + embedline + (gbclientId ? '|clientId=' + gbclientId : ''), targetdomain);
                    };

                    //////////////////////////////////////////////////////////////////////////////////////////////////////
                    $scope.myytdefaults = window._EPYTWIZ_.myytdefaults;
                    $scope.model = window._EPYTWIZ_.model;
                    $scope.model.gallery_style = '';
                    $scope.model.rblPlaylistStart = '0';
                    $scope.selectHeight();

                }]);
})(jQuery, angular);


