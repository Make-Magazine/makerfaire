var urlParams = new URLSearchParams(window.location.search);
var formID = 278;
window.onload = (event) => {
    document.querySelector('#form_select').value = '278';
};

// this query will be updates with our route
var query = {};

var items = {};
var types = {};
var attributes = {};
var attention = {};

Vue.use(VueRouter);
const routes = [
    { path: '/review' }
];
const router = new VueRouter({
    mode: 'history',
    routes
});
router.replace({ path: '/review', redirect: '/review' });

var review = new Vue({
    el: '#review',
    data() {
        return {
            filterCounts: {},
            makers: [],
            rmt: [],
            locations: [],

            statusArray: ['Proposed', 'Accepted', 'Pending', 'No Response', 'Wait List', 'Rejected', 'Cancelled', 'No Show'],
            currentView: urlParams.get('layout') ? urlParams.get('layout') : "list",
            searchQuery: urlParams.get('search') ? urlParams.get('search') : "",
            placedQuery: urlParams.get('placed') ? urlParams.get('placed') : "",
            entryIDQuery: "",
            layoutQuery: urlParams.get('layout') ? urlParams.get('layout') : "",
            // the splits here will both make sure these are arrays, and select all options in the multiselect
            selectedStatus: urlParams.get('status') ? urlParams.get('status').split(",") : [],
            selectedCat: urlParams.get('category') ? urlParams.get('category').split(",") : [],
            selectedEntryType: urlParams.get('type') ? urlParams.get('type').split(",") : [],
            selectedFlag: urlParams.get('flag') ? urlParams.get('flag').split(",") : [],
            selectedPrelimLoc: urlParams.get('location') ? urlParams.get('location').split(",") : [],
            perPage: 20,
            currentPage: 1,
            lastPage: 1, // this will store the last page accessed for getting back to with the back button
            modalShow: false,
            CustomSource: '',
            toggler: false,
            results: true,
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
            this.currentView = layoutQuery = query.layout = 'list';
            this.router.push({ path: 'review', query: query }).catch(() => { });
        },
        switchToGridView: function (ev) {
            this.currentView = layoutQuery = query.layout = 'grid';
            this.router.push({ path: 'review', query: query }).catch(() => { });
            this.entryIDQuery = "";
        },
        expandCard: function (projectID) {
            this.currentView = layoutQuery = query.layout = 'list';
            this.entryIDQuery = projectID;
        },
        backToGrid: function (anchor) {
            this.currentView = layoutQuery = query.layout = 'grid';
            var page = this.currentPage = this.lastPage;
            this.entryIDQuery = "";
            // because the pagination grid doesn't stay current with the page it's actually on, we will clear the active class and assign it back to the right pager later
            document.querySelector(".pagination .page-item.active").classList.remove('active');
            setTimeout(function () {
                window.location.hash = anchor;
                document.querySelector(".pagination .page-item button[aria-posinset='" + page + "']").parentNode.classList.add("active");
            }, 200);
        },
        pagClick(ev) { // store the last page a user actually navigated to
            var page = this.lastPage;
            if (document.querySelector(".pagination .page-item button[aria-posinset='" + page + "']")) {
                document.querySelector(".pagination .page-item button[aria-posinset='" + page + "']").parentNode.classList.remove("active");
            }
            this.lastPage = ev.target.ariaPosInSet;
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
                .then((response) => {
                    console.log(response.data.makers);
                    if(response.data.makers) {
                        this.makers = response.data.makers
                    } else {
                        this.results = false;
                    }
                    return response;
                })
        },
        showModal: function (img_class, image_id) {
            setLightBox(img_class, image_id);
        },
        processVimeo: function (url) {
            var vimeoRegex = /(?:vimeo)\.com.*(?:videos|video|channels|)\/([\d]+)/i;
            var parsed = url.match(vimeoRegex);
            return "//player.vimeo.com/video/" + parsed[1];
        },
        selectStatusFilter: function (status) {
            document.getElementById("statusFilter").value = status;
            this.selectedStatus = [status];
        },
        resetFilters: function () {
            this.searchQuery = "";
            this.placedQuery = "";
            this.selectedStatus = [];
            this.selectedCat = [];
            this.selectedEntryType = [];
            this.selectedFlag = [];
            this.selectedPrelimLoc = [];
            this.entryIDQuery = "";
            query = {};
            this.router.push({ path: 'review', query: query }).catch(() => { });
        },
        filterCommaList: function (field) {
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
        },
        getCountofStatus(status) {
            var statusFilter = this.filterBy.filter((item) => item.status == status);
            return statusFilter.length;
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
                // Then specify how you want your dates to be formatted
            return new Intl.DateTimeFormat('default', {dateStyle: 'short'}).format(date);
        },
        formatTime(dateString) {
            const date = new Date(dateString);
                // Then specify how you want your dates to be formatted
            return new Intl.DateTimeFormat('default', {timeStyle: 'short'}).format(date);
        }

        
    },
    mounted() {
        axios
            .get('/query/?type=entries&form=' + formID)
            .then((response) => {
                if(response.data.makers) {
                    this.makers = response.data.makers
                } else {
                    this.results = false;
                }
                return response;
            })
            .then((response) => {
                this.locations = response.data.locations;
                this.rmt = response.data.rmt
                // set these objects to create the editable rmt dropdowns 
                items = this.rmt.res_items
                types = this.rmt.res_types
                attributes = this.rmt.att_items
                attention = this.rmt.attn_items
            });

        document.getElementById("review").style.display = "block";
    },
    computed: {
        filterBy() {
            if (this.searchQuery || this.placedQuery || this.selectedStatus ||
                this.selectedCat || this.selectedEntryType ||
                this.selectedFlag || this.selectedPrelimLoc || this.entryIDQuery
            ) {
                var searchValue = this.searchQuery.toLowerCase();
                var placedValue = this.placedQuery;
                var entryIDValue = this.entryIDQuery;
                var layoutValue = this.layoutQuery;
                var statusFilter = this.selectedStatus;
                var catFilter = this.selectedCat;
                var entryTypeFilter = this.selectedEntryType;
                var flagFilter = this.selectedFlag;
                var prelimLocFilter = this.selectedPrelimLoc;

                var passEntryType = true;
                var passFlag = true;
                var passPrelimLoc = true;
                var passCat = true;
                var passStatus = true;

                // here we build the queryString based on the filters and add it to our route
                if (searchValue) { query.search = searchValue; } else { delete query.search; }
                if (placedValue) { query.placed = placedValue; } else { delete query.placed; }
                if (layoutValue) { query.layout = layoutValue; }
                if (statusFilter.toString() != "") { query.status = statusFilter.toString(); } else { delete query.status; }
                if (catFilter.toString() != "") { query.category = catFilter.toString(); } else { delete query.category; }
                if (entryTypeFilter.toString() != "") { query.type = entryTypeFilter.toString(); } else { delete query.type; }
                if (flagFilter.toString() != "") { query.flag = flagFilter.toString(); } else { delete query.flag; }
                if (prelimLocFilter.toString() != "") { query.location = prelimLocFilter.toString(); } else { delete query.location; }
                this.router.push({ path: 'review', query: query }).catch(() => { });

                return this.makers.filter(function (maker) {
                    //Filter by Entry Type
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

                    // CATegories - meow
                    if (catFilter != '') {
                        passCat = false;
                        catArr = maker.categories.split(", ")

                        //loop through categories set
                        catArr.forEach((cat) => {
                            if (catFilter.includes(cat)) {
                                passCat = true;
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
                    //console.log(maker.entry_placed.toLowerCase() + " \ " + placedValue);
                    return (maker.project_name.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.project_id.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.description.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.maker_name.toLowerCase().indexOf(searchValue) > -1 ||
                        maker.email.toLowerCase().indexOf(searchValue) > -1) &&
                        maker.project_id.indexOf(entryIDValue) > -1 &&
                        maker.entry_placed.toLowerCase().indexOf(placedValue) > -1 &&
                        passEntryType && passFlag && passPrelimLoc && passCat && passStatus;
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
        filteredCat() {
            if (this.makers) return this.filterCommaList('categories').sort();
        },
        filteredEntryType() {
            if (this.makers) return this.filterCommaList('entry_type').sort();
        },
        filteredFlag() {
            if (this.makers) return this.filterCommaList('flags').sort();
        },
        filteredPrelimLoc() {
            if (this.makers) return this.filterCommaList('prelim_loc').sort();
        }       
    },
    filters: {
        count: function (res) {
            var res = this.makers.length;
            return res;
        }
    }
});


function waitForElm(selector) {
    return new Promise(resolve => {
        if (document.querySelector(selector)) {
            return resolve(document.querySelector(selector));
        }

        const observer = new MutationObserver(mutations => {
            if (document.querySelector(selector)) {
                observer.disconnect();
                resolve(document.querySelector(selector));
            }
        });

        // If you get "parameter 1 is not of type 'Node'" error, see https://stackoverflow.com/a/77855838/492336
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
}