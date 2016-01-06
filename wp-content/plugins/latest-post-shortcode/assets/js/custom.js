function lps_embed_shortcode() {
    lps_preview_configures_shortcode();
    wp.media.editor.insert(jQuery('#lps_preview_embed_shortcode').html());
}

function lps_recalculate_width() {
    var ww = jQuery('#TB_window').innerWidth() - 30;
    var hh = jQuery('#TB_window').innerHeight() - 60;
    jQuery('#TB_ajaxContent').css('width', ww + 'px');
    jQuery('#TB_ajaxContent').css('height', hh + 'px');
    jQuery('#TB_ajaxContent').css('max-height', hh + 'px');
    jQuery('#TB_ajaxContent').css('overflow-y', 'auto');
}

function lps_init_embed_shortcode(ed) {
    var selected = '';
    if (typeof tinyMCE != 'undefined' && tinyMCE.activeEditor !== null) {
        selected = tinyMCE.activeEditor.selection.getContent();
    } else {
        selected = jQuery('#content').val();
    }
    var c = selected.replace('[latest-selected-content ', '');
    c = c.replace(']', '');
    var newTxt = c.match(/[\w-]+=\"[^\"]*\"/g);
    if (newTxt) {
        for (var i = 0; i < newTxt.length; i++) {
            var k = newTxt[i].split('=')[0];
            var v = newTxt[i].split('=')[1].replace('"', '');
            v = v.replace('"', '');
            v = v.replace(']', '');
            switch (k) {
                case 'limit' :
                    jQuery('#lps_limit').val(v);
                    break;
                case 'perpage' :
                    jQuery('#lps_per_page').val(v);
                    break;
                case 'offset' :
                    jQuery('#lps_offset').val(v);
                    break;
                case 'showpages' :
                    jQuery('#lps_showpages').val(v);
                    break;
                case 'pagespos' :
                    jQuery('#lps_showpages_pos').val(v);
                    break;
                case 'type' :
                    jQuery('#lps_post_type').val(v);
                    break;
                case 'display' :
                    jQuery('#lps_display').val(v);
                    break;
                case 'chrlimit' :
                    jQuery('#lps_chrlimit').val(v);
                    break;
                case 'image' :
                    jQuery('#lps_image').val(v);
                    break;
                case 'url' :
                    jQuery('#lps_url').val(v);
                    break;
                case 'elements' :
                    jQuery('#lps_elements').val(v);
                    jQuery('#lps_elements_img_' + v).prop("checked", true);
                    break;
                case 'linktext' :
                    jQuery('#lps_linktext').val(v);
                    break;
                case 'css' :
                    jQuery('#lps_css').val(v);
                    break;
                case 'taxonomy' :
                    jQuery('#lps_taxonomy').val(v);
                    break;
                case 'term' :
                    jQuery('#lps_term').val(v);
                    break;
                case 'tag' :
                    jQuery('#lps_tag').val(v);
                    break;
                case 'dtag' :
                    jQuery('#lps_dtag').val(v);
                    break;
                case 'id' :
                    jQuery('#lps_post_id').val(v);
                    break;
                case 'parent' :
                    jQuery('#lps_parent_id').val(v);
                    break;
                case 'show_extra' :
                    var p = v.split(',');
                    for (jj = 0; jj <= p.length; jj++) {
                        if (typeof jQuery('#lps_show_extra_' + p[jj]) != 'undefined') {
                            jQuery('#lps_show_extra_' + p[jj]).prop('checked', true);
                        }
                    }
                    break;
                case 'orderby' :
                    jQuery('#lps_orderby').val(v);
                    break;
                default :
                    break;
            }
            if (( jQuery('#lps_offset').val() != 0 || jQuery('#lps_per_page').val() != 0 )) {
                jQuery('#lps_pagination_options').show();
                jQuery('#lps_use_pagination').val('yes');
            } else {
                jQuery('#lps_pagination_options').hide();
            }
        }
    }
    lps_preview_configures_shortcode();
}

function lps_preview_configures_shortcode() {
    var sc = '[latest-selected-content';
    var limit = jQuery('#lps_limit').val();
    if (limit != '') {
        sc += ' limit="' + limit + '"';
    }
    var use_pagination = jQuery('#lps_use_pagination').val();
    var perpage = jQuery('#lps_per_page').val();
    var offset = jQuery('#lps_offset').val();
    var showpages = jQuery('#lps_showpages').val();
    var pagespos = jQuery('#lps_showpages_pos').val();
    if (use_pagination != '') {
        jQuery('#lps_pagination_options').show();
        if (perpage != 0) {
            sc += ' perpage="' + perpage + '"';
        }
        if (offset != 0) {
            sc += ' offset="' + offset + '"';
        }
        if (showpages != '') {
            sc += ' showpages="' + showpages + '"';
            if (pagespos != '') {
                sc += ' pagespos="' + pagespos + '"';
            }
        }
    } else {
        jQuery('#lps_pagination_options').hide();
    }
    var type = jQuery('#lps_post_type').val();
    if (type != '') {
        sc += ' type="' + type + '"';
    }
    var display = jQuery('#lps_display').val();
    if (display != '') {
        sc += ' display="' + display + '"';
        if (display.indexOf('excerpt-small') >= 0 || display.indexOf('content-small') >= 0) {
            jQuery('#lps_display_limit').show();
            var chrlimit = jQuery('#lps_chrlimit').val();
            if (chrlimit != '') {
                sc += ' chrlimit="' + chrlimit + '"';
            }
        } else {
            jQuery('#lps_display_limit').hide();
        }
    }
    var image = jQuery('#lps_image').val();
    if (image != '') {
        sc += ' image="' + image + '"';
    }
    var url = jQuery('#lps_url').val();
    if (url != '') {
        sc += ' url="' + url + '"';
        jQuery('#lps_url_options').show();
        jQuery('label.without-link').hide();
        jQuery('label.with-link').show();
        var linktext = jQuery('#lps_linktext').val();
        if (linktext != '') {
            sc += ' linktext="' + linktext + '"';
        }
    } else {
        jQuery('#lps_url_options').hide();
        jQuery('label.with-link').hide();
        jQuery('label.without-link').show();
    }
    var elements = jQuery('#lps_elements').val();
    if (elements != '') {
        sc += ' elements="' + elements + '"';
    }
    var css = jQuery('#lps_css').val();
    if (css != '') {
        sc += ' css="' + css + '"';
    }
    var taxonomy = jQuery('#lps_taxonomy').val();
    if (taxonomy != '') {
        sc += ' taxonomy="' + taxonomy + '"';
    }
    var term = jQuery('#lps_term').val();
    if (term != '') {
        sc += ' term="' + term + '"';
    }
    var dtag = jQuery('#lps_dtag').val();
    if (dtag != '') {
        sc += ' dtag="' + dtag + '"';
    } else {
        var tag = jQuery('#lps_tag').val();
        if (tag != '') {
            sc += ' tag="' + tag + '"';
        }
    }
    var id = jQuery('#lps_post_id').val();
    if (id != '') {
        sc += ' id="' + id + '"';
    }
    var parent = jQuery('#lps_parent_id').val();
    if (parent != '') {
        sc += ' parent="' + parent + '"';
    }
    var show_extra = jQuery('.lps_show_extra:checkbox').map(function () {
        return jQuery(this).is(":checked") ? jQuery(this).val() : '';
    }).get();
    if (show_extra != '') {
        show_extra = show_extra.filter(function (e) {
            return e
        });
        if (show_extra != '') {
            sc += ' show_extra="' + show_extra + '"';
        }
    }
    var orderby = jQuery('#lps_orderby').val();
    if (orderby != '') {
        sc += ' orderby="' + orderby + '"';
    }
    sc += ']';
    jQuery('#lps_preview_embed_shortcode').html(sc);
}

jQuery(document).ready(function () {
    jQuery('#lps_shortcode_button_open').click(function () {
        lps_init_embed_shortcode();
    });
    jQuery('#lps_button_embed_shortcode').click(lps_embed_shortcode);
    setTimeout(function () {
        if (typeof tinymce != 'undefined') {
            for (var i = 0; i < tinymce.editors.length; i++) {
                lps_init_embed_shortcode(tinymce.editors[i]);
            }
        }
    }, 2000);

    var visible = false;
    setInterval(function () {
        if (!visible) {
            if (jQuery('#TB_window').is(":visible") && jQuery('#TB_window .lps_shortcode_popup_container_table').is(":visible")) {
                visible = true;
                lps_recalculate_width();
            }
        }
    }, 2000);
});

