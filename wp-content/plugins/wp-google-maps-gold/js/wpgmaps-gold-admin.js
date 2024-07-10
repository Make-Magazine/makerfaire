jQuery(document).ready(function() {


	var index = jQuery('#wpgmaps_tabs ul').index(jQuery(window.location.hash));
	if (index > 0) { jQuery("#wpgmaps_tabs").tabs({active: index}); }

	var tgm_media_frame_default;

    jQuery(document.body).on('click.tgmOpenMediaManager', '#upload_default_rtlt_marker_btn', function(e){
        e.preventDefault();

        if ( tgm_media_frame_default ) {
            tgm_media_frame_default.open();
            return;
        }

        tgm_media_frame_default = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Default Marker Icon',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use as Default Marker'
            }
        });

        tgm_media_frame_default.on('select', function(){
            var media_attachment = tgm_media_frame_default.state().get('selection').first().toJSON();
            jQuery('#upload_default_rtlt_marker').val(media_attachment.url);
            jQuery("#wpgmza_mm_rtlt").html("<img src=\""+media_attachment.url+"\" />");
        });
        tgm_media_frame_default.open();
    });

    jQuery(".wpgmza_approve_device").click(function(){
    
        var wpgmza_action = jQuery(this).attr('wpgmza_action');
        var wpgmza_did = jQuery(this).attr('wpgmza_did');
        var wpgmza_otp = jQuery(this).attr('wpgmza_otp');

        var formData = {};
        formData.wpgmza_action = wpgmza_action;
        formData.did = wpgmza_did;
        formData.otp = wpgmza_otp;

        jQuery(this).find("i").removeClass("fa-check-circle");
        jQuery(this).find("i").addClass("fa-circle-o-notch");
        jQuery(this).find("i").addClass("fa-spin");

        wpgmza_gold_admin_ajax_call(formData, function(){
            location.reload();
        });

       
    });

    jQuery(".wpgmza_remove_device").click(function(){
    
        var wpgmza_action = jQuery(this).attr('wpgmza_action');
        var wpgmza_did = jQuery(this).attr('wpgmza_did');
        var wpgmza_otp = jQuery(this).attr('wpgmza_otp');

        var formData = {};
        formData.wpgmza_action = wpgmza_action;
        formData.did = wpgmza_did;
        formData.otp = wpgmza_otp;

        jQuery(this).find("i").removeClass("fa-times-circle");
        jQuery(this).find("i").addClass("fa-circle-o-notch");
        jQuery(this).find("i").addClass("fa-spin");

        wpgmza_gold_admin_ajax_call(formData, function(){
            location.reload();
        });
    });

    jQuery(".wpgmza_clear_device").click(function(){
    
        var wpgmza_action = jQuery(this).attr('wpgmza_action');
        var wpgmza_did = jQuery(this).attr('wpgmza_did');
        var wpgmza_mid = jQuery(this).attr('wpgmza_mid');

        var formData = {};
        formData.wpgmza_action = wpgmza_action;
        formData.did = wpgmza_did;
        formData.mid = wpgmza_mid;

        jQuery(this).find("i").removeClass("fa-trash-o");
        jQuery(this).find("i").addClass("fa-circle-o-notch");
        jQuery(this).find("i").addClass("fa-spin");

        wpgmza_gold_admin_ajax_call(formData, function(){
            location.reload();
        });
    });

    function wpgmza_gold_admin_ajax_call(formData, callback){
    
        formData.via_ajax = 1;
        

        jQuery.get(window.location.protocol + "//" + window.location.host + "", formData, function(data, status, xhr){
            if(status === "success"){
                if(parseInt(data) === 1){
                    callback();
                } else {
                    console.log("WPGM: Ajax Request Failed (2)");
                    console.log(data);
                }
            }else{
                console.log("WPGM: Ajax Request Failed (1)");
            }
        });
    }

        
    if(jQuery('#wpgmza_rtlt_route').attr('checked')){
        jQuery('.wpgmza_route_style_holder').fadeIn();
    }else{
        jQuery('.wpgmza_route_style_holder').fadeOut();
    }

    jQuery('#wpgmza_rtlt_route').on('change', function(){
        if(jQuery(this).attr('checked')){
            jQuery('.wpgmza_route_style_holder').fadeIn();
        }else{
            jQuery('.wpgmza_route_style_holder').fadeOut();
        }
    });
                                
});

