/* Edit entry public facing page */
function showEdit(){
    jQuery('#viewEntry').hide();
    jQuery('#editEntry').show();
}


// owl-carousel for the page entry project gallery
jQuery(document).ready(function(){
    jQuery(".owl-carousel").owlCarousel({
        margin:10,
        loop:true,
        autoWidth:false,
        nav:true,
        navText : ['<i class="fa fa-chevron-left" aria-hidden="true"></i>','<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
        lazyLoad: true,
        items:4,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            600:{
                items:2,
                nav:false
            },
            900:{
                items:3,
                nav:false
            },
        }
    });

    jQuery('#projectGallery .owl-item').on("click", function () {
        //every time you click on an owl item, open a dialog modal to show the images
        var owlItem = jQuery(this);
        jQuery('body').append('<div id="dialog"><img src="' + jQuery("img", this).attr('src') + '" width="100%" /></div>');
        jQuery('#dialog').dialog({
            dialogClass: "hide-heading",
            modal: true,
            // these buttons will go to the next image from the #projectGallery and replace the src of the image in the modal with the next or previous image in the gallery
            buttons: [
                {
                    "class": "dialog-nav-btn dialog-prev-btn",
                    click: function() {
                        jQuery("#dialog img").attr("src", owlItem.prev(".owl-item").children(".gallery-item").children("img").attr("src"));
                        owlItem = owlItem.prev();
                    }
                },
                {
                    "class": "dialog-nav-btn dialog-next-btn",
                    click: function() {  
                        jQuery("#dialog img").attr("src", owlItem.next(".owl-item").children(".gallery-item").children("img").attr("src"));
                        owlItem = owlItem.next();
                    }
                }
            ],
            close: function(event, ui) {
                jQuery(this).remove();
            },
            open: function(event, ui) { 
              jQuery('.ui-widget-overlay').bind('click', function(){ 
                  jQuery("#dialog").dialog('close');
            }); 
          }
        });
    });
});