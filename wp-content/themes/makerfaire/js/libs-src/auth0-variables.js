var AUTH0_CLIENT_ID    = 'Ya3K0wmP182DRTexd1NdoeLolgXOlqO1';
var AUTH0_DOMAIN       = 'makermedia.auth0.com';

if (typeof templateUrl === 'undefined') {
  var templateUrl = window.location.origin;
}
var AUTH0_CALLBACK_URL = templateUrl + "/authenticate-redirect/";
var AUTH0_REDIRECT_URL = window.location;
