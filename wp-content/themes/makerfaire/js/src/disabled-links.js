jQuery(document).ready(function() {
  /* Allows users to add a class of 'disabled' to the navigation menu to disable a navigation link*/
  jQuery("li.disabled a").each(function() {
    jQuery(this).removeAttr("href");
  });

  /**
   * Cancel the default action of all hyperlinks that have # as the address,
   * and the ones with class="blankLink" or rel="blankLink"
   */
  jQuery('a.blankLink').click(function(e) {
    e.preventDefault();
  });
  jQuery('a[rel="blankLink"]').click(function(e) {
    e.preventDefault();
  });
});
