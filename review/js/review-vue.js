var urlParams = new URLSearchParams(window.location.search);
var formID = document.getElementById("form_select").value;

new Vue({
    el: '#review',
    data() {
        return {
            makers: [],
            currentView: urlParams.get('layout') ? urlParams.get('layout') : "grid",
            searchQuery: urlParams.get('search') ? urlParams.get('search') : "",
            selectedStatus: '',
            selectedPrimeCat: '',
            selectedEntryType: [],
            selectedFlag: [],
            selectedPrelimLoc: [],
            perPage: 20,
            currentPage: 1,
            modalShow: false,
            CustomSource: '',
            toggler: false
        }
    },
    /*
    watch: {
        // when makers data has fully loaded from the axios call, this will run
        makers: function (makersLoaded, makersEmpty) {
        }
    },*/
    methods: {
        switchToListView: function (ev) {
            this.currentView = 'list';
        },
        switchToGridView: function (ev) {
            this.currentView = 'grid';
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
        resetFilters: function () {
            this.searchQuery = "";
            this.selectedStatus = '';
            this.selectedPrimeCat = '';
            this.selectedEntryType = [];
            this.selectedFlag = [];
        },
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
                this.selectedFlag
            ) {
                var searchValue     = this.searchQuery;
                var statusFilter    = this.selectedStatus;
                var primeCatFilter  = this.selectedPrimeCat;
                var entryTypeFilter = this.selectedEntryType;
                var flagFilter      = this.selectedFlag;
                var prelimLocFilter = this.selectedPrelimLoc;

                var passEntryType   = true;
                var passFlag        = true;
                var passPrelimLoc   = true;

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
                    if (flagFilter != '') {
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
                    return (maker.project_name.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.project_id.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.description.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.maker_name.toLowerCase().indexOf(searchValue) > -1) &&
                        maker.status.indexOf(statusFilter) > -1 &&
                        maker.prime_cat.indexOf(primeCatFilter) > -1 &&
                        passEntryType && passFlag && passPrelimLoc;
                })
            } else {
                return this.makers;
            }
        },
        filteredStatus() {
            if (this.makers) {
                var filteredStatus = Array.from(new Set(this.makers.map(maker => maker.status)));
                return filteredStatus;
            }
        },
        filteredPrimeCat() {
            if (this.makers) {
                filteredPrimeCat = Array.from(new Set(this.makers.map(maker => maker.prime_cat)));
                return filteredPrimeCat;
            }
        },
        filteredEntryType() {
            if (this.makers) {                
                filteredEntryType = filterCommaList('entry_type', this.makers);                           
                return filteredEntryType;
            }
        },
        filteredFlag() {
            if (this.makers) {
                filteredFlag = filterCommaList('flags', this.makers);                
                return filteredFlag;
            }
        },
        filteredPrelimLoc() {
            if (this.makers) {
                filteredPrelimLoc = filterCommaList('prelim_loc', this.makers);                                
                return filteredPrelimLoc;
            }
        },
    },
    filters: {
        count: function (res) {
            var res = this.makers.length;
            return res;
        }
    }
    //method to do function
});

function filterCommaList(field, makers){    
    filteredList = [];
    makers.forEach((maker) => {                
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