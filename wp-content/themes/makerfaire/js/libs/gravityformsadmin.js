jQuery( document ).ready(function() {   
	 var $tableheader = jQuery('th#header');
	 var $tableheadentry = jQuery('th#details');
	 //$tableheadentry.parents('table.entry-detail-view') .children('tbody') .hide();
	 jQuery(function() {
		     
		    jQuery($tableheadentry).click(
		    		function() {
		    			jQuery(this) .parents('table.entry-detail-view') .children('tbody') .toggle();
		    		}
		    	);
		    jQuery($tableheader).click(
		    		function() {
		    			jQuery(this) .parents('table.entry-detail-view') .children('tbody') .toggle();
		    		}
		    	);
		    	
		    jQuery('#datetimepicker').datetimepicker({value:'2015/04/15 05:03',step:10});

		    
		    jQuery('#datetimepickerstart').datetimepicker({
		    	formatTime:'g:i a',
		    	formatDate:'d.m.Y',
		    	defaultTime:'10:00 am'
		    });
		    jQuery('#datetimepickerend').datetimepicker({
		    	formatTime:'g:i a',
		    	formatDate:'d.m.Y',
		    	defaultTime:'10:00 am'
		    });
		    
 	
		    		
                    jQuery('#gf_admin_page_title').click(
			 function() {
				 window.location="/wp-admin/admin.php?page=gf_entries&view=entry&id=20&lid="+prompt('Enter your ID!', ' ');
				 }
			 );
		});                
        
        //function to make certain fields in the individual entry display stand out
        //and to apply the same class to the parent field.
        jQuery('.entryStandout').each(function(){
            jQuery(this).parent().addClass("standoutParent");
        });
       
	//on update of rating submit ajax to update value in database
        jQuery('.star-rating :radio').change(               
            function(){                               
                var entry_id = jQuery("input[name=entry_info_entry_id]").val();;
              
                var data = {
			'action': 'update-entry-rating',
			'rating_entry_id': entry_id,
                        'rating': this.value,
                        'rating_user': userSettings.uid
		};
                jQuery.post(ajaxurl, data, function(response) {
                    jQuery('#updateMSG').text(response);
		});
            } 
          );
    jQuery( "#tabs" ).tabs(); 
});