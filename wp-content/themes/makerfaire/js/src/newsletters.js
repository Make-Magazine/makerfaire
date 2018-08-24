jQuery(document).ready(function(){
  // Thank you modal with more nl signups
  jQuery(".fancybox-thx").fancybox({
    autoSize : false,
    width  : 400,
    autoHeight : true,
    padding : 0,
    afterLoad   : function() {
      this.content = this.content.html();
    }
  });
  // Final thank you modal
  jQuery(".nl-thx-p2").fancybox({
    autoSize : false,
    width  : 400,
    autoHeight : true,
    padding : 0,
    afterLoad   : function() {
      this.content = this.content.html();
    }
  });
  // Newsletter signup contest
  jQuery(".fancybox-nl-contest").fancybox({
    autoSize : false,
    width  : 400,
    autoHeight : true,
    padding : 0,
    afterLoad   : function() {
      this.content = this.content.html();
    }
  });
  // reCAPRCHA error message
  jQuery(".nl-modal-error").fancybox({
    autoSize : false,
    width  : 250,
    autoHeight : true,
    padding : 0,
    afterLoad   : function() {
      this.content = this.content.html();
    }
  });
});



// Footer Mobile
var onSubmitFooterMob = function(token) {
  var bla = jQuery('#wc-email-m').val();
  jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1m').serialize());
  jQuery('.fancybox-thx').trigger('click');
  jQuery('.nl-modal-email-address').text(bla);
  jQuery('.whatcounts-signup2 #email').val(bla);
}
jQuery(document).on('submit', '.whatcounts-signup1m', function (e) {
  e.preventDefault();
  onSubmitFooterMob();
});
// Header Overlay
var onSubmitOverlay = function(token) {
  var bla = jQuery('#wc-email-o').val();
  jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1o').serialize());
  jQuery('.fancybox-thx').trigger('click');
  jQuery('.nl-modal-email-address').text(bla);
  jQuery('.whatcounts-signup2 #email').val(bla);
}
jQuery(document).on('submit', '.whatcounts-signup1o', function (e) {
  e.preventDefault();
  onSubmitOverlay();
});
// Thank you modal
jQuery(document).on('submit', '.whatcounts-signup2', function (e) {
  jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup2').serialize());
  e.preventDefault();
  jQuery('.nl-thx-p2').trigger('click');
  jQuery('.fancybox-thx').hide();
  jQuery('.nl-thx-p2').trigger('click');
});

// Newsletter signup page
jQuery(document).on('submit', '#nlp-form', function (e) {
  e.preventDefault();
  // First check if any checkboxes are checked
  var anyBoxesChecked = false;
  jQuery('#nlp-form input[type="checkbox"]').each(function() {
    if (jQuery(this).is(":checked")) {
      anyBoxesChecked = true;
    }
  });
  if (anyBoxesChecked == false) {
    jQuery('.pull-right[data-toggle="tooltip"]').tooltip();
    jQuery('.pull-right[data-toggle="tooltip"]').tooltip('show');
    return false;
  }
  // Now get the email into the form and send
  else {
    var nlpEmail = jQuery('#nlp-input').val();
    jQuery('#nlp-form #email').val(nlpEmail);
    if (jQuery('#nlp-form #email').val() == '') {
      jQuery('#nlp-input').tooltip();
      jQuery('#nlp-input').tooltip('show');
      return false;
    }
    else {
      onSubmitJoin();
    }
  }
});


var onSubmitJoin = function(token) {
  jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('#nlp-form').serialize());
  jQuery('.fancybox-thx').trigger('click');
  jQuery('.nl-modal-email-address').text(nlpEmail);
  jQuery('#whatcounts-signup2 #email').val(nlpEmail);
}

// Newsletter signup contest
var onSubmitContest = function(token) {
  jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('#nlp-contest').serialize());
  jQuery('.fancybox-nl-contest').trigger('click');
  jQuery('#nlp-input').val('');
}
jQuery(document).on('submit', '#nlp-contest', function (e) {
  e.preventDefault();
  // Now get the email into the form and send
  var nlpEmail = jQuery('#nlp-input').val();
  jQuery('#nlp-contest #email').val(nlpEmail);
  if (jQuery('#nlp-contest #email').val() == '') {
    jQuery('#nlp-input').tooltip()
    jQuery('#nlp-input').tooltip('show')
    return false;
  }
  else {
    onSubmitContest();
  }
});
jQuery('#nlp-input').keypress(function (e) {
  if (e.keyCode ==13 || e.which == 13) {
    jQuery('#nlp-contest .btn-cyan').submit();
    return false;
  }
});
  
var recaptchaKey = '6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP';
onloadCallback = function() {
  if ( jQuery('#recapcha-footer-desktop').length ) {
    grecaptcha.render('recapcha-footer-desktop', {
      'sitekey' : recaptchaKey,
      'callback' : onSubmitFooterDesk
    });
  }
  if ( jQuery('#recapcha-footer-mobile').length ) {
    grecaptcha.render('recapcha-footer-mobile', {
      'sitekey' : recaptchaKey,
      'callback' : onSubmitFooterMob
    });
  }
  if ( jQuery('#recapcha-contest').length ) {
    grecaptcha.render('recapcha-contest', {
      'sitekey' : recaptchaKey,
      'callback' : onSubmitContest
    });
  }
  if ( jQuery('#recapcha-overlay').length ) {
    grecaptcha.render('recapcha-overlay', {
      'sitekey' : recaptchaKey,
      'callback' : onSubmitOverlay
    });
  }
  if ( jQuery('#recapcha-join').length ) {
    grecaptcha.render('recapcha-join', {
      'sitekey' : recaptchaKey,
      'callback' : onSubmitJoin
    });
  }
};