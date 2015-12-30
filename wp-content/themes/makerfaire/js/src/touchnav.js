// navigation accesibility module
function TouchNav(opt) {
  this.options = {
    hoverClass: 'hover',
    menuItems: 'li',
    menuOpener: 'a',
    menuDrop: 'ul',
    navBlock: null
  };
  for (var p in opt) {
    if (opt.hasOwnProperty(p)) {
      this.options[p] = opt[p];
    }
  }
  this.init();
}
TouchNav.isActiveOn = function(elem) {
  return elem && elem.touchNavActive;
};
TouchNav.prototype = {
  init: function() {
    if (typeof this.options.navBlock === 'string') {
      this.menu = document.getElementById(this.options.navBlock);
    } else if (typeof this.options.navBlock === 'object') {
      this.menu = this.options.navBlock;
    }
    if (this.menu) {
      this.addEvents();
    }
  },
  addEvents: function() {
    // attach event handlers
    var self = this;
    var touchEvent = (navigator.pointerEnabled && 'pointerdown') || (navigator.msPointerEnabled && 'MSPointerDown') || (this.isTouchDevice && 'touchstart');
    this.menuItems = lib.queryElementsBySelector(this.options.menuItems, this.menu);

    var initMenuItem = function(item) {
      var currentDrop = lib.queryElementsBySelector(self.options.menuDrop, item)[0],
        currentOpener = lib.queryElementsBySelector(self.options.menuOpener, item)[0];

      // only for touch input devices
      if (currentDrop && currentOpener && (self.isTouchDevice || self.isPointerDevice)) {
        lib.event.add(currentOpener, 'click', lib.bind(self.clickHandler, self));
        lib.event.add(currentOpener, 'mousedown', lib.bind(self.mousedownHandler, self));
        lib.event.add(currentOpener, touchEvent, function(e) {
          if (!self.isTouchPointerEvent(e)) {
            self.preventCurrentClick = false;
            return;
          }
          self.touchFlag = true;
          self.currentItem = item;
          self.currentLink = currentOpener;
          self.pressHandler.apply(self, arguments);
        });
      }
      // for desktop computers and touch devices
      jQuery(item).bind('mouseenter', function() {
        if (!self.touchFlag) {
          self.currentItem = item;
          self.mouseoverHandler();
        }
      });
      jQuery(item).bind('mouseleave', function() {
        if (!self.touchFlag) {
          self.currentItem = item;
          self.mouseoutHandler();
        }
      });
      item.touchNavActive = true;
    };

    // addd handlers for all menu items
    for (var i = 0; i < this.menuItems.length; i++) {
      initMenuItem(self.menuItems[i]);
    }

    // hide dropdowns when clicking outside navigation
    if (this.isTouchDevice || this.isPointerDevice) {
      lib.event.add(document.documentElement, 'mousedown', lib.bind(this.clickOutsideHandler, this));
      lib.event.add(document.documentElement, touchEvent, lib.bind(this.clickOutsideHandler, this));
    }
  },
  mousedownHandler: function(e) {
    if (this.touchFlag) {
      e.preventDefault();
      this.touchFlag = false;
      this.preventCurrentClick = false;
    }
  },
  mouseoverHandler: function() {
    lib.addClass(this.currentItem, this.options.hoverClass);
    jQuery(this.currentItem).trigger('itemhover');
  },
  mouseoutHandler: function() {
    lib.removeClass(this.currentItem, this.options.hoverClass);
    jQuery(this.currentItem).trigger('itemleave');
  },
  hideActiveDropdown: function() {
    for (var i = 0; i < this.menuItems.length; i++) {
      if (lib.hasClass(this.menuItems[i], this.options.hoverClass)) {
        lib.removeClass(this.menuItems[i], this.options.hoverClass);
        jQuery(this.menuItems[i]).trigger('itemleave');
      }
    }
    this.activeParent = null;
  },
  pressHandler: function(e) {
    // hide previous drop (if active)
    if (this.currentItem !== this.activeParent) {
      if (this.activeParent && this.currentItem.parentNode === this.activeParent.parentNode) {
        lib.removeClass(this.activeParent, this.options.hoverClass);
      } else if (!this.isParent(this.activeParent, this.currentLink)) {
        this.hideActiveDropdown();
      }
    }
    // handle current drop
    this.activeParent = this.currentItem;
    if (lib.hasClass(this.currentItem, this.options.hoverClass)) {
      this.preventCurrentClick = false;
    } else {
      e.preventDefault();
      this.preventCurrentClick = true;
      lib.addClass(this.currentItem, this.options.hoverClass);
      jQuery(this.currentItem).trigger('itemhover');
    }
  },
  clickHandler: function(e) {
    // prevent first click on link
    if (this.preventCurrentClick) {
      e.preventDefault();
    }
  },
  clickOutsideHandler: function(event) {
    var e = event.changedTouches ? event.changedTouches[0] : event;
    if (this.activeParent && !this.isParent(this.menu, e.target)) {
      this.hideActiveDropdown();
      this.touchFlag = false;
    }
  },
  isParent: function(parent, child) {
    while (child.parentNode) {
      if (child.parentNode == parent) {
        return true;
      }
      child = child.parentNode;
    }
    return false;
  },
  isTouchPointerEvent: function(e) {
    return (e.type.indexOf('touch') > -1) ||
      (navigator.pointerEnabled && e.pointerType === 'touch') ||
      (navigator.msPointerEnabled && e.pointerType == e.MSPOINTER_TYPE_TOUCH);
  },
  isPointerDevice: (function() {
    return !!(navigator.pointerEnabled || navigator.msPointerEnabled);
  }()),
  isTouchDevice: (function() {
    return !!(('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch);
  }())
};
