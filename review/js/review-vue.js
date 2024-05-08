var urlParams = new URLSearchParams(window.location.search);
var formID = document.getElementById("form_select").value;
// this query will be updates with our route
var query = {};

Vue.use(VueRouter);
const routes = [
    { path: '/review'  }
];
const router = new VueRouter({
    mode: 'history',
    routes
});
router.replace({ path: '/review', redirect: '/review' });

new Vue({
    el: '#review',
    data() {
        return {
            makers: [],
            currentView: urlParams.get('layout') ? urlParams.get('layout') : "grid",
            searchQuery: urlParams.get('search') ? urlParams.get('search') : "",
            // the splits here will both make sure these are arrays, and select all options in the multiselect
            selectedStatus: urlParams.get('status') ? urlParams.get('status').split(",") : [],
            selectedPrimeCat: urlParams.get('category') ? urlParams.get('category').split(",") : [],
            selectedEntryType: urlParams.get('type') ? urlParams.get('type').split(",") : [],
            selectedFlag: urlParams.get('flag') ? urlParams.get('flag').split(",") : [],
            selectedPrelimLoc: urlParams.get('location') ? urlParams.get('location').split(",") : [],
            perPage: 20,
            currentPage: 1,
            modalShow: false,
            CustomSource: '',
            toggler: false,
            router: router
        }
    },
    
    watch: {
        // when makers data has fully loaded from the axios call, this will run
        makers: function (makersLoaded, makersEmpty) {
            instgrm.Embeds.process();
        }
    },
    methods: {
        switchToListView: function (ev) {
            this.currentView = 'list';
            query.layout = 'list';
            this.router.push({ path: 'review', query: query }).catch(()=>{});
        },
        switchToGridView: function (ev) {
            this.currentView = 'grid';
            query.layout = 'grid';
            this.router.push({ path: 'review', query: query }).catch(()=>{});
        },
        expandCard: function (projectID) {
            this.currentView = 'list';
            this.searchQuery = projectID;
        },
        switchDateOrder: function (ev) {
            ev.target.title = (ev.target.title == "See Oldest") ? "See Newest" : "See Oldest";
            this.makers.reverse();
        },
        updateForm: function (event) {
            this.makers = [];
            var formID = document.getElementById("form_select").value;
            axios
                .get('/query/?type=entries&form=' + formID)
                .then(response => (this.makers = response.data.makers));
        },
        showModal: function (img_class, image_id) {
            setLightBox(img_class, image_id);
        },
        processVimeo: function(url) {
            var vimeoRegex = /(?:vimeo)\.com.*(?:videos|video|channels|)\/([\d]+)/i;
            var parsed = url.match(vimeoRegex);
            return "//player.vimeo.com/video/" + parsed[1];    
        },
        resetFilters: function () {
            this.searchQuery = "";
            this.selectedStatus = [];
            this.selectedPrimeCat = [];
            this.selectedEntryType = [];
            this.selectedFlag = [];
            this.selectedPrelimLoc = [];
            query = {};
            this.router.push({ path: 'review', query: query }).catch(()=>{});
        },
        filterCommaList: function(field){                
            filteredList = [];
            
            this.makers.forEach((maker) => {                
                if (maker[field] != '') {               
                    //breakup the comma separated string into an array
                    entryFieldArr = maker[field].split(", ");
            
                    //loop through values set
                    entryFieldArr.forEach((filter) => {                                                  
                        //only set unique values
                        if (filter != 'gppa-unchecked') {  
                            filteredList.indexOf(filter) === -1 ? filteredList.push(filter) : '';
                        }
                    });            
                }
            });
        
            return filteredList;
        }      
    },
    mounted() {
        axios
            .get('/query/?type=entries&form=' + formID)
            .then(response => (this.makers = response.data.makers));
    },
    computed: {
        filterBy() {
            if (this.searchQuery || this.selectedStatus ||
                this.selectedPrimeCat || this.selectedEntryType ||
                this.selectedFlag || this.selectedPrelimLoc
            ) {
                var searchValue     = this.searchQuery.toLowerCase();
                var statusFilter    = this.selectedStatus;
                var primeCatFilter  = this.selectedPrimeCat;
                var entryTypeFilter = this.selectedEntryType;
                var flagFilter      = this.selectedFlag;
                var prelimLocFilter = this.selectedPrelimLoc;

                var passEntryType   = true;
                var passFlag        = true;
                var passPrelimLoc   = true;
                var passPrimeCat    = true;
                var passStatus      = true;

                // here we build the queryString based on the filters and add it to our route
                if(searchValue) { query.search = searchValue; }
                if(statusFilter.toString() != "") { query.status = statusFilter.toString(); }
                if(primeCatFilter.toString() != "") { query.category = primeCatFilter.toString(); }
                if(entryTypeFilter.toString() != "") { query.type = entryTypeFilter.toString(); }
                if(flagFilter.toString() != "") { query.flag = flagFilter.toString(); }
                if(prelimLocFilter.toString() != "") { query.location = prelimLocFilter.toString(); }
                this.router.push({ path: 'review', query: query }).catch(()=>{});

                return this.makers.filter(function (maker) {

                    //Entry Type
                    if (entryTypeFilter != '') {
                        passEntryType = false;
                        //breakup the entry types into an array
                        entryTypeArr = maker.entry_type.split(", ");

                        //loop through entry types set
                        entryTypeArr.forEach((entry_type) => {
                            if (entryTypeFilter.includes(entry_type)) {
                                passEntryType = true;
                            }
                        });
                    }

                    //Flag Filter           
                    if (flagFilter != '') {
                        passFlag = false;
                        //breakup the entry types into an array
                        flagsArr = maker.flags.split(", ")

                        //loop through entry types set
                        flagsArr.forEach((flag) => {
                            if (flagFilter.includes(flag)) {
                                passFlag = true;
                            }
                        });
                    }

                    //Preliminary location
                    if (prelimLocFilter != '') {
                        passPrelimLoc = false;
                        //breakup the entry types into an array
                        prelimLocArr = maker.prelim_loc.split(", ")

                        //loop through entry types set
                        prelimLocArr.forEach((prelim_loc) => {
                            if (prelimLocFilter.includes(prelim_loc)) {
                                passPrelimLoc = true;
                            }
                        });
                    }

                    // Primary Category
                    if (primeCatFilter != '') {
                        passPrimeCat = false;

                        //loop through entry types set
                        primeCatFilter.forEach((prime_cat) => {
                            if (maker.prime_cat == prime_cat) {
                                passPrimeCat = true;
                            }
                        });
                    }

                    // Status
                    if (statusFilter != '') {
                        passStatus = false;

                        //loop through entry types set
                        statusFilter.forEach((status) => {
                            if (maker.status == status) {
                                passStatus = true;
                            }
                        });
                    }

                    return (maker.project_name.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.project_id.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.description.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.maker_name.toLowerCase().indexOf(searchValue) > -1) &&
                        passEntryType && passFlag && passPrelimLoc && passPrimeCat && passStatus;
                })
            } else {
                return this.makers;
            }
        },
        filteredStatus() {
            if (this.makers) {
                var filteredStatus = Array.from(new Set(this.makers.map(maker => maker.status)));
                return filteredStatus.sort();
            }
        },
        filteredPrimeCat() {
            if (this.makers) {
                filteredPrimeCat = Array.from(new Set(this.makers.map(maker => maker.prime_cat)));
                return filteredPrimeCat.sort();
            }
        },
        filteredEntryType() {
            if (this.makers) return this.filterCommaList('entry_type').sort();                                           
        },
        filteredFlag() {
            if (this.makers) return this.filterCommaList('flags').sort();                                
        },
        filteredPrelimLoc() {
            if (this.makers) return this.filterCommaList('prelim_loc').sort();                                                
        },
    },
    filters: {
        count: function (res) {
            var res = this.makers.length;
            return res;
        }
    }
});  