<?php
/* Class to update all stuff maker related */
if ( ! class_exists( 'GFRMTHELPER' ) ) {
	die();
}

class GFRMTHELPER {
  function __construct(){
    global $wpdb;
  }

	/*
   * This function is called when there is an entry update or new entry submission
   * $type - this tells us if this is a new submission or an update to the entry
	*/
	public static function gravityforms_makerInfo($entry,$form) {
		//format Entry information
    $entryData = self::gravityforms_format_record($entry,$form);

    //build/update RMT data
    self::buildRmtData($entryData);

    //update/insert into maker tables
    self::updateMakerTables($entry['id']);
	}

	/*
	 * Function for formatting gravity forms lead into usable jdb data
	*/
	public static function gravityforms_format_record($lead,$form) {
    $entry_id = $lead['id'];
		//load form
		$form_id  = $form['id'];
    $project_name = (isset($lead['109'])&&$lead['109']!='' ? $lead['109']:(isset($lead['151']) ? $lead['151']:''));

		// Load Names
    $isGroup =false;
    if(isset($lead['105'])){
      $isGroup =(strpos($lead['105'], 'group') !== false);
    }
    $isOneMaker =false;
    if(isset($lead['105'])&&$lead['105']!=''){
      $isOneMaker =(strpos($lead['105'], 'One') !== false);
    }

    $makerArray=array();
    if(!$isGroup){
      //if this isn't a group we need to have a valid email for the presenter(maker 1) record.  if not set, use contact email
      //need a valid first name
      $email = (isset($lead['161'])&&$lead['161']!='' ? $lead['161']:$entry_id.'-presenter@makermedia.com');
      $presenterArr=array(
          'first_name'  => (isset($lead['160.3']) ? $lead['160.3']:''),
          'last_name'   => (isset($lead['160.6']) ? $lead['160.6']:''),
          'bio'         => (isset($lead['234'])   ? $lead['234']:''),
          'email'       => $email,
          'phone'       => (isset($lead['185'])   ? $lead['185']:''),
          'twitter'     => (isset($lead['201'])   ? $lead['201']:''),
          'photo'       => (isset($lead['217'])   ? $lead['217']:''),
          'website'     => (isset($lead['209'])   ? $lead['209']:''),
      );
    }else{
      $presenterArr = array(
          'first_name'  => $project_name,
          'last_name'   => '',
          'bio'         => (isset($lead['110'])   ? $lead['110']:''),
          'email'       => $entry_id.'-group@makermedia.com',
          'phone'       => (isset($lead['99'])    ? $lead['99']:''),
          'twitter'     => (isset($lead['322'])   ? $lead['322']:''),
          'photo'       => (isset($lead['111'])   ? $lead['111']:''),
          'website'     => (isset($lead['112'])   ? $lead['112']:''),
      );
    }
    //build Maker Array
    $makerArray['contact'] =
        array(
          'first_name'  => (isset($lead['96.3'])  ? $lead['96.3']:''),
          'last_name'   => (isset($lead['96.6'])  ? $lead['96.6']:''),
          'bio'         => '',
          'email'       => (isset($lead['98'])    ? $lead['98']:''),
          'phone'       => (isset($lead['99'])    ? $lead['99']:''),
          'twitter'     => (isset($lead['201'])   ? $lead['201']:''),
          'photo'       => '',
          'website'     => ''
      );
    $makerArray['presenter'] = $presenterArr;

    //only set the below data if the entry is not marked as one maker
    if(!$isOneMaker){
      $makerArray['presenter2']= array(
          'first_name'  => (isset($lead['158.3']) ? $lead['158.3']:''),
          'last_name'   => (isset($lead['158.6']) ? $lead['158.6']:''),
          'bio'         => (isset($lead['258'])   ? $lead['258']:''),
          'email'       => (isset($lead['162'])   ? $lead['162']:''),
          'phone'       => (isset($lead['192'])   ? $lead['192']:''),
          'twitter'     => (isset($lead['208'])   ? $lead['208']:''),
          'photo'       => (isset($lead['224'])   ? $lead['224']:''),
          'website'     => (isset($lead['216'])   ? $lead['216']:''),
      );
      $makerArray['presenter3'] = array(
          'first_name'  => (isset($lead['155.3']) ? $lead['155.3']:''),
          'last_name'   => (isset($lead['155.6']) ? $lead['155.6']:''),
          'bio'         => (isset($lead['259'])   ? $lead['259']:''),
          'email'       => (isset($lead['167'])   ? $lead['167']:''),
          'phone'       => (isset($lead['190'])   ? $lead['190']:''),
          'twitter'     => (isset($lead['207'])   ? $lead['207']:''),
          'photo'       => (isset($lead['223'])   ? $lead['223']:''),
          'website'     => (isset($lead['215'])   ? $lead['215']:''),
      );
      $makerArray['presenter4'] = array(
          'first_name'  => (isset($lead['156.3']) ? $lead['156.3']:''),
          'last_name'   => (isset($lead['156.6']) ? $lead['156.6']:''),
          'bio'         => (isset($lead['260'])   ? $lead['260']:''),
          'email'       => (isset($lead['166'])   ? $lead['166']:''),
          'phone'       => (isset($lead['191'])   ? $lead['191']:''),
          'twitter'     => (isset($lead['206'])   ? $lead['206']:''),
          'photo'       => (isset($lead['222'])   ? $lead['222']:''),
          'website'     => (isset($lead['214'])   ? $lead['214']:''),
      );
      $makerArray['presenter5'] = array(
          'first_name'  => (isset($lead['157.3']) ? $lead['157.3']:''),
          'last_name'   => (isset($lead['157.6']) ? $lead['157.6']:''),
          'bio'         => (isset($lead['261'])   ? $lead['261']:''),
          'email'       => (isset($lead['165'])   ? $lead['165']:''),
          'phone'       => (isset($lead['189'])   ? $lead['189']:''),
          'twitter'     => (isset($lead['205'])   ? $lead['205']:''),
          'photo'       => (isset($lead['220'])   ? $lead['220']:''),
          'website'     => (isset($lead['213'])   ? $lead['213']:''),
      );
      $makerArray['presenter6'] = array(
          'first_name'  => (isset($lead['159.3']) ? $lead['159.3']:''),
          'last_name'   => (isset($lead['159.6']) ? $lead['159.6']:''),
          'bio'         => (isset($lead['262'])   ? $lead['262']:''),
          'email'       => (isset($lead['164'])   ? $lead['164']:''),
          'phone'       => (isset($lead['188'])   ? $lead['188']:''),
          'twitter'     => (isset($lead['204'])   ? $lead['204']:''),
          'photo'       => (isset($lead['221'])   ? $lead['221']:''),
          'website'     => (isset($lead['211'])   ? $lead['211']:''),
      );
      $makerArray['presenter7'] = array(
          'first_name'  => (isset($lead['154.3']) ? $lead['154.3']:''),
          'last_name'   => (isset($lead['154.6']) ? $lead['154.6']:''),
          'bio'         => (isset($lead['263'])   ? $lead['263']:''),
          'email'       => (isset($lead['163'])   ? $lead['163']:''),
          'phone'       => (isset($lead['187'])   ? $lead['187']:''),
          'twitter'     => (isset($lead['203'])   ? $lead['203']:''),
          'photo'       => (isset($lead['219'])   ? $lead['219']:''),
          'website'     => (isset($lead['212'])   ? $lead['212']:''),
      );
    }

    // Load Categories (old topics field - no longer used in current forms)
		$fieldtopics=RGFormsModel::get_field($form,'147');
    if(!is_array($fieldtopics['inputs'] ))  $fieldtopics['inputs'] =array();
		$topicsarray = array();

		foreach($fieldtopics['inputs'] as $topic) {
			if (strlen($lead[$topic['id']]) > 0)  $topicsarray[] = $lead[$topic['id']];
		}

    $leadCategory = array();
    $MAD          = 0;
    //Categories (current fields in use)
    foreach($lead as $leadKey=>$leadValue){
      //4 additional categories
      $pos = strpos($leadKey, '321');
      if ($pos !== false) {
        $leadCategory[]=$leadValue;
      }
      //main catgory
      $pos = strpos($leadKey, '320');
      if ($pos !== false) {
        $leadCategory[]=$leadValue;
      }
      //check the flag field 304
      $pos = strpos($leadKey, '304');
      if ($pos !== false) {
        if($leadValue=='Mobile App Discover')  $MAD = 1;
      }
    }

    //verify we only have unique categories
    $leadCategory = array_unique($leadCategory);
    $catList = implode(',', $leadCategory);

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

		$entry_data = array(
				'form_id'               => $form_id, //(Form ID)
				'noise'                 => isset($lead['72'])   ? $lead['72']  : '',
				'radio'                 => isset($lead['78'])   ? $lead['78']  : '',
				'hands_on'              => isset($lead['66'])   ? $lead['66']  : '',
				'referrals'             => isset($lead['127'])  ? $lead['127'] : '',
				'food_details'          => isset($lead['144'])  ? $lead['144'] : '',
				'fire'                  => isset($lead['83'])   ? $lead['83']  : '',
				'booth_size_details'    => isset($lead['61'])   ? $lead['61']  : '',
				'layout'                => isset($lead['65'])   ? $lead['65']  : '',
				'amps_details'          => isset($lead['76'])   ? $lead['76']  : '',
				'booth_size'            => isset($lead['60'])   ? $lead['60']  : '',
				'hear_about'            => isset($lead['128'])  ? $lead['128'] : '',
        'maker_array'           => $makerArray,
				'maker_faire'           => isset($lead['131'])  ? $lead['131'] : '',
				'project_website'       => isset($lead['27'])   ? $lead['27']  : '',
				'supporting_documents'  => isset($lead['122'])  ? $lead['122'] : '',
				'tables_chairs'         => isset($lead['62'])   ? $lead['62']  : '',
				'project_video'         => isset($lead['32'])   ? $lead['32']  : '',
				'cats'                  => isset($topicsarray)  ? $topicsarray : '',
				'loctype'               => isset($lead['69'])   ? $lead['69']  : '',
				'tables_chairs_details' => isset($lead['288'])  ? $lead['288'] : '',
				'internet'              => isset($lead['77'])   ? $lead['77']  : '',
				'project_photo'         => isset($lead['22'])   ? $lead['22']  : '',
				'project_name'          => isset($lead['151'])  ? $lead['151'] : '',
				'first_time'            => isset($lead['130'])  ? $lead['130'] : '',
				'power'                 => isset($lead['73'])   ? $lead['73']  : '',
				'food'                  => isset($lead['44'])   ? $lead['44']  : '',
				'safety_details'        => isset($lead['85'])   ? $lead['85']  : '',
				'anything_else'         => isset($lead['134'])  ? $lead['134'] : '',
				'phone1_type'           => isset($lead['148'])  ? $lead['148'] : '',
				'lighting'              => isset($lead['71'])   ? $lead['71']  : '',
				'project_photo_thumb'   => '',
				'private_address'       => isset($lead['101.1'])  ? $lead['101.1'] : '',
				'private_state'         => isset($lead['101.4'])  ? $lead['101.4'] : '',
				'private_city'          => isset($lead['101.3'])  ? $lead['101.3'] : '',
				'private_address2'      => isset($lead['101.2'])  ? $lead['101.2'] : '',
				'private_country'       => isset($lead['101.6'])  ? $lead['101.6'] : '',
				'private_zip'           => isset($lead['101.5'])  ? $lead['101.5'] : '',
				'placement'             => isset($lead['68'])     ? $lead['68']    : '',
				'phone2_type'           => isset($lead['149'])    ? $lead['149']   : '',
				'radio_frequency'       => $rfarray,
				'what_are_you_powering' => isset($lead['74'])     ? $lead['74']   : '',
				'private_description'   => isset($lead['11'])     ? $lead['11']   : '',
				'org_type'              => isset($lead['45'])     ? $lead['45']   : '',
				'public_description'    => isset($lead['16'])     ? $lead['16']   : '',
				'activity'              => isset($lead['84'])     ? $lead['84']   : '',
				'amps'                  => isset($lead['75'])     ? $lead['75']   : '',
				'sales_details'         => isset($lead['52'])     ? $lead['52']   : '',
				'phone2'                => isset($lead['100'])    ? $lead['100']  : '',
				'maker'                 => isset($lead['105'])    ? $lead['105']  : '',
				'non_profit_desc'       => isset($lead['47'])     ? $lead['47']   : '',
				'plans'                 => isset($plansarray)     ? $plansarray   : '',
				'launch_details'        => isset($lead['54'])     ? $lead['54']   : '',
				'crowdfunding'          => isset($lead['56'])     ? $lead['56']   : '',
				'crowdfunding_details'  => isset($lead['59'])     ? $lead['59']   : '',
				'special_request'       => isset($lead['64'])     ? $lead['64']   : '',
				'hands_on_desc'         => isset($lead['67'])     ? $lead['67']   : '',
				'activity_wrist'        => isset($lead['293'])    ? $lead['293']  : '',
				'loctype_outdoors'      => $locationsarray,
				'makerfaire_other'      => isset($lead['132'])    ? $lead['132']  : '',
				'under_18'              => (isset($lead['295']) && $lead['295'] == "Yes") ? 'NO'  : 'YES',
				'entry_id'              => $entry_id,
				'status'                => isset($lead['303'])    ? $lead['303']  : '',
				'waste'                 => (isset($lead['317']) && $lead['317'] == "Yes") ?  'YES' : 'NO',
				'waste_detail'          => isset($lead['318'])    ? $lead['318']  : '',
				'learn_to'              => isset($lead['319'])    ? $lead['319']  : '',
        'numTables'             => isset($lead['347'])    ? $lead['347']  : 0,
        'numChairs'             => isset($lead['348'])    ? $lead['348']  : 0,
        '344'                   => isset($lead['344'])    ? $lead['344']  : 0,
        '345'                   => isset($lead['345'])    ? $lead['345']  : 0,
        '81'                    => isset($lead['81'])     ? $lead['81']   : '',
        'fType'                 => isset($form['form_type'])  ? $form['form_type'] : '',
        'paymentElectr'         => isset($lead['8'])      ? $lead['8']    : '',
        'paymentDescElect'      => isset($lead['12'])     ? $lead['12']   : '',
        'paymentTable'          => isset($lead['14'])     ? $lead['14']   : '',
        'origEntryID'           => isset($lead['20'])     ? $lead['20']   : '',
        'presentation_type'     => isset($lead['1'])      ? $lead['1']    : '',
        'onsitePhone'           => isset($lead['265'])    ? $lead['265']  : '',
        'categories'            => $leadCategory,
        'mobileAppDiscover'     => $MAD
		);

		return $entry_data;
	}

  public static function buildRmtData($entryData){
    global $wpdb;
    $resourceID  = array();
    $attributeID = array();
    $attribute   = array();
    $resource    = array();
    $entryID     = $entryData['entry_id'];
    $user        = NULL;

    /*
     *  E N T R Y   R E S O U R C E S   M A P P I N G
     *   build list of resource ID's and tokens
     */
    $sql = "select resource_category_id, ID,token from wp_rmt_resources";
    foreach($wpdb->get_results($sql) as $row){
      $resourceID[$row->token] = array('id'=>$row->ID,'cat_id'=>$row->resource_category_id);
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
      $resource[] = array($resourceID['TBL_6x30'],1,'');
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
      if($entryData['amps'] == '5 amps (0-500 watts, 120V)'){
        $resource[] = array($resourceID['120V-05A'],1,'');
      }elseif($entryData['amps'] == '10 amps (501-1000 watts, 120V)'){
        $resource[] = array($resourceID['120V-10A'],1,'');
      }elseif($entryData['amps'] == '15 amps (1001-1500 watts, 120V)'){
        $resource[] = array($resourceID['120V-15A'],1,'');
      }elseif($entryData['amps'] == '20 amps (1501-2000 watts, 120V)'){
        $resource[] = array($resourceID['120V-20A'],1,'');
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
      //get original entry id
      $entryID = ($entryData['origEntryID'] != '' ? $entryData['origEntryID']:$entryID);
      //check if any electrical resources have been set
      $sql = "SELECT wp_rmt_entry_resources.ID "
              . " from wp_rmt_entry_resources, wp_rmt_resources, wp_rmt_resource_categories "
              . " where resource_id=wp_rmt_resources.ID and "
              . "       resource_category_id=wp_rmt_resource_categories.ID and "
              . "       entry_id = $entryID and "
              . "       wp_rmt_resource_categories.category like '%electrical%'";
      //if an electrical resource has been set, delete it
      $resourceElec = $wpdb->get_var($sql);

      if($resourceElec != NULL){ //if result, update.
        //delete any electrical resources MF-901
        $wpdb->delete( 'wp_rmt_entry_resources', array( 'ID' => $resourceElec ) );
      }

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
    }

    /*
     * E N T R Y   A T T R I B U T E   M A P P I N G
     *    build list of resource ID's and tokens
     */
    /* Field ID 74 - what_are_you_powering   */
    /* Field ID 76 = amps_details (textarea) */
    if($entryData['what_are_you_powering']!='' || $entryData['amps_details']!=''){
      $details = 'What are you Powering? - ' . $entryData['what_are_you_powering'] .'<br/>'.
                 'Amps Detail - '.$entryData['amps_details'];
      $attribute[] = array($attributeID['ELEC'], 'What are you Powering? / Amps Detail',$details);
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
      if($entryData['booth_size']!=''){
        $attribute[] = array($attributeID['SPACESIZE'],$entryData['booth_size'],$entryData['booth_size_details']);
      }
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

    global $current_user;
    $user = (isset($current_user->ID) ? $current_user->ID:NULL);

    //if this is a payment form overwrite the user
    if($entryData['fType'] == 'Payment'){
      $user = 0;  //user = 0 - payment form
    }
    //add resources to the table
    foreach($resource as $value){
      $resource_id = $value[0]['id'];
      $cat_id      = $value[0]['cat_id'];
      $qty         = $value[1];
      $comment     = htmlspecialchars($value[2]);
      //find if they already have a resource set with the same Item (ie. chairs, tables, electricity, etc)
      $res = $wpdb->get_row('SELECT entry_res.*, res.resource_category_id '
              . ' FROM `wp_rmt_entry_resources` entry_res,wp_rmt_resources res '
              . ' where entry_id='.$entryID.' and entry_res.resource_id = res.ID and resource_category_id='.$cat_id);
      //matching record found
      if ( null !== $res ) {
        //check lockbit
        if($res->lockBit==0){
          //If there are changes, update this record
          if($res->resource_id!=$resource_id || $res->qty!=$qty){
            $wpdb->get_results('update `wp_rmt_entry_resources` '
                  . ' set `resource_id` = '.$resource_id.', `qty` = '.$qty.',user='.$user.',update_stamp=now() where id='.$res->ID);
          }
        }
      }else{
        //on new records the user is always null unless this is a payment form
        if($entryData['fType'] != 'Payment'){
          $user = 'NULL';
        }
        //insert this record
        $wpdb->get_results("INSERT INTO `wp_rmt_entry_resources` "
                . " (`entry_id`, `resource_id`, `qty`, `comment`, user) "
                . " VALUES (".$entryID.",".$resource_id .",".$qty . ',"' . $comment.'",'.$user.')');
      }
    }

    //add attributes to the table
    foreach($attribute as $value){
      $attribute_id = $value[0];
      $attvalue     = htmlspecialchars($value[1]);
      $comment      = htmlspecialchars($value[2]);

      //check if attribute is locked
       $res = $wpdb->get_row("select * from `wp_rmt_entry_attributes` "
                . ' where entry_id = '.$entryID.' and attribute_id = '.$attribute_id);

       //matching record found
      if ( null !== $res ) {
        if($res->lockBit==0){  //If this attribute is not locked, update this record
          //if this is a payment record, append the payment comment to the end of the existing comment
          if($entryData['fType'] == 'Payment'){
            $comment = $res->comment.'<br/>'.$entryData['fType'] . ' Form Comment - ' . $comment;
          }
          //if there are changes, update the record
          if($res->comment!=$comment || $res->value!=$attvalue){
            $wpdb->get_results('update `wp_rmt_entry_attributes` '
                  . ' set comment="'.$comment.'", user='.$user.', value="'.$attvalue .'",	update_stamp=now()'
                  . ' where id='.$res->ID);
          }
        }
      }else{
        $wpdb->get_results("INSERT INTO `wp_rmt_entry_attributes`(`entry_id`, `attribute_id`, `value`,`comment`,user) "
                      . " VALUES (".$entryID.",".$attribute_id .',"'.$attvalue . '","' . $comment.'",'.$user.')');
      }
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

    // update custom meta field (do not update if meta already exists)
    $metaValue = gform_get_meta( $entryData['entry_id'], 'res_status' );
    if(empty($metaValue)){
      gform_update_meta( $entryData['entry_id'], 'res_status',$status );
    }

    $metaValue = gform_get_meta( $entryData['entry_id'], 'res_assign' );
    if(empty($metaValue)){
      gform_update_meta( $entryData['entry_id'], 'res_assign',$assignTo );
    }
  }

  /*
   * This table will add/update records to the following tables:
   *    wp_mf_entity
   *    wp_mf_maker
   *    wp_mf_maker_to_entity
   *
   * NOT USED - PLEASE REFER TO updateMakerTables FUNCTION INSSTEAD
   */
  public static function updateMakerTable($entryData){
    global $wpdb;
    $form_id = $entryData['form_id'];
    if($entryData['fType']=='Presentation'){
      $entryData['project_photo'] = $entryData['maker_array']['presenter']['photo'];
    }
    //determine faire
    $faire = $wpdb->get_var('select faire from wp_mf_faire where FIND_IN_SET ('.$form_id.', wp_mf_faire.form_ids)> 0');

    //wp_mf_entity
    $wp_mf_entitysql = "insert into wp_mf_entity "
                    . "    (lead_id, presentation_title, presentation_type, special_request, "
                    . "     OnsitePhone, desc_short, desc_long, project_photo, status,category,faire,mobile_app_discover) "
                    . " VALUES ('".$entryData['entry_id']             . "',"
                            . ' "'.htmlentities($entryData['project_name'])         . '", '
                            . ' "'.htmlentities($entryData['presentation_type'])    . '", '
                            . ' "'.htmlentities($entryData['special_request'])      . '", '
                            . ' "'.$entryData['onsitePhone']          . '", '
                            . ' "'.htmlentities($entryData['public_description'])   . '", '
                            . ' "'.htmlentities($entryData['private_description'])  . '", '
                            . ' "'.htmlentities($entryData['project_photo'])        . '", '
                            . ' "'.$entryData['status']               . '", '
                            . ' "'.implode(',',$entryData['categories']) . '", '
                            . ' "'.$faire                             . '", '
                            . '  '.$entryData['mobileAppDiscover']    . ') '
                    . ' ON DUPLICATE KEY UPDATE presentation_title  = "'.htmlentities($entryData['project_name'])           . '", '
                    . '                         presentation_type   = "'.htmlentities($entryData['presentation_type'])      . '", '
                    . '                         special_request     = "'.htmlentities($entryData['special_request'])        . '", '
                    . '                         OnsitePhone         = "'.htmlentities($entryData['onsitePhone'])            . '", '
                    . '                         desc_short          = "'.htmlentities($entryData['public_description'])     . '", '
                    . '                         desc_long           = "'.htmlentities($entryData['public_description'])     . '", '
                    . '                         project_photo       = "'.$entryData['project_photo']          . '", '
                    . '                         status              = "'.$entryData['status']                 . '", '
                    . '                         category            = "'.implode(',',$entryData['categories']). '", '
                    . '                         faire               = "'.$faire                               . '", '
                    . '                         mobile_app_discover = "'.$entryData['mobileAppDiscover']      . '"';
    $wpdb->get_results($wp_mf_entitysql);

    /*  wp_mf_maker table
     *
     *  maker array structure -
     *    types - contact, presenter, presenter2, presenter3, presenter4, presenter5, presenter6, presenter7
     *    for each type the following fields are set -
              'first_name'
              'last_name'
              'bio'
              'email'
              'phone'
              'twitter'
              'photo'
              'website'
     */
    foreach($entryData['maker_array'] as $type =>$typeArray){
      $firstName  =  (isset($typeArray['first_name']) ? esc_sql($typeArray['first_name']) : '');
      $lastName   =  (isset($typeArray['last_name'])  ? esc_sql($typeArray['last_name'])  : '');

      //we need to have at least 1 presenter/maker.  if these fields are empty, pull from the contact info
      if(trim($firstName)=='' && trim($lastName)==''){
        if($type=='presenter'){
          $typeArray = $entryData['maker_array']['contact'];
          //let's try to get the name again
          $firstName  =  (isset($typeArray['first_name']) ? esc_sql($typeArray['first_name']) : '');
          $lastName   =  (isset($typeArray['last_name'])  ? esc_sql($typeArray['last_name'])  : '');
        }
      }


      if($entryData['fType']=='Sponsor' and $type!='contact'){
        //set name to company name
        $firstName = htmlentities($entryData['project_name']);
        $lastName  = ' ';
      }

      $email    = (isset($typeArray['email'])   ? esc_sql($typeArray['email'])    : '');

      //if email is blank we need to create a dummy email for them
      if($email ==''){
        $email = $entryData['entry_id'] .'-'.$type.'@makermedia.com';
      }
      if((trim($firstName)=='' && trim($lastName)=='')|| trim($email)==''){
        //don't write the record, no maker here
      }else{
        $bio      = (isset($typeArray['bio'])     ? htmlentities($typeArray['bio'])      : '');
        $phone    = (isset($typeArray['phone'])   ? esc_sql($typeArray['phone'])    : '');
        $twitter  = (isset($typeArray['twitter']) ? esc_sql($typeArray['twitter'])  : '');
        $photo    = (isset($typeArray['photo'])   ? esc_sql($typeArray['photo'])    : '');
        $website  = (isset($typeArray['website']) ? esc_sql($typeArray['website'])  : '');

        $results = $wpdb->get_results($wpdb->prepare("SELECT maker_id FROM wp_mf_maker WHERE email=%s", $email) );
        if ($wpdb->num_rows != 0){
              $guid = $results[0]->maker_id;
        }else{
          $guid = createGUID($entryData['entry_id'] .'-'.$type);
        }

        $wp_mf_makersql = "INSERT INTO wp_mf_maker "
                        . " (`First Name`, `Last Name`, `Bio`, `Email`, `phone`, `TWITTER`,  `maker_id`, `Photo`, `website`) "
                        . ' VALUES ("'.$firstName.'","'.$lastName.'","'.$bio.'","'.$email.'", "'.$phone.'", '
                                     . '"'.$twitter.'", "'.$guid.'","'.$photo.'","'.$website.'")'
                        . ' ON DUPLICATE KEY UPDATE lead_id='.$entryData['entry_id'].',form_id  = '.$form_id;

        //only update non blank fields
        $wp_mf_makersql .= ($firstName!=''? ',  `First Name` = "'.$firstName .'"':'');
        $wp_mf_makersql .= ($lastName!='' ? ',  `Last Name`  = "'.$lastName  .'"':'');
        $wp_mf_makersql .= ($bio!=''      ? ',  `Bio`         = "'.$bio       .'"':'');
        $wp_mf_makersql .= ($phone!=''    ? ',  `phone`       = "'.$phone     .'"':'');
        $wp_mf_makersql .= ($twitter!=''  ? ',  `TWITTER`     = "'.$twitter   .'"':'');
        $wp_mf_makersql .= ($photo!=''    ? ',  `Photo`       = "'.$photo     .'"':'');
        $wp_mf_makersql .= ($website!=''  ? ',  `website`     = "'.$website   .'"':'');

        $wpdb->get_results($wp_mf_makersql);

        //build maker to entity table
        //(key is on maker_id, entity_id and maker_type.  if record already exists, no update is needed)
        $wp_mf_maker_to_entity = "INSERT INTO `wp_mf_maker_to_entity`" . " (`maker_id`, `entity_id`, `maker_type`) "
                              . ' VALUES ("'.$guid.'",'.$entryData['entry_id'].',"'.$type.'") ON DUPLICATE KEY UPDATE maker_id="'.$guid.'";';

        $wpdb->get_results($wp_mf_maker_to_entity);
      }
    }
  }


  /*
   * This table will add/update records to the following tables:
   *    wp_mf_entity, wp_mf_maker, wp_mf_maker_to_entity
   */
   public static function updateMakerTables($entryID){
    global $wpdb;
    $entry    = GFAPI::get_entry($entryID);
    $form_id  = $entry['form_id'];
    $form     = GFAPI::get_form($form_id);

    //build Maker Data Array
    $data = self::buildMakerData($entry,$form);
    $makerData  = $data['maker'];
    $entityData = $data['entity'];

    $categories = is_array($entityData['categories'] ? implode(',',$entityData['categories']) :'');
    /*
     * Update Entity Table - wp_mf_entity
     */
    $wp_mf_entitysql = "insert into wp_mf_entity (lead_id, presentation_title, presentation_type, special_request, "
                    . "     OnsitePhone, desc_short, desc_long, project_photo, status,category,faire,mobile_app_discover,form_id) "
                    . " VALUES ('" . $entryID             . "',"
                            . ' "' . $entityData['project_name']            . '", '
                            . ' "' . $entityData['presentation_type']       . '", '
                            . ' "' . $entityData['special_request']         . '", '
                            . ' "' . $entityData['onsitePhone']             . '", '
                            . ' "' . $entityData['public_description']      . '", '
                            . ' "' . $entityData['private_description']     . '", '
                            . ' "' . $entityData['project_photo']           . '", '
                            . ' "' . $entityData['status']                  . '", '
                            . ' "' . $categories . '", '
                            . ' "' . $entityData['faire']                   . '", '
                            . '  ' . $entityData['mobile_app_discover']     . ','
                            . '  ' . $entityData['form_id'].') '
                    . ' ON DUPLICATE KEY UPDATE presentation_title  = "'.$entityData['project_name']            . '", '
                    . '                         presentation_type   = "'.$entityData['presentation_type']       . '", '
                    . '                         special_request     = "'.$entityData['special_request']         . '", '
                    . '                         OnsitePhone         = "'.$entityData['onsitePhone']             . '", '
                    . '                         desc_short          = "'.$entityData['public_description']      . '", '
                    . '                         desc_long           = "'.$entityData['private_description']     . '", '
                    . '                         project_photo       = "'.$entityData['project_photo']           . '", '
                    . '                         status              = "'.$entityData['status']                  . '", '
                    . '                         category            = "'.$categories. '", '
                    . '                         faire               = "'.$entityData['faire']                   . '", '
                    . '                         form_id             =  '.$entityData['form_id']                 . ','
                    . '                         mobile_app_discover = "'.$entityData['mobile_app_discover']     . '"';
    $wpdb->get_results($wp_mf_entitysql);

    /*  Update Maker Table - wp_mf_maker table
     *    $makerData types - contact, presenter, presenter2-7
     */

    //loop thru
    foreach($makerData as $type => $typeArray){
      $firstName = (isset($typeArray['first_name']) ? esc_sql($typeArray['first_name']) : '');
      $lastName  = (isset($typeArray['last_name'])  ? esc_sql($typeArray['last_name'])  : '');
      $email     = (isset($typeArray['email'])      ? esc_sql($typeArray['email'])      : '');

      if((trim($firstName) == '' && trim($lastName) == '') || trim($email) == '') {
        //don't write the record, no maker here.  Move along
      }else{
        $bio      = (isset($typeArray['bio'])     ? htmlentities($typeArray['bio']) : '');
        $phone    = (isset($typeArray['phone'])   ? esc_sql($typeArray['phone'])    : '');
        $twitter  = (isset($typeArray['twitter']) ? esc_sql($typeArray['twitter'])  : '');
        $photo    = (isset($typeArray['photo'])   ? esc_sql($typeArray['photo'])    : '');
        $website  = (isset($typeArray['website']) ? esc_sql($typeArray['website'])  : '');

        /*  GUID
         * If this maker is already in the DB - pull the maker_id, else let's create one
         */
        $results = $wpdb->get_results($wpdb->prepare("SELECT maker_id FROM wp_mf_maker WHERE email=%s", $email) );
        $guid = ($wpdb->num_rows != 0?$guid = $results[0]->maker_id: createGUID($entryID .'-'.$type));

        $wp_mf_makersql = "INSERT INTO wp_mf_maker "
                        . " (`First Name`, `Last Name`, `Bio`, `Email`, `phone`, `TWITTER`,  `maker_id`, `Photo`, `website`) "
                        . ' VALUES ("'.$firstName.'","'.$lastName.'","'.$bio.'","'.$email.'", "'.$phone.'", '
                                 . '"'.$twitter.'", "'.$guid.'", "'.$photo.'", "'.$website.'")'
                        . ' ON DUPLICATE KEY UPDATE maker_id="'.$guid.'"';

        //only update non blank fields
        $wp_mf_makersql .= ($firstName != '' ? ', `First Name` = "' . $firstName . '"' : '');
        $wp_mf_makersql .= ($lastName  != '' ? ', `Last Name`  = "' . $lastName  . '"' : '');
        $wp_mf_makersql .= ($bio       != '' ? ', `Bio`        = "' . $bio       . '"' : '');
        $wp_mf_makersql .= ($phone     != '' ? ', `phone`      = "' . $phone     . '"' : '');
        $wp_mf_makersql .= ($twitter   != '' ? ', `TWITTER`    = "' . $twitter   . '"' : '');
        $wp_mf_makersql .= ($photo     != '' ? ', `Photo`      = "' . $photo     . '"' : '');
        $wp_mf_makersql .= ($website   != '' ? ', `website`    = "' . $website   . '"' : '');

        $wpdb->get_results($wp_mf_makersql);

        //build maker to entity table
        //(key is on maker_id, entity_id and maker_type.  if record already exists, no update is needed)
        $wp_mf_maker_to_entity = "INSERT INTO `wp_mf_maker_to_entity`"
                              . " (`maker_id`, `entity_id`, `maker_type`) "
                              . ' VALUES ("'.$guid.'",'.$entryID.',"'.$type.'") '
                              . ' ON DUPLICATE KEY UPDATE maker_id="'.$guid.'";';

        $wpdb->get_results($wp_mf_maker_to_entity);
      }
    }
  }

  //function to build the maker data table to update the wp_mf_maker table
  public static function buildMakerData($lead,$form){
    global $wpdb;
    $form_type = (isset($form['form_type'])  ? $form['form_type'] : '');
    $entry_id     = $lead['id'];
		$form_id      = $form['id'];
    $project_name = (isset($lead['109'])&&$lead['109']!='' ? $lead['109']:(isset($lead['151']) ? $lead['151']:''));

    // Load Names
    $isGroup =false;
    if(isset($lead['105'])){
      $isGroup =(strpos($lead['105'], 'group') !== false?true:false);
    }

    $isOneMaker =false;
    if(isset($lead['105'])&&$lead['105']!=''){
      $isOneMaker =(strpos($lead['105'], 'One') !== false?true:false);
    }

    /*
     * Build Maker Array
     */
    $makerArray = array();

    //Contact
    $makerArray['contact'] =
        array(
          'first_name'  => (isset($lead['96.3'])  ? $lead['96.3']:''),
          'last_name'   => (isset($lead['96.6'])  ? $lead['96.6']:''),
          'bio'         => '',
          'email'       => (isset($lead['98'])    ? $lead['98']:''),
          'phone'       => (isset($lead['99'])    ? $lead['99']:''),
          'twitter'     => (isset($lead['201'])   ? $lead['201']:''),
          'photo'       => '',
          'website'     => ''
      );

    // Presenter / Maker 1
    if(!$isGroup){
      //if this isn't a group we need to have a valid email for the presenter(maker 1) record.
      // if not set, use contact email
      $email = (isset($lead['161'])&&$lead['161']!='' ? $lead['161']:$entry_id.'-presenter@makermedia.com');
      $makerArray['presenter'] = array(
          'first_name'  => (isset($lead['160.3']) ? $lead['160.3']:''),
          'last_name'   => (isset($lead['160.6']) ? $lead['160.6']:''),
          'bio'         => (isset($lead['234'])   ? $lead['234']:''),
          'email'       => $email,
          'phone'       => (isset($lead['185'])   ? $lead['185']:''),
          'twitter'     => (isset($lead['201'])   ? $lead['201']:''),
          'photo'       => (isset($lead['217'])   ? $lead['217']:''),
          'website'     => (isset($lead['209'])   ? $lead['209']:''),
      );
    }else{
      // if field 105 indicates this is a group,
      //  set Presenter/Maker 1 to the group information
      $makerArray['presenter'] = array(
          'first_name'  => $project_name,
          'last_name'   => '',
          'bio'         => (isset($lead['110'])   ? $lead['110']:''),
          'email'       => $entry_id.'-group@makermedia.com',
          'phone'       => (isset($lead['99'])    ? $lead['99']:''),
          'twitter'     => (isset($lead['322'])   ? $lead['322']:''),
          'photo'       => (isset($lead['111'])   ? $lead['111']:''),
          'website'     => (isset($lead['112'])   ? $lead['112']:''),
      );
    }

    // we need to have at least 1 presenter/maker.  if these fields are empty, pull from the contact info
    if(trim($makerArray['presenter']['first_name'])=='' && trim($makerArray['presenter']['last_name'])==''){
      //let's try to get the name from the contact info
      $firstName  =  (isset($entryData['maker_array']['contact']['first_name']) ? esc_sql($entryData['maker_array']['contact']['first_name']) : '');
      $lastName   =  (isset($entryData['maker_array']['contact']['last_name'])  ? esc_sql($entryData['maker_array']['contact']['last_name'])  : '');
    }

    // If sponsor, Set Presenter/Maker 1 name to company name
    if($form['form_type']=='Sponsor'){
        $entryData['maker_array']['presenter']['first_name'] = htmlentities($project_name);
        $entryData['maker_array']['presenter']['last_name']  = ' ';
      }

    // only set the below data if the entry is not marked as one maker
    if(!$isOneMaker){
      $makerArray['presenter2']= array(
          'first_name'  => (isset($lead['158.3']) ? $lead['158.3']:''),
          'last_name'   => (isset($lead['158.6']) ? $lead['158.6']:''),
          'bio'         => (isset($lead['258'])   ? $lead['258']:''),
          'email'       => (isset($lead['162'])   ? $lead['162']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['192'])   ? $lead['192']:''),
          'twitter'     => (isset($lead['208'])   ? $lead['208']:''),
          'photo'       => (isset($lead['224'])   ? $lead['224']:''),
          'website'     => (isset($lead['216'])   ? $lead['216']:''),
      );
      $makerArray['presenter3'] = array(
          'first_name'  => (isset($lead['155.3']) ? $lead['155.3']:''),
          'last_name'   => (isset($lead['155.6']) ? $lead['155.6']:''),
          'bio'         => (isset($lead['259'])   ? $lead['259']:''),
          'email'       => (isset($lead['167'])   ? $lead['167']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['190'])   ? $lead['190']:''),
          'twitter'     => (isset($lead['207'])   ? $lead['207']:''),
          'photo'       => (isset($lead['223'])   ? $lead['223']:''),
          'website'     => (isset($lead['215'])   ? $lead['215']:''),
      );
      $makerArray['presenter4'] = array(
          'first_name'  => (isset($lead['156.3']) ? $lead['156.3']:''),
          'last_name'   => (isset($lead['156.6']) ? $lead['156.6']:''),
          'bio'         => (isset($lead['260'])   ? $lead['260']:''),
          'email'       => (isset($lead['166'])   ? $lead['166']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['191'])   ? $lead['191']:''),
          'twitter'     => (isset($lead['206'])   ? $lead['206']:''),
          'photo'       => (isset($lead['222'])   ? $lead['222']:''),
          'website'     => (isset($lead['214'])   ? $lead['214']:''),
      );
      $makerArray['presenter5'] = array(
          'first_name'  => (isset($lead['157.3']) ? $lead['157.3']:''),
          'last_name'   => (isset($lead['157.6']) ? $lead['157.6']:''),
          'bio'         => (isset($lead['261'])   ? $lead['261']:''),
          'email'       => (isset($lead['165'])   ? $lead['165']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['189'])   ? $lead['189']:''),
          'twitter'     => (isset($lead['205'])   ? $lead['205']:''),
          'photo'       => (isset($lead['220'])   ? $lead['220']:''),
          'website'     => (isset($lead['213'])   ? $lead['213']:''),
      );
      $makerArray['presenter6'] = array(
          'first_name'  => (isset($lead['159.3']) ? $lead['159.3']:''),
          'last_name'   => (isset($lead['159.6']) ? $lead['159.6']:''),
          'bio'         => (isset($lead['262'])   ? $lead['262']:''),
          'email'       => (isset($lead['164'])   ? $lead['164']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['188'])   ? $lead['188']:''),
          'twitter'     => (isset($lead['204'])   ? $lead['204']:''),
          'photo'       => (isset($lead['221'])   ? $lead['221']:''),
          'website'     => (isset($lead['211'])   ? $lead['211']:''),
      );
      $makerArray['presenter7'] = array(
          'first_name'  => (isset($lead['154.3']) ? $lead['154.3']:''),
          'last_name'   => (isset($lead['154.6']) ? $lead['154.6']:''),
          'bio'         => (isset($lead['263'])   ? $lead['263']:''),
          'email'       => (isset($lead['163'])   ? $lead['163']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['187'])   ? $lead['187']:''),
          'twitter'     => (isset($lead['203'])   ? $lead['203']:''),
          'photo'       => (isset($lead['219'])   ? $lead['219']:''),
          'website'     => (isset($lead['212'])   ? $lead['212']:''),
      );
    }

    /*
     * set entity information
     */
    $leadCategory = array();
    $MAD          = 0;

    //Categories (current fields in use)
    foreach($lead as $leadKey=>$leadValue){
      //4 additional categories
      $pos = strpos($leadKey, '321');
      if ($pos !== false) {
        $leadCategory[]=$leadValue;
      }
      //main catgory
      $pos = strpos($leadKey, '320');
      if ($pos !== false) {
        $leadCategory[]=$leadValue;
      }
      //check the flag field 304
      $pos = strpos($leadKey, '304');
      if ($pos !== false) {
        if($leadValue=='Mobile App Discover')  $MAD = 1;
      }
    }

    //verify we only have unique categories
    $leadCategory = array_unique($leadCategory);

    //determine faire
    $faire = $wpdb->get_var('select faire from wp_mf_faire where FIND_IN_SET ('.$form_id.', wp_mf_faire.form_ids)> 0');

    if($form_type == 'Presentation') {
      $project_photo = $entryData['maker_array']['presenter']['photo'];
    }else{
      $project_photo = (isset($lead['22']) ? $lead['22'] : '');
    }
    //if the entry status is active, use field 303 as the status, else use entry status
    if($lead['status'] == 'active'){
      $status = (isset($lead['303']) ? htmlentities($lead['303']) : '');
    }else{
      $status = $lead['status'];
    }

    $entityArray =
      array(
        'project_photo'       => $project_photo,
        'project_name'        => (isset($lead['151']) ? htmlentities($lead['151']) : ''),
        'presentation_type'   => (isset($lead['1'])   ? htmlentities($lead['1'])   : ''),
        'special_request'     => (isset($lead['64'])  ? htmlentities($lead['64'])  : ''),
        'onsitePhone'         => (isset($lead['265']) ? htmlentities($lead['265']) : ''),
        'public_description'  => (isset($lead['16'])  ? htmlentities($lead['16'])  : ''),
        'private_description' => (isset($lead['11'])  ? htmlentities($lead['11'])  : ''),
        'status'              => $status,
        'categories'          => $leadCategory,
        'faire'               => $faire,
        'mobile_app_discover' => $MAD,
        'form_id'             => $form_id
    );
    $return = array('maker'=>$makerArray,'entity'=>$entityArray);
    return $return;
  }


}