<?php
/**
 * Frontend entry export bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\Foundation\Components\SecureDownload;
use GravityKit\GravityView\View\View;
use Throwable;
use WP_Error;
use ZipArchive;

/**
 * Exports selected entries as CSV or TSV files.
 *
 * @since 3.0.0
 */
final class ExportCsvAction implements BulkAction {
	const FORMAT_CSV      = 'csv';
	const FORMAT_TSV      = 'tsv';
	const OUTPUT_SINGLE   = 'single';
	const OUTPUT_SEPARATE = 'separate';
	const CLEANUP_HOOK    = 'gk_gravityview_bulk_actions_cleanup_export';

	/**
	 * Registers background cleanup hooks.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( self::CLEANUP_HOOK, [ __CLASS__, 'cleanup_scheduled_export' ], 10, 3 );
	}

	/**
	 * Cleans up a scheduled export directory.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dir     Export directory.
	 * @param int    $view_id View ID.
	 * @param int    $blog_id Blog ID.
	 *
	 * @return void
	 */
	public static function cleanup_scheduled_export( $dir, $view_id, $blog_id = 0 ) {
		$dir     = (string) $dir;
		$view_id = absint( $view_id );
		$blog_id = absint( $blog_id );

		if ( is_multisite() && $blog_id && get_current_blog_id() !== $blog_id ) {
			switch_to_blog( $blog_id );

			try {
				self::cleanup_scheduled_export( $dir, $view_id );
			} finally {
				restore_current_blog();
			}

			return;
		}

		if ( '' === $dir || ! self::is_export_directory_path_for_view( $dir, $view_id ) ) {
			return;
		}

		self::delete_path( $dir, true );
	}

	/**
	 * Cleans generated export files and scheduled cleanup events for the current site.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function cleanup_all_exports_for_current_site() {
		self::unschedule_cleanup_events();

		$root = self::get_export_site_root();

		if ( '' === $root || ! self::is_export_site_root( $root ) ) {
			return;
		}

		self::delete_path( $root, true );
	}

	/**
	 * Unschedules all export cleanup events for the current site.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private static function unschedule_cleanup_events() {
		if ( function_exists( 'wp_unschedule_hook' ) ) {
			wp_unschedule_hook( self::CLEANUP_HOOK );
			return;
		}

		foreach ( (array) _get_cron_array() as $timestamp => $hooks ) {
			if ( empty( $hooks[ self::CLEANUP_HOOK ] ) || ! is_array( $hooks[ self::CLEANUP_HOOK ] ) ) {
				continue;
			}

			foreach ( $hooks[ self::CLEANUP_HOOK ] as $event ) {
				wp_unschedule_event( (int) $timestamp, self::CLEANUP_HOOK, isset( $event['args'] ) ? (array) $event['args'] : [] );
			}
		}
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function key() {
		return 'export_csv';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function config() {
		return [
			'label'              => __( 'Export Entries', 'gk-gravityview' ),
			'callback'           => [ $this, 'process' ],
			'dismiss_callback'   => [ $this, 'dismiss' ],
			'available_callback' => [ $this, 'is_available' ],
			'background'         => [
				'enabled'           => true,
				'batch_size'        => 1,
				'sticky_result'     => true,
				'complete_callback' => [ $this, 'complete' ],
			],
			'settings_schema'    => [
				'format'     => [
					'type'    => 'radio',
					'label'   => __( 'File type', 'gk-gravityview' ),
					'default' => self::FORMAT_CSV,
					'options' => [
						self::FORMAT_CSV => 'CSV',
						self::FORMAT_TSV => 'TSV',
					],
				],
				'output'     => [
					'type'    => 'radio',
					'label'   => __( 'Output', 'gk-gravityview' ),
					'default' => self::OUTPUT_SINGLE,
					'options' => [
						self::OUTPUT_SINGLE   => __( 'Single file', 'gk-gravityview' ),
						self::OUTPUT_SEPARATE => __( 'Separate files in a ZIP', 'gk-gravityview' ),
					],
				],
				'use_labels' => [
					'type'    => 'checkbox',
					'label'   => __( 'Use labels instead of field IDs', 'gk-gravityview' ),
					'default' => 1,
					'desc'    => __( 'Use field labels for file headers.', 'gk-gravityview' ),
				],
			],
			'lock'               => false,
		];
	}

	/**
	 * Returns whether the current View can export entries.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view   View.
	 * @param array $action Action configuration.
	 *
	 * @return bool
	 */
	public function is_available( View $view, array $action = [] ) {
		if ( self::OUTPUT_SEPARATE === $this->get_output_mode( $action ) && ! class_exists( ZipArchive::class ) ) {
			return false;
		}

		return ! is_wp_error( $view->can_render( [ 'csv' ], new \GV\Frontend_Request() ) );
	}

	/**
	 * Writes selected entry export files.
	 *
	 * Synchronous requests also create the final download immediately. Background
	 * requests write files per batch and let complete() create the final download
	 * after the last batch has run.
	 *
	 * @since 3.0.0
	 *
	 * @param int[]  $entry_ids  Entry IDs.
	 * @param array  $entries    Entries keyed by ID.
	 * @param View   $view       View context.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $context    Optional background context.
	 *
	 * @return array|WP_Error
	 */
	public function process( array $entry_ids, array $entries, View $view, $action_key = '', array $action = [], array $context = [] ) {
		$format = $this->get_export_format( $action );
		$output = $this->get_output_mode( $action );
		$export = $this->get_export_location( $view, $context, $format );

		if ( is_wp_error( $export ) ) {
			return $export;
		}

		$result = self::OUTPUT_SEPARATE === $output
			? $this->write_separate_entry_files( $entries, $view, $action, $export, $format )
			: $this->write_single_file_part( $entries, $view, $action, $export, $format, $context );

		if ( is_wp_error( $result ) ) {
			$this->schedule_cleanup( $export );

			return $result;
		}

		if ( $this->is_background_context( $context ) ) {
			return $this->add_cleanup_result( $result, $export );
		}

		$count = self::OUTPUT_SEPARATE === $output ? $this->count_exported_entry_files( $export['dir'], $format ) : $this->count_exported_rows( $export['dir'] );

		$download = self::OUTPUT_SEPARATE === $output
			? $this->create_zip( $export, $format )
			: $this->create_single_file( $export, $format );

		if ( is_wp_error( $download ) ) {
			$this->schedule_cleanup( $export );

			return $download;
		}

		return [
			'processed' => $count,
			'failed'    => $result['failed'],
			'message'   => $this->get_success_message( $count, $download['url'], $format, $download['label'] ),
		];
	}

	/**
	 * Creates the final download after the final background batch.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $context    Background context.
	 *
	 * @return array|WP_Error
	 */
	public function complete( View $view, $action_key, array $action, array $context ) {
		$format = $this->get_export_format( $action );
		$output = $this->get_output_mode( $action );
		$export = $this->get_export_location( $view, $context, $format );

		if ( is_wp_error( $export ) ) {
			return $export;
		}

		$count = self::OUTPUT_SEPARATE === $output ? $this->count_exported_entry_files( $export['dir'], $format ) : $this->count_exported_rows( $export['dir'] );

		$download = self::OUTPUT_SEPARATE === $output
			? $this->create_zip( $export, $format )
			: $this->create_single_file( $export, $format );

		if ( is_wp_error( $download ) ) {
			$this->schedule_cleanup( $export );

			return $download;
		}

		return [
			'notice'  => [
				'message' => $this->get_success_message( $count, $download['url'], $format, $download['label'] ),
			],
			'cleanup' => [
				'blog_id' => (int) ( $export['blog_id'] ?? get_current_blog_id() ),
				'dir'     => $export['dir'],
				'view_id' => (int) ( $export['view_id'] ?? 0 ),
			],
		];
	}

	/**
	 * Cleans up generated export files when a sticky result notice is dismissed.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $result     Stored result data.
	 * @param string $token      Result token.
	 *
	 * @return void
	 */
	public function dismiss( View $view, $action_key, array $action, array $result, $token ) {
		$cleanup = isset( $result['result']['cleanup'] ) && is_array( $result['result']['cleanup'] ) ? $result['result']['cleanup'] : [];
		$dir     = (string) ( $cleanup['dir'] ?? '' );

		if ( '' === $dir || ! $this->is_export_directory_for_view( $dir, $view ) ) {
			return;
		}

		self::delete_path( $dir, true );
	}

	/**
	 * Writes one file per entry for the selected entries.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $entries Entry arrays.
	 * @param View   $view    View.
	 * @param array  $action  Action configuration.
	 * @param array  $export  Export location.
	 * @param string $format  Export format.
	 *
	 * @return array|WP_Error
	 */
	private function write_separate_entry_files( array $entries, View $view, array $action, array $export, $format ) {
		$processed = 0;
		$failed    = 0;

		foreach ( $entries as $entry ) {
			$gv_entry = \GV\GF_Entry::from_entry( $entry );

			if ( ! $gv_entry ) {
				++$failed;
				continue;
			}

			$file = $this->render_entry_file( $view, $gv_entry, $action, $format );

			if ( is_wp_error( $file ) ) {
				++$failed;
				continue;
			}

			$path = trailingslashit( $export['dir'] ) . $this->get_entry_filename( (int) $gv_entry->ID, $format );

			if ( is_wp_error( $this->write_file( $path, $file ) ) ) {
				++$failed;
				continue;
			}

			++$processed;
		}

		if ( ! $processed && $failed ) {
			return new WP_Error( 'gravityview_bulk_export_csv_failed', __( 'The selected entries could not be exported.', 'gk-gravityview' ) );
		}

		return [
			'processed' => $processed,
			'failed'    => $failed,
		];
	}

	/**
	 * Writes one row-part file for a single-file export.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $entries Entry arrays.
	 * @param View   $view    View.
	 * @param array  $action  Action configuration.
	 * @param array  $export  Export location.
	 * @param string $format  Export format.
	 * @param array  $context Optional background context.
	 *
	 * @return array|WP_Error
	 */
	private function write_single_file_part( array $entries, View $view, array $action, array $export, $format, array $context = [] ) {
		$gv_entries = [];
		$failed     = 0;

		foreach ( $entries as $entry ) {
			$gv_entry = \GV\GF_Entry::from_entry( $entry );

			if ( ! $gv_entry ) {
				++$failed;
				continue;
			}

			$gv_entries[] = $gv_entry;
		}

		if ( [] === $gv_entries ) {
			return new WP_Error( 'gravityview_bulk_export_csv_failed', __( 'The selected entries could not be exported.', 'gk-gravityview' ) );
		}

		$rows = $this->get_export_rows( $view, $gv_entries, $action );

		if ( is_wp_error( $rows ) ) {
			return $rows;
		}

		$header_path = trailingslashit( $export['dir'] ) . $this->get_header_filename( $format );

		if ( ! file_exists( $header_path ) ) {
			$header_file = $this->render_tabular_file( [ $rows['headers'] ], $format, true, $view->form ? $view->form->form : null );

			if ( is_wp_error( $header_file ) ) {
				return $header_file;
			}

			$header_written = $this->write_file( $header_path, $header_file );

			if ( is_wp_error( $header_written ) ) {
				return $header_written;
			}
		}

		$part_path = trailingslashit( $export['dir'] ) . $this->get_part_filename( $context, $format );
		$part_file = $this->render_tabular_file( $rows['rows'], $format, false, $view->form ? $view->form->form : null );

		if ( is_wp_error( $part_file ) ) {
			return $part_file;
		}

		$part_written = $this->write_file( $part_path, $part_file );

		if ( is_wp_error( $part_written ) ) {
			return $part_written;
		}

		$count_written = $this->write_file( trailingslashit( $export['dir'] ) . $this->get_part_count_filename( $context ), (string) count( $rows['rows'] ) );

		if ( is_wp_error( $count_written ) ) {
			return $count_written;
		}

		return [
			'processed' => count( $rows['rows'] ),
			'failed'    => $failed,
		];
	}

	/**
	 * Renders a single entry using GravityView's CSV field rendering.
	 *
	 * @since 3.0.0
	 *
	 * @param View      $view   View.
	 * @param \GV\Entry $entry  Entry.
	 * @param array     $action Action configuration.
	 * @param string    $format Export format.
	 *
	 * @return string|WP_Error
	 */
	private function render_entry_file( View $view, \GV\Entry $entry, array $action, $format ) {
		$rows = $this->get_export_rows( $view, [ $entry ], $action );

		if ( is_wp_error( $rows ) ) {
			return $rows;
		}

		return $this->render_tabular_file( array_merge( [ $rows['headers'] ], $rows['rows'] ), $format, true, $view->form ? $view->form->form : null );
	}

	/**
	 * Returns tabular headers and rows for entries.
	 *
	 * @since 3.0.0
	 *
	 * @param View        $view    View.
	 * @param \GV\Entry[] $entries Entries.
	 * @param array       $action  Action configuration.
	 *
	 * @return array|WP_Error
	 */
	private function get_export_rows( View $view, array $entries, array $action ) {
		$first_entry = reset( $entries );

		if ( ! $first_entry instanceof \GV\Entry ) {
			return new WP_Error( 'gravityview_bulk_export_csv_failed', __( 'The selected entries could not be exported.', 'gk-gravityview' ) );
		}

		$headers    = [];
		$rows       = [];
		$renderer   = new \GV\Field_Renderer();
		$request    = new \GV\Frontend_Request();
		$settings   = isset( $action['settings'] ) && is_array( $action['settings'] ) ? $action['settings'] : [];
		$use_labels = ! array_key_exists( 'use_labels', $settings ) || ! empty( $settings['use_labels'] );
		$fields     = $this->get_csv_fields( $view, $first_entry );

		foreach ( $fields as $field ) {
			$field  = clone $field;
			$source = \GV\View::get_source( $field, $view );
			$label  = $use_labels ? $field->get_label( $view, $source, $first_entry ) : '';

			$headers[] = $this->normalize_cell_value( $label ? $label : $field->ID );
		}

		foreach ( $entries as $entry ) {
			$row = [];

			foreach ( $fields as $field ) {
				$field = clone $field;
				$field->update_configuration( [ 'show_as_link' => '0' ] );

				$source = \GV\View::get_source( $field, $view );
				$row[]  = $this->normalize_cell_value( $renderer->render( $field, $view, $source, $entry, $request, '\GV\Field_CSV_Template' ) );
			}

			$rows[] = $row;
		}

		return [
			'headers' => array_map( [ '\GV\Utils', 'strip_excel_formulas' ], array_values( $headers ) ),
			'rows'    => array_map(
				static function ( array $row ) {
					return array_map( [ '\GV\Utils', 'strip_excel_formulas' ], $row );
				},
				$rows
			),
		];
	}

	/**
	 * Normalizes a rendered cell value before writing it to a tabular export.
	 *
	 * CSV field templates can receive display-safe values from Gravity Forms. The
	 * export should preserve legitimate field markup while converting display
	 * entities such as `&#039;` back to human-readable characters.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Rendered cell value.
	 *
	 * @return string
	 */
	private function normalize_cell_value( $value ) {
		$value   = (string) $value;
		$charset = get_option( 'blog_charset' ) ? get_option( 'blog_charset' ) : 'UTF-8';

		return html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, $charset );
	}

	/**
	 * Renders rows using fputcsv-compatible output.
	 *
	 * @since 3.0.0
	 *
	 * @param array      $rows        Rows.
	 * @param string     $format      Export format.
	 * @param bool       $include_bom Whether to prepend the UTF-8 BOM.
	 * @param array|null $form        Gravity Forms form array.
	 *
	 * @return string|WP_Error
	 */
	private function render_tabular_file( array $rows, $format, $include_bom = true, $form = null ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- fputcsv requires a stream; php://temp avoids a persistent temp file.
		$stream = fopen( 'php://temp', 'w+' );

		if ( ! $stream ) {
			return new WP_Error( 'gravityview_bulk_export_csv_stream_failed', __( 'The export file could not be created.', 'gk-gravityview' ) );
		}

		if ( $include_bom && apply_filters( 'gform_include_bom_export_entries', true, $form ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fputs -- fputcsv stream output mirrors GravityView's existing CSV export implementation.
			fputs( $stream, "\xef\xbb\xbf" );
		}

		$delimiter = self::FORMAT_TSV === $format ? "\t" : ',';

		foreach ( $rows as $row ) {
			fputcsv( $stream, $row, $delimiter );
		}

		rewind( $stream );

		$file = stream_get_contents( $stream );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing the php://temp stream opened above.
		fclose( $stream );

		return false === $file ? '' : $file;
	}

	/**
	 * Returns fields included in GravityView CSV output for an entry.
	 *
	 * @since 3.0.0
	 *
	 * @param View      $view  View.
	 * @param \GV\Entry $entry Entry.
	 *
	 * @return array
	 */
	private function get_csv_fields( View $view, \GV\Entry $entry ) {
		$allowed = [];

		foreach ( $view->fields->by_position( 'directory_*' )->by_visible( $view )->all() as $field ) {
			$allowed[] = $field;
		}

		/**
		 * Allowlist more entry fields by ID that are output in CSV requests.
		 *
		 * This is the same filter used by GravityView's View CSV endpoint.
		 *
		 * @param array     $allowed Allowed field IDs.
		 * @param \GV\View  $view    View.
		 * @param \GV\Entry $entry   Entry.
		 */
		$allowed_field_ids = apply_filters( 'gravityview/csv/entry/fields', wp_list_pluck( $allowed, 'ID' ), $view, $entry );
		$allowed           = array_filter(
			$allowed,
			static function ( $field ) use ( $allowed_field_ids ) {
				return in_array( $field->ID, $allowed_field_ids, true );
			}
		);

		foreach ( array_diff( $allowed_field_ids, wp_list_pluck( $allowed, 'ID' ) ) as $field_id ) {
			$allowed[] = is_numeric( $field_id ) ? \GV\GF_Field::by_id( $view->form, $field_id ) : \GV\Internal_Field::by_id( $field_id );
		}

		return array_filter( $allowed );
	}

	/**
	 * Returns export directory and URL data.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view    View.
	 * @param array  $context Optional background context.
	 * @param string $format  Export format.
	 *
	 * @return array|WP_Error
	 */
	private function get_export_location( View $view, array $context = [], $format = self::FORMAT_CSV ) {
		$token = $this->get_export_token( $context );
		$root  = $this->get_export_root_for_view( $view );
		$dir   = trailingslashit( $root ) . $token;

		if ( ! wp_mkdir_p( $dir ) ) {
			return new WP_Error( 'gravityview_bulk_export_csv_directory_failed', __( 'The export directory could not be created.', 'gk-gravityview' ) );
		}

		$protected = $this->write_public_temp_index_files( $root, $dir );

		if ( is_wp_error( $protected ) ) {
			return $protected;
		}

		return [
			'blog_id'       => get_current_blog_id(),
			'dir'           => $dir,
			'file_filename' => $this->get_export_filename( $view, $format ),
			'view_id'       => (int) $view->ID,
			'zip_filename'  => $this->get_zip_filename( $view ),
		];
	}

	/**
	 * Returns the export root directory for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return string
	 */
	private function get_export_root_for_view( View $view ) {
		return self::get_export_root_for_view_id( (int) $view->ID );
	}

	/**
	 * Returns the export root directory for a View ID.
	 *
	 * Export working files live under the server temp directory so generated
	 * files are only reachable through SecureDownload URLs.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View ID.
	 *
	 * @return string
	 */
	private static function get_export_root_for_view_id( $view_id ) {
		return self::get_export_site_root() . '/view-' . absint( $view_id );
	}

	/**
	 * Returns the export root directory for the current site.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private static function get_export_site_root() {
		return untrailingslashit( trailingslashit( get_temp_dir() ) . 'gravityview/bulk-actions/export-entries/site-' . get_current_blog_id() );
	}

	/**
	 * Returns whether a path is the export root for the current site.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path Absolute path.
	 *
	 * @return bool
	 */
	private static function is_export_site_root( $path ) {
		$path     = untrailingslashit( wp_normalize_path( (string) $path ) );
		$expected = untrailingslashit( wp_normalize_path( self::get_export_site_root() ) );

		return '' !== $expected && $path === $expected;
	}

	/**
	 * Adds directory-index sentinels when WordPress' temp dir falls under wp-content.
	 *
	 * Export paths include a random token and downloads go through SecureDownload.
	 * These index files only guard hosts where get_temp_dir() falls back to a
	 * web-accessible wp-content directory with directory listing enabled.
	 *
	 * @since 3.0.0
	 *
	 * @param string $root Export root directory.
	 * @param string $dir  Token-specific export directory.
	 *
	 * @return true|WP_Error
	 */
	private function write_public_temp_index_files( $root, $dir ) {
		if ( ! self::is_path_inside_wp_content( $root ) ) {
			return true;
		}

		foreach ( self::get_export_index_directories( $dir ) as $directory ) {
			if ( ! is_dir( $directory ) ) {
				continue;
			}

			$index = trailingslashit( $directory ) . 'index.html';

			if ( file_exists( $index ) ) {
				continue;
			}

			$written = $this->write_file( $index, '' );

			if ( is_wp_error( $written ) ) {
				return $written;
			}
		}

		return true;
	}

	/**
	 * Returns whether a path is inside wp-content.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path Absolute path.
	 *
	 * @return bool
	 */
	private static function is_path_inside_wp_content( $path ) {
		$path    = realpath( $path );
		$content = realpath( WP_CONTENT_DIR );

		if ( false === $path || false === $content ) {
			return false;
		}

		$path    = untrailingslashit( wp_normalize_path( $path ) );
		$content = untrailingslashit( wp_normalize_path( $content ) );

		return $path === $content || 0 === strpos( $path, trailingslashit( $content ) );
	}

	/**
	 * Returns directories that should get index sentinels for an export path.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dir Token-specific export directory.
	 *
	 * @return string[]
	 */
	private static function get_export_index_directories( $dir ) {
		$base    = untrailingslashit( wp_normalize_path( trailingslashit( get_temp_dir() ) . 'gravityview' ) );
		$current = untrailingslashit( wp_normalize_path( $dir ) );
		$paths   = [];

		while ( $current && ( $current === $base || 0 === strpos( $current, trailingslashit( $base ) ) ) ) {
			$paths[] = $current;

			if ( $current === $base ) {
				break;
			}

			$parent = dirname( $current );

			if ( $parent === $current ) {
				break;
			}

			$current = $parent;
		}

		return array_reverse( array_unique( $paths ) );
	}

	/**
	 * Returns the stable export token for this request or job.
	 *
	 * @since 3.0.0
	 *
	 * @param array $context Optional background context.
	 *
	 * @return string
	 */
	private function get_export_token( array $context = [] ) {
		$token = (string) ( $context['job_data']['result_token'] ?? '' );

		if ( '' === $token ) {
			$token = wp_generate_uuid4();
		}

		return sanitize_key( $token );
	}

	/**
	 * Writes a file using WordPress' filesystem API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path    Absolute file path.
	 * @param string $content File contents.
	 *
	 * @return true|WP_Error
	 */
	private function write_file( $path, $content ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return new WP_Error( 'gravityview_bulk_export_csv_filesystem_unavailable', __( 'The WordPress filesystem could not be initialized.', 'gk-gravityview' ) );
		}

		if ( ! $wp_filesystem->put_contents( $path, $content, FS_CHMOD_FILE ) ) {
			return new WP_Error( 'gravityview_bulk_export_csv_file_write_failed', __( 'The export file could not be written.', 'gk-gravityview' ) );
		}

		return true;
	}

	/**
	 * Deletes a file or directory using WordPress' filesystem API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path      Absolute path.
	 * @param bool   $recursive Whether to delete directories recursively.
	 *
	 * @return void
	 */
	private static function delete_path( $path, $recursive = false ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			gravityview()->log->debug( 'Export cleanup skipped because the WordPress filesystem could not be initialized.' );

			return;
		}

		$wp_filesystem->delete( $path, $recursive );
	}

	/**
	 * Reads a generated export file.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path Absolute file path.
	 *
	 * @return string|WP_Error
	 */
	private function read_file( $path ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return new WP_Error( 'gravityview_bulk_export_csv_filesystem_unavailable', __( 'The WordPress filesystem could not be initialized.', 'gk-gravityview' ) );
		}

		$content = $wp_filesystem->get_contents( $path );

		if ( false === $content ) {
			return new WP_Error( 'gravityview_bulk_export_csv_file_read_failed', __( 'The export file could not be read.', 'gk-gravityview' ) );
		}

		return $content;
	}

	/**
	 * Returns files matching a generated export pattern.
	 *
	 * @since 3.0.0
	 *
	 * @param string $pattern Glob pattern.
	 *
	 * @return string[]|WP_Error
	 */
	private function get_matching_files( $pattern ) {
		$files = glob( $pattern );

		if ( false === $files ) {
			return new WP_Error( 'gravityview_bulk_export_csv_file_read_failed', __( 'The export file could not be read.', 'gk-gravityview' ) );
		}

		return $files;
	}

	/**
	 * Assembles the final export file from the header and per-entry parts.
	 *
	 * On the `direct` WP filesystem transport (the common case for sites that
	 * actually run background exports) this streams part files into the
	 * destination so peak memory stays at one part. Non-direct transports
	 * (FTP, SSH, etc.) fall back to read-concat-write because the alternative
	 * would require bypassing the WP_Filesystem abstraction.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $path        Destination file path.
	 * @param string   $header_path Header file path.
	 * @param string[] $parts       Sorted per-entry part file paths.
	 *
	 * @return true|WP_Error
	 */
	private function assemble_export_file( string $path, string $header_path, array $parts ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! $wp_filesystem && ! WP_Filesystem() ) {
			return new WP_Error( 'gravityview_bulk_export_csv_filesystem_unavailable', __( 'The WordPress filesystem could not be initialized.', 'gk-gravityview' ) );
		}

		if ( 'direct' !== $wp_filesystem->method ) {
			$result = $this->assemble_export_file_buffered( $path, $header_path, $parts );

			if ( is_wp_error( $result ) ) {
				self::delete_path( $path, false );
			}

			return $result;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Direct transport streams large exports to disk without buffering the whole file.
		$out = @fopen( $path, 'wb' );

		if ( ! $out ) {
			return new WP_Error( 'gravityview_bulk_export_csv_file_write_failed', __( 'The export file could not be written.', 'gk-gravityview' ) );
		}

		$fail = static function ( string $code, string $message ) use ( $path ): WP_Error {
			self::delete_path( $path, false );

			return new WP_Error( $code, $message );
		};

		// Stream the header first, then each per-entry part. Avoid array_merge() here
		// because parts can number in the hundreds of thousands on large exports.
		$header_written = $this->stream_into( $out, $header_path );

		if ( is_wp_error( $header_written ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Direct transport uses native streams to avoid buffering large exports.
			fclose( $out );

			return $fail( $header_written->get_error_code(), $header_written->get_error_message() );
		}

		foreach ( $parts as $part ) {
			$copied = $this->stream_into( $out, $part );

			if ( is_wp_error( $copied ) ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Direct transport uses native streams to avoid buffering large exports.
				fclose( $out );

				return $fail( $copied->get_error_code(), $copied->get_error_message() );
			}
		}

		// fclose can surface a deferred write/flush failure that fwrite() didn't.
		$flushed = fflush( $out );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Direct transport uses native streams to avoid buffering large exports.
		$closed = fclose( $out );

		if ( ! $flushed || ! $closed ) {
			return $fail( 'gravityview_bulk_export_csv_file_write_failed', __( 'The export file could not be written.', 'gk-gravityview' ) );
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- File was written through native direct stream in this branch.
		@chmod( $path, FS_CHMOD_FILE );

		return true;
	}

	/**
	 * Streams the contents of a source file into an open write handle.
	 *
	 * @since 3.0.0
	 *
	 * @param resource $out    Open write handle.
	 * @param string   $source Source file path.
	 *
	 * @return true|WP_Error
	 */
	private function stream_into( $out, string $source ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Direct transport streams large exports from disk without buffering the whole file.
		$in = @fopen( $source, 'rb' );

		if ( ! $in ) {
			return new WP_Error( 'gravityview_bulk_export_csv_file_read_failed', __( 'The export file could not be read.', 'gk-gravityview' ) );
		}

		$copied = stream_copy_to_stream( $in, $out );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Direct transport uses native streams to avoid buffering large exports.
		fclose( $in );

		if ( false === $copied ) {
			return new WP_Error( 'gravityview_bulk_export_csv_file_write_failed', __( 'The export file could not be written.', 'gk-gravityview' ) );
		}

		return true;
	}

	/**
	 * Buffered fallback for non-direct WP_Filesystem transports.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $path        Destination file path.
	 * @param string   $header_path Header file path.
	 * @param string[] $parts       Sorted per-entry part file paths.
	 *
	 * @return true|WP_Error
	 */
	private function assemble_export_file_buffered( string $path, string $header_path, array $parts ) {
		$content = $this->read_file( $header_path );

		if ( is_wp_error( $content ) ) {
			return $content;
		}

		foreach ( $parts as $part ) {
			$part_content = $this->read_file( $part );

			if ( is_wp_error( $part_content ) ) {
				return $part_content;
			}

			$content .= $part_content;
		}

		return $this->write_file( $path, $content );
	}

	/**
	 * Creates a single export file from generated row parts.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $export Export location.
	 * @param string $format Export format.
	 *
	 * @return array|WP_Error
	 */
	private function create_single_file( array $export, $format ) {
		$header_path = trailingslashit( $export['dir'] ) . $this->get_header_filename( $format );
		$parts       = $this->get_matching_files( trailingslashit( $export['dir'] ) . 'part-*.' . $this->get_file_extension( $format ) );

		if ( is_wp_error( $parts ) ) {
			return $parts;
		}

		if ( empty( $parts ) || ! file_exists( $header_path ) ) {
			return new WP_Error( 'gravityview_bulk_export_csv_empty', __( 'No export files were created for the selected entries.', 'gk-gravityview' ) );
		}

		natsort( $parts );

		$filename = $export['file_filename'] ?? 'entry-data.' . $this->get_file_extension( $format );
		$path     = trailingslashit( $export['dir'] ) . $filename;

		$written = $this->assemble_export_file( $path, $header_path, $parts );

		if ( is_wp_error( $written ) ) {
			return $written;
		}

		$url = $this->get_secure_download_url( $path, $filename );

		if ( is_wp_error( $url ) ) {
			return $url;
		}

		$this->delete_intermediate_single_file_parts( $export['dir'], $format );
		$this->schedule_cleanup( $export );

		return [
			'path'  => $path,
			'url'   => $url,
			'label' => $this->get_download_label( $format ),
		];
	}

	/**
	 * Creates a ZIP archive from generated entry files.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $export Export location.
	 * @param string $format  Export format.
	 *
	 * @return array|WP_Error
	 */
	private function create_zip( array $export, $format ) {
		if ( ! class_exists( ZipArchive::class ) ) {
			return new WP_Error( 'gravityview_bulk_export_csv_zip_unavailable', __( 'ZIP support is not available on this site.', 'gk-gravityview' ) );
		}

		$files = $this->get_matching_files( trailingslashit( $export['dir'] ) . 'entry-*.' . $this->get_file_extension( $format ) );

		if ( is_wp_error( $files ) ) {
			return $files;
		}

		if ( empty( $files ) ) {
			return new WP_Error( 'gravityview_bulk_export_csv_empty', __( 'No export files were created for the selected entries.', 'gk-gravityview' ) );
		}

		$zip_filename = $export['zip_filename'] ?? 'entry-data.zip';
		$zip_path     = trailingslashit( $export['dir'] ) . $zip_filename;
		$zip          = new ZipArchive();
		$opened       = $zip->open( $zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE );

		if ( true !== $opened ) {
			return new WP_Error( 'gravityview_bulk_export_csv_zip_failed', __( 'The ZIP file could not be created.', 'gk-gravityview' ) );
		}

		foreach ( $files as $file ) {
			if ( ! $zip->addFile( $file, basename( $file ) ) ) {
				$zip->close();

				return new WP_Error( 'gravityview_bulk_export_csv_zip_failed', __( 'The ZIP file could not be created.', 'gk-gravityview' ) );
			}
		}

		if ( ! $zip->close() ) {
			return new WP_Error( 'gravityview_bulk_export_csv_zip_failed', __( 'The ZIP file could not be created.', 'gk-gravityview' ) );
		}

		$url = $this->get_secure_download_url( $zip_path, $zip_filename );

		if ( is_wp_error( $url ) ) {
			return $url;
		}

		foreach ( $files as $file ) {
			self::delete_path( $file );
		}

		$this->schedule_cleanup( $export );

		return [
			'path'  => $zip_path,
			'url'   => $url,
			'label' => __( 'Download ZIP', 'gk-gravityview' ),
		];
	}

	/**
	 * Adds cleanup metadata to a background batch result.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Batch result.
	 * @param array $export Export location.
	 *
	 * @return array
	 */
	private function add_cleanup_result( array $result, array $export ) {
		$result['cleanup'] = [
			'blog_id' => (int) ( $export['blog_id'] ?? get_current_blog_id() ),
			'dir'     => $export['dir'],
			'view_id' => (int) ( $export['view_id'] ?? 0 ),
		];

		return $result;
	}

	/**
	 * Returns a secure download URL for a generated export file.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path     Absolute file path.
	 * @param string $filename Download filename.
	 *
	 * @return string|WP_Error
	 */
	private function get_secure_download_url( $path, $filename ) {
		try {
			$result = SecureDownload::get_instance()->generate_download_url(
				$path,
				[
					'cache_duration' => 0,
					'disposition'    => 'attachment',
					'expires_in'     => ResultStore::DEFAULT_TTL,
					'filename'       => $filename,
					'users'          => [ get_current_user_id() ],
				]
			);
		} catch ( Throwable $e ) {
			return new WP_Error( 'gravityview_bulk_export_csv_secure_download_failed', $e->getMessage() );
		}

		if ( ! is_array( $result ) || empty( $result['url'] ) ) {
			return new WP_Error( 'gravityview_bulk_export_csv_secure_download_failed', __( 'The secure download link could not be created.', 'gk-gravityview' ) );
		}

		return (string) $result['url'];
	}

	/**
	 * Returns whether a directory belongs to this View's export root.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dir  Directory path.
	 * @param View   $view View.
	 *
	 * @return bool
	 */
	private function is_export_directory_for_view( $dir, View $view ) {
		return self::is_export_directory_path_for_view( $dir, (int) $view->ID );
	}

	/**
	 * Returns whether a directory belongs to the expected export root for a View ID.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dir     Directory path.
	 * @param int    $view_id View ID.
	 *
	 * @return bool
	 */
	private static function is_export_directory_path_for_view( $dir, $view_id ) {
		$root = self::get_export_root_for_view_id( $view_id );

		$dir  = realpath( $dir );
		$root = realpath( $root );

		if ( false === $dir || false === $root ) {
			return false;
		}

		$dir  = untrailingslashit( wp_normalize_path( $dir ) );
		$root = untrailingslashit( wp_normalize_path( $root ) );

		return $dir !== $root && 0 === strpos( $dir, trailingslashit( $root ) );
	}

	/**
	 * Deletes single-file export part files after the final file exists.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dir    Export directory.
	 * @param string $format Export format.
	 *
	 * @return void
	 */
	private function delete_intermediate_single_file_parts( $dir, $format ) {
		$patterns = [
			trailingslashit( $dir ) . $this->get_header_filename( $format ),
			trailingslashit( $dir ) . 'part-*.' . $this->get_file_extension( $format ),
			trailingslashit( $dir ) . 'part-*.count',
		];

		foreach ( $patterns as $pattern ) {
			$files = glob( $pattern );

			if ( false === $files ) {
				continue;
			}

			foreach ( $files as $file ) {
				self::delete_path( $file );
			}
		}
	}

	/**
	 * Schedules cleanup for a generated export directory.
	 *
	 * @since 3.0.0
	 *
	 * @param array $export Export location.
	 *
	 * @return void
	 */
	private function schedule_cleanup( array $export ) {
		$dir     = (string) ( $export['dir'] ?? '' );
		$view_id = absint( $export['view_id'] ?? 0 );

		if ( '' === $dir || ! $view_id || ! self::is_export_directory_path_for_view( $dir, $view_id ) ) {
			return;
		}

		$scheduled = wp_schedule_single_event( time() + ResultStore::DEFAULT_TTL + HOUR_IN_SECONDS, self::CLEANUP_HOOK, [ $dir, $view_id, (int) ( $export['blog_id'] ?? get_current_blog_id() ) ] );

		if ( false === $scheduled ) {
			gravityview()->log->debug(
				'Export cleanup could not be scheduled.',
				[
					'view_id' => $view_id,
					'dir'     => $dir,
				]
			);
		}
	}

	/**
	 * Returns the single export filename.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view   View.
	 * @param string $format Export format.
	 *
	 * @return string
	 */
	private function get_export_filename( View $view, $format ) {
		return sanitize_file_name(
			strtr(
				'gravityview-view-[view_id]-entry-data-[timestamp].[extension]',
				[
					'[view_id]'   => (int) $view->ID,
					'[timestamp]' => current_time( 'Ymd-His' ),
					'[extension]' => $this->get_file_extension( $format ),
				]
			)
		);
	}

	/**
	 * Returns the ZIP filename for the export.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return string
	 */
	private function get_zip_filename( View $view ) {
		return sanitize_file_name(
			strtr(
				'gravityview-view-[view_id]-entry-data-[timestamp].zip',
				[
					'[view_id]'   => (int) $view->ID,
					'[timestamp]' => current_time( 'Ymd-His' ),
				]
			)
		);
	}

	/**
	 * Formats the success message with a download link.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $processed      Number of exported entries.
	 * @param string $url            Download URL.
	 * @param string $format         Export format.
	 * @param string $download_label Download link label.
	 *
	 * @return string
	 */
	private function get_success_message( $processed, $url, $format, $download_label ) {
		return strtr(
			/* translators: [count] is the number of exported entries. [format] is CSV or TSV. [link_start] is the opening download link. [download_label] is the download link label. [link_end] is the closing download link. */
			_n( '[count] entry exported as [format]. [link_start][download_label][link_end]', '[count] entries exported as [format]. [link_start][download_label][link_end]', (int) $processed, 'gk-gravityview' ),
			[
				'[count]'          => number_format_i18n( (int) $processed ),
				'[format]'         => $this->get_format_label( $format ),
				'[link_start]'     => '<a href="' . esc_url( $url ) . '">',
				'[download_label]' => esc_html( $download_label ),
				'[link_end]'       => '</a>',
			]
		);
	}

	/**
	 * Returns the export filename for an entry.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $entry_id Entry ID.
	 * @param string $format   Export format.
	 *
	 * @return string
	 */
	private function get_entry_filename( $entry_id, $format ) {
		return sanitize_file_name(
			strtr(
				/* translators: [entry_id] is the exported entry ID. [extension] is csv or tsv. */
				__( 'entry-[entry_id].[extension]', 'gk-gravityview' ),
				[
					'[entry_id]'  => (int) $entry_id,
					'[extension]' => $this->get_file_extension( $format ),
				]
			)
		);
	}

	/**
	 * Returns the header filename for single-file exports.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format Export format.
	 *
	 * @return string
	 */
	private function get_header_filename( $format ) {
		return '_headers.' . $this->get_file_extension( $format );
	}

	/**
	 * Returns a row part filename for single-file exports.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $context Optional background context.
	 * @param string $format  Export format.
	 *
	 * @return string
	 */
	private function get_part_filename( array $context, $format ) {
		return sanitize_file_name(
			strtr(
				'part-[cursor].[extension]',
				[
					'[cursor]'    => str_pad( (string) max( 0, (int) ( $context['cursor'] ?? 0 ) ), 10, '0', STR_PAD_LEFT ),
					'[extension]' => $this->get_file_extension( $format ),
				]
			)
		);
	}

	/**
	 * Returns a row count filename for single-file exports.
	 *
	 * @since 3.0.0
	 *
	 * @param array $context Optional background context.
	 *
	 * @return string
	 */
	private function get_part_count_filename( array $context ) {
		return sanitize_file_name(
			strtr(
				'part-[cursor].count',
				[
					'[cursor]' => str_pad( (string) max( 0, (int) ( $context['cursor'] ?? 0 ) ), 10, '0', STR_PAD_LEFT ),
				]
			)
		);
	}

	/**
	 * Counts generated entry files.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dir    Export directory.
	 * @param string $format Export format.
	 *
	 * @return int
	 */
	private function count_exported_entry_files( $dir, $format ) {
		$files = $this->get_matching_files( trailingslashit( $dir ) . 'entry-*.' . $this->get_file_extension( $format ) );

		return is_array( $files ) ? count( $files ) : 0;
	}

	/**
	 * Counts generated rows for a single-file export.
	 *
	 * @since 3.0.0
	 *
	 * @param string $dir Export directory.
	 *
	 * @return int
	 */
	private function count_exported_rows( $dir ) {
		$files = $this->get_matching_files( trailingslashit( $dir ) . 'part-*.count' );
		$count = 0;

		if ( ! is_array( $files ) ) {
			return 0;
		}

		foreach ( $files as $file ) {
			$file_count = $this->read_file( $file );

			if ( is_wp_error( $file_count ) ) {
				continue;
			}

			$count += max( 0, (int) $file_count );
		}

		return $count;
	}

	/**
	 * Returns the configured export format.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action configuration.
	 *
	 * @return string
	 */
	private function get_export_format( array $action ) {
		$settings = isset( $action['settings'] ) && is_array( $action['settings'] ) ? $action['settings'] : [];
		$format   = sanitize_key( $settings['format'] ?? self::FORMAT_CSV );

		return in_array( $format, [ self::FORMAT_CSV, self::FORMAT_TSV ], true ) ? $format : self::FORMAT_CSV;
	}

	/**
	 * Returns the configured output mode.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action configuration.
	 *
	 * @return string
	 */
	private function get_output_mode( array $action ) {
		$settings = isset( $action['settings'] ) && is_array( $action['settings'] ) ? $action['settings'] : [];
		$output   = sanitize_key( $settings['output'] ?? self::OUTPUT_SINGLE );

		return in_array( $output, [ self::OUTPUT_SINGLE, self::OUTPUT_SEPARATE ], true ) ? $output : self::OUTPUT_SINGLE;
	}

	/**
	 * Returns the export file extension.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format Export format.
	 *
	 * @return string
	 */
	private function get_file_extension( $format ) {
		return self::FORMAT_TSV === $format ? 'tsv' : 'csv';
	}

	/**
	 * Returns the display label for an export format.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format Export format.
	 *
	 * @return string
	 */
	private function get_format_label( $format ) {
		return self::FORMAT_TSV === $format ? 'TSV' : 'CSV';
	}

	/**
	 * Returns the download link label.
	 *
	 * @since 3.0.0
	 *
	 * @param string $format Export format.
	 *
	 * @return string
	 */
	private function get_download_label( $format ) {
		return strtr(
			/* translators: [format] is CSV or TSV. */
			__( 'Download [format]', 'gk-gravityview' ),
			[
				'[format]' => $this->get_format_label( $format ),
			]
		);
	}

	/**
	 * Whether this process call is running inside a background batch.
	 *
	 * @since 3.0.0
	 *
	 * @param array $context Background context.
	 *
	 * @return bool
	 */
	private function is_background_context( array $context ) {
		return ! empty( $context['job_data']['result_token'] );
	}
}
