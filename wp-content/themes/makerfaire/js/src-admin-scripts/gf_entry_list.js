/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery( document ).ready(function() {
  //change search button text
  jQuery('#entry_search_button').text('Add Filter');

  //add any filters after the filter box
  jQuery('#entry_search_container').append('<span class="gf_admin_page_formname">Status: Accepted <a style="color:red" href="javascript:document.location = \'?page=gf_entries&amp;view=entries&amp;id=46&amp;dir=DESC&amp;filterField[]=45|is|Independent Maker or Group\';">X</a></span>');
  jQuery('#entry_search_container').append('<span class="gf_admin_page_formname">Are you a:: Independent Maker or Group <a style="color:red" href="javascript:document.location = \'?page=gf_entries&amp;view=entries&amp;id=46&amp;dir=DESC&amp;filterField[]=303|is|Accepted\';">X</a></span>');
});

jQuery(window).load(function() {
  var topFields = ['entry_id','302','304','303'];
  var newOptions = '';
  jQuery("#entry_filters .gform-filter-field > option").each(function() {
    if(jQuery.inArray(this.value, topFields)!=-1){
      newOptions += '<option value="'+this.value+'">'+this.text+'</option>';
      jQuery(this).remove();
    }
  });
  jQuery("#entry_filters .gform-filter-field").prepend(newOptions);
});