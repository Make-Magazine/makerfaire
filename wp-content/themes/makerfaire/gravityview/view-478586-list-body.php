<?php /** Template Name: Maker Admin Manage Entries */
if (!is_user_logged_in())
    auth_redirect();

get_header();

$current_user = wp_get_current_user();

//require_once our model
require_once( get_template_directory().'/models/maker.php' );

//instantiate the model
$maker   = new maker($current_user->user_email);

$tableData = $maker->get_table_data();
$entries   = $tableData['data'];

?>
<div class="content col-md-12 maker-admin-manage-faire-entries-mobile">
  <script>
  jQuery(document).ready(function() {
    jQuery('a[href$="jpg"], a[href$="png"], a[href$="jpeg"]').fancybox();
    //jQuery('[data-toggle="tooltip"]').tooltip();
    jQuery('body').tooltip( {selector: '[data-toggle=tooltip]'} );
    //returns content based on separate element - For Notificationd, get your tickets and
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
  });
  </script>
  <?php

  if(!empty($entries)){ ?>
    <div class="clearfix">
      <div>
        <h2 style="float:left">Welcome to the Maker Faire Portal</h2>

        <div class="dropdown show" style="float:right">
          <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Apply to Participate
          </a>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <ul>
              <li><a class="dropdown-item" href="http://makerfaire:8888/new-york/call-for-makers/">World Maker Faire New York</a></li>
              <li><a class="dropdown-item" href="http://makerfaire:8888/bay-area/call-for-makers/">Maker Faire Bay Area</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <h3 class="title-head pull-left">Manage your Maker Faire Entries</h3>
    </div>
    User <?php echo $current_user->user_email;?><br/>
    <hr class="header-break">
    <?php

    foreach($entries as $entryData) {
      $image =  (isset($entryData['project_photo']) && $entryData['project_photo'] != '' ? $entryData['project_photo']:get_template_directory_uri() .'/images/no-image.png');

      //status specific logic
      $statusBlock  = ($entryData['status'] == 'Accepted' ? 'greenStatus':'greyStatus');
      $dispCancel   = ($entryData['status'] != 'Cancelled' && $entryData['status']!='Rejected' && $entryData['maker_type'] == 'contact' ?true:false);
      $dispDelete   = ($entryData['status'] == 'Proposed' || $entryData['status'] == 'In Progress'?true:false);

      /* Editing the Entry */

      /* First, determine who has access to edit an entry */
      $disp_edit = false;

      if(strtolower($entryData['maker-edit']) == 'yes'){
        //all makers, creator and contact can edit
        $disp_edit = true;
      }elseif($entryData['maker_type'] == 'contact'){
        //only creator and contact can edit
        $disp_edit = true;
      }

      /* Next determine what edit links are displayed */

      /*
       * There are two edit entry links on the page for each entry.
       * Note: $disp_edit must be true for either of these links to appear
       *
       *  1) View/Edit Public Information
       *      Displayed if status is accepted
       *  2) Edit Entry
       *      In the edit form settings of each form, producers have the option to display the
       *      setup/resources assigned to an entry and supply them a link to an edit form that
       *      allows the makers to only edit specific setup/resource information about their
       *      exhibit. This 'lock down' on information restricts the maker from making changes
       *      to pertinant information after a certain date before a faire.
       *    If the status is Accepted and the display setup/resources is set to true
       *      use the URL supplied in the form settings to edit resoures.
       *    If the status is Accepted or Proposed and the display setup/resources is set to false
       *      use the gravityview edit entry link that allows full edit of the entry information
       */
      $dispRMTeditLink = false;
      $dispGVeditLink  = false;
      $dispEditPub     = false;

      if($disp_edit){
        if($entryData['mat_disp_res_link'] == 'yes' && $entryData['status'] == 'Accepted'){
          $dispRMTeditLink = true;  //display resource edit only link
        }else{
          if($entryData['status'] == 'Accepted' || $entryData['status'] == 'Proposed')
            $dispGVeditLink = true; //display full form edit thru gravity forms
        }

        //display the 'view/edit public information' link is the status is accepted
        $dispEditPub = ($entryData['status'] == 'Accepted' ? true : false);
        //if form type = sponsor or startup do not display
        if($entryData['form_type']=='Sponsor'||$entryData['form_type']=='Startup Sponsor')
          $dispEditPub = false;

        //if form type = sponsor or startup do not display
        if($entryData['form_type']=='Sponsor'||$entryData['form_type']=='Startup Sponsor')
          $dispEditPub = false;

        //set edit links

        //Public facing profile page edit link 'View/Edit Public Information'
        $viewEditLink = "/maker/entry/" . $entryData['lead_id']."/edit/";

        //GV Edit Link
        $GVeditLink = do_shortcode('[gv_entry_link action="edit" return="url" view_id="478586" entry_id="'.$entryData['lead_id'].'"]');
        $GVeditLink = str_replace('/view/', '/', $GVeditLink);  //remove view slug from URL

        //RMT edit link
        $RMTeditLink = '<span class="editLink">
                          <button type="button" class="btn btn-default btn-no-border edit-button toggle-popover" data-toggle="popover">
                            <i class="fa fa-eye" aria-hidden="true"></i>View/Edit Setup
                          </button>
                          <div class="popover-content hidden">'.
                            $entryData['mat_res_modal_layout'].'
                            <div class="clear">';
                              if($entryData['mat_edit_res_url'] != '') {
                                $RMTeditLink .= '<a href="'.$entryData['mat_edit_res_url'].'">Edit</a>';
                              }
        $RMTeditLink .= '   </div>
                          </div>
                        </span>';

      }

      ?>
      <div class="maker-admin-list-wrp">
        <div class="gv-list-view-title-maker-entry">
          <div class="statusBox <?php echo $statusBlock;?>">
            <div class="statusFaire"><span class="gv-field-label"><?php echo $entryData['faire_name'];?></span> </div>
            <div class="statusText"><span class="gv-field-label">Status: </span> <?php echo $entryData['status'];?></div>
          </div> <!-- close .statusBox -->
          <div class="entryImg">
            <div class="faire-entry-image-wrp">
              <a href="<?php echo $image;?>">
                <div class="image-container" style="background-image: url('<?php echo $image;?>');"></div>
              </a>
            </div>
          </div> <!-- close .entryImg-->
          <div class="entry-main-content">
            <?php if($entryData['mat_message'] !='') { ?>
            <!-- MAT messaging -->
            <div class="hidden-xs mat_message" style="background-color:#F4D03F; padding:10px">
                <?php echo $entryData['mat_message'];?>
            </div>
            <div class="clear"></div>
            <?php }                                    ?>

            <!-- Project Name -->
            <div class="entryName entryData">
              <h3 class="entry-title"><?php echo $entryData['presentation_title'];?></h3>
            </div><!-- close .entryName -->

            <!-- Form Type -->
            <div class="exhibitID entryData">
              <?php echo $entryData['form_type'];?>: <span class="entryStandout"><?php echo $entryData['lead_id'];?></span>
            </div> <!-- close exhibitID -->

            <div>
              <?php
              if($dispEditPub) { ?>
                <span class="editLink">
                  <a href="<?php echo $viewEditLink;?>">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                    View/Edit Public Information
                  </a>
                </span>
              <?php
              }
              if($dispRMTeditLink) {
                echo $RMTeditLink;
              }elseif($dispGVeditLink){
                ?>
                <span class="editLink">
                  <a href="<?php echo $GVeditLink;?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit Entry</a>
                </span>
                <?php
              }
              ?>
            </div>
            <div class="clear"></div>

            <div class="actionSection">
              <div class="submit-date">
                <span class="gv-field-label">Submitted: </span> <?php echo esc_html( GFCommon::format_date( $entryData['date_created'], false, 'm/d/Y' )); ?>
              </div>
              <!-- Action Bar for Entries -->
              <div class="entry-action-buttons">
                <!-- Get Your Tickets Section -->
                <?php
                //only display if there are tickets and if the entry has been accepted
                if(!empty($entryData['ticketing']) && $entryData['status'] == 'Accepted'){ ?>
                  <button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
                    <span class="hideSmall">GET YOUR </span>ENTRY PASSES
                    <div class="fa fa-ticket fa-lg toggle-popover" data-toggle="popover"></div>
                  </button>
                  <div class="popover-content hidden">
                    <?php
                    foreach($entryData['ticketing'] as $ticket){
                      ?>
                      <div class="row mat-ticketing">
                        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                          <div class="title"><?php echo $ticket['title'];?></div>
                          <div class="subtitle"><?php echo $ticket['subtitle'];?></div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                          <a target="_blank" href="<?php echo $ticket['link'];?>">
                            <i class="fa fa-chevron-circle-right fa-2x" aria-hidden="true"></i>
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
                if(isset($entryData['tasks'])){
                  if(!empty($entryData['tasks']) && count($entryData['tasks']['toDo']) > 0 || count($entryData['tasks']['done'])>0){
                    $tasks = $entryData['tasks'];
                    ?>

                    <button type="button" class="btn btn-default btn-no-border notifications-button toggle-popover" data-toggle="popover">TASKS
                      <div class="notification-counter toggle-popover" data-toggle="popover"><?php echo count($tasks['toDo']);?></div>
                    </button>
                    <div class="popover-content hidden">
                      <div class="manage-entry-popover row">
                        <div class="manage-links">
                          <?php
                          foreach($tasks['toDo'] as $task) { ?>
                            <a target="_blank" href="<?php echo $task['action_url'];?>"><?php echo $task['description'];?> <span class="todoTasks" style="color:red"><i class="fa fa-arrow-right" aria-hidden="true"></i>To Do</span></a>
                            <?php
                          }
                          ?>
                        </div>
                        <div class="manage-links">
                          <!--<h4 class="tasks"><i class="fa fa-check" aria-hidden="true"></i>Done</h4>-->
                          <?php
                          foreach($tasks['done'] as $task) { ?>
                            <a target="_blank" href="<?php echo $task['action_url'];?>"><?php echo $task['description'];?> <span class="doneTasks" style="color:green"><i class="fa fa-check" aria-hidden="true"></i>Done</span></a>
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
                  <div class="toggle-popover fa fa-cog fa-lg" data-toggle="popover"></div>
                </button>
                <div class="popover-content hidden">
                  <div class="manage-entry-popover row">
                    <div class="manage-links">
                      <?php
                      //View Link
                      $url = do_shortcode('[gv_entry_link action="read" return="url" view_id="478586" entry_id="'.$entryData['lead_id'].'"]');
                      $url = str_replace('/view/', '/', $url);  //remove view slug from URL
                      ?><a href="<?php echo $url;?>">View Entry</a><?php

                      if($dispEditPub) { ?>
                        <a href="<?php echo $viewEditLink;?>">
                          View/Edit Public Information
                        </a>
                      <?php
                      }

                      if($dispRMTeditLink) {
                        echo $RMTeditLink;
                      }elseif($dispGVeditLink){
                        ?>
                          <a href="<?php echo $GVeditLink;?>">Edit Entry</a>
                        <?php
                      }
                      ?>
                    </div>
                    <div>
                      <?php
                      if($dispCancel){  ?>
                        <a href="#cancelEntry" data-toggle="modal" data-entry-id="<?php echo $entryData['lead_id'];?>" data-projName="<?php echo $entryData['presentation_title'];?>">Cancel Entry</a>
                      <?php
                      }
                      //Delete Link
                      if($dispDelete){
                        ?>
                        <a href="#deleteEntry" data-toggle="modal" data-entry-id="<?php echo $entryData['lead_id'];?>" data-projName="<?php echo $entryData['presentation_title'];?>">Delete Entry</a>
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
    } //end foreach entry loop
  } else{
    ?>
    <h2>Welcome to the Maker Faire Portal!</h2>
    <h3>Maker Faire Bay Area and World Maker Faire New York Participants</h3>
    <h4>This is where participants can manage their Maker Faire entries for World Maker Faire New York and Bay Area Maker Faire.
    You are logged in as <?php echo $current_user->user_email;?>, but you do not seem to have applied yet. There are many ways to participate:</h4>
    <ul class="text-center ui-grid-group-list">
      <li class="font-weight-bold">Maker Exhibitors</li>
      <li>Performers</li>
      <li>Presenters</li>
      <li>Startup Sponsors</li>
      <li>Event Sponsors</li>
    </ul>
    <div class="row text-center">
      <div class="col-md-6"><a href="/new-york/call-for-makers/" class="btn btn-alert btn-info">World Maker Faire New York</a></div>
      <div class="col-md-6"><a href="/bay-area/call-for-makers/" class="btn btn-alert btn-info">Maker Faire Bay Area</a></div>
    </div>
    <h3 class="text-center">Join us in co-creating the next Maker Faire!</h3>
    <?php
  }//end check !empty?>

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

  <!--Modal to delete entry-->
  <div class="modal" id="deleteEntry">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title">Delete <span id="delProjName"></span>, Exhibit ID: <span id="deleteEntryID" name="entryID"></span></h4>
        </div>
        <div class="modal-body">
          <div id="deleteText">
            <p>Are you sure you want to trash this entry? You can not reverse this action.</p>
          </div>
          <span id="deleteResponse"></span>
          <br>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" id="submitDelete">Yes, delete it</button>
          <button type="button" class="btn btn-default" id="cancelDelete" data-dismiss="modal">No, I'll keep it</button>
          <button type="button" class="btn btn-default" id="closeDelete" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div> <!-- / .maker-admin-manage-faire-entries-mobile -->
<?php if(!empty($entries))  echo $maker->createPageLinks( 'pagination pagination-sm' );?>
<?php get_footer();
