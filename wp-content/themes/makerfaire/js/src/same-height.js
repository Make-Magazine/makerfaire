// set same height for blocks
function setSameHeight(opt) {
  // default options
  var options = {
    holder: null,
    skipClass: 'same-height-ignore',
    leftEdgeClass: 'same-height-left',
    rightEdgeClass: 'same-height-right',
    elements: '>*',
    flexible: false,
    multiLine: false,
    useMinHeight: false,
    biggestHeight: false
  };
  for (var p in opt) {
    if (opt.hasOwnProperty(p)) {
      options[p] = opt[p];
    }
  }
  // init script
  if (options.holder) {
    var holders = lib.queryElementsBySelector(options.holder);
    lib.each(holders, function(ind, curHolder) {
      var curElements = [],
        resizeTimer, postResizeTimer;
      var tmpElements = lib.queryElementsBySelector(options.elements, curHolder);

      // get resize elements
      for (var i = 0; i < tmpElements.length; i++) {
        if (!lib.hasClass(tmpElements[i], options.skipClass)) {
          curElements.push(tmpElements[i]);
        }
      }
      if (!curElements.length) return;

      // resize handler
      function doResize() {
        for (var i = 0; i < curElements.length; i++) {
          curElements[i].style[options.useMinHeight && SameHeight.supportMinHeight ? 'minHeight' : 'height'] = '';
        }

        if (options.multiLine) {
          // resize elements row by row
          SameHeight.resizeElementsByRows(curElements, options);
        } else {
          // resize elements by holder
          SameHeight.setSize(curElements, curHolder, options);
        }
      }
      doResize();

      // handle flexible layout / font resize
      function flexibleResizeHandler() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
          doResize();
          clearTimeout(postResizeTimer);
          postResizeTimer = setTimeout(doResize, 100);
        }, 1);
      }
      if (options.flexible) {
        addEvent(window, 'resize', flexibleResizeHandler);
        addEvent(window, 'orientationchange', flexibleResizeHandler);
        FontResizeEvent.onChange(flexibleResizeHandler);
      }
      // handle complete page load including images and fonts
      addEvent(window, 'load', flexibleResizeHandler);
    });
  }

  // event handler helper functions
  function addEvent(object, event, handler) {
    if (object.addEventListener) object.addEventListener(event, handler, false);
    else if (object.attachEvent) object.attachEvent('on' + event, handler);
  }
}

/*
 * SameHeight helper module
 */
SameHeight = {
  supportMinHeight: typeof document.documentElement.style.maxHeight !== 'undefined', // detect css min-height support
  setSize: function(boxes, parent, options) {
    var calcHeight, holderHeight = typeof parent === 'number' ? parent : this.getHeight(parent);

    for (var i = 0; i < boxes.length; i++) {
      var box = boxes[i];
      var depthDiffHeight = 0;
      var isBorderBox = this.isBorderBox(box);
      lib.removeClass(box, options.leftEdgeClass);
      lib.removeClass(box, options.rightEdgeClass);

      if (typeof parent != 'number') {
        var tmpParent = box.parentNode;
        while (tmpParent != parent) {
          depthDiffHeight += this.getOuterHeight(tmpParent) - this.getHeight(tmpParent);
          tmpParent = tmpParent.parentNode;
        }
      }
      calcHeight = holderHeight - depthDiffHeight;
      calcHeight -= isBorderBox ? 0 : this.getOuterHeight(box) - this.getHeight(box);
      if (calcHeight > 0) {
        box.style[options.useMinHeight && this.supportMinHeight ? 'minHeight' : 'height'] = calcHeight + 'px';
      }
    }

    lib.addClass(boxes[0], options.leftEdgeClass);
    lib.addClass(boxes[boxes.length - 1], options.rightEdgeClass);
    return calcHeight;
  },
  getOffset: function(obj) {
    if (obj.getBoundingClientRect) {
      var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft;
      var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
      var clientLeft = document.documentElement.clientLeft || document.body.clientLeft || 0;
      var clientTop = document.documentElement.clientTop || document.body.clientTop || 0;
      return {
        top: Math.round(obj.getBoundingClientRect().top + scrollTop - clientTop),
        left: Math.round(obj.getBoundingClientRect().left + scrollLeft - clientLeft)
      };
    } else {
      var posLeft = 0,
        posTop = 0;
      while (obj.offsetParent) {
        posLeft += obj.offsetLeft;
        posTop += obj.offsetTop;
        obj = obj.offsetParent;
      }
      return {
        top: posTop,
        left: posLeft
      };
    }
  },
  getStyle: function(el, prop) {
    if (document.defaultView && document.defaultView.getComputedStyle) {
      return document.defaultView.getComputedStyle(el, null)[prop];
    } else if (el.currentStyle) {
      return el.currentStyle[prop];
    } else {
      return el.style[prop];
    }
  },
  getStylesTotal: function(obj) {
    var sum = 0;
    for (var i = 1; i < arguments.length; i++) {
      var val = parseFloat(this.getStyle(obj, arguments[i]));
      if (!isNaN(val)) {
        sum += val;
      }
    }
    return sum;
  },
  getHeight: function(obj) {
    return obj.offsetHeight - this.getStylesTotal(obj, 'borderTopWidth', 'borderBottomWidth', 'paddingTop', 'paddingBottom');
  },
  getOuterHeight: function(obj) {
    return obj.offsetHeight;
  },
  isBorderBox: function(obj) {
    var f = this.getStyle,
      styleValue = f(obj, 'boxSizing') || f(obj, 'WebkitBoxSizing') || f(obj, 'MozBoxSizing');
    return styleValue === 'border-box';
  },
  resizeElementsByRows: function(boxes, options) {
    var currentRow = [],
      maxHeight, maxCalcHeight = 0,
      firstOffset = this.getOffset(boxes[0]).top;
    for (var i = 0; i < boxes.length; i++) {
      if (this.getOffset(boxes[i]).top === firstOffset) {
        currentRow.push(boxes[i]);
      } else {
        maxHeight = this.getMaxHeight(currentRow);
        maxCalcHeight = Math.max(maxCalcHeight, this.setSize(currentRow, maxHeight, options));
        firstOffset = this.getOffset(boxes[i]).top;
        currentRow = [boxes[i]];
      }
    }
    if (currentRow.length) {
      maxHeight = this.getMaxHeight(currentRow);
      maxCalcHeight = Math.max(maxCalcHeight, this.setSize(currentRow, maxHeight, options));
    }
    if (options.biggestHeight) {
      for (i = 0; i < boxes.length; i++) {
        boxes[i].style[options.useMinHeight && this.supportMinHeight ? 'minHeight' : 'height'] = maxCalcHeight + 'px';
      }
    }
  },
  getMaxHeight: function(boxes) {
    var maxHeight = 0;
    for (var i = 0; i < boxes.length; i++) {
      maxHeight = Math.max(maxHeight, this.getOuterHeight(boxes[i]));
    }
    return maxHeight;
  }
};
