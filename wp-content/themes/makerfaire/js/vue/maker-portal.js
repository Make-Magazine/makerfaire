var urlParams = new URLSearchParams(window.location.search);
var email = document.getElementById("user_email").value;

new Vue({
    el: '#manageEntries',
    data() {
        return {
            faire_entries: [],
            pop2: false
        }
    },
    watch: {
        // when makers data has fully loaded from the axios call, this will run
        faire_entries: function () {
            //if no data was found for this user, prompt them to apply
            if (this.faire_entries.length === 0) {
                $loadingMsg = "I'm sorry. We could not find any entries for your email (" + email + ").<br/>Please submit one <a href='https://makerfaire.com/bay-area/apply'>HERE</a>";
            } else {
                $loadingMsg = '';
            }
            document.getElementById("loadingMsg").innerHTML = $loadingMsg;
        }
    },
    methods: {        
        showDialog: function () {
            openDialog();
        },
    },
    mounted() {
        axios
            .get('/query/?type=maker-portal&email=' + email)
            .then(response => (this.faire_entries = response.data.data));      
    },
    computed: {


    },
    filters: {
        count: function (res) {
            var res = this.faire_entries.length;
            return res;
        }
    }
});  