var urlParams = new URLSearchParams(window.location.search);
var email = document.getElementById("user_email").value;

new Vue({
    el: '#manageEntries',
    data() {
        return {
            entries: [],
            pop2: false
        }
    },
    watch: {
        // when makers data has fully loaded from the axios call, this will run
        entries: function () {
            //if no data was found for this user, prompt them to apply
            if (this.entries.length === 0) {
                $loadingMsg = "I'm sorry. We could not find any entries for your email (" + email + ").<br/>Please submit one <a href='https://makerfaire.com/bay-area/apply'>HERE</a>";
            } else {
                $loadingMsg = '';
            }
            document.getElementById("loadingMsg").innerHTML = $loadingMsg;
        }
    },
    methods: {
        onOpen(e) {
            this.id = e.target.id;
            this.$root.$emit('bv::show::popover',e.target.id);
          },
    },
    mounted() {
        axios
            .get('/query/?type=maker-portal&email=' + email)
            .then(response => (this.entries = response.data.data));      
    },
    computed: {


    },
    filters: {
        count: function (res) {
            var res = this.entries.length;
            return res;
        }
    }
});  