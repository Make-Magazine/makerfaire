<?php /** Template Name: Maker Admin Manage Entries */
if (!is_user_logged_in())
    auth_redirect();

get_header();
?>
<div class="content col-md-12 maker-admin-manage-faire-entries-mobile">
  <script>
  jQuery(document).ready(function() {
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
  <div class="clearfix">
    <?php
    $current_user = wp_get_current_user();

    //require_once our model
    require_once( get_template_directory().'/models/maker.php' );

    //instantiate the model
    $maker   = new maker($current_user->user_email);

    $tableData = $maker->get_table_data();
    $entries   = $tableData['data'];

?>
    <div class="settings-pop-btn pull-right">
      <button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
        Settings &amp; Help<i class="fa fa-cog"></i>
      </button>
      <div class="popover-content hidden">
        <div class="manage-entry-popover">
          <a href="/login/?mode=reset">Change Password</a>
          <a href="/login/?action=logout">Log Out</a>
          <h6 class="popover-head">Questions?</h6>
          <a href="http://makerfaire.com/all-toolkits/">Visit your Toolkit</a>
        </div>
      </div> <!-- / .popover-content -->
    </div> <!-- / .settings-pop-btn -->
  </div>
  <div class="clearfix">
    <h2 class="title-head pull-left">Manage your Maker Faire Entries</h2>
    <span class="submit-entry pull-right">
      <a href="/new-york/call-for-makers/" target="_blank" class="btn btn-primary btn-no-border">
        Submit another entry
      </a>
    </span>
  </div>
  User <?php echo $current_user->user_email;?><br/>
  <hr class="header-break">
  <?php

  foreach($entries as $entryData) {
    //get tasks
    $tasks     = $maker->get_tasks_by_entry($entryData['lead_id']);
    //prepare the data
    if($entryData['status']=='Accepted'){
      $statusBlock = 'greenStatus';
    }else{
      $statusBlock = 'greyStatus';
    }
    //$image = legacy_get_fit_remote_image_url($entryData['project_photo'],275,275);
    $image =  (isset($entryData['project_photo'])&&$entryData['project_photo']!=''?$entryData['project_photo']:get_template_directory_uri() .'/images/no-image.png');
    ?>

    <div class="maker-admin-list-wrp">
      <div class="gv-list-view-title-maker-entry">
        <div class="statusBox <?php echo $statusBlock;?>">
          <div class="statusFaire"><span class="gv-field-label"><?php echo $entryData['faire_name'];?></span> </div>
          <div class="statusText"><span class="gv-field-label">Status: </span> <?php echo $entryData['status'];?></div>
        </div> <!-- close .statusBox -->
        <div class="entryImg">
          <div class="faire-entry-image-wrp">
            <a class="thickbox" href="<?php echo $image;?>">
              <div class="image-container" style="background-image: url('<?php echo $image;?>');"></div>
              <!--<img class="img-responsive" src="<?php echo $image;?>" alt="Project Photo" />-->
            </a>
          </div>
        </div> <!-- close .entryImg-->
        <div class="entry-main-content">
          <div class="entryName entryData">
            <h3 class="entry-title">
              <?php //if status is accepted, the title links to the public facing entry page
              if($entryData['status']=='Accepted') {?>
                <a target="_blank" href="/maker/entry/<?php echo $entryData['lead_id'];?>"><?php echo $entryData['presentation_title'];?></a>
              <?php
              }else{
                echo $entryData['presentation_title'];
              } ?>
            </h3>
          </div><!-- close .entryName -->
          <div class="exhibitID entryData">
            <?php echo $entryData['form_type'];?>: <span class="entryStandout"><?php echo $entryData['lead_id'];?></span>
          </div> <!-- close exhibitID -->

          <?php if($entryData['mat_message'] !='') { ?>
          <div class="mat_message">
            <?php echo $entryData['mat_message'];?>
          </div>
          <div class="clear"></div>
          <?php }                                    ?>
          <div>
            <?php
            //Add link to edit entry
            $disp_edit = (($entryData['status'] == 'Proposed' || $entryData['status']=='Wait List' || $entryData['status'] == 'Accepted')
                          && $entryData['maker_type']=='contact' ? true: false);

            if($disp_edit){
              $url = do_shortcode('[gv_entry_link action="edit" return="url" view_id="478586" entry_id="'.$entryData['lead_id'].'"]');
              $url = str_replace('/view/', '/', $url);  //remove view slug from URL
              echo  '<span class="editLink">'
                  . '  <i class="fa fa-pencil-square-o" aria-hidden="true"></i>'
                  . '  <a href="'. $url .'">Edit Entry</a>'
                  . '</span>';
            }
            ?>
          </div>

          <div>
            <?php
            $viewEditLink = "/maker/entry/" . $entryData['lead_id']."/edit/";

            if($entryData['status']=='Accepted') { ?>
              <span class="editLink">
                <i class="fa fa-eye" aria-hidden="true"></i>
                <a href="<?php echo $viewEditLink;?>">View/Edit Public Information</a>
              </span>
              <?php
            } ?>

          </div>
          <div class="clear"></div>


          <div class="actionSection">
            <div class="submit-date">
              <span class="gv-field-label">Submitted: </span> <?php echo date('M j, Y g:i  A',strtotime($entryData['date_created']));?>
            </div>
            <!-- Action Bar for Entries -->
            <div class="entry-action-buttons">
              <!-- Get Your Tickets Section -->
              <?php
              //only display if there are tickets and if the entry has been accepted
              if(!empty($entryData['ticketing']) && $entryData['status']=='Accepted'){ ?>
                <button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
                  <span class="hideSmall">GET YOUR </span>ENTRY PASSES<i class="fa fa-ticket fa-lg" aria-hidden="true"></i>
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
              <?php if(isset($tasks) && count($tasks['toDo']) > 0 || count($tasks['done'])>0) {?>
                <button type="button" class="btn btn-default btn-no-border notifications-button toggle-popover"
                  data-toggle="popover">
                  Tasks
                  <span class="fa-stack fa-lg">
                    <i class="fa fa-circle"></i>
                    <span class="notification-counter"><?php echo count($tasks['toDo']);?></span>
                  </span>
                </button>
                <div class="popover-content hidden">
                  <div class="manage-entry-popover row">
                    <div class="manage-links">
                      <!--<h4 class="tasks"><i class="fa fa-arrow-right" aria-hidden="true"></i>To Do</h4>-->
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
              <?php } ?>

              <!-- Manage Entry links -->
              <button type="button" class="btn btn-default btn-no-border manage-button toggle-popover" data-toggle="popover">
                MANAGE<i class="fa fa-cog fa-lg"></i>
              </button>
              <div class="popover-content hidden">
                <div class="manage-entry-popover row">
                  <div class="manage-links">
                    <?php
                    //View Link
                    $url = do_shortcode('[gv_entry_link action="read" return="url" view_id="478586" entry_id="'.$entryData['lead_id'].'"]');
                    $url = str_replace('/view/', '/', $url);  //remove view slug from URL
                    ?>
                    <a href="<?php echo $url;?>">View Entry</a>
                    <?php if($entryData['status']=='Accepted') { ?>
                    <a href="/maker/entry/<?php echo $entryData['lead_id'];?>/edit">View/Edit Public Information</a>
                    <?php } ?>
                    <?php
                    $class = '';
                    $tooltip = '';
                    //edit link
                    if($disp_edit){
                      $url = do_shortcode('[gv_entry_link action="edit" return="url" view_id="478586" entry_id="'.$entryData['lead_id'].'"]');
                      $url = str_replace('/view/', '/', $url);  //remove view slug from URL
                      echo '<a href="'. $url .'">Edit Entry</a>';
                    }else{
                      echo  '<div class="disabled" data-placement="left"  data-toggle="tooltip" title="Only the main contact can edit">Edit Entry</div>';
                    }
                    ?>
                  </div>
                  <div>
                    <?php
                    //cancel link - only shown if Status is not currently Cancel
                    if($entryData['status']!='Cancelled'){
                      ?>
                      <a href="#cancelEntry" data-toggle="modal" data-entry-id="<?php echo $entryData['lead_id'];?>" data-projName="<?php echo $entryData['presentation_title'];?>">Cancel Entry</a>
                      <?php
                    }

                    ?>
                    <?php
                    //Delete Link
                    if($entryData['status']=='Proposed' || $entryData['status']=='In Progress'){
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
  <?php } //end foreach entry loop ?>

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
<?php echo $maker->createPageLinks( 'pagination pagination-sm' );?>
<?php get_footer(); ?>
