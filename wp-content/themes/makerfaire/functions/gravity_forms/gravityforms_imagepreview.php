<?php
/**
 * Upload image action for Gravity Forms
 * This script displays the thumbnail upon image upload for multi file field.
 * Change the ID to your form ID
 */
function wd_gravity_image_thumb_upload() {
    
	// change this to your page ID so it's not loaded on each page
	
    if ( is_page('641770') ) {        
		
		// change this to your form ID so we know where the images are uploaded to
        $upload_path = GFFormsModel::get_upload_url( '258' );
        
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

            //Converting Image to the base64
            
            function convertImgToBase64URL(url, callback, outputFormat){
                var img = new Image();
                img.crossOrigin = 'Anonymous';
                img.onload = function(){
                    var canvas = document.createElement('CANVAS'),
                    ctx = canvas.getContext('2d'), dataURL;
                    canvas.height = (300 * img.height)/img.width;
                    canvas.width = 300; //img.width;
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    dataURL = canvas.toDataURL(outputFormat);
                    callback(dataURL);
                    canvas = null; 
                };
                img.src = url;
                return url;
            }
           
            var previewURL = convertImgToBase64URL( myFilePath + temp_name , function(base64Img){
              var ffid = "#"+fid;
              $(ffid).attr("src", base64Img); 
              console.log('RESULT:', base64Img);   
            });

            html = '<img id="'+fid+"\" src='"+previewURL+"' style='max-width:150px;height:auto;'/><img class='gform_delete' " + "src='" + imagesUrl + "/delete.png' "+ "onclick='gformDeleteUploadedFile(" + formId + "," + fieldId + ", this);' " + "alt='" + strings.delete_file + "' title='" + strings.delete_file + "' />";
            return html;
        });

    });
    </script>

        <?php }

    }

add_action('wp_head','wd_gravity_image_thumb_upload');