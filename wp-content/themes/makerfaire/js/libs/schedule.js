/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

window.onload=function (e) {
   if (jQuery( "#scheduleTab" ).length ) { //do the schedule tabs exist?S
        var hash = window.location.hash;
        hash = hash.toLowerCase();
     
        if(hash!==''){ //was a hash set?
            jQuery('.dropdown li').each(function(){ jQuery(this).removeClass('active');});
            jQuery('.dropdown a[href="'+hash+'"]').tab('show'); // Select tab by name
            var dispText = jQuery('.dropdown a[href="'+hash+'"]').text(); // Select tab by name
            //set dropdown text
            jQuery ('button .pickText').text(dispText);
        }                
   }
};
jQuery(document).ready(function () {
    jQuery('.dropdown-menu li').click(function() {
        jQuery('.dropdown li').each(function(){ jQuery(this).removeClass('active');});
        jQuery(this).addClass('selected');
        
        var dispText = jQuery(this).text();
        //set dropdown text
        jQuery ('button .pickText').text(dispText);
    });
});


