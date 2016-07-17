<?php
/**
 * Maker Model represents the Maker Entity including all methods and properties
 * relevant to handling data management and profile.
 *
 * @author rich.haynie
 */
class maker {
  /**
   * @var string
   */
  var $maker_email;

  private $_settings;
  /**
   * @var string
   */
  private $_displayname;
  /**
   * @var array
   */

  private $_initialized = false;

    /**
   * @param string $maker_email
   * @param array $args
   */
  function __construct( $maker_email='', $args = array() ) {
    $this->maker_email = $maker_email;
    $this->get_maker_data();

    //MAT pagination
    //TBD: Rich, should this be it's own class??
    $this->dispLimit = 20;
    $this->dispPage  = get_query_var('page',1);
    if($this->dispPage <= 0)  $this->dispPage=1;

    $this->totalNumEntries = 0;

    $this->isSponsor = FALSE;
    $this->isMaker   = FALSE;

    /**
     * Copy properties in from $args, if they exist.
     */
    foreach( $args as $property => $value ) {
      if ( property_exists( $this, $property ) ) {
        $this->$property = $value;
      } else if ( property_exists( $this, $property = "form_{$property}" ) ) {
        $this->$property = $value;
      }
    }
  }

  /**
   * @return string
   */
  public function get_maker_data() {
    global $wpdb;

    //based on maker email retrieve maker information from the DB
    $results = $wpdb->get_row("SELECT * FROM wp_mf_maker WHERE email='".$this->maker_email."'", ARRAY_A );

    //if maker found
    if ( null !== $results ) {
      $this->first_name = $results['First Name'];
      $this->last_name  = $results['Last Name'];
      $this->maker_id   = $results['maker_id'];
    } else {
      //use the Current User WP information
      global $current_user;

      if($current_user->user_firstname != ''){
        $this->first_name = $current_user->user_firstname;
        $this->last_name  = $current_user->user_lastname;
      }elseif( $current_user->display_name!=''){
        //use display name
        $this->first_name = $current_user->display_name;
        $this->last_name  = '';
      }  else {
        //as a last resort use email
        $this->first_name = $current_user->user_email;
        $this->last_name  = '';
      }

      $this->maker_id   = '';
    }
    return;
  }

  //returns a list of entries associated with this maker
  public function get_table_data() {
    //use the Current User WP information
    global $current_user; global $wpdb;
    $entries = array();

    $maker_array = array();
    if($this->maker_id==''){
      return array('data'=>array());
    }
    if ( current_user_can( 'mat_view_created_entries') ) {
      //also return entries created by current user
      $query = "SELECT wp_mf_maker_to_entity.maker_type, wp_mf_entity.*, wp_mf_faire.faire_name "
            . " FROM   wp_mf_maker_to_entity"
              . " left outer join wp_mf_entity on wp_mf_entity.lead_id = entity_id"
              . " left outer join wp_mf_faire on wp_mf_entity.faire = wp_mf_faire.faire"
              . " left outer join wp_rg_lead on wp_rg_lead.id = wp_mf_maker_to_entity.entity_id"
            . " WHERE (maker_id = '".$this->maker_id."' or created_by = '".$current_user->ID."')"
              . " and wp_rg_lead.status != 'trash' group by lead_id ORDER BY `wp_mf_entity`.`lead_id` DESC";
    } else {
      $query = "SELECT wp_mf_maker_to_entity.maker_type, wp_mf_entity.*, wp_mf_faire.faire_name
                FROM  wp_mf_maker_to_entity
                      left outer join wp_mf_entity
                        on wp_mf_entity.lead_id = entity_id
                      left outer join wp_mf_faire
                        on wp_mf_entity.faire = wp_mf_faire.faire
                WHERE maker_id in(".$maker_id.") and status != 'trash'
                group by lead_id
                ORDER BY `wp_mf_entity`.`lead_id` DESC";
    }

    //based on maker email retrieve maker information from the DB
    //get entry count
    $total = $wpdb->get_row("SELECT count(*) as total from (".$query.") src", ARRAY_A );
    $this->totalNumEntries = $total['total'];

    // If the display limit is greater than the total number of entries,
    //  reset the current page to 1
    if($this->dispLimit > $this->totalNumEntries) $this->dispPage = 1;
    $limit = ($this->dispPage - 1 ) * $this->dispLimit;
    $results = $wpdb->get_results($query ." LIMIT " . $limit . ",". $this->dispLimit, ARRAY_A );

    foreach($results as $row){
      $data = array();
      foreach($row as $key=>$value){
        $data[$key] = $value;
      }

      //get entry
      $entry = GFAPI::get_entry($row['lead_id']);

      if(is_array($entry)){
        $data['date_created'] = $entry['date_created'];
        $data['ticketing'] = entryTicketing($entry,'MAT');
      }else{
        $data['date_created'] = '';
        $data['ticketing']    = '';
      }

      //get form_type
      $form_id  = $entry['form_id'];
      $form     = GFAPI::get_form($form_id);
      if(isset($form['form_type']) &&
          ($form['form_type']=='Sponsor' ||
           $form['form_type']=='Startup Sponsor')){
        $this->isSponsor = TRUE;
      }
      if(isset($form['form_type']) &&
          ($form['form_type']=='Exhibit' ||
           $form['form_type']=='Performer'||
           $form['form_type']=='Presentation')){
        $this->isMaker = TRUE;
      }
      $data['form_type'] = $form['form_type'];

      //get MAT messaging
      $data['mat_message'] = rgar($form, 'mat_message');

      $entries['data'][]=$data;
    }

    return $entries;
  }

  /*
   * This table will add/update records to the following tables:
   *    wp_mf_entity, wp_mf_maker, wp_mf_maker_to_entity
   */
   public function updateMakerTable($entryID){
    global $wpdb;
    $entry    = GFAPI::get_entry($entryID);
    $form_id  = $entry['form_id'];
    $form     = GFAPI::get_form($form_id);

    $form_type = (isset($form['form_type'])  ? $form['form_type'] : '');

    //build Maker Data Array
    $data = self::buildMakerData($entry,$form);
    $makerData  = $data['maker'];
    $entityData = $data['entity'];

    /*
     * Update Entity Table - wp_mf_entity
     */
    $wp_mf_entitysql = "insert into wp_mf_entity (lead_id, presentation_title, presentation_type, special_request, "
                    . "     OnsitePhone, desc_short, desc_long, project_photo, status,category,faire,mobile_app_discover) "
                    . " VALUES ('" . $entryID             . "',"
                            . ' "' . $entityData['project_name']            . '", '
                            . ' "' . $entityData['presentation_type']       . '", '
                            . ' "' . $entityData['special_request']         . '", '
                            . ' "' . $entityData['onsitePhone']             . '", '
                            . ' "' . $entityData['public_description']      . '", '
                            . ' "' . $entityData['private_description']     . '", '
                            . ' "' . $entityData['project_photo']           . '", '
                            . ' "' . $entityData['status']                  . '", '
                            . ' "' . implode(',',$entityData['categories']) . '", '
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
                    . '                         category            = "'.implode(',',$entityData['categories']) . '", '
                    . '                         faire               = "'.$entityData['faire']                   . '", '             . '", '
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
        $guid = ($wpdb->num_rows != 0?$guid = $results[0]->maker_id: createGUID($entryData['entry_id'] .'-'.$type));

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
        $wp_mf_maker_to_entity = "INSERT INTO `wp_mf_maker_to_entity`" . " (`maker_id`, `entity_id`, `maker_type`) "
                              . ' VALUES ("'.$guid.'",'.$entryData['entry_id'].',"'.$type.'") ON DUPLICATE KEY UPDATE maker_id="'.$guid.'";';

        $wpdb->get_results($wp_mf_maker_to_entity);
      }
    }
  }

  //function to build the maker data table to update the wp_mf_maker table
  public static function buildMakerData($entry,$form){
    global $wpdb;
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

    $entityArray =
      array(
        'project_photo'       => $project_photo,
        'project_name'        => (isset($lead['151']) ? htmlentities($lead['151']) : ''),
        'presentation_type'   => (isset($lead['1'])   ? htmlentities($lead['1'])   : ''),
        'special_request'     => (isset($lead['64'])  ? htmlentities($lead['64'])  : ''),
        'onsitePhone'         => (isset($lead['265']) ? htmlentities($lead['265']) : ''),
        'public_description'  => (isset($lead['16'])  ? htmlentities($lead['16'])  : ''),
        'private_description' => (isset($lead['11'])  ? htmlentities($lead['11'])  : ''),
        'status'              => (isset($lead['303']) ? htmlentities($lead['303']) : ''),
        'categories'          => $leadCategory,
        'faire'               => $faire,
        'mobile_app_discover' => $MAD,
        'form_id'             => $form_id
    );
    $return = array('maker'=>$makerArray,'entity'=>$entityArray);
    return $makerArray;
  }

  //MAT pagination
  public function createPageLinks( $list_class ='',  $links=3) {
    if($this->dispLimit > $this->totalNumEntries){
      return '';
    }

    $last       = ceil( $this->totalNumEntries / $this->dispLimit );

    $start      = ( ( $this->dispPage - $links ) > 0 ) ? $this->dispPage - $links : 1;
    $end        = ( ( $this->dispPage + $links ) < $last ) ? $this->dispPage + $links : $last;

    $html       = '<ul class="' . $list_class . '">';

    $class      = ( $this->dispPage == 1 ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a href="?page=' . ( $this->dispPage - 1 ) . '">&laquo;</a></li>';

    if ( $start > 1 ) {
        $html   .= '<li><a href="?page=1">1</a></li>';
        $html   .= '<li class="disabled"><span>...</span></li>';
    }

    for ( $i = $start ; $i <= $end; $i++ ) {
        $class  = ( $this->dispPage == $i ) ? "active" : "";
        $html   .= '<li class="' . $class . '"><a href="?page=' . $i . '">' . $i . '</a></li>';
    }

    if ( $end < $last ) {
        $html   .= '<li class="disabled"><span>...</span></li>';
        $html   .= '<li><a href="?page=' . $last . '">' . $last . '</a></li>';
    }

    $class      = ( $this->dispPage == $last ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a href="?page=' . ( $this->dispPage + 1 ) . '">&raquo;</a></li>';

    $html       .= '</ul>';

    return $html;
  }

  //check if current user has access to this entry
  public function check_entry_access($entry ) {
    global $current_user; global $wpdb;

    //check if entry was created by logged on user and if they have the correct role set
    if ( current_user_can( 'mat_view_created_entries') ) {
      if($entry['created_by']==$current_user->ID) return true;
    }

    $query = "SELECT count(*)
              FROM   wp_mf_maker_to_entity
              left  outer join wp_mf_entity
                    on wp_mf_entity.lead_id = entity_id
              WHERE maker_id ='".$this->maker_id."'
              AND   wp_mf_maker_to_entity.entity_id = ".$entry['id']."
              AND   status != 'trash'";
    $count = $wpdb->get_var($query);
    if($count > 0) return true;
  }

  /*
   * Function to retrieve all tasks assigned to a specific entry
   */
  public function get_tasks_by_entry($entryID=0) {
    $return['done'] = $return['toDo'] = array();
    if($entryID==0) {
      $return['error'] = 'Error - Entry ID not passed';
      return $return;
    }
    global $wpdb;
    $query = 'SELECT * FROM `wp_mf_entity_tasks` where lead_id = '. $entryID;

    $results = $wpdb->get_results($query, ARRAY_A );
    foreach($results as $result){
      if($result['completed']==NULL){
        $return['toDo'][]=$result;
      }else{
        $return['done'][]=$result;
      }
    }

    return $return;
  }
}