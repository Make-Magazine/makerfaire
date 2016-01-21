/*
 * jQuery Carousel plugin
 */
(function($) {
  function ScrollGallery(options) {
    this.options = $.extend({
      mask: 'div.mask',
      slider: '>*',
      slides: '>*',
      activeClass: 'active',
      disabledClass: 'disabled',
      btnPrev: 'a.btn-prev',
      btnNext: 'a.btn-next',
      generatePagination: false,
      pagerList: '<ul>',
      pagerListItem: '<li><a href="#"></a></li>',
      pagerListItemText: 'a',
      pagerLinks: '.pagination li',
      currentNumber: 'span.current-num',
      totalNumber: 'span.total-num',
      btnPlay: '.btn-play',
      btnPause: '.btn-pause',
      btnPlayPause: '.btn-play-pause',
      galleryReadyClass: 'gallery-js-ready',
      autorotationActiveClass: 'autorotation-active',
      autorotationDisabledClass: 'autorotation-disabled',
      stretchSlideToMask: false,
      circularRotation: true,
      disableWhileAnimating: false,
      autoRotation: false,
      pauseOnHover: isTouchDevice ? false : true,
      maskAutoSize: false,
      switchTime: 4000,
      animSpeed: 600,
      event: 'click',
      swipeThreshold: 15,
      handleTouch: true,
      vertical: false,
      useTranslate3D: false,
      step: false
    }, options);
    this.init();
  }
  ScrollGallery.prototype = {
    init: function() {
      if (this.options.holder) {
        this.findElements();
        this.attachEvents();
        this.refreshPosition();
        this.refreshState(true);
        this.resumeRotation();
        this.makeCallback('onInit', this);
      }
    },
    findElements: function() {
      // define dimensions proporties
      this.fullSizeFunction = this.options.vertical ? 'outerHeight' : 'outerWidth';
      this.innerSizeFunction = this.options.vertical ? 'height' : 'width';
      this.slideSizeFunction = 'outerHeight';
      this.maskSizeProperty = 'height';
      this.animProperty = this.options.vertical ? 'marginTop' : 'marginLeft';

      // control elements
      this.gallery = $(this.options.holder).addClass(this.options.galleryReadyClass);
      this.mask = this.gallery.find(this.options.mask);
      this.slider = this.mask.find(this.options.slider);
      this.slides = this.slider.find(this.options.slides);
      this.btnPrev = this.gallery.find(this.options.btnPrev);
      this.btnNext = this.gallery.find(this.options.btnNext);
      this.currentStep = 0;
      this.stepsCount = 0;

      // get start index
      if (this.options.step === false) {
        var activeSlide = this.slides.filter('.' + this.options.activeClass);
        if (activeSlide.length) {
          this.currentStep = this.slides.index(activeSlide);
        }
      }

      // calculate offsets
      this.calculateOffsets();

      // create gallery pagination
      if (typeof this.options.generatePagination === 'string') {
        this.pagerLinks = $();
        this.buildPagination();
      } else {
        this.pagerLinks = this.gallery.find(this.options.pagerLinks);
        this.attachPaginationEvents();
      }

      // autorotation control buttons
      this.btnPlay = this.gallery.find(this.options.btnPlay);
      this.btnPause = this.gallery.find(this.options.btnPause);
      this.btnPlayPause = this.gallery.find(this.options.btnPlayPause);

      // misc elements
      this.curNum = this.gallery.find(this.options.currentNumber);
      this.allNum = this.gallery.find(this.options.totalNumber);
    },
    attachEvents: function() {
      // bind handlers scope
      var self = this;
      this.bindHandlers(['onWindowResize']);
      $(window).bind('load resize orientationchange', this.onWindowResize);

      // previous and next button handlers
      if (this.btnPrev.length) {
        this.prevSlideHandler = function(e) {
          e.preventDefault();
          self.prevSlide();
        };
        this.btnPrev.bind(this.options.event, this.prevSlideHandler);
      }
      if (this.btnNext.length) {
        this.nextSlideHandler = function(e) {
          e.preventDefault();
          self.nextSlide();
        };
        this.btnNext.bind(this.options.event, this.nextSlideHandler);
      }

      // pause on hover handling
      if (this.options.pauseOnHover && !isTouchDevice) {
        this.hoverHandler = function() {
          if (self.options.autoRotation) {
            self.galleryHover = true;
            self.pauseRotation();
          }
        };
        this.leaveHandler = function() {
          if (self.options.autoRotation) {
            self.galleryHover = false;
            self.resumeRotation();
          }
        };
        this.gallery.bind({
          mouseenter: this.hoverHandler,
          mouseleave: this.leaveHandler
        });
      }

      // autorotation buttons handler
      if (this.btnPlay.length) {
        this.btnPlayHandler = function(e) {
          e.preventDefault();
          self.startRotation();
        };
        this.btnPlay.bind(this.options.event, this.btnPlayHandler);
      }
      if (this.btnPause.length) {
        this.btnPauseHandler = function(e) {
          e.preventDefault();
          self.stopRotation();
        };
        this.btnPause.bind(this.options.event, this.btnPauseHandler);
      }
      if (this.btnPlayPause.length) {
        this.btnPlayPauseHandler = function(e) {
          e.preventDefault();
          if (!self.gallery.hasClass(self.options.autorotationActiveClass)) {
            self.startRotation();
          } else {
            self.stopRotation();
          }
        };
        this.btnPlayPause.bind(this.options.event, this.btnPlayPauseHandler);
      }

      // enable hardware acceleration
      if (isTouchDevice && this.options.useTranslate3D) {
        this.slider.css({
          '-webkit-transform': 'translate3d(0px, 0px, 0px)'
        });
      }

      // swipe event handling
      if (isTouchDevice && this.options.handleTouch && window.Hammer && this.mask.length) {
        this.swipeHandler = new Hammer.Manager(this.mask[0]);
        this.swipeHandler.add(new Hammer.Pan({
          direction: self.options.vertical ? Hammer.DIRECTION_VERTICAL : Hammer.DIRECTION_HORIZONTAL,
          threshold: self.options.swipeThreshold
        }));

        this.swipeHandler.on('panstart', function() {
          if (self.galleryAnimating) {
            self.swipeHandler.stop();
          } else {
            self.pauseRotation();
            self.originalOffset = parseFloat(self.slider.css(self.animProperty));
          }
        }).on('panmove', function(e) {
          var tmpOffset = self.originalOffset + e[self.options.vertical ? 'deltaY' : 'deltaX'];
          tmpOffset = Math.max(Math.min(0, tmpOffset), self.maxOffset);
          self.slider.css(self.animProperty, tmpOffset);
        }).on('panend', function(e) {
          self.resumeRotation();
          if (e.distance > self.options.swipeThreshold) {
            if (e.offsetDirection === Hammer.DIRECTION_RIGHT || e.offsetDirection === Hammer.DIRECTION_DOWN) {
              self.nextSlide();
            } else {
              self.prevSlide();
            }
          } else {
            self.switchSlide();
          }
        });
      }
    },
    onWindowResize: function() {
      if (!this.galleryAnimating) {
        this.calculateOffsets();
        this.refreshPosition();
        this.buildPagination();
        this.refreshState();
        this.resizeQueue = false;
      } else {
        this.resizeQueue = true;
      }
    },
    refreshPosition: function() {
      this.currentStep = Math.min(this.currentStep, this.stepsCount - 1);
      this.tmpProps = {};
      this.tmpProps[this.animProperty] = this.getStepOffset();
      this.slider.stop().css(this.tmpProps);
    },
    calculateOffsets: function() {
      var self = this,
        tmpOffset, tmpStep;
      if (this.options.stretchSlideToMask) {
        var tmpObj = {};
        tmpObj[this.innerSizeFunction] = this.mask[this.innerSizeFunction]();
        this.slides.css(tmpObj);
      }

      this.maskSize = this.mask[this.innerSizeFunction]();
      this.sumSize = this.getSumSize();
      this.maxOffset = this.maskSize - this.sumSize;

      // vertical gallery with single size step custom behavior
      if (this.options.vertical && this.options.maskAutoSize) {
        this.options.step = 1;
        this.stepsCount = this.slides.length;
        this.stepOffsets = [0];
        tmpOffset = 0;
        for (var i = 0; i < this.slides.length; i++) {
          tmpOffset -= $(this.slides[i])[this.fullSizeFunction](true);
          this.stepOffsets.push(tmpOffset);
        }
        this.maxOffset = tmpOffset;
        return;
      }

      // scroll by slide size
      if (typeof this.options.step === 'number' && this.options.step > 0) {
        this.slideDimensions = [];
        this.slides.each($.proxy(function(ind, obj) {
          self.slideDimensions.push($(obj)[self.fullSizeFunction](true));
        }, this));

        // calculate steps count
        this.stepOffsets = [0];
        this.stepsCount = 1;
        tmpOffset = tmpStep = 0;
        while (tmpOffset > this.maxOffset) {
          tmpOffset -= this.getSlideSize(tmpStep, tmpStep + this.options.step);
          tmpStep += this.options.step;
          this.stepOffsets.push(Math.max(tmpOffset, this.maxOffset));
          this.stepsCount++;
        }
      }
      // scroll by mask size
      else {
        // define step size
        this.stepSize = this.maskSize;

        // calculate steps count
        this.stepsCount = 1;
        tmpOffset = 0;
        while (tmpOffset > this.maxOffset) {
          tmpOffset -= this.stepSize;
          this.stepsCount++;
        }
      }
    },
    getSumSize: function() {
      var sum = 0;
      this.slides.each($.proxy(function(ind, obj) {
        sum += $(obj)[this.fullSizeFunction](true);
      }, this));
      this.slider.css(this.innerSizeFunction, sum);
      return sum;
    },
    getStepOffset: function(step) {
      step = step || this.currentStep;
      if (typeof this.options.step === 'number') {
        return this.stepOffsets[this.currentStep];
      } else {
        return Math.min(0, Math.max(-this.currentStep * this.stepSize, this.maxOffset));
      }
    },
    getSlideSize: function(i1, i2) {
      var sum = 0;
      for (var i = i1; i < Math.min(i2, this.slideDimensions.length); i++) {
        sum += this.slideDimensions[i];
      }
      return sum;
    },
    buildPagination: function() {
      if (typeof this.options.generatePagination === 'string') {
        if (!this.pagerHolder) {
          this.pagerHolder = this.gallery.find(this.options.generatePagination);
        }
        if (this.pagerHolder.length && this.oldStepsCount != this.stepsCount) {
          this.oldStepsCount = this.stepsCount;
          this.pagerHolder.empty();
          this.pagerList = $(this.options.pagerList).appendTo(this.pagerHolder);
          for (var i = 0; i < this.stepsCount; i++) {
            $(this.options.pagerListItem).appendTo(this.pagerList).find(this.options.pagerListItemText).text(i + 1);
          }
          this.pagerLinks = this.pagerList.children();
          this.attachPaginationEvents();
        }
      }
    },
    attachPaginationEvents: function() {
      var self = this;
      this.pagerLinksHandler = function(e) {
        e.preventDefault();
        self.numSlide(self.pagerLinks.index(e.currentTarget));
      };
      this.pagerLinks.bind(this.options.event, this.pagerLinksHandler);
    },
    prevSlide: function() {
      if (!(this.options.disableWhileAnimating && this.galleryAnimating)) {
        if (this.currentStep > 0) {
          this.currentStep--;
          this.switchSlide();
        } else if (this.options.circularRotation) {
          this.currentStep = this.stepsCount - 1;
          this.switchSlide();
        }
      }
    },
    nextSlide: function(fromAutoRotation) {
      if (!(this.options.disableWhileAnimating && this.galleryAnimating)) {
        if (this.currentStep < this.stepsCount - 1) {
          this.currentStep++;
          this.switchSlide();
        } else if (this.options.circularRotation || fromAutoRotation === true) {
          this.currentStep = 0;
          this.switchSlide();
        }
      }
    },
    numSlide: function(c) {
      if (this.currentStep != c) {
        this.currentStep = c;
        this.switchSlide();
      }
    },
    switchSlide: function() {
      var self = this;
      this.galleryAnimating = true;
      this.tmpProps = {};
      this.tmpProps[this.animProperty] = this.getStepOffset();
      this.slider.stop().animate(this.tmpProps, {
        duration: this.options.animSpeed,
        complete: function() {
          // animation complete
          self.galleryAnimating = false;
          if (self.resizeQueue) {
            self.onWindowResize();
          }

          // onchange callback
          self.makeCallback('onChange', self);
          self.autoRotate();
        }
      });
      this.refreshState();

      // onchange callback
      this.makeCallback('onBeforeChange', this);
    },
    refreshState: function(initial) {
      if (this.options.step === 1 || this.stepsCount === this.slides.length) {
        this.slides.removeClass(this.options.activeClass).eq(this.currentStep).addClass(this.options.activeClass);
      }
      this.pagerLinks.removeClass(this.options.activeClass).eq(this.currentStep).addClass(this.options.activeClass);
      this.curNum.html(this.currentStep + 1);
      this.allNum.html(this.stepsCount);

      // initial refresh
      if (this.options.maskAutoSize && typeof this.options.step === 'number') {
        this.tmpProps = {};
        this.tmpProps[this.maskSizeProperty] = this.slides.eq(Math.min(this.currentStep, this.slides.length - 1))[this.slideSizeFunction](true);
        this.mask.stop()[initial ? 'css' : 'animate'](this.tmpProps);
      }

      // disabled state
      if (!this.options.circularRotation) {
        this.btnPrev.add(this.btnNext).removeClass(this.options.disabledClass);
        if (this.currentStep === 0) this.btnPrev.addClass(this.options.disabledClass);
        if (this.currentStep === this.stepsCount - 1) this.btnNext.addClass(this.options.disabledClass);
      }

      // add class if not enough slides
      this.gallery.toggleClass('not-enough-slides', this.sumSize <= this.maskSize);
    },
    startRotation: function() {
      this.options.autoRotation = true;
      this.galleryHover = false;
      this.autoRotationStopped = false;
      this.resumeRotation();
    },
    stopRotation: function() {
      this.galleryHover = true;
      this.autoRotationStopped = true;
      this.pauseRotation();
    },
    pauseRotation: function() {
      this.gallery.addClass(this.options.autorotationDisabledClass);
      this.gallery.removeClass(this.options.autorotationActiveClass);
      clearTimeout(this.timer);
    },
    resumeRotation: function() {
      if (!this.autoRotationStopped) {
        this.gallery.addClass(this.options.autorotationActiveClass);
        this.gallery.removeClass(this.options.autorotationDisabledClass);
        this.autoRotate();
      }
    },
    autoRotate: function() {
      var self = this;
      clearTimeout(this.timer);
      if (this.options.autoRotation && !this.galleryHover && !this.autoRotationStopped) {
        this.timer = setTimeout(function() {
          self.nextSlide(true);
        }, this.options.switchTime);
      } else {
        this.pauseRotation();
      }
    },
    bindHandlers: function(handlersList) {
      var self = this;
      $.each(handlersList, function(index, handler) {
        var origHandler = self[handler];
        self[handler] = function() {
          return origHandler.apply(self, arguments);
        };
      });
    },
    makeCallback: function(name) {
      if (typeof this.options[name] === 'function') {
        var args = Array.prototype.slice.call(arguments);
        args.shift();
        this.options[name].apply(this, args);
      }
    },
    destroy: function() {
      // destroy handler
      $(window).unbind('load resize orientationchange', this.onWindowResize);
      this.btnPrev.unbind(this.options.event, this.prevSlideHandler);
      this.btnNext.unbind(this.options.event, this.nextSlideHandler);
      this.pagerLinks.unbind(this.options.event, this.pagerLinksHandler);
      this.gallery.unbind('mouseenter', this.hoverHandler);
      this.gallery.unbind('mouseleave', this.leaveHandler);

      // autorotation buttons handlers
      this.stopRotation();
      this.btnPlay.unbind(this.options.event, this.btnPlayHandler);
      this.btnPause.unbind(this.options.event, this.btnPauseHandler);
      this.btnPlayPause.unbind(this.options.event, this.btnPlayPauseHandler);

      // destroy swipe handler
      if (this.swipeHandler) {
        this.swipeHandler.destroy();
      }

      // remove inline styles, classes and pagination
      var unneededClasses = [this.options.galleryReadyClass, this.options.autorotationActiveClass, this.options.autorotationDisabledClass];
      this.gallery.removeClass(unneededClasses.join(' '));
      this.slider.add(this.slides).removeAttr('style');
      if (typeof this.options.generatePagination === 'string') {
        this.pagerHolder.empty();
      }
    }
  };

  // detect device type
  var isTouchDevice = /Windows Phone/.test(navigator.userAgent) || ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;

  // jquery plugin
  $.fn.scrollGallery = function(opt) {
    return this.each(function() {
      $(this).data('ScrollGallery', new ScrollGallery($.extend(opt, {
        holder: this
      })));
    });
  };
}(jQuery));

/*
 * jQuery Cycle Carousel plugin
 */
(function($) {
  function ScrollAbsoluteGallery(options) {
    this.options = $.extend({
      activeClass: 'active',
      mask: 'div.slides-mask',
      slider: '>ul',
      slides: '>li',
      btnPrev: '.btn-prev',
      btnNext: '.btn-next',
      pagerLinks: 'ul.pager > li',
      generatePagination: false,
      pagerList: '<ul>',
      pagerListItem: '<li><a href="#"></a></li>',
      pagerListItemText: 'a',
      galleryReadyClass: 'gallery-js-ready',
      currentNumber: 'span.current-num',
      totalNumber: 'span.total-num',
      maskAutoSize: false,
      autoRotation: false,
      pauseOnHover: false,
      stretchSlideToMask: false,
      switchTime: 3000,
      animSpeed: 500,
      handleTouch: true,
      swipeThreshold: 15,
      vertical: false,
      reverse: false
    }, options);
    this.init();
  }
  ScrollAbsoluteGallery.prototype = {
    init: function() {
      if (this.options.holder) {
        this.findElements();
        this.attachEvents();
        this.makeCallback('onInit', this);
      }
    },
    findElements: function() {
      // find structure elements
      this.holder = $(this.options.holder).addClass(this.options.galleryReadyClass);
      this.mask = this.holder.find(this.options.mask);
      this.slider = this.mask.find(this.options.slider);
      this.slides = this.slider.find(this.options.slides);
      this.btnPrev = this.holder.find(this.options.btnPrev);
      this.btnNext = this.holder.find(this.options.btnNext);

      // slide count display
      this.currentNumber = this.holder.find(this.options.currentNumber);
      this.totalNumber = this.holder.find(this.options.totalNumber);

      // create gallery pagination
      if (typeof this.options.generatePagination === 'string') {
        this.pagerLinks = this.buildPagination();
      } else {
        this.pagerLinks = this.holder.find(this.options.pagerLinks);
      }

      // define index variables
      this.sizeProperty = this.options.vertical ? 'height' : 'width';
      this.positionProperty = this.options.vertical ? 'top' : 'left';
      this.animProperty = this.options.vertical ? 'marginTop' : 'marginLeft';

      this.slideSize = this.slides[this.sizeProperty]();
      this.currentIndex = 0;
      this.prevIndex = 0;

      // reposition elements
      this.options.maskAutoSize = this.options.vertical ? false : this.options.maskAutoSize;
      if (this.options.vertical) {
        this.mask.css({
          height: this.slides.innerHeight()
        });
      }
      if (this.options.maskAutoSize) {
        this.mask.css({
          height: this.slider.height()
        });
      }
      this.slider.css({
        position: 'relative',
        height: this.options.vertical ? this.slideSize * this.slides.length : '100%'
      });
      this.slides.css({
        position: 'absolute'
      }).css(this.positionProperty, -9999).eq(this.currentIndex).css(this.positionProperty, 0);
      this.refreshState();
    },
    buildPagination: function() {
      var pagerLinks = $();
      if (!this.pagerHolder) {
        this.pagerHolder = this.holder.find(this.options.generatePagination);
      }
      if (this.pagerHolder.length) {
        this.pagerHolder.empty();
        this.pagerList = $(this.options.pagerList).appendTo(this.pagerHolder);
        for (var i = 0; i < this.slides.length; i++) {
          $(this.options.pagerListItem).appendTo(this.pagerList).find(this.options.pagerListItemText).text(i + 1);
        }
        pagerLinks = this.pagerList.children();
      }
      return pagerLinks;
    },
    attachEvents: function() {
      // attach handlers
      var self = this;
      if (this.btnPrev.length) {
        this.btnPrevHandler = function(e) {
          e.preventDefault();
          self.prevSlide();
        };
        this.btnPrev.click(this.btnPrevHandler);
      }
      if (this.btnNext.length) {
        this.btnNextHandler = function(e) {
          e.preventDefault();
          self.nextSlide();
        };
        this.btnNext.click(this.btnNextHandler);
      }
      if (this.pagerLinks.length) {
        this.pagerLinksHandler = function(e) {
          e.preventDefault();
          self.numSlide(self.pagerLinks.index(e.currentTarget));
        };
        this.pagerLinks.click(this.pagerLinksHandler);
      }

      // handle autorotation pause on hover
      if (this.options.pauseOnHover) {
        this.hoverHandler = function() {
          clearTimeout(self.timer);
        };
        this.leaveHandler = function() {
          self.autoRotate();
        };
        this.holder.bind({
          mouseenter: this.hoverHandler,
          mouseleave: this.leaveHandler
        });
      }

      // handle holder and slides dimensions
      this.resizeHandler = function() {
        if (!self.animating) {
          if (self.options.stretchSlideToMask) {
            self.resizeSlides();
          }
          self.resizeHolder();
          self.setSlidesPosition(self.currentIndex);
        }
      };
      $(window).bind('load resize orientationchange', this.resizeHandler);
      if (self.options.stretchSlideToMask) {
        self.resizeSlides();
      }

      // handle swipe on mobile devices
      if (this.options.handleTouch && window.Hammer && this.mask.length && this.slides.length > 1 && isTouchDevice) {
        this.swipeHandler = new Hammer.Manager(this.mask[0]);
        this.swipeHandler.add(new Hammer.Pan({
          direction: self.options.vertical ? Hammer.DIRECTION_VERTICAL : Hammer.DIRECTION_HORIZONTAL,
          threshold: self.options.swipeThreshold
        }));

        this.swipeHandler.on('panstart', function() {
          if (self.animating) {
            self.swipeHandler.stop();
          } else {
            clearTimeout(self.timer);
          }
        }).on('panmove', function(e) {
          self.swipeOffset = -self.slideSize + e[self.options.vertical ? 'deltaY' : 'deltaX'];
          self.slider.css(self.animProperty, self.swipeOffset);
          clearTimeout(self.timer);
        }).on('panend', function(e) {
          if (e.distance > self.options.swipeThreshold) {
            if (e.offsetDirection === Hammer.DIRECTION_RIGHT || e.offsetDirection === Hammer.DIRECTION_DOWN) {
              self.nextSlide();
            } else {
              self.prevSlide();
            }
          } else {
            var tmpObj = {};
            tmpObj[self.animProperty] = -self.slideSize;
            self.slider.animate(tmpObj, {
              duration: self.options.animSpeed
            });
            self.autoRotate();
          }
          self.swipeOffset = 0;
        });
      }

      // start autorotation
      this.autoRotate();
      this.resizeHolder();
      this.setSlidesPosition(this.currentIndex);
    },
    resizeSlides: function() {
      this.slideSize = this.mask[this.options.vertical ? 'height' : 'width']();
      this.slides.css(this.sizeProperty, this.slideSize);
    },
    resizeHolder: function() {
      if (this.options.maskAutoSize) {
        this.mask.css({
          height: this.slides.eq(this.currentIndex).outerHeight(true)
        });
      }
    },
    prevSlide: function() {
      if (!this.animating && this.slides.length > 1) {
        if (this.options.reverse) {
          this.direction = 1;
        } else {
          this.direction = -1;
        }
        this.prevIndex = this.currentIndex;
        if (this.currentIndex > 0) this.currentIndex--;
        else this.currentIndex = this.slides.length - 1;
        this.switchSlide();
      }
    },
    nextSlide: function(fromAutoRotation) {
      if (!this.animating && this.slides.length > 1) {
        if (this.options.reverse) {
          this.direction = -1;
        } else {
          this.direction = 1;
        }
        this.prevIndex = this.currentIndex;
        if (this.currentIndex < this.slides.length - 1) this.currentIndex++;
        else this.currentIndex = 0;
        this.switchSlide();
      }
    },
    numSlide: function(c) {
      if (!this.animating && this.currentIndex !== c && this.slides.length > 1) {
        this.direction = c > this.currentIndex ? 1 : -1;
        this.prevIndex = this.currentIndex;
        this.currentIndex = c;
        this.switchSlide();
      }
    },
    preparePosition: function() {
      // prepare slides position before animation
      this.setSlidesPosition(this.prevIndex, this.direction < 0 ? this.currentIndex : null, this.direction > 0 ? this.currentIndex : null, this.direction);
    },
    setSlidesPosition: function(index, slideLeft, slideRight, direction) {
      // reposition holder and nearest slides
      if (this.slides.length > 1) {
        var prevIndex = (typeof slideLeft === 'number' ? slideLeft : index > 0 ? index - 1 : this.slides.length - 1);
        var nextIndex = (typeof slideRight === 'number' ? slideRight : index < this.slides.length - 1 ? index + 1 : 0);

        this.slider.css(this.animProperty, this.swipeOffset ? this.swipeOffset : -this.slideSize);
        this.slides.css(this.positionProperty, -9999).eq(index).css(this.positionProperty, this.slideSize);
        if (prevIndex === nextIndex && typeof direction === 'number') {
          var calcOffset = direction > 0 ? this.slideSize * 2 : 0;
          this.slides.eq(nextIndex).css(this.positionProperty, calcOffset);
        } else {
          this.slides.eq(prevIndex).css(this.positionProperty, 0);
          this.slides.eq(nextIndex).css(this.positionProperty, this.slideSize * 2);
        }
      }
    },
    switchSlide: function() {
      // prepare positions and calculate offset
      var self = this;
      var oldSlide = this.slides.eq(this.prevIndex);
      var newSlide = this.slides.eq(this.currentIndex);
      this.animating = true;

      // resize mask to fit slide
      if (this.options.maskAutoSize) {
        this.mask.animate({
          height: newSlide.outerHeight(true)
        }, {
          duration: this.options.animSpeed
        });
      }

      // start animation
      var animProps = {};
      animProps[this.animProperty] = this.direction > 0 ? -this.slideSize * 2 : 0;
      this.preparePosition();
      this.slider.animate(animProps, {
        duration: this.options.animSpeed,
        complete: function() {
          self.setSlidesPosition(self.currentIndex);

          // start autorotation
          self.animating = false;
          self.autoRotate();

          // onchange callback
          self.makeCallback('onChange', self);
        }
      });

      // refresh classes
      this.refreshState();

      // onchange callback
      this.makeCallback('onBeforeChange', this);
    },
    refreshState: function(initial) {
      // slide change function
      this.slides.removeClass(this.options.activeClass).eq(this.currentIndex).addClass(this.options.activeClass);
      var text = this.pagerLinks.removeClass(this.options.activeClass).eq(this.currentIndex).addClass(this.options.activeClass).text();
      this.holder.find('.opener .selected').each(function() {
        jQuery(this).text(text);
      });

      // display current slide number
      this.currentNumber.html(this.currentIndex + 1);
      this.totalNumber.html(this.slides.length);

      // add class if not enough slides
      this.holder.toggleClass('not-enough-slides', this.slides.length === 1);
    },
    autoRotate: function() {
      var self = this;
      clearTimeout(this.timer);
      if (this.options.autoRotation) {
        this.timer = setTimeout(function() {
          self.nextSlide();
        }, this.options.switchTime);
      }
    },
    makeCallback: function(name) {
      if (typeof this.options[name] === 'function') {
        var args = Array.prototype.slice.call(arguments);
        args.shift();
        this.options[name].apply(this, args);
      }
    },
    destroy: function() {
      // destroy handler
      this.btnPrev.unbind('click', this.btnPrevHandler);
      this.btnNext.unbind('click', this.btnNextHandler);
      this.pagerLinks.unbind('click', this.pagerLinksHandler);
      this.holder.unbind('mouseenter', this.hoverHandler);
      this.holder.unbind('mouseleave', this.leaveHandler);
      $(window).unbind('load resize orientationchange', this.resizeHandler);
      clearTimeout(this.timer);

      // destroy swipe handler
      if (this.swipeHandler) {
        this.swipeHandler.destroy();
      }

      // remove inline styles, classes and pagination
      this.holder.removeClass(this.options.galleryReadyClass);
      this.slider.add(this.slides).removeAttr('style');
      if (typeof this.options.generatePagination === 'string') {
        this.pagerHolder.empty();
      }
    }
  };

  // detect device type
  var isTouchDevice = /Windows Phone/.test(navigator.userAgent) || ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;

  // jquery plugin
  $.fn.scrollAbsoluteGallery = function(opt) {
    return this.each(function() {
      $(this).data('ScrollAbsoluteGallery', new ScrollAbsoluteGallery($.extend(opt, {
        holder: this
      })));
    });
  };
}(jQuery));
/*
 * jQuery Cycle Carousel plugin
 */
(function($) {
  function ScrollCloneGallery(options) {
    this.options = $.extend({
      activeClass: 'active',
      cloneClass: 'clone',
      holderClass: '.slide-holder',
      slider: '.slide-list',
      btnPrev: '.btn-prev',
      btnNext: '.btn-next',
      pagerLinks: 'ul.pager > li',
      generatePagination: false,
      pagerList: '<ul>',
      pagerListItem: '<li><a href="#"></a></li>',
      pagerListItemText: 'a',
      pauseOnHover: true,
      autoRotation: true,
      switchTime: 5000,
      animSpeed: 1000,
      swipeThreshold: 15
    }, options);
    this.init();
  }
  ScrollCloneGallery.prototype = {
    init: function() {
      this.findElements();
      this.attachEvents();
      this.refreshClasses();
      this.makeCallback('onInit', this);
    },
    findElements: function() {
      var self = this;
      this.obj = $(this.options.holder);
      this.gallery = this.obj;
      this.mask = this.obj.find(self.options.holderClass);
      this.slider = this.mask.find(self.options.slider);
      this.slides = this.slider.children();
      this.btnPrev = this.obj.find(self.options.btnPrev);
      this.btnNext = this.obj.find(self.options.btnNext);
      this.len = this.slides.length;
      this.slideWidth = this.slides.eq(0).outerWidth(true);
      this.sliderWidth = this.len * this.slideWidth;
      this.maxMarginHorizont = -(self.sliderWidth * 2);
      this.curMargin = -this.sliderWidth;
      this.index = 0;
      this.timer = 0;
      this.animation = false;
      this.slides.clone().addClass(this.options.cloneClass).prependTo(this.slider);
      this.slides.clone().addClass(this.options.cloneClass).appendTo(this.slider);
      this.slides.eq(0).addClass(this.options.activeClass);
      this.slider.css({
        marginLeft: -this.sliderWidth
      });
      this.isTouchDevice = (/MSIE 10.*Touch/.test(navigator.userAgent)) || ('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch;
      if (typeof this.options.generatePagination === 'string') {
        this.pagerLinks = this.buildPagination();
      } else {
        this.pagerLinks = this.obj.find(this.options.pagerLinks);
      }
    },
    buildPagination: function() {
      var pagerLinks = $();
      if (!this.pagerHolder) {
        this.pagerHolder = this.obj.find(this.options.generatePagination);
      }
      if (this.pagerHolder.length) {
        this.pagerHolder.empty();
        this.pagerList = $(this.options.pagerList).appendTo(this.pagerHolder);
        for (var i = 0; i < this.slides.length; i++) {
          $(this.options.pagerListItem).appendTo(this.pagerList).find(this.options.pagerListItemText).text(i + 1);
        }
        pagerLinks = this.pagerList.children();
      }
      return pagerLinks;
    },
    attachEvents: function() {
      var self = this;
      // btnPrev click
      this.btnPrev.click(function(e) {
        e.preventDefault();
        if (!self.animation) {
          self.slidePrev();
        }
      });
      // btnNext click
      this.btnNext.click(function(e) {
        e.preventDefault();
        if (!self.animation) {
          self.slideNext();
        }
      });
      // window resize
      $(window).bind('resize orientationchange', function() {
        self.refreshSlides();
      });
      // swipe events
      if (this.isTouchDevice) {
        this.swipeHandler = new Hammer.Manager(self.gallery[0]);
        this.swipeHandler.add(new Hammer.Pan({
          direction: Hammer.DIRECTION_HORIZONTAL,
          threshold: this.options.swipeThreshold
        }));
        this.swipeHandler.on('panend', function(e) {
          if (e.distance > self.options.swipeThreshold) {
            if (e.offsetDirection === Hammer.DIRECTION_RIGHT || e.offsetDirection === Hammer.DIRECTION_DOWN) {
              self.slideNext();
            } else {
              self.slidePrev();
            }
          } else {
            self.switchSlide();
          }
        });
      }
      if (this.pagerLinks.length) {
        this.pagerLinksHandler = function(e) {
          e.preventDefault();
          self.numSlide(self.pagerLinks.index(e.currentTarget));
        };
        this.pagerLinks.click(this.pagerLinksHandler);
      }
      // handle autorotation pause on hover
      if (this.options.pauseOnHover) {
        this.hoverHandler = function() {
          clearTimeout(self.timer);
        };
        this.leaveHandler = function() {
          self.autoRotate();
        };
        this.gallery.bind({
          mouseenter: this.hoverHandler,
          mouseleave: this.leaveHandler
        });
      }
      // start autorotation
      this.autoRotate();
    },
    autoRotate: function() {
      var self = this;
      clearTimeout(this.timer);
      if (this.options.autoRotation) {
        this.timer = setTimeout(function() {
          self.slideNext();
        }, this.options.switchTime);
      }
    },
    numSlide: function(c) {
      if (!this.animation && this.index !== c && this.slides.length > 1) {
        this.index = c;
        this.curMargin = -(this.sliderWidth + this.index * this.slideWidth);
        this.switchSlide();
      }
    },
    switchSlide: function() {
      var self = this;
      this.animation = true;
      // onchange callback
      this.makeCallback('onBeforeChange', this);
      this.slider.animate({
        marginLeft: self.curMargin
      }, {
        duration: self.options.animSpeed,
        complete: function() {
          self.curMargin = parseInt(self.slider.css('marginLeft'), 10);
          if (self.curMargin < self.maxMarginHorizont) {
            self.curMargin = -self.sliderWidth + Math.abs(self.maxMarginHorizont) - Math.abs(self.curMargin);
          } else {
            if (self.curMargin > -self.sliderWidth) {
              self.curMargin = -(self.sliderWidth * 2) + self.sliderWidth - Math.abs(self.curMargin);
            }
          }
          self.refreshSlides();
          self.refreshClasses();
          self.slider.css({
            marginLeft: self.curMargin
          });
          self.animation = false;
          self.autoRotate();
          // onchange callback
          self.makeCallback('onChange', self);
        }
      });
    },
    slideNext: function() {
      var self = this;
      this.index++;
      if (this.index > this.len) self.index = 1;
      this.curMargin -= self.slideWidth;
      this.switchSlide();
    },
    slidePrev: function() {
      var self = this;
      this.index--;
      if (this.index < 0) self.index = self.len - 1;
      this.curMargin += self.slideWidth;
      this.switchSlide();
    },
    refreshSlides: function() {
      this.slideWidth = this.slides.eq(0).outerWidth(true);
      this.sliderWidth = this.len * this.slideWidth;
      this.curMargin = -(this.sliderWidth + this.index * this.slideWidth);
      this.slider.css({
        marginLeft: this.curMargin
      });
    },
    refreshClasses: function() {
      var self = this;
      this.slider.children().removeClass(self.options.activeClass);
      if (this.index === this.len) {
        self.index = 0;
        self.slider.children().eq(self.len * 2).addClass(self.options.activeClass);
      } else {
        self.slides.eq(self.index).addClass(self.options.activeClass);
      }
      this.pagerLinks.removeClass(this.options.activeClass).eq(this.index).addClass(this.options.activeClass);
    },
    makeCallback: function(name) {
      if (typeof this.options[name] === 'function') {
        var args = Array.prototype.slice.call(arguments);
        args.shift();
        this.options[name].apply(this, args);
      }
    }
  }
  $.fn.scrollCloneGallery = function(options) {
    return this.each(function() {
      $(this).data('ScrollCloneGallery', new ScrollCloneGallery($.extend(options, {
        holder: this
      })));
    })
  }
}(jQuery));
