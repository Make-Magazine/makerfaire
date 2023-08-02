<?php
/**
 * Upload image action for Gravity Forms
 * This script displays the thumbnail upon image upload for multi file field.
 * Change the ID to your form ID
 */
function wd_gravity_image_thumb_upload() {
    
	// only run this on pages with gravity forms
    if ( /*is_page('641770')*/ has_gform() ) {        
		// change this to your form ID so we know where the images are uploaded to
        $gf_id = get_shortcode_attributes('gravityform')[0]['id'];
        $upload_path = GFFormsModel::get_upload_url( $gf_id );
     ?>

     <style>
        .ginput_preview_list {
            overflow: hidden;
            margin: 0 -1%;
        }

        .ginput_preview_list .ginput_preview{
            float: left;
            width: 170px;
            margin: 1%;
            display: block;
            position: relative;
            margin-bottom: 15px;
        }   

        .ginput_preview_list .gform_delete{
            position: absolute;
            top: 10px;
            right: 30px;
            background-color: white;
            border-radius: 20px;
            padding: 10px;   
        }
    </style>


    <script type="text/javascript"> 
    jQuery(document).ready(function($) {
        gform.addFilter('gform_file_upload_markup', function (html, file, up, strings, imagesUrl, response) {
            //Path of your temp file
            var myFilePath = '<?php echo $upload_path . '/tmp/'; ?>';  
            var temp_name = rgars( response, 'data/temp_filename' );      

            var formId = up.settings.multipart_params.form_id,
            fieldId = up.settings.multipart_params.field_id;
            
            var fileName = up.settings.multipart_params.gform_unique_id + '_input_' + fieldId +'_'+ file.target_name;
            var fid =  "fid"+  Math.ceil((Math.random() * (10000 - 1000)) + 1000); 
           
            var previewURL = myFilePath + temp_name;

            html = '<img id="'+fid+"\" src='"+previewURL+"' alt='"+fileName+"' style='max-width:150px;height:auto;'/><img class='gform_delete' " + "src='" + imagesUrl + "/delete.png' "+ "onclick='gformDeleteUploadedFile(" + formId + "," + fieldId + ", this);' " + "alt='" + strings.delete_file + "' title='" + strings.delete_file + "' />";
            return html;
        });

    });
    </script>

        <?php }

    }

add_action('wp_head','wd_gravity_image_thumb_upload');

// conditional to check whether Gravity Forms shortcode is on a page
function has_gform() {
    global $post;
    $all_content = get_the_content();
    if (strpos($all_content,'[gravityform') !== false) {
        return true;
    } else {
        return false;
    }
}