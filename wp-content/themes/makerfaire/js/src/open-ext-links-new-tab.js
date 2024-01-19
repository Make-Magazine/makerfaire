// Open external links in new tab, unless it's a make site, or targeted self, or an email link
jQuery(document).ready(function($) {
	$(document.links).filter(function() {
		if($(this).attr("target") != "_self" && $(this).not('[href*="mailto:"]') && $(this).not('[href*="javascript:void(0);"]') && $(this).attr("href") != "javascript:void(0);" ){
			alert("here we go");
			return (this.hostname.indexOf("make") == -1 || this.hostname.indexOf("mfaire") == -1);
		}
	}).attr('target', '_blank');
});
