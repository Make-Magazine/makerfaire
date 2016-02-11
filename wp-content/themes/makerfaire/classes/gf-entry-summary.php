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
function gf_summary_metabox($form, $lead)
{

$jdb_success = gform_get_meta( $lead['id'], 'mf_jdb_sync');

if ( $jdb_success == '' ) {
	$jdb_fail = gform_get_meta( $lead['id'], 'mf_jdb_sync_fail', true );
	$jdb      = '[FAILED] : N/A';
	if ( $jdb_success == '' )
		$jdb  = '[FAILED] : ' . date( 'M jS, Y g:i A', $jdb_fail - ( 7 * 3600 ) );
} else {
	$jdb = '[SUCCESS] : ' . date( 'M jS, Y g:i A', $jdb_success - ( 7 * 3600 ) );
}

$entry_id = $lead['id'];
$photo = (isset($lead['22'])?$lead['22']:'');
$short_description = $lead['16'];
$long_description = (isset($lead['21'])?$lead['21']:'');
$project_name = (isset($lead['151'])?$lead['151']:'');
$size_request = (isset($lead['60'])?$lead['60']:'');
$size_request_other = (isset($lead['61'])?$lead['61']:'');
$entry_form_type = $form['title'];
$entry_form_status = (isset($lead['303'])?$lead['303']:'');
$wkey = (isset($lead['27'])?$lead['27']:'');
$vkey = (isset($lead['32'])?$lead['32']:'');

$makerfirstname1=$lead['160.3'];$makerlastname1=$lead['160.6'];
$makerPhoto1    =$lead['217'];
$makerfirstname2=$lead['158.3'];$makerlastname2=$lead['158.6'];
$makerPhoto2    =$lead['224'];
$makerfirstname3=$lead['155.3'];$makerlastname3=$lead['155.6'];
$makerPhoto3    =$lead['223'];
$makerfirstname4=$lead['156.3'];$makerlastname4=$lead['156.6'];
$makerPhoto4    =$lead['222'];
$makerfirstname5=$lead['157.3'];$makerlastname5=$lead['157.6'];
$makerPhoto5    =$lead['220'];
$makerfirstname6=$lead['159.3'];$makerlastname6=$lead['159.6'];
$makerPhoto6    =(isset($lead['221'])?$lead['221']:'');
$makerfirstname7=$lead['154.3'];$makerlastname7=$lead['154.6'];
$makerPhoto7    =(isset($lead['219'])?$lead['219']:'');
$makergroupname=$lead['109'];
$field55=RGFormsModel::get_field($form,'55');
$whatareyourplansvalues=$field55['choices'];

$main_description = '';
// Check if we are loading the public description or a short description
if ( isset( $long_description ) ) {
	$main_description = $long_description;
} else if ( isset($short_description ) ) {
	$main_description = $short_description;
}


?>
<table class="fixed entry-detail-view">
	<thead>
		<th colspan="2" style="text-align: left" id="header">
			<h1>
				<?php echo esc_html($project_name); ?>
			</h1>
		</th>
	</thead>
	<tbody>
		<tr>
			<td style="width:440px; padding:5px;" valign="top">
				<a href="<?php echo $photo;?>" class='thickbox'>
				<img width="400px" src="<?php echo legacy_get_resized_remote_image_url($photo, 400,400);?>" alt="" /></a>
			</td>
			<td style="width:340px;" valign="top">
				<table>
					<tr>
						<td colspan="2">
							<p>
								<?php echo stripslashes( nl2br( $main_description, "\n" )  ); ?>
							</p>
						</td>
					</tr>
					<tr>

						<td style="width: 80px;" valign="top"><strong>Type:</strong></td>
						<td valign="top"><?php echo esc_attr( ucfirst( $entry_form_type ) ); ?></td>
					</tr>
					<tr>
						<td style="width: 80px;" valign="top"><strong>Status:</strong></td>
						<td valign="top"><?php echo esc_attr( $entry_form_status ); ?></td>
					</tr>
					<?php
							?>
					<tr>
						<td style="width: 80px;" valign="top"><strong>Website:</strong></td>
						<td valign="top"><a
							href="<?php echo esc_url(  $wkey ); ?>" target="_blank"><?php echo esc_url( $wkey ); ?></a></td>
					</tr>
					<tr>
						<td valign="top"><strong>Video:</strong></td>
						<td><?php
								  echo ( isset( $vkey ) ) ? '<a href="' . esc_url( $vkey ) . '" target="_blank">' . esc_url( $vkey ) . '</a><br/>' : '' ;
								?>
						</td>
					</tr>
					<tr>
						<td style="width: 80px;" valign="top"><strong>Maker Names:</strong></td>
						<td valign="top"><?php echo !empty($makergroupname) ? $makergroupname.'(Group)</br>' : ''; ?>
                                                <?php if(!empty($makerPhoto1)){?>
                                                    <a href="<?php echo $makerPhoto1;?>" class='thickbox'>
                                                    <img width="30px" src="<?php echo legacy_get_resized_remote_image_url($makerPhoto1, 30,30);?>" alt="" />
                                                    </a>
                                                <?php  }?>
						<?php echo !empty($makerfirstname1) ?  $makerfirstname1.' '.$makerlastname1.'</br>' : '' ; ?>
						<?php if(!empty($makerPhoto2)){?>
                                                    <a href="<?php echo $makerPhoto2;?>" class='thickbox'>
                                                    <img width="30px" src="<?php echo legacy_get_resized_remote_image_url($makerPhoto2, 30,30);?>" alt="" />
                                                    </a>
                                                <?php  }?>
                                                <?php echo !empty($makerfirstname2) ?  $makerfirstname2.' '.$makerlastname2.'</br>' : '' ; ?>
                                                <?php if(!empty($makerPhoto3)){?>
                                                    <a href="<?php echo $makerPhoto3;?>" class='thickbox'>
                                                    <img width="30px" src="<?php echo legacy_get_resized_remote_image_url($makerPhoto3, 30,30);?>" alt="" />
                                                    </a>
                                                <?php  }?>
						<?php echo !empty($makerfirstname3) ?  $makerfirstname3.' '.$makerlastname3.'</br>' : '' ; ?>
                                                <?php if(!empty($makerPhoto4)){?>
                                                    <a href="<?php echo $makerPhoto4;?>" class='thickbox'>
                                                    <img width="30px" src="<?php echo legacy_get_resized_remote_image_url($makerPhoto4, 30,30);?>" alt="" />
                                                    </a>
                                                <?php  }?>
						<?php echo !empty($makerfirstname4) ?  $makerfirstname4.' '.$makerlastname4.'</br>' : '' ; ?>
                                                <?php if(!empty($makerPhoto5)){?>
                                                    <a href="<?php echo $makerPhoto5;?>" class='thickbox'>
                                                    <img width="30px" src="<?php echo legacy_get_resized_remote_image_url($makerPhoto5, 30,30);?>" alt="" />
                                                    </a>
                                                <?php  }?>
						<?php echo !empty($makerfirstname5) ?  $makerfirstname5.' '.$makerlastname5.'</br>' : '' ; ?>
                                                <?php if(!empty($makerPhoto6)){?>
                                                    <a href="<?php echo $makerPhoto6;?>" class='thickbox'>
                                                    <img width="30px" src="<?php echo legacy_get_resized_remote_image_url($makerPhoto6, 30,30);?>" alt="" />
                                                    </a>
                                                <?php  }?>
						<?php echo !empty($makerfirstname6) ?  $makerfirstname6.' '.$makerlastname6.'</br>' : '' ; ?>
                                                <?php if(!empty($makerPhoto7)){?>
                                                    <a href="<?php echo $makerPhoto7;?>" class='thickbox'>
                                                    <img width="30px" src="<?php echo legacy_get_resized_remote_image_url($makerPhoto7, 30,30);?>" alt="" />
                                                    </a>
                                                <?php  }?>
						<?php echo !empty($makerfirstname7) ?  $makerfirstname7.' '.$makerlastname7.'</br>' : '' ; ?>

</td>
					</tr>
					<tr>
						<td style="width: 80px;" valign="top"><strong>What are your plans:</strong></td>


						<td valign="top">
						<?php
						for ($i=0; $i < count($whatareyourplansvalues); $i++)
						{
							echo (!empty($lead['55.'.$i])) ? $lead['55.'.$i].'<br />' : '';
						}
?>

</td>
					</tr>
					<tr>
						<td valign="top"><strong>Size Request:</strong></td>
						<td>
						<?php echo ( isset( $size_request ) ) ? $size_request : 'Not Filled out' ; ?>
						<?php echo ( isset( $size_request_other ) ) ? 'Other: '.$size_request_other : '' ; ?>
						</td>
					</tr>
                                        <tr>
                                            <td colspan="2">
                                                <a target="_blank" href="/wp-content/themes/makerfaire/fpdi/makersigns.php?eid=<?php echo $entry_id;?>"><input class="button button-large button-primary" style="text-align:center" value="Download Maker Sign" /></a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2">
                                                <a href="<?php echo admin_url( 'admin-post.php?action=createCSVfile&exForm='.$form['id'].'&exEntry='. $entry_id );?>"><input class="button button-large button-primary"  style="text-align:center" value="Export All Fields" /></a>
                                            </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
                            <label >Email Note To:</label><br />
				<?php
				$emailto1 = array("Alasdair Allan"          => "alasdair@makezine.com",
                          "Brian Jepson"            => "bjepson@makermedia.com",
                          "Bridgette Vanderlaan"    => "bvanderlaan@mac.com",
                          "Caleb Kraft"             => "caleb@makermedia.com",
                          "Dale Dougherty"          => "dale@makermedia.com",
                          "DC Denison"              => "dcdenison@makermedia.com",
                          "Deanna Brown"            => "deanna@makermedia.com",
                          "Jay Kravitz"             => "jay@thecrucible.org",
                          "Jess Hobbs"              => "jess@makermedia.com",
                          "Jonathan Maginn"         => "jonathan.maginn@sbcglobal.net",
                          "Kate Rowe"               => "krowe@makermedia.com");
        $emailto2 = array("Kerry Moore"             => "kerry@contextfurniture.com",
                          "Kim Dow"                 => "dow@dowhouse.com",
                          "Louise Glasgow"          => "lglasgow@makermedia.com",
                          "Miranda Mota"            => "miranda@makermedia.com",
                          "Nick Normal"             => "nicknormal@gmail.com",
                          "Rob Bullington"          => "rbullington@makermedia.com",
                          "Sabrina Merlo"           => "smerlo@makermedia.com",
                          "Sherry Huss"             => "sherry@makermedia.com",
                          "Tami Jo Benson"          => "tj@tamijo.com",
                          "Travis Good"             => "travisgood@gmail.com");
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
				?>
				<div style="float:left">
				<?php foreach ( $emailtoaliases as $name => $email ) {
					echo('<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="'.$email.'" /><strong>'.$name.'</strong> <br />');
					 } ?>
					 </div>
			   <div style="float:left">
				<?php foreach ( $emailto1 as $name => $email ) {
					echo('<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="'.$email.'" />'.$name.'<br />');
					 } ?>
					 </div>
			   <div style="float:left">
				<?php foreach ( $emailto2 as $name => $email ) {
					echo('<input type="checkbox"  name="gentry_email_notes_to_sidebar[]" style="margin: 3px;" value="'.$email.'" />'.$name.' <br />');
					 } ?>
				</div>
				</td>
			<td style="vertical-align: top; padding: 10px;"><textarea
					name="new_note_sidebar"
					style="width: 90%; height: 140px;" cols=""
					rows=""></textarea>
					<?php
						$note_button = '<input type="submit" name="add_note_sidebar" value="' . __( 'Add Note', 'gravityforms' ) . '" class="button" style="width:auto;padding-bottom:2px;" onclick="jQuery(\'#action\').val(\'add_note_sidebar\');"/>';
						echo apply_filters( 'gform_addnote_button', $note_button );	?>
			</td>
		</tr>
	</tbody>
</table>

<?php
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

    $makerfirstname1=$lead['160.3'];$makerlastname1=$lead['160.6'];
    $makerfirstname2=$lead['158.3'];$makerlastname2=$lead['158.6'];
    $makerfirstname3=$lead['155.3'];$makerlastname3=$lead['155.6'];
    $makerfirstname4=$lead['156.3'];$makerlastname4=$lead['156.6'];
    $makerfirstname5=$lead['157.3'];$makerlastname5=$lead['157.6'];
    $makerfirstname6=$lead['159.3'];$makerlastname6=$lead['159.6'];
    $makerfirstname7=$lead['154.3'];$makerlastname7=$lead['154.6'];

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
                  'logistics'=>array(60,61,62,288,64,65,68,69,70,71,72,73,74,75,76),
                  'additional'=>array(123,130,287,134),
                  'images'=>array(22,217,224,223,222,220,221,219,111),
                  'imagesOver'=>array(324,334,326,338,333,337,332,336,331,335)
        );
    ?>
<div id="tabs" class="adminEntrySummary">
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation"><a href="#tabs-1" aria-controls="tabs-1" role="tab" data-toggle="tabs-1">Content</a></li>
    <li role="presentation"><a href="#tabs-2" aria-controls="tabs-2" role="tab" data-toggle="tabs-2">Logistics/Production</a></li>
    <li role="presentation"><a href="#additional" aria-controls="additional" role="tab" data-toggle="additional">Additional Information</a></li>
    <li role="presentation"><a href="#addForms" aria-controls="addForms" role="tab" data-toggle="addForms">Additional Forms</a></li>
    <li role="presentation"><a href="#tabs-3" aria-controls="tabs-3" role="tab" data-toggle="tabs-3">Other Entries</a></li>
    <li role="presentation"><a href="#images" aria-controls="images" role="tab" data-toggle="images">Images</a></li>
    <li role="presentation" aria-selected="true"><a href="#resources" aria-controls="resources" role="tab" data-toggle="resources">Resources</a></li>
  </ul>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane" id="tabs-1">
       <?php echo displayContent($data['content'],$lead,$fieldData);?>
    </div>
    <div role="tabpanel" class="tab-pane" id="tabs-2">
      <?php echo displayContent($data['logistics'],$lead,$fieldData);?>
    </div>
    <div role="tabpanel" class="tab-pane" id="additional">
      <?php echo displayContent($data['additional'],$lead,$fieldData);?>
    </div>
    <div role="tabpanel" class="tab-pane" id="addForms">
        <?php echo getmetaData($entry_id);?>
    </div>
  <div role="tabpanel" class="tab-pane" id="tabs-3">
    <!-- Additional Entries -->
    <table width="100%">
        <tr>
            <th>Maker Name  </th>
            <th>Maker Type  </th>
            <th>Record ID   </th>
            <th>Project Name</th>
            <th>Form Name   </th>
            <th>Status      </th>
        </tr>
    <?php
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

                . '                     WHERE value = "'.$key.'"'
                . '                     and lead_id != '.$entry_id.' group by lead_id order by lead_id');

        $return = array();
        foreach($results as $addData){
            $outputURL = admin_url( 'admin.php' ) . "?page=mf_entries&view=mfentry&id=".$addData->form_id . '&lid='.$addData->lead_id;
            echo '<tr>';

            //only display the first instance of the email
            foreach($email as $typeKey=>$typeData){
                $name = $typeKey;
                $type = $typeData;
                if($name!='') break;
            }
                echo '<td>'.$type .'</td>';
                echo '<td>'.$name .'</td>';
            echo '<td><a target="_blank" href="'.$outputURL.'">'.$addData->lead_id.'</a></td>'
                   . '<td>'.$addData->projectName.'</td>'
                   . '<td>'.$addData->title.'</td>'
                   . '<td>'.$addData->status.'</td>'
                . '</tr>';
        }
    }
    ?>
      </table>
    </div>
    <div role="tabpanel" class="tab-pane"  id="images">
      <?php echo displayContent($data['images'],$lead,$fieldData,'grid');?>
      <?php echo displayContent($data['imagesOver'],$lead,$fieldData,'grid');?>
    </div>

    <div role="tabpanel" class="tab-pane"  id="resources">
      <div class="entry-resource">
      <?php entryResources($lead);?>
      </div>
    </div>
  </div> <!-- .tab-content -->
</div>

    <?php
}

function displayContent($content,$lead,$fieldData,$display = 'table'){
   global $display_empty_fields;
   $return = '';
    if($display=='table')   $return .= '<table>';
   $form = GFAPI::get_form( $lead['form_id'] );

    foreach($content as $fieldID){
        if(isset($fieldData[$fieldID])){
            $field = $fieldData[$fieldID];
            $value         = RGFormsModel::get_lead_field_value( $lead, $field );
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

function getmetaData($entry_id){
  $return = '';

  $metaData = mf_get_form_meta( 'entry_id',$entry_id );
  foreach($metaData as $data){
    $entry = GFAPI::get_entry( $data->lead_id );
    //check if entry-id is valid
    if(is_array($entry)){         //display entry data
      $formPull = GFAPI::get_form( $data->form_id );
      $return .=  '<h2>'.$formPull['title'].'</h2>';
      $return .= '<table>';
      foreach($formPull['fields'] as $formFields){
        $gwreadonly_enable = (isset($formFields['gwreadonly_enable'])?$formFields['gwreadonly_enable']:0);
        //exclude page breaks and the entry fields used to verify the entry
        // and the display only fields from the additional forms
        if($formFields['type']!='page' &&
          $formFields['inputName']!='entry-id' &&
          $formFields['inputName']!='contact-email' &&
          $gwreadonly_enable !=1){

          $display_empty_fields = false;
          switch ( RGFormsModel::get_input_type( $formFields ) ) {
            case 'section' :
              if ( ! GFCommon::is_section_empty( $formFields, $formPull, $entry ) || $display_empty_fields ) {
                $count ++;
                $is_last = $count >= $field_count ? true : false;
                ?>
                <tr>
                  <td colspan="2" class="entry-view-section-break<?php echo $is_last ? ' lastrow' : '' ?>"><?php echo esc_html( GFCommon::get_label( $formFields ) ) ?></td>
                </tr>
                <?php
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
                $return .= $content;
              }
              break;
            }
        }
      }
      $return .= '</table>';
    }
  }

  return $return;
}

// this function returns all entries with a
// meta key set to a certain meta value
function mf_get_form_meta( $meta_key,$meta_value ) {
	global $wpdb;
	$table_name = RGFormsModel::get_lead_meta_table_name();
  $entry = GFAPI::get_entry( $meta_value );

  //retrieve the most current records for each additional form/entry id/form_id combination
  $results  = $wpdb->get_results( $sql = $wpdb->prepare("select * from "
          . "(SELECT * FROM {$table_name}
              WHERE meta_value=%d AND meta_key=%s
              order by id desc) custom
          group by meta_value, form_id, lead_id", $meta_value, $meta_key));

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
                  . ' <tr><th>Item</th>'
                  . ' <th>Qty</th>'
                  . ' <th>Value</th>'
                  . ' <th>Comments</th>'
                  . ' <th>User</th>'
                  . ' <th>Last Updated</th>'
                  . ' <th><p onclick="addRow(\'resource\')"><i class="fa fa-plus-circle"></i></p></th></tr></thead>';
  $return = '';
  foreach($results as $result){
    if($result->user==NULL){
      $dispUser = 'Initial';
    }else{
      $userInfo = get_userdata( $result->user );
      $dispUser = $userInfo->display_name;
    }
    $resourceDisp .= '<tr id="resRow'.$result->ID.'">'
                      . ' <td id="resitem_'.$result->ID.'" data-itemID="'.$result->item_id.'">'.$result->item.'</td>'
                      . ' <td id="restype_'.$result->ID.'" data-typeID="'.$result->resource_id.'" class="editable dropdown">'.$result->type.'</td>'
                      . ' <td id="resqty_'.$result->ID.'"  class="editable numeric">'.$result->qty.'</td>'
                      . ' <td id="rescomment_'.$result->ID.'" class="editable textAreaEdit">'.$result->comment.'</td>'
                      . ' <td id="resuser_'.$result->ID.'">'.$dispUser.'</td>'
                      . ' <td id="resdateupdate_'.$result->ID.'">'.date('m/d/y h:i a',strtotime($result->update_stamp)).'</td>'
                      . ' <td width="10%"><p onclick="resAttDelete(\'#resRow'.$result->ID.'\')"><i class="fa fa-minus-circle"></i></p></td></tr>';
  }
  $resourceDisp .= '</table>';

  //gather attribute data
  $sql = "SELECT wp_rmt_entry_attributes.*, attribute_id,value,wp_rmt_entry_att_categories.category
          FROM `wp_rmt_entry_attributes`, wp_rmt_entry_att_categories
          where attribute_id = wp_rmt_entry_att_categories.ID
          and entry_id = ".$entry_id = $lead['id'] ." order by category";

  $results = $wpdb->get_results($sql);
  $attDisp = '<table id="attTable"><thead><tr>'
                . ' <th>Attribute</th>'
                . ' <th>Value</th>'
                . ' <th>Comment</th>'
                . ' <th>User</th>'
                . ' <th>Last Updated</th>'
                . ' <th><p onclick="addRow(\'attribute\')"><i class="fa fa-plus-circle"></i></p></th></tr></thead>';
  foreach($results as $result){
    if($result->user==NULL){
      $dispUser = 'Initial';
    }else{
      $userInfo = get_userdata( $result->user );
      $dispUser = $userInfo->display_name;
    }
    $attDisp .= '<tr id="attRow'.$result->ID.'">'
                    . ' <td id="attcategory_'.$result->ID.'">'.$result->category.'</td>'
                    . ' <td id="attvalue_'.$result->ID.'" class="editable textAreaEdit">'.$result->value.'</td>'
                    . ' <td id="attcomment_'.$result->ID.'" class="editable textAreaEdit">'.$result->comment.'</td>'
                    . ' <td id="attuser_'.$result->ID.'">'.$dispUser.'</td>'
                    . ' <td id="attdateupdate_'.$result->ID.'">'.date('m/d/y h:i a',strtotime($result->update_stamp)).'</td>'
                    . ' <td><p onclick="resAttDelete(\'#attRow'.$result->ID.'\')"><i class="fa fa-minus-circle"></i></p></td></tr>';
  }
  $attDisp .= '</table>';

  //build attribute drop down values
   //build Item to type drop down array
  $sql = "SELECT ID, category FROM wp_rmt_entry_att_categories";
  $results = $wpdb->get_results($sql);
  $attArr = array();
  foreach($results as $result){
    $attArr[] = array('key'=>$result->ID,'value'=>$result->category);
  }
  ?>
    <script>
      //store items as JS object
      var items = [];
      <?php foreach($itemArr as $itemKey=>$item){?>
        items.push({'key':<?php echo $itemKey;?>,'value': "<?php echo $item;?>"});
      <?php } ?>
      var types      = <?php echo json_encode($typeArr);?>;
      var attributes = <?php echo json_encode($attArr);?>;
    </script>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
          <div class="panel-heading edu1 active-state" role="tab" id="headingOne">
              <h4 class="panel-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Resources</a></h4>
          </div>
          <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body"><?php echo $resourceDisp;?></div>
          </div>
      </div>
      <div class="panel panel-default">
          <div class="panel-heading edu1" role="tab" id="headingTwo">
              <h4 class="panel-title">
                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Attributes</a></h4>
          </div>
          <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
              <div class="panel-body"><?php echo $attDisp;?></div>
          </div>
      </div>

  </div> <?php
}
