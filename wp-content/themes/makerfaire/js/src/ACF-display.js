/* 
 * function to copy element text to the clipboard when button is clicked
 */


function copyMe(elmnt) {  
  var n = jQuery("#"+elmnt).text();
  n = jQuery.trim(n);
  //alert("copying "+ elmnt+" "+n);
  jQuery(".copied").attr("value", n).select();
  document.execCommand("copy");    
}