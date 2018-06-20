/*
 * This JS is used on the WP login page to force the user to the auth0 login page
 */
var baseurl = window.location.origin;

var AUTH0_CLIENT_ID    = 'Ya3K0wmP182DRTexd1NdoeLolgXOlqO1';
var AUTH0_DOMAIN       = 'makermedia.auth0.com';
var AUTH0_CALLBACK_URL = baseurl+"/authenticate-redirect/";

var webAuth = new auth0.WebAuth({
  domain: AUTH0_DOMAIN,
  clientID: AUTH0_CLIENT_ID,
  redirectUri: AUTH0_CALLBACK_URL,
  audience: 'https://' + AUTH0_DOMAIN + '/userinfo',
  responseType: 'token id_token',
  scope: 'openid profile',
  leeway: 60
});

localStorage.setItem('redirect_to',baseurl + "/manage-entries/");
webAuth.authorize(); //login to auth0
