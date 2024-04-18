    <?php
    if (!defined('WP_ADMIN')) {
        define('WP_ADMIN', false);
    }

    require_once '../wp-load.php';

    if (!is_user_logged_in())
        auth_redirect();


    $form_list = array();
    $forms = GFAPI::get_forms(true, false, 'id', 'DESC');
    foreach ($forms as $form) {
        if (isset($form['form_type']) && $form['form_type'] == 'Master') {
            $form_list[$form['id']] = $form['title'];
        }
    }

    include 'templates/header.php';
    ?>
    <!-- Our application root element -->
    <div id="review" style="width:95%; margin: 35px auto;">
        <button-counter></button-counter>
        <b-row>
            <b-col align-self="center" cols="8">
                <h2>Maker Faire Admin Review</h2>
            </b-col>
            <b-col align-self="center" cols="4">
                <select id="form_select" v-on:change="updateForm">
                    <?php
                    foreach ($form_list as $form_key => $form) {
                        echo '<option value="' . $form_key . '">' . $form . '</option>';
                    }
                    ?>
                </select>
                <b-button @click="updateForm" variant="outline-primary">Refresh Data</b-button>    
            </b-col>
        </b-row>
        
        <hr/>
        <?php  include('templates/filters.html'); ?>        

        <hr/>
        <b-row align-h="between">
            <b-col>
                Found {{filterBy.length.toLocaleString()}} Results
            </b-col>
            <b-col cols="2">
                <div class="listGrid-toolbar">
                    <i class="bi bi-arrow-down-up" v-if="makers.length>0" @click="switchDateOrder" title="See Oldest" style="margin-right:5px;"></i>
                    <span class="listGrid-switch-iconGroup">
                        <i class="bi bi-list listGrid-switch-icon" v-bind:class="{ active: currentView=='list'}" aria-hidden="true" :title="currentView=='grid' ? 'switch to List View': 'List View'" v-on:click="switchToListView"></i>
                        <i class="bi bi-grid listGrid-switch-icon" v-bind:class="{ active: currentView=='grid'}" :title="currentView=='list' ? 'switch to Grid View': 'Grid View'" aria-hidden="true" v-on:click="switchToGridView"></i>
                    </span>
                </div>
            </b-col>
        </b-row>

        <div v-if="currentView=='grid'">
            <?php  include('templates/grid.html'); ?>
        </div>

        <div v-if="currentView=='list'">
            <?php  include('templates/list.php'); ?>
        </div>

        <div class="no-results" v-if="!filterBy.length && makers.length">No Results to Show</div>
        <div id="loader" v-if="makers.length==0">
            <img src="/review/img/loading.gif" />
        </div>
    </div>
    <?php
    include 'templates/footer.php';
