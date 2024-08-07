<?php
// Adding Entry Detail and checking for Processing Posts
add_action("gform_entry_detail_content_before", "add_main_text_before", 10, 2);

function add_main_text_before($form, $entry) {
  $mode = empty($_POST['screen_mode']) ? 'view' : $_POST['screen_mode'];
  if ($mode != "view")
    return;
  if (is_null($form['fields']))
    return;


  if (isset($form['form_type']) && $form['form_type'] != 'Default') {
    echo gf_summary_metabox($form, $entry);
    echo gf_collapsible_sections($form, $entry);
  }
  return;
}

// Summary Metabox
function gf_summary_metabox($form, $entry) {

  $entry_id = $entry['id'];

  //find primary photo
  $photo = (isset($entry['22']) ? $entry['22'] : '');
  //starting with ba24, the photo field was set as a multi image
  $photoField = gfapi::get_field($form, '22');
  if (isset($photoField->multipleFiles) && $photoField->multipleFiles) {
    $photoField = json_decode(stripslashes($photo), true);
    $photo = $photoField[0];
  }

  $short_description = (isset($entry['16']) ? $entry['16'] : '');
  $long_description = (isset($entry['21']) ? $entry['21'] : '');
  $project_name = (isset($entry['151']) ? $entry['151'] : '');
  $areyoua = (isset($entry['45']) ? $entry['45'] : '');
  $size_request = (isset($entry['60']) ? $entry['60'] : '');
  $size_request_heightwidth = ((isset($entry['345']) && strlen($entry['345']) > 0) ? $entry['345'] . ' X ' : '') .
    ((isset($entry['344']) && strlen($entry['344']) > 0) ? $entry['344'] : '');
  $size_request_other = (isset($entry['61']) ? $entry['61'] : '');

  //starting BA23 we are now using exhibit type instead of form_type
  $exhibit_types = array_filter($entry, function ($key) {
    return strpos($key, '339.') === 0;
  }, ARRAY_FILTER_USE_KEY);

  $exhibit_types = implode(",", array_filter($exhibit_types));
  $entry_form_type = ($exhibit_types != '' ? $exhibit_types : $form['form_type']);

  $entry_form_status = (isset($entry['303']) ? $entry['303'] : '');
  $wkey = (isset($entry['27']) ? $entry['27'] : '');
  $vkey = (isset($entry['32']) ? $entry['32'] : '');

  $makerfirstname1 = (isset($entry['160.3']) ? $entry['160.3'] : '');
  $makerlastname1 = (isset($entry['160.6']) ? $entry['160.6'] : '');
  $makerPhoto1 = (isset($entry['217']) ? $entry['217'] : '');
  $makerfirstname2 = (isset($entry['158.3']) ? $entry['158.3'] : '');
  $makerlastname2 = (isset($entry['158.6']) ? $entry['158.6'] : '');
  $makerPhoto2 = (isset($entry['224']) ? $entry['224'] : '');
  $makerfirstname3 = (isset($entry['155.3']) ? $entry['155.3'] : '');
  $makerlastname3 = (isset($entry['155.6']) ? $entry['155.6'] : '');
  $makerPhoto3 = (isset($entry['223']) ? $entry['223'] : '');
  $makerfirstname4 = (isset($entry['156.3']) ? $entry['156.3'] : '');
  $makerlastname4 = (isset($entry['156.6']) ? $entry['156.6'] : '');
  $makerPhoto4 = (isset($entry['222']) ? $entry['222'] : '');
  $makerfirstname5 = (isset($entry['157.3']) ? $entry['157.3'] : '');
  $makerlastname5 = (isset($entry['157.6']) ? $entry['157.6'] : '');
  $makerPhoto5 = (isset($entry['220']) ? $entry['220'] : '');
  $makerfirstname6 = (isset($entry['159.3']) ? $entry['159.3'] : '');
  $makerlastname6 = (isset($entry['159.6']) ? $entry['159.6'] : '');
  $makerPhoto6 = (isset($entry['221']) ? $entry['221'] : '');
  $makerfirstname7 = (isset($entry['154.3']) ? $entry['154.3'] : '');
  $makerlastname7 = (isset($entry['154.6']) ? $entry['154.6'] : '');
  $makerPhoto7 = (isset($entry['219']) ? $entry['219'] : '');
  $makergroupname = (isset($entry['109']) ? $entry['109'] : '');
  $makerGroupPhoto = (isset($entry['111']) ? $entry['111'] : '');
  $suppToken  = (isset($entry['fg_easypassthrough_token']) ? $entry['fg_easypassthrough_token'] : '');

  $field55 = RGFormsModel::get_field($form, '55');
  $whatareyourplansvalues = (isset($field55['choices']) ? $field55['choices'] : '');

  $main_description = '';
  // Check if we are loading the public description or a short description
  if (isset($long_description) && $long_description != '') {
    $main_description = $long_description;
  } else if (isset($short_description)) {
    $main_description = $short_description;
  }

  //pull faireID
  global $wpdb;
  $faire = $wpdb->get_var('select faire from wp_mf_faire where find_in_set (' . $form['id'] . ', wp_mf_faire.form_ids) > 0');

  //is there a parent entry?
  $parent_entry_ID = $entry['gpnf_entry_parent'];
  $parent_form = ($parent_entry_ID != '' ? $entry['gpnf_entry_parent_form'] : '');

  //starting BA23 we added a new field of final Weekend
  $finalWeekend = array_filter($entry, function ($key) {
    return strpos($key, '879.') === 0;
  }, ARRAY_FILTER_USE_KEY);

  $finalWeekend = implode("<br/>", array_filter($finalWeekend));

  $return = '

<table cellspacing="0" class="gf-entry-summary">
		<tr>
			<th colspan="2" style="text-align: left;" id="header">
				<h1>' . esc_html($project_name) . '</h1>' .
    ($parent_entry_ID != '' ? '<a target="_blank" href="/wp-admin/admin.php?page=gf_entries&view=entry&id=' . $parent_form . '&lid=' . $parent_entry_ID . '" target="_blank"><input class="button button-large button-primary" style="text-align:center" value="Parent Entry" /></a>' : '') .
    '</th>
		</tr>
	
		<tr>
			<td class="entry-image" valign="top">
				<a href="' . $photo . '" ><img width="100%" src="' . legacy_get_fit_remote_image_url($photo, 400, 400) . '" alt="" /></a>
			</td>
			<td valign="top">
				<table class="entry-overview">
					<tr>
						<td colspan="2">
							<p>' . stripslashes(nl2br($main_description, "\n")) . '</p>
						</td>
					</tr>
					<tr>
						<td valign="top"><strong>Type:</strong></td>
						<td valign="top">' . esc_attr(ucfirst($entry_form_type)) . '</td>
					</tr>
					<tr>
						<td valign="top"><strong>Status:</strong></td>
						<td valign="top">' . esc_attr($entry_form_status) . '</td>
					</tr>' .
    ($suppToken != '' && $form['form_type'] == 'Master'
      ?
      '<tr>
            <td valign="top"><strong>Supplemental Token ID:</strong></td>
						<td valign="top">' . $suppToken . '</td>
          </tr>'
      : '')
    . '<tr>
						<td valign="top"><strong>Website:</strong></td>
						<td valign="top"><a href="' . esc_url($wkey) . '" target="_blank">' . esc_url($wkey) . '</a></td>
					</tr>
					<tr>
						<td valign="top"><strong>Video:</strong></td>
						<td>' . ((isset($vkey)) ? '<a href="' . esc_url($vkey) . '" target="_blank">' . esc_url($vkey) . '</a><br/>' : '') . '</td>
					</tr>
					<tr>
						<td valign="top"><strong>Maker Names:</strong></td>
						<td valign="top">' . (!empty($makergroupname) ? $makergroupname . '(Group)</br>' : '');

  //loop thru all 7 maker photos
  for ($x = 1; $x <= 7; $x++) {
    if (!empty(${"makerPhoto_$x"})) {
      $return .= '<a href="' . ${"makerPhoto_$x"} . '" ><img width="30px" src="' . legacy_get_resized_remote_image_url(${"makerPhoto_$x"}, 30, 30) . '" alt="" /></a>';
    }
    $return .= (!empty(${"makerfirstname$x"}) ? ${"makerfirstname$x"} . ' ' . ${"makerlastname$x"} . '</br>' : '');
  }

  if (!empty($makerGroupPhoto)) {
    $return .= 'Group Photo<br/>
                    <a href="' . $makerGroupPhoto . '" >
                    <img width="30px" src="' . legacy_get_resized_remote_image_url($makerGroupPhoto, 30, 30) . '" alt="" />
                    </a>';
  }

  $return .= (!empty($makerfirstname7) ? $makerfirstname7 . ' ' . $makerlastname7 . '</br>' : '');
  $return .= '
            </td>
					</tr>
          <tr>
						<td valign="top"><strong>We are (a/an)...:</strong></td>
						<td>' . ((isset($areyoua)) ? $areyoua : '') . '</td>
					</tr>
					<tr>
						<td valign="top"><strong>What are your plans:</strong></td>
						<td valign="top">';

  if (is_array($whatareyourplansvalues)) {
    for ($i = 0; $i < count($whatareyourplansvalues); $i++) {
      $return .= ((!empty($entry['55.' . $i])) ? $entry['55.' . $i] . '<br />' : '');
    }
  }
  $return .= '
            </td>
					</tr>
          <tr>
            <td valign="top"><strong>Fee Indicator:</strong></td>
						<td>' . ((isset($entry[434])) ? $entry[434] : 'No') . '</td>
          </tr>
          <tr>
            <td valign="top"><strong>CM Indicator:</strong></td>
						<td>' . ((isset($entry[376])) ? $entry[376] : 'No') . '</td>
          </tr>
					<tr>
						<td valign="top"><strong>Size Request:</strong></td>
						<td>
              ' . ((isset($size_request)) ? $size_request : 'Not Filled out') .
    ((isset($size_request_heightwidth) && $size_request_heightwidth != '') ? ' - ' . $size_request_heightwidth : '') .
    ((strlen($size_request_other) > 0) ? ' <br />Comment: ' . $size_request_other : '') . '
						</td>
					</tr>
          <tr>
            <td valign="top"><strong>Schedule/Location:</strong></td>
            <td>' . display_schedule($form['id'], $entry, 'summary') . '</td>
          </tr>
          <tr>
            <td valign="top"><strong>Final Weekend:</strong></td>
            <td>' . $finalWeekend . '</td>        
          </tr>
          <tr>
            <td>
              <a target="_blank" href="/maker-sign/' . $entry_id . '/' . $faire . '/"><input class="button button-large button-primary" style="text-align:center" value="Download Maker Sign" /></a>
            </td>
            <td>
              <a href="' . admin_url('admin-post.php?action=createCSVfile&exForm=' . $form['id'] . '&exEntry=' . $entry_id) . '"><input class="button button-large button-primary"  style="text-align:center" value="Export All Fields" /></a>
            </td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
<table cellspacing="0" class="gf-entry-summary">
		<tr>
			<td>
        <label >Email Note To:</label><br />';

  $emailto1 = array(
    "Dale Dougherty" => "dale@make.co",
    "Siana Alcorn" => "siana@make.co",
    "Gillian Mutti" => "gillian@make.co",
    "Jennifer Blakeslee" => "jennifer@make.co"
  );
  $emailto2 = array(
    "Webmaster" => "webmaster@make.co",
    "Editors" => "editor@make.co",
    "Rob Bullington" => "rob@make.co",
    "Keith Hammond" => "keith@make.co",
    "Katie Kunde" => "katie@make.co"
  );
  $emailtoaliases = array(
    "Maker Relations" => "makers@make.co",
    "PR" => "pr@make.co",
    //"Sales" => "sales@makerfaire.com",
    //"Sustainability" => "sustainability@makerfaire.com",
    //"Speakers" => "speakers@makerfaire.com"
  );

  $return .= '<div style="float:left">';
  foreach ($emailtoaliases as $name => $email) {
    $return .= '<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="' . $email . '" />'
      . '<span title="' . $email . '"><strong>' . $name . '</strong></span><br />';
  }
  $return .= '
				</div>
			  <div style="float:left">';
  foreach ($emailto1 as $name => $email) {
    $return .= '<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="' . $email . '" />'
      . '<span title="' . $email . '">' . $name . '</span><br />';
  }
  $return .= '
				</div>            
			  <div style="float:left">';
  foreach ($emailto2 as $name => $email) {
    $return .= '<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="' . $email . '" />'
      . '<span title="' . $email . '">' . $name . '</span><br />';
  }
  $return .= '
				</div>
            <div class="clear"></div><br/>Enter Email: <input type="email" placeholder="example@make.co" name="otherEmail" size="40" />
			</td>
			<td style="vertical-align: top; padding: 10px;">
        <textarea	name="new_note_sidebar"	style="width: 90%; height: 240px;" cols=""	rows=""></textarea>';
  $note_button = '<input type="button" name="add_note_sidebar" value="' . __('Add Note', 'gravityforms') . '" class="button" style="width:auto;padding-bottom:2px;" onclick="updateMgmt(\'add_note_sidebar\');"/>';
  $return .= apply_filters('gform_addnote_button', $note_button);
  $return .= '<span class="updMsg add_note_sidebarMsg"></span>
			</td>
		</tr>
</table>';

  return $return;
}

//end function

function gf_collapsible_sections($form, $entry) {
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
  $entry_id = $entry['id'];

  $makerfirstname1 = (isset($entry['160.3']) ? $entry['160.3'] : '');
  $makerlastname1 = (isset($entry['160.6']) ? $entry['160.6'] : '');

  $makerfirstname2 = (isset($entry['158.3']) ? $entry['158.3'] : '');
  $makerlastname2 = (isset($entry['158.6']) ? $entry['158.6'] : '');

  $makerfirstname3 = (isset($entry['155.3']) ? $entry['155.3'] : '');
  $makerlastname3 = (isset($entry['155.6']) ? $entry['155.6'] : '');

  $makerfirstname4 = (isset($entry['156.3']) ? $entry['156.3'] : '');
  $makerlastname4 = (isset($entry['156.6']) ? $entry['156.6'] : '');

  $makerfirstname5 = (isset($entry['157.3']) ? $entry['157.3'] : '');
  $makerlastname5 = (isset($entry['157.6']) ? $entry['157.6'] : '');

  $makerfirstname6 = (isset($entry['159.3']) ? $entry['159.3'] : '');
  $makerlastname6 = (isset($entry['159.6']) ? $entry['159.6'] : '');

  $makerfirstname7 = (isset($entry['154.3']) ? $entry['154.3'] : '');
  $makerlastname7 = (isset($entry['154.6']) ? $entry['154.6'] : '');

  $contactFirstName = (isset($entry['96.3']) ? $entry['96.3'] : '');
  $contactLastName  = (isset($entry['96.6']) ? $entry['96.6'] : '');

  //email fields
  $emailArray = array();

  if (isset($entry['98']) && $entry['98'] != '')
    $emailArray[$entry['98']]['Contact'] = $contactFirstName . ' ' . $contactLastName;
  if (isset($entry['161']) && $entry['161'] != '')
    $emailArray[$entry['161']]['Maker 1'] = $makerfirstname1 . ' ' . $makerlastname1;
  if (isset($entry['162']) && $entry['162'] != '')
    $emailArray[$entry['162']]['Maker 2'] = $makerfirstname2 . ' ' . $makerlastname2;
  if (isset($entry['167']) && $entry['167'] != '')
    $emailArray[$entry['167']]['Maker 3'] = $makerfirstname3 . ' ' . $makerlastname3;
  if (isset($entry['166']) && $entry['166'] != '')
    $emailArray[$entry['166']]['Maker 4'] = $makerfirstname4 . ' ' . $makerlastname4;
  if (isset($entry['165']) && $entry['165'] != '')
    $emailArray[$entry['165']]['Maker 5'] = $makerfirstname5 . ' ' . $makerlastname5;
  if (isset($entry['164']) && $entry['164'] != '')
    $emailArray[$entry['164']]['Maker 6'] = $makerfirstname6 . ' ' . $makerlastname6;
  if (isset($entry['163']) && $entry['163'] != '')
    $emailArray[$entry['163']]['Maker 7'] = $makerfirstname7 . ' ' . $makerlastname7;

  //for supplement forms, let's see if there is a field set to pull in email
  $return = get_value_by_label('contact-email', $form, array());
  if (isset($return['id']) && isset($entry[$return['id']])) {
    $emailArray[$entry[$return['id']]]['contact-email'] = $entry[$return['id']];
  }


  foreach ($form['fields'] as $field) {
    $fieldData[$field['id']] = $field;
  }

  $data = array(
    'content' => array(11, 16, 320, 321, 66, 67, 293),
    'logistics' => array(60, 344, 345, 61, 62, 347, 348, 64, 65, 68, 69, 70, 71, 72, 73, 74, 75, 76, 803, 806, 805),
    'additional' => array(123, 130, 287, 134, 37, 38, 41),
    'images' => array(22, 65, 111, 122, 217, 224, 223, 222, 220, 221, 219, 878),
    'imagesOver' => array(324, 334, 330, 338, 333, 337, 332, 336, 331, 335)
  );
  //additional Entries
  $addEntries = '<table width="100%">
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

  $addEntriesCnt = 0;
  foreach ($emailArray as $key => $email) {
    $results = $wpdb->get_results('SELECT  *,
                                            (SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = 151 ) as projectName,
                                            (SELECT meta_value FROM wp_gf_entry_meta detail2 WHERE detail2.entry_id = wp_gf_entry_meta.entry_id AND meta_key = 303 ) as status,
                                            (SELECT status FROM wp_gf_entry WHERE wp_gf_entry.id = wp_gf_entry_meta.entry_id) as lead_status
                                      FROM wp_gf_entry_meta
                                      JOIN wp_gf_form on wp_gf_form.id = wp_gf_entry_meta.form_id
                                     WHERE meta_value = "' . $key . '"' .
      '  AND entry_id != ' . $entry_id . '
                                  GROUP BY entry_id
                                  ORDER BY entry_id');

    foreach ($results as $addData) {
      $outputURL = admin_url('admin.php') . "?page=gf_entries&view=entry&id=" . $addData->form_id . '&lid=' . $addData->entry_id;
      $addEntriesCnt++;
      $addEntries .= '<tr>';

      //only display the first instance of the email
      foreach ($email as $typeKey => $typeData) {
        $name = $typeKey;
        $type = $typeData;
        if ($name != '')
          break;
      }
      $addEntries .= '<td>' . $type . '</td>';
      $addEntries .= '<td>' . $name . '</td>';
      $addEntries .= '<td><a target="_blank" href="' . $outputURL . '">' . $addData->entry_id . '</a></td>'
        . '<td>' . $addData->projectName . '</td>'
        . '<td>' . $addData->title . '</td>'
        . '<td>' . ($addData->lead_status == 'active' ? $addData->status : ucwords($addData->lead_status)) . '</td>'
        . '</tr>';
    }
  }
  $addEntries .= '</table>';

  //form data
  $addFormsData = getmetaData($entry_id);
  $pmtFormsData = getmetaData($entry_id, 'payments');
  $return = '
  <div id="tabs" class="adminEntrySummary">
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation"><a href="#tabs-1" aria-controls="tabs-1" role="tab" data-toggle="tabs-1"><br/>Content</a></li>
      <li role="presentation"><a href="#tabs-2" aria-controls="tabs-2" role="tab" data-toggle="tabs-2">Logistics/<br/>Production</a></li>
      <li role="presentation"><a href="#additional" aria-controls="additional" role="tab" data-toggle="additional">Additional<br/>Information</a></li>
      <li role="presentation"><a href="#addForms" aria-controls="addForms" role="tab" data-toggle="addForms">Additional<br/>Forms (' . $addFormsData[1] . ')</a></li>
      <li role="presentation"><a href="#payments" aria-controls="payments" role="tab" data-toggle="payments"><br/>Payments (' . $pmtFormsData[1] . ')</a></li>
      <li role="presentation"><a href="#tabs-3" aria-controls="tabs-3" role="tab" data-toggle="tabs-3">Other<br/>Entries (' . $addEntriesCnt . ')</a></li>
      <li role="presentation"><a href="#images" aria-controls="images" role="tab" data-toggle="images">Images/<br/>Downloads</a></li>
      <li role="presentation" aria-selected="true"><a href="#resources" aria-controls="resources" role="tab" data-toggle="resources"><br/>Resources</a></li>
      <li role="presentation" aria-selected="true"><a href="#ticketing" aria-controls="ticketing" role="tab" data-toggle="ticketing"><br/>Ticketing</a></li>
    </ul>
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane" id="tabs-1">
        ' . displayContent($data['content'], $entry, $fieldData) . '
      </div>
      <div role="tabpanel" class="tab-pane" id="tabs-2">
        ' . displayContent($data['logistics'], $entry, $fieldData) .
    '
      </div>
      <div role="tabpanel" class="tab-pane" id="additional">
        ' . displayContent($data['additional'], $entry, $fieldData) . '
      </div>

      <div role="tabpanel" class="tab-pane" id="addForms">
        ' . $addFormsData[0] . '
      </div>
      <div role="tabpanel" class="tab-pane" id="payments">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            ' . $pmtFormsData[0] . '
          </div>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane" id="tabs-3">
        ' . $addEntries . '
      </div>
      <div role="tabpanel" class="tab-pane"  id="images">
        ' . displayContent($data['images'], $entry, $fieldData) . '
        ' . displayContent($data['imagesOver'], $entry, $fieldData) . '
      </div>

      <div role="tabpanel" class="tab-pane"  id="resources">
        <div class="entry-resource">
          ' . entryResources($entry) . '
        </div>
      </div>

      <div role="tabpanel" class="tab-pane"  id="ticketing">
        <div class="panel-group">';

  $ticketing = entryTicketing($entry);
  if ($ticketing) {
    $return .= $ticketing;
  } else {
    $EBcount = $wpdb->get_var("select count(*) from eb_event, wp_mf_faire where wp_mf_faire_id = wp_mf_faire.id and "
      . "FIND_IN_SET (" . $entry['form_id'] . ",wp_mf_faire.form_ids)> 0");
    if ($EBcount >= 1) {
      $return .= '
            <div id="noTickets">
              No Access Codes found for this entry.<br/>
              
              <input type="button" name="" value="Generate Access Codes" class="button" 
                style="width:auto;padding-bottom:2px;" onclick="ebAccessTokens()" />
              <br/>
              
            </div>

            <div style="display:none" id="createTickets">
              <i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i>
              <span class="sr-only">Loading...</span>
              <i>Please be patient.  This may take a while to complete</i>
            </div>';
    } else {
      //no eventbrite event set up
      $return .= '
                  <div id="noTickets">
                    I\'m sorry.  There is not an Eventbrite event set up for this faire.
                  </div>';
    }
  }
  $return .= '
        </div>
      </div>
    </div> <!-- .tab-content -->
  </div>';

  return $return;
}

function displayContent($content, $entry, $fieldData, $display = 'table') {
  global $display_empty_fields;
  $return = '';
  if ($display === 'table')
    $return .= '<table>';
  $form = GFAPI::get_form($entry['form_id']);

  foreach ($content as $fieldID) {
    if (isset($fieldData[$fieldID])) {
      $field = $fieldData[$fieldID];
      $value = RGFormsModel::get_lead_field_value($entry, $field);

      if ($field->type != 'fileupload') {
        $display_value = GFCommon::get_lead_field_display($field, $value, $entry['currency']);
        $display_value = apply_filters('gform_entry_field_value', $display_value, $field, $entry, $form);
      } else {
        //display images in a grid
        if ($value != '') {
          if ($field->multipleFiles) {
            if (!empty($value)) {
              $array = json_decode($value, true);
              $display_value = '';
              foreach ($array as $file) {
                $path = pathinfo($file);
                $ext = strtolower($path['extension']);
                $supported_image = array('gif', 'jpg', 'jpeg', 'png');
                if (in_array($ext, $supported_image)) {
                  $displayItem = '<img width="100px" src="' . legacy_get_resized_remote_image_url($file, 100, 100) . '" alt="" />';
                } else {
                  $displayItem = $path['basename'];
                }
                $display_value .= '<a href="' . $file . '" target="_blank">' . $displayItem . '</a><br/>';
              }
            }
          } else {
            $path = pathinfo($value);
            $ext = (isset($path['extension']) ? strtolower($path['extension']) : '');
            $supported_image = array('gif', 'jpg', 'jpeg', 'png');
            if (in_array($ext, $supported_image)) {
              $displayItem = '<img width="100px" src="' . legacy_get_resized_remote_image_url($value, 100, 100) . '" alt="" />';
            } else {
              $displayItem = $path['basename'];
            }
            $display_value = '<a href="' . $value . '" target="_blank">' . $displayItem . '</a>';
          }
        } else {
          $display_value = '';
        }
      }


      if ($display_empty_fields || !empty($display_value) || $display_value === '0') {
        $display_value = empty($display_value) && $display_value !== '0' ? '&nbsp;' : $display_value;
        if ($display === 'table') {
          $content = '
                  <tr>
                     <td colspan="2" class="entry-view-field-name">' . esc_html(GFCommon::get_label($field)) . '</td>
                  </tr>
                  <tr>
                     <td colspan="2" class="entry-view-field-value">' . $display_value . '</td>
                  </tr>';
        } else {
          $content = '<div style="' . ($field->cssClass === '' ? 'float:left;' : '') . 'padding:5px;margin:10px" class="' . $field['cssClass'] . '">' . esc_html(GFCommon::get_label($field)) . '<br/>' . $display_value . '</div>';
        }
        $content = apply_filters('gform_field_content', $content, $field, $value, $entry['id'], $form['id']);
        $return .= $content;
      }
    }
  }
  if ($display === 'table')
    $return .= '</table>';
  if ($display === 'grid')
    $return .= '<div class="clear"></div>';

  return $return;
}

function getmetaData($entry_id, $type = '') {
  $return = '';
  $metaData = mf_get_form_meta('entry_id', $entry_id);


  $formCount = 0;
  foreach ($metaData as $data) {
    $entry = GFAPI::get_entry($data->entry_id);
    //check if entry-id is valid
    if (is_array($entry)) {  //display entry data
      $formPull = GFAPI::get_form($data->form_id);
      if (!isset($formPull['form_type']))
        $formPull['form_type'] = '';
      /*
             * determine if we should display form data
             * If type = blank, display all forms but Payment type
             * If type = payments, only display forms with type of Payment
             */
      if (($type == '' && $formPull['form_type'] != 'Payment') ||
        ($type == 'payments' && $formPull['form_type'] == 'Payment') ||
        ($type == 'payments' && $formPull['form_type'] == 'Invoice')
      ) {
        $formCount++;
        $formTable = '<table>';

        $count = 0;
        $field_count = sizeof($formPull['fields']);
        $has_product_fields = false;
        foreach ($formPull['fields'] as $formFields) {
          $gwreadonly_enable = (isset($formFields['gwreadonly_enable']) ? $formFields['gwreadonly_enable'] : 0);
          //exclude page breaks and the entry fields used to verify the entry
          // and the display only fields from the additional forms
          if (
            $formFields['type'] != 'page' &&
            $formFields['inputName'] != 'entry-id' &&
            $formFields['inputName'] != 'contact-email' &&
            $gwreadonly_enable != 1
          ) {

            $display_empty_fields = false;

            switch (RGFormsModel::get_input_type($formFields)) {
              case 'section':
                if (!GFCommon::is_section_empty($formFields, $formPull, $entry) || $display_empty_fields) {
                  $count++;
                  $is_last = $count >= $field_count ? true : false;
                  $formTable .= '
                  <tr>
                    <td colspan="2" class="entry-view-section-break' . ($is_last ? ' lastrow' : '') . '">' . esc_html(GFCommon::get_label($formFields)) . '</td>
                  </tr>';
                }
                break;

              case 'captcha':
              case 'html':
              case 'password':
              case 'page':
                //ignore captcha, html, password, page field
                break;

              default:
                //ignore product fields as they will be grouped together at the end of the grid
                if (GFCommon::is_product_field($formFields->type)) {
                  $has_product_fields = true;
                  //continue 2;
                  break;
                }

                $value = RGFormsModel::get_lead_field_value($entry, $formFields);
                $display_value = GFCommon::get_lead_field_display($formFields, $value, $entry['currency']);
                $display_value = apply_filters('gform_entry_field_value', $display_value, $formFields, $entry, $formPull);

                if ($display_empty_fields || !empty($display_value) || $display_value === '0') {
                  $display_value = empty($display_value) && $display_value !== '0' ? '&nbsp;' : $display_value;

                  $content = '
                    <tr>
                      <td colspan="2" class="entry-view-field-name">' . esc_html(GFCommon::get_label($formFields)) . '</td>
                    </tr>
                    <tr>
                      <td colspan="2" class="entry-view-field-value">' . $display_value . '</td>
                    </tr>';

                  $content = apply_filters('gform_field_content', $content, $formFields, $value, $entry['id'], $formPull['id']);
                  $formTable .= $content;
                }
                break;
            }
          }
        }
        if ($has_product_fields) {
          $format = 'html';
          $formTable .= GFCommon::get_submitted_pricing_fields($formPull, $entry, $format);
        }

        //display any payment notes
        if (isset($data->lead_id)) {
          $notes = RGFormsModel::get_lead_notes($data->lead_id);
          foreach ($notes as $note) {
            if ($note->user_name == 'PayPal') {
              $formTable .= '<tr><td colspan="2" class="entry-view-field-name">PayPal</td></tr>';
              $formTable .= '<tr><td colspan="2" class="entry-view-field-value">' .
                esc_html(GFCommon::format_date($note->date_created, false)) . '<br/>' .
                $note->value . '</td>' .
                '</tr>';
            }
          }
        }
        $formTable .= '</table>';

        //let's set up each form as it's own collapsible section
        $return .= '<div class="panel-heading" id="headingOne">' .
          '<div class="row">' .
          '<div class="col-md-9">'
          . '<h3 class="panel-title">' . $formPull['title'] . '</h3>';
        if (isset($entry['payment_status']) && $entry['payment_status'] != NULL) {
          $return .= '<br/>Status: ' . $entry['payment_status'] .
            ($entry['payment_amount'] != NULL ? ' (' . GFCommon::to_money($entry['payment_amount'], $entry['currency']) . ')' : '') .
            ($entry['payment_date'] != NULL ? ' - ' . $entry['payment_date'] : '');
        }
        $return .= '</div>' .
          '<div class="col-md-3">' .
          '<button type="button"   class="btn btn-info" data-toggle="collapse" data-target="#entr_' . $entry['id'] . '">Show/Hide Form Data</button>' .
          '</div>' .
          '</div>' . //close .row
          '<hr/>' .
          '</div>' . //close .panel-heading
          '<div id="entr_' . $entry['id'] . '" class="panel-collapse collapse" role="tabpanel">' .
          '<div class="panel-body">' . $formTable . '</div>' .
          '</div>'; //close panel-collapse
      }
    }
  }
  return array($return, $formCount);
}

// this function returns all entries with a
// meta key set to a certain meta value

function mf_get_form_meta($meta_key, $meta_value) {
  global $wpdb;
  $table_name = 'wp_gf_entry_meta';
  $entry = GFAPI::get_entry($meta_value);


  //retrieve the most current records for each additional form/entry id/form_id combination
  $results = $wpdb->get_results($sql = $wpdb->prepare("SELECT * FROM {$table_name}
              WHERE meta_value=%d AND meta_key=%s
              order by id desc", $meta_value, $meta_key));
  return $results;
}

//retrieves resource and attribute information for the entry
function entryResources($entry) {
  $rmt_data       = GFRMTHELPER::rmt_get_entry_data($entry['id']);
  $rmt_table_data = GFRMTHELPER::rmt_table_data();

  $itemArr = $rmt_table_data['resource_categories'];
  $typeArr = $rmt_table_data['resources'];
  $attArr  = $rmt_table_data['attItems'];
  $attnArr = $rmt_table_data['attnItems'];

  //display resource data
  $resourceDisp = '<table id="resTable"><thead>'
    . ' <tr>'
    . ' <th>Lock</th>'
    . ' <th>Category</th>'
    . ' <th>Resource</th>'
    . ' <th>Qty</th>'
    . ' <th>Comments</th>'
    . ' <th>User</th>'
    . ' <th>Last Updated</th>'
    . ' <th><p class="addIcon" onclick="addRow(\'resource\','.$entry['id'].')"><i class="fa fa-circle-plus fa-lg"></i></p></th>'
    . ' </tr></thead>';
  $return = '';
  $resourceDisp .= '<tbody>';
  foreach ($rmt_data['resources'] as $data) {
    $resourceDisp .= '<tr id="resRow' . $data['id'] . '">'
      . ' <td class="lock"><span class="lockIcon" onclick="resAttLock(\'#resRow' . $data['id'] . '\',' . $data['lock'] . ')">' . ($data['lock'] == 1 ? '<i class="fas fa-lock fa-lg"></i>' : '<i class="fas fa-lock-open fa-lg"></i>') . '</span></td>'
      . ' <td id="resitem_' . $data['id'] . '" data-itemID="' . $data['category_id'] . '">' . $data['category'] . '</td>'
      . ' <td id="restype_' . $data['id'] . '" data-typeID="' . $data['resource_id'] . '" class="editable dropdown">' . $data['resource'] . '</td>'
      . ' <td id="resqty_' . $data['id'] . '"  class="editable numeric">' . $data['qty'] . '</td>'
      . ' <td id="rescomment_' . $data['id'] . '" class="editable textAreaEdit">' . $data['comment'] . '</td>'
      . ' <td id="resuser_' . $data['id'] . '">' . $data['user'] . '</td>'
      . ' <td id="resdateupdate_' . $data['id'] . '">' . $data['last_updated']
      . '</td>'
      . ' <td class="delete"><span class="delIcon" onclick="resAttDelete(\'resRow' . $data['id'] . '\', \'' . $entry['id'] . '\')"><i class="fa fa-circle-minus fa-lg"></i></span></td>'
      . ' </tr>';
  }
  $resourceDisp .= '</tbody>';
  $resourceDisp .= '</table>';

  //display attribute data    
  $attDisp = '<table id="attTable"><thead><tr>'
    . ' <th>Attribute</th>'
    . ' <th>Value</th>'
    . ' <th>Comment</th>'
    . ' <th>User</th>'
    . ' <th>Last Updated</th>'
    . ' <th><span class="addIcon" onclick="addRow(\'attribute\','.$entry['id'].')"><i class="fa fa-circle-plus fa-lg"></i></span></th></tr></thead>';
  $attDisp .= '<tbody>';

  foreach ($rmt_data['attributes'] as $data) {
    $attDisp .= '<tr id="attRow' . $data['id'] . '">'
      . ' <td id="attcategory_' . $data['id'] . '">' . $data['attribute'] . '</td>'
      . ' <td id="attvalue_' . $data['id'] . '" class="editable textAreaEdit">' . $data['value'] . '</td>'
      . ' <td id="attcomment_' . $data['id'] . '" class="editable textAreaEdit">' . $data['comment'] . '</td>'
      . ' <td id="attuser_' . $data['id'] . '">' . $data['user'] . '</td>'
      . ' <td id="attdateupdate_' . $data['id'] . '">' . $data['last_updated'] . '</td>'
      . ' <td class="delete"><span class="delIcon" onclick="resAttDelete(\'attRow' . $data['id'] . '\')"><i class="fa fa-circle-minus fa-lg"></i></span></td></tr>';
  }
  $attDisp .= '</tbody>';
  $attDisp .= '</table>';

  //build attention section        
  $attnDisp = '<table id="attnTable"><thead><tr>'
    . ' <th>Attention</th>'
    . ' <th>Comment</th>'
    . ' <th>User</th>'
    . ' <th>Last Updated</th>'
    . ' <th><span onclick="addRow(\'attention\','.$entry['id'].')"><i class="fa fa-circle-plus fa-lg"></i></span></th></tr></thead>';
  $attnDisp .= '<tbody>';
  
  foreach ($rmt_data['attention'] as $data) {
    $attnDisp .= '<tr id="attnRow' . $data['id'] . '">'
      . ' <td id="attnvalue_' . $data['id'] . '">' . $data['attention'] . '</td>'
      . ' <td id="attncomment_' . $data['id'] . '" class="editable textAreaEdit">' . $data['comment'] . '</td>'
      . ' <td id="attnuser_' . $data['id'] . '">' . $data['user'] . '</td>'
      . ' <td id="attndateupdate_' . $data['id'] . '">' . $data['last_updated'] . '</td>'
      . ' <td><span onclick="resAttDelete(\'attnRow' . $data['id'] . '\')"><i class="fa fa-circle-minus fa-lg"></i></span></td></tr>';
  }
  $attnDisp .= '</tbody>';
  $attnDisp .= '</table>';

  // this output won't work for vue, which will get these same values from the rmt object
  $return = '
  <script>
    //store items as JS object
    var items = [];';
  foreach ($itemArr as $itemKey => $item) {
    $return .= 'items.push({"key":' . $itemKey . ',"value": "' . $item . '"});';
  }
  $return .= '
    var types      = ' . json_encode($typeArr) . ';
    var attributes = ' . json_encode($attArr) . ';
    var attention  = ' . json_encode($attnArr) . ';
  </script>



  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading" id="headingOne">
        <h4 class="panel-title">
          Resources
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse" role="tabpanel">
        <div class="panel-body">' . $resourceDisp . '</div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading" id="headingTwo">
        <h4 class="panel-title">
          Attributes
        </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse" role="tabpanel">
        <div class="panel-body">' . $attDisp . '</div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading" id="headingTwo">
        <h4 class="panel-title">
          Attention
        </h4>
      </div>
      <div id="collapseThree" class="panel-collapse" role="tabpanel">
        <div class="panel-body">' . $attnDisp . '</div>
      </div>
    </div>
  </div>';

  return $return;
}

function entryTicketing($entry, $format = 'admin') {
  global $wpdb;
  $return = '';
  $entry_id = $entry['id'];

  $sql = 'select eb_entry_access_code.*, eb_eventToTicket.title, eb_eventToTicket.subtitle,'
    . ' (SELECT EB_event_id FROM `eb_event` where eb_event.ID = eb_eventToTicket.eventID) as event_id'
    . ' from eb_entry_access_code,eb_eventToTicket'
    . ' where eb_eventToTicket.ID=eb_entry_access_code.EBticket_id'
    . ' and entry_id = ' . $entry_id . ' order by eb_eventToTicket.disp_order';

  $results = $wpdb->get_results($sql);
  if ($wpdb->num_rows > 0) {
    $attnArr = array();
    //determine output format
    if ($format == 'MAT') {  //return data
      $return = array();
      foreach ($results as $result) {
        if ($result->hidden == 0) {
          $return[] = array('title' => $result->title, 'subtitle' => $result->subtitle, 'link' => 'https://www.eventbrite.com/e/' . $result->event_id . '?discount=' . $result->access_code);
        }
      }
    } else {
      //admin format
      $return = '<table>'
        . '  <thead>'
        . '    <th>Access Code</th>'
        . '    <th></th>'
        . '    <th>Show to Maker</th>'
        . '  </thead>';

      foreach ($results as $result) {
        $return .= '<tr>';
        $return .= '<td><a target="_blank" href="https://www.eventbrite.com/e/' . $result->event_id . '?discount=' . $result->access_code . '">' . $result->access_code . '</a></td>';
        $return .= '<td><h4>' . $result->title . '</h4>' . $result->subtitle . '</td>';
        $return .= '<td><p class="' . ($result->hidden == 0 ? 'checked' : '') . '" id="HT' . $result->access_code . '" onclick="hiddenTicket(\'' . $result->access_code . '\')">';
        $return .= '<i class="' . ($result->hidden == 0 ? 'fa fa-check-' : '') . 'far fa-square-o" aria-hidden="true"></i>';

        $return .= '</p></td>';
        $return .= '</tr>';
      }
      $return .= '</table>';
    }
  }

  return $return;
}
