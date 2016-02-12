jQuery( document ).ready(function() {   
    jQuery(':input',document.myForm).bind("change", function() { 
       setConfirmUnload(true); 
    }); // Prevent accidental navigation away
});

function setConfirmUnload(on) {
     // To avoid IE7 and prior jQuery version issues   
     // we are directly using window.onbeforeunload event
     window.onbeforeunload = (on) ? unloadMessage : null;
}

function unloadMessage() {

    if(Confirm('You have entered new data on this page.  If you navigate away from this page without first saving your data, the changes will be lost.')) {

            jQuery.ajax({
               type: "POST",
               url: "some.php",
               data: "name=John&location=Boston",
               success: function(msg){
                 alert( "Data Saved: " + msg );
               }
             });

    }

}