var urlParams = new URLSearchParams(window.location.search);
window.app = new Vue({
    el: '#review',
    data() {
        return {
            makers: null,
            currentView: urlParams.get('layout') ? urlParams.get('layout') : "grid",
            searchQuery: urlParams.get('search') ? urlParams.get('search') : "",
            selectedStatus: '',
            perPage: 8,
            currentPage: 1,
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
        }
    },
    mounted() {
        axios
            .get('/query/?type=entries&form=260')
            .then(response => (this.makers = response.data.makers))
            .then((data) => this.$el.classList.remove("preload"));
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
                       maker.description.toLowerCase().indexOf(searchValue) > -1) &&
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
    }    
})