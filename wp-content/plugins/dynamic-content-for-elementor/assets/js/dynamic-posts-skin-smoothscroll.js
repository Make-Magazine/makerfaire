/**
* demo3.js
* http://www.codrops.com
*
* Licensed under the MIT license.
* http://www.opensource.org/licenses/mit-license.php
*
* Copyright 2019, Codrops
* http://www.codrops.com
*/

var Widget_DCE_Dynamicposts_smoothscroll_Handler = function ($scope, $) {

    var smsc = null;

    var elementSettings = dceGetElementSettings($scope);
    var id_scope = $scope.attr('data-id');
    var smoothscroll_image_perspective = elementSettings[dceDynamicPostsSkinPrefix+'smoothscroll_image_perspective'];
    var smoothscroll_image_scale = elementSettings[dceDynamicPostsSkinPrefix+'smoothscroll_image_scale'];
    var smoothscroll_image_translatey = elementSettings[dceDynamicPostsSkinPrefix+'smoothscroll_image_translatey'];
    var smoothscroll_content_translatey = elementSettings[dceDynamicPostsSkinPrefix+'smoothscroll_content_translatey'];
	var smooothscroll = $scope.find('.dce-posts-container.dce-skin-smoothscroll');

	// helper functions
    const MathUtils = {
        // map number x from range [a, b] to [c, d]
        map: (x, a, b, c, d) => (x - a) * (d - c) / (b - a) + c,
        // linear interpolation
        lerp: (a, b, n) => (1 - n) * a + n * b,
        // Random float
        getRandomFloat: (min, max) => (Math.random() * (max - min) + min).toFixed(2)
    };

    // body element
    const body = document.body;

    // calculate the viewport size
    let winsize;
    const calcWinsize = () => winsize = {width: window.innerWidth, height: window.innerHeight};
    calcWinsize();
    // and recalculate on resize
    window.addEventListener('resize', calcWinsize);

    // scroll position
    let docScroll;
    // for scroll speed calculation
    let lastScroll;
    let scrollingSpeed = 0;
    // scroll position update function
    const getPageYScroll = () => docScroll = window.pageYOffset || document.documentElement.scrollTop;
    window.addEventListener('scroll', getPageYScroll);

    var offset = $scope.find('.dce-smoothscroll-container').offset();

    // Item
    class Item {
        constructor(el) {
            // the .item element
            this.DOM = {el: el};
            if(this.DOM.el.querySelector('.dce-post-image')){
                this.DOM.image = this.DOM.el.querySelector('.dce-post-image');

                // 3d stuff
                this.DOM.block = this.DOM.el.querySelector('.dce-post-block');
                this.DOM.block.style.perspective = '1000px';
            }else{

                this.DOM.image = this.DOM.el.querySelector('.dce-post-block');

                // 3d stuff
                this.DOM.block = this.DOM.el;
                this.DOM.block.style.perspective = '1000px';
            }
            this.DOM.imageWrapper = this.DOM.image.parentNode;


            if(this.DOM.el.querySelector('.dce-image-area')){
                this.DOM.imagearea = this.DOM.el.querySelector('.dce-image-area');
                this.DOM.imagearea.style.perspective = '1000px';
            }
            if(this.DOM.el.querySelector('.dce-content-area')){
                this.DOM.contentarea = this.DOM.el.querySelector('.dce-content-area');
                this.DOM.contentarea.style.perspective = '1000px';
            }else{
                this.DOM.contentarea = this.DOM.el.querySelector('.dce-post-title');
            }

            this.DOM.imageWrapper.style.transformOrigin = '50% 100%';
            this.ry = MathUtils.getRandomFloat(-0.5,0.5);
            this.rz = MathUtils.getRandomFloat(-0.5,0.5);

            this.renderedStyles = {
                // here we define which property will change as we scroll the page and the item is inside the viewport
                // in this case we will be:
                // - translating the inner image
                // - rotating the item
                // we interpolate between the previous and current value to achieve a smooth effect
                innerTranslationY: {
                    // interpolated value
                    previous: 0,
                    // current value
                    current: 0,
                    // amount to interpolate
                    ease: 0.1,
                    // current value setter
                    setValue: () => {
                        // the maximum value to translate the image is set in a CSS variable (--overflow)
                        const toValue = parseInt(getComputedStyle(this.DOM.image).getPropertyValue('--overflow'), 10);
                        const fromValue = -1 * toValue;
                        return Math.max(Math.min(MathUtils.map(this.props.top - docScroll, winsize.height, -1 * this.props.height, fromValue, toValue), toValue), fromValue);
                    }
                },
                itemRotation: {
                    // interpolated value
                    previous: 0,
                    // current value
                    current: 0,
                    // amount to interpolate
                    ease: 0.1,
                    toValue: Number(MathUtils.getRandomFloat(-70,-50)),
                    // current value setter
                    setValue: () => {
                        const toValue = this.renderedStyles.itemRotation.toValue;
                        const fromValue = toValue*-1;
                        const val = MathUtils.map(this.props.top - docScroll, winsize.height*1.5, -1 * this.props.height, fromValue, toValue);
                        return Math.min(Math.max(val, toValue), fromValue);
                    }
                },
                imageScale: {
                    // interpolated value
                    previous: 0,
                    // current value
                    current: 0,
                    // amount to interpolate
                    ease: 0.1,
                    // current value setter
                    setValue: () => {
                        const toValue = 1.5;
                        const fromValue = 1;
                        const val = MathUtils.map(this.props.top - docScroll, winsize.height, -1 * this.props.height, fromValue, toValue);
                        return Math.max(Math.min(val, toValue), fromValue);
                    }
                },
                itemTranslationY: {
                    previous: 0,
                    current: 0,
                    ease: 0.1,
                    fromValue: Number(MathUtils.getRandomFloat(0,400)),
                    setValue: () => {
                        const fromValue = this.renderedStyles.itemTranslationY.fromValue;
                        const toValue = -1*fromValue;
                        const val = MathUtils.map(this.props.top - docScroll, winsize.height, -1 * this.props.height, fromValue, toValue);
                        return fromValue < 0 ? Math.min(Math.max(val, fromValue), toValue) : Math.max(Math.min(val, fromValue), toValue);
                    }
                }
            };
            // gets the item's height and top (relative to the document)
            this.getSize();
            // set the initial values
            this.update();
            // use the IntersectionObserver API to check when the element is inside the viewport
            // only then the element styles will be updated
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => this.isVisible = entry.intersectionRatio > 0);
            });
            this.observer.observe(this.DOM.el);
            // init/bind events
            this.initEvents();
        }
        update() {
            // sets the initial value (no interpolation)
            for (const key in this.renderedStyles ) {
                this.renderedStyles[key].current = this.renderedStyles[key].previous = this.renderedStyles[key].setValue();
            }
            // apply changes/styles
            this.layout();
        }
        getSize() {
            const rect = this.DOM.el.getBoundingClientRect();
            this.props = {
                // item's height
                height: rect.height,
                // offset top relative to the document
                top: docScroll + rect.top
            };
        }
        initEvents() {
            window.addEventListener('resize', () => this.resize());
        }
        resize() {
            // gets the item's height and top (relative to the document)
            this.getSize();
            // on resize reset sizes and update styles
            this.update();
        }
        render() {
            // update the current and interpolated values
            for (const key in this.renderedStyles ) {
                this.renderedStyles[key].current = this.renderedStyles[key].setValue();
                this.renderedStyles[key].previous = MathUtils.lerp(this.renderedStyles[key].previous, this.renderedStyles[key].current, this.renderedStyles[key].ease);
            }

            // and apply changes
            this.layout();
        }
        layout() {
            // translates the image
            var scale3d     = smoothscroll_image_scale ? `scale3d(${this.renderedStyles.imageScale.previous},${this.renderedStyles.imageScale.previous},1)` : '';
            var translate3d = smoothscroll_image_translatey ? `translate3d(0,${this.renderedStyles.innerTranslationY.previous}px,0)` : '';
            this.DOM.image.style.transform =  scale3d + translate3d;

            // rotate the image wrapper

            if(smoothscroll_image_perspective) this.DOM.imageWrapper.style.transform = `rotate3d(1,${this.ry},${this.rz},${this.renderedStyles.itemRotation.previous}deg)`;
            // translate the title
            if(smoothscroll_content_translatey) this.DOM.contentarea.style.transform = `translate3d(0,${this.renderedStyles.itemTranslationY.previous}px,0)`;
        }
    }

    // SmoothScroll
    class SmoothScroll {
        constructor() {
            // the <main> element
            if( !$('#dce-smootscroll-main').length )
            $('body').wrapInner('<div id="dce-smootscroll-main"><div id="dce-smootscroll-wrap-'+id_scope+'"></div></div>');


            var generatedWrap = $('#dce-smootscroll-main');
            this.DOM = {main: generatedWrap[0]};

            // the scrollable element
            // we translate this element when scrolling (y-axis)
            this.DOM.scrollable = this.DOM.main.querySelector('#dce-smootscroll-wrap-'+id_scope);

            // the items on the page
            this.items = [];
            this.DOM.content = this.DOM.main.querySelector('.dce-smoothscroll-wrapper');
            [...this.DOM.content.querySelectorAll('.dce-smoothscroll-item')].forEach(item => this.items.push(new Item(item)));

            // here we define which property will change as we scroll the page
            // in this case we will be translating on the y-axis
            // we interpolate between the previous and current value to achieve the smooth scrolling effect
            this.renderedStyles = {
                translationY: {
                    // interpolated value
                    previous: 0,
                    // current value
                    current: 0,
                    // amount to interpolate
                    ease: 0.1,
                    // current value setter
                    // in this case the value of the translation will be the same like the document scroll
                    setValue: () => docScroll
                }
            };
            // set the body's height
            this.setSize();
            // set the initial values
            this.update();
            // the <main> element's style needs to be modified
            this.style();
            // init/bind events
            this.initEvents();
            // start the render loop
            requestAnimationFrame(() => this.render());
        }
        update() {
            // sets the initial value (no interpolation) - translate the scroll value
            for (const key in this.renderedStyles ) {
                this.renderedStyles[key].current = this.renderedStyles[key].previous = this.renderedStyles[key].setValue();
            }
            // translate the scrollable element
            this.layout();
        }
        layout() {
            this.DOM.scrollable.style.transform = `translate3d(0,${-1*this.renderedStyles.translationY.previous}px,0)`;
        }
        setSize() {
            // set the heigh of the body in order to keep the scrollbar on the page
            body.style.height = `${this.DOM.scrollable.scrollHeight}px`;
        }
        style() {
            // the <main> needs to "stick" to the screen and not scroll
            // for that we set it to position fixed and overflow hidden
            this.DOM.main.style.position = 'fixed';
            this.DOM.main.style.width = this.DOM.main.style.height = '100%';
            this.DOM.main.style.top = this.DOM.main.style.left = 0;
            this.DOM.main.style.overflow = 'hidden';
        }
        initEvents() {
            // on resize reset the body's height
            window.addEventListener('resize', () => this.setSize());
        }
        remove(){
            this.DOM.main.removeAttribute("style")
        }
        render() {
            // Get scrolling speed
            // Update lastScroll
            scrollingSpeed = Math.abs(docScroll - lastScroll);
            lastScroll = docScroll;

            // update the current and interpolated values
            for (const key in this.renderedStyles ) {
                this.renderedStyles[key].current = this.renderedStyles[key].setValue();
                this.renderedStyles[key].previous = MathUtils.lerp(this.renderedStyles[key].previous, this.renderedStyles[key].current, this.renderedStyles[key].ease);
            }
            // and translate the scrollable element
            this.layout();

            // for every item
            for (const item of this.items) {
                // if the item is inside the viewport call it's render function
                // this will update item's styles, based on the document scroll value and the item's position on the viewport
                if ( item.isVisible ) {
                    if ( item.insideViewport ) {
                        item.render();
                    }
                    else {
                        item.insideViewport = true;
                        item.update();
                    }
                }
                else {
                    item.insideViewport = false;
                }
            }

            // loop..
            requestAnimationFrame(() => this.render());
        }
    }

    // Get the scroll position and update the lastScroll variable
    if(smooothscroll.length){
        getPageYScroll();
        lastScroll = docScroll;
        // Initialize the Smooth Scrolling
        if(smsc) smsc.remove();
        smsc = new SmoothScroll();
    }
};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.smoothscroll', Widget_DCE_Dynamicposts_smoothscroll_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.smoothscroll', Widget_DCE_Dynamicposts_smoothscroll_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.smoothscroll', Widget_DCE_Dynamicposts_smoothscroll_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.smoothscroll', Widget_DCE_Dynamicposts_smoothscroll_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.smoothscroll', Widget_DCE_Dynamicposts_smoothscroll_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.smoothscroll', Widget_DCE_Dynamicposts_smoothscroll_Handler);
});
