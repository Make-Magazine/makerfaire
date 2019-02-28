/* 
 * function to copy element text to the clipboard when button is clicked
 */


function copyMe(elmnt) {  
  var n = jQuery("#"+elmnt).text();
  n = jQuery.trim(n);
  
  jQuery(".copied").attr("value", n).select();
  document.execCommand("copy");    
  alert("HTML has been copied to your clipboard.");
}