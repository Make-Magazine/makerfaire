window.app = new Vue({
    el: '#review',
    data() {
        return {
            makers: null,
            currentView: "grid",
            expandToggle: "0",
            searchQuery: ''
        }
    },
    methods: {
        switchToListView: function(ev){
            this.currentView = 'list';
        },
        switchToGridView: function(ev){
            this.currentView = 'grid';
        },
        expand: function(event) {
            if(this.expandToggle == 0) {
                event.target.innerHTML = "Shrink";
                event.target.parentElement.querySelector('.tabs').innerHTML += event.target.parentElement.querySelector('.tab-content .card-body:last-of-type').innerHTML;
                this.expandToggle = 1;
                event.target.remove();
            } 
            if(this.expandToggle == 1) {
                event.target.innerHtml = "Expand";
                this.expandToggle = 0;
            }
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