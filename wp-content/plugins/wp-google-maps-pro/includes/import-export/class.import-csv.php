<?php
/**
 * Improved CSV importer
 * 
 * This stays within the V7 spec for imports, which may not be ideal long term
 * 
 * A good step in the right direction, but still a time saving approach as we do not want to rebuild all import modules at this state (2022-04-01)
 * 
 * At a later stage, and ideally in parts, we will rework all the classes, and the primary importer systems to be more modular
 *
 * @package WPGMapsPro\ImportExport
 * @since 9.0.0
 * 
 */

namespace WPGMZA;

class ImportCSV extends Import {
	protected $notices;
	protected $import_type;

	/**
	 * Constructor 
	*/
	public function __construct($file = '', $file_url = '', $options = array()){
		Import::__construct($file, $file_url, $options);
		
		$this->notices = array();

		$this->failure_message_by_handle = array(); // Still needed for initial parsing errors
	}

	/**
	 * Check if this is a batched import
	 * 
	 * Batch imports are class based, not optional
	 * 
	 * So the class dictates this on a strict boolean return 
	 * 
	 * @return bool
	*/
	public function isBatched(){
		return true;
	}

	/**
	 * Check options.
	 *
	 * @throws Exception On malformed options.
	 * 
	 * @return void
	*/
	protected function check_options() {
		if (!is_array( $this->options)) {

			if(empty($this->options)){
				$this->options = array();
			} else {
				throw new \Exception( __( 'Error: Malformed options.', 'wp-google-maps' ) );
			}
		}

		$modes = array('create_update', 'replace');

		$conditionals = array('geocode', 'remap_columns', 'keep_map_id', 'apply', 'delete_import');
		foreach($conditionals as $name){
			$this->options[$name] = !empty($this->options[$name]) && $this->options[$name] === 'true' ? true : false;
		}

		if(!empty($this->options['applys']) && is_string($this->options['applys'])){
			$this->options['applys'] = explode(',', $this->options['applys']);
		} else {
			$this->options['applys'] = array();
		}

		if($this->options['apply']){
			/* Force disable the keep map ID option */
			$this->options['keep_map_id'] = false;
		}

		if ($this->options['apply'] && empty($this->options['applys'])) {
			$this->options['applys'] = import_export_get_maps_list('ids');
		}

		$this->options['applys'] = $this->check_ids( $this->options['applys'] );
		
		if(empty($this->options['headerRemap']) || !is_array($this->options['headerRemap'])){
			$this->options['headerRemap'] = false;
		}

		$this->options['batch_size'] = !empty($this->options['batch_size']) ? intval($this->options['batch_size']) : 1;

		$this->options['mode'] = !empty($this->options['mode']) ? $this->options['mode'] : 'create_update';
		if(!in_array($this->options['mode'], $modes)){
			$this->options['mode'] = 'create_update';
		}

		$this->options['import_id'] = !empty($this->options['import_id']) ? intval($this->options['import_id']) : false;
	}

	/**
	 * Parse file data
	 * 
	 * @return void
	 */
	protected function parse_file(){
		$original_data = $this->file_data;
		$converted = preg_replace('/\r(\n?)/u', "\r\n", $this->file_data);
		if(empty($converted)){
			$error = preg_last_error();
			$message = __('Error when converting line endings', 'wp-google-maps') . ' (' . $error . ')';
			switch($error){
				case PREG_INTERNAL_ERROR:
					$message = __('Internal error when converting line endings', 'wp-google-maps');
					break;
				case PREG_BACKTRACK_LIMIT_ERROR:
					$message = __('Backtrack limit error when converting line endings', 'wp-google-maps');
					break;
				case PREG_RECURSION_LIMIT_ERROR:
					$message = __('Recursion limit error when converting line endings', 'wp-google-maps');
					break;
				case PREG_BAD_UTF8_ERROR:
					$message = __('Bad UTF-8 error when converting line endings', 'wp-google-maps');
					break;
				case PREG_BAD_UTF8_OFFSET_ERROR:
					$message = __('Bad UTF-8 offset error when converting line endings', 'wp-google-maps');
					break;
				default:
					break;
			}
			
			$this->failure_message_by_handle['converting_line_endings'] = $message;
			$this->failure('converting_line_endings', 0);
		} else {
			$this->file_data = $converted;
		}

		$fp = fopen('php://memory', 'r+');
		fwrite($fp, $this->file_data);
		rewind($fp);

		$headers = false;
		$rows = 0;

		while($cells = fgetcsv($fp)){
			if(empty($headers)){
				$headers = $cells;
				foreach($headers as $key => $value){
					$headers[$key] = preg_replace('/[\r\n]/', '', nl2br($value));
				}
			} else {
				$rows ++;
			}
		}
		fclose($fp);

		$this->headers = $headers;
		$this->total_rows = $rows;

		$this->import_type = 'marker';
		if( in_array('map_title', $headers, true) ) {
			$this->import_type = 'map';
		} else if ( in_array( 'address', $headers, true ) || ( in_array( 'lat', $headers, true ) && ( in_array( 'lng', $headers, true ) ) ) ) {
			$this->import_type = 'marker';
		} else if ( in_array( 'center_x', $headers, true ) && in_array( 'center_y', $headers, true ) && in_array( 'radius', $headers, true ) ) {
			$this->import_type = 'circle';
		} else if ( in_array( 'center_x', $headers, true ) && in_array( 'center_y', $headers, true ) && in_array( 'fontSize', $headers, true ) ) {
			$this->import_type = 'pointlabel';
		} else if ( in_array( 'polydata', $headers, true ) && in_array( 'innerpolydata', $headers, true ) ) {
			$this->import_type = 'polygon';
		} else if ( in_array( 'polydata', $headers, true ) ) {
			$this->import_type = 'polyline';
		} else if ( in_array( 'corner_ax', $headers, true ) && in_array( 'corner_ay', $headers, true ) && in_array( 'corner_bx', $headers, true ) && in_array( 'corner_by', $headers, true ) && in_array('image', $headers, true) ) {
			$this->import_type = 'imageoverlay';
		} else if ( in_array( 'corner_ax', $headers, true ) && in_array( 'corner_ay', $headers, true ) && in_array( 'corner_bx', $headers, true ) && in_array( 'corner_by', $headers, true ) ) {
			$this->import_type = 'rectangle';
		} else if ( in_array( 'dataset', $headers, true ) ) {
			$this->import_type = 'dataset';
		}
		
		/* We could move this to another helper maybe in the main import class, just for reusability */
		$this->columnLookup = Import::getColumnLookup($this->import_type);

		$this->headerRemap = array();
		foreach($this->headers as $key => $name){
			$normalized = str_replace(array(" ", "-"), "_", strtolower(trim($name)));

			$isField = false;
			$fieldName = false;
			if($this->import_type === 'marker'){
				/* This is a marker based import, let's try and determine if this is a custom field? */
				$fieldName = strtolower(trim($name));
				$fieldName = str_replace(array("custom field:", "marker field:"), "field:", $fieldName);

				if(strpos($fieldName, "field:") !== FALSE){
					$isField = true;
					$fieldName = trim(str_replace("field:", "", $fieldName));
				} else {
					/* In some cases, the user may omit the field name, in this case we do need to prepare for it anyway */
					$isField = true;
					$fieldName = trim($fieldName);
				}
			}

			foreach($this->columnLookup as $column){
				if(is_array($column)){
					if(!empty($isField) && !empty($fieldName)){
						/* Potentially a custom field */
						if(strtolower($column['name']) == $fieldName){
							/* We have a match */
							$this->headerRemap[$key] = $column['slug'] . $column['id'];
						} else if($column['id'] === 'create'){
							if(!isset($this->headerRemap[$key])){
								$this->headerRemap[$key] = $column['slug'] . $column['id'] . '_' . str_replace(" ", "_", strtolower($fieldName));
							}
						}
					}
				} else {
					if($normalized === $column){
						$this->headerRemap[$key] = $column;
					} else if (trim($name) === $column){
						/* Some database columns are cased differently, so this is a fallback for those cases */
						$this->headerRemap[$key] = $column;
					}
				}
			}
		}

	}

	/**
	 * Get admin options, this is HTML which is to be rendered to the end user
	 *
	 * @return string
	*/
	public function admin_options(){
		global $wpgmza;

		$doingEdit = !empty($_POST['schedule_id']) ? true : false;
		$source = $this->getSource();

		$document = new DOMDocument();
		$document->loadPHPFile($wpgmza->internalEngine->getTemplate('import-export/import-csv-options.html.php', WPGMZA_PRO_DIR_PATH));

		if($sourceElement = $document->querySelector('*[data-wpgmza-import-source]')){
			$sourceElement->setAttribute('data-wpgmza-import-source', $source);
		}

		if($doingEdit){
			$document->querySelector('.wpgmza-import-options-inner')->setAttribute('data-editing', 'true');
		}

		/* Localize import ID */
		if(!empty($_POST['import_id'])){
			$document->querySelector('.wpgmza-import-options-inner')->setAttribute('data-import-id', absint($_POST['import_id']));	
		}

		/* Localize import URL */
		if(!empty($_POST['import_url'])){
			$document->querySelector('.wpgmza-import-options-inner')->setAttribute('data-import-url', esc_attr($_POST['import_url']));	
		}

		/* Localize schedule ID */
		if(!empty($_POST['schedule_id'])){
			$document->querySelector('.wpgmza-import-options-inner')->setAttribute('data-schedule-id', absint($_POST['schedule_id']));	
		}

		$localizedNotices = array(
			'importing_notice' => __( 'Importing, this may take a moment...', 'wp-google-maps' ),
			'import_complete' => __( 'Import completed.', 'wp-google-maps' ),
			'import_map_select_warning' => __( 'Please select at least one map to import to, or deselect the "Apply import data to" option.', 'wp-google-maps' ),
			'scheduling_notice' =>  __( 'Scheduling, this may take a moment...', 'wp-google-maps' ),
			'scheduling_complete' => __( 'Scheduling completed.', 'wp-google-maps' ),
			'schedule_map_warning' => __( 'The schedule must target an existing map, or use map ID\'s specified in the file.', 'wp-google-maps' ),
			'schedule_date_warning' => __( 'Please enter a start date.', 'wp-google-maps' ),
			'schedule_edit' => __('Edit', 'wp-google-maps'),
			'schedule_delete' => __('Delete', 'wp-google-maps'),
			'schedule_not_found' => __('No schedule found', 'wp-google-maps'),
			'schedule_next_run' => __( 'Next Scheduled Run', 'wp-google-maps' ),
		);

		$document->querySelector('.wpgmza-import-options-inner')->setAttribute('data-localized-strings', json_encode($localizedNotices));

		if($this->import_type === 'map'){
			$document->querySelector('div[data-import-type="dataset"]')->remove();
		} else {
			$maps = import_export_get_maps_list('apply', $doingEdit && !empty($this->options['applys']) ? $this->options['applys'] : false, true);

			if($mapTable = $document->querySelector('#maps_apply_import tbody')){
				$mapTable->import($maps);
			}
			
			if($this->import_type !== 'marker'){
				$document->querySelector('.geocode-option-wrap')->addClass('wpgmza-hidden');
			}
		} 


		$document->populate(
			array(
				'source' => $source,
				'import_type' => ucwords($this->import_type . 's'),
				'total_rows' => (!empty($this->total_rows) ? intval($this->total_rows) : 0) . ''
			)
		);

		$remapWrapper = $document->querySelector('.remap_wrapper');
		if(!empty($this->headers) && !empty($this->columnLookup) && $remapWrapper){
			foreach($this->headers as $key => $name){
				$row = $document->createElement('div');
				$row->addClass('wpgmza-row');

				$colA = $document->createElement('div');
				$colA->addClass('wpgmza-col');
				$colA->appendText(esc_html($name));

				$colB = $document->createElement('div');
				$colB->addClass('wpgmza-col');

				$select = $document->createElement('select');
				foreach ($this->columnLookup as $column) {
					$internalMap = false;
					if(is_array($column)){
						/* Normalize custom field mapping slugs */
						$internalMap = $column;
						$column = $column['slug'] . $column['id'];

						if(!empty($this->headerRemap[$key]) && strpos($this->headerRemap[$key], 'custom_field__create_') !== FALSE){
							if(!is_numeric($internalMap['id']) && $internalMap['id'] === 'create'){
								$newTag = str_replace("custom_field__create", "", $this->headerRemap[$key]);
								$newTag = str_replace(" ", "_", strtolower($newTag));

								$column = $internalMap['slug'] . $internalMap['id'] . $newTag;
							}
						}
					}

					$option = $document->createElement('option');
					$option->setAttribute('value', esc_attr($column));
					
					$label = $column;

					if(!empty($internalMap)){
						/* Custom field, remap the visual element */
						$label = "Field: " . $internalMap['name'];
					}

					$label = $this->getLabelFromColumnName($label);

					$option->appendText($label);

					if($doingEdit && !empty($this->options['headerRemap']) && is_array($this->options['headerRemap'])){
						if($column === $this->options['headerRemap'][$key]){
							/* From storage */
							$option->setAttribute('selected', 'selected');
						}
					} else {
						if($column === $this->headerRemap[$key]){
							/* Predicted guess */
							$option->setAttribute('selected', 'selected');
						}
					}
					$select->appendChild($option);
				}

				$select->setAttribute('name', 'headerRemap[]');

				$colB->appendChild($select);

				$row->appendChild($colA);
				$row->appendChild($colB);

				$remapWrapper->appendChild($row);
			}
		}

		$scheduleSelect = $document->querySelector('select.import-schedule-csv-options');
		if($scheduleSelect){
			$schedule_intervals = wp_get_schedules();
			$internal_intervals = array();

			foreach($schedule_intervals as $alias => $interval){
				if(strpos($alias, 'wpgmza') !== FALSE){
					$internal_intervals[$alias] = $interval;
				}
			}

			if(empty($internal_intervals)){
				$internal_intervals = $schedule_intervals;
			}

			foreach($internal_intervals as $alias => $interval){
				$option = $document->createElement('option');
				$option->setAttribute('value', esc_attr($alias));
				$option->appendText(esc_html($interval['display']));

				if($doingEdit && !empty($this->options['interval'])){
					if($this->options['interval'] === $alias){
						$option->setAttribute('selected', 'selected');
					}
				}

				$scheduleSelect->appendChild($option);
			}
		}

		if($doingEdit){
			$document->querySelector('#import-csv')->setAttribute('style', "display:none;");

			$editableOptions = $document->querySelectorAll('*[data-option]');
			foreach($editableOptions as $element){
				$optionName = $element->getAttribute('data-option');
				$elementType = $element->getAttribute('type');

				if(!empty($this->options[$optionName])){
					if(empty($elementType)){
						/* Probably a select or something else */
						$elementType = $element->tagName;
					}

					switch($elementType){
						case 'checkbox':
							$element->setAttribute('checked', 'checked');
							break;
						case 'select':
							$items = $element->querySelectorAll('option');
							foreach($items as $item){
								if($item->getAttribute('value') === $this->options[$optionName]){
									/* This is the one */
									$item->setAttribute('selected', 'selected');
								}
							}
							break;
						default: 
							$element->setAttribute('value', $this->options[$optionName]);
							break;
					}
				}
			}
			
			$deleteFile = $document->querySelector('.delete-after-import');
			if(!empty($deleteFile)){
				$deleteFile->addClass('wpgmza-hidden');

				if(!empty($deleteFile->parentNode)){
					$column = $deleteFile->parentNode;
					if(!empty($column->parentNode)){
						$row = $column->parentNode;
						$row->addClass('wpgmza-hidden');
					}
				}
			}
		} else {
			$document->querySelector('#import-schedule-csv-options')->setAttribute('style', "display:none;");
			$document->querySelector('#import-schedule-csv-cancel')->setAttribute('style', "display:none;");

		}


		return $document->html;
	}

	/** 
	 * Logs failures by their handle
	 * 
	 * This really only applies to the primary parse as this class uses the batch importer instead of the one-shot original solution
	 * 
	 * It still must be supported as the parser does some prelim checks before the batch importer actually runs
	 * 
	 * @param string $handle The failure handle
	 * @param int $row_index The row which failed
	 * 
	 * @return void
	*/
	protected function failure($handle, $row_index) {
		if(!isset($this->failed_rows_by_handle[$handle])){
			$this->failed_rows_by_handle[$handle] = array();
		}
		
		$this->failed_rows_by_handle[$handle][] = $row_index;
		$this->log("$handle on $row_index");
	}

	/**
	 * Process the import
	 * 
	 * @return void
	*/
	public function import(){
		/* Hand over to the batch importer */ 
		$batchedImport = new BatchedImport\CSV();
		if(!empty($this->options)){
			foreach($this->options as $key => $value){
				if(!empty($value)){
					$batchedImport->{$key} = $value;
				}
			}
		}

		if(!empty($this->file)){
			$batchedImport->source = $this->file; 
		} else if(!empty($this->file_url)){
			$batchedImport->source = $this->file_url; 
			$batchedImport->sourceIsRemote = true;
		}

		if(!empty($batchedImport->batch_size)){
			$batchedImport->iterations = $batchedImport->batch_size;
		}

		if(!empty($this->import_type)){
			$batchedImport->type = $this->import_type;
		}

		$batchedImport->start();

		$this->batchId = $batchedImport->id;
		$this->batchClass = "CSV";
	}

	/**
	 * Get the import source
	 * 
	 * @return string
	*/
	public function getSource(){
		$source = !empty($this->file) ? basename($this->file) : (!empty($this->file_url) ? $this->file_url : '');
		return esc_html($source);
	}

	/**
	 * Simple converter for common column names 
	 * 
	 * Might be remapped, or might just be blanket converted
	 * 
	 * @param string $column The raw column name
	 * 
	 * @return string
	*/
	public function getLabelFromColumnName($column){
		$label = $column;

		$converted = array(
			'custom_field__create' => 'Field: New',
		);

		if(!empty($converted[$column])){
			$label = $converted[$column];
		}

		return $label;
	}
}