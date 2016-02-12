// Compiled file - any changes will be overwritten by grunt task

/* node_modules/bootstrap/dist/js/bootstrap.min.js */
/*!
 * Bootstrap v3.3.6 (http://getbootstrap.com)
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under the MIT license
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(a){"use strict";var b=a.fn.jquery.split(" ")[0].split(".");if(b[0]<2&&b[1]<9||1==b[0]&&9==b[1]&&b[2]<1||b[0]>2)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher, but lower than version 3")}(jQuery),+function(a){"use strict";function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one("bsTransitionEnd",function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b(),a.support.transition&&(a.event.special.bsTransitionEnd={bindType:a.support.transition.end,delegateType:a.support.transition.end,handle:function(b){return a(b.target).is(this)?b.handleObj.handler.apply(this,arguments):void 0}})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var c=a(this),e=c.data("bs.alert");e||c.data("bs.alert",e=new d(this)),"string"==typeof b&&e[b].call(c)})}var c='[data-dismiss="alert"]',d=function(b){a(b).on("click",c,this.close)};d.VERSION="3.3.6",d.TRANSITION_DURATION=150,d.prototype.close=function(b){function c(){g.detach().trigger("closed.bs.alert").remove()}var e=a(this),f=e.attr("data-target");f||(f=e.attr("href"),f=f&&f.replace(/.*(?=#[^\s]*$)/,""));var g=a(f);b&&b.preventDefault(),g.length||(g=e.closest(".alert")),g.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(g.removeClass("in"),a.support.transition&&g.hasClass("fade")?g.one("bsTransitionEnd",c).emulateTransitionEnd(d.TRANSITION_DURATION):c())};var e=a.fn.alert;a.fn.alert=b,a.fn.alert.Constructor=d,a.fn.alert.noConflict=function(){return a.fn.alert=e,this},a(document).on("click.bs.alert.data-api",c,d.prototype.close)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.button"),f="object"==typeof b&&b;e||d.data("bs.button",e=new c(this,f)),"toggle"==b?e.toggle():b&&e.setState(b)})}var c=function(b,d){this.$element=a(b),this.options=a.extend({},c.DEFAULTS,d),this.isLoading=!1};c.VERSION="3.3.6",c.DEFAULTS={loadingText:"loading..."},c.prototype.setState=function(b){var c="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();b+="Text",null==f.resetText&&d.data("resetText",d[e]()),setTimeout(a.proxy(function(){d[e](null==f[b]?this.options[b]:f[b]),"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c))},this),0)},c.prototype.toggle=function(){var a=!0,b=this.$element.closest('[data-toggle="buttons"]');if(b.length){var c=this.$element.find("input");"radio"==c.prop("type")?(c.prop("checked")&&(a=!1),b.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==c.prop("type")&&(c.prop("checked")!==this.$element.hasClass("active")&&(a=!1),this.$element.toggleClass("active")),c.prop("checked",this.$element.hasClass("active")),a&&c.trigger("change")}else this.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};var d=a.fn.button;a.fn.button=b,a.fn.button.Constructor=c,a.fn.button.noConflict=function(){return a.fn.button=d,this},a(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(c){var d=a(c.target);d.hasClass("btn")||(d=d.closest(".btn")),b.call(d,"toggle"),a(c.target).is('input[type="radio"]')||a(c.target).is('input[type="checkbox"]')||c.preventDefault()}).on("focus.bs.button.data-api blur.bs.button.data-api",'[data-toggle^="button"]',function(b){a(b.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(b.type))})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.carousel"),f=a.extend({},c.DEFAULTS,d.data(),"object"==typeof b&&b),g="string"==typeof b?b:f.slide;e||d.data("bs.carousel",e=new c(this,f)),"number"==typeof b?e.to(b):g?e[g]():f.interval&&e.pause().cycle()})}var c=function(b,c){this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=null,this.sliding=null,this.interval=null,this.$active=null,this.$items=null,this.options.keyboard&&this.$element.on("keydown.bs.carousel",a.proxy(this.keydown,this)),"hover"==this.options.pause&&!("ontouchstart"in document.documentElement)&&this.$element.on("mouseenter.bs.carousel",a.proxy(this.pause,this)).on("mouseleave.bs.carousel",a.proxy(this.cycle,this))};c.VERSION="3.3.6",c.TRANSITION_DURATION=600,c.DEFAULTS={interval:5e3,pause:"hover",wrap:!0,keyboard:!0},c.prototype.keydown=function(a){if(!/input|textarea/i.test(a.target.tagName)){switch(a.which){case 37:this.prev();break;case 39:this.next();break;default:return}a.preventDefault()}},c.prototype.cycle=function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},c.prototype.getItemIndex=function(a){return this.$items=a.parent().children(".item"),this.$items.index(a||this.$active)},c.prototype.getItemForDirection=function(a,b){var c=this.getItemIndex(b),d="prev"==a&&0===c||"next"==a&&c==this.$items.length-1;if(d&&!this.options.wrap)return b;var e="prev"==a?-1:1,f=(c+e)%this.$items.length;return this.$items.eq(f)},c.prototype.to=function(a){var b=this,c=this.getItemIndex(this.$active=this.$element.find(".item.active"));return a>this.$items.length-1||0>a?void 0:this.sliding?this.$element.one("slid.bs.carousel",function(){b.to(a)}):c==a?this.pause().cycle():this.slide(a>c?"next":"prev",this.$items.eq(a))},c.prototype.pause=function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},c.prototype.next=function(){return this.sliding?void 0:this.slide("next")},c.prototype.prev=function(){return this.sliding?void 0:this.slide("prev")},c.prototype.slide=function(b,d){var e=this.$element.find(".item.active"),f=d||this.getItemForDirection(b,e),g=this.interval,h="next"==b?"left":"right",i=this;if(f.hasClass("active"))return this.sliding=!1;var j=f[0],k=a.Event("slide.bs.carousel",{relatedTarget:j,direction:h});if(this.$element.trigger(k),!k.isDefaultPrevented()){if(this.sliding=!0,g&&this.pause(),this.$indicators.length){this.$indicators.find(".active").removeClass("active");var l=a(this.$indicators.children()[this.getItemIndex(f)]);l&&l.addClass("active")}var m=a.Event("slid.bs.carousel",{relatedTarget:j,direction:h});return a.support.transition&&this.$element.hasClass("slide")?(f.addClass(b),f[0].offsetWidth,e.addClass(h),f.addClass(h),e.one("bsTransitionEnd",function(){f.removeClass([b,h].join(" ")).addClass("active"),e.removeClass(["active",h].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger(m)},0)}).emulateTransitionEnd(c.TRANSITION_DURATION)):(e.removeClass("active"),f.addClass("active"),this.sliding=!1,this.$element.trigger(m)),g&&this.cycle(),this}};var d=a.fn.carousel;a.fn.carousel=b,a.fn.carousel.Constructor=c,a.fn.carousel.noConflict=function(){return a.fn.carousel=d,this};var e=function(c){var d,e=a(this),f=a(e.attr("data-target")||(d=e.attr("href"))&&d.replace(/.*(?=#[^\s]+$)/,""));if(f.hasClass("carousel")){var g=a.extend({},f.data(),e.data()),h=e.attr("data-slide-to");h&&(g.interval=!1),b.call(f,g),h&&f.data("bs.carousel").to(h),c.preventDefault()}};a(document).on("click.bs.carousel.data-api","[data-slide]",e).on("click.bs.carousel.data-api","[data-slide-to]",e),a(window).on("load",function(){a('[data-ride="carousel"]').each(function(){var c=a(this);b.call(c,c.data())})})}(jQuery),+function(a){"use strict";function b(b){var c,d=b.attr("data-target")||(c=b.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,"");return a(d)}function c(b){return this.each(function(){var c=a(this),e=c.data("bs.collapse"),f=a.extend({},d.DEFAULTS,c.data(),"object"==typeof b&&b);!e&&f.toggle&&/show|hide/.test(b)&&(f.toggle=!1),e||c.data("bs.collapse",e=new d(this,f)),"string"==typeof b&&e[b]()})}var d=function(b,c){this.$element=a(b),this.options=a.extend({},d.DEFAULTS,c),this.$trigger=a('[data-toggle="collapse"][href="#'+b.id+'"],[data-toggle="collapse"][data-target="#'+b.id+'"]'),this.transitioning=null,this.options.parent?this.$parent=this.getParent():this.addAriaAndCollapsedClass(this.$element,this.$trigger),this.options.toggle&&this.toggle()};d.VERSION="3.3.6",d.TRANSITION_DURATION=350,d.DEFAULTS={toggle:!0},d.prototype.dimension=function(){var a=this.$element.hasClass("width");return a?"width":"height"},d.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var b,e=this.$parent&&this.$parent.children(".panel").children(".in, .collapsing");if(!(e&&e.length&&(b=e.data("bs.collapse"),b&&b.transitioning))){var f=a.Event("show.bs.collapse");if(this.$element.trigger(f),!f.isDefaultPrevented()){e&&e.length&&(c.call(e,"hide"),b||e.data("bs.collapse",null));var g=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[g](0).attr("aria-expanded",!0),this.$trigger.removeClass("collapsed").attr("aria-expanded",!0),this.transitioning=1;var h=function(){this.$element.removeClass("collapsing").addClass("collapse in")[g](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!a.support.transition)return h.call(this);var i=a.camelCase(["scroll",g].join("-"));this.$element.one("bsTransitionEnd",a.proxy(h,this)).emulateTransitionEnd(d.TRANSITION_DURATION)[g](this.$element[0][i])}}}},d.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var b=a.Event("hide.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.dimension();this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded",!1),this.$trigger.addClass("collapsed").attr("aria-expanded",!1),this.transitioning=1;var e=function(){this.transitioning=0,this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")};return a.support.transition?void this.$element[c](0).one("bsTransitionEnd",a.proxy(e,this)).emulateTransitionEnd(d.TRANSITION_DURATION):e.call(this)}}},d.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()},d.prototype.getParent=function(){return a(this.options.parent).find('[data-toggle="collapse"][data-parent="'+this.options.parent+'"]').each(a.proxy(function(c,d){var e=a(d);this.addAriaAndCollapsedClass(b(e),e)},this)).end()},d.prototype.addAriaAndCollapsedClass=function(a,b){var c=a.hasClass("in");a.attr("aria-expanded",c),b.toggleClass("collapsed",!c).attr("aria-expanded",c)};var e=a.fn.collapse;a.fn.collapse=c,a.fn.collapse.Constructor=d,a.fn.collapse.noConflict=function(){return a.fn.collapse=e,this},a(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(d){var e=a(this);e.attr("data-target")||d.preventDefault();var f=b(e),g=f.data("bs.collapse"),h=g?"toggle":e.data();c.call(f,h)})}(jQuery),+function(a){"use strict";function b(b){var c=b.attr("data-target");c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));var d=c&&a(c);return d&&d.length?d:b.parent()}function c(c){c&&3===c.which||(a(e).remove(),a(f).each(function(){var d=a(this),e=b(d),f={relatedTarget:this};e.hasClass("open")&&(c&&"click"==c.type&&/input|textarea/i.test(c.target.tagName)&&a.contains(e[0],c.target)||(e.trigger(c=a.Event("hide.bs.dropdown",f)),c.isDefaultPrevented()||(d.attr("aria-expanded","false"),e.removeClass("open").trigger(a.Event("hidden.bs.dropdown",f)))))}))}function d(b){return this.each(function(){var c=a(this),d=c.data("bs.dropdown");d||c.data("bs.dropdown",d=new g(this)),"string"==typeof b&&d[b].call(c)})}var e=".dropdown-backdrop",f='[data-toggle="dropdown"]',g=function(b){a(b).on("click.bs.dropdown",this.toggle)};g.VERSION="3.3.6",g.prototype.toggle=function(d){var e=a(this);if(!e.is(".disabled, :disabled")){var f=b(e),g=f.hasClass("open");if(c(),!g){"ontouchstart"in document.documentElement&&!f.closest(".navbar-nav").length&&a(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(a(this)).on("click",c);var h={relatedTarget:this};if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;e.trigger("focus").attr("aria-expanded","true"),f.toggleClass("open").trigger(a.Event("shown.bs.dropdown",h))}return!1}},g.prototype.keydown=function(c){if(/(38|40|27|32)/.test(c.which)&&!/input|textarea/i.test(c.target.tagName)){var d=a(this);if(c.preventDefault(),c.stopPropagation(),!d.is(".disabled, :disabled")){var e=b(d),g=e.hasClass("open");if(!g&&27!=c.which||g&&27==c.which)return 27==c.which&&e.find(f).trigger("focus"),d.trigger("click");var h=" li:not(.disabled):visible a",i=e.find(".dropdown-menu"+h);if(i.length){var j=i.index(c.target);38==c.which&&j>0&&j--,40==c.which&&j<i.length-1&&j++,~j||(j=0),i.eq(j).trigger("focus")}}}};var h=a.fn.dropdown;a.fn.dropdown=d,a.fn.dropdown.Constructor=g,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=h,this},a(document).on("click.bs.dropdown.data-api",c).on("click.bs.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.bs.dropdown.data-api",f,g.prototype.toggle).on("keydown.bs.dropdown.data-api",f,g.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",g.prototype.keydown)}(jQuery),+function(a){"use strict";function b(b,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},c.DEFAULTS,e.data(),"object"==typeof b&&b);f||e.data("bs.modal",f=new c(this,g)),"string"==typeof b?f[b](d):g.show&&f.show(d)})}var c=function(b,c){this.options=c,this.$body=a(document.body),this.$element=a(b),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};c.VERSION="3.3.6",c.TRANSITION_DURATION=300,c.BACKDROP_TRANSITION_DURATION=150,c.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},c.prototype.toggle=function(a){return this.isShown?this.hide():this.show(a)},c.prototype.show=function(b){var d=this,e=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(e),this.isShown||e.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",function(){d.$element.one("mouseup.dismiss.bs.modal",function(b){a(b.target).is(d.$element)&&(d.ignoreBackdropClick=!0)})}),this.backdrop(function(){var e=a.support.transition&&d.$element.hasClass("fade");d.$element.parent().length||d.$element.appendTo(d.$body),d.$element.show().scrollTop(0),d.adjustDialog(),e&&d.$element[0].offsetWidth,d.$element.addClass("in"),d.enforceFocus();var f=a.Event("shown.bs.modal",{relatedTarget:b});e?d.$dialog.one("bsTransitionEnd",function(){d.$element.trigger("focus").trigger(f)}).emulateTransitionEnd(c.TRANSITION_DURATION):d.$element.trigger("focus").trigger(f)}))},c.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),this.resize(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",a.proxy(this.hideModal,this)).emulateTransitionEnd(c.TRANSITION_DURATION):this.hideModal())},c.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.trigger("focus")},this))},c.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",a.proxy(function(a){27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},c.prototype.resize=function(){this.isShown?a(window).on("resize.bs.modal",a.proxy(this.handleUpdate,this)):a(window).off("resize.bs.modal")},c.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.$body.removeClass("modal-open"),a.resetAdjustments(),a.resetScrollbar(),a.$element.trigger("hidden.bs.modal")})},c.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},c.prototype.backdrop=function(b){var d=this,e=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var f=a.support.transition&&e;if(this.$backdrop=a(document.createElement("div")).addClass("modal-backdrop "+e).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){return this.ignoreBackdropClick?void(this.ignoreBackdropClick=!1):void(a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide()))},this)),f&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;f?this.$backdrop.one("bsTransitionEnd",b).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):b()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");var g=function(){d.removeBackdrop(),b&&b()};a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",g).emulateTransitionEnd(c.BACKDROP_TRANSITION_DURATION):g()}else b&&b()},c.prototype.handleUpdate=function(){this.adjustDialog()},c.prototype.adjustDialog=function(){var a=this.$element[0].scrollHeight>document.documentElement.clientHeight;this.$element.css({paddingLeft:!this.bodyIsOverflowing&&a?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!a?this.scrollbarWidth:""})},c.prototype.resetAdjustments=function(){this.$element.css({paddingLeft:"",paddingRight:""})},c.prototype.checkScrollbar=function(){var a=window.innerWidth;if(!a){var b=document.documentElement.getBoundingClientRect();a=b.right-Math.abs(b.left)}this.bodyIsOverflowing=document.body.clientWidth<a,this.scrollbarWidth=this.measureScrollbar()},c.prototype.setScrollbar=function(){var a=parseInt(this.$body.css("padding-right")||0,10);this.originalBodyPad=document.body.style.paddingRight||"",this.bodyIsOverflowing&&this.$body.css("padding-right",a+this.scrollbarWidth)},c.prototype.resetScrollbar=function(){this.$body.css("padding-right",this.originalBodyPad)},c.prototype.measureScrollbar=function(){var a=document.createElement("div");a.className="modal-scrollbar-measure",this.$body.append(a);var b=a.offsetWidth-a.clientWidth;return this.$body[0].removeChild(a),b};var d=a.fn.modal;a.fn.modal=b,a.fn.modal.Constructor=c,a.fn.modal.noConflict=function(){return a.fn.modal=d,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(c){var d=a(this),e=d.attr("href"),f=a(d.attr("data-target")||e&&e.replace(/.*(?=#[^\s]+$)/,"")),g=f.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(e)&&e},f.data(),d.data());d.is("a")&&c.preventDefault(),f.one("show.bs.modal",function(a){a.isDefaultPrevented()||f.one("hidden.bs.modal",function(){d.is(":visible")&&d.trigger("focus")})}),b.call(f,g,this)})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tooltip"),f="object"==typeof b&&b;(e||!/destroy|hide/.test(b))&&(e||d.data("bs.tooltip",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.type=null,this.options=null,this.enabled=null,this.timeout=null,this.hoverState=null,this.$element=null,this.inState=null,this.init("tooltip",a,b)};c.VERSION="3.3.6",c.TRANSITION_DURATION=150,c.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1,viewport:{selector:"body",padding:0}},c.prototype.init=function(b,c,d){if(this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d),this.$viewport=this.options.viewport&&a(a.isFunction(this.options.viewport)?this.options.viewport.call(this,this.$element):this.options.viewport.selector||this.options.viewport),this.inState={click:!1,hover:!1,focus:!1},this.$element[0]instanceof document.constructor&&!this.options.selector)throw new Error("`selector` option must be specified when initializing "+this.type+" on the window.document object!");for(var e=this.options.trigger.split(" "),f=e.length;f--;){var g=e[f];if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));else if("manual"!=g){var h="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.getOptions=function(b){return b=a.extend({},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},c.prototype.getDelegateOptions=function(){var b={},c=this.getDefaults();return this._options&&a.each(this._options,function(a,d){c[a]!=d&&(b[a]=d)}),b},c.prototype.enter=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusin"==b.type?"focus":"hover"]=!0),c.tip().hasClass("in")||"in"==c.hoverState?void(c.hoverState="in"):(clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show())},c.prototype.isInStateTrue=function(){for(var a in this.inState)if(this.inState[a])return!0;return!1},c.prototype.leave=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget).data("bs."+this.type);return c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c)),b instanceof a.Event&&(c.inState["focusout"==b.type?"focus":"hover"]=!1),c.isInStateTrue()?void 0:(clearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide())},c.prototype.show=function(){var b=a.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){this.$element.trigger(b);var d=a.contains(this.$element[0].ownerDocument.documentElement,this.$element[0]);if(b.isDefaultPrevented()||!d)return;var e=this,f=this.tip(),g=this.getUID(this.type);this.setContent(),f.attr("id",g),this.$element.attr("aria-describedby",g),this.options.animation&&f.addClass("fade");var h="function"==typeof this.options.placement?this.options.placement.call(this,f[0],this.$element[0]):this.options.placement,i=/\s?auto?\s?/i,j=i.test(h);j&&(h=h.replace(i,"")||"top"),f.detach().css({top:0,left:0,display:"block"}).addClass(h).data("bs."+this.type,this),this.options.container?f.appendTo(this.options.container):f.insertAfter(this.$element),this.$element.trigger("inserted.bs."+this.type);var k=this.getPosition(),l=f[0].offsetWidth,m=f[0].offsetHeight;if(j){var n=h,o=this.getPosition(this.$viewport);h="bottom"==h&&k.bottom+m>o.bottom?"top":"top"==h&&k.top-m<o.top?"bottom":"right"==h&&k.right+l>o.width?"left":"left"==h&&k.left-l<o.left?"right":h,f.removeClass(n).addClass(h)}var p=this.getCalculatedOffset(h,k,l,m);this.applyPlacement(p,h);var q=function(){var a=e.hoverState;e.$element.trigger("shown.bs."+e.type),e.hoverState=null,"out"==a&&e.leave(e)};a.support.transition&&this.$tip.hasClass("fade")?f.one("bsTransitionEnd",q).emulateTransitionEnd(c.TRANSITION_DURATION):q()}},c.prototype.applyPlacement=function(b,c){var d=this.tip(),e=d[0].offsetWidth,f=d[0].offsetHeight,g=parseInt(d.css("margin-top"),10),h=parseInt(d.css("margin-left"),10);isNaN(g)&&(g=0),isNaN(h)&&(h=0),b.top+=g,b.left+=h,a.offset.setOffset(d[0],a.extend({using:function(a){d.css({top:Math.round(a.top),left:Math.round(a.left)})}},b),0),d.addClass("in");var i=d[0].offsetWidth,j=d[0].offsetHeight;"top"==c&&j!=f&&(b.top=b.top+f-j);var k=this.getViewportAdjustedDelta(c,b,i,j);k.left?b.left+=k.left:b.top+=k.top;var l=/top|bottom/.test(c),m=l?2*k.left-e+i:2*k.top-f+j,n=l?"offsetWidth":"offsetHeight";d.offset(b),this.replaceArrow(m,d[0][n],l)},c.prototype.replaceArrow=function(a,b,c){this.arrow().css(c?"left":"top",50*(1-a/b)+"%").css(c?"top":"left","")},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},c.prototype.hide=function(b){function d(){"in"!=e.hoverState&&f.detach(),e.$element.removeAttr("aria-describedby").trigger("hidden.bs."+e.type),b&&b()}var e=this,f=a(this.$tip),g=a.Event("hide.bs."+this.type);return this.$element.trigger(g),g.isDefaultPrevented()?void 0:(f.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one("bsTransitionEnd",d).emulateTransitionEnd(c.TRANSITION_DURATION):d(),this.hoverState=null,this)},c.prototype.fixTitle=function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},c.prototype.hasContent=function(){return this.getTitle()},c.prototype.getPosition=function(b){b=b||this.$element;var c=b[0],d="BODY"==c.tagName,e=c.getBoundingClientRect();null==e.width&&(e=a.extend({},e,{width:e.right-e.left,height:e.bottom-e.top}));var f=d?{top:0,left:0}:b.offset(),g={scroll:d?document.documentElement.scrollTop||document.body.scrollTop:b.scrollTop()},h=d?{width:a(window).width(),height:a(window).height()}:null;return a.extend({},e,g,h,f)},c.prototype.getCalculatedOffset=function(a,b,c,d){return"bottom"==a?{top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{top:b.top+b.height/2-d/2,left:b.left-c}:{top:b.top+b.height/2-d/2,left:b.left+b.width}},c.prototype.getViewportAdjustedDelta=function(a,b,c,d){var e={top:0,left:0};if(!this.$viewport)return e;var f=this.options.viewport&&this.options.viewport.padding||0,g=this.getPosition(this.$viewport);if(/right|left/.test(a)){var h=b.top-f-g.scroll,i=b.top+f-g.scroll+d;h<g.top?e.top=g.top-h:i>g.top+g.height&&(e.top=g.top+g.height-i)}else{var j=b.left-f,k=b.left+f+c;j<g.left?e.left=g.left-j:k>g.right&&(e.left=g.left+g.width-k)}return e},c.prototype.getTitle=function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},c.prototype.getUID=function(a){do a+=~~(1e6*Math.random());while(document.getElementById(a));return a},c.prototype.tip=function(){if(!this.$tip&&(this.$tip=a(this.options.template),1!=this.$tip.length))throw new Error(this.type+" `template` option must consist of exactly 1 top-level element!");return this.$tip},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},c.prototype.enable=function(){this.enabled=!0},c.prototype.disable=function(){this.enabled=!1},c.prototype.toggleEnabled=function(){this.enabled=!this.enabled},c.prototype.toggle=function(b){var c=this;b&&(c=a(b.currentTarget).data("bs."+this.type),c||(c=new this.constructor(b.currentTarget,this.getDelegateOptions()),a(b.currentTarget).data("bs."+this.type,c))),b?(c.inState.click=!c.inState.click,c.isInStateTrue()?c.enter(c):c.leave(c)):c.tip().hasClass("in")?c.leave(c):c.enter(c)},c.prototype.destroy=function(){var a=this;clearTimeout(this.timeout),this.hide(function(){a.$element.off("."+a.type).removeData("bs."+a.type),a.$tip&&a.$tip.detach(),a.$tip=null,a.$arrow=null,a.$viewport=null})};var d=a.fn.tooltip;a.fn.tooltip=b,a.fn.tooltip.Constructor=c,a.fn.tooltip.noConflict=function(){return a.fn.tooltip=d,this}}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.popover"),f="object"==typeof b&&b;(e||!/destroy|hide/.test(b))&&(e||d.data("bs.popover",e=new c(this,f)),"string"==typeof b&&e[b]())})}var c=function(a,b){this.init("popover",a,b)};if(!a.fn.tooltip)throw new Error("Popover requires tooltip.js");c.VERSION="3.3.6",c.DEFAULTS=a.extend({},a.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),c.prototype=a.extend({},a.fn.tooltip.Constructor.prototype),c.prototype.constructor=c,c.prototype.getDefaults=function(){return c.DEFAULTS},c.prototype.setContent=function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content").children().detach().end()[this.options.html?"string"==typeof c?"html":"append":"text"](c),a.removeClass("fade top bottom left right in"),a.find(".popover-title").html()||a.find(".popover-title").hide()},c.prototype.hasContent=function(){return this.getTitle()||this.getContent()},c.prototype.getContent=function(){var a=this.$element,b=this.options;return a.attr("data-content")||("function"==typeof b.content?b.content.call(a[0]):b.content)},c.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")};var d=a.fn.popover;a.fn.popover=b,a.fn.popover.Constructor=c,a.fn.popover.noConflict=function(){return a.fn.popover=d,this}}(jQuery),+function(a){"use strict";function b(c,d){this.$body=a(document.body),this.$scrollElement=a(a(c).is(document.body)?window:c),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||"")+" .nav li > a",this.offsets=[],this.targets=[],this.activeTarget=null,this.scrollHeight=0,this.$scrollElement.on("scroll.bs.scrollspy",a.proxy(this.process,this)),this.refresh(),this.process()}function c(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})}b.VERSION="3.3.6",b.DEFAULTS={offset:10},b.prototype.getScrollHeight=function(){return this.$scrollElement[0].scrollHeight||Math.max(this.$body[0].scrollHeight,document.documentElement.scrollHeight)},b.prototype.refresh=function(){var b=this,c="offset",d=0;this.offsets=[],this.targets=[],this.scrollHeight=this.getScrollHeight(),a.isWindow(this.$scrollElement[0])||(c="position",d=this.$scrollElement.scrollTop()),this.$body.find(this.selector).map(function(){var b=a(this),e=b.data("target")||b.attr("href"),f=/^#./.test(e)&&a(e);return f&&f.length&&f.is(":visible")&&[[f[c]().top+d,e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){b.offsets.push(this[0]),b.targets.push(this[1])})},b.prototype.process=function(){var a,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.getScrollHeight(),d=this.options.offset+c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;if(this.scrollHeight!=c&&this.refresh(),b>=d)return g!=(a=f[f.length-1])&&this.activate(a);if(g&&b<e[0])return this.activeTarget=null,this.clear();for(a=e.length;a--;)g!=f[a]&&b>=e[a]&&(void 0===e[a+1]||b<e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){this.activeTarget=b,this.clear();var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");
d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),d.trigger("activate.bs.scrollspy")},b.prototype.clear=function(){a(this.selector).parentsUntil(this.options.target,".active").removeClass("active")};var d=a.fn.scrollspy;a.fn.scrollspy=c,a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=d,this},a(window).on("load.bs.scrollspy.data-api",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);c.call(b,b.data())})})}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.tab");e||d.data("bs.tab",e=new c(this)),"string"==typeof b&&e[b]()})}var c=function(b){this.element=a(b)};c.VERSION="3.3.6",c.TRANSITION_DURATION=150,c.prototype.show=function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){var e=c.find(".active:last a"),f=a.Event("hide.bs.tab",{relatedTarget:b[0]}),g=a.Event("show.bs.tab",{relatedTarget:e[0]});if(e.trigger(f),b.trigger(g),!g.isDefaultPrevented()&&!f.isDefaultPrevented()){var h=a(d);this.activate(b.closest("li"),c),this.activate(h,h.parent(),function(){e.trigger({type:"hidden.bs.tab",relatedTarget:b[0]}),b.trigger({type:"shown.bs.tab",relatedTarget:e[0]})})}}},c.prototype.activate=function(b,d,e){function f(){g.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),b.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),h?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu").length&&b.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),e&&e()}var g=d.find("> .active"),h=e&&a.support.transition&&(g.length&&g.hasClass("fade")||!!d.find("> .fade").length);g.length&&h?g.one("bsTransitionEnd",f).emulateTransitionEnd(c.TRANSITION_DURATION):f(),g.removeClass("in")};var d=a.fn.tab;a.fn.tab=b,a.fn.tab.Constructor=c,a.fn.tab.noConflict=function(){return a.fn.tab=d,this};var e=function(c){c.preventDefault(),b.call(a(this),"show")};a(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',e).on("click.bs.tab.data-api",'[data-toggle="pill"]',e)}(jQuery),+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.affix"),f="object"==typeof b&&b;e||d.data("bs.affix",e=new c(this,f)),"string"==typeof b&&e[b]()})}var c=function(b,d){this.options=a.extend({},c.DEFAULTS,d),this.$target=a(this.options.target).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(b),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};c.VERSION="3.3.6",c.RESET="affix affix-top affix-bottom",c.DEFAULTS={offset:0,target:window},c.prototype.getState=function(a,b,c,d){var e=this.$target.scrollTop(),f=this.$element.offset(),g=this.$target.height();if(null!=c&&"top"==this.affixed)return c>e?"top":!1;if("bottom"==this.affixed)return null!=c?e+this.unpin<=f.top?!1:"bottom":a-d>=e+g?!1:"bottom";var h=null==this.affixed,i=h?e:f.top,j=h?g:b;return null!=c&&c>=e?"top":null!=d&&i+j>=a-d?"bottom":!1},c.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(c.RESET).addClass("affix");var a=this.$target.scrollTop(),b=this.$element.offset();return this.pinnedOffset=b.top-a},c.prototype.checkPositionWithEventLoop=function(){setTimeout(a.proxy(this.checkPosition,this),1)},c.prototype.checkPosition=function(){if(this.$element.is(":visible")){var b=this.$element.height(),d=this.options.offset,e=d.top,f=d.bottom,g=Math.max(a(document).height(),a(document.body).height());"object"!=typeof d&&(f=e=d),"function"==typeof e&&(e=d.top(this.$element)),"function"==typeof f&&(f=d.bottom(this.$element));var h=this.getState(g,b,e,f);if(this.affixed!=h){null!=this.unpin&&this.$element.css("top","");var i="affix"+(h?"-"+h:""),j=a.Event(i+".bs.affix");if(this.$element.trigger(j),j.isDefaultPrevented())return;this.affixed=h,this.unpin="bottom"==h?this.getPinnedOffset():null,this.$element.removeClass(c.RESET).addClass(i).trigger(i.replace("affix","affixed")+".bs.affix")}"bottom"==h&&this.$element.offset({top:g-b-f})}};var d=a.fn.affix;a.fn.affix=b,a.fn.affix.Constructor=c,a.fn.affix.noConflict=function(){return a.fn.affix=d,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var c=a(this),d=c.data();d.offset=d.offset||{},null!=d.offsetBottom&&(d.offset.bottom=d.offsetBottom),null!=d.offsetTop&&(d.offset.top=d.offsetTop),b.call(c,d)})})}(jQuery);;/* node_modules/bootstrap-dialog/dist/js/bootstrap-dialog.min.js */
!function(t,e){"use strict";"undefined"!=typeof module&&module.exports?module.exports=e(require("jquery"),require("bootstrap")):"function"==typeof define&&define.amd?define("bootstrap-dialog",["jquery","bootstrap"],function(t){return e(t)}):t.BootstrapDialog=e(t.jQuery)}(this,function(t){"use strict";var e=t.fn.modal.Constructor,n=function(t,n){e.call(this,t,n)};n.getModalVersion=function(){var e=null;return e="undefined"==typeof t.fn.modal.Constructor.VERSION?"v3.1":/3\.2\.\d+/.test(t.fn.modal.Constructor.VERSION)?"v3.2":/3\.3\.[1,2]/.test(t.fn.modal.Constructor.VERSION)?"v3.3":"v3.3.4"},n.ORIGINAL_BODY_PADDING=parseInt(t("body").css("padding-right")||0,10),n.METHODS_TO_OVERRIDE={},n.METHODS_TO_OVERRIDE["v3.1"]={},n.METHODS_TO_OVERRIDE["v3.2"]={hide:function(e){if(e&&e.preventDefault(),e=t.Event("hide.bs.modal"),this.$element.trigger(e),this.isShown&&!e.isDefaultPrevented()){this.isShown=!1;var n=this.getGlobalOpenedDialogs();0===n.length&&this.$body.removeClass("modal-open"),this.resetScrollbar(),this.escape(),t(document).off("focusin.bs.modal"),this.$element.removeClass("in").attr("aria-hidden",!0).off("click.dismiss.bs.modal"),t.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",t.proxy(this.hideModal,this)).emulateTransitionEnd(300):this.hideModal()}}},n.METHODS_TO_OVERRIDE["v3.3"]={setScrollbar:function(){var t=n.ORIGINAL_BODY_PADDING;this.bodyIsOverflowing&&this.$body.css("padding-right",t+this.scrollbarWidth)},resetScrollbar:function(){var t=this.getGlobalOpenedDialogs();0===t.length&&this.$body.css("padding-right",n.ORIGINAL_BODY_PADDING)},hideModal:function(){this.$element.hide(),this.backdrop(t.proxy(function(){var t=this.getGlobalOpenedDialogs();0===t.length&&this.$body.removeClass("modal-open"),this.resetAdjustments(),this.resetScrollbar(),this.$element.trigger("hidden.bs.modal")},this))}},n.METHODS_TO_OVERRIDE["v3.3.4"]=t.extend({},n.METHODS_TO_OVERRIDE["v3.3"]),n.prototype={constructor:n,getGlobalOpenedDialogs:function(){var e=[];return t.each(o.dialogs,function(t,n){n.isRealized()&&n.isOpened()&&e.push(n)}),e}},n.prototype=t.extend(n.prototype,e.prototype,n.METHODS_TO_OVERRIDE[n.getModalVersion()]);var o=function(e){this.defaultOptions=t.extend(!0,{id:o.newGuid(),buttons:[],data:{},onshow:null,onshown:null,onhide:null,onhidden:null},o.defaultOptions),this.indexedButtons={},this.registeredButtonHotkeys={},this.draggableData={isMouseDown:!1,mouseOffset:{}},this.realized=!1,this.opened=!1,this.initOptions(e),this.holdThisInstance()};return o.BootstrapDialogModal=n,o.NAMESPACE="bootstrap-dialog",o.TYPE_DEFAULT="type-default",o.TYPE_INFO="type-info",o.TYPE_PRIMARY="type-primary",o.TYPE_SUCCESS="type-success",o.TYPE_WARNING="type-warning",o.TYPE_DANGER="type-danger",o.DEFAULT_TEXTS={},o.DEFAULT_TEXTS[o.TYPE_DEFAULT]="Information",o.DEFAULT_TEXTS[o.TYPE_INFO]="Information",o.DEFAULT_TEXTS[o.TYPE_PRIMARY]="Information",o.DEFAULT_TEXTS[o.TYPE_SUCCESS]="Success",o.DEFAULT_TEXTS[o.TYPE_WARNING]="Warning",o.DEFAULT_TEXTS[o.TYPE_DANGER]="Danger",o.DEFAULT_TEXTS.OK="OK",o.DEFAULT_TEXTS.CANCEL="Cancel",o.DEFAULT_TEXTS.CONFIRM="Confirmation",o.SIZE_NORMAL="size-normal",o.SIZE_SMALL="size-small",o.SIZE_WIDE="size-wide",o.SIZE_LARGE="size-large",o.BUTTON_SIZES={},o.BUTTON_SIZES[o.SIZE_NORMAL]="",o.BUTTON_SIZES[o.SIZE_SMALL]="",o.BUTTON_SIZES[o.SIZE_WIDE]="",o.BUTTON_SIZES[o.SIZE_LARGE]="btn-lg",o.ICON_SPINNER="glyphicon glyphicon-asterisk",o.defaultOptions={type:o.TYPE_PRIMARY,size:o.SIZE_NORMAL,cssClass:"",title:null,message:null,nl2br:!0,closable:!0,closeByBackdrop:!0,closeByKeyboard:!0,spinicon:o.ICON_SPINNER,autodestroy:!0,draggable:!1,animate:!0,description:"",tabindex:-1},o.configDefaultOptions=function(e){o.defaultOptions=t.extend(!0,o.defaultOptions,e)},o.dialogs={},o.openAll=function(){t.each(o.dialogs,function(t,e){e.open()})},o.closeAll=function(){t.each(o.dialogs,function(t,e){e.close()})},o.moveFocus=function(){var e=null;t.each(o.dialogs,function(t,n){e=n}),null!==e&&e.isRealized()&&e.getModal().focus()},o.METHODS_TO_OVERRIDE={},o.METHODS_TO_OVERRIDE["v3.1"]={handleModalBackdropEvent:function(){return this.getModal().on("click",{dialog:this},function(t){t.target===this&&t.data.dialog.isClosable()&&t.data.dialog.canCloseByBackdrop()&&t.data.dialog.close()}),this},updateZIndex:function(){var e=1040,n=1050,i=0;t.each(o.dialogs,function(t,e){i++});var s=this.getModal(),a=s.data("bs.modal").$backdrop;return s.css("z-index",n+20*(i-1)),a.css("z-index",e+20*(i-1)),this},open:function(){return!this.isRealized()&&this.realize(),this.getModal().modal("show"),this.updateZIndex(),this}},o.METHODS_TO_OVERRIDE["v3.2"]={handleModalBackdropEvent:o.METHODS_TO_OVERRIDE["v3.1"].handleModalBackdropEvent,updateZIndex:o.METHODS_TO_OVERRIDE["v3.1"].updateZIndex,open:o.METHODS_TO_OVERRIDE["v3.1"].open},o.METHODS_TO_OVERRIDE["v3.3"]={},o.METHODS_TO_OVERRIDE["v3.3.4"]=t.extend({},o.METHODS_TO_OVERRIDE["v3.1"]),o.prototype={constructor:o,initOptions:function(e){return this.options=t.extend(!0,this.defaultOptions,e),this},holdThisInstance:function(){return o.dialogs[this.getId()]=this,this},initModalStuff:function(){return this.setModal(this.createModal()).setModalDialog(this.createModalDialog()).setModalContent(this.createModalContent()).setModalHeader(this.createModalHeader()).setModalBody(this.createModalBody()).setModalFooter(this.createModalFooter()),this.getModal().append(this.getModalDialog()),this.getModalDialog().append(this.getModalContent()),this.getModalContent().append(this.getModalHeader()).append(this.getModalBody()).append(this.getModalFooter()),this},createModal:function(){var e=t('<div class="modal" role="dialog" aria-hidden="true"></div>');return e.prop("id",this.getId()),e.attr("aria-labelledby",this.getId()+"_title"),e},getModal:function(){return this.$modal},setModal:function(t){return this.$modal=t,this},createModalDialog:function(){return t('<div class="modal-dialog"></div>')},getModalDialog:function(){return this.$modalDialog},setModalDialog:function(t){return this.$modalDialog=t,this},createModalContent:function(){return t('<div class="modal-content"></div>')},getModalContent:function(){return this.$modalContent},setModalContent:function(t){return this.$modalContent=t,this},createModalHeader:function(){return t('<div class="modal-header"></div>')},getModalHeader:function(){return this.$modalHeader},setModalHeader:function(t){return this.$modalHeader=t,this},createModalBody:function(){return t('<div class="modal-body"></div>')},getModalBody:function(){return this.$modalBody},setModalBody:function(t){return this.$modalBody=t,this},createModalFooter:function(){return t('<div class="modal-footer"></div>')},getModalFooter:function(){return this.$modalFooter},setModalFooter:function(t){return this.$modalFooter=t,this},createDynamicContent:function(t){var e=null;return e="function"==typeof t?t.call(t,this):t,"string"==typeof e&&(e=this.formatStringContent(e)),e},formatStringContent:function(t){return this.options.nl2br?t.replace(/\r\n/g,"<br />").replace(/[\r\n]/g,"<br />"):t},setData:function(t,e){return this.options.data[t]=e,this},getData:function(t){return this.options.data[t]},setId:function(t){return this.options.id=t,this},getId:function(){return this.options.id},getType:function(){return this.options.type},setType:function(t){return this.options.type=t,this.updateType(),this},updateType:function(){if(this.isRealized()){var t=[o.TYPE_DEFAULT,o.TYPE_INFO,o.TYPE_PRIMARY,o.TYPE_SUCCESS,o.TYPE_WARNING,o.TYPE_DANGER];this.getModal().removeClass(t.join(" ")).addClass(this.getType())}return this},getSize:function(){return this.options.size},setSize:function(t){return this.options.size=t,this.updateSize(),this},updateSize:function(){if(this.isRealized()){var e=this;this.getModal().removeClass(o.SIZE_NORMAL).removeClass(o.SIZE_SMALL).removeClass(o.SIZE_WIDE).removeClass(o.SIZE_LARGE),this.getModal().addClass(this.getSize()),this.getModalDialog().removeClass("modal-sm"),this.getSize()===o.SIZE_SMALL&&this.getModalDialog().addClass("modal-sm"),this.getModalDialog().removeClass("modal-lg"),this.getSize()===o.SIZE_WIDE&&this.getModalDialog().addClass("modal-lg"),t.each(this.options.buttons,function(n,o){var i=e.getButton(o.id),s=["btn-lg","btn-sm","btn-xs"],a=!1;if("string"==typeof o.cssClass){var d=o.cssClass.split(" ");t.each(d,function(e,n){-1!==t.inArray(n,s)&&(a=!0)})}a||(i.removeClass(s.join(" ")),i.addClass(e.getButtonSize()))})}return this},getCssClass:function(){return this.options.cssClass},setCssClass:function(t){return this.options.cssClass=t,this},getTitle:function(){return this.options.title},setTitle:function(t){return this.options.title=t,this.updateTitle(),this},updateTitle:function(){if(this.isRealized()){var t=null!==this.getTitle()?this.createDynamicContent(this.getTitle()):this.getDefaultText();this.getModalHeader().find("."+this.getNamespace("title")).html("").append(t).prop("id",this.getId()+"_title")}return this},getMessage:function(){return this.options.message},setMessage:function(t){return this.options.message=t,this.updateMessage(),this},updateMessage:function(){if(this.isRealized()){var t=this.createDynamicContent(this.getMessage());this.getModalBody().find("."+this.getNamespace("message")).html("").append(t)}return this},isClosable:function(){return this.options.closable},setClosable:function(t){return this.options.closable=t,this.updateClosable(),this},setCloseByBackdrop:function(t){return this.options.closeByBackdrop=t,this},canCloseByBackdrop:function(){return this.options.closeByBackdrop},setCloseByKeyboard:function(t){return this.options.closeByKeyboard=t,this},canCloseByKeyboard:function(){return this.options.closeByKeyboard},isAnimate:function(){return this.options.animate},setAnimate:function(t){return this.options.animate=t,this},updateAnimate:function(){return this.isRealized()&&this.getModal().toggleClass("fade",this.isAnimate()),this},getSpinicon:function(){return this.options.spinicon},setSpinicon:function(t){return this.options.spinicon=t,this},addButton:function(t){return this.options.buttons.push(t),this},addButtons:function(e){var n=this;return t.each(e,function(t,e){n.addButton(e)}),this},getButtons:function(){return this.options.buttons},setButtons:function(t){return this.options.buttons=t,this.updateButtons(),this},getButton:function(t){return"undefined"!=typeof this.indexedButtons[t]?this.indexedButtons[t]:null},getButtonSize:function(){return"undefined"!=typeof o.BUTTON_SIZES[this.getSize()]?o.BUTTON_SIZES[this.getSize()]:""},updateButtons:function(){return this.isRealized()&&(0===this.getButtons().length?this.getModalFooter().hide():this.getModalFooter().show().find("."+this.getNamespace("footer")).html("").append(this.createFooterButtons())),this},isAutodestroy:function(){return this.options.autodestroy},setAutodestroy:function(t){this.options.autodestroy=t},getDescription:function(){return this.options.description},setDescription:function(t){return this.options.description=t,this},setTabindex:function(t){return this.options.tabindex=t,this},getTabindex:function(){return this.options.tabindex},updateTabindex:function(){return this.isRealized()&&this.getModal().attr("tabindex",this.getTabindex()),this},getDefaultText:function(){return o.DEFAULT_TEXTS[this.getType()]},getNamespace:function(t){return o.NAMESPACE+"-"+t},createHeaderContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("header")),e.append(this.createTitleContent()),e.prepend(this.createCloseButton()),e},createTitleContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("title")),e},createCloseButton:function(){var e=t("<div></div>");e.addClass(this.getNamespace("close-button"));var n=t('<button class="close">&times;</button>');return e.append(n),e.on("click",{dialog:this},function(t){t.data.dialog.close()}),e},createBodyContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("body")),e.append(this.createMessageContent()),e},createMessageContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("message")),e},createFooterContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("footer")),e},createFooterButtons:function(){var e=this,n=t("<div></div>");return n.addClass(this.getNamespace("footer-buttons")),this.indexedButtons={},t.each(this.options.buttons,function(t,i){i.id||(i.id=o.newGuid());var s=e.createButton(i);e.indexedButtons[i.id]=s,n.append(s)}),n},createButton:function(e){var n=t('<button class="btn"></button>');return n.prop("id",e.id),n.data("button",e),"undefined"!=typeof e.icon&&""!==t.trim(e.icon)&&n.append(this.createButtonIcon(e.icon)),"undefined"!=typeof e.label&&n.append(e.label),n.addClass("undefined"!=typeof e.cssClass&&""!==t.trim(e.cssClass)?e.cssClass:"btn-default"),"undefined"!=typeof e.hotkey&&(this.registeredButtonHotkeys[e.hotkey]=n),n.on("click",{dialog:this,$button:n,button:e},function(t){var e=t.data.dialog,n=t.data.$button,o=n.data("button");"function"==typeof o.action&&o.action.call(n,e,t),o.autospin&&n.toggleSpin(!0)}),this.enhanceButton(n),n},enhanceButton:function(t){return t.dialog=this,t.toggleEnable=function(t){var e=this;return"undefined"!=typeof t?e.prop("disabled",!t).toggleClass("disabled",!t):e.prop("disabled",!e.prop("disabled")),e},t.enable=function(){var t=this;return t.toggleEnable(!0),t},t.disable=function(){var t=this;return t.toggleEnable(!1),t},t.toggleSpin=function(e){var n=this,o=n.dialog,i=n.find("."+o.getNamespace("button-icon"));return"undefined"==typeof e&&(e=!(t.find(".icon-spin").length>0)),e?(i.hide(),t.prepend(o.createButtonIcon(o.getSpinicon()).addClass("icon-spin"))):(i.show(),t.find(".icon-spin").remove()),n},t.spin=function(){var t=this;return t.toggleSpin(!0),t},t.stopSpin=function(){var t=this;return t.toggleSpin(!1),t},this},createButtonIcon:function(e){var n=t("<span></span>");return n.addClass(this.getNamespace("button-icon")).addClass(e),n},enableButtons:function(e){return t.each(this.indexedButtons,function(t,n){n.toggleEnable(e)}),this},updateClosable:function(){return this.isRealized()&&this.getModalHeader().find("."+this.getNamespace("close-button")).toggle(this.isClosable()),this},onShow:function(t){return this.options.onshow=t,this},onShown:function(t){return this.options.onshown=t,this},onHide:function(t){return this.options.onhide=t,this},onHidden:function(t){return this.options.onhidden=t,this},isRealized:function(){return this.realized},setRealized:function(t){return this.realized=t,this},isOpened:function(){return this.opened},setOpened:function(t){return this.opened=t,this},handleModalEvents:function(){return this.getModal().on("show.bs.modal",{dialog:this},function(t){var e=t.data.dialog;if(e.setOpened(!0),e.isModalEvent(t)&&"function"==typeof e.options.onshow){var n=e.options.onshow(e);return n===!1&&e.setOpened(!1),n}}),this.getModal().on("shown.bs.modal",{dialog:this},function(t){var e=t.data.dialog;e.isModalEvent(t)&&"function"==typeof e.options.onshown&&e.options.onshown(e)}),this.getModal().on("hide.bs.modal",{dialog:this},function(t){var e=t.data.dialog;if(e.setOpened(!1),e.isModalEvent(t)&&"function"==typeof e.options.onhide){var n=e.options.onhide(e);return n===!1&&e.setOpened(!0),n}}),this.getModal().on("hidden.bs.modal",{dialog:this},function(e){var n=e.data.dialog;n.isModalEvent(e)&&"function"==typeof n.options.onhidden&&n.options.onhidden(n),n.isAutodestroy()&&(delete o.dialogs[n.getId()],t(this).remove()),o.moveFocus()}),this.handleModalBackdropEvent(),this.getModal().on("keyup",{dialog:this},function(t){27===t.which&&t.data.dialog.isClosable()&&t.data.dialog.canCloseByKeyboard()&&t.data.dialog.close()}),this.getModal().on("keyup",{dialog:this},function(e){var n=e.data.dialog;if("undefined"!=typeof n.registeredButtonHotkeys[e.which]){var o=t(n.registeredButtonHotkeys[e.which]);!o.prop("disabled")&&o.focus().trigger("click")}}),this},handleModalBackdropEvent:function(){return this.getModal().on("click",{dialog:this},function(e){t(e.target).hasClass("modal-backdrop")&&e.data.dialog.isClosable()&&e.data.dialog.canCloseByBackdrop()&&e.data.dialog.close()}),this},isModalEvent:function(t){return"undefined"!=typeof t.namespace&&"bs.modal"===t.namespace},makeModalDraggable:function(){return this.options.draggable&&(this.getModalHeader().addClass(this.getNamespace("draggable")).on("mousedown",{dialog:this},function(t){var e=t.data.dialog;e.draggableData.isMouseDown=!0;var n=e.getModalDialog().offset();e.draggableData.mouseOffset={top:t.clientY-n.top,left:t.clientX-n.left}}),this.getModal().on("mouseup mouseleave",{dialog:this},function(t){t.data.dialog.draggableData.isMouseDown=!1}),t("body").on("mousemove",{dialog:this},function(t){var e=t.data.dialog;e.draggableData.isMouseDown&&e.getModalDialog().offset({top:t.clientY-e.draggableData.mouseOffset.top,left:t.clientX-e.draggableData.mouseOffset.left})})),this},realize:function(){return this.initModalStuff(),this.getModal().addClass(o.NAMESPACE).addClass(this.getCssClass()),this.updateSize(),this.getDescription()&&this.getModal().attr("aria-describedby",this.getDescription()),this.getModalFooter().append(this.createFooterContent()),this.getModalHeader().append(this.createHeaderContent()),this.getModalBody().append(this.createBodyContent()),this.getModal().data("bs.modal",new n(this.getModal(),{backdrop:"static",keyboard:!1,show:!1})),this.makeModalDraggable(),this.handleModalEvents(),this.setRealized(!0),this.updateButtons(),this.updateType(),this.updateTitle(),this.updateMessage(),this.updateClosable(),this.updateAnimate(),this.updateSize(),this.updateTabindex(),this},open:function(){return!this.isRealized()&&this.realize(),this.getModal().modal("show"),this},close:function(){return this.getModal().modal("hide"),this}},o.prototype=t.extend(o.prototype,o.METHODS_TO_OVERRIDE[n.getModalVersion()]),o.newGuid=function(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(t){var e=16*Math.random()|0,n="x"===t?e:3&e|8;return n.toString(16)})},o.show=function(t){return new o(t).open()},o.alert=function(){var e={},n={type:o.TYPE_PRIMARY,title:null,message:null,closable:!1,draggable:!1,buttonLabel:o.DEFAULT_TEXTS.OK,callback:null};return e="object"==typeof arguments[0]&&arguments[0].constructor==={}.constructor?t.extend(!0,n,arguments[0]):t.extend(!0,n,{message:arguments[0],callback:"undefined"!=typeof arguments[1]?arguments[1]:null}),new o({type:e.type,title:e.title,message:e.message,closable:e.closable,draggable:e.draggable,data:{callback:e.callback},onhide:function(t){!t.getData("btnClicked")&&t.isClosable()&&"function"==typeof t.getData("callback")&&t.getData("callback")(!1)},buttons:[{label:e.buttonLabel,action:function(t){t.setData("btnClicked",!0),"function"==typeof t.getData("callback")&&t.getData("callback")(!0),t.close()}}]}).open()},o.confirm=function(){var e={},n={type:o.TYPE_PRIMARY,title:null,message:null,closable:!1,draggable:!1,btnCancelLabel:o.DEFAULT_TEXTS.CANCEL,btnOKLabel:o.DEFAULT_TEXTS.OK,btnOKClass:null,callback:null};return e="object"==typeof arguments[0]&&arguments[0].constructor==={}.constructor?t.extend(!0,n,arguments[0]):t.extend(!0,n,{message:arguments[0],closable:!1,buttonLabel:o.DEFAULT_TEXTS.OK,callback:"undefined"!=typeof arguments[1]?arguments[1]:null}),null===e.btnOKClass&&(e.btnOKClass=["btn",e.type.split("-")[1]].join("-")),new o({type:e.type,title:e.title,message:e.message,closable:e.closable,draggable:e.draggable,data:{callback:e.callback},buttons:[{label:e.btnCancelLabel,action:function(t){"function"==typeof t.getData("callback")&&t.getData("callback")(!1),t.close()}},{label:e.btnOKLabel,cssClass:e.btnOKClass,action:function(t){"function"==typeof t.getData("callback")&&t.getData("callback")(!0),t.close()}}]}).open()},o.warning=function(t,e){return new o({type:o.TYPE_WARNING,message:t}).open()},o.danger=function(t,e){return new o({type:o.TYPE_DANGER,message:t}).open()},o.success=function(t,e){return new o({type:o.TYPE_SUCCESS,message:t}).open()},o});;/* node_modules/bootstrap-dialog/examples/assets/bootstrap-dialog/js/bootstrap-dialog.min.js */
!function(t,e){"use strict";"undefined"!=typeof module&&module.exports?module.exports=e(require("jquery"),require("bootstrap")):"function"==typeof define&&define.amd?define("bootstrap-dialog",["jquery","bootstrap"],function(t){return e(t)}):t.BootstrapDialog=e(t.jQuery)}(this,function(t){"use strict";var e=t.fn.modal.Constructor,n=function(t,n){e.call(this,t,n)};n.getModalVersion=function(){var e=null;return e="undefined"==typeof t.fn.modal.Constructor.VERSION?"v3.1":/3\.2\.\d+/.test(t.fn.modal.Constructor.VERSION)?"v3.2":/3\.3\.[1,2]/.test(t.fn.modal.Constructor.VERSION)?"v3.3":"v3.3.4"},n.ORIGINAL_BODY_PADDING=parseInt(t("body").css("padding-right")||0,10),n.METHODS_TO_OVERRIDE={},n.METHODS_TO_OVERRIDE["v3.1"]={},n.METHODS_TO_OVERRIDE["v3.2"]={hide:function(e){if(e&&e.preventDefault(),e=t.Event("hide.bs.modal"),this.$element.trigger(e),this.isShown&&!e.isDefaultPrevented()){this.isShown=!1;var n=this.getGlobalOpenedDialogs();0===n.length&&this.$body.removeClass("modal-open"),this.resetScrollbar(),this.escape(),t(document).off("focusin.bs.modal"),this.$element.removeClass("in").attr("aria-hidden",!0).off("click.dismiss.bs.modal"),t.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",t.proxy(this.hideModal,this)).emulateTransitionEnd(300):this.hideModal()}}},n.METHODS_TO_OVERRIDE["v3.3"]={setScrollbar:function(){var t=n.ORIGINAL_BODY_PADDING;this.bodyIsOverflowing&&this.$body.css("padding-right",t+this.scrollbarWidth)},resetScrollbar:function(){var t=this.getGlobalOpenedDialogs();0===t.length&&this.$body.css("padding-right",n.ORIGINAL_BODY_PADDING)},hideModal:function(){this.$element.hide(),this.backdrop(t.proxy(function(){var t=this.getGlobalOpenedDialogs();0===t.length&&this.$body.removeClass("modal-open"),this.resetAdjustments(),this.resetScrollbar(),this.$element.trigger("hidden.bs.modal")},this))}},n.METHODS_TO_OVERRIDE["v3.3.4"]=t.extend({},n.METHODS_TO_OVERRIDE["v3.3"]),n.prototype={constructor:n,getGlobalOpenedDialogs:function(){var e=[];return t.each(o.dialogs,function(t,n){n.isRealized()&&n.isOpened()&&e.push(n)}),e}},n.prototype=t.extend(n.prototype,e.prototype,n.METHODS_TO_OVERRIDE[n.getModalVersion()]);var o=function(e){this.defaultOptions=t.extend(!0,{id:o.newGuid(),buttons:[],data:{},onshow:null,onshown:null,onhide:null,onhidden:null},o.defaultOptions),this.indexedButtons={},this.registeredButtonHotkeys={},this.draggableData={isMouseDown:!1,mouseOffset:{}},this.realized=!1,this.opened=!1,this.initOptions(e),this.holdThisInstance()};return o.BootstrapDialogModal=n,o.NAMESPACE="bootstrap-dialog",o.TYPE_DEFAULT="type-default",o.TYPE_INFO="type-info",o.TYPE_PRIMARY="type-primary",o.TYPE_SUCCESS="type-success",o.TYPE_WARNING="type-warning",o.TYPE_DANGER="type-danger",o.DEFAULT_TEXTS={},o.DEFAULT_TEXTS[o.TYPE_DEFAULT]="Information",o.DEFAULT_TEXTS[o.TYPE_INFO]="Information",o.DEFAULT_TEXTS[o.TYPE_PRIMARY]="Information",o.DEFAULT_TEXTS[o.TYPE_SUCCESS]="Success",o.DEFAULT_TEXTS[o.TYPE_WARNING]="Warning",o.DEFAULT_TEXTS[o.TYPE_DANGER]="Danger",o.DEFAULT_TEXTS.OK="OK",o.DEFAULT_TEXTS.CANCEL="Cancel",o.DEFAULT_TEXTS.CONFIRM="Confirmation",o.SIZE_NORMAL="size-normal",o.SIZE_SMALL="size-small",o.SIZE_WIDE="size-wide",o.SIZE_LARGE="size-large",o.BUTTON_SIZES={},o.BUTTON_SIZES[o.SIZE_NORMAL]="",o.BUTTON_SIZES[o.SIZE_SMALL]="",o.BUTTON_SIZES[o.SIZE_WIDE]="",o.BUTTON_SIZES[o.SIZE_LARGE]="btn-lg",o.ICON_SPINNER="glyphicon glyphicon-asterisk",o.defaultOptions={type:o.TYPE_PRIMARY,size:o.SIZE_NORMAL,cssClass:"",title:null,message:null,nl2br:!0,closable:!0,closeByBackdrop:!0,closeByKeyboard:!0,spinicon:o.ICON_SPINNER,autodestroy:!0,draggable:!1,animate:!0,description:"",tabindex:-1},o.configDefaultOptions=function(e){o.defaultOptions=t.extend(!0,o.defaultOptions,e)},o.dialogs={},o.openAll=function(){t.each(o.dialogs,function(t,e){e.open()})},o.closeAll=function(){t.each(o.dialogs,function(t,e){e.close()})},o.moveFocus=function(){var e=null;t.each(o.dialogs,function(t,n){e=n}),null!==e&&e.isRealized()&&e.getModal().focus()},o.METHODS_TO_OVERRIDE={},o.METHODS_TO_OVERRIDE["v3.1"]={handleModalBackdropEvent:function(){return this.getModal().on("click",{dialog:this},function(t){t.target===this&&t.data.dialog.isClosable()&&t.data.dialog.canCloseByBackdrop()&&t.data.dialog.close()}),this},updateZIndex:function(){var e=1040,n=1050,i=0;t.each(o.dialogs,function(t,e){i++});var s=this.getModal(),a=s.data("bs.modal").$backdrop;return s.css("z-index",n+20*(i-1)),a.css("z-index",e+20*(i-1)),this},open:function(){return!this.isRealized()&&this.realize(),this.getModal().modal("show"),this.updateZIndex(),this}},o.METHODS_TO_OVERRIDE["v3.2"]={handleModalBackdropEvent:o.METHODS_TO_OVERRIDE["v3.1"].handleModalBackdropEvent,updateZIndex:o.METHODS_TO_OVERRIDE["v3.1"].updateZIndex,open:o.METHODS_TO_OVERRIDE["v3.1"].open},o.METHODS_TO_OVERRIDE["v3.3"]={},o.METHODS_TO_OVERRIDE["v3.3.4"]=t.extend({},o.METHODS_TO_OVERRIDE["v3.1"]),o.prototype={constructor:o,initOptions:function(e){return this.options=t.extend(!0,this.defaultOptions,e),this},holdThisInstance:function(){return o.dialogs[this.getId()]=this,this},initModalStuff:function(){return this.setModal(this.createModal()).setModalDialog(this.createModalDialog()).setModalContent(this.createModalContent()).setModalHeader(this.createModalHeader()).setModalBody(this.createModalBody()).setModalFooter(this.createModalFooter()),this.getModal().append(this.getModalDialog()),this.getModalDialog().append(this.getModalContent()),this.getModalContent().append(this.getModalHeader()).append(this.getModalBody()).append(this.getModalFooter()),this},createModal:function(){var e=t('<div class="modal" role="dialog" aria-hidden="true"></div>');return e.prop("id",this.getId()),e.attr("aria-labelledby",this.getId()+"_title"),e},getModal:function(){return this.$modal},setModal:function(t){return this.$modal=t,this},createModalDialog:function(){return t('<div class="modal-dialog"></div>')},getModalDialog:function(){return this.$modalDialog},setModalDialog:function(t){return this.$modalDialog=t,this},createModalContent:function(){return t('<div class="modal-content"></div>')},getModalContent:function(){return this.$modalContent},setModalContent:function(t){return this.$modalContent=t,this},createModalHeader:function(){return t('<div class="modal-header"></div>')},getModalHeader:function(){return this.$modalHeader},setModalHeader:function(t){return this.$modalHeader=t,this},createModalBody:function(){return t('<div class="modal-body"></div>')},getModalBody:function(){return this.$modalBody},setModalBody:function(t){return this.$modalBody=t,this},createModalFooter:function(){return t('<div class="modal-footer"></div>')},getModalFooter:function(){return this.$modalFooter},setModalFooter:function(t){return this.$modalFooter=t,this},createDynamicContent:function(t){var e=null;return e="function"==typeof t?t.call(t,this):t,"string"==typeof e&&(e=this.formatStringContent(e)),e},formatStringContent:function(t){return this.options.nl2br?t.replace(/\r\n/g,"<br />").replace(/[\r\n]/g,"<br />"):t},setData:function(t,e){return this.options.data[t]=e,this},getData:function(t){return this.options.data[t]},setId:function(t){return this.options.id=t,this},getId:function(){return this.options.id},getType:function(){return this.options.type},setType:function(t){return this.options.type=t,this.updateType(),this},updateType:function(){if(this.isRealized()){var t=[o.TYPE_DEFAULT,o.TYPE_INFO,o.TYPE_PRIMARY,o.TYPE_SUCCESS,o.TYPE_WARNING,o.TYPE_DANGER];this.getModal().removeClass(t.join(" ")).addClass(this.getType())}return this},getSize:function(){return this.options.size},setSize:function(t){return this.options.size=t,this.updateSize(),this},updateSize:function(){if(this.isRealized()){var e=this;this.getModal().removeClass(o.SIZE_NORMAL).removeClass(o.SIZE_SMALL).removeClass(o.SIZE_WIDE).removeClass(o.SIZE_LARGE),this.getModal().addClass(this.getSize()),this.getModalDialog().removeClass("modal-sm"),this.getSize()===o.SIZE_SMALL&&this.getModalDialog().addClass("modal-sm"),this.getModalDialog().removeClass("modal-lg"),this.getSize()===o.SIZE_WIDE&&this.getModalDialog().addClass("modal-lg"),t.each(this.options.buttons,function(n,o){var i=e.getButton(o.id),s=["btn-lg","btn-sm","btn-xs"],a=!1;if("string"==typeof o.cssClass){var d=o.cssClass.split(" ");t.each(d,function(e,n){-1!==t.inArray(n,s)&&(a=!0)})}a||(i.removeClass(s.join(" ")),i.addClass(e.getButtonSize()))})}return this},getCssClass:function(){return this.options.cssClass},setCssClass:function(t){return this.options.cssClass=t,this},getTitle:function(){return this.options.title},setTitle:function(t){return this.options.title=t,this.updateTitle(),this},updateTitle:function(){if(this.isRealized()){var t=null!==this.getTitle()?this.createDynamicContent(this.getTitle()):this.getDefaultText();this.getModalHeader().find("."+this.getNamespace("title")).html("").append(t).prop("id",this.getId()+"_title")}return this},getMessage:function(){return this.options.message},setMessage:function(t){return this.options.message=t,this.updateMessage(),this},updateMessage:function(){if(this.isRealized()){var t=this.createDynamicContent(this.getMessage());this.getModalBody().find("."+this.getNamespace("message")).html("").append(t)}return this},isClosable:function(){return this.options.closable},setClosable:function(t){return this.options.closable=t,this.updateClosable(),this},setCloseByBackdrop:function(t){return this.options.closeByBackdrop=t,this},canCloseByBackdrop:function(){return this.options.closeByBackdrop},setCloseByKeyboard:function(t){return this.options.closeByKeyboard=t,this},canCloseByKeyboard:function(){return this.options.closeByKeyboard},isAnimate:function(){return this.options.animate},setAnimate:function(t){return this.options.animate=t,this},updateAnimate:function(){return this.isRealized()&&this.getModal().toggleClass("fade",this.isAnimate()),this},getSpinicon:function(){return this.options.spinicon},setSpinicon:function(t){return this.options.spinicon=t,this},addButton:function(t){return this.options.buttons.push(t),this},addButtons:function(e){var n=this;return t.each(e,function(t,e){n.addButton(e)}),this},getButtons:function(){return this.options.buttons},setButtons:function(t){return this.options.buttons=t,this.updateButtons(),this},getButton:function(t){return"undefined"!=typeof this.indexedButtons[t]?this.indexedButtons[t]:null},getButtonSize:function(){return"undefined"!=typeof o.BUTTON_SIZES[this.getSize()]?o.BUTTON_SIZES[this.getSize()]:""},updateButtons:function(){return this.isRealized()&&(0===this.getButtons().length?this.getModalFooter().hide():this.getModalFooter().show().find("."+this.getNamespace("footer")).html("").append(this.createFooterButtons())),this},isAutodestroy:function(){return this.options.autodestroy},setAutodestroy:function(t){this.options.autodestroy=t},getDescription:function(){return this.options.description},setDescription:function(t){return this.options.description=t,this},setTabindex:function(t){return this.options.tabindex=t,this},getTabindex:function(){return this.options.tabindex},updateTabindex:function(){return this.isRealized()&&this.getModal().attr("tabindex",this.getTabindex()),this},getDefaultText:function(){return o.DEFAULT_TEXTS[this.getType()]},getNamespace:function(t){return o.NAMESPACE+"-"+t},createHeaderContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("header")),e.append(this.createTitleContent()),e.prepend(this.createCloseButton()),e},createTitleContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("title")),e},createCloseButton:function(){var e=t("<div></div>");e.addClass(this.getNamespace("close-button"));var n=t('<button class="close">&times;</button>');return e.append(n),e.on("click",{dialog:this},function(t){t.data.dialog.close()}),e},createBodyContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("body")),e.append(this.createMessageContent()),e},createMessageContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("message")),e},createFooterContent:function(){var e=t("<div></div>");return e.addClass(this.getNamespace("footer")),e},createFooterButtons:function(){var e=this,n=t("<div></div>");return n.addClass(this.getNamespace("footer-buttons")),this.indexedButtons={},t.each(this.options.buttons,function(t,i){i.id||(i.id=o.newGuid());var s=e.createButton(i);e.indexedButtons[i.id]=s,n.append(s)}),n},createButton:function(e){var n=t('<button class="btn"></button>');return n.prop("id",e.id),n.data("button",e),"undefined"!=typeof e.icon&&""!==t.trim(e.icon)&&n.append(this.createButtonIcon(e.icon)),"undefined"!=typeof e.label&&n.append(e.label),n.addClass("undefined"!=typeof e.cssClass&&""!==t.trim(e.cssClass)?e.cssClass:"btn-default"),"undefined"!=typeof e.hotkey&&(this.registeredButtonHotkeys[e.hotkey]=n),n.on("click",{dialog:this,$button:n,button:e},function(t){var e=t.data.dialog,n=t.data.$button,o=n.data("button");"function"==typeof o.action&&o.action.call(n,e,t),o.autospin&&n.toggleSpin(!0)}),this.enhanceButton(n),n},enhanceButton:function(t){return t.dialog=this,t.toggleEnable=function(t){var e=this;return"undefined"!=typeof t?e.prop("disabled",!t).toggleClass("disabled",!t):e.prop("disabled",!e.prop("disabled")),e},t.enable=function(){var t=this;return t.toggleEnable(!0),t},t.disable=function(){var t=this;return t.toggleEnable(!1),t},t.toggleSpin=function(e){var n=this,o=n.dialog,i=n.find("."+o.getNamespace("button-icon"));return"undefined"==typeof e&&(e=!(t.find(".icon-spin").length>0)),e?(i.hide(),t.prepend(o.createButtonIcon(o.getSpinicon()).addClass("icon-spin"))):(i.show(),t.find(".icon-spin").remove()),n},t.spin=function(){var t=this;return t.toggleSpin(!0),t},t.stopSpin=function(){var t=this;return t.toggleSpin(!1),t},this},createButtonIcon:function(e){var n=t("<span></span>");return n.addClass(this.getNamespace("button-icon")).addClass(e),n},enableButtons:function(e){return t.each(this.indexedButtons,function(t,n){n.toggleEnable(e)}),this},updateClosable:function(){return this.isRealized()&&this.getModalHeader().find("."+this.getNamespace("close-button")).toggle(this.isClosable()),this},onShow:function(t){return this.options.onshow=t,this},onShown:function(t){return this.options.onshown=t,this},onHide:function(t){return this.options.onhide=t,this},onHidden:function(t){return this.options.onhidden=t,this},isRealized:function(){return this.realized},setRealized:function(t){return this.realized=t,this},isOpened:function(){return this.opened},setOpened:function(t){return this.opened=t,this},handleModalEvents:function(){return this.getModal().on("show.bs.modal",{dialog:this},function(t){var e=t.data.dialog;if(e.setOpened(!0),e.isModalEvent(t)&&"function"==typeof e.options.onshow){var n=e.options.onshow(e);return n===!1&&e.setOpened(!1),n}}),this.getModal().on("shown.bs.modal",{dialog:this},function(t){var e=t.data.dialog;e.isModalEvent(t)&&"function"==typeof e.options.onshown&&e.options.onshown(e)}),this.getModal().on("hide.bs.modal",{dialog:this},function(t){var e=t.data.dialog;if(e.setOpened(!1),e.isModalEvent(t)&&"function"==typeof e.options.onhide){var n=e.options.onhide(e);return n===!1&&e.setOpened(!0),n}}),this.getModal().on("hidden.bs.modal",{dialog:this},function(e){var n=e.data.dialog;n.isModalEvent(e)&&"function"==typeof n.options.onhidden&&n.options.onhidden(n),n.isAutodestroy()&&(delete o.dialogs[n.getId()],t(this).remove()),o.moveFocus()}),this.handleModalBackdropEvent(),this.getModal().on("keyup",{dialog:this},function(t){27===t.which&&t.data.dialog.isClosable()&&t.data.dialog.canCloseByKeyboard()&&t.data.dialog.close()}),this.getModal().on("keyup",{dialog:this},function(e){var n=e.data.dialog;if("undefined"!=typeof n.registeredButtonHotkeys[e.which]){var o=t(n.registeredButtonHotkeys[e.which]);!o.prop("disabled")&&o.focus().trigger("click")}}),this},handleModalBackdropEvent:function(){return this.getModal().on("click",{dialog:this},function(e){t(e.target).hasClass("modal-backdrop")&&e.data.dialog.isClosable()&&e.data.dialog.canCloseByBackdrop()&&e.data.dialog.close()}),this},isModalEvent:function(t){return"undefined"!=typeof t.namespace&&"bs.modal"===t.namespace},makeModalDraggable:function(){return this.options.draggable&&(this.getModalHeader().addClass(this.getNamespace("draggable")).on("mousedown",{dialog:this},function(t){var e=t.data.dialog;e.draggableData.isMouseDown=!0;var n=e.getModalDialog().offset();e.draggableData.mouseOffset={top:t.clientY-n.top,left:t.clientX-n.left}}),this.getModal().on("mouseup mouseleave",{dialog:this},function(t){t.data.dialog.draggableData.isMouseDown=!1}),t("body").on("mousemove",{dialog:this},function(t){var e=t.data.dialog;e.draggableData.isMouseDown&&e.getModalDialog().offset({top:t.clientY-e.draggableData.mouseOffset.top,left:t.clientX-e.draggableData.mouseOffset.left})})),this},realize:function(){return this.initModalStuff(),this.getModal().addClass(o.NAMESPACE).addClass(this.getCssClass()),this.updateSize(),this.getDescription()&&this.getModal().attr("aria-describedby",this.getDescription()),this.getModalFooter().append(this.createFooterContent()),this.getModalHeader().append(this.createHeaderContent()),this.getModalBody().append(this.createBodyContent()),this.getModal().data("bs.modal",new n(this.getModal(),{backdrop:"static",keyboard:!1,show:!1})),this.makeModalDraggable(),this.handleModalEvents(),this.setRealized(!0),this.updateButtons(),this.updateType(),this.updateTitle(),this.updateMessage(),this.updateClosable(),this.updateAnimate(),this.updateSize(),this.updateTabindex(),this},open:function(){return!this.isRealized()&&this.realize(),this.getModal().modal("show"),this},close:function(){return this.getModal().modal("hide"),this}},o.prototype=t.extend(o.prototype,o.METHODS_TO_OVERRIDE[n.getModalVersion()]),o.newGuid=function(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(t){var e=16*Math.random()|0,n="x"===t?e:3&e|8;return n.toString(16)})},o.show=function(t){return new o(t).open()},o.alert=function(){var e={},n={type:o.TYPE_PRIMARY,title:null,message:null,closable:!1,draggable:!1,buttonLabel:o.DEFAULT_TEXTS.OK,callback:null};return e="object"==typeof arguments[0]&&arguments[0].constructor==={}.constructor?t.extend(!0,n,arguments[0]):t.extend(!0,n,{message:arguments[0],callback:"undefined"!=typeof arguments[1]?arguments[1]:null}),new o({type:e.type,title:e.title,message:e.message,closable:e.closable,draggable:e.draggable,data:{callback:e.callback},onhide:function(t){!t.getData("btnClicked")&&t.isClosable()&&"function"==typeof t.getData("callback")&&t.getData("callback")(!1)},buttons:[{label:e.buttonLabel,action:function(t){t.setData("btnClicked",!0),"function"==typeof t.getData("callback")&&t.getData("callback")(!0),t.close()}}]}).open()},o.confirm=function(){var e={},n={type:o.TYPE_PRIMARY,title:null,message:null,closable:!1,draggable:!1,btnCancelLabel:o.DEFAULT_TEXTS.CANCEL,btnOKLabel:o.DEFAULT_TEXTS.OK,btnOKClass:null,callback:null};return e="object"==typeof arguments[0]&&arguments[0].constructor==={}.constructor?t.extend(!0,n,arguments[0]):t.extend(!0,n,{message:arguments[0],closable:!1,buttonLabel:o.DEFAULT_TEXTS.OK,callback:"undefined"!=typeof arguments[1]?arguments[1]:null}),null===e.btnOKClass&&(e.btnOKClass=["btn",e.type.split("-")[1]].join("-")),new o({type:e.type,title:e.title,message:e.message,closable:e.closable,draggable:e.draggable,data:{callback:e.callback},buttons:[{label:e.btnCancelLabel,action:function(t){"function"==typeof t.getData("callback")&&t.getData("callback")(!1),t.close()}},{label:e.btnOKLabel,cssClass:e.btnOKClass,action:function(t){"function"==typeof t.getData("callback")&&t.getData("callback")(!0),t.close()}}]}).open()},o.warning=function(t,e){return new o({type:o.TYPE_WARNING,message:t}).open()},o.danger=function(t,e){return new o({type:o.TYPE_DANGER,message:t}).open()},o.success=function(t,e){return new o({type:o.TYPE_SUCCESS,message:t}).open()},o});;/* node_modules/fancybox/dist/js/jquery.fancybox.pack.js */
/*! fancyBox v2.1.5 fancyapps.com | fancyapps.com/fancybox/#license */
(function(s,H,f,w){var K=f("html"),q=f(s),p=f(H),b=f.fancybox=function(){b.open.apply(this,arguments)},J=navigator.userAgent.match(/msie/i),C=null,t=H.createTouch!==w,u=function(a){return a&&a.hasOwnProperty&&a instanceof f},r=function(a){return a&&"string"===f.type(a)},F=function(a){return r(a)&&0<a.indexOf("%")},m=function(a,d){var e=parseInt(a,10)||0;d&&F(a)&&(e*=b.getViewport()[d]/100);return Math.ceil(e)},x=function(a,b){return m(a,b)+"px"};f.extend(b,{version:"2.1.5",defaults:{padding:15,margin:20,
width:800,height:600,minWidth:100,minHeight:100,maxWidth:9999,maxHeight:9999,pixelRatio:1,autoSize:!0,autoHeight:!1,autoWidth:!1,autoResize:!0,autoCenter:!t,fitToView:!0,aspectRatio:!1,topRatio:0.5,leftRatio:0.5,scrolling:"auto",wrapCSS:"",arrows:!0,closeBtn:!0,closeClick:!1,nextClick:!1,mouseWheel:!0,autoPlay:!1,playSpeed:3E3,preload:3,modal:!1,loop:!0,ajax:{dataType:"html",headers:{"X-fancyBox":!0}},iframe:{scrolling:"auto",preload:!0},swf:{wmode:"transparent",allowfullscreen:"true",allowscriptaccess:"always"},
keys:{next:{13:"left",34:"up",39:"left",40:"up"},prev:{8:"right",33:"down",37:"right",38:"down"},close:[27],play:[32],toggle:[70]},direction:{next:"left",prev:"right"},scrollOutside:!0,index:0,type:null,href:null,content:null,title:null,tpl:{wrap:'<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',image:'<img class="fancybox-image" src="{href}" alt="" />',iframe:'<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen'+
(J?' allowtransparency="true"':"")+"></iframe>",error:'<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',closeBtn:'<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a>',next:'<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',prev:'<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'},openEffect:"fade",openSpeed:250,openEasing:"swing",openOpacity:!0,
openMethod:"zoomIn",closeEffect:"fade",closeSpeed:250,closeEasing:"swing",closeOpacity:!0,closeMethod:"zoomOut",nextEffect:"elastic",nextSpeed:250,nextEasing:"swing",nextMethod:"changeIn",prevEffect:"elastic",prevSpeed:250,prevEasing:"swing",prevMethod:"changeOut",helpers:{overlay:!0,title:!0},onCancel:f.noop,beforeLoad:f.noop,afterLoad:f.noop,beforeShow:f.noop,afterShow:f.noop,beforeChange:f.noop,beforeClose:f.noop,afterClose:f.noop},group:{},opts:{},previous:null,coming:null,current:null,isActive:!1,
isOpen:!1,isOpened:!1,wrap:null,skin:null,outer:null,inner:null,player:{timer:null,isActive:!1},ajaxLoad:null,imgPreload:null,transitions:{},helpers:{},open:function(a,d){if(a&&(f.isPlainObject(d)||(d={}),!1!==b.close(!0)))return f.isArray(a)||(a=u(a)?f(a).get():[a]),f.each(a,function(e,c){var l={},g,h,k,n,m;"object"===f.type(c)&&(c.nodeType&&(c=f(c)),u(c)?(l={href:c.data("fancybox-href")||c.attr("href"),title:f("<div/>").text(c.data("fancybox-title")||c.attr("title")).html(),isDom:!0,element:c},
f.metadata&&f.extend(!0,l,c.metadata())):l=c);g=d.href||l.href||(r(c)?c:null);h=d.title!==w?d.title:l.title||"";n=(k=d.content||l.content)?"html":d.type||l.type;!n&&l.isDom&&(n=c.data("fancybox-type"),n||(n=(n=c.prop("class").match(/fancybox\.(\w+)/))?n[1]:null));r(g)&&(n||(b.isImage(g)?n="image":b.isSWF(g)?n="swf":"#"===g.charAt(0)?n="inline":r(c)&&(n="html",k=c)),"ajax"===n&&(m=g.split(/\s+/,2),g=m.shift(),m=m.shift()));k||("inline"===n?g?k=f(r(g)?g.replace(/.*(?=#[^\s]+$)/,""):g):l.isDom&&(k=c):
"html"===n?k=g:n||g||!l.isDom||(n="inline",k=c));f.extend(l,{href:g,type:n,content:k,title:h,selector:m});a[e]=l}),b.opts=f.extend(!0,{},b.defaults,d),d.keys!==w&&(b.opts.keys=d.keys?f.extend({},b.defaults.keys,d.keys):!1),b.group=a,b._start(b.opts.index)},cancel:function(){var a=b.coming;a&&!1===b.trigger("onCancel")||(b.hideLoading(),a&&(b.ajaxLoad&&b.ajaxLoad.abort(),b.ajaxLoad=null,b.imgPreload&&(b.imgPreload.onload=b.imgPreload.onerror=null),a.wrap&&a.wrap.stop(!0,!0).trigger("onReset").remove(),
b.coming=null,b.current||b._afterZoomOut(a)))},close:function(a){b.cancel();!1!==b.trigger("beforeClose")&&(b.unbindEvents(),b.isActive&&(b.isOpen&&!0!==a?(b.isOpen=b.isOpened=!1,b.isClosing=!0,f(".fancybox-item, .fancybox-nav").remove(),b.wrap.stop(!0,!0).removeClass("fancybox-opened"),b.transitions[b.current.closeMethod]()):(f(".fancybox-wrap").stop(!0).trigger("onReset").remove(),b._afterZoomOut())))},play:function(a){var d=function(){clearTimeout(b.player.timer)},e=function(){d();b.current&&b.player.isActive&&
(b.player.timer=setTimeout(b.next,b.current.playSpeed))},c=function(){d();p.unbind(".player");b.player.isActive=!1;b.trigger("onPlayEnd")};!0===a||!b.player.isActive&&!1!==a?b.current&&(b.current.loop||b.current.index<b.group.length-1)&&(b.player.isActive=!0,p.bind({"onCancel.player beforeClose.player":c,"onUpdate.player":e,"beforeLoad.player":d}),e(),b.trigger("onPlayStart")):c()},next:function(a){var d=b.current;d&&(r(a)||(a=d.direction.next),b.jumpto(d.index+1,a,"next"))},prev:function(a){var d=
b.current;d&&(r(a)||(a=d.direction.prev),b.jumpto(d.index-1,a,"prev"))},jumpto:function(a,d,e){var c=b.current;c&&(a=m(a),b.direction=d||c.direction[a>=c.index?"next":"prev"],b.router=e||"jumpto",c.loop&&(0>a&&(a=c.group.length+a%c.group.length),a%=c.group.length),c.group[a]!==w&&(b.cancel(),b._start(a)))},reposition:function(a,d){var e=b.current,c=e?e.wrap:null,l;c&&(l=b._getPosition(d),a&&"scroll"===a.type?(delete l.position,c.stop(!0,!0).animate(l,200)):(c.css(l),e.pos=f.extend({},e.dim,l)))},
update:function(a){var d=a&&a.originalEvent&&a.originalEvent.type,e=!d||"orientationchange"===d;e&&(clearTimeout(C),C=null);b.isOpen&&!C&&(C=setTimeout(function(){var c=b.current;c&&!b.isClosing&&(b.wrap.removeClass("fancybox-tmp"),(e||"load"===d||"resize"===d&&c.autoResize)&&b._setDimension(),"scroll"===d&&c.canShrink||b.reposition(a),b.trigger("onUpdate"),C=null)},e&&!t?0:300))},toggle:function(a){b.isOpen&&(b.current.fitToView="boolean"===f.type(a)?a:!b.current.fitToView,t&&(b.wrap.removeAttr("style").addClass("fancybox-tmp"),
b.trigger("onUpdate")),b.update())},hideLoading:function(){p.unbind(".loading");f("#fancybox-loading").remove()},showLoading:function(){var a,d;b.hideLoading();a=f('<div id="fancybox-loading"><div></div></div>').click(b.cancel).appendTo("body");p.bind("keydown.loading",function(a){27===(a.which||a.keyCode)&&(a.preventDefault(),b.cancel())});b.defaults.fixed||(d=b.getViewport(),a.css({position:"absolute",top:0.5*d.h+d.y,left:0.5*d.w+d.x}));b.trigger("onLoading")},getViewport:function(){var a=b.current&&
b.current.locked||!1,d={x:q.scrollLeft(),y:q.scrollTop()};a&&a.length?(d.w=a[0].clientWidth,d.h=a[0].clientHeight):(d.w=t&&s.innerWidth?s.innerWidth:q.width(),d.h=t&&s.innerHeight?s.innerHeight:q.height());return d},unbindEvents:function(){b.wrap&&u(b.wrap)&&b.wrap.unbind(".fb");p.unbind(".fb");q.unbind(".fb")},bindEvents:function(){var a=b.current,d;a&&(q.bind("orientationchange.fb"+(t?"":" resize.fb")+(a.autoCenter&&!a.locked?" scroll.fb":""),b.update),(d=a.keys)&&p.bind("keydown.fb",function(e){var c=
e.which||e.keyCode,l=e.target||e.srcElement;if(27===c&&b.coming)return!1;e.ctrlKey||e.altKey||e.shiftKey||e.metaKey||l&&(l.type||f(l).is("[contenteditable]"))||f.each(d,function(d,l){if(1<a.group.length&&l[c]!==w)return b[d](l[c]),e.preventDefault(),!1;if(-1<f.inArray(c,l))return b[d](),e.preventDefault(),!1})}),f.fn.mousewheel&&a.mouseWheel&&b.wrap.bind("mousewheel.fb",function(d,c,l,g){for(var h=f(d.target||null),k=!1;h.length&&!(k||h.is(".fancybox-skin")||h.is(".fancybox-wrap"));)k=h[0]&&!(h[0].style.overflow&&
"hidden"===h[0].style.overflow)&&(h[0].clientWidth&&h[0].scrollWidth>h[0].clientWidth||h[0].clientHeight&&h[0].scrollHeight>h[0].clientHeight),h=f(h).parent();0!==c&&!k&&1<b.group.length&&!a.canShrink&&(0<g||0<l?b.prev(0<g?"down":"left"):(0>g||0>l)&&b.next(0>g?"up":"right"),d.preventDefault())}))},trigger:function(a,d){var e,c=d||b.coming||b.current;if(c){f.isFunction(c[a])&&(e=c[a].apply(c,Array.prototype.slice.call(arguments,1)));if(!1===e)return!1;c.helpers&&f.each(c.helpers,function(d,e){if(e&&
b.helpers[d]&&f.isFunction(b.helpers[d][a]))b.helpers[d][a](f.extend(!0,{},b.helpers[d].defaults,e),c)})}p.trigger(a)},isImage:function(a){return r(a)&&a.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i)},isSWF:function(a){return r(a)&&a.match(/\.(swf)((\?|#).*)?$/i)},_start:function(a){var d={},e,c;a=m(a);e=b.group[a]||null;if(!e)return!1;d=f.extend(!0,{},b.opts,e);e=d.margin;c=d.padding;"number"===f.type(e)&&(d.margin=[e,e,e,e]);"number"===f.type(c)&&(d.padding=[c,c,
c,c]);d.modal&&f.extend(!0,d,{closeBtn:!1,closeClick:!1,nextClick:!1,arrows:!1,mouseWheel:!1,keys:null,helpers:{overlay:{closeClick:!1}}});d.autoSize&&(d.autoWidth=d.autoHeight=!0);"auto"===d.width&&(d.autoWidth=!0);"auto"===d.height&&(d.autoHeight=!0);d.group=b.group;d.index=a;b.coming=d;if(!1===b.trigger("beforeLoad"))b.coming=null;else{c=d.type;e=d.href;if(!c)return b.coming=null,b.current&&b.router&&"jumpto"!==b.router?(b.current.index=a,b[b.router](b.direction)):!1;b.isActive=!0;if("image"===
c||"swf"===c)d.autoHeight=d.autoWidth=!1,d.scrolling="visible";"image"===c&&(d.aspectRatio=!0);"iframe"===c&&t&&(d.scrolling="scroll");d.wrap=f(d.tpl.wrap).addClass("fancybox-"+(t?"mobile":"desktop")+" fancybox-type-"+c+" fancybox-tmp "+d.wrapCSS).appendTo(d.parent||"body");f.extend(d,{skin:f(".fancybox-skin",d.wrap),outer:f(".fancybox-outer",d.wrap),inner:f(".fancybox-inner",d.wrap)});f.each(["Top","Right","Bottom","Left"],function(a,b){d.skin.css("padding"+b,x(d.padding[a]))});b.trigger("onReady");
if("inline"===c||"html"===c){if(!d.content||!d.content.length)return b._error("content")}else if(!e)return b._error("href");"image"===c?b._loadImage():"ajax"===c?b._loadAjax():"iframe"===c?b._loadIframe():b._afterLoad()}},_error:function(a){f.extend(b.coming,{type:"html",autoWidth:!0,autoHeight:!0,minWidth:0,minHeight:0,scrolling:"no",hasError:a,content:b.coming.tpl.error});b._afterLoad()},_loadImage:function(){var a=b.imgPreload=new Image;a.onload=function(){this.onload=this.onerror=null;b.coming.width=
this.width/b.opts.pixelRatio;b.coming.height=this.height/b.opts.pixelRatio;b._afterLoad()};a.onerror=function(){this.onload=this.onerror=null;b._error("image")};a.src=b.coming.href;!0!==a.complete&&b.showLoading()},_loadAjax:function(){var a=b.coming;b.showLoading();b.ajaxLoad=f.ajax(f.extend({},a.ajax,{url:a.href,error:function(a,e){b.coming&&"abort"!==e?b._error("ajax",a):b.hideLoading()},success:function(d,e){"success"===e&&(a.content=d,b._afterLoad())}}))},_loadIframe:function(){var a=b.coming,
d=f(a.tpl.iframe.replace(/\{rnd\}/g,(new Date).getTime())).attr("scrolling",t?"auto":a.iframe.scrolling).attr("src",a.href);f(a.wrap).bind("onReset",function(){try{f(this).find("iframe").hide().attr("src","//about:blank").end().empty()}catch(a){}});a.iframe.preload&&(b.showLoading(),d.one("load",function(){f(this).data("ready",1);t||f(this).bind("load.fb",b.update);f(this).parents(".fancybox-wrap").width("100%").removeClass("fancybox-tmp").show();b._afterLoad()}));a.content=d.appendTo(a.inner);a.iframe.preload||
b._afterLoad()},_preloadImages:function(){var a=b.group,d=b.current,e=a.length,c=d.preload?Math.min(d.preload,e-1):0,f,g;for(g=1;g<=c;g+=1)f=a[(d.index+g)%e],"image"===f.type&&f.href&&((new Image).src=f.href)},_afterLoad:function(){var a=b.coming,d=b.current,e,c,l,g,h;b.hideLoading();if(a&&!1!==b.isActive)if(!1===b.trigger("afterLoad",a,d))a.wrap.stop(!0).trigger("onReset").remove(),b.coming=null;else{d&&(b.trigger("beforeChange",d),d.wrap.stop(!0).removeClass("fancybox-opened").find(".fancybox-item, .fancybox-nav").remove());
b.unbindEvents();e=a.content;c=a.type;l=a.scrolling;f.extend(b,{wrap:a.wrap,skin:a.skin,outer:a.outer,inner:a.inner,current:a,previous:d});g=a.href;switch(c){case "inline":case "ajax":case "html":a.selector?e=f("<div>").html(e).find(a.selector):u(e)&&(e.data("fancybox-placeholder")||e.data("fancybox-placeholder",f('<div class="fancybox-placeholder"></div>').insertAfter(e).hide()),e=e.show().detach(),a.wrap.bind("onReset",function(){f(this).find(e).length&&e.hide().replaceAll(e.data("fancybox-placeholder")).data("fancybox-placeholder",
!1)}));break;case "image":e=a.tpl.image.replace(/\{href\}/g,g);break;case "swf":e='<object id="fancybox-swf" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="'+g+'"></param>',h="",f.each(a.swf,function(a,b){e+='<param name="'+a+'" value="'+b+'"></param>';h+=" "+a+'="'+b+'"'}),e+='<embed src="'+g+'" type="application/x-shockwave-flash" width="100%" height="100%"'+h+"></embed></object>"}u(e)&&e.parent().is(a.inner)||a.inner.append(e);b.trigger("beforeShow");
a.inner.css("overflow","yes"===l?"scroll":"no"===l?"hidden":l);b._setDimension();b.reposition();b.isOpen=!1;b.coming=null;b.bindEvents();if(!b.isOpened)f(".fancybox-wrap").not(a.wrap).stop(!0).trigger("onReset").remove();else if(d.prevMethod)b.transitions[d.prevMethod]();b.transitions[b.isOpened?a.nextMethod:a.openMethod]();b._preloadImages()}},_setDimension:function(){var a=b.getViewport(),d=0,e=!1,c=!1,e=b.wrap,l=b.skin,g=b.inner,h=b.current,c=h.width,k=h.height,n=h.minWidth,v=h.minHeight,p=h.maxWidth,
q=h.maxHeight,t=h.scrolling,r=h.scrollOutside?h.scrollbarWidth:0,y=h.margin,z=m(y[1]+y[3]),s=m(y[0]+y[2]),w,A,u,D,B,G,C,E,I;e.add(l).add(g).width("auto").height("auto").removeClass("fancybox-tmp");y=m(l.outerWidth(!0)-l.width());w=m(l.outerHeight(!0)-l.height());A=z+y;u=s+w;D=F(c)?(a.w-A)*m(c)/100:c;B=F(k)?(a.h-u)*m(k)/100:k;if("iframe"===h.type){if(I=h.content,h.autoHeight&&1===I.data("ready"))try{I[0].contentWindow.document.location&&(g.width(D).height(9999),G=I.contents().find("body"),r&&G.css("overflow-x",
"hidden"),B=G.outerHeight(!0))}catch(H){}}else if(h.autoWidth||h.autoHeight)g.addClass("fancybox-tmp"),h.autoWidth||g.width(D),h.autoHeight||g.height(B),h.autoWidth&&(D=g.width()),h.autoHeight&&(B=g.height()),g.removeClass("fancybox-tmp");c=m(D);k=m(B);E=D/B;n=m(F(n)?m(n,"w")-A:n);p=m(F(p)?m(p,"w")-A:p);v=m(F(v)?m(v,"h")-u:v);q=m(F(q)?m(q,"h")-u:q);G=p;C=q;h.fitToView&&(p=Math.min(a.w-A,p),q=Math.min(a.h-u,q));A=a.w-z;s=a.h-s;h.aspectRatio?(c>p&&(c=p,k=m(c/E)),k>q&&(k=q,c=m(k*E)),c<n&&(c=n,k=m(c/
E)),k<v&&(k=v,c=m(k*E))):(c=Math.max(n,Math.min(c,p)),h.autoHeight&&"iframe"!==h.type&&(g.width(c),k=g.height()),k=Math.max(v,Math.min(k,q)));if(h.fitToView)if(g.width(c).height(k),e.width(c+y),a=e.width(),z=e.height(),h.aspectRatio)for(;(a>A||z>s)&&c>n&&k>v&&!(19<d++);)k=Math.max(v,Math.min(q,k-10)),c=m(k*E),c<n&&(c=n,k=m(c/E)),c>p&&(c=p,k=m(c/E)),g.width(c).height(k),e.width(c+y),a=e.width(),z=e.height();else c=Math.max(n,Math.min(c,c-(a-A))),k=Math.max(v,Math.min(k,k-(z-s)));r&&"auto"===t&&k<B&&
c+y+r<A&&(c+=r);g.width(c).height(k);e.width(c+y);a=e.width();z=e.height();e=(a>A||z>s)&&c>n&&k>v;c=h.aspectRatio?c<G&&k<C&&c<D&&k<B:(c<G||k<C)&&(c<D||k<B);f.extend(h,{dim:{width:x(a),height:x(z)},origWidth:D,origHeight:B,canShrink:e,canExpand:c,wPadding:y,hPadding:w,wrapSpace:z-l.outerHeight(!0),skinSpace:l.height()-k});!I&&h.autoHeight&&k>v&&k<q&&!c&&g.height("auto")},_getPosition:function(a){var d=b.current,e=b.getViewport(),c=d.margin,f=b.wrap.width()+c[1]+c[3],g=b.wrap.height()+c[0]+c[2],c={position:"absolute",
top:c[0],left:c[3]};d.autoCenter&&d.fixed&&!a&&g<=e.h&&f<=e.w?c.position="fixed":d.locked||(c.top+=e.y,c.left+=e.x);c.top=x(Math.max(c.top,c.top+(e.h-g)*d.topRatio));c.left=x(Math.max(c.left,c.left+(e.w-f)*d.leftRatio));return c},_afterZoomIn:function(){var a=b.current;a&&((b.isOpen=b.isOpened=!0,b.wrap.css("overflow","visible").addClass("fancybox-opened"),b.update(),(a.closeClick||a.nextClick&&1<b.group.length)&&b.inner.css("cursor","pointer").bind("click.fb",function(d){f(d.target).is("a")||f(d.target).parent().is("a")||
(d.preventDefault(),b[a.closeClick?"close":"next"]())}),a.closeBtn&&f(a.tpl.closeBtn).appendTo(b.skin).bind("click.fb",function(a){a.preventDefault();b.close()}),a.arrows&&1<b.group.length&&((a.loop||0<a.index)&&f(a.tpl.prev).appendTo(b.outer).bind("click.fb",b.prev),(a.loop||a.index<b.group.length-1)&&f(a.tpl.next).appendTo(b.outer).bind("click.fb",b.next)),b.trigger("afterShow"),a.loop||a.index!==a.group.length-1)?b.opts.autoPlay&&!b.player.isActive&&(b.opts.autoPlay=!1,b.play(!0)):b.play(!1))},
_afterZoomOut:function(a){a=a||b.current;f(".fancybox-wrap").trigger("onReset").remove();f.extend(b,{group:{},opts:{},router:!1,current:null,isActive:!1,isOpened:!1,isOpen:!1,isClosing:!1,wrap:null,skin:null,outer:null,inner:null});b.trigger("afterClose",a)}});b.transitions={getOrigPosition:function(){var a=b.current,d=a.element,e=a.orig,c={},f=50,g=50,h=a.hPadding,k=a.wPadding,n=b.getViewport();!e&&a.isDom&&d.is(":visible")&&(e=d.find("img:first"),e.length||(e=d));u(e)?(c=e.offset(),e.is("img")&&
(f=e.outerWidth(),g=e.outerHeight())):(c.top=n.y+(n.h-g)*a.topRatio,c.left=n.x+(n.w-f)*a.leftRatio);if("fixed"===b.wrap.css("position")||a.locked)c.top-=n.y,c.left-=n.x;return c={top:x(c.top-h*a.topRatio),left:x(c.left-k*a.leftRatio),width:x(f+k),height:x(g+h)}},step:function(a,d){var e,c,f=d.prop;c=b.current;var g=c.wrapSpace,h=c.skinSpace;if("width"===f||"height"===f)e=d.end===d.start?1:(a-d.start)/(d.end-d.start),b.isClosing&&(e=1-e),c="width"===f?c.wPadding:c.hPadding,c=a-c,b.skin[f](m("width"===
f?c:c-g*e)),b.inner[f](m("width"===f?c:c-g*e-h*e))},zoomIn:function(){var a=b.current,d=a.pos,e=a.openEffect,c="elastic"===e,l=f.extend({opacity:1},d);delete l.position;c?(d=this.getOrigPosition(),a.openOpacity&&(d.opacity=0.1)):"fade"===e&&(d.opacity=0.1);b.wrap.css(d).animate(l,{duration:"none"===e?0:a.openSpeed,easing:a.openEasing,step:c?this.step:null,complete:b._afterZoomIn})},zoomOut:function(){var a=b.current,d=a.closeEffect,e="elastic"===d,c={opacity:0.1};e&&(c=this.getOrigPosition(),a.closeOpacity&&
(c.opacity=0.1));b.wrap.animate(c,{duration:"none"===d?0:a.closeSpeed,easing:a.closeEasing,step:e?this.step:null,complete:b._afterZoomOut})},changeIn:function(){var a=b.current,d=a.nextEffect,e=a.pos,c={opacity:1},f=b.direction,g;e.opacity=0.1;"elastic"===d&&(g="down"===f||"up"===f?"top":"left","down"===f||"right"===f?(e[g]=x(m(e[g])-200),c[g]="+=200px"):(e[g]=x(m(e[g])+200),c[g]="-=200px"));"none"===d?b._afterZoomIn():b.wrap.css(e).animate(c,{duration:a.nextSpeed,easing:a.nextEasing,complete:b._afterZoomIn})},
changeOut:function(){var a=b.previous,d=a.prevEffect,e={opacity:0.1},c=b.direction;"elastic"===d&&(e["down"===c||"up"===c?"top":"left"]=("up"===c||"left"===c?"-":"+")+"=200px");a.wrap.animate(e,{duration:"none"===d?0:a.prevSpeed,easing:a.prevEasing,complete:function(){f(this).trigger("onReset").remove()}})}};b.helpers.overlay={defaults:{closeClick:!0,speedOut:200,showEarly:!0,css:{},locked:!t,fixed:!0},overlay:null,fixed:!1,el:f("html"),create:function(a){var d;a=f.extend({},this.defaults,a);this.overlay&&
this.close();d=b.coming?b.coming.parent:a.parent;this.overlay=f('<div class="fancybox-overlay"></div>').appendTo(d&&d.lenth?d:"body");this.fixed=!1;a.fixed&&b.defaults.fixed&&(this.overlay.addClass("fancybox-overlay-fixed"),this.fixed=!0)},open:function(a){var d=this;a=f.extend({},this.defaults,a);this.overlay?this.overlay.unbind(".overlay").width("auto").height("auto"):this.create(a);this.fixed||(q.bind("resize.overlay",f.proxy(this.update,this)),this.update());a.closeClick&&this.overlay.bind("click.overlay",
function(a){if(f(a.target).hasClass("fancybox-overlay"))return b.isActive?b.close():d.close(),!1});this.overlay.css(a.css).show()},close:function(){q.unbind("resize.overlay");this.el.hasClass("fancybox-lock")&&(f(".fancybox-margin").removeClass("fancybox-margin"),this.el.removeClass("fancybox-lock"),q.scrollTop(this.scrollV).scrollLeft(this.scrollH));f(".fancybox-overlay").remove().hide();f.extend(this,{overlay:null,fixed:!1})},update:function(){var a="100%",b;this.overlay.width(a).height("100%");
J?(b=Math.max(H.documentElement.offsetWidth,H.body.offsetWidth),p.width()>b&&(a=p.width())):p.width()>q.width()&&(a=p.width());this.overlay.width(a).height(p.height())},onReady:function(a,b){var e=this.overlay;f(".fancybox-overlay").stop(!0,!0);e||this.create(a);a.locked&&this.fixed&&b.fixed&&(b.locked=this.overlay.append(b.wrap),b.fixed=!1);!0===a.showEarly&&this.beforeShow.apply(this,arguments)},beforeShow:function(a,b){b.locked&&!this.el.hasClass("fancybox-lock")&&(!1!==this.fixPosition&&f("*").filter(function(){return"fixed"===
f(this).css("position")&&!f(this).hasClass("fancybox-overlay")&&!f(this).hasClass("fancybox-wrap")}).addClass("fancybox-margin"),this.el.addClass("fancybox-margin"),this.scrollV=q.scrollTop(),this.scrollH=q.scrollLeft(),this.el.addClass("fancybox-lock"),q.scrollTop(this.scrollV).scrollLeft(this.scrollH));this.open(a)},onUpdate:function(){this.fixed||this.update()},afterClose:function(a){this.overlay&&!b.coming&&this.overlay.fadeOut(a.speedOut,f.proxy(this.close,this))}};b.helpers.title={defaults:{type:"float",
position:"bottom"},beforeShow:function(a){var d=b.current,e=d.title,c=a.type;f.isFunction(e)&&(e=e.call(d.element,d));if(r(e)&&""!==f.trim(e)){d=f('<div class="fancybox-title fancybox-title-'+c+'-wrap">'+e+"</div>");switch(c){case "inside":c=b.skin;break;case "outside":c=b.wrap;break;case "over":c=b.inner;break;default:c=b.skin,d.appendTo("body"),J&&d.width(d.width()),d.wrapInner('<span class="child"></span>'),b.current.margin[2]+=Math.abs(m(d.css("margin-bottom")))}d["top"===a.position?"prependTo":
"appendTo"](c)}}};f.fn.fancybox=function(a){var d,e=f(this),c=this.selector||"",l=function(g){var h=f(this).blur(),k=d,l,m;g.ctrlKey||g.altKey||g.shiftKey||g.metaKey||h.is(".fancybox-wrap")||(l=a.groupAttr||"data-fancybox-group",m=h.attr(l),m||(l="rel",m=h.get(0)[l]),m&&""!==m&&"nofollow"!==m&&(h=c.length?f(c):e,h=h.filter("["+l+'="'+m+'"]'),k=h.index(this)),a.index=k,!1!==b.open(h,a)&&g.preventDefault())};a=a||{};d=a.index||0;c&&!1!==a.live?p.undelegate(c,"click.fb-start").delegate(c+":not('.fancybox-item, .fancybox-nav')",
"click.fb-start",l):e.unbind("click.fb-start").bind("click.fb-start",l);this.filter("[data-fancybox-start=1]").trigger("click");return this};p.ready(function(){var a,d;f.scrollbarWidth===w&&(f.scrollbarWidth=function(){var a=f('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo("body"),b=a.children(),b=b.innerWidth()-b.height(99).innerWidth();a.remove();return b});f.support.fixedPosition===w&&(f.support.fixedPosition=function(){var a=f('<div style="position:fixed;top:20px;"></div>').appendTo("body"),
b=20===a[0].offsetTop||15===a[0].offsetTop;a.remove();return b}());f.extend(b.defaults,{scrollbarWidth:f.scrollbarWidth(),fixed:f.support.fixedPosition,parent:f("body")});a=f(s).width();K.addClass("fancybox-lock-test");d=f(s).width();K.removeClass("fancybox-lock-test");f("<style type='text/css'>.fancybox-margin{margin-right:"+(d-a)+"px;}</style>").appendTo("head")})})(window,document,jQuery);;/* node_modules/hammerjs/hammer.min.js */
/*! Hammer.JS - v2.0.6 - 2015-12-23
 * http://hammerjs.github.io/
 *
 * Copyright (c) 2015 Jorik Tangelder;
 * Licensed under the  license */
!function(a,b,c,d){"use strict";function e(a,b,c){return setTimeout(j(a,c),b)}function f(a,b,c){return Array.isArray(a)?(g(a,c[b],c),!0):!1}function g(a,b,c){var e;if(a)if(a.forEach)a.forEach(b,c);else if(a.length!==d)for(e=0;e<a.length;)b.call(c,a[e],e,a),e++;else for(e in a)a.hasOwnProperty(e)&&b.call(c,a[e],e,a)}function h(b,c,d){var e="DEPRECATED METHOD: "+c+"\n"+d+" AT \n";return function(){var c=new Error("get-stack-trace"),d=c&&c.stack?c.stack.replace(/^[^\(]+?[\n$]/gm,"").replace(/^\s+at\s+/gm,"").replace(/^Object.<anonymous>\s*\(/gm,"{anonymous}()@"):"Unknown Stack Trace",f=a.console&&(a.console.warn||a.console.log);return f&&f.call(a.console,e,d),b.apply(this,arguments)}}function i(a,b,c){var d,e=b.prototype;d=a.prototype=Object.create(e),d.constructor=a,d._super=e,c&&hb(d,c)}function j(a,b){return function(){return a.apply(b,arguments)}}function k(a,b){return typeof a==kb?a.apply(b?b[0]||d:d,b):a}function l(a,b){return a===d?b:a}function m(a,b,c){g(q(b),function(b){a.addEventListener(b,c,!1)})}function n(a,b,c){g(q(b),function(b){a.removeEventListener(b,c,!1)})}function o(a,b){for(;a;){if(a==b)return!0;a=a.parentNode}return!1}function p(a,b){return a.indexOf(b)>-1}function q(a){return a.trim().split(/\s+/g)}function r(a,b,c){if(a.indexOf&&!c)return a.indexOf(b);for(var d=0;d<a.length;){if(c&&a[d][c]==b||!c&&a[d]===b)return d;d++}return-1}function s(a){return Array.prototype.slice.call(a,0)}function t(a,b,c){for(var d=[],e=[],f=0;f<a.length;){var g=b?a[f][b]:a[f];r(e,g)<0&&d.push(a[f]),e[f]=g,f++}return c&&(d=b?d.sort(function(a,c){return a[b]>c[b]}):d.sort()),d}function u(a,b){for(var c,e,f=b[0].toUpperCase()+b.slice(1),g=0;g<ib.length;){if(c=ib[g],e=c?c+f:b,e in a)return e;g++}return d}function v(){return qb++}function w(b){var c=b.ownerDocument||b;return c.defaultView||c.parentWindow||a}function x(a,b){var c=this;this.manager=a,this.callback=b,this.element=a.element,this.target=a.options.inputTarget,this.domHandler=function(b){k(a.options.enable,[a])&&c.handler(b)},this.init()}function y(a){var b,c=a.options.inputClass;return new(b=c?c:tb?M:ub?P:sb?R:L)(a,z)}function z(a,b,c){var d=c.pointers.length,e=c.changedPointers.length,f=b&Ab&&d-e===0,g=b&(Cb|Db)&&d-e===0;c.isFirst=!!f,c.isFinal=!!g,f&&(a.session={}),c.eventType=b,A(a,c),a.emit("hammer.input",c),a.recognize(c),a.session.prevInput=c}function A(a,b){var c=a.session,d=b.pointers,e=d.length;c.firstInput||(c.firstInput=D(b)),e>1&&!c.firstMultiple?c.firstMultiple=D(b):1===e&&(c.firstMultiple=!1);var f=c.firstInput,g=c.firstMultiple,h=g?g.center:f.center,i=b.center=E(d);b.timeStamp=nb(),b.deltaTime=b.timeStamp-f.timeStamp,b.angle=I(h,i),b.distance=H(h,i),B(c,b),b.offsetDirection=G(b.deltaX,b.deltaY);var j=F(b.deltaTime,b.deltaX,b.deltaY);b.overallVelocityX=j.x,b.overallVelocityY=j.y,b.overallVelocity=mb(j.x)>mb(j.y)?j.x:j.y,b.scale=g?K(g.pointers,d):1,b.rotation=g?J(g.pointers,d):0,b.maxPointers=c.prevInput?b.pointers.length>c.prevInput.maxPointers?b.pointers.length:c.prevInput.maxPointers:b.pointers.length,C(c,b);var k=a.element;o(b.srcEvent.target,k)&&(k=b.srcEvent.target),b.target=k}function B(a,b){var c=b.center,d=a.offsetDelta||{},e=a.prevDelta||{},f=a.prevInput||{};(b.eventType===Ab||f.eventType===Cb)&&(e=a.prevDelta={x:f.deltaX||0,y:f.deltaY||0},d=a.offsetDelta={x:c.x,y:c.y}),b.deltaX=e.x+(c.x-d.x),b.deltaY=e.y+(c.y-d.y)}function C(a,b){var c,e,f,g,h=a.lastInterval||b,i=b.timeStamp-h.timeStamp;if(b.eventType!=Db&&(i>zb||h.velocity===d)){var j=b.deltaX-h.deltaX,k=b.deltaY-h.deltaY,l=F(i,j,k);e=l.x,f=l.y,c=mb(l.x)>mb(l.y)?l.x:l.y,g=G(j,k),a.lastInterval=b}else c=h.velocity,e=h.velocityX,f=h.velocityY,g=h.direction;b.velocity=c,b.velocityX=e,b.velocityY=f,b.direction=g}function D(a){for(var b=[],c=0;c<a.pointers.length;)b[c]={clientX:lb(a.pointers[c].clientX),clientY:lb(a.pointers[c].clientY)},c++;return{timeStamp:nb(),pointers:b,center:E(b),deltaX:a.deltaX,deltaY:a.deltaY}}function E(a){var b=a.length;if(1===b)return{x:lb(a[0].clientX),y:lb(a[0].clientY)};for(var c=0,d=0,e=0;b>e;)c+=a[e].clientX,d+=a[e].clientY,e++;return{x:lb(c/b),y:lb(d/b)}}function F(a,b,c){return{x:b/a||0,y:c/a||0}}function G(a,b){return a===b?Eb:mb(a)>=mb(b)?0>a?Fb:Gb:0>b?Hb:Ib}function H(a,b,c){c||(c=Mb);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return Math.sqrt(d*d+e*e)}function I(a,b,c){c||(c=Mb);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return 180*Math.atan2(e,d)/Math.PI}function J(a,b){return I(b[1],b[0],Nb)+I(a[1],a[0],Nb)}function K(a,b){return H(b[0],b[1],Nb)/H(a[0],a[1],Nb)}function L(){this.evEl=Pb,this.evWin=Qb,this.allow=!0,this.pressed=!1,x.apply(this,arguments)}function M(){this.evEl=Tb,this.evWin=Ub,x.apply(this,arguments),this.store=this.manager.session.pointerEvents=[]}function N(){this.evTarget=Wb,this.evWin=Xb,this.started=!1,x.apply(this,arguments)}function O(a,b){var c=s(a.touches),d=s(a.changedTouches);return b&(Cb|Db)&&(c=t(c.concat(d),"identifier",!0)),[c,d]}function P(){this.evTarget=Zb,this.targetIds={},x.apply(this,arguments)}function Q(a,b){var c=s(a.touches),d=this.targetIds;if(b&(Ab|Bb)&&1===c.length)return d[c[0].identifier]=!0,[c,c];var e,f,g=s(a.changedTouches),h=[],i=this.target;if(f=c.filter(function(a){return o(a.target,i)}),b===Ab)for(e=0;e<f.length;)d[f[e].identifier]=!0,e++;for(e=0;e<g.length;)d[g[e].identifier]&&h.push(g[e]),b&(Cb|Db)&&delete d[g[e].identifier],e++;return h.length?[t(f.concat(h),"identifier",!0),h]:void 0}function R(){x.apply(this,arguments);var a=j(this.handler,this);this.touch=new P(this.manager,a),this.mouse=new L(this.manager,a)}function S(a,b){this.manager=a,this.set(b)}function T(a){if(p(a,dc))return dc;var b=p(a,ec),c=p(a,fc);return b&&c?dc:b||c?b?ec:fc:p(a,cc)?cc:bc}function U(a){this.options=hb({},this.defaults,a||{}),this.id=v(),this.manager=null,this.options.enable=l(this.options.enable,!0),this.state=gc,this.simultaneous={},this.requireFail=[]}function V(a){return a&lc?"cancel":a&jc?"end":a&ic?"move":a&hc?"start":""}function W(a){return a==Ib?"down":a==Hb?"up":a==Fb?"left":a==Gb?"right":""}function X(a,b){var c=b.manager;return c?c.get(a):a}function Y(){U.apply(this,arguments)}function Z(){Y.apply(this,arguments),this.pX=null,this.pY=null}function $(){Y.apply(this,arguments)}function _(){U.apply(this,arguments),this._timer=null,this._input=null}function ab(){Y.apply(this,arguments)}function bb(){Y.apply(this,arguments)}function cb(){U.apply(this,arguments),this.pTime=!1,this.pCenter=!1,this._timer=null,this._input=null,this.count=0}function db(a,b){return b=b||{},b.recognizers=l(b.recognizers,db.defaults.preset),new eb(a,b)}function eb(a,b){this.options=hb({},db.defaults,b||{}),this.options.inputTarget=this.options.inputTarget||a,this.handlers={},this.session={},this.recognizers=[],this.element=a,this.input=y(this),this.touchAction=new S(this,this.options.touchAction),fb(this,!0),g(this.options.recognizers,function(a){var b=this.add(new a[0](a[1]));a[2]&&b.recognizeWith(a[2]),a[3]&&b.requireFailure(a[3])},this)}function fb(a,b){var c=a.element;c.style&&g(a.options.cssProps,function(a,d){c.style[u(c.style,d)]=b?a:""})}function gb(a,c){var d=b.createEvent("Event");d.initEvent(a,!0,!0),d.gesture=c,c.target.dispatchEvent(d)}var hb,ib=["","webkit","Moz","MS","ms","o"],jb=b.createElement("div"),kb="function",lb=Math.round,mb=Math.abs,nb=Date.now;hb="function"!=typeof Object.assign?function(a){if(a===d||null===a)throw new TypeError("Cannot convert undefined or null to object");for(var b=Object(a),c=1;c<arguments.length;c++){var e=arguments[c];if(e!==d&&null!==e)for(var f in e)e.hasOwnProperty(f)&&(b[f]=e[f])}return b}:Object.assign;var ob=h(function(a,b,c){for(var e=Object.keys(b),f=0;f<e.length;)(!c||c&&a[e[f]]===d)&&(a[e[f]]=b[e[f]]),f++;return a},"extend","Use `assign`."),pb=h(function(a,b){return ob(a,b,!0)},"merge","Use `assign`."),qb=1,rb=/mobile|tablet|ip(ad|hone|od)|android/i,sb="ontouchstart"in a,tb=u(a,"PointerEvent")!==d,ub=sb&&rb.test(navigator.userAgent),vb="touch",wb="pen",xb="mouse",yb="kinect",zb=25,Ab=1,Bb=2,Cb=4,Db=8,Eb=1,Fb=2,Gb=4,Hb=8,Ib=16,Jb=Fb|Gb,Kb=Hb|Ib,Lb=Jb|Kb,Mb=["x","y"],Nb=["clientX","clientY"];x.prototype={handler:function(){},init:function(){this.evEl&&m(this.element,this.evEl,this.domHandler),this.evTarget&&m(this.target,this.evTarget,this.domHandler),this.evWin&&m(w(this.element),this.evWin,this.domHandler)},destroy:function(){this.evEl&&n(this.element,this.evEl,this.domHandler),this.evTarget&&n(this.target,this.evTarget,this.domHandler),this.evWin&&n(w(this.element),this.evWin,this.domHandler)}};var Ob={mousedown:Ab,mousemove:Bb,mouseup:Cb},Pb="mousedown",Qb="mousemove mouseup";i(L,x,{handler:function(a){var b=Ob[a.type];b&Ab&&0===a.button&&(this.pressed=!0),b&Bb&&1!==a.which&&(b=Cb),this.pressed&&this.allow&&(b&Cb&&(this.pressed=!1),this.callback(this.manager,b,{pointers:[a],changedPointers:[a],pointerType:xb,srcEvent:a}))}});var Rb={pointerdown:Ab,pointermove:Bb,pointerup:Cb,pointercancel:Db,pointerout:Db},Sb={2:vb,3:wb,4:xb,5:yb},Tb="pointerdown",Ub="pointermove pointerup pointercancel";a.MSPointerEvent&&!a.PointerEvent&&(Tb="MSPointerDown",Ub="MSPointerMove MSPointerUp MSPointerCancel"),i(M,x,{handler:function(a){var b=this.store,c=!1,d=a.type.toLowerCase().replace("ms",""),e=Rb[d],f=Sb[a.pointerType]||a.pointerType,g=f==vb,h=r(b,a.pointerId,"pointerId");e&Ab&&(0===a.button||g)?0>h&&(b.push(a),h=b.length-1):e&(Cb|Db)&&(c=!0),0>h||(b[h]=a,this.callback(this.manager,e,{pointers:b,changedPointers:[a],pointerType:f,srcEvent:a}),c&&b.splice(h,1))}});var Vb={touchstart:Ab,touchmove:Bb,touchend:Cb,touchcancel:Db},Wb="touchstart",Xb="touchstart touchmove touchend touchcancel";i(N,x,{handler:function(a){var b=Vb[a.type];if(b===Ab&&(this.started=!0),this.started){var c=O.call(this,a,b);b&(Cb|Db)&&c[0].length-c[1].length===0&&(this.started=!1),this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:vb,srcEvent:a})}}});var Yb={touchstart:Ab,touchmove:Bb,touchend:Cb,touchcancel:Db},Zb="touchstart touchmove touchend touchcancel";i(P,x,{handler:function(a){var b=Yb[a.type],c=Q.call(this,a,b);c&&this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:vb,srcEvent:a})}}),i(R,x,{handler:function(a,b,c){var d=c.pointerType==vb,e=c.pointerType==xb;if(d)this.mouse.allow=!1;else if(e&&!this.mouse.allow)return;b&(Cb|Db)&&(this.mouse.allow=!0),this.callback(a,b,c)},destroy:function(){this.touch.destroy(),this.mouse.destroy()}});var $b=u(jb.style,"touchAction"),_b=$b!==d,ac="compute",bc="auto",cc="manipulation",dc="none",ec="pan-x",fc="pan-y";S.prototype={set:function(a){a==ac&&(a=this.compute()),_b&&this.manager.element.style&&(this.manager.element.style[$b]=a),this.actions=a.toLowerCase().trim()},update:function(){this.set(this.manager.options.touchAction)},compute:function(){var a=[];return g(this.manager.recognizers,function(b){k(b.options.enable,[b])&&(a=a.concat(b.getTouchAction()))}),T(a.join(" "))},preventDefaults:function(a){if(!_b){var b=a.srcEvent,c=a.offsetDirection;if(this.manager.session.prevented)return void b.preventDefault();var d=this.actions,e=p(d,dc),f=p(d,fc),g=p(d,ec);if(e){var h=1===a.pointers.length,i=a.distance<2,j=a.deltaTime<250;if(h&&i&&j)return}if(!g||!f)return e||f&&c&Jb||g&&c&Kb?this.preventSrc(b):void 0}},preventSrc:function(a){this.manager.session.prevented=!0,a.preventDefault()}};var gc=1,hc=2,ic=4,jc=8,kc=jc,lc=16,mc=32;U.prototype={defaults:{},set:function(a){return hb(this.options,a),this.manager&&this.manager.touchAction.update(),this},recognizeWith:function(a){if(f(a,"recognizeWith",this))return this;var b=this.simultaneous;return a=X(a,this),b[a.id]||(b[a.id]=a,a.recognizeWith(this)),this},dropRecognizeWith:function(a){return f(a,"dropRecognizeWith",this)?this:(a=X(a,this),delete this.simultaneous[a.id],this)},requireFailure:function(a){if(f(a,"requireFailure",this))return this;var b=this.requireFail;return a=X(a,this),-1===r(b,a)&&(b.push(a),a.requireFailure(this)),this},dropRequireFailure:function(a){if(f(a,"dropRequireFailure",this))return this;a=X(a,this);var b=r(this.requireFail,a);return b>-1&&this.requireFail.splice(b,1),this},hasRequireFailures:function(){return this.requireFail.length>0},canRecognizeWith:function(a){return!!this.simultaneous[a.id]},emit:function(a){function b(b){c.manager.emit(b,a)}var c=this,d=this.state;jc>d&&b(c.options.event+V(d)),b(c.options.event),a.additionalEvent&&b(a.additionalEvent),d>=jc&&b(c.options.event+V(d))},tryEmit:function(a){return this.canEmit()?this.emit(a):void(this.state=mc)},canEmit:function(){for(var a=0;a<this.requireFail.length;){if(!(this.requireFail[a].state&(mc|gc)))return!1;a++}return!0},recognize:function(a){var b=hb({},a);return k(this.options.enable,[this,b])?(this.state&(kc|lc|mc)&&(this.state=gc),this.state=this.process(b),void(this.state&(hc|ic|jc|lc)&&this.tryEmit(b))):(this.reset(),void(this.state=mc))},process:function(){},getTouchAction:function(){},reset:function(){}},i(Y,U,{defaults:{pointers:1},attrTest:function(a){var b=this.options.pointers;return 0===b||a.pointers.length===b},process:function(a){var b=this.state,c=a.eventType,d=b&(hc|ic),e=this.attrTest(a);return d&&(c&Db||!e)?b|lc:d||e?c&Cb?b|jc:b&hc?b|ic:hc:mc}}),i(Z,Y,{defaults:{event:"pan",threshold:10,pointers:1,direction:Lb},getTouchAction:function(){var a=this.options.direction,b=[];return a&Jb&&b.push(fc),a&Kb&&b.push(ec),b},directionTest:function(a){var b=this.options,c=!0,d=a.distance,e=a.direction,f=a.deltaX,g=a.deltaY;return e&b.direction||(b.direction&Jb?(e=0===f?Eb:0>f?Fb:Gb,c=f!=this.pX,d=Math.abs(a.deltaX)):(e=0===g?Eb:0>g?Hb:Ib,c=g!=this.pY,d=Math.abs(a.deltaY))),a.direction=e,c&&d>b.threshold&&e&b.direction},attrTest:function(a){return Y.prototype.attrTest.call(this,a)&&(this.state&hc||!(this.state&hc)&&this.directionTest(a))},emit:function(a){this.pX=a.deltaX,this.pY=a.deltaY;var b=W(a.direction);b&&(a.additionalEvent=this.options.event+b),this._super.emit.call(this,a)}}),i($,Y,{defaults:{event:"pinch",threshold:0,pointers:2},getTouchAction:function(){return[dc]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.scale-1)>this.options.threshold||this.state&hc)},emit:function(a){if(1!==a.scale){var b=a.scale<1?"in":"out";a.additionalEvent=this.options.event+b}this._super.emit.call(this,a)}}),i(_,U,{defaults:{event:"press",pointers:1,time:251,threshold:9},getTouchAction:function(){return[bc]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime>b.time;if(this._input=a,!d||!c||a.eventType&(Cb|Db)&&!f)this.reset();else if(a.eventType&Ab)this.reset(),this._timer=e(function(){this.state=kc,this.tryEmit()},b.time,this);else if(a.eventType&Cb)return kc;return mc},reset:function(){clearTimeout(this._timer)},emit:function(a){this.state===kc&&(a&&a.eventType&Cb?this.manager.emit(this.options.event+"up",a):(this._input.timeStamp=nb(),this.manager.emit(this.options.event,this._input)))}}),i(ab,Y,{defaults:{event:"rotate",threshold:0,pointers:2},getTouchAction:function(){return[dc]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.rotation)>this.options.threshold||this.state&hc)}}),i(bb,Y,{defaults:{event:"swipe",threshold:10,velocity:.3,direction:Jb|Kb,pointers:1},getTouchAction:function(){return Z.prototype.getTouchAction.call(this)},attrTest:function(a){var b,c=this.options.direction;return c&(Jb|Kb)?b=a.overallVelocity:c&Jb?b=a.overallVelocityX:c&Kb&&(b=a.overallVelocityY),this._super.attrTest.call(this,a)&&c&a.offsetDirection&&a.distance>this.options.threshold&&a.maxPointers==this.options.pointers&&mb(b)>this.options.velocity&&a.eventType&Cb},emit:function(a){var b=W(a.offsetDirection);b&&this.manager.emit(this.options.event+b,a),this.manager.emit(this.options.event,a)}}),i(cb,U,{defaults:{event:"tap",pointers:1,taps:1,interval:300,time:250,threshold:9,posThreshold:10},getTouchAction:function(){return[cc]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,f=a.deltaTime<b.time;if(this.reset(),a.eventType&Ab&&0===this.count)return this.failTimeout();if(d&&f&&c){if(a.eventType!=Cb)return this.failTimeout();var g=this.pTime?a.timeStamp-this.pTime<b.interval:!0,h=!this.pCenter||H(this.pCenter,a.center)<b.posThreshold;this.pTime=a.timeStamp,this.pCenter=a.center,h&&g?this.count+=1:this.count=1,this._input=a;var i=this.count%b.taps;if(0===i)return this.hasRequireFailures()?(this._timer=e(function(){this.state=kc,this.tryEmit()},b.interval,this),hc):kc}return mc},failTimeout:function(){return this._timer=e(function(){this.state=mc},this.options.interval,this),mc},reset:function(){clearTimeout(this._timer)},emit:function(){this.state==kc&&(this._input.tapCount=this.count,this.manager.emit(this.options.event,this._input))}}),db.VERSION="2.0.6",db.defaults={domEvents:!1,touchAction:ac,enable:!0,inputTarget:null,inputClass:null,preset:[[ab,{enable:!1}],[$,{enable:!1},["rotate"]],[bb,{direction:Jb}],[Z,{direction:Jb},["swipe"]],[cb],[cb,{event:"doubletap",taps:2},["tap"]],[_]],cssProps:{userSelect:"none",touchSelect:"none",touchCallout:"none",contentZooming:"none",userDrag:"none",tapHighlightColor:"rgba(0,0,0,0)"}};var nc=1,oc=2;eb.prototype={set:function(a){return hb(this.options,a),a.touchAction&&this.touchAction.update(),a.inputTarget&&(this.input.destroy(),this.input.target=a.inputTarget,this.input.init()),this},stop:function(a){this.session.stopped=a?oc:nc},recognize:function(a){var b=this.session;if(!b.stopped){this.touchAction.preventDefaults(a);var c,d=this.recognizers,e=b.curRecognizer;(!e||e&&e.state&kc)&&(e=b.curRecognizer=null);for(var f=0;f<d.length;)c=d[f],b.stopped===oc||e&&c!=e&&!c.canRecognizeWith(e)?c.reset():c.recognize(a),!e&&c.state&(hc|ic|jc)&&(e=b.curRecognizer=c),f++}},get:function(a){if(a instanceof U)return a;for(var b=this.recognizers,c=0;c<b.length;c++)if(b[c].options.event==a)return b[c];return null},add:function(a){if(f(a,"add",this))return this;var b=this.get(a.options.event);return b&&this.remove(b),this.recognizers.push(a),a.manager=this,this.touchAction.update(),a},remove:function(a){if(f(a,"remove",this))return this;if(a=this.get(a)){var b=this.recognizers,c=r(b,a);-1!==c&&(b.splice(c,1),this.touchAction.update())}return this},on:function(a,b){var c=this.handlers;return g(q(a),function(a){c[a]=c[a]||[],c[a].push(b)}),this},off:function(a,b){var c=this.handlers;return g(q(a),function(a){b?c[a]&&c[a].splice(r(c[a],b),1):delete c[a]}),this},emit:function(a,b){this.options.domEvents&&gb(a,b);var c=this.handlers[a]&&this.handlers[a].slice();if(c&&c.length){b.type=a,b.preventDefault=function(){b.srcEvent.preventDefault()};for(var d=0;d<c.length;)c[d](b),d++}},destroy:function(){this.element&&fb(this,!1),this.handlers={},this.session={},this.input.destroy(),this.element=null}},hb(db,{INPUT_START:Ab,INPUT_MOVE:Bb,INPUT_END:Cb,INPUT_CANCEL:Db,STATE_POSSIBLE:gc,STATE_BEGAN:hc,STATE_CHANGED:ic,STATE_ENDED:jc,STATE_RECOGNIZED:kc,STATE_CANCELLED:lc,STATE_FAILED:mc,DIRECTION_NONE:Eb,DIRECTION_LEFT:Fb,DIRECTION_RIGHT:Gb,DIRECTION_UP:Hb,DIRECTION_DOWN:Ib,DIRECTION_HORIZONTAL:Jb,DIRECTION_VERTICAL:Kb,DIRECTION_ALL:Lb,Manager:eb,Input:x,TouchAction:S,TouchInput:P,MouseInput:L,PointerEventInput:M,TouchMouseInput:R,SingleTouchInput:N,Recognizer:U,AttrRecognizer:Y,Tap:cb,Pan:Z,Swipe:bb,Pinch:$,Rotate:ab,Press:_,on:m,off:n,each:g,merge:pb,extend:ob,assign:hb,inherit:i,bindFn:j,prefixed:u});var pc="undefined"!=typeof a?a:"undefined"!=typeof self?self:{};pc.Hammer=db,"function"==typeof define&&define.amd?define(function(){return db}):"undefined"!=typeof module&&module.exports?module.exports=db:a[c]=db}(window,document,"Hammer");
//# sourceMappingURL=hammer.min.map;/* js/libs-src/confirmation.js */
jQuery( document ).ready(function() {   
    jQuery(':input',document.myForm).bind("change", function() { 
       setConfirmUnload(true); 
    }); // Prevent accidental navigation away
});

function setConfirmUnload(on) {
     // To avoid IE7 and prior jQuery version issues   
     // we are directly using window.onbeforeunload event
     window.onbeforeunload = (on) ? unloadMessage : null;
}

function unloadMessage() {

    if(Confirm('You have entered new data on this page.  If you navigate away from this page without first saving your data, the changes will be lost.')) {

            jQuery.ajax({
               type: "POST",
               url: "some.php",
               data: "name=John&location=Boston",
               success: function(msg){
                 alert( "Data Saved: " + msg );
               }
             });

    }

};/* js/libs-src/jquery.countdown.js */
/* http://keith-wood.name/countdown.html
   Countdown for jQuery v1.6.1.
   Written by Keith Wood (kbwood{at}iinet.com.au) January 2008.
   Available under the MIT (https://github.com/jquery/jquery/blob/master/MIT-LICENSE.txt) license.
   Please attribute the author if you use it. */

/* Display a countdown timer.
   Attach it with options like:
   $('div selector').countdown(
       {until: new Date(2009, 1 - 1, 1, 0, 0, 0), onExpiry: happyNewYear}); */

(function($) { // Hide scope, no $ conflict

/* Countdown manager. */
function Countdown() {
	this.regional = []; // Available regional settings, indexed by language code
	this.regional[''] = { // Default regional settings
		// The display texts for the counters
		labels: ['Years', 'Months', 'Weeks', 'Days', 'Hours', 'Minutes', 'Seconds'],
		// The display texts for the counters if only one
		labels1: ['Year', 'Month', 'Week', 'Day', 'Hour', 'Minute', 'Second'],
		compactLabels: ['y', 'm', 'w', 'd'], // The compact texts for the counters
		whichLabels: null, // Function to determine which labels to use
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'], // The digits to display
		timeSeparator: ':', // Separator for time periods
		isRTL: false // True for right-to-left languages, false for left-to-right
	};
	this._defaults = {
		until: null, // new Date(year, mth - 1, day, hr, min, sec) - date/time to count down to
			// or numeric for seconds offset, or string for unit offset(s):
			// 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds
		since: null, // new Date(year, mth - 1, day, hr, min, sec) - date/time to count up from
			// or numeric for seconds offset, or string for unit offset(s):
			// 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds
		timezone: null, // The timezone (hours or minutes from GMT) for the target times,
			// or null for client local
		serverSync: null, // A function to retrieve the current server time for synchronisation
		format: 'dHMS', // Format for display - upper case for always, lower case only if non-zero,
			// 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds
		layout: '', // Build your own layout for the countdown
		compact: false, // True to display in a compact format, false for an expanded one
		significant: 0, // The number of periods with values to show, zero for all
		description: '', // The description displayed for the countdown
		expiryUrl: '', // A URL to load upon expiry, replacing the current page
		expiryText: '', // Text to display upon expiry, replacing the countdown
		alwaysExpire: false, // True to trigger onExpiry even if never counted down
		onExpiry: null, // Callback when the countdown expires -
			// receives no parameters and 'this' is the containing division
		onTick: null, // Callback when the countdown is updated -
			// receives int[7] being the breakdown by period (based on format)
			// and 'this' is the containing division
		tickInterval: 1 // Interval (seconds) between onTick callbacks
	};
	$.extend(this._defaults, this.regional['']);
	this._serverSyncs = [];
	// Shared timer for all countdowns
	function timerCallBack(timestamp) {
		var drawStart = (timestamp < 1e12 ? // New HTML5 high resolution timer
			(drawStart = performance.now ?
			(performance.now() + performance.timing.navigationStart) : Date.now()) :
			// Integer milliseconds since unix epoch
			timestamp || new Date().getTime());
		if (drawStart - animationStartTime >= 1000) {
			plugin._updateTargets();
			animationStartTime = drawStart;
		}
		requestAnimationFrame(timerCallBack);
	}
	var requestAnimationFrame = window.requestAnimationFrame ||
		window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame ||
		window.oRequestAnimationFrame || window.msRequestAnimationFrame || null;
		// This is when we expect a fall-back to setInterval as it's much more fluid
	var animationStartTime = 0;
	if (!requestAnimationFrame || $.noRequestAnimationFrame) {
		$.noRequestAnimationFrame = null;
		setInterval(function() { plugin._updateTargets(); }, 980); // Fall back to good old setInterval
	}
	else {
		animationStartTime = window.animationStartTime ||
			window.webkitAnimationStartTime || window.mozAnimationStartTime ||
			window.oAnimationStartTime || window.msAnimationStartTime || new Date().getTime();
		requestAnimationFrame(timerCallBack);
	}
}

var Y = 0; // Years
var O = 1; // Months
var W = 2; // Weeks
var D = 3; // Days
var H = 4; // Hours
var M = 5; // Minutes
var S = 6; // Seconds

$.extend(Countdown.prototype, {
	/* Class name added to elements to indicate already configured with countdown. */
	markerClassName: 'hasCountdown',
	/* Name of the data property for instance settings. */
	propertyName: 'countdown',

	/* Class name for the right-to-left marker. */
	_rtlClass: 'countdown_rtl',
	/* Class name for the countdown section marker. */
	_sectionClass: 'countdown_section',
	/* Class name for the period amount marker. */
	_amountClass: 'countdown_amount',
	/* Class name for the countdown row marker. */
	_rowClass: 'countdown_row',
	/* Class name for the holding countdown marker. */
	_holdingClass: 'countdown_holding',
	/* Class name for the showing countdown marker. */
	_showClass: 'countdown_show',
	/* Class name for the description marker. */
	_descrClass: 'countdown_descr',

	/* List of currently active countdown targets. */
	_timerTargets: [],

	/* Override the default settings for all instances of the countdown widget.
	   @param  options  (object) the new settings to use as defaults */
	setDefaults: function(options) {
		this._resetExtraLabels(this._defaults, options);
		$.extend(this._defaults, options || {});
	},

	/* Convert a date/time to UTC.
	   @param  tz     (number) the hour or minute offset from GMT, e.g. +9, -360
	   @param  year   (Date) the date/time in that timezone or
	                  (number) the year in that timezone
	   @param  month  (number, optional) the month (0 - 11) (omit if year is a Date)
	   @param  day    (number, optional) the day (omit if year is a Date)
	   @param  hours  (number, optional) the hour (omit if year is a Date)
	   @param  mins   (number, optional) the minute (omit if year is a Date)
	   @param  secs   (number, optional) the second (omit if year is a Date)
	   @param  ms     (number, optional) the millisecond (omit if year is a Date)
	   @return  (Date) the equivalent UTC date/time */
	UTCDate: function(tz, year, month, day, hours, mins, secs, ms) {
		if (typeof year == 'object' && year.constructor == Date) {
			ms = year.getMilliseconds();
			secs = year.getSeconds();
			mins = year.getMinutes();
			hours = year.getHours();
			day = year.getDate();
			month = year.getMonth();
			year = year.getFullYear();
		}
		var d = new Date();
		d.setUTCFullYear(year);
		d.setUTCDate(1);
		d.setUTCMonth(month || 0);
		d.setUTCDate(day || 1);
		d.setUTCHours(hours || 0);
		d.setUTCMinutes((mins || 0) - (Math.abs(tz) < 30 ? tz * 60 : tz));
		d.setUTCSeconds(secs || 0);
		d.setUTCMilliseconds(ms || 0);
		return d;
	},

	/* Convert a set of periods into seconds.
	   Averaged for months and years.
	   @param  periods  (number[7]) the periods per year/month/week/day/hour/minute/second
	   @return  (number) the corresponding number of seconds */
	periodsToSeconds: function(periods) {
		return periods[0] * 31557600 + periods[1] * 2629800 + periods[2] * 604800 +
			periods[3] * 86400 + periods[4] * 3600 + periods[5] * 60 + periods[6];
	},

	/* Attach the countdown widget to a div.
	   @param  target   (element) the containing division
	   @param  options  (object) the initial settings for the countdown */
	_attachPlugin: function(target, options) {
		target = $(target);
		if (target.hasClass(this.markerClassName)) {
			return;
		}
		var inst = {options: $.extend({}, this._defaults), _periods: [0, 0, 0, 0, 0, 0, 0]};
		target.addClass(this.markerClassName).data(this.propertyName, inst);
		this._optionPlugin(target, options);
	},

	/* Add a target to the list of active ones.
	   @param  target  (element) the countdown target */
	_addTarget: function(target) {
		if (!this._hasTarget(target)) {
			this._timerTargets.push(target);
		}
	},

	/* See if a target is in the list of active ones.
	   @param  target  (element) the countdown target
	   @return  (boolean) true if present, false if not */
	_hasTarget: function(target) {
		return ($.inArray(target, this._timerTargets) > -1);
	},

	/* Remove a target from the list of active ones.
	   @param  target  (element) the countdown target */
	_removeTarget: function(target) {
		this._timerTargets = $.map(this._timerTargets,
			function(value) { return (value == target ? null : value); }); // delete entry
	},

	/* Update each active timer target. */
	_updateTargets: function() {
		for (var i = this._timerTargets.length - 1; i >= 0; i--) {
			this._updateCountdown(this._timerTargets[i]);
		}
	},

	/* Reconfigure the settings for a countdown div.
	   @param  target   (element) the control to affect
	   @param  options  (object) the new options for this instance or
	                    (string) an individual property name
	   @param  value    (any) the individual property value (omit if options
	                    is an object or to retrieve the value of a setting)
	   @return  (any) if retrieving a value */
	_optionPlugin: function(target, options, value) {
		target = $(target);
		var inst = target.data(this.propertyName);
		if (!options || (typeof options == 'string' && value == null)) { // Get option
			var name = options;
			options = (inst || {}).options;
			return (options && name ? options[name] : options);
		}

		if (!target.hasClass(this.markerClassName)) {
			return;
		}
		options = options || {};
		if (typeof options == 'string') {
			var name = options;
			options = {};
			options[name] = value;
		}
		this._resetExtraLabels(inst.options, options);
		$.extend(inst.options, options);
		this._adjustSettings(target, inst);
		var now = new Date();
		if ((inst._since && inst._since < now) || (inst._until && inst._until > now)) {
			this._addTarget(target[0]);
		}
		this._updateCountdown(target, inst);
	},

	/* Redisplay the countdown with an updated display.
	   @param  target  (jQuery) the containing division
	   @param  inst    (object) the current settings for this instance */
	_updateCountdown: function(target, inst) {
		var $target = $(target);
		inst = inst || $target.data(this.propertyName);
		if (!inst) {
			return;
		}
		$target.html(this._generateHTML(inst)).toggleClass(this._rtlClass, inst.options.isRTL);
		if ($.isFunction(inst.options.onTick)) {
			var periods = inst._hold != 'lap' ? inst._periods :
				this._calculatePeriods(inst, inst._show, inst.options.significant, new Date());
			if (inst.options.tickInterval == 1 ||
					this.periodsToSeconds(periods) % inst.options.tickInterval == 0) {
				inst.options.onTick.apply(target, [periods]);
			}
		}
		var expired = inst._hold != 'pause' &&
			(inst._since ? inst._now.getTime() < inst._since.getTime() :
			inst._now.getTime() >= inst._until.getTime());
		if (expired && !inst._expiring) {
			inst._expiring = true;
			if (this._hasTarget(target) || inst.options.alwaysExpire) {
				this._removeTarget(target);
				if ($.isFunction(inst.options.onExpiry)) {
					inst.options.onExpiry.apply(target, []);
				}
				if (inst.options.expiryText) {
					var layout = inst.options.layout;
					inst.options.layout = inst.options.expiryText;
					this._updateCountdown(target, inst);
					inst.options.layout = layout;
				}
				if (inst.options.expiryUrl) {
					window.location = inst.options.expiryUrl;
				}
			}
			inst._expiring = false;
		}
		else if (inst._hold == 'pause') {
			this._removeTarget(target);
		}
		$target.data(this.propertyName, inst);
	},

	/* Reset any extra labelsn and compactLabelsn entries if changing labels.
	   @param  base     (object) the options to be updated
	   @param  options  (object) the new option values */
	_resetExtraLabels: function(base, options) {
		var changingLabels = false;
		for (var n in options) {
			if (n != 'whichLabels' && n.match(/[Ll]abels/)) {
				changingLabels = true;
				break;
			}
		}
		if (changingLabels) {
			for (var n in base) { // Remove custom numbered labels
				if (n.match(/[Ll]abels[02-9]/)) {
					base[n] = null;
				}
			}
		}
	},

	/* Calculate interal settings for an instance.
	   @param  target  (element) the containing division
	   @param  inst    (object) the current settings for this instance */
	_adjustSettings: function(target, inst) {
		var now;
		var serverOffset = 0;
		var serverEntry = null;
		for (var i = 0; i < this._serverSyncs.length; i++) {
			if (this._serverSyncs[i][0] == inst.options.serverSync) {
				serverEntry = this._serverSyncs[i][1];
				break;
			}
		}
		if (serverEntry != null) {
			serverOffset = (inst.options.serverSync ? serverEntry : 0);
			now = new Date();
		}
		else {
			var serverResult = ($.isFunction(inst.options.serverSync) ?
				inst.options.serverSync.apply(target, []) : null);
			now = new Date();
			serverOffset = (serverResult ? now.getTime() - serverResult.getTime() : 0);
			this._serverSyncs.push([inst.options.serverSync, serverOffset]);
		}
		var timezone = inst.options.timezone;
		timezone = (timezone == null ? -now.getTimezoneOffset() : timezone);
		inst._since = inst.options.since;
		if (inst._since != null) {
			inst._since = this.UTCDate(timezone, this._determineTime(inst._since, null));
			if (inst._since && serverOffset) {
				inst._since.setMilliseconds(inst._since.getMilliseconds() + serverOffset);
			}
		}
		inst._until = this.UTCDate(timezone, this._determineTime(inst.options.until, now));
		if (serverOffset) {
			inst._until.setMilliseconds(inst._until.getMilliseconds() + serverOffset);
		}
		inst._show = this._determineShow(inst);
	},

	/* Remove the countdown widget from a div.
	   @param  target  (element) the containing division */
	_destroyPlugin: function(target) {
		target = $(target);
		if (!target.hasClass(this.markerClassName)) {
			return;
		}
		this._removeTarget(target[0]);
		target.removeClass(this.markerClassName).empty().removeData(this.propertyName);
	},

	/* Pause a countdown widget at the current time.
	   Stop it running but remember and display the current time.
	   @param  target  (element) the containing division */
	_pausePlugin: function(target) {
		this._hold(target, 'pause');
	},

	/* Pause a countdown widget at the current time.
	   Stop the display but keep the countdown running.
	   @param  target  (element) the containing division */
	_lapPlugin: function(target) {
		this._hold(target, 'lap');
	},

	/* Resume a paused countdown widget.
	   @param  target  (element) the containing division */
	_resumePlugin: function(target) {
		this._hold(target, null);
	},

	/* Pause or resume a countdown widget.
	   @param  target  (element) the containing division
	   @param  hold    (string) the new hold setting */
	_hold: function(target, hold) {
		var inst = $.data(target, this.propertyName);
		if (inst) {
			if (inst._hold == 'pause' && !hold) {
				inst._periods = inst._savePeriods;
				var sign = (inst._since ? '-' : '+');
				inst[inst._since ? '_since' : '_until'] =
					this._determineTime(sign + inst._periods[0] + 'y' +
						sign + inst._periods[1] + 'o' + sign + inst._periods[2] + 'w' +
						sign + inst._periods[3] + 'd' + sign + inst._periods[4] + 'h' +
						sign + inst._periods[5] + 'm' + sign + inst._periods[6] + 's');
				this._addTarget(target);
			}
			inst._hold = hold;
			inst._savePeriods = (hold == 'pause' ? inst._periods : null);
			$.data(target, this.propertyName, inst);
			this._updateCountdown(target, inst);
		}
	},

	/* Return the current time periods.
	   @param  target  (element) the containing division
	   @return  (number[7]) the current periods for the countdown */
	_getTimesPlugin: function(target) {
		var inst = $.data(target, this.propertyName);
		return (!inst ? null : (!inst._hold ? inst._periods :
			this._calculatePeriods(inst, inst._show, inst.options.significant, new Date())));
	},

	/* A time may be specified as an exact value or a relative one.
	   @param  setting      (string or number or Date) - the date/time value
	                        as a relative or absolute value
	   @param  defaultTime  (Date) the date/time to use if no other is supplied
	   @return  (Date) the corresponding date/time */
	_determineTime: function(setting, defaultTime) {
		var offsetNumeric = function(offset) { // e.g. +300, -2
			var time = new Date();
			time.setTime(time.getTime() + offset * 1000);
			return time;
		};
		var offsetString = function(offset) { // e.g. '+2d', '-4w', '+3h +30m'
			offset = offset.toLowerCase();
			var time = new Date();
			var year = time.getFullYear();
			var month = time.getMonth();
			var day = time.getDate();
			var hour = time.getHours();
			var minute = time.getMinutes();
			var second = time.getSeconds();
			var pattern = /([+-]?[0-9]+)\s*(s|m|h|d|w|o|y)?/g;
			var matches = pattern.exec(offset);
			while (matches) {
				switch (matches[2] || 's') {
					case 's': second += parseInt(matches[1], 10); break;
					case 'm': minute += parseInt(matches[1], 10); break;
					case 'h': hour += parseInt(matches[1], 10); break;
					case 'd': day += parseInt(matches[1], 10); break;
					case 'w': day += parseInt(matches[1], 10) * 7; break;
					case 'o':
						month += parseInt(matches[1], 10);
						day = Math.min(day, plugin._getDaysInMonth(year, month));
						break;
					case 'y':
						year += parseInt(matches[1], 10);
						day = Math.min(day, plugin._getDaysInMonth(year, month));
						break;
				}
				matches = pattern.exec(offset);
			}
			return new Date(year, month, day, hour, minute, second, 0);
		};
		var time = (setting == null ? defaultTime :
			(typeof setting == 'string' ? offsetString(setting) :
			(typeof setting == 'number' ? offsetNumeric(setting) : setting)));
		if (time) time.setMilliseconds(0);
		return time;
	},

	/* Determine the number of days in a month.
	   @param  year   (number) the year
	   @param  month  (number) the month
	   @return  (number) the days in that month */
	_getDaysInMonth: function(year, month) {
		return 32 - new Date(year, month, 32).getDate();
	},

	/* Determine which set of labels should be used for an amount.
	   @param  num  (number) the amount to be displayed
	   @return  (number) the set of labels to be used for this amount */
	_normalLabels: function(num) {
		return num;
	},

	/* Generate the HTML to display the countdown widget.
	   @param  inst  (object) the current settings for this instance
	   @return  (string) the new HTML for the countdown display */
	_generateHTML: function(inst) {
		var self = this;
		// Determine what to show
		inst._periods = (inst._hold ? inst._periods :
			this._calculatePeriods(inst, inst._show, inst.options.significant, new Date()));
		// Show all 'asNeeded' after first non-zero value
		var shownNonZero = false;
		var showCount = 0;
		var sigCount = inst.options.significant;
		var show = $.extend({}, inst._show);
		for (var period = Y; period <= S; period++) {
			shownNonZero |= (inst._show[period] == '?' && inst._periods[period] > 0);
			show[period] = (inst._show[period] == '?' && !shownNonZero ? null : inst._show[period]);
			showCount += (show[period] ? 1 : 0);
			sigCount -= (inst._periods[period] > 0 ? 1 : 0);
		}
		var showSignificant = [false, false, false, false, false, false, false];
		for (var period = S; period >= Y; period--) { // Determine significant periods
			if (inst._show[period]) {
				if (inst._periods[period]) {
					showSignificant[period] = true;
				}
				else {
					showSignificant[period] = sigCount > 0;
					sigCount--;
				}
			}
		}
		var labels = (inst.options.compact ? inst.options.compactLabels : inst.options.labels);
		var whichLabels = inst.options.whichLabels || this._normalLabels;
		var showCompact = function(period) {
			var labelsNum = inst.options['compactLabels' + whichLabels(inst._periods[period])];
			return (show[period] ? self._translateDigits(inst, inst._periods[period]) +
				(labelsNum ? labelsNum[period] : labels[period]) + ' ' : '');
		};
		var showFull = function(period) {
			var labelsNum = inst.options['labels' + whichLabels(inst._periods[period])];
			return ((!inst.options.significant && show[period]) ||
				(inst.options.significant && showSignificant[period]) ?
				'<span class="' + plugin._sectionClass + '">' +
				'<span class="' + plugin._amountClass + '">' +
				self._translateDigits(inst, inst._periods[period]) + '</span><br/>' +
				(labelsNum ? labelsNum[period] : labels[period]) + '</span>' : '');
		};
		return (inst.options.layout ? this._buildLayout(inst, show, inst.options.layout,
			inst.options.compact, inst.options.significant, showSignificant) :
			((inst.options.compact ? // Compact version
			'<span class="' + this._rowClass + ' ' + this._amountClass +
			(inst._hold ? ' ' + this._holdingClass : '') + '">' +
			showCompact(Y) + showCompact(O) + showCompact(W) + showCompact(D) +
			(show[H] ? this._minDigits(inst, inst._periods[H], 2) : '') +
			(show[M] ? (show[H] ? inst.options.timeSeparator : '') +
			this._minDigits(inst, inst._periods[M], 2) : '') +
			(show[S] ? (show[H] || show[M] ? inst.options.timeSeparator : '') +
			this._minDigits(inst, inst._periods[S], 2) : '') :
			// Full version
			'<span class="' + this._rowClass + ' ' + this._showClass + (inst.options.significant || showCount) +
			(inst._hold ? ' ' + this._holdingClass : '') + '">' +
			showFull(Y) + showFull(O) + showFull(W) + showFull(D) +
			showFull(H) + showFull(M) + showFull(S)) + '</span>' +
			(inst.options.description ? '<span class="' + this._rowClass + ' ' + this._descrClass + '">' +
			inst.options.description + '</span>' : '')));
	},

	/* Construct a custom layout.
	   @param  inst             (object) the current settings for this instance
	   @param  show             (string[7]) flags indicating which periods are requested
	   @param  layout           (string) the customised layout
	   @param  compact          (boolean) true if using compact labels
	   @param  significant      (number) the number of periods with values to show, zero for all
	   @param  showSignificant  (boolean[7]) other periods to show for significance
	   @return  (string) the custom HTML */
	_buildLayout: function(inst, show, layout, compact, significant, showSignificant) {
		var labels = inst.options[compact ? 'compactLabels' : 'labels'];
		var whichLabels = inst.options.whichLabels || this._normalLabels;
		var labelFor = function(index) {
			return (inst.options[(compact ? 'compactLabels' : 'labels') +
				whichLabels(inst._periods[index])] || labels)[index];
		};
		var digit = function(value, position) {
			return inst.options.digits[Math.floor(value / position) % 10];
		};
		var subs = {desc: inst.options.description, sep: inst.options.timeSeparator,
			yl: labelFor(Y), yn: this._minDigits(inst, inst._periods[Y], 1),
			ynn: this._minDigits(inst, inst._periods[Y], 2),
			ynnn: this._minDigits(inst, inst._periods[Y], 3), y1: digit(inst._periods[Y], 1),
			y10: digit(inst._periods[Y], 10), y100: digit(inst._periods[Y], 100),
			y1000: digit(inst._periods[Y], 1000),
			ol: labelFor(O), on: this._minDigits(inst, inst._periods[O], 1),
			onn: this._minDigits(inst, inst._periods[O], 2),
			onnn: this._minDigits(inst, inst._periods[O], 3), o1: digit(inst._periods[O], 1),
			o10: digit(inst._periods[O], 10), o100: digit(inst._periods[O], 100),
			o1000: digit(inst._periods[O], 1000),
			wl: labelFor(W), wn: this._minDigits(inst, inst._periods[W], 1),
			wnn: this._minDigits(inst, inst._periods[W], 2),
			wnnn: this._minDigits(inst, inst._periods[W], 3), w1: digit(inst._periods[W], 1),
			w10: digit(inst._periods[W], 10), w100: digit(inst._periods[W], 100),
			w1000: digit(inst._periods[W], 1000),
			dl: labelFor(D), dn: this._minDigits(inst, inst._periods[D], 1),
			dnn: this._minDigits(inst, inst._periods[D], 2),
			dnnn: this._minDigits(inst, inst._periods[D], 3), d1: digit(inst._periods[D], 1),
			d10: digit(inst._periods[D], 10), d100: digit(inst._periods[D], 100),
			d1000: digit(inst._periods[D], 1000),
			hl: labelFor(H), hn: this._minDigits(inst, inst._periods[H], 1),
			hnn: this._minDigits(inst, inst._periods[H], 2),
			hnnn: this._minDigits(inst, inst._periods[H], 3), h1: digit(inst._periods[H], 1),
			h10: digit(inst._periods[H], 10), h100: digit(inst._periods[H], 100),
			h1000: digit(inst._periods[H], 1000),
			ml: labelFor(M), mn: this._minDigits(inst, inst._periods[M], 1),
			mnn: this._minDigits(inst, inst._periods[M], 2),
			mnnn: this._minDigits(inst, inst._periods[M], 3), m1: digit(inst._periods[M], 1),
			m10: digit(inst._periods[M], 10), m100: digit(inst._periods[M], 100),
			m1000: digit(inst._periods[M], 1000),
			sl: labelFor(S), sn: this._minDigits(inst, inst._periods[S], 1),
			snn: this._minDigits(inst, inst._periods[S], 2),
			snnn: this._minDigits(inst, inst._periods[S], 3), s1: digit(inst._periods[S], 1),
			s10: digit(inst._periods[S], 10), s100: digit(inst._periods[S], 100),
			s1000: digit(inst._periods[S], 1000)};
		var html = layout;
		// Replace period containers: {p<}...{p>}
		for (var i = Y; i <= S; i++) {
			var period = 'yowdhms'.charAt(i);
			var re = new RegExp('\\{' + period + '<\\}(.*)\\{' + period + '>\\}', 'g');
			html = html.replace(re, ((!significant && show[i]) ||
				(significant && showSignificant[i]) ? '$1' : ''));
		}
		// Replace period values: {pn}
		$.each(subs, function(n, v) {
			var re = new RegExp('\\{' + n + '\\}', 'g');
			html = html.replace(re, v);
		});
		return html;
	},

	/* Ensure a numeric value has at least n digits for display.
	   @param  inst   (object) the current settings for this instance
	   @param  value  (number) the value to display
	   @param  len    (number) the minimum length
	   @return  (string) the display text */
	_minDigits: function(inst, value, len) {
		value = '' + value;
		if (value.length >= len) {
			return this._translateDigits(inst, value);
		}
		value = '0000000000' + value;
		return this._translateDigits(inst, value.substr(value.length - len));
	},

	/* Translate digits into other representations.
	   @param  inst   (object) the current settings for this instance
	   @param  value  (string) the text to translate
	   @return  (string) the translated text */
	_translateDigits: function(inst, value) {
		return ('' + value).replace(/[0-9]/g, function(digit) {
				return inst.options.digits[digit];
			});
	},

	/* Translate the format into flags for each period.
	   @param  inst  (object) the current settings for this instance
	   @return  (string[7]) flags indicating which periods are requested (?) or
	            required (!) by year, month, week, day, hour, minute, second */
	_determineShow: function(inst) {
		var format = inst.options.format;
		var show = [];
		show[Y] = (format.match('y') ? '?' : (format.match('Y') ? '!' : null));
		show[O] = (format.match('o') ? '?' : (format.match('O') ? '!' : null));
		show[W] = (format.match('w') ? '?' : (format.match('W') ? '!' : null));
		show[D] = (format.match('d') ? '?' : (format.match('D') ? '!' : null));
		show[H] = (format.match('h') ? '?' : (format.match('H') ? '!' : null));
		show[M] = (format.match('m') ? '?' : (format.match('M') ? '!' : null));
		show[S] = (format.match('s') ? '?' : (format.match('S') ? '!' : null));
		return show;
	},

	/* Calculate the requested periods between now and the target time.
	   @param  inst         (object) the current settings for this instance
	   @param  show         (string[7]) flags indicating which periods are requested/required
	   @param  significant  (number) the number of periods with values to show, zero for all
	   @param  now          (Date) the current date and time
	   @return  (number[7]) the current time periods (always positive)
	            by year, month, week, day, hour, minute, second */
	_calculatePeriods: function(inst, show, significant, now) {
		// Find endpoints
		inst._now = now;
		inst._now.setMilliseconds(0);
		var until = new Date(inst._now.getTime());
		if (inst._since) {
			if (now.getTime() < inst._since.getTime()) {
				inst._now = now = until;
			}
			else {
				now = inst._since;
			}
		}
		else {
			until.setTime(inst._until.getTime());
			if (now.getTime() > inst._until.getTime()) {
				inst._now = now = until;
			}
		}
		// Calculate differences by period
		var periods = [0, 0, 0, 0, 0, 0, 0];
		if (show[Y] || show[O]) {
			// Treat end of months as the same
			var lastNow = plugin._getDaysInMonth(now.getFullYear(), now.getMonth());
			var lastUntil = plugin._getDaysInMonth(until.getFullYear(), until.getMonth());
			var sameDay = (until.getDate() == now.getDate() ||
				(until.getDate() >= Math.min(lastNow, lastUntil) &&
				now.getDate() >= Math.min(lastNow, lastUntil)));
			var getSecs = function(date) {
				return (date.getHours() * 60 + date.getMinutes()) * 60 + date.getSeconds();
			};
			var months = Math.max(0,
				(until.getFullYear() - now.getFullYear()) * 12 + until.getMonth() - now.getMonth() +
				((until.getDate() < now.getDate() && !sameDay) ||
				(sameDay && getSecs(until) < getSecs(now)) ? -1 : 0));
			periods[Y] = (show[Y] ? Math.floor(months / 12) : 0);
			periods[O] = (show[O] ? months - periods[Y] * 12 : 0);
			// Adjust for months difference and end of month if necessary
			now = new Date(now.getTime());
			var wasLastDay = (now.getDate() == lastNow);
			var lastDay = plugin._getDaysInMonth(now.getFullYear() + periods[Y],
				now.getMonth() + periods[O]);
			if (now.getDate() > lastDay) {
				now.setDate(lastDay);
			}
			now.setFullYear(now.getFullYear() + periods[Y]);
			now.setMonth(now.getMonth() + periods[O]);
			if (wasLastDay) {
				now.setDate(lastDay);
			}
		}
		var diff = Math.floor((until.getTime() - now.getTime()) / 1000);
		var extractPeriod = function(period, numSecs) {
			periods[period] = (show[period] ? Math.floor(diff / numSecs) : 0);
			diff -= periods[period] * numSecs;
		};
		extractPeriod(W, 604800);
		extractPeriod(D, 86400);
		extractPeriod(H, 3600);
		extractPeriod(M, 60);
		extractPeriod(S, 1);
		if (diff > 0 && !inst._since) { // Round up if left overs
			var multiplier = [1, 12, 4.3482, 7, 24, 60, 60];
			var lastShown = S;
			var max = 1;
			for (var period = S; period >= Y; period--) {
				if (show[period]) {
					if (periods[lastShown] >= max) {
						periods[lastShown] = 0;
						diff = 1;
					}
					if (diff > 0) {
						periods[period]++;
						diff = 0;
						lastShown = period;
						max = 1;
					}
				}
				max *= multiplier[period];
			}
		}
		if (significant) { // Zero out insignificant periods
			for (var period = Y; period <= S; period++) {
				if (significant && periods[period]) {
					significant--;
				}
				else if (!significant) {
					periods[period] = 0;
				}
			}
		}
		return periods;
	}
});

// The list of commands that return values and don't permit chaining
var getters = ['getTimes'];

/* Determine whether a command is a getter and doesn't permit chaining.
   @param  command    (string, optional) the command to run
   @param  otherArgs  ([], optional) any other arguments for the command
   @return  true if the command is a getter, false if not */
function isNotChained(command, otherArgs) {
	if (command == 'option' && (otherArgs.length == 0 ||
			(otherArgs.length == 1 && typeof otherArgs[0] == 'string'))) {
		return true;
	}
	return $.inArray(command, getters) > -1;
}

/* Process the countdown functionality for a jQuery selection.
   @param  options  (object) the new settings to use for these instances (optional) or
                    (string) the command to run (optional)
   @return  (jQuery) for chaining further calls or
            (any) getter value */
$.fn.countdown = function(options) {
	var otherArgs = Array.prototype.slice.call(arguments, 1);
	if (isNotChained(options, otherArgs)) {
		return plugin['_' + options + 'Plugin'].
			apply(plugin, [this[0]].concat(otherArgs));
	}
	return this.each(function() {
		if (typeof options == 'string') {
			if (!plugin['_' + options + 'Plugin']) {
				throw 'Unknown command: ' + options;
			}
			plugin['_' + options + 'Plugin'].
				apply(plugin, [this].concat(otherArgs));
		}
		else {
			plugin._attachPlugin(this, options || {});
		}
	});
};

/* Initialise the countdown functionality. */
var plugin = $.countdown = new Countdown(); // Singleton instance

})(jQuery);
;/* js/libs-src/jquery.tablesorter.min.js */

(function($){$.extend({tablesorter:new
function(){var parsers=[],widgets=[];this.defaults={cssHeader:"header",cssAsc:"headerSortUp",cssDesc:"headerSortDown",cssChildRow:"expand-child",sortInitialOrder:"asc",sortMultiSortKey:"shiftKey",sortForce:null,sortAppend:null,sortLocaleCompare:true,textExtraction:"simple",parsers:{},widgets:[],widgetZebra:{css:["even","odd"]},headers:{},widthFixed:false,cancelSelection:true,sortList:[],headerList:[],dateFormat:"us",decimal:'/\.|\,/g',onRenderHeader:null,selectorHeaders:'thead th',debug:false};function benchmark(s,d){log(s+","+(new Date().getTime()-d.getTime())+"ms");}this.benchmark=benchmark;function log(s){if(typeof console!="undefined"&&typeof console.debug!="undefined"){console.log(s);}else{alert(s);}}function buildParserCache(table,$headers){if(table.config.debug){var parsersDebug="";}if(table.tBodies.length==0)return;var rows=table.tBodies[0].rows;if(rows[0]){var list=[],cells=rows[0].cells,l=cells.length;for(var i=0;i<l;i++){var p=false;if($.metadata&&($($headers[i]).metadata()&&$($headers[i]).metadata().sorter)){p=getParserById($($headers[i]).metadata().sorter);}else if((table.config.headers[i]&&table.config.headers[i].sorter)){p=getParserById(table.config.headers[i].sorter);}if(!p){p=detectParserForColumn(table,rows,-1,i);}if(table.config.debug){parsersDebug+="column:"+i+" parser:"+p.id+"\n";}list.push(p);}}if(table.config.debug){log(parsersDebug);}return list;};function detectParserForColumn(table,rows,rowIndex,cellIndex){var l=parsers.length,node=false,nodeValue=false,keepLooking=true;while(nodeValue==''&&keepLooking){rowIndex++;if(rows[rowIndex]){node=getNodeFromRowAndCellIndex(rows,rowIndex,cellIndex);nodeValue=trimAndGetNodeText(table.config,node);if(table.config.debug){log('Checking if value was empty on row:'+rowIndex);}}else{keepLooking=false;}}for(var i=1;i<l;i++){if(parsers[i].is(nodeValue,table,node)){return parsers[i];}}return parsers[0];}function getNodeFromRowAndCellIndex(rows,rowIndex,cellIndex){return rows[rowIndex].cells[cellIndex];}function trimAndGetNodeText(config,node){return $.trim(getElementText(config,node));}function getParserById(name){var l=parsers.length;for(var i=0;i<l;i++){if(parsers[i].id.toLowerCase()==name.toLowerCase()){return parsers[i];}}return false;}function buildCache(table){if(table.config.debug){var cacheTime=new Date();}var totalRows=(table.tBodies[0]&&table.tBodies[0].rows.length)||0,totalCells=(table.tBodies[0].rows[0]&&table.tBodies[0].rows[0].cells.length)||0,parsers=table.config.parsers,cache={row:[],normalized:[]};for(var i=0;i<totalRows;++i){var c=$(table.tBodies[0].rows[i]),cols=[];if(c.hasClass(table.config.cssChildRow)){cache.row[cache.row.length-1]=cache.row[cache.row.length-1].add(c);continue;}cache.row.push(c);for(var j=0;j<totalCells;++j){cols.push(parsers[j].format(getElementText(table.config,c[0].cells[j]),table,c[0].cells[j]));}cols.push(cache.normalized.length);cache.normalized.push(cols);cols=null;};if(table.config.debug){benchmark("Building cache for "+totalRows+" rows:",cacheTime);}return cache;};function getElementText(config,node){var text="";if(!node)return"";if(!config.supportsTextContent)config.supportsTextContent=node.textContent||false;if(config.textExtraction=="simple"){if(config.supportsTextContent){text=node.textContent;}else{if(node.childNodes[0]&&node.childNodes[0].hasChildNodes()){text=node.childNodes[0].innerHTML;}else{text=node.innerHTML;}}}else{if(typeof(config.textExtraction)=="function"){text=config.textExtraction(node);}else{text=$(node).text();}}return text;}function appendToTable(table,cache){if(table.config.debug){var appendTime=new Date()}var c=cache,r=c.row,n=c.normalized,totalRows=n.length,checkCell=(n[0].length-1),tableBody=$(table.tBodies[0]),rows=[];for(var i=0;i<totalRows;i++){var pos=n[i][checkCell];rows.push(r[pos]);if(!table.config.appender){var l=r[pos].length;for(var j=0;j<l;j++){tableBody[0].appendChild(r[pos][j]);}}}if(table.config.appender){table.config.appender(table,rows);}rows=null;if(table.config.debug){benchmark("Rebuilt table:",appendTime);}applyWidget(table);setTimeout(function(){$(table).trigger("sortEnd");},0);};function buildHeaders(table){if(table.config.debug){var time=new Date();}var meta=($.metadata)?true:false;var header_index=computeTableHeaderCellIndexes(table);$tableHeaders=$(table.config.selectorHeaders,table).each(function(index){this.column=header_index[this.parentNode.rowIndex+"-"+this.cellIndex];this.order=formatSortingOrder(table.config.sortInitialOrder);this.count=this.order;if(checkHeaderMetadata(this)||checkHeaderOptions(table,index))this.sortDisabled=true;if(checkHeaderOptionsSortingLocked(table,index))this.order=this.lockedOrder=checkHeaderOptionsSortingLocked(table,index);if(!this.sortDisabled){var $th=$(this).addClass(table.config.cssHeader);if(table.config.onRenderHeader)table.config.onRenderHeader.apply($th);}table.config.headerList[index]=this;});if(table.config.debug){benchmark("Built headers:",time);log($tableHeaders);}return $tableHeaders;};function computeTableHeaderCellIndexes(t){var matrix=[];var lookup={};var thead=t.getElementsByTagName('THEAD')[0];var trs=thead.getElementsByTagName('TR');for(var i=0;i<trs.length;i++){var cells=trs[i].cells;for(var j=0;j<cells.length;j++){var c=cells[j];var rowIndex=c.parentNode.rowIndex;var cellId=rowIndex+"-"+c.cellIndex;var rowSpan=c.rowSpan||1;var colSpan=c.colSpan||1
var firstAvailCol;if(typeof(matrix[rowIndex])=="undefined"){matrix[rowIndex]=[];}for(var k=0;k<matrix[rowIndex].length+1;k++){if(typeof(matrix[rowIndex][k])=="undefined"){firstAvailCol=k;break;}}lookup[cellId]=firstAvailCol;for(var k=rowIndex;k<rowIndex+rowSpan;k++){if(typeof(matrix[k])=="undefined"){matrix[k]=[];}var matrixrow=matrix[k];for(var l=firstAvailCol;l<firstAvailCol+colSpan;l++){matrixrow[l]="x";}}}}return lookup;}function checkCellColSpan(table,rows,row){var arr=[],r=table.tHead.rows,c=r[row].cells;for(var i=0;i<c.length;i++){var cell=c[i];if(cell.colSpan>1){arr=arr.concat(checkCellColSpan(table,headerArr,row++));}else{if(table.tHead.length==1||(cell.rowSpan>1||!r[row+1])){arr.push(cell);}}}return arr;};function checkHeaderMetadata(cell){if(($.metadata)&&($(cell).metadata().sorter===false)){return true;};return false;}function checkHeaderOptions(table,i){if((table.config.headers[i])&&(table.config.headers[i].sorter===false)){return true;};return false;}function checkHeaderOptionsSortingLocked(table,i){if((table.config.headers[i])&&(table.config.headers[i].lockedOrder))return table.config.headers[i].lockedOrder;return false;}function applyWidget(table){var c=table.config.widgets;var l=c.length;for(var i=0;i<l;i++){getWidgetById(c[i]).format(table);}}function getWidgetById(name){var l=widgets.length;for(var i=0;i<l;i++){if(widgets[i].id.toLowerCase()==name.toLowerCase()){return widgets[i];}}};function formatSortingOrder(v){if(typeof(v)!="Number"){return(v.toLowerCase()=="desc")?1:0;}else{return(v==1)?1:0;}}function isValueInArray(v,a){var l=a.length;for(var i=0;i<l;i++){if(a[i][0]==v){return true;}}return false;}function setHeadersCss(table,$headers,list,css){$headers.removeClass(css[0]).removeClass(css[1]);var h=[];$headers.each(function(offset){if(!this.sortDisabled){h[this.column]=$(this);}});var l=list.length;for(var i=0;i<l;i++){h[list[i][0]].addClass(css[list[i][1]]);}}function fixColumnWidth(table,$headers){var c=table.config;if(c.widthFixed){var colgroup=$('<colgroup>');$("tr:first td",table.tBodies[0]).each(function(){colgroup.append($('<col>').css('width',$(this).width()));});$(table).prepend(colgroup);};}function updateHeaderSortCount(table,sortList){var c=table.config,l=sortList.length;for(var i=0;i<l;i++){var s=sortList[i],o=c.headerList[s[0]];o.count=s[1];o.count++;}}function multisort(table,sortList,cache){if(table.config.debug){var sortTime=new Date();}var dynamicExp="var sortWrapper = function(a,b) {",l=sortList.length;for(var i=0;i<l;i++){var c=sortList[i][0];var order=sortList[i][1];var s=(table.config.parsers[c].type=="text")?((order==0)?makeSortFunction("text","asc",c):makeSortFunction("text","desc",c)):((order==0)?makeSortFunction("numeric","asc",c):makeSortFunction("numeric","desc",c));var e="e"+i;dynamicExp+="var "+e+" = "+s;dynamicExp+="if("+e+") { return "+e+"; } ";dynamicExp+="else { ";}var orgOrderCol=cache.normalized[0].length-1;dynamicExp+="return a["+orgOrderCol+"]-b["+orgOrderCol+"];";for(var i=0;i<l;i++){dynamicExp+="}; ";}dynamicExp+="return 0; ";dynamicExp+="}; ";if(table.config.debug){benchmark("Evaling expression:"+dynamicExp,new Date());}eval(dynamicExp);cache.normalized.sort(sortWrapper);if(table.config.debug){benchmark("Sorting on "+sortList.toString()+" and dir "+order+" time:",sortTime);}return cache;};function makeSortFunction(type,direction,index){var a="a["+index+"]",b="b["+index+"]";if(type=='text'&&direction=='asc'){return"("+a+" == "+b+" ? 0 : ("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : ("+a+" < "+b+") ? -1 : 1 )));";}else if(type=='text'&&direction=='desc'){return"("+a+" == "+b+" ? 0 : ("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : ("+b+" < "+a+") ? -1 : 1 )));";}else if(type=='numeric'&&direction=='asc'){return"("+a+" === null && "+b+" === null) ? 0 :("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : "+a+" - "+b+"));";}else if(type=='numeric'&&direction=='desc'){return"("+a+" === null && "+b+" === null) ? 0 :("+a+" === null ? Number.POSITIVE_INFINITY : ("+b+" === null ? Number.NEGATIVE_INFINITY : "+b+" - "+a+"));";}};function makeSortText(i){return"((a["+i+"] < b["+i+"]) ? -1 : ((a["+i+"] > b["+i+"]) ? 1 : 0));";};function makeSortTextDesc(i){return"((b["+i+"] < a["+i+"]) ? -1 : ((b["+i+"] > a["+i+"]) ? 1 : 0));";};function makeSortNumeric(i){return"a["+i+"]-b["+i+"];";};function makeSortNumericDesc(i){return"b["+i+"]-a["+i+"];";};function sortText(a,b){if(table.config.sortLocaleCompare)return a.localeCompare(b);return((a<b)?-1:((a>b)?1:0));};function sortTextDesc(a,b){if(table.config.sortLocaleCompare)return b.localeCompare(a);return((b<a)?-1:((b>a)?1:0));};function sortNumeric(a,b){return a-b;};function sortNumericDesc(a,b){return b-a;};function getCachedSortType(parsers,i){return parsers[i].type;};this.construct=function(settings){return this.each(function(){if(!this.tHead||!this.tBodies)return;var $this,$document,$headers,cache,config,shiftDown=0,sortOrder;this.config={};config=$.extend(this.config,$.tablesorter.defaults,settings);$this=$(this);$.data(this,"tablesorter",config);$headers=buildHeaders(this);this.config.parsers=buildParserCache(this,$headers);cache=buildCache(this);var sortCSS=[config.cssDesc,config.cssAsc];fixColumnWidth(this);$headers.click(function(e){var totalRows=($this[0].tBodies[0]&&$this[0].tBodies[0].rows.length)||0;if(!this.sortDisabled&&totalRows>0){$this.trigger("sortStart");var $cell=$(this);var i=this.column;this.order=this.count++%2;if(this.lockedOrder)this.order=this.lockedOrder;if(!e[config.sortMultiSortKey]){config.sortList=[];if(config.sortForce!=null){var a=config.sortForce;for(var j=0;j<a.length;j++){if(a[j][0]!=i){config.sortList.push(a[j]);}}}config.sortList.push([i,this.order]);}else{if(isValueInArray(i,config.sortList)){for(var j=0;j<config.sortList.length;j++){var s=config.sortList[j],o=config.headerList[s[0]];if(s[0]==i){o.count=s[1];o.count++;s[1]=o.count%2;}}}else{config.sortList.push([i,this.order]);}};setTimeout(function(){setHeadersCss($this[0],$headers,config.sortList,sortCSS);appendToTable($this[0],multisort($this[0],config.sortList,cache));},1);return false;}}).mousedown(function(){if(config.cancelSelection){this.onselectstart=function(){return false};return false;}});$this.bind("update",function(){var me=this;setTimeout(function(){me.config.parsers=buildParserCache(me,$headers);cache=buildCache(me);},1);}).bind("updateCell",function(e,cell){var config=this.config;var pos=[(cell.parentNode.rowIndex-1),cell.cellIndex];cache.normalized[pos[0]][pos[1]]=config.parsers[pos[1]].format(getElementText(config,cell),cell);}).bind("sorton",function(e,list){$(this).trigger("sortStart");config.sortList=list;var sortList=config.sortList;updateHeaderSortCount(this,sortList);setHeadersCss(this,$headers,sortList,sortCSS);appendToTable(this,multisort(this,sortList,cache));}).bind("appendCache",function(){appendToTable(this,cache);}).bind("applyWidgetId",function(e,id){getWidgetById(id).format(this);}).bind("applyWidgets",function(){applyWidget(this);});if($.metadata&&($(this).metadata()&&$(this).metadata().sortlist)){config.sortList=$(this).metadata().sortlist;}if(config.sortList.length>0){$this.trigger("sorton",[config.sortList]);}applyWidget(this);});};this.addParser=function(parser){var l=parsers.length,a=true;for(var i=0;i<l;i++){if(parsers[i].id.toLowerCase()==parser.id.toLowerCase()){a=false;}}if(a){parsers.push(parser);};};this.addWidget=function(widget){widgets.push(widget);};this.formatFloat=function(s){var i=parseFloat(s);return(isNaN(i))?0:i;};this.formatInt=function(s){var i=parseInt(s);return(isNaN(i))?0:i;};this.isDigit=function(s,config){return/^[-+]?\d*$/.test($.trim(s.replace(/[,.']/g,'')));};this.clearTableBody=function(table){if($.browser.msie){function empty(){while(this.firstChild)this.removeChild(this.firstChild);}empty.apply(table.tBodies[0]);}else{table.tBodies[0].innerHTML="";}};}});$.fn.extend({tablesorter:$.tablesorter.construct});var ts=$.tablesorter;ts.addParser({id:"text",is:function(s){return true;},format:function(s){return $.trim(s.toLocaleLowerCase());},type:"text"});ts.addParser({id:"digit",is:function(s,table){var c=table.config;return $.tablesorter.isDigit(s,c);},format:function(s){return $.tablesorter.formatFloat(s);},type:"numeric"});ts.addParser({id:"currency",is:function(s){return/^[£$€?.]/.test(s);},format:function(s){return $.tablesorter.formatFloat(s.replace(new RegExp(/[£$€]/g),""));},type:"numeric"});ts.addParser({id:"ipAddress",is:function(s){return/^\d{2,3}[\.]\d{2,3}[\.]\d{2,3}[\.]\d{2,3}$/.test(s);},format:function(s){var a=s.split("."),r="",l=a.length;for(var i=0;i<l;i++){var item=a[i];if(item.length==2){r+="0"+item;}else{r+=item;}}return $.tablesorter.formatFloat(r);},type:"numeric"});ts.addParser({id:"url",is:function(s){return/^(https?|ftp|file):\/\/$/.test(s);},format:function(s){return jQuery.trim(s.replace(new RegExp(/(https?|ftp|file):\/\//),''));},type:"text"});ts.addParser({id:"isoDate",is:function(s){return/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(s);},format:function(s){return $.tablesorter.formatFloat((s!="")?new Date(s.replace(new RegExp(/-/g),"/")).getTime():"0");},type:"numeric"});ts.addParser({id:"percent",is:function(s){return/\%$/.test($.trim(s));},format:function(s){return $.tablesorter.formatFloat(s.replace(new RegExp(/%/g),""));},type:"numeric"});ts.addParser({id:"usLongDate",is:function(s){return s.match(new RegExp(/^[A-Za-z]{3,10}\.? [0-9]{1,2}, ([0-9]{4}|'?[0-9]{2}) (([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(AM|PM)))$/));},format:function(s){return $.tablesorter.formatFloat(new Date(s).getTime());},type:"numeric"});ts.addParser({id:"shortDate",is:function(s){return/\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}/.test(s);},format:function(s,table){var c=table.config;s=s.replace(/\-/g,"/");if(c.dateFormat=="us"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/,"$3/$1/$2");}else if(c.dateFormat=="uk"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/,"$3/$2/$1");}else if(c.dateFormat=="dd/mm/yy"||c.dateFormat=="dd-mm-yy"){s=s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})/,"$1/$2/$3");}return $.tablesorter.formatFloat(new Date(s).getTime());},type:"numeric"});ts.addParser({id:"time",is:function(s){return/^(([0-2]?[0-9]:[0-5][0-9])|([0-1]?[0-9]:[0-5][0-9]\s(am|pm)))$/.test(s);},format:function(s){return $.tablesorter.formatFloat(new Date("2000/01/01 "+s).getTime());},type:"numeric"});ts.addParser({id:"metadata",is:function(s){return false;},format:function(s,table,cell){var c=table.config,p=(!c.parserMetadataName)?'sortValue':c.parserMetadataName;return $(cell).metadata()[p];},type:"numeric"});ts.addWidget({id:"zebra",format:function(table){if(table.config.debug){var time=new Date();}var $tr,row=-1,odd;$("tr:visible",table.tBodies[0]).each(function(i){$tr=$(this);if(!$tr.hasClass(table.config.cssChildRow))row++;odd=(row%2==0);$tr.removeClass(table.config.widgetZebra.css[odd?0:1]).addClass(table.config.widgetZebra.css[odd?1:0])});if(table.config.debug){$.tablesorter.benchmark("Applying Zebra widget",time);}}});})(jQuery);;/* js/libs-src/schedule.js */
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

window.onload=function (e) {
   if (jQuery( "#scheduleTab" ).length ) { //do the schedule tabs exist?S
        var hash = window.location.hash;
        hash = hash.toLowerCase();
     
        if(hash!==''){ //was a hash set?
            jQuery('.dropdown li').each(function(){ jQuery(this).removeClass('active');});
            jQuery('.dropdown a[href="'+hash+'"]').tab('show'); // Select tab by name
            var dispText = jQuery('.dropdown a[href="'+hash+'"]').text(); // Select tab by name
            //set dropdown text
            jQuery ('button .pickText').text(dispText);
        }                
   }
};
jQuery(document).ready(function () {
    jQuery('.dropdown-menu li').click(function() {
        jQuery('.dropdown li').each(function(){ jQuery(this).removeClass('active');});
        jQuery(this).addClass('selected');
        
        var dispText = jQuery(this).text();
        //set dropdown text
        jQuery ('button .pickText').text(dispText);
    });
});;/* js/libs-src/ytv.js */
/*
 * YouTube TV
 *
 * Copyright 2013, Jacob Kelley - http://jakiestfu.com/
 * Released under the MIT Licence
 * http://opensource.org/licenses/MIT
 *
 * Github:
 * Version: 1.0.2
 */
/*jslint browser: true, undef:true, unused:true*/
/*global define, module, ender */

(function(win, doc) {
    'use strict';
    var YTV = YTV || function(id, opts){

        var noop = function(){},
            settings = {
                element: null,
                user: null,
                fullscreen: false,
                accent: '#fff',
                controls: true,
                annotations: false,
                autoplay: false,
                chainVideos: true,
                browsePlaylists: false,
                wmode: 'opaque',
                events: {
                    videoReady: noop,
                    stateChange: noop
                }
            },

            cache = {},
            utils = {
                events: {
                    addEvent: function addEvent(element, eventName, func) {
                        if (element.addEventListener) {
                            return element.addEventListener(eventName, func, false);
                        } else if (element.attachEvent) {
                            return element.attachEvent("on" + eventName, func);
                        }
                    },
                    removeEvent: function addEvent(element, eventName, func) {
                        if (element.addEventListener) {
                            return element.removeEventListener(eventName, func, false);
                        } else if (element.attachEvent) {
                            return element.detachEvent("on" + eventName, func);
                        }
                    },
                    prevent: function(e) {
                        if (e.preventDefault) {
                            e.preventDefault();
                        } else {
                            e.returnValue = false;
                        }
                    }
                },
                addCSS: function(css){
                    var head = doc.getElementsByTagName('head')[0],
                        style = doc.createElement('style');
                        style.type = 'text/css';
                    if (style.styleSheet){
                        style.styleSheet.cssText = css;
                    } else {
                        style.appendChild(doc.createTextNode(css));
                    }
                    head.appendChild(style);
                },
                addCommas: function(str){
                    var x = str.split('.'),
                        x1 = x[0],
                        x2 = x.length > 1 ? '.' + x[1] : '',
                        rgx = /(\d+)(\d{3})/;
                    while (rgx.test(x1)) {
                        x1 = x1.replace(rgx, '$1' + ',' + '$2');
                    }
                    return x1 + x2;
                },
                parentUntil: function(el, attr) {
                    while (el.parentNode) {
                       if (el.getAttribute && el.getAttribute(attr)){
                            return el;
                        }
                        el = el.parentNode;
                    }
                    return null;
                },
                ajax: {
                    get: function(url, fn){
                        var handle;
                        if (win.XMLHttpRequest){
                            handle = new XMLHttpRequest();
                        } else {
                            handle = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        handle.onreadystatechange = function(){
                            if (handle.readyState === 4 && handle.status === 200){
                                fn.call(this, JSON.parse(handle.responseText));
                            }
                        };
                        handle.open("GET",url,true);
                        handle.send();
                    }
                },
                endpoints: {
                    base: 'http://gdata.youtube.com/',
                    userInfo: function(){
                        return utils.endpoints.base+'feeds/api/users/'+settings.user+'?v=2&alt=json';
                    },
                    userVids: function(){
                        return utils.endpoints.base+'feeds/api/users/'+settings.user+'/uploads/?v=2&alt=json&format=5&max-results=50';
                    },
                    userPlaylists: function(){
                        return utils.endpoints.base+'feeds/api/users/'+settings.user+'/playlists/?v=2&alt=json&format=5&max-results=50';
                    },
                    playlistVids: function(){
                        return utils.endpoints.base+'feeds/api/playlists/'+(settings.playlist)+'?v=2&alt=json&format=5&max-results=50';
                    }
                },
                deepExtend: function(destination, source) {
                    var property;
                    for (property in source) {
                        if (source[property] && source[property].constructor && source[property].constructor === Object) {
                            destination[property] = destination[property] || {};
                            utils.deepExtend(destination[property], source[property]);
                        } else {
                            destination[property] = source[property];
                        }
                    }
                    return destination;
                }
            },
            prepare = {
                youtube: function(fn){
                    var tag = doc.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    var firstScriptTag = doc.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    win.onYouTubeIframeAPIReady = fn;
                },
                build: function(){
                    settings.element.className = "ytv-canvas";
                    if(settings.fullscreen){
                        settings.element.className += " ytv-full";
                    }
                    utils.addCSS( '.ytv-list .ytv-active a{border-left-color: '+(settings.accent)+';}' );
                },
                playlists: function(res){
                    if(res && res.feed){
                        var list = '<div class="ytv-playlists"><ul>',
                            playlists = res.feed.entry,
                            i;
                        for(i=0; i<playlists.length; i++){
                            var data = {
                                title: playlists[i].title.$t,
                                plid: playlists[i].yt$playlistId.$t,
                                thumb: playlists[i].media$group.media$thumbnail[1].url
                            };
                            list += '<a href="#" data-ytv-playlist="'+(data.plid)+'">';
                                list += '<div class="ytv-thumb"><div class="ytv-thumb-stroke"></div><img src="'+(data.thumb)+'"></div>';
                                list += '<span>'+(data.title)+'</span>';
                            list += '</a>';
                        }
                        list += '</ul></div>';

                        var lh = doc.getElementsByClassName('ytv-list-header')[0],
                            headerLink = lh.children[0];
                        headerLink.href="#";
                        headerLink.target="";
                        headerLink.setAttribute('data-ytv-playlist-toggle', 'true');
                        doc.getElementsByClassName('ytv-list-header')[0].innerHTML += list;
                        lh.className += ' ytv-has-playlists';
                    }
                },
                compileList: function(data){
                    if(data && data.feed){
                        utils.ajax.get( utils.endpoints.userInfo(), function(userInfo){
                            var list = '',
                                user = {
                                    title: userInfo.entry.title.$t,
                                    url: 'http://youtube.com/user/'+userInfo.entry.yt$username.$t,
                                    thumb: userInfo.entry.media$thumbnail.url,
                                    summary: userInfo.entry.summary.$t,
                                    subscribers: userInfo.entry.yt$statistics.subscriberCount,
                                    views: userInfo.entry.yt$statistics.totalUploadViews
                                },
                                videos = data.feed.entry,
                                first = true,
                                i;
                            if(settings.playlist){
                                user.title += ' &middot; '+(data.feed.media$group.media$title.$t);
                            }
                            list += '<div class="ytv-list-header">';
                                list += '<a href="'+(user.url)+'" target="_blank">';
                                    list += '<img src="'+(user.thumb)+'">';
                                    list += '<span>'+(user.title)+' <i class="ytv-arrow down"></i></span>';
                                list += '</a>';
                            list += '</div>';

                            list += '<div class="ytv-list-inner"><ul>';
                            for(i=0; i<videos.length; i++){
                                if(videos[i].media$group.yt$duration){
                                    var video = {
                                        title: videos[i].title.$t,
                                        slug: videos[i].media$group.yt$videoid.$t,
                                        link: videos[i].link[0].href,
                                        published: videos[i].published.$t,
                                        rating: videos[i].yt$rating,
                                        stats: videos[i].yt$statistics,
                                        duration: (videos[i].media$group.yt$duration.seconds),
                                        thumb: videos[i].media$group.media$thumbnail[1].url
                                    };

                                    var date = new Date(null);
                                    date.setSeconds(video.duration);
                                    var timeSlots = (date.toTimeString().substr(0, 8)).split(':'),
                                        time = timeSlots[1] + ':' + timeSlots[2];

                                    var isFirst = '';
                                    if(first===true){
                                        isFirst = ' class="ytv-active"';
                                        first = video.slug;
                                    }

                                    list += '<li'+isFirst+'><a href="#" data-ytv="'+(video.slug)+'" class="ytv-clear">';
                                    list += '<div class="ytv-thumb"><div class="ytv-thumb-stroke"></div><span>'+(time)+'</span><img src="'+(video.thumb)+'"></div>';
                                    list += '<div class="ytv-content"><b>'+(video.title)+'</b>';
                                    if (video.stats)
                                    {
                                        list+'</b><span class="ytv-views">'+utils.addCommas(video.stats.viewCount)+' Views</span>';
                                    }
                                    list += '</div></a></li>';
                                }
                            }
                            list += '</ul></div>';
                            settings.element.innerHTML = '<div class="ytv-relative"><div class="ytv-video"><div id="ytv-video-player"></div></div><div class="ytv-list">'+list+'</div></div>';

                            action.logic.loadVideo(first, settings.autoplay);

                            if(settings.browsePlaylists){
                                utils.ajax.get( utils.endpoints.userPlaylists(), prepare.playlists );
                            }

                        });
                    }
                }
            },
            action = {

                logic: {

                    playerStateChange: function(d){
                        console.log(d);
                    },

                    loadVideo: function(slug, autoplay){

                        var house = doc.getElementsByClassName('ytv-video')[0];
                        house.innerHTML = '<div id="ytv-video-player"></div>';

                        cache.player = new YT.Player('ytv-video-player', {
                            videoId: slug,
                            events: {
                                onReady: settings.events.videoReady,
                                onStateChange: function(e){
                                    if( (e.target.getPlayerState()===0) && settings.chainVideos ){
                                        var ns = doc.getElementsByClassName('ytv-active')[0].nextSibling,
                                            link = ns.children[0];
                                        link.click();
                                    }
                                    settings.events.stateChange.call(this, e);
                                }
                            },
                            playerVars: {
                                enablejsapi: 1,
                                origin: doc.domain,
                                controls: settings.controls ? 1 : 0,
                                rel: 0,
                                showinfo: 0,
                                iv_load_policy: settings.annotations ? '' : 3,
                                autoplay: autoplay ? 1 : 0,
                                wmode: settings.wmode
                            }
                        });

                    }

                },

                endpoints: {
                    videoClick: function(e){
                        var target = utils.parentUntil(e.target ? e.target : e.srcElement, 'data-ytv');

                        if(target){

                            if(target.getAttribute('data-ytv')){

                                // Load Video
                                utils.events.prevent(e);

                                var activeEls = doc.getElementsByClassName('ytv-active'),
                                    i;
                                for(i=0; i<activeEls.length; i++){
                                    activeEls[i].className="";
                                }
                                target.parentNode.className="ytv-active";
                                action.logic.loadVideo(target.getAttribute('data-ytv'), true);

                            }

                        }
                    },
                    playlistToggle: function(e){
                        var target = utils.parentUntil(e.target ? e.target : e.srcElement, 'data-ytv-playlist-toggle');

                        if(target && target.getAttribute('data-ytv-playlist-toggle')){

                            // Toggle Playlist
                            utils.events.prevent(e);
                            var lh = doc.getElementsByClassName('ytv-list-header')[0];
                            if(lh.className.indexOf('ytv-playlist-open')===-1){
                                lh.className += ' ytv-playlist-open';
                            } else {
                                lh.className = lh.className.replace(' ytv-playlist-open', '');
                            }
                        }
                    },
                    playlistClick: function(e){
                        var target = utils.parentUntil(e.target ? e.target : e.srcElement, 'data-ytv-playlist');

                        if(target && target.getAttribute('data-ytv-playlist')){

                            // Load Playlist
                            utils.events.prevent(e);

                            settings.playlist = target.getAttribute('data-ytv-playlist');
                            target.children[1].innerHTML = 'Loading...';

                            utils.ajax.get( utils.endpoints.playlistVids(), function(res){
                                var lh = doc.getElementsByClassName('ytv-list-header')[0];
                                lh.className = lh.className.replace(' ytv-playlist-open', '');
                                prepare.compileList(res);
                            });
                        }
                    }
                },
                loadAds: function(){
                    utils.ajax.get( utils.endpoints.adPlaylist(), function(data){
                        var videos = data.feed.entry,
                            i;
                        cache.ads = [];
                        for(i=0; i<videos.length; i++){
                            cache.ads.push({
                                title: videos[i].title.$t,
                                slug: videos[i].media$group.yt$videoid.$t,
                                duration: parseInt(videos[i].media$group.yt$duration.seconds, 10),
                            });
                        }
                    });
                },
                bindEvents: function(){

                    utils.events.addEvent( settings.element, 'click', action.endpoints.videoClick );
                    utils.events.addEvent( settings.element, 'click', action.endpoints.playlistToggle );
                    utils.events.addEvent( settings.element, 'click', action.endpoints.playlistClick );

                }
            },

            initialize = function(id, opts){
                utils.deepExtend(settings, opts);
                settings.element = (typeof id==='string') ? doc.getElementById(id) : id;
                if(settings.element && settings.user){
                    prepare.youtube(function(){
                        prepare.build();
                        action.bindEvents();
                        utils.ajax.get( settings.playlist ? utils.endpoints.playlistVids() : utils.endpoints.userVids(), prepare.compileList );
                    });
                }
            };

            /*
             * Public
             */
            this.destroy = function(){
                utils.events.removeEvent( settings.element, 'click', action.endpoints.videoClick );
                utils.events.removeEvent( settings.element, 'click', action.endpoints.playlistToggle );
                utils.events.removeEvent( settings.element, 'click', action.endpoints.playlistClick );
                settings.element.className = '';
                settings.element.innerHTML = '';
            };
            this.fullscreen = {
                state: function(){
                    return (settings.element.className).indexOf('ytv-full') !== -1;
                },
                enter: function(){
                    if( (settings.element.className).indexOf('ytv-full') === -1 ){
                        settings.element.className += 'ytv-full';
                    }
                },
                exit: function(){
                    if( (settings.element.className).indexOf('ytv-full') !== -1 ){
                        settings.element.className = (settings.element.className).replace('ytv-full', '');
                    }
                }
            };

        initialize(id, opts);
    };
    if ((typeof module !== 'undefined') && module.exports) {
        module.exports = YTV;
    }
    if (typeof ender === 'undefined') {
        this.YTV = YTV;
    }
    if ((typeof define === "function") && define.amd) {
        define("YTV", [], function() {
            return YTV;
        });
    }
    if ((typeof jQuery !== 'undefined')) {
        jQuery.fn.extend({
            ytv: function(options) {
                return this.each(function() {
                    new YTV(this, options);
                });
            }
        });
    }
}).call(this, window, document);

jQuery( document ).ready( function() {
    jQuery('.small-youtube').ytv({
        user: 'makemagazine',
        playlist: 'PLwhkA66li5vDEuZtgSeshEn3AAzJKxam3'
        }
    );
});
