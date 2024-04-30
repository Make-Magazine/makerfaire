<?php

/**
 * Template Name: Maker Portal
 *
 * @version 1.0
 */
if (!is_user_logged_in())
  auth_redirect();

//get logged in users information
$current_user = wp_get_current_user();

get_header();
?>
<style>
  #content {
    background: #f9f9f9;
  }

  .card {
    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, .5);
  }

  .card-body {max-width:100%}
  .tooltip {
    top: -32px !important;
  }
</style>
<div id="manageEntries" style="width:95%; margin: 35px auto;" class="maker-portal">
  <input type="hidden" id="user_email" value="<?php echo $current_user->user_email; ?>" />
  <div class="row">
    <h1 style="text-align:center">Hello <?php echo $current_user->user_email; ?></h1>

    <h4 id="loadingMsg">Please wait while we retrieve your submitted entries.</h4>
    <div v-for="(faire, faire_name) in faire_entries" style="margin-bottom:50px">
      <h2>{{faire_name}} Entries</h2>

      <b-card :id="entry.project_id" v-for="entry in faire.entries" :key="entry.project_id" style="margin-bottom:50px;">
        <input type="hidden" name="entry_info_entry_id" :value=entry.project_id />
        <b-row fluid>
          <b-col md="3" sm="12">
            <b-img-lazy thumbnail fluid :src="entry.photo" :alt="entry.project_name"></b-img>
          </b-col>
          <b-col md="9" sm="12">
            <b-row align-v="baseline" align-h="between">
              <b-col md="8" sm="12">
                <h3>{{entry.project_name}}</h3>
              </b-col>
              <b-col><span :class="'status_'+entry.project_id">{{entry.status}}</span></b-col>
              <b-col md="2" sm="5" style="color: #ccc;">{{entry.project_id}}</b-col>
            </b-row>
            <b-row> <!-- Link Row -->
              <b-col md="auto" sm="12" lg="8">
                <span v-if="entry.req_entry_type !== null && entry.req_entry_type !== '' ">
                  <b>Requested Entry Type:</b> {{entry.req_entry_type}}
                  <br />
                </span>
                <span v-if="entry.entry_type  !== null ">
                  <b>Programmed Entry Type(s):</b> {{entry.entry_type}}
                </span>
              </b-col>
              <b-col>
                <span class="editLink">
                  <a :href="'/maker/entry/'+entry.project_id">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                    <span v-if="entry.status=='Accepted'">View My Public Page</span>
                    <span v-else>Preview My Maker Page</span>
                  </a>
                </span>

              </b-col>

            </b-row>
            <b-row style="padding:20px 10px;"><!-- MAT messaging -->
              <b-col sm="12" style="border: thin solid grey; padding: 10px">
                <div style="text-align:center" v-html='faire.maker_messaging'></div>
              </b-col>
            </b-row>

            <div style="margin-top: auto; padding-top: 15px; font-size: 20px">
              <b-row align-h="between"><!-- Tickets/Tasks/Manage-->
                <b-col><!-- Tasks - This should only show for current faire -->
                  <span v-if="entry.tasks.toDo.length || entry.tasks.done.length">
                    <b-button v-b-tooltip.hover title="My Tasks" :id="'entry-tasks-'+entry.project_id" variant="primary" class="notifications-button">
                      <i class="fas fa-tasks"></i>
                      <div class="notification-counter toggle-popover" data-toggle="popover" :data-count="entry.tasks.toDo.length">{{entry.tasks.toDo.length}}</div>
                    </b-button>

                    <b-popover ref="popover" :target="'entry-tasks-'+entry.project_id" title="My Tasks">
                      <b-row v-for="toDo in entry.tasks.toDo">
                        <b-col>
                          <div class="manage-links">
                            <a target="_blank" :href="toDo.action_url">{{toDo.description}}</a>
                          </div>
                        </b-col>
                        <b-col>
                          <span class="todoTasks" style="color:red">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>To Do
                          </span>
                        </b-col>
                      </b-row>
                      <b-row v-for="done in entry.tasks.done">
                        <b-col>{{done.description}}</b-col>
                        <b-col>
                          <span class="doneTasks" style="color:green">
                            <i class="fa fa-check" aria-hidden="true"></i>Done
                          </span>
                        </b-col>
                      </b-row>
                    </b-popover>
                  </span>
                </b-col>

                <b-col><!-- tickets - This should only show for current faire -->
                  <span v-if="entry.tickets.length">
                    <b-button v-b-tooltip.hover title="Get My Tickets" :id="'entry-tickets-'+entry.project_id" variant="primary" class="notifications-button">
                      <i class="fas fa-ticket"></i>
                    </b-button>

                    <b-popover ref="popover" :target="'entry-tickets-'+entry.project_id" title="My Tickets">

                      <!--<b-collapse :id="'entry-tickets-'+entry.project_id" class="mt-2">-->

                      <b-row v-for="ticket in entry.tickets">
                        <b-col cols="10">
                          <a target="_blank" :href="ticket.link">
                            <div class="title">{{ticket.title}}</div>
                            <div class="subtitle">{{ticket.subtitle}}</div>
                          </a>
                        </b-col>
                        <b-col cols="2">
                          <a target="_blank" :href="ticket.link">
                            <i class="fa fa-chevron-circle-right" aria-hidden="true"></i>
                          </a>
                        </b-col>
                      </b-row>

                      <!--</b-collapse>-->
                    </b-popover>
                </b-col>
                <b-col cols="1" align-self="end"><!-- Manage Entry-->
                  <b-button v-b-tooltip.hover title="Manage My Entry" :id="'entry-manage-'+entry.project_id" variant="primary" class="notifications-button">
                    <i class="fas fa-cog"></i>
                  </b-button>

                  <!--<b-collapse :id="'entry-manage-'+entry.project_id" class="mt-2">-->
                  <b-popover ref="popover" :target="'entry-manage-'+entry.project_id" title="Manage My Entry">              
                    <span style="color:red">X</span> <a href="#cancelEntry" data-toggle="modal" :data-entry-id="entry.project_id" :data-projName="entry.project_name">Cancel Entry</a>
                    <br/>
                    <a target="_blank" :href="entry.gv_edit_link"><i class="fas fa-edit" aria-hidden="true"></i>Edit Submitted Info</a>
                    <span v-if="entry.ep_token!=''"> 
                      <div>
                      <a target="_blank" :href="'/bay-area/logistics-information/?ep_token='+entry.ep_token"><i class="fas fa-edit" aria-hidden="true"></i>Manage Logistics Info</a>
                      </div>
                    </span>  
                    <!--<b-col md="auto" sm="12"></b-col>
                    <b-col md="auto" sm="12"><a href="/bay-area/public-information/?ep_token="><i class="fas fa-edit" aria-hidden="true"></i>Manage Public Info</a></b-col>-->
                    <!--</b-collapse>-->
                  </b-popover>

                </b-col>

              </b-row>
            </div>


          </b-col>
        </b-row>
        <!--
        <b-row>
          <b-col v-if="Date.now() < new Date(entry.faire_end_dt)">
            <b-row>
              <b-col>Current Faire</b-col>
            </b-row>
          </b-col>
          <b-col v-if="Date.now() > new Date(entry.faire_end_dt)">
            Past Faire
          </b-col>
        </b-row>              -->
      </b-card>
    </div>
  </div>
</div>
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
          <p>Sorry you can't make it. Why are you canceling?</p><br />
          <textarea rows="4" cols="50" name="cancelReason"></textarea>
        </div>
        <span id="cancelResponse"></span><br />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="submitCancel">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>