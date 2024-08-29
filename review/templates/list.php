<b-card no-body :id="'listView-' + maker.project_id" v-for="(maker, maker_id) in filterBy.slice((currentPage-1)*perPage,(currentPage-1)*perPage+perPage)" :key="'list-' + maker.project_id">
    <input type="hidden" name="entry_info_entry_id" :value=maker.project_id />
    <b-row class="header">
        <b-col cols="7">
            <h3>{{maker.project_name}}</h3>
        </b-col>
        <b-col cols="2">{{maker.entry_type}}</b-col>
        <b-col cols="1" class="placed">{{maker.entry_placed}}</b-col>
        <b-col cols="1"><span :class="'status_'+maker.project_id">{{maker.status}}</span></b-col>
        <b-col cols="1" style="color: #ccc;">{{maker.project_id}}</b-col>
        <b-button v-if="entryIDQuery" @click="backToGrid(maker.project_id)" variant="outline-primary" v-b-tooltip.hover title="Back to Grid"><i class="bi bi-arrow-return-left"></i><i aria-hidden="true" class="bi bi-grid"></i></b-button>
    </b-row>
    <b-tabs card v-bind:id=maker.project_id>
        <b-tab v-for="(tab,tabKey) in maker.tabs" :key="tabKey+'-'+maker_id" :title="tab.title">
            <b-card-text>
                <b-container fluid class="bv-example-row">                    
                    <div v-for="(tab_content,tab_type) in tab.tab_content" :key="tabKey+'-'+maker_id">                        
                        <!-- Initial section-->
                        <b-row v-if="tab_type=='initial'" class="blocks" v-for="(block,block_id) in tab_content.blocks" :key="block_id">
                            <?php  include('templates/blocks.html'); ?>      
                        </b-row>
                        <!-- Expand Section -->
                        <div v-if="tab_type=='expand'">
                            <b-button class="expand" v-b-toggle="'collapse-'+tabKey+maker.project_id" variant="primary">
                                <span class="when-opened">
                                    Less Info -
                                </span>
                                <span class="when-closed">
                                    More Info +
                                </span>
                            </b-button>
                            <b-collapse :id="'collapse-'+tabKey+maker.project_id" class="mt-2">
                                <b-row class="blocks"  v-for="(block,block_id) in tab_content.blocks" :key="block_id">
                                    <?php  include('templates/blocks.html'); ?>   
                                </b-row>
                    
                            </b-collapse>
                        </div>

                    </div>    
                    <!--
                    <div v-if="tab.tab_content.initial && tab.tab_content.initial > 0">
                        initial content
                        {{tab.tab_content.initial}}
                    </div>
                    <div v-if="tab.tab_content.expand && tab.tab_content.expand > 0">
                        {{tab.tab_content.expand}}
                    </div>-->
                    

                  
                </b-container>
            </b-card-text>
        </b-tab>
    </b-tabs>

</b-card>

<b-pagination v-if="filterBy.length>0" v-model="currentPage" :total-rows="filterBy.length" :per-page="perPage" prev-text="<" next-text=">" @page-click="pagClick(event, currentPage)">
</b-pagination>