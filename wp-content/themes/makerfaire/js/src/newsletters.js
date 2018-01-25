jQuery(document).ready(function(){

  var recaptchaKey = '6Lffo0EUAAAAABhGRLPk751JrmCLqR5bvUR9RYZJ';
  var recaptchaFootDesk, recaptchaFootMob, recaptchaContest, recaptchaOverlay, recaptchaJoin;
  onloadCallback = function() {
    if ( jQuery('#recapcha-footer-desktop').length ) {
      recaptchaFootDesk = grecaptcha.render('recapcha-footer-desktop', {
        'sitekey' : recaptchaKey
      });
    }
    if ( jQuery('#recapcha-footer-mobile').length ) {
      recaptchaFootMob = grecaptcha.render('recapcha-footer-mobile', {
        'sitekey' : recaptchaKey
      });
    }
    if ( jQuery('#recapcha-contest').length ) {
      recaptchaContest = grecaptcha.render('recapcha-contest', {
        'sitekey' : recaptchaKey
      });
    }
    if ( jQuery('#recapcha-overlay').length ) {
      recaptchaOverlay = grecaptcha.render('recapcha-overlay', {
        'sitekey' : recaptchaKey
      });
    }
    if ( jQuery('#recapcha-join').length ) {
      recaptchaJoin = grecaptcha.render('recapcha-join', {
        'sitekey' : recaptchaKey
      });
    }
  };


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


  // Footer Desktop
  jQuery(document).on('submit', '.whatcounts-signup1', function (e) {
    e.preventDefault();
    if ( grecaptcha.getResponse(recaptchaFootDesk) != "" ) {
      var bla = jQuery('#wc-email').val();
      jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    } else {
      $('.nl-modal-error').trigger('click');
    }
  });
  // Footer Mobile
  jQuery(document).on('submit', '.whatcounts-signup1m', function (e) {
    e.preventDefault();
    if ( grecaptcha.getResponse(recaptchaFootMob) != "" ) {
      var bla = jQuery('#wc-email-m').val();
      jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1m').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    } else {
      $('.nl-modal-error').trigger('click');
    }
  });
  // Header Overlay
  jQuery(document).on('submit', '.whatcounts-signup1o', function (e) {
    e.preventDefault();
    if ( grecaptcha.getResponse(recaptchaOverlay) != "" ) {
      var bla = jQuery('#wc-email-o').val();
      jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup1o').serialize());
      jQuery('.fancybox-thx').trigger('click');
      jQuery('.nl-modal-email-address').text(bla);
      jQuery('.whatcounts-signup2 #email').val(bla);
    } else {
      $('.nl-modal-error').trigger('click');
    }
  });
  // Thank you modal
  jQuery(document).on('submit', '.whatcounts-signup2', function (e) {
    e.preventDefault();
    jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('.whatcounts-signup2').serialize());
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
        if ( grecaptcha.getResponse(recaptchaJoin) != "" ) {
          jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('#nlp-form').serialize());
          jQuery('.fancybox-thx').trigger('click');
          jQuery('.nl-modal-email-address').text(nlpEmail);
          jQuery('#whatcounts-signup2 #email').val(nlpEmail);
        } else {
          $('.nl-modal-error').trigger('click');
        }
      }
    }
  });

  // Newsletter signup contest
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
      if ( grecaptcha.getResponse(recaptchaContest) != "" ) {
        jQuery.post('https://secure.whatcounts.com/bin/listctrl', jQuery('#nlp-contest').serialize());
        jQuery('.fancybox-nl-contest').trigger('click');
        jQuery('#nlp-input').val('');
      } else {
        $('.nl-modal-error').trigger('click');
      }
    }
  });
  jQuery('#nlp-input').keypress(function (e) {
    if (e.keyCode ==13 || e.which == 13) {
      jQuery('#nlp-contest .btn-cyan').submit();
      return false;
    }
  });
});