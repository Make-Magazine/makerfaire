  function printSigns(type,faire){
    jQuery('#processButton').val("Creating PDF's. . . ");
    if(type=='signs'){
      pdfLink = 'makersigns';
    }else if(type=='presenter'){
      pdfLink = 'presenterSigns';
    }else{
      pdfLink = 'tabletag';
    }
    var folder = type;
    if(type=='signs') folder = 'maker';
    jQuery("a.fairsign").each(function(){
      jQuery(this).html('Creating');
      jQuery(this).attr("disabled","disabled");

      jQuery.ajax({
        type: "GET",
        url: "/wp-content/themes/makerfaire/generate_pdf/"+pdfLink+".php",
        data: { eid: jQuery(this).attr('id'), type: 'save', faire: faire },
      }).done(function(data) {
        jQuery('#'+data).html(data);
        jQuery('#'+data).attr("href", "/wp-content/themes/makerfaire/signs/"+faire+"/"+folder+"/"+data+'.pdf');
      });
    });
  }

  function fireEvent(obj,evt){
    var fireOnThis = obj;
    if( document.createEvent ) {
      var evObj = document.createEvent('MouseEvents');
      evObj.initEvent( evt, true, false );
      fireOnThis.dispatchEvent(evObj);
    } else if( document.createEventObject ) {
      fireOnThis.fireEvent('on'+evt);
    }
  }

  function createZip(faire,type) {
    var data = {
      'action': 'createSignZip',
      'faire': faire,
      'type': type,
      'seltype': jQuery('input[name='+faire+'seltype]:checked').val(),
      'selstatus': jQuery('input[name='+faire+'selstatus]:checked').val(),
      'error': jQuery('input[name='+faire+'filtererror]:checked').val(),
      'filform' :  jQuery('select[name='+faire+'filterform]').val()
    };
    jQuery.post(ajaxurl, data, function(response) {
      if(response.msg != ''){
        // alert(response.msg);
      }
    });
    jQuery('#collapse'+faire+' .'+type+'.updateMsg').html('A batch process has been triggered to update the zip file.  Please check back in a few minutes.');
  }

  function createPDF(faire, type) {
    jQuery('#collapse'+faire+' .'+type+'.pdfEntList').html('Process submitted, this could take a while to run depending on the number of signs to create. Please check back for an update.');
    var data = {
      'action': 'createEntList',
      'faire': faire,
      'type': type
    };
    jQuery.post(ajaxurl, data, function(response) {
      //if(response.entList!=''){
        //jQuery('#collapse'+faire+' .'+type+'.pdfEntList').html(response.entList);
        //printSigns(type, faire);
      //}
    });
  }

