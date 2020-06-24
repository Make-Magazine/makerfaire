<?php
/**
 * DFP Ad Block
 *
 * Initializes all of the ads for Maker Faire.
 *
 */
global $post; ?>

<script type='text/javascript'>
	var googletag = googletag || {};
	googletag.cmd = googletag.cmd || [];
	(function() {
	var gads = document.createElement('script');
	gads.async = true;
	gads.type = 'text/javascript';
	var useSSL = 'https:' == document.location.protocol;
	gads.src = (useSSL ? 'https:' : 'http:') +
	'//www.googletagservices.com/tag/js/gpt.js';
	var node = document.getElementsByTagName('script')[0];
	node.parentNode.insertBefore(gads, node);
	})();
</script>
<script type="text/javascript">

googletag.cmd.push(function() {

	var slot1= googletag.defineSlot('/11548178/MakerFaire', [[728,90]],'div-gpt-ad-664089004995786621-1').addService(googletag.pubads()).setTargeting('pos', 'atf');
	var slot2= googletag.defineSlot('/11548178/MakerFaire', [[300,250],[300,600]],'div-gpt-ad-664089004995786621-2').addService(googletag.pubads()).setTargeting('pos', 'atf');
	var slot3= googletag.defineSlot('/11548178/MakerFaire', [[300,250],[300,600]],'div-gpt-ad-664089004995786621-3').addService(googletag.pubads()).setTargeting('pos', 'btf');
	var slot4= googletag.defineSlot('/11548178/MakerFaire', [[728,90]],'div-gpt-ad-664089004995786621-4').addService(googletag.pubads()).setTargeting('pos', 'btf');

	<?php
		// if (has_tag('project-remake')) {
		// 	echo "googletag.pubads().setTargeting('sponsor',['schick']);";
		// }
	?>
	googletag.pubads();
	googletag.enableServices();
});
</script>
<!-- End: GPT -->