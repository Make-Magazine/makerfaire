var urlParams = new URLSearchParams(window.location.search);
var email = document.getElementById("user_email").value;

new Vue({
    el: '#manageEntries',
    data() {
        return {
            faire_entries: [],
            pop2: false,
            showData: false
        }
    },
    watch: {
        // when makers data has fully loaded from the axios call, this will run
        faire_entries: function () {
            this.showData = true;            
        }
    },
    methods: {
        cancelModal: function (modal_id, popover_id){
            //hide the popover                                                
            this.$root.$emit('bv::hide::popover', popover_id);

            //show the modal
            this.$bvModal.show(modal_id);
        },
        submitCancel: function (entry_id) {        
            var entry_id = jQuery("#cancelEntryID").html();
            
            //hide the submit button            
            jQuery('#cancelModal'+entry_id + ' #submitButton').hide();
            jQuery('#cancelModal'+entry_id + ' #cancelButton').hide();
            
            var cancel_reason = jQuery('#cancelModal'+entry_id +' textarea[name="cancelReason"]').val();
            var data = {
                'action': 'maker-cancel-entry',
                'cancel_entry_id': entry_id,
                'cancel_reason': cancel_reason
            };
            
            jQuery('#cancelResponse').html('<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Processing...</span>');            

            jQuery.post(vueAjax.ajaxurl, data, function (response) {                                
                jQuery('#cancelResponse').text(response);
                jQuery('#cancelModal'+entry_id + ' #cancelButton').addClass('btn-success');
                jQuery('#cancelModal'+entry_id + ' #cancelButton').show();
                jQuery('#cancelModal'+entry_id + ' #cancelButton').text('Close');                                
                
            });
        }
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

window.addEventListener("load", (event) => {
    document.querySelector("#profile-view #dropdownMenuLink").addEventListener('click', function() {
        document.querySelector("#profile-view .profile-menu").classList.toggle('show');
    },
    false);
});