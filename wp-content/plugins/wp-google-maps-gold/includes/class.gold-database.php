<?php

namespace WPGMZA;

#[\AllowDynamicProperties]
class GoldDatabase
{
	public function __construct()
	{
		global $wpgmza;
		global $wpgmza_gold_version;
		
		$this->version = get_option('wpgmza_gold_db_version');
		
		if(version_compare($this->version, $wpgmza_gold_version, '<'))
			$this->install();
	}
	
	public function install()
	{
		global $wpgmza_gold_version;
		
		require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
		
		$this->installRatingsTable();
		$this->installMarkersHasRatingsTable();
		$this->installDevicesTable();
		
		MarkerRating::install();
		
		update_option('wpgmza_gold_db_version', $wpgmza_gold_version);
	}
	
	protected function installRatingsTable(){
		global $WPGMZA_TABLE_NAME_RATINGS;
		
		$sql = "CREATE TABLE `$WPGMZA_TABLE_NAME_RATINGS` (
			id int(11) NOT NULL AUTO_INCREMENT,
			created DATETIME NOT NULL,
			amount DECIMAL(9,3),
			ip_address VARCHAR(40),
			user_agent TEXT,
			user_guid VARCHAR(36),
			author int(11) NULL,
			PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			";

		dbDelta($sql);
	}
	
	protected function installMarkersHasRatingsTable(){
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;
		
		$sql = "CREATE TABLE `$WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS` (
			marker_id int(11) NOT NULL,
			rating_id int(11) NOT NULL,
			PRIMARY KEY  (marker_id, rating_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			";

		dbDelta($sql);
	}

	protected function installDevicesTable(){
		global $WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES;
		
		$sql = "CREATE TABLE {$WPGMZA_TABLE_NAME_LIVE_TRACKING_DEVICES} (
			id int(11) NOT NULL AUTO_INCREMENT,
			deviceID VARCHAR(100) NOT NULL,
			name VARCHAR(256) NULL DEFAULT NULL,
			last_contact DATETIME NOT NULL,
			marker_id int(11) NOT NULL DEFAULT -1,
			polyline_id int(11) NOT NULL DEFAULT -1,
			approved int(1) NOT NULL DEFAULT 0,
			other_data TEXT NULL,
			PRIMARY KEY  (id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			";

		dbDelta($sql);

	}
}