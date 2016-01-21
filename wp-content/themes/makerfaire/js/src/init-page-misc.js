// page init
jQuery(function() {
  initPopups();
  initBackgroundResize();
  initTouchNav();
  initSameHeight();
  initInputs();
  initTabs();
  initOpenClose();
  initDropDownClasses();
});

// popups init
function initPopups() {
  jQuery('.social-popup').contentPopup({
    mode: 'click',
    hideOnClickLink: false,
    hideOnClickOutside: false
  });
}
// stretch background to fill blocks
function initBackgroundResize() {
  jQuery('.bg-stretch').each(function() {
    ImageStretcher.add({
      container: this,
      image: 'img'
    });
  });
}
// handle dropdowns on mobile devices
function initTouchNav() {
  jQuery('#nav').each(function() {
    new TouchNav({
      navBlock: this
    });
  });
}
// align blocks height
function initSameHeight() {
  setSameHeight({
    holder: '.follow-wrap',
    elements: '.social-holder',
    flexible: true,
    multiLine: true
  });
}
// clear inputs on focus
function initInputs() {
  PlaceholderInput.replaceByOptions({
    // filter options
    clearInputs: true,
    clearTextareas: true,
    clearPasswords: true,
    skipClass: 'default',
    // input options
    wrapWithElement: true,
    showUntilTyping: true,
    getParentByClass: 'email-holder',
    placeholderAttr: 'placeholder'
  });
}
// content tabs init
function initTabs() {
  openCloseInstance = jQuery('div.open-close').data('OpenClose');
  jQuery('ul.tabset').contentTabs({
    addToParent: true,
    tabLinks: 'a'
  });
  jQuery('ul.tabset a').on('click', function() {
    openCloseInstance.hideSlide();
  });
}
// open-close init
function initOpenClose() {
  jQuery('div.open-close').openClose({
    activeClass: 'active',
    opener: '.opener',
    slider: '.slide',
    animSpeed: 400,
    effect: 'slide',
    hideOnClickOutside: true
  });
}
// add classes if item has dropdown
function initDropDownClasses() {
  jQuery('#nav li').each(function() {
    var item = jQuery(this);
    var drop = item.find('ul');
    var link = item.find('a').eq(0);
    if (drop.length) {
      item.addClass('has-drop-down');
      if (link.length) link.addClass('has-drop-down-a');
    }
  });
}
