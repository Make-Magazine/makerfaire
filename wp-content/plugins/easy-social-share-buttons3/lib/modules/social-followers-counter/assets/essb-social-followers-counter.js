/**
 * ESSB Instagram Custom Feed Code
 */

jQuery(document).ready(function($){
	"use strict";
	/** 
	 * Setup update of the feed function
	 */
	var essbInstagramFollowersUpdate = window.essbInstagramFollowersUpdate = function (id, source) {		
		var url = '';
		
		source = source.replace('#', '').replace('@', '');
		
		url = 'https://instagram.com/' + source;
		
		$.ajax({
            type: 'GET',
            url: url,
            async: true,
            cache: false
        }).done( function (data) {
            data = JSON.parse(data.split("window._sharedData = ")[1].split(";<\/script>")[0]).entry_data.ProfilePage[0];
        	data = essbInstagramFollowersConvertSource(data);    
        	essbInstagramFollowersCacheUpdate(id, data);
        });
	};
	
	var essbInstagramFollowersCacheUpdate = window.essbInstagramFollowersCacheUpdate = function(id, data) {
		console.log('(update) Instagram Followers: ' + data.followers);
		  $.ajax({
              url: essbInstagramFollowersUpdater.ajaxurl,
              type: 'POST',
              async: true,
              cache: false,
              data: {
                  action: 'essb-instagram-followers-request-cache',
                  security: essbInstagramFollowersUpdater.nonce,
                  data: data && data.followers ? data.followers : ''
              }
          }).done( function( response ){
        	  console.log('(update) Instagram Followers stored');
        	  if (id && id != '')
        		  $('#' + id + ' .essbfc-followers-count').html(response);
          });
	};	
	
	var essbInstagramFollowersConvertSource = function(data) {
		var profileData = {}, images = [];			
		
		if (data.graphql && data.graphql.user) {
			profileData = {
				'bio': 	data.graphql.user.biography || '',
				'external': 	data.graphql.user.external_url || '',
				'followers': 	data.graphql.user.edge_followed_by.count || '',
				'profile': 	data.graphql.user.profile_pic_url || '',
				'profile_hd': 	data.graphql.user.profile_pic_url_hd || '',
			};
		}
		return profileData;
	};
	
	// Begin update the instagram feed information
	$('.essbfc-container .essbfc-instagram').each(function() {
		var requireUpdate = $(this).data('call-update') || '',
			profile = $(this).data('profile');
		
		if (requireUpdate.toString() == 'true') {
			$(this).attr('id', 'essbfc-instagram-'+Math.random().toString(36).substr(2, 9));
			
			essbInstagramFollowersUpdate($(this).attr('id'), profile);
		}
	});
	
	if (typeof (essbInstagramFollowersUpdater) != 'undefined' && (essbInstagramFollowersUpdater.forced_update || '') == 'true') {
		essbInstagramFollowersUpdate('', essbInstagramFollowersUpdater.instagram_user)
	}
	
});