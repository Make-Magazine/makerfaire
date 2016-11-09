    <!-- Site ID -->
<div class="sumome-plugin-main">
    <div class="site-ID-container">
      <div class="row row3 site-ID">
        <div class="columns">
          <div class="list-bullet">
            <h4 class="list-number-title">Your Site Id</h4>
          </div>

          <form method="post" action="options.php">
            <?php settings_fields('sumome'); ?>

            <table>
              <?php do_settings_fields('sumome', 'sumome-settings') ?>
            </table>
            <div class="sumome-instructions">
              NOTE: If you already have a site ID from a previous installation and you wish to retain all your settings then enter the site ID above otherwise you may use a new site ID to perform a new installation.  Changing the site ID will lose all settings, apps, and purchases.
            </div>
            <?php submit_button(); ?>
          </form>
        </div>
      </div>
    </div>
</div>
