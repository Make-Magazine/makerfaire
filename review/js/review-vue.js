var urlParams = new URLSearchParams(window.location.search);
window.app = new Vue({
    el: '#review',
    data() {
        return {
            makers: [],
            currentView: urlParams.get('layout') ? urlParams.get('layout') : "grid",
            searchQuery: urlParams.get('search') ? urlParams.get('search') : "",
            selectedStatus: '',
            perPage: 20,
            currentPage: 1,
        }
    },
    watch: {
        // when makers data has fully loaded from the axios call, this will run
        makers: function (makersLoaded, makersEmpty) {
        }
    },
    methods: {
        switchToListView: function(ev){
            this.currentView = 'list';
        },
        switchToGridView: function(ev){
            this.currentView = 'grid';
        },
        expandCard: function(projectID){
            this.currentView = 'list';
            this.searchQuery = projectID;
        },
        switchDateOrder: function(ev) {
            console.log(ev.target.innerHTML);
            ev.target.innerHTML = (ev.target.innerHTML == "See Oldest") ? "See Newest" : "See Oldest";
            this.makers.reverse();
        }
    },
    mounted() {
        axios
            .get('/query/?type=entries&form=260')
            .then(response => (this.makers = response.data.makers));
        // vue loads later, we don't know what we clicking, this function is in review.js
        document.addEventListener( "click", clickListener );
    },
    computed: {
        filterBy(){
          if(this.searchQuery || this.selectedStatus){
            var searchValue = this.searchQuery;
            var statusFilter = this.selectedStatus;
            return this.makers.filter(function(maker){
                return (maker.project_name.toLowerCase().indexOf(searchValue) > -1 ||
                       maker.project_id.toLowerCase().indexOf(searchValue) > -1 ||
                       maker.description.toLowerCase().indexOf(searchValue) > -1 ||
                       maker.maker_name.toLowerCase().indexOf(searchValue) > -1) &&
                       maker.status.indexOf(statusFilter) > -1
            })
          }else{
            return this.makers;
          }
        },
        filteredStatus() {
            if(this.makers) {
                var filteredStatus = Array.from(new Set(this.makers.map(maker => maker.status)));
                return filteredStatus;
            }
        }
    },
    filters: {
        count: function (res) {
          var res = this.makers.length;
          console.log(res);
          return res;
        }
    }  
})