/*
 * This JS is used on the WP login page to force the user to the auth0 login page
 */
function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

var baseurl = window.location.origin;

var AUTH0_CLIENT_ID    = 'Ya3K0wmP182DRTexd1NdoeLolgXOlqO1';
var AUTH0_DOMAIN       = 'makermedia.auth0.com';
var AUTH0_CALLBACK_URL = baseurl+"/authenticate-redirect/";
var AUTH0_REDIRECT_URL = baseurl;

var webAuth = new auth0.WebAuth({
  domain: AUTH0_DOMAIN,
  clientID: AUTH0_CLIENT_ID,
  redirectUri: AUTH0_CALLBACK_URL,
  audience: 'https://' + AUTH0_DOMAIN + '/userinfo',
  responseType: 'token id_token',
  scope: 'openid profile',
  leeway: 60
});

var redirect_to = decodeURIComponent(GetURLParameter('redirect_to'));
if(redirect_to ==='') redirect_to = baseurl;
localStorage.setItem('redirect_to', redirect_to);
console.log(redirect_to);

webAuth.authorize(); //login to auth0