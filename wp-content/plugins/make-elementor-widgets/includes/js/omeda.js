jQuery(document).ready(function ($) {
    save_omeda_postalID($);
});

function save_omeda_postalID($) {
    var updateOmedaIdBtn = $('#make-update-Omeda-ID')

    updateOmedaIdBtn.on('click', function (e) {
        e.preventDefault();

        // retrieve ID entered
        var postal_id = $("#omeda_postal_id").val();
        if(postal_id==''){
            makeModal("You must enter an account number to proceed.");
            return;
        }

        // set data object
        var data = {
            action: 'saveOmedaID',
            nonce: make_ajax_object.ajaxnonce,
            postal_id: postal_id
        };

        // call Ajax
        $.ajax({
            url: make_ajax_object.ajaxurl,  // Ajax handler
            type: "post",
            data: data,
            beforeSend: function (xhr) {
                // show loading text
                updateOmedaIdBtn.css({"pointer-events":"none", "background":"grey", "color":"white"});
                updateOmedaIdBtn.html("Loading <i class='fa fa-spinner'></i>");
            },
            success: function (res) {
                console.log('success');
                makeModal(res);
                $('.elementor-widget-mysubs').load(document.URL + " .elementor-widget-mysubs > *", function(){
					updateOmedaIdBtn.css({"pointer-events":"all", "background":"white", "color": "#005e9a"});
                    updateOmedaIdBtn.html("Update");
                    save_omeda_postalID($);
                    $(".more-info").click(function(){
						$(this).parent().toggleClass( "open" );
					});
				});
				
            },
            error: function (e) {
                console.log(e);
            },

        });

    });

}
