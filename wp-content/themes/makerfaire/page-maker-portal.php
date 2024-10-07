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
$user_email = $current_user->user_email;
get_header();
?>
<style>
  #content {
    background: #f9f9f9;
  }
  .floating-footer { display: none !important; }
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

  .popover-body a .fas {
    margin-right: 8px;
  }

  .tasks-row .align-self-end {
    text-align: right;
  }
</style>
<div id="manageEntries" style="width:95%; margin: 35px auto;margin-bottom: 0px;" class="maker-portal">

  <?php
  //only allow alicia, rio and webmaster to test emails
  if (
    $current_user->user_email == 'alicia@make.co'    ||
    $current_user->user_email == 'rio@make.co'       ||
    $current_user->user_email == 'webmaster@make.co' ||
    $current_user->user_email == 'siana@make.co'     ||
    $current_user->user_email == 'nicole@make.co'    ||
    $current_user->user_email == 'jamie@make.co'
  ) {
    //if (in_array('administrator', $current_user->roles)) { //allow admins to test any email		
    global $wp;
    $current_slug = add_query_arg(array(), $wp->request);

    echo '<div class="account-form-wrapper" style="text-align:center">                            
        <div class="account-form">							
          <form action="/' . $current_slug . '">
            <b>Admin only</b> Enter in an email to see what user sees.
            <input id="test_email" name="test_email" value="" />
            <input type="submit" value="Submit">
            ' . (isset($_GET['test_email']) ? '<br/><i>Testing Email: ' . $_GET['test_email'] . '</i>' : '') . '
          </form>
        </div>
      </div>
      <br/>';

    if (isset($_GET['test_email']) && $_GET['test_email'] != '') {
      $user_email = $_GET['test_email'];
    }
  }
  ?>
  <input type="hidden" id="user_email" value="<?php echo $user_email; ?>" />
  <div class="row">
    <h1 style="text-align:center">Hello <?php echo $user_email; ?></h1>

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
              <b-col cols="6"><span :class="'status_'+entry.project_id">{{entry.status}}</span></b-col>
              <b-col cols="6" md="2" sm="5" style="color: #ccc;">{{entry.project_id}}</b-col>
            </b-row>
            <b-row> <!-- Link Row -->
              <b-col md="8" sm="12">
                <span v-if="entry.req_entry_type !== null && entry.req_entry_type !== '' ">
                  <b>Requested Entry Type:</b> {{entry.req_entry_type}}
                  <br />
                </span>
                <span v-if="entry.entry_type  !== null ">
                  <b>Participating As:</b> {{entry.entry_type}}
                </span>
              </b-col>

              <b-col>
                <span>
                  <div v-if="entry.status=='Accepted'">
                    <a target="_blank" :href="'/maker/entry/'+entry.project_id+'/'">
                      <i class="fa fa-eye" aria-hidden="true"></i>
                      Share My Entry Page
                    </a>
                  </div>
                  
                  <div v-if="Date.now() <= new Date(faire.faire_end_dt).getTime()">
                    <a target="_blank" :href="'/maker/entry/'+entry.project_id+'/edit/'">
                      <i class="fa fa-edit" aria-hidden="true"></i>
                      Edit My Entry Page
                    </a>
                  </div>
                </span>
              </b-col>
            </b-row>
            <b-row style="padding:20px 10px;"><!-- Form specific messaging -->
              <b-col sm="12" style="border: thin solid grey; padding: 10px">
                <div style="text-align:center" v-html='entry.maker_message'></div>
              </b-col>
            </b-row>

            <!-- this data is only shown on active faires -->
            <span v-if="Date.now() <= new Date(faire.faire_end_dt).getTime()">
              <b-row>
                <b-col cols="12" sm="6" style="margin-bottom:10px;">
                  <b-row v-if="entry.links.length" v-for="link in entry.links">
                    <b-col>
                      <a :href="link.link" target="_blank">{{link.title}}</a>
                      <span v-if="entry.res_message!='' && link.title.includes('Exhibit')" class="setup-btn-wrapper"><!-- resource messaging -->
                        <b-button v-b-toggle="'collapse-'+entry.project_id" variant="primary">Check Setup</b-button>
                        <b-collapse :id="'collapse-'+entry.project_id" class="mt-2">
                          <b-card>
                            <div style="text-align:center" v-html='entry.res_message'></div>
                          </b-card>
                        </b-collapse>
                      </span>
                    </b-col>
                  </b-row>
                </b-col>
                <b-col cols="12" sm="6">
                  <b-row align-h="between" v-if="entry.status!='Cancelled'" class="tasks-row"><!-- Tickets/Tasks/Manage Section-->
                    <b-col cols="7"><!-- Tasks-->
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

                    <b-col cols="5" align-self="end"><!-- Manage Entry-->
                      <b-button v-b-tooltip.hover title="Manage My Entry" :id="'entry-manage-'+entry.project_id" variant="primary" class="notifications-button">
                        <i class="fas fa-cog"></i>
                      </b-button>

                      <b-popover ref="popover" :target="'entry-manage-'+entry.project_id" :id="'manage-popover-'+entry.project_id" title="Manage My Entry">
                        <span v-if="entry.status !='Cancelled' && entry.status!='Rejected'">
                          <p>
                            <a href="#" @click="cancelModal('cancelModal'+entry.project_id, 'manage-popover-'+entry.project_id)" style="text-decoration:none;">
                              <span style="color:red">
                                <i class="fas fa-times"></i> Cancel Entry
                              </span>
                            </a>
                          </p>
                        </span>

                        <p><a :href="entry.gv_edit_link"><i class="fas fa-edit" aria-hidden="true"></i>Edit Full Entry</a></p>
                        <p><a href="https://makerfaire.com/bay-area/maker-shipping/"><i class="fas fa-truck" aria-hidden="true"></i>Shipping Form</a></p>
                        <p><a href="https://makerfaire.com/bay-area/special-request-form/"><i class="fas fa-sparkles" aria-hidden="true"></i>Special Requests</a></p>
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
              </b-row>
            </span>



          </b-col>
        </b-row>
        <!-- this data is only shown on active faires -->
        <span v-if="Date.now() <= new Date(faire.faire_end_dt).getTime()">
          <div class="mat-ticketing" v-if="entry.status=='Accepted' && entry.tickets.length">
            <b-row><b-col>
                <h2 style="text-align:center">My Entry Passes</h2>
              </b-col></b-row>
            <b-row v-for="ticket in entry.tickets"><!-- tickets - This should only show for current faire -->
<<<<<<< HEAD

              <b-col>
                <a target="_blank" :href="ticket.link" class="ticket-btn">
                  <div class="title"><i aria-hidden="true" class="fas fa-ticket"></i> {{ticket.title}}</div>
                  <div class="subtitle">{{ticket.subtitle}}</div>
                </a>
              </b-col>

=======
                <b-col>
                  <a target="_blank" :href="ticket.link" class="ticket-btn">
                      <div class="title"><i aria-hidden="true" class="fas fa-ticket"></i> {{ticket.title}}</div>
                      <div class="subtitle">{{ticket.subtitle}}</div>
                  </a>
                </b-col>  
>>>>>>> 868a9a63d6367b1ece6b9e74d6b0cfa0ee2fc37b
            </b-row>
          </div>
        </span>

      </b-card>
    </div>
  </div>
</div>

<?php get_footer(); ?>