<b-card no-body :id="'listView-' + maker.project_id" v-for="(maker, maker_id) in filterBy.slice((currentPage-1)*perPage,(currentPage-1)*perPage+perPage)" :key="'list-' + maker.project_id">
    <input type="hidden" name="entry_info_entry_id" :value=maker.project_id />
    <b-row class="header">
        <b-col cols="8">
            <h3>{{maker.project_name}}</h3>
        </b-col>
        <b-col cols="2">{{maker.entry_type}}</b-col>
        <b-col cols="1"><span :class="'status_'+maker.project_id">{{maker.status}}</span></b-col>
        <b-col cols="1" style="color: #ccc;">{{maker.project_id}}</b-col>
    </b-row>
    <b-tabs card v-bind:id=maker.project_id>
        <b-tab v-for="(tab,tabKey) in maker.tabs" :key="tabKey+'-'+maker_id" :title="tab.title">
            <b-card-text>
                <b-container fluid class="bv-example-row">
                    <!-- Initial section-->
                    <b-row class="blocks" v-for="(block,block_id) in tab.tab_content.initial.blocks" :key="block_id">
                        <?php  include('templates/blocks.html'); ?>      
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
                                <?php  include('templates/blocks.html'); ?>   
                            </b-row>

                        </b-collapse>
                    </div>
                </b-container>
            </b-card-text>
        </b-tab>
    </b-tabs>
</b-card>

<b-pagination v-if="filterBy.length>0" v-model="currentPage" :total-rows="filterBy.length" :per-page="perPage" prev-text="<" next-text=">">
</b-pagination>