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
      //return array('data'=>array());
    }

    if ( current_user_can( 'mat_view_created_entries') ) {
      //also return entries created by current user
      $query = "SELECT wp_mf_maker_to_entity.maker_type, wp_mf_entity.*, wp_mf_faire.faire_name, wp_mf_faire.end_dt "
            . " FROM   wp_mf_maker_to_entity"
              . " left outer join wp_mf_entity on wp_mf_entity.lead_id = entity_id"
              . " left outer join wp_mf_faire on wp_mf_entity.faire = wp_mf_faire.faire"
              . " left outer join wp_rg_lead on wp_rg_lead.id = wp_mf_maker_to_entity.entity_id"
            . " WHERE (maker_id = '".$this->maker_id."' or created_by = '".$current_user->ID."')"
              . " and wp_rg_lead.status != 'trash' group by lead_id ORDER BY `wp_mf_entity`.`lead_id` DESC";
    } else {
      $query = "SELECT wp_mf_maker_to_entity.maker_type, wp_mf_entity.*, wp_mf_faire.faire_name, wp_mf_faire.end_dt
                FROM  wp_mf_maker_to_entity
                      left outer join wp_mf_entity
                        on wp_mf_entity.lead_id = entity_id
                      left outer join wp_mf_faire
                        on wp_mf_entity.faire = wp_mf_faire.faire
                WHERE maker_id in('".$this->maker_id ."') and status != 'trash'
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
        $today = date("Y-m-d H:i:s");
        //set ticketing and task information if the faire is not past
        if ($row['end_dt'] >= $today) {
          $data['ticketing']    = entryTicketing($entry,'MAT');
          //get tasks
          $data['tasks'] = $this->get_tasks_by_entry($row['lead_id']);
        }

      }else{
        $data['date_created'] = '';
        $data['ticketing']    = '';
        $data['tasks']        = '';
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

      //do not return if form type
      if($form['form_type'] != 'Other'           && $form['form_type'] != 'Payment' &&
         $form['form_type'] != 'Show Management' && $form['form_type'] != ''){
        //get MAT messaging
        $text = GFCommon::replace_variables(rgar($form, 'mat_message'),$form, $entry,false,false);
        $text = do_shortcode( $text ); //process any conditional logic
        $data['mat_message']          = $text;

        //MAT switch to display the edit resources link
        $data['mat_disp_res_link']    = rgar($form, 'mat_disp_res_link');

        //process any shortcode logic in the resource modal layout
        $text = GFCommon::replace_variables(rgar($form, 'mat_res_modal_layout'),$form, $entry);
        $text = do_shortcode( $text );
        $data['mat_res_modal_layout'] = $text;

        //set the URL for the edit resource link
        $url = rgar($form, 'mat_edit_res_url');

        //add entry ID parameter and email
        if($url != '') {
          $url .= '?entry-id='.$row['lead_id'];
          if(isset($entry['98'])) $url .= '&contact-email='.$entry['98'];
        }
        $data['mat_edit_res_url'] = $url;

        $entries['data'][]=$data;
      }
    }
    if(!isset($entries['data'])) $entries['data']=array();
    return $entries;
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
              AND   wp_mf_maker_to_entity.maker_type = 'contact'
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
      if($result['completed']==NULL || $result['completed'] == '0000-00-00 00:00:00'){
        $return['toDo'][]=$result;
      }else{
        $return['done'][]=$result;
      }
    }
    return $return;
  }
}