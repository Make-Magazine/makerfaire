<script>
function __smReady(sumome) {
  sumome.core.emit('login');
  sumome.core.on('login', function() {
    jQuery('.sumome-wp-dash-logged-in').removeClass('status-logged-out');
    jQuery('.sumome-wp-dash-logged-in').addClass('status-logged-in');
    jQuery('.sumome-wp-dash-logged-out').removeClass('status-logged-out');
    jQuery('.sumome-wp-dash-logged-out').addClass('status-logged-in');

    getLoadInformation();
  });

  //auto populate form when clicking badge to login
  if (!getCookie('__smUser')) {
    sumome.core.on('startApp', function() {
      sumo_plugin_populate_form();
    });
  }

  function show_sumome_login() {
    sumome.core.emit('startApp', {app: 'login',   opts: { launch: false }});

    sumo_plugin_populate_form();

    sumome.core.on('login', function() {
      sumome_login_refresh();
    });
  }

  jQuery(document).on('click','.connect-button', function (e) {
    show_sumome_login();
  });

  jQuery(document).on('click','.sumome-login-login,.sumome-login-signup', function (e) {
    sumo_plugin_populate_form();
  });

  function sumo_plugin_populate_form() {
    var populateLoginInterval=setInterval(
      function(){
        if (jQuery('.sumome-login input[name=email]').is(':visible') || jQuery('.sumome-register input[name=email]').is(':visible')) {
          jQuery('.sumome-login input[name=email]').val('<?php print esc_js(get_option('admin_email', ''))?>');
          jQuery('.sumome-register input[name=email]').val('<?php print esc_js(get_option('admin_email', ''))?>');
          clearInterval(populateLoginInterval);
        }
      }
    , 100);
  }

  if (getCookie('__smUser')) {
    sumome.core.on('removeCookie',  function(cookie) {
      if (cookie=='__smUser') {
        sumo_logout();
      }

    });
  } else {
    sumome.core.on('removeCookie',  function(cookie) {
      if (cookie=='__smUser') {
        jQuery('.sumome-wp-dash-logged-in').addClass('status-logged-out');
        jQuery('.sumome-wp-dash-logged-in').removeClass('status-logged-in');
        jQuery('.sumome-wp-dash-logged-out').addClass('status-logged-out');
        jQuery('.sumome-wp-dash-logged-out').removeClass('status-logged-in');
      }

    });
  }

  function get_sumome_sandbox() {
    window.sumome = sumome;
    var sandbox = new sumome.core.Sandbox(sumome.core, 'wordpress', {}, 'wordpress');
    return sandbox.properties;
  }

  jQuery(document).on('getSandbox',function () {
    return get_sumome_sandbox();
  });

  jQuery(document).on('click', '.sumo-notifications',function () {
    sumome.core.emit('startApp', 'ee27a0af-9947-40c9-8eab-0ab6a4f7a9c1');
  });
  jQuery(document).on('click', '.sumo-store',function () {
    sumome.core.emit('startApp', 'app_store');
  });
  jQuery(document).on('click', '.sumo-apps',function () {
    sumome.core.emit('startApp', 'launcher');
  });
  jQuery(document).on('click', '.sumo-settings',function () {
    sumome.core.emit('startApp', '5d26e2da-aef5-41c1-947a-624497281723');
  });

  jQuery(document).on('click', '.sumome-control-close',function () {
     jQuery('.sumome-logged-in-container-overlay').removeClass('dim');
  });

  jQuery(document).on('click', '.popup-container .site-ID button',function () {
    function _sumome_r() {
      return (Math.random().toString(16)+"000000000").substr(2,8);
    }
    var new_sumome_site_id = _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r() + _sumome_r();
    jQuery('.sumome-site-id').val(new_sumome_site_id);
  });

  jQuery(document).on('click','.site-ID .submit input[type=submit]', function (e) {
    sumo_logout();
    sumome.core.emit('logout', false);

  });
  jQuery(document).on('click', '.sumome-plugin-dashboard-widget-learn-more',function () {
      jQuery('.sumome-plugin-dashboard-widget').removeClass('minimized');
  });

  function sumo_logout() {
      setCookie('__smToken', '', -1);
      setCookie('__smUser', '', -1);
      if(typeof sumo_logout_redirect=='function'){
        sumo_logout_redirect();
      }
  }

};
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
}
var setCookie = function(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + "; " + expires;
}

function sumome_login_refresh() {
  document.location.href='<?php print admin_url('admin.php?page=sumo')?>';
}

</script>
