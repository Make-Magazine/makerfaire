window.addEventListener('load', function() {
  // buttons and event listeners
  var loginBtn    = document.getElementById('qsLoginBtn');
  var logoutBtn   = document.getElementById('qsLogoutBtn');
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

  loginBtn.addEventListener('click', function(e) {
    e.preventDefault();
    localStorage.setItem('redirect_to',AUTH0_REDIRECT_URL);
    webAuth.authorize();
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
    // Remove tokens and expiry time from localStorage
    localStorage.removeItem('access_token');
    localStorage.removeItem('id_token');
    localStorage.removeItem('expires_at');
    displayButtons();
    clearTimeout(tokenRenewalTimeout);
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
        loginBtn.style.display = 'none';

        //after login redirect to previous page (after 5 second delay)
        var redirect_url = localStorage.getItem('redirect_to');
        setTimeout(function(){location.href=redirect_url;} , 3000);

      } else if (err) {
        console.log(err);
        alert(
          'Error: ' + err.error + '. Check the console for further details.'
        );
      }
      displayButtons();
    });
  }

  function displayButtons() {
    if (isAuthenticated()) {
      loginBtn.style.display = 'none';
      getProfile();
      profileView.style.display = 'flex';
    } else {
      loginBtn.style.display = 'flex';
      profileView.style.display = 'none';
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