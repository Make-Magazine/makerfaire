<?php
/*
*
* Template Name: Registration
* GIGYA OLD
*
*/
?>
<?php get_header('login'); ?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-8">
	<br />
	<h4>Please sign in using one of the following providers:</h4><br /><br />
	<div id="loginDiv"></div>
	<script type="text/javascript">
	    gigya.socialize.showLoginUI({
			height: 85
	        ,width: 360
			,showTermsLink:false // remove 'Terms' link
			,hideGigyaLink:true // remove 'Gigya' link
			,buttonsStyle: '' // Change the default buttons design to "Full Logos" design
			,showWhatsThis: true // Pop-up a hint describing the Login Plugin, when the user rolls over the Gigya link.
	        ,containerID: 'loginDiv' // The component will embed itself inside the loginDiv Div
			,cid:''
			});
	</script>
    <br />
    <br /><br /><br />
	<h4>Click the button below to sign out from Gigya platform:</h4><br /><br />
    <input id="btnLogout" type="button" value="Sign Out"
            onclick="logoutFromGS()"/>
    <br />
    <br />
    <div id="status"></div>

		</div><!--Content-->

		<?php get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>