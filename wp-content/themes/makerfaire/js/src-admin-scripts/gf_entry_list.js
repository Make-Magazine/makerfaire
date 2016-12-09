/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery( document ).ready(function() {
  //change search button text
  jQuery('#entry_search_button').text('Add Filter');
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

  //add multi filters to end of edit and view links
  jQuery('.column-primary a').each(function() {
    var filterParam = getAllUrlParams().filterField;

    var oldFilters = '';
    // if there are, be sure to include them
    if(filterParam == undefined){
      //keep going, nothing to add here
    }else if(Array.isArray(filterParam)){
      //add the current filterField parameters to the new URL
      for (i = 0, len = filterParam.length, oldFilters = ""; i < len; i++) {
        oldFilters += '&filterField[]='+filterParam[i];
      }
    }else{
      oldFilters = '&filterField[]='+filterParam;
    }

    var href = jQuery(this).attr('href');
    jQuery(this).attr('href', href + oldFilters);
  });

  //overwrite the 'Search' gravity form JSfunction
  Search = function(sort_field_id, sort_direction, form_id, search, filter, field_id, operator) {
		var search_qs = search == "" ? "" : "&filterField[]=" + encodeURIComponent(field_id+'|'+operator+'|'+search);
		var filter_qs = filter == "" ? "" : "&filter=" + filter;

    //first, check if there are other filters set in the url
    var filterParam = getAllUrlParams().filterField;
    var oldFilters = '';

    // if there are, be sure to include them
    if(filterParam == undefined){
      //keep going, nothing to add here
    }else if(Array.isArray(filterParam)){
      //add the current filterField parameters to the new URL
      for (i = 0, len = filterParam.length, oldFilters = ""; i < len; i++) {
        oldFilters += '&filterField[]='+filterParam[i];
      }
    }else{
      oldFilters = '&filterField[]='+filterParam;
    }

    var location = "?page=gf_entries&view=entries&id=" + form_id + "&orderby=" + sort_field_id + "&order=" + sort_direction + oldFilters + search_qs +filter_qs;

    document.location = location;
  }
  jQuery('#entry_list_form').show();
  jQuery('#entry_search_container').show();
});

//find all the instances of a parameter in the url
function getAllUrlParams(url) {

  // get query string from url (optional) or window
  var queryString = url ? url.split('?')[1] : window.location.search.slice(1);
  queryString = decodeURIComponent(queryString);

  // we'll store the parameters here
  var obj = {};

  // if query string exists
  if (queryString) {

    // stuff after # is not part of query string, so get rid of it
    queryString = queryString.split('#')[0];

    // split our query string into its component parts
    var arr = queryString.split('&');

    for (var i=0; i<arr.length; i++) {
      // separate the keys and the values
      var a = arr[i].split('=');

      // in case params look like: list[]=thing1&list[]=thing2
      var paramNum = undefined;
      var paramName = a[0].replace(/\[\d*\]/, function(v) {
        paramNum = v.slice(1,-1);
        return '';
      });

      // set parameter value (use 'true' if empty)
      var paramValue = typeof(a[1])==='undefined' ? true : a[1];

      // (optional) keep case consistent
      //paramName = paramName.toLowerCase();
      //paramValue = paramValue.toLowerCase();

      // if parameter name already exists
      if (obj[paramName]) {
        // convert value to array (if still string)
        if (typeof obj[paramName] === 'string') {
          obj[paramName] = [obj[paramName]];
        }
        // if no array index number specified...
        if (typeof paramNum === 'undefined') {
          // put the value on the end of the array
          obj[paramName].push(paramValue);
        }
        // if array index number specified...
        else {
          // put the value at that index number
          obj[paramName][paramNum] = paramValue;
        }
      }
      // if param name doesn't exist yet, set it
      else {
        obj[paramName] = paramValue;
      }
    }
  }

  return obj;
}