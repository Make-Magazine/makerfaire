window.app = new Vue({
    el: '#review',
    data() {
        return {
            makers: null,
            currentView: "grid",
            expandToggle: "0"
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
            console.log(this.expandToggle);
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
            .then(response => (this.makers = response.data.makers))
    }
})