/* 
 * function to copy element text to the clipboard when button is clicked
 */

function copyMe(element) {  
  // Copy the html inside the field
  var copyHTML = unescapeHTML(jQuery('#'+element).html());
  navigator.clipboard.writeText(copyHTML).then(function(x) {
    alert('Image HTML copied to clipboard.');
  });
}

function unescapeHTML(escapedHTML) {
  return escapedHTML.replace(/&lt;/g,'<').replace(/&gt;/g,'>');
}