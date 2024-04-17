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

    include 'header.php';
    ?>
    <!-- Our application root element -->
    <div id="review" style="width:95%; margin: 35px auto;">
        <button-counter></button-counter>
        <b-row>
            <b-col align-self="center" cols="9">
                <h2>Maker Faire Admin Review</h2>
            </b-col>
            <b-col align-self="center" cols="3">
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

        
        <b-row align-h="between">
            <b-col align-self="center"><input type="text" v-model="searchQuery" placeholder="Search..." /></b-col>
            <b-col>
                <label>Admin Entry Type</label>
                <b-form-group label="" v-slot="{ ariaDescribedby }">
                    <b-form-checkbox-group id="entryTypeFilter" v-model="selectedEntryType" :aria-describedby="ariaDescribedby" name="entryTypeFil">
                        <b-form-checkbox v-for="type in filteredEntryType" :value="type">{{type}}</b-form-checkbox>
                    </b-form-checkbox-group>
                </b-form-group></b-col>


            <b-col align-self="center">
                <div class="select-wrapper">
                    <label>Status</label>
                    <select v-model="selectedStatus">
                        <option type="select" value="">All</option>
                        <template v-for="status in filteredStatus">
                            <option type="select" :id="status" :name="status" :value="status">{{status}}</option>
                        </template>
                    </select>
                </div>
            </b-col>

            <b-col align-self="center">
                <div class="select-wrapper">
                    <label>Primary Category</label>
                    <select v-model="selectedPrimeCat">
                        <option type="select" value="">All</option>
                        <template v-for="prime_cat in filteredPrimeCat">
                            <option type="select" :id="prime_cat" :name="prime_cat" :value="prime_cat">{{prime_cat}}</option>
                        </template>
                    </select>
                </div>
            </b-col>
            <b-col align-self="center" cols="2"><b-button @click="resetFilters" variant="outline-primary">Reset Filters</b-button></b-col>
        </b-row>

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
            <b-card-group deck>
                <b-card no-body v-for="(maker, maker_id) in filterBy.slice((currentPage-1)*perPage,(currentPage-1)*perPage+perPage)" :title="maker.project_name" img-top :key="'grid-'+maker.project_id" @click="expandCard(maker.project_id)">
                    <template #header>
                        <b-img-lazy thumbnail fluid class="grid-image" :alt="maker.project_name" :src="maker.photo" />
                        <h5 class="hidden">{{maker.maker_name}}</h5>
                    </template>
                    <b-card-text>
                        {{maker.description}}
                    </b-card-text>
                    <template #footer>
                        <small class="text-muted"><span :class="'status_'+maker.project_id">{{maker.status}}</span></small>
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
                    <b-col cols="8">
                        <h3>{{maker.project_name}}</h3>
                    </b-col>
                    <b-col cols="1">{{maker.entry_type}}</b-col>
                    <b-col cols="2"><span :class="'status_'+maker.project_id">{{maker.status}}</span></b-col>
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
                                                    <b-img-lazy thumbnail fluid :src="field.value" :alt="field.label"></b-img>
                                                </div>
                                                <span v-else-if="field.type === 'multipleFiles'">
                                                    <b-container class="p-4 bg-dark">
                                                        <b-row>
                                                            <b-col fluid="sm" v-for="(image,image_id) in field.value">
                                                                <img :class="maker.project_id+'-img'+field_id" :id="maker.project_id+'-img-' + image_id" :src="image" alt="Image" @click="showModal(maker.project_id+'-img'+field_id, image_id)">
                                                            </b-col>

                                                        </b-row>
                                                    </b-container>
                                                </span>
                                                <span v-else-if="field.type === 'website'">
                                                    <b-link :href="field.value" target="_blank">{{field.value}}</b-link>
                                                </span>

                                                <span v-else-if="field.type === 'video'">
                                                    <b-button @click="showModal(maker.project_id+'-video-initial', 0)" variant="outline-primary">Show in Modal</b-button>
                                                    <div><a :class="maker.project_id+'-video-initial'" :href="field.value" target="_blank">{{field.value}}</a></div>
                                                </span>

                                                <span v-else-if="field.type === 'notes'" class="notes">
                                                    <b-list-group>
                                                        <b-list-group-item v-for="(note,i) in field.value" :key="maker_id+'-note-' + i">
                                                            <b-row>
                                                                <b-col cols="4">{{note.date_created}}</b-col>
                                                                <b-col cols="2">{{note.user_name}}</b-col>
                                                                <b-col cols="6"><span v-html='note.value'></span></b-col>
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
                                                    <b-table-simple striped responsive>
                                                        <b-thead>
                                                            <b-tr>
                                                                <b-th v-for="(listItem,label) in field.value[0]"><span v-html="label"></b-th>
                                                            </b-tr>
                                                        </b-thead>
                                                        <b-tbody>
                                                            <b-tr v-for="list in field.value">
                                                                <b-td v-for="listItem in list"><span v-html="listItem"></b-td>
                                                            </b-tr>
                                                        </b-tbody>
                                                    </b-table-simple>
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
                                                            <b-img-lazy thumbnail fluid :src="field.value" :alt="field.label"></b-img>
                                                        </div>
                                                        <span v-else-if="field.type === 'multipleFiles'">
                                                            <b-container class="p-4 bg-dark">
                                                                <b-row>
                                                                    <b-col fluid="sm" :key="maker.project_id+'-img-' + image_id" :id="maker.project_id+'-img-' + image_id" v-for="(image,image_id) in field.value">
                                                                        <img :class="maker.project_id+'-img'+field_id" :id="maker.project_id+'-img-' + image_id" :src="image" alt="Image" @click="showModal(maker.project_id+'-img'+field_id, image_id)">
                                                                    </b-col>
                                                                </b-row>
                                                            </b-container>
                                                        </span>
                                                        <span v-else-if="field.type === 'website'">
                                                            <b-link :href="field.value" target="_blank">{{field.value}}</b-link>
                                                        </span>

                                                        <span v-else-if="field.type === 'video'">
                                                            <b-button @click="showModal(maker.project_id+'-video-expand', 0)" variant="outline-primary">Show in Modal</b-button>
                                                            <div><a :class="maker.project_id+'-video-expand'" :href="field.value" target="_blank">{{field.value}}</a></div>
                                                        </span>

                                                        <span v-else-if="field.type === 'notes'" class="notes">
                                                            <b-list-group>
                                                                <b-list-group-item v-for="(note,i) in field.value" :key="maker_id+'-note-' + i">
                                                                    <b-row>
                                                                        <b-col cols="4">{{note.date_created}}</b-col>
                                                                        <b-col cols="2">{{note.user_name}}</b-col>
                                                                        <b-col cols="6"><span>{{note.value}}</span></b-col>
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
                                                            <b-table-simple striped responsive>
                                                                <b-thead>
                                                                    <b-tr>
                                                                        <b-th v-for="(listItem,label) in field.value[0]"><span v-html="label"></b-th>
                                                                    </b-tr>
                                                                </b-thead>
                                                                <b-tbody>
                                                                    <b-tr v-for="list in field.value">
                                                                        <b-td v-for="listItem in list"><span v-html="listItem"></b-td>
                                                                    </b-tr>
                                                                </b-tbody>
                                                            </b-table-simple>
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
    </div>
    <?php
    include 'footer.php';
