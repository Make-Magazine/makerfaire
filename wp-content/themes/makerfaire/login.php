
<?php
//Wordpress header and Theme header call
get_header();

/*
 * Template Name: Login Page
 */
// Get the action
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
$mode   = isset($_REQUEST['mode'])   ? $_REQUEST['mode'] : 'signin';
$sign   = isset($_REQUEST['sign'])   ? (int) $_REQUEST['sign'] : '';

//Skip if user is logged in.
if (is_user_logged_in() && $action == 'logout') {
  wp_logout();
  wp_redirect(home_url());
}
//Enqueue Auth0 Required scripts
wp_enqueue_script( 'wpa0_lock', WP_Auth0_Options::Instance()->get('cdn_url'), 'jquery' );

//Enqueue Login Style
$current_theme = wp_get_theme();
wp_enqueue_style('login-styles', get_stylesheet_directory_uri() . '/css/login-styles.css', array(), $current_theme->get( 'Version' ));

//Setup dynamic message area depeding on modes or referrer
$loginmessage = '';
switch ($sign) {
    case "1":
        $currentloginurl = basename($_SERVER['REQUEST_URI']);
        $loginmessage = "Log in to submit an entry.
<br />If you haven’t logged in before, Sign up.
 <br /><br />
If you’ve logged in before and are experiencing issues, we’ve updated our login system. <br />
- Try resetting your password. <br />
- If you previously logged in with Facebook or Google, you will have to Sign Up again. Your previous account will be connected to your new sign up.<br />";
        break;
     case "2":
        $loginmessage = "Log in to submit or manage an entry <br /><br />
If you’ve logged in before and are experiencing issues, we’ve updated our login system. <br />
- Try resetting your password. <br />
- If you previously logged in with Facebook or Google, you will have to Sign Up again. Your previous account will be connected to your new sign up.<br />";

        break;
    case "3":
        $loginmessage = "Log in to manage your entries <br /><br />
If you’ve logged in before and are experiencing issues, we’ve updated our login system. <br />
- Try resetting your password. <br />
- If you previously logged in with Facebook or Google, you will have to Sign Up again. Your previous account will be connected to your new sign up.<br />";

        break;
    case "5":
        $loginmessage = "Log in to submit your information. <br />If you haven't signed in before, Sign Up. <br /><br />
If you’ve logged in before and are experiencing issues, we’ve updated our login system. <br />
- Try resetting your password. <br />
- If you previously logged in with Facebook or Google, you will have to Sign Up again. Your previous account will be connected to your new sign up.<br />";

        break;
    default:
        $loginmessage = "Log In <br /><br />
If you’ve logged in before and are experiencing issues, we’ve updated our login system. <br />
- Try resetting your password. <br />
- If you previously logged in with Facebook or Google, you will have to Sign Up again. Your previous account will be connected to your new sign up.<br />";

        break;
}
if (strpos(wp_referer_field(),'edit-entry') > 0)
  $loginmessage = 'Sign in to submit or manage<br /> your entries.';
if ($mode == "reset")
  $loginmessage = "Change your password";


?>
<div class="clear"></div>

<div class="container">
  <div class="row padbottom padtop vertical-align">
    <div class="col-md-2 col-md-offset-2">
      <?php
      /**
       * Detect Auth0 plugin.
       */
      if (isset($_GET['wle'])) {
        wp_login_form();
      } else {
        renderAuth0Form(true, array( "mode" => $mode));
      }

      ?>
    </div>
    <div class="col-md-offset-2 hidden-xs">
      <div>
        <ul class="list-unstyled">
          <li><?php echo $loginmessage; ?></li>
          <li class="mftagline padtop">
            <img style="width: auto; height: 58px; margin-right:10px;" src="https://makerfaire.com/wp-content/uploads/2015/05/makey-lg-01.png">The Maker Faire Team
          </li>
        </ul>
      </div>
    </div>
  </div>
</div><!--Container-->

<?php wp_footer();


/* Page specific functions */
function renderAuth0Form($canShowLegacyLogin = true, $specialSettings = array()){
  if (!$canShowLegacyLogin || !isset($_GET['wle'])) {
    //Require auth0
    require_once( ABSPATH . 'wp-content/plugins/auth0/templates/auth0-login-form.php');
  }else{
    wp_login_form();
  }
}