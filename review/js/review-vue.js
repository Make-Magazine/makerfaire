var urlParams = new URLSearchParams(window.location.search);
var formID    = document.getElementById("form_select").value; 

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
            modalShow: false,
            selectedImgPath: ''

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
            ev.target.title = (ev.target.title == "See Oldest") ? "See Newest" : "See Oldest";
            this.makers.reverse();
        },
        updateForm: function(event) {
            var formID = event.target.value;
            console.log(formID);
            axios
            .get('/query/?type=entries&form='+formID)
            .then(response => (this.makers = response.data.makers));
        },
        showModal(imgPath) {
            alert(imagePath);
            this.selectedImgPath = imgPath
            this.modalShow = true
        }
    },
    mounted() {
        axios
            .get('/query/?type=entries&form='+formID)
            .then(response => (this.makers = response.data.makers));
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