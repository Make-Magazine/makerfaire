<?php
/**
 * Unique functions for Cloud EWWW I.O. plugin.
 *
 * This file contains functions that are unique to the EWWW IO Cloud plugin.
 *
 * @link https://ewww.io
 * @package EWWW_Image_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Make sure the cloud constant is defined.
 *
 * Check to see if the cloud constant is defined (which would mean we've already run init) and set it properly if not.
 */
function ewww_image_optimizer_cloud_init() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	ewww_image_optimizer_disable_tools();
	if ( ! defined( 'EWWW_IMAGE_OPTIMIZER_CLOUD' ) ) {
		define( 'EWWW_IMAGE_OPTIMIZER_CLOUD', true );
	}
	if ( ! defined( 'EWWW_IMAGE_OPTIMIZER_NOEXEC' ) ) {
		define( 'EWWW_IMAGE_OPTIMIZER_NOEXEC', true );
	}
	global $exactdn;
	if (
		! ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' ) &&
		empty( $_POST['ewww_image_optimizer_cloud_key'] ) &&
		( ! is_object( $exactdn ) || ! $exactdn->get_exactdn_domain() )
	) {
		add_action( 'network_admin_notices', 'ewww_image_optimizer_cloud_key_missing' );
		add_action( 'admin_notices', 'ewww_image_optimizer_cloud_key_missing' );
	}
	if ( 10 === (int) ewww_image_optimizer_get_option( 'ewww_image_optimizer_jpg_level' ) ) {
		ewww_image_optimizer_set_option( 'ewww_image_optimizer_jpg_level', 20 );
	}
	if ( 10 === (int) ewww_image_optimizer_get_option( 'ewww_image_optimizer_png_level' ) ) {
		ewww_image_optimizer_set_option( 'ewww_image_optimizer_png_level', 20 );
	}
	if ( ! empty( $_GET['ewwwio_cloud_activated'] ) ) {
		add_action( 'network_admin_notices', 'ewww_image_optimizer_install_cloud_plugin_success' );
		add_action( 'admin_notices', 'ewww_image_optimizer_install_cloud_plugin_success' );
	}
	ewwwio_memory( __FUNCTION__ );
}

/**
 * Let the user know activation was successful.
 */
function ewww_image_optimizer_install_cloud_plugin_success() {
	echo "<div id='ewww-image-optimizer-cloud-plugin-activated' class='notice notice-success is-dismissible'><p>" . esc_html__( 'Plugin' ) . ' <strong>' . esc_html__( 'activated' ) . '</strong>.</p></div>';
}

/**
 * Stub function from core.
 */
function ewww_image_optimizer_exec_init() {
}

/**
 * Another stub.
 */
function ewww_image_optimizer_tool_init() {
}

/**
 * And another stub.
 */
function ewww_image_optimizer_exec_check() {
}

/**
 * And one more stub...
 *
 * @param bool $quiet Not used, obviously.
 */
function ewww_image_optimizer_notice_utils( $quiet = null ) {
}

/**
 * Set some default option values.
 */
function ewww_image_optimizer_set_defaults() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Set defaults for all options that need to be autoloaded.
	add_option( 'ewww_image_optimizer_noauto', false );
	add_option( 'ewww_image_optimizer_disable_editor', false );
	add_option( 'ewww_image_optimizer_debug', false );
	add_option( 'ewww_image_optimizer_metadata_remove', true );
	add_option( 'ewww_image_optimizer_jpg_level', '10' );
	add_option( 'ewww_image_optimizer_png_level', '10' );
	add_option( 'ewww_image_optimizer_gif_level', '10' );
	add_option( 'ewww_image_optimizer_pdf_level', '0' );
	add_option( 'ewww_image_optimizer_exactdn', false );
	add_option( 'exactdn_all_the_things', true );
	add_option( 'exactdn_lossy', true );
	add_option( 'ewww_image_optimizer_lazy_load', false );
	add_option( 'ewww_image_optimizer_webp_for_cdn', false );

	add_site_option( 'ewww_image_optimizer_metadata_remove', true );
	add_site_option( 'ewww_image_optimizer_jpg_level', '30' );
	add_site_option( 'ewww_image_optimizer_png_level', '20' );
	add_site_option( 'ewww_image_optimizer_gif_level', '10' );
	add_site_option( 'ewww_image_optimizer_pdf_level', '10' );
	add_site_option( 'ewww_image_optimizer_backup_files', 1 );
	add_site_option( 'exactdn_all_the_things', true );
	add_site_option( 'exactdn_lossy', true );
}

/**
 * Checks which tools should be skipped.
 *
 * @return array {
 *     A list of tools to skip.
 *
 *     @type bool $jpegtran
 *     @type bool $optipng
 *     @type bool $gifsicle
 *     @type bool $pngout
 *     @type bool $pngquant
 *     @type bool $webp
 * }
 */
function ewww_image_optimizer_skip_tools() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	$skip['jpegtran'] = true;
	$skip['optipng']  = true;
	$skip['gifsicle'] = true;
	$skip['pngout']   = true;
	$skip['pngquant'] = true;
	$skip['webp']     = true;
	return $skip;
}

/**
 * See if the plugin should avoid the exec() function.
 *
 * It should, because this is the cloud version.
 */
function ewww_image_optimizer_define_noexec() {
	if ( defined( 'EWWW_IMAGE_OPTIMIZER_NOEXEC' ) ) {
		return;
	}
	define( 'EWWW_IMAGE_OPTIMIZER_NOEXEC', true );
}

/**
 * Display a notice in the admin for a missing API key.
 */
function ewww_image_optimizer_cloud_key_missing() {
	if ( ! function_exists( 'is_plugin_active_for_network' ) && is_multisite() ) {
		// Need to include the plugin library for the is_plugin_active function.
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	if ( is_multisite() && is_plugin_active_for_network( EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE_REL ) ) {
		$options_page = 'network/settings.php';
	} else {
		$options_page = 'options-general.php';
	}
	$settings_url = admin_url( "$options_page?page=" . plugin_basename( EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE ) );
	echo "<div id='ewww-image-optimizer-cloud-key-missing' class='notice notice-error'><p><strong>" .
		esc_html__( 'EWWW I.O. Cloud requires an API key or an ExactDN subscription to optimize images.', 'ewww-image-optimizer-cloud' ) .
		"</strong> <a href='https://ewww.io/plans/'>" . esc_html__( 'Purchase a subscription.', 'ewww-image-optimizer-cloud' ) .
		"</a> <a href='$settings_url'>" . esc_html__( 'Then, activate it on the settings page.', 'ewww-image-optimizer-cloud' ) . '</a></p></div>';
}

/**
 * Check the mimetype of the given file with various methods.
 *
 * Checks WebP files using a direct pattern match, then prefers fileinfo, getimagesize (for images
 * only), or mime_content_type.
 *
 * @param string $path The absolute path to the file.
 * @param string $case The type of file we are checking. Accepts 'i' for
 *                     images/pdfs or 'b' for binary.
 * @return bool|string A valid mime-type or false.
 */
function ewww_image_optimizer_mimetype( $path, $case ) {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	ewwwio_debug_message( "testing mimetype: $path" );
	$type = false;
	if ( 'i' === $case && ewww_image_optimizer_stream_wrapped( $path ) ) {
		return ewww_image_optimizer_quick_mimetype( $path );
	}
	if ( 'i' === $case && preg_match( '/^RIFF.+WEBPVP8/', file_get_contents( $path, null, null, 0, 16 ) ) ) {
		/* return 'image/webp'; */
	}
	if ( 'i' === $case ) {
		$fhandle = fopen( $path, 'rb' );
		if ( $fhandle ) {
			// Read first 12 bytes, which equates to 24 hex characters.
			$magic = bin2hex( fread( $fhandle, 12 ) );
			if ( 0 === strpos( $magic, '52494646' ) && 16 === strpos( $magic, '57454250' ) ) {
				$type = 'image/webp';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			if ( 'ffd8ff' === substr( $magic, 0, 6 ) ) {
				$type = 'image/jpeg';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			if ( '89504e470d0a1a0a' === substr( $magic, 0, 16 ) ) {
				$type = 'image/png';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			if ( '474946383761' === substr( $magic, 0, 12 ) || '474946383961' === substr( $magic, 0, 12 ) ) {
				$type = 'image/gif';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			if ( '25504446' === substr( $magic, 0, 8 ) ) {
				$type = 'application/pdf';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			ewwwio_debug_message( "match not found for image: $magic" );
		} else {
			ewwwio_debug_message( 'could not open for reading' );
		}
	}
	if ( 'b' === $case ) {
		$fhandle = fopen( $path, 'rb' );
		if ( $fhandle ) {
			// Read first 4 bytes, which equates to 8 hex characters.
			$magic = bin2hex( fread( $fhandle, 4 ) );
			// Mac (Mach-O) binary.
			if ( 'cffaedfe' === $magic || 'feedface' === $magic || 'feedfacf' === $magic || 'cefaedfe' === $magic || 'cafebabe' === $magic ) {
				$type = 'application/x-executable';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			// ELF (Linux or BSD) binary.
			if ( '7f454c46' === $magic ) {
				$type = 'application/x-executable';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			// MS (DOS) binary.
			if ( '4d5a9000' === $magic ) {
				$type = 'application/x-executable';
				ewwwio_debug_message( "ewwwio type: $type" );
				return $type;
			}
			ewwwio_debug_message( "match not found for binary: $magic" );
		} else {
			ewwwio_debug_message( 'could not open for reading' );
		}
	}
	return false;
}

/**
 * Process an image.
 *
 * @param string $file Full absolute path to the image file.
 * @param int    $gallery_type 1=WordPress, 2=nextgen, 3=flagallery, 4=aux_images, 5=image editor,
 *                             6=imagestore.
 * @param bool   $converted True if this is a resize and the full image was converted to a
 *                          new format.
 * @param bool   $new True if this is a new image, so it should attempt conversion regardless of
 *                    previous results.
 * @param bool   $fullsize True if this is a full size (original) image.
 * @return array {
 *     Status of the optimization attempt.
 *
 *     @type string $file The filename or false on error.
 *     @type string $results The results of the optimization.
 *     @type bool $converted True if an image changes formats.
 *     @type string The original filename if converted.
 * }
 */
function ewww_image_optimizer( $file, $gallery_type = 4, $converted = false, $new = false, $fullsize = false ) {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	session_write_close();
	if ( function_exists( 'wp_raise_memory_limit' ) ) {
		wp_raise_memory_limit( 'image' );
	}
	// If the plugin gets here without initializing, we need to run through some things first.
	if ( ! defined( 'EWWW_IMAGE_OPTIMIZER_CLOUD' ) ) {
		ewww_image_optimizer_cloud_init();
	}
	$bypass_optimization = apply_filters( 'ewww_image_optimizer_bypass', false, $file );
	if ( true === $bypass_optimization ) {
		ewwwio_debug_message( "optimization bypassed: $file" );
		// Tell the user optimization was skipped.
		return array( false, __( 'Optimization skipped', 'ewww-image-optimizer-cloud' ), $converted, $file );
	}
	global $ewww_image;
	global $s3_uploads_image;
	if ( empty( $s3_uploads_image ) ) {
		$s3_uploads_image = false;
	}
	if ( ewww_image_optimizer_stream_wrapped( $file ) && class_exists( 'S3_Uploads' ) ) {
		$s3_uploads_image    = $file;
		$s3_uploads_instance = S3_Uploads::get_instance();
		$s3_uploads_instance->setup();
		if ( ! function_exists( 'wp_tempnam' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		}
		$file = $s3_uploads_instance->copy_image_from_s3( $file );
	}
	// Initialize the original filename.
	$original = $file;
	$result   = '';
	// Check that the file exists.
	if ( false === is_file( $file ) ) {
		/* translators: %s: Image filename */
		$msg = sprintf( __( 'Could not find %s', 'ewww-image-optimizer-cloud' ), $file );
		ewwwio_debug_message( "file doesn't appear to exist: $file" );
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		return array( false, $msg, $converted, $original );
	}
	// Check that the file is writable.
	if ( false === is_writable( $file ) ) {
		/* translators: %s: Image filename */
		$msg = sprintf( __( '%s is not writable', 'ewww-image-optimizer-cloud' ), $file );
		ewwwio_debug_message( "couldn't write to the file $file" );
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		return array( false, $msg, $converted, $original );
	}
	$file_perms = 'unknown';
	if ( ewww_image_optimizer_function_exists( 'fileperms' ) ) {
		$file_perms = substr( sprintf( '%o', fileperms( $file ) ), -4 );
	}
	$file_owner = 'unknown';
	$file_group = 'unknown';
	if ( ewww_image_optimizer_function_exists( 'posix_getpwuid' ) ) {
		$file_owner = posix_getpwuid( fileowner( $file ) );
		$file_owner = 'xxxxxxxx' . substr( $file_owner['name'], -4 );
	}
	if ( ewww_image_optimizer_function_exists( 'posix_getgrgid' ) ) {
		$file_group = posix_getgrgid( filegroup( $file ) );
		$file_group = 'xxxxx' . substr( $file_group['name'], -5 );
	}
	ewwwio_debug_message( "permissions: $file_perms, owner: $file_owner, group: $file_group" );
	$type = ewww_image_optimizer_mimetype( $file, 'i' );
	if ( ! $type ) {
		ewwwio_debug_message( 'could not find any functions for mimetype detection' );
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		// Otherwise we store an error message since we couldn't get the mime-type.
		return array( false, __( 'Unknown file type', 'ewww-image-optimizer-cloud' ), $converted, $original );
	}
	// Not an image or pdf.
	if ( strpos( $type, 'image' ) === false && strpos( $type, 'pdf' ) === false ) {
		ewwwio_debug_message( "unsupported type: $type" );
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		return array( false, __( 'Unsupported file type', 'ewww-image-optimizer-cloud' ) . ": $type", $converted, $original );
	}
	if ( ! is_object( $ewww_image ) || ! $ewww_image instanceof EWWW_Image || $ewww_image->file !== $file ) {
		$ewww_image = new EWWW_Image( 0, '', $file );
	}
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_lossy_skip_full' ) && $fullsize ) {
		$skip_lossy = true;
	} else {
		$skip_lossy = false;
	}
	if ( ini_get( 'max_execution_time' ) < 90 && ewww_image_optimizer_stl_check() ) {
		set_time_limit( 0 );
	}
	// Get the original image size.
	$orig_size = ewww_image_optimizer_filesize( $file );
	ewwwio_debug_message( "original filesize: $orig_size" );
	if ( $orig_size < ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_size' ) ) {
		ewwwio_debug_message( "optimization bypassed due to filesize: $file" );
		// Tell the user optimization was skipped.
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		return array( false, __( 'Optimization skipped', 'ewww-image-optimizer-cloud' ), $converted, $file );
	}
	if ( 'image/png' === $type && ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_png_size' ) && $orig_size > ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_png_size' ) ) {
		ewwwio_debug_message( "optimization bypassed due to filesize: $file" );
		// Tell the user optimization was skipped.
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		return array( false, __( 'Optimization skipped', 'ewww-image-optimizer-cloud' ), $converted, $file );
	}
	$backup_hash = '';
	// Initialize $new_size with a zero.
	$new_size = 0;
	// Set the optimization process to OFF.
	$optimize = false;
	// Toggle the convert process to ON.
	$convert = true;
	// Allow other plugins to mangle the image however they like prior to optimization.
	do_action( 'ewww_image_optimizer_pre_optimization', $file, $type, $fullsize );
	// Run the appropriate optimization/conversion for the mime-type.
	switch ( $type ) {
		case 'image/jpeg':
			// If jpg2png conversion is enabled, and this image is in the WordPress media library.
			if ( ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_jpg_to_png' ) && 1 === (int) $gallery_type ) || ! empty( $_REQUEST['ewww_convert'] ) ) {
				// Generate the filename for a PNG.
				if ( $converted ) { // If this is a resize version...
					// just change the file extension.
					$pngfile = preg_replace( '/\.\w+$/', '.png', $file );
				} else { // If this is a full size image...
					// get a unique filename for the png image.
					list( $pngfile, $filenum ) = ewww_image_optimizer_unique_filename( $file, '.png' );
				}
			} else { // Otherwise, set it to OFF.
				$convert = false;
				$pngfile = '';
			}
			// Check for previous optimization, so long as the force flag is on and this isn't a new image that needs converting.
			if ( empty( $_REQUEST['ewww_force'] ) && ! ( $new && $convert ) ) {
				$results_msg = ewww_image_optimizer_check_table( $file, $orig_size );
				if ( $results_msg ) {
					$file = ewww_image_optimizer_s3_uploads_image_cleanup( $file );
					return array( $file, $results_msg, $converted, $original );
				}
			}
			$ewww_image->level = (int) ewww_image_optimizer_get_option( 'ewww_image_optimizer_jpg_level' );
			if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_jpg_level' ) > 0 ) {
				list( $file, $converted, $result, $new_size, $backup_hash ) = ewww_image_optimizer_cloud_optimizer( $file, $type, $convert, $pngfile, 'image/png', $skip_lossy );
				if ( $converted ) {
					if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_delete_originals' ) ) {
						// Delete the original JPG.
						unlink( $original );
					}
					$converted = $filenum;
					ewww_image_optimizer_webp_create( $file, $new_size, 'image/png', null );
				} else {
					ewww_image_optimizer_webp_create( $file, $new_size, $type, null );
				}
			} else {
				ewww_image_optimizer_webp_create( $file, $orig_size, $type, null );
			}
			break;
		case 'image/png':
			// Png2jpg conversion is turned on, and the image is in the WordPress media library.
			// We let the API check for transparency and conversion based on the fill color sent to the API.
			if ( ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_png_to_jpg' ) || ! empty( $_REQUEST['ewww_convert'] ) )
				&& 1 === (int) $gallery_type && ! $skip_lossy ) {
				ewwwio_debug_message( 'PNG to JPG conversion turned on' );
				// If the user set a fill background for transparency.
				$cloud_background = '';
				$background       = ewww_image_optimizer_jpg_background();
				if ( $background ) {
					$cloud_background = "#$background";
				}
				$quality = ewww_image_optimizer_jpg_quality();
				$quality = $quality ? $quality : '82';
				// If this is a resize version...
				if ( $converted ) { // If this is a resize version...
					// Just replace the file extension with .jpg.
					$jpgfile = preg_replace( '/\.\w+$/', '.jpg', $file );
				} else { // If this is a full version...
					// Construct the filename for the new JPG.
					list( $jpgfile, $filenum ) = ewww_image_optimizer_unique_filename( $file, '.jpg' );
				}
			} else {
				ewwwio_debug_message( 'PNG to JPG conversion turned off' );
				// Turn the conversion process OFF.
				$convert          = false;
				$jpgfile          = '';
				$r                = null;
				$g                = null;
				$b                = null;
				$cloud_background = '';
				$quality          = null;
			} // End if().
			// Check for previous optimization, so long as the force flag is on and this isn't a new image that needs converting.
			if ( empty( $_REQUEST['ewww_force'] ) && ! ( $new && $convert ) ) {
				$results_msg = ewww_image_optimizer_check_table( $file, $orig_size );
				if ( $results_msg ) {
					$file = ewww_image_optimizer_s3_uploads_image_cleanup( $file );
					return array( $file, $results_msg, $converted, $original );
				}
			}
			$ewww_image->level = (int) ewww_image_optimizer_get_option( 'ewww_image_optimizer_png_level' );
			if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_png_level' ) > 0 ) {
				list( $file, $converted, $result, $new_size, $backup_hash ) = ewww_image_optimizer_cloud_optimizer( $file, $type, $convert, $jpgfile, 'image/jpeg', $skip_lossy, $cloud_background, $quality );
				if ( $converted ) {
					if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_delete_originals' ) ) {
						// Delete the original JPG.
						unlink( $original );
					}
					$converted = $filenum;
					ewww_image_optimizer_webp_create( $file, $new_size, 'image/jpeg', null );
				} else {
					ewww_image_optimizer_webp_create( $file, $new_size, $type, null );
				}
			} else {
				ewww_image_optimizer_webp_create( $file, $orig_size, $type, null );
			}
			break;
		case 'image/gif':
			// If gif2png is turned on, and the image is in the WordPress media library.
			if ( ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_gif_to_png' ) || ! empty( $_REQUEST['ewww_convert'] ) )
				&& 1 === (int) $gallery_type && ! ewww_image_optimizer_is_animated( $file ) ) {
				// Generate the filename for a PNG.
				if ( $converted ) { // If this is a resize version...
					// Just change the file extension.
					$pngfile = preg_replace( '/\.\w+$/', '.png', $file );
				} else { // If this is the full version...
					// Construct the filename for the new PNG.
					list( $pngfile, $filenum ) = ewww_image_optimizer_unique_filename( $file, '.png' );
				}
			} else {
				// Turn conversion OFF.
				$convert = false;
				$pngfile = '';
			}
			// Check for previous optimization, so long as the force flag is on and this isn't a new image that needs converting.
			if ( empty( $_REQUEST['ewww_force'] ) && ! ( $new && $convert ) ) {
				$results_msg = ewww_image_optimizer_check_table( $file, $orig_size );
				if ( $results_msg ) {
					$file = ewww_image_optimizer_s3_uploads_image_cleanup( $file );
					return array( $file, $results_msg, $converted, $original );
				}
			}
			$ewww_image->level = (int) ewww_image_optimizer_get_option( 'ewww_image_optimizer_gif_level' );
			if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_gif_level' ) > 0 ) {
				list( $file, $converted, $result, $new_size, $backup_hash ) = ewww_image_optimizer_cloud_optimizer( $file, $type, $convert, $pngfile, 'image/png', $skip_lossy );
				if ( $converted ) {
					if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_delete_originals' ) ) {
						// Delete the original JPG.
						unlink( $original );
					}
					$converted = $filenum;
					ewww_image_optimizer_webp_create( $file, $new_size, 'image/png', null );
				}
			}
			break;
		case 'application/pdf':
			if ( empty( $_REQUEST['ewww_force'] ) ) {
				$results_msg = ewww_image_optimizer_check_table( $file, $orig_size );
				if ( $results_msg ) {
					$file = ewww_image_optimizer_s3_uploads_image_cleanup( $file );
					return array( $file, $results_msg, false, $original );
				}
			}
			$ewww_image->level = (int) ewww_image_optimizer_get_option( 'ewww_image_optimizer_pdf_level' );
			if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_pdf_level' ) > 0 ) {
				list( $file, $converted, $result, $new_size, $backup_hash ) = ewww_image_optimizer_cloud_optimizer( $file, $type );
			}
			break;
		default:
			// if not a JPG, PNG, or GIF, tell the user we don't work with strangers.
			ewww_image_optimizer_s3_uploads_image_cleanup( $file );
			return array( false, __( 'Unsupported file type', 'ewww-image-optimizer-cloud' ) . ": $type", $converted, $original );
	} // End switch().
	// allow other plugins to run operations on the images after optimization.
	// NOTE: it is recommended to do any image modifications prior to optimization, otherwise you risk un-optimizing your images here.
	do_action( 'ewww_image_optimizer_post_optimization', $file, $type, $fullsize );
	// if their cloud api license limit has been exceeded.
	if ( 'exceeded' === $result ) {
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		return array( false, __( 'License exceeded', 'ewww-image-optimizer-cloud' ), $converted, $original );
	}
	if ( ! empty( $new_size ) ) {
		// Set correct file permissions.
		$stat  = stat( dirname( $file ) );
		$perms = $stat['mode'] & 0000666; // same permissions as parent folder, strip off the executable bits.
		chmod( $file, $perms );
		$results_msg = ewww_image_optimizer_update_table( $file, $new_size, $orig_size, $original, $backup_hash );
		if ( $s3_uploads_image && strpos( $file, 's3-uploads' ) === false ) {
			copy( $file, $s3_uploads_image );
		}
		$file = ewww_image_optimizer_s3_uploads_image_cleanup( $file );
		ewwwio_memory( __FUNCTION__ );
		return array( $file, $results_msg, $converted, $original );
	} else {
		ewww_image_optimizer_s3_uploads_image_cleanup( $file );
	}
	ewwwio_memory( __FUNCTION__ );
	// otherwise, send back the filename, the results (some sort of error message), the $converted flag, and the name of the original image.
	return array( false, $result, $converted, $original );
}

/**
 * Creates webp images alongside JPG and PNG files.
 *
 * @param string $file The name of the JPG/PNG file.
 * @param int    $orig_size The filesize of the JPG/PNG file.
 * @param string $type The mime-type of the incoming file.
 * @param string $tool The path to the cwebp binary, if installed.
 * @param bool   $recreate True to keep the .webp image even if it is larger than the JPG/PNG.
 */
function ewww_image_optimizer_webp_create( $file, $orig_size, $type, $tool, $recreate = false ) {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	$webpfile    = $file . '.webp';
	$bypass_webp = apply_filters( 'ewww_image_optimizer_bypass_webp', false, $file );
	if ( true === $bypass_webp ) {
		ewwwio_debug_message( "webp generation bypassed: $file" );
		return;
	} elseif ( ! ewww_image_optimizer_get_option( 'ewww_image_optimizer_webp' ) ) {
		return;
	} elseif ( is_file( $webpfile ) && empty( $_REQUEST['ewww_force'] ) && ! $recreate ) {
		ewwwio_debug_message( 'webp file exists, not forcing or recreating' );
		return;
	}
	ewww_image_optimizer_cloud_optimizer( $file, $type, false, $webpfile, 'image/webp' );
	$webp_size = ewww_image_optimizer_filesize( $webpfile );
	ewwwio_debug_message( "webp is $webp_size vs. $type is $orig_size" );
	if ( is_file( $webpfile ) && $orig_size < $webp_size && ! ewww_image_optimizer_get_option( 'ewww_image_optimizer_webp_force' ) ) {
		ewwwio_debug_message( 'webp file was too big, deleting' );
		unlink( $webpfile );
	} elseif ( is_file( $webpfile ) ) {
		// Set correct file permissions.
		$stat  = stat( dirname( $webpfile ) );
		$perms = $stat['mode'] & 0000666; // same permissions as parent folder, strip off the executable bits.
		chmod( $webpfile, $perms );
	}
	ewwwio_memory( __FUNCTION__ );
}

/**
 * Replaces the translation url on the settings page with one for the 'cloud' plugin.
 *
 * @param string $output This will be the filtered settings page which converts from array to string at priority 10.
 * @return string The same settings page, but with a new translation site url.
 */
function ewww_image_optimizer_translation_site_url( $output ) {
	$output = preg_replace( '/translate.wordpress.org.projects.wp-plugins.ewww-image-optimizer\//', 'translate.wordpress.org/projects/wp-plugins/ewww-image-optimizer-cloud/', $output );
	return $output;
}
add_filter( 'ewww_image_optimizer_settings', 'ewww_image_optimizer_translation_site_url', 11 );
