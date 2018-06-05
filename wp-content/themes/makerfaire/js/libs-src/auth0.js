window.addEventListener('load', function() {
  // buttons and event listeners
  var loginBtn    = document.getElementById('newLoginBtn');
  var logoutBtn   = document.getElementById('newLogoutBtn');
  var profileView = document.getElementById('profile-view');
  var tokenRenewalTimeout;

  //default profile view to hidden
  loginBtn.style.display    = 'none';
  profileView.style.display = 'none';

  var userProfile;
  var webAuth = new auth0.WebAuth({
    domain: AUTH0_DOMAIN,
    clientID: AUTH0_CLIENT_ID,
    redirectUri: AUTH0_CALLBACK_URL,
    audience: 'https://' + AUTH0_DOMAIN + '/userinfo',
    responseType: 'token id_token',
    scope: 'openid profile',
    leeway: 60
  });
  localStorage.setItem('redirect_to',AUTH0_REDIRECT_URL);

  loginBtn.addEventListener('click', function(e) {
    e.preventDefault();
    localStorage.setItem('redirect_to',AUTH0_REDIRECT_URL);
    webAuth.authorize(); //login to auth0
  });

  logoutBtn.addEventListener('click', logout);

  function scheduleRenewal() {
    var expiresAt = JSON.parse(localStorage.getItem('expires_at'));
    var delay = expiresAt - Date.now();
    if (delay > 0) {
      tokenRenewalTimeout = setTimeout(function() {
        renewToken();
      }, delay);
    }
  }

  function setSession(authResult) {
    // Set the time that the access token will expire at
    var expiresAt = JSON.stringify(
      authResult.expiresIn * 1000 + new Date().getTime()
    );
    localStorage.setItem('access_token', authResult.accessToken);
    localStorage.setItem('id_token', authResult.idToken);
    localStorage.setItem('expires_at', expiresAt);
    scheduleRenewal();
  }

  function logout() {
    // Remove tokens and expiry time from localStorage for auth0
    localStorage.removeItem('access_token');
    localStorage.removeItem('id_token');
    localStorage.removeItem('expires_at');
    clearTimeout(tokenRenewalTimeout);

    //logout of auth0
    localStorage.setItem('redirect_to' ,AUTH0_REDIRECT_URL);
    webAuth.logout({
      returnTo: AUTH0_REDIRECT_URL
    });
    displayButtons();

  }

  function isAuthenticated() {
    // Check whether the current time is past the
    // access token's expiry time
    var expiresAt = JSON.parse(localStorage.getItem('expires_at'));
    return new Date().getTime() < expiresAt;
  }

  function handleAuthentication() {
    webAuth.parseHash(function(err, authResult) {
      if (authResult && authResult.accessToken && authResult.idToken) {
        window.location.hash = '';
        setSession(authResult);

        //after login redirect to previous page (after 5 second delay)
        var redirect_url = localStorage.getItem('redirect_to');
        setTimeout(function(){location.href=redirect_url;} , 2500);
      } else if (err) {
        console.log(err);
        alert(
          'Error: ' + err.error + '. Check the console for further details.'
        );
      }
     setTimeout(function(){displayButtons();}, 1500); // hold off on displaying the buttons until we know we're logged in
    });
  }

  function displayButtons() {
    if (isAuthenticated()) {
      loginBtn.style.display = 'none';
      profileView.style.display = 'flex';
      getProfile();
      //login to wordpress if not already
      WPlogin();//login to wordpress
    } else {
      loginBtn.style.display = 'flex';
      profileView.style.display = 'none';
      //logout of wordpress if not already
      WPlogout();//login to wordpress
    }
  }

  function getProfile() {
    if (!userProfile) {
      var accessToken = localStorage.getItem('access_token');

      if (!accessToken) {
        console.log('Access token must exist to fetch profile');
      }

      webAuth.client.userInfo(accessToken, function(err, profile) {
        if (profile) {
          userProfile = profile;
          displayProfile();
        }
      });
    } else {
      displayProfile();
    }
  }

  function displayProfile() {
    // display the avatar
      document.querySelector('#profile-view img').src = userProfile.picture;
  }

  function renewToken() {
    webAuth.checkSession({},
      function(err, result) {
        if (err) {
          console.log(err);
        } else {
          setSession(result);
          displayButtons();
        }
      }
    );
  }

  function WPlogin(){
    if (isAuthenticated()) {
      getProfile();
      if (typeof userProfile !== 'undefined') {
        var user_id = userProfile.sub;
        var access_token = localStorage.getItem('access_token');
        var id_token     = localStorage.getItem('id_token');

        //login to wordpress
        var data = {
          'action'              : 'mm_wplogin',
          'auth0_userProfile'   : userProfile,
          'auth0_access_token'  : access_token,
          'auth0_id_token'      : id_token
        };
        jQuery.post(ajax_object.ajax_url, data, function(response) {
          //alert('Got this from the server: ' + response);
        });
      }
    }
  }

  function WPlogout(){
    //logout of wordpress
    var data = {
      'action': 'mm_wplogout'
    };
    if ( jQuery( '#wpadminbar' ).length ) {
        jQuery( 'body' ).removeClass( 'adminBar' ).removeClass( 'logged-in' );
        jQuery( '#wpadminbar' ).remove();
        jQuery( '#mm-preview-settings-bar' ).remove();
    }

    jQuery.post(ajax_object.ajax_url, data, function(response) {
      //alert('Got this from the server: ' + response);
    });
  }

  //check if logged in another place
  webAuth.checkSession({},
    function(err, result) {
      if (err) {
        console.log(err);
      } else {
        setSession(result);
        displayButtons();
      }
    }
  );

  //handle authentication
  handleAuthentication();
  scheduleRenewal();
});