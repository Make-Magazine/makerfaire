<?php
if ( ! class_exists( 'GFJDBHELPER' ) ) {
	die();
}

class GFJDBHELPER {
	/*
	 	Function to send notes directly to JDB.
	  	note             	(the text)
		app_id          	(not sure what this is.  ignore it)
		CS_ID          		(obviously)
		note_id         	(a unique identifier so we only add a note once)
		date_posted  		(original date of creation on the WP side format: yyyy-mm-dd hh:mm:ss )
		author           	(name of the poster)
		email           	(email of the poster)
	*/
	public static function gravityforms_send_note_to_jdb( $id = 0, $noteid=0, $note = '' , $note_username='', $note_datecreated='') {
		$local_server = array( 'localhost', 'make.com', 'makerfaire.local', 'staging.makerfaire.com' );
		$remote_post_url = 'http://db.makerfaire.com/addExhibitNote';
		if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], $local_server ) )
			$remote_post_url= 'http://makerfaire.local/wp-content/allpostdata.php';
		$encoded_array = http_build_query(
				array( 'CS_ID' => intval( $id ),
						'note_id' => intval( $noteid ),
						'note' => esc_attr( $note ),
						'author' => esc_attr($note_username),
						'date_posted' => esc_attr(date("Y-m-d H:i:s", strtotime($note_datecreated)))
				)
		);

		$post_body = array(
				'method' => 'POST',
				'timeout' => 45,
				'headers' => array(),
				'body' => $encoded_array);
		$res  = wp_remote_post( $remote_post_url, $post_body  );
		$er  = 0;

		if ( 200 == $res['response']['code'] ) {
			$body = json_decode( $res['body'] );
			if ( 'ERROR' != $body->status ) {
				$er = time();
			}
			gform_update_meta( $id, 'mf_jdb_note_sync', $body );

		}
		return $er;
	}

	public static function gravityforms_send_entry_to_jdb ($id)
	{
		$mysqli = new mysqli ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}

		error_log('gravityforms_send_entry_to_jdb');
		$entry_id=$id;
		$entry = GFAPI::get_entry($entry_id);
		$form = GFAPI::get_form($entry['form_id']);
		//$jdb_encoded_entry = gravityforms_to_jdb_record($entry,$row[0],$row[1]);
		$jdb_encoded_entry = http_build_query(self::gravityforms_to_jdb_record($entry,$entry_id,$form));
		$synccontents = '"'.$mysqli->real_escape_string($jdb_encoded_entry).'"';
		$results_on_send = self::gravityforms_send_record_to_jdb($entry_id,$jdb_encoded_entry);
		$results_on_send_prepared = '"'.$mysqli->real_escape_string($results_on_send).'"';

		// MySqli Insert Query
		$insert_row = $mysqli->query("INSERT INTO `wp_rg_lead_jdb_sync`(`lead_id`, `synccontents`, `jdb_response`) VALUES ($entry_id,$synccontents, $results_on_send_prepared)");
		if($insert_row){
			error_log( 'Success! Response from JDB  was: ' .$results_on_send .'<br />');
		}else{
			die('Error : ('. $mysqli->errno .') '. $mysqli->error);
		};

	}

	public static function gravityforms_send_record_to_jdb($entry_id, $jdb_encoded_record) {
		$local_server = array (
				'localhost',
				'make.com',
				'makerfaire.local',
				'staging.makerfaire.com'
		);
		$remote_post_url = 'http://db.makerfaire.com/updateExhibitInfo';
		if (isset ( $_SERVER ['HTTP_HOST'] ) && in_array ( $_SERVER ['HTTP_HOST'], $local_server ))
			$remote_post_url = 'http://makerfaire.local/wp-content/allpostdata.php';

		$post_body = array (
				'method' => 'POST',
				'timeout' => 45,
				'headers' => array (),
				'body' => $jdb_encoded_record
		);

		$res = wp_remote_post ( $remote_post_url, $post_body );
		if (200 == wp_remote_retrieve_response_code ( $res )) {
			$body = json_decode ( $res ['body'] );
			if ($body->exhibit_id == '' || $body->exhibit_id == 0) {
				gform_update_meta ( $entry_id, 'mf_jdb_sync', 'fail' );
			} else {
				gform_update_meta ( $entry_id, 'mf_jdb_sync', time () );
			}
		}
		else 	gform_update_meta ( $entry_id, 'mf_jdb_sync', 'fail' );

		return ($res ['body']);
	}
	/*
	 * Function for formatting gravity forms lead into usable jdb data
	*/
	public static function gravityforms_to_jdb_record($lead,$lead_id,$form) {
		//load form
		$form_id = $form['id'];

		// Load Names
		$allmakername='';
		$makerfirstname1 = (isset($lead['160.3']) ? $lead['160.3']:'');
    $makerlastname1  = (isset($lead['160.6']) ? $lead['160.6']:'');
		$makerfirstname2 = (isset($lead['158.3']) ? $lead['158.3']:'');
    $makerlastname2  = (isset($lead['158.6']) ? $lead['158.6']:'');
		$makerfirstname3 = (isset($lead['155.3']) ? $lead['155.3']:'');
    $makerlastname3  = (isset($lead['155.6']) ? $lead['155.6']:'');
		$makerfirstname4 = (isset($lead['156.3']) ? $lead['156.3']:'');
    $makerlastname4  = (isset($lead['156.6']) ? $lead['156.6']:'');
		$makerfirstname5 = (isset($lead['157.3']) ? $lead['157.3']:'');
    $makerlastname5  = (isset($lead['157.6']) ? $lead['157.6']:'');
		$makerfirstname6 = (isset($lead['159.3']) ? $lead['159.3']:'');
    $makerlastname6  = (isset($lead['159.6']) ? $lead['159.6']:'');
		$makerfirstname7 = (isset($lead['154.3']) ? $lead['154.3']:'');
    $makerlastname7  = (isset($lead['154.6']) ? $lead['154.6']:'');
		$allmakername = $allmakername + !empty($makerfirstname1) ?       $makerfirstname1.' '.$makerlastname1 : '' ;
		$allmakername = $allmakername + !empty($makerfirstname2) ?  ', '.$makerfirstname2.' '.$makerlastname2 : '' ;
		$allmakername = $allmakername + !empty($makerfirstname3) ?  ', '.$makerfirstname3.' '.$makerlastname3 : '' ;
		$allmakername = $allmakername + !empty($makerfirstname4) ?  ', '.$makerfirstname4.' '.$makerlastname4 : '' ;
		$allmakername = $allmakername + !empty($makerfirstname5) ?  ', '.$makerfirstname5.' '.$makerlastname5 : '' ;
		$allmakername = $allmakername + !empty($makerfirstname6) ?  ', '.$makerfirstname6.' '.$makerlastname6 : '' ;
		$allmakername = $allmakername + !empty($makerfirstname7) ?  ', '.$makerfirstname7.' '.$makerlastname7 : '' ;

    // Load Categories
		$fieldtopics=RGFormsModel::get_field($form,'147');
    if(!is_array($fieldtopics['inputs'] ))  $fieldtopics['inputs'] =array();
		$topicsarray = array();

		foreach($fieldtopics['inputs'] as $topic) {
			if (strlen($lead[$topic['id']]) > 0)  $topicsarray[] = $lead[$topic['id']];
		}

		// Load Plans
		$fieldplans=RGFormsModel::get_field($form,'55');
		if(!is_array($fieldplans['inputs'] ))  $fieldplans['inputs'] =array();

    $plansarray = array();
    foreach($fieldplans['inputs'] as $plan) {
			if (strlen($lead[$plan['id']]) > 0)  $plansarray[] = $lead[$plan['id']];
		}

		// Load Locations
		$fieldlocations=RGFormsModel::get_field($form,'70');
    if(!is_array($fieldlocations['inputs'] ))  $fieldlocations['inputs'] =array();

		$locationsarray = array();
		foreach($fieldlocations['inputs'] as $location) {
			if (strlen($lead[$location['id']]) > 0)  $locationsarray[] = $lead[$location['id']];
		}

		// Load RF
		$rfinputs=RGFormsModel::get_field($form,'79');
    if(!is_array($rfinputs['inputs'] ))  $rfinputs['inputs'] =array();

		$rfarray = array();
		foreach($rfinputs['inputs'] as $rfinput) {
			if (strlen($lead[$rfinput['id']]) > 0)  $rfarray[] = $lead[$rfinput['id']];
		}
		// Load statuses
		//$entrystatuses=RGFormsModel::get_field($form,'303');
		//$currentstatus = "";
		//foreach($entrystatuses['inputs'] as $entrystatus)
		//{
			//	if (strlen($lead[$entrystatus['id']]) > 0)  $currentstatus = $lead[$entrystatus['id']];
			//}
		$jdb_entry_data = array(
				'form_type' => $form_id, //(Form ID)
				'return_form_type' => self::gravityforms_form_type_jdb($form_id), //(Form ID)
				'noise' => isset($lead['72']) ? $lead['72'] : '',
				'radio' => isset($lead['78']) ? $lead['78'] : '',
				'hands_on' => isset($lead['66']) ? $lead['66'] : '',
				'referrals' => isset($lead['127']) ? $lead['127']  : '',
				'food_details' => isset($lead['144']) ? $lead['144']  : '',
				'fire' =>  isset($lead['83']) ? $lead['83']  : '',
				'booth_size_details' => isset($lead['61']) ? $lead['61']  : '',
				'layout' => isset($lead['65']) ? $lead['65']  : '',
				'amps_details' =>  isset($lead['76']) ? $lead['76']  : '',
				'booth_size' => isset($lead['60']) ? $lead['60']  : '',
				'group_bio' => isset($lead['110']) ? $lead['110']  : '',
				'group_website' => isset($lead['112']) ? $lead['112']  : '',
				'hear_about' => isset($lead['128']) ? $lead['128']  : '',
				'maker_faire' => isset($lead['131']) ? $lead['131']  : '',
				'project_website' => isset($lead['27']) ? $lead['27']  : '',
				'supporting_documents' => isset($lead['122']) ? $lead['122']  : '',
				'tables_chairs' => isset($lead['62']) ? $lead['62']  : '',
				'project_video' => isset($lead['32']) ? $lead['32']  : '',
				'cats' => isset($topicsarray) ? $topicsarray  : '',
				'loctype' => isset($lead['69']) ? $lead['69']  : '',
				'tables_chairs_details' => isset($lead['288']) ? $lead['288']  : '',
				'internet' => isset($lead['77']) ? $lead['77']  : '',
				'maker_photo' => isset($lead['217']) ? $lead['217']  : '',
				'email' => isset($lead['98']) ? $lead['98']  : '',
				'project_photo' => isset($lead['22']) ? $lead['22']  : '',
				'project_name' => isset($lead['151']) ? $lead['151']  : '',
				'first_time' => isset($lead['130']) ? $lead['130']  : '',
				'power' => isset($lead['73']) ? $lead['73']  : '',
				'food' => isset($lead['44']) ? $lead['44']  : '',
				'safety_details' => isset($lead['85']) ? $lead['85']  : '',
				'anything_else' => isset($lead['134']) ? $lead['134']  : '',
				'phone1_type' => isset($lead['148']) ? $lead['148']  : '',
				'maker_bio' => isset($lead['234']) ? $lead['234']  : '',
				'group_photo' => isset($lead['111']) ? $lead['111']  : '',
				'lighting' => isset($lead['71']) ? $lead['71']  : '',
				'phone1' => isset($lead['99']) ? $lead['99']  : '',
				'project_photo_thumb' => '',
				'group_name' => isset($lead['109']) ? $lead['109']  : '',
				'private_address' => isset($lead['101.1']) ? $lead['101.1']  : '',
				'private_state' => isset($lead['101.4']) ? $lead['101.4']  : '',
				'private_city' => isset($lead['101.3']) ? $lead['101.3']  : '',
				'private_address2' => isset($lead['101.2']) ? $lead['101.2']  : '',
				'private_country' => isset($lead['101.6']) ? $lead['101.6']  : '',
				'private_zip' => isset($lead['101.5']) ? $lead['101.5']  : '',
				'placement' => isset($lead['68']) ? $lead['68']  : '',
				'firstname' => isset($lead['96.3']) ? $lead['96.3']  : '',
				'lastname' => isset($lead['96.6']) ? $lead['96.6']  : '',
				'phone2_type' => isset($lead['149']) ? $lead['149']  : '',
				'maker_name' => isset($allmakername) ? $allmakername  : '',
				'radio_frequency' => $rfarray,
				'what_are_you_powering' => isset($lead['74']) ? $lead['74']  : '',
				'private_description' => isset($lead['11']) ? $lead['11']  : '',
				'org_type' => isset($lead['45']) ? $lead['45']  : '',
				'public_description' => isset($lead['16']) ? $lead['16']  : '',
				'activity' => isset($lead['84']) ? $lead['84']  : '',
				'amps' => isset($lead['75']) ? $lead['75']  : '',
				'sales_details' => isset($lead['52']) ? $lead['52']  : '',
				'phone2' => isset($lead['100']) ? $lead['100']  : '',
				'maker' => isset($lead['105']) ? $lead['105']  : '',
				'non_profit_desc' => isset($lead['47']) ? $lead['47']  : '',
				'plans' => isset($plansarray) ? $plansarray  : '',
				'launch_details' => isset($lead['54']) ? $lead['54']  : '',
				'crowdfunding' => isset($lead['56']) ? $lead['56']  : '',
				'crowdfunding_details' => isset($lead['59']) ? $lead['59']  : '',
				'special_request' => isset($lead['64']) ? $lead['64']  : '',
				'hands_on_desc' => isset($lead['67']) ? $lead['67']  : '',
				'activity_wrist' => isset($lead['293']) ? $lead['293']  : '',
				'loctype_outdoors' => $locationsarray,
				'makerfaire_other' => isset($lead['132']) ? $lead['132']  : '',
				'under_18' => (isset($lead['295']) && $lead['295'] == "Yes") ? 'NO'  : 'YES',
				'CS_ID' => $lead_id,
				'status' => isset($lead['303']) ? $lead['303']  : '',
				'waste' => (isset($lead['317']) && $lead['317'] == "Yes") ?  'YES' : 'NO',
				'waste_detail' => isset($lead['318']) ? $lead['318']  : '',
				'learn_to' => isset($lead['319']) ? $lead['319']  : '',
        'numTables'  => isset($lead['347']) ? $lead['347']  : 0,
        'numChairs'  => isset($lead['348']) ? $lead['348']  : 0,
        '344'        => isset($lead['344']) ? $lead['344']  : 0,
        '345'        => isset($lead['345']) ? $lead['345']  : 0,
        '81'         => isset($lead['81'])  ? $lead['81']   : '',
        'fType'            => isset($form['form_type']) ? $form['form_type'] : '',
        'paymentElectr'     => isset($lead['8'])  ? $lead['8']   : '',
        'paymentDescElect'  => isset($lead['12']) ? $lead['12']  : '',
        'paymentTable'      => isset($lead['14']) ? $lead['14']  : '',
        'origEntryID'       => isset($lead['20']) ? $lead['20']  : '',
				//'m_maker_name' => isset($lead['96']) ? $lead['96']  : '',
				//'maker_email' => isset($lead['161']) ? $lead['161']  : '',
				//'presentation' => isset($lead['No']) ? $lead['999']  : '', //(No match)
				//'performance' => isset($lead['No']) ? $lead['999']  : '', // (No match)
				//'maker_photo_thumb' => '', //$lead['http://mf.insourcecode.com/wp-content/uploads/2013/02/IMG_1823_crop1-362x500.jpg (No Match)']
				//'ignore' => isset($lead['']) ? $lead['999']  : '',
				//'tags' => isset($lead['3d-imaging, alternative-energy, art, art-cars, bicycles, biology, chemistry, circuit-bending, computers']) ? $lead['999']  : '',// (No Match)
				//'group_photo_thumb' => isset($lead['']) ? $lead['999']  : '',// (No Match)
				//'large_non_profit' => isset($lead['I am a large non-profit.']) ? $lead['999']  : '',// (No Match)
				//'m_maker_bio' => $lead[' (Depends on Contact vs. Maker issue?)'],
		);
    //build rmt tables
    self::buildRmtData($jdb_entry_data);
		return $jdb_entry_data;


	}

	/*
	 * Function to do the actual sending to jdb
	*/
	public static function gravityforms_post_submission_entry_to_jdb( $entry_id,$jdb_encoded_record ) {
		// Don't sync from any of our testing locations.
		$local_server = array( 'localhost', 'make.com', 'makerfaire.local', 'staging.makerfaire.com' );
		//$remote_post_url = 'http://db.makerfaire.com/updateExhibitInfo';
		$remote_post_url='';
		if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], $local_server ) )
			$remote_post_url= 'http://makerfaire.local/wp-content/allpostdata.php';

		$post_body = array(
				'method' => 'POST',
				'timeout' => 45,
				'headers' => array(),
				'body' => $jdb_encoded_record);

		$res  = wp_remote_post( $remote_post_url, $post_body  );
		if ( 200 == wp_remote_retrieve_response_code( $res ) ) {
			$body = json_decode( $res['body'] );
			if ( $body->exhibit_id == '' && $body->exhibit_id == 0 ) {
				gform_update_meta( $entry_id, 'mf_jdb_sync', 'fail' );
			} else {
				gform_update_meta( $entry_id, 'mf_jdb_sync', time() );
			}
		}
		return ($res['body']);
	}
	public static function gravityforms_sync_all_entry_notes($entry_id) {
		$mysqli = new mysqli ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}

		$result = $mysqli->query ( 'SELECT value,id,user_name,date_created FROM wp_rg_lead_notes where lead_id=' . $entry_id . '' );

		while ( $row = $result->fetch_row () ) {
			$results_on_send = self::gravityforms_send_note_to_jdb ( $entry_id, $row[1], $row [0], $row [2], $row[3] );
		}
	}


	/*
	 * Sync MakerFaire Application Statuses
	*
	* @access private
	* @param int $id Post id to SYNC
	* @param string $app_status Post status
	* =====================================================================*/
	public static function gravityforms_sync_status_jdb( $id = 0, $status = '' ) {
		$local_server = array( 'localhost', 'make.com', 'makerfaire.local', 'staging.makerfaire.com' );
		$remote_post_url = 'http://db.makerfaire.com/updateExhibitStatus';
		if ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], $local_server ) )
			$remote_post_url= 'http://makerfaire.local/wp-content/allpostdata.php';
		$encoded_array = http_build_query(  array( 'CS_ID' => intval( $id ), 'status' => esc_attr( $status )));

		$post_body = array(
				'method' => 'POST',
				'timeout' => 45,
				'headers' => array(),
				'body' => $encoded_array);
		print_r($encoded_array);
		$res  = wp_remote_post( $remote_post_url, $post_body  );
		$er  = 0;

		if ( 200 == $res['response']['code'] ) {
			$body = json_decode( $res['body'] );
			if ( 'ERROR' != $body->status ) {
				$er = time();
			}
		}
		self::gravityforms_sync_all_entry_notes($id);
		gform_update_meta( $id, 'mf_jdb_status_sync', $er );

		return $er;
	}

	public static function gravityforms_form_type_jdb($formid = 0)
	{
		$return_formtype = 'Other';

		switch ($formid) {
			case 20:
			case 22:
			case 25:
				$return_formtype = 'Exhibit';
				break;
			case 12:
			case 15:
			case 31:
			case 26:
				$return_formtype = 'Presentation';
				break;
			case 13:
			case 27:
				$return_formtype = 'Performance';
				break;
			case 16:
			case 28:
			case 29:
				$return_formtype = 'Sponsor';
				break;
			default:
				$return_formtype = 'Other';
				break;
		}

		return $return_formtype;
	}

  public static function buildRmtData($entryData){
    global $wpdb;
    $resourceID  = array();
    $attributeID = array();
    $attribute   = array();
    $resource    = array();
    $entryID     = $entryData['CS_ID'];

    /*
     *  E N T R Y   R E S O U R C E S   M A P P I N G
     *   build list of resource ID's and tokens
     */
    $sql = "select ID,token from wp_rmt_resources";
    foreach($wpdb->get_results($sql) as $row){
      $resourceID[$row->token] = $row->ID;
    }

    /* build list of attribute ID's and tokens */
    $sql = "select ID,token from wp_rmt_entry_att_categories";
    foreach($wpdb->get_results($sql) as $row){
      $attributeID[$row->token] = $row->ID;
    }

    //resource ID's are set based on token
    /* Resource Mapping */

    /*  Field ID 62 = tables_chairs */
    if($entryData['tables_chairs'] == '1 table and 2 chairs'){
      $resource[] = array($resourceID['TBL_8x30'],1,'');
      $resource[] = array($resourceID['CH_FLD'],2,'');
    }elseif($entryData['tables_chairs'] == 'More than 1 table and 2 chairs. List specific number of tables and chairs below.'){
      /*  Field ID 347 (Number of Tables)
       *  Field ID 348 (Number of Chairs) */
      $resource[] = array($resourceID['TBL_8x30'],$entryData['numTables'],'');
      $resource[] = array($resourceID['CH_FLD'],$entryData['numChairs'],'');
    }

    /*  Field ID 73 = power */
    if($entryData['power'] == 'Yes'){
      /*  Field ID 75 = amps  */
      /*  Field ID 74 - what_are_you_powering */
      if($entryData['amps'] == '5 amps (0-500 watts, 120V)'){
        $resource[] = array($resourceID['120V-05A'],1,'');
      }elseif($entryData['amps'] == '10 amps (501-1000 watts, 120V)'){
        $resource[] = array($resourceID['120V-10A'],1,'');
      }elseif($entryData['amps'] == '15 amps (1001-1500 watts, 120V)'){
        $resource[] = array($resourceID['120V-15A'],1,'');
      }elseif($entryData['amps'] == '20 amps (1501-2000 watts, 120V)'){
        $resource[] = array($resourceID['120V-20A'],1,);
      }elseif($entryData['amps'] == '30 amps (2000-3000 watts, 120V)'){
        $resource[] = array($resourceID['120V-30A'],1,'');
      }elseif($entryData['amps'] == '50 amps (3001-5000 watts, 120V)'){
        $resource[] = array($resourceID['120V-50A'],1,'');
      }else{
        //Other. Power request specified in the Special Power Requirements box
        //[go to Field ID 76]
        /* Field ID 76 = amps_details (textarea) */
        //what resource should i use??
      }
    }

    //if form type=payment we need to map resource fields back to the original entry
    if($entryData['fType'] == 'Payment' ){
      $pos = strpos($entryData['paymentElectr'], '5 Amp (120v)');
      if ($pos !== false)     $resource[] = array($resourceID['120V-05A'],1,'');
      $pos = strpos($entryData['paymentElectr'], '10 Amp (120v)');
      if ($pos !== false)     $resource[] = array($resourceID['120V-10A'],1,'');
      $pos = strpos($entryData['paymentElectr'], '15 Amp (120v)');
      if ($pos !== false)     $resource[] = array($resourceID['120V-15A'],1,'');
      $pos = strpos($entryData['paymentElectr'], '20 Amp (120v)');
      if ($pos !== false)     $resource[] = array($resourceID['120V-20A'],1,'');
      $pos = strpos($entryData['paymentElectr'], '30 Amp (120v)');
      if ($pos !== false)     $resource[] = array($resourceID['120V-30A'],1,'');
      $pos = strpos($entryData['paymentElectr'], '50 Amp (120v)');
      if ($pos !== false)     $resource[] = array($resourceID['120V-50A'],1,'');
      $pos = strpos($entryData['paymentElectr'], 'Other/Not Listed');
      if ($pos !== false)     $attribute[] = array($attributeID['ELEC'],'Special Request', $entryData['paymentDescElect']);

      //field 14 - tables
      $pos = strpos($entryData['paymentTable'], 'One table');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],1,'');
      $pos = strpos($entryData['paymentTable'], 'Two tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],2,'');
      $pos = strpos($entryData['paymentTable'], 'Three Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],3,'');
      $pos = strpos($entryData['paymentTable'], 'Four Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],4,'');
      $pos = strpos($entryData['paymentTable'], 'Five Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],5,'');
      $pos = strpos($entryData['paymentTable'], 'Six Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],6,'');
      $pos = strpos($entryData['paymentTable'], 'Seven Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],7,'');
      $pos = strpos($entryData['paymentTable'], 'Eight Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],8,'');
      $pos = strpos($entryData['paymentTable'], 'Nine Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],9,'');
      $pos = strpos($entryData['paymentTable'], 'Ten Tables');
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],10,'');
      $pos = strpos($entryData['paymentTable'], "I don't need a table");
      if ($pos !== false)     $resource[] = array($resourceID['TBL_8x30'],0,'');
      //get original entry id
      $entryID = ($entryData['origEntryID'] !='' ?$entryData['origEntryID']:$entryID);
    }
    /*
     * E N T R Y   A T T R I B U T E   M A P P I N G
     *    build list of resource ID's and tokens
     */

    if($entryData['what_are_you_powering']!=''){
      $attribute[] = array($attributeID['ELEC'], 'What are you Powering?',$entryData['what_are_you_powering']);
    }
    if($entryData['amps_details']!=''){
      $attribute[] = array($attributeID['ELEC'],'Special Request', $entryData['amps_details']);
    }

    /*  Field ID 64 = special_request (textarea)*/
    if($entryData['special_request']!=''){
      $attribute[] = array($attributeID['SPECL'],'Special',$entryData['special_request']);
    }

    /*  Field ID 83 = fire
     *  Field ID 85 = safety_details
     */
    if($entryData['fire'] == 'Yes'){
      $attribute[] = array($attributeID['FIRE'],'',$entryData['safety_details']);
    }

    /*  Field ID 60 = booth_size
     *  Field ID 61 = booth_size_details
     */
    if($entryData['booth_size'] == "Other"){ //concatenate field ID 345 x field ID 344
      //  Field ID 344 - Requested space size length and
      //  Field ID 345 - Requested space size width
      $attribute[] = array($attributeID['SPACESIZE'],$entryData['345'].' X '.$entryData['344'],$entryData['booth_size_details']);
    }else{
      $attribute[] = array($attributeID['SPACESIZE'],$entryData['booth_size'],$entryData['booth_size_details']);
    }

    /*  Field ID 69 (Exposure) = loctype */
    if($entryData['loctype'] != ""){
      $attribute[] = array($attributeID['EX_IN'],$entryData['loctype'],$entryData['placement'].','.implode(',',$entryData['loctype_outdoors']));
    }

    /*  Field ID 71 = lighting*/
    if($entryData['lighting']!=''){
      $attribute[] = array($attributeID['LIGHT'],$entryData['lighting'],'');
    }

    /*  Field ID 72 = noise */
    if($entryData['noise']!=''){
      $attribute[] = array($attributeID['NOISE'],$entryData['noise'],'');
    }

    /*  Field ID 77 = internet */
    if($entryData['internet']!=''){
      $attribute[] = array($attributeID['INTRNT'],$entryData['internet'],'');
    }

    //add resources to the table
    foreach($resource as $value){
      $resource_id = $value[0];
      $qty         = $value[1];
      $comment     = htmlspecialchars($value[2]);

      //if the resource has already been added, update the qty
      $resourceCount = $wpdb->get_var("select count(*) from `wp_rmt_entry_resources` where entry_id = $entryID and resource_id = $resource_id");
      if($resourceCount >0){ //if result, update.
        $wpdb->get_results("update `wp_rmt_entry_resources set qty = $qty where  entry_id = $entryID and resource_id = $resource_id");
      }else{
        //else insert
        $wpdb->get_results("INSERT INTO `wp_rmt_entry_resources`(`entry_id`, `resource_id`, `qty`, `comment`) "
                        . " VALUES (".$entryID.",".$resource_id .",".$qty . ',"' . $comment.'")');
      }
    }

    //add attributes to the table
    foreach($attribute as $value){
      $attribute_id = $value[0];
      $attvalue     = htmlspecialchars($value[1]);
      $comment      = htmlspecialchars($value[2]);
      $wpdb->get_results("INSERT INTO `wp_rmt_entry_attributes`(`entry_id`, `attribute_id`, `value`,`comment`) "
                      . " VALUES (".$entryID.",".$attribute_id .',"'.$attvalue . '","' . $comment.'")');
    }

    //set resource status and assign to
    //assign values can be found in functions.php in custom_entry_meta function
    $assignTo    = 'na';//not assigned to anyone
    $status      = 'ready';//ready
    //field ID 83
    if( $entryData['fire'] == 'Yes' ||
        $entryData['activity']=='Yes' ||
        $entryData['activity_wrist'] == 'Yes'  ||
        $entryData['booth_size'] == "Other"
            ){
      $status   = 'review';
      $assignTo = 'jay'; //Jay
    }elseif($entryData['power'] == 'Yes' &&
            $entryData['amps']=='Other. Power request specified in the Special Power Requirements box'){
      $status   = 'review';
      $assignTo = 'kerry'; //Kerry
    }elseif($entryData['special_request']!=''){
      $status   = 'review';
      $assignTo = 'kerry'; //Kerry
    }

    // update custom meta field
    gform_update_meta( $entryData['CS_ID'], 'res_status',$status );
    gform_update_meta( $entryData['CS_ID'], 'res_assign',$assignTo );
  }
}