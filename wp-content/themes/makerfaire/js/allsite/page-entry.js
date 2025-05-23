/* Edit entry public facing page */
function showEdit(){
    jQuery('#viewEntry').hide();
    jQuery('#editEntry').show();
}


// owl-carousel for the page entry project gallery
jQuery(document).ready(function(){
    
    var numImages = jQuery('.owl-carousel .gallery-item').length;
    jQuery(".owl-carousel").owlCarousel({
        margin:10,
        loop: false,
        autoWidth:false,
        nav: numImages <= 4 ? false : true,
        navText : numImages <= 4 ? [] : ['<i class="fa fa-chevron-left" aria-hidden="true"></i>','<i class="fa fa-chevron-right" aria-hidden="true"></i>'],
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
            1200:{
                items:4,
                nav:false
            },
        },
        onInitialize: function (event) {
            if (numImages < 4) {
               this.settings.items = numImages;
            }
        }
    });

    jQuery('#projectGallery .owl-item').on("click", function () {
        //every time you click on an owl item, open a dialog modal to show the images
        var owlItem = jQuery(this);
        jQuery('body').append('<div id="dialog"><img src="' + jQuery("img", this).attr('data-photo') + '" width="100%" /></div>');
        jQuery('#dialog').dialog({
            dialogClass: ["hide-heading", "entry-gallery"],
            modal: true,
            // these buttons will go to the next image from the #projectGallery and replace the src of the image in the modal with the next or previous image in the gallery
            buttons: numImages <= 1 ? [] : [
                {
                    "class": "dialog-nav-btn dialog-prev-btn",
                    click: function() {
                        if(owlItem.prev(".owl-item").children(".gallery-item").children("img").attr("data-photo")) {
                            jQuery("#dialog img").attr("src", owlItem.prev(".owl-item").children(".gallery-item").children("img").attr("data-photo"));
                            owlItem = owlItem.prev();
                        } else {
                            jQuery("#dialog").dialog('close');
                        }
                    }
                },
                {
                    "class": "dialog-nav-btn dialog-next-btn",
                    click: function() {  
                        if(owlItem.next(".owl-item").children(".gallery-item").children("img").attr("data-photo")) {
                            jQuery("#dialog img").attr("src", owlItem.next(".owl-item").children(".gallery-item").children("img").attr("data-photo"));
                            owlItem = owlItem.next();
                        } else {
                            jQuery("#dialog").dialog('close');
                        }
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

    // photo modal for the main project image
    jQuery('.exhibit-picture img').on("click", function () {
        jQuery('body').append('<div id="dialog" class="projectImageDialog"><img src="' + jQuery(this).attr('data-photo') + '" width="100%" /></div>');
        var winW = jQuery(window).width() - 50;
        var winH = jQuery(window).height() - 150;
        jQuery('#dialog').dialog({
            dialogClass: ["hide-heading", "entry-gallery"],
            modal: true,
            height: winH,
            width: winW,
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
})