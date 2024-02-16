/* 
 * function to copy element text to the clipboard when button is clicked
 */

function copyMe(element) {  
  // Copy the html inside the field
  var copyHTML = jQuery('#'+element).html();

  //TBD replace &lt; with < and &gt; with > also, get this to work in Chrome
  navigator.clipboard.writeText(copyHTML);
  
  alert('Image HTML copied to clipboard.');
  
}