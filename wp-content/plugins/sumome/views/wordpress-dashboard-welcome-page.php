<div class="sumome-plugin-dashboard-widget-inner">
<?php
if(isset($_COOKIE['__smUser'])) {
  $sumomeStatus="status-logged-in";
} else{
  $sumomeStatus="status-logged-out";
}

if (!isset($noClose)) print '<div class="sumome-plugin-dashboard-widget-close-button"><div></div></div>';
?>
	<div class="sumome-plugin-dashboard-widget-header">
		<div class="forms">

			<div class="sumome-wp-dash-logged-in <?php print $sumomeStatus?>">
				<div class="sumome-plugin-dashboard-widget-header-title">Sumo is Connected!</div>
				<div class="sumome-plugin-dashboard-widget-header-button">
		            <button type="submit" class="button green dashboard-button" onclick="document.location.href='<?php print admin_url('admin.php?page=sumo')?>'">DASHBOARD</button>
		        </div>
			</div>

			<div class="sumome-wp-dash-logged-out <?php print $sumomeStatus?>">


				<div class="sumome-popup-forms">

				</div>
				<div class="sumome-plugin-dashboard-widget-header-title">Please Connect Sumo</div>
				<div class="sumome-plugin-dashboard-widget-header-desc">Sumo is the #1 plugin to grow your WordPress site.<br>
				Connect today and you'll <b>grow</b> your traffic, <b>build</b> a massive following, and <b>track</b> your progress.</div>
				<div class="sumome-plugin-dashboard-widget-header-button">
		            <button type="submit" class="button green connect-button" id="connectFormButton">CONNECT SUMO</button>
		            <div class="sumome-plugin-dashboard-widget-learn-more">Learn More</div>
		        </div>
			</div>
		</div>
	</div>
	<div class="sumome-plugin-dashboard-widget-container">
		<div class="sumome-plugin-dashboard-widget-top-note-container">
			<div class="sumome-plugin-dashboard-widget-top-note">
				<div class="sumome-plugin-dashboard-widget-top-note-title">Grow Your Site</div>
				<div class="sumome-plugin-dashboard-widget-top-note-desc">Sumo is the most trusted way to grow your site,<br> <b>used by 500,000+ websites.</b><br><br>
				<ul>
					<li>12 of the most essential tools, all in ONE place</li>
					<li>No coding needed</li>
					<li>Get started in 37 seconds</li>
				</ul>
				</div>
			</div>
		</div>
		<div class="sumome-plugin-dashboard-widget-separator2"></div>
		<div class="sumome-plugin-dashboard-widget-separator"></div>

		<div class="sumome-plugin-dashboard-widget-row">
			<div class="sumome-plugin-left">
				<div class="sumome-plugin-dashboard-widget-row-title">Grow Your Traffic</div>
				<div class="sumome-plugin-dashboard-widget-row-desc">
					<ul>
						<li>Add Sharing buttons to your site</li>
						<li>Make your posts and images go viral</li>
						<li>Get free traffic</li>
					</ul>
				</div>
			</div>
			<div class="sumome-plugin-right"><img src="<?php print plugins_url('images/sumome-site-welcome1.png', dirname(__FILE__))?>"></div>
		</div>

		<div class="sumome-plugin-dashboard-widget-row no-mobile">
			<div class="sumome-plugin-left"><img src="<?php print plugins_url('images/sumome-site-welcome2.png', dirname(__FILE__))?>"></div>
			<div class="sumome-plugin-right">
				<div class="sumome-plugin-dashboard-widget-row-title">Build A Following</div>
				<div class="sumome-plugin-dashboard-widget-row-desc">
					<ul>
						<li>Get people coming back to your site</li>
						<li>Grow your email list</li>
						<li>Get more social media followers</li>
					</ul>
				</div>

			</div>
		</div>

		<div class="sumome-plugin-dashboard-widget-row mobile">
			<div class="sumome-plugin-left">
				<div class="sumome-plugin-dashboard-widget-row-title">Build A Following</div>
				<div class="sumome-plugin-dashboard-widget-row-desc">
					<ul>
						<li>Get people coming back to your site</li>
						<li>Grow your email list</li>
						<li>Get more social media followers</li>
					</ul>
				</div>
			</div>
			<div class="sumome-plugin-right"><img src="<?php print plugins_url('images/sumome-site-welcome2.png', dirname(__FILE__))?>"></div>
		</div>


		<div class="sumome-plugin-dashboard-widget-row">
			<div class="sumome-plugin-left">
				<div class="sumome-plugin-dashboard-widget-row-title">Track Your Progress</div>
				<div class="sumome-plugin-dashboard-widget-row-desc">
					<ul>
						<li>Discover where people are clicking on your site</li>
						<li>See how many visitors you get in real-time</li>
						<li>Learn if people are actually reading your posts</li>
					</ul>
				</div>
			</div>
			<div class="sumome-plugin-right"><img src="<?php print plugins_url('images/sumome-site-welcome3.png', dirname(__FILE__))?>"></div>
		</div>

		<div class="sumome-plugin-dashboard-widget-separator2">
			<div class="sumome-plugin-dashboard-widget-middle-note-title">Your Favorite Websites Already Use Sumo</div>

			<div class="sumome-plugin-dashboard-widget-middle-note-desc">500,000+ sites are powered by Sumo.</div>

			<div class="sumome-plugin-dashboard-widget-middle-note-clients">
				<img src="<?php print plugins_url('images/sumome-site-clients-airbnb.png', dirname(__FILE__))?>">
				<img src="<?php print plugins_url('images/sumome-site-clients-chive.png', dirname(__FILE__))?>">
				<img src="<?php print plugins_url('images/sumome-site-clients-tonyrobbins.png', dirname(__FILE__))?>">
				<img src="<?php print plugins_url('images/sumome-site-clients-entrepreneur.png', dirname(__FILE__))?>">
				<img src="<?php print plugins_url('images/sumome-site-clients-beachbody.png', dirname(__FILE__))?>">
				<img src="<?php print plugins_url('images/sumome-site-clients-artofman.png', dirname(__FILE__))?>">
				<img src="<?php print plugins_url('images/sumome-site-clients-4hourworkweek.png', dirname(__FILE__))?>">
			</div>
		</div>

		<div class="sumome-plugin-dashboard-widget-row">
			<div class="sumome-plugin-left">
				<div class="sumome-plugin-dashboard-widget-row-title">We've Got Your Back</div>
				<div class="sumome-plugin-dashboard-widget-row-desc">
					<ul>
						<li>Unlimited help from our experts</li>
						<li>Make sure your site is running smoothly</li>
						<li>Lightning-fast response time</li>
					</ul>
				</div>
			</div>
			<div class="sumome-plugin-right"><img src="<?php print plugins_url('images/sumome-site-team.jpg', dirname(__FILE__))?>"></div>
		</div>


		<div class="forms">

			<div class="sumome-wp-dash-logged-in <?php print $sumomeStatus?>">
				<div class="sumome-plugin-dashboard-widget-header-button sumome-plugin-dashboard-widget-footer-button">
		            <button type="submit" class="button green dashboard-button" onclick="document.location.href='<?php print admin_url('admin.php?page=sumo')?>'">DASHBOARD</button>
		        </div>
			</div>

			<div class="sumome-wp-dash-logged-out <?php print $sumomeStatus?>">
					<div class="sumome-plugin-dashboard-widget-header-button sumome-plugin-dashboard-widget-footer-button">
		            <button type="submit" class="button green connect-button" id="connectFormButton">CONNECT SUMO</button>
		            <div class="sumome-plugin-dashboard-widget-learn-more">Learn More</div>
		        </div>
			</div>
		</div>


		<div class="sumome-plugin-dashboard-widget-row <?php print $sumomeStatus?>">
			<div class="sumome-plugin-center">Need to restore an existing account?
		<?php
		if (substr_count($_SERVER['REQUEST_URI'], 'dashboard')>0) {
			?>
			<a href="<?php print admin_url('admin.php?page=sumo-siteID')?>">Click here</a>
			<?php
		} else {
			?>
			<div class="sumome-plugin-linkalike sumome-link-button sumome-tile-advanced-settings item-tile" data-name="sumome-control-advanced-settings" data-title="">Click here</div>
			<?php
		}
		?>
			</div>
		</div>





	</div>



</div>


<script>
<?php
if (wp_is_mobile()) {
?>
	jQuery('.sumome-plugin-dashboard-widget').addClass('minimized');
<?php
}
?>
jQuery(document).on('click', '.sumome-plugin-dashboard-widget div.sumome-plugin-dashboard-widget-close-button',function () {
  jQuery('.sumome-plugin-dashboard-widget').addClass('minimized');
  jQuery.post(ajaxurl, { action: 'sumome_hide_dashboard_overlay' }, function(data) {

  });
});

</script>

