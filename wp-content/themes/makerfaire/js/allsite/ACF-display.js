/* 
 * function to copy element text to the clipboard when button is clicked
 */


function copyMe(element) {  

  var $temp = jQuery("<input>");
 jQuery("body").append($temp);
 $temp.val(jQuery('#'+element).html()).select();
 document.execCommand("copy");
 alert('Image HTML copied to clipboard.');
 $temp.remove();
  
}