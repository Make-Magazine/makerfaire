<?php
/**
 * WP Go Maps Pro Import / Export API: Backup class
 *
 * Leverages the Import/Export modules for some functionality
 *
 * @package WPGMapsPro\ImportExport
 * @since 8.1.9
 */

namespace WPGMZA;

#[\AllowDynamicProperties]
class Backup {
	const CORE_DIR = "wp-google-maps";
	const BACKUP_DIR = "backups";
	
	const BACKUP_BASE_NAME = "wpgmza_backup";
	const BACKUP_BASE_STAMP = "Y-m-d-H-i";

	const FLAG_TYPE_MANUAL = "M";
	const FLAG_TYPE_PRE_IMPORT = "PI";
	const FLAG_TYPE_POST_UPDATE = "PU";

	const MAX_AUTO_BACKUPS = 3;

	const DISABLE_THRESHOLD = 1000;

	public function __construct() {
		$this->isReady = false;

		$this->loadDependencies();
		$this->prepareStorage();
	}

	/**
	 * Create a specific backup
	 * 
	 * @param string $filename The name
	 * @param string $flag The flag type
	 * 
	 * @return bool
	*/
	public function createBackup($filename = false, $flag = "M"){
		if($flag !== self::FLAG_TYPE_MANUAL){
			/* Not a manual backup, run safety checks to auto-disable if necessary */
			$autoDisabled = $this->disableAutoBackups();
			if($autoDisabled){
				return false;
			}
		}

		if(empty($filename) || strpos($filename, '.') !== FALSE){
			/*
			 * No filename, or filename contained a file suffix
			 *
			 * Use the default base name
			*/
			$filename = self::BACKUP_BASE_NAME;
		}

		if($this->isFlagAutomatedType($flag)){
			$this->removeOldBackupsOfType($flag);
		}

		$backupDate = date(self::BACKUP_BASE_STAMP);
		$filename = "{$filename}__{$backupDate}--{$flag}.json";

		$export = new ExportJSON();
		$json = $export->get_json();

		$storageDir = $this->getStorageDir();

		if(file_exists($storageDir)){
			$storagePath = implode('/', array($storageDir, $filename));
			if(!file_exists($storagePath)){
				try{
					@file_put_contents($storagePath, $json);
					return true;
				} catch (\Exception $ex){
					/* Do nothing, fail gracefully */
				} catch (\Error $err){
					/* Do nothing, fail gracefully */
				}
			}
		}
		return false;
	}

	/**
	 * Get all backups from storage
	 * 
	 * @return array
	*/
	public function getBackupFiles(){
		$backupDir = $this->getStorageDir();
		try{
			$files = list_files($backupDir);

			if(!empty($files)){
				$backups = array();

				foreach ($files as $filename) {
					$backups[] = $this->decodeFileName($filename);
				}

				return array_reverse($backups);
			}
		} catch (\Exception $ex){
			/* Might be running mid-cron, we can just ignore it */
		} catch (\Error $err){
			/* Same as exception */
		}
		return false;
	}

	/**
	 * Delete a specific backup
	 * 
	 * @param string $filename The name of the file to be deletece
	 * 
	 * @return bool
	*/
	public function deleteBackup($filename){
		if(strpos($filename, '.json') === FALSE){
			$filename .= ".json";
		}

		$files = $this->getBackupFiles();
		if(!empty($files)){
			foreach ($files as $file) {
				if($file['filename'] === $filename){
					if(file_exists($file['dir'])){
						wp_delete_file($file['dir']);
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Remove old backups for a specific type
	 * 
	 * @param string $type The type of backup
	 * 
	 * @return void
	*/
	private function removeOldBackupsOfType($type){
		$filesOfType = array();

		$files = $this->getBackupFiles();
		if(!empty($files)){
			foreach ($files as $file) {
				if($file['flag'] === $type){
					$filesOfType[] = $file;
				}
			}
		}

		if(count($filesOfType) >= self::MAX_AUTO_BACKUPS){
			if(!empty($filesOfType[count($filesOfType) - 1])){
				$fileToDelete = $filesOfType[count($filesOfType) - 1];

				if(file_exists($fileToDelete['dir'])){
					wp_delete_file($fileToDelete['dir']);
				}
			}
		}
	}

	/**
	 * Decode the file name, so that it can be printed
	 * 
	 * @param string $filename
	 * 
	 * @return array
	*/ 
	private function decodeFileName($filename){
		$filename = str_replace($this->getStorageDir() . "/", "", $filename);

		$decoded = array(
			"filename" => $filename,
			"dir" => $this->getStorageDir() . "/" . $filename,
			"url" => $this->getStorageDir(true) . "/" . $filename,
		);

		if(strpos($filename, "__") !== FALSE){
			$filename = str_replace(".json", "", $filename);
			
			$splitIndex = strpos($filename, "__");
			$rootName = substr($filename, 0, $splitIndex);
			$split = substr($filename, $splitIndex);

			if(!empty($rootName)){
				$rootName = str_replace("_", " ", $rootName);

				if(strpos($rootName, "wpgmza") !== FALSE){
					$rootName = str_replace("wpgmza", "WPGMZA", $rootName);
				}
				$decoded["pretty"] = ucwords($rootName);
			}

			$flag = self::FLAG_TYPE_MANUAL;
			if(!empty($split)){
				$split = str_replace("__", "", $split);
				if(strpos($split, "--") !== FALSE){
					$flag = substr($split, strpos($split, "--"));
					$flag = str_replace("--", "", $flag);
				}

				$decoded['flag'] = $flag;
				$decoded['flag_alias'] = $this->getFlagAlias($flag);

				$split = explode("-", $split);

				if(!empty($split)){
					$dateMap = array(
						'year',
						'month',
						'day',
						'hour',
						'min'
					);

					$decoded['date'] = array();
					foreach ($split as $key => $value) {
						if(!empty($dateMap[$key])){
							$decoded['date'][$dateMap[$key]] = intval($value);
						}
					}
				}

				if($this->isFlagAutomatedType($decoded['flag'])){
					$decoded["pretty"] .= " (Automated)";
				}
			}
		}

		return $decoded;
	}

	/**
	 * Is this an automatic flag type
	 * 
	 * @return bool
	*/
	private function isFlagAutomatedType($flag = "M"){
		return $flag !== self::FLAG_TYPE_MANUAL;
	}

	/**
	 * Get an alias for the backup flag
	 * 
	 * @param string $flag The type of backup being created as a flag
	 * 
	 * @return string
	*/
	private function getFlagAlias($flag = "M"){
		$type = "Unknown";
		switch ($flag) {
			case self::FLAG_TYPE_MANUAL:
				$type = "Manual";
				break;
			case self::FLAG_TYPE_POST_UPDATE:
				$type = "Post-Update";
				break;
			case self::FLAG_TYPE_PRE_IMPORT:
				$type = "Pre-Import";
				break;
		}

		return $type;
	}

	/**
	 * Load dependency files
	 * 
	 * @return void
	*/
	private function loadDependencies(){
		$path = plugin_dir_path( __FILE__ );
		require_once( $path . 'class.export.php' );		
		require_once( $path . 'class.export-json.php' );		
	}

	/**
	 * Prepare the storage directory
	 * 
	 * @return void
	*/
	private function prepareStorage(){
        $backupDir = $this->getStorageDir();
        if(!file_exists($backupDir)){
        	wp_mkdir_p($backupDir);
        }

        $isReady = true;
	}

	/**
	 * Get the storage directory path
	 * 
	 * @param bool $asUrl Should it be a URL
	 * 
	 * @return string
	*/
	private function getStorageDir($asUrl = false){
		$uploadDir = wp_upload_dir();
		if (!empty($uploadDir['basedir'])){
			if($asUrl && !empty($uploadDir['baseurl'])){
    			$uploadDir = $uploadDir['baseurl'];
			} else {
    			$uploadDir = $uploadDir['basedir'];
			}

    		return implode("/", 
    			array(
    				$uploadDir,
    				self::CORE_DIR,
    				self::BACKUP_DIR
    			)
    		);
    	}
    	return false;
	}

	/**
	 * Disable backups if the user has more than the safe threshold
	 * 
	 * If so, we will set a persistent notice, and disable the setting
	 * 
	 * The user can re-enable this manaully, and after that, no notice will be shown
	 * 
	 * This is a final safety marker 
	 * 
	 * Returns true if this trigger was actioned
	 * 
	 * @return bool
	*/
	private function disableAutoBackups(){
		global $wpgmza, $wpdb, $WPGMZA_TABLE_NAME_MARKERS;

		try{
			$noticeIssued = $wpgmza->adminNotices->get('disable_backups');
			if(empty($noticeIssued)){
				/* We have no previous notices, meaning this has never been disabled */

				$count = $wpdb->get_col("SELECT COUNT(id) FROM `{$WPGMZA_TABLE_NAME_MARKERS}`");

				if(!empty($count) && is_array($count)){
					$count = array_shift($count);
					if(!empty($count)){
						$count = intval($count);
						if($count >= self::DISABLE_THRESHOLD){
							$wpgmza->adminNotices->create(
								'disable_backups', 
								array(
									'link' => 'admin.php?page=wp-google-maps-menu-settings#miscellaneous',
									'link_label' => __("Manage Backups", "wp-google-maps"),
									'title' => 'disable_backups'
								)
							);

							if(empty($wpgmza->settings->disable_automatic_backups)){
								$wpgmza->settings->disable_automatic_backups = true;
							}

							return true;
						}
					}
				}

			}
		} catch (\Exception $ex){
			// Do nothing
		} catch (\Error $err){
			// Say nothing
		}

		return false;
	}
}
