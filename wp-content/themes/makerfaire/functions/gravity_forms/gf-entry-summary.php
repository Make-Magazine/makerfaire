<?php
// Adding Entry Detail and checking for Processing Posts

add_action("gform_entry_detail_content_before", "add_main_text_before", 10, 2);
function add_main_text_before($form, $lead){
	$mode = empty( $_POST['screen_mode'] ) ? 'view' : $_POST['screen_mode'];
	if ($mode != "view") return;
	echo gf_summary_metabox($form, $lead);
  echo gf_collapsible_sections($form, $lead);
}

// Summary Metabox
function gf_summary_metabox($form, $lead) {

$entry_id = $lead['id'];
$photo                    = (isset($lead['22'])?$lead['22']:'');
$short_description        = (isset($lead['16'])?$lead['16']:'');
$long_description         = (isset($lead['21'])?$lead['21']:'');
$project_name             = (isset($lead['151'])?$lead['151']:'');
$areyoua                  = (isset($lead['45'])?$lead['45']:'');
$size_request             = (isset($lead['60'])?$lead['60']:'');
$size_request_heightwidth = ((isset($lead['344']) && strlen($lead['344']) > 0 ) ? $lead['344'].' X ':'').(isset($lead['345'])?$lead['345']:'');
$size_request_other       = (isset($lead['61'])?$lead['61']:'');
$entry_form_type          = $form['title'];
$entry_form_status        = (isset($lead['303'])?$lead['303']:'');
$wkey                     = (isset($lead['27'])?$lead['27']:'');
$vkey                     = (isset($lead['32'])?$lead['32']:'');

$makerfirstname1          = (isset($lead['160.3'])?$lead['160.3']:'');
$makerlastname1           = (isset($lead['160.6'])?$lead['160.6']:'');
$makerPhoto1              = (isset($lead['217'])?$lead['217']:'');
$makerfirstname2          = (isset($lead['158.3'])?$lead['158.3']:'');
$makerlastname2           = (isset($lead['158.6'])?$lead['158.6']:'');
$makerPhoto2              = (isset($lead['224'])?$lead['224']:'');
$makerfirstname3          = (isset($lead['155.3'])?$lead['155.3']:'');
$makerlastname3           = (isset($lead['155.6'])?$lead['155.6']:'');
$makerPhoto3              = (isset($lead['223'])?$lead['223']:'');
$makerfirstname4          = (isset($lead['156.3'])?$lead['156.3']:'');
$makerlastname4           = (isset($lead['156.6'])?$lead['156.6']:'');
$makerPhoto4              = (isset($lead['222'])?$lead['222']:'');
$makerfirstname5          = (isset($lead['157.3'])?$lead['157.3']:'');
$makerlastname5           = (isset($lead['157.6'])?$lead['157.6']:'');
$makerPhoto5              = (isset($lead['220'])?$lead['220']:'');
$makerfirstname6          = (isset($lead['159.3'])?$lead['159.3']:'');
$makerlastname6           = (isset($lead['159.6'])?$lead['159.6']:'');
$makerPhoto6              = (isset($lead['221'])?$lead['221']:'');
$makerfirstname7          = (isset($lead['154.3'])?$lead['154.3']:'');
$makerlastname7           = (isset($lead['154.6'])?$lead['154.6']:'');
$makerPhoto7              = (isset($lead['219'])?$lead['219']:'');
$makergroupname           = (isset($lead['109'])?$lead['109']:'');
$makerGroupPhoto          = (isset($lead['111'])?$lead['111']:'');

$field55=RGFormsModel::get_field($form,'55');
$whatareyourplansvalues = $field55['choices'];

$main_description = '';
// Check if we are loading the public description or a short description
if ( isset( $long_description ) && $long_description!='') {
	$main_description = $long_description;
} else if ( isset($short_description ) ) {
	$main_description = $short_description;
}

//pull faireID
global $wpdb;
$faire = $wpdb->get_var('select faire from wp_mf_faire where INSTR (wp_mf_faire.form_ids,'. $form['id'].')> 0');
$return = '
<table class="fixed entry-detail-view">
	<thead>
		<th colspan="2" style="text-align: left" id="header"><h1>'. esc_html($project_name).'</h1></th>
	</thead>
	<tbody>
		<tr>
			<td style="width:440px; padding:5px;" valign="top">
				<a href="'. $photo.'" class="thickbox"><img width="400px" src="'.legacy_get_fit_remote_image_url($photo, 400,400).'" alt="" /></a>
			</td>
			<td style="word-break: break-all;" valign="top">
				<table style="word-break: break-all;">
					<tr>
						<td colspan="2">
							<p>'.stripslashes( nl2br( $main_description, "\n" )  ).'</p>
						</td>
					</tr>
					<tr>
						<td style="width: 80px; word-break: break-all;" valign="top"><strong>Type:</strong></td>
						<td valign="top">'.esc_attr( ucfirst( $entry_form_type ) ).'</td>
					</tr>
					<tr>
						<td style="width: 80px; word-break: break-all;" valign="top"><strong>Status:</strong></td>
						<td valign="top">'. esc_attr( $entry_form_status ).'</td>
					</tr>
					<tr>
						<td style="width: 80px; word-break: break-all;" valign="top"><strong>Website:</strong></td>
						<td valign="top"><a href="'. esc_url(  $wkey ).'" target="_blank">'. esc_url( $wkey ).'</a></td>
					</tr>
					<tr>
						<td valign="top"><strong>Video:</strong></td>
						<td>'. (( isset( $vkey ) ) ? '<a href="' . esc_url( $vkey ) . '" target="_blank">' . esc_url( $vkey ) . '</a><br/>' : '') . '</td>
					</tr>
					<tr>
						<td style="width: 80px; word-break: break-all;" valign="top"><strong>Maker Names:</strong></td>
						<td valign="top">'. (!empty($makergroupname) ? $makergroupname.'(Group)</br>' : '');

              //loop thru all 7 maker photos
              for ($x = 1; $x <= 7; $x++) {
                if(!empty(${"makerPhoto_$x"})){
                  $return .= '<a href="'. ${"makerPhoto_$x"}.'" class="thickbox"><img width="30px" src="'. legacy_get_resized_remote_image_url(${"makerPhoto_$x"}, 30,30).'" alt="" /></a>';
                }
                $return .= (!empty(${"makerfirstname$x"}) ?  ${"makerfirstname$x"}.' '.${"makerlastname$x"}.'</br>' : '') ;
              }

              if(!empty($makerGroupPhoto)){
                  $return .=   'Group Photo<br/>
                    <a href="'. $makerGroupPhoto.'" class="thickbox">
                    <img width="30px" src="'.legacy_get_resized_remote_image_url($makerGroupPhoto, 30,30).'" alt="" />
                    </a>';
              }

              $return .=  (!empty($makerfirstname7) ?  $makerfirstname7.' '.$makerlastname7.'</br>' : '') ;
              $return .= '
            </td>
					</tr>
          <tr>
						<td valign="top"><strong>We are (a/an)...:</strong></td>
						<td>'.(( isset( $areyoua ) ) ? $areyoua : '') .'</td>
					</tr>
					<tr>
						<td style="width: 80px;" valign="top"><strong>What are your plans:</strong></td>
						<td valign="top">';

						for ($i=0; $i < count($whatareyourplansvalues); $i++) {
							$return .=  ((!empty($lead['55.'.$i])) ? $lead['55.'.$i].'<br />' : '');
						}
            $return .= '
            </td>
					</tr>
          <tr>
            <td valign="top"><strong>Fee Indicator:</strong></td>
						<td>'.(( isset( $lead[434] ) ) ? $lead[434] : 'No') .'</td>
          </tr>
          <tr>
            <td valign="top"><strong>CM Indicator:</strong></td>
						<td>'.(( isset( $lead[376] ) ) ? $lead[376] : 'No') .'</td>
          </tr>
					<tr>
						<td valign="top"><strong>Size Request:</strong></td>
						<td>
              '.(( isset( $size_request ) ) ? $size_request : 'Not Filled out') .
                (( isset( $size_request_heightwidth ) ) ? $size_request_heightwidth : '') .
                (( strlen( $size_request_other ) > 0 ) ? ' <br />Comment: '.$size_request_other : '') .'
						</td>
					</tr>
          <tr>
            <td valign="top"><strong>Schedule/Location:</strong></td>
            <td>'. display_schedule($form['id'],$lead,'summary').'</td>
          </tr>
          <tr>
            <td>
              <a target="_blank" href="/maker-sign/'. $entry_id.'/'. $faire.'"><input class="button button-large button-primary" style="text-align:center" value="Download Maker Sign" /></a>
            </td>
            <td>
              <a href="'. admin_url( 'admin-post.php?action=createCSVfile&exForm='.$form['id'].'&exEntry='. $entry_id ).'"><input class="button button-large button-primary"  style="text-align:center" value="Export All Fields" /></a>
            </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
        <label >Email Note To:</label><br />';

				$emailto1 = array("Alasdair Allan"          => "alasdair@makezine.com",
                          "Audrey Donaldson"        => "audrey@makermedia.com",
                          "Bridgette Vanderlaan"    => "bvanderlaan@mac.com",
                          "Caleb Kraft"             => "caleb@makermedia.com",
                          "Dale Dougherty"          => "dale@makermedia.com",
                          "DC Denison"              => "dcdenison@makermedia.com",
                          "Jay Kravitz"             => "jay@thecrucible.org",
                          "Jess Hobbs"              => "jess@makermedia.com",
                          "Jonathan Maginn"         => "jonathan.maginn@sbcglobal.net",
                          "Kate Rowe"               => "krowe@makermedia.com");
        $emailto2 = array("Kerry Moore"             => "kerry@contextfurniture.com",
                          "Kim Dow"                 => "dow@dowhouse.com",
                          "Louise Glasgow"          => "lglasgow@makermedia.com",
                          "Matt Stultz"             => "mstultz@makermedia.com",
                          "Miranda Mota"            => "miranda@makermedia.com",
                          "Rob Bullington"          => "rbullington@makermedia.com",
                          "Sabrina Merlo"           => "smerlo@makermedia.com",
                          "Sherry Huss"             => "sherry@makermedia.com",
                          "Siana Alcorn"            => "siana@makermedia.com",
                          "Tami Jo Benson"          => "tj@tamijo.com");
        $emailtoaliases = array("3D Printing"       => "3dprinting@makermedia.com",
                                "Editors"           => "editor@makezine.com",
                                "Maker Relations"   => "makers@makerfaire.com",
                                "Marketing"         => "marketing@makermedia.com",
                                "PR"                => "pr@makerfaire.com",
                                "Shed"              => "shedmakers@makermedia.com",
                                "Education"         => "education@makermedia.com",
                                "Sales"             => "sales@makerfaire.com",
                                "Sustainability"    => "sustainability@makerfaire.com",
                                "Speakers"          => "speakers@makerfaire.com");

        if(in_array($form['id'], array(64, 65, 67, 68))){
           $emailtoaliases["National Team"] = "nationalmakers@makerfaire.com";
        }
				$return .=
       '<div style="float:left">';
          foreach ( $emailtoaliases as $name => $email ) {
            $return .= '<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="'.$email.'" /><strong>'.$name.'</strong> <br />';
					 }
        $return .= '
				</div>
			  <div style="float:left">';
          foreach ( $emailto1 as $name => $email ) {
            $return .= '<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="'.$email.'" />'.$name.'<br />';
					}
        $return .=   '
				</div>
			  <div style="float:left">';
          foreach ( $emailto2 as $name => $email ) {
            $return .= '<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="'.$email.'" />'.$name.' <br />';
					}
        $return .= '
				</div>
			</td>
			<td style="vertical-align: top; padding: 10px;">
        <textarea	name="new_note_sidebar"	style="width: 90%; height: 140px;" cols=""	rows=""></textarea>';
          $note_button = '<input type="button" name="add_note_sidebar" value="' . __( 'Add Note', 'gravityforms' ) . '" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'add_note_sidebar\');"/>';
          $return .=  apply_filters( 'gform_addnote_button', $note_button );
          $return .=  '<span class="updMsg add_note_sidebarMsg"></span>
			</td>
		</tr>
	</tbody>
</table>';

  return $return;
} //end function

function gf_collapsible_sections($form, $lead){
  /*
   * 1. Content
        Include field IDs:
        11 [Tell us about your project and exhibit.]
        16 [Provide a short description for our website, mobile app, and your sign.]
        2. Logistics
        Include field IDs:
        60 [Space Size Request]
        61 [Other: List the specific dimensions (__ft x __ft ) and provide additional details about the size of space you require.]
        62 [Tables and Chairs]
        288 [How many tables and chairs?]
   */
  global $wpdb;
  $entry_id = $lead['id'];

  $makerfirstname1          = (isset($lead['160.3'])?$lead['160.3']:'');
  $makerlastname1           = (isset($lead['160.6'])?$lead['160.6']:'');

  $makerfirstname2          = (isset($lead['158.3'])?$lead['158.3']:'');
  $makerlastname2           = (isset($lead['158.6'])?$lead['158.6']:'');

  $makerfirstname3          = (isset($lead['155.3'])?$lead['155.3']:'');
  $makerlastname3           = (isset($lead['155.6'])?$lead['155.6']:'');

  $makerfirstname4          = (isset($lead['156.3'])?$lead['156.3']:'');
  $makerlastname4           = (isset($lead['156.6'])?$lead['156.6']:'');

  $makerfirstname5          = (isset($lead['157.3'])?$lead['157.3']:'');
  $makerlastname5           = (isset($lead['157.6'])?$lead['157.6']:'');

  $makerfirstname6          = (isset($lead['159.3'])?$lead['159.3']:'');
  $makerlastname6           = (isset($lead['159.6'])?$lead['159.6']:'');

  $makerfirstname7          = (isset($lead['154.3'])?$lead['154.3']:'');
  $makerlastname7           = (isset($lead['154.6'])?$lead['154.6']:'');

  //email fields
  $emailArray = array();
  if(isset($lead['98'])  && $lead['98']  != '')  $emailArray[$lead['98']]['Contact']   = $lead['96.3'].' '.$lead[ '96.6'];
  if(isset($lead['161']) && $lead['161'] != '')  $emailArray[$lead['161']]['Maker 1']  = $makerfirstname1.' '.$makerlastname1;
  if(isset($lead['162']) && $lead['162'] != '')  $emailArray[$lead['162']]['Maker 2']  = $makerfirstname2.' '.$makerlastname2;
  if(isset($lead['167']) && $lead['167'] != '')  $emailArray[$lead['167']]['Maker 3']  = $makerfirstname3.' '.$makerlastname3;
  if(isset($lead['166']) && $lead['166'] != '')  $emailArray[$lead['166']]['Maker 4']  = $makerfirstname4.' '.$makerlastname4;
  if(isset($lead['165']) && $lead['165'] != '')  $emailArray[$lead['165']]['Maker 5']  = $makerfirstname5.' '.$makerlastname5;
  if(isset($lead['164']) && $lead['164'] != '')  $emailArray[$lead['164']]['Maker 6']  = $makerfirstname6.' '.$makerlastname6;
  if(isset($lead['163']) && $lead['163'] != '')  $emailArray[$lead['163']]['Maker 7']  = $makerfirstname7.' '.$makerlastname7;

  foreach($form['fields'] as $field){
    $fieldData[$field['id']] = $field;
  }

  $data = array('content'=> array(11,16,320,321,66,67,293),
                'logistics'=>array(60,344,345,61,62,347,348,64,65,68,69,70,71,72,73,74,75,76),
                'additional'=>array(123,130,287,134),
                'images'=>array(22,217,224,223,222,220,221,219,111),
                'imagesOver'=>array(324,334,326,338,333,337,332,336,331,335)
      );
  //additional Entries
  $addEntries =
        '<table width="100%">
          <thead>
            <tr>
              <th>Maker Name  </th>
              <th>Maker Type  </th>
              <th>Record ID   </th>
              <th>Project Name</th>
              <th>Form Name   </th>
              <th>Status      </th>
            </tr>
          </thead>';
  $addEntriesCnt=0;
  foreach($emailArray as $key=>$email){
    $results = $wpdb->get_results( 'SELECT *, '
                          . ' (select value from wp_rg_lead_detail detail2 '
                          . '  where detail2.lead_id = wp_rg_lead_detail.lead_id and '
                          . '        field_number    = 151 '
                          . ' ) as projectName, '
                          . ' (select value from wp_rg_lead_detail detail2 '
                          . '  where detail2.lead_id = wp_rg_lead_detail.lead_id and '
                          . '        field_number    = 303 '
                          . ' ) as status '
                  . ' FROM wp_rg_lead_detail '
                  . ' join wp_rg_form on wp_rg_form.id = wp_rg_lead_detail.form_id '
                  . ' WHERE value = "'.$key.'"'
                  . '   and lead_id != '.$entry_id.' group by lead_id order by lead_id');


    foreach($results as $addData){
      $outputURL = admin_url( 'admin.php' ) . "?page=gf_entries&view=mfentry&id=".$addData->form_id . '&lid='.$addData->lead_id;
      $addEntriesCnt++;
      $addEntries .=  '<tr>';

      //only display the first instance of the email
      foreach($email as $typeKey=>$typeData){
        $name = $typeKey;
        $type = $typeData;
        if($name!='') break;
      }
      $addEntries .=  '<td>'.$type .'</td>';
      $addEntries .=  '<td>'.$name .'</td>';
      $addEntries .=  '<td><a target="_blank" href="'.$outputURL.'">'.$addData->lead_id.'</a></td>'
          . '<td>'.$addData->projectName.'</td>'
          . '<td>'.$addData->title.'</td>'
          . '<td>'.$addData->status.'</td>'
          . '</tr>';
    }
  }
  $addEntries .= '</table>';

  //form data
  $addFormsData = getmetaData($entry_id);
  $pmtFormsData = getmetaData($entry_id,'payments');
  $return = '
  <div id="tabs" class="adminEntrySummary">
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation"><a href="#tabs-1" aria-controls="tabs-1" role="tab" data-toggle="tabs-1"><br/>Content</a></li>
      <li role="presentation"><a href="#tabs-2" aria-controls="tabs-2" role="tab" data-toggle="tabs-2">Logistics/<br/>Production</a></li>
      <li role="presentation"><a href="#additional" aria-controls="additional" role="tab" data-toggle="additional">Additional<br/>Information</a></li>
      <li role="presentation"><a href="#addForms" aria-controls="addForms" role="tab" data-toggle="addForms">Additional<br/>Forms ('. $addFormsData[1].')</a></li>
      <li role="presentation"><a href="#payments" aria-controls="payments" role="tab" data-toggle="payments"><br/>Payments ('. $pmtFormsData[1].')</a></li>
      <li role="presentation"><a href="#tabs-3" aria-controls="tabs-3" role="tab" data-toggle="tabs-3">Other<br/>Entries ('. $addEntriesCnt.')</a></li>
      <li role="presentation"><a href="#images" aria-controls="images" role="tab" data-toggle="images"><br/>Images</a></li>
      <li role="presentation" aria-selected="true"><a href="#resources" aria-controls="resources" role="tab" data-toggle="resources"><br/>Resources</a></li>
      <li role="presentation" aria-selected="true"><a href="#ticketing" aria-controls="ticketing" role="tab" data-toggle="ticketing"><br/>Ticketing</a></li>
    </ul>
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane" id="tabs-1">
        '. displayContent($data['content'],$lead,$fieldData).'
      </div>
      <div role="tabpanel" class="tab-pane" id="tabs-2">
        '. displayContent($data['logistics'],$lead,$fieldData).'
      </div>
      <div role="tabpanel" class="tab-pane" id="additional">
        '. displayContent($data['additional'],$lead,$fieldData).'
      </div>

      <div role="tabpanel" class="tab-pane" id="addForms">
        '. $addFormsData[0] .'
      </div>
      <div role="tabpanel" class="tab-pane" id="payments">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            '. $pmtFormsData[0] .'
          </div>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="tabs-3">
        '. $addEntries.'
      </div>
      <div role="tabpanel" class="tab-pane"  id="images">
        '. displayContent($data['images'],$lead,$fieldData,'grid').'
        '. displayContent($data['imagesOver'],$lead,$fieldData,'grid').'
      </div>

      <div role="tabpanel" class="tab-pane"  id="resources">
        <div class="entry-resource">
          '. entryResources($lead).'
        </div>
      </div>

      <div role="tabpanel" class="tab-pane"  id="ticketing">
        <div class="panel-group">'.

          $ticketing = entryTicketing($lead);
          if($ticketing){
            $return .= $ticketing;
          }else{
            $return .= '
            <div id="noTickets">
              No Access Codes found for this entry. Click the ticket icon to generate<br/>
              <br/>
              <p onclick="ebAccessTokens()"><i class="fa fa-ticket fa-3x" aria-hidden="true"></i></i></p>
            </div>

            <div style="display:none" id="createTickets">
              <i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i>
              <span class="sr-only">Loading...</span>
            </div>
            <i>Please be patient.  This may take a while to complete</i>';
          }
        $return .= '
        </div>
      </div>
    </div> <!-- .tab-content -->
  </div>';

  return $return;
}

function displayContent($content,$lead,$fieldData,$display = 'table'){
  global $display_empty_fields;
  $return = '';
  if($display=='table')   $return .= '<table>';
  $form = GFAPI::get_form( $lead['form_id'] );

  foreach($content as $fieldID){
    if(isset($fieldData[$fieldID])){
      $field = $fieldData[$fieldID];
      $value = RGFormsModel::get_lead_field_value( $lead, $field );
      if(RGFormsModel::get_input_type($field)!='fileupload'){
        $display_value = GFCommon::get_lead_field_display( $field, $value, $lead['currency'] );
        $display_value = apply_filters( 'gform_entry_field_value', $display_value, $field, $lead, $form );
      }else{
        //display images in a grid
        if($value!=''){
          $display_value = '<img width="100px" src="'. legacy_get_resized_remote_image_url($value, 100,100).'" alt="" />';
        }else{
          $display_value = '';
        }
      }


      if ( $display_empty_fields || ! empty( $display_value ) || $display_value === '0' ) {
        $display_value = empty( $display_value ) && $display_value !== '0' ? '&nbsp;' : $display_value;
        if($display=='table'){
        $content = '
                <tr>
                <td colspan="2" class="entry-view-field-name">' . esc_html( GFCommon::get_label( $field ) ) . '</td>
                </tr>
                <tr>
                <td colspan="2" class="entry-view-field-value">' . $display_value . '</td>
                </tr>';
        }else{
          $content = '<div style="'.($field['cssClass']==''?'float:left;':'').'padding:5px;margin:10px" class="'.$field['cssClass'].'">'.esc_html( GFCommon::get_label( $field ) ).'<br/>'.$display_value.'</div>';
        }
        $content = apply_filters( 'gform_field_content', $content, $field, $value, $lead['id'], $form['id'] );
        $return .=  $content;
      }
    }
  }
  if($display=='table')   $return .= '</table>';
  if($display=='grid')   $return .= '<div class="clear"></div>';
  return $return;
}

function getmetaData($entry_id,$type=''){
  $return = '';
  $metaData = mf_get_form_meta( 'entry_id',$entry_id );

  $formCount=0;
  foreach($metaData as $data){
    $entry = GFAPI::get_entry( $data->lead_id );
    //check if entry-id is valid
    if(is_array($entry)){  //display entry data
      $formPull = GFAPI::get_form( $data->form_id );
      if(!isset($formPull['form_type'])) $formPull['form_type']='';
      /*
       * determine if we should display form data
       * If type = blank, display all forms but Payment type
       * If type = payments, only display forms with type of Payment
       */
      if( ($type == ''         && $formPull['form_type'] != 'Payment') ||
          ($type == 'payments' && $formPull['form_type'] == 'Payment')){
        $formCount ++;
        $formTable = '<table>';

        $count = 0;
        $field_count = sizeof( $formPull['fields'] );
        $has_product_fields = false;
        foreach($formPull['fields'] as $formFields){
          $gwreadonly_enable = (isset($formFields['gwreadonly_enable'])?$formFields['gwreadonly_enable']:0);
          //exclude page breaks and the entry fields used to verify the entry
          // and the display only fields from the additional forms
          if( $formFields['type']      != 'page' &&
              $formFields['inputName'] != 'entry-id' &&
              $formFields['inputName'] != 'contact-email' &&
              $gwreadonly_enable !=1){

            $display_empty_fields = false;

            switch ( RGFormsModel::get_input_type( $formFields ) ) {
              case 'section' :
                if ( ! GFCommon::is_section_empty( $formFields, $formPull, $entry ) || $display_empty_fields ) {
                  $count ++;
                  $is_last = $count >= $field_count ? true : false;
                  $formTable .= '
                  <tr>
                    <td colspan="2" class="entry-view-section-break'. ($is_last ? ' lastrow' : '') .'">'. esc_html( GFCommon::get_label( $formFields ) ) .'</td>
                  </tr>';
                }
                break;

              case 'captcha':
              case 'html':
              case 'password':
              case 'page':
                //ignore captcha, html, password, page field
                break;

              default :
                //ignore product fields as they will be grouped together at the end of the grid
                if ( GFCommon::is_product_field( $formFields->type ) ) {
                  $has_product_fields = true;
                  continue;
                }

                $value         = RGFormsModel::get_lead_field_value( $entry, $formFields );
                $display_value = GFCommon::get_lead_field_display( $formFields, $value, $entry['currency'] );
                $display_value = apply_filters( 'gform_entry_field_value', $display_value, $formFields, $entry, $formPull );

                if ( $display_empty_fields || ! empty( $display_value ) || $display_value === '0' ) {
                  $display_value = empty( $display_value ) && $display_value !== '0' ? '&nbsp;' : $display_value;

                  $content = '
                    <tr>
                      <td colspan="2" class="entry-view-field-name">' . esc_html( GFCommon::get_label( $formFields ) ) . '</td>
                    </tr>
                    <tr>
                      <td colspan="2" class="entry-view-field-value">' . $display_value . '</td>
                    </tr>';

                  $content = apply_filters( 'gform_field_content', $content, $formFields, $value, $entry['id'], $formPull['id'] );
                  $formTable .= $content;
                }
                break;
            }
          }
        }
        if($has_product_fields){
          $format = 'html';
          $formTable .= GFCommon::get_submitted_pricing_fields( $formPull, $entry, $format);
        }

        //display any payment notes
        $notes = RGFormsModel::get_lead_notes( $data->lead_id );
        foreach($notes as $note){
          if($note->user_name=='PayPal'){
            $formTable .= '<tr><td colspan="2" class="entry-view-field-name">PayPal</td></tr>';
            $formTable .= '<tr><td colspan="2" class="entry-view-field-value">'.
                          esc_html(GFCommon::format_date($note->date_created, false)).'<br/>'.
                          $note->value.'</td>'.
                        '</tr>';
          }
        }
        $formTable .= '</table>';

        //let's set up each form as it's own collapsible section
        $return .=
            '<div class="panel-heading" id="headingOne">'.
              '<div class="row">'.
                '<div class="col-md-9">'
                . '<h3 class="panel-title">'.$formPull['title'].'</h3>';
                if(isset($entry['payment_status']) && $entry['payment_status']!=NULL){
                  $return .=  '<br/>Status: '.$entry['payment_status'].
                              ($entry['payment_amount']!=NULL?' ('.GFCommon::to_money( $entry['payment_amount'], $entry['currency']).')':'').
                              ($entry['payment_date']!=NULL?' - '.$entry['payment_date']:'');
                }
           $return .=
                '</div>'.
                '<div class="col-md-3">'.
                  '<button type="button"   class="btn btn-info" data-toggle="collapse" data-target="#entr_'.$entry['id'].'">Show/Hide Form Data</button>'.
                '</div>'.
              '</div>'. //close .row
              '<hr/>'.
            '</div>'. //close .panel-heading
            '<div id="entr_'.$entry['id'].'" class="panel-collapse collapse" role="tabpanel">'.
              '<div class="panel-body">'.$formTable.'</div>'.
            '</div>'; //close panel-collapse
      }
    }
  }
  return array($return,$formCount);
}

// this function returns all entries with a
// meta key set to a certain meta value
function mf_get_form_meta( $meta_key,$meta_value ) {
	global $wpdb;
	$table_name = RGFormsModel::get_lead_meta_table_name();
  $entry = GFAPI::get_entry( $meta_value );

  //retrieve the most current records for each additional form/entry id/form_id combination
  $results  = $wpdb->get_results( $sql = $wpdb->prepare("SELECT * FROM {$table_name}
              WHERE meta_value=%d AND meta_key=%s
              order by id desc", $meta_value, $meta_key));
	return $results;
}

//retrieves resource and attribute information for the entry
function entryResources($lead){
  global $wpdb;
  //create JS array for item drop down and type drop down
  $sql = "SELECT * FROM `wp_rmt_resource_categories` order by category ASC";
  $results = $wpdb->get_results($sql);
  $itemArr = array();
  $itemDD = 'var itemDrop=[];';
  foreach($results as $result){
    $itemDD .= 'itemDrop['.$result->ID.']="'.addslashes($result->category).'";';
  }

  //build Item to type drop down array
  $sql = "SELECT wp_rmt_resource_categories.ID as item_id, wp_rmt_resource_categories.category as item, wp_rmt_resources.ID as type_id, wp_rmt_resources.type FROM `wp_rmt_resource_categories` right outer join wp_rmt_resources on wp_rmt_resource_categories.ID= wp_rmt_resources.resource_category_id ORDER BY `wp_rmt_resource_categories`.`category` ASC, type ASC";
  $results = $wpdb->get_results($sql);
  $itemArr = array();
  foreach($results as $result){
    $itemArr[$result->item_id] = $result->item;
    $typeArr[$result->item_id][$result->type_id] = $result->type;
  }

  //gather resource data
  $sql = "SELECT er.*, type, wp_rmt_resource_categories.category as item, wp_rmt_resource_categories.ID as item_id "
         . "FROM `wp_rmt_entry_resources` er, wp_rmt_resources, wp_rmt_resource_categories "
        . "where er.resource_id = wp_rmt_resources.ID "
          . "and resource_category_id = wp_rmt_resource_categories.ID  "
          . "and er.entry_id = ".$entry_id = $lead['id']." order by item ASC, type ASC";

  $results = $wpdb->get_results($sql);
  $resourceDisp = '<table id="resTable"><thead>'
                  . ' <tr>'
                    . ' <th>Lock</th>'
                    . ' <th>Item</th>'
                    . ' <th>Type</th>'
                    . ' <th>Qty</th>'
                    . ' <th>Comments</th>'
                    . ' <th>User</th>'
                    . ' <th>Last Updated</th>'
                    . ' <th><p class="addIcon" onclick="addRow(\'resource\')"><i class="fa fa-plus-circle fa-lg"></i></p></th>'
                  . ' </tr></thead>';
  $return = '';
  $resourceDisp .= '<tbody>';
  foreach($results as $result){
    if($result->user==NULL){
      $dispUser = 'Initial';
    }elseif($result->user==0){
      $dispUser = 'Payment';
    }else{
      $userInfo = get_userdata( $result->user );
      $dispUser = $userInfo->display_name;
    }
    $resourceDisp .= '<tr id="resRow'.$result->ID.'">'
                      . ' <td class="lock"><span class="lockIcon" onclick="resAttLock(\'#resRow'.$result->ID.'\','.$result->lockBit.')">'.($result->lockBit==1?'<i class="fa fa-lock fa-lg"></i>':'<i class="fa fa-unlock-alt fa-lg"></i>').'</span></td>'
                      . ' <td id="resitem_'.$result->ID.'" data-itemID="'.$result->item_id.'">'.$result->item.'</td>'
                      . ' <td id="restype_'.$result->ID.'" data-typeID="'.$result->resource_id.'" class="editable dropdown">'.$result->type.'</td>'
                      . ' <td id="resqty_'.$result->ID.'"  class="editable numeric">'.$result->qty.'</td>'
                      . ' <td id="rescomment_'.$result->ID.'" class="editable textAreaEdit">'.$result->comment.'</td>'
                      . ' <td id="resuser_'.$result->ID.'">'.$dispUser.'</td>'
                      . ' <td id="resdateupdate_'.$result->ID.'">'.date('m/d/y h:i a',strtotime($result->update_stamp)).'</td>'
                      . ' <td class="delete"><span class="delIcon" onclick="resAttDelete(\'#resRow'.$result->ID.'\')"><i class="fa fa-minus-circle fa-lg"></i></span></td>'
                  . ' </tr>';
  }
  $resourceDisp .= '</tbody>';
  $resourceDisp .= '</table>';

  //gather attribute data
  $sql = "SELECT wp_rmt_entry_attributes.*, attribute_id,value,wp_rmt_entry_att_categories.category
          FROM `wp_rmt_entry_attributes`, wp_rmt_entry_att_categories
          where attribute_id = wp_rmt_entry_att_categories.ID
          and entry_id = ".$entry_id = $lead['id'] ." order by category";

  $results = $wpdb->get_results($sql);
  $attDisp = '<table id="attTable"><thead><tr>'
                . ' <th>Lock</th>'
                . ' <th>Attribute</th>'
                . ' <th>Value</th>'
                . ' <th>Comment</th>'
                . ' <th>User</th>'
                . ' <th>Last Updated</th>'
                . ' <th><span class="addIcon" onclick="addRow(\'attribute\')"><i class="fa fa-plus-circle fa-lg"></i></span></th></tr></thead>';
  $attDisp .= '<tbody>';
  foreach($results as $result){
    if($result->user==NULL){
      $dispUser = 'Initial';
    }elseif($result->user==0){
      $dispUser = 'Payment';
    }else{
      $userInfo = get_userdata( $result->user );
      $dispUser = $userInfo->display_name;
    }
    $attDisp .= '<tr id="attRow'.$result->ID.'">'
                    . ' <td class="lock">'
                    . '   <span class="lockIcon" onclick="resAttLock(\'#attRow'.$result->ID.'\','.$result->lockBit.')">'
                    . ($result->lockBit==1?'<i class="fa fa-lock fa-lg"></i>':'<i class="fa fa-unlock-alt fa-lg"></i>')
                    . '   </span>'
                    . ' </td>'
                    . ' <td id="attcategory_'.$result->ID.'">'.$result->category.'</td>'
                    . ' <td id="attvalue_'.$result->ID.'" class="editable textAreaEdit">'.$result->value.'</td>'
                    . ' <td id="attcomment_'.$result->ID.'" class="editable textAreaEdit">'.$result->comment.'</td>'
                    . ' <td id="attuser_'.$result->ID.'">'.$dispUser.'</td>'
                    . ' <td id="attdateupdate_'.$result->ID.'">'.date('m/d/y h:i a',strtotime($result->update_stamp)).'</td>'
                    . ' <td class="delete"><span class="delIcon" onclick="resAttDelete(\'#attRow'.$result->ID.'\')"><i class="fa fa-minus-circle fa-lg"></i></span></td></tr>';
  }
  $attDisp .= '</tbody>';
  $attDisp .= '</table>';

  //build attribute drop down values
   //build Item to type drop down array
  $sql = "SELECT ID, category FROM wp_rmt_entry_att_categories";
  $results = $wpdb->get_results($sql);
  $attArr = array();
  foreach($results as $result){
    $attArr[] = array('key'=>$result->ID,'value'=>$result->category);
  }

  //build attention section
  $attnDisp = '';
  $sql = "SELECT wp_rmt_entry_attn.*, wp_rmt_attn.value
          FROM `wp_rmt_entry_attn`, wp_rmt_attn
          where wp_rmt_entry_attn.attn_id = wp_rmt_attn.ID
          and entry_id = ".$entry_id = $lead['id'] ." order by wp_rmt_attn.value";

  $results = $wpdb->get_results($sql);
  $attnDisp = '<table id="attnTable"><thead><tr>'
                . ' <th>Attention</th>'
                . ' <th>Comment</th>'
                . ' <th>User</th>'
                . ' <th>Last Updated</th>'
                . ' <th><span onclick="addRow(\'attention\')"><i class="fa fa-plus-circle"></i></span></th></tr></thead>';
  $attnDisp .= '<tbody>';
  foreach($results as $result){
    if($result->user==NULL){
      $dispUser = 'Initial';
    }else{
      $userInfo = get_userdata( $result->user );
      $dispUser = $userInfo->display_name;
    }
    $attnDisp .= '<tr id="attnRow'.$result->ID.'">'
                    . ' <td id="attnvalue_'.$result->ID.'">'.$result->value.'</td>'
                    . ' <td id="attncomment_'.$result->ID.'" class="editable textAreaEdit">'.$result->comment.'</td>'
                    . ' <td id="attnuser_'.$result->ID.'">'.$dispUser.'</td>'
                    . ' <td id="attndateupdate_'.$result->ID.'">'.date('m/d/y h:i a',strtotime($result->update_stamp)).'</td>'
                    . ' <td><span onclick="resAttDelete(\'#attnRow'.$result->ID.'\')"><i class="fa fa-minus-circle"></i></span></td></tr>';
  }
  $attnDisp .= '</tbody>';
  $attnDisp .= '</table>';

  //build attention drop down values
  $sql = "SELECT ID, value FROM wp_rmt_attn";
  $results = $wpdb->get_results($sql);
  $attnArr = array();
  foreach($results as $result){
    $attnArr[] = array('key'=>$result->ID,'value'=>$result->value);
  }
  $return = '
  <script>
    //store items as JS object
    var items = [];';
    foreach($itemArr as $itemKey=>$item){
      $return .= 'items.push({"key":'. $itemKey.',"value": "'. $item.'"});';
    }
    $return .= '
    var types      = '. json_encode($typeArr).';
    var attributes = '. json_encode($attArr).';
    var attention  = '. json_encode($attnArr).';
  </script>
  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading" id="headingOne">
        <h4 class="panel-title">
          <a class="accordion-toggle" data-toggle="collapse" href="#collapseOne">Resources</a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel">
        <div class="panel-body">'.$resourceDisp.'</div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading" id="headingTwo">
        <h4 class="panel-title">
          <a class="accordion-toggle" data-toggle="collapse" href="#collapseTwo">Attributes</a>
        </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel">
        <div class="panel-body">'.$attDisp.'</div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading" id="headingTwo">
        <h4 class="panel-title">
          <a class="accordion-toggle" data-toggle="collapse" href="#collapseThree">Attention</a>
        </h4>
      </div>
      <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel">
        <div class="panel-body">'.$attnDisp.'</div>
      </div>
    </div>
  </div>';
  return $return;
}

function entryTicketing($lead,$format='admin'){
  global $wpdb;
  $return = 0;
  $entry_id = $lead['id'];
  $sql = 'select eb_entry_access_code.*, eb_eventToTicket.title, eb_eventToTicket.subtitle,'
          . ' (SELECT EB_event_id FROM `eb_event` where eb_event.ID = eb_eventToTicket.eventID) as event_id'
          . ' from eb_entry_access_code,eb_eventToTicket'
          . ' where eb_eventToTicket.ticketID=eb_entry_access_code.EBticket_id'
          . ' and entry_id = '.$entry_id .' order by eb_eventToTicket.disp_order';

  $results = $wpdb->get_results($sql);
  if($wpdb->num_rows > 0){
    $attnArr = array();
    //determine output format
    if($format=='MAT'){  //return data
      $return = array();
      foreach($results as $result){
        if($result->hidden==0){
          $return[] = array('title'=>$result->title,'subtitle'=>$result->subtitle,'link'=>'https://www.eventbrite.com/e/'.$result->event_id.'?access='.$result->access_code);
        }
      }
    }else{
      //admin format
      $return =  '<table>'
       . '  <thead>'
       . '    <th>Access Code</th>'
       . '    <th></th>'
       . '    <th>Show to Maker</th>'
       . '  </thead>';

      foreach($results as $result){
        $return .=  '<tr>';
        $return .=  '<td><a target="_blank" href="https://www.eventbrite.com/e/'.$result->event_id.'?access='.$result->access_code.'">'.$result->access_code.'</a></td>';
        $return .=  '<td><h4>' . $result->title . '</h4>' .$result->subtitle.'</td>';
        $return .=  '<td><p class="'.($result->hidden==0?'checked':'').'" id="HT'.$result->access_code.'" onclick="hiddenTicket(\''.$result->access_code.'\')">';
        $return .=  '<i class="fa fa-'.($result->hidden==0?'check-':'').'square-o" aria-hidden="true"></i>';

        $return .='</p></td>';
        $return .=  '</tr>';
      }
      $return .= '</table>';
    }
  }

  return $return;
}