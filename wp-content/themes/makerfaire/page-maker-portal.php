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

  .card-body {
    max-width: 100%
  }

  .tooltip {
    top: -32px !important;
  }

  .modal-title {
    width: 100%;
  }

  #cancelText textarea {
    width: 100%;
  }

  #cancelText ::placeholder {
    color: #848484;
    font-style: italic;
  }
</style>
<div id="manageEntries" style="width:95%; margin: 35px auto;" class="maker-portal">
  <input type="hidden" id="user_email" value="<?php echo $current_user->user_email; ?>" />
  <div class="row">
    <h1 style="text-align:center">Hello <?php echo $current_user->user_email; ?></h1>

    <h4 id="loadingMsg">Please wait while we retrieve your submitted entries.</h4>
    <div v-if="showData" v-for="(faire, faire_name) in faire_entries" style="margin-bottom:50px">
      <h2 v-if="Date.now() < new Date(faire.faire_end_dt) || faire.entries.length!=0">{{faire_name}} Entries</h2>
      <span v-if="Date.now() < new Date(faire.faire_end_dt) && faire.entries.length==0">
        I'm sorry. We could not find any entries for your email.<br />Please submit one <a href='https://makerfaire.com/bay-area/apply'>HERE</a>
      </span>

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
                  <!--<span class="editLink" v-if="entry.status !='Cancelled' && entry.status!='Rejected'">-->
                  <a target="_blank" :href="'/maker/entry/'+entry.project_id+'/edit/'">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                    <span v-if="entry.status=='Accepted'">View My Public Page</span>
                    <span v-else>Preview My Public Page</span>
                  </a>
                </span>
              </b-col>
            </b-row>
            <b-row style="padding:20px 10px;"><!-- MAT messaging -->
              <b-col sm="12" style="border: thin solid grey; padding: 10px">
                <div style="text-align:center" v-html='faire.maker_messaging'></div>
              </b-col>
            </b-row>

            <!-- logistic links -->
            <span v-if="entry.links.length">
              <b-row>
                <b-col v-for="link in entry.links">
                  <a :href="link.link" target="_blank">{{link.title}}</a>
                </b-col>
              </b-row>
            </span>       

            <div style="margin-top: auto; padding-top: 15px; font-size: 20px">
              <b-row align-h="between" v-if="entry.status!='Cancelled'"><!-- Tickets/Tasks/Manage Section-->
                <b-col><!-- Tasks - This should only show for current faire -->
                  <span v-if="entry.tasks.toDo.length || entry.tasks.done.length">
                    <b-button v-b-tooltip.hover title="My Tasks" :id="'entry-tasks-'+entry.project_id" variant="primary" class="notifications-button">
                      <i class="fas fa-tasks"></i>
                      <div class="notification-counter toggle-popover" data-toggle="popover" :data-count="entry.tasks.toDo.length">{{entry.tasks.toDo.length}}</div>
                    </b-button>

                    <b-popover ref="popover" :target="'entry-tasks-'+entry.project_id" :id="'entry-tasks-'+entry.project_id" title="My Tasks">
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

                  <b-popover ref="popover" :target="'entry-manage-'+entry.project_id" :id="'manage-popover-'+entry.project_id" title="Manage My Entry">
                    <span v-if="entry.status !='Cancelled' && entry.status!='Rejected'">
                      <p>
                        <a href="#" @click="cancelModal('cancelModal'+entry.project_id, 'manage-popover-'+entry.project_id)">
                          <span style="color:red">
                            <i class="fas fa-times"></i> Cancel Entry
                          </span>
                        </a>
                      </p>
                    </span>

                    <p><a :href="entry.gv_edit_link"><i class="fas fa-edit" aria-hidden="true"></i>Edit Submitted Info</a></p>

                    <!--<span v-if="entry.ep_token!=''"> 
                      <div>
                      <a target="_blank" :href="'/bay-area/logistics-information/?ep_token='+entry.ep_token"><i class="fas fa-edit" aria-hidden="true"></i>Manage Logistics Info</a>
                      </div>
                    </span>  -->
                    <!--<b-col md="auto" sm="12"></b-col>
                    <b-col md="auto" sm="12"><a href="/bay-area/public-information/?ep_token="><i class="fas fa-edit" aria-hidden="true"></i>Manage Public Info</a></b-col>-->
                  </b-popover>

                  <b-modal :id="'cancelModal'+entry.project_id" size="lg" :title="'Cancel '+entry.project_name+', Entry ID:'+entry.project_id">
                    <template #modal-header="{ close }">
                      <h5>"<span id="projName">{{entry.project_name}}</span>" Exhibit ID: <span id="cancelEntryID" name="entryID">{{entry.project_id}}</span></h5>
                    </template>

                    <div id="cancelText">
                      <p>Are you sure you want to cancel?</p><br />
                      <textarea rows="4" cols="50" name="cancelReason" placeholder="Please let us know why you are cancelling your Maker Faire entry"></textarea>
                    </div>
                    <template #modal-footer="{ ok, cancel }">
                      <span id="cancelResponse"></span><br />
                      <b-button id="cancelButton" size="sm" variant="outline" @click="cancel()">
                        No, go back.
                      </b-button>
                      <b-button id="submitButton" size="sm" variant="danger" @click="submitCancel(entry.project_id)">
                        Yes, Cancel.
                      </b-button>
                    </template>
                  </b-modal>
                </b-col>
              </b-row>
            </div>


          </b-col>
        </b-row>

      </b-card>
    </div>
  </div>
</div>

<?php get_footer(); ?>