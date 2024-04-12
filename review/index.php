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

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

        <title>Maker Faire Admin Review</title>

    </head>

    <body>
        <h2>Maker Faire Admin Review</h2>

        <!-- Our application root element -->
        <div id="review" style="width:95%; margin:0 auto;">
            <select id="form_select" v-on:change="updateForm">
                <?php
                foreach ($form_list as $form_key => $form) {
                    echo '<option value="' . $form_key . '">' . $form . '</option>';
                }
                ?>
            </select>
            <b-container fluid class="filter-options">
                <input type="text" v-model="searchQuery" placeholder="Search..." />
                <div class="select-wrapper">
                    <label>Status</label>
                    <select v-model="selectedStatus">
                        <option type="select" value="">All</option>
                        <template v-for="status in filteredStatus">
                            <option type="select" :id="status" :name="status" :value="status">{{status}}</option>
                        </template>
                    </select>
                </div>
                <div class="select-wrapper">
                    <label>Primary Category</label>
                    <select v-model="selectedPrimeCat">
                        <option type="select" value="">All</option>
                        <template v-for="prime_cat in filteredPrimeCat">
                            <option type="select" :id="prime_cat" :name="prime_cat" :value="prime_cat">{{prime_cat}}</option>
                        </template>
                    </select>
                </div>
                {{ filterBy.length }} results
                <div class="listGrid-toolbar">
                    <!-- <span class="listGrid-caption">{{caption}}</span> -->
                    <i class="bi bi-arrow-down-up" v-if="makers.length>0" @click="switchDateOrder" title="See Oldest" style="margin-right:5px;"></i>
                    <span class="listGrid-switch-iconGroup">
                        <i class="bi bi-list listGrid-switch-icon" v-bind:class="{ active: currentView=='list'}" aria-hidden="true" :title="currentView=='grid' ? 'switch to List View': 'List View'" v-on:click="switchToListView"></i>
                        <i class="bi bi-grid listGrid-switch-icon" v-bind:class="{ active: currentView=='grid'}" :title="currentView=='list' ? 'switch to Grid View': 'Grid View'" aria-hidden="true" v-on:click="switchToGridView"></i>
                    </span>
                </div>
                <div>

                </div>
            </b-container>
            <div v-if="currentView=='grid'">
                <b-card-group deck>
                    <b-card no-body v-for="(maker, maker_id) in filterBy.slice((currentPage-1)*perPage,(currentPage-1)*perPage+perPage)" :title="maker.project_name" img-top :key="'grid-'+maker.project_id" @click="expandCard(maker.project_id)">
                        <template #header>
                            <b-img thumbnail fluid class="grid-image" :alt="maker.project_name" :src="maker.photo" />
                            <h5 class="hidden">{{maker.maker_name}}</h5>
                        </template>
                        <b-card-text>
                            {{maker.description}}
                        </b-card-text>
                        <template #footer>
                            <small class="text-muted">{{maker.status}}</small>
                        </template>
                    </b-card>

                    <b-pagination v-if="filterBy.length>0" v-model="currentPage" :total-rows="filterBy.length" :per-page="perPage" first-text="First" prev-text="Prev" next-text="Next" last-text="Last" aria-controls="Grid">
                    </b-pagination>

                </b-card-group>
            </div>

            <div v-if="currentView=='list'">
                <b-pagination v-if="filterBy.length>0" v-model="currentPage" :total-rows="filterBy.length" :per-page="perPage" first-text="First" prev-text="Prev" next-text="Next" last-text="Last"></b-pagination>
                <b-card no-body :id="'listView-' + maker.project_id" v-for="(maker, maker_id) in filterBy.slice((currentPage-1)*perPage,(currentPage-1)*perPage+perPage)" :key="'list-' + maker.project_id">
                    <input type="hidden" name="entry_info_entry_id" :value=maker.project_id />
                    <b-row class="header">
                        <b-col cols="9">
                            <h3>{{maker.project_name}}</h3>
                        </b-col>
                        <b-col cols="2">{{maker.status}}</b-col>
                        <b-col cols="1" style="color: #ccc;">{{maker.project_id}}</b-col>
                    </b-row>
                    <b-tabs card v-bind:id=maker.project_id>
                        <b-tab v-for="(tab,tabKey) in maker.tabs" :key="tabKey+'-'+maker_id" :title="tab.title">
                            <b-card-text>
                                <b-container fluid class="bv-example-row">
                                    <!-- Initial section-->
                                    <b-row v-for="(block,block_id) in tab.tab_content.initial.blocks" :key="block_id">
                                        <b-col v-for="(fields, column_id) in block.columns" :key="'dyn-column-' + column_id">
                                            <div v-for="(field, field_id) in fields" :key="maker_id-'field-' + field_id">
                                                <div v-if="field.value">
                                                    <label class="fieldLabel">{{ field.label }}</label>
                                                    <div class="image-wrapper" v-if="field.type === 'fileupload'">
                                                        <b-img thumbnail fluid :src="field.value" :alt="field.label"></b-img>
                                                    </div>
                                                    <span v-else-if="field.type === 'multipleFiles'">
                                                        <b-container class="p-4 bg-dark">
                                                            <b-row>
                                                                <b-col fluid="sm" :key="maker.project_id+'-img-' + image_id" :id="maker.project_id+'-img-' + image_id" v-for="(image,image_id) in field.value">
                                                                    <picture v-bind="'image' + image_id">
                                                                        <img v-bind:src="image" v-bind:alt="field.label" @click="showModal(image)" />
                                                                    </picture>
                                                                </b-col>
                                                            </b-row>
                                                        </b-container>
                                                    </span>
                                                    <span v-else-if="field.type === 'website'">
                                                        <b-link :href="field.value" target="_blank">{{field.value}}</b-link>
                                                    </span>
                                                    <!--                                             
                                                <span v-else-if="field.type === 'video'">
                                                    <b-embed type="iframe" aspect="16by9" :src="field.value"
                                                        allowfullscreen></b-embed>
                                                </span>-->
                                                    <span v-else-if="field.type === 'notes'">
                                                        <b-list-group>
                                                            <b-list-group-item v-for="(note,i) in field.value" :key="maker_id+'-note-' + i">
                                                                <b-row>
                                                                    <b-col>{{note.date_created}}</b-col>
                                                                    <b-col>{{note.user_name}}</b-col>
                                                                    <b-col cols="8">{{note.value}}</b-col>
                                                                </b-row>
                                                            </b-list-group-item>
                                                        </b-list-group>
                                                    </span>
                                                    <span v-else-if="field.type === 'checkbox'">
                                                        <ul>
                                                            <li v-for="(value,i) in field.value" :key="maker_id+'-list-' + i">
                                                                {{value}}
                                                            </li>
                                                        </ul>
                                                    </span>
                                                    <span v-else-if="field.type === 'listRepeat'">
                                                        <div v-for="(value,i) in field.value">
                                                            <b-table striped hover :items="value"></b-table>
                                                        </div>

                                                    </span>
                                                    <span v-else-if="field.type === 'list'">
                                                        <b-table striped hover :items="field.value"></b-table>
                                                    </span>
                                                    <span v-else-if="field.type === 'address'">
                                                        <b-list-group>
                                                            <b-list-group-item v-for="(value,i) in field.value" :key="maker_id+'-address-' + i">
                                                                {{value.label}} - {{value.value}}
                                                            </b-list-group-item>
                                                        </b-list-group>
                                                    </span>
                                                    <span v-else-if="field.type === 'html'" v-html="field.value"></span>
                                                    <span v-else>
                                                        {{field.value}}
                                                    </span>
                                                </div>
                                            </div>
                                        </b-col>
                                    </b-row>

                                    <!-- Expand Section -->
                                    <div v-if="tab.tab_content.expand">
                                        <b-button class="expand" v-b-toggle="'collapse-'+tabKey+maker.project_id" variant="primary">
                                            <span class="when-opened">
                                                Less Info -
                                            </span>
                                            <span class="when-closed">
                                                More Info +
                                            </span>
                                        </b-button>
                                        <b-collapse :id="'collapse-'+tabKey+maker.project_id" class="mt-2">

                                            <b-row v-for="(block,block_id) in tab.tab_content.expand.blocks" :key="block_id">
                                                <b-col v-for="(fields, column_id) in block.columns" :key="'dyn-column-' + column_id">
                                                    <div v-for="(field, field_id) in fields" :key="maker_id-'field-' + field_id">
                                                        <div v-if="field.value">
                                                            <label class="fieldLabel">{{ field.label }}</label>
                                                            <div v-if="field.type === 'fileupload'">
                                                                <b-img thumbnail fluid :src="field.value" :alt="field.label"></b-img>
                                                            </div>
                                                            <span v-else-if="field.type === 'multipleFiles'">
                                                                <b-container class="p-4 bg-dark">
                                                                    <b-row>
                                                                        <b-col fluid="sm" :key="maker.project_id+'-img-' + image_id" :id="maker.project_id+'-img-' + image_id" v-for="(image,image_id) in field.value">
                                                                            <picture v-bind="'image' + image_id">
                                                                                <img v-bind:src="image" v-bind:alt="field.label" @click="showModal(image)" />
                                                                            </picture>
                                                                        </b-col>
                                                                    </b-row>
                                                                </b-container>
                                                            </span>
                                                            <span v-else-if="field.type === 'website'">
                                                                <b-link :href="field.value" target="_blank">{{field.value}}</b-link>
                                                            </span>
                                                            <!--
                                                <span v-else-if="field.type === 'video'">
                                                    <b-embed type="iframe" aspect="16by9" :src="field.value"
                                                        allowfullscreen></b-embed>
                                                </span>-->
                                                            <span v-else-if="field.type === 'notes'">
                                                                <b-list-group>
                                                                    <b-list-group-item v-for="(note,i) in field.value" :key="maker_id+'-note-' + i">
                                                                        <b-row>
                                                                            <b-col>{{note.date_created}}</b-col>
                                                                            <b-col>{{note.user_name}}</b-col>
                                                                            <b-col cols="8">{{note.value}}</b-col>
                                                                        </b-row>
                                                                    </b-list-group-item>
                                                                </b-list-group>
                                                            </span>
                                                            <span v-else-if="field.type === 'checkbox'">
                                                                <ul>
                                                                    <li v-for="(value,i) in field.value" :key="maker_id+'-list-' + i">
                                                                        {{value}}
                                                                    </li>
                                                                </ul>
                                                            </span>
                                                            <span v-else-if="field.type === 'listRepeat'">
                                                                <div v-for="(value,i) in field.value">
                                                                    <b-table striped hover :items="value"></b-table>
                                                                </div>

                                                            </span>
                                                            <span v-else-if="field.type === 'list'">
                                                                <b-table striped hover :items="field.value"></b-table>
                                                            </span>
                                                            <span v-else-if="field.type === 'address'">
                                                                <b-list-group>
                                                                    <b-list-group-item v-for="(value,i) in field.value" :key="maker_id+'-address-' + i">
                                                                        {{value.label}} - {{value.value}}
                                                                    </b-list-group-item>
                                                                </b-list-group>
                                                            </span>
                                                            <span v-else-if="field.type === 'html'" v-html="field.value"></span>
                                                            <span v-else>
                                                                {{field.value}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </b-col>
                                            </b-row>

                                        </b-collapse>
                                    </div>
                                </b-container>
                            </b-card-text>
                        </b-tab>
                    </b-tabs>
                </b-card>

                <b-pagination v-if="filterBy.length>0" v-model="currentPage" :total-rows="filterBy.length" :per-page="perPage" first-text="First" prev-text="Prev" next-text="Next" last-text="Last">
                </b-pagination>
            </div>
            <div class="no-results" v-if="!filterBy.length && makers.length">No Results to Show</div>
            <div id="loader" v-if="makers.length==0">
                <img src="/review/img/loading.gif" />
            </div>

            <b-modal id="image-modal" hide-footer>
                <img :src='selectedImgPath' />
            </b-modal>

        </div>

        <!-- Required scripts, vue loads first -->
        <script src="/review/js/min/vue.min.js"></script>
        <script src="/review/js/min/review.min.js"></script>
        <script src="/wp-includes/js/jquery/jquery.min.js" id="jquery-core-js"></script>

    </body>
    <footer>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- Required Stylesheets -->
        <link type="text/css" rel="stylesheet" href="css/style.min.css" />
        <!-- Load polyfills to support older browsers -->
        <script src="https://polyfill.io/v3/polyfill.min.js?features=es2015%2CIntersectionObserver"></script>
    </footer>

    </html>