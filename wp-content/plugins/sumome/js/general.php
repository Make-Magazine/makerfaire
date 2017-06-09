<script>
var setCookie = function(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
  var expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + "; " + expires;
}
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
jQuery(document).ready(function() {
    <?php
    if(isset($_COOKIE['__smUser'])) {
      print 'sumo_login_start_page_refresh();';
    }
    ?>

    function sumo_login_start_form_submit() {
      jQuery('.main-bottom').hide();
      setTimeout( function() {
        jQuery('.logged-in').html('');
        jQuery('.logged-in').append('<div class="loading"><img src="<?php echo plugins_url('images/sumome-loading.gif', dirname(__FILE__)) ?>"></div>');
      },500);

      jQuery('.logged-out').hide();
      jQuery('.logged-in').show();

      setTimeout( function() {
        document.location.href='';
      },3000);
    }

    function sumo_login_start_page_refresh() {
      jQuery('.main-bottom').hide();
      jQuery('.logged-in').html('');
      jQuery('.logged-in').append('<div class="loading"><img src="<?php echo plugins_url('images/sumome-loading.gif', dirname(__FILE__)) ?>"></div>');
      jQuery('.logged-out').hide();
      jQuery('.logged-in').show();
      sumo_login();
    }

    function sumo_login() {
      jQuery('.site-ID-container').hide();
      jQuery('.list-number').hide();

      jQuery.post(ajaxurl, { action: 'sumome_main' }, function(data) {
        jQuery('.logged-in').html(data);
        jQuery('.main-bottom').show();
      });
    }


   jQuery(document).on('click','.sumome-logged-in-container .items div, a.sumome-popup-iframe,.sumome-button,.sumome-link-button', function (e) {

      var dataName=jQuery(this).data('name');
      var dataType=jQuery(this).data('type');
      var dataTitle=jQuery(this).data('title');

      //auto size inner frame
      jQuery('.sumome-plugin-popup-container-inner').addClass('default-height');

      jQuery('.sumome-plugin-popup-container-inner').css("height", function(){
        return jQuery('.sumome-plugin-popup-container-inner').height()-jQuery('.sumome-plugin-popup-container .sumome-modal-header').height();
      });

      jQuery('.sumome-plugin-popup-container').css("top", ((jQuery(window).height()-jQuery('.sumome-plugin-popup-container-inner').height())/2)-jQuery('#wpadminbar').height());


      jQuery('.sumome-plugin-popup-container-inner').removeClass('default-height');

      if (dataType!="sumome-app") {
        if (!jQuery(this).hasClass('sumome-popup-no-dim') && !jQuery(this).parent().hasClass('sumome-popup-no-dim')) {
        	jQuery('.sumome-logged-in-container-overlay').addClass('dim');
        }
        jQuery('.sumome-plugin-popup-container .popup-title').html(dataTitle);
      }

      if (dataName=="sumome-control-advanced-settings") {
		    document.location.href='<?php print admin_url('admin.php?page=sumo-siteID')?>';
      } else if (jQuery(this).hasClass('sumome-popup-iframe')) {
        var popupHref=jQuery(this).data('href');
        if (jQuery(this).attr('href')) popupHref=jQuery(this).attr('href');

        jQuery('.sumome-plugin-popup-container .sumome-plugin-popup-contents').html('<iframe class="popup-iframe" src="'+popupHref+'"></iframe>');
        jQuery('.sumome-plugin-popup-container-inner').addClass('disable-scroll');

        jQuery('.sumome-plugin-popup-container').show();

        e.stopImmediatePropagation();
        return false;

      } else if (dataName=="sumome-control-help") {
        window.open('http://help.sumome.com');
      } else if (dataName=="sumome-control-about") {
        window.open('https://sumo.com/about?src=wpplugin');
      } else if (dataName=="sumome-control-account-settings") {
        window.open('https://sumo.com/account');
      } else if (dataName=="sumome-control-statistics") {
        document.location.href='<?php print admin_url('admin.php?page=sumo-statistics')?>';
      } else {
        jQuery('.'+dataName).click();
      }

    });

    jQuery(document).on('click','.back-logged-in', function () {
      jQuery('.logged-in .items').show();
      jQuery('.tabbed-content-container').hide();
      jQuery('.back-logged-in').hide();
    });

    //save new site-id and then logout
    jQuery(document).on('click','.popup-close,.sumome-modal-close', function () {
      jQuery('.sumome-plugin-popup-container .sumome-plugin-popup-contents').html('');
      jQuery('.sumome-plugin-popup-container').hide();
      jQuery('.sumome-plugin-popup-container-inner').removeClass('disable-scroll');
      jQuery('.sumome-logged-in-container-overlay').removeClass('dim');
      jQuery('.sumome-plugin-popup-container-inner').height('');
      getLoadInformation();
    });

    jQuery(document).on('click','.sumome-plugin-popup-container .site-ID .button-primary', function (e) {
      var press = jQuery.Event("keypress");
      press.shiftKey = false;
      jQuery('.sumome-control-close').trigger(press).click();
    });

});

function getLoadInformation(specificRequest) {
  data=jQuery(document).triggerHandler('getSandbox');

  //notifications
  jQuery('.notification-count').remove();
  if (data.unreadNotificationCount!=null && data.unreadNotificationCount>0) {
    jQuery('.sumo-notifications .item-tile-title').append('<div class="notification-count">'+data.unreadNotificationCount+'</div>');
  }

}

jQuery(window).load(function() {
	<?php
    if (isset($_GET['changeSiteKey']) && $_GET['changeSiteKey'] && !$_POST) {
    	?>
    	jQuery('.sumome-link-button.sumome-tile-advanced-settings').click();
    	<?php
    }
    ?>
});

</script>
