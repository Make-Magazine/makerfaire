window.app = new Vue({
    el: '#review',
    data() {
        return {
            makers: null,
            currentView: "grid",
            searchQuery: ''
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
          if(this.searchQuery){
            var value = this.searchQuery;
            return this.makers.filter(function(maker){
                return maker.project_name.toLowerCase().indexOf(value) > -1 ||
                       maker.description.toLowerCase().indexOf(value) > -1 
            })
          }else{
            return this.makers;
          }
        }
    }    
})