<?php

/*
 * Used to fix orientation issues on iphone and android images
 */

add_action( 'gform_post_submission', 'fix_image_orientation', 10, 2 );

function triggerCronImg($entry, $form) {
  wp_schedule_single_event(time() + 1,'fix_image_orientation', array($entry, $form));
}
function fix_image_orientation( $entry, $form ) {
  $fields = $form['fields'];
  foreach ($fields as $field){
    //is this an image field
    if($field['type']=='fileupload'){
      //check if the field is set?
      if(isset($entry[$field['id']])) {
        $image = $entry[$field['id']];

        //$exif = exif_read_data($image);
        $exif = @read_exif_data( $image );

        if ( $exif['MimeType'] == 'image/jpeg' ) {
          $exif_orient = isset($exif['Orientation'])?$exif['Orientation']:0;
          $rotateImage = 0;

          if ( 6 == $exif_orient ) {
            $rotateImage = 90;
          } elseif ( 3 == $exif_orient ) {
            $rotateImage = 180;
          } elseif ( 8 == $exif_orient ) {
            $rotateImage = 270;
          }

          //rotate image
          if($rotateImage != 0) {
            rotateImage($image,$rotateImage);
          }
        }
      }
    }
    //if it is, check the image orientation and correct if necessary
  }
}

function rotateImage($image,$rotateImage) {
  $image = parse_url($image, PHP_URL_PATH);

  //To get the dir, use: dirname($path)
  $img_path = $_SERVER['DOCUMENT_ROOT'] . $image;
  @set_time_limit( 900 );
  if ( class_exists( 'Imagick' ) ) {

    do_action( 'imf_imagick_fix', $img_path, $rotateImage );

    $imagick = new Imagick();
    $ImagickPixel = new ImagickPixel();
    $imagick->readImage( $img_path );
    $imagick->rotateImage( $ImagickPixel, $rotateImage );
    $imagick->setImageOrientation( 1 );
    $imagick->writeImage( $img_path );
    $imagick->clear();
    $imagick->destroy();

    do_action( 'imf_imagick_fixed', $img_path, $rotateImage );

  } else {

    do_action( 'imf_fix', $img_path, $rotateImage );

    $rotateImage = -$rotateImage;
    $source = imagecreatefromjpeg( $img_path );
    $rotate = imagerotate( $source, $rotateImage, 0 );
    imagejpeg( $rotate, $img_path );

    do_action( 'imf_fixed', $img_path, $rotateImage );
  }
}