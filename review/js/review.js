window.app = new Vue({
    el: '#review',
    data() {
        return {
            makers: null,
            currentView: "grid",
            searchQuery: '',
            selectedStatus: '',
        }
    },
    methods: {
        switchToListView: function(ev){
            this.currentView = 'list';
        },
        switchToGridView: function(ev){
            this.currentView = 'grid';
        }
    },
    mounted() {
        axios
            .get('/wp-json/makerfaire/v2/fairedata/entryReview/260/BA23')
            .then(response => (this.makers = response.data.makers));
    },
    computed: {
        filterBy(){
          if(this.searchQuery || this.selectedStatus){
            var searchValue = this.searchQuery;
            var statusFilter = this.selectedStatus;
            console.log(statusFilter);
            return this.makers.filter(function(maker){
                return (maker.project_name.toLowerCase().indexOf(searchValue) > -1 ||
                       maker.description.toLowerCase().indexOf(searchValue) > -1) &&
                       maker.status === statusFilter
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