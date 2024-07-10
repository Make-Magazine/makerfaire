<?php

namespace WPGMZA;

class MarkerRating extends Factory implements \JsonSerializable
{
	const TAMPERING_COUNTERMEASURE_BASIC_ONLY		= "basic-only";
	const TAMPERING_COUNTERMEASURE_ANTI_SPAM		= "anti-spam";
	const TAMPERING_COUNTERMEASURE_REQUIRE_ACCOUNT	= "require-account";
	
	const REJECTED_INVALID_AMOUNT					= "invalid-amount";
	const REJECTED_LOGIN_REQUIRED					= "login-required";
	const REJECTED_INVALID_GUID						= "invalid-guid";
	
	protected $marker;
	
	protected $average;
	protected $count;
	
	public function __construct($marker)
	{
		if(!($marker instanceof Marker))
			throw new \Exception('Input must be an instance of WPGMZA\\Marker');
		
		$this->average = 0;
		$this->count = 0;
		
		$this->marker = $marker;
		$this->read();
	}
	
	public static function install()
	{
		global $wpgmza;
		// Grab the defaults from the HTML settings panel
		$document = new DOMDocument();

		if(!empty($wpgmza) && !empty($wpgmza->goldAddOn) && $wpgmza->goldAddOn->checkCoreAtlasNovus()){
			/* TODO: V6 should only use this approach and not be blocked into an IF statement */
			// V9.0.0 + Atlas Novus
			$document->loadPHPFile($wpgmza->internalEngine->getTemplate('marker-rating-settings.html.php', WPGMZA_GOLD_DIR_PATH));
		} else {
			$document->loadPHPFile(plugin_dir_path(__DIR__) . 'html/marker-rating-settings.html.php');
		}
		
		$defaults = $document->serializeFormData();
		
		// Access the settings directly, the plugin object has not loaded yet
		$settings = new GlobalSettings();
		
		foreach($defaults as $key => $value)
			$settings->{$key} = $value;
	}
	
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		return array(
			'average' => $this->average,
			'count' => $this->count
		);
	}
	
	public function read()
	{
		global $wpdb;
		global $WPGMZA_TABLE_NAME_RATINGS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;
		
		if(isset($marker->averageRating) && isset($marker->numRatings))
		{
			// TODO: Bulk read
			$this->average = $marker->averageRating;
			$this->count = $marker->numRatings;
			
			unset($this->marker->averageRating);
			unset($this->marker->numRatings);
		}
		else
		{
			$stmt = $wpdb->prepare("SELECT AVG(amount) AS average, count(id) AS count
				FROM $WPGMZA_TABLE_NAME_RATINGS 
				WHERE id IN (
					SELECT rating_id
					FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS
					WHERE marker_id=%d
				)
			", array(
				$this->marker->id
			));
			
			$result = $wpdb->get_row($stmt);
			
			$this->average = $result->average;
			$this->count = $result->count;
		}
	}
	
	public function update($amount, $userGuid=null)
	{
		global $wpgmza;
		global $wpdb;
		global $WPGMZA_TABLE_NAME_RATINGS;
		global $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS;
		
		$existing_record_id = null;
		$ip_address = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$author		= get_current_user_id();
		
		if(!$this->isAmountValid($amount))
		{
			$this->reasonForRejection = MarkerRating::REJECTED_INVALID_AMOUNT;
			return false;
		}
		
		switch($wpgmza->settings->marker_rating_tampering_countermeasures)
		{
			case MarkerRating::TAMPERING_COUNTERMEASURE_REQUIRE_ACCOUNT:
			
				if(!$author)
				{
					$this->reasonForRejection = MarkerRating::REJECTED_LOGIN_REQUIRED;
					return false;
				}
				
			case MarkerRating::TAMPERING_COUNTERMEASURE_BASIC_ONLY:
			case MarkerRating::TAMPERING_COUNTERMEASURE_ANTI_SPAM:
			default:
				
				// If the user is logged in, always use their user ID, regardless of what countermeasure is selected
				if($author)
				{
					$stmt = $wpdb->prepare("SELECT id FROM $WPGMZA_TABLE_NAME_RATINGS WHERE author=%d AND id IN (SELECT rating_id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS WHERE marker_id=%d)", array(
						$author,
						$this->marker->id
					));
					
					$existing_record_id = $wpdb->get_var($stmt);
					
					break;
				}
				
				// User is not logged in. Check against GUID
				if(!preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $userGuid))
				{
					$this->reasonForRejection = MarkerRating::REJECTED_INVALID_GUID;
					return false;
				}
				
				$stmt = $wpdb->prepare("SELECT id FROM $WPGMZA_TABLE_NAME_RATINGS WHERE user_guid LIKE %s AND id IN (SELECT rating_id FROM $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS WHERE marker_id=%d)", array(
					$userGuid,
					$this->marker->id
				));
				
				$existing_record_id = $wpdb->get_var($stmt);
				
				break;
		}
		
		if($existing_record_id)
		{
			$qstr = "UPDATE $WPGMZA_TABLE_NAME_RATINGS SET amount=%d, ip_address=%s, user_agent=%s";
			$params = array(
				$amount,
				$ip_address,
				$user_agent
			);
			
			if($author)
			{
				$qstr .= ", author=%d";
				$params[] = $author;
			}
			
			$qstr .= " WHERE id=%d";
			$params[] = $existing_record_id;
			
			$stmt = $wpdb->prepare($qstr, $params);
			$wpdb->query($stmt);
		}
		else
		{
			$stmt = $wpdb->prepare("INSERT INTO $WPGMZA_TABLE_NAME_RATINGS (created, amount, ip_address, user_agent, user_guid, author) VALUES (NOW(), %f, %s, %s, %s, %d)", array(
				$amount,
				$ip_address,
				$user_agent,
				$userGuid,
				$author
			));
			
			$wpdb->query($stmt);
			
			$rating_id = $wpdb->insert_id;
			
			$stmt = $wpdb->prepare("INSERT INTO $WPGMZA_TABLE_NAME_MARKERS_HAS_RATINGS (rating_id, marker_id) VALUES (%d, %d)", array($rating_id, $this->marker->id));
			$wpdb->query($stmt);
		}
		
		// Refresh
		$this->read();
		
		$this->reasonForRejection = null;
		return true;
	}
	
	protected function isAmountValid($amount)
	{
		global $wpgmza;
		
		return ($amount >= $wpgmza->settings->minimum_rating && $amount <= $wpgmza->settings->maximum_rating);
	}
	
	public static function onGlobalSettingsTabs($html)
	{
		return $html . "<li><a href=\"#marker-ratings\">".__("Marker Ratings","wp-google-maps")."</a></li>";
	}
	
	public static function onGlobalSettingsTabContent($html)
	{
		global $wpgmza;
		
		MarkerRatingWidget::enqueueStyles();
		
		$document = new DOMDocument();
		if(!empty($wpgmza) && !empty($wpgmza->goldAddOn) && $wpgmza->goldAddOn->checkCoreAtlasNovus()){
			/* TODO: V6 should only use this approach and not be blocked into an IF statement */
			// V9.0.0 + Atlas Novus
			$document->loadPHPFile($wpgmza->internalEngine->getTemplate('marker-rating-settings.html.php', WPGMZA_GOLD_DIR_PATH));
		} else {
			$document->loadPHPFile(plugin_dir_path(__DIR__) . 'html/marker-rating-settings.html.php');
		}
		
		$document->populate($wpgmza->settings);
		
		return $html . $document->html;
	}
	
	public static function onSaveSettings()
	{
		global $wpgmza;
		
		$document = new DOMDocument();
		
		if(!empty($wpgmza) && !empty($wpgmza->goldAddOn) && $wpgmza->goldAddOn->checkCoreAtlasNovus()){
			/* TODO: V6 should only use this approach and not be blocked into an IF statement */
			// V9.0.0 + Atlas Novus
			$document->loadPHPFile($wpgmza->internalEngine->getTemplate('marker-rating-settings.html.php', WPGMZA_GOLD_DIR_PATH));
		} else {
			$document->loadPHPFile(plugin_dir_path(__DIR__) . 'html/marker-rating-settings.html.php');
		}
		
		$document->populate($_POST);
		$data = $document->serializeFormData();
		
		$wpgmza->settings->set($data);
	}
}

add_filter('wpgmza_global_settings_tabs', array('WPGMZA\\MarkerRating', 'onGlobalSettingsTabs'), 9, 1);
add_filter('wpgmza_global_settings_tab_content', array('WPGMZA\\MarkerRating', 'onGlobalSettingsTabContent'), 9, 1);

add_action('admin_post_wpgmza_settings_page_post_pro', function() {
	
	MarkerRating::onSaveSettings();
	
});