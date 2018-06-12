// Open external links in new tab, unless it's a make site, or targeted self, or an email link
jQuery(document).ready(function($) {
 $(document.links).filter(function() {
   if($(this).attr("target") != "_self" && $(this).not('[href*="mailto:"]') && $(this).not('[href*="javascript:void(0);"]')){
       return this.hostname.indexOf("make") == -1;
   }
 }).attr('target', '_blank');
});