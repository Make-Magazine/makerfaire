    <?php
    if (!defined('WP_ADMIN')) {
        define('WP_ADMIN', false);
    }

    require_once '../wp-load.php';

    if (!is_user_logged_in())
        auth_redirect();
    
    if ( !current_user_can( 'admin_review' ) ) {
        echo 'The current user can not access this page.';
        die();
    }

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
    <div id="review" style="width:95%; margin: 35px auto;display:none;">            
        <b-row>
            <b-col align-self="center" cols="8">
                <h2>Maker Faire Admin Review</h2>
            </b-col>
            <b-col align-self="center" cols="4" class="text-right form-select">
                <select id="form_select" v-on:change="updateForm" text-right>
                    <?php
                    foreach ($form_list as $form_key => $form) {
                        echo '<option value="' . $form_key . '">' . $form . '</option>';
                    }
                    ?>
                </select>
                <b-button @click="updateForm" variant="outline-primary" v-b-tooltip.hover title="Refresh Data"><i class="bi bi-arrow-repeat"></i></b-button>    
            </b-col>
        </b-row>
        
        <hr/>
        <?php  include('templates/filters.html'); ?>        

        <hr/>
        <b-row align-h="between" class="admin-actions">
            <b-col cols="2">
                {{filterBy.length.toLocaleString()}} Entries
            </b-col>
            <b-col>
                <b-pagination v-if="filterBy.length>0" v-model="currentPage" @page-click="pagClick(event, currentPage)"
                    :total-rows="filterBy.length" 
                    :per-page="perPage" 
                    prev-text="<" 
                    next-text=">" 
                    aria-controls="Grid">
                </b-pagination>
            </b-col>
            <b-col cols="2">
                <div class="listGrid-toolbar text-right">
                    <i class="bi bi-arrow-down-up" v-if="makers.length>0" @click="switchDateOrder" v-b-tooltip.hover title="See Oldest" style="margin-right:5px;"></i>
                    <span class="listGrid-switch-iconGroup">
                        <i class="bi bi-list listGrid-switch-icon"  v-b-tooltip.hover v-bind:class="{ active: currentView=='list'}" aria-hidden="true" :title="currentView=='grid' ? 'Switch to List View': 'List View'" v-on:click="switchToListView"></i>
                        <i class="bi bi-grid listGrid-switch-icon"  v-b-tooltip.hover v-bind:class="{ active: currentView=='grid'}" :title="currentView=='list' ? 'Switch to Grid View': 'Grid View'" aria-hidden="true" v-on:click="switchToGridView"></i>
                    </span>
                </div>
            </b-col>
        </b-row>
        <b-row align-h="between" v-if="selectedStatus!='' || selectedEntryType!='' || selectedPrimeCat!='' || selectedFlag!='' || selectedPrelimLoc!=''">
            <b-col cols="12">Filters: 
                <b-badge pill variant="primary" v-if="selectedStatus">{{selectedStatus.toString().trim()}}</b-badge>
                <b-badge pill variant="primary" v-if="selectedEntryType">{{selectedEntryType.toString().trim()}}</b-badge>
                <b-badge pill variant="primary" v-if="selectedPrimeCat">{{selectedPrimeCat.toString().trim()}}</b-badge>
                <b-badge pill variant="primary" v-if="selectedFlag">{{selectedFlag.toString().trim()}}</b-badge>
                <b-badge pill variant="primary" v-if="selectedPrelimLoc">{{selectedPrelimLoc.toString().trim()}}</b-badge>
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
