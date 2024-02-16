/* 
 * function to copy element text to the clipboard when button is clicked
 */

function copyMe(element) {  
  // Copy the html inside the field
  var copyHTML = decodeHtml(jQuery('#'+element).html());
  navigator.clipboard.write(copyHTML);
  alert('Image HTML copied to clipboard.');
}

function decodeHtml(html) {
  var txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}