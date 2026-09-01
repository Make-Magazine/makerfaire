<?php
/**
 * WordPress Importer class for managing parsing of WXR files.
 *
 * Copied from WordPress Importer plugin
 * http://wordpress.org/extend/plugins/wordpress-importer/
 * Version: 0.6.1
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package GravityKit\GravityView\Lib\XmlParser
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Lib\XmlParser;

/**
 * WordPress Importer class for managing parsing of WXR files.
 *
 * @since 3.0.0
 */
class WXRParser {
	function parse( $file ) {
		// Attempt to use proper XML parsers first
		if ( extension_loaded( 'simplexml' ) ) {
			$parser = new WXRParserSimpleXML();
			$result = $parser->parse( $file );

			// If SimpleXML succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'SimpleXML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		} elseif ( extension_loaded( 'xml' ) ) {
			$parser = new WXRParserXML();
			$result = $parser->parse( $file );

			// If XMLParser succeeds or this is an invalid WXR file then return the results
			if ( ! is_wp_error( $result ) || 'XML_parse_error' != $result->get_error_code() ) {
				return $result;
			}
		}

		// We have a malformed XML file, so display the error and fallthrough to regex
		if ( isset( $result ) && defined( 'IMPORT_DEBUG' ) && IMPORT_DEBUG ) {
			echo '<pre>';
			if ( 'SimpleXML_parse_error' == $result->get_error_code() ) {
				foreach ( $result->get_error_data() as $error ) {
					echo $error->line . ':' . $error->column . ' ' . esc_html( $error->message ) . "\n";
				}
			} elseif ( 'XML_parse_error' == $result->get_error_code() ) {
				$error = $result->get_error_data();
				echo $error[0] . ':' . $error[1] . ' ' . esc_html( $error[2] );
			}
			echo '</pre>';
			echo '<p><strong>' . __( 'There was an error when reading this WXR file', 'gk-gravityview' ) . '</strong><br />';
			echo __( 'Details are shown above. The importer will now try again with a different parser...', 'gk-gravityview' ) . '</p>';
		}

		// use regular expressions if nothing else available or this is bad XML
		$parser = new WXRParserRegex();
		return $parser->parse( $file );
	}
}
