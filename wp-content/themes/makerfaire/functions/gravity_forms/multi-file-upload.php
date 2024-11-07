<?php
gw_multi_file_merge_tag()->register_settings( array(
	'form_id' => 296,
	'markup'  => array(
		array(
			'file_types' => array( 'jpg', 'jpeg', 'png', 'gif' ),
			'markup'     => '<div class="gw-image grid-image">
                                <a href="{url}" class="gravityview-fancybox gw-image-link" data-fancybox="gallery-296-36-{entry_id}" rel="gv-field-296-36-{entry_id}"><img src="{url}" width="100%" /></a>
                            </div>'
		),
		array(
			'file_types' => array( 'mp4', 'ogg', 'webm' ),
			'markup'     => '<div class="grid-video">
                                <video width="320" height="240" controls>
			                        <source src="{url}" type="video/{ext}">
			                        Your browser does not support the video tag.
			                    </video>
                            </div>'
        ),
		array(
			'file_types' => array( 'MOV' ),
			'markup'     => '<div class="grid-video">
                                <video width="320" height="240" controls>                                   
                                    <source src="{url}" >                                    
                                    Your browser does not support the video tag.
                                </video>
                            </div>'
        ),
        array(
            'file_types' => array( 'pdf', 'txt', 'doc', 'docx', 'ppt', 'eps', 'zip' ),
            'markup' => '<div class="gw-file gw-text grid-doc">  
                {ext} file uploaded

                <a href="{url}">Click here to view</a>                
            </div>'
        )        
	)
) );