<?php
/**
 * The entry loop for the list output.
 *
 * @global \GV\Template_Context $gravityview
 */

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template loaded without context', array( 'file' => __FILE__ ) );
	return;
}
$viewID = '642359'; 

$template = $gravityview->template;
$current_user = wp_get_current_user();

//require_once our model
require_once( get_template_directory().'/models/maker.php' );

//currently this view is only for form 260
$form_id  = 260;
$form     = GFAPI::get_form($form_id);

//instantiate the model
$maker   = new maker($current_user->user_email);

/** @action `gravityview/template/list/body/before` */
$template::body_before( $gravityview );
?>
<div class="content col-md-12 maker-admin-manage-faire-entries-mobile">
  <script>
  jQuery(document).ready(function() {
    jQuery('a[href$="jpg"], a[href$="png"], a[href$="jpeg"]').fancybox();
    //jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('body').tooltip( {selector: '[data-toggle=tooltip]'} );
    //returns content based on separate element - For Notifications, get your tickets and
    jQuery(".toggle-popover").popover({
        html : true,
        placement : "auto bottom",
        content: function() {
          return jQuery(this).next('.popover-content').html();
        },
        title: ''
    });
    jQuery('body').on('click', function (e) {
      //did not click a popover toggle or popover
      if (jQuery(e.target).data('toggle') !== 'popover'
          && jQuery(e.target).parents('.popover.in').length === 0) {
          jQuery('[data-toggle="popover"]').popover('hide');
      }
    });
	jQuery(".notification-counter").each(function() {
		if(jQuery(this).attr("data-count") != 0) {
			jQuery(this).click();
		}
	});
  });
  </script>
	<?php
	$current_user = wp_get_current_user();
	?>
	<div class="clearfix">
		<div>
		<h2 style="float:left">Welcome to the Maker Faire Portal</h2>
		<div class="clearfix"></div>
		<h3 class="title-head pull-left" style="margin-top:0px;">Manage your Maker Faire Entries</h3>
		<div class="clearfix"></div>
		User <?php echo $current_user->user_email;?>
		<div class="dropdown show">
			<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Apply to Participate
			</a>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
			<ul class="bulletless">
				<li><a class="dropdown-item" href="/bay-area/call-for-makers/" target="_blank">Maker Faire Bay Area</a></li>
				<!--<li><a class="dropdown-item" href="/make-education-forum/" target="_blank">Make: Education Forum</a></li>-->
				<!--<li><a class="dropdown-item" href="/virtually-maker-faire-call-2020-for-makers/" target="_blank">Virtually Maker Faire</a></li>-->
				<!--<li><a class="dropdown-item" href="/new-york/call-for-makers/" target="_blank">World Maker Faire New York</a></li>-->
				<!--<li><a class="dropdown-item" href="/bay-area/call-for-makers/" target="_blank">Maker Faire Bay Area</a></li>-->
			</ul>
			</div>
		</div>
		</div>
	</div>

	<hr class="header-break">
	<?php
// There are no entries.
if ( ! $gravityview->entries->count() ) {

	$no_results_css_class = 'gv-no-results gv-no-results-text';

	if ( 1 === (int) $gravityview->view->settings->get( 'no_entries_options', '0' ) ) {
		$no_results_css_class = 'gv-no-results gv-no-results-form';
	}

	?>
	<div class="gv-list-view <?php echo esc_attr( $no_results_css_class ); ?>">
		<div class="gv-list-view-title">
			<h3><?php echo gv_no_results( true, $gravityview ); ?></h3>
		</div>
	</div>
	<?php
} else {
	$editEntryLabel = 'Manage Photos';
	$post_id = get_the_ID();

	// There are entries. Loop through them.	
	foreach ( $gravityview->entries->all() as $multientry ) {				
		$entryData = $multientry->as_entry();				
	
		$image =  (isset($entryData['22']) && $entryData['22'] != '' ? $entryData['22']:get_template_directory_uri() .'/images/no-image.png');
		$entryStatus = (isset($entryData['303']) && $entryData['303'] != '' ? $entryData['303'] : 'Unknown');
		
		//with BA23 we introduced Pending. We need to call it Proposed in the back end as a lot of code is linked to this. Rename so makers see pending
		if($entryStatus=='Proposed')	$entryStatus="Pending";
		
		//status specific logic
		$statusBlock  = ($entryStatus == 'Accepted' ? 'greenStatus':'greyStatus');				
		$dispCancel   = ($entryStatus != 'Cancelled' && $entryStatus != 'Rejected'?true:false);
		//$dispDelete   = ($entryStatus == 'Proposed' || $entryStatus == 'In Progress'?true:false);		
		$dispDelete = FALSE;

		//edit media links (defined in the 'Edit Entry' section of the view)
		$dispGVeditLink = TRUE;
		
		$GVeditLink = do_shortcode('[gv_entry_link post_id="'.$post_id.'" action="edit" return="url" view_id="'.$viewID.'" entry_id="'.$entryData['id'].'"]');
		
		//set tasks for entry
		$entryData['tasks'] = $maker->get_tasks_by_entry($entryData['id']);			

        //get Maker messaging
        $text = GFCommon::replace_variables(rgar($form, 'mat_message'),$form, $entryData,false,false);
        $text = do_shortcode( $text ); //process any conditional logic
        $entryData['mat_message']          = $text;

        //MAT switch to display the edit resources link
        $entryData['mat_disp_res_link']    = rgar($form, 'mat_disp_res_link');

        //process any shortcode logic in the resource modal layout
        $text = GFCommon::replace_variables(rgar($form, 'mat_res_modal_layout'),$form, $entryData);
        $text = do_shortcode( $text );
        $entryData['mat_res_modal_layout'] = $text;

        //set the URL for the edit resource link
        $entryData['mat_edit_res_url'] = "/bay-area/logistics-information/?ep_token=". $entryData['fg_easypassthrough_token'];

		//RMT edit link
		$dispRMTeditLink = ($entryData['mat_disp_res_link'] == 'yes' && $entryStatus == 'Accepted')? TRUE: FALSE;        
		if($dispRMTeditLink){
			$RMTeditLink = '<span class="editLink">
			<button type="button" class="btn btn-default btn-no-border edit-button toggle-popover" data-toggle="popover">
			  <i class="fa fa-eye" aria-hidden="true"></i>View/Edit Setup
			</button>
			<div class="popover-content hidden">'.
			  $entryData['mat_res_modal_layout'].'
			  <div class="clear">
				  <a href="'.$entryData['mat_edit_res_url'].'">Edit</a>
				</div>
			</div>
		  </span>';
		}
		
		//default ticketing to off TBD 
		//TBD check if the faire is passed
		$entryData['ticketing'] = entryTicketing($entryData,'MAT');
		/*
		 //set ticketing and task information if the faire is not past
		 if ($row['end_dt'] >= $today) {
			$data['ticketing']    = entryTicketing($entry,'MAT');
			//get tasks
			$data['tasks'] = $this->get_tasks_by_entry($row['lead_id']);
		  }
*/

		//Public facing profile page edit link 'See Your Maker Page'
		$dispEditPub = ($entryStatus == 'Accepted' ? true : false);		
        $viewEditLink = "/maker/entry/" . $entryData['id']."/edit/";					

		//Maker Faire portal html
		?>
		<div class="maker-admin-list-wrp">
			<div class="gv-list-view-title-maker-entry">
				<div class="statusBox <?php echo $statusBlock;?>">
					<!-- TBD set faire name dynamically -->
					<div class="statusFaire"><span class="gv-field-label">Maker Faire Bay Area 2023</span> </div>
					<div class="statusText"><span class="gv-field-label">Status: </span> <?php echo $entryStatus;?></div>
				</div> <!-- close .statusBox -->
				<div class="entryImg">
					<div class="faire-entry-image-wrp">
					<?php if(!isset($entryData['22']) || $entryData['22'] == '' && $dispGVeditLink ) { // user doesn't have an image set ?>
						<div class="empty-image"><p><b style="font-weight: 500;">You have not set a featured image.</b><br /><br />Please <a href="<?php echo $GVeditLink;?>">manage your photos here</a>.</p></div>
					<?php } else { ?>
						<a href="<?php echo $image;?>">
							<div class="image-container" style="background-image: url('<?php echo $image;?>');"></div>
						</a>
					<?php } ?>
					</div>
				</div> <!-- close .entryImg-->
				<div class="entry-main-content">
					<?php if($entryData['mat_message'] !='') { ?>
						<!-- MAT messaging -->
						<div class="hidden-xs mat_message" style="background-color:#f4d03e; padding:10px">
							<?php echo $entryData['mat_message'];?>
						</div>
						<div class="clear"></div>
					<?php }                                    ?>

					<!-- Project Name -->
					<div class="entryName entryData">
						<h3 class="entry-title"><?php echo $entryData['151'];?></h3>
					</div><!-- close .entryName -->

					<!-- Form Type -->
					<div class="exhibitID entryData">
						<span class="entryStandout"><?php echo $entryData['id'];?></span>
					</div> <!-- close exhibitID -->

					<div class="editLinkRow">
						<?php              
						if($dispEditPub) { ?>
							<span class="editLink">
								<a href="<?php echo $viewEditLink;?>">
									<i class="fa fa-eye" aria-hidden="true"></i>
									See Your Maker Page
								</a>
							</span>
						<?php
						}
						if($dispRMTeditLink) {
							echo $RMTeditLink;
						}elseif($dispGVeditLink){
							?>
							<span class="editLink">
								<a href="<?php echo $GVeditLink;?>"><i class="fa fa-image" aria-hidden="true"></i><?php echo $editEntryLabel;?></a>
							</span>
							<?php
						}
						
						?>
						<span class="editLink">
							<a href="/bay-area/logistics-information/?ep_token=<?php echo $entryData['fg_easypassthrough_token'];?>"><i class="fas fa-edit" aria-hidden="true"></i>Manage Logistics Info</a>
						</span>
						<span class="editLink">
							<a href="/bay-area/public-information/?ep_token=<?php echo $entryData['fg_easypassthrough_token'];?>"><i class="fas fa-edit" aria-hidden="true"></i>Manage Public Info</a>
						</span>

					</div>
						<div class="clear"></div>

						<div class="actionSection">
						<?php /*<div class="submit-date">
							<span class="gv-field-label">Submitted: </span> <?php echo esc_html( GFCommon::format_date( $entryData['date_created'], false, 'm/d/Y' )); ?>
						</div> */ ?>
						<!-- Action Bar for Entries -->
						<div class="entry-action-buttons">
							<!-- Get Your Tickets Section -->
							<?php
							//only display if there are tickets and if the entry has been accepted
							if(!empty($entryData['ticketing']) && $entryStatus == 'Accepted'){ ?>
							<button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
								<span class="hideSmall">GET YOUR</span>&nbsp;ENTRY PASSES
									<div class="fa fa-ticket toggle-popover" data-toggle="popover"></div>
							</button>
							<div class="popover-content hidden">
								<?php
								foreach($entryData['ticketing'] as $ticket){
								?>
								<div class="row mat-ticketing">
									<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
										<a target="_blank" href="<?php echo $ticket['link'];?>">
											<div class="title"><?php echo $ticket['title'];?></div>
											<div class="subtitle"><?php echo $ticket['subtitle'];?></div>
										</a>
									</div>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<a target="_blank" href="<?php echo $ticket['link'];?>">
											<i class="fa fa-circle-chevron-right" aria-hidden="true"></i>
										</a>
									</div>
								</div>
								<?php
								}
								?>
								<div class="footer">
								To share a ticket with a friend, just send them the Eventbrite URL
								</div>
							</div>
							<?php
							}
							?>

							<!-- Tasks -->
							<?php
							if(isset($entryData['tasks']) && !empty($entryData['tasks'])){
								if(count($entryData['tasks']['toDo']) > 0 || count($entryData['tasks']['done'])>0){
									$tasks = $entryData['tasks'];
									?>

									<button type="button" class="btn btn-default btn-no-border notifications-button toggle-popover" data-toggle="popover">TASKS
											<div class="notification-counter toggle-popover" data-toggle="popover" data-count="<?php echo count($tasks['toDo']);?>"><?php echo count($tasks['toDo']);?></div>
									</button>
									<div class="popover-content hidden">
									<div class="manage-entry-popover row">
										<div class="manage-links">
										<?php
										foreach($tasks['toDo'] as $task) { ?>
											<a target="_blank" href="<?php echo $task['action_url'];?>"><?php echo $task['description'];?> <span class="todoTasks" style="color:red"><i class="fas fa-arrow-right" aria-hidden="true"></i>To Do</span></a>
											<?php
										}
										?>
										</div>
										<div class="manage-links">
										<!--<h4 class="tasks"><i class="fa fa-check" aria-hidden="true"></i>Done</h4>-->
										<?php
										foreach($tasks['done'] as $task) { ?>
											<a target="_blank" href="<?php echo $task['action_url'];?>"><?php echo $task['description'];?> <span class="doneTasks" style="color:green"><i class="fas fa-check" aria-hidden="true"></i>Done</span></a>
											<?php
										}
										?>
										</div>
									</div>
									</div>
									<?php
								}
							}
							?>

							<!-- Manage Entry links -->
							<button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">MANAGE
									<div class="toggle-popover fa fa-cog" data-toggle="popover"></div>
							</button>
							<div class="popover-content hidden">
								<div class="manage-entry-popover row">
									<div class="manage-links">
									<?php
									//View Link
									$url = do_shortcode('[gv_entry_link action="read" return="url" view_id="'.$viewID.'" entry_id="'.$entryData['id'].'"]');
									$url = str_replace('/view/', '/', $url);  //remove view slug from URL
									/* ?><a href="<?php echo $url;?>">View Entry</a><?php */

									if($dispEditPub) { ?>
										<a href="<?php echo $viewEditLink;?>">
											See Your Maker Page
										</a>
									<?php
									}

									if($dispRMTeditLink) {
										echo $RMTeditLink;
									}elseif($dispGVeditLink){
										?>
										<a href="<?php echo $GVeditLink;?>"><?php echo $editEntryLabel;?></a>
										<?php
									}
									?>
									</div>
									<div>
									<?php
									if($dispCancel){  ?>
										<a href="#cancelEntry" data-toggle="modal" data-entry-id="<?php echo $entryData['id'];?>" data-projName="<?php echo $entryData['151'];?>">Cancel Entry</a>
									<?php
									}
									//Delete Link
									if($dispDelete){
										?>
										<a href="#deleteEntry" data-toggle="modal" data-entry-id="<?php echo $entryData['id'];?>" data-projName="<?php echo $entryData['151'];?>">Delete Entry</a>
										<?php
									}
									?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div><!-- /entry-main-content-->
				<div class="clear"></div>
			</div> <!-- close .gv-list-view-title-maker-entry -->
      	</div> <!-- close .maker-admin-list-wrp-->
	  	<?php
		//end maker portal html
	} //end foreach
}//end check for records
?>
 <!-- Modal to cancel entry -->
 <div class="modal" id="cancelEntry">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title">Cancel <span id="projName"></span>, Exhibit ID: <span id="cancelEntryID" name="entryID"></span></h4>
        </div>
        <div class="modal-body">
            <div id="cancelText">
                <p>Sorry you can't make it. Why are you canceling?</p><br/>
                <textarea rows="4" cols="50" name="cancelReason"></textarea>
            </div>
        <span id="cancelResponse"></span><br/>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" id="submitCancel">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

</div> <!-- / .maker-admin-manage-faire-entries-mobile -->
<?php
/** @action `gravityview/template/list/body/after` */
$template::body_after( $gravityview );
