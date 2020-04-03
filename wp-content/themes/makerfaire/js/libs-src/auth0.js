window.addEventListener('load', function() {
	
	if ( jQuery( "#wp-admin-bar-logout" ).length ) {
		jQuery( "#wp-admin-bar-logout" ).remove();
	}
	if ( !jQuery( "#LoginBtn" ).length ) {
		var loginBtn = document.createElement('div');
		loginBtn.setAttribute("id", "LoginBtn");
	}else{
		var loginBtn    = document.getElementById('LoginBtn');
	}

	if ( !jQuery( "#LogoutBtn" ).length ) {
		var logoutBtn = document.createElement('div');
		logoutBtn.setAttribute("id", "LogoutBtn");
	}else{
		var logoutBtn    = document.getElementById('LogoutBtn');
	}

	if ( !jQuery( "#profile-view" ).length ) {
		var profileView = document.createElement('div');
		profileView.setAttribute("id", "profile-view");
	}else{
		var profileView    = document.getElementById('profile-view');
	}
	
	//default profile view to hidden
	loginBtn.style.display    = 'none';
	profileView.style.display = 'none';
	
	var userProfile;
	
	var progressBar = jQuery(".progress .progress-bar");
	function updateProgressBar(percent) {
		if ( jQuery( '#authenticated-redirect' ).length ) {
			progressBar.attr("aria-valuenow", percent).css("width", percent).text(percent);
		}
	}
	
	var webAuth = new auth0.WebAuth({
		domain: AUTH0_DOMAIN,
		clientID: AUTH0_CLIENT_ID,
		redirectUri: AUTH0_CALLBACK_URL,
		audience: 'https://' + AUTH0_DOMAIN + '/userinfo',
		responseType: 'token id_token',
		scope: 'openid profile email user_metadata', //scope of data pulled by auth0
		leeway: 60
	});
	
	function clearLocalStorage() {
		 localStorage.removeItem('access_token');
		 localStorage.removeItem('id_token');
		 localStorage.removeItem('expires_at');
	}
	
	loginBtn.addEventListener('click', function(e) {
		e.preventDefault();
		if(location.href.indexOf('authenticated') >= 0){
			localStorage.setItem('redirect_to', templateUrl);
		}else{
			localStorage.setItem('redirect_to',location.href);
		}
		webAuth.authorize(); //login to auth0
	});

	logoutBtn.addEventListener('click', function(e) {
		e.preventDefault();

		// Remove tokens and expiry time from localStorage
		localStorage.removeItem('access_token');
		localStorage.removeItem('id_token');
		localStorage.removeItem('expires_at');

		//redirect to auth0 logout page
		window.location.href = 'https://makermedia.auth0.com/v2/logout?returnTo='+templateUrl+ '&client_id='+AUTH0_CLIENT_ID;
	});

	function setSession(authResult) {
		if ( authResult ) {
			// Set the time that the access token will expire at
			var expiresAt = JSON.stringify(
			authResult.expiresIn * 1000 + new Date().getTime()
			);
			localStorage.setItem('access_token', authResult.accessToken);
			localStorage.setItem('id_token', authResult.idToken);
			localStorage.setItem('expires_at', expiresAt);
		} else {
			clearLocalStorage();
		}
	}

	function isAuthenticated() {
		// Check whether the current time is past the access token's expiry time
		if(localStorage.getItem('expires_at')){
			var expiresAt = JSON.parse(localStorage.getItem('expires_at'));
			return new Date().getTime() < expiresAt;
		}else{
			return false;
		}
	}

	function displayButtons() {
		if (isAuthenticated()) {
		  loginBtn.style.display = 'none';

		  //get user profile from auth0
		  profileView.style.display = 'flex';
		  updateProgressBar("50%");
		  getProfile();

		  //login redirect
		  if ( jQuery( '#authenticated-redirect' ).length ) { //are we on the authentication page?
			if(localStorage.getItem('redirect_to')){    //redirect
			  var redirect_url = localStorage.getItem('redirect_to'); //retrieve redirect URL
			  localStorage.removeItem('redirect_to'); //unset after retrieved
			  location.href=redirect_url;
			}else{  //redirect to home page
			  location.href=templateUrl;
			}
		  }
		} else {
		  loginBtn.style.display = 'flex';
		  profileView.style.display = 'none';
			if ( jQuery( '#authenticated-redirect' ).length ) { 
				jQuery(".redirect-message").html("<a href='javascript:location.reload();'>Try your login again</a>");
			}
		}
	}

	function getProfile() {
		var accessToken = localStorage.getItem('access_token');
		if (!accessToken) {
			console.log('Access token must exist to fetch profile');
			errorMsg('Login without Access Token');
		}

		webAuth.client.userInfo(accessToken, function(err, profile) {
			if (profile) {
				userProfile = profile;
				// make sure that there isn't a wordpress acount with a different user logged in
				if(ajax_object.wp_user_email && ajax_object.wp_user_email != userProfile.email) {
					WPlogout("wp_only");
				}
				// display the avatar
				document.querySelector('.dropdown-toggle img').src = userProfile.picture;
				document.querySelector('.profile-info img').src = userProfile.picture;
				document.querySelector('.dropdown-toggle img').style.display = "block";
				document.querySelector('#LoginBtn').style.display = "none";
				document.querySelector('.profile-email').innerHTML = userProfile.email; 
				if(userProfile['http://makershare.com/first_name'] != undefined && userProfile['http://makershare.com/last_name'] != undefined) {
					document.querySelector('.profile-info .profile-name').innerHTML = userProfile['http://makershare.com/first_name'] + " " + userProfile['http://makershare.com/last_name'];
				}
				updateProgressBar("75%");
				WPlogin();
			}
			if (err) {
				errorMsg("There was an issue logging in at the getProfile phase. That error was: " + JSON.stringify(err));
			}
		});

	}

	function WPlogin(){
		if (typeof userProfile !== 'undefined') {
			var user_id      = userProfile.sub;
			var access_token = localStorage.getItem('access_token');
			var id_token     = localStorage.getItem('id_token');

			//login to wordpress
			var data = {
				'action'              : 'mm_wplogin',
				'auth0_userProfile'   : userProfile,
				'auth0_access_token'  : access_token,
				'auth0_id_token'      : id_token
			};

			jQuery.ajax({
				type: 'POST',
				url: ajax_object.ajax_url,
				data: data,
				timeout: 10000,
				success: function(data){
				},
			}).fail(function(xhr, status, error) {
				if(status === 'timeout') {
					 alert( "Your login has timed out. Please try the login again." );
					 errorMsg(userProfile.email + " ran over the timeout limit of 10 seconds. Error was: " + JSON.stringify(error));
					 location.href = templateUrl;
				} else {
					 alert( "I'm sorry. We had an issue logging you into our system. Please try the login again." );
					 errorMsg(userProfile.email + " had an issue logging in at the WP Login phase. That error is: " + JSON.stringify(error));
				}
			});

		}else{

		}
	}

	function WPlogout(wp_only){
		//logout of wordpress, in most cases this includes logging out of auth0. In cases where there is already a different wp user, just log them out and log back in with the new user
		var data = {
			'action': 'mm_wplogout',
		};
		if ( jQuery( '#wpadminbar' ).length ) {
			jQuery( 'body' ).removeClass( 'adminBar' ).removeClass( 'logged-in' );
			jQuery( '#wpadminbar' ).remove();
			jQuery( '#mm-preview-settings-bar' ).remove();
		}
		jQuery.post(ajax_object.ajax_url, data, function(response) {
			if(wp_only != "wp_only"){
				// load this in an iframe so page itself doesn't get sent back to homepage, hopefully
				// auth0 application only allows set urls as the returnto, with the homepage being the only one being set
				jQuery("#auth0Logout").attr("src", 'https://makermedia.auth0.com/v2/logout?returnTo=' + templateUrl + '&client_id='+AUTH0_CLIENT_ID);
			}else{
				WPlogin();
			}
		});
	}

	//check if logged in another place
	webAuth.checkSession({},
		function(err, result) {
			if (err) {
				clearLocalStorage();
				if(err.error!=='login_required'){
					errorMsg(userProfile.email + " had an issue logging in at the checkSession phase. That error was: " + JSON.stringify(err));
				}
			} else {
				setSession(result);
			}
			displayButtons();
		}
	);
	
	// this is for logging errors to the php error logs
	function errorMsg(message) {
		var data = {
			'action'       : 'make_error_log',
			'make_error'   : message
		};
		jQuery.post(ajax_object.ajax_url, data, function(response) {});
	}
		
});
